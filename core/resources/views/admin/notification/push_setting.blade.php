@extends('admin.layouts.app')
@section('panel')
    @push('topBar')
        @include('admin.notification.top_bar')
    @endpush
    <div class="row">
        <div class="col-md-12 mb-30">
            <div class="card bl--5 border--primary">
                <div class="card-body">
                    <p class="text--primary">@lang('If you want to send push notification by the firebase, Your system must be SSL certified')</p>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <form method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('API Key') </label>
                                    <input type="text" class="form-control" placeholder="@lang('API Key')" name="apiKey" value="{{ @gs('firebase_config')->apiKey }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Auth Domain') </label>
                                    <input type="text" class="form-control" placeholder="@lang('Auth Domain')" name="authDomain" value="{{ @gs('firebase_config')->authDomain }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Project Id') </label>
                                    <input type="text" class="form-control" placeholder="@lang('Project Id')" name="projectId" value="{{ @gs('firebase_config')->projectId }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Storage Bucket') </label>
                                    <input type="text" class="form-control" placeholder="@lang('Storage Bucket')" name="storageBucket" value="{{ @gs('firebase_config')->storageBucket }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Messaging Sender Id') </label>
                                    <input type="text" class="form-control" placeholder="@lang('Messaging Sender Id')" name="messagingSenderId" value="{{ @gs('firebase_config')->messagingSenderId }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('App Id') </label>
                                    <input type="text" class="form-control" placeholder="@lang('App Id')" name="appId" value="{{ @gs('firebase_config')->appId }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>@lang('Measurement Id') </label>
                                    <input type="text" class="form-control" placeholder="@lang('Measurement Id')" name="measurementId" value="{{ @gs('firebase_config')->measurementId }}" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div><!-- card end -->
        </div>
    </div>

    <div id="pushNotifyModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Firebase Setup')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="steps-tab" data-bs-toggle="tab" data-bs-target="#steps" type="button" role="tab" aria-controls="steps" aria-selected="true">@lang('Steps')</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="configs-tab" data-bs-toggle="tab" data-bs-target="#configs" type="button" role="tab" aria-controls="configs" aria-selected="false">@lang('Configs')</button>
                        </li>
                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="steps" role="tabpanel" aria-labelledby="steps-tab">
                            <div class="table-responsive overflow-hidden">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>@lang('To Do')</th>
                                            <th>@lang('Description')</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>@lang('Step 1')</td>
                                            <td>@lang('Go to your Firebase account and select') <span class="text--primary">"@lang('Go to console')</span>" @lang('in the upper-right corner of the page.')</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Step 2')</td>
                                            <td>@lang('Click on the') <span class="text--primary">"@lang('Add Project')</span>" @lang('button.')</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Step 3')</td>
                                            <td>@lang('Enter the project name and click on the') <span class="text--primary">"@lang('Continue')</span>" @lang('button.')</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Step 4')</td>
                                            <td>@lang('Enable Google Analytics and click on the') <span class="text--primary">"@lang('Continue')</span>" @lang('button.')</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Step 5')</td>
                                            <td>@lang('Choose the default account for the Google Analytics account and click on the') <span class="text--primary">"@lang('Create Project')</span>" @lang('button.')</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Step 6')</td>
                                            <td>@lang('Within your Firebase project, select the gear next to Project Overview and choose Project settings.')</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Step 7')</td>
                                            <td>@lang('Next, set up a web app under the General section of your project settings.')</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Step 8')</td>
                                            <td>@lang('Go to the Service accounts tab and generate a new private key.')</td>
                                        </tr>
                                        <tr>
                                            <td>@lang('Step 9')</td>
                                            <td>@lang('A JSON file will be downloaded. Upload the downloaded file here.')</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane fade mt-3 ms-2 text-center" id="configs" role="tabpanel" aria-labelledby="configs-tab">
                            <img src="{{ getImage('assets/images/firebase/' . 'configs.png') }}" alt="Firebase Config">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="pushConfigJson" role="dialog" tabindex="-1">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Upload Push Notification Configuration File')</h5>
                    <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.setting.notification.push.upload') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="mt-2">@lang('File')</label>
                            <input type="file" class="form-control" name="file" accept=".json" required>
                            <small class="mt-3 text-muted">@lang('Supported Files: .json')</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn--primary w-100 h-45" type="submit">@lang('Upload')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <button type="button" data-bs-target="#pushNotifyModal" data-bs-toggle="modal" class="btn btn-sm btn-outline--info">
        <i class="las la-question"></i>@lang('Help')
    </button>
    <button class="btn btn-outline--primary updateBtn btn-sm" data-bs-toggle="modal" data-bs-target="#pushConfigJson" type="button"><i class="las la-upload"></i>@lang('Upload Config File')</button>

    <a href="{{ route('admin.setting.notification.push.download') }}" class="btn btn-outline--info updateBtn btn-sm  @if (!$fileExists) disabled @endif" @disabled(!$fileExists)>
        <i class="las la-download"></i>@lang('Download File')
    </a>
@endpush
