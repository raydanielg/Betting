@extends($activeTemplate . 'layouts.frontend')
@section('frontend')
    <x-breadcrumb pageTitle="{{ $pageTitle }}" />

    <div class="t-pt-50 t-pb-50">
        <div class="container">
            <div class="row gy-4 justify-content-center">
                @foreach ($blogs as $blog)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-duration="0.5s" data-wow-delay="0.3s">
                        <div class="post-card">
                            <div class="post-card__thumb">
                                <img class="img-fluid" src="{{ frontendImage('blog', 'thumb_' . @$blog->data_values->image, '415x250') }}" alt="@lang('image')">
                            </div>
                            <div class="post-card__content">
                                <small class="text-muted"><i class="la la-clock"></i> {{ showDateTime(@$blog->created_at, 'Y-m-d h:i A') }}</small>
                                <h5 class="post-card__title my-2">
                                    <a class="text--base" href="{{ route('blog.details', $blog->slug) }}">
                                        {{ __(@$blog->data_values->title) }}
                                    </a>
                                </h5>
                                <p>@php echo strLimit(strip_tags(__(@$blog->data_values->description)), 90) @endphp</p>
                                <a class="btn btn--base" href="{{ route('blog.details', $blog->slug) }}">@lang('Read More')</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ paginateLinks($blogs) }}
            </div>
        </div>
    </div>
@endsection
