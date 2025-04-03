@extends('admin.layouts.app')

@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card ">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light">
                            <thead>
                                <tr>
                                    <th>@lang('Subject')</th>
                                    <th>@lang('Submitted By')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Priority')</th>
                                    <th>@lang('Last Reply')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.ticket.view', $item->id) }}" class="fw-bold"> [@lang('Ticket')#{{ $item->ticket }}] {{ strLimit($item->subject, 30) }} </a>
                                        </td>

                                        <td>
                                            @if ($item->user_id)
                                                <a href="{{ route('admin.users.detail', $item->user_id) }}"> {{ @$item->user->fullname }}</a>
                                            @else
                                                <p class="fw-bold"> {{ $item->name }}</p>
                                            @endif
                                        </td>
                                        <td>
                                            @php echo $item->statusBadge; @endphp
                                        </td>
                                        <td>
                                            @if ($item->priority == Status::PRIORITY_LOW)
                                                <span class="badge badge--dark">@lang('Low')</span>
                                            @elseif($item->priority == Status::PRIORITY_MEDIUM)
                                                <span class="badge  badge--warning">@lang('Medium')</span>
                                            @elseif($item->priority == Status::PRIORITY_HIGH)
                                                <span class="badge badge--danger">@lang('High')</span>
                                            @endif
                                        </td>

                                        <td>
                                            {{ diffForHumans($item->last_reply) }}
                                        </td>

                                        <td>
                                            <a href="{{ route('admin.ticket.view', $item->id) }}" class="btn btn-sm btn-outline--primary ms-1">
                                                <i class="las la-desktop"></i> @lang('Details')
                                            </a>
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
                @if ($items->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($items) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>
    </div>
@endsection


@push('breadcrumb-plugins')
    <x-search-form placeholder="Search here..." />
@endpush
