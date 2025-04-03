<!-- meta tags and other links -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ gs()->siteName($pageTitle ?? '') }}</title>

    <link rel="shortcut icon" type="image/png" href="{{ siteFavicon() }}">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/global/css/bootstrap.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/bootstrap-toggle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/line-awesome.min.css') }}">

    @stack('style-lib')

    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/iziToast_custom.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/global/css/custom-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/css/custom.css') }}">

    <style>
        table .user .thumb img {
            border: 2px solid #f3f3f3;
            box-shadow: none;
        }

        .select2-container--default .select2-results__group {
            cursor: default;
            display: block;
            padding: 6px;
            color: #626262;
            font-weight: 500;
        }
        .pointer-events-none {
            pointer-events: none;
        }
        .pointer-events-auto {
            pointer-events: auto;
        }
    </style>

    @stack('style')
</head>

<body>
    @yield('content')

    <script src="{{ asset('assets/global/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/bootstrap-toggle.min.js') }}"></script>

    @include('partials.notify')
    @stack('script-lib')

    <script src="{{ asset('assets/global/js/nicEdit.js') }}"></script>

    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/app.js') }}"></script>

    {{-- LOAD NIC EDIT --}}
    <script>
        "use strict";
        bkLib.onDomLoaded(function() {
            $(".nicEdit").each(function(index) {
                $(this).attr("id", "nicEditor" + index);
                new nicEditor({
                    fullPanel: true
                }).panelInstance('nicEditor' + index, {
                    hasPanel: true
                });
            });
        });
        (function($) {
            $(document).on('mouseover ', '.nicEdit-main,.nicEdit-panelContain', function() {
                $('.nicEdit-main').focus();
            });

            $('.breadcrumb-nav-open').on('click', function() {
                $(this).toggleClass('active');
                $('.breadcrumb-nav').toggleClass('active');
            });

            $('.breadcrumb-nav-close').on('click', function() {
                $('.breadcrumb-nav').removeClass('active');
            });

            if ($('.topTap').length) {
                $('.breadcrumb-nav-open').removeClass('d-none');
            }

            $('.makeSlug').on('input', function(e) {
                let name = this.value;
                let slug = name.replace(/\s+/g, '-').toLowerCase();
                $('[name=slug]').val(slug);
            });

            $('.checkSlug').on('keyup', function(e) {
                var keyCode = e.keyCode || e.which;
                var regex = /^[A-Za-z0-9]+$/;
                var isValid = regex.test(String.fromCharCode(keyCode));
                if (e.keyCode == 32) {
                    $(this).val($(this).val().replace(/\s+/g, '-'));
                }
            });
        })(jQuery);
    </script>

    @stack('script')

</body>

</html>
