<?php

namespace App\Lib\OddsApi;

use App\Models\League;
use App\Models\Category;
use App\Constants\Status;
use App\Lib\OddsApi\OddsApi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class GetSports
 *
 * Handles fetching sports/leagues data from the Odds API and adjusts the local database
 * categories and leagues based on the data fetched.
 */
class GetSports extends OddsApi {

    /**
     * GetSports constructor.
     *
     * Initializes the process of fetching sports data and updating the local database.
     * Fetches sports data from the Odds API and calls the methods to update categories and leagues.
     *
     * @throws \Exception
     */
    public function __construct() {

        parent::__construct(); // Call the parent constructor to initialize API settings

        $sports = $this->fetchSports(); // Fetch sports data from the API

        if (isset($sports['error_code'])) {
            throw new \Exception(@$sports['message']);
        } else {
            // Begin a database transaction to ensure data integrity
            DB::transaction(function () use ($sports) {
                // Update leagues based on the fetched sports data
                $this->adjustLeagues($sports);
            });
        }
    }

    /**
     * Adjust leagues based on the fetched sports data.
     *
     * This method processes the sports data to update existing leagues and
     * insert new leagues into the database. It groups leagues by their
     * corresponding categories and checks for existing entries before
     * performing updates or inserts.
     *
     * @param array $sports The sports data fetched from the Odds API.
     *
     * @return void
     */
    private function adjustLeagues($sports) {

        $categories = Category::get();  // Retrieve all categories from the database
        $newLeagues = [];
        foreach (collect($sports)->whereIn('group', $categories->pluck('odds_api_name')->toArray()) as $sport) {

            $sport = (object) $sport; // Cast to an object for easier property access


            $category = $categories->where('odds_api_name', $sport->group)->first(); // Find the corresponding category using the odds_api_name

            // If no corresponding category is found, skip to the next sport
            if (!$category) {
                continue;
            }

            // Check if the league already exists based on the league name and category
            $league  = League::whereNull('odds_api_sport_key')->where('name', $sport->title)->where('category_id', $category->id)->first();


            if ($league) {
                // Update the existing league with new data
                $league->odds_api_sport_key = $sport->key;
                $league->api_status         = $sport->active;
                $league->save();
            } else {
                $leagueExists = League::where('odds_api_sport_key', $sport->key)->exists(); // Check if the league with the given key already exists
                if (!$leagueExists) {
                    $newLeagues[] = $this->prepareNewLeague($sport, $category->id);  // Prepare a new league entry
                }
            }
        }

        // Insert new leagues into the database if there are any
        if (!empty($newLeagues)) {
            try {
                League::insert($newLeagues);
            } catch (\Exception $e) {
                Log::error('Failed to insert new leagues: ' . $e->getMessage());
                throw $e;
            }
        }
    }

    /**
     * Prepares a new league data array for insertion.
     *
     * @param object $sport The sport data object containing league information.
     * @param int $categoryId The ID of the category the league belongs to.
     * @return array The prepared league data array.
     */
    private function prepareNewLeague($sport, $categoryId) {
        return [
            'odds_api_sport_key' => $sport->key,
            'category_id'        => $categoryId,
            'name'               => $sport->title,
            'short_name'         => $sport->title,
            'slug'               => createUniqueSlug($sport->key, League::class),
            'description'        => $sport->description,
            'has_outrights'      => $sport->has_outrights,
            'api_status'         => $sport->active,
            'status'             => Status::DISABLE,
            'manually_added'     => Status::NO,
            'created_at'         => now(),
            'updated_at'         => now(),
        ];
    }
}
