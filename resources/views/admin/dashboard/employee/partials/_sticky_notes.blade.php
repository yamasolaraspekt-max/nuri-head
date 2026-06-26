<div class="sticky-notes-wrapper" id="personalStickyNotesRoot">
    <div class="sn-toolbar">
        <div class="tabs" id="sn-tabs">
            <button class="tab-btn active" type="button" data-status="open">
                <i data-lucide="sticky-note"></i> Aktiv
            </button>
            <button class="tab-btn" type="button" data-status="done">
                <i data-lucide="archive"></i> Archiv
            </button>
        </div>

        <button class="btn btn-primary sn-add-btn" type="button" id="sn-open-create">
            <i data-lucide="plus"></i> Neu
        </button>
    </div>

    <label class="sn-search-wrap">
        <i data-lucide="search"></i>
        <input type="search" id="sn-search" placeholder="Notizen suchen..." autocomplete="off">
    </label>

    <div class="sn-grid" id="sn-grid">
        <div class="empty-state">Lädt...</div>
    </div>
</div>

<div class="overlay" id="noteModalOverlay" aria-hidden="true">
    <div class="sn-modal" role="dialog" aria-modal="true" aria-labelledby="noteModalTitle">
        <div class="panel-header">
            <div>
                <div class="panel-title" id="noteModalTitle">Neue Notiz</div>
                <div class="panel-subtitle" id="noteModalSubtitle">Direkt speichern und später bearbeiten</div>
            </div>
            <button class="close-btn" type="button" id="sn-close-modal" aria-label="Schließen">
                <i data-lucide="x"></i>
            </button>
        </div>

        <form id="addNoteForm" autocomplete="off">
            <input type="hidden" id="noteId" value="">

            <div class="form-group">
                <label for="noteTitle">Titel</label>
                <input type="text" id="noteTitle" class="form-control" placeholder="Titel der Notiz" required>
            </div>

            <div class="form-group">
                <label for="noteContent">Inhalt</label>
                <textarea id="noteContent" class="notes-area" placeholder="Inhalt..." required></textarea>
            </div>

            <div class="form-row">
                <div class="form-group" style="flex: 1;">
                    <label for="noteCategory">Kategorie</label>
                    <select id="noteCategory" class="form-control select2" style="width: 100%;">
                        <option value="">Keine Kategorie</option>
                    </select>
                </div>
                <div class="form-group" style="width: 92px;">
                    <label for="noteColor">Farbe</label>
                    <input type="color" id="noteColor" class="form-control" value="#fef9c3">
                </div>
            </div>

            <div class="sn-modal-actions">
                <button type="button" class="btn" id="sn-cancel-modal">Abbrechen</button>
                <button type="submit" class="btn btn-primary" id="sn-save-btn">
                    <i data-lucide="save"></i> Speichern
                </button>
            </div>
        </form>
    </div>
</div>

