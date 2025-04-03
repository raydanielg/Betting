@props([
    'placeholder' => 'Search...',
    'btn' => 'btn--primary',
    'dateSearch' => 'no',
    'keySearch' => 'yes',
])

<form class="d-flex flex-wrap gap-2">
    @if ($keySearch == 'yes')
        <x-search-key-field placeholder="{{ $placeholder }}" btn="{{ $btn }}" />
    @endif
    @if ($dateSearch == 'yes')
        <x-search-date-field  />
    @endif
</form>
