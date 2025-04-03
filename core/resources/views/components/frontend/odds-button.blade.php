@props(['outcome' => null, 'marketIsLocked' => null])

@aware(['betPlacedIds' => []])

<div class="option-odd-list__item">
    @if ($outcome)
        <button @class([
            'btn btn-sm btn-light text--small border oddBtn ',
            'active' => in_array($outcome->id, $betPlacedIds),
            'locked' => ($outcome->locked || $marketIsLocked) ,
        ]) data-outcome_id="{{ $outcome->id }}" >{{ rateData($outcome->odds) }} </button>
    @else
        <button class="btn btn-light border" disabled> - </button>
    @endif
</div>
