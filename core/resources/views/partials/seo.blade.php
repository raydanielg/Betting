@if($seo)
    <meta name="title" Content="{{ gs()->siteName(__($pageTitle)) }}">
    <meta name="description" content="{{ @$seoContents->description ?? $seo->description }}">
    <meta name="keywords" content="{{ implode(',',@$seoContents->keywords ?? $seo->keywords) }}">
    <link rel="shortcut icon" href="{{ siteFavicon() }}" type="image/x-icon">

    {{--<!-- Apple Stuff -->--}}
    <link rel="apple-touch-icon" href="{{ siteLogo() }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-title" content="{{ gs()->siteName($pageTitle) }}">
    {{--<!-- Google / Search Engine Tags -->--}}
    <meta itemprop="name" content="{{ gs()->siteName($pageTitle) }}">
    <meta itemprop="description" content="{{ @$seoContents->description ?? $seo->description }}">
    <meta itemprop="image" content="{{ $seoImage ?? getImage(getFilePath('seo') .'/'. $seo->image) }}">
    {{--<!-- Facebook Meta Tags -->--}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ @$seoContents->social_title ?? $seo->social_title }}">
    <meta property="og:description" content="{{ @$seoContents->social_description ?? $seo->social_description }}">
    <meta property="og:image" content="{{ $seoImage ?? getImage(getFilePath('seo') .'/'. $seo->image) }}">
    <meta property="og:image:type" content="image/{{ pathinfo(getImage(getFilePath('seo')) .'/'. $seo->image)['extension'] }}">
    @php $socialImageSize = explode('x', getFileSize('seo')) @endphp
    <meta property="og:image:width" content="{{ $socialImageSize[0] }}">
    <meta property="og:image:height" content="{{ $socialImageSize[1] }}">
    <meta property="og:url" content="{{ url()->current() }}">
    {{--<!-- Twitter Meta Tags -->--}}
    <meta name="twitter:card" content="summary_large_image">
@endif
