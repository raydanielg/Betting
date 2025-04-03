@extends($activeTemplate . 'layouts.app')
@section('content')
    <header class="header-primary">
        <div class="container-fluid">
            @include($activeTemplate . 'partials.header')
        </div>
    </header>
    <main class="home-page">
        @include($activeTemplate . 'partials.category')
        <div class="sports-body">
            <div class="row g-3">
                @yield('bet')
                <div class="col-12">
                    <div class="footer footer--light">
                        @include($activeTemplate . 'partials.footer_top')
                    </div>
                </div>
                <div class="col-12">
                    <div class="footer-bottom">
                        <div class="container-fluid">
                            @include($activeTemplate . 'partials.footer_bottom')
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="betslip">
            <div class="betslip-header">
                <div class="list-group bet-type">
                    <label for="betslips" class="bet-type__btn">
                        <input id="betslips" type="radio" name="bet-type" class="" checked>
                        <span>@lang('Bet Slip')</span>
                    </label>

                    <label for="mybets-btn" class="bet-type__btn">
                        <input id="mybets-btn" type="radio" name="bet-type" @checked(request()->has('mybets'))>
                        <span>@lang('My Bets')</span>
                    </label>
                </div>
            </div>
            <div class="bet-slip-container betslip-inner">
                @include($activeTemplate . 'partials.bet_slip')
            </div>

            <div class="mybet-container betslip-inner">
                @include($activeTemplate . 'partials.my_bets')
            </div>
        </div>
        @include($activeTemplate . 'partials.mobile_menu')
    </main>
@endsection


