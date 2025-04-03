<?php

namespace App\Http\Controllers\Gateway\StripeJs;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Session;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Stripe;


class ProcessController extends Controller
{

    public static function process($deposit)
    {
        $StripeJSAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $val['key'] = $StripeJSAcc->publishable_key;
        $val['name'] = auth()->user()->username;
        $val['description'] = "Payment with Stripe";
        $val['amount'] = round($deposit->final_amount,2) * 100;
        $val['currency'] = $deposit->method_currency;
        $send['val'] = $val;


        $alias = $deposit->gateway->alias;

        $send['src'] = "https://checkout.stripe.com/checkout.js";
        $send['view'] = 'user.payment.' . $alias;
        $send['method'] = 'post';
        $send['url'] = route('ipn.'.$deposit->gateway->alias).($deposit->is_web ? '?trx='.encrypt($deposit->trx) : '');
        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        try{
            $track = $request->trx ? decrypt($request->trx) : Session::get('Track');
            $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
            abort_if(!$deposit,404);
        }catch(\Exception $e){
            abort(500);
        }

        if ($deposit->status == Status::PAYMENT_SUCCESS) {
            $notify[] = ['error', 'Invalid request.'];
            return redirect()->away($deposit->failed_url)->withNotify($notify);
        }
        $StripeJSAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);


        Stripe::setApiKey($StripeJSAcc->secret_key);

        Stripe::setApiVersion("2020-03-02");

        try {
            $customer =  Customer::create([
                'email' => $request->stripeEmail,
                'source' => $request->stripeToken,
            ]);
        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }

        try {
            $charge = Charge::create([
                'customer' => $customer->id,
                'description' => 'Payment with Stripe',
                'amount' => round($deposit->final_amount,2) * 100,
                'currency' => $deposit->method_currency,
            ]);
        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }


        if ($charge['status'] == 'succeeded') {
            PaymentController::userDataUpdate($deposit);
            $notify[] = ['success', 'Payment captured successfully'];
            return redirect($deposit->success_url)->withNotify($notify);
        }else{
            $notify[] = ['error', 'Failed to process'];
            return back()->withNotify($notify);
        }
    }
}