<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bet;
use App\Models\Market;

class BetController extends Controller {
    private $pageTitle;

    public function index() {
        $this->pageTitle = 'All Placed Bets';
        return $this->betData('');
    }

    public function pending() {
        $this->pageTitle = 'Pending Bets';
        return $this->betData('pending');
    }

    public function won() {
        $this->pageTitle = 'Won bets';
        return $this->betData('win');
    }

    public function loss() {
        $this->pageTitle = 'Loss Bets';
        return $this->betData('loss');
    }

    public function refunded() {
        $this->pageTitle = 'Refunded Bets';
        return $this->betData('refunded');
    }

    protected function betData($scope) {
        $pageTitle = $this->pageTitle;
        if ($scope) {
            $bets = Bet::$scope();
        } else {
            $bets = Bet::query();
        }
        if ($gameId = request()->game_id) {
            $bets->whereHas('bets.market', function($query) use ($gameId) {
                $query->where('game_id', $gameId);
            });
        }
        $bets = $bets->searchable(['bet_number'])->with(['user', 'bets' => function ($query) {
            $query->relationalData();
        }])->orderBy('id', 'desc')->paginate(getPaginate());

        return view('admin.bet.index', compact('pageTitle', 'bets'));
    }

    public function getByMarket($id) {
        $market    = Market::with('betItems')->findOrFail($id);
        $pageTitle = 'All bet for - ';
        if (@$market->game?->teamOne && @$market->game?->teamTwo){
            $pageTitle .= @$market->game->teamOne->name;
            $pageTitle .= ' VS ';
            $pageTitle .= @$market->game->teamTwo->name;
        }else{
            $pageTitle .= $market->game?->league?->name;
        }
        $betNumber = $market->betItems->pluck('bet_id')->unique();
        $bets      = Bet::whereIn('id', $betNumber)->searchable(['bet_number'])->with(['user', 'bets' => function ($query) {
            $query->relationalData();
        }])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.bet.index', compact('pageTitle', 'bets'));
    }

    public function details($id) {
        $bet  = Bet::where('id', $id)->with('bets.market', 'bets.outcome')->first();
        $bets = $bet->bets;
        return view('admin.bet.details', compact('bets', 'bet'));
    }
}
