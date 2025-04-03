<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Model;

class GatewayCurrency extends Model {

    protected $hidden = [
        'gateway_parameter',
    ];

    protected $casts = ['status' => 'boolean'];

    // Relation
    public function method() {
        return $this->belongsTo(Gateway::class, 'method_code', 'code');
    }

    public function currencyIdentifier() {
        return $this->name ?? $this->method->name . ' ' . $this->currency;
    }

    public function scopeBaseCurrency() {
        return $this->method->crypto == Status::ENABLE ? 'USD' : $this->currency;
    }

    public function scopeBaseSymbol() {
        return $this->method->crypto == Status::ENABLE ? '$' : $this->symbol;
    }

}
