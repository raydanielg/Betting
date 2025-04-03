<div class="dashboard-sidebar">
    <div class="widget-card widget-card--primary">
        <div class="widget-card__head">
            <span class="widget-card__id"> <i class="la la-user"></i> {{ auth()->user()->username }}</span>
            <button class="btn widget-card__reload" id="reload" type="button">
                <i class="las la-sync"></i>
            </button>
        </div>
        <div class="widget-card__body">
            <h5 class="widget-card__balance">{{ showAmount(auth()->user()->balance) }}</h5>
            <span class="widget-card__balance-text">@lang('Current Balance')</span>
            <div class="d-flex gap-2">
                <a class="btn widget-card__withdraw flex-shrink-0 flex-grow-1" href="{{ route('user.withdraw') }}"> <i class="fa fa-minus"></i> @lang('Withdraw')</a>
                <a class="btn widget-card__deposit flex-shrink-0 flex-grow-1" href="{{ route('user.deposit.index') }}"> <i class="fa fa-plus"></i> @lang('Deposit')</a>
            </div>
        </div>
    </div>


    <div class="dashboard-menu overflow-hidden">
        <div class="dashboard-menu__body">
            <ul class="list dashboard-menu__list">
                <li>
                    <a class="dashboard-menu__link {{ menuActive('user.home') }}" href="{{ route('user.home') }}">
                        <span class="dashboard-menu__icon">
                            <i class="las la-home"></i>
                        </span>
                        <span class="dashboard-menu__text"> @lang('Dashboard') </span>
                    </a>
                </li>

                <li>
                    <a class="dashboard-menu__link {{ menuActive('user.bets.*') }}" href="{{ route('user.bets.all') }}">
                        <span class="dashboard-menu__icon">
                            <i class="las la-list"></i>
                        </span>
                        <span class="dashboard-menu__text"> @lang('My Bets') </span>
                    </a>
                </li>

                <li>
                    <a class="dashboard-menu__link {{ menuActive('user.deposit.history') }}" href="{{ route('user.deposit.history') }}">
                        <span class="dashboard-menu__icon">
                            <i class="las la-wallet"></i>
                        </span>
                        <span class="dashboard-menu__text"> @lang('Deposit History') </span>
                    </a>
                </li>

                <li>
                    <a class="dashboard-menu__link {{ menuActive('user.withdraw.history') }}" href="{{ route('user.withdraw.history') }}">
                        <span class="dashboard-menu__icon">
                            <i class="las la-list"></i>
                        </span>
                        <span class="dashboard-menu__text"> @lang('Withdrawal History') </span>
                    </a>
                </li>

                @if (gs('referral_program'))
                    <li>
                        <a class="dashboard-menu__link {{ menuActive('user.referral.commissions') }}" href="{{ route('user.referral.commissions') }}">
                            <span class="dashboard-menu__icon">
                                <i class="las la-sitemap"></i>
                            </span>
                            <span class="dashboard-menu__text"> @lang('Referral Commissions') </span>
                        </a>
                    </li>
                @endif

                <li>
                    <a class="dashboard-menu__link {{ menuActive('user.transactions') }}" href="{{ route('user.transactions') }}">
                        <span class="dashboard-menu__icon">
                            <i class="las la-exchange-alt"></i>
                        </span>
                        <span class="dashboard-menu__text"> @lang('Transactions') </span>
                    </a>
                </li>

                <li>
                    <a class="dashboard-menu__link {{ menuActive('ticket.*') }}" href="{{ route('ticket.index') }}">
                        <span class="dashboard-menu__icon">
                            <i class="las la-ticket-alt"></i>
                        </span>
                        <span class="dashboard-menu__text">@lang('Support Tickets')</span>
                    </a>
                </li>

                <li>
                    <a class="dashboard-menu__link {{ menuActive('user.profile.setting') }}" href="{{ route('user.profile.setting') }}">
                        <span class="dashboard-menu__icon">
                            <i class="las la-user-edit"></i>
                        </span>
                        @lang('My Profile')
                    </a>
                </li>

                <li>
                    <a class="dashboard-menu__link {{ menuActive('user.change.password') }}" href="{{ route('user.change.password') }}">
                        <span class="dashboard-menu__icon">
                            <i class="las la-lock"></i>
                        </span>
                        @lang('Change Password')
                    </a>
                </li>

                <li>
                    <a class="dashboard-menu__link {{ menuActive('user.twofactor') }}" href="{{ route('user.twofactor') }}">
                        <span class="dashboard-menu__icon">
                            <i class="las la-user-shield"></i>
                        </span>
                        @lang('2FA Security')
                    </a>
                </li>


                <li>
                    <a class="dashboard-menu__link" href="{{ route('user.logout') }}">
                        <span class="dashboard-menu__icon">
                            <i class="las la-sign-out-alt"></i>
                        </span>
                        <span class="dashboard-menu__text"> @lang('Logout') </span>
                    </a>
                </li>
            </ul>
        </div>

    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";

            $('#reload').on('click', function() {
                location.reload();
            });

            $('.dashboard-sidebar__nav-toggle-btn').on('click', function() {
                $('.body-overlay').toggleClass('active')
            });

            $('.dashboard-menu__head-close').on('click', function() {
                $('body').removeClass('.dashboard-menu-open')
                $('.body-overlay').removeClass('active')
            });

            $('.body-overlay').on('click', function() {
                $('.dashboard-menu__head-close').trigger('click')
                $('.body-overlay').removeClass('active')
            });
        })(jQuery);
    </script>
@endpush
