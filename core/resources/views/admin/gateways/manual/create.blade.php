@extends('admin.layouts.app')
@section('panel')
    @push('topBar')
        @include('admin.gateways.top_bar')
    @endpush
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <form action="{{ route('admin.gateway.manual.store') }}" class="disableSubmission" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="payment-method-item">
                            <div class="gateway-body mb-3">
                                <div class="gateway-thumb">
                                    <div class="thumb">
                                        <x-image-uploader image="" class="w-100" type="gateway" :required=false />
                                    </div>
                                </div>
                                <div class="gateway-content">
                                    <div class="row mb-none-15">
                                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 mb-15">
                                            <div class="form-group">
                                                <label>@lang('Gateway Name')</label>
                                                <input type="text" class="form-control " name="name" value="{{ old('name') }}" required />
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 mb-15">

                                            <div class="form-group">
                                                <label>@lang('Currency')</label>
                                                <input type="text" name="currency" class="form-control border-radius-5" required value="{{ old('currency') }}">
                                            </div>

                                        </div>
                                        <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4 mb-15">
                                            <div class="form-group">
                                                <label>@lang('Rate')</label>
                                                <div class="input-group">
                                                    <div class="input-group-text">1 {{ __(gs('cur_text')) }} =</div>
                                                    <input type="number" step="any" class="form-control" name="rate" required value="{{ old('rate') }}">
                                                    <div class="input-group-text"><span class="currency_symbol"></span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="payment-method-body">

                                <div class="row">

                                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                        <div class="card border border--primary mt-3">
                                            <h5 class="card-header bg--primary">@lang('Range')</h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>@lang('Minimum Amount')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="form-control" name="min_limit" required value="{{ old('min_limit') }}">
                                                        <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                                                    </div>
                                                    <span class="min-limit-error-message text--danger"></span>
                                                </div>
                                                <div class="form-group">
                                                    <label>@lang('Maximum Amount')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="form-control" name="max_limit" required value="{{ old('max_limit') }}">
                                                        <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                                                    </div>
                                                    <span class="max-limit-error-message text--danger"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                        <div class="card border border--primary mt-3">
                                            <h5 class="card-header bg--primary">@lang('Charge')</h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label>@lang('Fixed Charge')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="form-control" name="fixed_charge" required value="{{ old('fixed_charge') }}">
                                                        <div class="input-group-text">{{ __(gs('cur_text')) }}</div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label>@lang('Percent Charge')</label>
                                                    <div class="input-group">
                                                        <input type="number" step="any" class="form-control" name="percent_charge" required value="{{ old('percent_charge') }}">
                                                        <div class="input-group-text">%</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="card border border--primary mt-3">
                                            <h5 class="card-header bg--primary">@lang('Deposit Instruction')</h5>
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <textarea rows="8" class="form-control border-radius-5 nicEdit" name="instruction">{{ old('instruction') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="submitRequired bg--warning form-change-alert d-none mt-3"><i class="fas fa-exclamation-triangle"></i> @lang('You\'ve to click on the submit button to apply the changes')</div>
                                        <div class="card border border--primary mt-3">
                                            <div class="card-header bg--primary d-flex justify-content-between">
                                                <h5 class="text-white">@lang('User Data')</h5>
                                                <button type="button" class="btn btn-sm btn-outline-light float-end form-generate-btn"> <i class="la la-fw la-plus"></i>@lang('Add New')</button>
                                            </div>
                                            <div class="card-body">
                                                <x-generated-form />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-form-generator-modal />
@endsection


@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.gateway.manual.index') }}" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('input[name=currency]').on('input', function() {
                $('.currency_symbol').text($(this).val());
            });

            @if (old('currency'))
                $('input[name=currency]').trigger('input');
            @endif

            let minLimit = $('[name=min_limit]');
            let maxLimit = $('[name=max_limit]');
            let minLimitErrorMessage = $('.min-limit-error-message');
            let maxLimitErrorMessage = $('.max-limit-error-message');
            let minLimitValue;
            let maxLimitValue;
            let hasError = false;

            function validateInput() {
                minLimitValue = Number(minLimit.val());
                maxLimitValue = Number(maxLimit.val());

                if (minLimitValue && maxLimitValue && minLimitValue >= maxLimitValue) {
                    minLimitErrorMessage.text('Minimum amount should be less than maximum amount');
                    maxLimitErrorMessage.empty();
                    hasError = true;
                    return;
                }
                hasError = false;
                emptyErrorMessage();
            }

            minLimit.on('input', validateInput);
            maxLimit.on('input', validateInput);

            function emptyErrorMessage() {
                minLimitErrorMessage.empty();
                maxLimitErrorMessage.empty();
            }

            $('form').on('submit', function(e) {
                validateInput();
                if (hasError) {
                    e.preventDefault();
                }
            });

        })(jQuery);
    </script>
@endpush
