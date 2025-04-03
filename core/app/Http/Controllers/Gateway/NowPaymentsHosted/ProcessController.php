<?php

namespace App\Http\Controllers\Gateway\NowPaymentsHosted;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Lib\CurlRequest;
use App\Models\Deposit;
use App\Models\Gateway;

class ProcessController extends Controller {
    public static function process($deposit) {
        $nowPaymentsAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $response       = CurlRequest::curlPostContent('https://api.nowpayments.io/v1/payment', json_encode([
            'price_amount'     => $deposit->final_amount,
            'price_currency'   => gs('cur_text'),
            'pay_currency'     => $deposit->method_currency,
            'ipn_callback_url' => route('ipn.NowPaymentsHosted'),
            'order_id'         => $deposit->trx,

        ]), [
            "x-api-key: $nowPaymentsAcc->api_key",
            'Content-Type: application/json',
        ]);
        $response = json_decode($response);

        if (!$response) {
            $send['error']   = true;
            $send['message'] = 'Some problem ocurred with api.';
            return json_encode($send);
        }
        if(!@$response->status){
            $send['error']   = true;
            $send['message'] = 'Invalid api key';
            return json_encode($send);
        }

        if ($deposit->btc_amount == 0 || $deposit->btc_wallet == "") {
            $sendto              = $response->pay_address;
            $deposit->btc_wallet = $sendto;
            $deposit->btc_amount    = $response->pay_amount;
            $deposit->update();
        }

        $send['amount']   = $deposit->btc_amount;
        $send['sendto']   = $deposit->btc_wallet;
        $send['img']      = cryptoQR($deposit->btc_wallet);
        $send['currency'] = $deposit->method_currency;
        $send['view']     = 'user.payment.crypto';
        return json_encode($send);

    }

   public function ipn() {
        if (isset($_SERVER['HTTP_X_NOWPAYMENTS_SIG']) && !empty($_SERVER['HTTP_X_NOWPAYMENTS_SIG'])) {
            $recived_hmac = $_SERVER['HTTP_X_NOWPAYMENTS_SIG'];
            $request_json = file_get_contents('php://input');
            $request_data = json_decode($request_json, true);
            ksort($request_data);
            $sorted_request_json = json_encode($request_data, JSON_UNESCAPED_SLASHES);
            if ($request_json !== false && !empty($request_json)) {
                $gateway    = Gateway::where('alias', 'NowPaymentsHosted')->first();
                $gatewayAcc = json_decode($gateway->gateway_parameters);
                $hmac       = hash_hmac("sha512", $sorted_request_json, trim($gatewayAcc->secret_key->value));
                if ($hmac == $recived_hmac) {
                    if ($request_data['payment_status']=='confirmed' || $request_data['payment_status']=='finished') {
                        if ($request_data['actually_paid'] == $request_data['pay_amount']) {
                            $deposit = Deposit::where('status', 0)->where('trx', $request_data['order_id'])->first();
                            if ($deposit) {
                                PaymentController::userDataUpdate($deposit);
                            }
                        }
                    }
                }
            }
        }
    }
}
