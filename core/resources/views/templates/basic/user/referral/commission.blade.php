@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="row gy-4">

        <div class="col-12">

            <a class="text-muted text-decoration-underline" href="{{ route('user.referral.users') }}">
                <i class="la la-users"></i> @lang('My Referred Users')
            </a>

            <div class="table-responsive mt-2">
                <table class="table-responsive--md custom--table table">
                    <thead>
                        <tr>
                            <th>@lang('TRX No.')</th>
                            <th>@lang('From')</th>
                            <th>@lang('Level')</th>
                            <th>@lang('Percent')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Type')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($logs as $log)
                            <tr>
                                <td>
                                    #{{ @$log->trx }}
                                    <br>
                                    <small class="text-muted"><em> <i class="la la-clock"></i> {{ showDateTime($log->created_at) }}</em></small>
                                </td>
                                <td> {{ @$log->byWho->username }} </td>
                                <td> {{ ordinal($log->level) }} @lang('Level') </td>
                                <td> {{ getAmount($log->percent) }}% </td>
                                <td> {{ showAmount($log->commission_amount) }}</td>
                                <td> {{ __($log->commissionType()) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="100%">@lang('No commission log found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4 align-items-center pagination-wrapper">
            {{ $logs->links() }}
        </div>
    </div>
@endsection

@push('breadcrumb-items')
    <div class="d-flex gap-2">

        <form class="ms-auto min-width-220" method="GET">
            <select class="form-control form--control select2" id="referralType" data-minimum-results-for-search="-1" name="type">
                <option value="">@lang('Any Type')</option>
                <option value="deposit" @selected($type == 'deposit')>@lang('Deposit Commissions')</option>
                <option value="bet" @selected($type == 'bet')>@lang('Bet Place Commissions')</option>
                <option value="win" @selected($type == 'win')>@lang('Bet Win Commissions')</option>
            </select>
        </form>
        <x-search-form btn="btn-light" placeholder="TRX No." />
    </div>
@endpush

@push('style')
    <style>
        .min-width-220 {
            min-width: 220px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('#referralType').on('change', function() {
                $(this).closest('form').submit();
            });
        })(jQuery);
    </script>
@endpush
