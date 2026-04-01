@php
    $prev = $activities->get($currentIndex - 1);
    $curr = $activities->get($currentIndex);
    $next = $activities->get($currentIndex + 1);
@endphp

@if ($curr)
    <div class="d-flex align-items-center justify-content-center">
        @if ($prev)
            <button class="btn btn-icon btn-flat-primary mr-1 mb-1 waves-effect waves-lightml-2 mr-2"
                    onclick="loadActivityCarousel({{ $prev->phase_id }}, {{ $prev->id }}, {{ $productId }})"><i class="feather icon-chevron-left"></i></button>
        @else
            <button class="btn btn-icon btn-flat-primary mr-1 mb-1 waves-effect waves-lightml-2 mr-2" disabled><i class="feather icon-chevron-left"></i></button>
        @endif

        <span class="mx-2">{{ $curr->title }}</span>

        @if ($next)
            <button class="btn btn-icon btn-flat-primary mr-1 mb-1 waves-effect waves-lightml-2 ml-2"
                    onclick="loadActivityCarousel({{ $next->phase_id }}, {{ $next->id }}, {{ $productId }})"><i class="feather icon-chevron-right"></i></button>
        @else
            <button class="btn btn-icon btn-flat-primary mr-1 mb-1 waves-effect waves-lightml-2" disabled><i class="feather icon-chevron-right"></i></button>
         @endif
    </div>
@else
    <div class="text-danger">❌ Aktivität nicht gefunden.</div>
@endif
