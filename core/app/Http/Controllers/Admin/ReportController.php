<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CommissionLog;
use App\Models\NotificationLog;
use App\Models\Transaction;
use App\Models\UserLogin;
use Illuminate\Http\Request;

class ReportController extends Controller {
    public function transaction(Request $request, $userId = null) {
        $pageTitle = 'Transaction Logs';

        $remarks = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::searchable(['trx', 'user:username'])->filter(['trx_type', 'remark'])->dateFilter()->orderBy('id', 'desc')->with('user');
        if ($userId) {
            $transactions = $transactions->where('user_id', $userId);
        }
        $transactions = $transactions->paginate(getPaginate());

        return view('admin.reports.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function loginHistory(Request $request) {
        $pageTitle = 'User Login History';
        $loginLogs = UserLogin::orderBy('id', 'desc')->searchable(['user:username'])->dateFilter()->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs'));
    }

    public function loginIpHistory($ip) {
        $pageTitle = 'Login by - ' . $ip;
        $loginLogs = UserLogin::where('user_ip', $ip)->orderBy('id', 'desc')->with('user')->paginate(getPaginate());
        return view('admin.reports.logins', compact('pageTitle', 'loginLogs', 'ip'));
    }

    public function notificationHistory(Request $request) {
        $pageTitle = 'Notification History';
        $logs      = NotificationLog::orderBy('id', 'desc')->searchable(['user:username'])->dateFilter()->with('user')->paginate(getPaginate());
        return view('admin.reports.notification_history', compact('pageTitle', 'logs'));
    }

    public function emailDetails($id) {
        $pageTitle = 'Email Details';
        $email     = NotificationLog::findOrFail($id);
        return view('admin.reports.email_details', compact('pageTitle', 'email'));
    }

    public function referralCommissions() {
        $pageTitle = 'Referral Commissions';
        $logs      = CommissionLog::searchable(['trx', 'byWho:username', 'toUser:username']);
        if (request()->type) {
            $type = request()->type;
            $logs = $logs->where('type', $type);
        }
        $logs = $logs->with(['byWho', 'toUser'])->orderBy('id', 'desc')->dateFilter()->paginate(getPaginate());
        return view('admin.reports.referral_commissions', compact('pageTitle', 'logs'));
    }
}
