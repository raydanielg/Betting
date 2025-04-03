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
                    <form action="{{ $data->url }}" class="text-end" method="{{ $data->method }}">
                        <input name="hidden" type="hidden" custom="{{ $data->custom }}">
                        <script src="{{ $data->checkout_js }}" @foreach ($data->val as $key => $value)
                                data-{{ $key }}="{{ $value }}" @endforeach></script>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('input[type="submit"]').addClass("btn btn--xl btn--base w-100");
        })(jQuery);
    </script>
@endpush
