@props(['marketType', 'game', 'betPlacedIds' => []])

@php
    $market = $game->markets->firstWhere('market_type', $marketType);
    $nameOne = $marketType === 'totals' ? 'Over' : $game->teamOne?->name ?? 'N/A';
    $nameTwo = $marketType === 'totals' ? 'Under' : $game->teamTwo?->name ?? 'N/A';
    $outcomeOne = $market?->outcomes->filter(fn($outcome) => strcasecmp($outcome->name, $nameOne) === 0)->first();
    $outcomeTwo = $market?->outcomes->filter(fn($outcome) => strcasecmp($outcome->name, $nameTwo) === 0)->first();
    $drawOutcome = $market?->outcomes->filter(fn($outcome) => strcasecmp($outcome->name, 'Draw') === 0)->first();
@endphp


<div class="sports-card-inner">
    <div class="sports-card-top-inner sports-card-heading">
        <span class="team-select-title">{{ $market == 'totals' ? 'O' : 1 }}</span>
        <span class="team-select-title">
            @if ($marketType == 'h2h')
                -
            @elseif($marketType == 'h2h_3way')
                x
            @elseif($marketType == 'spreads')
                @lang('Spreads')
            @elseif($marketType == 'totals')
                @lang('Total')
            @endif
        </span>
        <span class="team-select-title">{{ $market == 'totals' ? 'U' : 2 }}</span>
    </div>

    <div class="sports-card-body">
        <div class="option-odd-lists">
            <x-frontend.odds-button :outcome="$outcomeOne" :marketIsLocked="@$market->locked" />

            @if ($marketType == 'h2h' || !$market)
                <x-frontend.odds-button :marketIsLocked="@$market->locked" />
            @elseif($marketType == 'h2h_3way')
                <x-frontend.odds-button :outcome="$drawOutcome" :marketIsLocked="@$market->locked" />
            @elseif($marketType == 'spreads')
                <div class="option-odd-list__item">
                    <span class="point">{{ formattedSpreadsPoint(@$outcomeOne->point, @$outcomeTwo->point) }}</span>
                </div>
            @elseif($marketType == 'totals')
                <div class="option-odd-list__item">
                    <span class="point">{{ $outcomeOne->point }}</span>
                </div>
            @endif

            <x-frontend.odds-button :outcome="$outcomeTwo" :marketIsLocked="@$market->locked" />
        </div>
    </div>
</div>
