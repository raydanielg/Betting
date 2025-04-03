@props([
    'link' => null,
    'title' => null,
    'value' => null,
    'icon' => '',
    'bg' => 'primary',
    'type'=>1
])
<a href="{{ $link }}" class="widget-eight bg--{{ $bg }} @if($type == 2) style-two @endif">
    <div class="widget-eight__description">
        <p class="widget-eight__content-title">{{ __($title) }}</p>
        <h3 class="widget-eight__content-amount">{{ $value }}</h3>
    </div>
    <span class="widget-eight__content-icon">
        <span class="icon">
            <i class="{{ $icon }}"></i>
        </span>
    </span>
</a>
