@if($content->hasPages())
  <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
    <div style="font-size:12px;color:#6b7280;">
      Zeige <strong>{{ $content->firstItem() ?? 0 }}</strong>
      bis <strong>{{ $content->lastItem() ?? 0 }}</strong>
      von <strong>{{ $content->total() }}</strong> Einträgen
    </div>
    <div>
      {{ $content->onEachSide(1)->links('pagination::bootstrap-4') }}
    </div>
  </div>
@else
  <div style="font-size:12px;color:#6b7280;">
    {{ $content->total() }} Einträge
  </div>
@endif