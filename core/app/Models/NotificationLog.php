<?php

namespace App\Models;

use App\Traits\ApiQuery;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use ApiQuery;

    public function user(){
    	return $this->belongsTo(User::class);
    }
}
