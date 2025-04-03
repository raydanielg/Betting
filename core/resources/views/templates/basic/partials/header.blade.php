    <div class="header-fluid-custom-parent">

        <div class="logo">
            <a href="{{ route('home') }}">
                <img class="img-fluid" src="{{ siteLogo() }}" alt="@lang('logo')">
            </a>
        </div>

        <nav class="primary-menu-container">

            <ul class="align-items-center justify-content-start gap-3 mb-0 p-0 list-unstyled d-none d-lg-flex">
                <li>
                    <a href="{{ route('home') }}" class="text-white sm-text">
                        @lang('Home')
                    </a>
                </li>
                <li>
                    <a href="{{ route('blog') }}" class="text-white sm-text">
                        @lang('News & Updates')
                    </a>
                </li>
                <li>
                    <a href="{{ route('contact') }}" class="text-white sm-text">
                        @lang('Contact')
                    </a>
                </li>
            </ul>

            <ul class="list list--row primary-menu justify-content-end align-items-center right-side-nav gap-3 gap-sm-4">
                <li>
                    <div class="select-lang--container">
                        <div class="select-lang">
                            <select class="form-select oddsType">
                                <option value="" disabled>@lang('Odds Type')</option>
                                <option value="decimal" @selected(session('odds_type') == 'decimal')>@lang('Decimal Odds')</option>
                                <option value="fraction" @selected(session('odds_type') == 'fraction')>@lang('Fraction Odds')</option>
                                <option value="american" @selected(session('odds_type') == 'american')>@lang('American Odds')</option>
                            </select>
                        </div>
                    </div>
                </li>
                @if (gs('multi_language'))
                    @php
                        $languages = App\Models\Language::all();
                        $language = $languages->where('code', '!=', session('lang'));
                        $activeLanguage = $languages->where('code', session('lang'))->first();
                    @endphp
                    <li>
                        <div class="dropdown-lang dropdown mt-0">
                            <a href="#" class="language-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <img class="flag" src="{{ getImage(getFilePath('language') . '/' . @$activeLanguage->image, getFileSize('language')) }}" alt="us">
                                <span class="language-text text-white">{{ __(@$activeLanguage->code) }}</span>
                            </a>
                            <ul class="dropdown-menu" style="">
                                @foreach ($language as $item)
                                    <li>
                                        <a href="javascript:void(0)" class="langSel" data-code="{{ $item->code }}">
                                            <img class="flag" src="{{ getImage(getFilePath('language') . '/' . @$item->image, getFileSize('language')) }}" alt="image">
                                            {{ __(@$item->name) }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </li>
                @endif

                <li class="d-none d-lg-block">
                    @if (auth()->check() && !request()->routeIs('user.*'))
                        <div class="dropdown-center user-profile-dropdown">
                            <button class="dropdown-toggle user-profile-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="la la-user-circle"></i> {{ auth()->user()->username }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{route('user.home')}}">@lang('Dashboard')</a></li>
                                <li><a class="dropdown-item" href="{{route('user.deposit.index')}}">@lang('Deposit Money')</a></li>
                                <li><a class="dropdown-item" href="{{route('user.profile.setting')}}">@lang('My Profile')</a></li>
                                <li><a class="dropdown-item" href="{{route('user.logout')}}">@lang('Logout')</a></li>
                            </ul>
                        </div>
                    @else
                        @if (Route::is('user.login'))
                            <a class="btn btn--signup" href="{{ route('user.register') }}"> @lang('Sign Up') </a>
                        @else
                            @if (in_array(request()->route()->getName(), ['home', 'category.games', 'game.markets']))
                                <button class="btn btn--signup" data-bs-toggle="modal" data-bs-target="#loginModal" type="button">
                                    <i class="la la-sign-in"></i> @lang('Login')
                                </button>
                            @else
                                <a class="btn btn--signup" href="{{ route('user.login') }}">
                                    <i class="la la-sign-in"></i> @lang('Login')
                                </a>
                            @endif
                        @endif
                    @endif
                </li>
            </ul>
        </nav>
    </div>

    @php
        $loginContent = getContent('login.content', true);
    @endphp

    @if (in_array(request()->route()->getName(), ['home', 'category.games', 'game.markets']))
        <div class="modal fade login-modal" id="loginModal" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body p-3 p-sm-5">
                        <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                            <i class="las la-times"></i>
                        </span>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h4 class="mt-0">{{ __(@$loginContent->data_values->heading) }}</h4>
                        </div>
                        @include($activeTemplate . 'partials.login')
                    </div>
                </div>
            </div>
        </div>
    @endif
