@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-12">

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="form-group position-relative mb-0">
                                <div class="system-search-icon"><i class="las la-search"></i></div>
                                <input class="form-control searchInput" type="search" placeholder="@lang('Search')...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row gy-4">
                <div class="col-12">
                    <div class="emptyArea"></div>
                </div>
                @foreach ($settings as $key => $setting)
                    <div class="col-xxl-4 col-md-6 {{ $key }} searchItems">
                        @php
                            $params = null;
                            if (@$setting->params) {
                                foreach ($setting->params as $paramVal) {
                                    $params[] = array_values((array) $paramVal)[0];
                                }
                            }
                        @endphp
                        <x-widget style="2" link="{{ route($setting->route_name, $params) }}" icon="{{ $setting->icon }}" heading="{{ $setting->title }}" subheading="{{ $setting->subtitle }}" cover_cursor=1 icon_style="fill" color="primary" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/admin/js/highlighter22.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            var settingsData = @json($settings);
            // Function to filter settings based on search query
            function filterSettings(query) {
                let filteredSettings = [];
                for (var key in settingsData) {
                    if (settingsData.hasOwnProperty(key)) {
                        var setting = settingsData[key];
                        // Check if the query matches keyword, title, or subtitle
                        var keywordMatch = setting.keyword.some(function(keyword) {
                            return keyword.toLowerCase().includes(query.toLowerCase());
                        });
                        var titleMatch = setting.title.toLowerCase().includes(query.toLowerCase());
                        var subtitleMatch = setting.subtitle.toLowerCase().includes(query.toLowerCase());

                        // If any match is found, add the setting to filtered settings
                        if (keywordMatch || titleMatch || subtitleMatch) {
                            filteredSettings[key] = setting;
                        }
                    }
                }
                return filteredSettings;
            }

            function isEmpty(obj) {
                return Object.keys(obj).length === 0;
            }

            // Function to render filtered settings
            function renderSettings(filteredSettings, query) {
                $('.searchItems').addClass('d-none');
                $('.emptyArea').html('');
                if (isEmpty(filteredSettings)) {
                    $('.emptyArea').html(`<div class="col-12 searchItems text-center mt-4"><div class="card">
                                <div class="card-body">
                                    <div class="empty-search text-center">
                                        <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                                        <h5 class="text-muted">@lang('No search result found.')</h5>
                                    </div>
                                </div>
                            </div>
                        </div>`);
                } else {
                    for (const key in filteredSettings) {
                        if (Object.hasOwnProperty.call(filteredSettings, key)) {
                            const element = filteredSettings[key];
                            var setting = element;
                            $(`.searchItems.${key}`).removeClass('d-none');
                        }
                    }
                }
            }


            $('.searchInput').on('input', function() {
                var query = $(this).val().trim();
                var filteredData = filterSettings(query);
                renderSettings(filteredData, query);
            });

            $('.searchInput').highlighter22({
                targets: [".widget-two__content h3", ".widget-two__content p"],
            });
            $('.searchInput').focus();

        })(jQuery);
    </script>
@endpush

@push('style')
    <style>
        .system-search-icon {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            aspect-ratio: 1;
            padding: 5px;
            display: grid;
            place-items: center;
            color: #888;
        }

        .system-search-icon~.form-control {
            padding-left: 45px;
        }

        .widget-seven .widget-seven__content-amount {
            font-size: 22px;
        }

        .widget-seven .widget-seven__content-subheading {
            font-weight: normal;
        }

        .empty-search img {
            width: 120px;
            margin-bottom: 15px;
        }

        a.item-link:focus,
        a.item-link:hover {
            background: #4634ff38;
        }
    </style>
@endpush
