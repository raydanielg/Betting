@extends($activeTemplate . 'layouts.frontend')
@section('frontend')
    <x-breadcrumb pageTitle="{{ $pageTitle }}" />
    <div class="container">
        <div class="row">
            <div class="t-pt-50 t-pb-50">
                <p class="privacy-policy-section__content-text">
                    @php echo @$policy->data_values->details @endphp
                </p>
            </div>
        </div>
    </div>
@endsection
