@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-3">
        @foreach ($crons as $cron)
            <div class="col-md-12">
                <div class="card @if ($cron->logs->where('error', '!=', null)->count()) error-bg @endif">

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __($cron->name) }}</h5>

                        <div class="d-flex flex-lg-row flex-column justify-content-between gap-2">
                            <div class="flex-shrink-0 text-end d-flex align-items-start gap-2">
                                <a href="{{ route('admin.cron.schedule.logs', $cron->id) }}" class="btn btn-sm btn-outline--primary"><i class="las la-history"></i> @lang('Logs')</a>

                                <a href="{{route('cron.manual.run', $cron->alias)}}" class="btn btn-sm btn-outline--success"><i class="las la-check-circle"></i> @lang('Run Now')</a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">

                        <div>
                            <small class="fw-bold text-muted">@lang('Command'): </small>
                            <small>
                                <span class="text--info fw-bold">curl -s {{ route('home') . '/' . $cron->url }}</span>
                            </small>
                        </div>

                        <div>
                            <small class="fw-bold text-muted">@lang('Last Run'): </small>
                            <small class="text-muted">
                                @if ($cron->last_run)
                                    @if (Carbon\Carbon::parse($cron->last_run)->isToday())
                                        @lang('Today'),
                                    @elseif(Carbon\Carbon::parse($cron->last_run)->isYesterday())
                                        @lang('Yesterday'),
                                    @else
                                        {{ showDateTime($cron->last_run) }}
                                    @endif
                                    {{ showDateTime($cron->last_run, 'h:i A') }}
                                @else
                                    --
                                @endif
                            </small>
                        </div>

                        <div>
                            <small class="fw-bold text-muted">@lang('Interval Recommendation'): <span class="fw-semibold text--warning">{{ $cron->interval_info }}</span></small>
                        </div>

                        @if ($cron->description)
                            <p class="mt-2"><i class="la la-info-circle"></i> {{ __($cron->description) }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <x-confirmation-modal />
@endsection

@push('style')
    <style>
        .error-bg {
            background-color: #f2dede;
        }
    </style>
@endpush
