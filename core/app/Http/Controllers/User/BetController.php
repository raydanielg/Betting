<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\Referral;
use App\Models\Bet;
use App\Models\BetItem;
use App\Models\Transaction;
use Illuminate\Http\Request;

class BetController extends Controller {

    public function placeBet(Request $request) {
        $status = implode(',', [Status::SINGLE_BET, Status::MULTI_BET]);

        $request->validate([
            'type'         => "required|integer|in:$status",
            'stake_amount' => 'required_if:type,2|nullable|numeric|gt:0',
        ]);

        $user    = auth()->user();
        $betType = $request->type;
        $bets    = collect(session('bets'));

        $isSuspended = $bets->contains(function ($bet) {
            return isSuspendBet($bet);
        });

        if ($isSuspended) {
            $notify[] = ['error', 'You have to remove suspended bet from bet slip'];
            return back()->withNotify($notify);
        }

        if (blank($bets)) {
            $notify[] = ['error', 'No bet item found in bet slip'];
            return back()->withNotify($notify);
        }

        if ($bets->count() < 2 && $betType == Status::MULTI_BET) {
            $notify[] = ['error', 'Multi bet requires more than one bet'];
            return back()->withNotify($notify);
        }

        $totalStakeAmount = $betType == Status::SINGLE_BET ? getAmount($bets->sum('stake_amount'), 8) : $request->stake_amount;

        $minLimit = $betType == Status::SINGLE_BET ? gs('single_bet_min_limit') : gs('multi_bet_min_limit');
        $maxLimit = $betType == Status::SINGLE_BET ? gs('single_bet_max_limit') : gs('multi_bet_max_limit');

        if ($totalStakeAmount < $minLimit) {
            $notify[] = ['error', 'Min stake limit ' . $minLimit . ' ' . gs('cur_text')];
            return back()->withNotify($notify);
        }
        if ($totalStakeAmount > $maxLimit) {
            $notify[] = ['error', 'Max stake limit ' . $maxLimit . ' ' . gs('cur_text')];
            return back()->withNotify($notify);
        }

        if ($totalStakeAmount > $user->balance) {
            $notify[] = ['error', "You don't have sufficient balance"];
            return back()->withNotify($notify);
        }

        $user->balance -= $totalStakeAmount;
        $user->save();

        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $totalStakeAmount;
        $transaction->post_balance = $user->balance;
        $transaction->trx_type     = '-';
        $transaction->details      = 'For bet placing';
        $transaction->trx          = getTrx();
        $transaction->remark       = 'bet_placed';
        $transaction->save();

        if ($betType == Status::SINGLE_BET) {
            $this->placeSingleBet();
        } else {
            $this->placeMultiBet();
        }

        if (gs('bet_commission') && gs('referral_program')) {
            Referral::levelCommission($user, $totalStakeAmount, $transaction->trx, 'bet');
        }

        session()->forget('bets');
        $notify[] = ['success', 'Bet placed successfully'];
        return to_route('home', 'mybets')->withNotify($notify);
    }

    private function placeSingleBet() {
        $betData = collect(session('bets'));

        foreach ($betData as $betItem) {
            $returnAmount = $betItem->stake_amount * $betItem->odds;
            $bet          = $this->saveBetData(Status::SINGLE_BET, $betItem->stake_amount, $returnAmount);
            $this->saveBetDetail($bet->id, $betItem);
        }
    }

    private function placeMultiBet() {

        $bet          = $this->saveBetData(Status::MULTI_BET, request()->stake_amount);
        $returnAmount = $bet->stake_amount;
        $betData      = collect(session('bets'));
        foreach ($betData as $betItem) {
            $returnAmount *= $betItem->odds;
            $this->saveBetDetail($bet->id, $betItem);
        }

        $bet->return_amount = $returnAmount;
        $bet->save();
    }

    private function saveBetData($type, $stakeAmount, $returnAmount = 0) {
        $bet                = new Bet();
        $bet->bet_number    = getTrx(8);
        $bet->user_id       = auth()->id();
        $bet->type          = $type;
        $bet->stake_amount  = $stakeAmount;
        $bet->return_amount = $returnAmount;
        $bet->status        = Status::BET_PENDING;
        $bet->save();

        return $bet;
    }

    private function saveBetDetail($betId, $betItem) {
        $betDetail             = new BetItem();
        $betDetail->bet_id     = $betId;
        $betDetail->market_id  = $betItem->market_id;
        $betDetail->outcome_id = $betItem->outcome_id;
        $betDetail->odds       = $betItem->odds;
        $betDetail->status     = Status::BET_PENDING;
        $betDetail->save();
    }
}
