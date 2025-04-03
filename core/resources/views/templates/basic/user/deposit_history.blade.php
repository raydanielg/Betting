@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="row gy-4">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table-responsive--sm custom--table table">
                    <thead>
                        <tr>
                            <th>@lang('TRX No.')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Charge')</th>
                            <th>@lang('Status')</th>
                            <th>@lang('Details')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($deposits as $deposit)


                            <tr>
                                <td>
                                    #{{ $deposit->trx }}
                                    <br>
                                    <small class="text-muted"><em> <i class="la la-clock"></i> {{ showDateTime($deposit->created_at) }}</small>
                                </td>

                                <td>{{ showAmount($deposit->amount) }}</td>
                                <td>{{ showAmount($deposit->charge) }}</td>

                                <td> @php echo $deposit->userStatusBadge @endphp</td>

                                @php

                                    $details = [
                                        [
                                            'name' => 'TRX No.',
                                            'type' => 'text',
                                            'value' => '#'.$deposit->trx,
                                        ],
                                        [
                                            'name' => 'Initiated At',
                                            'type' => 'text',
                                            'value' => showDateTime($deposit->created_at, 'd M Y, h:i A'),
                                        ],
                                        [
                                            'name' => 'Amount',
                                            'type' => 'text',
                                            'value' => '<h4 class="m-0">'.showAmount($deposit->amount).'</h4>',
                                        ],
                                        [
                                            'name' => 'Charge',
                                            'type' => 'text',
                                            'value' => showAmount($deposit->charge),
                                        ],
                                        [
                                            'name' => 'Rate',
                                            'type' => 'text',
                                            'value' => showAmount($deposit->rate, currencyFormat: false) . ' ' . __($deposit->method_currency),
                                        ],
                                        [
                                            'name' => 'Total Paid',
                                            'type' => 'text',
                                            'value' => '<h6 class="m-0">'.showAmount($deposit->final_amount, currencyFormat: false) . ' ' . __($deposit->method_currency).'</h6>',
                                        ],
                                        [
                                            'name' => 'Status',
                                            'type' => 'text',
                                            'value' => $deposit->userStatusBadge

                                        ],
                                        [
                                            'name' => 'Payment Gateway',
                                            'type' => 'text',
                                            'value' => $deposit->method_code < 5000 ? __(@$deposit->gateway->name)  : trans('Google Pay')

                                        ],

                                    ];

                                    if (!empty($deposit->detail) && is_array($deposit->detail) && $deposit->method_code >= 1000 && $deposit->method_code <= 5000) {
                                        foreach ($deposit->detail as $info) {
                                            if ($info->type === 'file') {
                                                $info->value = route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $info->value));
                                            }
                                            $details[] = $info;
                                        }
                                    }
                                @endphp

                                <td>
                                    <a href="javascript:void(0)" class="btn btn-outline--base btn--sm detailBtn" data-info="{{ json_encode($details) }}" @if ($deposit->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif>
                                        <i class="las la-desktop"></i> @lang('View')
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="text-center">@lang('No deposit log found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 align-items-center pagination-wrapper">
                {{ $deposits->links() }}
            </div>

        </div>
    </div>


    <div id="detailModal" class="modal custom--modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="modal-title mb-2">@lang('Deposit Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>

                    <ul class="list-group list-group-flush userData mb-2">
                    </ul>
                    <div class="feedback p-3 rounded d-none"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-items')
   <x-search-form btn="btn-light" placeholder="TRX No." />
@endpush

@push('style')
    <style>
        .input-group-text {
            border-radius: 0 5px 5px 0 !important;
        }

        .feedback {
            background: hsl(var(--danger) / 0.2);
        }
    </style>
@endpush



@push('script')
    <script>
        (function($) {
            "use strict";

            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');

                var userData = $(this).data('info');
                var html = '';
                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += `<li class="list-group-item px-0 py-2 d-flex flex-wrap align-items-center justify-content-between">
                                        <small class="deposit-card__title">
                                            ${element.name}
                                        </small>
                                        <small class="text-end">
                                            ${element.value}
                                        </small>
                                    </li>`;
                        }
                    });
                }

                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <p>${$(this).data('admin_feedback')}</p>
                    `;
                } else {
                    var adminFeedback = '';
                }

                if (adminFeedback) {
                    modal.find('.feedback').removeClass('d-none').html(adminFeedback);
                } else {
                    modal.find('.feedback').empty().addClass('d-none');
                }

                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