@push('styles')
    <style>
        .sticky-notes-wrapper {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 0;
            gap: 1rem;
        }

        .sn-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
        }

        .sn-search-wrap {
            position: relative;
            display: block;
        }

        .sn-search-wrap i {
            position: absolute;
            left: .85rem;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: var(--color-text-muted, #6b7280);
            pointer-events: none;
        }

        .sn-search-wrap input {
            width: 100%;
            height: 40px;
            border-radius: var(--radius-md, .9rem);
            border: 1px solid var(--color-border, #e5e7eb);
            background: var(--color-surface, #fff);
            padding: 0 .85rem 0 2.35rem;
            font-weight: 700;
            outline: none;
        }

        .sn-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 1rem;
            overflow-y: auto;
            padding-right: 5px;
            flex: 1;
            min-height: 0;
        }

        .sn-card {
            border-radius: var(--radius-md, .9rem);
            padding: 1rem;
            box-shadow: var(--shadow-sm, 0 4px 12px rgba(15, 23, 42, .06));
            display: flex;
            flex-direction: column;
            gap: .65rem;
            transition: transform .2s ease, box-shadow .2s ease;
            border: 1px solid rgba(0, 0, 0, .06);
            position: relative;
            min-height: 155px;
            overflow: hidden;
        }

        .sn-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md, 0 12px 28px rgba(15, 23, 42, .08));
        }

        .sn-card-header {
            display: flex;
            align-items: flex-start;
            gap: .55rem;
            min-width: 0;
        }

        .sn-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            margin-top: 2px;
            flex: 0 0 auto;
        }

        .sn-title {
            font-weight: 900;
            font-size: .9rem;
            flex: 1;
            min-width: 0;
            color: rgba(0, 0, 0, .82);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sn-card-tools {
            display: flex;
            align-items: center;
            gap: .25rem;
            flex: 0 0 auto;
        }

        .sn-icon-btn {
            width: 30px;
            height: 30px;
            display: inline-grid;
            place-items: center;
            border-radius: .75rem;
            border: 1px solid rgba(255, 255, 255, .45);
            background: rgba(255, 255, 255, .5);
            color: rgba(0, 0, 0, .72);
            transition: transform .18s ease, background .18s ease, color .18s ease;
        }

        .sn-icon-btn:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, .85);
            color: rgba(0, 0, 0, .92);
        }

        .sn-icon-btn.danger:hover {
            color: var(--color-danger, #e50656);
            background: rgba(255, 240, 245, .95);
        }

        .sn-icon-btn i {
            width: 15px;
            height: 15px;
        }

        .sn-body {
            font-size: .8rem;
            font-weight: 650;
            color: rgba(0, 0, 0, .72);
            flex: 1;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .sn-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .55rem;
            margin-top: auto;
        }

        .sn-category-badge {
            align-self: flex-start;
            font-size: .64rem;
            font-weight: 900;
            background: rgba(255, 255, 255, .55);
            padding: 3px 8px;
            border-radius: 999px;
            text-transform: uppercase;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sn-date {
            font-size: .66rem;
            font-weight: 800;
            color: rgba(0, 0, 0, .48);
            white-space: nowrap;
        }

        .overlay {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(15, 23, 42, .55);
            z-index: 2400;
        }

        .overlay.show {
            display: flex;
        }

        .sn-modal {
            width: min(460px, 100%);
            max-height: calc(100vh - 2rem);
            overflow-y: auto;
            background: var(--color-surface, #fff);
            padding: 1.35rem;
            border-radius: var(--radius-lg, 1.25rem);
            box-shadow: var(--shadow-lg, 0 24px 70px rgba(15, 23, 42, .16));
            border: 1px solid var(--color-border, #e5e7eb);
        }

        .panel-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .panel-title {
            font-size: 1rem;
            font-weight: 900;
            color: var(--color-text, #111827);
        }

        .panel-subtitle {
            margin-top: .15rem;
            font-size: .76rem;
            font-weight: 700;
            color: var(--color-text-muted, #6b7280);
        }

        .close-btn {
            width: 36px;
            height: 36px;
            display: grid;
            place-items: center;
            border-radius: .85rem;
            border: 1px solid var(--color-border, #e5e7eb);
            background: var(--color-surface-2, #f9fafb);
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group label {
            display: block;
            margin-bottom: .35rem;
            font-size: .74rem;
            font-weight: 900;
            color: var(--color-text-muted, #6b7280);
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .form-row {
            display: flex;
            gap: 1rem;
        }

        .form-control {
            width: 100%;
            height: 42px;
            border-radius: var(--radius-md, .9rem);
            padding: 0 .8rem;
            border: 1px solid var(--color-border, #e5e7eb);
            background: var(--color-surface, #fff);
            color: var(--color-text, #111827);
            outline: none;
        }

        input[type="color"].form-control {
            padding: 3px;
            cursor: pointer;
        }

        .notes-area {
            width: 100%;
            min-height: 130px;
            resize: vertical;
            border-radius: var(--radius-lg, 1.25rem);
            padding: .9rem;
            font-size: .86rem;
            font-weight: 600;
            line-height: 1.55;
            border: 1px solid var(--color-border, #e5e7eb);
            background: var(--color-surface-2, #f9fafb);
            outline: none;
        }

        .sn-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: .65rem;
            margin-top: .5rem;
        }

        .empty-state {
            grid-column: 1 / -1;
            display: grid;
            place-items: center;
            min-height: 140px;
            padding: 1rem;
            border-radius: var(--radius-lg, 1.25rem);
            border: 1px dashed var(--color-border, #e5e7eb);
            background: var(--color-surface-2, #f9fafb);
            color: var(--color-text-muted, #6b7280);
            font-weight: 800;
            text-align: center;
        }

        .sn-toast {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            z-index: 3000;
            max-width: min(360px, calc(100vw - 2rem));
            padding: .85rem 1rem;
            border-radius: .95rem;
            background: #111827;
            color: #fff;
            font-weight: 800;
            font-size: .82rem;
            box-shadow: var(--shadow-lg, 0 24px 70px rgba(15, 23, 42, .16));
            opacity: 0;
            transform: translateY(10px);
            transition: opacity .18s ease, transform .18s ease;
            pointer-events: none;
        }

        .sn-toast.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
@endpush

@push('scripts')
    <script>
        (function () {
            'use strict';

            if (window.__personalStickyNotesPartialLoaded) {
                return;
            }

            window.__personalStickyNotesPartialLoaded = true;

            window.personalStickyNotesRoutes = window.personalStickyNotesRoutes || {
                fetch: @json(route('personal_notes.fetch')),
                categories: @json(route('personal_notes.categories')),
                store: @json(route('personal_notes.store')),
                updateBase: @json(url('/personal-notes/update')),
                doneBase: @json(url('/personal-notes/done')),
                deleteBase: @json(url('/personal-notes/delete'))
            };

            const state = {
                status: 'open',
                notes: [],
                categories: [],
                search: '',
                loading: false
            };

            function csrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
            }

            function hasJquery() {
                return Boolean(window.jQuery || window.$);
            }

            function jq() {
                return window.jQuery || window.$;
            }

            function routeUrl(base, id) {
                return `${String(base || '').replace(/\/$/, '')}/${encodeURIComponent(id)}`;
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function normalizeNote(raw) {
                const category = raw.category || raw.note_category || raw.category_name || raw.category_title || '';

                return {
                    id: raw.id,
                    title: raw.title || raw.name || 'Ohne Titel',
                    note: raw.note || raw.content || raw.description || '',
                    category_id: raw.category_id || raw.note_category_id || raw.category?.id || '',
                    category: typeof category === 'object' ? (category.name || category.title || '') : category,
                    color: raw.color || raw.background_color || '#fef9c3',
                    status: raw.status || (raw.done || raw.is_done ? 'done' : 'open'),
                    created_at: raw.created_at || raw.createdAt || '',
                    updated_at: raw.updated_at || raw.updatedAt || ''
                };
            }

            function normalizeResponseNotes(data) {
                const list = Array.isArray(data)
                    ? data
                    : (data.notes || data.data || data.items || data.personal_notes || []);

                return Array.isArray(list) ? list.map(normalizeNote) : [];
            }

            function normalizeCategories(data) {
                const list = Array.isArray(data)
                    ? data
                    : (data.categories || data.data || data.items || []);

                if (!Array.isArray(list)) return [];

                return list.map(item => ({
                    id: item.id,
                    text: item.text || item.name || item.title || item.category || 'Kategorie'
                })).filter(item => item.id !== undefined && item.id !== null);
            }

            function showToast(message) {
                const old = document.querySelector('.sn-toast');
                if (old) old.remove();

                const toast = document.createElement('div');
                toast.className = 'sn-toast';
                toast.textContent = message || 'Aktion ausgeführt';
                document.body.appendChild(toast);

                requestAnimationFrame(() => toast.classList.add('show'));
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 220);
                }, 2600);
            }

            function refreshIcons() {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            }

            function filteredNotes() {
                const q = state.search.trim().toLowerCase();

                return state.notes.filter(note => {
                    const statusOk = state.status === 'done'
                        ? note.status === 'done'
                        : note.status !== 'done';

                    if (!statusOk) return false;
                    if (!q) return true;

                    return [note.title, note.note, note.category]
                        .join(' ')
                        .toLowerCase()
                        .includes(q);
                });
            }

            function renderNotes() {
                const grid = document.getElementById('sn-grid');
                if (!grid) return;

                const notes = filteredNotes();

                if (!notes.length) {
                    grid.innerHTML = `<div class="empty-state">${state.status === 'done' ? 'Keine archivierten Notizen gefunden.' : 'Keine aktiven Notizen gefunden.'}</div>`;
                    refreshIcons();
                    return;
                }

                grid.innerHTML = notes.map(note => {
                    const payload = encodeURIComponent(JSON.stringify(note));
                    const checked = note.status === 'done' ? 'checked' : '';
                    const actionTitle = note.status === 'done' ? 'Wieder aktivieren' : 'Archivieren';
                    const dateText = note.updated_at || note.created_at || '';

                    return `
                        <article class="sn-card" style="background:${escapeHtml(note.color)}" data-note-id="${escapeHtml(note.id)}">
                            <div class="sn-card-header">
                                <input class="sn-checkbox" type="checkbox" ${checked} data-note-done="${escapeHtml(note.id)}" title="${actionTitle}">
                                <div class="sn-title" title="${escapeHtml(note.title)}">${escapeHtml(note.title)}</div>
                                <div class="sn-card-tools">
                                    <button class="sn-icon-btn" type="button" data-note-edit="${payload}" title="Bearbeiten">
                                        <i data-lucide="pencil"></i>
                                    </button>
                                    <button class="sn-icon-btn danger" type="button" data-note-delete="${escapeHtml(note.id)}" title="Löschen">
                                        <i data-lucide="trash-2"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="sn-body">${escapeHtml(note.note)}</div>
                            <div class="sn-footer">
                                <span class="sn-category-badge">${escapeHtml(note.category || 'Ohne Kategorie')}</span>
                                ${dateText ? `<span class="sn-date">${escapeHtml(String(dateText).slice(0, 10))}</span>` : ''}
                            </div>
                        </article>
                    `;
                }).join('');

                refreshIcons();
            }

            async function fetchJson(url, options = {}) {
                const response = await fetch(url, {
                    credentials: 'same-origin',
                    ...options,
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(options.body ? { 'Content-Type': 'application/json' } : {}),
                        ...(options.headers || {})
                    }
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    const message = data.message || data.error || 'Serverfehler';
                    throw new Error(message);
                }

                return data;
            }

            async function loadCategories() {
                const select = document.getElementById('noteCategory');
                const routes = window.personalStickyNotesRoutes || {};

                if (!select || !routes.categories) return;

                try {
                    const data = await fetchJson(routes.categories);
                    state.categories = normalizeCategories(data);

                    select.innerHTML = '<option value="">Keine Kategorie</option>' + state.categories.map(category => {
                        return `<option value="${escapeHtml(category.id)}">${escapeHtml(category.text)}</option>`;
                    }).join('');

                    if (hasJquery() && typeof jq()(select).select2 === 'function') {
                        const $select = jq()(select);
                        if ($select.data('select2')) {
                            $select.select2('destroy');
                        }
                        $select.select2({
                            width: '100%',
                            dropdownParent: jq()('#noteModalOverlay'),
                            placeholder: 'Kategorie wählen',
                            allowClear: true
                        });
                    }
                } catch (error) {
                    console.error('Personal note categories failed:', error);
                }
            }

            async function loadNotes() {
                const grid = document.getElementById('sn-grid');
                const routes = window.personalStickyNotesRoutes || {};

                if (!routes.fetch) {
                    if (grid) grid.innerHTML = '<div class="empty-state">Route personal_notes.fetch fehlt.</div>';
                    return;
                }

                if (state.loading) return;
                state.loading = true;

                if (grid) grid.innerHTML = '<div class="empty-state">Lädt...</div>';

                try {
                    const url = new URL(routes.fetch, window.location.origin);
                    url.searchParams.set('status', state.status);

                    const data = await fetchJson(url.toString());
                    state.notes = normalizeResponseNotes(data);
                    renderNotes();
                } catch (error) {
                    console.error('Personal notes load failed:', error);
                    if (grid) grid.innerHTML = `<div class="empty-state">Notizen konnten nicht geladen werden: ${escapeHtml(error.message)}</div>`;
                } finally {
                    state.loading = false;
                }
            }

            function openNoteModal(note = null) {
                const overlay = document.getElementById('noteModalOverlay');
                const idInput = document.getElementById('noteId');
                const title = document.getElementById('noteTitle');
                const content = document.getElementById('noteContent');
                const category = document.getElementById('noteCategory');
                const color = document.getElementById('noteColor');
                const modalTitle = document.getElementById('noteModalTitle');
                const modalSub = document.getElementById('noteModalSubtitle');
                const saveBtn = document.getElementById('sn-save-btn');

                if (!overlay) return;

                if (idInput) idInput.value = note?.id || '';
                if (title) title.value = note?.title || '';
                if (content) content.value = note?.note || '';
                if (color) color.value = note?.color || '#fef9c3';

                if (category) {
                    category.value = note?.category_id || '';
                    if (hasJquery() && typeof jq()(category).select2 === 'function') {
                        jq()(category).val(note?.category_id || '').trigger('change');
                    }
                }

                if (modalTitle) modalTitle.textContent = note?.id ? 'Notiz bearbeiten' : 'Neue Notiz';
                if (modalSub) modalSub.textContent = note?.id ? 'Änderungen werden direkt gespeichert' : 'Direkt im Dashboard speichern';
                if (saveBtn) saveBtn.innerHTML = note?.id ? '<i data-lucide="save"></i> Aktualisieren' : '<i data-lucide="save"></i> Speichern';

                overlay.classList.add('show');
                overlay.setAttribute('aria-hidden', 'false');
                setTimeout(() => title?.focus(), 80);
                refreshIcons();
            }

            function closeNoteModal() {
                const overlay = document.getElementById('noteModalOverlay');
                const form = document.getElementById('addNoteForm');

                if (form) form.reset();

                const idInput = document.getElementById('noteId');
                if (idInput) idInput.value = '';

                const color = document.getElementById('noteColor');
                if (color) color.value = '#fef9c3';

                const category = document.getElementById('noteCategory');
                if (category && hasJquery() && typeof jq()(category).select2 === 'function') {
                    jq()(category).val('').trigger('change');
                }

                if (overlay) {
                    overlay.classList.remove('show');
                    overlay.setAttribute('aria-hidden', 'true');
                }
            }

            async function saveNote(event) {
                event.preventDefault();

                const routes = window.personalStickyNotesRoutes || {};
                const id = document.getElementById('noteId')?.value || '';
                const title = document.getElementById('noteTitle')?.value.trim() || '';
                const note = document.getElementById('noteContent')?.value.trim() || '';
                const categoryId = document.getElementById('noteCategory')?.value || '';
                const color = document.getElementById('noteColor')?.value || '#fef9c3';

                if (!title || !note) {
                    showToast('Titel und Inhalt sind erforderlich');
                    return;
                }

                const url = id ? routeUrl(routes.updateBase, id) : routes.store;
                const method = id ? 'PUT' : 'POST';

                if (!url) {
                    showToast(id ? 'Update-Route fehlt' : 'Store-Route fehlt');
                    return;
                }

                try {
                    await fetchJson(url, {
                        method,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken()
                        },
                        body: JSON.stringify({
                            title,
                            note,
                            content: note,
                            category_id: categoryId || null,
                            color,
                            priority: 'medium'
                        })
                    });

                    showToast(id ? 'Notiz aktualisiert' : 'Notiz gespeichert');
                    closeNoteModal();
                    state.status = 'open';
                    activateTab('open', false);
                    await loadNotes();
                } catch (error) {
                    console.error('Personal note save failed:', error);
                    showToast(error.message || 'Notiz konnte nicht gespeichert werden');
                }
            }

            async function toggleDone(id) {
                const routes = window.personalStickyNotesRoutes || {};
                const url = routeUrl(routes.doneBase, id);

                if (!url) {
                    showToast('Archiv-Route fehlt');
                    return;
                }

                try {
                    await fetchJson(url, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken()
                        },
                        body: JSON.stringify({
                            status: state.status === 'done' ? 'open' : 'done',
                            done: state.status !== 'done'
                        })
                    });

                    showToast(state.status === 'done' ? 'Notiz wieder aktiviert' : 'Notiz archiviert');
                    await loadNotes();
                } catch (error) {
                    console.error('Personal note done failed:', error);
                    showToast(error.message || 'Status konnte nicht geändert werden');
                    renderNotes();
                }
            }

            async function deleteNote(id) {
                const routes = window.personalStickyNotesRoutes || {};
                const url = routeUrl(routes.deleteBase, id);

                if (!url) {
                    showToast('Lösch-Route fehlt');
                    return;
                }

                const confirmed = window.Swal
                    ? await window.Swal.fire({
                        title: 'Notiz löschen?',
                        text: 'Diese Notiz wird gelöscht.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ja, löschen',
                        cancelButtonText: 'Abbrechen'
                    }).then(result => result.isConfirmed)
                    : window.confirm('Notiz wirklich löschen?');

                if (!confirmed) return;

                try {
                    await fetchJson(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken()
                        }
                    });

                    showToast('Notiz gelöscht');
                    await loadNotes();
                } catch (error) {
                    console.error('Personal note delete failed:', error);
                    showToast(error.message || 'Notiz konnte nicht gelöscht werden');
                }
            }

            function activateTab(status, shouldLoad = true) {
                state.status = status || 'open';

                document.querySelectorAll('#sn-tabs .tab-btn').forEach(button => {
                    button.classList.toggle('active', button.dataset.status === state.status);
                });

                if (shouldLoad) loadNotes();
            }

            function bindEvents() {
                document.getElementById('sn-open-create')?.addEventListener('click', () => openNoteModal());
                document.getElementById('sn-close-modal')?.addEventListener('click', closeNoteModal);
                document.getElementById('sn-cancel-modal')?.addEventListener('click', closeNoteModal);
                document.getElementById('addNoteForm')?.addEventListener('submit', saveNote);

                document.getElementById('noteModalOverlay')?.addEventListener('click', event => {
                    if (event.target?.id === 'noteModalOverlay') closeNoteModal();
                });

                document.addEventListener('keydown', event => {
                    if (event.key === 'Escape' && document.getElementById('noteModalOverlay')?.classList.contains('show')) {
                        closeNoteModal();
                    }
                });

                document.getElementById('sn-tabs')?.addEventListener('click', event => {
                    const button = event.target.closest('[data-status]');
                    if (!button) return;
                    activateTab(button.dataset.status || 'open');
                });

                document.getElementById('sn-search')?.addEventListener('input', event => {
                    state.search = event.target.value || '';
                    renderNotes();
                });

                document.getElementById('sn-grid')?.addEventListener('click', event => {
                    const editButton = event.target.closest('[data-note-edit]');
                    const deleteButton = event.target.closest('[data-note-delete]');
                    const doneInput = event.target.closest('[data-note-done]');

                    if (editButton) {
                        try {
                            const note = JSON.parse(decodeURIComponent(editButton.dataset.noteEdit || ''));
                            openNoteModal(note);
                        } catch (error) {
                            console.error('Personal note edit failed:', error);
                            showToast('Notiz konnte nicht geöffnet werden');
                        }
                        return;
                    }

                    if (deleteButton) {
                        deleteNote(deleteButton.dataset.noteDelete);
                        return;
                    }

                    if (doneInput) {
                        toggleDone(doneInput.dataset.noteDone);
                    }
                });
            }

            function initPersonalStickyNotes() {
                if (!document.getElementById('personalStickyNotesRoot')) return;

                bindEvents();
                loadCategories();
                loadNotes();
                refreshIcons();
            }

            window.openNoteModal = openNoteModal;
            window.closeNoteModal = closeNoteModal;
            window.reloadPersonalStickyNotes = loadNotes;

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initPersonalStickyNotes, { once: true });
            } else {
                initPersonalStickyNotes();
            }
        })();
    </script>
@endpush
