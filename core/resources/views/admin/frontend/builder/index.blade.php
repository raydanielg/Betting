@extends('admin.layouts.app')
@section('panel')
    @if ($pData->is_default == Status::NO)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('admin.frontend.manage.pages.update') }}" method="POST">
                            @csrf
                            <input name="id" type="hidden" value="{{ $pData->id }}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between">
                                            <label> @lang('Page Name')</label>
                                            <a href="javascript:void(0)" class="buildSlug"><i class="las la-link"></i> @lang('Make Slug')</a>
                                        </div>
                                        <input class="form-control" name="name" type="text"
                                               value="{{ $pData->name }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="d-flex justify-content-between">
                                            <label> @lang('Page Slug')</label>
                                            <div class="slug-verification d-none"></div>
                                        </div>
                                        <input class="form-control" name="slug" type="text"
                                               value="{{ $pData->slug }}" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button class="btn btn--primary w-100 h-45"
                                                type="submit">@lang('Submit')</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-1 g-lg-3">
        <div class="col-6 col-lg-6 col-xl-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __($pData->name) }} @lang('Page')</h3>
                </div>

                <div class="card-body">
                    <div class="submitRequired bg--warning d-none form-change-alert"><i
                           class="fas fa-exclamation-triangle"></i>
                        @lang('You\'ve to click on the Update Now button to apply the changes')</div>
                    <form action="{{ route('admin.frontend.manage.section.update', $pData->id) }}" method="post">
                        @csrf
                        <ol id="page_sections" class="simple_with_drop vertical sec-item">
                            @if ($pData->secs != null)
                                @foreach (json_decode($pData->secs) as $sec)
                                    <li class="sortable-item highlight icon-move item">
                                        <i class="sortable-icon"></i>
                                        <span class="d-inline-block me-auto"> {{ __(@$sections[$sec]['name']) }}</span>
                                        <i class="ms-auto d-inline-block remove-icon remove-icon-color la la-trash"></i>
                                        <input name="secs[]" type="hidden" value="{{ $sec }}">
                                    </li>
                                @endforeach
                            @else
                                <li class="empty-state">
                                    <span>@lang('Drag & drop your section here')</span>
                                </li>
                            @endif
                        </ol>
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Update Now')</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-6 col-xl-4">

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">@lang('Sections')</h3>
                    <small class="text--primary">@lang('Drag the section to the left side you want to show on the page.')</small>
                </div>

                <div class="card-body">
                    <ol id="sections_items" class="simple_with_no_drop vertical">
                        @foreach ($sections as $k => $secs)
                            @if (!@$secs['no_selection'])
                                <li class="highlight icon-move clearfix" data-key="{{ $k }}">
                                    <i class="sortable-icon"></i>
                                    <span class="d-inline-block me-auto"> {{ __($secs['name']) }}</span>
                                    <i class="ms-auto d-inline-block remove-icon remove-icon-color la la-trash"></i>
                                    @if ($secs['builder'])
                                        <div class="float-end d-inline-block manage-content">
                                            <a class="btn cog-btn text-center"
                                               href="{{ route('admin.frontend.sections', $k) }}" target="_blank">
                                                <i class="la la-cog m-0 p-0"></i>
                                            </a>
                                        </div>
                                    @endif
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@push('script-lib')
    <script src="{{ asset('assets/admin/js/jquery-ui.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            var initialSections = getSectionKeys();

            function getSectionKeys() {
                return $(document).find('#page_sections input[name="secs[]"]').map(function() {
                    return $(this).val();
                }).get();
            }

            $("#page_sections").sortable({
                items: "li:not(.empty-state)",
                update: () => handleShowSubmissionAlert()
            });

            $("#sections_items li").draggable({
                stop: function(event, ui) {
                    const element = ui.helper;
                    const key = element.data('key');
                    element.append(`<input type="hidden" name="secs[]" value="${key}">`)

                    if ($('#page_sections').children().length == 0) {
                        watchState(true);
                    }
                    handleShowSubmissionAlert();
                    $('#page_sections').removeClass('dropping');
                },
                start: function(event, ui, offset) {
                    const height = $('.empty-state').outerHeight();

                    if ($('#page_sections').children().length == 1) {
                        $('.empty-state').remove();
                    }

                    $('#page_sections').addClass('dropping').css('min-height', `${height}px`);
                },
                helper: function() {
                    var originalElement = $(this);
                    var originalWidth = '100%';
                    var clonedElement = originalElement.clone();
                    clonedElement.css('width', originalWidth);
                    const len = $('#page_sections').children().length;
                    return clonedElement;
                },
                connectToSortable: '#page_sections'
            });

            $("#page_sections").droppable({
                accept: '#sections_items li',
                drop: function(event, ui) {
                    let originalWidth = $(event.target).width();
                    $(this).append(ui.draggable);
                    ui.draggable.removeAttr('style');
                    ui.draggable.removeClass();
                    ui.draggable.addClass('highlight icon-move item ui-sortable-handle').css('height',
                        'auto');
                }
            });

            $(document).on('click', ".remove-icon", function() {
                $(this).parent('.highlight').remove();
                handleShowSubmissionAlert();
                watchState();
            });

            function watchState(override = false) {
                if ($('#page_sections').children().length == 0 || override) {
                    $('#page_sections').html(`<li class="empty-state">
                        <span>@lang('Drag & drop your section here')</span>
                    </li>`);
                }
            }

            function handleShowSubmissionAlert() {
                const arraysAreEqual = (arr1, arr2) => JSON.stringify(arr1) === JSON.stringify(arr2);
                if (!arraysAreEqual(initialSections, getSectionKeys())) {
                    $('.submitRequired').removeClass('d-none');
                }
            }



            $('.buildSlug').on('click', function() {
                let closestForm = $(this).closest('form');
                let title = closestForm.find('[name=name]').val();
                closestForm.find('[name=slug]').val(title);
                closestForm.find('[name=slug]').trigger('input');
            });

            $('[name=slug]').on('input', function() {
                let closestForm = $(this).closest('form');
                closestForm.find('[type=submit]').addClass('disabled')
                let slug = $(this).val();
                slug = slug.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
                $(this).val(slug)
                if (slug) {
                    $('.slug-verification').removeClass('d-none');
                    $('.slug-verification').html(`
                        <small class="text--info"><i class="las la-spinner la-spin"></i> @lang('Checking')</small>
                    `);
                    $.get("{{ route('admin.frontend.manage.pages.check.slug', $pData->id) }}", {
                        slug: slug
                    }, function(response) {
                        if (!response.exists) {
                            $('.slug-verification').html(`
                                <small class="text--success"><i class="las la-check"></i> @lang('Available')</small>
                            `);
                            closestForm.find('[type=submit]').removeClass('disabled')
                        }
                        if (response.exists) {
                            $('.slug-verification').html(`
                                <small class="text--danger"><i class="las la-times"></i> @lang('Slug already exists')</small>
                            `);
                        }
                    });
                } else {
                    $('.slug-verification').addClass('d-none');
                }
            })
        })(jQuery);
    </script>
