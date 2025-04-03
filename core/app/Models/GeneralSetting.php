<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model {
    protected $casts = [
        'mail_config'           => 'object',
        'sms_config'            => 'object',
        'global_shortcodes'     => 'object',
        'socialite_credentials' => 'object',
        'ods_api_regions'       => 'array',
        'ods_api_markets'       => 'array',
    ];

    protected $hidden = ['email_template', 'mail_config', 'sms_config', 'system_info'];

    public function scopeSiteName($query, $pageTitle) {
        $pageTitle = empty($pageTitle) ? '' : ' - ' . $pageTitle;
        return $this->site_name . $pageTitle;
    }

    protected static function boot() {
        parent::boot();
        static::saved(function () {
            \Cache::forget('GeneralSetting');
        });
    }
}
