@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="show-filter mb-3 text-end">
        <button class="btn btn--base showFilterBtn btn-sm" type="button"><i class="las la-filter"></i> @lang('Filter')</button>
    </div>
    <div class="responsive-filter-card mb-3">
        <form>
            <div class="d-flex flex-wrap gap-4">
                <div class="flex-grow-1">
                    <label class="form-label">@lang('Transaction No.')</label>
                    <input class="form-control form--control" name="search" type="text" value="{{ request()->search }}">
                </div>
                <div class="flex-grow-1">
                    <label class="form-label">@lang('Type')</label>
                    <select class="form-control select2" name="trx_type" data-minimum-results-for-search="-1">
                        <option value="">@lang('All')</option>
                        <option value="+" @selected(request()->trx_type == '+')>@lang('Plus')</option>
                        <option value="-" @selected(request()->trx_type == '-')>@lang('Minus')</option>
                    </select>
                </div>
                <div class="flex-grow-1">
                    <label class="form-label">@lang('Remark')</label>
                    <select class="form-control select2" name="remark" data-minimum-results-for-search="-1">
                        <option value="">@lang('Any')</option>
                        @foreach ($remarks as $remark)
                            <option value="{{ $remark->remark }}" @selected(request()->remark == $remark->remark)>{{ __(keyToTitle($remark->remark)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-grow-1 align-self-end">
                    <button class="btn btn--base btn--xl w-100"><i class="las la-filter"></i> @lang('Filter')</button>
                </div>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table-responsive--md custom--table table">
            <thead>
                <tr>
                    <th>@lang('Transaction No.')</th>
                    <th>@lang('Transacted')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Post Balance')</th>
                    <th>@lang('Detail')</th>
                </tr>
            </thead>

            <tbody>
                @forelse($transactions as $trx)
                    <tr>
                        <td>#{{ $trx->trx }}</td>
                        <td>
                            {{ showDateTime($trx->created_at) }}
                        </td>
                        <td>
                            <span class="@if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                            </span>
                        </td>
                        <td>
                            {{ showAmount($trx->post_balance) }}
                        </td>
                        <td><p>{{ $trx->details }}</p></td>
                    </tr>
                @empty
                    <tr>
                        <td class="text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 align-items-center pagination-wrapper">
        {{ $transactions->links() }}
    </div>
@endsection
@push('style')
    <style>
        .select2-container {
            width: 100% !important;
        }
    </style>
@endpush
