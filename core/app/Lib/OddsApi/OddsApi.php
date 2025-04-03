<?php

namespace App\Lib\OddsApi;

use App\Constants\Status;
use App\Lib\CurlRequest;
use App\Models\Game;
use App\Models\Team;
use Illuminate\Support\Facades\Log;

/**
 * Class OddsApi
 *
 * This class provides methods to interact with the Odds API, allowing
 * retrieval of sports data and odds for specific leagues.
 */

class OddsApi {
    protected $baseUri = 'https://api.the-odds-api.com/v4/'; // Base URI for the Odds API
    protected $apiKey; // API key for authentication with the Odds API

    /**
     * OddsApi constructor.
     *
     * Initializes the OddsApi instance by setting the API key from the configuration.
     */
    public function __construct() {
        $this->apiKey = trim(gs('ods_api_key'));
        if(!$this->apiKey) {
            Log::error('Odds API key not set in API Setting');
            throw new \Exception("Odds API key not set in API Setting");
        }
    }

    /**
     * Fetch sports data from the Odds API.
     *
     * This method sends a request to the Odds API to retrieve sports data,
     * and returns the decoded JSON response.
     *
     * @return array The fetched sports data.
     *
     * @throws \Exception If there is an error fetching the data.
     */

    public function fetchSports() {
        $url = "{$this->baseUri}sports/?apiKey={$this->apiKey}&all=true";
        try {
            // Fetch data from the API using the CurlRequest
            $response = CurlRequest::curlContent($url);
            // Decode the JSON response into an array
            return json_decode($response, true);
        } catch (\Exception $e) {
            // Log the error and throw an exception
            Log::error('Error fetching sports data from API: ' . $e->getMessage());
            throw new \Exception('Error fetching sports data: ' . $e->getMessage());
        }
    }

    /**
     * Fetch odds data for a specific league from the Odds API.
     *
     * This method sends a request to the Odds API to retrieve odds data. It decodes the JSON response
     * and checks for any errors returned by the API.
     *
     * @param string $leagueKey The key identifier for the league for which odds are being fetched.
     * @param bool $hasOutrights Indicates whether the league has outright markets available.
     *
     * @return array The decoded JSON response containing odds data for the specified league.
     *
     * @throws \Exception If there is an error fetching the odds data or if the API returns an error code.
     */
    public function fetchOdds($leagueKey, $hasOutrights, $regions = null) {

        $regions = $regions ?: implode(',', gs('ods_api_regions'));

        $markets = gs('ods_api_markets');
        if (!$hasOutrights) {
            $outrightsKey = array_search('outrights', $markets);
            if ($outrightsKey) {
                unset($markets[$outrightsKey]);
            }
        }

        $markets = implode(',', $markets);

        $url = "{$this->baseUri}sports/{$leagueKey}/odds/?apiKey={$this->apiKey}&regions={$regions}&markets={$markets}";

        try {
            $response = CurlRequest::curlContent($url);
            $response = json_decode($response);

            if (isset($response->error_code)) {
                throw new \Exception(@$response->message);
            }

            return $response;
        } catch (\Exception $e) {
            throw new \Exception('Error fetching odds data: ' . $e->getMessage());
        }
    }


    /**
     * Fetch game/event data for a specific league from the odds API.
     *
     * This method sends a request to the odds API to retrieve game/event data. It decodes the JSON response
     * and checks for any errors returned by the API.
     *
     * @param string $leagueKey The key identifier for the league for which game/event are being fetched.
     *
     * @return array The decoded JSON response containing game/event data for the specified league.
     *
     * @throws \Exception If there is an error fetching the game/event data or if the API returns an error code.
     */
    public function fetchGames($leagueKey) {
        $url = "{$this->baseUri}sports/{$leagueKey}/events?apiKey={$this->apiKey}";
        try {
            $response = CurlRequest::curlContent($url);
            $response = json_decode($response);

            if (isset($response->error_code)) {
                throw new \Exception(@$response->message);
            }

            return $response;
        } catch (\Exception $e) {
            throw new \Exception('Error fetching games data: ' . $e->getMessage());
        }
    }

    protected function saveGame($league, $event, $homeTeam, $awayTeam) {
        $game              = new Game();
        $game->ods_api_id  = $event->id;
        $game->team_one_id = $homeTeam->id ?? 0;
        $game->team_two_id = $awayTeam->id ?? 0;
        $game->league_id   = $league->id;

        if ($homeTeam && $awayTeam) {
            $title = $homeTeam->name . ' vs ' . @$awayTeam->name;
        } else {
            $title = $league->name;
        }

        $game->slug           = createUniqueSlug($title, Game::class);
        $game->bet_start_time = now();
        $game->start_time     = $event->commence_time;
        $game->is_outright    = $league->has_outrights ?? 0;
        $game->manually_added = Status::NO;
        $game->save();
        return $game;
    }

    protected function saveTeam($league, $teamName) {
        $team = Team::where('category_id', $league->category_id)->where('name', $teamName)->first();

        if (!$team) {
            $team                 = new Team();
            $team->name           = $teamName;
            $team->short_name     = $teamName;
            $team->category_id    = $league->category_id;
            $team->manually_added = Status::NO;
            $team->slug           = createUniqueSlug($teamName, Team::class);
            $team->save();
        }

        return $team;
    }
}
