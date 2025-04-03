@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex mb-3 justify-content-between align-items-center flex-wrap gap-2">
                        <h4>@lang('Content Management Options')</h4>
                        <div class="position-relative">
                            <div class="system-search-icon"><i class="las la-search"></i></div>
                            <input class="form-control searchInput" type="search" placeholder="@lang('Search')...">
                        </div>
                    </div>
                    <div class="row gy-4">
                        <div class="col-12 m-0">
                            <div class="emptyArea"></div>
                        </div>
                        @foreach (getPageSections(true) as $k => $secs)
                            @if ($secs['builder'] && !@$secs['hide_builder'])
                                <div class="col-md-3 searchItem">
                                    <div class="frontend-section-card">
                                        <h6>{{ __($secs['name']) }}</h6>
                                        <a href="{{ route('admin.frontend.sections', $k) }}" class="btn btn--light btn-sm"><i class="las la-cog me-0"></i></a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .frontend-section-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ededed;
            padding: 15px;
            border-radius: 5px;
            background: #fff;
            transition: all .2s;
        }

        .frontend-section-card:hover {
            background: #e7e7e7;
        }

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

        .searchInput {
            border: 1px solid #ededed;
        }

        .system-search-icon~.form-control {
            padding-left: 45px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            var searchInput = $('.searchInput');
            var searchItem = $('.searchItem');

            var emptyArea = $('.emptyArea');
            var emptyHtml = `<div class="searchItem text-center mt-4"><div class="empty-notification-list text-center">
                        <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                            <h5 class="text-muted">@lang('No search result found')</h5>
                    </div></div>`;

            searchInput.on('input', function() {
                var searchInput = $(this).val().toLowerCase();
                var empty = true;

                searchItem.filter(function(idx, elem) {

                    if ($(elem).find('.frontend-section-card h6').text().trim().toLowerCase().indexOf(searchInput) >= 0) {
                        $(elem).show();
                        emptyArea.empty();
                        empty = false;
                    } else {
                        $(elem).hide();
                    }

                }).sort();

                if (empty) {
                    emptyArea.html(emptyHtml);
                }

            });

        })(jQuery);
    </script>
@endpush
