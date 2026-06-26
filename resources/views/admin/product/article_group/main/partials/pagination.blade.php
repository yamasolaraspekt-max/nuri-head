@if(method_exists($data, 'links') && $data->hasPages())
    <div class="oc-pagination">
        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
            <div style="font-size:12px;color:#6b7280;">
                Zeige <strong>{{ $data->firstItem() ?? 0 }}</strong>
                bis <strong>{{ $data->lastItem() ?? 0 }}</strong>
                von <strong>{{ $data->total() }}</strong> Einträgen
            </div>
            <div>
                {{ $data->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>
@endif