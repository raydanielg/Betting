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
                                    <th>@lang('Email')</th>
                                    <th>@lang('Subscribe At')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subscribers as $subscriber)
                                    <tr>
                                        <td>{{ $subscriber->email }}</td>
                                        <td>{{ showDateTime($subscriber->created_at) }}</td>
                                        <td>
                                            <button class="btn btn-sm btn-outline--danger confirmationBtn"
                                                    data-question="@lang('Are you sure to remove this subscriber?')"
                                                    data-action="{{ route('admin.subscriber.remove', $subscriber->id) }}">
                                                <i class="las la-trash"></i> @lang('Remove')
                                            </button>
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
                @if ($subscribers->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($subscribers) }}
                    </div>
                @endif
            </div><!-- card end -->
        </div>


    </div>

    <x-confirmation-modal />
@endsection
@if ($subscribers->count())
    @push('breadcrumb-plugins')
        <a href="{{ route('admin.subscriber.send.email') }}" class="btn btn-sm btn-outline--primary"><i class="las la-paper-plane"></i>@lang('Send Email')</a>
    @endpush
@endif
