@extends('admin.layouts.app')
@section('panel')
    @php
        $sessionData = session('SEND_NOTIFICATION_TO_SUBSCRIBER') ?? [];
    @endphp
    @empty(!$sessionData)
        <div class="notification-data-and-loader">
            <div class="row  mb-4 justify-content-center">
                <div class="col-sm-7">
                    <div class="row gy-4 justify-content-center">
                        <div class="col-sm-6">
                            <x-widget link="javascript:void(0)" style="6" icon="fas fa-list" title="Email should be sent"
                                value="{{ @$sessionData['total_subscriber'] }}" bg="primary" :viewMoreIcon=false />
                        </div>
                        <div class="col-sm-6">
                            <x-widget link="javascript:void(0)" style="6" icon="fa-solid fa-envelope-circle-check" title="Email has been sent"
                                value="{{ @$sessionData['total_sent'] }}" bg="success" :viewMoreIcon=false />
                        </div>
                        <div class="col-sm-6">
                            <x-widget link="javascript:void(0)" style="6" icon="fa-solid fa-paper-plane" title="Email has yet to be sent"
                                :viewMoreIcon=false value="{{ @$sessionData['total_subscriber'] - @$sessionData['total_sent'] }}" bg="warning" />
                        </div>
                        <div class="col-sm-6">
                            <x-widget link="javascript:void(0)" style="6" icon="fas fa-envelope" title="Email per batch"
                                value="{{ @$sessionData['batch'] }}" bg="primary" :viewMoreIcon=false />
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
                                            @lang('Email will be sent again with a') <span class="coaling-time-count"></span>
                                            @lang(' second delay. Avoid closing or refreshing the browser.')
                                        </p>
                                        <p class="text--primary">
                                            @lang(' ' . @$sessionData['total_sent'] . ' out of ' . @$sessionData['total_subscriber'] . 'email were successfully transmitted')
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
                <form class="notify-form" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-12 subject-wrapper">
                                <label>@lang('Subject') <span class="text-danger">*</span> </label>
                                <input type="text" class="form-control" placeholder="@lang('Subject / Title')" name="subject"
                                    value="{{ old('subject', @$sessionData['subject']) }}">
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>@lang('Message') <span class="text-danger">*</span> </label>
                                    <textarea class="form-control nicEdit" id="nicEdit" name="message" rows="10">{{ old('message', @$sessionData['message']) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4 start-from-col">
                                        <div class="form-group">
                                            <label>@lang('Start Form') </label>
                                            <input class="form-control" name="start" value="{{ old('start', @$sessionData['start']) }}" type="number"
                                                placeholder="@lang('Start form user id. e.g. 1')" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4 per-batch-col">
                                        <div class="form-group">
                                            <label>@lang('Per Batch') </label>
                                            <div class="input-group">
                                                <input class="form-control" name="batch" value="{{ old('batch', @$sessionData['batch']) }}"
                                                    type="number" placeholder="@lang('How many subscriber')" required>
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
                                                <input class="form-control" name="cooling_time" value="{{ old('cooling_time', @$sessionData['batch']) }}"
                                                    type="number" placeholder="@lang('Waiting time')" required>
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
            "use strict";

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

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.subscriber.index') }}" />
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
