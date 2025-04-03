<?php

namespace App\Http\Controllers\Gateway\Skrill;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;

class ProcessController extends Controller
{

    /*
     * Skrill Gateway
     */
    public static function process($deposit)
    {
        $general = gs();
        $skrillAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);


        $val['pay_to_email'] = trim($skrillAcc->pay_to_email);
        $val['transaction_id'] = "$deposit->trx";

        $val['return_url'] = $deposit->success_url;
        $val['return_url_text'] = "Return $general->site_name";
        $val['cancel_url'] = $deposit->failed_url;
        $val['status_url'] = route('ipn.'.$deposit->gateway->alias);
        $val['language'] = 'EN';
        $val['amount'] = round($deposit->final_amount,2);
        $val['currency'] = "$deposit->method_currency";
        $val['detail1_description'] = "$general->site_name";
        $val['detail1_text'] = "Pay To $general->site_name";
        $val['logo_url'] = siteLogo();

        $send['val'] = $val;
        $send['view'] = 'user.payment.redirect';
        $send['method'] = 'post';
        $send['url'] = 'https://www.moneybookers.com/app/payment.pl';
        return json_encode($send);
    }


    public function ipn()
    {
        $deposit = Deposit::where('trx', $_POST['transaction_id'])->orderBy('id', 'DESC')->first();
        $skrillrAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $concatFields = $_POST['merchant_id']
            . $_POST['transaction_id']
            . strtoupper(md5($skrillrAcc->secret_key))
            . $_POST['mb_amount']
            . $_POST['mb_currency']
            . $_POST['status'];

        if (strtoupper(md5($concatFields)) == $_POST['md5sig'] && $_POST['status'] == 2 && $_POST['pay_to_email'] == $skrillrAcc->pay_to_email && $deposit->status = Status::PAYMENT_INITIATE) {
            PaymentController::userDataUpdate($deposit);
        }
    }
}
