<form class="verify-gcaptcha disableSubmission" action="{{ route('user.register') }}" method="POST">
    @csrf
    <div class="row">
        @if (session()->get('reference') != null)
            <div class="form-group">
                <label class="form-label">@lang('Reference Id')</label>
                <input class="form-control form--control" type="text" value="{{ session()->get('reference') }}" readonly>
            </div>
        @endif
        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label class="form-label">@lang('First Name')</label>
                <input class="form-control form--control" name="firstname" type="text" value="{{ old('firstname') }}" required>
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label class="form-label">@lang('Last Name')</label>
                <input class="form-control form--control" name="lastname" type="text" value="{{ old('lastname') }}" required>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="form-group">
                <label class="form-label">@lang('Email Address')</label>
                <input class="form-control form--control checkUser" name="email" type="email" value="{{ old('email') }}" required>
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label class="form-label">@lang('Password')</label>
                <div class="input-group input--group">
                    <input class="form-control form--control @if (gs('secure_password')) secure-password @endif" name="password" type="password" required>
                    <span class="input-group-text pass-toggle">
                        <i class="las la-eye"></i>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-sm-12">
            <div class="form-group">
                <label class="form-label">@lang('Confirm Password')</label>
                <div class="input-group input--group">
                    <input class="form-control form--control" name="password_confirmation" type="password" required>
                    <span class="input-group-text pass-toggle">
                        <i class="las la-eye"></i>
                    </span>
                </div>
            </div>
        </div>

        <x-captcha />

        @if (gs('agree'))
            @php
                $policyElements = getContent('policy_pages.element', orderById: true);
            @endphp
            <div class="col-12">
                <div class="form-group form-check d-flex flex-wrap align-items-center gap-2">
                    <input class="form-check-input custom--check" id="agree" name="agree" type="checkbox" @checked(old('agree')) required>
                    <div>
                        <label class="form-check-label sm-text t-heading-font heading-clr fw-md" for="agree">
                            @lang('I agree with')
                        </label>
                        <span class="sm-text">
                            @foreach ($policyElements as $policy)
                                <a href="{{ route('policy.pages', $policy->slug) }}" target="_blank">{{ __(@$policy->data_values->title) }}</a>
                                @if (!$loop->last)
                                    ,
                                @endif
                            @endforeach
                        </span>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <button class="btn btn--xl btn--base w-100 mt-3" type="submit">@lang('Register')</button>

    <div class="d-flex justify-content-center align-items-center gap-2 mt-2">
        <span class="d-inline-block sm-text"> @lang('Already have an account?') </span>
        <a class="t-link d-inline-block t-link--base base-clr sm-text lh-1 text-center text-end" href="{{ route('user.login') }}">
            @lang('Login')
        </a>
    </div>
</form>

@push('style')
    <style>
        .form-group {
            margin: 8px 0;
        }
    </style>
@endpush

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif

@push('script')
    <script>
        "use strict";
        (function($) {
            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                var data = {
                    email: value,
                    _token: token
                }
                $.post(url, data, function(response) {
                    if (response.data != false) {
                        $('#existModalCenter').modal('show');
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
