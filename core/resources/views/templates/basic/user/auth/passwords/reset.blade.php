@extends($activeTemplate . 'layouts.frontend')
@section('frontend')
    @php
        $resetPasswordContent = getContent('reset_password.content', true);
    @endphp

    <div class="login-page section" style="background-image: url({{ frontendImage('reset_password', @$resetPasswordContent->data_values->background_image, '1920x1070') }});">
        <div class="container">
            <div class="row g-3 align-items-center justify-content-lg-between justify-content-center">
                <div class="col-lg-6 d-lg-block d-none">
                    <img class="login-page__img img-fluid" src="{{ frontendImage('reset_password', @$resetPasswordContent->data_values->image, '1380x1150') }}" alt="@lang('image')">
                </div>
                <div class="col-lg-6 col-xl-5 col-md-8">
                    <div class="login-form">
                        <form action="{{ route('user.password.update') }}" method="POST">
                            @csrf

                            <input name="email" type="hidden" value="{{ $email }}">
                            <input name="token" type="hidden" value="{{ $token }}">

                            <h4 class="login-form__title">@lang('Reset Password')</h4>
                            <p class="text-muted">@lang('Please create a strong and unique password and ensure that you do not share it with anyone')</p>

                            <div class="form-group">
                                <label class="form-label">@lang('Password')</label>
                                <div class="input-group input--group mb-3">
                                    <input class="form-control form--control @if (gs('secure_password')) secure-password @endif" name="password" type="password" required>
                                    <span class="input-group-text pass-toggle">
                                        <i class="las la-eye"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">@lang('Confirm Password')</label>
                                <div class="input-group input--group mb-3">
                                    <input class="form-control form--control" name="password_confirmation" type="password" required>
                                    <span class="input-group-text pass-toggle">
                                        <i class="las la-eye"></i>
                                    </span>
                                </div>
                                <small class="text--danger passNotMatch"></small>
                            </div>

                            <button class="btn btn--xl btn--base w-100" type="submit">
                                @lang('Submit')
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
