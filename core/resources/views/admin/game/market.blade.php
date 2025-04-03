@extends('admin.layouts.app')

@section('panel')
    <div class="row g-3">

        <div class="col-lg-5 col-xl-4 col-xxl-3 all-markets-list position-sticky">
            <div class="card">
                <button type="button" class="hideMarketList"><i class="la la-times"></i></button>
                <div class="card-header d-flex justify-content-between justify-content-between gap-4 align-items-center">
                    <h6 class="card-title mb-0">@lang('Markets')</h6>
                    @if (!$game->is_outright)
                        <nav class="custom-tab-nav flex-grow-1 flex-shrink-0">
                            <button type="button" class="custom-tab-nav-item active border-radius-left"
                                data-target="team-markets">@lang('Team')</button>
                            <button type="button" class="custom-tab-nav-item border-radius-right"
                                data-target="player-markets">@lang('Player')</button>
                        </nav>
                    @endif
                </div>

                <div class=" card-body">
                    @if ($game->is_outright)
                        @php
                            $allMarkets = getMarkets()->where('key', 'outrights');
                        @endphp

                        @include('admin.game.partials.markets_list', ['gameMarkets' => $allMarkets])
                    @else
                        @php
                            $category = $game->league->category->odds_api_name??$game->league->category->name;
                            $allMarkets = getMarkets()
                                ->where('key', '!=', 'outrights')
                                ->filter(function ($market) use ($category) {
                                    return !isset($market->sports) || in_array($category, $market->sports);
                                });
                            $teamMarkets = $allMarkets->where('player_props', 0);
                            $playerMarkets = $allMarkets->where('player_props', 1);
                        @endphp

                        <div class="custom-tab" id="team-markets">
                            <nav class="custom-tab-nav flex-grow-1 flex-shrink-0 mb-3">
                                <button type="button" class="custom-tab-nav-item active border-radius-left"
                                    data-target="team-markets-any-time">@lang('Any Time')</button>
                                <button type="button" class="custom-tab-nav-item border-radius-right"
                                    data-target="team-markets-game-time">@lang('Game Period')</button>
                            </nav>

                            <div class="custom-tab" id="team-markets-any-time">
                                @include('admin.game.partials.markets_list', [
                                    'gameMarkets' => @$teamMarkets?->where('game_period_market', 0),
                                ])
                            </div>

                            <div class="custom-tab" id="team-markets-game-time">
                                @include('admin.game.partials.markets_list', [
                                    'gameMarkets' => @$teamMarkets?->where('game_period_market', 1),
                                ])
                            </div>
                        </div>

                        <div class="custom-tab" id="player-markets">
                            @include('admin.game.partials.markets_list', [
                                'gameMarkets' => @$playerMarkets,
                            ])
                        </div>
                    @endif

                </div>
            </div>

            @if (!$game->is_outright)
                <button class="btn btn-outline--info w-100 h-45 mt-4" id="addCustomMarket"><i
                        class="las la-lg la-chess-king"></i>@lang('Add Custom Markets')</button>
            @endif

        </div>
        <div class="col-lg-7 col-xl-8 col-xxl-9">
            <div class="mb-3">
                <div class="card">
                    <div class="card-header">
                        @include('admin.game.partials.game_title')
                    </div>
                    <div class="card-body d-flex flex-wrap justify-content-between bg-light gap-4">
                        <div>
                            <small class="text-muted"> @lang('League')</small>
                            <h6 class="f-size-16px"> {{ __(@$game->league->name) }}</h6>
                        </div>

                        <div>
                            <small class="text-muted"> @lang('Category')</small>
                            <h6 class="f-size-16px"> {{ __(@$game->league->category->name) }}</h6>
                        </div>

                        <div>
                            <small class="text-muted"> @lang('Game Starts From')</small>
                            <h6 class="f-size-16px"> {{ showDateTime($game->start_time, 'd M Y, h:i A') }}</h6>
                        </div>

                        <div>
                            <small class="text-muted"> @lang('Betting Starts From')</small>
                            <h6 class="f-size-16px"> {{ showDateTime($game->bet_start_time, 'd M Y, h:i A') }}</h6>
                        </div>

                        <div>
                            <small class="text-muted"> @lang('Status')</small>
                            <div>
                                @php echo $game->statusBadge; @endphp
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <button type="button" class="showMarketList"> <i class="la la-bars"></i> @lang('Markets')</button>

            <form action="{{ route('admin.market.store') }}" method="POST">
                @csrf
                <input type="hidden" name="game_id" value="{{ $game->id }}">

                <div class="row g-3" id="outcomesContainer"></div>

                <div class="sticky-submit-button">
                    <button type="submit" class="btn btn--primary h-45 w-100 mt-3" id="submitBtn" @disabled($game->status == Status::GAME_ENDED || $game->status == Status::GAME_CANCELLED)>@lang('Save Changes')</button>
                </div>
            </form>
        </div>
    </div>

    @if (!$game->is_outright)
        <div class="modal" id="customMarketModal" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
            tabindex="-1">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Add New Custom Market')</h5>
                        <button class="close close-option-modal" data-bs-dismiss="modal" type="button" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <form id="outcomeStore" action="" method="POST">
                        <div class="modal-body">
                            <div class="form-group">
                                <label class="form-label">@lang('Title')</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Market Type')</label>
                                <select class="form-control select2" name="market_type" required>
                                    <option value="">@lang('Select One')</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn--primary w-100 h-45 customMarketAddSubmitBtn"
                                type="submit">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <x-back route="{{ route('admin.game.index') }}"></x-back>
