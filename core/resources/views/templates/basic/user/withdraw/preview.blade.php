@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="card custom--card">
        <div class="card-body">
            <div class="alert alert-primary">
                <p class="mb-0"><i class="las la-info-circle"></i> @lang('You are requesting a withdrawal of') <b>{{ showAmount($withdraw->amount) }}</b>. @lang('The admin will send')
                    <b class="text--success">{{ showAmount($withdraw->final_amount, currencyFormat: false) . ' ' . $withdraw->currency }} </b> @lang('to your account.')
                </p>
            </div>
            <form action="{{ route('user.withdraw.submit') }}" class="disableSubmission" method="post" enctype="multipart/form-data">
                @csrf
                <div class="mb-2">
                    @php
                        echo $withdraw->method->description;
                    @endphp
                </div>
                <x-viser-form identifier="id" identifierValue="{{ $withdraw->method->form_id }}" />
                @if (auth()->user()->ts)
                    <div class="form-group">
                        <label>@lang('Google Authenticator Code')</label>
                        <input type="text" name="authenticator_code" class="form-control form--control" required>
                    </div>
                @endif
                <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
            </form>
        </div>
    </div>
@endsection


