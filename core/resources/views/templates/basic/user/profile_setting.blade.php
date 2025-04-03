@extends($activeTemplate . 'layouts.master')
@section('master')
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mt-0 mb-2">{{ $user->fullname }}</h4>

            <ul class="list-group d-flex flex-column flex-md-row flex-wrap gap-3 justify-content-between">
                <li class="list-group-item d-flex flex-column border-0 p-0">
                    <small class="text-muted">@lang('Username')</small>
                    <span class="fw-semibold lh-1"> {{ $user->username }}</span>
                </li>
                <li class="list-group-item d-flex flex-column border-0 p-0">
                    <small class="text-muted">@lang('Email')</small>
                    <span class="fw-semibold lh-1"> {{ $user->email }}</span>
                </li>
                <li class="list-group-item d-flex flex-column border-0 p-0">
                    <small class="text-muted">@lang('Mobile')</small>
                    <span class="fw-semibold lh-1"> {{ $user->mobile }}</span>
                </li>

                <li class="list-group-item d-flex flex-column border-0 p-0">
                    <small class="text-muted">@lang('Country')</small>
                    <span class="fw-semibold lh-1"> {{ $user->country_name }}</span>
                </li>

                <li class="list-group-item d-flex flex-column border-0 p-0">
                    <small class="text-muted">@lang('KYC')</small>
                    @if ($user->kv)
                        <span class="fw-semibold lh-1 text--success"> <i class="la la-check-circle"></i> @lang('Verified')</span>
                    @else
                        <span class="fw-semibold lh-1 text--danger"> <i class="la la-times-circle"></i> @lang('Not Verified')</span>
                    @endif
                </li>
            </ul>

        </div>
    </div>


    <div class="mb-3">
        <h5 class="m-0">@lang('Update Your Profile')</h5>
        <small class="text-muted">
            @lang('Keep your information up to date to ensure smooth communication and account management. Please review and update your details below.')
        </small>
    </div>

    <form class="register" method="post" enctype="multipart/form-data">
        @csrf
        <div class="card custom--card">
            <div class="card-body">
               <h6 class="m-0 mb-2">@lang('Personal Information')</h6>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('First Name')</label>
                        <input type="text" class="form-control form--control" name="firstname" value="{{ $user->firstname }}" required>
                    </div>
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('Last Name')</label>
                        <input type="text" class="form-control form--control" name="lastname" value="{{ $user->lastname }}" required>
                    </div>
                </div>

               <h6 class="mt-3 mb-2">@lang('Contact Information')</h6>
                <div class="row">
                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('State') <span class="text-muted">(@lang('Optional'))</span></label>
                        <input type="text" class="form-control form--control" name="state" value="{{ @$user->state }}">
                    </div>

                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('Zip Code') <span class="text-muted">(@lang('Optional'))</span></label>
                        <input type="text" class="form-control form--control" name="zip" value="{{ @$user->zip }}">
                    </div>

                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('City') <span class="text-muted">(@lang('Optional'))</span></label>
                        <input type="text" class="form-control form--control" name="city" value="{{ @$user->city }}">
                    </div>


                    <div class="form-group col-sm-6">
                        <label class="form-label">@lang('Address') <span class="text-muted">(@lang('Optional'))</span></label>
                        <input type="text" class="form-control form--control" name="address" value="{{ @$user->address }}">
                    </div>


                </div>


            </div>
        </div>

        <button class="btn btn--base w-100 mt-3" type="submit">@lang('Update Profile')</button>
    </form>
@endsection
