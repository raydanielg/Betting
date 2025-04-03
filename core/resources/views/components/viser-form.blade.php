<div class="row">
    @foreach($formData as $data)
        <div class="col-md-{{ @$data->width ?? '12' }}">
            <div class="form-group">
                <label class="form-label">{{ __($data->name) }} @if(@$data->instruction) <span data-bs-toggle="tooltip" data-bs-title="{{ __($data->instruction) }}"><i class="fas fa-info-circle"></i></span> @endif @if($data->is_required == 'required' && ($data->type == 'checkbox' || $data->type == 'radio')) <span class="text--danger">*</span> @endif </label>
                @if($data->type == 'text')
                    <input type="text"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    value="{{ old($data->label) }}"
                    @if($data->is_required == 'required') required @endif
                    >
                @elseif($data->type == 'url')
                    <input type="url"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    value="{{ old($data->label) }}"
                    @if($data->is_required == 'required') required @endif
                    >
                @elseif($data->type == 'email')
                    <input type="email"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    value="{{ old($data->label) }}"
                    @if($data->is_required == 'required') required @endif
                    >
                @elseif($data->type == 'datetime')
                    <input type="datetime-local"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    value="{{ old($data->label) }}"
                    @if($data->is_required == 'required') required @endif
                    >
                @elseif($data->type == 'date')
                    <input type="date"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    value="{{ old($data->label) }}"
                    @if($data->is_required == 'required') required @endif
                    >
                @elseif($data->type == 'time')
                    <input type="time"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    value="{{ old($data->label) }}"
                    @if($data->is_required == 'required') required @endif
                    >
                @elseif($data->type == 'number')
                    <input type="number"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    value="{{ old($data->label) }}"
                    step="any"
                    @if($data->is_required == 'required') required @endif
                    >
                @elseif($data->type == 'textarea')
                    <textarea
                        class="form-control form--control"
                        name="{{ $data->label }}"
                        @if($data->is_required == 'required') required @endif
                    >{{ old($data->label) }}</textarea>
                @elseif($data->type == 'select')
                    <select
                        class="form-select form--control select2" data-minimum-results-for-search="-1"
                        name="{{ $data->label }}"
                        @if($data->is_required == 'required') required @endif
                    >
                        <option value="">@lang('Select One')</option>
                        @foreach ($data->options as $item)
                            <option value="{{ $item }}" @selected($item == old($data->label))>{{ __($item) }}</option>
                        @endforeach
                    </select>
                @elseif($data->type == 'checkbox')
                    <div class="d-flex gap-3 flex-wrap">
                        @foreach($data->options as $option)
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    name="{{ $data->label }}[]"
                                    type="checkbox"
                                    value="{{ $option }}"
                                    id="{{ $data->label }}_{{ titleToKey($option) }}"
                                >
                                <label class="form-check-label" for="{{ $data->label }}_{{ titleToKey($option) }}">{{ $option }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="checkbox-required-error text--danger"></div>
                @elseif($data->type == 'radio')
                    <div class="d-flex gap-3 flex-wrap">
                        @foreach($data->options as $option)
                            <div class="form-check">
                                <input
                                class="form-check-input"
                                name="{{ $data->label }}"
                                type="radio"
                                value="{{ $option }}"
                                id="{{ $data->label }}_{{ titleToKey($option) }}"
                                @checked($option == old($data->label))
                                >
                                <label class="form-check-label" for="{{ $data->label }}_{{ titleToKey($option) }}">{{ $option }}</label>
                            </div>
                        @endforeach
                    </div>
                @elseif($data->type == 'file')
                    <input
                    type="file"
                    class="form-control form--control"
                    name="{{ $data->label }}"
                    @if($data->is_required == 'required') required @endif
                    accept="@foreach(explode(',',$data->extensions) as $ext) .{{ $ext }}, @endforeach"
                    >
                    <pre class="text--base mt-1">@lang('Supported mimes'): {{ $data->extensions }}</pre>
                @endif
            </div>
        </div>
    @endforeach
</div>
@push('script')
    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
@endpush
