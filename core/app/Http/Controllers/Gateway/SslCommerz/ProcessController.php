<?php
namespace App\Http\Controllers\Gateway\SslCommerz;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Lib\CurlRequest;
class ProcessController extends Controller{

    public static function process($deposit){
        $parameters = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $postData                    = array();
        $alias = $deposit->gateway->alias;
        $postData['store_id']        = $parameters->store_id;
        $postData['store_passwd']    = $parameters->store_password;
        $postData['total_amount']    = $deposit->final_amount;
        $postData['currency']        = $deposit->method_currency;
        $postData['tran_id']         = $deposit->trx;
        $postData['success_url']     = route('ipn.'.$alias);
        $postData['fail_url']        = $deposit->failed_url;
        $postData['cancel_url']      = $deposit->failed_url;
        $postData['emi_option'] = "0";

        if(auth()->check()){
            $user = auth()->user();
            $postData['cus_name']  = $user->fullname;
            $postData['cus_email'] = $user->email;
            $postData['cus_phone'] = $user->phone;
        }

        $paymentUrl = "https://securepay.sslcommerz.com/gwprocess/v3/api.php";
        // $paymentUrl = "https://sandbox.sslcommerz.com/gwprocess/v3/api.php";
        $response = CurlRequest::curlPostContent($paymentUrl, $postData);
        $response = json_decode($response);

        if(!$response || !@$response->status){
            $send['error'] = true;
            $send['message'] = 'Something went wrong';
            return json_encode($send);
        }

        if($response->status != 'SUCCESS'){
            $send['error'] = true;
            $send['message'] = 'Something went wrong';
            return json_encode($send);
        }
        $send['redirect']     = true;
        $send['redirect_url'] = $response->redirectGatewayURL;
        return json_encode($send);
    }

    public function ipn(Request $request){
        $track = $request->tran_id;
        $status = $request->status;
        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();
        if ($status == 'VALID' && @$deposit->status == Status::PAYMENT_INITIATE) {
            if (isset($_POST) && isset($_POST['verify_sign']) && isset($_POST['verify_key'])) {
                $preDefineKey = explode(',', $_POST['verify_key']);
                $newData = array();
                if (!empty($preDefineKey)) {
                    foreach ($preDefineKey as $value) {
                        if (isset($_POST[$value])) {
                            $newData[$value] = ($_POST[$value]);
                        }
                    }
                }
                $parameters = json_decode($deposit->gatewayCurrency()->gateway_parameter);

                $newData['store_passwd'] = md5($parameters->store_password);

                ksort($newData);
                $hashString = "";
                foreach ($newData as $key => $value) {$hashString .= $key . '=' . ($value) . '&';}
                $hashString = rtrim($hashString, '&');
                if (md5($hashString) == $_POST['verify_sign']) {
                    $input  = $request->except('method');
                    $ssltxt = "";
                    foreach ($input as $key => $value) {
                        $ssltxt .= "$key : $value <br>";
                    }
                    PaymentController::userDataUpdate($deposit);
                    $notify[] = ['success', 'Payment captured successfully'];
                    return redirect($deposit->success_url)->withNotify($notify);
                }
            }
        }
        $notify[] = ['error','Invalid request'];
        return redirect($deposit->failed_url)->withNotify($notify);
    }
}
