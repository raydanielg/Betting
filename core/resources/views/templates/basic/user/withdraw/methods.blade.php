@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="card custom--card">
        <div class="card-body">
            <form action="{{ route('user.withdraw.money') }}" method="post" class="withdraw-form disableSubmission">
                @csrf
                <div class="gateway-card">
                    <div class="row justify-content-center gy-sm-4 gy-3">
                        <div class="col-lg-6">
                            <h5 class="card-title mb-3">@lang('Choose Withdrawal Method')</h5>
                            <div class="payment-system-list is-scrollable gateway-outcome-list">
                                @foreach ($withdrawMethod as $data)
                                    <label for="{{ titleToKey($data->name) }}" class="payment-item gateway-outcome">
                                        <div class="payment-item__info">
                                            <span class="payment-item__check"></span>
                                            <span class="payment-item__name">{{ __($data->name) }}</span>
                                        </div>
                                        <div class="payment-item__thumb">
                                            <img class="payment-item__thumb-img" src="{{ getImage(getFilePath('withdrawMethod') . '/' . $data->image) }}" alt="@lang('payment-thumb')">
                                        </div>
                                        <input class="payment-item__radio gateway-input" id="{{ titleToKey($data->name) }}" hidden data-gateway='@json($data)' type="radio" name="method_code" value="{{ $data->id }}" @checked(old('method_code', $loop->first) == $data->id) data-min-amount="{{ showAmount($data->min_limit) }}" data-max-amount="{{ showAmount($data->max_limit) }}">
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="payment-system-list p-3">
                                <div class="deposit-info">
                                    <div class="deposit-info__title">
                                        <p class="text mb-0">@lang('Amount')</p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <div class="deposit-info__input-group input-group">
                                            <span class="deposit-info__input-group-text">{{ gs('cur_sym') }}</span>
                                            <input type="text" class="form-control form--control amount" name="amount" placeholder="@lang('00.00')" value="{{ old('amount') }}" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="deposit-info">
                                    <div class="deposit-info__title">
                                        <p class="text has-icon"> @lang('Limit')</p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <p class="text"><span class="gateway-limit">@lang('0.00')</span> </p>
                                    </div>
                                </div>
                                <div class="deposit-info">
                                    <div class="deposit-info__title">
                                        <p class="text has-icon">@lang('Processing Charge')
                                            <span data-bs-toggle="tooltip" title="@lang('Processing charge for withdraw method')" class="proccessing-fee-info"><i class="las la-info-circle"></i> </span>
                                        </p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <p class="text">{{ gs('cur_sym') }}<span class="processing-fee">@lang('0.00')</span>
                                            {{ __(gs('cur_text')) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="deposit-info total-amount pt-3">
                                    <div class="deposit-info__title">
                                        <p class="text">@lang('Receivable')</p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <p class="text">{{ gs('cur_sym') }}<span class="final-amount">@lang('0.00')</span>
                                            {{ __(gs('cur_text')) }}</p>
                                    </div>
                                </div>

                                <div class="deposit-info gateway-conversion d-none total-amount pt-2">
                                    <div class="deposit-info__title">
                                        <p class="text">@lang('Conversion')
                                        </p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <p class="text"></p>
                                    </div>
                                </div>
                                <div class="deposit-info conversion-currency d-none total-amount pt-2">
                                    <div class="deposit-info__title">
                                        <p class="text">
                                            @lang('In') <span class="gateway-currency"></span>
                                        </p>
                                    </div>
                                    <div class="deposit-info__input">
                                        <p class="text">
                                            <span class="in-currency"></span>
                                        </p>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn--base w-100" disabled>
                                    @lang('Confirm Withdraw')
                                </button>
                                <div class="info-text pt-3">
                                    <p class="text">@lang('Safely withdraw your funds using our highly secure process and various withdrawal method')</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .deposit-info__input-group .form--control {
            padding: 5px !important;
        }
    </style>
@endpush
@push('script')
    <script>
        "use strict";
        (function($) {

            var amount = parseFloat($('.amount').val() || 0);
            var gateway, minAmount, maxAmount;


            $('.amount').on('input', function(e) {
                amount = parseFloat($(this).val());
                if (!amount) {
                    amount = 0;
                }
                calculation();
            });

            $('.gateway-input').on('change', function(e) {
                gatewayChange();
            });

            function gatewayChange() {
                let gatewayElement = $('.gateway-input:checked');
                let methodCode = gatewayElement.val();

                gateway = gatewayElement.data('gateway');
                minAmount = gatewayElement.data('min-amount');
                maxAmount = gatewayElement.data('max-amount');

                let processingFeeInfo =
                    `${parseFloat(gateway.percent_charge).toFixed(2)}% with ${parseFloat(gateway.fixed_charge).toFixed(2)} {{ __(gs('cur_text')) }} charge for processing fees`
                $(".proccessing-fee-info").attr("data-bs-original-title", processingFeeInfo);

                calculation();
            }

            gatewayChange();


            function calculation() {
                if (!gateway) return;
                $(".gateway-limit").text(minAmount + " - " + maxAmount);

                let percentCharge = 0;
                let fixedCharge = 0;
                let totalPercentCharge = 0;

                if (amount) {
                    percentCharge = parseFloat(gateway.percent_charge);
                    fixedCharge = parseFloat(gateway.fixed_charge);
                    totalPercentCharge = parseFloat(amount / 100 * percentCharge);
                }

                let totalCharge = parseFloat(totalPercentCharge + fixedCharge);
                let totalAmount = parseFloat((amount || 0) - totalPercentCharge - fixedCharge);

                $(".final-amount").text(totalAmount.toFixed(2));
                $(".processing-fee").text(totalCharge.toFixed(2));
                $("input[name=currency]").val(gateway.currency);
                $(".gateway-currency").text(gateway.currency);

                if (amount < Number(gateway.min_limit) || amount > Number(gateway.max_limit)) {
                    $(".withdraw-form button[type=submit]").attr('disabled', true);
                } else {
                    $(".withdraw-form button[type=submit]").removeAttr('disabled');
                }

                if (gateway.currency != "{{ gs('cur_text') }}") {
                    $('.withdraw-form').addClass('adjust-height')
                    $(".gateway-conversion, .conversion-currency").removeClass('d-none');
                    $(".gateway-conversion").find('.deposit-info__input .text').html(
                        `1 {{ __(gs('cur_text')) }} = <span class="rate">${parseFloat(gateway.rate).toFixed(2)}</span>  <span class="method_currency">${gateway.currency}</span>`
                    );
                    $('.in-currency').text(parseFloat(totalAmount * gateway.rate).toFixed(2))
                } else {
                    $(".gateway-conversion, .conversion-currency").addClass('d-none');
                    $('.withdraw-form').removeClass('adjust-height')
                }
            }

            $('.gateway-input').change();
        })(jQuery);
    </script>
@endpush
