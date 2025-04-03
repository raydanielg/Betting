@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card custom--card">
                <div class="card-body">
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
                        <button class="btn btn--xl btn--base w-100" id="btn-confirm" type="button" onClick="payWithRave()">@lang('Pay Now')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
    <script>
        "use strict"
        var btn = document.querySelector("#btn-confirm");
        btn.setAttribute("type", "button");
        const API_publicKey = "{{ $data->API_publicKey }}";

        function payWithRave() {
            var x = getpaidSetup({
                PBFPubKey: API_publicKey,
                customer_email: "{{ $data->customer_email }}",
                amount: "{{ $data->amount }}",
                customer_phone: "{{ $data->customer_phone }}",
                currency: "{{ $data->currency }}",
                txref: "{{ $data->txref }}",
                onclose: function() {},
                callback: function(response) {
                    var txref = response.tx.txRef;
                    var status = response.tx.status;
                    var chargeResponse = response.tx.chargeResponseCode;
                    if (chargeResponse == "00" || chargeResponse == "0") {
                        window.location = '{{ url('ipn/flutterwave') }}/' + txref + '/' + status;
                    } else {
                        window.location = '{{ url('ipn/flutterwave') }}/' + txref + '/' + status;
                    }
                }
            });
        }
    </script>
@endpush
