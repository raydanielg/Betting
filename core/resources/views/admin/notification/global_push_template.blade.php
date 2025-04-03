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
                <form action="{{ route('admin.setting.notification.global.push.update') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('Notification Title') </label>
                                <input class="form-control" placeholder="@lang('Notification Title')" name="push_title" value="{{ gs('push_title') }}" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>@lang('Push Notification Body') </label>
                                <textarea class="form-control" rows="4" placeholder="@lang('Push Notification Body')" name="push_template" required>{{ gs('push_template') }}</textarea>
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
