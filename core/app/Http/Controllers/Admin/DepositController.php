<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Models\Deposit;
use App\Models\Gateway;
use Illuminate\Http\Request;

class DepositController extends Controller {
    public function pending($userId = null) {
        $pageTitle = 'Pending Deposits';
        $deposits  = $this->depositData('pending', userId: $userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function approved($userId = null) {
        $pageTitle = 'Approved Deposits';
        $deposits  = $this->depositData('approved', userId: $userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function successful($userId = null) {
        $pageTitle = 'Successful Deposits';
        $deposits  = $this->depositData('successful', userId: $userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function rejected($userId = null) {
        $pageTitle = 'Rejected Deposits';
        $deposits  = $this->depositData('rejected', userId: $userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function initiated($userId = null) {
        $pageTitle = 'Initiated Deposits';
        $deposits  = $this->depositData('initiated', userId: $userId);
        return view('admin.deposit.log', compact('pageTitle', 'deposits'));
    }

    public function deposit($userId = null) {
        $pageTitle   = 'Deposit History';
        $depositData = $this->depositData($scope = null, $summary = true, userId: $userId);
        $deposits    = $depositData['data'];
        $summary     = $depositData['summary'];
        $successful  = $summary['successful'];
        $pending     = $summary['pending'];
        $rejected    = $summary['rejected'];
        $initiated   = $summary['initiated'];
        return view('admin.deposit.log', compact('pageTitle', 'deposits', 'successful', 'pending', 'rejected', 'initiated'));
    }

    protected function depositData($scope = null, $summary = false, $userId = null) {
        if ($scope) {
            $deposits = Deposit::$scope()->with(['user', 'gateway']);
        } else {
            $deposits = Deposit::with(['user', 'gateway']);
        }

        if ($userId) {
            $deposits = $deposits->where('user_id', $userId);
        }
        $deposits = $deposits->searchable(['trx', 'user:username'])->dateFilter();

        $request = request();

        if ($request->method) {
            if ($request->method != Status::GOOGLE_PAY) {
                $method   = Gateway::where('alias', $request->method)->firstOrFail();
                $deposits = $deposits->where('method_code', $method->code);
            } else {
                $deposits = $deposits->where('method_code', Status::GOOGLE_PAY);
            }
        }

        if (!$summary) {
            return $deposits->orderBy('id', 'desc')->paginate(getPaginate());
        } else {
            $successful = clone $deposits;
            $pending    = clone $deposits;
            $rejected   = clone $deposits;
            $initiated  = clone $deposits;

            $successfulSummary = $successful->where('status', Status::PAYMENT_SUCCESS)->sum('amount');
            $pendingSummary    = $pending->where('status', Status::PAYMENT_PENDING)->sum('amount');
            $rejectedSummary   = $rejected->where('status', Status::PAYMENT_REJECT)->sum('amount');
            $initiatedSummary  = $initiated->where('status', Status::PAYMENT_INITIATE)->sum('amount');

            return [
                'data'    => $deposits->orderBy('id', 'desc')->paginate(getPaginate()),
                'summary' => [
                    'successful' => $successfulSummary,
                    'pending'    => $pendingSummary,
                    'rejected'   => $rejectedSummary,
                    'initiated'  => $initiatedSummary,
                ],
            ];
        }
    }

    public function details($id) {
        $deposit   = Deposit::where('id', $id)->with(['user', 'gateway'])->firstOrFail();
        $pageTitle = $deposit->user->username . ' requested ' . showAmount($deposit->amount);
        $details   = ($deposit->detail != null) ? json_encode($deposit->detail) : null;
        return view('admin.deposit.detail', compact('pageTitle', 'deposit', 'details'));
    }

    public function approve($id) {
        $deposit = Deposit::where('id', $id)->where('status', Status::PAYMENT_PENDING)->firstOrFail();

        PaymentController::userDataUpdate($deposit, true);

        $notify[] = ['success', 'Deposit request approved successfully'];

        return to_route('admin.deposit.pending')->withNotify($notify);
    }

    public function reject(Request $request) {
        $request->validate([
            'id'      => 'required|integer',
            'message' => 'required|string|max:255',
        ]);
        $deposit = Deposit::where('id', $request->id)->where('status', Status::PAYMENT_PENDING)->firstOrFail();

        $deposit->admin_feedback = $request->message;
        $deposit->status         = Status::PAYMENT_REJECT;
        $deposit->save();

        notify($deposit->user, 'DEPOSIT_REJECT', [
            'method_name'       => $deposit->methodName(),
            'method_currency'   => $deposit->method_currency,
            'method_amount'     => showAmount($deposit->final_amount, currencyFormat: false),
            'amount'            => showAmount($deposit->amount, currencyFormat: false),
            'charge'            => showAmount($deposit->charge, currencyFormat: false),
            'rate'              => showAmount($deposit->rate, currencyFormat: false),
            'trx'               => $deposit->trx,
            'rejection_message' => $request->message,
        ]);

        $notify[] = ['success', 'Deposit request rejected successfully'];
        return to_route('admin.deposit.pending')->withNotify($notify);

    }
}
