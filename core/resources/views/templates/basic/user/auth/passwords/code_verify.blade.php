@extends($activeTemplate . 'layouts.frontend')
@section('frontend')
    @php
        $codeVerifyContent = getContent('code_verify.content', true);
    @endphp

    <div class="login-page section" style="background-image: url({{ frontendImage('code_verify', @$codeVerifyContent->data_values->background_image, '1920x1070') }});">
        <div class="container">
            <div class="row g-3 align-items-center justify-content-lg-between justify-content-center">
                <div class="col-lg-6 d-lg-block d-none">
                    <img class="login-page__img img-fluid" src="{{ frontendImage('code_verify', @$codeVerifyContent->data_values->image, '1380x1150') }}" alt="@lang('image')">
                </div>
                <div class="col-lg-6 col-xl-5 col-md-8">
                    <div class="d-flex justify-content-center justify-content-lg-end">
                        <div class="verification-code-wrapper">
                            <div class="verification-area">
                                <form class="submit-form" action="{{ route('user.password.verify.code') }}" method="POST">
                                    @csrf
                                    <input name="email" type="hidden" value="{{ $email }}">
                                    <div class="col-12">
                                        <h5 class="login-form__title">@lang('Verify Email Address')</h5>
                                        <p class="text-muted">@lang('A 6 digit verification code sent to your email address') : {{ showEmailAddress($email) }}</p>
                                    </div>
                                    @include($activeTemplate . 'partials.verification_code')
                                    <div class="col-12">
                                        <button class="btn btn--xl btn--base w-100" type="submit">@lang('Submit')</button>
                                    </div>
                                    <div class="col-12 mt-2">
                                        @lang('Please check including your Junk/Spam Folder. if not found, you can')
                                        <a class="text--base" href="{{ route('user.password.request') }}">@lang('Try to send again')</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
