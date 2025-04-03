@props([
    'link' => null,
    'title' => null,
    'value' => null,
    'bg' => 'primary',
    'color' => 'white',
])

<div class="widget-two box--shadow2 b-radius--5 bg--{{ $bg }} has-link">
    @if ($link)
        <a href="{{ $link }}" class="item-link"></a>
    @endif

    <div class="widget-two__content">
        <h2 class="text-{{ $color }}">{{ $value }}</h2>
        <p class="text-{{ $color }}">{{ __($title) }}</p>
    </div>
</div>
