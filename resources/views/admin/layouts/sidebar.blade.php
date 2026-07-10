<style>
    .sa-sidebar-right {
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        gap: 6px;
        flex-shrink: 0;
        margin-left: auto;
    }

    .sa-sidebar-count {
        min-width: 18px;
        height: 18px;
        padding: 0 6px;
        border-radius: 999px;
        background: rgba(229, 6, 86, .12);
        color: #e50656;
        border: 1px solid rgba(229, 6, 86, .18);
        font-size: 10px;
        font-weight: 950;
        line-height: 16px;
        text-align: center;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 5px 12px rgba(229, 6, 86, .08);
        transition:
            opacity .18s ease,
            transform .18s ease,
            background-color .18s ease,
            color .18s ease,
            border-color .18s ease;
    }

    .sa-sidebar-count.is-empty {
        display: none !important;
    }

    .sa-sidebar-count.is-hot {
        background: rgba(239, 68, 68, .13);
        color: #ef4444;
        border-color: rgba(239, 68, 68, .25);
    }

    .sa-sidebar-count.is-info {
        background: rgba(116, 178, 212, .14);
        color: var(--brand-blue);
        border-color: rgba(116, 178, 212, .25);
    }

    .sa-sidebar-count.is-success {
        background: rgba(147, 194, 28, .16);
        color: #6c970f;
        border-color: rgba(147, 194, 28, .28);
    }

    .sa-sidebar-count.is-pulse {
        animation: saSidebarCountPulse .45s ease;
    }

    @keyframes saSidebarCountPulse {
        0% {
            transform: scale(1);
        }

        45% {
            transform: scale(1.22);
        }

        100% {
            transform: scale(1);
        }
    }

    .nav-item .sa-sidebar-count,
    .submenu-link .sa-sidebar-count {
        margin-left: auto;
    }

    .submenu-link,
    .nav-item {
        gap: 8px;
    }

    .submenu-link {
        justify-content: space-between;
    }

    .submenu-link .nav-item-content,
    .nav-item .nav-item-content {
        min-width: 0;
    }

    .submenu-link .nav-item-content span,
    .nav-item .nav-item-content span {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    html.dark .sa-sidebar-count {
        background: rgba(229, 6, 86, .18);
        color: #fda4af;
        border-color: rgba(253, 164, 175, .25);
    }

    html.dark .sa-sidebar-count.is-info {
        background: rgba(116, 178, 212, .18);
        color: #bfdbfe;
        border-color: rgba(191, 219, 254, .22);
    }

    html.dark .sa-sidebar-count.is-success {
        background: rgba(147, 194, 28, .18);
        color: #d9f99d;
        border-color: rgba(217, 249, 157, .22);
    }
</style>

<style>
    /*
    |--------------------------------------------------------------------------
    | SA compact sidebar wording + colored icons
    |--------------------------------------------------------------------------
    */
    .sidebar-nav {
        font-size: 12px !important;
    }

    .nav-item {
        min-height: 32px !important;
        padding: 7px 9px !important;
        font-size: 12px !important;
        font-weight: 850 !important;
        border-radius: 9px !important;
    }

    .submenu-link {
        min-height: 28px !important;
        padding: 6px 9px !important;
        font-size: 11px !important;
        font-weight: 750 !important;
        border-radius: 8px !important;
    }

    .submenu-inner {
        padding-left: 28px !important;
        gap: 2px !important;
    }

    .sa-section-toggle {
        min-height: 30px !important;
        padding: 6px 8px !important;
        font-size: 10px !important;
        letter-spacing: .04em !important;
    }

    .sa-section-custom-icon {
        width: 16px !important;
        height: 16px !important;
        flex-basis: 16px !important;
    }

    .icon-md {
        width: 15px !important;
        height: 15px !important;
    }

    .icon-sm {
        width: 13px !important;
        height: 13px !important;
    }

    .sa-sidebar-count {
        min-width: 17px !important;
        height: 17px !important;
        padding: 0 5px !important;
        font-size: 9px !important;
        line-height: 15px !important;
    }

    .sa-sidebar-section {
        margin: 4px 0 !important;
    }

    .sa-icon-blue {
        color: #74b2d4 !important;
        stroke: currentColor !important;
    }

    .sa-icon-green {
        color: #93c21c !important;
        stroke: currentColor !important;
    }

    .sa-icon-orange {
        color: #f8ac00 !important;
        stroke: currentColor !important;
    }

    .sa-icon-pink {
        color: #e50656 !important;
        stroke: currentColor !important;
    }

    .sa-icon-purple {
        color: #8b5cf6 !important;
        stroke: currentColor !important;
    }

    .sa-icon-cyan {
        color: #06b6d4 !important;
        stroke: currentColor !important;
    }

    .sa-icon-slate {
        color: #64748b !important;
        stroke: currentColor !important;
    }

    html.dark .sa-icon-blue {
        color: #93c5fd !important;
    }

    html.dark .sa-icon-green {
        color: #bef264 !important;
    }

    html.dark .sa-icon-orange {
        color: #fbbf24 !important;
    }

    html.dark .sa-icon-pink {
        color: #fb7185 !important;
    }

    html.dark .sa-icon-purple {
        color: #c4b5fd !important;
    }

    html.dark .sa-icon-cyan {
        color: #67e8f9 !important;
    }

    html.dark .sa-icon-slate {
        color: #cbd5e1 !important;
    }
</style>

@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Str;
    use Illuminate\Support\Facades\Route;

    /*
    |--------------------------------------------------------------------------
    | Auth / Employee
    |--------------------------------------------------------------------------
    | Important:
    | In this app:
    | users.id   = Laravel auth user id
    | users.name = employees.id
    | user_rolls.user_id = users.id (FK auf users)
    |--------------------------------------------------------------------------
    */
    $authUser = auth()->user();

    $userId = $authUser?->id;
    $employeeId = $authUser?->name;
    $user_id = auth()->user()->id;

    /*
    |--------------------------------------------------------------------------
    | Safe Route Helper
    |--------------------------------------------------------------------------
    */
    $safeRoute = function (string $name, $fallback = '#', array $params = []) {
        try {
            return Route::has($name)
                ? route($name, $params)
                : ($fallback === '#' ? '#' : url($fallback));
        } catch (\Throwable $e) {
            return $fallback === '#' ? '#' : url($fallback);
        }
    };

    /*
    |--------------------------------------------------------------------------
    | Permission Helpers
    |--------------------------------------------------------------------------
    */
    $truthyPermission = function ($query, string $column) {
        $query->where($column, 1)
            ->orWhere($column, '1')
            ->orWhere($column, 'on')
            ->orWhere($column, true);
    };

    $hasPermission = function (?string $itemId, string $permission = 'is_read') use ($userId, $authUser, $truthyPermission) {
        if (!$itemId) {
            return true;
        }

        // Super-Admin (is_admin) sieht alle Menüpunkte.
        if ($authUser && $authUser->is_admin) {
            return true;
        }

        if (!$userId) {
            return false;
        }

        return DB::table('user_rolls')
            ->where('user_id', $userId)
            ->where('item_id', $itemId)
            ->where(function ($query) use ($permission, $truthyPermission) {
                $truthyPermission($query, $permission);
            })
            ->exists();
    };

    /*
    |--------------------------------------------------------------------------
    | Special Permissions
    |--------------------------------------------------------------------------
    */
    // Spezial-Rechte: user_rolls.user_id = users.id; Super-Admins haben alles.
    $isSuperAdmin = (bool) ($authUser?->is_admin);
    $canSalary = $isSuperAdmin;
    $canDeleteGarbage = $isSuperAdmin;

    if (!$isSuperAdmin && $userId) {
        $canSalary = DB::table('user_rolls')
            ->where('user_id', $userId)
            ->where('item_id', 'Super')
            ->where(function ($query) use ($truthyPermission) {
                $truthyPermission($query, 'is_add');
            })
            ->exists();

        $canDeleteGarbage = DB::table('user_rolls')
            ->where('user_id', $userId)
            ->where('item_id', 'Administrator')
            ->where(function ($query) use ($truthyPermission) {
                $truthyPermission($query, 'is_delete');
            })
            ->exists();
    }

    $canSeeAllReports = $isSuperAdmin;

    if (!$isSuperAdmin && $userId) {
        $canSeeAllReports = DB::table('user_rolls')
            ->where('user_id', $userId)
            ->where('item_id', 'Administrator')
            ->where(function ($query) use ($truthyPermission) {
                $truthyPermission($query, 'is_read');
            })
            ->exists();
    }
    /*
    |--------------------------------------------------------------------------
    | Current lead history URL
    |--------------------------------------------------------------------------
    | The global sidebar is used on many pages. On a customer profile/history page
    | we can open the exact customer history. Otherwise we fall back to customer list.
    |--------------------------------------------------------------------------
    */
    $currentLeadHistoryCustomerId = null;

    try {
        if (isset($customer) && is_object($customer) && !empty($customer->id)) {
            $currentLeadHistoryCustomerId = $customer->id;
        } elseif (request()->route('customer')) {
            $routeCustomer = request()->route('customer');
            $currentLeadHistoryCustomerId = is_object($routeCustomer) ? ($routeCustomer->id ?? null) : $routeCustomer;
        } elseif (request()->id) {
            $currentLeadHistoryCustomerId = request()->id;
        } elseif (request()->customer_id) {
            $currentLeadHistoryCustomerId = request()->customer_id;
        }
    } catch (\Throwable $e) {
        $currentLeadHistoryCustomerId = null;
    }

    $leadHistorySidebarUrl = Route::has('customers.history.show') && $currentLeadHistoryCustomerId
        ? route('customers.history.show', $currentLeadHistoryCustomerId)
        : url('new_lead_view');

    /*
    |--------------------------------------------------------------------------
    | Sidebar Structure
    |--------------------------------------------------------------------------
    | count_key values are filled by JS using /api/sidebar-counts
    |--------------------------------------------------------------------------
    */
    $sidebarSections = [
        // ============================================================================
        // NAV-01 v2 (Phase II · Commit 1): strikt ZWEI Ebenen — keine doppelten Aufklapp-Labels.
        //   Ebene 1 = Bereich (aufklappbare Sektion, EIN Wort)  = Haupt-Navi
        //   Ebene 2 = flache Links (KEIN 'children'/'submenu')  = Unter-Navi
        // Ganze Bereiche gaten ueber Sektions-'permission' (Filter-Map unten). Finance bleibt Item-Gate.
        // Section-Gate NUR wo ALT es hatte; Artikel-Daten/Lager/HR-Daten/Phasen/Stammdaten/Filialen/Planung
        // waren in ALT UNGEGATET -> bleiben ungegatet (keine Verengung).
        // nur-URL (nicht in Sidebar): offers/wizard (durch wizard-smart ersetzt), Rechnungen-Canvas,
        // Buchhaltungs-Suite (Weiche 3 eingefroren). Junk/Papierkorb = letzte Links ihres Bereichs.
        // ============================================================================
        [
            'title' => 'Arbeitsbereich',
            'items' => [
                ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'tone' => 'text-brand-blue', 'url' => url('/'), 'active_routes' => ['/']],
                ['label' => 'Was jetzt?', 'icon' => 'inbox', 'tone' => 'text-brand-blue', 'url' => $safeRoute('admin.arbeitsliste', 'arbeitsliste'), 'active_routes' => ['/arbeitsliste']],
                ['label' => 'Lead-Kanban', 'icon' => 'kanban', 'tone' => 'text-brand-blue', 'url' => url('lead/kanban'), 'active_routes' => ['/lead/kanban']],
                ['label' => 'Meine Aufgaben', 'icon' => 'check-square', 'tone' => 'text-brand-green', 'url' => url('personal/task/' . $employeeId), 'count_key' => 'tasks', 'active_routes' => ['/personal/task', '/admin/todo/personal']],
                ['label' => 'Tagesberichte', 'icon' => 'file-clock', 'url' => $safeRoute('employee.daily.plan'), 'count_key' => 'daily_reports'],
                ['label' => 'Mein Kalender', 'icon' => 'calendar', 'url' => url('tasks/calendar/personal'), 'active_routes' => ['/tasks/calendar/personal']],
                ['label' => 'Terminübersicht', 'icon' => 'calendar-check', 'url' => url('customer/appointments'), 'count_key' => 'appointments', 'active_routes' => ['/customer/appointments']],
                ['label' => 'Persönliche Notizen', 'icon' => 'list-checks', 'tone' => 'text-brand-blue', 'url' => $safeRoute('notes.details'), 'count_key' => 'personal_notes'],
            ],
        ],
        [
            'title' => 'Anfragen',
            'permission' => 'Inquiry',
            'items' => [
                ['label' => 'Neue Anfrage', 'icon' => 'plus-circle', 'url' => $safeRoute('inquiry.create', 'inquiry_create'), 'active_routes' => ['/inquiry_create', '/inquiries/create']],
                ['label' => 'Meine Anfragen', 'icon' => 'user-check', 'url' => $safeRoute('my.inquiry.view'), 'count_key' => 'my_inquiries_unpublished', 'active_routes' => ['/my-inquiry', '/my_inquiry']],
                ['label' => 'Kundenanfragen', 'icon' => 'users', 'url' => $safeRoute('inquiry.customer'), 'count_key' => 'customer_inquiries_unpublished', 'active_routes' => ['/inquiry/customer', '/customer-inquiries']],
                ['label' => 'Veröffentlichte Anfragen', 'icon' => 'check-circle', 'url' => $safeRoute('inquiry.published.list'), 'count_key' => 'inquiries_published', 'active_routes' => ['/inquiry_published_list', '/published-inquiries']],
                ['label' => 'Website-Leads', 'icon' => 'globe', 'url' => $safeRoute('fusion.forms.index'), 'count_key' => 'website_leads', 'active_routes' => ['/fusion/forms']],
                ['label' => 'Junk', 'icon' => 'alert-triangle', 'url' => url('inquiry_junklist'), 'count_key' => 'inquiries_junk', 'active_routes' => ['/inquiry_junklist']],
                ['label' => 'Papierkorb', 'icon' => 'trash-2', 'url' => url('inquiry_deleted_list'), 'count_key' => 'inquiries_deleted', 'active_routes' => ['/inquiry_deleted_list']],
            ],
        ],
        [
            'title' => 'Leads',
            'permission' => 'Customer',
            'items' => [
                ['label' => 'Neuer Lead', 'icon' => 'user-plus', 'url' => url('new_lead_create'), 'active_routes' => ['/new_lead_create']],
                ['label' => 'Leadliste', 'icon' => 'list', 'url' => url('new_lead_view'), 'count_key' => 'customers', 'active_routes' => ['/new_lead_view', '/customer/profile', '/lead/profile']],
                ['label' => 'Kundenakte', 'icon' => 'clock', 'url' => $leadHistorySidebarUrl, 'active_routes' => ['/customers/*/history', '/customers/history']],
                // Commit 2: Tippfehler-Pfad entschaerft -> Route-Name (waiting.loop.leads); Pfad in web.php auf /waiting_leads korrigiert (Alt-Pfad redirected)
                ['label' => 'Warteschleife', 'icon' => 'clock', 'url' => $safeRoute('waiting.loop.leads', 'waiting_leads'), 'count_key' => 'customers_waiting', 'active_routes' => ['/wating_leads', '/waiting_leads']],
                ['label' => 'Junk', 'icon' => 'alert-triangle', 'url' => url('lead_junks'), 'count_key' => 'customers_junk', 'active_routes' => ['/lead_junks']],
                ['label' => 'Gelöscht', 'icon' => 'trash-2', 'url' => url('deleted_leads'), 'count_key' => 'customers_deleted', 'active_routes' => ['/deleted_leads']],
            ],
        ],
        [
            'title' => 'Kontakte',
            'permission' => 'Partner',
            'items' => [
                // NAV Phase III (Fläche 1, Design A): 8 Kontakt-Typen -> Tab-Leiste (admin/contacts/_tabs).
                // Sidebar 8 -> 1; Alt-Routen bleiben live (= die Tabs), aktiv bei jedem Tab via active_routes.
                ['label' => 'Alle Kontakte', 'icon' => 'list', 'url' => $safeRoute('all.contacts', 'all-contacts'), 'count_key' => 'contacts', 'active_routes' => ['/all-contacts', '/brand', '/brands', '/distributors', '/external_personal']],
            ],
        ],
        [
            'title' => 'Kommunikation',
            'permission' => 'Email',
            'items' => [
                ['label' => 'Posteingang', 'icon' => 'inbox', 'url' => url('/email_view'), 'active_routes' => ['/email_view']],
                ['label' => 'Lead E-Mails', 'icon' => 'mail-open', 'url' => $safeRoute('lead.email.inbox'), 'active_routes' => ['/lead-email', '/lead/emails']],
                // Commit 2: von Legacy chats.view (MessageController) auf modernen chat.index (Chat\ChatController) umgehaengt
                ['label' => 'Bitrix24-Chat', 'icon' => 'message-square', 'url' => $safeRoute('chat.index', 'admin/chat'), 'count_key' => 'chat_unread', 'active_routes' => ['/admin/chat', '/chats']],
            ],
        ],
        [
            'title' => 'Angebote',
            'items' => [
                ['label' => 'Angebots-Assistent', 'icon' => 'cpu', 'url' => url('offers/wizard-smart'), 'active_routes' => ['/offers/wizard-smart']],
                ['label' => 'Übersicht', 'icon' => 'list', 'url' => url('admin/offers'), 'count_key' => 'offers', 'active_routes' => ['/admin/offers', '/offers/list']],
                ['label' => 'Vorlagen', 'icon' => 'file-text', 'url' => $safeRoute('offer-templates.index', 'offer-templates'), 'count_key' => 'offer_templates', 'active_routes' => ['/offer-templates']],
            ],
        ],
        [
            'title' => 'Aufträge',
            'items' => [
                ['label' => 'Übersicht', 'icon' => 'briefcase', 'url' => $safeRoute('deal.all.list'), 'count_key' => 'deals', 'active_routes' => ['/deal', '/deals', '/auftrag']],
                ['label' => 'Feinaufmaß-Kanban', 'icon' => 'ruler', 'url' => $safeRoute('deal.measurements.kanban', 'deal-measurements-kanban'), 'count_key' => 'deal_measurements', 'active_routes' => ['/deal-measurements-kanban', '/deal-measurements']],
                // Commit 2: Route-Name korrigiert (invoices.index existiert nicht -> admin.invoices.index); Invoice-Flaechen unberuehrt (Weiche 3)
                ['label' => 'Rechnungen', 'icon' => 'receipt-text', 'url' => $safeRoute('admin.invoices.index', 'admin/invoices'), 'count_key' => 'invoices', 'active_routes' => ['/admin/invoices', '/invoices/canvas']],
                ['label' => 'Junk', 'icon' => 'slash', 'url' => $safeRoute('deal.junk.list'), 'count_key' => 'deals_junk'],
                ['label' => 'Gelöscht', 'icon' => 'trash', 'url' => $safeRoute('deal.delete.list'), 'count_key' => 'deals_deleted'],
            ],
        ],
        [
            'title' => 'Montage',
            'items' => array_values(array_filter([
                ['label' => 'Einsatzplan', 'icon' => 'calendar-range', 'tone' => 'text-brand-purple', 'url' => $safeRoute('planner.index', '/planner'), 'count_key' => 'planner_projects', 'active_routes' => ['/planner/projects']],
                ['label' => 'Allgemeine Aufgaben', 'icon' => 'users-round', 'tone' => 'text-brand-blue', 'url' => $safeRoute('general-tasks.index', 'general-tasks'), 'count_key' => 'general_tasks_open', 'active_routes' => ['/general-tasks']],
                $canSeeAllReports ? ['label' => 'Berichts-Übersicht', 'icon' => 'file-text', 'tone' => 'text-brand-blue', 'url' => url('admin/overdue-center/recent'), 'count_key' => 'reports_all_remaining', 'active_routes' => ['/admin/overdue-center/recent', '/overdue-center/recent']] : null,
                ['label' => 'Überfällige Berichte', 'icon' => 'clipboard-check', 'tone' => 'text-brand-green', 'url' => url('admin/reports'), 'count_key' => 'reports_my_remaining', 'active_routes' => ['/admin/reports', '/reports']],
            ])),
        ],
        [
            'title' => 'Artikel',
            'permission' => 'Product',
            'items' => [
                ['label' => 'Neuer Artikel', 'icon' => 'plus', 'url' => $safeRoute('product.create', 'product_create')],
                ['label' => 'Katalog', 'icon' => 'file', 'url' => $safeRoute('product.info'), 'count_key' => 'products', 'active_routes' => ['/product', '/product_view', '/products']],
                ['label' => 'Favoriten', 'icon' => 'star', 'url' => $safeRoute('product.favorites.index'), 'count_key' => 'product_favorites'],
                ['label' => 'Stamm-Listen', 'icon' => 'award', 'url' => $safeRoute('stamp.lists.index'), 'count_key' => 'stamp_favorites'],
                ['label' => 'Preisvergleich', 'icon' => 'layers', 'url' => $safeRoute('admin.products.difference')],
                ['label' => 'Master-Sets', 'icon' => 'shopping-cart', 'url' => $safeRoute('admin.master_sets.index'), 'count_key' => 'master_sets'],
                ['label' => 'Lieferanten-Schnittstellen', 'icon' => 'plug-zap', 'url' => $safeRoute('admin.supplier-connectors.index'), 'active_routes' => ['/admin/supplier-connectors']],
                ['label' => 'GC Online / IDS', 'icon' => 'arrow-up-circle', 'url' => $safeRoute('ids.search.form')],
            ],
        ],
        [
            'title' => 'Artikel-Daten',
            'items' => [
                ['label' => 'Einheiten', 'icon' => 'sliders-horizontal', 'url' => $safeRoute('measure.info'), 'count_key' => 'measures'],
                ['label' => 'Rabattgruppen', 'icon' => 'percent', 'url' => $safeRoute('discount_group.info'), 'count_key' => 'discount_groups'],
                ['label' => 'Artikel-Gruppen', 'icon' => 'layers', 'url' => $safeRoute('article_group.index'), 'count_key' => 'article_groups'],
                ['label' => 'Checklisten-Formulare', 'icon' => 'layout', 'url' => $safeRoute('product.formula.index'), 'count_key' => 'product_formulas'],
                ['label' => 'Anfragevorschläge', 'icon' => 'users', 'url' => $safeRoute('product.position.view'), 'count_key' => 'product_positions'],
            ],
        ],
        [
            'title' => 'Lager',
            'items' => [
                ['label' => 'Inventar', 'icon' => 'warehouse', 'url' => $safeRoute('inventory.index'), 'count_key' => 'inventory'],
                ['label' => 'Lieferscheine', 'icon' => 'file-text', 'url' => $safeRoute('delivery-notes.index'), 'count_key' => 'delivery_notes'],
                ['label' => 'Betriebsmittel', 'icon' => 'qr-code', 'url' => $safeRoute('handover.details.asset'), 'count_key' => 'assets'],
                ['label' => 'Übergaben', 'icon' => 'repeat', 'url' => $safeRoute('handover.details')],
                ['label' => 'Lagerausgaben', 'icon' => 'send', 'url' => $safeRoute('request.out.details'), 'count_key' => 'inventory_requests'],
                ['label' => 'Kaufanfragen', 'icon' => 'shopping-basket', 'url' => $safeRoute('purchase.request'), 'count_key' => 'purchase_requests'],
                ['label' => 'Maschinen & Fahrzeuge', 'icon' => 'car', 'url' => $safeRoute('machine.inventory'), 'count_key' => 'machines'],
            ],
        ],
        [
            'title' => 'Energie',
            'items' => [
                ['label' => 'PVGIS', 'icon' => 'sun', 'url' => $safeRoute('admin.pvgis.index'), 'active_routes' => ['/admin/pvgis']],
                ['label' => 'Wechselrichter-Auslegung', 'icon' => 'zap', 'url' => $safeRoute('energie.wr-auslegung'), 'active_routes' => ['/admin/energie/wr-auslegung']],
                ['label' => 'Wärmepumpen-Auslegung', 'icon' => 'flame', 'url' => $safeRoute('energie.wp-auslegung'), 'active_routes' => ['/admin/energie/wp-auslegung']],
                ['label' => 'Sanierungs-Wirtschaftlichkeit', 'icon' => 'trending-down', 'url' => $safeRoute('energie.sanierung'), 'active_routes' => ['/admin/energie/sanierung']],
                ['label' => 'Energiekonzept', 'icon' => 'file-check', 'url' => $safeRoute('energie.energiekonzept'), 'active_routes' => ['/admin/energie/energiekonzept']],
                ['label' => 'Grundriss-Editor', 'icon' => 'pen-tool', 'url' => $safeRoute('energie.grundriss'), 'active_routes' => ['/admin/energie/grundriss']],
                ['label' => 'Plan-Import', 'icon' => 'file-up', 'url' => $safeRoute('energie.plan-upload'), 'active_routes' => ['/admin/energie/plan-upload']],
                ['label' => 'Heizlast-Rechner', 'icon' => 'thermometer-sun', 'url' => $safeRoute('energie.heizlast'), 'active_routes' => ['/admin/energie/heizlast']],
                ['label' => 'Fußboden-Check', 'icon' => 'grid', 'url' => $safeRoute('energie.fussboden-check'), 'active_routes' => ['/admin/energie/fussboden-check']],
                ['label' => 'Materialliste', 'icon' => 'layers', 'url' => $safeRoute('energie.materialliste'), 'active_routes' => ['/admin/energie/materialliste']],
                ['label' => 'Heizkörper-Check', 'icon' => 'thermometer', 'url' => $safeRoute('radiator.config.view'), 'active_routes' => ['/radiator_config_view']],
                ['label' => 'Wirtschaftlichkeit', 'icon' => 'calculator', 'url' => $safeRoute('economic_calculations.index', 'admin/economic-calculations'), 'active_routes' => ['/admin/economic-calculations', '/profitability']],
                // Finance-Gate 1:1 erhalten (Item-Level)
                ['label' => 'Förderungen', 'icon' => 'file-text', 'permission' => 'Finance', 'url' => $safeRoute('foerderungen.index'), 'count_key' => 'fundings'],
            ],
        ],
        [
            'title' => 'Tickets',
            'permission' => 'Problem',
            'items' => [
                ['label' => 'Neues Ticket', 'icon' => 'plus', 'url' => url('problem_create'), 'active_routes' => ['/problem_create']],
                ['label' => 'Ticket-Übersicht', 'icon' => 'list', 'url' => url('problem_view'), 'count_key' => 'tickets_open', 'active_routes' => ['/problem_view', '/problem/profile']],
                ['label' => 'Fehlerkatalog', 'icon' => 'alert-circle', 'url' => url('error'), 'count_key' => 'errors', 'active_routes' => ['/error']],
            ],
        ],
        [
            'title' => 'Wartung',
            'items' => [
                ['label' => 'Wartungsverträge', 'icon' => 'folder-open', 'url' => url('admin/maintenance/contracts'), 'count_key' => 'maintenance_contracts'],
                ['label' => 'Wartungs-Checklisten', 'icon' => 'plus-circle', 'url' => $safeRoute('admin.maintenance_checklists.index') . '#new-checklist', 'count_key' => 'maintenance_checklists'],
            ],
        ],
        [
            'title' => 'Mitarbeiter',
            'permission' => 'Employee',
            'items' => array_values(array_filter([
                ['label' => 'Mitarbeiter anlegen', 'icon' => 'user-plus', 'url' => $safeRoute('emp.create', 'emp_create')],
                ['label' => 'Übersicht', 'icon' => 'users', 'url' => $safeRoute('emp.info'), 'count_key' => 'employees', 'active_routes' => ['/emp_info', '/employee_profile', '/employees']],
                ['label' => 'Zeitpläne', 'icon' => 'clock', 'url' => $safeRoute('time_management.slots'), 'count_key' => 'time_slots'],
                ['label' => 'Teams', 'icon' => 'layers', 'url' => $safeRoute('teams.index'), 'count_key' => 'teams'],
                ['label' => 'Arbeitsorte', 'icon' => 'map-pin', 'url' => $safeRoute('work.place.index'), 'count_key' => 'work_places'],
                ['label' => 'Anwesenheit', 'icon' => 'user-check', 'url' => $safeRoute('admin.attendance.analytics'), 'count_key' => 'attendance_today'],
                ['label' => 'Krankheit & Urlaub', 'icon' => 'activity', 'url' => $safeRoute('employee.sickness-holiday-analyser'), 'active_routes' => ['/employee/sickness-holiday-analyser']],
                $canSalary ? ['label' => 'Lohn & Vollkosten', 'icon' => 'calculator', 'url' => $safeRoute('salary.index'), 'count_key' => 'salaries'] : null,
            ])),
        ],
        [
            'title' => 'HR-Daten',
            'items' => [
                ['label' => 'Vertragstypen', 'icon' => 'file-signature', 'url' => $safeRoute('contract.type.info'), 'count_key' => 'contract_types'],
                ['label' => 'Sprachen', 'icon' => 'languages', 'url' => $safeRoute('language.info'), 'count_key' => 'languages'],
                ['label' => 'Länder & Nationalitäten', 'icon' => 'globe', 'url' => $safeRoute('country.info'), 'count_key' => 'countries'],
                ['label' => 'Gesetzliche Feiertage', 'icon' => 'calendar-days', 'url' => $safeRoute('public-holidays.index'), 'count_key' => 'public_holidays'],
                ['label' => 'Feiertagskalender', 'icon' => 'calendar', 'url' => $safeRoute('holiday.info'), 'count_key' => 'holidays'],
                ['label' => 'Urlaubsanspruch', 'icon' => 'calendar-check', 'url' => $safeRoute('leave.day.info'), 'count_key' => 'leave_days'],
                ['label' => 'Steuerklassen', 'icon' => 'percent', 'url' => $safeRoute('tax.info'), 'count_key' => 'taxes'],
            ],
        ],
        [
            'title' => 'Organisation',
            'permission' => 'Organization',
            'items' => [
                ['label' => 'Abteilungen', 'icon' => 'building', 'url' => $safeRoute('department.info'), 'count_key' => 'departments'],
                ['label' => 'Stellen & Qualifikationen', 'icon' => 'briefcase', 'url' => $safeRoute('position.index'), 'count_key' => 'positions'],
                ['label' => 'Organigramm', 'icon' => 'git-branch', 'url' => $safeRoute('department.organize')],
                ['label' => 'Stellenbesetzung', 'icon' => 'network', 'url' => $safeRoute('employee.organization.index', 'employee-organization'), 'count_key' => 'department_positions', 'active_routes' => ['/employee-organization']],
            ],
        ],
        [
            'title' => 'Phasen',
            'items' => [
                // Lead-Phasen-Verwaltung (HTML-Seite lead-stages.manage; NICHT die JSON-API lead-stages.index)
                ['label' => 'Lead-Phasen', 'icon' => 'flag', 'url' => $safeRoute('lead-stages.manage', 'admin/lead-stages/manage'), 'active_routes' => ['/admin/lead-stages/manage']],
                ['label' => 'Arbeitsschritte', 'icon' => 'clock', 'url' => $safeRoute('task_phase.index'), 'count_key' => 'task_phases'],
                ['label' => 'Projekt-Struktur', 'icon' => 'flag', 'url' => $safeRoute('stages.index'), 'count_key' => 'stages'],
            ],
        ],
        [
            'title' => 'Stammdaten',
            'items' => [
                ['label' => 'Notiz-Kategorien', 'icon' => 'folder', 'url' => $safeRoute('note.category.view'), 'count_key' => 'note_categories'],
                ['label' => 'Kalkulationssätze', 'icon' => 'calculator', 'url' => $safeRoute('admin.costing_sets.index')],
            ],
        ],
        [
            'title' => 'Filialen',
            'items' => [
                ['label' => 'Filialen', 'icon' => 'map-pin', 'url' => $safeRoute('branch.info'), 'count_key' => 'branches'],
                // Finance-Gate 1:1 erhalten (Item-Level)
                ['label' => 'Filial-Betriebskosten', 'icon' => 'receipt', 'permission' => 'Finance', 'url' => $safeRoute('branch.expense'), 'count_key' => 'branch_expenses'],
                ['label' => 'Ratenzahlungen', 'icon' => 'credit-card', 'permission' => 'Finance', 'url' => $safeRoute('assets.installment.show'), 'count_key' => 'installments'],
            ],
        ],
        [
            'title' => 'E-Mail-Einrichtung',
            'permission' => 'Email',
            'items' => [
                ['label' => 'E-Mail-Konten', 'icon' => 'settings', 'url' => url('/email_configuration'), 'active_routes' => ['/email_configuration']],
                ['label' => 'Lead-Konten', 'icon' => 'settings-2', 'url' => $safeRoute('lead-email-accounts.index'), 'active_routes' => ['/lead-email-accounts']],
                ['label' => 'Domain-Filter', 'icon' => 'filter', 'url' => $safeRoute('lead.email.domain.filters.index'), 'active_routes' => ['/lead-email-domain-filters', '/lead/email/domain/filters']],
            ],
        ],
        [
            'title' => 'System',
            'items' => [
                ['label' => 'Systemwarnung', 'icon' => 'triangle-alert', 'url' => $safeRoute('admin.system-warning.index')],
                ['label' => 'Feedback', 'icon' => 'info', 'url' => $safeRoute('system.feedback.index')],
                ['label' => 'KI-Wissen', 'icon' => 'book-open', 'url' => $safeRoute('admin.chat.learnings.index')],
                // Commit 2 (P0): Nav-Gate gesetzt — GarbageController prueft item_id='Administrator'; Nav gleichzieht (is_admin bypass bleibt)
                ['label' => 'Datenbankbereinigung', 'icon' => 'trash-2', 'permission' => 'Administrator', 'url' => $safeRoute('admin.garbage.index')],
            ],
        ],
        [
            'title' => 'Benutzer',
            'permission' => 'Users',
            'items' => [
                ['label' => 'Admin-Benutzer', 'icon' => 'shield-user', 'url' => url('/admin_user'), 'count_key' => 'admin_users'],
                ['label' => 'Eingeschränkte Benutzer', 'icon' => 'user-lock', 'url' => url('/limit_user'), 'count_key' => 'limited_users'],
                ['label' => 'Berechtigungen', 'icon' => 'settings', 'url' => $safeRoute('user-rolls.index'), 'count_key' => 'user_roles'],
                ['label' => 'Mein Profil', 'icon' => 'circle-user', 'url' => url('/user'), 'count_key' => 'users'],
            ],
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | System Menu
    |--------------------------------------------------------------------------
    */
    $systemMenus = array_values(array_filter([
        [
            'label' => 'Wissensdatenbank',
            'icon' => 'circle-help',
            'tone' => 'text-brand-blue',
            'url' => $safeRoute('knowledge.base'),
            'count_key' => 'knowledge_base',
        ],
    ]));

    /*
    |--------------------------------------------------------------------------
    | Permission Filter
    |--------------------------------------------------------------------------
    */
    $filterSidebarItems = function (array $items) use (&$filterSidebarItems, $hasPermission) {
        $filtered = [];

        foreach ($items as $item) {
            $permission = $item['permission'] ?? $item['data_name'] ?? null;

            if ($permission && !$hasPermission($permission, 'is_read')) {
                continue;
            }

            if (!empty($item['children']) && is_array($item['children'])) {
                $item['children'] = $filterSidebarItems($item['children']);

                if (empty($item['children']) && empty($item['url'])) {
                    continue;
                }
            }

            $filtered[] = $item;
        }

        return $filtered;
    };

    $sidebarSections = array_values(array_filter(array_map(function ($section) use ($filterSidebarItems, $hasPermission) {
        // Section-Gate: ganzer Bereich unsichtbar, wenn Recht fehlt (2-Ebenen-Nav; Bereich = Haupt-Navi).
        if (!empty($section['permission']) && !$hasPermission($section['permission'], 'is_read')) {
            $section['items'] = [];
            return $section;
        }
        $section['items'] = $filterSidebarItems($section['items'] ?? []);
        return $section;
    }, $sidebarSections), function ($section) {
        return !empty($section['items']);
    }));

    $systemMenus = $filterSidebarItems($systemMenus);

    /*
    |--------------------------------------------------------------------------
    | Relevant Icon Colors
    |--------------------------------------------------------------------------
    */
    $iconTone = function (string $icon) {
        return [
            'layout-dashboard' => 'sa-icon-blue',
            'kanban' => 'sa-icon-purple',
            'file-text' => 'sa-icon-blue',
            'clipboard-check' => 'sa-icon-green',
            'file-clock' => 'sa-icon-orange',
            'mail' => 'sa-icon-blue',
            'inbox' => 'sa-icon-blue',
            'mail-open' => 'sa-icon-cyan',
            'settings' => 'sa-icon-slate',
            'settings-2' => 'sa-icon-slate',
            'filter' => 'sa-icon-purple',
            'message-square' => 'sa-icon-green',
            'plus-circle' => 'sa-icon-green',
            'user-check' => 'sa-icon-green',
            'list' => 'sa-icon-blue',
            'users' => 'sa-icon-green',
            'check-circle' => 'sa-icon-green',
            'alert-triangle' => 'sa-icon-orange',
            'trash-2' => 'sa-icon-pink',
            'trash' => 'sa-icon-pink',
            'globe' => 'sa-icon-cyan',
            'user-plus' => 'sa-icon-green',
            'clock' => 'sa-icon-orange',
            'factory' => 'sa-icon-purple',
            'truck' => 'sa-icon-orange',
            'wrench' => 'sa-icon-orange',
            'pen-tool' => 'sa-icon-purple',
            'landmark' => 'sa-icon-blue',
            'shield' => 'sa-icon-green',
            'clipboard' => 'sa-icon-blue',
            'cpu' => 'sa-icon-purple',
            'briefcase' => 'sa-icon-green',
            'receipt-text' => 'sa-icon-orange',
            'slash' => 'sa-icon-pink',
            'check-square' => 'sa-icon-green',
            'list-todo' => 'sa-icon-purple',
            'folder' => 'sa-icon-blue',
            'list-checks' => 'sa-icon-green',
            'calendar-days' => 'sa-icon-blue',
            'calendar' => 'sa-icon-blue',
            'calendar-check' => 'sa-icon-green',
            'folder-open' => 'sa-icon-blue',
            'life-buoy' => 'sa-icon-pink',
            'alert-circle' => 'sa-icon-orange',
            'contact' => 'sa-icon-green',
            'layers' => 'sa-icon-purple',
            'map-pin' => 'sa-icon-pink',
            'calculator' => 'sa-icon-orange',
            'file-signature' => 'sa-icon-blue',
            'languages' => 'sa-icon-purple',
            'percent' => 'sa-icon-orange',
            'network' => 'sa-icon-cyan',
            'building' => 'sa-icon-blue',
            'git-branch' => 'sa-icon-purple',
            'box' => 'sa-icon-green',
            'star' => 'sa-icon-orange',
            'award' => 'sa-icon-purple',
            'thermometer' => 'sa-icon-pink',
            'shopping-cart' => 'sa-icon-green',
            'plug-zap' => 'sa-icon-orange',
            'arrow-up-circle' => 'sa-icon-blue',
            'sliders-horizontal' => 'sa-icon-slate',
            'warehouse' => 'sa-icon-green',
            'qr-code' => 'sa-icon-purple',
            'repeat' => 'sa-icon-blue',
            'send' => 'sa-icon-cyan',
            'shopping-basket' => 'sa-icon-orange',
            'car' => 'sa-icon-pink',
            'receipt' => 'sa-icon-orange',
            'credit-card' => 'sa-icon-blue',
            'user-cog' => 'sa-icon-purple',
            'shield-user' => 'sa-icon-green',
            'user-lock' => 'sa-icon-pink',
            'circle-user' => 'sa-icon-blue',
            'flag' => 'sa-icon-orange',
            'book-open' => 'sa-icon-blue',
            'info' => 'sa-icon-green',
            'circle-help' => 'sa-icon-blue',
            'triangle-alert' => 'sa-icon-orange',
            'search' => 'sa-icon-blue',
        ][$icon] ?? 'sa-icon-slate';
    };


    /*
    |--------------------------------------------------------------------------
    | Render Helpers
    |--------------------------------------------------------------------------
    */
    $renderCountBadge = function (?string $countKey) {
        if (!$countKey) {
            return '';
        }

        return '
                        <span
                            class="sa-sidebar-count is-empty"
                            data-sidebar-count="' . e($countKey) . '"
                            data-count-value="0"
                            title=""
                        >0</span>
                    ';
    };

    $renderActiveRoutes = function (array $item) {
        $routes = $item['active_routes'] ?? [];

        if (empty($routes)) {
            return '';
        }

        if (is_string($routes)) {
            $routes = [$routes];
        }

        return ' data-active-routes="' . e(implode(',', $routes)) . '"';
    };

    $renderSidebarItem = function (array $item) use ($renderCountBadge, $renderActiveRoutes, $iconTone) {
        $hasChildren = !empty($item['children']);
        $label = $item['label'] ?? '';
        $icon = $item['icon'] ?? 'circle';
        $tone = $item['tone'] ?? $iconTone($icon);
        $style = !empty($item['style']) ? ' style="' . e($item['style']) . '"' : '';

        $submenuId = $item['submenu'] ?? ('sub-' . Str::slug($label));
        $wrapperId = !empty($item['id']) ? ' id="' . e($item['id']) . '"' : '';
        $permission = $item['permission'] ?? $item['data_name'] ?? null;
        $dataName = $permission ? ' data-name="' . e($permission) . '"' : '';
        $countBadge = $renderCountBadge($item['count_key'] ?? null);

        $html = '<div class="sa-sidebar-menu-block"' . $wrapperId . $dataName . '>';

        if ($hasChildren) {
            $html .= '
                                            <button class="nav-item sa-sidebar-parent" type="button" onclick="toggleSubmenu(\'' . e($submenuId) . '\')">
                                                <div class="nav-item-content">
                                                    <i data-lucide="' . e($icon) . '" class="icon-md ' . e($tone) . '"' . $style . '></i>
                                                    <span>' . e($label) . '</span>
                                                </div>

                                                <span class="sa-sidebar-right">
                                                    ' . $countBadge . '

                                                    <i data-lucide="chevron-down"
                                                       id="icon-' . e($submenuId) . '"
                                                       class="icon-md text-muted"
                                                       style="transition: transform 0.2s;"></i>
                                                </span>
                                            </button>

                                            <div class="submenu" id="' . e($submenuId) . '">
                                                <div class="submenu-inner">
                                        ';

            foreach ($item['children'] as $child) {
                $childLabel = $child['label'] ?? '';
                $childIcon = $child['icon'] ?? 'circle';
                $childUrl = $child['url'] ?? '#';
                $childTone = $child['tone'] ?? $iconTone($childIcon);
                $childStyle = !empty($child['style']) ? ' style="' . e($child['style']) . '"' : '';
                $childCountBadge = $renderCountBadge($child['count_key'] ?? null);
                $activeRoutes = $renderActiveRoutes($child);

                $html .= '
                                                <a href="' . e($childUrl) . '" class="submenu-link"' . $activeRoutes . '>
                                                    <span class="nav-item-content">
                                                        <i data-lucide="' . e($childIcon) . '" class="icon-sm ' . e($childTone) . '"' . $childStyle . '></i>
                                                        <span>' . e($childLabel) . '</span>
                                                    </span>

                                                    ' . $childCountBadge . '
                                                </a>
                                            ';
            }

            $html .= '
                                                </div>
                                            </div>
                                        ';
        } else {
            $url = $item['url'] ?? '#';
            $active = !empty($item['active']) ? ' active' : '';
            $activeRoutes = $renderActiveRoutes($item);

            $html .= '
                                            <a href="' . e($url) . '" class="nav-item' . $active . '"' . $activeRoutes . '>
                                                <div class="nav-item-content">
                                                    <i data-lucide="' . e($icon) . '" class="icon-md ' . e($tone) . '"' . $style . '></i>
                                                    <span>' . e($label) . '</span>
                                                </div>

                                                ' . $countBadge . '
                                            </a>
                                        ';
        }

        $html .= '</div>';

        return $html;
    };

    /*
    |--------------------------------------------------------------------------
    | Section Tooltip Helper
    |--------------------------------------------------------------------------
    */
    $collectSectionTooltipItems = function (array $items) use (&$collectSectionTooltipItems) {
        $labels = [];

        foreach ($items as $item) {
            if (!empty($item['label'])) {
                $labels[] = $item['label'];
            }

            if (!empty($item['children']) && is_array($item['children'])) {
                foreach ($item['children'] as $child) {
                    if (!empty($child['label'])) {
                        $labels[] = '• ' . $child['label'];
                    }
                }
            }
        }

        return array_slice($labels, 0, 16);
    };


    /*
    |--------------------------------------------------------------------------
    | Customized SVG Icons For Sidebar Groups
    |--------------------------------------------------------------------------
    | These are inline SVGs, so no external icon package is needed for the group
    | headers. They use currentColor and follow the same dark/light theme colors.
    */
    $sectionSvgIcon = function (string $title) {
        $icons = [
            'Arbeitsbereich' => '<svg class="sa-section-custom-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="2"></rect><rect x="14" y="3" width="7" height="7" rx="2"></rect><rect x="3" y="14" width="7" height="7" rx="2"></rect><path d="M15 17.5h5"></path><path d="M17.5 15v5"></path></svg>',
            'CRM' => '<svg class="sa-section-custom-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v7A2.5 2.5 0 0 1 17.5 16H10l-4.5 4v-4A2.5 2.5 0 0 1 4 13.5z"></path><path d="M8 8.5h8"></path><path d="M8 12h5"></path></svg>',
            'Berichte' => '<svg class="sa-section-custom-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h10l4 4v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"></path><path d="M15 4v5h5"></path><path d="M7 13h10"></path><path d="M7 17h7"></path></svg>',
            'Vertrieb' => '<svg class="sa-section-custom-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16l-1.4 11.2A2 2 0 0 1 16.6 20H7.4a2 2 0 0 1-2-1.8z"></path><path d="M9 7a3 3 0 0 1 6 0"></path><path d="M9 13h6"></path><path d="M12 10v6"></path></svg>',
            'Projekte' => '<svg class="sa-section-custom-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="15" rx="2"></rect><path d="M8 3v4"></path><path d="M16 3v4"></path><path d="M4 10h16"></path><path d="M8 15l2 2 5-5"></path></svg>',
            'Support' => '<svg class="sa-section-custom-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="8"></circle><path d="M12 8v5"></path><path d="M12 16h.01"></path><path d="M5.8 5.8l3.1 3.1"></path><path d="M18.2 5.8l-3.1 3.1"></path><path d="M5.8 18.2l3.1-3.1"></path><path d="M18.2 18.2l-3.1-3.1"></path></svg>',
            'Personal' => '<svg class="sa-section-custom-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="9" cy="8" r="3"></circle><path d="M3.8 19a5.2 5.2 0 0 1 10.4 0"></path><circle cx="17" cy="10" r="2.4"></circle><path d="M14.8 19a4.2 4.2 0 0 1 5.4-4"></path></svg>',
            'Artikel & Lager' => '<svg class="sa-section-custom-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9l8-4 8 4-8 4z"></path><path d="M4 9v6l8 4 8-4V9"></path><path d="M12 13v6"></path><path d="M8 7l8 4"></path></svg>',
            'Finanzen' => '<svg class="sa-section-custom-icon" viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="5" width="17" height="14" rx="2"></rect><path d="M7 9h10"></path><path d="M7 13h3"></path><path d="M14 13h3"></path><path d="M7 16h3"></path><path d="M14 16h3"></path></svg>',
            'Admin' => '<svg class="sa-section-custom-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3l7 4v5c0 4.5-2.8 7.5-7 9-4.2-1.5-7-4.5-7-9V7z"></path><path d="M9 12l2 2 4-5"></path></svg>',
            'System' => '<svg class="sa-section-custom-icon" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06A1.8 1.8 0 0 0 15 19.4a1.8 1.8 0 0 0-1 .6V20a2 2 0 0 1-4 0v-.1a1.8 1.8 0 0 0-1-.6 1.8 1.8 0 0 0-1.98.36l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-.6-1H4a2 2 0 0 1 0-4h.1a1.8 1.8 0 0 0 .6-1 1.8 1.8 0 0 0-.36-1.98l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.8 1.8 0 0 0 9 4.6a1.8 1.8 0 0 0 1-.6V4a2 2 0 0 1 4 0v.1a1.8 1.8 0 0 0 1 .6 1.8 1.8 0 0 0 1.98-.36l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.8 1.8 0 0 0 19.4 9c.2.35.4.67.6 1h.1a2 2 0 0 1 0 4H20a1.8 1.8 0 0 0-.6 1z"></path></svg>',
        ];

        // v2-Nav: neue Ein-Wort-Bereiche auf bestehende Bereichs-Icons mappen (kein neues SVG noetig).
        $iconAlias = [
            'Anfragen' => 'CRM', 'Leads' => 'Vertrieb', 'Kontakte' => 'Personal',
            'Kommunikation' => 'CRM', 'Angebote' => 'Berichte', 'Aufträge' => 'Vertrieb',
            'Montage' => 'Projekte', 'Artikel' => 'Artikel & Lager', 'Artikel-Daten' => 'Admin',
            'Lager' => 'Artikel & Lager', 'Planung' => 'Projekte', 'Tickets' => 'Support',
            'Wartung' => 'Projekte', 'Mitarbeiter' => 'Personal', 'HR-Daten' => 'Admin',
            'Organisation' => 'Personal', 'Phasen' => 'Projekte', 'Stammdaten' => 'Berichte',
            'Filialen' => 'Finanzen', 'E-Mail-Einrichtung' => 'CRM', 'Benutzer' => 'Admin',
        ];
        $title = $iconAlias[$title] ?? $title;

        return $icons[$title] ?? '<svg class="sa-section-custom-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v11a2.5 2.5 0 0 1-2.5 2.5h-11A2.5 2.5 0 0 1 4 17.5z"></path><path d="M8 9h8"></path><path d="M8 13h5"></path></svg>';
    };

@endphp

<nav class="sidebar-nav">
    <button class="nav-item sa-sidebar-search-btn" onclick="document.getElementById('searchInput')?.focus()"
        id="sidebarSearchBtn" type="button">
        <div class="nav-item-content">
            <i data-lucide="search" class="icon-md"></i>
            <span>Suchen</span>
        </div>

        <kbd class="sa-sidebar-kbd">⌘K</kbd>
    </button>

    <style>
        @media (max-width: 767px) {
            #sidebarSearchBtn {
                display: none;
            }
        }

        .sa-sidebar-search-btn {
            color: var(--text-muted);
            margin-bottom: 12px;
        }

        .sa-sidebar-kbd {
            font-size: 10px;
            background: var(--border-color);
            color: var(--text-muted);
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
            font-weight: 900;
        }

        .sa-sidebar-menu-block {
            width: 100%;
        }

        .submenu-inner {
            padding-left: 36px;
        }

        .submenu-link,
        .nav-item {
            gap: 10px;
        }

        .submenu-link {
            justify-content: space-between;
        }

        .submenu-link .nav-item-content,
        .nav-item .nav-item-content {
            min-width: 0;
        }

        .submenu-link span,
        .nav-item-content span {
            line-height: 1.2;
        }

        .sa-sidebar-right {
            display: inline-flex;
            align-items: center;
            justify-content: flex-end;
            gap: 8px;
            flex-shrink: 0;
            margin-left: auto;
        }

        .sa-sidebar-count {
            min-width: 20px;
            height: 20px;
            padding: 0 7px;
            border-radius: 999px;
            background: rgba(229, 6, 86, .12);
            color: #e50656;
            border: 1px solid rgba(229, 6, 86, .18);
            font-size: 10px;
            font-weight: 950;
            line-height: 18px;
            text-align: center;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 14px rgba(229, 6, 86, .08);
            flex-shrink: 0;
        }

        .nav-item>.sa-sidebar-count,
        .submenu-link>.sa-sidebar-count {
            margin-left: auto;
        }

        /*
        |--------------------------------------------------------------------------
        | Collapsible Sidebar Sections
        |--------------------------------------------------------------------------
        */
        .sa-sidebar-section {
            position: relative;
            margin: 8px 0;
        }

        .sa-sidebar-section:first-of-type {
            margin-top: 0;
        }

        .sa-section-toggle {
            width: 100%;
            min-height: 36px;
            padding: 8px 10px;
            border-radius: 10px;
            color: var(--text-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .055em;
            transition: background-color var(--transition), color var(--transition);
            position: relative;
        }

        .sa-section-toggle:hover {
            background: var(--bg-hover);
            color: var(--brand-blue);
        }

        .sa-section-title-wrap {
            min-width: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }


        .sa-section-custom-icon {
            width: 19px;
            height: 19px;
            flex: 0 0 19px;
            color: currentColor;
            fill: none;
            stroke: currentColor;
            stroke-width: 1.9;
            stroke-linecap: round;
            stroke-linejoin: round;
            opacity: .95;
            filter: drop-shadow(0 4px 8px rgba(116, 178, 212, .12));
            transition: transform var(--transition), opacity var(--transition), color var(--transition);
        }

        .sa-section-toggle:hover .sa-section-custom-icon,
        .sa-sidebar-section.is-open>.sa-section-toggle .sa-section-custom-icon {
            opacity: 1;
            transform: scale(1.08);
        }

        .sa-section-title {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sa-section-right {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            flex-shrink: 0;
        }

        .sa-section-count {
            min-width: 20px;
            height: 18px;
            padding: 0 6px;
            border-radius: 999px;
            background: rgba(116, 178, 212, .12);
            color: var(--brand-blue);
            border: 1px solid rgba(116, 178, 212, .18);
            font-size: 10px;
            font-weight: 950;
            line-height: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .sa-section-chevron {
            transition: transform var(--transition-layout);
        }

        .sa-sidebar-section.is-open .sa-section-chevron {
            transform: rotate(180deg);
        }

        .sa-section-body {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows var(--transition-layout);
        }

        .sa-sidebar-section.is-open .sa-section-body {
            grid-template-rows: 1fr;
        }

        .sa-section-body-inner {
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding-top: 4px;
        }

        .sa-sidebar-section.is-open .sa-section-body-inner {
            overflow: visible;
        }

        .sa-sidebar-section.is-collapsed .sa-section-body-inner {
            overflow: hidden;
        }

        .sa-sidebar-section.is-open>.sa-section-toggle {
            color: var(--brand-blue);
            background: rgba(116, 178, 212, .08);
        }

        .sa-section-tooltip {
            position: absolute;
            left: calc(100% + 12px);
            top: 0;
            width: 280px;
            max-height: 380px;
            overflow-y: auto;
            padding: 12px;
            border-radius: 14px;
            background: var(--brand-slate);
            color: #ffffff;
            box-shadow: var(--shadow-float);
            border: 1px solid rgba(255, 255, 255, .10);
            z-index: 999999;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateX(-6px);
            transition: opacity .18s ease, transform .18s ease, visibility .18s ease;
            text-transform: none;
            letter-spacing: 0;
        }

        .sa-section-tooltip strong {
            display: block;
            margin-bottom: 8px;
            color: #ffffff;
            font-size: 13px;
            font-weight: 950;
        }

        .sa-section-tooltip span {
            display: block;
            padding: 5px 0;
            color: #e5e7eb;
            font-size: 12px;
            font-weight: 750;
            line-height: 1.35;
            border-top: 1px solid rgba(255, 255, 255, .08);
        }

        .sa-sidebar-section.is-collapsed>.sa-section-toggle:hover .sa-section-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }

        .sidebar-left a.is-route-active,
        .sidebar-left button.is-route-active,
        .sidebar-left .nav-item.is-route-active,
        .sidebar-left .nav-link.is-route-active,
        .sidebar-left .submenu-link.is-route-active,
        .sidebar-left li.is-route-active>a {
            background: rgba(147, 194, 28, .16) !important;
            color: var(--brand-green) !important;
            font-weight: 900 !important;
            box-shadow: inset 3px 0 0 var(--brand-green);
        }

        .sidebar-left a.is-route-active i,
        .sidebar-left a.is-route-active svg,
        .sidebar-left button.is-route-active i,
        .sidebar-left button.is-route-active svg,
        .sidebar-left .nav-item.is-route-active i,
        .sidebar-left .nav-item.is-route-active svg,
        .sidebar-left .submenu-link.is-route-active i,
        .sidebar-left .submenu-link.is-route-active svg {
            color: var(--brand-green) !important;
            stroke: currentColor !important;
        }

        .sidebar-left .submenu.open-by-route {
            grid-template-rows: 1fr !important;
        }

        .sidebar-left .submenu.open-by-route .submenu-inner {
            overflow: visible;
        }

        .sidebar-left .has-active-child,
        .sidebar-left .nav-item.has-active-child,
        .sidebar-left .nav-link.has-active-child {
            background: rgba(116, 178, 212, .10) !important;
            color: var(--brand-blue) !important;
            font-weight: 900 !important;
        }

        html.dark .sa-sidebar-count {
            background: rgba(229, 6, 86, .18);
            color: #fda4af;
            border-color: rgba(253, 164, 175, .25);
        }

        html.dark .sa-section-toggle {
            color: #cbd5e1;
        }

        html.dark .sa-section-toggle:hover,
        html.dark .sa-sidebar-section.is-open>.sa-section-toggle {
            background: rgba(116, 178, 212, .14);
            color: #bfdbfe;
        }

        html.dark .sa-section-count {
            background: rgba(116, 178, 212, .18);
            color: #bfdbfe;
            border-color: rgba(191, 219, 254, .20);
        }

        html.dark .sa-section-tooltip {
            background: #1f2937;
            border-color: #374151;
        }

        html.dark .sidebar-left a.is-route-active,
        html.dark .sidebar-left button.is-route-active,
        html.dark .sidebar-left .nav-item.is-route-active,
        html.dark .sidebar-left .submenu-link.is-route-active {
            background: rgba(147, 194, 28, .22) !important;
            color: #d9f99d !important;
            box-shadow: inset 3px 0 0 #93c21c;
        }

        html.dark .sidebar-left .has-active-child,
        html.dark .sidebar-left .nav-item.has-active-child {
            background: rgba(116, 178, 212, .16) !important;
            color: #bfdbfe !important;
        }

        @media (max-width: 767px) {
            .sa-section-tooltip {
                display: none;
            }
        }
    </style>

    @foreach($sidebarSections as $sectionIndex => $section)
        @if(!empty($section['items']))
            @php
                $sectionTitle = $section['title'] ?? 'Bereich';
                $sectionId = 'sidebar-section-' . Str::slug($sectionTitle) . '-' . $sectionIndex;
                $isDefaultOpen = in_array($sectionTitle, ['Arbeitsbereich'], true);
                $tooltipItems = $collectSectionTooltipItems($section['items']);
                $sectionCountKey = $section['count_key'] ?? null;
                $sectionCountHtml = $sectionCountKey
                    ? '<span class="sa-section-count" data-sidebar-count="' . e($sectionCountKey) . '" style="display:none;">0</span>'
                    : '<span class="sa-section-count">' . count($section['items']) . '</span>';
            @endphp

            <div id="{{ $sectionId }}" class="sa-sidebar-section {{ $isDefaultOpen ? 'is-open' : 'is-collapsed' }}"
                data-sidebar-section data-section-default-open="{{ $isDefaultOpen ? '1' : '0' }}">
                <button type="button" class="sa-section-toggle" onclick="toggleSidebarSection('{{ $sectionId }}')"
                    aria-expanded="{{ $isDefaultOpen ? 'true' : 'false' }}">
                    <span class="sa-section-title-wrap">
                        {!! $sectionSvgIcon($sectionTitle) !!}
                        <span class="sa-section-title">{{ $sectionTitle }}</span>
                    </span>

                    <span class="sa-section-right">
                        {!! $sectionCountHtml !!}
                        <i data-lucide="chevron-down" class="icon-sm sa-section-chevron"></i>
                    </span>

                    <span class="sa-section-tooltip">
                        <strong>{{ $sectionTitle }}</strong>

                        @foreach($tooltipItems as $tooltipItem)
                            <span>{{ $tooltipItem }}</span>
                        @endforeach
                    </span>
                </button>

                <div class="sa-section-body">
                    <div class="sa-section-body-inner">
                        @foreach($section['items'] as $menu)
                            {!! $renderSidebarItem($menu) !!}
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    @if(!empty($systemMenus))
        @php
            $systemSectionId = 'sidebar-section-system';
            $systemTooltipItems = $collectSectionTooltipItems($systemMenus);
        @endphp

        <div id="{{ $systemSectionId }}" class="sa-sidebar-section is-collapsed" data-sidebar-section
            data-section-default-open="0">
            <button type="button" class="sa-section-toggle" onclick="toggleSidebarSection('{{ $systemSectionId }}')"
                aria-expanded="false">
                <span class="sa-section-title-wrap">
                    {!! $sectionSvgIcon('System') !!}
                    <span class="sa-section-title">Wissen</span>
                </span>

                <span class="sa-section-right">
                    <span class="sa-section-count">{{ count($systemMenus) }}</span>
                    <i data-lucide="chevron-down" class="icon-sm sa-section-chevron"></i>
                </span>

                <span class="sa-section-tooltip">
                    <strong>Wissen</strong>

                    @foreach($systemTooltipItems as $tooltipItem)
                        <span>{{ $tooltipItem }}</span>
                    @endforeach
                </span>
            </button>

            <div class="sa-section-body">
                <div class="sa-section-body-inner">
                    @foreach($systemMenus as $menu)
                        {!! $renderSidebarItem($menu) !!}
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</nav>
