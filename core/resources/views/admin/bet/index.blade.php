@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card b-radius--10">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Bet ID')</th>
                                    <th>@lang('Bet Placed At')</th>
                                    <th>@lang('User')</th>
                                    <th>@lang('Type')</th>
                                    <th>@lang('Stake Amount')</th>
                                    <th>@lang('Return')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($bets as $bet)
                                    <tr>
                                        <td>
                                            <span>{{ __($bet->bet_number) }}</span>
                                        </td>

                                        <td>
                                            {{ showDateTime($bet->created_at) }}
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.users.detail', @$bet->user_id) }}"><span>@</span>{{ @$bet->user->username }}</a>
                                        </td>

                                        <td>
                                            @php echo $bet->betTypeBadge @endphp
                                        </td>

                                        <td> {{ showAmount($bet->stake_amount) }} </td>
                                        <td> {{ showAmount($bet->return_amount) }} </td>
                                        <td>
                                            @php echo $bet->betStatusBadge @endphp
                                            @if ($bet->status == Status::BET_WIN || $bet->status == Status::BET_REFUNDED)
                                                <br>
                                                @if($bet->is_settled)
                                                    <small class="text--success">@lang('Settled')</small>
                                                @else
                                                    <small class="text--warning">@lang('Unsettled')</small>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline--primary bet-detail" data-id="{{ $bet->id }}" type="button">
                                                <i class="las la-desktop"></i> @lang('Detail')
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if ($bets->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($bets) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="betDetailModal" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="m-0">@lang('Bet Detail')</h5>
                    <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <div class="table-responsive--lg table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Game')</th>
                                    <th>@lang('Market')</th>
                                    <th>@lang('Outcome')</th>
                                    <th>@lang('Odds')</th>
                                    <th>@lang('Status')</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form placeholder="Bet ID" />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.bet-detail').on('click', function(e) {
                var modal = $('#betDetailModal');
                modal.find('.modal-body').html('<div style="height: 30px;" class="d-flex justify-content-center align-items-center"><i class="fa fa-spin fa-circle-notch"></i></div>');
                var modal = $('#betDetailModal');
                modal.modal('show');

                const handleBetDetails = (data) => {
                    modal.find('.modal-body').html(data);
                }

                $.get("{{ route('admin.bet.details', '') }}/" + $(this).data('id'), (result) => handleBetDetails(result));
            });

        })(jQuery)
    </script>
@endpush
