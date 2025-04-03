@extends('admin.layouts.app')
@section('panel')
@push('topBar')
  @include('admin.notification.top_bar')
@endpush
<div class="row">
	<div class="col-lg-12">
        <div class="card">
            <div class="card-body px-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two custom-data-table">
                        <thead>
                            <tr>
                                <th>@lang('Name')</th>
                                <th>@lang('Subject')</th>
                                <th>@lang('Edit Template')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td>{{ __($template->name) }}</td>
                                <td>{{ __($template->subject) }}</td>
                                <td>
                                    <div class="action-btns">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.setting.notification.template.edit', ['email',$template->id]) }}" class="btn btn-outline--primary">@lang('Email')</a>
                                            <span class="btn btn--primary">@if($template->email_status != Status::ENABLE)<i class="las la-times"></i> @else <i class="las la-check"></i> @endif</span>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.setting.notification.template.edit', ['sms',$template->id]) }}"  class="btn btn-outline--info">@lang('SMS')</a>
                                            <span class="btn btn--info">@if($template->sms_status != Status::ENABLE)<i class="las la-times"></i> @else <i class="las la-check"></i> @endif</span>
                                        </div>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.setting.notification.template.edit', ['push',$template->id]) }}" class="btn btn-outline--success">@lang('Push')</a>
                                            <span class="btn btn--success">@if($template->push_status != Status::ENABLE)<i class="las la-times"></i> @else <i class="las la-check"></i> @endif</span>
                                        </div>
                                    </div>
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
        </div><!-- card end -->
    </div>
</div>
@endsection
@push('style')
    <style>
        i.fas.fa-circle {
            font-size: 12px;
        }
        .btn-group button{
            padding: 0px 15px;
        }
        .btn-group span{
            width: 34px;
            font-size: 10px;
            line-height: 24px;
        }
        .table td{
            white-space: unset;
        }

        .action-btns{
            display: flex;
            justify-content: flex-end;
            gap: 4px;
            row-gap: 5px;
            flex-wrap: wrap;
        }
    </style>
@endpush
