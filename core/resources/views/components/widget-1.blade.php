@props([
    'link' => '',
    'title' => '',
    'value' => '',
    'icon' => '',
    'bg' => 'primary',
    'color' => 'white',
    'icon_color' => null,
])

@php
    $iconColor = $icon_color ?? $color;
@endphp

<div class="card bg--{{ $bg }} overflow-hidden box--shadow2">
    @if ($link)
        <a href="{{ $link }}" class="item-link"></a>
    @endif
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-4">
                <i class="la {{ $icon }} f-size--56 text--{{ $iconColor }}"></i>
            </div>
            <div class="col-8 text-end">
                <span class="text--{{ $color }} text--small">{{ __($title) }}</span>
                <h2 class="text--{{ $color }}">{{ $value }}</h2>
            </div>
        </div>
    </div>
</div>
