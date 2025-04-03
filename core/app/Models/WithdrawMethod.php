<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class WithdrawMethod extends Model
{
    use GlobalStatus;

    protected $casts = [
        'user_data' => 'object',
    ];

    public function form()
    {
        return $this->belongsTo(Form::class);
    }
}
