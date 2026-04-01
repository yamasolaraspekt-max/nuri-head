{{-- resources/views/admin/employee/position/partials/_positions_table.blade.php --}}
<div class="table-responsive">
  <table class="table mb-0 table-hover">
    <thead style="background:rgba(192,216,234,.35)">
      <tr>
        <th data-sort="id" style="cursor:pointer; width:80px;">ID</th>
        <th data-sort="position" style="cursor:pointer;">POSITION</th>
        <th data-sort="qualification" style="cursor:pointer; width:200px;">QUALIFIKATION</th>
        <th data-sort="price" style="cursor:pointer; width:140px;">PREIS</th>
        <th data-sort="status" style="cursor:pointer; width:120px;">STATUS</th>
        <th style="width:320px;">AKTION</th>
      </tr>
    </thead>

    <tbody>
      @forelse($data as $item)
        <tr>
          <td>{{ $item->id }}</td>

          <td>
            <div style="font-weight:900;">{{ $item->position }}</div>
            <div style="color:#64748b; font-size:12px; font-weight:700;">
              {{ \Illuminate\Support\Str::limit(strip_tags((string)$item->description), 80) }}
            </div>
          </td>

          <td>
            @if($item->qualificationRef)
              <span class="badge-pill" style="background:rgba(147,194,28,.16);">
                {{ $item->qualificationRef->name }}
              </span>
            @else
              <span style="color:#64748b; font-weight:700;">-</span>
            @endif
          </td>

          <td style="font-weight:900;">
            {{ $item->price !== null ? number_format($item->price,2,',','.') . ' €' : '-' }}
          </td>

          <td>
            @if($item->status==='Published')
              <span class="badge-pill badge-on">Aktiv</span>
            @else
              <span class="badge-pill badge-off">Inaktiv</span>
            @endif
          </td>

          <td style="white-space:nowrap;">
            <button class="btn btn-sm"
              style="border-radius:10px; background:rgba(116,178,212,.16);"
              data-action="desc"
              data-id="{{ $item->id }}"
              data-position="{{ e($item->position) }}"
              data-desc="{{ e($item->description ?? '') }}">
              Beschreibung
            </button>

            <button class="btn btn-sm"
              style="border-radius:10px; background:rgba(207,224,155,.40);"
              data-action="edit"
              data-id="{{ $item->id }}"
              data-position="{{ e($item->position) }}"
              data-status="{{ $item->status }}"
              data-qid="{{ $item->qualification_id }}"
              data-price="{{ $item->price }}">
              Bearbeiten
            </button>

            <button class="btn btn-sm"
              style="border-radius:10px; background:rgba(147,194,28,.18);"
              data-action="toggle"
              data-id="{{ $item->id }}">
              Toggle
            </button>

            <button class="btn btn-sm"
              style="border-radius:10px; background:rgba(239,68,68,.16);"
              data-action="delete"
              data-id="{{ $item->id }}">
              Löschen
            </button>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6" style="color:#64748b; padding:18px; font-weight:800;">
            Keine Daten gefunden.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

<div class="mt-2">
  {!! $data->links() !!}
</div>