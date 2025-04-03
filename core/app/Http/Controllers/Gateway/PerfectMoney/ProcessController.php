<?php

namespace App\Http\Controllers\Gateway\PerfectMoney;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;

class ProcessController extends Controller
{

    /*
     * Perfect Money Gateway
     */
    public static function process($deposit)
    {
        $gateway_currency = $deposit->gatewayCurrency();

        $perfectAcc = json_decode($gateway_currency->gateway_parameter);

        $val['PAYEE_ACCOUNT'] = trim($perfectAcc->wallet_id);
        $val['PAYEE_NAME'] = gs('site_name');
        $val['PAYMENT_ID'] = "$deposit->trx";
        $val['PAYMENT_AMOUNT'] = round($deposit->final_amount,2);
        $val['PAYMENT_UNITS'] = "$deposit->method_currency";

        $val['STATUS_URL'] = route('ipn.'.$deposit->gateway->alias);
        $val['PAYMENT_URL'] = $deposit->success_url;
        $val['PAYMENT_URL_METHOD'] = 'POST';
        $val['NOPAYMENT_URL'] = $deposit->failed_url;
        $val['NOPAYMENT_URL_METHOD'] = 'POST';
        $val['SUGGESTED_MEMO'] = auth()->user()->username;
        $val['BAGGAGE_FIELDS'] = 'IDENT';


        $send['val'] = $val;
        $send['view'] = 'user.payment.redirect';
        $send['method'] = 'post';
        $send['url'] = 'https://perfectmoney.is/api/step1.asp';

        return json_encode($send);
    }
    public function ipn()
    {
        $deposit = Deposit::where('trx', $_POST['PAYMENT_ID'])->orderBy('id', 'DESC')->first();
        $pmAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $passphrase = strtoupper(md5($pmAcc->passphrase));

        define('ALTERNATE_PHRASE_HASH', $passphrase);
        define('PATH_TO_LOG', '/somewhere/out/of/document_root/');
        $string =
            $_POST['PAYMENT_ID'] . ':' . $_POST['PAYEE_ACCOUNT'] . ':' .
            $_POST['PAYMENT_AMOUNT'] . ':' . $_POST['PAYMENT_UNITS'] . ':' .
            $_POST['PAYMENT_BATCH_NUM'] . ':' .
            $_POST['PAYER_ACCOUNT'] . ':' . ALTERNATE_PHRASE_HASH . ':' .
            $_POST['TIMESTAMPGMT'];

        $hash = strtoupper(md5($string));
        $hash2 = $_POST['V2_HASH'];

        if ($hash == $hash2) {

            foreach ($_POST as $key => $value) {
                $details[$key] = $value;
            }
            $deposit->detail = $details;
            $deposit->save();

            $amount = $_POST['PAYMENT_AMOUNT'];
            $unit = $_POST['PAYMENT_UNITS'];
            if ($_POST['PAYEE_ACCOUNT'] == $pmAcc->wallet_id && $unit == $deposit->method_currency && $amount == round($deposit->final_amount,2) && $deposit->status == Status::PAYMENT_INITIATE) {
                //Update User Data
                PaymentController::userDataUpdate($deposit);
            }
        }
    }
}
