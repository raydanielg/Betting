@extends($activeTemplate . 'layouts.frontend')
@section('frontend')
    @php
        $contactContent = getContent('contact.content', true);
        $contactElements = getContent('contact.element', false, null, true);
    @endphp

    <div class="section contact-section" style="background-image: url({{ frontendImage('contact', @$contactContent->data_values->background_image, '1600x1100') }});">
        <div class="container">
            <div class="row g-3 align-items-lg-center justify-content-lg-between">
                <div class="col-lg-5">
                    <ul class="list">
                        @foreach ($contactElements as $contact)
                            <li>
                                <div class="contact-card">
                                    <span class="contact-card__icon">
                                        @php echo @$contact->data_values->icon @endphp
                                    </span>
                                    <div class="contact-card__content">
                                        <h5 class="contact-card__title">{{ __(@$contact->data_values->heading) }}</h5>
                                        <p class="contact-card__text">
                                            {{ __(@$contact->data_values->details) }}
                                        </p>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="col-lg-6">
                    <div class="login-form">
                        <form class="verify-gcaptcha" method="POST">
                            @csrf
                            <h4 class="login-form__title">{{ __(@$contactContent->data_values->heading) }} </h4>
                            <div class="row">
                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Name')</label>
                                        <input class="form-control form--control mb-3" name="name" type="text" value="{{ old('name', @$user->fullname) }}" @if ($user) readonly @endif required>
                                    </div>
                                </div>

                                <div class="col-12 col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">@lang('Email')</label>
                                        <input class="form-control form--control mb-3" name="email" type="email" value="{{ old('email', @$user->email) }}" @if ($user) readonly @endif required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Subject')</label>
                                <input class="form-control form--control mb-3" name="subject" type="text" value="{{ old('subject') }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">@lang('Message')</label>
                                <textarea class="form-control form--control" name="message" cols="30" rows="5" required>{{ old('message') }}</textarea>
                            </div>
                            <x-captcha />
                            <button class="btn btn--xl btn--base w-100" type="submit">@lang('Send Message')</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
