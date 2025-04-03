<div class="d-flex flex-column gap-3">
    @foreach ($bets as $betData)
        <div class="list-group list--group">
            <div class="list-group-item head d-flex justify-content-center">
                @if (@$betData->outcome->market->market_type == 'outrights')
                    {{ __(@$betData->outcome->market->title) }}
                @else
                    {{ __(@$betData->outcome->market->game?->teamOne?->short_name) }}
                    <span class="text--base mx-2">@lang('vs')</span>
                    {{ __($betData->outcome->market->game?->teamTwo?->short_name) }}
                @endif
            </div>
            <div class="list-group-item d-flex justify-content-between flex-wrap">
                <small>@lang('Market')</small>
                <small> {{ __($betData->outcome->market->market_title) }} </small>
            </div>

            <div class="list-group-item d-flex justify-content-between flex-wrap">
                <small>@lang('Outcome')</small>
                <small> {{ __($betData->outcome->name) }} </small>
            </div>

            <div class="list-group-item d-flex justify-content-between flex-wrap">
                <small>@lang('Status')</small>
                <span> @php echo $betData->statusBadge @endphp </span>
            </div>
        </div>
    @endforeach
</div>
