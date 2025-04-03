<div class="modal fade" id="formGenerateModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">@lang('Generate Form')</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
              <i class="las la-times"></i>
          </button>
        </div>
        <form class="{{ $formClassName ?? 'generate-form' }}">
            @csrf
              <div class="modal-body">
                <input type="hidden" name="update_id" value="">
                <div class="form-group">
                    <label>@lang('Type')</label>
                    <select name="form_type" class="form-control select2" data-minimum-results-for-search="-1" required>
                        <option value="">@lang('Select One')</option>
                        <option value="text">@lang('Text')</option>
                        <option value="email">@lang('Email')</option>
                        <option value="number">@lang('Number')</option>
                        <option value="url">@lang('URL')</option>
                        <option value="datetime">@lang('Date & Time')</option>
                        <option value="date">@lang('Date')</option>
                        <option value="time">@lang('Time')</option>
                        <option value="textarea">@lang('Textarea')</option>
                        <option value="select">@lang('Select')</option>
                        <option value="checkbox">@lang('Checkbox')</option>
                        <option value="radio">@lang('Radio')</option>
                        <option value="file">@lang('File')</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>@lang('Is Required')</label>
                    <select name="is_required" class="form-control select2" data-minimum-results-for-search="-1" required>
                        <option value="">@lang('Select One')</option>
                        <option value="required">@lang('Required')</option>
                        <option value="optional">@lang('Optional')</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>@lang('Label')</label>
                    <input type="text" name="form_label" class="form-control" required>
                </div>
                <div class="form-group extra_area">

                </div>
                <div class="form-group">
                    <label>@lang('Width')</label>
                    <select name="form_width" class="form-control select2" data-minimum-results-for-search="-1" required>
                        <option value="">@lang('Select One')</option>
                        <option value="12">@lang('100%')</option>
                        <option value="6">@lang('50%')</option>
                        <option value="4">@lang('33%')</option>
                        <option value="3">@lang('25%')</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>@lang('Instruction') <small>@lang('(if any)')</small></label>
                    <input type="text" name="instruction" class="form-control">
                </div>
              </div>
              <div class="modal-footer">
                  <button type="submit" class="btn btn--primary w-100 h-45 generatorSubmit">@lang('Add')</button>
              </div>
          </form>
      </div>
    </div>
</div>


@push('script-lib')
<script src="{{ asset('assets/global/js/form_generator.js') }}"></script>
@endpush
