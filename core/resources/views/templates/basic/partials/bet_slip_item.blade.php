<li class="betslip-item" data-outcome_id="{{ @$outcome->id }}" data-outcome_odds="{{ $bet->odds }}">
    @php
        $category = $outcome->market->game->league->category;
        $league = $outcome->market->game->league;
        $market = $outcome->market;
    @endphp
    <div class="betslip-item-header">
        <div class="betslip-item-league">
            @php
                echo $category->icon;
            @endphp
            <span class="betslip-item-league__name">{{ __($league->name) }}</span>
        </div>
        <button class="betslip-item-close removeFromSlip" data-outcome_id="{{ @$outcome->id }}" type="button">
            <i class="las la-times"></i>
        </button>
    </div>
    <div class="betslip-item-body">
        <div class="betslip-item-teams">
            @if ($market->market_type != 'outrights')
                <div class="betslip-item-team">
                    <img class="betslip-item-team__logo" src="{{ $market->game?->teamOne?->teamImage() }}" alt="team-image">
                    <span class="betslip-item-team__name">{{ __($market->game?->teamOne?->short_name) }}</span>
                </div>
                <div class="betslip-item-team">
                    <img class="betslip-item-team__logo" src="{{ $market->game?->teamTwo?->teamImage() }}" alt="team-image">
                    <span class="betslip-item-team__name">{{ __(@$market->game->teamTwo?->short_name) }}</span>
                </div>
            @endif
        </div>
        <div class="betslip-item-market">
            <span class="betslip-item-market__type">{{ @$outcome->market->title }}</span>

            <div class="betslip-item-market__wrapper">
                <span class="betslip-item-market__label">{{ __($outcome->name) }}</span>
                @if (isSuspendBet($bet))
                    <div class="badge badge--danger">@lang('Suspended')</div>
                @else
                    <div class="betslip-item-market__score">{{ rateData($bet->odds) }}</div>
                @endif
            </div>
        </div>
        <div class="betslip-item-stake">
            <label class="betslip-item-stake__label">@lang('Stake')</label>
            <div class="input-group">
                <span class="input-group-text">{{ gs('cur_sym') }}</span>
                <input class="form-control form--control investAmount" name="invest_amount" type="number" @if (@$bet->stake_amount) value="{{ @$bet->stake_amount }}" @endif autocomplete="off" step="any" placeholder="0.0">
                <span class="input-group-text">{{ gs('cur_text') }}</span>
            </div>
            <small class="text--danger validation-msg"></small>
        </div>
        <div class="betslip-item-return">
            <p class="betslip-item-return__text">@lang('Potential winnings') <span class="bet-return-amount">{{ showAmount($bet->return_amount, currencyFormat: false) }} {{ gs('cur_text') }}</span></p>
        </div>
    </div>
</li>