@endpush

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.frontend.manage.pages') }}" />
@endpush

@push('style')
    <style>
        .simple_with_drop,
        .simple_with_no_drop {
            user-select: none;
        }

        .icon-move .sortable-icon {
            font-family: "Line Awesome Free";
            font-weight: 900;
            font-style: normal;
            font-size: 14px;
        }

        .simple_with_no_drop .sortable-icon:before {
            content: "\f060";
        }

        .simple_with_drop .sortable-icon:before {
            content: "\f2a1";
        }

        .span4 {
            width: 300px;
        }

        ol li.highlight {
            background: #000;
            color: #464646;
        }

        ol.vertical {
            margin: 0 0 9px 0;
            min-height: 10px;
        }

        li {
            line-height: 18px;
        }

        .icon-move {
            background-position: -168px -72px;
        }

        ol i.icon-move {
            cursor: pointer;
        }

        ol {
            display: block;
            list-style-type: decimal;
            margin-block-start: 1em;
            margin-block-end: 1em;
            margin-inline-start: 0px;
            margin-inline-end: 0px;
        }

        .vertical li i {
            color: #777777 !important;
            padding-right: 10px;
        }

        .sec-item li i {
            color: #000000;
        }

        .sec-item li i.fa-times {
            color: #ea5455;
            padding-right: 15px;
        }

        ol.vertical li {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 10px 0;
            padding: 10px;
            color: #474747;
            background: #fdfdfd;
            font-size: 16px;
            font-weight: 600;
            border-radius: .5rem;
            cursor: move;
            position: relative;
            min-height: 55px;
            border: 1px solid rgb(0 0 0 / 6%);
        }

        ol.sec-item li {
            margin: 10px 0;
            padding: 10px;
            color: #424242 !important;
            background: #fdfdfd;
            font-size: 24px;
            font-weight: 600;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            border-radius: .5rem;
            border: 1px solid rgb(0 0 0 / 6%);
        }

        .ol.sec-item li.d-none {
            display: none !important;
        }

        [class*="span"] {
            float: left;
            margin-left: 20px;
        }

        .row {
            *zoom: 1;
        }

        .row {
            position: relative;
        }

        .dragged {
            display: none !important;
        }

        ol.vertical li i.remove-icon {
            display: none !important;
        }

        ol.sec-item li i.remove-icon {
            display: block !important;
            cursor: pointer;
        }

        ol.sec-item li .manage-content {
            display: none !important;
        }

        ol.vertical li span {
            font-size: 0.938rem;
        }

        .cog-btn i {
            color: #fff !important
        }

        .cog-btn:hover i {
            color: #4634ff !important
        }

        .simple_with_drop .remove-icon {
            font-size: 20px;
            color: #8c8c8c !important;
        }

        .bodywrapper__inner {
            overflow: hidden;
        }

        @media(max-width: 767px) {
            .vertical li span {
                font-size: 12px !important;
            }

            .manage-content,
            .simple_with_drop .remove-icon {
                position: absolute;
                top: 50%;
                right: 5px;
                transform: translateY(-50%);
            }
        }

        @media(max-width: 480px) {

            ol.sec-item li,
            ol.vertical li {
                padding-right: 30px;
                flex-wrap: nowrap;
            }

            .vertical li span {
                font-size: 10px !important;
                display: block !important;
                line-height: 12px;
            }

            a.btn.cog-btn {
                padding: 0;
            }

            .simple_with_drop i,
            .simple_with_no_drop i {
                font-size: 15px !important;
            }

            .btn--primary {
                height: 32px;
                line-height: 1;
                font-size: 12px;
            }

            .card-title {
                font-size: 18px
            }

            .text--primary {
                font-size: 12px;
                display: block;
            }
        }

        .empty-state {
            border: 2px dotted #ccc !important;
            text-align: center !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 3rem !important;
            cursor: default !important;
        }

        #page_sections.dropping {
            border: 2px solid #4634FF;
            border-radius: 10px;
            border: 2px dotted #ccc !important;
            padding: 0 1rem !important;
        }

        .remove-icon-color {
            color: #777777 !important;
        }

        .remove-icon-color:hover {
            color: #ea5455 !important;
        }
    </style>
@endpush
