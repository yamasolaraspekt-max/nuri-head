@forelse($content as $item)
  @php
    $statusClass = match($item->status) {
      'Verfügbar' => 'green',
      'Teilweise' => 'orange',
      'Nicht verfügbar' => 'red',
      default => 'blue',
    };

    $employee = $item->handoverEmployee
      ? trim(($item->handoverEmployee->name ?? '').' '.($item->handoverEmployee->lastname ?? ''))
      : '—';

    $customerName = $item->customer
      ? ($item->customer->display_name ?? (($item->customer->firma ?: trim(($item->customer->name ?? '').' '.($item->customer->lastname ?? ''))) ?: '#'.$item->customer->id))
      : '—';

    $objectName = $item->alternative
      ? (($item->alternative->object_name ?: '#'.$item->alternative->id).' · '.trim(($item->alternative->street ?? '').' '.($item->alternative->city ?? '')))
      : '—';

    $dealLabel = $item->deal
      ? ($item->deal->order_number ?: '#'.$item->deal->id)
      : '—';

    $destinationLabel = ($item->destination_type ?? 'customer') === 'warehouse' ? 'Lager' : 'Kunde';
    $destinationClass = ($item->destination_type ?? 'customer') === 'warehouse' ? 'orange' : 'blue';

    $branch = $item->branch->branch ?? '—';
    $progress = (int) ($item->progress ?? 0);
    $children = $item->linkedNotes ?? collect();

    $groupTotal = 1 + $children->count();
    $groupCompleted = (int) (($progress >= 100 ? 1 : 0) + $children->where('progress', '>=', 100)->count());
    $groupOpen = $groupTotal - $groupCompleted;

    $hasChildren = $children->count() > 0;
    $groupId = 'delivery-group-'.$item->id;
  @endphp

  <div class="oc-item">
    <div class="oc-item-row">
      <div class="oc-cell">
        <div class="oc-cell-title">ID</div>
        <span class="oc-id-badge">#{{ $item->id }}</span>
      </div>

      <div class="oc-cell">
        <div class="oc-cell-title">Lieferschein</div>
        <div class="oc-main">
          <a href="{{ route('delivery-notes.profile', $item->id) }}" class="oc-ttl" style="text-decoration:none;color:inherit;">
              {{ $item->delivery_note ?: '—' }}
          </a>
          <div class="oc-subt">{{ $item->delivered_from ?: '—' }}</div>

          <div class="oc-group-summary">
            <span class="oc-group-badge total">{{ $groupTotal }} Lieferscheine</span>
            <span class="oc-group-badge complete">{{ $groupCompleted }} komplett</span>
            <span class="oc-group-badge open">{{ $groupOpen }} offen</span>
          </div>

          @if($hasChildren)
            <div style="margin-top:10px;">
              <button
                type="button"
                class="oc-group-toggle"
                data-collapse-target="{{ $groupId }}"
                aria-expanded="false"
              >
                <svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M9 18l6-6-6-6"/>
                </svg>
                <span data-collapse-label>Gruppe öffnen</span>
              </button>
            </div>
          @endif
        </div>
      </div>

      <div class="oc-cell">
        <div class="oc-cell-title">Bezug</div>
        <div class="oc-meta-stack">
          <span class="oc-status-pill {{ $destinationClass }}" style="width:max-content;">{{ $destinationLabel }}</span>
          <span><strong>Kunde:</strong> {{ $customerName }}</span>
          <span class="oc-muted"><strong>Objekt:</strong> {{ $objectName }}</span>
          <span class="oc-muted"><strong>Auftrag:</strong> {{ $dealLabel }}</span>
          <span class="oc-muted"><strong>Zweig:</strong> {{ $branch }}</span>
        </div>
      </div>

      <div class="oc-cell">
        <div class="oc-cell-title">Übergabe</div>
        <div class="oc-meta-stack">
          <span>{{ $employee }}</span>
          <span class="oc-muted">{{ $item->handover_date ?: '—' }}</span>
          <span class="oc-muted">{{ $item->order_no ?: 'Keine Bestellnummer' }}</span>
        </div>
      </div>

      <div class="oc-cell">
        <div class="oc-cell-title">Status</div>
        <span class="oc-status-pill {{ $statusClass }}">{{ $item->status ?: '—' }}</span>
      </div>

      <div class="oc-cell">
        <div class="oc-cell-title">Fortschritt</div>
        <div class="oc-main">
          <div class="oc-ttl" style="font-size:14px;">{{ $progress }}%</div>
          <div class="oc-progress">
            <div class="oc-progress-bar" style="width: {{ max(0, min(100, $progress)) }}%;"></div>
          </div>
        </div>
      </div>

      <div class="oc-cell">
        <div class="oc-cell-title">Aktionen</div>
        <div class="oc-actions">
          <button type="button" class="oc-btn-ic primary" data-action="edit" data-id="{{ $item->id }}" title="Bearbeiten">
            ✎
          </button>

          <button type="button" class="oc-btn-ic warning" data-action="progress" data-id="{{ $item->id }}" data-progress="{{ $progress }}" title="Fortschritt">
            %
          </button>

          <button type="button" class="oc-btn-ic success" data-action="pdf" data-id="{{ $item->id }}" title="PDF">
            PDF
          </button>

          <button type="button" class="oc-btn-ic success" data-action="toggle-status" data-id="{{ $item->id }}" title="Status ändern">
            ✓
          </button>

          <a href="{{ route('delivery-notes.images.index', $item->id) }}" class="oc-btn-ic primary" title="Bilder">
            IMG
          </a>

          <button type="button" class="oc-btn-ic danger" data-action="delete" data-id="{{ $item->id }}" data-label="{{ $item->delivery_note }}" title="Löschen">
            ×
          </button>
        </div>
      </div>
    </div>

    @if($hasChildren)
      <div class="oc-child-wrap" id="{{ $groupId }}">
        @foreach($children as $child)
          @include('admin.product.delivery.partials.table-child', ['child' => $child])
        @endforeach
      </div>
    @endif
  </div>
@empty
  <div class="oc-empty">Keine Datensätze gefunden.</div>
@endforelse