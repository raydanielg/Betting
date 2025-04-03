<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Bet;

class BetLogController extends Controller {
    protected $pageTitle;

    public function index($type = null) {
        $this->pageTitle = 'My Bets';
        return $this->bets();
    }

    public function pending() {
        $this->pageTitle = 'Pending Bets';
        return $this->bets('pending');
    }

    public function wins() {
        $this->pageTitle = 'Won Bets';
        return $this->bets('win');
    }

    public function losses() {
        $this->pageTitle = 'Lost Bets';
        return $this->bets('loss');
    }

    public function refunded() {
        $this->pageTitle = 'Refunded Bets';
        return $this->bets('refunded');
    }

    private function bets($scope = null) {
        $user = auth()->user();
        $widget['totalBet']         = Bet::where('user_id', $user->id)->count();
        $widget['pendingBet']       = Bet::where('user_id', $user->id)->pending()->count();
        $widget['wonBet']           = Bet::where('user_id', $user->id)->win()->count();
        $widget['loseBet']          = Bet::where('user_id', $user->id)->loss()->count();
        $widget['refundedBet']      = Bet::where('user_id', $user->id)->refunded()->count();

        if ($scope) {
            try {
                $bets      = Bet::$scope();
            } catch (\Exception $e) {
                abort(404);
            }
        } else {
            $bets = Bet::query();
        }

        $pageTitle = $this->pageTitle;
        $bets = $bets->where('user_id', auth()->id())->withCount('bets')->searchable(['bet_number'])->orderBy('id', 'desc')->paginate(getPaginate());

        return view('Template::user.bet.index', compact('pageTitle', 'bets', 'widget'));
    }

    public function details($id) {
        $bet       = Bet::where('user_id', auth()->id())->where('id', $id)->with('bets.market', 'bets.outcome')->first();
        $bets      = $bet->bets;
        $pageTitle = 'Bet Details';
        return view('Template::user.bet.details', compact('pageTitle', 'bets', 'bet'));
    }
}
