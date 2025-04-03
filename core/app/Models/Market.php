<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Market extends Model {
    use GlobalStatus;

    protected $appends = ['market_title'];

    public function game() {
        return $this->belongsTo(Game::class);
    }

    public function upcomingGame() {
        return $this->belongsTo(Game::class)->where('bet_start_time', '>=', now())->count();
    }

    public function outcomes() {
        return $this->hasMany(Outcome::class);
    }
    public function winOutcome() {
        return $this->belongsTo(Outcome::class);
    }

    public function betItems() {
        return $this->hasMany(BetItem::class, 'market_id');
    }

    public function scopeResultDeclared($query) {
        return $query->where('result_declared', Status::YES);
    }

    public function scopeResultUndeclared($query) {
        return $query->where('result_declared', Status::NO);
    }

    public function scopeLocked($query) {
        return $query->where('locked', Status::MARKET_LOCKED);
    }

    public function scopeUnLocked($query) {
        return $query->where('locked', Status::MARKET_UNLOCKED);
    }

    public function scopeFilterByGamePeriod($query) {
        $query->where(function ($query) {
            $query->where('game_period_market', Status::NO)
            ->orWhere(function ($query) {
                $query->where('game_period_market', Status::YES)
                ->whereHas('game', function ($gameQuery) {
                    $gameQuery->runningGame();
                });
            });
        });
    }

    public function scopeMarketAvailable($query) {
        $query->unLocked()->resultUndeclared()->with('outcomes.bets', 'betItems.bet')->withWhereHas('game', function ($game) {
            $game->expired()->with([
                'teamOne',
                'teamTwo',
                'league' => function ($league) {
                    $league->active()->with(['category' => function ($category) {
                        $category->active();
                    }]);
                },
            ]);
        });
    }

    public function getMarketTitleAttribute() {
        if ($this->market_type == 'h2h') {
            $title = 'Head to Head';
        } else if ($this->market_type == 'h2h_3way') {
            $title = 'Head to Head 3 way';
        } else if ($this->market_type == 'spreads') {
            $title = 'Spreads';
        } else if ($this->market_type == 'totals') {
            $title = 'Totals';
        } else if ($this->market_type == 'outrights') {
            $title = 'Outrights';
        } else {
            $title = $this->title;
        }
        return $title;
    }
}
