@extends('admin.layouts.app')
@push('topBar')
  @include('admin.notification.top_bar')
@endpush
@section('panel')
    @include('admin.notification.template.nav')
    @include('admin.notification.template.shortcodes')


    <form action="{{ route('admin.setting.notification.template.update',['push',$template->id]) }}" method="post">
        @csrf
        <div class="row">

            <div class="col-md-12">
                <div class="card mt-4">
                    <div class="card-header bg--primary">
                        <h5 class="card-title text-white">@lang('Push Notification Template')</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>@lang('Notification Title')</label>
                                    <input type="text" class="form-control" placeholder="@lang('Notification Title')" name="push_title" value="{{ $template->push_title }}">
                                    <small class="text-primary"><i><i class="las la-info-circle"></i> @lang('Make the field empty if you want to use global template\'s title as notification title.')</i></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Status')</label>
                                    <input type="checkbox" data-height="46px" data-width="100%" data-onstyle="-success"
                                       data-offstyle="-danger" data-bs-toggle="toggle" data-on="@lang('Send Push Notify')"
                                       data-off="@lang("Don't Send")" name="push_status"
                                       @if($template->push_status) checked @endif>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Message')</label>
                                    <textarea name="push_body" rows="10" class="form-control" placeholder="@lang('Your message using short-codes')" required>{{ $template->push_body }}</textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </div>
            </div>

        </div>

    </form>
@endsection


@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.setting.notification.templates') }}" />
@endpush
