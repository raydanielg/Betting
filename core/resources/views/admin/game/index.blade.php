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
                                    <th>@lang('League') | @lang('Title')</th>
                                    <th>@lang('Category')</th>
                                    <th>@lang('Game Starts From')</th>
                                    <th>@lang('Bet Starts From')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($games as $game)
                                    <tr>

                                        <td>
                                            <small class="fw-semibold">{{ __(@$game->league->short_name) }}</small>
                                            <br>
                                            @if (@$game->teamOne && @$game->teamTwo)
                                                {{ __($game->teamOne->name) }} <em>@lang('vs')</em> {{ __(@$game->teamTwo->name) }}
                                            @else
                                                {{ __(@$game->title) }}
                                            @endif
                                        </td>

                                        <td>
                                            {{ __(@$game->league->category->name) }}
                                        </td>

                                        <td>
                                            {{ showDateTime($game->start_time, 'd M, Y h:i A') }}
                                        </td>

                                        <td>
                                            {{ showDateTime($game->bet_start_time, 'd M, Y h:i A') }}
                                        </td>

                                        <td>
                                            @php echo $game->statusBadge @endphp
                                            @if (!in_array($game->status, [Status::GAME_CANCELLED, Status::GAME_ENDED]))
                                                <button class="btn btn-sm btn-no--bg p-0 ms-1 changeStatusBtn" data-status="{{ $game->status }}" data-id="{{ $game->id }}" title="@lang('Change Status')"><i class="la la-pencil"></i>
                                            @endif
                                        </td>

                                        <td>
                                            <div class="d-flex justify-content-end gap-1 align-items-center">
                                                <a class="btn btn-sm btn-outline--primary" href="{{ route('admin.game.edit', $game->id) }}"> <i class="la la-pencil"></i>@lang('Edit')</a>

                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline--dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="las la-ellipsis-v"></i>@lang('More')
                                                    </button>
                                                    <ul class="dropdown-menu">

                                                        <li><a class="dropdown-item" href="{{ route('admin.market.index', $game->id) }}">@lang('Markets') ({{ $game->markets_count }})</a></li>
                                                        <li><a class="dropdown-item" href="{{ route('admin.bet.index') }}?game_id={{ $game->id }}">@lang('Bets') ({{ @$game->total_bets_count }})</a></li>

                                                        <li><a class="dropdown-item" href="{{ route('admin.outcomes.declare.pending') }}?game_id={{ $game->id }}">@lang('Declare Outcomes')</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
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

                @if ($games->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($games) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" id="offcanvasRight" aria-labelledby="offcanvasRightLabel" tabindex="-1">
        <div class="offcanvas-header">
            <h5 id="offcanvasRightLabel">@lang('Filter by')</h5>
            <button class="close bg--transparent" data-bs-dismiss="offcanvas" type="button" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="">
                <div class="form-group">
                    <label>@lang('Category')</label>
                    <select class="form-control select2" name="category_id">
                        <option value="">@lang('All')</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request()->category_id == $category->id)>{{ @$category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>@lang('Leauge')</label>
                    <select class="form-control select2" name="league_id">
                        <option value="">@lang('All')</option>
                        @foreach ($leagues as $league)
                            <option value="{{ $league->id }}" @selected(request()->league_id == $league->id)>{{ __($league->name) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>@lang('Team One')</label>
                    <select class="form-control select2" name="team_one_id">
                        <option value="">@lang('All')</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" @selected(request()->team_one_id == $team->id)>{{ $team->short_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>@lang('Team Two')</label>
                    <select class="form-control select2" name="team_two_id">
                        <option value="">@lang('All')</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}" @selected(request()->team_two_id == $team->id)>{{ @$team->short_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>@lang('Game Started From')</label>
                    <input name="start_time" type="search" class="datepicker-here form-control bg--white pe-2 date-range" placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->start_time }}">
                </div>
                <div class="form-group">
                    <label>@lang('Bet Started From')</label>
                    <input name="bet_start_time" type="search" class="datepicker-here form-control bg--white pe-2 date-range" placeholder="@lang('Start Date - End Date')" autocomplete="off" value="{{ request()->bet_start_time }}">
                </div>

                <div class="form-group">
                    <button class="btn btn--primary w-100 h-45"> @lang('Filter')</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="statusModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Change Game Status')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="la la-times"></i>
                    </button>
                </div>

                <form action="" method="post">
                    @csrf
                    <div class="modal-body">

                        <div class="form-check mb-3 open-for-betting">
                            <label class="form-check-label mb-0 flex-shrink-0" for="open_for_betting">
                                <input type="radio" id="open_for_betting" class="form-check-input" name="status" value="{{ Status::GAME_OPEN_FOR_BETTING }}">@lang('Open for Betting')
                            </label>
                            <small class="text-muted d-block"><i class="la la-info-circle"></i> @lang('Select this status when the game is currently open for betting. Bettors can place bets during this period. Ensure that the \'Bet Start From\' has passed, as this status indicates that betting is actively available for the game.')</small>
                        </div>


                        <div class="form-check mb-3 close-for-betting">
                            <label class="form-check-label mb-0 flex-shrink-0" for="closed_for_betting">
                                <input type="radio" id="closed_for_betting" class="form-check-input" name="status" value="{{ Status::GAME_CLOSED_FOR_BETTING }}">@lang('Close for Betting')
                            </label>

                            <small class="text-muted d-block"><i class="la la-info-circle"></i> @lang('Select this status when the game is no longer accepting bets. No new bets can be placed, but existing bets will remain valid and can be settled once the game concludes. Use this status to indicate that betting is officially closed for the game.')</small>
                        </div>

                        <div class="form-check mb-3 game-cancelled">
                            <label class="form-check-label mb-0 flex-shrink-0" for="game_cancelled">
                                <input type="radio" id="game_cancelled" class="form-check-input" name="status" value="{{ Status::GAME_CANCELLED }}" @checked(@$game->status == Status::GAME_CANCELLED)>@lang('Game Cancelled')
                            </label>

                            <small class="text-muted d-block"><i class="la la-info-circle"></i> @lang('Select this if the game has been cancelled. No bets can be placed or settled, and any existing bets will be voided.') <span class="text--warning">@lang('The stake amounts for all existing bets need to be refunded manually.')</span></small>
                        </div>

                        <div class="form-check game-completed">
                            <label class="form-check-label mb-0 flex-shrink-0" for="GAME_ENDED">
                                <input type="radio" id="GAME_ENDED" class="form-check-input" name="status" value="{{ Status::GAME_ENDED }}">@lang('Game Ended')
                            </label>

                            <small class="text-muted d-block"><i class="la la-info-circle"></i> @lang('Select this status when the game has concluded. All bets can now be settled based on the final outcome. Ensure to update this status promptly after the game ends to allow for accurate bet settlement and result processing.') </small>
                        </div>

                        <p class="p-3 bg-light text-muted mt-3 rounded">
                            <i class="la la-info-circle"></i> <span class="text--danger">@lang('Once you have changed the game status to \'Game Ended\' or \'Game Cancelled\',  this action cannot be undone.')</span>
                            <br>
                            <i class="la la-info-circle"></i> @lang('Games will only be displayed on the website when their status is set to "Open for Betting."')
                        </p>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Save Changes')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('style')
    <style>
        .thumb img {
            width: 30px;
            height: 30px;
        }

        .btn-no--bg {
            background-color: transparent;
            color: #464646;
        }

        .btn-no--bg:hover i {
            color: #252525 !important;
        }
    </style>
@endpush

@push('breadcrumb-plugins')
    <button class="btn btn-sm btn-outline--info " data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" type="button" aria-controls="offcanvasRight"><i class="las la-filter"></i> @lang('Filter')</button>
    <a class="btn btn-sm btn-outline--primary " href="{{ route('admin.game.create') }}"><i class="las la-plus"></i>@lang('Add New Game')</a>
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/daterangepicker.min.js') }}"></script>
@endpush

@push('style-lib')
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/global/css/daterangepicker.css') }}">
@endpush

@push('style')
    <style>
        .form-check .form-check-input {
            margin-left: -16px;
            margin-top: 0px;
            margin-right: 6px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict"

            const datePicker = $('.date-range').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                },
                showDropdowns: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 15 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Last 6 Months': [moment().subtract(6, 'months').startOf('month'), moment().endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                },
                maxDate: moment()
            });
            const changeDatePickerText = (event, startDate, endDate) => {
                $(event.target).val(startDate.format('MMMM DD, YYYY') + ' - ' + endDate.format('MMMM DD, YYYY'));
            }


            $('.date-range').on('apply.daterangepicker', (event, picker) => changeDatePickerText(event, picker.startDate, picker.endDate));


            if ($('.date-range').val()) {
                let dateRange = $('.date-range').val().split(' - ');
                $('.date-range').data('daterangepicker').setStartDate(new Date(dateRange[0]));
                $('.date-range').data('daterangepicker').setEndDate(new Date(dateRange[1]));
            }

            $('.changeStatusBtn').on('click', function() {
                const modal = $('#statusModal');
                const status = $(this).data('status');

                modal.find('.form-check').removeClass('d-none');

                let url = `{{ route('admin.game.status', ':id') }}`;

                url = url.replace(':id', $(this).data('id'));
                modal.find('form').attr('action', url);

                if (status == '{{ Status::GAME_OPEN_FOR_BETTING }}') {
                    modal.find('.open-for-betting').addClass('d-none');
                }

                if (status == '{{ Status::GAME_CLOSED_FOR_BETTING }}') {
                    modal.find('.open-for-betting').removeClass('d-none');
                    modal.find('.close-for-betting').addClass('d-none');
                }

                modal.modal('show');
            });

        })(jQuery)
    </script>
@endpush
