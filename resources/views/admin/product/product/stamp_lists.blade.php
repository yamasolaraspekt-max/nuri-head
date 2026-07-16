@extends('admin.layouts.app')

@section('title') Stammartikel-Listen @endsection

@section('style')
<style>
    :root {
        --stamp-bg: #f6f7fb;
        --stamp-shell: #ffffff;
        --stamp-border: rgba(15,23,42,.06);
        --stamp-muted: #6b7280;
        --stamp-shadow: 0 18px 45px rgba(15,23,42,.08);
        --stamp-radius-xl: 20px;
    }

    body {
        background: var(--stamp-bg) !important;
    }

    .stamp-layout {
        border-radius: var(--stamp-radius-xl);
        background: var(--stamp-shell);
        box-shadow: var(--stamp-shadow);
        border: 1px solid var(--stamp-border);
        padding: 1.25rem 1.25rem 1.5rem;
    }

    .stamp-header {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .stamp-header h2 {
        font-size: 1.35rem;
        margin: 0;
    }

    .stamp-header small {
        font-size: .8rem;
        color: var(--stamp-muted);
    }

    .stamp-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        align-items: center;
    }

    .stamp-pill {
        font-size: .75rem;
        padding: .15rem .6rem;
        border-radius: 999px;
        background: #fee2e2;
        color: #b91c1c;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
    }

    .stamp-grid {
        display: grid;
        grid-template-columns: minmax(0, 280px) minmax(0, 1fr);
        gap: 1rem;
    }

    /* LEFT SIDEBAR */
    .stamp-sidebar {
        border-radius: 18px;
        border: 1px solid rgba(148,163,184,.35);
        background: #111827;
        color: #e5e7eb;
        padding: .9rem .9rem 1rem;
        min-height: 420px;
        display: flex;
        flex-direction: column;
    }

    .stamp-sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .6rem;
        margin-bottom: .6rem;
    }

    .stamp-sidebar-header h5 {
        margin: 0;
        font-size: .9rem;
    }

    .stamp-sidebar-header small {
        font-size: .7rem;
        color: #9ca3af;
    }

    .stamp-sidebar-tabs {
        display: flex;
        border-radius: 999px;
        background: rgba(15,23,42,.7);
        padding: .15rem;
        margin-bottom: .6rem;
    }

    .stamp-sidebar-tab {
        flex: 1;
        font-size: .75rem;
        border-radius: 999px;
        padding: .25rem .4rem;
        text-align: center;
        cursor: pointer;
        color: #9ca3af;
    }

    .stamp-sidebar-tab.active {
        background: #1f2937;
        color: #e5e7eb;
        font-weight: 600;
    }

    .stamp-sidebar-search {
        margin-bottom: .6rem;
    }

    .stamp-sidebar-search input {
        background: #020617;
        border-radius: 999px;
        border: 1px solid rgba(148,163,184,.5);
        color: #e5e7eb;
        font-size: .78rem;
    }

    .stamp-sidebar-folders {
        flex: 1;
        overflow-y: auto;
        padding-right: .15rem;
    }

    .stamp-folder {
        display: flex;
        gap: .6rem;
        padding: .45rem .5rem;
        border-radius: 12px;
        cursor: pointer;
        align-items: center;
        transition: background .12s ease-out, transform .12s;
    }

    .stamp-folder:hover {
        background: rgba(15,23,42,.85);
        transform: translateY(-1px);
    }

    .stamp-folder.active {
        background: linear-gradient(135deg, rgba(248,113,113,.18), rgba(239,68,68,.12));
        box-shadow: 0 0 0 1px rgba(248,113,113,.45);
    }

    .stamp-folder-icon {
        width: 34px;
        height: 30px;
        flex-shrink: 0;
    }

    .stamp-folder-title {
        font-size: .82rem;
        margin-bottom: 2px;
    }

    .stamp-folder-meta {
        font-size: .7rem;
        color: #9ca3af;
    }

    .stamp-folder-owner {
        font-size: .7rem;
        color: #fecaca;
    }

    .stamp-folder-actions {
        margin-left: auto;
        display: flex;
        gap: .25rem;
        align-items: center;
    }

    .stamp-folder-actions .btn {
        padding: 0;
        border-radius: 999px;
        width: 20px;
        height: 20px;
        line-height: 20px;
        font-size: .7rem;
    }

    .stamp-sidebar-footer {
        margin-top: .6rem;
        font-size: .7rem;
        color: #9ca3af;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* RIGHT PANEL */
    .stamp-main {
        display: flex;
        flex-direction: column;
        gap: .75rem;
    }

    .stamp-cards-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: .6rem;
    }

    .stamp-stat-card {
        border-radius: 14px;
        border: 1px solid rgba(148,163,184,.20);
        padding: .55rem .7rem;
        background: #fef2f2;
        display: flex;
        align-items: center;
        gap: .45rem;
    }

    .stamp-stat-icon {
        width: 30px;
        height: 30px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fee2e2;
        flex-shrink: 0;
    }

    .stamp-stat-icon i {
        font-size: 1.1rem;
    }

    .stamp-stat-text small {
        font-size: .7rem;
        color: #9b2c2c;
    }

    .stamp-stat-text strong {
        display: block;
        font-size: .95rem;
    }

    .stamp-main-header {
        display: flex;
        flex-wrap: wrap;
        gap: .45rem .75rem;
        align-items: center;
        justify-content: space-between;
    }

    .stamp-main-header-left span {
        display: block;
        font-size: .75rem;
        color: var(--stamp-muted);
    }

    .stamp-main-header-left h4 {
        margin: 0;
        font-size: 1rem;
    }

    .stamp-main-filters {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
        align-items: center;
    }

    .stamp-main-filters select,
    .stamp-main-filters input {
        font-size: .78rem;
        height: 30px;
    }

    .stamp-main-body {
        border-radius: 14px;
        border: 1px solid rgba(148,163,184,.20);
        background: #ffffff;
        padding: .5rem .7rem;
        min-height: 260px;
    }

    .stamp-table {
        font-size: .78rem;
    }

    .stamp-table thead th {
        font-size: .74rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6b7280;
        border-bottom-width: 1px;
    }

    .stamp-table tbody td {
        vertical-align: middle;
    }

    .stamp-empty {
        text-align: center;
        padding: 2rem 0;
        color: #9ca3af;
        font-size: .8rem;
    }

    .stamp-pagination {
        margin-top: .4rem;
        font-size: .78rem;
    }

    @media (max-width: 991.98px) {
        .stamp-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>
@endsection

@section('content')
<div class="app-content"> 

    <div class="content-wrapper"> 

        <div class="content-body">
            <div class="stamp-layout">

                <div class="stamp-header">
                    <div>
                        <h2>Stamm-Listen & Sets</h2>
                        <small>Erstellen Sie Sammlungen Ihrer Stamm-Artikel, z.B. nach Projekt, Serie oder Einsatz.</small>
                    </div>
                    <div class="stamp-header-actions">
                        <span class="stamp-pill">
                            <i class="feather icon-folder"></i>
                            <span><span id="stamp-my-count-label">0</span> eigene Ordner</span>
                        </span>
                        <span class="stamp-pill">
                            <i class="feather icon-users"></i>
                            <span><span id="stamp-other-count-label">0</span> freigegeben</span>
                        </span>
                        <button type="button" class="btn btn-sm btn-danger" id="stamp-create-list-btn">
                            <i class="feather icon-plus mr-25"></i> Ordner anlegen
                        </button>
                    </div>
                </div>

                <div class="stamp-grid">
                    {{-- LEFT: Sidebar --}}
                    <aside class="stamp-sidebar">
                        <div class="stamp-sidebar-header">
                            <div>
                                <h5>Stempel-Ordner</h5>
                                <small>Eigene & Team-Sammlungen.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-light" id="stamp-refresh-btn">
                                <i class="feather icon-rotate-cw"></i>
                            </button>
                        </div>

                        <div class="stamp-sidebar-tabs">
                            <div class="stamp-sidebar-tab active" data-tab="mine">Meine Listen</div>
                            <div class="stamp-sidebar-tab" data-tab="others">Team / Andere</div>
                        </div>

                        <div class="stamp-sidebar-search">
                            <input type="text" id="stamp-search-lists" class="form-control form-control-sm"
                                   placeholder="Ordner suchen...">
                        </div>

                        <div id="stamp-sidebar-folders" class="stamp-sidebar-folders">
                            {{-- AJAX partial stamp_lists_sidebar --}}
                        </div>

                        <div class="stamp-sidebar-footer">
                            <span id="stamp-sidebar-footer-text">Keine Auswahl</span>
                            <button type="button" class="btn btn-sm btn-outline-light" id="stamp-edit-list-btn" disabled>
                                <i class="feather icon-edit"></i>
                            </button>
                        </div>
                    </aside>

                    {{-- RIGHT: Main --}}
                    <section class="stamp-main">
                        <div id="stamp-stats-cards" class="stamp-cards-row">
                            {{-- AJAX partial stamp_lists_stats --}}
                        </div>

                        <div class="stamp-main-header">
                            <div class="stamp-main-header-left">
                                <span id="stamp-selected-folder-label">Kein Ordner ausgewählt</span>
                                <h4 id="stamp-selected-folder-name"></h4>
                            </div>
                            <div class="stamp-main-filters">
                                <input type="text" id="stamp-items-search" class="form-control form-control-sm"
                                       placeholder="Stempel in diesem Ordner suchen...">
                                <select id="stamp-items-sort" class="form-control form-control-sm">
                                    <option value="stamp_articles.name|asc">Name A–Z</option>
                                    <option value="stamp_articles.name|desc">Name Z–A</option>
                                    <option value="stamp_articles.article_no|asc">Art.Nr. ↑</option>
                                    <option value="stamp_articles.article_no|desc">Art.Nr. ↓</option>
                                    <option value="added_at|desc">Zuletzt hinzugefügt</option>
                                    <option value="added_at|asc">Älteste zuerst</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="stamp-items-reset-btn">
                                    Filter zurücksetzen
                                </button>
                            </div>
                        </div>

                        <div class="stamp-main-body"> 
                            
                            <div id="stamp-items-list">
                                <div class="stamp-empty">
                                    Bitte wählen Sie links einen Ordner aus, um die Stempel-Artikel zu sehen.
                                </div>
                            </div>
                            <div id="stamp-items-pagination" class="stamp-pagination"></div>
                        </div>

                        <div class="mt-1">
                            <hr>
                            <div class="row align-items-center">
                                <div class="col-md-5 mb-50">
                                    <label style="font-size:.8rem;">Produkt/Stempel in Ordner hinzufügen</label>
                                    <div class="input-group input-group-sm">
                                        <input type="text"
                                            id="stamp-add-query"
                                            class="form-control"
                                            placeholder="Nach Art.Nr., Name oder Hersteller suchen...">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-danger" type="button" id="stamp-add-search-btn">
                                                <i class="feather icon-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted" style="font-size:.7rem;">
                                        Wählen Sie links zuerst einen Ordner aus, dann fügen Sie hier Produkte hinzu.
                                    </small>
                                </div>
                                <div class="col-md-7 mb-50">
                                    <div id="stamp-add-results" style="max-height:180px; overflow:auto; font-size:.78rem;">
                                        {{-- AJAX-Ergebnisse --}}
                                    </div>
                                </div>
                            </div>
                        </div>


                    </section>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal create/edit list --}}
