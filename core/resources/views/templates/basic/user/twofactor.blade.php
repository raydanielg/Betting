@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="row justify-content-center gy-4">
        @if (!$user->ts)
            <div class="col-md-6">
                <div class="card custom--card">
                    <div class="card-body">
                        <div class="mb-3">
                            <h5 class="card-title ">@lang('Add Your Account')</h5>
                            <small class="text-muted lh-1">
                                @lang('Use the QR code or setup key on your Google Authenticator app to add your account.')
                            </small>
                        </div>

                        <div class="form-group mx-auto text-center">
                            <img class="mx-auto" src="{{ $qrCodeUrl }}" alt="QR">
                        </div>

                        <div class="form-group">
                            <label class="form-label">@lang('Setup Key')</label>
                            <div class="input-group">
                                <input type="text" name="key" value="{{ $secret }}" class="form-control form--control referralURL" readonly>
                                <button type="button" class="input-group-text copytext" id="copyBoard"> <i class="fas fa-copy"></i> </button>
                            </div>
                        </div>

                        <p class="sm-text text-muted"><i class="la la-info-circle"></i> @lang('Google Authenticator is a multifactor app for mobile devices. It generates timed codes used during the 2-step verification process. To use Google Authenticator, install the Google Authenticator application on your mobile device.') <a class="text--base" href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en" target="_blank">@lang('Download')</a></p>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-md-6">
            @if ($user->ts)
                <div class="card custom--card">
                    <form action="{{ route('user.twofactor.disable') }}" method="POST">
                        <div class="card-body">
                            <div class="mb-3">
                                <h5 class="card-title">@lang('Disable 2FA Security')</h5>
                            </div>
                            @csrf
                            <input type="hidden" name="key" value="{{ $secret }}">
                            <div class="form-group">
                                <label class="form-label">@lang('Google Authenticator OTP')</label>
                                <input type="text" class="form-control form--control" name="code" required>
                            </div>
                            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            @else
                <div class="card custom--card">
                    <form action="{{ route('user.twofactor.enable') }}" method="POST">
                        <div class="card-body">
                            <div class="mb-3">
                                <h5 class="card-title">@lang('Enable 2FA Security')</h5>
                            </div>
                            @csrf
                            <input type="hidden" name="key" value="{{ $secret }}">
                            <div class="form-group">
                                <label class="form-label">@lang('Google Authenticator OTP')</label>
                                <input type="text" class="form-control form--control" name="code" required>
                            </div>
                            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('style')
    <style>
        .copied::after {
            background-color: #{{ gs('base_color') }};
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('#copyBoard').on('click', function() {
                var copyText = document.getElementsByClassName("referralURL");
                copyText = copyText[0];
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                /*For mobile devices*/
                document.execCommand("copy");
                copyText.blur();
                this.classList.add('copied');
                setTimeout(() => this.classList.remove('copied'), 1500);
            });
        })(jQuery);
    </script>
@endpush
