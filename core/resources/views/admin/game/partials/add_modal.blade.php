<div class="modal fade" id="addMarketModal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add New Market')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>

            <form action="{{ route('admin.market.store') }}" method="POST">
                @csrf
                <input name="game_id" type="hidden" value="{{ $game->id }}">
                <div class="modal-body">
                    @include('admin.game.partials.game_title')

                    <div id="marketType">
                        <div class="form-group">
                            <label>@lang('Market Type')</label>
                            <select name="market_type" class="select2 form-control"  required>
                                <option value="" selected disabled>@lang('Select One')</option>
                                @foreach (getMarkets() as $market)
                                    <option value="{{$market->key}}">{{$market->title}}</option>
                                @endforeach
                                <option value="others">@lang('Others')</option>
                            </select>
                        </div>
                        <h6 class="devide-text d-none" id="outcomesHeader">
                            <span>@lang('Outcomes')</span>
                        </h6>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('style')
    <style>
        .input-group-wrapper .input-group-text {
            background-color: transparent;
            position: relative;
            transition: all linear 0.3s;
            border-right: 0 !important;
            gap: 8px;
            flex-wrap: nowrap;
            font-size: 13px;
            color: rgb(0 0 0 / 60%);
            font-weight: 500;
        }

        .input-group-wrapper .form-select {
            appearance: none !important;
            background-position: right 0rem center !important;
            background-size: 14px 12px !important;
        }

        .input-group-wrapper .form-control {
            transition: all linear 0.3s;
            border-left: 0;
        }

        .small-title {
            font-size: 13px;
            color: rgb(0 0 0 / 60%);
            font-weight: 500;
        }

        .input-group-wrapper .input-group-text::after {
            content: "";
            position: absolute;
            top: 50%;
            right: 1px;
            height: calc(100% - 20px);
            width: 1px;
            background-color: #ced4da;
            transform: translateY(-50%);
        }

        .input-group-wrapper:has(.input-group:nth-child(2)) .input-group:first-child .input-group-text {
            border-bottom-left-radius: 0;
            border-bottom: 0;
        }

        .input-group-wrapper:has(.input-group:nth-child(2)) .input-group:first-child .form-control {
            border-bottom-right-radius: 0;
            border-bottom: 0;
        }

        .input-group-wrapper:has(.input-group:nth-child(2)) .input-group:last-child .input-group-text {
            border-top-left-radius: 0;
        }

        .input-group-wrapper:has(.input-group:nth-child(2)) .input-group:last-child .form-control {
            border-top-right-radius: 0;
        }

        .input-group-wrapper:has(.input-group:nth-child(2)) .input-group-text {
            max-width: 100px;
            width: 100%;
            border-right: 0;
        }

        .input-group-wrapper:has(.input-group:first-child .form-control:focus) .input-group:first-child .form-control {
            border-color: #4634ff;
        }

        .input-group-wrapper:has(.input-group:first-child .form-control:focus) .input-group:first-child .input-group-text {
            border-color: #4634ff;
        }

        .input-group-wrapper:has(.input-group:first-child .form-control:focus) .input-group:last-child .input-group-text {
            border-top-color: #4634ff;
        }

        .input-group-wrapper:has(.input-group:first-child .form-control:focus) .input-group:last-child .form-control {
            border-top-color: #4634ff;
        }

        .input-group-wrapper:has(.input-group:last-child .form-control:focus) .input-group:last-child .form-control {
            border-color: #4634ff;
        }

        .input-group-wrapper:has(.input-group:last-child .form-control:focus) .input-group:last-child .input-group-text {
            border-color: #4634ff;
        }

        .input-group-wrapper:has(.input-group:nth-child(2)) .form-control {
            border-left: 0;
        }

        .input-group-wrapper .form-control:focus {
            box-shadow: unset;
        }

        .devide-text {
            text-align: center;
            padding-block: 16px;
            position: relative;
            z-index: 1;
        }

        .devide-text::after {
            content: "";
            position: absolute;
            height: 1px;
            width: 100%;
            background-color: #ced4da;
            top: 50%;
            left: 0;
            z-index: -1;
        }

        .devide-text span {
            background-color: #ffffff;
            padding-inline: 8px;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            const modal = $('#addMarketModal');

            const handleAddMarket = (e) => {
                modal.modal('show');
            }

            const handleModalOnShow = () => {
                $('#marketTitle').remove();

                const marketTypeElement = modal.find('[name=market_type]');

                modal.find('[name=market_type]').select2({
                    dropdownParent: modal.find('[name=market_type]').parent()
                });

                if (marketTypeElement.val() == 'others') {
                    $('#marketType').after(marketTitleComponent());
                } else {
                    $('#marketTitle').remove();
                }
            }


            function clearOutcomes() {
                $('#outcomesHeader').addClass('d-none');
                $('#outcomesWrapper').remove();
                $('.totalsPoint').remove();
            }

            const handleMarketTypeChange = (e) => {
                const marketType = e.target.value;
                const outcomesHeader = $('#outcomesHeader');

                clearOutcomes();

                $('#marketTitle').remove();


                if (marketType == 'others') {
                    $('#marketType').after(marketTitleComponent());
                }

                outcomesHeader.removeClass('d-none');
                if (marketType == 'h2h') {
                    $(outcomesHeader).after(h2hOutcomes());
                } else if (marketType == 'h2h_3way') {
                    $(outcomesHeader).after(h2hOutcomes(true));
                } else if (marketType == 'spreads') {
                    $(outcomesHeader).after(spreadsOutcomes());
                    $(document).find('.pointTypeSelect').first().change();
                } else if (marketType == 'totals') {
                    $(outcomesHeader).after(totalsOutcomes());
                }
            }


            function h2hOutcomes(hasDraw = false) {
                return outcomeParentComponent(
                    h2hOddsComponent(0, 'Team One', `{{$game->teamOne?->name}}`)[0].outerHTML +
                    (hasDraw ? h2hOddsComponent(1, 'Draw', 'Draw')[0].outerHTML : '') +
                    h2hOddsComponent(hasDraw ? 2 : 1, 'Team Two', '{{ $game->teamTwo?->name}}')[0].outerHTML
                );
            }

            function spreadsOutcomes() {
                return outcomeParentComponent(
                    spreadsOddsComponent(0, 'Team One', '{{ $game->teamOne?->name }}')[0].outerHTML +
                    spreadsOddsComponent(1, 'Team Two', '{{ $game->teamTwo?->name }}')[0].outerHTML
                );


            }

            function totalsOutcomes() {
                return (
                    outcomeParentComponent(totalsOddsComponent(0, 'Over', 'Over')[0].outerHTML + totalsOddsComponent(1, 'Under', 'Under')[0].outerHTML)[0].outerHTML

                    +

                    $(`<div class="form-group totalsPoint">
                        <label class="required">@lang('Point')</label>
                        <input type="number" step="any" name="point" class="form-control" placeholder="0.00" required>
                    </div>`)[0].outerHTML);
            }


            function outcomeParentComponent(children) {
                return $(`<div class="d-flex justify-content-between gap-3 gap-lg-4 flex-wrap flex-sm-nowrap" id="outcomesWrapper">${children}</div>`);
            }


            function marketTitleComponent() {
                return $(`<div class="col-lg-12" id="marketTitle">
                    <div class="form-group">
                        <label class="required">@lang('Title')</label>
                        <input class="form-control" name="title" type="text" value="{{ old('title') }}" required>
                    </div>
                </div>`)
            }



            function spreadsOddsComponent(index, label, name) {
                return $(`
                    <div class="form-group flex-grow-1">
                        <label>${label}</label>
                        <div class="input-group-wrapper">
                            <div class="input-group">
                                <span class="input-group-text">Odds</span>
                                <input type="hidden" name="outcomes[${index}][name]" value="${name}">
                                <input type="number" name="outcomes[${index}][odds]" step="any" value="" class="form-control" placeholder="0.00" required>
                            </div>

                            <div class="input-group">
                                <span class="input-group-text">
                                    <small class="small-title">Point</small>
                                    <select name="outcomes[${index}][point_type]" class="form-control form-select border-0 p-0 h-auto pointTypeSelect">
                                        <option value="+"> + </option>
                                        <option value="-"> - </option>
                                    </select>
                                </span>
                                <input type="number" step="any" name="outcomes[${index}][point]" value="" class="form-control spreadPointInput" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                `);
            }

            function totalsOddsComponent(index, label, name, odds = '', point = '') {
                return $(`
                    <div class="form-group flex-grow-1">
                        <label>${label}</label>
                        <div class="input-group-wrapper">
                            <div class="input-group">
                                <span class="input-group-text">Odds</span>
                                <input type="hidden" name='outcomes[${index}][name]' value='${name}''>
                                <input type="number" name="outcomes[${index}][odds]" step="any" value="" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                `);
            }

            function h2hOddsComponent(index, label, name, odds = '') {
                return $(`
                    <div class="form-group flex-grow-1">
                        <label>${label}</label>
                        <div class="input-group-wrapper">
                            <div class="input-group">
                                <span class="input-group-text">Odds</span>
                                <input type="hidden" name='outcomes[${index}][name]' value='${name}'>
                                <input type="number" name='outcomes[${index}][odds]' step="any" value="" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                `);
            }

            const handlePointTypeChange = (e) => {
                const value = e.target.value;

                if(value == '+') {
                    modal.find('.pointTypeSelect').not($(e.target)).val('-')
                }else{
                    modal.find('.pointTypeSelect').not($(e.target)).val('+')
                }
            }

            const handleSpredPointInput = (e) => {
                const point = e.target.value;
                modal.find('.spreadPointInput').not($(e.target)).val(point);
            }

            modal.on('show.bs.modal', handleModalOnShow);
            $(document).on('change', '[name=market_type]', (e) => handleMarketTypeChange(e));
            $(document).on('change', '.pointTypeSelect', (e) => handlePointTypeChange(e));
            $(document).on('input', '.spreadPointInput', (e) => handleSpredPointInput(e))
            $('.addMarketBtn').on('click', () => handleAddMarket());

        })(jQuery);
    </script>
@endpush
