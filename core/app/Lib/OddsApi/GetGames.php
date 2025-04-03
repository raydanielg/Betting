<?php

namespace App\Lib\OddsApi;

use App\Models\Game;
use App\Models\League;
use App\Lib\OddsApi\OddsApi;
use Illuminate\Support\Facades\Log;

class GetGames extends OddsApi
{
    public function __construct() {
        parent::__construct();

        try {
            $this->initializeGamesProcessing();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function initializeGamesProcessing() {
        $leagues = League::hasApiSportKey()->running()->whereNotNull('odds_api_sport_key')->get();

        if (blank($leagues)) {
            return; // No active leagues found, exit early
        }

        foreach ($leagues as $league) {
            try {
                $this->fetchAndSaveGamesByLeague($league);
            } catch (\Exception $e) {
                Log::error('Error processing odds for games ' . $league->odds_api_sport_key . ': ' . $e->getMessage());
                throw new \Exception($e->getMessage());
            }
        }
    }

    public function fetchAndSaveGamesByLeague($league) {

        $events = $this->fetchGames($league->odds_api_sport_key);

        if (empty($events)) {
            Log::info("No games found for league: " . $league->odds_api_sport_key);
            return;
        }

        foreach ($events as $event) {
            $homeTeam = null;
            $awayTeam = null;

            $homeTeam = !empty($event->home_team) ? $this->saveTeam($league, $event->home_team) : null;
            $awayTeam = !empty($event->away_team) ? $this->saveTeam($league, $event->away_team) : null;

            $game = Game::where('ods_api_id', $event->id)->first();

            if (!$game) {
                $game = $this->saveGame($league, $event, $homeTeam, $awayTeam);
            } else {
                $game->start_time = $event->commence_time;
                $game->save();
            }
        }
    }
}
