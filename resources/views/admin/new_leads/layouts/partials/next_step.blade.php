@php
    $prev = $activities->get($currentIndex - 1);
    $curr = $activities->get($currentIndex);
    $next = $activities->get($currentIndex + 1);
@endphp

@if ($curr)
    <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded shadow-sm" style="min-height: 40px;">
        {{-- Left Arrow --}}
        @if ($prev)
            <button class="btn btn-sm btn-outline-secondary" onclick="loadActivityCarousel({{ $prev->phase_id }}, {{ $prev->id }}, {{ $productId }})">
                <i class="feather icon-chevron-left"></i>
            </button>
        @else
            <button class="btn btn-sm btn-outline-secondary" disabled>
                <i class="feather icon-chevron-left text-muted"></i>
            </button>
        @endif

        {{-- Center Title --}}
        <span class="mx-3 font-weight-bold text-dark">
            {{ $curr->title }}
        </span>

        {{-- Right Arrow --}}
        @if ($next)
            <button class="btn btn-sm btn-outline-secondary" onclick="loadActivityCarousel({{ $next->phase_id }}, {{ $next->id }}, {{ $productId }})">
                <i class="feather icon-chevron-right"></i>
            </button>
        @else
            <button class="btn btn-sm btn-outline-secondary" disabled>
                <i class="feather icon-chevron-right text-muted"></i>
            </button>
        @endif
    </div>
@else
    <div class="alert alert-warning py-2 px-3 mt-1 small mb-0">
        ❌ Aktuelle Aktivität nicht gefunden.
    </div>
@endif
