@extends($activeTemplate . 'layouts.frontend')
@section('frontend')
    @php
        $userBanContent = getContent('user_ban.content', true);
    @endphp
    <div class="login-page section" style="background-image: url({{ frontendImage('user_ban', @$userBanContent->data_values->background_image, '1920x1070') }});">
        <div class="container">
            <div class="row g-3 align-items-center justify-content-lg-between">
                <div class="col-lg-6 col-xl-7 d-lg-block d-none">
                    <img class="login-page__img img-fluid" src="{{ frontendImage('user_ban', @$userBanContent->data_values->image, '1380x1150') }}" alt="@lang('image')">
                </div>
                <div class="col-lg-6 col-xl-5">
                    <div class="login-form">
                        <div class="col-12">
                            <h4 class="login-form__title text--danger">@lang('You are banned')</h4>
                            <p>{{ __($user->ban_reason) }}</p>
                        </div>
                        <div class="col-12">
                            <a class="btn btn--xl btn--base w-100" href="{{ route('home') }}">
                                @lang('Go To Home')
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
