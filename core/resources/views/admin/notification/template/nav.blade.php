<div class="col-12">
    <div class="row">
        <div class="col-xxl-3 col-xl-4 col-md-4 col-sm-6">
            <a href="{{ route('admin.setting.notification.template.edit',['email',$template->id]) }}" class="notification-via mb-4 {{ menuActive('admin.setting.notification.template.edit',param:'email') }} d-block">
                <span class="active-badge"> <i class="las la-check"></i> </span>
                <div class="send-via-method">
                    <i class="las la-envelope"></i>
                    <h5>@lang('Email Template')</h5>
                </div>
            </a>
        </div>
        <div class="col-xxl-3 col-xl-4 col-md-4 col-sm-6">
            <a href="{{ route('admin.setting.notification.template.edit',['sms',$template->id]) }}" class="notification-via mb-4 {{ menuActive('admin.setting.notification.template.edit',param:'sms') }} d-block">
                <span class="active-badge"> <i class="las la-check"></i> </span>
                <div class="send-via-method">
                    <i class="las la-mobile-alt"></i>
                    <h5>@lang('SMS Template')</h5>
                </div>
            </a>
        </div>
        <div class="col-xxl-3 col-xl-4 col-md-4 col-sm-12">
            <a href="{{ route('admin.setting.notification.template.edit',['push',$template->id]) }}" class="notification-via mb-4 {{ menuActive('admin.setting.notification.template.edit',param:'push') }} d-block">
                <span class="active-badge"> <i class="las la-check"></i> </span>
                <div class="send-via-method">
                    <i class="las la-bell"></i>
                    <h5>@lang('Push Notification Template')</h5>
                </div>
            </a>
        </div>
    </div>
</div>
