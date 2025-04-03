@php
    $betData = collect(session()->get('bets'));
    $outcomeId = $betData->pluck('outcome_id')->toArray();
    $outcomes = App\Models\Outcome::whereIn('id', $outcomeId)
        ->when(!empty($outcomeId), function ($query) use ($outcomeId) {
            $query->orderByRaw('FIELD(id, ' . implode(',', $outcomeId) . ')');
        })
        ->with(['market.game.teamOne', 'market.game.teamTwo'])
        ->get();

    $bets = $betData->zip($outcomes);
    $totalReturn = 0;
@endphp

<div class="betslip__body">
    <ul class="list betslip__list">
        @foreach ($bets as $bet)
            @include($activeTemplate . 'partials.bet_slip_item', ['bet' => $bet[0], 'outcome' => $bet[1]])
        @endforeach
    </ul>

    <span class="empty-slip-message">
        <span class="d-flex justify-content-center align-items-center">
            <img src="{{ asset($activeTemplateTrue . 'images/empty_list.png') }}" alt="@lang('image')">
        </span>
        @lang('Your selections will be displayed here')
    </span>
</div>

<div class="betslip__footer" id="betSlipBody">
    <div class="list betslip__footer-list">
        <div class="betslip-select mb-2">
            <select name="bet_type" class="form-select">
                <option value="{{ Status::SINGLE_BET }}">@lang('Single')</option>
                <option value="{{ Status::MULTI_BET }}">@lang('Multibet')</option>
            </select>
        </div>

        <div class="betslip-righ">
            <div class="betslip__list-ratio">
                <span class="mb-1">@lang('STAKE')</span>

                <div class="position-relative">
                    <span class="betslip-input-inner">{{ __(gs('cur_text')) }}</span>
                    <input class="amount" name="total_invest" type="number" step="any" placeholder="0.0">
                </div>
            </div>

            <div class="betslip__list-content my-2">
                <div class="betslip__list-match">@lang('Singles') (x<span class="bet-slip-count">{{ $bets->count() }}</span>)</div>
            </div>

            <div class="bet-return">
                <small class="text--danger total-stake-amount"></small>
                <small class="text--danger total-validation-msg"></small>
                <span>@lang('Returns'): {{ gs('cur_sym') }}<span class="total-return-amount">{{ getAmount($totalReturn) }}</span></span>
            </div>
        </div>
    </div>

    <div class="betslip__footer-bottom d-flex align-items-center">
        <input class="form-control form--control betslip-form" type="number" placeholder="@lang('Enter Amount')">
        <button class="delete-btn deleteAll"> <i class="las la-trash-alt"></i></button>
        <div class="place-btn">
            <button class="btn btn--base btn--md sm-text betslip__footer-btn bet-place-btn betPlaceBtn" type="button">
                @lang('PLACE BET')
            </button>
        </div>
    </div>
</div>

