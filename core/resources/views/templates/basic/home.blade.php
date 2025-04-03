@extends($activeTemplate . 'layouts.bet')
@section('bet')
    @php
        $betPlacedIds = collect(session()->get('bets'))
            ->pluck('outcome_id')
            ->toArray();
    @endphp

    <div class="col-12">
        @include($activeTemplate . 'partials.slider')
    </div>

    <div class="col-12 top-sticky">
        @include($activeTemplate . 'partials.leagues')
    </div>

    <div class="col-12">
        <div class="betting-body">
            <div class="row g-3">
                @foreach ($games as $game)
                    @php
                        $outrightMarket = $game->markets->where('market_type', 'outrights')->first();
                        $marketDescription = $activeLeague->description ?? $activeLeague->name;
                    @endphp

                    @if ($outrightMarket)
                        <div class="col-12">
                            <div class="sports-card">
                                <div class="sports-card-wrapper sports-card-wrapper-lg">
                                    <x-frontend.odds-teams :game="$game" :marketTitle="$outrightMarket->title" />
                                    @foreach ($outrightMarket->outcomes as $outcome)
                                        <div class="sports-card-inner">
                                            <div class="sports-card-top-inner sports-card-heading">
                                                <span class="team-select-title">{{ $outcome->name }}</span>
                                            </div>
                                            <div class="sports-card-body">
                                                <div class="option-odd-lists"><x-frontend.odds-button :outcome="$outcome" /></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col-12">
                            <div class="sports-card">
                                <div class="sports-card-wrapper">
                                    <x-frontend.odds-teams :game="$game" />
                                    <x-frontend.odds-list :game="$game" marketType="h2h" :betPlacedIds="$betPlacedIds" />
                                    <x-frontend.odds-list :game="$game" marketType="h2h_3way" :betPlacedIds="$betPlacedIds" />
                                    <x-frontend.odds-list :game="$game" marketType="spreads" :betPlacedIds="$betPlacedIds" />
                                    <x-frontend.odds-list :game="$game" marketType="totals" :betPlacedIds="$betPlacedIds" />
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            @if (blank($games))
                <div class="empty-message mt-3">
                    <img class="img-fluid" src="{{ asset($activeTemplateTrue . 'images/empty_message.png') }}" alt="@lang('image')">
                    <p>@lang('No game available')</p>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            function getOdds(id, callback) {
                $.get(`{{ route('market.odds', '') }}/${id}`,
                    function(data) {
                        callback(data);
                    }
                );
            }

            const slider = $('.sports-card');
            let isDown = false;
            let startX;
            let scrollLeft;
            slider.on('mousedown', function(e) {
                isDown = true;
                startX = e.pageX - slider.offset().left;
                scrollLeft = slider.scrollLeft();
                slider.css('cursor', 'grabbing');
            });
            slider.on('mouseleave', function() {
                isDown = false;
                slider.css('cursor', 'grab');
            });
            slider.on('mouseup', function() {
                isDown = false;
                slider.css('cursor', 'grab');
            });
            slider.on('mousemove', function(e) {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - slider.offset().left;
                const walk = (x - startX) * 2;
                slider.scrollLeft(scrollLeft - walk);
            });

        })(jQuery);
    </script>
@endpush
