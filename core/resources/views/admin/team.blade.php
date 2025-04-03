@extends('admin.layouts.app')
@section('panel')

<div class="alert alert-info p-3" role="alert">
        <p>
            @lang('Teams will be automatically added when the Fetch Games or Fetch Odds cron job runs on the server. You can also add teams manually.')
        </p>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Short Name')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($teams as $team)
                                    <tr>
                                        <td>
                                            <div class="user">
                                                <div class="thumb">
                                                    <img src="{{ $team->teamImage() }}" alt="@lang('image')">
                                                </div>
                                                <span class="name">{{ __($team->name) }}</span>
                                            </div>
                                        </td>

                                        <td>{{ __($team->short_name) }}</td>
                                        <td>{{ __($team->category->name) }}</td>
                                        <td>
                                            @php
                                                $team->image_with_path = $team->teamImage();
                                            @endphp

                                            <button class="btn btn-sm btn-outline--primary cuModalBtn editBtn" data-category_id="{{ $team->category_id }}" data-image="{{ $team->image_with_path }}" data-resource="{{ $team }}" data-modal_title="@lang('Edit Team')" data-has_status="1" type="button">
                                                <i class="la la-pencil"></i>@lang('Edit')
                                            </button>
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

                @if ($teams->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($teams) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="cuModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form action="{{ route('admin.team.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label>@lang('Image')</label>
                                    <x-image-uploader image="{{ getImage(getFilePath('team'), getFileSize('team')) }}" class="w-100" type="team" :required=false />
                                </div>
                            </div>
                            <div class="col-lg-6">

                                <div class="form-group">
                                    <label>@lang('Category')</label>
                                    <select class="form-control select2" name="category_id" required>
                                        <option value="">@lang('Select One')</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>@lang('Name')</label>
                                    <input class="form-control makeSlug" name="name" type="text" value="{{ old('name') }}" required />
                                </div>

                                <div class="form-group">
                                    <label>@lang('Short Name')</label>
                                    <input class="form-control" name="short_name" type="text" value="{{ old('short_name') }}" required />
                                </div>

                                <div class="form-group">
                                    <label>@lang('Slug')</label>
                                    <input class="form-control checkSlug" name="slug" type="text" value="{{ old('slug') }}" required />
                                    <code>@lang('Spaces are not allowed')</code>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Name / Slug / Category" />
    <button class="btn btn-sm btn-outline--primary h-45 cuModalBtn" data-modal_title="@lang('Add New Team')" type="button">
        <i class="las la-plus"></i>@lang('Add New')
    </button>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/cu-modal.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            let modal = $('#cuModal');
            $('.editBtn').on('click', function() {
                modal.find('select[name=category_id]').val($(this).data('category_id')).change();
                modal.find('[name=image]').removeAttr('required');
                modal.find('[name=image]').closest('.form-group').find('label').first().removeClass('required');
                modal.find('.image-upload-preview').attr('style', `background-image: url(${$(this).data('image')})`);
            });

            var placeHolderImage = "{{ getImage(getFilePath('team'), getFileSize('team')) }}";
            $('#cuModal').on('hidden.bs.modal', function() {
                modal.find('select[name=category_id]').val('').change();
                modal.find('.image-upload-preview').attr('style', `background-image: url(${placeHolderImage})`);
                modal.find('[name=image]').attr('required', 'required');
                modal.find('[name=image]').closest('.form-group').find('label').first().addClass('required');
                $('#cuModal form')[0].reset();
            });

        })(jQuery);
    </script>
@endpush
