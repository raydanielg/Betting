<?php

namespace App\Http\Controllers\Gateway\Mollie;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;
use Mollie\Laravel\Facades\Mollie;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $general =  gs();
        $mollieAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);

        config(['mollie.key' => trim($mollieAcc->api_key)]);

        try {
            $payment = Mollie::api()->payments->create([
                'amount' => [
                    'currency' => "$deposit->method_currency",
                    'value' => ''.sprintf('%0.2f', round($deposit->final_amount,2)).'',
                ],
                'description' => "Payment To $general->site_name Account",
                'redirectUrl' => route('ipn.' . $deposit->gateway->alias) . '?deposit_id=' . encrypt($deposit->id),
                'metadata' => [
                    "order_id" => $deposit->trx,
                ],
            ]);
            $payment = Mollie::api()->payments->get($payment->id);
        } catch (\Exception $e) {
            $send['error'] = true;
            $send['message'] = $e->getMessage();
            return json_encode($send);
        }

        $deposit->btc_wallet = $payment->id;
        $deposit->save();

        $send['redirect'] = true;
        $send['redirect_url'] = $payment->getCheckoutUrl();

        return json_encode($send);
    }

    public function ipn()
    {
        $depositId = decrypt($_GET['deposit_id']);

        if ($depositId ==  null) {
            return to_route('home');
        }

        $deposit = Deposit::where('id', $depositId)->where('status', Status::PAYMENT_INITIATE)->first();

        $paymentId = $deposit->btc_wallet;

        $mollieAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        config(['mollie.key' => trim($mollieAcc->api_key)]);
        $payment = Mollie::api()->payments->get($paymentId);
        $deposit->detail = $payment->details;
        $deposit->save();

        if ($payment->status == "paid") {
            PaymentController::userDataUpdate($deposit);
            $notify[] = ['success', 'Transaction was successful'];
            return redirect($deposit->success_url)->withNotify($notify);
        }

        $notify[] = ['error', 'Invalid request'];
        return redirect(to: $deposit->failed_url)->withNotify($notify);
    }
}