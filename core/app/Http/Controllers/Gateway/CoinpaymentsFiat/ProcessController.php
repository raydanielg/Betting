<?php

namespace App\Http\Controllers\Gateway\CoinpaymentsFiat;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    /*
     * CoinPaymentHosted Gateway
     */

    public static function process($deposit)
    {
        $coinpayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);

        $val['merchant'] = $coinpayAcc->merchant_id;
        $val['item_name'] = 'Payment to ' . gs('site_name');
        $val['currency'] = $deposit->method_currency;
        $val['currency_code'] = "$deposit->method_currency";
        $val['amountf'] = round($deposit->final_amount,2);
        $val['ipn_url'] =  route('ipn.'.$deposit->gateway->alias);
        $val['custom'] = "$deposit->trx";
        $val['amount'] = round($deposit->final_amount,2);
        $val['return'] = $deposit->success_url;
        $val['cancel_return'] = $deposit->failed_url;
        $val['notify_url'] = route('ipn.'.$deposit->gateway->alias);
        $val['success_url'] = $deposit->success_url;
        $val['cancel_url'] = $deposit->failed_url;
        $val['custom'] = $deposit->trx;
        $val['cmd'] = '_pay_simple';
        $val['want_shipping'] = 0;
        $send['val'] = $val;
        $send['view'] = 'user.payment.redirect';
        $send['method'] = 'post';
        $send['url'] = 'https://www.coinpayments.net/index.php';

        return json_encode($send);
    }

    public function ipn(Request $request)
    {

        $track = $request->custom;
        $status = $request->status;
        $amount1 = floatval($request->amount1);
        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();

        if ($status >= 100 || $status == 2) {
            $coinPayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
            if ($deposit->method_currency == $request->currency1 && round($deposit->final_amount,2) <= $amount1  && $coinPayAcc->merchant_id == $request->merchant && $deposit->status == Status::PAYMENT_INITIATE) {
                PaymentController::userDataUpdate($deposit);
            }
        }
    }
}
