<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

class Game extends Model {

    public function league() {
        return $this->belongsTo(League::class);
    }

    public function teams() {
        return $this->belongsToMany(Team::class);
    }

    public function markets() {
        return $this->hasMany(Market::class);
    }

    public function teamOne() {
        return $this->belongsTo(Team::class, 'team_one_id');
    }

    public function teamTwo() {
        return $this->belongsTo(Team::class, 'team_two_id');
    }

    // Scopes
    public function scopeInPlay($query) {
        return $query->where('start_time', '<=', now())->where('is_outright', Status::NO)->whereNotIn('games.status', [Status::GAME_CANCELLED, Status::GAME_ENDED])
        ;
    }

    public function scopeNotOpenForBetting($query) {
        return $query->where('games.status', Status::GAME_NOT_OPEN_FOR_BETTING);
    }

    public function scopeOpenForBetting($query) {
        return $query->where('games.status', Status::GAME_OPEN_FOR_BETTING);
    }

    public function scopeCancelled($query) {
        return $query->where('games.status', Status::GAME_CANCELLED);
    }

    public function scopeClosedForBetting($query) {
        return $query->where('games.status', Status::GAME_CLOSED_FOR_BETTING);
    }

    public function scopeCompleted($query) {
        return $query->where('games.status', Status::GAME_ENDED);
    }

    public function scopeBetNotStarted($query) {
        return $query->where('bet_start_time', '>=', now());
    }

    public function getIsInPlayAttribute() {
        return now()->gte($this->start_time);
    }

    public function getIsExpiredAttribute() {
        return $this->status == Status::DISABLE;
    }

    public function scopeHasActiveCategory($query) {
        return $query->whereHas('league.category', function ($category) {
            $category->active();
        });
    }

    public function scopeRunningGame($query) {
        return $query->where('start_time', '<=', now());
    }

    public function scopeUpcomingGame($query) {
        return $query->where('start_time', '>', now())->where('games.status', '!=', Status::GAME_ENDED);
    }

    public function scopeHasActiveLeague($query) {
        return $query->whereHas('league', function ($league) {
            $league->active();
        });
    }

    public function scopeDateTimeFilter($query, $column) {
        if (!request()->$column) {
            return $query;
        }

        try {
            $date      = explode('-', request()->$column);
            $startDate = Carbon::parse(trim($date[0]))->format('Y-m-d');
            $endDate   = @$date[1] ? Carbon::parse(trim(@$date[1]))->format('Y-m-d') : $startDate;
        } catch (\Exception $e) {
            throw ValidationException::withMessages(['error' => 'Invalid date provided']);
        }

        return $query->whereDate($column, '>=', $startDate)->whereDate($column, '<=', $endDate);
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(
            get: fn () => $this->badgeData(),
        );
    }

    public function badgeData()
    {
        $html = '';

        if ($this->status == Status::GAME_CLOSED_FOR_BETTING) {
            $html = '<span class="badge badge--dark">' . trans('Closed for Betting') . '</span>';
        }elseif ($this->status == Status::GAME_OPEN_FOR_BETTING) {
            $html = '<span class="badge badge--primary">' . trans('Open for Betting') . '</span>';
        }elseif ($this->status == Status::GAME_CANCELLED) {
            $html = '<span class="badge badge--warning">' . trans('Cancelled') . '</span>';
        }elseif ($this->status == Status::GAME_ENDED) {
            $html = '<span class="badge badge--success">' . trans('Completed') . '</span>';
        }else{
            $html = '<span class="badge badge--dark">' . trans('Not Open for Betting') . '</span>';
        }

        return $html;
    }

    public function getTotalBetItemsAttribute()
    {
        return $this->markets->sum(function ($market) {
            return $market->betItems->count();
        });
    }

}
