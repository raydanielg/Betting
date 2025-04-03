<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class League extends Model {
    use GlobalStatus;

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function games() {
        return $this->hasMany(Game::class);
    }

    public function runningGame() {
        return $this->hasMany(Game::class)->where('bet_start_time', '<', now());
    }

    public function upcomingGame() {
        return $this->hasMany(Game::class)->where('bet_start_time', '>', now());
    }

    public function runningActiveGames() {
        return $this->hasMany(Game::class)->runningGame()->openForBetting();
    }

    public function upcomingActiveGames() {
        return $this->hasMany(Game::class)->upcomingGame()->openForBetting();
    }

    public function logo(){
        return getImage(getFilePath('league') . '/' . $this->image, $this->name[0]);
    }

    public function scopeActiveForUser($query) {
        return $query->running()->whereHas('category', function ($q) {
            $q->active();
        });
    }

    public function scopeRunning($query) {
        return $query->apiActive()->active();
    }

    public function scopeApiActiveAndDisabled($query) {
        return $query->apiActive()->inactive();
    }

    public function scopeApiActive($query) {
        return $query->where('api_status', Status::ENABLE);
    }

    public function scopeHasApiSportKey($query) {
        return $query->whereNotNull('odds_api_sport_key');
    }

    public function scopeHasApiSportKeyAndRunning($query) {
        return $query->hasApiSportKey('odds_api_sport_key')->running();
    }

    public function scopeNoApiSportKeyAndRunning($query) {
        return $query->whereNull('odds_api_sport_key')->running();
    }

    public function scopeHasOpenForBettingGames($query) {
        return $query->whereHas('games', function($game){
            return $game->openForBetting();
        });
    }

    public function apiStatusBadge(): Attribute
    {
        return new Attribute(
            get: fn () => $this->apiStatusBadgeData(),
        );
    }

    public function apiStatusBadgeData()
    {
        $html = '';
        if ($this->api_status == Status::ENABLE) {
            $html = '<span class="badge badge--success">' . trans('Yes') . '</span>';
        } else {
            $html = '<span class="badge badge--warning">' . trans('No') . '</span>';
        }
        return $html;
    }

}
