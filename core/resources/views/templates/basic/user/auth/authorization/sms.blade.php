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
                                <form class="submit-form" action="{{ route('user.verify.mobile') }}" method="POST">
                                    @csrf

                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                                            <h5 class="login-form__title">@lang('Verify Mobile Number')</h5>
                                            <a href="{{ route('user.logout') }}" class="btn btn-outline--base btn--sm">@lang('Logout')</a>
                                        </div>
                                        <p class="text-muted mt-3">@lang('A 6 digit verification code sent to your mobile number') : {{ showMobileNumber(auth()->user()->mobile) }}</p>
                                    </div>

                                    @include($activeTemplate . 'partials.verification_code')

                                    <div class="col-12">
                                        <button class="btn btn--xl btn--base w-100" type="submit">@lang('Submit')</button>
                                    </div>

                                    <div class="mt-3">
                                        <p>
                                            @lang('If you don\'t get any code'), <span class="countdown-wrapper">@lang('try again after') <span id="countdown" class="fw-bold">--</span> @lang('seconds')</span> <a href="{{ route('user.send.verify.code', 'sms') }}" class="try-again-link d-none"> @lang('Try again')</a>
                                        </p>
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
@push('script')
    <script>
        var distance = Number("{{ @$user->ver_code_send_at->addMinutes(2)->timestamp - time() }}");
        var x = setInterval(function() {
            distance--;
            document.getElementById("countdown").innerHTML = distance;
            if (distance <= 0) {
                clearInterval(x);
                document.querySelector('.countdown-wrapper').classList.add('d-none');
                document.querySelector('.try-again-link').classList.remove('d-none');
            }
        }, 1000);
    </script>
@endpush
