@extends('admin.layouts.app')
@section('panel')
    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body">
                @forelse($updates as $update)
                    <div class="update-card mb-4">
                        <h5>@lang('Version') {{ $update->version }} | @lang('Uploaded'): {{ $update->created_at->format('Y-m-d') }}</h5>
                        <hr>
                        <ul>
                            @foreach ($update->update_log as $log)
                                <li>{{ __($log) }}</li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <div class="empty-list-area text-center">
                        <div class="empty-list-icon">
                            <img src="{{ getImage('assets/images/empty_list.png') }}" alt="empty">
                        </div>
                        <h5>@lang('No update log found yet!')</h5>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
@push('style')
    <style>
        .empty-list-area .empty-list-icon img {
            width: 130px;
            margin-bottom: 20px
        }
    </style>
@endpush
