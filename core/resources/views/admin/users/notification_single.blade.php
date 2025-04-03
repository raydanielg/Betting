@extends('admin.layouts.app')

@section('panel')
    <div class="row mb-none-30">
        <div class="col-xl-12">
            <div class="card">
                <form action="{{ route('admin.users.notification.single', $user->id) }}" class="notificationForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="via" value="email">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    @if (gs('en'))
                                    <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-3 col-sm-6">
                                        <div class="notification-via mb-4 active" data-method="email">
                                            <span class="active-badge"> <i class="las la-check"></i> </span>
                                            <div class="send-via-method">
                                                <i class="las la-envelope"></i>
                                                <h5>@lang('Send Via Email')</h5>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if (gs('sn'))
                                    <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-3 col-sm-6">
                                        <div class="notification-via mb-4" data-method="sms">
                                            <span class="active-badge"> <i class="las la-check"></i> </span>
                                            <div class="send-via-method">
                                                <i class="las la-mobile-alt"></i>
                                                <h5>@lang('Send Via SMS')</h5>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if (gs('pn'))
                                    <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-3 col-sm-12">
                                        <div class="notification-via mb-4" data-method="push">
                                            <span class="active-badge"> <i class="las la-check"></i> </span>
                                            <div class="send-via-method">
                                                <i class="las la-bell"></i>
                                                <h5>@lang('Send Via Firebase')</h5>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-md-12 subject-wrapper">
                                <label>@lang('Subject') </label>
                                <input type="text" class="form-control" placeholder="@lang('Subject / Title')" name="subject">
                            </div>
                            <div class="form-group col-md-12 push-notification-file d-none">
                                <label>@lang('Image (optional)') </label>
                                <input type="file" class="form-control" accept=".png,.jpg,.jpeg" name="image">
                                <small class="mt-3 text-muted"> @lang('Supported Files'):<b>@lang('.png, .jpg, .jpeg')</b> </small>
                            </div>
                            <div class="form-group col-md-12">
                                <label>@lang('Message') </label>
                                <textarea name="message" rows="10" class="form-control nicEdit"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn w-100 h-45 btn--primary">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('script')
    <script>
        (function($) {
            "use strict"

            $('.notification-via').on('click',function () {
                $('.notification-via').removeClass('active');
                $(this).addClass('active');
                $('[name=via]').val($(this).data('method'));
                if($(this).data('method') == 'email'){
                    var nicPrev = $('.nicEdit').prev('div');
                    nicPrev.prev('div').removeClass('d-none');
                    nicPrev.removeClass('d-none');
                    $('.nicEdit').css('display','none')

                }else{
                    var nicPrev = $('.nicEdit').prev('div');
                    nicPrev.prev('div').addClass('d-none');
                    nicPrev.addClass('d-none');
                    $('.nicEdit').css('display','block')
                    $('.nicEdit').val("")
                }

                if($(this).data('method') == 'push'){
                    $('.push-notification-file').removeClass('d-none');
                }else{
                    $('.push-notification-file').addClass('d-none');
                    $('.push-notification-file [type=file]').val('');
                }

                if($(this).data('method') == 'push' || $(this).data('method') == 'email'){
                    $('.subject-wrapper').removeClass('d-none');
                }else{
                    $('.subject-wrapper').addClass('d-none')
                }
                $('.subject-wrapper').find('input').val('');
            });


            $('.notificationForm').on('submit',function (e) {
                if ($('.notification-via.active').data('method') != 'email') {
                    e.preventDefault();
                    var val = $('.nicEdit').val();
                    setTimeout(() => {
                        $('.nicEdit').val(val);
                        document.getElementsByClassName('notificationForm')[0].submit();
                    }, 1);
                }

            });

        })(jQuery);
    </script>
@endpush
