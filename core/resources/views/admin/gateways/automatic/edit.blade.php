@extends('admin.layouts.app')
@section('panel')
    @push('topBar')
        @include('admin.gateways.top_bar')
    @endpush
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <form action="{{ route('admin.gateway.automatic.update', $gateway->code) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="alias" value="{{ $gateway->alias }}">
                    <input type="hidden" name="description" value="{{ $gateway->description }}">


                    <div class="card-body">
                        <div class="payment-method-item block-item">
                            <div class="row align-items-center gy-1">
                                <div class="col-lg-8 col-sm-6">
                                    <h3>{{ __($gateway->name) }}</h3>
                                </div>
                                <div class="col-lg-4 col-sm-6">
                                    @if (count($supportedCurrencies) > 0)
                                        <div class="input-group d-flex flex-nowrap justify-content-sm-end">
                                            <select class="newCurrencyVal ">
                                                <option value="">@lang('Select currency')</option>
                                                @forelse($supportedCurrencies as $currency => $symbol)
                                                    <option value="{{ $currency }}" data-symbol="{{ $symbol }}">{{ __($currency) }} </option>
                                                @empty
                                                    <option value="">@lang('No available currency support')</option>
                                                @endforelse

                                            </select>
                                            <button type="button" class="btn btn--primary input-group-text newCurrencyBtn" data-crypto="{{ $gateway->crypto }}" data-name="{{ $gateway->name }}">@lang('Add new')</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="gateway-body">
                                <div class="gateway-thumb">
                                    <div class="thumb">
                                        <x-image-uploader image="{{ $gateway->image }}" class="w-100" type="gateway" :required=false />
                                    </div>
                                </div>
                                <div class="gateway-content">
                                    @if ($gateway->code < 1000 && $gateway->extra)
                                        <div class="payment-method-body mt-2">
                                            <h4 class="mb-3 payment-method-body-title">@lang('Configurations')</h4>
                                            <div class="row">
                                                @foreach ($gateway->extra as $key => $param)
                                                    <div class="form-group col-lg-6">
                                                        <label>{{ __(@$param->title) }}</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" value="{{ route($param->value) }}" readonly>
                                                            <button type="button" class="copyInput input-group-text" title="@lang('Copy')"><i class="fas fa-copy"></i></button>
                                                        </div>
                                                        @if ($key == 'cron')
                                                            <small><i class="las la-info-circle"></i> @lang('Set the URL to your server\'s cron job to validate the payment.')</small>
                                                        @endif

                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    <div class="payment-method-body mt-2">
                                        <h4 class="mb-3 payment-method-body-title">@lang('Global Setting for') {{ __($gateway->name) }}</h4>
                                        <div class="row">
                                            @foreach ($parameters->where('global', true) as $key => $param)
                                                <div class="form-group col-xl-6 col-lg-12 col-md-6">
                                                    <label>{{ __(@$param->title) }}</label>
                                                    <input type="text" class="form-control" name="global[{{ $key }}]" value="{{ @$param->value }}" required>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- payment-method-item start -->

                        @isset($gateway->currencies)
                            @foreach ($gateway->currencies as $gatewayCurrency)
                                <input type="hidden" name="currency[{{ $currencyIndex }}][symbol]" value="{{ $gatewayCurrency->symbol }}">
                                <div class="payment-method-item block-item child--item">
                                    <div class="payment-method-header">
                                        <div class="content">
                                            <div class="d-flex justify-content-between">
                                                <h4 class="mb-3">{{ __($gateway->name) }} - {{ __($gatewayCurrency->currency) }}</h4>
                                                <div class="remove-btn">
                                                    <button type="button" class="btn btn--danger confirmationBtn" data-question="@lang('Are you sure to delete this gateway currency?')" data-action="{{ route('admin.gateway.automatic.remove', $gatewayCurrency->id) }}">
                                                        <i class="la la-trash-o me-2"></i>@lang('Remove')
                                                    </button>
                                                </div>


                                            </div>
                                            <div class="form-group payment-method-title-input">
                                                <input type="text" class="form-control" name="currency[{{ $currencyIndex }}][name]" value="{{ $gatewayCurrency->name }}" required>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="payment-method-body">
                                        <div class="row g-4">
                                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                                <div class="card border border--primary">
                                                    <h5 class="card-header bg--primary">@lang('Range')</h5>
                                                    <div class="card-body">
                                                        <div class="form-group">
                                                            <label>@lang('Minimum Amount')</label>
                                                            <div class="input-group">
                                                                <input type="number" step="any" class="form-control minAmount" name="currency[{{ $currencyIndex }}][min_amount]" value="{{ getAmount($gatewayCurrency->min_amount) }}" required>
                                                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                                                            </div>
                                                            <span class="min-amount-error-message text--danger"></span>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>@lang('Maximum Amount')</label>
                                                            <div class="input-group">
                                                                <input type="number" step="any" class="form-control maxAmount" name="currency[{{ $currencyIndex }}][max_amount]" value="{{ getAmount($gatewayCurrency->max_amount) }}" required>
                                                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                                                            </div>
                                                            <span class="max-amount-error-message text--danger"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                                <div class="card border border--primary">
                                                    <h5 class="card-header bg--primary">@lang('Charge')</h5>
                                                    <div class="card-body">
                                                        <div class="form-group">
                                                            <label>@lang('Fixed Charge')</label>
                                                            <div class="input-group">
                                                                <input type="number" step="any" class="form-control" name="currency[{{ $currencyIndex }}][fixed_charge]" value="{{ getAmount($gatewayCurrency->fixed_charge) }}" required>
                                                                <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>@lang('Percent Charge')</label>
                                                            <div class="input-group">
                                                                <input type="number" step="any" class="form-control" name="currency[{{ $currencyIndex }}][percent_charge]" value="{{ getAmount($gatewayCurrency->percent_charge) }}" required>
                                                                <div class="input-group-text">%</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                                <div class="card border border--primary">
                                                    <h5 class="card-header bg--primary">@lang('Currency')</h5>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>@lang('Currency')</label>
                                                                    <input type="text" name="currency[{{ $currencyIndex }}][currency]" class="form-control border-radius-5 " value="{{ $gatewayCurrency->currency }}" readonly>
                                                                </div>

                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>@lang('Symbol')</label>
                                                                    <input type="text" name="currency[{{ $currencyIndex }}][symbol]" class="form-control border-radius-5 symbl" value="{{ $gatewayCurrency->symbol }}" data-crypto="{{ $gateway->crypto }}" required>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>@lang('Rate')</label>
                                                            <div class="input-group">
                                                                <div class="input-group-text">1 {{ gs('cur_text') }} =</div>
                                                                <input type="number" step="any" class="form-control" name="currency[{{ $currencyIndex }}][rate]" value="{{ getAmount($gatewayCurrency->rate) }}" required>
                                                                <div class="input-group-text"><span class="currency_symbol">{{ __($gatewayCurrency->baseSymbol()) }}</span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            @if ($parameters->where('global', false)->count() != 0)
                                                @php
                                                    $globalParameters = json_decode($gatewayCurrency->gateway_parameter);
                                                @endphp
                                                <div class="col-lg-12">
                                                    <div class="card border border--dark mt-4">
                                                        <h5 class="card-header bg--dark">@lang('Configuration')</h5>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                @foreach ($parameters->where('global', false) as $key => $param)
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label>{{ __($param->title) }}</label>
                                                                            <input type="text" class="form-control" name="currency[{{ $currencyIndex }}][param][{{ $key }}]" value="{{ $globalParameters->$key }}" required>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @php $currencyIndex++ @endphp
                            @endforeach
                        @endisset

                        <!-- payment-method-item end -->


                        <!-- **new payment-method-item start -->
                        <div class="payment-method-item child--item newMethodCurrency d-none">
                            <input disabled type="hidden" name="currency[{{ $currencyIndex }}][symbol]" class="currencySymbol">
                            <div class="payment-method-header">
                                <div class="content">
                                    <div class="d-flex justify-content-between">
                                        <div class="form-group">
                                            <h4 class="mb-3" id="payment_currency_name">@lang('Name')</h4>
                                            <input disabled type="text" class="form-control" name="currency[{{ $currencyIndex }}][name]" required>
                                        </div>
                                        <div class="remove-btn">
                                            <button type="button" class="btn btn-danger newCurrencyRemove">
                                                <i class="la la-trash-o me-2"></i>@lang('Remove')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="payment-method-body">
                                <div class="row g-4">
                                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                        <div class="card border border--primary">
                                            <h5 class="card-header bg--primary">@lang('Range')</h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>@lang('Minimum Amount')</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                                                        <input disabled type="number" step="any" class="form-control minAmount" name="currency[{{ $currencyIndex }}][min_amount]" required>
                                                    </div>
                                                    <span class="min-amount-error-message text--danger"></span>
                                                </div>

                                                <div class="form-group">
                                                    <label>@lang('Maximum Amount')</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                                                        <input disabled type="number" step="any" class="form-control maxAmount" name="currency[{{ $currencyIndex }}][max_amount]" required>
                                                    </div>
                                                    <span class="max-amount-error-message text--danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                        <div class="card border border--primary">
                                            <h5 class="card-header bg--primary">@lang('Charge')</h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>@lang('Fixed Charge')</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                                                        <input disabled type="number" step="any" class="form-control" name="currency[{{ $currencyIndex }}][fixed_charge]" required>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>@lang('Percent Charge')</label>
                                                    <div class="input-group">
                                                        <div class="input-group-text">%</div>
                                                        <input disabled type="number" step="any" class="form-control" name="currency[{{ $currencyIndex }}][percent_charge]" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                        <div class="card border border--primary">
                                            <h5 class="card-header bg--primary">@lang('Currency')</h5>
                                            <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>@lang('Currency')</label>
                                                            <input disabled type="step" class="form-control currencyText border-radius-5" name="currency[{{ $currencyIndex }}][currency]" readonly>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>@lang('Symbol')</label>
                                                            <input disabled type="text" name="currency[{{ $currencyIndex }}][symbol]" class="form-control border-radius-5 symbl" ata-crypto="{{ $gateway->crypto }}" disabled>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="form-group">
                                                    <label>@lang('Rate')</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">
                                                            1 {{ __(gs('cur_text')) }} =
                                                        </span>
                                                        <input disabled type="number" step="any" class="form-control" name="currency[{{ $currencyIndex }}][rate]" required>
                                                        <div class="input-group-text"><span class="currency_symbol"></span></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($parameters->where('global', false)->count() != 0)
                                        <div class="col-lg-12">
                                            <div class="card border border--dark mt-4">
                                                <h5 class="card-header bg--dark">@lang('Configuration')</h5>
                                                <div class="card-body">
                                                    <div class="row">
                                                        @foreach ($parameters->where('global', false) as $key => $param)
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>{{ __($param->title) }}</label>
                                                                    <input disabled type="text" class="form-control" name="currency[{{ $currencyIndex }}][param][{{ $key }}]" required>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                        <!-- **new payment-method-item end -->
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">
                            @lang('Submit')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <x-confirmation-modal />

