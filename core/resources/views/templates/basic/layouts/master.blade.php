@extends($activeTemplate . 'layouts.app')
@section('content')
    @include($activeTemplate . 'partials.user_header')
    <div class="user-dashboard">
        <div class="container">
            <div class="dashboard-wrapper">
                @include($activeTemplate . 'partials.dashboard_sidebar')
                <div class="dashboard-right">
                    @if (!Route::is('user.home'))
                        {{-- Breadcrumb --}}
                        <div class="d-flex justify-content-between gap-3 mb-4">
                            <h5 class="m-0">{{ __($pageTitle) }}</h5>

                            <div class="ms-auto">
                                @stack('breadcrumb-items')
                            </div>
                        </div>
                    @endif

                    @yield('master')
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid mt-auto px-0">
        <div class="footer-bottom footer-bottom--dark">
            <div class="container">
                @include($activeTemplate . 'partials.footer_bottom')
            </div>
        </div>
    </div>

    @include($activeTemplate . 'partials.dashboard_mobile_menu')
@endsection

@push('style-lib')
    <link href="{{ asset($activeTemplateTrue . 'css/dashboard.css') }}" rel="stylesheet">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.showFilterBtn').on('click', function() {
                $('.responsive-filter-card').slideToggle();
            });

            function formatState(state) {
                if (!state.id) return console.log(state.text);
                state.text;
                let gatewayData = $(state.element).data();
                return $(`<div class="d-flex gap-2">${gatewayData.imageSrc ? `<div class="select2-image-wrapper"><img class="select2-image" src="${gatewayData.imageSrc}"></div>` : '' }<div class="select2-content"> <p class="select2-title">${gatewayData.title}</p><p class="select2-subtitle">${gatewayData.subtitle}</p></div></div>`);
            }

            $('.select2').each(function(index, element) {
                $(element).select2();
            });


            $('.select2-basic').each(function(index, element) {
                $(element).select2({
                    dropdownParent: $(element).closest('.select2-parent')
                });
            });
        })(jQuery)
    </script>
@endpush
