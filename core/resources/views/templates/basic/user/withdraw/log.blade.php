@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="row gy-4">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table-responsive--md custom--table table">
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
                        @forelse($withdrawals as $withdraw)
                            <tr>
                                <td>
                                    #{{ $withdraw->trx }}
                                    <br>
                                    <small class="text-muted"><em> <i class="la la-clock"></i> {{ showDateTime($withdraw->created_at) }}</small>
                                </td>

                                <td>
                                    {{ showAmount($withdraw->amount) }}
                                </td>

                                <td>
                                    {{ showAmount($withdraw->charge) }}
                                </td>

                                <td>
                                    @php echo $withdraw->statusBadge @endphp
                                </td>

                                @php

                                    $details = [
                                        [
                                            'name' => 'TRX No.',
                                            'type' => 'text',
                                            'value' => '#' . $withdraw->trx,
                                        ],
                                        [
                                            'name' => 'Requested At',
                                            'type' => 'text',
                                            'value' => showDateTime($withdraw->created_at, 'd M Y, h:i A'),
                                        ],
                                        [
                                            'name' => 'Amount',
                                            'type' => 'text',
                                            'value' => '<h4 class="m-0">' . showAmount($withdraw->amount) . '</h4>',
                                        ],
                                        [
                                            'name' => 'Processing Charge',
                                            'type' => 'text',
                                            'value' => showAmount($withdraw->charge),
                                        ],
                                        [
                                            'name' => 'After Charge',
                                            'type' => 'text',
                                            'value' => '<h6 class="m-0">' . showAmount($withdraw->amount - $withdraw->charge) . '</h6>',
                                        ],
                                        [
                                            'name' => 'Rate',
                                            'type' => 'text',
                                            'value' => showAmount($withdraw->rate, currencyFormat: false) . ' ' . __($withdraw->currency),
                                        ],
                                        [
                                            'name' => 'Receivable Amount',
                                            'type' => 'text',
                                            'value' => '<h4 class="m-0 text--success">' . showAmount($withdraw->final_amount, currencyFormat: false) . ' ' . __($withdraw->currency) . '</h4>',
                                        ],
                                        [
                                            'name' => 'Status',
                                            'type' => 'text',
                                            'value' => $withdraw->statusBadge,
                                        ],
                                        [
                                            'name' => 'Payment Gateway',
                                            'type' => 'text',
                                            'value' => __(@$withdraw->method->name),
                                        ],
                                    ];

                                    foreach ($withdraw->withdraw_information as $info) {
                                        $details[] = $info;
                                        if ($info->type == 'file') {
                                            $details[$key]->value = route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $info->value));
                                        }
                                    }

                                @endphp

                                <td>
                                    <button class="btn btn--sm btn-outline--base detailBtn" data-user_data="{{ json_encode($details) }}" @if ($withdraw->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $withdraw->admin_feedback }}" @endif>
                                        <i class="las la-desktop"></i> @lang('View')
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">@lang('No withdrawals found')</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4 align-items-center pagination-wrapper">
                {{ $withdrawals->links() }}
            </div>
        </div>
    </div>


    <div id="detailModal" class="modal custom--modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="modal-title mb-2">@lang('Withdrawal Details')</h5>
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

                var userData = $(this).data('user_data');
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
