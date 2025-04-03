@php
    $categories = App\Models\Category::active()->orderBy('name')->get();
    $gameType = session('game_type', 'live');
@endphp

<nav class="sports-category">

    <a class="sports-category__link live-btn @if (session('game_type') == 'live') active @endif" href="{{ route('switch.type') }}">
        <span class="sports-category__icon">
            <i class="la la-desktop"></i>
        </span>
        <span class="sports-category__text">
            @lang('LIVE ONLY')
        </span>
    </a>

    <div class="sports-category__list">
        @foreach ($categories as $category)
            <a class="sports-category__link @if (@$activeCategory->id == $category->id) active @endif" href="{{ route('category.games', $category->slug) }}">
                <span class="sports-category__icon">
                    @php echo $category->icon @endphp
                </span>
                <span class="sports-category__text">
                    {{ strLimit(__($category->name), 20) }}
                </span>
            </a>
        @endforeach
    </div>
</nav>
