<?php

namespace App\Http\Controllers\Gateway\Checkout;

use App\Models\Deposit;
use App\Lib\CurlRequest;
use App\Constants\Status;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;

class ProcessController extends Controller
{

    public static function process($deposit)
    {
        $alias = $deposit->gateway->alias;

        $send['track']  = $deposit->trx;
        $send['view']   = 'user.payment.' . $alias;
        $send['method'] = 'post';
        $send['url']    = route('ipn.' . $alias);
        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $track   = $request->track;
        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();

        if ($deposit->status == Status::PAYMENT_SUCCESS) {
            $notify[] = ['error', 'Invalid request.'];
            return redirect($deposit->failed_url)->withNotify($notify);
        }
        $request->validate([
            'cardNumber' => 'required',
            'cardExpiry' => 'required',
            'cardCVC'    => 'required',
        ]);

        $checkoutAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $processingChannelId = $checkoutAcc->processing_channel_id;
        $publicKey   = $checkoutAcc->public_key;
        $secretKey   = $checkoutAcc->secret_key;
        $cardExpire  = explode('/', $request->cardExpiry);



        $data = array(
            'type'         => 'card',
            'number'       => str_replace(' ', '', $request->cardNumber),
            'expiry_month' => $cardExpire[0],
            'expiry_year'  => $cardExpire[1],
            'cvv'          => $request->cardCVC,
        );

        $tokenUrl = 'https://api.checkout.com/tokens';
        $paymentUrl = "https://api.checkout.com/payments";

        $response = CurlRequest::curlPostContent($tokenUrl, json_encode($data), array(
            'Content-Type: application/json',
            'Authorization: ' . $publicKey,
        ));

        $response = json_decode($response);

        if (@$response->token) {
            $cardToken      = $response->token;
        } else {

            foreach ($response->error_codes ?? [] as $error) {
                $notify[] = ['error', $error];
            }
            if (!isset($notify)) {
                $notify[] = ['error','Something went wrong'];
            }
            return redirect($deposit->failed_url)->withNotify($notify);
        }

        $data = array(
            "source"                => array(
                "type"  => "token",
                "token" => $cardToken,
            ),
            "amount"                => (int)(round($deposit->final_amount, 2) *100),
            "currency"              => $deposit->method_currency,
            "processing_channel_id" => $processingChannelId,
            "reference"             => $deposit->trx,
            "capture"               => true,
            "customer"              => array(
                "email" => @$deposit->user->email,
                "name"  => @$deposit->user->fullname,
                "phone" => array(
                    "number"       => @$deposit->user->mobileNumber,
                ),
            )
        );



        $data = json_encode($data);

        $result = CurlRequest::curlPostContent($paymentUrl, $data, array(
            "Authorization: $secretKey",
            "Content-Type: application/json",
            "Content-Length: " . strlen($data))
        );

        $result = json_decode($result);




        if ((@$result->status == 'Authorized' || @$result->status == 'Captured')) {
            PaymentController::userDataUpdate($deposit);
            $notify[] = ['success', 'Payment captured successfully'];
            return redirect($deposit->success_url)->withNotify($notify);
        }

        $notify[] = ['error', 'Payment Failed'];
        return back()->withNotify($notify);
    }
}
