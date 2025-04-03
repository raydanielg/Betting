@props([
    'link' => '',
    'title' => '',
    'value' => '',
    'heading' => '',
    'subheading' => '',
    'icon' => '',
    'bg' => 'white',
    'color' => 'primary',
    'icon_style' => 'outline',
    'overlay_icon' => 1,
    'cover_cursor' => 0,
])
<div class="widget-two box--shadow2 b-radius--5 @if($cover_cursor && $link) has-link @endif bg--{{ $bg }}">

    @if($cover_cursor)
    <a href="{{ $link }}" class="item-link"></a>
    @endif
    @if ((bool) $overlay_icon)
        <i class="{{ $icon }} overlay-icon text--{{ $color }}"></i>
    @endif

    <div class="widget-two__icon b-radius--5  @if ($icon_style == 'outline') border border--{{ $color }} text--{{ $color }} @else bg--{{ $color }} @endif ">
        <i class="{{ $icon }}"></i>
    </div>

    <div class="widget-two__content">
        <h3>{{ $value || $value === "0" || $value === 0 ? $value : __($heading) }}</h3>
        <p>{{ __($title ? $title : $subheading) }}</p>
    </div>

    @if ($link && !$cover_cursor)
        <a href="{{ $link }}" class="widget-two__btn btn btn-outline--{{ $color }}">@lang('View All')</a>
    @endif
</div>