@push('style-lib')
    <link href="{{ asset($activeTemplateTrue . 'css/skeleton.css') }}" rel="stylesheet">
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            let betType;
            let stakeAmount;
            let totalStakeAmount;
            let totalReturnAmount = 0;
            const isLoggedIn = Number("{{ auth()->check() ? 1 : 0 }}");
            const isMultiBet = Number("{{ Status::MULTI_BET }}");
            const isSingleBet = Number("{{ Status::SINGLE_BET }}");
            let curText = "{{ gs('cur_text') }}";


            initBetType();
            totalStakeInput();
            betReturnAmount();
            showEmptyMessage()

            function showEmptyMessage() {
                if (Number($('.betslip__list li').length)) {
                    $('.empty-slip-message').hide();
                } else {
                    $('.empty-slip-message').show();
                }
            }

            function initBetType() {
                betType = sessionStorage.getItem('type');
                if (!betType) {
                    betType = isSingleBet;
                    sessionStorage.setItem('type', betType);
                }
                $('[name=bet_type]').val(betType);
                controlStakeInputFields();
            }

            function controlStakeInputFields() {
                if (betType == isSingleBet) {
                    $(document).find('.betslip-item-body > :nth-last-child(-n+2)').removeClass('d-none');
                } else {
                    $(document).find('.betslip-item-body > :nth-last-child(-n+2)').addClass('d-none');
                }
            }

            function betSlipCount() {
                let totalBetSlipData = $('.betslip__list li').length;

                if (!totalBetSlipData) {
                    sessionStorage.removeItem('total_stake_amount');
                    totalStakeInput();
                    showEmptyMessage();
                    return 0;
                }
                return totalBetSlipData;
            }

            function setStakeAmount(amount = 0) {
                $('.investAmount').each(function(index) {
                    $(this).val(amount);
                    let odd = Number($(this).closest('li').data('outcome_odds'));
                    $(this).closest('.betslip-right').find('.bet-return-amount').text(Math.abs(amount * odd).toFixed(2))
                });
            }

            function totalStakeInput(totalStakeAmount = 0) {
                totalStakeAmount = sessionStorage.getItem('total_stake_amount');
                $('[name=total_invest]').val(totalStakeAmount);
            }

            function totalMultiBetReturnAmount() {
                let totalMultiBetReturnAmount = $('[name=total_invest]').val();
                let multiBetOdd = 1;
                $('.betslip__list li').each(function(index) {
                    var odd = $(this).data('outcome_odds');
                    multiBetOdd *= odd;
                });

                totalReturnAmount = Math.abs(totalMultiBetReturnAmount * multiBetOdd).toFixed(2);

                $('.total-return-amount').text(totalReturnAmount);
            }

            function totalSingleBetReturnAmount() {
                let totalSingleBetReturnAmount = 0;
                $('.investAmount').each(function(index) {
                    var odd = Number($(this).closest('li').data('outcome_odds'));


                    $(this).closest('.betslip-item-stake').siblings('.betslip-item-return').find('.bet-return-amount').text(`${Math.abs($(this).val() * odd).toFixed(2)} ${curText}`);


                    totalSingleBetReturnAmount += Number($(this).val()) * odd;
                });

                totalReturnAmount = Math.abs(totalSingleBetReturnAmount).toFixed(2);
                $('.total-return-amount').text(totalReturnAmount);
            }

            function betReturnAmount() {
                betType == isMultiBet ? totalMultiBetReturnAmount() : totalSingleBetReturnAmount();
            }

            function showTotalBetSlipCount(count = 0) {
                $('.bet-slip-count').text(count);
            }

            function skeleton(type) {
                let loader = `<li class="loading">
                                    <button class="betslip__list-close"></button>
                                    <div class="betslip__list-content">
                                        <span class="betslip__list-match"></span>
                                        <span class="betslip__list-team"></span>
                                        <span class="betslip__list-market"></span>
                                        <div class="betslip__list-text"></div>
                                    </div>
                                    <div class="betslip-right">
                                        <div class="betslip__list-ratio">
                                            <span></span>
                                        </div>
                                        <span class="betslip-return"></span>
                                    </div>
                                </li>`;
                $('.betslip__list').append(loader);

                if (type == 'show') {
                    $(document).find('.loading').show();
                } else {
                    $(document).find('.loading').remove();
                }
            }


            function removeSessionTotalStakeAmount() {
                if (sessionStorage.getItem('total_stake_amount')) {
                    sessionStorage.removeItem('total_stake_amount');
                }
            }

            $(document).on('click', '.oddBtn', function() {
                let button = $(this);
                if ($(this).hasClass('active')) {
                    const outcomeId = $(button).data('outcome_id');
                    $(document).find(`.betslip-item[data-outcome_id="${outcomeId}"]`).remove();
                    removeBet(button);
                    return;
                }

                $('.empty-slip-message').hide();

                skeleton('show');

                $('#betslips').prop('checked', true);
                $('#mybets-btn').prop('checked', false);
                let currentUrl = new URL(window.location.href);
                currentUrl.searchParams.delete('mybets');
                window.history.pushState({}, '', currentUrl.toString());

                let data = {
                    _token: '{{ csrf_token() }}',
                    id: $(this).data('outcome_id'),
                    type: betType,
                    amount: sessionStorage.getItem('total_stake_amount')
                }

                $.get(`{{ route('bet.slip.add') }}`, data,
                    function(response) {
                        if (response.error) {
                            skeleton('hide');
                            $('.empty-slip-message').show();
                            notify('error', response.error);
                        } else {
                            button.addClass('active');
                            setTimeout(() => {
                                skeleton('hide');
                                $('.betslip__list').append(response);
                                controlStakeInputFields();
                                showTotalBetSlipCount(betSlipCount())
                                betReturnAmount();

                                $('.betslip__body').animate({
                                    scrollTop: $('.betslip__body')[0].scrollHeight
                                }, 1000);

                            }, 500);
                        }
                    }
                );
            });

            $(document).on('input focusout', '.investAmount', function(event) {
                $('.total-validation-msg').text('');
                $('.total-stake-amount').text('');
                $('.betslip__list li').find('.validation-msg').text('')

                stakeAmount = Number($(this).val());
                if (!stakeAmount) {
                    return;
                }

                if (stakeAmount < 0) {
                    $(this).closest('.betslip-item-stake').find('.validation-msg').text('@lang('Invalid stake amount')');
                    return;
                }

                let odd = Number($(this).closest('li').data('outcome_odds'));
                $(this).closest('.betslip-item-stake').siblings('.betslip-item-return').find('.bet-return-amount').text(`${Math.abs(stakeAmount * odd).toFixed(2)} ${curText}`);

                if (event.type == 'focusout') {
                    let data = {
                        _token: '{{ csrf_token() }}',
                        id: $(this).closest('li').data('outcome_id'),
                        amount: stakeAmount
                    }
                    $.ajax({
                        type: "POST",
                        url: `{{ route('bet.slip.update') }}`,
                        data: data,
                        success: function(response) {
                            if (betType == isSingleBet) {
                                var isInvestAmountSame = false;
                                var firstInvestAmountValue = $('.investAmount').first().val();
                                if (betSlipCount() > 1) {
                                    $('.investAmount').each(function(index) {
                                        var currentInvestAmountValue = $(this).val();
                                        if (currentInvestAmountValue && currentInvestAmountValue == firstInvestAmountValue) {
                                            isInvestAmountSame = true;
                                        } else {
                                            isInvestAmountSame = false;
                                        }
                                    });
                                }


                                if (isInvestAmountSame) {
                                    $('[name=total_invest]').val(firstInvestAmountValue)
                                    sessionStorage.setItem('total_stake_amount', firstInvestAmountValue);
                                } else {
                                    removeSessionTotalStakeAmount();
                                    totalStakeInput();
                                }
                            } else {
                                removeSessionTotalStakeAmount();
                                totalStakeInput();
                            }
                            betReturnAmount();
                        }
                    });
                }
            });


            $(document).on('click', '.removeFromSlip', function() {
                removeBet($(this));
            });

            function removeBet(button) {
                $('.total-validation-msg').text('');
                $('.total-stake-amount').text('');
                $('.betslip__list li').find('.validation-msg').text('')
                let id = button.data('outcome_id');
                let data = {
                    _token: '{{ csrf_token() }}'
                };
                $.post(`{{ route('bet.slip.remove', '') }}/${id}`, data,
                    function(response) {
                        if (response.status == 'success') {
                            $(document).find(`.oddBtn[data-outcome_id="${id}"]`).removeClass('active');
                            button.closest('.betslip-item').remove();
                            showTotalBetSlipCount(betSlipCount())
                            betReturnAmount();

                            if (Number($('.betslip__list li').length) == 1) {
                                sessionStorage.setItem('type', isSingleBet);
                                betType = isSingleBet;
                                $('[name=bet_type]').val(betType);
                                controlStakeInputFields();
                            }
                        }
                    }
                );
            }

            $('[name=bet_type]').on('change', function() {
                betType = $(this).data('value');
                $('.total-validation-msg').text('');
                $('.total-stake-amount').text('');
                $('.betslip__list li').find('.validation-msg').text('')

                betType = $(this).val();
                sessionStorage.setItem('type', betType);
                stakeAmount = sessionStorage.getItem('total_stake_amount');
                if (betType == isSingleBet) {
                    setStakeAmount(stakeAmount);
                    let totalSingleStakeAmount = 0;
                    $('.investAmount').each(function(index) {
                        if (!$(this).val()) {
                            $(this).closest('.betslip-right').find('.validation-msg').text(`@lang('Stake is required')`);
                        } else {
                            totalSingleStakeAmount += Number($(this).val());
                        }
                    });
                    if (totalSingleStakeAmount) {
                        stakeLimitValidation(totalSingleStakeAmount)
                    }
                } else {

                    totalStakeInput(stakeAmount);
                    if (stakeAmount) {
                        stakeLimitValidation(stakeAmount);
                    }
                }

                controlStakeInputFields();
                betReturnAmount();
            });

            $('.deleteAll').on('click', function() {
                let data = {
                    _token: '{{ csrf_token() }}'
                };
                $.post(`{{ route('bet.slip.remove.all', '') }}`, data,
                    function(response) {
                        if (response.status == 'success') {
                            $('.betslip__list li').remove();
                            $('.oddBtn').removeClass('active');
                            showTotalBetSlipCount(betSlipCount());
                            betReturnAmount();
                        }
                    }
                );
            })

            $('[name=total_invest]').on('input focusout', function(event) {
                $('.total-validation-msg').text('');
                $('.total-stake-amount').text('');
                $('.betslip__list li').find('.validation-msg').text('');

                totalStakeAmount = Number($(this).val());
                if (totalStakeAmount < 0) {
                    $('.total-validation-msg').text(`@lang('Invalid total stake amount')`);
                    return;
                }

                if (!totalStakeAmount) {
                    let hasValue = false;
                    $('.investAmount').each(function(index) {
                        if ($(this).val()) {
                            hasValue = true;
                        }
                    });
                    if (hasValue) {
                        removeSessionTotalStakeAmount();
                    }
                    betReturnAmount();
                    return;
                } else {
                    sessionStorage.setItem('total_stake_amount', totalStakeAmount);
                    setStakeAmount(Number($(this).val()));
                    betReturnAmount();
                }

                if (event.type == 'focusout') {
                    let data = {
                        _token: '{{ csrf_token() }}',
                        amount: totalStakeAmount,
                    }
                    $.ajax({
                        type: "POST",
                        url: `{{ route('bet.slip.update.all') }}`,
                        data: data,
                        success: function(response) {
                            $('.total-validation-msg').text('');
                            if (response.error) {
                                if (response.success) {
                                    betReturnAmount();
                                }
                            }
                        }
                    });
                }
            });

            $('.betPlaceBtn').on('click', function(e) {
                let error = false;
                let message = '';
                let totalBetCount = betSlipCount();
                let finalStakeAmount = 0;
                const userBalance = `@json(auth()->user()?->balance)` * 1;


                if (betType == isMultiBet && totalBetCount < 2) {
                    notify('error', "Minimum of two bets are required for multi bet");
                    return;
                }

                if (betType == isMultiBet) {
                    finalStakeAmount = Number($('[name=total_invest]').val());
                    if (!finalStakeAmount) {
                        $('.total-validation-msg').text(`@lang('Stake amount is required')`);
                        notify('error', "Stake amount is required");
                        return;
                    }

                } else {
                    if (!totalBetCount) {
                        notify('error', "Your bet slip is empty");
                        return;
                    }
                    finalStakeAmount = 0;

                    $('.investAmount').each(function(index) {
                        if (!$(this).val()) {
                            $(this).closest('.betslip-item-stake').find('.validation-msg').text(`@lang('Stake is required')`);
                            notify('error', "Stake is required");
                            error = true;
                        } else {
                            finalStakeAmount += Number($(this).val());
                        }
                    });

                    if (error) {
                        return;
                    }
                }

                let stakeLimit = stakeLimitValidation(finalStakeAmount);

                if (stakeLimit) {
                    return;
                }

                stakeAmount = finalStakeAmount;

                const modal = isLoggedIn ? $("#betModal") : $("#loginModal");


                if (isLoggedIn) {
                    betReturnAmount();
                    modal.find('[name=stake_amount]').val(finalStakeAmount);
                    modal.find('#betStakeAmount').text(showAmount(finalStakeAmount));
                    modal.find('#betReturnAmount').text(showAmount(totalReturnAmount));
                    modal.find('[name=type]').val(betType);
                    if (finalStakeAmount > userBalance) {
                        modal.find('#userBalance').addClass('text--danger');
                        modal.find('button[type=submit]').attr('disabled', true);
                    } else {
                        modal.find('#userBalance').removeClass('text--danger');
                        modal.find('button[type=submit]').removeAttr('disabled');
                    }
                } else {
                    let html = `<input type="hidden" name="location" value=${window.location.href}/>`;
                    modal.find('[name=username]').parent('.form-group').append(html);;
                }


                modal.modal('show');
            });

            function stakeLimitValidation(finalAmount) {
                let minLimit = betType == isSingleBet ? Number("{{ getAmount(gs('single_bet_min_limit')) }}") : Number("{{ getAmount(gs('multi_bet_min_limit')) }}");
                let maxLimit = betType == isSingleBet ? Number("{{ getAmount(gs('single_bet_max_limit')) }}") : Number("{{ getAmount(gs('multi_bet_max_limit')) }}");
                if (finalAmount < minLimit) {
                    $('.total-stake-amount').text(`Total stake {{ gs('cur_sym') }}${finalAmount}`)
                    $('.total-validation-msg').text(`Min stake limit {{ gs('cur_sym') }}${minLimit}`);
                    return true;
                }

                if (finalAmount > maxLimit) {
                    $('.total-stake-amount').text(`Total stake {{ gs('cur_sym') }}${finalAmount}`)
                    $('.total-validation-msg').text(`Max stake limit {{ gs('cur_sym') }}${maxLimit}`);
                    return true;
                }
                return false;
            }

            $('#betForm').on('submit', function(e) {
                sessionStorage.removeItem('total_stake_amount');
                return true;
            });
        })(jQuery);
    </script>
@endpush
