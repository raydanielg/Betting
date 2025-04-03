@extends($activeTemplate . 'layouts.frontend')
@section('frontend')
    @php
        $forgetPasswordContent = getContent('forget_password.content', true);
    @endphp
    <div class="login-page section" style="background-image: url({{ frontendImage('forget_password', @$forgetPasswordContent->data_values->background_image, '1920x1070') }});">
        <div class="container">
            <div class="row g-3 align-items-center justify-content-lg-between justify-content-center">
                <div class="col-lg-6 d-lg-block d-none">
                    <img class="login-page__img img-fluid" src="{{ frontendImage('forget_password', @$forgetPasswordContent->data_values->image, '1380x1150') }}" alt="@lang('image')">
                </div>
                <div class="col-lg-6 col-xl-5 col-md-8">
                    <div class="login-form mt-0">
                        <form class="verify-gcaptcha" action="{{ route('user.password.email') }}" method="POST">
                            @csrf
                            <h4 class="login-form__title">{{ __($pageTitle) }}</h4>
                            <p class="text-muted">@lang('Please provide your email or username to find your account.')</p>
                            <div class="form-group">
                                <label class="form-label">@lang('Username or Email')</label>
                                <input class="form-control form--control mb-3" name="value" type="text" value="{{ old('value') }}" required>
                            </div>
                            <x-captcha />
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
