<div class="row">
    <div class="col-md-12">
        <div class="card overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive table-responsive--sm">
                    <table class="table align-items-center table--light">
                        <thead>
                        <tr>
                            <th>@lang('Short Code')</th>
                            <th>@lang('Description')</th>
                        </tr>
                        </thead>
                        <tbody class="list">
                            @foreach($template->shortcodes as $shortcode => $key)
                            <tr>
                                {{-- blade-formatter-disable --}}
                                <td><span class="short-codes">@php echo "{{". $shortcode ."}}"  @endphp</span></td>
                                {{-- blade-formatter-enable --}}
                                <td>{{ __($key) }}</td>
                            </tr>
                            @endforeach
                            @foreach(gs('global_shortcodes') as $shortCode => $codeDetails)
                            <tr>
                                {{-- blade-formatter-disable --}}
                                <td><span class="short-codes">@{{@php echo $shortCode @endphp}}</span></td>
                                {{-- blade-formatter-enable --}}
                                <td>{{ __($codeDetails) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div><!-- card end -->

    </div>
</div>
