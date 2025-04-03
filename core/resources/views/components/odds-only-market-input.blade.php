@props(['label', 'name', 'index', 'value'=> ''])

<div class="form-group flex-grow-1">
    <div class="input-group-wrapper">
        <div class="input-group">
            <span class="input-group-text">@lang('Odds')</span>
            <input type="hidden" name='outcomes[{{ @$index}}][name]' value='{{@$name}}'>
            <input type="number" name='outcomes[{{ @$index}}][odds]' step="any" value="{{@$value}}" class="form-control" placeholder="0.00" required>
        </div>
    </div>
</div>
