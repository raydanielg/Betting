@extends($activeTemplate . 'layouts.master')
@section('master')

    @if ($user->kv == Status::KYC_VERIFIED)
        <div class="alert alert-success" role="alert">
            <p class="m-0">
                @lang('Your KYC data has been successfully verified. Below are the details you provided.')
            </p>
        </div>
    @endif

    @if ($user->kv == Status::KYC_PENDING)
        <div class="alert alert-success" role="alert">
            <p class="m-0">
                @lang('Your KYC data has been successfully submitted and is under review. Below are the details you provided.')
            </p>
        </div>
    @endif

    @if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
        <h6 class="mb-1">@lang('Rejection Reason')</h6>
        <div class="alert alert-danger" role="alert">
            <p class="m-0">
                <p>{{ $user->kyc_rejection_reason }}</p>
            </p>
            <a href="{{ route('user.kyc.form') }}">@lang('Click here to resubmit')</a>
        </div>
    @endif

    <div class="card custom--card">
        <div class="card-body">
            <h5 class="mt-0">@lang('Data Submitted for Verification')</h5>
            <div class="preview-details">
                @if ($user->kyc_data)
                    <ul class="list-group list-group-flush">
                        @foreach ($user->kyc_data as $val)
                            @continue(!$val->value)
                            <li class="list-group-item d-flex justify-content-between bg-transparent px-0 align-items-center">
                                <span>{{ __($val->name) }}</span>
                                <span class="fw-bold">
                                    @if ($val->type == 'checkbox')
                                        {{ implode(',', $val->value) }}
                                    @elseif($val->type == 'file')
                                        <a href="{{ route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $val->value)) }}"><i class="fa-regular fa-file"></i> @lang('Attachment') </a>
                                    @else
                                        <p class="mb-0">{{ __($val->value) }}</p>
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <h5 class="text-center">@lang('KYC data not found')</h5>
                @endif
            </div>
        </div>
    </div>

@endsection

@push('style')
    <style>
        .preview-details .list-group-item {
            border-style: dashed;
            padding: 12px 0px;
        }

        @media screen and (max-width:575px) {
            .preview-details .list-group-item {
                font-size: 12px;
            }
        }
    </style>
@endpush
