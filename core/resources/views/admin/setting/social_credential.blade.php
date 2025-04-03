@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--sm table-responsive">
                        <table class="table table--light style--two custom-data-table">
                            <thead>
                                <tr>
                                    <th>@lang('Title')</th>
                                    <th>@lang('Client ID')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (gs('socialite_credentials') as $key => $credential)
                                    <tr>
                                        <td class="fw-bold">{{ ucfirst($key) }}</td>
                                        <td>{{ $credential->client_id }}</td>
                                        <td>
                                            @if (@$credential->status == Status::ENABLE)
                                                <span class="badge badge--success">@lang('Enabled')</span>
                                            @else
                                                <span class="badge badge--warning">@lang('Disabled')</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button class="btn btn-outline--primary btn-sm editBtn"
                                                        data-client_id="{{ $credential->client_id }}"
                                                        data-client_secret="{{ $credential->client_secret }}"
                                                        data-key="{{ $key }}"><i class="la la-cogs"></i>
                                                    @lang('Configure')
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline--dark helpBtn" data-target-key="{{ $key }}">
                                                    <i class="la la-question"></i> @lang('Help')
                                                </button>
                                                @if (@$credential->status == Status::ENABLE)
                                                    <button class="btn btn-outline--danger btn-sm confirmationBtn" data-question="@lang('Are you sure that you want to disable this login credential?')" data-action="{{ route('admin.setting.socialite.credentials.status.update', $key) }}">
                                                        <i class="las la-eye-slash"></i>@lang('Disable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline--success btn-sm confirmationBtn" data-question="@lang('Are you sure that you want to enable login credential?')" data-action="{{ route('admin.setting.socialite.credentials.status.update', $key) }}">
                                                        <i class="las la-eye"></i>@lang('Enable')
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit -->
    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Update Credential'): <span class="credential-name"></span></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Client ID')</label>
                            <input type="text" class="form-control" name="client_id">
                        </div>
                        <div class="form-group">
                            <label>@lang('Client Secret')</label>
                            <input type="text" class="form-control" name="client_secret">
                        </div>
                        <div class="form-group">
                            <label>@lang('Callback URL')</label>
                            <div class="input-group">
                                <input type="text" class="form-control callback" readonly>
                                <button type="button" class="input-group-text copyInput" title="@lang('Copy')">
                                    <i class="las la-clipboard"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45"
                                id="editBtn">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Help -->
    <div id="helpModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('How to get') <span class="title-key"></span> @lang('credentials')?</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $(document).on('click', '.editBtn', function() {
                let modal = $('#editModal');
                let data = $(this).data();
                let route = "{{ route('admin.setting.socialite.credentials.update', '') }}";
                let callbackUrl = "{{ route('user.social.login.callback', '') }}";
                modal.find('form').attr('action', `${route}/${data.key}`);
                modal.find('.credential-name').text(data.key);
                modal.find('[name=client_id]').val(data.client_id);
                modal.find('[name=client_secret]').val(data.client_secret);
                modal.find('.callback').val(`${callbackUrl}/${data.key}`);
                modal.modal('show');
            });
            $('.copyInput').on('click', function(e) {
                var copybtn = $(this);
                var input = copybtn.closest('.input-group').find('input');
                if (input && input.select) {
                    input.select();
                    try {
                        document.execCommand('SelectAll')
                        document.execCommand('Copy', false, null);
                        input.blur();
                        notify('success', `Copied: ${copybtn.closest('.input-group').find('input').val()}`);
                    } catch (err) {
                        alert('Please press Ctrl/Cmd + C to copy');
                    }
                }
            });

            $(document).on('click', '.helpBtn', function() {
                var modal = $('#helpModal');

                let rules = '';
                let key = $(this).data('target-key');
                modal.find('.title-key').text(key);

                if (key == 'google') {

                    rules = `<ul class="list-group list-group-flush">
                        <li class="list-group-item"><b>@lang('Step 1')</b>: @lang('Go to') <a href="https://console.developers.google.com" target="_blank">@lang('google developer console').</a></li>
                        <li class="list-group-item"><b>@lang('Step 2')</b>: @lang('Click on Select a project than click on') <a href="https://console.cloud.google.com/projectcreate" target="_blank">@lang('New Project')</a>  @lang('and create a project providing the project name').</li>
                        <li class="list-group-item"><b>@lang('Step 3')</b>: @lang('Click on') <a href="https://console.cloud.google.com/apis/credentials" target="_blank">@lang('credentials').</a></li>
                        <li class="list-group-item"><b>@lang('Step 4')</b>: @lang('Click on create credentials and select') <a href="https://console.cloud.google.com/apis/credentials/oauthclient" target="_blank">@lang('OAuth client ID').</a></li>
                        <li class="list-group-item"><b>@lang('Step 5')</b>: @lang('Click on') <a href="https://console.cloud.google.com/apis/credentials/consent" target="_blank">@lang('Configure Consent Screen').</a></li>
                        <li class="list-group-item"><b>@lang('Step 6')</b>: @lang('Choose External option and press the create button'). </li>
                        <li class="list-group-item"><b>@lang('Step 7')</b>: @lang('Please fill up the required informations for app configuration'). </li>
                        <li class="list-group-item"><b>@lang('Step 8')</b>: @lang('Again click on') <a href="https://console.cloud.google.com/apis/credentials" target="_blank">@lang('credentials')</a> @lang('and select type as web application and fill up the required informations. Also don\'t forget to add redirect url and press create button'). </li>
                        <li class="list-group-item"><b>@lang('Step 9')</b>: @lang('Finally you\'ve got the credentials. Please copy the Client ID and Client Secret and paste it in admin panel google configuration'). </li>
                    </ul>`;
                } else if (key == 'facebook') {
                    rules = ` <ul class="list-group list-group-flush">
                        <li class="list-group-item"><b>@lang('Step 1')</b>: @lang('Go to') <a href="https://developers.facebook.com/" target="_blank">@lang('facebook developer')</a></li>
                        <li class="list-group-item"><b>@lang('Step 2')</b>: @lang('Click on Get Started and create Meta Developer account').</li>
                        <li class="list-group-item"><b>@lang('Step 3')</b>: @lang('Create an app by selecting Consumer option').</li>
                        <li class="list-group-item"><b>@lang('Step 4')</b>: @lang('Click on Setup Facebook Login and select Web option').</li>
                        <li class="list-group-item"><b>@lang('Step 5')</b>: @lang('Add site url').</li>
                        <li class="list-group-item"><b>@lang('Step 6')</b>: @lang('Go to Facebook Login > Settings and add callback URL here').</li>
                        <li class="list-group-item"><b>@lang('Step 7')</b>: @lang('Go to Setting > Basic and copy the credentials and paste to admin panel').</li>

                    </ul>`;
                } else if (key == 'linkedin') {
                    rules = `<ul class="list-group list-group-flush">
                        <li class="list-group-item"><b>@lang('Step 1')</b>: @lang('Go to') <a href="https://developer.linkedin.com/" target="_blank">@lang('linkedin developer')</a>.</li>
                        <li class="list-group-item"><b>@lang('Step 2')</b>: @lang('Click on create app and provide required information').</li>
                        <li class="list-group-item"><b>@lang('Step 3')</b>: @lang('Click on Sign In with Linkedin > Request access').</li>
                        <li class="list-group-item"><b>@lang('Step 4')</b>: @lang('Click Auth option and copy the credentials and paste it to admin panel and don\'t forget to add redirect url here').</li>
                    </ul>`;
                }

                modal.find('.modal-body').html(rules);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
