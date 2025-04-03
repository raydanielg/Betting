<?php

namespace App\Http\Controllers\Gateway\Binance;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Lib\CurlRequest;
use App\Models\Deposit;
use App\Models\Gateway;
use Illuminate\Support\Str;

class ProcessController extends Controller
{
    public static function process($deposit)
    {
        $binanceAcc   = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $nonce = Str::random(32);
        $timestamp = round(microtime(true) * 1000);
        $request = array(
            "env" => array(
                "terminalType" => "APP"
            ),
            "merchantTradeNo" => $deposit->trx,
            "orderAmount" => $deposit->final_amount,
            "currency" => $deposit->method_currency,
            "goods" => array(
                "goodsType" =>  "01",
                "goodsCategory" => "Z000",
                "referenceGoodsId" =>  $deposit->trx,
                "goodsName" => "Deposit to " . gs('site_name'),
                "goodsDetail" => "Deposit to " . gs('site_name')
            ),
        );
        $jsonRequest        = json_encode($request);
        $payload            = $timestamp . "\n" . $nonce . "\n" . $jsonRequest . "\n";
        $apiKey             = $binanceAcc->api_key;
        $secretKey          = $binanceAcc->secret_key;
        $signature          = strtoupper(hash_hmac('SHA512', $payload, $secretKey));

        $headers            = array();
        $headers[]          = "Content-Type: application/json";
        $headers[]          = "BinancePay-Timestamp: $timestamp";
        $headers[]          = "BinancePay-Nonce: $nonce";
        $headers[]          = "BinancePay-Certificate-SN: $apiKey";
        $headers[]          = "BinancePay-Signature: $signature";

        $result = CurlRequest::curlPostContent('https://bpay.binanceapi.com/binancepay/openapi/v2/order',$request,$headers);

        $result = json_decode($result);

        if (@$result->status == "SUCCESS") {
            $send['redirect'] = true;
            $send['redirect_url'] = @$result->data->checkoutUrl;
        } else {
            $send['error']     = true;
            $send['message'] = (@$result->msg) ? @$result->errorMessage : 'Something went wrong';
        }
        return json_encode($send);
    }

    public function ipn(){
        $binance = Gateway::where('alias', 'Binance')->first();
        $binanceAcc   = json_decode($binance->gateway_parameters);
        $deposits = Deposit::initiated()->where('method_code', $binance->code)->where('created_at','>=',now()->subHours(24))->orderBy('last_cron')->limit(10)->get();
        $apiKey    = $binanceAcc->api_key->value;
        $secretKey = $binanceAcc->secret_key->value;
        $url = "https://bpay.binanceapi.com/binancepay/openapi/v2/order/query";

        foreach ($deposits as $deposit) {
            $deposit->last_cron = time();
            $deposit->save();
            $nonce = Str::random(32);
            $timestamp = round(microtime(true) * 1000);

            $request = array(
                "merchantTradeNo" => $deposit->trx,
            );

            $jsonRequest       = json_encode($request);
            $payload            = $timestamp . "\n" . $nonce . "\n" . $jsonRequest . "\n";
            $signature          = strtoupper(hash_hmac('SHA512', $payload, $secretKey));
            $headers            = array();
            $headers[]          = "Content-Type: application/json";
            $headers[]          = "BinancePay-Timestamp: $timestamp";
            $headers[]          = "BinancePay-Nonce: $nonce";
            $headers[]          = "BinancePay-Certificate-SN: $apiKey";
            $headers[]          = "BinancePay-Signature: $signature";

            $result = CurlRequest::curlPostContent($url,$request,$headers);
            $result = json_decode($result);
            if (@$result->data && @$result->data->status == "PAID" && @$result->data->orderAmount == $deposit->final_amount) {
                PaymentController::userDataUpdate($deposit);
            }

        }
    }

}
