@extends('admin.layouts.app')

@section('title') Produkt-Favoritenlisten @endsection

@section('style')
<style>
    :root {
        --fav-bg: #f6f7fb;
        --fav-shell: #ffffff;
        --fav-border: rgba(15,23,42,.06);
        --fav-muted: #6b7280;
        --fav-accent: #2563eb;
        --fav-shadow: 0 18px 45px rgba(15,23,42,.08);
        --fav-radius-xl: 20px;
    }

    body {
        background: var(--fav-bg) !important;
    }

    .favorite-layout {
        border-radius: var(--fav-radius-xl);
        background: var(--fav-shell);
        box-shadow: var(--fav-shadow);
        border: 1px solid var(--fav-border);
        padding: 1.25rem 1.25rem 1.5rem;
    }

    .fav-header {
        display: flex;
        justify-content: space-between;
        gap: .75rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .fav-header h2 {
        font-size: 1.35rem;
        margin: 0;
    }

    .fav-header small {
        font-size: .8rem;
        color: var(--fav-muted);
    }

    .fav-header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        align-items: center;
    }

    .fav-pill {
        font-size: .75rem;
        padding: .15rem .6rem;
        border-radius: 999px;
        background: #eff6ff;
        color: #1d4ed8;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
    }

    .fav-grid {
        display: grid;
        grid-template-columns: minmax(0, 280px) minmax(0, 1fr);
        gap: 1rem;
    }

    /* LEFT SIDEBAR */
    .fav-sidebar {
        border-radius: 18px;
        border: 1px solid rgba(148,163,184,.35);
        background: #0b1120;
        color: #e5e7eb;
        padding: .9rem .9rem 1rem;
        min-height: 420px;
        display: flex;
        flex-direction: column;
    }

    .fav-sidebar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .6rem;
        margin-bottom: .6rem;
    }

    .fav-sidebar-header h5 {
        margin: 0;
        font-size: .9rem;
    }

    .fav-sidebar-header small {
        font-size: .7rem;
        color: #9ca3af;
    }

    .fav-sidebar-tabs {
        display: flex;
        border-radius: 999px;
        background: rgba(15,23,42,.7);
        padding: .15rem;
        margin-bottom: .6rem;
    }

    .fav-sidebar-tab {
        flex: 1;
        font-size: .75rem;
        border-radius: 999px;
        padding: .25rem .4rem;
        text-align: center;
        cursor: pointer;
        color: #9ca3af;
    }

    .fav-sidebar-tab.active {
        background: #1f2937;
        color: #e5e7eb;
        font-weight: 600;
    }

    .fav-sidebar-search {
        margin-bottom: .6rem;
    }

    .fav-sidebar-search input {
        background: #020617;
        border-radius: 999px;
        border: 1px solid rgba(148,163,184,.5);
        color: #e5e7eb;
        font-size: .78rem;
    }

    .fav-sidebar-folders {
        flex: 1;
        overflow-y: auto;
        padding-right: .15rem;
    }

    .fav-folder {
        display: flex;
        gap: .6rem;
        padding: .45rem .5rem;
        border-radius: 12px;
        cursor: pointer;
        align-items: center;
        transition: background .12s ease-out, transform .12s;
    }

    .fav-folder:hover {
        background: rgba(15,23,42,.85);
        transform: translateY(-1px);
    }

    .fav-folder.active {
        background: linear-gradient(135deg, rgba(37,99,235,.12), rgba(59,130,246,.10));
        box-shadow: 0 0 0 1px rgba(59,130,246,.45);
    }

    .fav-folder-icon {
        width: 34px;
        height: 30px;
        flex-shrink: 0;
    }

    .fav-folder-title {
        font-size: .82rem;
        margin-bottom: 2px;
    }

    .fav-folder-meta {
        font-size: .7rem;
        color: #9ca3af;
    }

    .fav-folder-owner {
        font-size: .7rem;
        color: #cbd5f5;
    }

    .fav-folder-actions {
        margin-left: auto;
        display: flex;
        gap: .25rem;
        align-items: center;
    }

    .fav-folder-actions .btn {
        padding: 0;
        border-radius: 999px;
        width: 20px;
        height: 20px;
        line-height: 20px;
        font-size: .7rem;
    }

    .fav-sidebar-footer {
        margin-top: .6rem;
        font-size: .7rem;
        color: #9ca3af;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* RIGHT PANEL */
    .fav-main {
        display: flex;
        flex-direction: column;
        gap: .75rem;
    }

    .fav-cards-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: .6rem;
    }

    .fav-stat-card {
        border-radius: 14px;
        border: 1px solid rgba(148,163,184,.20);
        padding: .55rem .7rem;
        background: #f9fafb;
        display: flex;
        align-items: center;
        gap: .45rem;
    }

    .fav-stat-icon {
        width: 30px;
        height: 30px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #e5edff;
        flex-shrink: 0;
    }

    .fav-stat-icon i {
        font-size: 1.1rem;
    }

    .fav-stat-text small {
        font-size: .7rem;
        color: #6b7280;
    }

    .fav-stat-text strong {
        display: block;
        font-size: .95rem;
    }

    .fav-main-header {
        display: flex;
        flex-wrap: wrap;
        gap: .45rem .75rem;
        align-items: center;
        justify-content: space-between;
    }

    .fav-main-header-left span {
        display: block;
        font-size: .75rem;
        color: var(--fav-muted);
    }

    .fav-main-header-left h4 {
        margin: 0;
        font-size: 1rem;
    }

    .fav-main-filters {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
        align-items: center;
    }

    .fav-main-filters select,
    .fav-main-filters input {
        font-size: .78rem;
        height: 30px;
    }

    .fav-main-body {
        border-radius: 14px;
        border: 1px solid rgba(148,163,184,.20);
        background: #ffffff;
        padding: .5rem .7rem;
        min-height: 260px;
    }

    .fav-products-table {
        font-size: .78rem;
    }

    .fav-products-table thead th {
        font-size: .74rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #6b7280;
        border-bottom-width: 1px;
    }

    .fav-products-table tbody td {
        vertical-align: middle;
    }

    .fav-badge-brand {
        font-size: .7rem;
        border-radius: 999px;
        padding: .05rem .45rem;
        background: #eff6ff;
        color: #1e40af;
    }

    .fav-products-empty {
        text-align: center;
        padding: 2rem 0;
        color: #9ca3af;
        font-size: .8rem;
    }

    .fav-pagination {
        margin-top: .4rem;
        font-size: .78rem;
    }

    @media (max-width: 991.98px) {
        .fav-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    .fav-add-bar .list-group-item {
        padding: .25rem .5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .fav-add-bar .list-group-item small {
        color: #6b7280;
    }

</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>

    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-8 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">Produkt-Favoritenlisten</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Favoriten / Sammlungen</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body">
            <div class="favorite-layout">

                <div class="fav-header">
                    <div>
                        <h2>Sammlungen & Favoriten</h2>
                        <small>Strukturieren Sie Ihre Lieblingsprodukte in Ordnern – für Angebote, Projekte oder Sets.</small>
                    </div>
                    <div class="fav-header-actions">
                        <span class="fav-pill">
                            <i class="feather icon-folder"></i>
                            <span><span id="fav-my-count-label">0</span> eigene Ordner</span>
                        </span>
                        <span class="fav-pill">
                            <i class="feather icon-users"></i>
                            <span><span id="fav-other-count-label">0</span> freigegeben</span>
                        </span>
                        <button type="button" class="btn btn-sm btn-primary" id="fav-create-list-btn">
                            <i class="feather icon-plus mr-25"></i> Ordner anlegen
                        </button>
                    </div>
                </div>

                <div class="fav-grid">
                    {{-- LEFT: Sidebar with folders --}}
                    <aside class="fav-sidebar">
                        <div class="fav-sidebar-header">
                            <div>
                                <h5>Favoriten-Ordner</h5>
                                <small>Eigene & freigegebene Sammlungen.</small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-light" id="fav-refresh-btn" title="Aktualisieren">
                                <i class="feather icon-rotate-cw"></i>
                            </button>
                        </div>

                        <div class="fav-sidebar-tabs">
                            <div class="fav-sidebar-tab active" data-tab="mine">Meine Listen</div>
                            <div class="fav-sidebar-tab" data-tab="others">Team / Andere</div>
                        </div>

                        <div class="fav-sidebar-search">
                            <input type="text" id="fav-search-lists" class="form-control form-control-sm"
                                   placeholder="Ordner suchen...">
                        </div>

                        <div id="fav-sidebar-folders" class="fav-sidebar-folders">
                            {{-- filled via AJAX partial favorite_lists_sidebar --}}
                        </div>

                        <div class="fav-sidebar-footer">
                            <span id="fav-sidebar-footer-text">Keine Auswahl</span>
                            <button type="button" class="btn btn-sm btn-outline-light" id="fav-edit-list-btn" disabled>
                                <i class="feather icon-edit"></i>
                            </button>
                        </div>
                    </aside>

                    {{-- RIGHT: Main content with info cards + product list --}}
                    <section class="fav-main">
                        <div id="fav-stats-cards" class="fav-cards-row">
                            {{-- filled via AJAX partial favorite_lists_stats --}}
                        </div>

                        <div class="fav-main-header">
                            <div class="fav-main-header-left">
                                <span id="fav-selected-folder-label">Kein Ordner ausgewählt</span>
                                <h4 id="fav-selected-folder-name"></h4>
                            </div>
                            <div class="fav-main-filters">
                                <input type="text" id="fav-products-search" class="form-control form-control-sm"
                                       placeholder="Produkte in diesem Ordner suchen...">
                                <select id="fav-products-sort" class="form-control form-control-sm">
                                    <option value="products.product|asc">Name A–Z</option>
                                    <option value="products.product|desc">Name Z–A</option>
                                    <option value="products.article_no|asc">Art.Nr. ↑</option>
                                    <option value="products.article_no|desc">Art.Nr. ↓</option>
                                    <option value="brand_name|asc">Hersteller A–Z</option>
                                    <option value="brand_name|desc">Hersteller Z–A</option>
                                    <option value="added_at|desc">Zuletzt hinzugefügt</option>
                                    <option value="added_at|asc">Älteste zuerst</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="fav-products-reset-btn">
                                    Filter zurücksetzen
                                </button>
                            </div>
                        </div>

                        <div class="fav-main-body">

                            <div class="fav-add-bar mb-50">
                                <div class="input-group input-group-sm">
                                    <input type="text" id="fav-add-product-query" class="form-control"
                                        placeholder="Produkt nach Name / Art.Nr. suchen und zur Liste hinzufügen...">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-primary" type="button" id="fav-add-product-search-btn">
                                            <i class="feather icon-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div id="fav-add-product-results" class="mt-50" style="max-height:200px; overflow-y:auto; font-size:.78rem;"></div>
                            </div>

                            <div id="fav-products-list">
                                <div class="fav-products-empty">
                                    Bitte wählen Sie links einen Ordner aus, um die Produkte zu sehen.
                                </div>
                            </div>
                            <div id="fav-products-pagination" class="fav-pagination"></div>
                        </div>
                    </section>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Simple modal for create/edit folder --}}
