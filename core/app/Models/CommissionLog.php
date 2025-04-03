<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionLog extends Model {

    public function user() {
        return $this->belongsTo(User::class, 'to_id', 'id');
    }

    public function byWho() {
        return $this->belongsTo(User::class, 'from_id', 'id');
    }

    public function toUser() {
        return $this->belongsTo(User::class, 'to_id', 'id');
    }

    public function commissionType() {
        if($this->type == 'deposit') {
            return 'Deposit Commission';
        }elseif($this->type == 'bet') {
            return 'Bet Place Commission';
        }else{
            return 'Bet Win Commission';
        }
    }
}
