@extends('admin.layouts.app')
@section('panel')
    <form method="POST" action="{{ route('admin.setting.api.save') }}" id="apiForm">
        @csrf
        <div class="row gy-4">
            <div class="col-md-6">

                <div class="row gy-4">
                    <div class="col-md-12">

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">@lang('API Key')</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group mb-0">
                                    <input class="form-control" type="text" name="ods_api_key" required value="{{ gs('ods_api_key') }}">
                                    <small class="text-muted"><i class="la la-info-circle"></i> @lang('Get an API key via email by subscribing a plan.') <a href="https://the-odds-api.com/#get-access" target="blank">@lang('See Plans')</a></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">@lang('Bookmaker Regions')</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" name="ods_api_regions[]" value="us" id="us-region" @checked(in_array('us', gs('ods_api_regions')??[]))>
                                        <label class="form-check-label" for="us-region">
                                            @lang('US')
                                        </label>
                                    </div>

                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" name="ods_api_regions[]" value="uk" id="uk-region" @checked(in_array('uk', gs('ods_api_regions')??[]))>
                                        <label class="form-check-label" for="uk-region">
                                            @lang('UK')
                                        </label>
                                    </div>

                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" name="ods_api_regions[]" value="eu" id="eu-region" @checked(in_array('eu', gs('ods_api_regions')??[]))>
                                        <label class="form-check-label" for="eu-region">
                                            @lang('EU')
                                        </label>
                                    </div>

                                    <div class="form-check mb-0">
                                        <input class="form-check-input" type="checkbox" name="ods_api_regions[]" value="au" id="au-region" @checked(in_array('au', gs('ods_api_regions')??[]))>
                                        <label class="form-check-label" for="au-region">
                                            @lang('AU')
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <small class="text-muted"><i class="la la-info-circle"></i> @lang('The bookmakers provide the odds, and the integrated API offers several bookmakers from various regions. Valid regions are listed above—select the ones you want to use. Before selecting a region, ensure you understand both the region and the bookmaker, as this will impact the Usage Quota Cost of your API subscription.')
                                        <a href="https://the-odds-api.com/sports-odds-data/bookmaker-apis.html#us-bookmakers" target="blank">@lang('See the list of bookmakers by region.')</a>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="form-group mb-0">
                            <label>@lang('Markets')</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ods_api_markets[]" value="h2h" id="head-to-head" @checked(in_array('h2h', gs('ods_api_markets')??[]))>
                                <label class="form-check-label" for="head-to-head">
                                    @lang('Head to head') / @lang('Moneyline')
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ods_api_markets[]" value="spreads" id="spread-points" @checked(in_array('spreads', gs('ods_api_markets')??[]))>
                                <label class="form-check-label" for="spread-points">
                                    @lang('Points spread') / @lang('Handicap')
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ods_api_markets[]" value="totals" id="totals" @checked(in_array('totals', gs('ods_api_markets')??[]))>
                                <label class="form-check-label" for="totals">
                                    @lang('Total') / @lang('Over/Under')
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ods_api_markets[]" value="outrights" id="outrights" @checked(in_array('outrights', gs('ods_api_markets')??[]))>

                                <label class="form-check-label" for="outrights">
                                    @lang('Outrights') / @lang('Futures')
                                </label>
                            </div>

                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="la la-info-circle"></i>
                                    @lang('For more information on the betting market list,')
                                    <a href="https://the-odds-api.com/sports-odds-data/betting-markets.html" target="blank">@lang('Click here.')</a>
                                    @lang('We are fetching the odds of the market list above from the api. We fetch odds for the featured market types listed above from the API. Only the odds of these featured market types are retrieved from the provider. You also have the option to add more predefined markets for each game from Game Management > Markets of a game, as well as the option to add custom markets.')
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (!blank($categories))
            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">@lang('Category Specific Bookmaker Regions') <small class="text-muted">(@lang('Optional'))</small></h5>

                        <p class="fst-italic text--muted">
                            <i class="la la-info-circle"></i> @lang('Regions will be managed based on the selections made above for each category. If you want to set up regions for a specific category, you can do so here.')
                        </p>
                    </div>
                    <div class="card-body">
                        @php
                            $categories = $categories->chunk(ceil($categories->count() / 2));
                        @endphp

                        <div class="row">
                            <div class="col-sm-6">
                                <div class="list-group striped">
                                    @foreach ($categories[0] as $category)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ __($category->name) }}
                                            <input type="hidden" name="category_id[]" value="{{ $category->id }}">
                                            <div class="d-flex gap-3">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" name="category_api_regions[{{ $category->id }}][]" value="us" id="us-region{{ $category->id }}" @checked($category->regions ? in_array('us', $category->regions) : false)>
                                                    <label class="form-check-label" for="us-region{{ $category->id }}">
                                                        @lang('US')
                                                    </label>
                                                </div>

                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" name="category_api_regions[{{ $category->id }}][]" value="uk" id="uk-region{{ $category->id }}" @checked($category->regions ? in_array('uk', $category->regions) : false)>
                                                    <label class="form-check-label" for="uk-region{{ $category->id }}">
                                                        @lang('UK')
                                                    </label>
                                                </div>

                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" name="category_api_regions[{{ $category->id }}][]" value="eu" id="eu-region{{ $category->id }}" @checked($category->regions ? in_array('eu', $category->regions) : false)>
                                                    <label class="form-check-label" for="eu-region{{ $category->id }}">
                                                        @lang('EU')
                                                    </label>
                                                </div>

                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" name="category_api_regions[{{ $category->id }}][]" value="au" id="au-region{{ $category->id }}" @checked($category->regions ? in_array('au', $category->regions) : false)>
                                                    <label class="form-check-label" for="au-region{{ $category->id }}">
                                                        @lang('AU')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="list-group striped">
                                    @foreach ($categories[1] as $category)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ __($category->name) }}
                                            <input type="hidden" name="category_id[]" value="{{ $category->id }}">
                                            <div class="d-flex gap-3">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" name="category_api_regions[{{ $category->id }}][]" value="us" id="us-region{{ $category->id }}" @checked($category->regions ? in_array('us', $category->regions) : false)>
                                                    <label class="form-check-label" for="us-region{{ $category->id }}">
                                                        @lang('US')
                                                    </label>
                                                </div>

                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" name="category_api_regions[{{ $category->id }}][]" value="uk" id="uk-region{{ $category->id }}" @checked($category->regions ? in_array('uk', $category->regions) : false)>
                                                    <label class="form-check-label" for="uk-region{{ $category->id }}">
                                                        @lang('UK')
                                                    </label>
                                                </div>

                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" name="category_api_regions[{{ $category->id }}][]" value="eu" id="eu-region{{ $category->id }}" @checked($category->regions ? in_array('eu', $category->regions) : false)>
                                                    <label class="form-check-label" for="eu-region{{ $category->id }}">
                                                        @lang('EU')
                                                    </label>
                                                </div>

                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" name="category_api_regions[{{ $category->id }}][]" value="au" id="au-region{{ $category->id }}" @checked($category->regions ? in_array('au', $category->regions) : false)>
                                                    <label class="form-check-label" for="au-region{{ $category->id }}">
                                                        @lang('AU')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endif

        <button type="submit" class="btn btn--primary w-100 h-45 mt-3" form="apiForm">@lang('Submit')</button>

    </form>


@endsection


@push('style')
    <style>


        .list-group.striped .list-group-item:nth-child(odd){
            background-color: #fdfdfd;
        }

        .list-group.striped .list-group-item:nth-child(even){
            background-color: #f0f0f0;
        }

    </style>
@endpush
