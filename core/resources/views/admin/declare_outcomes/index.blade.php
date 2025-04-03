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
                                    <th>@lang('Game')</th>
                                    <th>@lang('Market')</th>
                                    <th>@lang('Total Bets')</th>
                                    @if (request()->routeIs('admin.outcomes.declare.declared'))
                                        <th>@lang('Win Outcome')</th>
                                    @endif
                                    @if (request()->routeIs('admin.outcomes.declare.pending'))
                                        <th>@lang('Action')</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($markets as $market)
                                    <tr>
                                        <td>
                                            @if (@$market->game?->teamOne && @$market->game?->teamTwo)
                                                {{ __(@$market->game->teamOne->name) }}
                                                @lang('VS')
                                                {{ __(@$market->game->teamTwo->name) }}
                                                <br>
                                                {{ showDateTime(@$market->game->start_time) }}
                                            @else
                                                {{ $market->game?->league?->name }}
                                            @endif
                                        </td>

                                        <td>{{ __(@$market->market_title) }}</td>

                                        <td>
                                            {{ getAmount(@$market->bet_items_count) }}
                                        </td>


                                        @if (request()->routeIs('admin.outcomes.declare.declared'))
                                            <td>
                                                @if (@$market->winOutcome)
                                                    <span>{{ __(@$market->winOutcome->name) }}</span>
                                                @else
                                                    <span class="text--warning">@lang('Refunded')</span>
                                                @endif
                                            </td>
                                        @endif

                                        @if (request()->routeIs('admin.outcomes.declare.pending'))
                                            <td>
                                                <div class="button--group">
                                                    <button class="btn btn-sm btn-outline--primary outcome-btn" data-market="{{ __($market->title ?? $market->market_title)  }}" data-outcomes='{{ $market->outcomes }}' type="button">
                                                        <i class="la la-info-circle"></i>@lang('Select Outcome')
                                                    </button>

                                                    <button class="btn btn-sm btn-outline--info confirmationBtn" data-action="{{ route('admin.outcomes.declare.refund', $market->id) }}" data-question="@lang('Do you want to return the bet amount for this market?')" type="button">
                                                        <i class="las la-undo-alt"></i> @lang('Refund Bet')
                                                    </button>

                                                    <a class="btn btn-sm btn-outline--dark" href="{{ route('admin.bet.market', $market->id) }}">
                                                        <i class="las la-clipboard-list"></i> @lang('Bets')
                                                    </a>
                                                </div>
                                            </td>
                                        @endif
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

                @if ($markets->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($markets) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal" id="outcomeModal" role="dialog" tabindex="-1">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div class="result-area"></div>
                        <div class="action-area"></div>
                    </div>
                    <div class="table-responsive--sm table-responsive">
                        <table class="table--light style--two table">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Rate')</th>
                                    <th>@lang('Bet Count')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-search-form />
@endpush

@push('style')
    <style>
        .thumb img {
            width: 30px;
            height: 30px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            let modal = $("#outcomeModal");
            $('.outcome-btn').on('click', function(e) {
                modal.find('tbody').html('')
                var market = $(this).data('market');
                var outcomes = $(this).data('outcomes');

                var modalTitle = `Outcomes for - ${market}`;
                modal.find('.modal-title').text(modalTitle);
                var tableRow = ``;
                $.each(outcomes, function(index, outcome) {
                    tableRow += `<tr>
                                    <td data-label="@lang('Name')">${outcome.name}</td>
                                    <td data-label="@lang('Odds')">${Math.abs(outcome.odds)}</td>
                                    <td data-label="@lang('Bet Count')">${outcome.bets_count}</td>
                                    <td data-label="@lang('Action')">
                                        <button class="btn btn-sm btn-outline--primary confirmationBtn" data-action="{{ route('admin.outcomes.declare.winner', '') }}/${outcome.id}" data-question="@lang('Are you sure to select') ${outcome.name}?">
                                            <i class="las la-trophy"></i>@lang('Select')
                                        </button>
                                    </td>
                                </tr>`;
                });
                modal.find('tbody').append(tableRow)
                modal.modal('show')
            });

            let confirmationModal = $("#confirmationModal");

            $(document).on('click', '.confirmationBtn', function(e) {
                modal.modal('hide');
                confirmationModal.modal('show');
            });

            $(document).on('click', '#confirmationModal [data-bs-dismiss=modal]', function(e) {
                let formUrl = $(document).find("#confirmationModal form").attr('action');
                confirmationModal.modal('hide')
                if (!formUrl.includes("match/market/refund")) {
                    modal.modal('show');
                }
            });

        })(jQuery);
    </script>
@endpush
