@extends($activeTemplate . 'layouts.frontend')
@section('frontend')
    <x-breadcrumb pageTitle="{{ $pageTitle }}" />

    <div class="section privacy-policy-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="privacy-policy-section__content">
                        <p class="privacy-policy-section__content-text">
                            @php echo $cookie->data_values->description @endphp
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
