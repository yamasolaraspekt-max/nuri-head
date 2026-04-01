<div class="stamp-stat-card">
    <div class="stamp-stat-icon">
        <i class="feather icon-folder"></i>
    </div>
    <div class="stamp-stat-text">
        <small>Eigene Ordner</small>
        <strong>{{ $stats['my_count'] }}</strong>
    </div>
</div>

<div class="stamp-stat-card">
    <div class="stamp-stat-icon">
        <i class="feather icon-users"></i>
    </div>
    <div class="stamp-stat-text">
        <small>Freigegebene Ordner</small>
        <strong>{{ $stats['other_count'] }}</strong>
    </div>
</div>

<div class="stamp-stat-card">
    <div class="stamp-stat-icon">
        <i class="feather icon-layers"></i>
    </div>
    <div class="stamp-stat-text">
        <small>Stempel in eigenen Ordnern</small>
        <strong>{{ $stats['my_items_sum'] }}</strong>
    </div>
</div>

<div class="stamp-stat-card">
    <div class="stamp-stat-icon">
        <i class="feather icon-eye"></i>
    </div>
    <div class="stamp-stat-text">
        <small>Stempel in freigegebenen Ordnern</small>
        <strong>{{ $stats['other_items_sum'] }}</strong>
    </div>
</div>