<div class="modal fade" id="favListModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="fav-list-form" class="modal-content">
            @csrf
            <input type="hidden" name="_method" id="fav_list_method" value="POST">
            <input type="hidden" id="fav_list_id">

            <div class="modal-header">
                <h5 class="modal-title" id="fav-list-modal-title">Ordner anlegen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Ordnername</label>
                    <input type="text" name="name" id="fav_list_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Beschreibung</label>
                    <textarea name="description" id="fav_list_description" class="form-control" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label>Farbe (optional)</label>
                    <input type="text" name="color" id="fav_list_color" class="form-control" placeholder="#93c21c">
                </div>
                <div class="form-group">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="fav_list_shared" name="is_shared">
                        <label class="custom-control-label" for="fav_list_shared">Für andere freigeben</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="fav-delete-list-btn" class="btn btn-outline-danger mr-auto d-none">
                    <i class="feather icon-trash"></i> Ordner löschen
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                <button type="submit" class="btn btn-primary">
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

        // Base URLs from Laravel (respecting /admin prefix)
        const listsUrl          = '{{ route("ajax.products.favorite-lists") }}';          // /admin/ajax/products/favorite-lists
        const listsStoreUrl     = '{{ route("ajax.products.favorite-lists.store") }}';    // POST store
        const favBaseUrl        = '{{ url("admin/ajax/products/favorite-lists") }}';      // /admin/ajax/products/favorite-lists
        const productSearchUrl  = '{{ route("ajax.products.search") }}';                  // /admin/ajax/products/search

        let currentListId   = null;
        let currentListsTab = 'mine'; // 'mine' | 'others'

        /**
         * Load sidebar lists + stats
         */
        function loadLists() {
            const search  = $('#fav-search-lists').val() || '';
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
                    $('#fav-sidebar-folders').html(res.lists_html || '');
                    $('#fav-stats-cards').html(res.stats_html || '');

                    // top header counters
                    const $my  = $('#fav-my-count');
                    const $oth = $('#fav-other-count');
                    if ($my.length && $oth.length) {
                        $('#fav-my-count-label').text($my.data('count') || 0);
                        $('#fav-other-count-label').text($oth.data('count') || 0);
                    }

                    // restore selection
                    if (currentListId) {
                        const $folder = $('.fav-folder[data-list-id="' + currentListId + '"]');
                        if ($folder.length) {
                            $folder.addClass('active');
                            $('#fav-sidebar-footer-text').text($folder.data('fullname') || '');
                            $('#fav-edit-list-btn').prop('disabled', false);
                        } else {
                            resetSelection();
                        }
                    } else {
                        resetSelection();
                    }

                    filterSidebarByTab();
                },
                error: function () {
                    toastr.error('Ordner konnten nicht geladen werden.');
                }
            });
        }

        /**
         * Reset right panel + footer when nothing selected
         */
        function resetSelection() {
            currentListId = null;
            $('.fav-folder').removeClass('active');
            $('#fav-sidebar-footer-text').text('Keine Auswahl');
            $('#fav-edit-list-btn').prop('disabled', true);
            $('#fav-selected-folder-label').text('Kein Ordner ausgewählt');
            $('#fav-selected-folder-name').text('');
            $('#fav-products-list').html(
                '<div class="fav-products-empty">Bitte wählen Sie links einen Ordner aus, um die Produkte zu sehen.</div>'
            );
            $('#fav-products-pagination').empty();
        }

        /**
         * Show only "mine" or "others" in sidebar based on active tab
         */
        function filterSidebarByTab() {
            $('.fav-folder').each(function () {
                const isMine  = $(this).data('mine') === 1 || $(this).data('mine') === '1';
                const isOther = !isMine;

                if (currentListsTab === 'mine' && !isMine) {
                    $(this).hide();
                } else if (currentListsTab === 'others' && !isOther) {
                    $(this).hide();
                } else {
                    $(this).show();
                }
            });
        }

        /**
         * Resolve products URL, respecting /admin prefix and pagination URLs
         */
        function buildProductsUrl(listId, pageUrl) {
            if (pageUrl) {
                // pagination links already contain full URL from Laravel
                return pageUrl;
            }
            return favBaseUrl + '/' + listId + '/products';
        }

        /**
         * Load products of a list (right panel)
         */
        function loadProducts(listId, pageUrl) {
            if (!listId) return;

            const sortVal = $('#fav-products-sort').val() || 'products.product|asc';
            const [sort_by, sort_dir] = sortVal.split('|');
            const search = $('#fav-products-search').val() || '';

            const ajaxUrl = buildProductsUrl(listId, pageUrl);

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
                    $('#fav-products-list').html(res.html || '');
                    $('#fav-products-pagination').html(res.pagination || '');
                },
                error: function () {
                    toastr.error('Produkte konnten nicht geladen werden.');
                }
            });
        }

        /**
         * Modal: create new folder
         */
        function openCreateModal() {
            $('#fav_list_id').val('');
            $('#fav_list_method').val('POST');
            $('#fav-list-modal-title').text('Ordner anlegen');
            $('#fav_list_name').val('');
            $('#fav_list_description').val('');
            $('#fav_list_color').val('');
            $('#fav_list_shared').prop('checked', true);
            $('#fav-delete-list-btn').addClass('d-none');
            $('#favListModal').modal('show');
        }

        /**
         * Modal: edit existing folder
         */
        function openEditModal(listId) {
            const $folder = $('.fav-folder[data-list-id="' + listId + '"]');
            if (!$folder.length) return;

            $('#fav_list_id').val(listId);
            $('#fav_list_method').val('PUT');
            $('#fav-list-modal-title').text('Ordner bearbeiten');
            $('#fav_list_name').val($folder.data('name') || '');
            $('#fav_list_description').val($folder.data('description') || '');
            $('#fav_list_color').val($folder.data('color') || '');
            $('#fav_list_shared').prop('checked', $folder.data('shared') == 1);
            $('#fav-delete-list-btn').removeClass('d-none');
            $('#favListModal').modal('show');
        }

        /**
         * Save folder (create/update)
         */
        function submitListForm(e) {
            e.preventDefault();

            const id     = $('#fav_list_id').val();
            const method = $('#fav_list_method').val() || 'POST';
            let   url    = listsStoreUrl;

            if (method === 'PUT' && id) {
                url = favBaseUrl + '/' + id;
            }

            const data = {
                name:        $('#fav_list_name').val(),
                description: $('#fav_list_description').val(),
                color:       $('#fav_list_color').val(),
                is_shared:   $('#fav_list_shared').is(':checked') ? 1 : 0,
                _method:     method,
                _token:      '{{ csrf_token() }}'
            };

            $.ajax({
                url:  url,
                type: 'POST',
                data: data,
                success: function (res) {
                    toastr.success(res.message || 'Gespeichert');
                    $('#favListModal').modal('hide');
                    loadLists();
                },
                error: function () {
                    toastr.error('Fehler beim Speichern.');
                }
            });
        }

        /**
         * Delete currently edited folder (from modal)
         */
        function deleteCurrentList() {
            const id = $('#fav_list_id').val();
            if (!id) return;

            if (!confirm('Diesen Ordner wirklich löschen?')) return;

            $.ajax({
                url:  favBaseUrl + '/' + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token:  '{{ csrf_token() }}'
                },
                success: function (res) {
                    toastr.success(res.message || 'Ordner gelöscht');
                    $('#favListModal').modal('hide');
                    currentListId = null;
                    loadLists();
                },
                error: function () {
                    toastr.error('Löschen fehlgeschlagen.');
                }
            });
        }

        /**
         * Render search results for "Add product to list"
         */
        function renderSearchResults(list) {
            const $container = $('#fav-add-product-results');

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

                html +=
                    '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                        '<div>' +
                            '<div>' + label + '</div>' +
                            '<small>' + (row.category || '-') + '</small>' +
                        '</div>' +
                        '<button type="button" class="btn btn-sm btn-outline-success fav-add-product-btn" data-product-id="' + row.id + '">' +
                            '<i class="feather icon-plus"></i>' +
                        '</button>' +
                    '</li>';
            });
            html += '</ul>';

            $container.html(html);
        }

        /**
         * Search products (global) to add into current list
         */
        function searchProductsForAdd() {
            const q = $('#fav-add-product-query').val() || '';
            if (!q.length) {
                $('#fav-add-product-results').empty();
                return;
            }

            $.ajax({
                url:  productSearchUrl,
                type: 'GET',
                dataType: 'json',
                data: { q: q },
                success: function (res) {
                    renderSearchResults(res || []);
                },
                error: function () {
                    toastr.error('Produktsuche fehlgeschlagen.');
                }
            });
        }

        /**
         * Attach product to current list (no duplicates – backend enforces unique index)
         */
        function addProductToCurrentList(productId) {
            if (!currentListId) {
                toastr.warning('Bitte zuerst links einen Ordner auswählen.');
                return;
            }

            $.ajax({
                url:  favBaseUrl + '/' + currentListId + '/products',
                type: 'POST',
                dataType: 'json',
                data: {
                    product_id: productId,
                    _token:     '{{ csrf_token() }}'
                },
                success: function (res) {
                    toastr.success(res.message || 'Produkt hinzugefügt.');
                    loadProducts(currentListId);
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

        $(function () {

            // initial load
            loadLists();

            // reload folders
            $('#fav-refresh-btn').on('click', function () {
                loadLists();
            });

            // search lists (enter)
            $('#fav-search-lists').on('keyup', function (e) {
                if (e.keyCode === 13) {
                    loadLists();
                }
            });

            // create folder
            $('#fav-create-list-btn').on('click', openCreateModal);

            // edit currently selected folder (footer button)
            $('#fav-edit-list-btn').on('click', function () {
                if (currentListId) {
                    openEditModal(currentListId);
                }
            });

            // modal form submit
            $('#fav-list-form').on('submit', submitListForm);

            // delete from modal
            $('#fav-delete-list-btn').on('click', deleteCurrentList);

            // tabs: mine / others
            $(document).on('click', '.fav-sidebar-tab', function () {
                $('.fav-sidebar-tab').removeClass('active');
                $(this).addClass('active');
                currentListsTab = $(this).data('tab');
                filterSidebarByTab();
            });

            // click on folder row (select list)
            $(document).on('click', '.fav-folder', function (e) {
                if ($(e.target).closest('.fav-folder-actions').length) {
                    return; // ignore when clicking edit/delete buttons
                }

                const id   = $(this).data('list-id');
                const name = $(this).data('name') || '';
                const full = $(this).data('fullname') || name;

                currentListId = id;

                $('.fav-folder').removeClass('active');
                $(this).addClass('active');

                $('#fav-sidebar-footer-text').text(full);
                $('#fav-edit-list-btn').prop('disabled', false);
                $('#fav-selected-folder-label').text('Aktive Liste');
                $('#fav-selected-folder-name').text(name);

                loadProducts(id);
            });

            // edit button inside folder row
            $(document).on('click', '.fav-folder-edit', function (e) {
                e.stopPropagation();
                const id = $(this).closest('.fav-folder').data('list-id');
                openEditModal(id);
            });

            // delete button inside folder row
            $(document).on('click', '.fav-folder-delete', function (e) {
                e.stopPropagation();
                const id = $(this).closest('.fav-folder').data('list-id');
                if (!id) return;
                if (!confirm('Diesen Ordner wirklich löschen?')) return;

                $.ajax({
                    url:  favBaseUrl + '/' + id,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token:  '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        toastr.success(res.message || 'Ordner gelöscht');
                        if (currentListId == id) {
                            currentListId = null;
                        }
                        loadLists();
                    },
                    error: function () {
                        toastr.error('Löschen fehlgeschlagen.');
                    }
                });
            });

            // products filters (right panel)
            $('#fav-products-search').on('keyup', function (e) {
                if (e.keyCode === 13 && currentListId) {
                    loadProducts(currentListId);
                }
            });

            $('#fav-products-sort').on('change', function () {
                if (currentListId) {
                    loadProducts(currentListId);
                }
            });

            $('#fav-products-reset-btn').on('click', function () {
                $('#fav-products-search').val('');
                $('#fav-products-sort').val('products.product|asc');
                if (currentListId) {
                    loadProducts(currentListId);
                }
            });

            // pagination for products
            $(document).on('click', '#fav-products-pagination a', function (e) {
                e.preventDefault();
                const href = $(this).attr('href');
                if (!href || !currentListId) return;
                loadProducts(currentListId, href);
            });

            // search products to add
            $('#fav-add-product-search-btn').on('click', function () {
                searchProductsForAdd();
            });

            $('#fav-add-product-query').on('keyup', function (e) {
                if (e.keyCode === 13) {
                    searchProductsForAdd();
                }
            });

            // click "add" in search results
            $(document).on('click', '.fav-add-product-btn', function () {
                const productId = $(this).data('product-id');
                addProductToCurrentList(productId);
            });

            // remove product from list
            $(document).on('click', '.fav-product-remove', function () {
                const $row   = $(this).closest('tr');
                const itemId = $row.data('item-id');
                if (!itemId || !currentListId) return;

                if (!confirm('Produkt aus diesem Ordner entfernen?')) return;

                $.ajax({
                    url:  favBaseUrl + '/' + currentListId + '/products/' + itemId,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _method: 'DELETE',
                        _token:  '{{ csrf_token() }}'
                    },
                    success: function (res) {
                        toastr.success(res.message || 'Entfernt.');
                        loadProducts(currentListId);
                        loadLists();
                    },
                    error: function () {
                        toastr.error('Entfernen fehlgeschlagen.');
                    }
                });
            });

            // select-all checkboxes (for future bulk actions)
            $(document).on('change', '#fav-products-check-all', function () {
                $('.fav-product-check').prop('checked', $(this).is(':checked'));
            });
        });

    })(jQuery);
</script>

@endsection
