<div class="d-flex flex-column gap-3 flex-wrap">
    @foreach ($gameMarkets ?? [] as $market)
        @php
            $isExists = $markets->where('market_type', $market->key)->first() ? true : false;
            if ($market->key == 'outrights') {
                $isExists = true;
            }
        @endphp
        <div class="form-check mb-0 flex-shrink-0">
            <label class="form-check-label user-select-none mb-0">
                <input type="checkbox" class="form-check-input marketCheckBox" data-market='@json($market)' id="{{ $market->key }}" @disabled($isExists) @checked($isExists)>
                {{ $market->name }}
            </label>

            @if (@$market->max_limit != 1 && $market->key != 'outrights')
                <button class="btn btn-sm btn--dark p-0 px-1 addAnotherBtn @if (!$isExists) d-none @endif" data-key="{{ $market->key }}" data-limit="{{ @$market->max_limit }}"><i class="la la-plus m-0"></i></button>
            @endif
        </div>
    @endforeach
</div>
