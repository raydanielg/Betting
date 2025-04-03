<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\ReferralSetting;use Illuminate\Http\Request;

class ReferralSettingsController extends Controller {
    public function index() {
        $pageTitle = 'Referral Setting';
        $levels    = ReferralSetting::get();

        return view('admin.referral', compact('pageTitle', 'levels'));
    }

    public function store(Request $request) {
        $request->validate([
            'level'           => 'required|array|min:1',
            'level.*'         => 'required|integer|min:1',
            'percent'         => 'required|array|min:1',
            'percent.*'       => 'required|numeric|gt:0|regex:/^\d+(\.\d{1,2})?$/',
            'commission_type' => 'required|in:deposit,bet,win',
        ], [
            'level.required'     => 'Minimum one level field is required',
            'level.*.required'   => 'Minimum one level value is required',
            'level.*.integer'    => 'Provide integer number as level',
            'level.*.min'        => 'Level should be grater than 0',
            'percent.required'   => 'Minimum one percentage field is required',
            'percent.*.required' => 'Minimum one percentage value is required',
            'percent.*.integer'  => 'Provide integer number as percentage',
            'percent.*.min'      => 'Percentage should be grater than 0',
        ]);

        ReferralSetting::where('commission_type', $request->commission_type)->delete();

        for ($i = 0; $i < count($request->level); $i++) {
            $referral                  = new ReferralSetting();
            $referral->level           = $request->level[$i];
            $referral->percent         = $request->percent[$i];
            $referral->commission_type = $request->commission_type;
            $referral->save();
        }

        $notify[] = ['success', 'Referral setting stored successfully'];
        return back()->withNotify($notify);
    }

    public function updateStatus($type) {
        $generalSetting = gs();

        if (@$generalSetting->$type == Status::ENABLE) {
            @$generalSetting->$type = Status::DISABLE;
            $generalSetting->save();
        } else if (@$generalSetting->$type == Status::DISABLE) {
            @$generalSetting->$type = Status::ENABLE;
            $generalSetting->save();
        } else {
            $notify[] = ['error', 'Something Wrong'];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Referral setting stored successfully'];
        return back()->withNotify($notify);
    }
}
