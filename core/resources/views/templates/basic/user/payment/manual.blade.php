@extends($activeTemplate . 'layouts.master')

@section('master')
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card custom--card">
                    <div class="card-body  ">
                        <form action="{{ route('user.deposit.manual.update') }}" method="POST" class="disableSubmission" enctype="multipart/form-data">
                            @csrf
                            <div class="alert alert-primary">
                                <p class="mb-0"><i class="las la-info-circle"></i> @lang('You are requesting') <b>{{ showAmount($data['amount']) }}</b> @lang('to deposit.') @lang('Please pay')
                                    <b>{{ showAmount($data['final_amount'], currencyFormat: false) . ' ' . $data['method_currency'] }} </b> @lang('for successful payment.')
                                </p>
                            </div>
                            @if(strip_tags($data->gateway->description))
                                <div class="mb-3">@php echo $data->gateway->description @endphp</div>
                            @endif
                            <x-viser-form identifier="id" identifierValue="{{ $gateway->form_id }}" />

                            <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
@endsection
