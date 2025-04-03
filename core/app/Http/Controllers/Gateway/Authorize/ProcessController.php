<?php

namespace App\Http\Controllers\Gateway\Authorize;

use App\Constants\Status;
use App\Models\Deposit;
use App\Http\Controllers\Gateway\PaymentController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use net\authorize\api\constants\ANetEnvironment;
use net\authorize\api\contract\v1\CreateTransactionRequest;
use net\authorize\api\contract\v1\CreditCardType;
use net\authorize\api\contract\v1\MerchantAuthenticationType;
use net\authorize\api\contract\v1\PaymentType;
use net\authorize\api\contract\v1\TransactionRequestType;
use net\authorize\api\controller\CreateTransactionController;


class ProcessController extends Controller
{

    public static function process($deposit)
    {
        $alias = $deposit->gateway->alias;
        $send['track'] = $deposit->trx;
        $send['view'] = 'user.payment.'.$alias;
        $send['method'] = 'post';
        $send['url'] = route('ipn.'.$alias);
        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $track = $request->trx ?? null;
        if (!$track) {
            $track = Session::get('Track');
        }
        $deposit = Deposit::where('trx', $track)->where('status',Status::PAYMENT_INITIATE)->orderBy('id', 'DESC')->first();

        $apiRequest = $deposit->is_web;

        if ($deposit->status == Status::PAYMENT_SUCCESS) {
            $notify[] = ['error', 'Invalid request.'];
            if ($apiRequest) return responseError('invalid_request', $notify);
            return redirect($deposit->failed_url)->withNotify($notify);
        }

        $request->validate([
            'cardNumber' => 'required',
            'cardExpiry' => 'required',
            'cardCVC' => 'required',
        ]);
        $cardNumber = str_replace(' ','',$request->cardNumber);
        $exp = str_replace(' ','',$request->cardExpiry);
        $cvc = $request->cardCVC;

        $credentials = json_decode($deposit->gatewayCurrency()->gateway_parameter);

        // Common setup for API credentials
        $merchantAuthentication = new MerchantAuthenticationType();
        $merchantAuthentication->setName($credentials->login_id);
        $merchantAuthentication->setTransactionKey($credentials->transaction_key);

        // Create the payment data for a credit card
        $creditCard = new CreditCardType();
        $creditCard->setCardNumber($cardNumber);
        $creditCard->setExpirationDate($exp);
        $creditCard->setCardCode($cvc);


        $paymentOne = new PaymentType();
        $paymentOne->setCreditCard($creditCard);

        // Create a transaction
        $transactionRequestType = new TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($deposit->final_amount);
        $transactionRequestType->setPayment($paymentOne);

        $transactionRequest = new CreateTransactionRequest();
        $transactionRequest->setMerchantAuthentication($merchantAuthentication);
        $transactionRequest->setRefId($deposit->trx);
        $transactionRequest->setTransactionRequest($transactionRequestType);

        $controller = new CreateTransactionController($transactionRequest);
        $response = $controller->executeWithApiResponse(ANetEnvironment::PRODUCTION);
        $response = $response->getTransactionResponse();

        if (($response != null) && ($response->getResponseCode() == "1")) {
            PaymentController::userDataUpdate($deposit);
            $notify[] = ['success', 'Payment captured successfully'];

            if($apiRequest) return responseSuccess('payment_success', $notify);

            return redirect($deposit->success_url)->withNotify($notify);
        }
        $notify[] = ['error','Something went wrong'];

        if($apiRequest) return responseError('payment_failed', $notify);
        
        return back()->withNotify($notify);

    }
}
