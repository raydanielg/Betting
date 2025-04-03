<?php

namespace App\Http\Controllers\Gateway\Cashmaal;

use App\Constants\Status;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    /*
     * Cashmaal
     */

    public static function process($deposit)
    {
    	$cashmaal = $deposit->gatewayCurrency();
    	$param = json_decode($cashmaal->gateway_parameter);
        $val['pay_method'] = " ";
        $val['amount'] = getAmount($deposit->final_amount);
        $val['currency'] = $cashmaal->currency;
        $val['succes_url'] = $deposit->success_url;
        $val['cancel_url'] = $deposit->failed_url;
        $val['client_email'] = auth()->user()->email;
        $val['web_id'] = $param->web_id;
        $val['order_id'] = $deposit->trx;
        $val['addi_info'] = "Deposit";
        $send['url'] = 'https://www.cashmaal.com/Pay/';
        $send['method'] = 'post';
        $send['view'] = 'user.payment.redirect';
        $send['val'] = $val;
        return json_encode($send);
    }

    public function ipn(Request $request)
    {

    	$gateway = GatewayCurrency::where('gateway_alias','Cashmaal')->where('currency',$request->currency)->first();
        $IPN_key=json_decode($gateway->gateway_parameter)->ipn_key;
        $web_id=json_decode($gateway->gateway_parameter)->web_id;


        $deposit = Deposit::where('trx', $_POST['order_id'])->orderBy('id', 'DESC')->first();
        if ($request->ipn_key != $IPN_key && $web_id != $request->web_id) {
        	$notify[] = ['error','Data invalid'];
        	return redirect($deposit->failed_url)->withNotify($notify);
        }

        if ($request->status == 2) {
        	$notify[] = ['info','Payment in pending'];
        	return redirect($deposit->failed_url)->withNotify($notify);
        }

        if ($request->status != 1) {
        	$notify[] = ['error','Data invalid'];
        	return redirect($deposit->failed_url)->withNotify($notify);
        }

		if($_POST['status'] == 1 && $deposit->status == Status::PAYMENT_INITIATE && $_POST['currency'] == $deposit->method_currency ){
			PaymentController::userDataUpdate($deposit);
            $notify[] = ['success', 'Transaction is successful'];
		}else{
			$notify[] = ['error','Payment failed'];
        	return redirect($deposit->failed_url)->withNotify($notify);
		}
		return redirect($deposit->success_url)->withNotify($notify);
    }
}
