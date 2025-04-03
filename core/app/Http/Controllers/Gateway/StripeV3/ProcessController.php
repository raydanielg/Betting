<?php

namespace App\Http\Controllers\Gateway\StripeV3;

use App\Constants\Status;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class ProcessController extends Controller
{

    public static function process($deposit)
    {
        $StripeAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $alias = $deposit->gateway->alias;
        \Stripe\Stripe::setApiKey("$StripeAcc->secret_key");
        try {
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data'=>[
                        'unit_amount' => round($deposit->final_amount,2) * 100,
                        'currency' => "$deposit->method_currency",
                        'product_data'=>[
                            'name' => gs('site_name'),
                            'description' => 'Deposit with Stripe',
                            'images' => [siteLogo()],
                        ]
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'cancel_url' => $deposit->failed_url,
                'success_url' => $deposit->success_url,
            ]);
        } catch (\Exception $e) {
            $send['error'] = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }

        $send['view'] = 'user.payment.'.$alias;
        $send['session'] = $session;
        $send['StripeJSAcc'] = $StripeAcc;
        $deposit->btc_wallet = json_decode(json_encode($session))->id;
        $deposit->save();
        return json_encode($send);
    }


    public function ipn(Request $request)
    {
        $StripeAcc = GatewayCurrency::where('gateway_alias','StripeV3')->orderBy('id','desc')->first();
        $gateway_parameter = json_decode($StripeAcc->gateway_parameter);


        \Stripe\Stripe::setApiKey($gateway_parameter->secret_key);

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = $gateway_parameter->end_point; // main
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];


        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch(\UnexpectedValueException $e) {
            // Invalid payload
            http_response_code(400);
            exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            http_response_code(400);
            exit();
        }

        // Handle the checkout.session.completed event
        if ($event->type == 'checkout.session.completed') {
            $session = $event->data->object;
            $deposit = Deposit::where('btc_wallet',  $session->id)->orderBy('id', 'DESC')->first();

            if($deposit->status==Status::PAYMENT_INITIATE){
                PaymentController::userDataUpdate($deposit);
            }
        }
        http_response_code(200);
    }
}
