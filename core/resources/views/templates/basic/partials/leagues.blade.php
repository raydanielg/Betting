<div class="sub-category-drawer">
    <ul class="sub-category-drawer__list">
        @foreach ($leagues as $league)
            <li>
                <a class="sub-category-drawer__link @if (@$activeLeague->id == $league->id) active @endif" href="{{ route('league.games', $league->slug) }}">
                    <span class="sub-category-drawer__flag">
                        <img class="sub-category-drawer__flag-img" src="{{ $league->logo() }}" alt="@lang('image')">
                    </span>
                    <span class="sub-category-drawer__text" title="{{ __($league->name) }}">
                        {{ __($league->short_name) }}
                    </span>
                </a>
            </li>
        @endforeach
    </ul>
</div>
