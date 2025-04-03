<?php

namespace App\Http\Controllers\Gateway\Blockchain;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Lib\CurlRequest;
use Illuminate\Http\Request;

class ProcessController extends Controller
{
    /*
     * Blockchain Pay Gateway
     */

    public static function process($deposit)
    {
        $blockchainAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);

        $url = "https://blockchain.info/ticker";
        $response = CurlRequest::curlContent($url);
        $res = json_decode($response);

        $btcrate = @$res->USD->last ?? 0;

        $usd = $deposit->final_amount;
        $btcamount = $usd / $btcrate;
        $btc = round($btcamount, 8);

        $deposit = Deposit::where('trx', $deposit->trx)->orderBy('id', 'DESC')->firstOrFail();


        if ($deposit->btc_amount == 0 || $deposit->btc_wallet == "") {
            $myApiKey = trim($blockchainAcc->api_key);
            $url = "https://api.blockchain.com/v3/exchange/deposits/BTC";
            $header = [
                "X-API-Token: $myApiKey"
            ];
            $response = CurlRequest::curlPostContent($url,header:$header);
            $response = json_decode($response);
            if (@$response->address == '') {
                $send['error'] = true;
                $send['message'] = 'BLOCKCHAIN API HAVING ISSUE. PLEASE TRY LATER. ' . @$response->message;
            } else {

                $sendto = $response->address;
                $deposit['btc_wallet'] = $sendto;
                $deposit['btc_amount'] = $btc;
                $deposit->update();
            }
        }
        $send['amount'] = $deposit->btc_amount;
        $send['sendto'] = $deposit->btc_wallet;
        $send['img'] = cryptoQR($deposit->btc_wallet);
        $send['currency'] = "BTC";
        $send['view'] = 'user.payment.crypto';
        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $track = $request->invoice_id;
        $value_in_btc = $request->value / 100000000;

        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();

        foreach ($_GET as $key => $value) {
            $details[$key] = $value;
        }
        $deposit->detail = $details;
        $deposit->save();

        if ($deposit->btc_amount == $value_in_btc && $request->address == $deposit->btc_wallet && $request->secret == "MySecret" && $request->confirmations > 2 && $deposit->status == Status::PAYMENT_INITIATE) {
            PaymentController::userDataUpdate($deposit);
        }
    }
}
