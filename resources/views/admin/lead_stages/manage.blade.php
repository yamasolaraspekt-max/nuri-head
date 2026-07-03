@extends('admin.layouts.app')

@section('title') Lead-Phasen @stop

@section('style')
<style>
    .lsp-wrap { padding: 10px 0 32px; --lsp-green:#93c21c; --lsp-blue:#74b2d4; --lsp-ink:#0f172a; --lsp-muted:#64748b; --lsp-line:rgba(15,23,42,.10); }
    .lsp-head { display:flex; align-items:flex-end; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-bottom:16px; }
    .lsp-head h2 { margin:0; font-size:20px; font-weight:800; color:var(--lsp-ink); }
    .lsp-head p { margin:4px 0 0; font-size:13px; color:var(--lsp-muted); max-width:640px; }
    .lsp-btn { display:inline-flex; align-items:center; gap:8px; border:0; border-radius:10px; padding:10px 16px; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; }
    .lsp-btn-primary { background:var(--lsp-green); color:#fff; }
    .lsp-btn-primary:hover { filter:brightness(.95); }
    .lsp-btn-ghost { background:transparent; color:var(--lsp-ink); border:1px solid var(--lsp-line); }
    .lsp-btn-ghost:hover { background:rgba(15,23,42,.04); }
    .lsp-note { display:flex; gap:10px; align-items:flex-start; background:rgba(116,178,212,.10); border:1px solid rgba(116,178,212,.30); border-radius:12px; padding:12px 14px; font-size:12.5px; color:#0f2f45; margin-bottom:18px; }
    .lsp-note b { font-weight:800; }

    .lsp-card { background:#fff; border:1px solid var(--lsp-line); border-radius:16px; overflow:hidden; box-shadow:0 12px 40px rgba(15,23,42,.06); }
    .lsp-table { width:100%; border-collapse:collapse; }
    .lsp-table th { text-align:left; font-size:11px; letter-spacing:.04em; text-transform:uppercase; color:var(--lsp-muted); font-weight:700; padding:12px 14px; border-bottom:1px solid var(--lsp-line); background:#fbfcfd; }
    .lsp-table td { padding:12px 14px; border-bottom:1px solid var(--lsp-line); font-size:13.5px; color:var(--lsp-ink); vertical-align:middle; }
    .lsp-table tr:last-child td { border-bottom:0; }
    .lsp-table tr.is-inactive td { opacity:.55; }

    .lsp-order { display:flex; flex-direction:column; gap:2px; }
    .lsp-order button { border:1px solid var(--lsp-line); background:#fff; width:24px; height:20px; border-radius:6px; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--lsp-muted); padding:0; }
    .lsp-order button:hover:not(:disabled) { background:rgba(15,23,42,.05); color:var(--lsp-ink); }
    .lsp-order button:disabled { opacity:.3; cursor:default; }

    .lsp-swatch { width:22px; height:22px; border-radius:7px; border:1px solid rgba(15,23,42,.15); display:inline-block; }
    .lsp-name { font-weight:700; }
    .lsp-key { font-family:ui-monospace,SFMono-Regular,Menlo,monospace; font-size:11.5px; color:var(--lsp-muted); background:rgba(15,23,42,.05); padding:2px 7px; border-radius:6px; }

    .lsp-badge { display:inline-block; font-size:10.5px; font-weight:700; padding:2px 8px; border-radius:999px; margin:1px 2px 1px 0; }
    .lsp-badge.def { background:rgba(147,194,28,.15); color:#5a7a0f; }
    .lsp-badge.prot { background:rgba(116,178,212,.18); color:#2a5a75; }
    .lsp-badge.closed { background:rgba(148,163,184,.20); color:#475569; }
    .lsp-badge.inactive { background:rgba(239,68,68,.12); color:#b91c1c; }

    .lsp-usage { font-variant-numeric:tabular-nums; color:var(--lsp-muted); }
    .lsp-actions { display:flex; gap:6px; justify-content:flex-end; }
    .lsp-icon-btn { border:1px solid var(--lsp-line); background:#fff; border-radius:8px; padding:6px 10px; font-size:12px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:5px; color:var(--lsp-ink); }
    .lsp-icon-btn:hover { background:rgba(15,23,42,.04); }
    .lsp-icon-btn.danger { color:#b91c1c; border-color:rgba(239,68,68,.30); }
    .lsp-icon-btn.danger:hover { background:rgba(239,68,68,.06); }

    /* Modal */
    .lsp-modal-overlay { position:fixed; inset:0; background:rgba(15,23,42,.45); display:none; align-items:center; justify-content:center; z-index:1080; padding:16px; }
    .lsp-modal-overlay.open { display:flex; }
    .lsp-modal { background:#fff; border-radius:18px; width:min(520px,100%); box-shadow:0 30px 80px rgba(15,23,42,.30); overflow:hidden; }
    .lsp-modal-head { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--lsp-line); }
    .lsp-modal-head h3 { margin:0; font-size:16px; font-weight:800; color:var(--lsp-ink); }
    .lsp-modal-body { padding:18px 20px; display:flex; flex-direction:column; gap:14px; }
    .lsp-field label { display:block; font-size:12px; font-weight:700; color:var(--lsp-ink); margin-bottom:6px; }
    .lsp-field .hint { font-size:11px; color:var(--lsp-muted); font-weight:400; }
    .lsp-input { width:100%; border:1px solid var(--lsp-line); border-radius:10px; padding:10px 12px; font-size:13.5px; color:var(--lsp-ink); box-sizing:border-box; }
    .lsp-input:focus { outline:none; border-color:var(--lsp-green); box-shadow:0 0 0 3px rgba(147,194,28,.15); }
    .lsp-row { display:flex; gap:12px; }
    .lsp-row > div { flex:1; }
    .lsp-color-wrap { display:flex; gap:8px; align-items:center; }
    .lsp-color-wrap input[type=color] { width:44px; height:40px; border:1px solid var(--lsp-line); border-radius:10px; padding:2px; background:#fff; cursor:pointer; }
    .lsp-icon-preview { display:flex; align-items:center; gap:8px; }
    .lsp-icon-preview .box { width:40px; height:40px; border:1px solid var(--lsp-line); border-radius:10px; display:flex; align-items:center; justify-content:center; color:var(--lsp-ink); }
    .lsp-check { display:flex; align-items:center; gap:9px; font-size:13px; color:var(--lsp-ink); cursor:pointer; }
    .lsp-check input { width:16px; height:16px; accent-color:var(--lsp-green); }
    .lsp-key-readonly { font-family:ui-monospace,Menlo,monospace; font-size:12px; color:var(--lsp-muted); background:rgba(15,23,42,.05); padding:8px 12px; border-radius:10px; }
    .lsp-modal-foot { display:flex; justify-content:flex-end; gap:10px; padding:16px 20px; border-top:1px solid var(--lsp-line); }
    .lsp-modal-err { display:none; background:rgba(239,68,68,.10); color:#b91c1c; border:1px solid rgba(239,68,68,.25); border-radius:10px; padding:9px 12px; font-size:12.5px; }
    .lsp-empty { padding:40px; text-align:center; color:var(--lsp-muted); font-size:14px; }
</style>
@stop

@section('content')
<div class="lsp-wrap">
    <div class="lsp-head">
        <div>
            <h2>Lead-Phasen</h2>
            <p>Die Spalten deines Lead-Kanbans. Hier zentral Reihenfolge, Farbe, Icon und Verhalten der Phasen verwalten.</p>
        </div>
        <button type="button" class="lsp-btn lsp-btn-primary" onclick="lspOpenCreate()">
            <i data-lucide="plus"></i> Neue Phase
        </button>
    </div>

    <div class="lsp-note">
        <i data-lucide="info" style="flex:0 0 auto;margin-top:1px;"></i>
        <div>
            Der technische <b>Schlüssel</b> bleibt beim Umbenennen unverändert — er steckt in allen Datensätzen (<code>lead_product_lists.status</code>).
            <b>Geschützte</b> Standard-Phasen lassen sich bearbeiten; beim Löschen werden vorhandene Einträge automatisch in die <b>vorherige</b> Phase verschoben.
        </div>
    </div>

    <div class="lsp-card">
        <table class="lsp-table">
            <thead>
                <tr>
                    <th style="width:52px;">Reihenf.</th>
                    <th style="width:56px;">Farbe</th>
                    <th>Name</th>
                    <th style="width:150px;">Schlüssel</th>
                    <th style="width:220px;">Status</th>
                    <th style="width:90px;">Einträge</th>
                    <th style="width:180px;"></th>
                </tr>
            </thead>
            <tbody id="lsp-tbody">
                @forelse($stages as $i => $stage)
                    <tr class="{{ $stage['is_active'] ? '' : 'is-inactive' }}" data-id="{{ $stage['id'] }}">
                        <td>
                            <div class="lsp-order">
                                <button type="button" title="Nach oben" onclick="lspMove({{ $stage['id'] }}, -1)" {{ $i === 0 ? 'disabled' : '' }}><i data-lucide="chevron-up" style="width:14px;height:14px;"></i></button>
                                <button type="button" title="Nach unten" onclick="lspMove({{ $stage['id'] }}, 1)" {{ $i === count($stages) - 1 ? 'disabled' : '' }}><i data-lucide="chevron-down" style="width:14px;height:14px;"></i></button>
                            </div>
                        </td>
                        <td><span class="lsp-swatch" style="background:{{ $stage['color'] }};"></span></td>
                        <td>
                            <div style="display:flex;align-items:center;gap:9px;">
                                <i data-lucide="{{ $stage['icon'] }}" style="width:17px;height:17px;color:{{ $stage['color'] }};"></i>
                                <span class="lsp-name">{{ $stage['name'] }}</span>
                            </div>
                        </td>
                        <td><span class="lsp-key">{{ $stage['key'] }}</span></td>
                        <td>
                            @if($stage['is_default'])<span class="lsp-badge def">Standard</span>@endif
                            @if($stage['is_protected'])<span class="lsp-badge prot">Geschützt</span>@endif
                            @if($stage['is_closed'])<span class="lsp-badge closed">Abschließend</span>@endif
                            @if(!$stage['is_active'])<span class="lsp-badge inactive">Inaktiv</span>@endif
                        </td>
                        <td><span class="lsp-usage">{{ $stage['usage_count'] }}</span></td>
                        <td>
                            <div class="lsp-actions">
                                <button type="button" class="lsp-icon-btn" onclick="lspOpenEdit({{ $stage['id'] }})"><i data-lucide="pencil" style="width:13px;height:13px;"></i> Bearbeiten</button>
                                <button type="button" class="lsp-icon-btn danger" onclick="lspDelete({{ $stage['id'] }})"><i data-lucide="trash-2" style="width:13px;height:13px;"></i></button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><div class="lsp-empty">Noch keine Phasen angelegt.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div class="lsp-modal-overlay" id="lsp-modal" onclick="if(event.target===this)lspCloseModal()">
    <div class="lsp-modal">
        <div class="lsp-modal-head">
            <h3 id="lsp-modal-title">Neue Phase</h3>
            <button type="button" class="lsp-btn lsp-btn-ghost" style="padding:6px 10px;" onclick="lspCloseModal()"><i data-lucide="x" style="width:15px;height:15px;"></i></button>
        </div>
        <div class="lsp-modal-body">
            <div class="lsp-modal-err" id="lsp-err"></div>
            <input type="hidden" id="lsp-id">
            <div class="lsp-field">
                <label>Name</label>
                <input type="text" class="lsp-input" id="lsp-name" maxlength="80" placeholder="z. B. Nachfassen" oninput="lspSyncIcon()">
            </div>
            <div id="lsp-key-block" class="lsp-field" style="display:none;">
                <label>Schlüssel <span class="hint">(technisch, unveränderlich)</span></label>
                <div class="lsp-key-readonly" id="lsp-key-view">—</div>
            </div>
            <div class="lsp-row">
                <div class="lsp-field">
                    <label>Farbe</label>
                    <div class="lsp-color-wrap">
                        <input type="color" id="lsp-color" value="#74b2d4" oninput="document.getElementById('lsp-color-hex').value=this.value">
                        <input type="text" class="lsp-input" id="lsp-color-hex" value="#74b2d4" maxlength="30" oninput="document.getElementById('lsp-color').value=this.value">
                    </div>
                </div>
                <div class="lsp-field">
                    <label>Icon <span class="hint">(Lucide-Name)</span></label>
                    <div class="lsp-icon-preview">
                        <span class="box"><i data-lucide="circle" id="lsp-icon-prev" style="width:18px;height:18px;"></i></span>
                        <input type="text" class="lsp-input" id="lsp-icon" value="circle" maxlength="60" placeholder="z. B. flag" oninput="lspSyncIcon()">
                    </div>
                </div>
            </div>
            <label class="lsp-check"><input type="checkbox" id="lsp-active" checked> Aktiv (im Kanban sichtbar)</label>
            <label class="lsp-check"><input type="checkbox" id="lsp-closed"> Abschließende Phase (Archiv / Ende der Pipeline)</label>
        </div>
        <div class="lsp-modal-foot">
            <button type="button" class="lsp-btn lsp-btn-ghost" onclick="lspCloseModal()">Abbrechen</button>
            <button type="button" class="lsp-btn lsp-btn-primary" id="lsp-save" onclick="lspSave()">Speichern</button>
        </div>
    </div>
</div>
@stop

@section('script')
<script>
    const LSP_BASE = @json(url('admin/lead-stages'));
    const LSP_CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const LSP_STAGES = @json($stages);

    function lspRefreshIcons() { if (window.lucide && window.lucide.createIcons) window.lucide.createIcons(); }

    async function lspApi(method, path, body) {
        const res = await fetch(LSP_BASE + path, {
            method,
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': LSP_CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: body ? JSON.stringify(body) : undefined,
        });
        let data = {};
        try { data = await res.json(); } catch (e) {}
        return { status: res.status, ok: res.ok, data };
    }

    function lspSyncIcon() {
        const icon = (document.getElementById('lsp-icon').value || 'circle').trim();
        const prev = document.getElementById('lsp-icon-prev');
        prev.setAttribute('data-lucide', icon || 'circle');
        lspRefreshIcons();
    }

    function lspOpenCreate() {
        document.getElementById('lsp-modal-title').textContent = 'Neue Phase';
        document.getElementById('lsp-id').value = '';
        document.getElementById('lsp-name').value = '';
        document.getElementById('lsp-color').value = '#74b2d4';
        document.getElementById('lsp-color-hex').value = '#74b2d4';
        document.getElementById('lsp-icon').value = 'circle';
        document.getElementById('lsp-active').checked = true;
        document.getElementById('lsp-closed').checked = false;
        document.getElementById('lsp-key-block').style.display = 'none';
        document.getElementById('lsp-err').style.display = 'none';
        lspSyncIcon();
        document.getElementById('lsp-modal').classList.add('open');
    }

    function lspOpenEdit(id) {
        const s = LSP_STAGES.find(x => x.id === id);
        if (!s) return;
        document.getElementById('lsp-modal-title').textContent = 'Phase bearbeiten';
        document.getElementById('lsp-id').value = s.id;
        document.getElementById('lsp-name').value = s.name;
        document.getElementById('lsp-color').value = s.color;
        document.getElementById('lsp-color-hex').value = s.color;
        document.getElementById('lsp-icon').value = s.icon;
        document.getElementById('lsp-active').checked = !!s.is_active;
        document.getElementById('lsp-closed').checked = !!s.is_closed;
        document.getElementById('lsp-key-view').textContent = s.key + (s.is_protected ? '  ·  geschützt' : '');
        document.getElementById('lsp-key-block').style.display = '';
        document.getElementById('lsp-err').style.display = 'none';
        lspSyncIcon();
        document.getElementById('lsp-modal').classList.add('open');
    }

    function lspCloseModal() { document.getElementById('lsp-modal').classList.remove('open'); }

    function lspErr(msg) {
        const el = document.getElementById('lsp-err');
        el.textContent = msg; el.style.display = 'block';
    }

    async function lspSave() {
        const id = document.getElementById('lsp-id').value;
        const name = document.getElementById('lsp-name').value.trim();
        if (!name) { lspErr('Bitte einen Namen eingeben.'); return; }
        const body = {
            name,
            color: document.getElementById('lsp-color-hex').value.trim() || '#74b2d4',
            icon: document.getElementById('lsp-icon').value.trim() || 'circle',
            is_active: document.getElementById('lsp-active').checked,
            is_closed: document.getElementById('lsp-closed').checked,
        };
        const btn = document.getElementById('lsp-save');
        btn.disabled = true;
        const r = id ? await lspApi('PUT', '/' + id, body) : await lspApi('POST', '', body);
        btn.disabled = false;
        if (r.ok && r.data.success) { location.reload(); return; }
        lspErr(r.data.message || 'Speichern fehlgeschlagen.');
    }

    async function lspDelete(id) {
        const s = LSP_STAGES.find(x => x.id === id);
        const label = s ? s.name : 'diese Phase';
        if (!confirm('Phase „' + label + '" löschen?')) return;

        let r = await lspApi('DELETE', '/' + id, {});
        if (r.ok && r.data.success) { location.reload(); return; }

        // Backend verlangt Bestätigung (Umzug in vorherige Phase / geschützt)
        if (r.status === 409 && (r.data.requires_transfer || r.data.requires_protected_confirmation)) {
            if (!confirm((r.data.message || 'Einträge werden in die vorherige Phase verschoben.') + '\n\nWirklich fortfahren?')) return;
            r = await lspApi('DELETE', '/' + id, { move_to_previous: true, force_delete_protected: true });
            if (r.ok && r.data.success) { location.reload(); return; }
        }
        alert(r.data.message || 'Phase konnte nicht gelöscht werden.');
    }

    async function lspMove(id, dir) {
        const ids = LSP_STAGES.map(s => s.id);
        const idx = ids.indexOf(id);
        const swap = idx + dir;
        if (swap < 0 || swap >= ids.length) return;
        [ids[idx], ids[swap]] = [ids[swap], ids[idx]];
        const items = ids.map((sid, i) => ({ id: sid, sort_order: (i + 1) * 10 }));
        const r = await lspApi('POST', '/reorder', { items });
        if (r.ok && r.data.success) { location.reload(); return; }
        alert(r.data.message || 'Sortierung konnte nicht gespeichert werden.');
    }

    document.addEventListener('DOMContentLoaded', lspRefreshIcons);
</script>
@stop
