@props(['team'])
<div class="sports-card__team">
    <span class="sports-card__team-flag">
        <img class="sports-card__team-flag-img" src="{{ $team->teamImage() }}" alt="image">
    </span>
    <span class="sports-card__team-name">
        {{ __($team->short_name) }}
    </span>
</div>