<div class="modal fade" id="stampListModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="stamp-list-form" class="modal-content">
            @csrf
            <input type="hidden" name="_method" id="stamp_list_method" value="POST">
            <input type="hidden" id="stamp_list_id">

            <div class="modal-header">
                <h5 class="modal-title" id="stamp-list-modal-title">Ordner anlegen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Ordnername</label>
                    <input type="text" name="name" id="stamp_list_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Beschreibung</label>
                    <textarea name="description" id="stamp_list_description" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Farbe (optional)</label>
                    <input type="text" name="color" id="stamp_list_color" class="form-control" placeholder="#ef4444">
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="stamp_list_shared" name="is_shared">
                        <label class="custom-control-label" for="stamp_list_shared">Für andere freigeben</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="stamp-delete-list-btn" class="btn btn-outline-danger mr-auto d-none">
                    <i class="feather icon-trash"></i> Ordner löschen
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                <button type="submit" class="btn btn-danger">
                    <i class="feather icon-save mr-25"></i> Speichern
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
    (function ($) {
        'use strict';

        // --- BASE URLs from routes ---
        const listsUrl          = '{{ route("ajax.stamp.lists") }}';              // GET /admin/ajax/stamp-articles/lists
        const listsStoreUrl     = '{{ route("ajax.stamp.lists.store") }}';       // POST /admin/ajax/stamp-articles/lists
        const baseStampListsUrl = '{{ url("admin/ajax/stamp-articles/lists") }}';// /admin/ajax/stamp-articles/lists
        const productSearchUrl  = '{{ route("ajax.products.search") }}';         // GET /admin/ajax/products/search

        let currentListId = null;
        let currentTab    = 'mine'; // mine | others

        // -----------------------------
        // LOAD / FILTER LISTS (LEFT)
        // -----------------------------
        function loadLists() {
            const search  = $('#stamp-search-lists').val() || '';
            const sortVal = 'created_at|desc';
            const [sort_by, sort_dir] = sortVal.split('|');

            $.ajax({
                url: listsUrl,
                type: 'GET',
                dataType: 'json',
                data: {
                    search:  search,
                    sort_by: sort_by,
                    sort_dir: sort_dir
                },
                success: function (res) {
                    $('#stamp-sidebar-folders').html(res.lists_html || '');
                    $('#stamp-stats-cards').html(res.stats_html || '');

                    // Counts for header pills
                    const $my  = $('#stamp-my-count');
                    const $oth = $('#stamp-other-count');
                    if ($my.length && $oth.length) {
                        $('#stamp-my-count-label').text($my.data('count') || 0);
                        $('#stamp-other-count-label').text($oth.data('count') || 0);
                    }

                    if (currentListId) {
                        const $folder = $('.stamp-folder[data-list-id="' + currentListId + '"]');
                        if ($folder.length) {
                            $folder.addClass('active');
                            $('#stamp-sidebar-footer-text').text($folder.data('fullname') || '');
                            $('#stamp-edit-list-btn').prop('disabled', false);
                        } else {
                            resetSelection();
                        }
                    } else {
                        resetSelection();
                    }

                    filterSidebarByTab();
                },
                error: function () {
                    toastr.error('Stempel-Ordner konnten nicht geladen werden.');
                }
            });
        }

        function resetSelection() {
            currentListId = null;
            $('.stamp-folder').removeClass('active');
            $('#stamp-sidebar-footer-text').text('Keine Auswahl');
            $('#stamp-edit-list-btn').prop('disabled', true);
            $('#stamp-selected-folder-label').text('Kein Ordner ausgewählt');
            $('#stamp-selected-folder-name').text('');
            $('#stamp-items-list').html(
                '<div class="stamp-empty">Bitte wählen Sie links einen Ordner aus, um die Stempel-Artikel zu sehen.</div>'
            );
            $('#stamp-items-pagination').empty();
        }

        function filterSidebarByTab() {
            $('.stamp-folder').each(function () {
                const isMine  = $(this).data('mine') == 1;
                const isOther = !isMine;

                if (currentTab === 'mine' && !isMine) {
                    $(this).hide();
                } else if (currentTab === 'others' && !isOther) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        }

        // -----------------------------
        // ITEMS IN A LIST (RIGHT)
        // -----------------------------
        function loadItems(listId, pageUrl) {
            if (!listId) return;

            const sortVal = $('#stamp-items-sort').val() || 'stamp_articles.name|asc';
            const [sort_by, sort_dir] = sortVal.split('|');
            const search = $('#stamp-items-search').val() || '';

            // Named route: ajax.stamp.lists.items -> /admin/ajax/stamp-articles/lists/{list}/items
            const ajaxUrl = pageUrl || (baseStampListsUrl + '/' + listId + '/items');

            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                dataType: 'json',
                data: {
                    search:  search,
                    sort_by: sort_by,
                    sort_dir: sort_dir
                },
                success: function (res) {
                    $('#stamp-items-list').html(res.html || '');
                    $('#stamp-items-pagination').html(res.pagination || '');
                    bindRemoveButtons();
                },
                error: function () {
                    toastr.error('Stempel-Artikel konnten nicht geladen werden.');
                }
            });
        }

        // -----------------------------
        // CREATE / EDIT LIST MODAL
        // -----------------------------
        function openCreateModal() {
            $('#stamp_list_id').val('');
            $('#stamp_list_method').val('POST');
            $('#stamp-list-modal-title').text('Ordner anlegen');
            $('#stamp_list_name').val('');
            $('#stamp_list_description').val('');
            $('#stamp_list_color').val('');
            $('#stamp_list_shared').prop('checked', true);
            $('#stamp-delete-list-btn').addClass('d-none');
            $('#stampListModal').modal('show');
        }

        function openEditModal(listId) {
            const $folder = $('.stamp-folder[data-list-id="' + listId + '"]');
            if (!$folder.length) return;

            $('#stamp_list_id').val(listId);
            $('#stamp_list_method').val('PUT');
            $('#stamp-list-modal-title').text('Ordner bearbeiten');
            $('#stamp_list_name').val($folder.data('name') || '');
            $('#stamp_list_description').val($folder.data('description') || '');
            $('#stamp_list_color').val($folder.data('color') || '');
            $('#stamp_list_shared').prop('checked', $folder.data('shared') == 1);
            $('#stamp-delete-list-btn').removeClass('d-none');
            $('#stampListModal').modal('show');
        }

        function submitListForm(e) {
            e.preventDefault();

            const id     = $('#stamp_list_id').val();
            const method = $('#stamp_list_method').val() || 'POST';
            let   url    = listsStoreUrl; // POST /admin/ajax/stamp-articles/lists

            // Update: PUT /admin/ajax/stamp-articles/lists/{list}
            if (method === 'PUT' && id) {
                url = baseStampListsUrl + '/' + id;
            }

            const data = {
                name:        $('#stamp_list_name').val(),
                description: $('#stamp_list_description').val(),
                color:       $('#stamp_list_color').val(),
                is_shared:   $('#stamp_list_shared').is(':checked') ? 1 : 0,
                _method:     method,
                _token:      '{{ csrf_token() }}'
            };

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function (res) {
                    toastr.success(res.message || 'Gespeichert');
                    $('#stampListModal').modal('hide');
                    loadLists();
                },
                error: function () {
                    toastr.error('Fehler beim Speichern.');
                }
            });
        }

        function deleteCurrentList() {
            const id = $('#stamp_list_id').val();
            if (!id) return;

            if (!confirm('Diesen Ordner wirklich löschen?')) return;

            // DELETE /admin/ajax/stamp-articles/lists/{list}
            $.ajax({
                url: baseStampListsUrl + '/' + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    toastr.success(res.message || 'Ordner gelöscht');
                    $('#stampListModal').modal('hide');
                    currentListId = null;
                    loadLists();
                },
                error: function () {
                    toastr.error('Löschen fehlgeschlagen.');
                }
            });
        }

        // -----------------------------
        // DETACH ITEM FROM LIST
        // -----------------------------
        function bindRemoveButtons() {
            $(document).off('click', '.stamp-item-remove-btn').on('click', '.stamp-item-remove-btn', function () {
                const itemId = $(this).data('item-id'); // StampArticleListItem::id
                const listId = currentListId;
                if (!listId || !itemId) return;

                if (!confirm('Diesen Eintrag aus dem Ordner entfernen?')) return;

                // DELETE /admin/ajax/stamp-articles/lists/{list}/detach/{stampArticle}
                $.ajax({
                    url: baseStampListsUrl + '/' + listId + '/detach/' + itemId,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        toastr.success(res.message || 'Entfernt');
                        loadItems(listId);
                        loadLists(); // Stats aktualisieren
                    },
                    error: function () {
                        toastr.error('Konnte nicht entfernt werden.');
                    }
                });
            });
        }

        // -----------------------------
        // SEARCH PRODUCTS TO ADD
        // -----------------------------
        function renderStampSearchResults(list) {
            const $container = $('#stamp-add-results');
            if (!list.length) {
                $container.html('<div class="text-muted" style="font-size:.75rem;">Keine Produkte gefunden.</div>');
                return;
            }

            let html = '<ul class="list-group list-group-flush">';
            list.forEach(function (row) {
                const label =
                    (row.article_no ? row.article_no + ' · ' : '') +
                    row.product +
                    (row.brand_name ? ' (' + row.brand_name + ')' : '');

                html += '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                            '<div>' +
                                '<div>' + label + '</div>' +
                                '<small class="text-muted">' + (row.category || '-') + '</small>' +
                            '</div>' +
                            '<button type="button" class="btn btn-sm btn-outline-success stamp-add-product-btn" data-product-id="' + row.id + '">' +
                                '<i class="feather icon-plus"></i>' +
                            '</button>' +
                        '</li>';
            });
            html += '</ul>';

            $container.html(html);
        }

        function searchProductsForStamp() {
            const q = $('#stamp-add-query').val() || '';
            if (!q.length) {
                $('#stamp-add-results').empty();
                return;
            }

            $.ajax({
                url: productSearchUrl, // /admin/ajax/products/search
                type: 'GET',
                dataType: 'json',
                data: { q: q },
                success: function (res) {
                    renderStampSearchResults(res || []);
                },
                error: function () {
                    toastr.error('Produktsuche fehlgeschlagen.');
                }
            });
        }

        // -----------------------------
        // ATTACH PRODUCT TO CURRENT LIST
        // -----------------------------
        function addProductToCurrentStampList(productId) {
            if (!currentListId) {
                toastr.warning('Bitte zuerst links einen Ordner auswählen.');
                return;
            }

            // POST /admin/ajax/stamp-articles/lists/{list}/attach
            $.ajax({
                url: baseStampListsUrl + '/' + currentListId + '/attach',
                type: 'POST',
                dataType: 'json',
                data: {
                    stamp_article_id: productId, // FK zeigt auf products.id
                    _token: '{{ csrf_token() }}'
                },
                success: function (res) {
                    toastr.success(res.message || 'Produkt hinzugefügt.');
                    loadItems(currentListId);
                    loadLists();
                },
                error: function (xhr) {
                    if (xhr.status === 409) {
                        toastr.info('Dieses Produkt ist bereits in diesem Ordner.');
                    } else {
                        toastr.error('Produkt konnte nicht hinzugefügt werden.');
                    }
                }
            });
        }

        // -----------------------------
        // INIT
        // -----------------------------
        $(function () {

            loadLists();

            // Refresh lists
            $('#stamp-refresh-btn').on('click', loadLists);

            // Search lists (press Enter)
            $('#stamp-search-lists').on('keyup', function (e) {
                if (e.keyCode === 13) {
                    loadLists();
                }
            });

            // New list
            $('#stamp-create-list-btn').on('click', openCreateModal);

            // Edit current list
            $('#stamp-edit-list-btn').on('click', function () {
                if (currentListId) {
                    openEditModal(currentListId);
                }
            });

            // Save list (create/update)
            $('#stamp-list-form').on('submit', submitListForm);

            // Delete from modal
            $('#stamp-delete-list-btn').on('click', deleteCurrentList);

            // Sidebar tabs (mine / others)
            $(document).on('click', '.stamp-sidebar-tab', function () {
                $('.stamp-sidebar-tab').removeClass('active');
                $(this).addClass('active');
                currentTab = $(this).data('tab');
                filterSidebarByTab();
            });

            // Folder click
            $(document).on('click', '.stamp-folder', function (e) {
                if ($(e.target).closest('.stamp-folder-actions').length) return;

                const id   = $(this).data('list-id');
                const name = $(this).data('name') || '';
                const full = $(this).data('fullname') || name;

                currentListId = id;
                $('.stamp-folder').removeClass('active');
                $(this).addClass('active');

                $('#stamp-sidebar-footer-text').text(full);
                $('#stamp-edit-list-btn').prop('disabled', false);
                $('#stamp-selected-folder-label').text('Aktive Liste');
                $('#stamp-selected-folder-name').text(name);

                loadItems(id);
            });

            // Edit/Delete directly from folder row
            $(document).on('click', '.stamp-folder-edit', function (e) {
                e.stopPropagation();
                const id = $(this).closest('.stamp-folder').data('list-id');
                openEditModal(id);
            });

            $(document).on('click', '.stamp-folder-delete', function (e) {
                e.stopPropagation();
                const id = $(this).closest('.stamp-folder').data('list-id');
                if (!id) return;
                if (!confirm('Diesen Ordner wirklich löschen?')) return;

                // DELETE /admin/ajax/stamp-articles/lists/{list}
                $.ajax({
                    url: baseStampListsUrl + '/' + id,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        toastr.success(res.message || 'Ordner gelöscht');
                        if (currentListId == id) currentListId = null;
                        loadLists();
                    },
                    error: function () {
                        toastr.error('Löschen fehlgeschlagen.');
                    }
                });
            });

            // Right: filters
            $('#stamp-items-search').on('keyup', function (e) {
                if (e.keyCode === 13 && currentListId) {
                    loadItems(currentListId);
                }
            });

            $('#stamp-items-sort').on('change', function () {
                if (currentListId) loadItems(currentListId);
            });

            $('#stamp-items-reset-btn').on('click', function () {
                $('#stamp-items-search').val('');
                $('#stamp-items-sort').val('stamp_articles.name|asc');
                if (currentListId) loadItems(currentListId);
            });

            // Search products to add
            $('#stamp-add-search-btn').on('click', function () {
                searchProductsForStamp();
            });

            $('#stamp-add-query').on('keyup', function (e) {
                if (e.keyCode === 13) {
                    searchProductsForStamp();
                }
            });

            // Click on "add" in result list
            $(document).on('click', '.stamp-add-product-btn', function () {
                const productId = $(this).data('product-id');
                addProductToCurrentStampList(productId);
            });

            // Optional: select-all checkbox in items table
            $(document).on('change', '#stamp-items-check-all', function () {
                $('.stamp-item-check').prop('checked', $(this).is(':checked'));
            });

            // Pagination for items
            $(document).on('click', '#stamp-items-pagination a', function (e) {
                e.preventDefault();
                const href = $(this).attr('href');
                if (!href || !currentListId) return;
                loadItems(currentListId, href);
            });
        });

    })(jQuery);
</script>
@endsection


@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Produktliste',
                url: "{{ url('product') }}"
            },
            {
                label: 'Stamm-Artikel,',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>


@endpush