@props([
    'link' => '',
    'icon' => '',
    'amount' => '',
    'title' => '',
])

<a class="widget-card widget-card--secondary h-100" href="{{ $link != '' ? $link : 'javascript:void(0)' }}">
    @if ($icon)
        <div class="widget-card__icon-container">
            <div class="widget-card__icon">
                <i class="{{ $icon }}"></i>
            </div>
        </div>
    @endif
    <div class="widget-card__body">
        <h5 class="widget-card__balance">{{ $amount }}</h5>
        <span class="widget-card__balance-text fw-bold">{{ __($title) }}</span>
    </div>
</a>
