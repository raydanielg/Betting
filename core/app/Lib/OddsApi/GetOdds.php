<?php

namespace App\Lib\OddsApi;

use App\Models\Game;
use App\Models\Team;
use App\Models\League;
use App\Models\Market;
use App\Models\Outcome;
use App\Lib\OddsApi\OddsApi;
use Illuminate\Support\Facades\Log;

class GetOdds extends OddsApi {

    private $type = null;

    public function __construct($type = null) {
        parent::__construct();
        $this->type = $type;

        try {
            $this->initializeOddsProcessing();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function initializeOddsProcessing() {

        $leaguesQuery = League::hasApiSportKey()->with('category')->running()->whereNotNull('odds_api_sport_key');

        if($this->type == 'running') {
            $leaguesQuery->whereHas('runningActiveGames');
        }

        $leagues = $leaguesQuery->get();

        if (blank($leagues)) {
            return; // No active leagues found, exit early
        }

        foreach ($leagues as $league) {
            try {
                $this->fetchAndSaveOddsByLeague($league);
            } catch (\Exception $e) {
                Log::error('Error processing odds for league ' . $league->odds_api_sport_key . ': ' . $e->getMessage());
                throw new \Exception($e->getMessage());

            }
        }
    }

    public function fetchAndSaveOddsByLeague($league) {
        $regions = @$league->category->regions? implode(",", $league->category->regions) : NULL;
        $events = $this->fetchOdds($league->odds_api_sport_key, $league->has_outrights, $regions);

        foreach ($events as $event) {
            $homeTeam = null;
            $awayTeam = null;

            if ($event->home_team) {
                $homeTeam = $this->saveTeam($league, $event->home_team);
            }

            if ($event->away_team) {
                $awayTeam = $event->away_team ? $this->saveTeam($league, $event->away_team) : null;
            }

            if (empty($event->bookmakers)) {
                continue;
            }

            $supportedMarkets = gs('ods_api_markets');

            $game = Game::where('ods_api_id', $event->id)->first();

            if (!$game) {
                $game = $this->saveGame($league, $event, $homeTeam, $awayTeam);
            } else {
                $game->start_time = $event->commence_time;
                $game->save();
            }

            $markets = [];

            foreach ($supportedMarkets as $supportedMarket) {
                $market = collect($event->bookmakers)->pluck('markets')->flatten()->where('key', $supportedMarket)->sortByDesc('last_update')->first();

                if (!$market) {
                    continue;
                }

                if ($market->key == 'h2h') {
                    $drawOutcome = collect($market->outcomes)->where('name', 'Draw')->first();
                    if ($drawOutcome) {
                        $newMarket      = clone $market;
                        $newMarket->key = 'h2h_3way';

                        $markets[] = $newMarket;

                        $market->outcomes = collect($market->outcomes)->reject(function ($outcome) {
                            return $outcome->name == 'Draw';
                        })->values()->toArray();
                    }
                }

                $markets[] = $market;
            }

            if (!empty($markets)) {
                $this->saveMarkets($league, $game, $markets);
            }

        }
    }

    public function getBookMakerKey($event) {
        $bookmakers = [];

        foreach ($event->bookmakers as $bookmaker) {
            $bookmaker    = [$bookmaker->key => count($bookmaker->markets)];
            $bookmakers[] = $bookmaker;
        }

        $highestOdds = max($bookmakers);
        return array_key_first($highestOdds);
    }

    public function saveMarkets($league, $game, $markets) {
        foreach ($markets as $market) {
            $dbMarket = $this->saveMarket($market, $game);
            $this->saveOutComes($league, $game, $market, $dbMarket);
        }
    }

    public function saveMarket($market, $game) {
        $marketData = getMarkets()->where('key', $market->key)->first();
        $newMarket                    = Market::where('market_type', $market->key)->with('outcomes')->where('game_id', $game->id)->firstOrNew();

        $newMarket->game_id           = $game->id;
        $newMarket->market_type       = $market->key;
        $newMarket->outcome_type      = $marketData->outcome_type;
        $newMarket->title             = $marketData->name;
        $newMarket->market_updated_at = $market->last_update;
        $newMarket->save();
        return $newMarket;
    }

    public function saveOutcomes($league, $game, $apiMarket, $dbMarket) {
        $teams = [];
        foreach ($apiMarket->outcomes as $outcome) {
            if ($outcome->name && $game->team_one_id == 0 && $game->team_two_id == 0) {
                $team = Team::firstOrCreate(
                    ['name' => $outcome->name, 'category_id' => $league->category_id],
                    [
                        'short_name' => $outcome->name,
                        'slug'       => createUniqueSlug($outcome->name, Team::class),
                    ]
                );

                $teams[] = $team->id;
            }

            Outcome::updateOrCreate(
                ['market_id' => $dbMarket->id, 'name' => $outcome->name],
                [
                    'odds'  => $outcome->price,
                    'point' => @$outcome->point,
                ]
            );
        }

        if (!empty($teams)) {
            $game->teams()->syncWithoutDetaching($teams);
        }
    }
}
