<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\OddsApi\GetGames;
use App\Lib\OddsApi\GetOdds;
use App\Lib\OddsApi\GetSports;
use App\Lib\Referral;
use App\Models\Bet;
use App\Models\CronJob;
use App\Models\CronJobLog;
use App\Models\Game;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CronController extends Controller {

    public function runManually(Request $request) {
        $alias = $request->alias;
        $result = $this->$alias();

        if (!$result['success']) {
            $notify[] = ['error', $result['error']];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Cron executed successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Fetch leagues data from the Odds API.
     *
     * This method sends a request to the Odds API to retrieve a list of leagues
     */
    public function fetchLeagues() {
        $startTime = now();
        $error = null;
        $this->updateLastCronTime('fetchLeagues');

        try {
            new GetSports();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error('Failed to fetch and save leagues data: ' . $error);
        }

        $this->storeCronLog('fetchLeagues', $startTime, now(), $error);

        if ($error) {
            return ['success' => false, 'error' => $error];
        }
        return ['success' => true];
    }

    /**
     * Fetch a list of in-play and pre-match games from the Odds API.
     */
    public function fetchGames() {
        $startTime = now();
        $error = null;
        $this->updateLastCronTime('fetchGames');

        try {
            new GetGames();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error('Failed to fetch and save game data: ' . $error);
        }

        $this->storeCronLog('fetchGames', $startTime, now(), $error);

        if ($error) {
            return ['success' => false, 'error' => $error];
        }
        return ['success' => true];
    }

    /**
     * Fetch live games from the odds API
     *
     * Update game status, odds, and betting status
     */
    public function fetchOdds() {
        $startTime = now();
        $error = null;
        $this->updateLastCronTime('fetchOdds');

        try {
            new GetOdds('active');
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error('Failed to fetch live matches: ' . $error);
        }

        $this->storeCronLog('fetchOdds', $startTime, now(), $error);

        if ($error) {
            return ['success' => false, 'error' => $error];
        }
        return ['success' => true];
    }

    public function fetchInPlayOdds() {
        $startTime = now();
        $error = null;
        $this->updateLastCronTime('fetchInPlayOdds');
        try {
            // Fetch running matches from the odds API
            // Update game status, odds, and betting status
            new GetOdds('running');
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error('Failed to fetch running matches: ' . $error);
        }

        $this->storeCronLog('fetchInPlayOdds', $startTime, now(), $error);
        if ($error) {
            return ['success' => false, 'error' => $error];
        }
        return ['success' => true];
    }

    public function win() {
        $startTime = now();
        $error = null;
        $this->updateLastCronTime('win');
        $bets = Bet::win()->notSettled()->orderBy('result_time', 'asc')->with('user')->limit(100)->get();

        try {
            foreach ($bets as $bet) {

                $transaction = Transaction::where('trx', $bet->bet_number)->where('remark', 'bet_won')->where('user_id', $bet->user_id)->exists();

                $bet->is_settled = Status::YES;
                $bet->save();

                if ($transaction) {
                    continue;
                }

                $user = $bet->user;
                $user->balance += $bet->return_amount;
                $user->save();

                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->amount       = $bet->return_amount;
                $transaction->post_balance = $user->balance;
                $transaction->trx_type     = '+';
                $transaction->trx          = $bet->bet_number;
                $transaction->remark       = 'bet_won';
                $transaction->details      = 'For bet winning';
                $transaction->save();

                if (gs('win_commission') && gs('referral_program')) {
                    Referral::levelCommission($user, $bet->return_amount, $bet->bet_number, 'win');
                }

                notify($user, 'BET_WIN', [
                    'username'   => $user->username,
                    'amount'     => $bet->return_amount,
                    'bet_number' => $bet->bet_number,
                ]);
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error('Failed to process win data: ' . $error);
        }

        $this->storeCronLog('win', $startTime, now(), $error);
        if ($error) {
            return ['success' => false, 'error' => $error];
        }
        return [
            'success' => true,
            'total_bets' => $bets->count()
        ];
    }

    public function lose() {
        $startTime = now();
        $error = null;
        $this->updateLastCronTime('lose');

        $bets = Bet::loss()->notSettled()->orderBy('result_time', 'asc')->with('user')->limit(100)->get();

        try {

            foreach ($bets as $bet) {
                $bet->is_settled = Status::YES;
                $bet->save();

                $user = $bet->user;
                notify($user, 'BET_LOSS', [
                    'username'   => $user->username,
                    'amount'     => $bet->stake_amount,
                    'bet_number' => $bet->bet_number,
                ]);
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error('Failed to process lose data: ' . $error);
        }

        $this->storeCronLog('lose', $startTime, now(), $error);
        if ($error) {
            return ['success' => false, 'error' => $error];
        }
        return [
            'success' => true,
            'total_bets' => $bets->count()
        ];
    }

    public function refund() {
        $startTime = now();
        $error = null;
        $this->updateLastCronTime('refund');

        $bets = Bet::refunded()->notSettled()->orderBy('result_time', 'asc')->with('user')->limit(100)->get();

        try {
            foreach ($bets as $bet) {

                $transaction = Transaction::where('trx', $bet->bet_number)->where('remark', 'bet_refunded')->where('user_id', $bet->user_id)->exists();

                $bet->is_settled = Status::YES;
                $bet->save();

                if ($transaction) {
                    continue;
                }

                $user = $bet->user;

                $user->balance += $bet->stake_amount;
                $user->save();

                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->amount       = $bet->stake_amount;
                $transaction->post_balance = $user->balance;
                $transaction->trx_type     = '+';
                $transaction->trx          = $bet->bet_number;
                $transaction->remark       = 'bet_refunded';
                $transaction->details      = 'For bet refund';
                $transaction->save();

                notify($user, 'BET_REFUNDED', [
                    'username'   => $user->username,
                    'amount'     => $bet->stake_amount,
                    'bet_number' => $bet->bet_number,
                ]);
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error('Failed to process lose data: ' . $error);
        }

        $this->storeCronLog('refund', $startTime, now(), $error);

        if ($error) {
            return ['success' => false, 'error' => $error];
        }
        return [
            'success' => true,
            'total_bets' => $bets->count()
        ];
    }

    public function setOpenForBetting() {
        $startTime = now();
        $error = null;
        $this->updateLastCronTime('setOpenForBetting');
        try {
            $games = Game::where('bet_start_time', '>=', now())->where('status', Status::GAME_NOT_OPEN_FOR_BETTING)->get();

            foreach ($games as $game) {
                $game->status = Status::GAME_OPEN_FOR_BETTING;
                $game->save();
            }
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error('Failed to set games for open for betting: ' . $error);
        }

        $this->storeCronLog('setOpenForBetting', $startTime, now(), $error);

        if ($error) {
            return ['success' => false, 'error' => $error];
        }

        return [
            'success' => true,
            'total_games' => $games->count(),
            'message' => 'Games are set to open for betting'
        ];
    }

    private function updateLastCronTime($alias) {
        $cronJob = CronJob::where('alias', $alias)->first();
        $cronJob->last_run = now();
        $cronJob->save();
    }

    private function storeCronLog($alias, $startTime, $endTime, $error = '') {
        $cronJob = CronJob::where('alias', $alias)->first();

        if ($cronJob) {
            $cronJob->last_run = $endTime;
            $cronJob->save();

            // Cron job log data store
            $startTime         = Carbon::parse($startTime);
            $endTime           = Carbon::parse($endTime);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);

            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cronJob->id;
            $cronLog->start_at    = $startTime;
            $cronLog->error       = $error;
            $cronLog->end_at      = $endTime;
            $cronLog->duration    = $diffInSeconds;
            $cronLog->save();
        }
    }
}
