@php
    $empId   = $item['employee_id'] ?? null;
    $text    = $item['text'] ?? '';
    $created = $item['created_at'] ?? null;

    // Employee model injected from parent
    $emp = $employee ?? null;

    // Display name
    $commentAuthorName = $emp
        ? trim(($emp->lastname ?? '') . ' ' . ($emp->name ?? ''))
        : 'Mitarbeiter #' . $empId;

    // Avatar with fallback
    $commentAvatar = ($emp && !empty($emp->image))
        ? asset('images/employee/' . $emp->image)
        : asset('images/gender/male.png');
@endphp

<div class="ap-report-comment">
    <div class="ap-report-comment-avatar">
        <img
            src="{{ $commentAvatar }}"
            alt="{{ $commentAuthorName }}"
        >
    </div>

    <div class="ap-report-comment-body">
        <div class="ap-report-comment-meta">
            <span class="ap-report-comment-author">
                {{ $commentAuthorName }}
            </span>

            @if($created)
                <span class="ap-report-comment-date">
                    {{ \Illuminate\Support\Carbon::parse($created)->format('d.m.Y H:i') }}
                </span>
            @endif
        </div>

        <div class="ap-report-comment-text">
            {{ $text }}
        </div>
    </div>
</div>
