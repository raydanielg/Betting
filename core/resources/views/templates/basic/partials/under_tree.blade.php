<ul @if ($isFirst) class="firstList" @endif>
    @foreach ($user->allReferrals as $under)
        @if ($loop->first)
            @php $layer++ @endphp
        @endif
        <li>{{ $under->username }}
            @if ($under->allReferrals->count() > 0 && $layer < $maxLevel)
                @include($activeTemplate . 'partials.under_tree', ['user' => $under, 'layer' => $layer, 'isFirst' => false])
            @endif
        </li>
    @endforeach
</ul>