@endsection





@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.gateway.automatic.index') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.newCurrencyBtn').on('click', function() {
                var form = $('.newMethodCurrency');
                var getCurrencySelected = $('.newCurrencyVal').find(':selected').val();
                var currency = $(this).data('crypto') == 1 ? 'USD' : `${getCurrencySelected}`;
                if (!getCurrencySelected) return;
                form.find('input').removeAttr('disabled');
                var symbol = $('.newCurrencyVal').find(':selected').data('symbol');
                form.find('.currencyText').val(getCurrencySelected);
                form.find('.currency_symbol').text(currency);
                $('#payment_currency_name').text(`${$(this).data('name')} - ${getCurrencySelected}`);
                form.removeClass('d-none');
                $('html, body').animate({
                    scrollTop: $('html, body').height()
                }, 'slow');

                $('.newCurrencyRemove').on('click', function() {
                    form.find('input').val('');
                    form.remove();
                });
            });

            $('.symbl').on('input', function() {
                var curText = $(this).data('crypto') == 1 ? 'USD' : $(this).val();
                $(this).parents('.payment-method-body').find('.currency_symbol').text(curText);
            });

            $('.copyInput').on('click', function(e) {
                var copybtn = $(this);
                var input = copybtn.closest('.input-group').find('input');
                if (input && input.select) {
                    input.select();
                    try {
                        document.execCommand('SelectAll')
                        document.execCommand('Copy', false, null);
                        input.blur();
                        notify('success', `Copied: ${copybtn.closest('.input-group').find('input').val()}`);
                    } catch (err) {
                        alert('Please press Ctrl/Cmd + C to copy');
                    }
                }
            });


            let minAmountValue;
            let maxAmountValue;
            let hasError = false;

            function validateInput() {
                let input = $(this);
                let container = input.closest('.card-body');
                let minAmount = container.find('.minAmount');
                let maxAmount = container.find('.maxAmount');
                let minAmountErrorMessage = container.find('.min-amount-error-message');
                let maxAmountErrorMessage = container.find('.max-amount-error-message');

                minAmountValue = Number(minAmount.val());
                maxAmountValue = Number(maxAmount.val());

                if (!minAmountValue) {
                    minAmountErrorMessage.text('@lang('Minimum amount field is required')');
                    maxAmountErrorMessage.empty();
                    return;
                }

                if (!maxAmountValue) {
                    maxAmountErrorMessage.text('@lang('Maximum amount field is required')');
                    minAmountErrorMessage.empty();
                    return;
                }

                if (minAmountValue >= maxAmountValue) {
                    minAmountErrorMessage.text('@lang('Minimum amount should be less than maximum amount')');
                    maxAmountErrorMessage.empty();
                    hasError = true;
                    return;
                }
                minAmountErrorMessage.empty();
                maxAmountErrorMessage.empty();
                hasError = false;
            }


            $(document).on('input', '.minAmount, .maxAmount', validateInput);

            $('form').on('submit', function(e) {
                validateInput();
                if (hasError) {
                    e.preventDefault();
                }
            });

        })(jQuery);
    </script>
@endpush
