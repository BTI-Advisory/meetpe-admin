@php
    $routeRecord = request()->route('record');
    $experienceId = is_object($routeRecord) ? $routeRecord->getKey() : (int) $routeRecord;
@endphp
<div>
    @livewire('experience-calendar', ['experienceId' => $experienceId], key('cal-' . $experienceId))
</div>
