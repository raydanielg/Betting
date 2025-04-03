@extends($activeTemplate . 'layouts.master')
@section('master')
    @php
        $kycContent = getContent('kyc_instructions.content', true);
    @endphp

    <div class="row gy-4">
        @if ($user->kv == Status::KYC_UNVERIFIED && $user->kyc_rejection_reason)
            <div class="col-12">
                <div class="alert alert-danger mt-0" role="alert">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <h5 class="m-0">@lang('KYC Documents Rejected')</h5>
                    </div>

                    <p class="mb-0">
                        {{ __(@$kycContent->data_values->reject) }}
                        <br>

                        <a href="{{ route('user.kyc.data') }}">@lang('Click here ro review')</a>
                    </p>

                </div>
            </div>
        @elseif ($user->kv == Status::KYC_UNVERIFIED)
            <div class="col-12">
                <div class="alert alert-warning mt-0" role="alert">
                    <h5 class="m-0 mb-3">@lang('KYC Verification Required')</h5>
                    <p class="mb-0">
                        {{ __(@$kycContent->data_values->for_verification) }}
                        <a class="text--base" href="{{ route('user.kyc.form') }}">@lang('Click Here to Submit Documents')</a>
                    </p>
                </div>
            </div>
        @elseif($user->kv == Status::KYC_PENDING)
            <div class="col-12">
                <div class="alert alert-info" role="alert">
                    <h5 class="m-0 mb-3">@lang('KYC Verification Pending')</h5>
                    <p class="mb-0">
                        {{ __(@$kycContent->data_values->for_pending) }}
                        <a class="text--base" href="{{ route('user.kyc.data') }}">@lang('See KYC data')</a>
                    </p>
                </div>
            </div>
        @endif

        <div class="col-md-12">
            <div class="row gy-4">
                <div class="col-xl-8">
                    <div class="d-flex gap-2 justify-content-between align-items-center mb-3">
                        <h5 class="m-0">@lang('Bet Chart')</h5>
                        <input class="form-control w-auto bg-white" name="date" type="text" value="{{ request()->date }}" autocomplete="off" placeholder="@lang('Start Date - End Date')">
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div id="betChart"></div>
                        </div>
                    </div>


                    @if (gs('referral_program'))
                        <div class="mt-4">
                            <h5 class="m-0">
                                @lang('Referral Your Friend')
                            </h5>

                            <div class="qr-code text--base mb-1 mt-3">
                                <div class="qr-code-copy-form" data-copy=true>
                                    <input id="qr-code-text" type="text" value="{{ route('home') }}?reference={{ $user->referral_code }}" readonly>
                                    <button class="text-copy-btn copy-btn lh-1 text-white" data-bs-toggle="tooltip" data-bs-original-title="@lang('Copy to clipboard')" type="button">@lang('Copy</')</button>
                                </div>
                            </div>

                            <small class="lh-1 text-muted"><i class="la la-info-circle"></i> @lang('Earn referral bonus by inviting your friends to join our platform! Simply share your referral link with them to get started.')</small>
                        </div>
                    @endif
                </div>
                <div class="col-xl-4">

                    <h5 class="mt-0">@lang('Latest Transactions')</h5>
                    <div class="card">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                @forelse($transactions as $trx)
                                    <li class="list-group-item px-0 py-2">

                                        <div class="d-flex justify-content-between flex-wrap ">
                                            <div class="d-flex flex-column">
                                                <small class="fw-semibold">#{{ $trx->trx }}</small>
                                                <small class="text-muted lh-1"><em>{{ showDateTime($trx->created_at) }}</em></small>
                                            </div>

                                            <span class="@if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                                {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                            </span>
                                        </div>

                                        <p class="sm-text text--base mb-0 mt-1"> {{ $trx->details }}</p>
                                    </li>
                                @empty
                                    <li class="list-group-item text-center px-0">
                                        <small class="text-muted">@lang('No transaction yet')</small>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link href="{{ asset('assets/global/css/daterangepicker.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/global/js/daterangepicker.min.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.copyBtn').on('click', function() {
                var copyText = document.getElementById("textToCopy");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");
                iziToast.success({
                    message: "Copied: " + copyText.value,
                    position: "topRight"
                });
            });

            var startsOne;
            var endOne;
            let startDate;
            let endDate;

            @if (@$request->starts_from_start)
                startsOne = moment(`{{ @$request->startDate }}`);
            @endif

            @if (@$request->starts_from_end)
                endOne = moment(`{{ @$request->endDate }}`);
            @endif


            function intDateRangePicker(element, start, end) {
                $(element).daterangepicker({
                    startDate: start,
                    endDate: end,
                    ranges: {
                        'Clear': ['', ''],
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    },
                    applyButtonClasses: 'btn btn--base',
                });

                $(element).on('apply.daterangepicker', function(ev, picker) {
                    if (!(picker.startDate.isValid() && picker.endDate.isValid())) {
                        $(element).val('');
                    }
                    window.location = appendQueryParameter('date', this.value);
                });
            }

            intDateRangePicker('[name=date]', startsOne, endOne);

            var betOutcomes = {
                series: [{
                    name: 'Total Stake',
                    data: [
                        @foreach ($report['bet_stake_amount'] as $stakeAmount)
                            "{{ $stakeAmount }}",
                        @endforeach
                    ]
                }, {
                    name: 'Total Return',
                    data: [
                        @foreach ($report['bet_return_amount'] as $returnAmount)
                            "{{ $returnAmount }}",
                        @endforeach
                    ]
                }],
                chart: {
                    type: 'bar',
                    height: 415,
                    toolbar: {
                        show: true,
                        tools: {
                            download: false
                        }
                    }
                },
                grid: {
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: false
                        }
                    },
                },
                plotOutcomes: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: [
                        @foreach ($report['bet_dates'] as $date)
                            "{{ $date }}",
                        @endforeach
                    ],
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return `${val} {{ gs('cur_text') }}`
                        }
                    }
                },
            };
            var chart = new ApexCharts(document.querySelector("#betChart"), betOutcomes);
            chart.render();
        })(jQuery);
    </script>
@endpush
