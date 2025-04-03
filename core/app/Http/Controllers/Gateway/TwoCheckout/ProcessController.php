<?php

namespace App\Http\Controllers\Gateway\TwoCheckout;

use App\Models\Deposit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Gateway\PaymentController;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $configuration = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $send['val'] = [
            'sid' => $configuration->merchant_code,
            'mode' => '2CO',
            'li_0_type' => 'product',
            'li_0_name' => $deposit->trx ?? gs('site_name'),
            'li_0_product_id' => "{$deposit->trx}",
            'li_0_price' => round($deposit->final_amount, 2),
            'li_0_quantity' => "1",
            'li_0_tangible' => "N",
            'currency_code' => $deposit->method_currency,
            'demo' => "Y",
        ];

        $send['view'] = 'user.payment.redirect';
        $send['method'] = 'post';
        $send['url'] = 'https://www.2checkout.com/checkout/purchase';
        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $deposit = Deposit::where('status',0)->where('trx',$request->li_0_product_id)->orderBy('id','desc')->first();
        if ($deposit) {
            $configuration = json_decode($deposit->gatewayCurrency()->gateway_parameter);
            $hash = strtoupper(md5($configuration->secret_key . $configuration->merchant_code . $request->order_number . round($deposit->final_amount,2)));
            if ($hash == $request->key) {
                PaymentController::userDataUpdate($deposit);
            }
        }
    }
}
