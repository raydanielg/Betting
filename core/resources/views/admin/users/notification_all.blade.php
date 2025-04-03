@extends('admin.layouts.app')
@section('panel')
    @php
        $sessionData = session('SEND_NOTIFICATION') ?? [];
        $viaName     = $sessionData['via'] ?? 'email';
        $viaText     = @$sessionData['via'] == 'push' ? 'Push notification ' : ucfirst($viaName);
    @endphp

    @empty(!$sessionData)
        <div class="notification-data-and-loader">
            <div class="row  mb-4 justify-content-center">
                <div class="col-sm-7">
                    <div class="row gy-4 justify-content-center">
                        <div class="col-sm-6">
                            <x-widget link="javascript:void(0)" style="6" icon="fas fa-list" :title="$viaText . ' should be sent'" value="{{ @$sessionData['total_user'] }}"
                                bg="primary" :viewMoreIcon=false />
                        </div>
                        <div class="col-sm-6">
                            <x-widget link="javascript:void(0)" style="6" icon="fa-solid fa-envelope-circle-check" :title="$viaText . ' has been sent'"
                                value="{{ @$sessionData['total_sent'] }}" bg="success" :viewMoreIcon=false />
                        </div>
                        <div class="col-sm-6">
                            <x-widget link="javascript:void(0)" style="6" icon="fa-solid fa-paper-plane" :title="$viaText . ' has yet to be sent'" :viewMoreIcon=false
                                value="{{ @$sessionData['total_user'] - @$sessionData['total_sent'] }}" bg="warning" />
                        </div>
                        <div class="col-sm-6">
                            <x-widget link="javascript:void(0)" style="6" icon="fas fa-envelope" :title="$viaText . ' per batch'" value="{{ @$sessionData['batch'] }}"
                                bg="primary" :viewMoreIcon=false />
                        </div>
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body p-5 text-center">
                                    <div class="coaling-loader flex-column d-flex justify-content-center">
                                        <div class="countdown">
                                            <div class="coaling-time">
                                                <span class="coaling-time-count">{{ @$sessionData['cooling_time'] }}</span>
                                            </div>
                                            <div class="svg-count">
                                                <svg viewBox="0 0 100 100">
                                                    <circle r="45" cx="50" cy="50" id="animate-circle"></circle>
                                                </svg>
                                            </div>
                                        </div>
                                        <p class="mt-2">
                                            @lang("$viaText will be sent again with a") <span class="coaling-time-count"></span>
                                            @lang(' second delay. Avoid closing or refreshing the browser.')
                                        </p>
                                        <p class="text--primary">
                                            @lang(' ' . @$sessionData['total_sent'] . ' out of ' . @$sessionData['total_user'] . ' ' . $viaName . ' were successfully transmitted')
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endempty

    <div class="row @empty(!$sessionData) d-none @endempty">
        <div class="col-xl-12">
            <div class="card">
                <form class="notify-form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="via" value="{{ $viaName }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    @if (gs('en'))
                                        <div class="col-xxl-2 col-xl-3 col-lg-4 col-md-3 col-sm-6">
                                            <div class="notification-via mb-4  @if ($viaName == 'email') active @endif " data-method="email">
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
                                            <div class="notification-via mb-4 @if ($viaName == 'sms') active @endif " data-method="sms">
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
                                            <div class="notification-via mb-4 @if ($viaName == 'push') active @endif" data-method="push">
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
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Being Sent To') </label>
                                    <select class="form-control select2" name="being_sent_to" required data-minimum-results-for-search="1">
                                        @foreach ($notifyToUser as $key => $toUser)
                                            <option value="{{ $key }}" @selected(old('being_sent_to', @$sessionData['being_sent_to']) == $key)>{{ __($toUser) }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text--info d-none userCountText"> <i class="las la-info-circle"></i> <strong
                                            class="userCount">0</strong> @lang('active users found to send the notification')</small>
                                </div>
                                <div class="input-append">
                                </div>
                            </div>
                            <div class="form-group col-md-12 subject-wrapper">
                                <label>@lang('Subject') <span class="text--danger">*</span> </label>
                                <input type="text" class="form-control" placeholder="@lang('Subject / Title')" name="subject"
                                    value="{{ old('subject', @$sessionData['subject']) }}">
                            </div>
                            <div class="form-group col-md-12 push-notification-file d-none">
                                <label>@lang('Image (optional)') </label>
                                <input type="file" class="form-control" name="image" accept=".png,.jpg,.jpeg">
                                <small class="mt-3 text-muted"> @lang('Supported Files'):<b>@lang('.png, .jpg, .jpeg')</b> </small>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Message') <span class="text--danger">*</span> </label>
                                    <textarea class="form-control nicEdit" id="nicEdit" name="message" rows="10">{{ old('message', @$sessionData['message']) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4 start-from-col">
                                        <div class="form-group">
                                            <label>@lang('Start Form') </label>
                                            <input class="form-control" name="start" value="{{ old('start', @$sessionData['start']) }}"
                                                type="number" placeholder="@lang('Start form user id. e.g. 1')" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 per-batch-col">
                                        <div class="form-group">
                                            <label>@lang('Per Batch') </label>
                                            <div class="input-group">
                                                <input class="form-control" name="batch" value="{{ old('batch', @$sessionData['batch']) }}"
                                                    type="number" placeholder="@lang('How many user')" required>
                                                <span class="input-group-text">
                                                    @lang('User')
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 cooling-period-col">
                                        <div class="form-group">
                                            <label>@lang('Cooling Period') </label>
                                            <div class="input-group">
                                                <input class="form-control" name="cooling_time"
                                                    value="{{ old('cooling_time', @$sessionData['batch']) }}" type="number"
                                                    placeholder="@lang('Waiting time')" required>
                                                <span class="input-group-text">
                                                    @lang('Seconds')
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn w-100 h-45 btn--primary me-2" type="submit">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        let formSubmit = false;

        (function($) {
            "use strict"


            $('select[name=being_sent_to]').on('change', function(e) {
                let methodName = $(this).val();
                if (!methodName) return;
                getUserCount(methodName);
                methodName = methodName.toUpperCase();

                if (methodName == 'SELECTEDUSERS') {
                    $('.input-append').html(`
                    <div class="form-group" id="user_list_wrapper">
                        <label class="required">@lang('Select User')</label>
                        <select name="user[]"  class="form-control" id="user_list" required multiple >
                            <option disabled>@lang('Select One')</option>
                        </select>
                    </div>
                    `);
                    fetchUserList();
                    return;
                }
                if (methodName == 'TOPDEPOSITEDUSERS') {
                    $('.input-append').html(`
                    <div class="form-group">
                        <label class="required">@lang('Number Of Top Deposited User')</label>
                        <input class="form-control" type="number" name="number_of_top_deposited_user" >
                    </div>
                    `);
                    return;
                }

                if (methodName == 'NOTLOGINUSERS') {
                    $('.input-append').html(`
                    <div class="form-group">
                        <label class="required">@lang('Number Of Days')</label>
                        <div class="input-group">
                            <input class="form-control" value="{{ old('number_of_days', @$sessionData['number_of_days']) }}" type="number" name="number_of_days" >
                            <span class="input-group-text">@lang('Days')</span>
                        </div>
                    </div>
                    `);
                    return;
                }

                $('.input-append').empty();

            }).change();

            function fetchUserList() {

                $('.row #user_list').select2({
                    ajax: {
                        url: "{{ route('admin.users.list') }}",
                        type: "get",
                        dataType: 'json',
                        delay: 1000,
                        data: function(params) {
                            return {
                                search: params.term,
                                page: params.page,
                            };
                        },
                        processResults: function(response, params) {
                            params.page = params.page || 1;
                            let data = response.users.data;
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        text: item.email,
                                        id: item.id
                                    }
                                }),
                                pagination: {
                                    more: response.more
                                }
                            };
                        },
                        cache: false,
                    },
                    dropdownParent: $('.input-append #user_list_wrapper')
                });

            }


            function getUserCount(methodName) {
                var methodNameUpper = methodName.toUpperCase();
                if (methodNameUpper == 'SELECTEDUSERS' || methodNameUpper == 'ALLUSERS' || methodNameUpper == 'TOPDEPOSITEDUSERS' ||
                    methodNameUpper == 'NOTLOGINUSERS') {
                    $('.userCount').text(0);
                    $('.userCountText').addClass('d-none');
                    return;
                }
                var route = "{{ route('admin.users.segment.count', ':methodName') }}"
                route = route.replace(':methodName', methodName)
                $.get(route, function(response) {
                    $('.userCount').text(response);
                    $('.userCountText').removeClass('d-none');
                });
            }

            $('.notification-via').on('click', function() {

                $('.notification-via').removeClass('active');
                $(this).addClass('active');
                $('[name=via]').val($(this).data('method'));

                if ($(this).data('method') == 'email') {
                    var nicPrev = $('.nicEdit').prev('div');
                    nicPrev.prev('div').removeClass('d-none');
                    nicPrev.removeClass('d-none');
                    $('.nicEdit').css('display', 'none')

                } else {
                    var nicPrev = $('.nicEdit').prev('div');
                    nicPrev.prev('div').addClass('d-none');
                    nicPrev.addClass('d-none');
                    $('.nicEdit').css('display', 'block')
                    $('.nicEdit').val("")
                }

                if ($(this).data('method') == 'push') {
                    $('.push-notification-file').removeClass('d-none');
                } else {
                    $('.push-notification-file').addClass('d-none');
                    $('.push-notification-file [type=file]').val('');
                }

                if ($(this).data('method') == 'push' || $(this).data('method') == 'email') {
                    $('.subject-wrapper').removeClass('d-none');
                } else {
                    $('.subject-wrapper').addClass('d-none')
                }
                $('.subject-wrapper').find('input').val('');
            });

            $(".notify-form").on("submit", function(e) {
                formSubmit = true;
            });

            @empty(!$sessionData)
                $(document).ready(function() {
                    const coalingTimeOut = setTimeout(() => {
                        let coalingTime = Number("{{ $sessionData['cooling_time'] }}");

                        $("#animate-circle").css({
                            "animation": `countdown ${coalingTime}s linear infinite forwards`
                        });

                        let $coalingCountElement = $('.coaling-time-count');
                        let $coalingLoaderElement = $(".coaling-loader");

                        $coalingCountElement.text(coalingTime);

                        const coalingIntVal = setInterval(function() {
                            coalingTime--;
                            $coalingCountElement.text(coalingTime);
                            if (coalingTime <= 0) {
                                formSubmit = true;
                                $("#animate-circle").css({
                                    "animation": `unset`
                                });
                                clearInterval(coalingIntVal);
                                clearTimeout(coalingTimeOut);
                                $(".notify-form").submit();
                            }
                        }, 1000);

                    }, 1000);
                });
            @endif

        })(jQuery);

        @if (!empty(@$sessionData) && @request()->email_sent && @request()->email_sent = 'yes')
            window.addEventListener('beforeunload', function(event) {
                if (!formSubmit) {
                    event.preventDefault();
                    event.returnValue = '';
                    var confirmationMessage = 'Are you sure you want to leave this page?';
                    (event || window.event).returnValue = confirmationMessage;
                    return confirmationMessage;
                }
            });
        @endif
    </script>
