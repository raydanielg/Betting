@auth
    @php
        $betHistory = App\Models\BetItem::whereHas('bet', function ($query) {
            $query->where('user_id', auth()->id())->notSettled();
        })
            ->with(['bet', 'market.game.teamOne', 'market.game.teamTwo', 'outcome'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->groupBy('bet_id')
            ->map(function ($group) {
                $bet = $group->first()->bet;
                return (object) [
                    'bet' => $bet,
                    'details' => $group,
                ];
            });
    @endphp

    @if ($betHistory->isNotEmpty())
        <div class="single-bet">
            <ul class="list bets__list">
                @foreach ($betHistory as $betData)
                    <li class="bet-list-item mb-0">
                        <div class="bet-list-item__body">
                            @foreach (@$betData->details ?? [] as $betDetail)
                                @php
                                    $market = @$betDetail->market;
                                    $outcome = @$betDetail->outcome;
                                @endphp

                                <div class="bet-single">

                                    <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                                        <span class="bet-market_type">{{ @$market->market_title }}</span>
                                        @php
                                            echo @$betDetail->statusBadge;
                                        @endphp
                                    </div>

                                    <div class="betslip-item-league">
                                        @php
                                            echo @$market->game->league->category->icon;
                                        @endphp
                                        <span class="betslip-item-league__name">{{ __(@$market->game->league->name) }}</span>
                                    </div>

                                    @if ($market->market_type != 'outrights')
                                        <div class="bet-single__teams">
                                            <span class="bet-single__team">{{ __(@$market->game->teamOne->name) }}</span>
                                            <span class="bet-single__vs">@lang('vs')</span>
                                            <span class="bet-single__team">{{ __(@$market->game->teamTwo->name) }}</span>
                                        </div>
                                    @endif

                                    <div class="bet-single__selected-team mb-2">
                                        <span class="name">{{ __(@$outcome->name) }}</span>
                                    </div>


                                </div>
                            @endforeach
                        </div>
                        <div class="bet-list-item__footer">
                            <div class="bet-single-info">
                                <div class="bet-single-info__item">
                                    <span class="label">@lang('Stake Amount')</span>
                                    <span class="value">{{ showAmount(@$betData->bet->stake_amount, exceptZeros: true) }}</span>
                                </div>
                                <div class="bet-single-info__item">
                                    <span class="label">@lang('Win Amount')</span>
                                    <span class="value">{{ showAmount(@$betData->bet->return_amount, exceptZeros: true) }}</span>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <div class="betslip__body h-100">
            <div class="empty-slip-message ">
                <span class="d-flex justify-content-center align-items-center">
                    <img src="{{ asset($activeTemplateTrue . 'images/empty_list.png') }}" alt="@lang('image')">
                </span>
                @lang('No bet placed yet')
            </div>
        </div>
    @endif
@else
    <div class="login-message">
        <p class="login-message-text">
            @lang('Login to see your open bets displayed here')
        </p>
    </div>
@endauth
