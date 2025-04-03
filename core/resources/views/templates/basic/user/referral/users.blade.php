@extends($activeTemplate . 'layouts.master')
@section('master')
@if (gs('referral_program'))
    <div class="mb-4">
        <div class="qr-code text--base mb-1 mt-3">
            <div class="qr-code-copy-form" data-copy=true>
                <input id="qr-code-text" type="text" value="{{ route('home') }}?reference={{ $user->referral_code }}" readonly>
                <button class="text-copy-btn copy-btn lh-1 text-white" data-bs-toggle="tooltip" data-bs-original-title="@lang('Copy to clipboard')" type="button">@lang('Copy</')</button>
            </div>
        </div>

        <small class="lh-1 text-muted"><i class="la la-info-circle"></i> @lang('Earn referral bonus by inviting your friends to join our platform! Simply share your referral link with them to get started.')</small>
    </div>
@endif
    <div class="card custom--card">


        <div class="row">
            <div class="col-xl-12">
                @if ($user->refBy)
                    <div class="d-flex justify-content-center flex-wrap">
                        <h5>
                            <span class="mb-2">@lang('You are referred by')</span>
                            <span class="text--base">{{ $user->refBy->username }}</span>
                        </h5>
                    </div>
                @endif

                <div class="treeview-container @if (!$user->refBy) mt-3 @endif">
                    <ul class="treeview">
                        @if ($user->allReferrals->count() > 0 && $maxLevel > 0)
                            <li class="items-expanded"> {{ $user->username }}
                                @include($activeTemplate . 'partials.under_tree', ['user' => $user, 'layer' => 0, 'isFirst' => true])
                            </li>
                        @else
                            <div class="text-center">
                                <i class="text-muted fal fa-user-alt-slash fa-3x"></i><br>
                                <p class="text-muted">@lang('No referred user found')</p>
                            </div>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('style-lib')
    <link href="{{ asset($activeTemplateTrue . 'css/treeView.css') }}" rel="stylesheet">
@endpush

@push('script-lib')
    <script src="{{ asset($activeTemplateTrue . 'js/treeView.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.treeview').treeView();
        })(jQuery);
    </script>
@endpush
