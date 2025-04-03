@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card custom--card">
                <div class="card-body">
                    <form action="{{ route('ipn.' . $deposit->gateway->alias) }}" method="POST">
                        @csrf
                        <ul class="list-group list-group-flush mb-3">
                            <li class="list-group-item d-flex flex-column flex-wrap px-0">
                                <small class="text-muted">@lang('Deposit Amount')</small>
                                <h6 class="m-0">{{ showAmount($deposit->amount) }}</h6>
                            </li>

                            <li class="list-group-item d-flex flex-column flex-wrap px-0">
                                <small class="text-muted">@lang('Payable Amount')</small>
                                <h5 class="m-0">{{ showAmount($deposit->final_amount, currencyFormat:false) }} {{ __($deposit->method_currency) }}</h5>
                            </li>
                        </ul>

                        <div class="text-end">
                            <button class="btn btn--xl btn--base w-100" id="btn-confirm" type="button">@lang('Pay Now')</button>
                            <script src="//js.paystack.co/v1/inline.js" data-key="{{ $data->key }}" data-email="{{ $data->email }}" data-amount="{{ round($data->amount) }}" data-currency="{{ $data->currency }}" data-ref="{{ $data->ref }}" data-custom-button="btn-confirm"></script>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
