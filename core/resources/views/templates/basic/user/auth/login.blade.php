@extends($activeTemplate . 'layouts.frontend')
@section('frontend')
    @php
        $loginContent = getContent('login.content', true);
    @endphp
    <div class="login-page section" style="background-image: url({{ frontendImage('login', @$loginContent->data_values->background_image, '1920x1070') }});">
        <div class="container">
            <div class="row g-3 align-items-center justify-content-lg-between justify-content-center">
                <div class="col-lg-6 d-lg-block d-none">
                    <img class="login-page__img img-fluid" src="{{ frontendImage('login', @$loginContent->data_values->image, '1380x1150') }}" alt="@lang('image')">
                </div>
                <div class="col-lg-6 col-xl-5 col-md-8">
                    <div class="login-form mt-0">
                        <div class="col-12">
                            <h4 class="login-form__title">{{ __(@$loginContent->data_values->heading) }}</h4>
                        </div>
                        @include($activeTemplate . 'partials.login')
                        @include($activeTemplate . 'partials.social_login')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
