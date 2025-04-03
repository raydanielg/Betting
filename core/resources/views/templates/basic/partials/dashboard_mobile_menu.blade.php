<div class="app-nav">
    <div class="container-fluid">
        <div class="row g-0">
            <div class="col-12">
                <ul class="app-nav__menu list list--row justify-content-between align-items-center">
                    <li>
                        <a class="app-nav__menu-link active" href="{{ route('home') }}">
                            <span class="app-nav__menu-icon">
                                <img src="{{ asset($activeTemplateTrue . 'images/bet-now.png') }}" alt="@lang('image')">
                            </span>
                            <span class="app-nav__menu-text"> @lang('Bet Now') </span>
                        </a>
                    </li>
                    <li>
                        <a class="app-nav__menu-link" href="{{ route('user.deposit.index') }}">
                            <span class="app-nav__menu-icon">
                                <img src="{{ asset($activeTemplateTrue . 'images/deposit.png') }}" alt="@lang('image')">
                            </span>
                            <span class="app-nav__menu-text"> @lang('Deposit') </span>
                        </a>
                    </li>

                    <li class="app-nav__menu-link-important-container">
                        <a class="app-nav__menu-link-important sidenav-toggler" href="javascript:void(0)">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>

                    <li>
                        <a class="app-nav__menu-link" href="{{ route('user.withdraw') }}">
                            <span class="app-nav__menu-icon">
                                <img src="{{ asset($activeTemplateTrue . 'images/withdraw.png') }}" alt="@lang('image')">
                            </span>
                            <span class="app-nav__menu-text"> @lang('Withdraw') </span>
                        </a>
                    </li>

                    <li>
                        <a class="app-nav__menu-link" href="{{ route('user.bets.all') }}">
                            <span class="app-nav__menu-icon">
                                <img src="{{ asset($activeTemplateTrue . 'images/my_bets.png') }}" alt="@lang('image')">
                            </span>
                            <span class="app-nav__menu-text">@lang('My Bets')</span>
                        </a>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</div>
