@extends('admin.layouts.app')
@section('panel')
    <div class="alert alert-info p-3 flex-column" role="alert">
        <p>
            @lang('If you update the system from an older version and have already added categories, please match the list with the Odds API Sports List and update the "Name in API" field if it matches.')
        </p>
    </div>

    <div class="row gy-3">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Name in API')</th>
                                    <th>@lang('Slug')</th>
                                    <th>@lang('Icon')</th>
                                    <th>@lang('Leagues')</th>
                                    <th>@lang('Teams')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($categories as $category)
                                    <tr>
                                        <td>{{ __($category->name) }}</td>
                                        <td>{{ __($category->odds_api_name ?? '...') }}</td>
                                        <td>{{ $category->slug }}</td>
                                        <td>@php echo $category->icon @endphp</td>
                                        <td>{{ $category->leagues_count }}</td>
                                        <td>{{ $category->teams_count }}</td>
                                        <td>
                                            @php echo $category->statusBadge @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary cuModalBtn editBtn" data-resource="{{ $category }}" data-modal_title="@lang('Edit Category')">
                                                    <i class="la la-pencil"></i>@lang('Edit')
                                                </button>

                                                @if ($category->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="@lang('Are you sure to enable this category?')" data-action="{{ route('admin.category.status', $category->id) }}">
                                                        <i class="la la-eye"></i> @lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to disable this category?')" data-action="{{ route('admin.category.status', $category->id) }}">
                                                        <i class="la la-eye-slash"></i> @lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($categories->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($categories) }}
                    </div>
                @endif

            </div>
        </div>
    </div>

    <div id="cuModal" class="modal modal-lg fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.category.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">

                        <div class="alert alert-info p-3 flex-column" role="alert">
                            <h4>@lang('Automatic Add Info')</h4>

                            <p>
                                @lang('You can manually add new categories here or import them from the API. If the "Name in API" field matches the Odds API sports group name, all leagues in that category will be automatically fetched when the Fetch Leagues cron job runs on the server. Otherwise, you will need to add them manually.')
                                <span class="text--danger">@lang('If the "Name in API" does not match the sports group name in the Odds API, leagues for that category cannot be fetched.')</span>

                                <a href="https://the-odds-api.com/sports-odds-data/sports-apis.html" target="blank">@lang('Click here to see the Odds API sports group names')</a>
                            </p>
                        </div>


                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input type="text" class="form-control makeSlug" name="name" value="{{ old('name') }}" required />
                                </div>

                                <div class="form-group">
                                    <label>@lang('Name in API')</label>
                                    <input type="text" class="form-control" name="odds_api_name" value="{{ old('odds_api_name') }}" />
                                    <small class="text-muted"><i class="la la-info-circle"></i> @lang('To fetch the odds correctly from the API, use the exact name as listed in the "Group" column of the table on the Sports List in the Odds API. Ensure the name matches exactly for accurate data retrieval.') <a href="https://the-odds-api.com/sports-odds-data/sports-apis.html" target="blank">@lang('View Sports List').</a></small>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Slug')</label>
                                    <input type="text" class="form-control checkSlug" name="slug" value="{{ old('slug') }}" required />
                                    <code>@lang('Spaces are not allowed')</code>
                                </div>
                                <div class="form-group sportsIconParent">
                                    <label>@lang('Icon')</label>
                                    <input type="text" class="form-control sportsIcon" autocomplete="off" name="icon" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />

    <!-- Modal -->
    <div class="modal fade" id="fetchCategoriesModal" tabindex="-1" aria-labelledby="fetchCategoriesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fetchCategoriesModalLabel">@lang('Add Categories from API')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i class="la la-times"></i></button>
                </div>

                <div class="modal-body">
                    <form action="{{ route('admin.category.fetched.save') }}" method="post" id="addCategoriesForm">
                        @csrf
                        <div class="categories-list"></div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100 h-45" form="addCategoriesForm">@lang('Add Selected Category')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Name" />

    <button type="button" class="btn btn-outline--dark" data-bs-toggle="modal" data-bs-target="#fetchCategoriesModal">
        <i class="la la-sync"></i> @lang('Fetch Categories')
    </button>

    <button type="button" class="btn btn-sm btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add New Category')">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('style-lib')
    <link href="{{ asset('assets/admin/css/sports-iconpicker.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/sport-icons-picker.js') }}"></script>
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
@endpush

@push('style')
    <style>
        td .custom-icon {
            font-size: 1.5rem;
        }

        input:focus {
            box-shadow: none !important;
        }
    </style>
@endpush


@push('script')
    <script>
        (function($) {
            "use strict";
            const categoryModal = $('#fetchCategoriesModal');

            categoryModal.on('show.bs.modal', function(e) {
                categoryModal.find('.categories-list').html(`<div class="text-center p-5"><i class="la la-circle-notch la-spin la-3x text-muted"></i></div>`);

                fetchCategories();
            });


            function fetchCategories() {
                $.get("{{ route('admin.category.fetch') }}",
                    function(response) {
                        if (response.status == 'error') {
                            categoryModal.find('.categories-list').html(`<h6 class="p-3 text-center text--danger">${response.message}</h6>`);
                        } else {
                            if (response.categories) {
                                if (response.categories.length) {
                                    let result = `<h6 class="text-center mb-3">@lang('Choose categories to add from the list below')</h6>`;
                                    response.categories.forEach((category) => {
                                        result += `
                                            <div class="form-check">
                                                <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input my-0" name="categories[]" id="category-slug" value="${category}">
                                                    ${category}
                                                </label>
                                            </div>`
                                    });
                                    categoryModal.find('.categories-list').html(result);
                                } else {
                                    categoryModal.find('.categories-list').html(`<p class="p-3 text-center">No more categories available</p>`);
                                }

                            }
                        }
                    }
                );
            }

        })(jQuery);
    </script>
@endpush
