@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="card custom--card">
        <div class="card-body">
            <form action="{{ route('ticket.store') }}" class="disableSubmission" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-label">@lang('Subject')</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" class="form-control form--control" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label class="form-label">@lang('Priority')</label>
                        <select name="priority" class="form-select form--control select2" data-minimum-results-for-search="-1" required>
                            <option value="3">@lang('High')</option>
                            <option value="2">@lang('Medium')</option>
                            <option value="1">@lang('Low')</option>
                        </select>
                    </div>
                    <div class="col-12 form-group">
                        <label class="form-label">@lang('Message')</label>
                        <textarea name="message" id="inputMessage" rows="6" class="form-control form--control" required>{{ old('message') }}</textarea>
                    </div>

                    <div class="col-md-9">
                        <button type="button" class="btn btn-dark btn-sm addAttachment my-2"> <i class="las la-plus"></i> @lang('Add Attachment') </button>
                        <br>
                        <small class="mb-2"><span class="text-muted"> <i class="la la-info-circle"></i> @lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')</span></small>

                        <div class="row fileUploadsContainer"></div>

                    </div>


                    <div class="col-md-3">
                        <button class="btn btn--base w-100 my-2" type="submit"><i class="las la-paper-plane"></i> @lang('Submit')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('breadcrumb-items')
    <a class="btn btn--base" href="{{ route('ticket.index') }}">
        <span class="dashboard-menu__text"><i class="la la-list"></i> @lang('All Tickets')</span>
    </a>
@endpush

@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addAttachment').on('click', function() {
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled', true)
                }
                $(".fileUploadsContainer").append(`
                    <div class="col-xl-6 col-lg-12 col-md-6 col-sm-6 removeFileInput">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="file" name="attachments[]" class="form-control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile bg--danger text-white border--danger"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                `)
            });
            $(document).on('click', '.removeFile', function() {
                $('.addAttachment').removeAttr('disabled', true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);
    </script>
@endpush
