@extends($activeTemplate . 'layouts.frontend')
@section('frontend')
    <x-breadcrumb pageTitle="{{ $pageTitle }}" />
    <div class="t-pt-50 t-pb-50">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="blog-details-wrapper">
                        <div class="blog-details__thumb">
                            <img src="{{ frontendImage('blog', @$blog->data_values->image, '830x500') }}" alt="@lang('image')">
                            <div class="post__date">
                                <span class="date">{{ showDateTime(@$blog->created_at, 'd') }}</span>
                                <span class="month">{{ showDateTime(@$blog->created_at, 'M') }}</span>
                            </div>
                        </div>
                        <div class="blog-details__content">
                            <h4 class="blog-details__title mb-3">{{ __(@$blog->data_values->title) }}</h4>
                            @php echo __(@$blog->data_values->description) @endphp
                        </div>
                        <div class="blog-details__footer">
                            <h4 class="caption">@lang('Share This Post')</h4>
                            <ul class="blog-social__link p-0">
                                <li><a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="https://twitter.com/intent/tweet?text={{ __(@$blog->data_values->title) }}%0A{{ url()->current() }}" target="_blank"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="http://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ __(@$blog->data_values->title) }}&media={{ frontendImage('blog', @$blog->data_values->image, '830x500') }}" target="_blank"><i class="fab fa-pinterest-p"></i></a></li>
                                <li><a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{ urlencode(url()->current()) }}&amp;title={{ __(@$blog->data_values->title) }}&amp;summary={{ __(@$blog->data_values->description) }}" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="fb-comments" data-href="{{ route('blog.details', $blog->slug) }}" data-numposts="5"></div>
                </div>
                <div class="col-lg-4">
                    <div class="sidebar">
                        <div class="widget">
                            <h5 class="widget__title mt-0">@lang('Recent Updates')</h5>
                            <ul class="small-post-list p-0">
                                @foreach ($latestBlogs as $item)
                                    <li class="small-post">
                                        <div class="small-post__thumb">
                                            <img src="{{ frontendImage('blog', 'thumb_' . @$item->data_values->image, '415x250') }}" alt="@lang('image')">
                                        </div>
                                        <div class="small-post__content">
                                            <h5 class="post__title m-0">
                                                <a class="text--base" href="{{ route('blog.details', $item->slug) }}">{{ __(@$item->data_values->title) }}</a>
                                            </h5>
                                            <p class="mb-0">{{ $item->created_at->format('d M, Y') }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('fbComment')
    @php echo loadExtension('fb-comment') @endphp
@endpush
