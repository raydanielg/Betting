@extends($activeTemplate . 'layouts.master')
@section('master')

    <div class="row gy-4">

        <div class="col-12 d-flex flex-wrap flex-sm-nowrap gap-2 gap-md-3">
            <div class="flex-grow-1">
                <x-user-dashboard-widget title="Total" amount="{{ getAmount($widget['totalBet']) }}" />
            </div>

            <div class="flex-grow-1">
                <x-user-dashboard-widget title="Pending" amount="{{ getAmount($widget['pendingBet']) }}" />
            </div>

            <div class="flex-grow-1">
                <x-user-dashboard-widget title="Won" amount="{{ getAmount($widget['wonBet']) }}" />
            </div>

            <div class="flex-grow-1">
                <x-user-dashboard-widget title="Lost" amount="{{ getAmount($widget['loseBet']) }}" />
            </div>

            <div class="flex-grow-1">
                <x-user-dashboard-widget title="Refunded" amount="{{ getAmount($widget['refundedBet']) }}" />
            </div>
        </div>


        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-sm-nowrap align-items-center mt-0 gap-3">
                <div class="action-area d-flex gap-2 flex-shrink-0">
                    <a class="btn btn-outline--base btn-sm {{ menuActive('user.bets.all') }}" href="{{ route('user.bets.all') }}">@lang('All')</a>
                    <a class="btn btn-outline--base btn-sm {{ menuActive('user.bets.pending') }}" href="{{ route('user.bets.pending') }}">@lang('Pending')</a>

                    <a class="btn btn-outline--base btn-sm {{ menuActive('user.bets.wins') }}" href="{{ route('user.bets.wins') }}">@lang('Won')</a>
                    <a class="btn btn-outline--base btn-sm {{ menuActive('user.bets.losses') }}" href="{{ route('user.bets.losses') }}">@lang('Lost')</a>
                    <a class="btn btn-outline--base btn-sm {{ menuActive('user.bets.refunded') }}" href="{{ route('user.bets.refunded') }}">@lang('Refunded')</a>
                </div>
                <div class="ms-auto search--form">
                    <x-search-form btn="btn-light" placeholder="Bet No." />
                </div>
            </div>
        </div>

        <div class="col-12">

            <div class="table-responsive">
                <table class="table-responsive--sm custom--table table">
                    <thead>
                        <tr>
                            <th>@lang('Bet No.')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Staked')</th>
                            <th>@lang('Return')</th>
                            <th>@lang('Status')</th>
                            @if (!Route::is('user.bets.pending'))
                                <th>@lang('Is Settled')</th>
                            @endif
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($bets as $bet)
                            <tr>
                                <td>
                                    #{{ $bet->bet_number }}
                                    <br>
                                    <small class="text-muted"><em> <i class="la la-clock"></i> {{ showDateTime($bet->created_at) }}</em></small>
                                </td>


                                <td>
                                    @php echo $bet->betTypeBadge @endphp
                                </td>

                                <td> {{ showAmount($bet->stake_amount) }} </td>
                                <td> {{ showAmount($bet->return_amount) }} </td>

                                <td>
                                    @php echo $bet->betStatusBadge @endphp
                                </td>

                                @if (!Route::is('user.bets.pending'))
                                    <td>
                                        @if ($bet->status == Status::BET_WIN || $bet->status == Status::BET_REFUNDED)
                                            <br>
                                            @if ($bet->is_settled)
                                                <small>@lang('Yes')</small>
                                            @else
                                                <small class="text--warning">@lang('No')</small>
                                            @endif
                                        @elseif($bet->status == Status::BET_LOSS)
                                            <small>@lang('Yes')</small>
                                        @else
                                            <small>@lang('...')</small>
                                        @endif
                                    </td>
                                @endif

                                <td>
                                    <button class="btn btn--sm btn-outline--base view-btn" data-id="{{ $bet->id }}" data-is_settled="{{ $bet->is_settled }}" data-bet_details='{{ $bet->bets }}' type="button">
                                        <i class="las la-desktop"></i> @lang('View')
                                    </button>
                                </td>
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
                {{ $bets->links() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="betDetailModal" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="m-0">@lang('Selections')</h5>
                    <span class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.view-btn').on('click', function(e) {
                var modal = $('#betDetailModal');
                modal.find('.modal-body').html('<div style="height: 30px;" class="d-flex justify-content-center align-items-center"><i class="fa fa-spin fa-circle-notch"></i></div>');
                var modal = $('#betDetailModal');
                modal.modal('show');

                const handleBetDetails = (data) => {
                    modal.find('.modal-body').html(data);
                }

                $.get("{{ route('user.bets.details', '') }}/" + $(this).data('id'), (result) => handleBetDetails(result));
            });
        })(jQuery)
    </script>
@endpush
