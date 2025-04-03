<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Category extends Model {
    use GlobalStatus;

    protected $casts = [
        'regions' => 'array'
    ];

    public function leagues() {
        return $this->hasMany(League::class);
    }

    public function teams() {
        return $this->hasMany(Team::class);
    }

    public function games() {
        return $this->hasManyThrough(Game::class, League::class);
    }


    public static function getGames($type) {
        return self::active()
            ->withCount(['games' => function ($game) use ($type) {
                $game->openForBetting()->$type()
                    ->hasActiveLeague()
                    ->hasActiveCategory();
            }])
            ->orderBy('name')
            ->get();

    }
}
