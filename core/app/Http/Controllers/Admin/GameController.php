<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Game;
use App\Models\League;
use App\Models\Market;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GameController extends Controller {
    protected $pageTitle;

    protected function gameData($scope = null) {

        $games = $scope ? Game::$scope() : Game::query();

        if (request()->start_time) {
            $games->dateTimeFilter('start_time');
        }

        if (request()->bet_start_time) {
            $games->dateTimeFilter('bet_start_time');
        }

        if (request()->category_id) {
            $games->whereHas('league.category', function ($q) {
                $q->where('category_id', request()->category_id);
            });
        }

        $games = $games->with(['teamOne', 'teamTwo', 'league.category'])
            ->filter(['league_id', 'team_one_id', 'team_two_id'])
            ->orderBy('id', 'desc')
            ->withCount(['markets', 'markets as total_bets_count' => function ($query) {
                $query->join('bet_items', 'bet_items.market_id', '=', 'markets.id');
            }])
            ->paginate(getPaginate());



        $pageTitle = $this->pageTitle;

        $teamsOne = Team::rightJoin('games', 'teams.id', 'games.team_two_id')->whereNotNull('teams.id')->select('teams.id', 'teams.name', 'teams.short_name')->distinct('teams.id')->get();
        $teamsTwo = Team::rightJoin('games', 'teams.id', 'games.team_one_id')->whereNotNull('teams.id')->select('teams.id', 'teams.name', 'teams.short_name')->distinct('teams.id')->get();

        $teams = $teamsOne->union($teamsTwo)->unique();

        $categories = Category::whereHas('leagues.games')->with('leagues')->get();
        $leagues = $categories->pluck('leagues')->flatten();

        return view('admin.game.index', compact('pageTitle', 'games', 'categories', 'leagues', 'teams'));
    }

    public function index() {
        $this->pageTitle = 'All Games';
        return $this->gameData();
    }

    public function notOpenForBetting() {
        $this->pageTitle = 'Not Open for Betting - Games';
        return $this->gameData('notOpenForBetting');
    }

    public function openForBetting() {
        $this->pageTitle = 'Open for Betting - Games';
        return $this->gameData('openForBetting');
    }

    public function closedForBetting() {
        $this->pageTitle = 'Open for Betting - Games';
        return $this->gameData('closedForBetting');
    }

    public function inPlay() {
        $this->pageTitle = 'In Play Games';
        return $this->gameData('inPlay');
    }

    public function upcoming() {
        $this->pageTitle = 'Upcoming Games';
        return $this->gameData('UpcomingGame');
    }

    public function ended() {
        $this->pageTitle = 'Ended Games';
        return $this->gameData('completed');
    }

    public function cancelled() {
        $this->pageTitle = 'Cancelled Games';
        return $this->gameData('cancelled');
    }

    public function create() {
        $pageTitle  = 'Add New Game';
        $leagues    = League::with('category')->orderBy('name')->get();
        $categories = Category::with([
            'leagues' => function ($query) {
                $query->orderBy('name');
            }
        ])
            ->whereHas('leagues')
            ->orderBy('name')->get();
        return view('admin.game.form', compact('pageTitle', 'leagues', 'categories'));
    }

    public function teamsByCategory($categoryId) {
        $teams = Team::where('category_id', $categoryId)->orderBy('name')->get();

        if (count($teams)) {
            return response()->json([
                'teams' => $teams,
            ]);
        } else {
            return response()->json([
                'error' => 'No teams found for this league\'s category',
            ]);
        }
    }

    public function edit($id) {
        $game      = Game::findOrFail($id);
        $pageTitle = 'Update Game';

        $categories = Category::with(['leagues' => function ($query) {
            $query->orderBy('name');
        }])->orderBy('name')->get();

        $leagues = League::latest()->with('category')->get();
        return view('admin.game.form', compact('game', 'pageTitle', 'leagues', 'categories'));
    }

    public function store(Request $request, $id = 0) {
        $this->validation($request, $id);
        $league = League::findOrFail($request->league_id);

        if ($id) {
            $game         = Game::findOrFail($id);
            $notification = 'Game updated successfully';
        } else {
            $game                 = new Game();
            $notification         = 'Game added successfully';
            $game->manually_added = Status::YES;
        }

        $game->team_one_id    = $request->team_one_id;
        $game->team_two_id    = $request->team_two_id;
        $game->slug           = createUniqueSlug($request->slug, Game::class, $id);
        $game->title          = $request->title;
        $game->league_id      = $league->id;
        $game->status         = $this->getGameStatus($request, $game);
        $game->start_time     = Carbon::parse($request->start_time);
        $game->bet_start_time = Carbon::parse($request->bet_start_time);
        $game->is_outright    = $request->event_type;

        $game->save();

        if ($game->is_outright) {
            $this->saveMarket($game);
        }

        $notify[] = ['success', $notification];

        if ($id) {
            return back()->withNotify($notify);
        }

        return to_route('admin.market.index', $game->id)->withNotify($notify);
    }

    public function saveMarket($game) {
        $market = $game->markets->first();

        if (!$market) {
            $market              = new Market();
            $market->market_type = 'outrights';
            $market->game_id     = $game->id;
            $market->title       = $game->title;
            $market->save();
        }
    }

    public function getGameStatus($request, $game) {

        if (in_array($game->status, [Status::GAME_CANCELLED, Status::GAME_ENDED, Status::GAME_CLOSED_FOR_BETTING])) {
            return $game->status;
        }

        $betStartTime = Carbon::parse($request->bet_start_time);

        // Default status
        $status = Status::GAME_NOT_OPEN_FOR_BETTING;

        // If the game is already open for betting but the bet start time has been modified
        if ($game->status == Status::GAME_OPEN_FOR_BETTING && $game->bet_start_time < $betStartTime->toDateTimeString()) {
            $status = Status::GAME_NOT_OPEN_FOR_BETTING;
        }

        // Open the game for betting if the bet start time is in the past or now
        if ($betStartTime <= now()) {
            $status = Status::GAME_OPEN_FOR_BETTING;
        }

        return $status;
    }

    public function updateStatus(Request $request, $id) {

        $allowedStatuses = implode(',', [Status::GAME_CANCELLED, Status::GAME_OPEN_FOR_BETTING, Status::GAME_CLOSED_FOR_BETTING, Status::GAME_ENDED]);
        $request->validate([
            'status' => 'required|in:' . $allowedStatuses,
        ], [
            'status.required' => 'Please select a status',
        ]);

        $game = Game::findOrFail($id);

        if ($game->status == Status::GAME_CANCELLED || $game->status == Status::GAME_ENDED) {
            $notify[] = ['error', 'Status can\'t be changed for this game.'];
            return back()->withNotify($notify);
        }

        $game->status = $request->status;
        $game->save();

        $notify[] = ['success', 'Status changed successfully'];
        return back()->withNotify($notify);
    }

    protected function validation($request) {
        $request->validate([
            'league_id'      => 'required|integer|gt:0',
            'team_one_id'    => 'required_if:event_type,0|:integer|gt:0',
            'team_two_id'    => 'required_if:event_type,0|:integer|gt:0|different:team_one_id',
            'slug'           => 'required|alpha_dash|max:255',
            'title'          => 'required|string',
            'start_time'     => 'required|date',
            'bet_start_time' => 'required|date',
            'event_type'     => 'required|in:0,1',
        ], [
            'slug.alpha_dash' => 'Only alpha numeric value. No space or special character is allowed',
        ]);
    }
}
