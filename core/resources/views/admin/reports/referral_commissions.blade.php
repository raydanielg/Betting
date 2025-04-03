@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn-outline--primary showFilterBtn btn-sm"><i class="las la-filter"></i> @lang('Filter')</button>
            </div>
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form action="">
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('TRX/Username')</label>
                                <input type="text" name="search" value="{{ request()->search }}" class="form-control">
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Commission Type')</label>
                                <select name="type" class="form-control select2"  data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    <option value="deposit">@lang('Deposit')</option>
                                    <option value="bet">@lang('Bet Place')</option>
                                    <option value="win">@lang('Bet Win')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Date')</label>
                                <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="datepicker-here form-control" data-position='bottom right' placeholder="@lang('Start date - End date')" autocomplete="off" value="{{ request()->date }}">
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('TRX')</th>
                                    <th>@lang('From')</th>
                                    <th>@lang('To')</th>
                                    <th>@lang('Level')</th>
                                    <th>@lang('Percent')</th>
                                    <th>@lang('Amount')</th>
                                    <th>@lang('Date')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($logs as $log)
                                    <tr>
                                        <td> {{ $log->trx }}</td>
                                        <td>
                                            <span class="fw-bold">{{ __(@$log->byWho->fullname) }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', @$log->byWho->id) }}"><span>@</span>{{ @$log->byWho->username }}</a>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ __(@$log->toUser->fullname) }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ route('admin.users.detail', @$log->toUser->id) }}"><span>@</span>{{ @$log->toUser->username }}</a>
                                            </span>
                                        </td>
                                        <td> {{ __(ordinal($log->level)) }} @lang('Level') </td>
                                        <td> {{ getAmount($log->percent) }} % </td>
                                        <td> {{ showAmount($log->commission_amount) }} </td>
                                        <td> {{ showDateTime($log->created_at, 'd M, Y') }} </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table><!-- table end -->
                    </div>
                </div>

                @if ($logs->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($logs) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vendor/datepicker.min.css') }}">
@endpush


@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            @if (request()->type)
                $('[name=type]').val(`{{ request()->type }}`);
            @endif

            if (!$('.datepicker-here').val()) {
                $('.datepicker-here').datepicker();
            }
        })(jQuery)
    </script>
@endpush