@endpush


@push('style')
    <style>
        .countdown {
            position: relative;
            height: 100px;
            width: 100px;
            text-align: center;
            margin: 0 auto;
        }

        .coaling-time {
            color: yellow;
            position: absolute;
            z-index: 999999;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 30px;
        }

        .coaling-loader svg {
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            transform: rotateY(-180deg) rotateZ(-90deg);
            position: relative;
            z-index: 1;
        }

        .coaling-loader svg circle {
            stroke-dasharray: 314px;
            stroke-dashoffset: 0px;
            stroke-linecap: round;
            stroke-width: 6px;
            stroke: #4634ff;
            fill: transparent;

        }

        .coaling-loader .svg-count {
            width: 100px;
            height: 100px;
            position: relative;
            z-index: 1;
        }

        .coaling-loader .svg-count::before {
            content: '';
            position: absolute;
            outline: 5px solid #f3f3f9;
            z-index: -1;
            width: calc(100% - 16px);
            height: calc(100% - 16px);
            left: 8px;
            top: 8px;
            z-index: -1;
            border-radius: 100%
        }

        .coaling-time-count {
            color: #4634ff;
        }

        @keyframes countdown {
            from {
                stroke-dashoffset: 0px;
            }

            to {
                stroke-dashoffset: 314px;
            }
        }
    </style>
@endpush
