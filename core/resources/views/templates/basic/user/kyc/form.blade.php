@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="card custom--card">
        
        <div class="card-body">
            <form action="{{ route('user.kyc.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <x-viser-form identifier="act" identifierValue="kyc" />
                <div class="text-end">
                    <button class="btn btn--base" type="submit">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
@endsection
