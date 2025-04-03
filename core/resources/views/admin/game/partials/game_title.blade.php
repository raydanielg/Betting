@if (@$game->teamOne && @$game->teamTwo)
    <div class="d-flex justify-content-between justify-content-lg-center align-items-center gap-2">
        <div class="team-logo d-flex gap-2 align-items-center">
            <img src="{{ $game?->teamOne->teamImage() }}" alt="image">
            <small>{{ __(@$game->teamOne->short_name) }}</small>
        </div>
        <span class="px-3">@lang('VS')</span>
        <div class="team-logo team-logo d-flex gap-2 align-items-center">
            <img src="{{ $game?->teamTwo->teamImage() }}" alt="image">
            <smal>{{ __(@$game->teamTwo->short_name) }}</smal>
        </div>
    </div>
@else
    <small class="text-muted"> @lang('Market')</small>
    <h6>{{ __($game->title) }}</h6>
@endif

@pushOnce('style')
    <style>
        .team-logo img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            padding: 3px;
            box-shadow: 0 0 10px 0px #ddd;
        }
    </style>
@endPushOnce
