@php
    use Illuminate\Support\Str;

    $employee = $employee ?? null;
    $sizeClass = $sizeClass ?? 'gt-mini-avatar';
    $imageClass = $imageClass ?? 'gt-mini-avatar-img';
    $fullName = $employee ? trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')) : '';
    $initials = $employee
        ? Str::upper(Str::substr($employee->name ?? 'M', 0, 1) . Str::substr($employee->lastname ?? '', 0, 1))
        : '?';
@endphp

@if($employee && !empty($employee->image))
    <img class="{{ $imageClass }}" src="{{ asset('images/employee/' . $employee->image) }}" alt="{{ $fullName }}" title="{{ $fullName }}">
@else
    <span class="{{ $sizeClass }}" title="{{ $fullName }}">{{ $initials }}</span>
@endif
