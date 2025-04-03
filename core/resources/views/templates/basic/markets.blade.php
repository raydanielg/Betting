@extends($activeTemplate . 'layouts.bet')
@section('bet')
    @php
        $outcomesId = collect(session()->get('bets'))->pluck('outcome_id')->toArray();
    @endphp
    <div class="odd-list pt-0">
        <div class="odd-list__title">@lang('Markets')</div>
        <div class="row gx-0 pd-lg-15 gx-lg-3 gy-3">
            <div class="col-12">
                <div class="odd-list__head">
                    @if ($game->teamOne && $game->teamTwo)
                        <div class="odd-list__team">
                            <div class="odd-list__team-name">{{ __($game->teamOne->name) }}</div>
                            <div class="odd-list__team-img">
                                <img class="odd-list__team-img-is" src="{{ $game->teamOne->teamImage() }}" alt="image" />
                            </div>
                        </div>

                        <div class="odd-list__team-divide">@lang('VS')</div>

                        <div class="odd-list__team justify-content-end">
                            <div class="odd-list__team-img">
                                <img class="odd-list__team-img-is" src="{{ $game->teamTwo->teamImage() }}" alt="image" />
                            </div>
                            <div class="odd-list__team-name">{{ __($game->teamTwo->name) }}</div>
                        </div>
                    @else
                        <div class="odd-list__team-name">{{ __($game->league->name) }}</div>
                    @endif
                </div>

                <div class="odd-list__body">
                    <div class="odd-list__body-content">
                        @forelse ($game->markets as $market)
                            <div class="accordion accordion--odd">
                                <div class="accordion-item ">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#market-{{ $market->id }}" aria-expanded="true">
                                            {{ __($market->title ?? $market->market_title) }}
                                        </button>
                                    </h2>
                                    <div id="market-{{ $market->id }}" class="accordion-collapse collapse show">
                                        <div class="accordion-body">
                                            <ul class="odd-list__outcomes">
                                                @forelse ($market->outcomes as $outcome)
                                                    <li class="flex-between">
                                                        <span class="odd-list__outcome-text">
                                                            {{ __($outcome->name) }}
                                                            @if ($outcome->point)
                                                                ({{ $outcome->point }})
                                                            @endif
                                                        </span>
                                                        <button class="odd-list__outcome oddBtn @if (in_array($outcome->id, $outcomesId)) active @endif @if ($outcome->locked || $market->locked) locked @endif" data-outcome_id="{{ $outcome->id }}">
                                                            <span class="odd-list__outcome-ratio">{{ rateData($outcome->odds) }} </span>
                                                        </button>
                                                    </li>
                                                @empty
                                                    <small class="text-muted"> @lang('No odds available for now')</small>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-message mt-3">
                                <img class="img-fluid" src="{{ asset($activeTemplateTrue . '/images/empty_message.png') }}" alt="@lang('image')">
                                <p>@lang('No markets available for now')</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