@endpush

@push('style')
    {{-- do not use style-lib here --}}
    <link rel="stylesheet" href="{{ asset('assets/admin/css/odds-market.css') }}">
@endpush

@push('script')
    <script type="module">
        import {
            marketComponent,
            oddsOnlyOutcome,
            spreadOutcome,
            outcomesComponent,
            addNewOutComeButton,
            totalsOutcome,
            marketRemoveButton,
            totalsCustomOutcome,
            oddsOnlyCustomOutcome,
            spreadCustomOutcome
            // customMarketComponent
        } from '{{ asset('assets/admin/js/oddsUtils.js') }}';


        (function($) {
            "use strict";
            const ODDS_ONLY = @json(Status::ODDS_ONLY);
            const SPREAD_POINT = @json(Status::SPREAD_POINT);
            const OVER_UNDER = @json(Status::OVER_UNDER);
            const HANDICAP = @json(Status::HANDICAP);

            const teamOne = `{{ $game->teamOne?->name }}`;
            const teamTwo = `{{ $game->teamTwo?->name }}`;
            const existingMarkets = JSON.parse(`@json($markets)`);

            const allMarkets = $('.marketCheckBox').map(function() {
                return $(this).data('market');
            }).get();

            const customMarketTypes = [{
                    'key': 'h2h',
                    'name': 'Moneyline',
                    'outcome_type': '{{ Status::ODDS_ONLY }}',
                    'outcomes': ['single'],
                    'game_period_market': 0,
                    'player_props': 0,
                    'description': '',
                    'max_limit': 1,
                    'market_type': 'custom',
                },
                {
                    'key': 'spreads',
                    'name': 'Spreads',
                    'outcome_type': '{{ Status::SPREAD_POINT }}',
                    'outcomes': ['team_one', 'team_two'],
                    'game_period_market': 0,
                    'player_props': 0,
                    'description': '',
                    'max_limit': 1,
                    'market_type': 'custom',
                },
                {
                    'key': 'totals',
                    'name': 'Totals',
                    'outcome_type': '{{ Status::OVER_UNDER }}',
                    'outcomes': ['Over', 'Under'],
                    'game_period_market': 0,
                    'player_props': 0,
                    'description': '',
                    'max_limit': 1, //Infinity
                    'market_type': 'custom',
                },
            ];

            let marketIndex = 0;

            const prepareOutcomes = (market, teamOne, teamTwo) => {
                let outcomes = [];

                outcomes = market.outcomes.map(outcome => {
                    if (outcome === 'team_one') {
                        return teamOne;
                    } else if (outcome === 'team_two') {
                        return teamTwo;
                    }
                    return outcome;
                });

                return outcomes;
            }

            existingMarkets.forEach((e) => {
                let market = allMarkets.find((element) => element.key == e.market_type);
                if (market == undefined) {
                    market = customMarketTypes.find((element) => element.outcome_type == e.outcome_type);
                    let previousName = market.name;
                    market.db_market = e;
                    market.title = e.title;
                    market.name = e.title;
                    market.player_props = e.player_props;
                    market.game_period_market = e.game_period_market;
                    $('#outcomesContainer').append(addCustomMarket(market));
                    market.name = previousName;
                } else {
                    market.db_market = e;
                    market.title = e.title;
                    if(market.name == 'Outrights'){
                        $('#outcomesContainer').append(addCustomMarket(market));
                    } else {
                        $('#outcomesContainer').append(getMarketWithOutcomes(market));
                    }
                }
                marketIndex++;
            });


            function getComponentName(market) {
                if (market.outcome_type == ODDS_ONLY) {
                    return oddsOnlyOutcome;
                } else if (market.outcome_type == SPREAD_POINT) {
                    return spreadOutcome;
                } else if (market.outcome_type == OVER_UNDER) {
                    return totalsOutcome
                }
            }

            function getCustomComponentName(market) {
                if (market.outcome_type == ODDS_ONLY) {
                    return oddsOnlyCustomOutcome;
                } else if (market.outcome_type == SPREAD_POINT) {
                    return spreadCustomOutcome;
                } else if (market.outcome_type == OVER_UNDER) {
                    return totalsCustomOutcome
                }
            }

            function removeMarket(key) {
                const marketElements = $(`.outcomes[data-key=${key}]`).closest('.singleMarket');
                const marketElementToRemove = marketElements.filter(function() {
                    return $(this).attr('data-market-type') !== 'custom';
                }).first();

                if (marketElementToRemove.length) {
                    marketElementToRemove.remove();
                }
            }

            const handleMarketSelect = (marketCheckBox) => {
                const market = JSON.parse(marketCheckBox.dataset.market);
                if (marketCheckBox.checked) {
                    const marketWithoutcomes = getMarketWithOutcomes(market);
                    marketWithoutcomes.find('.card-header').append(marketRemoveButton);

                    $('#outcomesContainer').append(marketWithoutcomes);

                    // Scroll to the newly added item
                    $('html, body').animate({
                        scrollTop: $('#outcomesContainer').prop("scrollHeight")
                    }, 300);

                    marketIndex += 1;
                } else {
                    removeMarket(market.key);
                }

                showAddAnotherMarketButton(marketCheckBox, market);
            }


            function getMarketWithOutcomes(market) {

                const marketComponentElement = $(marketComponent(market, marketIndex));

                let parentIndex = $(marketComponentElement)[0].dataset.index;

                const outcomesElement = getOutcomesElement(market, parentIndex);

                marketComponentElement.find('.card-body').append(outcomesElement);

                if (!market.outcomes.length) {
                    const addNewButton = addNewOutComeButton(market);
                    $(marketComponentElement).find('.card-header .btn-container').prepend(addNewButton);
                }

                return marketComponentElement;
            }

            function getOutcomesElement(market, marketIndex, outcomeIndex = 0, isNew = false) {

                const outcomes = prepareOutcomes(market, teamOne, teamTwo);
                const component = getComponentName(market);

                let outcomesElement = $(outcomesComponent(market.key));

                if (!outcomes.length && market.db_market && market.db_market.outcomes.length && !isNew) {
                    market.db_market.outcomes.forEach((marketOutcome) => {
                        outcomes.push(marketOutcome.name);
                    });
                }

                if (!outcomes.length) {
                    let params = {
                        marketIndex,
                        index: outcomeIndex,
                    }
                    return outcomesElement.append(component(params));
                }

                outcomes.forEach((element, index) => {
                    let outcomeValue;
                    if (market.db_market && market.db_market && market.db_market.outcomes) {
                        outcomeValue = market.db_market.outcomes.find((val) => val.name == element);
                    }

                    let params = {
                        marketIndex,
                        index,
                        name: element,
                        label: element,
                    }

                    if (outcomeValue) {
                        params.odds = outcomeValue.odds;
                        params.point = outcomeValue.point;
                        params.status = outcomeValue.status;
                        params.locked = outcomeValue.locked;
                        params.id = outcomeValue.id;
                    }
                    outcomesElement.append(component(params));
                });

                if (market.outcome_type == SPREAD_POINT && market.db_market && !market.db_market.outcomes) {
                    outcomesElement.each((i, element) => {
                        let elemetns = $(element).find('.pointTypeSelect');
                        if (elemetns.first().val() == '+') {
                            elemetns.last().val('-');
                        } else {
                            elemetns.last().val('+');
                        }
                    });
                }
                return outcomesElement;
            }

            const handleAddNewOutcome = (button) => {
                const parentElement = $(button).parents('.card').find('.card-body');
                const market = JSON.parse(button.dataset.market);
                let marketIndex = $(button).parents('.singleMarket')[0].dataset.index;
                let outcomeIndex = Number(parentElement.find('.singleOutcome').last()[0]?.dataset.index) + 1;
                let outcomesElement = '';
                if(isNaN(outcomeIndex)) {
                    outcomeIndex = 0;
                }
                if(market?.market_type == 'custom') {
                    outcomesElement = getCustomOutcomeElement(market, marketIndex, outcomeIndex, true);
                } else {
                    outcomesElement = getOutcomesElement(market, marketIndex, outcomeIndex, true);
                }
                outcomesElement.append('<button type="button" class="btn btn--danger removeOutcomeBtn"><i class="la la-times m-0"></i></button');
                parentElement.append(outcomesElement);
            }

            const handleRemoveOutcome = (button) => {
                $(button).parents('.outcomes').remove()
            }

            const handlePointTypeChange = (e) => {
                const value = e.target.value;
                const parent = $(e.target).parents('.outcomes');
                if (value == '+') {
                    parent.find('.pointTypeSelect').not($(e.target)).val('-')
                } else {
                    parent.find('.pointTypeSelect').not($(e.target)).val('+')
                }
            }

            const handleSpredPointInput = (e) => {
                const point = e.target.value;
                const parent = $(e.target).parents('.outcomes');
                parent.find('.spreadPointInput').not($(e.target)).val(point);
            }

            const handleCollapseOutcomes = (e) => {
                e.stopPropagation();
                const button = e.currentTarget;

                $(button).find('i').toggleClass('rotate-180');
                $(button).parents('.singleMarket').find('.card-body').toggleClass('d-none');
            }

            function showAddAnotherMarketButton(checkbox, market) {
                if (market.limit == 1) {
                    return;
                }

                const marketAdded = $('.singleMarket').find(`[data-key=${market.key}]`).length;

                if (marketAdded) {
                    $(checkbox).parent().siblings('.addAnotherBtn ').removeClass('d-none');
                } else {
                    $(checkbox).parent().siblings('.addAnotherBtn ').addClass('d-none');
                }
            }

            const handleAddAnotherMarket = (button) => {
                const data = button.dataset;
                const marketCheckBox = $(button).parent().find('.marketCheckBox')[0];
                const marketAdded = $('.singleMarket').find(`[data-key=${data.key}]`).length;

                if (data.limit != -1 && marketAdded >= data.limit) {
                    notify('error', `You can add maximum ${data.limit} markets of this type`);
                    return;
                }
                handleMarketSelect(marketCheckBox);
            }

            const handleRemoveMarket = (button) => {
                const marketType = $(button).parents('.singleMarket').data('market-type');
                if(marketType != 'custom'){
                    $(`#${marketType}`).prop('checked', false);
                }
                $(button).parents('.singleMarket').remove();
            }

            const handleShowMarketList = () => {
                const marketList = $('.all-markets-list');
                marketList.toggleClass('show');
            }

            $('.hideMarketList').on('click', handleShowMarketList);
            $('.showMarketList').on('click', handleShowMarketList);


            function showCustomTab() {
                $('.custom-tab-nav-item.active').each((i, e) => {
                    $(`#${$(e).data('target')}`).siblings().removeClass('show');
                    $(`#${$(e).data('target')}`).addClass('show');
                });
            }

            function handleTabClick(e) {
                $(e.currentTarget).parent().find('.custom-tab-nav-item').not($(e)).removeClass('active');
                $(e.currentTarget).addClass('active');
                showCustomTab();
            }
            let isProcessing = false;

            function handleSwitchMarketLock(e) {
                if (isProcessing) return;
                isProcessing = true;
                let switchMarket = e.currentTarget;
                let switchMarketId = $(switchMarket).data('id');

                $.ajax({
                    type: "POST",
                    url: `{{ route('admin.market.locked', '') }}/${switchMarketId}}`,
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.error) {
                            notify('error', response.error);
                            return;
                        }
                        return notify('success', response.success);
                    },
                    complete: function() {
                        isProcessing = false;
                    }
                });

            }

            function handleSwitchMarketStatus(e) {
                if (isProcessing) return;
                isProcessing = true;
                let switchMarket = e.currentTarget;
                let switchMarketId = $(switchMarket).data('id');
                $.ajax({
                    type: "POST",
                    url: `{{ route('admin.market.status', '') }}/${switchMarketId}}`,
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.error) {
                            notify('error', response.error);
                            return;
                        }
                        notify('success', response.success);
                    },
                    complete: function() {
                        isProcessing = false;
                    }
                });

            }

            showCustomTab();

            $(document).on('change', '.pointTypeSelect', (e) => handlePointTypeChange(e));
            $(document).on('input', '.spreadPointInput', (e) => handleSpredPointInput(e));
            $(document).on('click', '.removeOutcomeBtn', (e) => handleRemoveOutcome(e.currentTarget));
            $(document).on('click', '.addNewOutcome', (e) => handleAddNewOutcome(e.currentTarget));
            $(document).on('click', '.removeMarketBtn', (e) => handleRemoveMarket(e.currentTarget));
            $(document).on('click', '.collapseBtn', (e) => handleCollapseOutcomes(e));
            $(document).on('click', '.addAnotherBtn', (e) => handleAddAnotherMarket(e.currentTarget));
            $(document).on('click', '.custom-tab-nav-item', (e) => handleTabClick(e));

            $('.marketCheckBox').on('click', (e) => handleMarketSelect(e.target));

            $(document).off('click', '.switchMarketLock').on('click', '.switchMarketLock', (e) =>
                handleSwitchMarketLock(e));
            $(document).off('click', '.switchMarketStatus').on('click', '.switchMarketStatus', (e) =>
                handleSwitchMarketStatus(e));

            // custom market
            const customMarketModal = $("#customMarketModal");
            let marketOptions = `<option value="" selected>@lang('Select One')</option>`;
            for (let market of customMarketTypes) {
                marketOptions +=
                    `<option value="${ market.key }" data-market='${ JSON.stringify(market) }' id="${ market.key }">${ market.name }</option>`;
            }
            customMarketModal.find(`select[name="market_type"]`).html(marketOptions);
            $(document).on('click', '#addCustomMarket', function(e) {
                customMarketModal.modal('show');
            });

            $(document).on('click', '.customMarketAddSubmitBtn', function(e) {
                e.preventDefault();
                let market = customMarketModal.find(`select[name="market_type"] option:selected`).data(
                'market');
                let player_props = 0;
                let game_period_market = 0;
                if($("#player-markets").is('.show')) {
                    player_props = 1;
                } else if($('#team-markets-game-time').is('.show')) {
                    game_period_market = 1;
                }
                market.player_props = player_props;
                market.game_period_market = game_period_market;
                let previousName = market.name;
                let marketTitle = customMarketModal.find(`input[name="title"]`).val();
                if (marketTitle == '') {
                    notify('error', 'Market Title is required!');
                    return;
                }
                if (market == '') {
                    notify('error', 'Market type is required!');
                    return;
                }
                market.name = marketTitle;
                market.db_market = null;

                addCustomMarket(market, true);

                customMarketModal.modal('hide');


                $('html, body').animate({
                    scrollTop: $('#outcomesContainer').prop("scrollHeight")
                }, 300);

                marketIndex += 1;
                market.name = previousName;
                customMarketModal.find('#title').val('');
                customMarketModal.find('#market_type').val('').trigger('change');
            });


            function addCustomMarket(market, isNew = false) {
                const marketComponentElement = $(marketComponent(market, marketIndex));
                if (isNew) {
                    marketComponentElement.find('.card-header').append(marketRemoveButton);
                }

                let parentIndex = $(marketComponentElement)[0].dataset.index;
                const outcomesElement = getCustomOutcomeElement(market, marketIndex, 0, isNew)

                marketComponentElement.find('.card-body').append(outcomesElement);

                if (market.outcome_type == '{{ Status::ODDS_ONLY }}') {
                    const addNewButton = addNewOutComeButton(market);
                    $(marketComponentElement).find('.card-header .btn-container').prepend(addNewButton);
                }

                $('#outcomesContainer').append(marketComponentElement);
            }


            function getCustomOutcomeElement(market, marketIndex, outcomeIndex = 0, isNew = false) {
                let outcomesElement = $(outcomesComponent(market.key));
                const component = getCustomComponentName(market);
                let totalOutcomes = market.outcomes.length;
                if (market.db_market && market.db_market && market.db_market.outcomes && !isNew) {
                    totalOutcomes = market.db_market.outcomes.length;
                }
                for (let i = 0; i < totalOutcomes; i++) {
                    let outcomeValue;
                    if (market.db_market && market.db_market && market.db_market.outcomes && !isNew) {
                        outcomeValue = market.db_market.outcomes[i];
                    }

                    let params = {
                        marketIndex: marketIndex.toString(),
                        index: ((outcomeIndex > 0)? outcomeIndex : i),
                        name: '',
                        label: '',
                    }

                    if (outcomeValue) {
                        params.name = outcomeValue.name;
                        params.odds = outcomeValue.odds;
                        params.point = outcomeValue.point;
                        params.status = outcomeValue.status;
                        params.locked = outcomeValue.locked;
                        params.id = outcomeValue.id;
                    }
                    const outcomeComponentElement = $(component(params), '');
                    outcomesElement.append(outcomeComponentElement);
                }
                return outcomesElement;
            }



        })(jQuery);
    </script>
@endpush
