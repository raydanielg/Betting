@php
    $footerElements = getContent('footer.element', false, null, true);
@endphp

<div class="row align-items-center justify-content-between">
    <div class="col-sm-6">
        <p class="xsm-text text-sm-start m-0 text-center">
            @lang('Copyright') &copy; @php echo date('Y') @endphp <a class="t-link--base" href="{{ route('home') }}">{{ __(gs('site_name')) }}</a> @lang('All right reserved')
        </p>
    </div>
    <div class="col-sm-6">
        <ul class=" gap-1 list list--row justify-content-center justify-content-sm-end mt-sm-0 align-items-center mt-2 flex-wrap">
            @foreach ($footerElements as $footer)
                <li>
                    <span class="d-inline-block footer__flag">
                        <img class="footer__flag-img" src="{{ frontendImage('footer', @$footer->data_values->payment_method_image, '130x50') }}" alt="@lang('image')">
                    </span>
                </li>
            @endforeach
        </ul>
    </div>
</div>
