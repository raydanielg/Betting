<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Outcome extends Model {
    use GlobalStatus;

    protected $guarded = ['id'];

    public function market() {
        return $this->belongsTo(Market::class);
    }
    public function bets() {
        return $this->hasMany(BetItem::class);
    }

    public function scopeLocked($query) {
        return $query->where('locked', Status::OUTCOME_LOCKED);
    }

    public function scopeUnLocked($query) {
        return $query->where('locked', Status::OUTCOME_UNLOCKED);
    }

    public function scopeAvailableForBet($query) {
        return $query->active()->unLocked()->whereHas('market', function ($market) {
            $market->active()->unLocked()->resultUndeclared()->FilterByGamePeriod()
                ->whereHas('game', function ($game) {
                    $game->openForBetting()->hasActiveCategory()->hasActiveLeague();
                });
        });
    }
}
