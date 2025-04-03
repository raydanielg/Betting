@props([
    'link' => null,
    'title' => null,
    'value' => null,
    'icon' => '',
    'bg' => 'primary',
])

<div class="widget-six bg--white p-3 rounded-2 box-shadow3">
    <div class="widget-six__top">
        <i class="{{ $icon }} bg--{{ $bg }} text--white b-radius--5"></i>
        <p>{{ __($title) }}</p>
    </div>
    <div class="widget-six__bottom mt-3">
        <h4 class="widget-six__number">{{ $value }}</h4>
        <a href="{{ $link }}" class="widget-six__btn"><span class="text--small">@lang('View All')</span><i class="las la-arrow-right"></i></a>
    </div>
</div>
