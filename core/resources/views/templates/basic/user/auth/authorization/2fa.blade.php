@extends($activeTemplate . 'layouts.frontend')
@section('frontend')
    @php
        $codeVerifyContent = getContent('code_verify.content', true);
    @endphp
    <div class="login-page section" style="background-image: url({{ frontendImage('code_verify', @$codeVerifyContent->data_values->background_image, '1920x1070') }});">
        <div class="container">
            <div class="row g-3 align-items-center justify-content-lg-between">
                <div class="col-lg-6 col-xl-7 d-lg-block d-none">
                    <img class="login-page__img img-fluid" src="{{ frontendImage('code_verify', @$codeVerifyContent->data_values->image, '1380x1150') }}" alt="@lang('image')">
                </div>
                <div class="col-lg-6 col-xl-5">
                    <div class="d-flex justify-content-lg-end justify-content-center">
                        <div class="verification-code-wrapper">
                            <div class="verification-area">
                                <form class="submit-form" action="{{ route('user.2fa.verify') }}" method="POST">
                                    @csrf

                                    <div class="col-12">
                                        <h5 class="login-form__title">@lang('2FA Verification')</h5>
                                        <p class="text-muted">@lang('Check your google authenticator for verification code.')</p>
                                    </div>

                                    @include($activeTemplate . 'partials.verification_code')

                                    <div class="col-12">
                                        <button class="btn btn--xl btn--base w-100" type="submit">@lang('Submit')</button>
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
