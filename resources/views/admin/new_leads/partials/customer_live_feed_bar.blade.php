<div class="customer-live-feed"
     data-feed-root
     data-customer-id="{{ $item->id }}"
     data-feed-limit="10"
     data-customer-title="{{ $item->title }} {{ $item->name }} {{ $item->lastname }}">

    <!-- Left: Icon / type indicator -->
    <div class="live-feed-icon">
        <span class="live-feed-icon-pill">
            <i class="feather icon-activity"></i>
        </span>
    </div>

    <!-- Middle: Content -->
    <div class="live-feed-main">

        <!-- Active line -->
        <div class="live-feed-line" data-feed-line>
            <div class="live-feed-header">
                <div class="live-feed-title" data-feed-title>Aktivität</div>
                <div class="live-feed-time" data-feed-time>–</div>
            </div>

            <div class="live-feed-text" data-feed-text></div>

            <!-- Employees (appointments / tasks) -->
            <div class="live-feed-employees" data-feed-employees></div>

            <div class="live-feed-meta">
                <span class="live-feed-pill badge badge-light" data-feed-pill>Info</span>
                <span class="live-feed-counter text-muted" data-feed-counter></span>
            </div>
        </div>

        <!-- Empty state -->
        <div class="live-feed-empty" data-feed-empty>
            <strong>Keine Aktivitäten</strong><br>
            <span class="text-muted">Noch keine Produkte, Termine oder Aufgaben.</span>
        </div>

        <!-- Error message -->
        <div class="live-feed-error text-danger small d-none" data-feed-error></div>
    </div>

    <!-- Right: Controls -->
    <div class="live-feed-controls">
        <button type="button"
                class="live-feed-btn"
                title="Zurück"
                data-feed-prev>
            <i class="feather icon-skip-back"></i>
        </button>

        <button type="button"
                class="live-feed-btn"
                title="Pause / Abspielen"
                data-feed-toggle>
            <i class="feather icon-pause" data-feed-icon-pause></i>
            <i class="feather icon-play d-none" data-feed-icon-play></i>
        </button>

        <button type="button"
                class="live-feed-btn"
                title="Weiter"
                data-feed-next>
            <i class="feather icon-skip-forward"></i>
        </button>

        <!-- Expand to modal -->
        <button type="button"
                class="live-feed-btn"
                title="Liste öffnen"
                data-feed-expand>
            <i class="feather icon-maximize-2"></i>
        </button>
    </div>

</div>
