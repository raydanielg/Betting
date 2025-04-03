@extends($activeTemplate . 'layouts.app')
@section('content')
    <section class="maintenance-page flex-column justify-content-center">
        <div class="container">
            <div class="row justify-content-center align-items-center">
                <div class="col-lg-10 text-center">
                    <div class="row justify-content-center">
                        <div class="col-xl-10">
                            <h4 class="text--danger mb-2">{{ __(@$maintenance->data_values->heading) }}</h4>
                        </div>
                        <div class="col-sm-6 col-8 col-lg-12">
                            <img class="img-fluid mx-auto mb-5"
                                src="{{ getImage($activeTemplateTrue . 'images/maintenance.png') }}" alt="@lang('image')">
                        </div>
                    </div>
                    <p class="mx-auto text-center">@php echo $maintenance->data_values->description @endphp</p>
                </div>
            </div>
        </div>
    </section>
@endsection
