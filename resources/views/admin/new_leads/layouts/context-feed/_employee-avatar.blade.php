@php
    $employee = $employee ?? null;
    $size = $size ?? 30;

    $image = $employee?->image ?? null;
    $name = trim(($employee?->name ?? '') . ' ' . ($employee?->lastname ?? ''));

    $avatarUrl = $image
        ? asset('images/employee/' . $image)
        : asset('images/employee/default.png');

    $initials = collect(explode(' ', $name))
        ->filter()
        ->map(fn($part) => mb_substr($part, 0, 1))
        ->take(2)
        ->implode('');

    $initials = $initials ?: '?';
@endphp

@if($image)
    <img src="{{ $avatarUrl }}" alt="{{ $name ?: 'Mitarbeiter' }}" class="rounded-circle ma-feed-avatar" width="{{ $size }}"
        height="{{ $size }}" style="object-fit:cover;">
@else
    <span class="rounded-circle ma-feed-avatar-fallback" style="width:{{ $size }}px;height:{{ $size }}px;"
        title="{{ $name ?: 'Mitarbeiter' }}">
        {{ $initials }}
    </span>
@endif