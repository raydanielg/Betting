@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Bettor')</th>
                                    <th>@lang('Sent')</th>
                                    <th>@lang('Sender')</th>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr>
                                        <td>
                                            @if ($log->user)
                                                <span class="fw-bold">{{ $log->user->fullname }}</span>
                                                <br>
                                                <span class="small">
                                                    <a href="{{ route('admin.users.detail', $log->user_id) }}"><span>@</span>{{ $log->user->username }}</a>
                                                </span>
                                            @else
                                                <span class="fw-bold">@lang('System')</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ showDateTime($log->created_at) }}
                                            <br>
                                            {{ diffForHumans($log->created_at) }}
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ keyToTitle($log->notification_type) }}</span> <br> @lang('via') {{ __($log->sender) }}
                                        </td>
                                        <td>
                                            @if ($log->subject) {{ __($log->subject) }}
                                            @else
                                                @lang('N/A') @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline--primary notifyDetail" data-type="{{ $log->notification_type }}" @if ($log->notification_type == 'email') data-message="{{ route('admin.report.email.details', $log->id) }}" @else data-message="{{ $log->message }}" @if ($log->image) data-image="{{ asset(getFilePath('push') . '/' . $log->image) }}" @endif @endif data-sent_to="{{ $log->sent_to }}"><i class="las la-desktop"></i> @lang('Detail')</button>
                                        </td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table><!-- table end -->
                        </div>
                    </div>
                    @if ($logs->hasPages())
                        <div class="card-footer py-4">
                            {{ paginateLinks($logs) }}
                        </div>
                    @endif
                </div><!-- card end -->
            </div>
        </div>


        <div class="modal fade" id="notifyDetailModal" tabindex="-1" aria-labelledby="notifyDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="notifyDetailModalLabel">@lang('Notification Details')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><i class="las la-times"></i></button>
                    </div>
                    <div class="modal-body">
                        <h3 class="text-center mb-3">@lang('To'): <span class="sent_to"></span></h3>
                        <div class="detail"></div>
                    </div>
                </div>
            </div>
        </div>
    @endsection


    @push('breadcrumb-plugins')
        @if (@$user)
            <a href="{{ route('admin.users.notification.single', $user->id) }}" class="btn btn-outline--primary btn-sm"><i class="las la-paper-plane"></i> @lang('Send Notification')</a>
        @else
            <x-search-form placeholder="Search Username" dateSearch='yes' />
        @endif
    @endpush

    @push('script')
        <script>
            $('.notifyDetail').on('click', function() {
                var message = ''
                if ($(this).data('image')) {
                    message += `<img src="${$(this).data('image')}" class="w-100 mb-2" alt="image">`;
                }
                message += $(this).data('message');
                var sent_to = $(this).data('sent_to');
                var modal = $('#notifyDetailModal');
                if ($(this).data('type') == 'email') {
                    var message = `<iframe src="${message}" height="500" width="100%" title="Iframe Example"></iframe>`
                }
                $('.detail').html(message)
                $('.sent_to').text(sent_to)
                modal.modal('show');
            });
        </script>
    @endpush
