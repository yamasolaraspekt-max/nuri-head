@php
    /** @var array $stats */
    $myCount        = $stats['my_count']        ?? 0;
    $otherCount     = $stats['other_count']     ?? 0;
    $myItemsSum     = $stats['my_items_sum']    ?? 0;
    $otherItemsSum  = $stats['other_items_sum'] ?? 0;
@endphp

<div class="row no-gutters fav-stats-row">
    <div class="col-md-3 col-6 mb-50 pr-md-25">
        <div class="fav-stat-card mine">
            <div class="fav-stat-label">Meine Listen</div>
            <div class="fav-stat-value">{{ $myCount }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-50 pr-md-25">
        <div class="fav-stat-card mine">
            <div class="fav-stat-label">Produkte in meinen Listen</div>
            <div class="fav-stat-value">{{ $myItemsSum }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-50 pr-md-25">
        <div class="fav-stat-card shared">
            <div class="fav-stat-label">Geteilte Listen</div>
            <div class="fav-stat-value">{{ $otherCount }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-50">
        <div class="fav-stat-card shared">
            <div class="fav-stat-label">Produkte in geteilten Listen</div>
            <div class="fav-stat-value">{{ $otherItemsSum }}</div>
        </div>
    </div>
</div>

{{-- Hidden counters for JS if needed --}}
<span id="fav-my-count"    data-count="{{ $myCount }}"    hidden></span>
<span id="fav-other-count" data-count="{{ $otherCount }}" hidden></span>
