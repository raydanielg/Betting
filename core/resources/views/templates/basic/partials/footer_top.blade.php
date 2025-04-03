@php
    $footerContent = getContent('footer.content', true);
    $socialElements = getContent('social_icon.element', false, null, true);
    $policyElements = getContent('policy_pages.element', false, null, true);
@endphp

<div class="row g-4 justify-content-between">
    <div class="col-sm-12 col-xxl-4">
        <h5 class="footer__title">
            {{ __(@$footerContent->data_values->heading) }}
        </h5>
        <p class="footer__about">
            {{ __(@$footerContent->data_values->details) }}
        </p>
        <ul class="list list--row social-list flex-wrap">
            @foreach ($socialElements as $social)
                <li>
                    <a class="social-list__icon" href="{{ @$social->data_values->url }}" target="_blank">
                        @php echo @$social->data_values->icon @endphp
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="col-sm-4 col-xxl-3">
        <h5 class="footer__title">
            @lang('Usefull Link')
        </h5>
        <ul class="list footer__list">
            <li>
                <a class="footer__link" href="{{ route('home') }}"> @lang('Home') </a>
            </li>
            <li>
                <a class="footer__link" href="{{ route('blog') }}"> @lang('News & Updates') </a>
            </li>
            <li>
                <a class="footer__link" href="{{ route('contact') }}"> @lang('Contact') </a>
            </li>
        </ul>
    </div>

    <div class="col-sm-4 col-xxl-2">
        <h5 class="footer__title">
            @lang('Company Policy')
        </h5>
        <ul class="list footer__list">
            @foreach ($policyElements as $policy)
                <li>
                    <a class="footer__link" href="{{ route('policy.pages', $policy->slug) }}">
                        {{ __(@$policy->data_values->title) }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>
