<div class="col-md-12">
    <div class="card overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive table-responsive--sm">
                <table class=" table align-items-center table--light">
                    <thead>
                    <tr>
                        <th>@lang('Short Code') </th>
                        <th>@lang('Description')</th>
                    </tr>
                    </thead>
                    <tbody class="list">
                    {{-- blade-formatter-disable --}}
                    <tr>
                        <td><span class="short-codes">@{{fullname}}</span></td>
                        <td>@lang('Full Name of User')</td>
                    </tr>
                    <tr>
                        <td><span class="short-codes">@{{username}}</span></td>
                        <td>@lang('Username of User')</td>
                    </tr>
                    <tr>
                        <td><span class="short-codes">@{{message}}</span></td>
                        <td>@lang('Message')</td>
                    </tr>
                    @foreach(gs('global_shortcodes') as $shortCode => $codeDetails)
                    <tr>
                        <td><span class="short-codes">@{{@php echo $shortCode @endphp}}</span></td>
                        <td>{{ __($codeDetails) }}</td>
                    </tr>
                    @endforeach
                    {{-- blade-formatter-enable --}}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
