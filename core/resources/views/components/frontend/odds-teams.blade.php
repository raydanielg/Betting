@props(['game', 'marketTitle' => null])
<div class="sports-card-left">
    <div class="sports-card-heading">
        @if ($game->is_outright == Status::NO && $game->isInPlay)
            <p class="sports-card__info mb-0">
                <span class="sports-card__stream">
                    <i class="fa-regular fa-circle-play text--danger"></i>
                </span>
                <span class="sports-card__info-text">@lang('Live Now')</span>
            </p>
        @elseif($game->is_outright == Status::NO)
            <span class="sports-card__info-text">{{ carbonParse($game->start_time, 'd M, h:i') }}</span>
        @else
            <span class="sports-card__info-text empty"></span>
        @endif
    </div>

    <div class="sports-card-body">
        @if (!$marketTitle)
            @if ($game->teamOne)
                <x-frontend.odds-team :team='$game->teamOne' />
            @endif

            @if ($game->teamTwo)
                <x-frontend.odds-team :team='$game->teamTwo' />
            @endif
        @else
            {{ __($marketTitle) }}</span>
        @endif
        <div class="sports-card-left-bottom">
            <a href="{{ route('game.markets', $game->slug) }}" class="text--small ms-auto">@lang('All Markets') ({{ __(@$game->markets()->filterByGamePeriod()->count()) }})</a>
        </div>
    </div>
</div>

@once
    @push('style')
        <style>
            .empty {
                padding: 6px 0px;
            }
        </style>
    @endpush
@endonce
