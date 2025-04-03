@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card custom--card">
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between flex-wrap px-0">
                            <span>@lang('You have to pay')</span>
                            <span class="fw-bold">{{ showAmount($deposit->final_amount) }} {{ __($deposit->method_currency) }}</span>
                        </li>

                        <li class="list-group-item d-flex justify-content-between flex-wrap px-0">
                            <span>@lang('You will get')</span>
                            <span class="fw-bold">{{ showAmount($deposit->amount) }}</span>
                        </li>
                    </ul>

                    <div class="text-end">
                        <form action="{{ $data->url }}" method="{{ $data->method }}" id="apymentForm">
                            <script src="{{ $data->src }}" class="stripe-button"
                                @foreach ($data->val as $key => $value)
                                data-{{ $key }}="{{ $value }}" @endforeach>
                            </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="https://js.stripe.com/v3/"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('button[type="submit"]').removeClass().addClass("btn btn--xl btn--base w-100 mt-2").text("Pay Now");
            $('button[type="submit"]').text("Pay Now");
        })(jQuery);
    </script>
@endpush
