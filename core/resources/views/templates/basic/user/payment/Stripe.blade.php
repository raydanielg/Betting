@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="card custom--card">
        <div class="card-body">
            <div class="card-wrapper mb-3"></div>
            <form role="form" class="disableSubmission payment appPayment" id="payment-form" method="{{ $data->method }}" action="{{ $data->url }}">
                @csrf
                <input name="track" type="hidden" value="{{ $data->track }}">
                <div class="row ">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Name on Card')</label>
                            <div class="input-group">
                                <input class="form-control form--control" name="name" type="text" value="{{ old('name') }}" required autocomplete="off" autofocus />
                                <span class="input-group-text"><i class="fa fa-font"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Card Number')</label>
                            <div class="input-group">
                                <input class="form-control form--control" name="cardNumber" type="tel" value="{{ old('cardNumber') }}" autocomplete="off" required autofocus />
                                <span class="input-group-text"><i class="fa fa-credit-card"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('Expiration Date')</label>
                            <input class="form-control form--control" name="cardExpiry" type="tel" value="{{ old('cardExpiry') }}" autocomplete="off" required />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">@lang('CVC Code')</label>
                            <input class="form-control form--control" name="cardCVC" type="tel" value="{{ old('cardCVC') }}" autocomplete="off" required />
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button class="btn btn--xl btn--base" type="submit"> @lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/global/js/card.js') }}"></script>

    <script>
        (function($) {
            "use strict";
            var card = new Card({
                form: '#payment-form',
                container: '.card-wrapper',
                formSelectors: {
                    numberInput: 'input[name="cardNumber"]',
                    expiryInput: 'input[name="cardExpiry"]',
                    cvcInput: 'input[name="cardCVC"]',
                    nameInput: 'input[name="name"]'
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
