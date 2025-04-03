@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="card custom--card">
        <div class="card-body">
            <div class="mb-3">
                <h5 class="card-title">@lang('Update Your Password')</h5>
                <small class="text-muted">@lang('Update your password to keep your account secure. Enter your current password and choose a new one below.')</small>
            </div>
            <form method="post">
                @csrf
                <div class="form-group">
                    <label class="form-label">@lang('Current Password')</label>
                    <input type="password" class="form-control form--control" name="current_password" required autocomplete="current-password">
                </div>
                <div class="form-group">
                    <label class="form-label">@lang('New Password')</label>
                    <input type="password" class="form-control form--control @if (gs('secure_password')) secure-password @endif" name="password" required autocomplete="current-password">
                </div>
                <div class="form-group">
                    <label class="form-label">@lang('Confirm Password')</label>
                    <input type="password" class="form-control form--control" name="password_confirmation" required autocomplete="current-password">
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn--base w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@if (gs('secure_password'))
    @push('script-lib')
        <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
    @endpush
@endif
