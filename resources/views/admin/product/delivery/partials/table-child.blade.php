@php
  $statusClass = match($child->status) {
    'Verfügbar' => 'green',
    'Teilweise' => 'orange',
    'Nicht verfügbar' => 'red',
    default => 'blue',
  };

  $employee = $child->handoverEmployee
    ? trim(($child->handoverEmployee->name ?? '').' '.($child->handoverEmployee->lastname ?? ''))
    : '—';

  $customerName = $child->customer
    ? ($child->customer->display_name ?? (($child->customer->firma ?: trim(($child->customer->name ?? '').' '.($child->customer->lastname ?? ''))) ?: '#'.$child->customer->id))
    : '—';

  $objectName = $child->alternative
    ? (($child->alternative->object_name ?: '#'.$child->alternative->id).' · '.trim(($child->alternative->street ?? '').' '.($child->alternative->city ?? '')))
    : '—';

  $dealLabel = $child->deal
    ? ($child->deal->order_number ?: '#'.$child->deal->id)
    : '—';

  $destinationLabel = ($child->destination_type ?? 'customer') === 'warehouse' ? 'Lager' : 'Kunde';
  $destinationClass = ($child->destination_type ?? 'customer') === 'warehouse' ? 'orange' : 'blue';

  $branch = $child->branch->branch ?? '—';
  $progress = (int) ($child->progress ?? 0);
@endphp

<div class="oc-child-item">
  <div class="oc-cell">
    <div class="oc-cell-title">ID</div>
    <span class="oc-id-badge">#{{ $child->id }}</span>
  </div>

  <div class="oc-cell">
    <div class="oc-cell-title">Lieferschein</div>
    <div class="oc-main">
      <div class="oc-child-label">
        <span class="oc-child-dot"></span>
        Verlinkter Lieferschein
      </div>
      <a href="{{ route('delivery-notes.profile', $child->id) }}" class="oc-ttl" style="margin-top:6px;text-decoration:none;color:inherit;">
          {{ $child->delivery_note ?: '—' }}
      </a>
      <div class="oc-subt">{{ $child->delivered_from ?: '—' }}</div>
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
      <span class="oc-muted">{{ $child->handover_date ?: '—' }}</span>
      <span class="oc-muted">{{ $child->order_no ?: 'Keine Bestellnummer' }}</span>
    </div>
  </div>

  <div class="oc-cell">
    <div class="oc-cell-title">Status</div>
    <span class="oc-status-pill {{ $statusClass }}">{{ $child->status ?: '—' }}</span>
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
      <button type="button" class="oc-btn-ic primary" data-action="edit" data-id="{{ $child->id }}" title="Bearbeiten">✎</button>
      <button type="button" class="oc-btn-ic warning" data-action="progress" data-id="{{ $child->id }}" data-progress="{{ $progress }}" title="Fortschritt">%</button>
      <button type="button" class="oc-btn-ic success" data-action="pdf" data-id="{{ $child->id }}" title="PDF">PDF</button>
      <button type="button" class="oc-btn-ic success" data-action="toggle-status" data-id="{{ $child->id }}" title="Status ändern">✓</button>

      <a href="{{ route('delivery-notes.images.index', $child->id) }}" class="oc-btn-ic primary" title="Bilder">IMG</a>

      <a href="{{ route('delivery-notes.profile', $item->id) }}" class="oc-btn-ic success" title="Profil">
          <i class="feather icon-eye"></i>
      </a>
      <button type="button" class="oc-btn-ic danger" data-action="delete" data-id="{{ $child->id }}" data-label="{{ $child->delivery_note }}" title="Löschen">×</button>
    </div>
  </div>
</div>