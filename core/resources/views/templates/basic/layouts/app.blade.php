<!doctype html>
<html lang="{{ config('app.locale') }}" itemscope itemtype="http://schema.org/WebPage">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> {{ gs()->siteName(__($pageTitle)) }}</title>
    @include('partials.seo')
    <link href="{{ asset('assets/global/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/global/css/all.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/custom-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">
    <link href="{{ asset($activeTemplateTrue . 'css/slick.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/magnific-popup.css') }}" rel="stylesheet">
    <link href="{{ asset($activeTemplateTrue . 'css/simplebar.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
    <link href="{{ asset($activeTemplateTrue . 'css/main.css') }}" rel="stylesheet">
    @stack('style-lib')
    <link href="{{ asset($activeTemplateTrue . 'css/custom.css') }}" rel="stylesheet">
    @stack('style')
    <link rel="stylesheet" href="{{ asset($activeTemplateTrue . 'css/color.php') }}?color={{ gs('base_color') }}">
</head>

@php echo loadExtension('google-analytics') @endphp

<body>
    <div class="preloader">
        <div class="preloader__img">
            <img src="{{ siteFavicon() }}" alt="@lang('image')" />
        </div>
    </div>

    <div class="back-to-top">
        <span class="back-top">
            <i class="las la-angle-double-up"></i>
        </span>
    </div>

    <div class="body-overlay" id="body-overlay"></div>
    <div class="header-overlay"></div>

    @yield('content')

    @php
        $cookie = App\Models\Frontend::where('data_keys', 'cookie.data')->first();
    @endphp

    @if ($cookie->data_values->status == Status::ENABLE && !\Cookie::get('gdpr_cookie'))
        <div class="cookies-card text-center hide">
            <div class="cookies-card__icon bg--base">
                <i class="las la-cookie-bite"></i>
            </div>
            <p class="mt-4 cookies-card__content">{{ $cookie->data_values->short_desc }} <a href="{{ route('cookie.policy') }}" target="_blank">@lang('learn more')</a></p>
            <div class="cookies-card__btn mt-4">
                <a href="javascript:void(0)" class="btn btn--xl btn--base w-100 policy">@lang('Allow')</a>
            </div>
        </div>
    @endif

    @auth
        <div class="modal custom--modal" data-bs-backdrop="static" data-bs-keyboard="false" id="betModal" aria-hidden="true" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body">
                        <form id="betForm" action="{{ route('user.bet.place') }}" method="POST">
                            @csrf
                            <h4 class="text-center mt-0">@lang('Place Your Bet')</h4>

                            <input name="stake_amount" type="hidden">
                            <input name="type" type="hidden">
                            <h6 class="mb-0">@lang('Review Before Placing')</h6>
                            <ul class="list-group mb-3 list-group-flush">
                                <li class="list-group-item border-0 px-0 d-flex flex-column">
                                    <small class="text-muted">@lang('Current Balance')</small>
                                    <h6 class="m-0" id="userBalance">{{ showAmount(auth()->user()->balance) }} <a href="{{route('user.deposit.index')}}" class="btn btn--sm btn--success"> <i class="la la-plus"></i> @lang('Add Balance')</a></h6>
                                </li>

                                <li class="list-group-item border-0 px-0 d-flex flex-column">
                                    <small class="text-muted">@lang('Stake Amount') </small>
                                    <h4 class="m-0" id="betStakeAmount"></h4>
                                </li>
                                <li class="list-group-item border-0 px-0 d-flex flex-column">
                                    <small class="text-muted">@lang('Return Amount')</small>
                                    <h3 class="m-0" id="betReturnAmount"></h3>
                                </li>
                            </ul>
                        </form>

                        <small class="mb-3 d-block">
                            <i class="la la-info-circle"></i> @lang('Once confirmed, your bet cannot be canceled or modified. Please review your selection carefully before proceeding.')
                        </small>

                        <h5 class="text-center">@lang('Do you want to proceed?')</h5>

                        <div class="d-flex gap-3">
                            <button type="button" class="btn btn--lg btn--dark flex-grow-1" data-bs-dismiss="modal">@lang('Cancel')</button>
                            <button type="submit" class="btn btn--lg btn--base flex-grow-1" form="betForm">@lang('Confirm Bet')</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endauth

    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>

    @stack('script-lib')

    @php echo loadExtension('tawk-chat') @endphp

    @include('partials.notify')

    @if (gs('pn'))
        @include('partials.push_script')
    @endif

    <script src="{{ asset($activeTemplateTrue . 'js/slick.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/jquery.magnific-popup.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/simplebar.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/jquery.stepcycle.js') }}"></script>
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
    <script src="{{ asset($activeTemplateTrue . 'js/app.js') }}"></script>
    @stack('script')

    <script>
        (function($) {
            "use strict";

            $(".langSel").on("click", function() {
                window.location.href = "{{ route('home') }}/change/" + $(this).data('code');
            });

            $(".oddsType").on("change", function() {
                window.location.href = `{{ route('odds.type', '') }}/${$(this).val()}`;
            });

            $('.policy').on('click', function() {
                $.get('{{ route('cookie.accept') }}', function(response) {
                    $('.cookies-card').addClass('d-none');
                });
            });

            setTimeout(function() {
                $('.cookies-card').removeClass('hide')
            }, 2000);

            var inputElements = $('[type=text],select,textarea');

            $.each(inputElements, function(index, element) {
                element = $(element);
                element.closest('.form-group').find('label').attr('for', element.attr('name'));
                element.attr('id', element.attr('name'))
            });

            $.each($('input:not([type=checkbox]):not([type=hidden]), select, textarea'), function(i, element) {
                var elementType = $(element);
                if (elementType.attr('type') != 'checkbox') {
                    if (element.hasAttribute('required')) {
                        $(element).closest('.form-group').find('label').addClass('required');
                    }
                }
            });

            let disableSubmission = false;

            $('.disableSubmission').on('submit', function(e) {
                if (disableSubmission) {
                    e.preventDefault()
                } else {
                    disableSubmission = true;
                }
            });

            $.each($(".select2"), function() {
                $(this).wrap(`<div class="position-relative"></div>`).select2({
                    dropdownParent: $(this).parent(),
                });
            });

            Array.from(document.querySelectorAll('table')).forEach(table => {
                let heading = table.querySelectorAll('thead tr th');
                Array.from(table.querySelectorAll('tbody tr')).forEach(row => {
                    Array.from(row.querySelectorAll('td')).forEach((column, i) => {
                        (column.colSpan == 100) || column.setAttribute('data-label', heading[i].innerText)
                    });
                });
            });

        })(jQuery);

        function showAmount(amount, decimal = 2, separate = true, exceptZeros = false, currencyFormat = true) {
            amount *= 1;

            const settings = {
                currencyFormat: @json(gs('currency_format')),
                currencySymbol: @json(gs('cur_sym')),
                currencyText: @json(gs('cur_text')),
            }

            let separator = separate ? ',' : '';
            let printAmount = amount.toFixed(decimal).replace(/\B(?=(\d{3})+(?!\d))/g, separator);

            if (exceptZeros) {
                let parts = printAmount.split('.');
                if (parseInt(parts[1]) === 0) {
                    printAmount = parts[0];
                } else {
                    printAmount = printAmount.replace(/0+$/, '');
                }
            }

            if (currencyFormat) {
                if (settings.currencyFormat === @json(Status::CUR_BOTH)) {
                    return settings.currencySymbol + printAmount + ' ' + settings.currencyText;
                } else if (settings.currencyFormat ===  @json(Status::CUR_TEXT)) {
                    return printAmount + ' ' + settings.currencyText;
                } else {
                    return settings.currencySymbol + printAmount;
                }
            }

            return printAmount;
        }
    </script>
</body>

</html>
