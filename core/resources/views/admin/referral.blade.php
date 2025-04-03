@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">

        <div class="col-12">
            <div class="bg-white p-3" role="alert">
                <p>
                    <i class="la la-info-circle"></i>
                    @if (gs('referral_program'))
                        <span class="text--success">
                            @lang('Referral Program module is currently enabled.')
                        </span>
                    @else
                      <span class="text--danger">
                        @lang('Referral Program module is currently disabled.')
                      </span>
                    @endif

                    @lang('To use this feature, you must enable the Referral Program from the') <a href="{{ route('admin.setting.system.configuration') }}" class="fw-bold text-decoration-underline">@lang('System Configuration')</a>.
                </p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <h5 class="card-header d-flex gap-2 justify-content-between align-items-center">
                    <span>
                        @lang('Deposit Commission')
                        @if (gs('deposit_commission') == 0)
                            <span class="badge badge--danger">@lang('Disabled')</span>
                        @else
                            <span class="badge badge--success">@lang('Enabled')</span>
                        @endif
                    </span>

                    @if (gs('deposit_commission') == 0)
                        <a href="{{ route('admin.referral.status.update', 'deposit_commission') }}" class="btn btn-outline--success">
                            @lang('Enable Now')
                        </a>
                    @else
                        <a href="{{ route('admin.referral.status.update', 'deposit_commission') }}" class="btn btn-outline--danger">
                            @lang('Disable Now')
                        </a>
                    @endif
                </h5>

                <div class="card-body parent">
                    <div class="table-responsive--sm">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Level')</th>
                                    <th>@lang('Commission')</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($levels->where('commission_type', 'deposit') as $item)
                                    <tr>
                                        <td>@lang('LEVEL')# {{ $item->level }}</td>
                                        <td>{{ $item->percent }} %</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                        <div class="flex-fill">
                            <input type="number" name="level" placeholder="@lang('How many level')" class="form-control levelGenerate">
                        </div>
                        <button type="button" class="btn btn--primary generate flex-fill">
                            @lang('GENERATE')
                        </button>
                    </div>

                    <form action="{{ route('admin.referral.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="commission_type" value="deposit">
                        <div class="d-none levelForm">
                            <div class="form-group">
                                <label class="text--success"> @lang('Level & Commission :')
                                    <small>@lang('(Old Levels will Remove After Generate)')</small>
                                </label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="description referral-desc">
                                            <div class="row">
                                                <div class="col-md-12 planDescriptionContainer">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <h5 class="card-header d-flex gap-2 justify-content-between align-items-center">
                    <span>
                        @lang('Bet Place Commission')
                        @if (gs('bet_commission') == 0)
                            <span class="badge badge--danger">@lang('Disabled')</span>
                        @else
                            <span class="badge badge--success">@lang('Enabled')</span>
                        @endif
                    </span>

                    @if (gs('bet_commission') == 0)
                        <a href="{{ route('admin.referral.status.update', 'bet_commission') }}" class="btn btn-outline--success">
                            @lang('Enable Now')
                        </a>
                    @else
                        <a href="{{ route('admin.referral.status.update', 'bet_commission') }}" class="btn btn-outline--danger">
                            @lang('Disable Now')
                        </a>
                    @endif
                </h5>

                <div class="card-body parent">
                    <div class="table-responsive--sm">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Level')</th>
                                    <th>@lang('Commission')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($levels->where('commission_type', 'bet') as $item)
                                    <tr>
                                        <td>@lang('LEVEL')# {{ $item->level }}</td>
                                        <td>{{ $item->percent }} %</td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table><!-- table end -->
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                        <div class="flex-fill">
                            <input type="number" name="level" placeholder="@lang('How many level')" class="form-control input-lg levelGenerate">
                        </div>
                        <button type="button" class="btn btn--primary generate flex-fill"> @lang('GENERATE') </button>
                    </div>

                    <form action="{{ route('admin.referral.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="commission_type" value="bet">
                        <div class="d-none levelForm">
                            <div class="form-group">
                                <label class="text--success"> @lang('Level & Commission :')
                                    <small>@lang('(Old Levels will Remove After Generate)')</small>
                                </label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="description referral-desc">
                                            <div class="row">
                                                <div class="col-md-12 planDescriptionContainer">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <h5 class="card-header d-flex gap-2 justify-content-between align-items-center">
                    <span>
                        @lang('Bet Win Commission')
                        @if (gs('win_commission') == 0)
                            <span class="badge badge--danger">@lang('Disabled')</span>
                        @else
                            <span class="badge badge--success">@lang('Enabled')</span>
                        @endif
                    </span>

                    @if (gs('win_commission') == 0)
                        <a href="{{ route('admin.referral.status.update', 'win_commission') }}" class="btn btn-outline--success">
                            @lang('Enable Now')
                        </a>
                    @else
                        <a href="{{ route('admin.referral.status.update', 'win_commission') }}" class="btn btn-outline--danger">
                            @lang('Disable Now')
                        </a>
                    @endif
                </h5>

                <div class="card-body parent">
                    <div class="table-responsive--sm">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Level')</th>
                                    <th>@lang('Commission')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($levels->where('commission_type', 'win') as $item)
                                    <tr>
                                        <td>@lang('LEVEL')# {{ $item->level }}</td>
                                        <td>{{ $item->percent }} %</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table><!-- table end -->
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                        <div class="flex-fill">
                            <input type="number" name="level" placeholder="@lang('How many level')" class="form-control input-lg levelGenerate">
                        </div>
                        <button type="button" class="btn btn--primary generate flex-fill"> @lang('GENERATE') </button>
                    </div>

                    <form action="{{ route('admin.referral.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="commission_type" value="win">
                        <div class="d-none levelForm">
                            <div class="form-group">
                                <label class="text--success"> @lang('Level & Commission :')
                                    <small>@lang('(Old Levels will Remove After Generate)')</small>
                                </label>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="description referral-desc">
                                            <div class="row">
                                                <div class="col-md-12 planDescriptionContainer">

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            "use strict";

            var max = 1;
            $(document).ready(function() {
                $(".generate").on('click', function() {

                    var levelGenerate = $(this).parents('.parent').find('.levelGenerate').val();
                    var a = 0;
                    var val = 1;
                    var viewHtml = '';
                    if (levelGenerate !== '' && levelGenerate > 0) {
                        $(this).parents('.parent').find('.levelForm').removeClass('d-none');
                        $(this).parents('.parent').find('.levelForm').addClass('d-block');

                        for (a; a < parseInt(levelGenerate); a++) {
                            viewHtml += `<div class="input-group mt-4">
                                            <span class="input-group-text form-control">@lang('Level')</span>
                                            <input name="level[]" class="form-control" type="number" readonly value="${val++}" required placeholder="Level">
                                            <input name="percent[]" class="form-control" type="number" step=".01" required placeholder="@lang('Percentage %')">
                                            <button class="input-group-text bg--danger text-white border-0 delete_desc"><i class='fa fa-times'></i></button>
                                        </div>`;
                        }
                        $(this).parents('.parent').find('.planDescriptionContainer').html(viewHtml);

                    } else {
                        alert('Level Field Is Required');
                    }
                });

                $(document).on('click', '.delete_desc', function() {
                    $(this).closest('.input-group').remove();
                });
            });
        });
    </script>
@endpush
