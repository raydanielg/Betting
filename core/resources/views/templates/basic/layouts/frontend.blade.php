@extends($activeTemplate . 'layouts.app')
@section('content')
    <header class="header-primary user-header-primary">
        <div class="container">
            @include($activeTemplate . 'partials.header')
        </div>
    </header>
    @yield('frontend')
    @include($activeTemplate . 'partials.footer')
@endsection
