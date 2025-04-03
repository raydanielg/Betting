@extends('admin.layouts.app')
@section('panel')
    <div class="notify__area">
        @forelse($notifications as $notification)
            <div class="notify-item-wrapper">
                <a class="notify__item @if ($notification->is_read == Status::NO) unread--notification @endif" href="{{ route('admin.notification.read', $notification->id) }}">
                    <div class="notify__content d-flex justify-content-between">
                        <div>
                            <h6 class="title">{{ __($notification->title) }}</h6>
                            <span class="date"><i class="las la-clock"></i> {{ diffForHumans($notification->created_at) }}</span>
                        </div>
                    </div>
                </a>
                <button type="button" class="btn btn-sm btn-outline--danger notify-delete-btn confirmationBtn" data-question="@lang('Are you sure to delete the notification?')" data-action="{{ route('admin.notifications.delete.single',$notification->id) }}"><i class="las la-trash me-0"></i></button>
            </div>
        @empty
            <div class="card">
                <div class="card-body">
                    <div class="empty-notification-list text-center">
                        <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                        <h5 class="text-muted">@lang('No notification found.')</h5>
                    </div>
                </div>
            </div>
        @endforelse
        <div class="mt-3">
            {{ paginateLinks($notifications) }}
        </div>
    </div>

    <x-confirmation-modal />
@endsection
@push('breadcrumb-plugins')
    @if ($hasUnread)
        <a href="{{ route('admin.notifications.read.all') }}" class="btn btn-sm btn-outline--primary"><i class="las la-check"></i>@lang('Mark All as Read')</a>
    @endif
    @if ($hasNotification)
        <button class="btn btn-sm btn-outline--danger confirmationBtn" data-action="{{ route('admin.notifications.delete.all') }}" data-question="@lang('Are you sure to delete all notifications?')"><i class="las la-trash"></i>@lang('Delete all Notification')</button>
    @endif
@endpush
