<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{

    protected $casts = [
        'detail' => 'object'
    ];

    protected $hidden = ['detail'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function gateway()
    {
        return $this->belongsTo(Gateway::class, 'method_code', 'code');
    }

    public function methodName(){
        if ($this->method_code < 5000) {
            $methodName = @$this->gatewayCurrency()->name;
        }else{
            $methodName = 'Google Pay';
        }
        return $methodName;
    }

    public function statusBadge(): Attribute
    {
        return new Attribute(function(){
            $html = '';
            if($this->status == Status::PAYMENT_PENDING){
                $html = '<span class="badge badge--warning">'.trans('Pending').'</span>';
            }
            elseif($this->status == Status::PAYMENT_SUCCESS && $this->method_code >= 1000 && $this->method_code <= 5000){
                $html = '<span><span class="badge badge--success">'.trans('Approved').'</span><br>'.diffForHumans($this->updated_at).'</span>';
            }
            elseif($this->status == Status::PAYMENT_SUCCESS && ($this->method_code < 1000 || $this->method_code >= 5000)){
                $html = '<span class="badge badge--success">'.trans('Succeed').'</span>';
            }
            elseif($this->status == Status::PAYMENT_REJECT){
                $html = '<span><span class="badge badge--danger">'.trans('Rejected').'</span><br>'.diffForHumans($this->updated_at).'</span>';
            }else{
                $html = '<span class="badge badge--dark">'.trans('Initiated').'</span>';
            }
            return $html;
        });
    }

    public function userStatusBadge(): Attribute
    {
        return new Attribute(function(){
            $html = '';
            if($this->status == Status::PAYMENT_PENDING){
                $html = '<span class="badge badge--warning">'.trans('Pending').'</span>';
            }
            elseif($this->status == Status::PAYMENT_SUCCESS){
                $html = '<span><span class="badge badge--success">'.trans('Completed').'</span>';
            }
            elseif($this->status == Status::PAYMENT_REJECT){
                $html = '<span><span class="badge badge--danger">'.trans('Rejected').'</span>';
            }else{
                $html = '<span class="badge badge--dark">'.trans('Initiated').'</span>';
            }
            return $html;
        });
    }

    // scope
    public function gatewayCurrency()
    {
        return GatewayCurrency::where('method_code', $this->method_code)->where('currency', $this->method_currency)->first();
    }

    public function baseCurrency()
    {
        return @$this->gateway->crypto == Status::ENABLE ? 'USD' : $this->method_currency;
    }

    public function scopePending($query)
    {
        return $query->where('method_code','>=',1000)->where('status', Status::PAYMENT_PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('method_code','>=',1000)->where('status', Status::PAYMENT_REJECT);
    }

    public function scopeApproved($query)
    {
        return $query->where('method_code','>=',1000)->where('method_code','<',5000)->where('status', Status::PAYMENT_SUCCESS);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', Status::PAYMENT_SUCCESS);
    }

    public function scopeInitiated($query)
    {
        return $query->where('status', Status::PAYMENT_INITIATE);
    }
}
