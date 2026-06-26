@php
    $assignees = collect($assignees ?? []);
    $limit = $limit ?? 5;
@endphp

<div class="gt-mini-users">
    @forelse($assignees->take($limit) as $employee)
        @php
            $sizeClass = 'gt-mini-avatar';
            $imageClass = 'gt-mini-avatar-img';
        @endphp
        @include('admin.general_tasks.partials.employee-avatar')
    @empty
        <span class="gt-person-meta">Keine Zuweisung</span>
    @endforelse

    @if($assignees->count() > $limit)
        <span class="gt-more-avatar">+{{ $assignees->count() - $limit }}</span>
    @endif
</div>
