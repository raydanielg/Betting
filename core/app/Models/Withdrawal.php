<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $casts = [
        'withdraw_information' => 'object'
    ];

    protected $hidden = [
        'withdraw_information'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function method()
    {
        return $this->belongsTo(WithdrawMethod::class, 'method_id');
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function(){
            $html = '';
            if($this->status == Status::PAYMENT_PENDING){
                $html = '<span class="badge badge--warning">'.trans('Pending').'</span>';
            }elseif($this->status == Status::PAYMENT_SUCCESS){
                $html = '<span><span class="badge badge--success">'.trans('Approved').'</span><br>'.diffForHumans($this->updated_at).'</span>';
            }elseif($this->status == Status::PAYMENT_REJECT){
                $html = '<span><span class="badge badge--danger">'.trans('Rejected').'</span><br>'.diffForHumans($this->updated_at).'</span>';
            }
            return $html;
        });
    }

    public function scopePending($query)
    {
        return $query->where('status', Status::PAYMENT_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', Status::PAYMENT_SUCCESS);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', Status::PAYMENT_REJECT);
    }
}
