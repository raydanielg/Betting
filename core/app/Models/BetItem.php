<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class BetItem extends Model {
    protected $fillable = ['status'];

    protected $appends = [
        'status_badge',
    ];

    use GlobalStatus;

    public function bet() {
        return $this->belongsTo(Bet::class);
    }

    public function market() {
        return $this->belongsTo(Market::class);
    }

    public function outcome() {
        return $this->belongsTo(Outcome::class);
    }

    public function betData() {
        return $this->hasOne(Bet::class);
    }

    // Scope
    public function scopeHasSingleBet($query) {
        return $query->whereHas('bet', function ($q) {
            $q->where('type', Status::SINGLE_BET)->pending();
        });
    }

    public function scopeHasMultiBet($query) {
        return $query->whereHas('bet', function ($q) {
            $q->where('type', Status::MULTI_BET)->pending();
        });
    }

    public function scopePending($query) {
        return $query->where('status', Status::BET_PENDING);
    }

    public function getStatusBadgeAttribute() {
        if ($this->status == 1) {
            return '<span class="badge badge--success">' . trans("Won") . '</span>';
        } else if ($this->status == 2) {
            return '<span class="badge badge--warning">' . trans("Pending") . '</span>';
        } else if ($this->status == 3) {
            return '<span class="badge badge--danger">' . trans("Lost") . '</span>';
        } else if ($this->status == 4) {
            return '<span class="badge badge--primary">' . trans("Refunded") . '</span>';
        }
    }

    public function scopeRelationalData($query) {
        $query->with([
            'outcome' => function ($outcome) {
                $outcome->active()->with([
                    'market' => function ($market) {
                        $market->active()->with([
                            'game' => function ($game) {
                                $game->with([
                                    'teamOne',
                                    'teamTwo',
                                    'league' => function ($league) {
                                        $league->active()->with('category');
                                    },
                                ]);
                            },
                        ]);
                    },
                ]);
            },
        ]);
    }
}
