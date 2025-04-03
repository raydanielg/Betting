@extends('admin.layouts.app')
@section('panel')
    <div class="row mb-none-30">
        <div class="col-md-12 mb-30">
            <div class="card bl--5 border--primary">
                <div class="card-body">
                    <p class="text--primary">@lang('If the logo and favicon are not changed after you update from this page, please') <a href="{{ route('admin.system.optimize.clear') }}" class="text--info text-decoration-underline">@lang('clear the cache')</a> @lang('from your browser. As we keep the filename the same after the update, it may show the old image for the cache. usually, it works after clear the cache but if you still see the old logo or favicon, it may be caused by server level or network level caching. Please clear them too.')</p>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="form-group col-md-8 col-sm-6">
                                <label> @lang('Logo')</label>
                                <x-image-uploader name="logo" :imagePath="siteLogo() . '?' . time()" :size="false" class="w-100" id="uploadLogo" :required="false" />
                            </div>
                            <div class="form-group col-md-4 col-sm-6">
                                <label> @lang('Favicon')</label>
                                <x-image-uploader name="favicon" :imagePath="siteFavicon() . '?' . time()" :size="false" class="w-100" id="uploadFavicon" :required="false" />
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
