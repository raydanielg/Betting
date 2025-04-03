<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpdateLog extends Model
{
    protected $casts = ['update_log'=>'object'];
}
