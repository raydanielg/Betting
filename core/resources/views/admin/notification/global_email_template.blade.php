@extends('admin.layouts.app')
@section('panel')
    @push('topBar')
        @include('admin.notification.top_bar')
    @endpush
    <div class="row">
        @include('admin.notification.global_template_nav')
        @include('admin.notification.global_shortcodes')

        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-body">
                    <form action="{{ route('admin.setting.notification.global.email.update') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email Sent From - Name') </label>
                                    <input type="text" class="form-control " placeholder="@lang('Email address')" name="email_from_name" value="{{ gs('email_from_name') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email Sent From - Email') </label>
                                    <input type="text" class="form-control " placeholder="@lang('Email address')" name="email_from" value="{{ gs('email_from') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>@lang('Email Body') </label>
                                    <textarea name="email_template" rows="10" class="form-control emailTemplateEditor" id="htmlInput" placeholder="@lang('Your email template')">{{ gs('email_template') }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="previewContainer">
                                    <label>&nbsp;</label>
                                    <iframe id="iframePreview"></iframe>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn w-100 btn--primary h-45">@lang('Submit')</button>
                    </form>
                </div>
            </div><!-- card end -->
        </div>

    </div>
@endsection
@push('style')
    <style>
        #iframePreview {
            width: 100%;
            height: 400px;
            border: none;
        }

        .emailTemplateEditor {
            height: 400px;
        }
    </style>
@endpush

@push('script')
    <script>
        var iframe = document.getElementById('iframePreview');
        $(".emailTemplateEditor").on('input', function() {
            var htmlContent = document.getElementById('htmlInput').value;
            iframe.src = 'data:text/html;charset=utf-8,' + encodeURIComponent(htmlContent);
        }).trigger('input');
    </script>
@endpush
