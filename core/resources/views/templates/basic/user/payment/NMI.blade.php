@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="card custom--card">
        <div class="card-body">
            <div class="card-wrapper mb-3"></div>
            <form role="form" class="disableSubmission appPayment" id="payment-form" method="{{ $data->method }}"
                action="{{ $data->url }}">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <label class="form-label">@lang('Card Number')</label>
                        <div class="input-group">
                            <input type="tel" class="form-control form--control" name="billing-cc-number"
                                autocomplete="off" value="{{ old('billing-cc-number') }}" required autofocus />
                            <span class="input-group-text h-40"><i class="fas fa-credit-card"></i></span>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-6">
                        <label class="form-label">@lang('Expiration Date')</label>
                        <input type="tel" class="form-control form--control" name="billing-cc-exp"
                            value="{{ old('billing-cc-exp') }}" autocomplete="off" required />
                    </div>
                    <div class="col-md-6 ">
                        <label class="form-label">@lang('CVC Code')</label>
                        <input type="tel" class="form-control form--control" name="billing-cc-cvv"
                            value="{{ old('billing-cc-cvv') }}" autocomplete="off" required />
                    </div>
                </div>
                <br>
                <button class="btn btn--base w-100" type="submit"> @lang('Submit')</button>
            </form>
        </div>
    </div>
@endsection

@push('script-lib')
    <script src="{{ asset('assets/global/js/card.js') }}"></script>
@endpush
@push('script')
    <script>
        (function($) {
            "use strict";
            var card = new Card({
                form: '#payment-form',
                container: '.card-wrapper',
                formSelectors: {
                    numberInput: 'input[name="billing-cc-number"]',
                    expiryInput: 'input[name="billing-cc-exp"]',
                    cvcInput: 'input[name="billing-cc-cvv"]',
                }
            });

            @if ($deposit->from_api)
                $('.appPayment').on('submit', function() {
                    $(this).find('[type=submit]').html('<i class="las la-spinner fa-spin"></i>');
                })
            @endif
        })(jQuery);
    </script>
@endpush
