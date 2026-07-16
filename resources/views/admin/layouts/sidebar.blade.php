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
            'etage' => 'Heute',
            'items' => [
                ['label' => 'Dashboard', 'icon' => 'layout-dashboard', 'tone' => 'text-brand-blue', 'url' => url('/'), 'active_routes' => ['/']],
                ['label' => 'Arbeitsliste', 'icon' => 'inbox', 'tone' => 'text-brand-blue', 'url' => $safeRoute('admin.arbeitsliste', 'arbeitsliste'), 'active_routes' => ['/arbeitsliste']],
                ['label' => 'Meine Aufgaben', 'icon' => 'check-square', 'tone' => 'text-brand-green', 'url' => url('personal/task/' . $employeeId), 'count_key' => 'tasks', 'active_routes' => ['/personal/task', '/admin/todo/personal']],
                ['label' => 'Tagesberichte', 'icon' => 'file-clock', 'url' => $safeRoute('employee.daily.plan'), 'count_key' => 'daily_reports'],
                ['label' => 'Mein Kalender', 'icon' => 'calendar', 'url' => url('tasks/calendar/personal'), 'active_routes' => ['/tasks/calendar/personal']],
                ['label' => 'Alle Termine', 'icon' => 'calendar-check', 'url' => url('customer/appointments'), 'count_key' => 'appointments', 'active_routes' => ['/customer/appointments']],
                ['label' => 'Persönliche Notizen', 'icon' => 'list-checks', 'tone' => 'text-brand-blue', 'url' => $safeRoute('notes.details'), 'count_key' => 'personal_notes'],
                ['label' => 'Mein Profil', 'icon' => 'circle-user', 'url' => url('/user')],
            ],
        ],
        [
            'title' => 'Anfragen',
            'etage' => 'Vertrieb',
            'permission' => 'Inquiry',
            'items' => [
                ['label' => 'Neu erfassen', 'icon' => 'plus-circle', 'url' => $safeRoute('inquiry.create', 'inquiry_create'), 'active_routes' => ['/inquiry_create', '/inquiries/create']],
                ['label' => 'Meine', 'icon' => 'user-check', 'url' => $safeRoute('my.inquiry.view'), 'count_key' => 'my_inquiries_unpublished', 'active_routes' => ['/my-inquiry', '/my_inquiry']],
                ['label' => 'Von Kunden', 'icon' => 'users', 'url' => $safeRoute('inquiry.customer'), 'count_key' => 'customer_inquiries_unpublished', 'active_routes' => ['/inquiry/customer', '/customer-inquiries']],
                ['label' => 'Freigegeben', 'icon' => 'check-circle', 'url' => $safeRoute('inquiry.published.list'), 'count_key' => 'inquiries_published', 'active_routes' => ['/inquiry_published_list', '/published-inquiries']],
                ['label' => 'Website-Formulare', 'icon' => 'globe', 'url' => $safeRoute('fusion.forms.index'), 'count_key' => 'website_leads', 'active_routes' => ['/fusion/forms']],
                ['label' => 'Spam', 'icon' => 'alert-triangle', 'url' => url('inquiry_junklist'), 'count_key' => 'inquiries_junk', 'active_routes' => ['/inquiry_junklist']],
                ['label' => 'Papierkorb', 'icon' => 'trash-2', 'url' => url('inquiry_deleted_list'), 'count_key' => 'inquiries_deleted', 'active_routes' => ['/inquiry_deleted_list']],
            ],
        ],
        [
            'title' => 'Leads',
            'permission' => 'Customer',
            'items' => [
                ['label' => 'Neu anlegen', 'icon' => 'user-plus', 'url' => url('new_lead_create'), 'active_routes' => ['/new_lead_create']],
                ['label' => 'Übersicht', 'icon' => 'list', 'url' => url('new_lead_view'), 'count_key' => 'customers', 'active_routes' => ['/new_lead_view', '/customer/profile', '/lead/profile']],
                ['label' => 'Kanban', 'icon' => 'kanban', 'tone' => 'text-brand-blue', 'url' => url('lead/kanban'), 'active_routes' => ['/lead/kanban']],
                ['label' => 'Kundenhistorie', 'icon' => 'clock', 'url' => $leadHistorySidebarUrl, 'active_routes' => ['/customers/*/history', '/customers/history']],
                // Commit 2: Tippfehler-Pfad entschaerft -> Route-Name (waiting.loop.leads); Pfad in web.php auf /waiting_leads korrigiert (Alt-Pfad redirected)
                ['label' => 'Wiedervorlage', 'icon' => 'clock', 'url' => $safeRoute('waiting.loop.leads', 'waiting_leads'), 'count_key' => 'customers_waiting', 'active_routes' => ['/wating_leads', '/waiting_leads']],
                ['label' => 'Spam', 'icon' => 'alert-triangle', 'url' => url('lead_junks'), 'count_key' => 'customers_junk', 'active_routes' => ['/lead_junks']],
                ['label' => 'Papierkorb', 'icon' => 'trash-2', 'url' => url('deleted_leads'), 'count_key' => 'customers_deleted', 'active_routes' => ['/deleted_leads']],
            ],
        ],
        [
            'title' => 'Angebote',
            'permission' => 'Customer', // Rollen-Gate 2026-07-16 (Freischaltung steuert Navi)
            'items' => [
                ['label' => 'Assistent', 'icon' => 'cpu', 'url' => url('offers/wizard-smart'), 'active_routes' => ['/offers/wizard-smart']],
                ['label' => 'Nachfassen fällig · geplant', 'icon' => 'bell-ring', 'url' => url('#')],
                ['label' => 'Übersicht', 'icon' => 'list', 'url' => url('admin/offers'), 'count_key' => 'offers', 'active_routes' => ['/admin/offers', '/offers/list']],
                ['label' => 'Vorlagen', 'icon' => 'file-text', 'url' => $safeRoute('offer-templates.index', 'offer-templates'), 'count_key' => 'offer_templates', 'active_routes' => ['/offer-templates']],
                ['label' => 'Sets · geplant', 'icon' => 'layers', 'url' => url('#')],
                ['label' => 'Konfigurator · geplant', 'icon' => 'sliders-horizontal', 'url' => url('#')],
            ],
        ],
        [
            // AUSBAU 2026-07-16 (Roadmap-Navi): geplante Fläche — Pipeline über den ganzen Vorgangsfluss.
            'title' => 'Vorgänge',
            'etage' => 'Abwicklung',
            'permission' => 'Customer',
            'items' => [
                ['label' => 'Pipeline · geplant', 'icon' => 'git-branch', 'url' => url('#')],
            ],
        ],
        [
            'title' => 'Aufträge',
            'permission' => 'Customer', // Rollen-Gate 2026-07-16 (Freischaltung steuert Navi)
            'items' => [
                ['label' => 'Übersicht', 'icon' => 'briefcase', 'url' => $safeRoute('deal.all.list'), 'count_key' => 'deals', 'active_routes' => ['/deal', '/deals', '/auftrag']],
                ['label' => 'Auftragseingang · geplant', 'icon' => 'inbox', 'url' => url('#')],
                ['label' => 'Auftragsbestätigungen · geplant', 'icon' => 'file-check', 'url' => url('#')],
                ['label' => 'Auftragsstatus · geplant', 'icon' => 'git-commit', 'url' => url('#')],
                ['label' => 'Abnahme & Abrechnung · geplant', 'icon' => 'clipboard-signature', 'url' => url('#')],
                ['label' => 'Interne Arbeiten · geplant', 'icon' => 'wrench', 'url' => url('#')],
                ['label' => 'Spam', 'icon' => 'slash', 'url' => $safeRoute('deal.junk.list'), 'count_key' => 'deals_junk'],
                ['label' => 'Papierkorb', 'icon' => 'trash', 'url' => $safeRoute('deal.delete.list'), 'count_key' => 'deals_deleted'],
            ],
        ],
        [
            // Umbau 2026-07-16 (Yama): Auftrag = kaufmännische Erteilung — die operative Vorstufe
            // (Feinaufmaß, Material) läuft PARALLEL dazu und heißt Arbeitsvorbereitung (AV).
            // Sie übergibt an Baustelle (vor Ort) und Plantafel (Einsatzplanung).
            'title' => 'Arbeitsvorbereitung',
            'permission' => 'Customer',
            'items' => [
                ['label' => 'Feinaufmaß', 'icon' => 'ruler', 'url' => $safeRoute('deal.measurements.kanban', 'deal-measurements-kanban'), 'count_key' => 'deal_measurements', 'active_routes' => ['/deal-measurements-kanban', '/deal-measurements']],
                ['label' => 'Materialbedarf & Bestellungen · geplant', 'icon' => 'package-search', 'url' => url('#')],
            ],
        ],
        [
            'title' => 'Baustelle',
            'permission' => 'Customer',
            'items' => array_values(array_filter([
                ['label' => 'Aufgaben', 'icon' => 'users-round', 'tone' => 'text-brand-blue', 'url' => $safeRoute('general-tasks.index', 'general-tasks'), 'count_key' => 'general_tasks_open', 'active_routes' => ['/general-tasks']],
                $canSeeAllReports ? ['label' => 'Berichte', 'icon' => 'file-text', 'tone' => 'text-brand-blue', 'url' => url('admin/overdue-center/recent'), 'count_key' => 'reports_all_remaining', 'active_routes' => ['/admin/overdue-center/recent', '/overdue-center/recent']] : null,
                ['label' => 'Überfällig', 'icon' => 'clipboard-check', 'tone' => 'text-brand-green', 'url' => url('admin/reports'), 'count_key' => 'reports_my_remaining', 'active_routes' => ['/admin/reports', '/reports']],
                ['label' => 'Bautagebuch · geplant', 'icon' => 'notebook-pen', 'url' => url('#')],
                ['label' => 'Baudokumentation · geplant', 'icon' => 'camera', 'url' => url('#')],
                ['label' => 'Qualitätsprüfung · geplant', 'icon' => 'badge-check', 'url' => url('#')],
                ['label' => 'Mängel & Nacharbeiten · geplant', 'icon' => 'stamp', 'url' => url('#')],
            ])),
        ],
        [
            'title' => 'Tickets',
            'permission' => 'Problem',
            'items' => [
                ['label' => 'Neu erfassen', 'icon' => 'plus', 'url' => url('problem_create'), 'active_routes' => ['/problem_create']],
                ['label' => 'Übersicht', 'icon' => 'list', 'url' => url('problem_view'), 'count_key' => 'tickets_open', 'active_routes' => ['/problem_view', '/problem/profile']],
                ['label' => 'Fehlerkatalog', 'icon' => 'alert-circle', 'url' => url('error'), 'count_key' => 'errors', 'active_routes' => ['/error']],
                ['label' => 'Reklamationen · geplant', 'icon' => 'shield-alert', 'url' => url('#')],
            ],
        ],
        [
            'title' => 'Wartung',
            'permission' => 'Problem', // Rollen-Gate 2026-07-16 (Freischaltung steuert Navi)
            'items' => [
                ['label' => 'Fälligkeiten · geplant', 'icon' => 'calendar-clock', 'url' => url('#')],
                ['label' => 'Verträge', 'icon' => 'folder-open', 'url' => url('admin/maintenance/contracts'), 'count_key' => 'maintenance_contracts'],
                ['label' => 'Checklisten (Wartung)', 'icon' => 'plus-circle', 'url' => $safeRoute('admin.maintenance_checklists.index') . '#new-checklist', 'count_key' => 'maintenance_checklists'],
            ],
        ],
        [
            // Begriffs-Entscheid 2026-07-16 (Yama): «Disposition» ersetzt durch «Einsatzplanung» —
            // wer arbeitet wann auf welcher Baustelle (Montage/Service/Wartung). Konzept: konzept-einsatzplanung,
            // Kern wird mit nuriva geteilt (dort: Baustellen + Personal direkt).
            'title' => 'Montage',
            'etage' => 'Einsatzplanung',
            'permission' => 'Employee', // Rollen-Gate 2026-07-16 (Freischaltung steuert Navi)
            'items' => array_values(array_filter([
                ['label' => 'Plantafel', 'icon' => 'calendar-range', 'tone' => 'text-brand-purple', 'url' => $safeRoute('planner.index', '/planner'), 'count_key' => 'planner_projects', 'active_routes' => ['/planner/projects']],
                ['label' => 'Mein Tag · geplant', 'icon' => 'smartphone', 'url' => url('#')],
            ])),
        ],
        [
            // Navi-Audit v3 (2026-07-16): Kontakte-Sektion (1 Eintrag) + Kommunikation zusammengeführt.
            // Rechte je Item (Partner bzw. Email), KEIN Sektions-Gate — sonst verliert eine Rolle die andere Hälfte.
            'title' => 'Adressbuch',
            'etage' => 'Kontakte',
            'items' => [
                // NAV Phase III (Fläche 1, Design A): 8 Kontakt-Typen -> Tab-Leiste (admin/contacts/_tabs).
                ['label' => 'Übersicht', 'icon' => 'list', 'permission' => 'Partner', 'url' => $safeRoute('all.contacts', 'all-contacts'), 'count_key' => 'contacts', 'active_routes' => ['/all-contacts', '/brand', '/brands', '/distributors', '/external_personal']],
                ['label' => 'Posteingang', 'icon' => 'inbox', 'permission' => 'Email', 'url' => url('/email_view'), 'active_routes' => ['/email_view']],
                ['label' => 'Lead E-Mails', 'icon' => 'mail-open', 'permission' => 'Email', 'url' => $safeRoute('lead.email.inbox'), 'active_routes' => ['/lead-email', '/lead/emails']],
                // ENTFERNT auf Anweisung Yama 2026-07-16: Bitrix24-Chat („brauchen wir nicht"). Route/Controller unberührt; zum Reaktivieren einkommentieren.
                // ['label' => 'Bitrix24-Chat', 'icon' => 'message-square', 'url' => $safeRoute('chat.index', 'admin/chat'), 'count_key' => 'chat_unread', 'active_routes' => ['/admin/chat', '/chats']],
            ],
        ],
        [
            // AUSBAU 2026-07-16: Objektakte (Konzept playground «Kunde→Objekt→Projekt») — geplant.
            'title' => 'Kunden & Objekte',
            'permission' => 'Customer',
            'items' => [
                ['label' => 'Gebäudeakte · geplant', 'icon' => 'building-2', 'url' => url('#')],
                ['label' => 'Projekt-Akte · geplant', 'icon' => 'folder-kanban', 'url' => url('#')],
                ['label' => 'Serienbriefe · geplant', 'icon' => 'mails', 'url' => url('#')],
                ['label' => 'Dokumenten-Center (DMS) · geplant', 'icon' => 'folder-open', 'url' => url('#')],
            ],
        ],
        [
            // Navi-Audit v3: NEU — „Planung & 3D" aus Energie herausgelöst (Yamas Thema; wächst mit 3D-Planer/Solar-API).
            'title' => 'Planung & 3D',
            'etage' => 'Tools',
            'items' => [
                ['label' => 'Grundriss-Editor', 'icon' => 'pen-tool', 'url' => $safeRoute('energie.grundriss'), 'active_routes' => ['/admin/energie/grundriss']],
                ['label' => 'Plan-Import', 'icon' => 'file-up', 'url' => $safeRoute('energie.plan-upload'), 'active_routes' => ['/admin/energie/plan-upload']],
                ['label' => 'Dachplaner · geplant', 'icon' => 'box', 'url' => url('#')],
                ['label' => 'Dachbelegung (PV) · geplant', 'icon' => 'layout-grid', 'url' => url('#')],
            ],
        ],
        [
            // Navi-Audit v3: ex „Energie" — jetzt reines Rechner-Thema.
            'title' => 'Energie-Rechner',
            'items' => [
                // ARCHITEKTUR-REGEL 2026-07-16 (Yama): APIs/Datenquellen (PVGIS, Wetter, Google, NAT/Heizgradtage)
                // sind KEINE Navi-Punkte — sie werden als Operanden in Planer/Konfigurator/Rechner integriert
                // (PLZ -> Klima automatisch; Services KlimaPlzService/KlimaBinService existieren dafür).
                // ['label' => 'PVGIS', 'icon' => 'sun', 'url' => $safeRoute('admin.pvgis.index'), 'active_routes' => ['/admin/pvgis']],
                ['label' => 'Wechselrichter-Auslegung', 'icon' => 'zap', 'url' => $safeRoute('energie.wr-auslegung'), 'active_routes' => ['/admin/energie/wr-auslegung']],
                ['label' => 'Wärmepumpen-Auslegung', 'icon' => 'flame', 'url' => $safeRoute('energie.wp-auslegung'), 'active_routes' => ['/admin/energie/wp-auslegung']],
                ['label' => 'Sanierungs-Wirtschaftlichkeit', 'icon' => 'trending-down', 'url' => $safeRoute('energie.sanierung'), 'active_routes' => ['/admin/energie/sanierung']],
                ['label' => 'Konzept-Simulator', 'icon' => 'file-check', 'url' => $safeRoute('energie.energiekonzept'), 'active_routes' => ['/admin/energie/energiekonzept']],
                ['label' => 'Heizlast', 'icon' => 'thermometer-sun', 'url' => $safeRoute('energie.heizlast'), 'active_routes' => ['/admin/energie/heizlast']],
                // GEPARKT 2026-07-15 — Neu-Ausarbeitung Wirtschaftlichkeit/Förderung. Vorübergehend aus der Navi; Routen/Controller bleiben unberührt. Zum Reaktivieren die zwei Zeilen wieder einkommentieren.
                // ['label' => 'Wirtschaftlichkeit', 'icon' => 'calculator', 'url' => $safeRoute('economic_calculations.index', 'admin/economic-calculations'), 'active_routes' => ['/admin/economic-calculations', '/profitability']],
                // ['label' => 'Förderungen', 'icon' => 'file-text', 'permission' => 'Finance', 'url' => $safeRoute('foerderungen.index'), 'count_key' => 'fundings'],
            ],
        ],
        [
            'title' => 'Detailprüfung',
            'items' => [
                ['label' => 'Fußboden-Check', 'icon' => 'grid', 'url' => $safeRoute('energie.fussboden-check'), 'active_routes' => ['/admin/energie/fussboden-check']],
                ['label' => 'Heizkörper-Check', 'icon' => 'thermometer', 'url' => $safeRoute('radiator.config.view'), 'active_routes' => ['/radiator_config_view']],
                ['label' => 'Materialliste', 'icon' => 'layers', 'url' => $safeRoute('energie.materialliste'), 'active_routes' => ['/admin/energie/materialliste']],
            ],
        ],
        [
            'title' => 'Artikel',
            'etage' => 'Materialwirtschaft',
            'permission' => 'Product',
            'items' => [
                ['label' => 'Neu anlegen', 'icon' => 'plus', 'url' => $safeRoute('product.create', 'product_create')],
                ['label' => 'Katalog', 'icon' => 'file', 'url' => $safeRoute('product.info'), 'count_key' => 'products', 'active_routes' => ['/product', '/product_view', '/products']],
                ['label' => 'Favoriten', 'icon' => 'star', 'url' => $safeRoute('product.favorites.index'), 'count_key' => 'product_favorites'],
                ['label' => 'Stammartikel-Listen', 'icon' => 'award', 'url' => $safeRoute('stamp.lists.index'), 'count_key' => 'stamp_favorites'],
                ['label' => 'Master-Sets', 'icon' => 'shopping-cart', 'url' => $safeRoute('admin.master_sets.index'), 'count_key' => 'master_sets'],
            ],
        ],
        [
            'title' => 'Einkauf',
            'permission' => 'Product',
            'items' => [
                ['label' => 'Preisvergleich', 'icon' => 'layers', 'url' => $safeRoute('admin.products.difference')],
                ['label' => 'Großhandel-Suche (IDS)', 'icon' => 'arrow-up-circle', 'url' => $safeRoute('ids.search.form')],
                ['label' => 'Lieferanten-Schnittstellen', 'icon' => 'plug-zap', 'url' => $safeRoute('admin.supplier-connectors.index'), 'active_routes' => ['/admin/supplier-connectors']],
                ['label' => 'Artikelzuordnung · geplant', 'icon' => 'git-merge', 'url' => url('#')],
                ['label' => 'Warenkörbe · geplant', 'icon' => 'shopping-cart', 'url' => url('#')],
                ['label' => 'GAEB / Ausschreibungen · geplant', 'icon' => 'file-spreadsheet', 'url' => url('#')],
            ],
        ],
        [
            'title' => 'Datenpflege',
            'permission' => 'Product', // Rollen-Gate 2026-07-16 (Freischaltung steuert Navi)
            'items' => [
                ['label' => 'Einheiten', 'icon' => 'sliders-horizontal', 'url' => $safeRoute('measure.info'), 'count_key' => 'measures'],
                ['label' => 'Rabattgruppen', 'icon' => 'percent', 'url' => $safeRoute('discount_group.info'), 'count_key' => 'discount_groups'],
                ['label' => 'Warengruppen', 'icon' => 'layers', 'url' => $safeRoute('article_group.index'), 'count_key' => 'article_groups'],
                ['label' => 'Positionsvorschläge', 'icon' => 'users', 'url' => $safeRoute('product.position.view'), 'count_key' => 'product_positions'],
            ],
        ],
        [
            'title' => 'Lager',
            'permission' => 'Product', // Rollen-Gate 2026-07-16 (Freischaltung steuert Navi)
            'items' => [
                ['label' => 'Lieferscheine', 'icon' => 'file-text', 'url' => $safeRoute('delivery-notes.index'), 'count_key' => 'delivery_notes'],
                ['label' => 'Wareneingang', 'icon' => 'package-check', 'url' => $safeRoute('admin.goods-receipts.index')],
                ['label' => 'Übergaben', 'icon' => 'repeat', 'url' => $safeRoute('handover.details')],
                ['label' => 'Warenausgang', 'icon' => 'send', 'url' => $safeRoute('request.out.details'), 'count_key' => 'inventory_requests'],
                ['label' => 'Materialentnahmen · geplant', 'icon' => 'package-minus', 'url' => url('#')],
                ['label' => 'Kaufanfragen', 'icon' => 'shopping-basket', 'url' => $safeRoute('purchase.request'), 'count_key' => 'purchase_requests'],
            ],
        ],
        [
            'title' => 'Fakturierung',
            'etage' => 'Rechnungswesen',
            'permission' => 'Finance', // Rollen-Gate 2026-07-16 (Freischaltung steuert Navi)
            'items' => [
                ['label' => 'Rechnungen', 'icon' => 'receipt-text', 'permission' => 'Invoice', 'url' => $safeRoute('admin.invoices.index', 'admin/invoices'), 'count_key' => 'invoices', 'active_routes' => ['/admin/invoices', '/invoices/canvas']],
                ['label' => 'Gutschriften · geplant', 'icon' => 'file-minus', 'url' => url('#')],
                ['label' => 'E-Rechnungen (XRechnung/ZUGFeRD) · geplant', 'icon' => 'file-digit', 'url' => url('#')],
                // Navi-Audit v3: aus „Stammdaten" hierher (Kostenbasis gehört zu Finanzen).
            ],
        ],
        [
            // AUSBAU 2026-07-16: Buchhaltung — Kern im Haus (app/Services/Accounting, FIBU-Transplant 08.07.).
            // Verdrahtung abgestimmt mit dem accounting-Strang (web.php:5494 «Nav-Eintrag folgt separat»).
            'title' => 'Buchhaltung',
            'permission' => 'Finance',
            'items' => [
                ['label' => 'Offene Posten · geplant', 'icon' => 'list-checks', 'url' => url('#')],
                ['label' => 'Mahnwesen · geplant', 'icon' => 'alarm-clock', 'url' => url('#')],
                ['label' => 'Journal · geplant', 'icon' => 'book-open-check', 'url' => url('#')],
                ['label' => 'Kassenbuch · geplant', 'icon' => 'wallet', 'url' => url('#')],
                ['label' => 'Eingangsrechnungen · geplant', 'icon' => 'file-input', 'url' => url('#')],
                ['label' => 'Ausgangsrechnungen · geplant', 'icon' => 'file-output', 'url' => url('#')],
                ['label' => 'Daueraufträge · geplant', 'icon' => 'repeat', 'url' => url('#')],
            ],
        ],
        [
            'title' => 'Perioden',
            'permission' => 'Finance',
            'items' => [
                ['label' => 'Monatsabschluss · geplant', 'icon' => 'lock', 'url' => url('#')],
                ['label' => 'Abschreibungen (AfA) · geplant', 'icon' => 'building', 'url' => url('#')],
                ['label' => 'Bilanz & SuSa · geplant', 'icon' => 'scale', 'url' => url('#')],
                ['label' => 'Kontenrahmen · geplant', 'icon' => 'list-tree', 'url' => url('#')],
            ],
        ],
        [
            'title' => 'Steuern',
            'permission' => 'Finance',
            'items' => [
                ['label' => 'UStVA · geplant', 'icon' => 'percent', 'url' => url('#')],
                ['label' => 'DATEV-Export · geplant', 'icon' => 'file-spreadsheet', 'url' => url('#')],
                ['label' => 'Kanzlei-Übergabe · geplant', 'icon' => 'briefcase', 'url' => url('#')],
                ['label' => 'Belegarchiv · geplant', 'icon' => 'archive', 'url' => url('#')],
                ['label' => 'GoBD & Prüfzentrum · geplant', 'icon' => 'shield-check', 'url' => url('#')],
            ],
        ],
        [
            'title' => 'Bank',
            'permission' => 'Finance',
            'items' => [
                ['label' => 'Zahlungsverkehr · geplant', 'icon' => 'landmark', 'url' => url('#')],
                ['label' => 'Kontenanbindung & Dienstleister · geplant', 'icon' => 'plug', 'url' => url('#')],
            ],
        ],
        [
            // 2026-07-16 (Yama): laufende Verpflichtungen der Firma — echte Begriffe statt Zahlungsarten.
            'title' => 'Verpflichtungen',
            'permission' => 'Finance',
            'items' => [
                ['label' => 'Kredite & Finanzierung', 'icon' => 'credit-card', 'url' => $safeRoute('assets.installment.show'), 'count_key' => 'installments'],
                ['label' => 'Versicherungen · geplant', 'icon' => 'shield', 'url' => url('#')],
                ['label' => 'Mieten & Pacht · geplant', 'icon' => 'home', 'url' => url('#')],
                ['label' => 'Abgaben · geplant', 'icon' => 'landmark', 'url' => url('#')],
                ['label' => 'Filial-Betriebskosten', 'icon' => 'receipt', 'permission' => 'Finance', 'url' => $safeRoute('branch.expense'), 'count_key' => 'branch_expenses'],
            ],
        ],
        [
            // 2026-07-16 (Yama): Kostenrechnung — Kostenstellen/Kostenträger sichtbar.
            'title' => 'Kostenrechnung',
            'permission' => 'Finance',
            'items' => [
                ['label' => 'Kostenstellen · geplant', 'icon' => 'list-tree', 'url' => url('#')],
                ['label' => 'Kostenträger · geplant', 'icon' => 'tags', 'url' => url('#')],
                ['label' => 'Kalkulationssätze', 'icon' => 'calculator', 'url' => $safeRoute('admin.costing_sets.index')],
            ],
        ],
        [
            'title' => 'Mitarbeiter',
            'etage' => 'Personal',
            'permission' => 'Employee',
            'items' => array_values(array_filter([
                ['label' => 'Neu anlegen', 'icon' => 'user-plus', 'url' => $safeRoute('emp.create', 'emp_create')],
                ['label' => 'Übersicht', 'icon' => 'users', 'url' => $safeRoute('emp.info'), 'count_key' => 'employees', 'active_routes' => ['/emp_info', '/employee_profile', '/employees']],
                ['label' => 'Teams', 'icon' => 'layers', 'url' => $safeRoute('teams.index'), 'count_key' => 'teams'],
                ['label' => 'Arbeitsverträge · geplant', 'icon' => 'file-signature', 'url' => url('#')],
                ['label' => 'Personalakte · geplant', 'icon' => 'folder-heart', 'url' => url('#')],
            ])),
        ],
        [
            'title' => 'Zeitwirtschaft',
            'permission' => 'Employee',
            'items' => [
                ['label' => 'Erfassung · geplant', 'icon' => 'timer', 'url' => url('#')],
                ['label' => 'Überstunden · geplant', 'icon' => 'clock-4', 'url' => url('#')],
                ['label' => 'Schichtpläne', 'icon' => 'clock', 'url' => $safeRoute('time_management.slots'), 'count_key' => 'time_slots'],
                ['label' => 'Arbeitsorte', 'icon' => 'map-pin', 'url' => $safeRoute('work.place.index'), 'count_key' => 'work_places'],
                ['label' => 'Anwesenheit', 'icon' => 'user-check', 'url' => $safeRoute('admin.attendance.analytics'), 'count_key' => 'attendance_today'],
                ['label' => 'Abwesenheiten', 'icon' => 'activity', 'url' => $safeRoute('employee.sickness-holiday-analyser'), 'active_routes' => ['/employee/sickness-holiday-analyser']],
                ['label' => 'Dienstplanung · geplant', 'icon' => 'calendar-cog', 'url' => url('#')],
            ],
        ],
        [
            'title' => 'Rekrutierung & Entwicklung',
            'permission' => 'Employee',
            'items' => [
                ['label' => 'HR-Prozesse · geplant', 'icon' => 'workflow', 'url' => url('#')],
                ['label' => 'Beurteilungen · geplant', 'icon' => 'graduation-cap', 'url' => url('#')],
                ['label' => 'Bewerbungen · geplant', 'icon' => 'user-plus', 'url' => url('#')],
                ['label' => 'Einarbeitung & Austritt · geplant', 'icon' => 'door-open', 'url' => url('#')],
                ['label' => 'Schulungen & Zertifikate · geplant', 'icon' => 'award', 'url' => url('#')],
            ],
        ],
        [
            // Themen-Etagen 2026-07-16: Yamas Thema «Lohn & Gehalt» — aus Mitarbeiter/Stammdaten herausgelöst.
            'title' => 'Lohn & Gehalt',
            'permission' => 'Employee',
            'items' => array_values(array_filter([
                $canSalary ? ['label' => 'Vollkosten', 'icon' => 'calculator', 'url' => $safeRoute('salary.index'), 'count_key' => 'salaries'] : null,
                ['label' => 'Lohnvorbereitung · geplant', 'icon' => 'receipt-euro', 'url' => url('#')],
                ['label' => 'Lohnarten · geplant', 'icon' => 'coins', 'url' => url('#')],
            ])),
        ],
        [
            'title' => 'Stammdaten',
            'permission' => 'Employee', // Rollen-Gate 2026-07-16 (Freischaltung steuert Navi)
            'items' => [
                ['label' => 'Vertragstypen', 'icon' => 'file-signature', 'url' => $safeRoute('contract.type.info'), 'count_key' => 'contract_types'],
                ['label' => 'Sprachen', 'icon' => 'languages', 'url' => $safeRoute('language.info'), 'count_key' => 'languages'],
                ['label' => 'Länder & Nationalitäten', 'icon' => 'globe', 'url' => $safeRoute('country.info'), 'count_key' => 'countries'],
                ['label' => 'Gesetzliche Feiertage', 'icon' => 'calendar-days', 'url' => $safeRoute('public-holidays.index'), 'count_key' => 'public_holidays'],
                ['label' => 'Betriebskalender', 'icon' => 'calendar', 'url' => $safeRoute('holiday.info'), 'count_key' => 'holidays'],
                ['label' => 'Urlaubsanspruch', 'icon' => 'calendar-check', 'url' => $safeRoute('leave.day.info'), 'count_key' => 'leave_days'],
                ['label' => 'Steuerklassen', 'icon' => 'percent', 'url' => $safeRoute('tax.info'), 'count_key' => 'taxes'],
            ],
        ],
        [
            // Themen-Etagen 2026-07-16: Yamas Thema «Firma» — Filialen, Mietobjekte, Verträge, Versicherungen.
            'title' => 'Firma',
            'etage' => 'Unternehmen',
            'items' => [
                ['label' => 'Filialen', 'icon' => 'map-pin', 'url' => $safeRoute('branch.info'), 'count_key' => 'branches'],
                ['label' => 'Mietobjekte · geplant', 'icon' => 'home', 'url' => url('#')],
                ['label' => 'Vertragsmanagement · geplant', 'icon' => 'file-signature', 'url' => url('#')],
                ['label' => 'AGB & Rechtstexte · geplant', 'icon' => 'scale', 'url' => url('#')],
            ],
        ],
        [
            'title' => 'Organisation',
            'permission' => 'Organization',
            'items' => [
                ['label' => 'Abteilungen', 'icon' => 'building', 'url' => $safeRoute('department.info'), 'count_key' => 'departments'],
                ['label' => 'Stellen & Qualifikationen', 'icon' => 'briefcase', 'url' => $safeRoute('position.index'), 'count_key' => 'positions'],
                ['label' => 'Organigramm', 'icon' => 'git-branch', 'url' => $safeRoute('department.organize')],
                ['label' => 'Besetzung', 'icon' => 'network', 'url' => $safeRoute('employee.organization.index', 'employee-organization'), 'count_key' => 'department_positions', 'active_routes' => ['/employee-organization']],
            ],
        ],
        [
            'title' => 'Anlagevermögen',
            'permission' => 'Finance', // Rollen-Gate 2026-07-16 (Freischaltung steuert Navi)
            'items' => [
                ['label' => 'Inventur', 'icon' => 'warehouse', 'url' => $safeRoute('inventory.index'), 'count_key' => 'inventory'],
                ['label' => 'Betriebsmittel', 'icon' => 'qr-code', 'url' => $safeRoute('handover.details.asset'), 'count_key' => 'assets'],
                ['label' => 'Fuhrpark & Maschinen', 'icon' => 'car', 'url' => $safeRoute('machine.inventory'), 'count_key' => 'machines'],
            ],
        ],
        [
            // AUSBAU 2026-07-16: ZAHLEN-Etage (Profit-Center-Bild) — geplante Cockpits.
            'title' => 'Cockpits',
            'etage' => 'Controlling',
            'permission' => 'Finance',
            'items' => [
                ['label' => 'Gesamtfirma · geplant', 'icon' => 'gauge', 'url' => url('#')],
                ['label' => 'Je Abteilung · geplant', 'icon' => 'building', 'url' => url('#')],
                ['label' => 'Geschäftsführung · geplant', 'icon' => 'crown', 'url' => url('#')],
            ],
        ],
        [
            'title' => 'Auswertungen',
            'permission' => 'Finance',
            'items' => [
                ['label' => 'Umsätze · geplant', 'icon' => 'trending-up', 'url' => url('#')],
                ['label' => 'BWA · geplant', 'icon' => 'bar-chart-3', 'url' => url('#')],
                ['label' => 'Bereichs-GuV · geplant', 'icon' => 'table-2', 'url' => url('#')],
                ['label' => 'Abteilungsvergleich · geplant', 'icon' => 'git-compare', 'url' => url('#')],
                ['label' => 'Auslastung · geplant', 'icon' => 'activity', 'url' => url('#')],
                ['label' => 'Kapazität & Produktivität · geplant', 'icon' => 'trending-up', 'url' => url('#')],
            ],
        ],
        [
            'title' => 'Steuerung',
            'permission' => 'Finance',
            'items' => [
                ['label' => 'Strategische Übersicht · geplant', 'icon' => 'compass', 'url' => url('#')],
                ['label' => 'Ziele · geplant', 'icon' => 'target', 'url' => url('#')],
                ['label' => 'Liquidität & Finanzplanung · geplant', 'icon' => 'wallet-cards', 'url' => url('#')],
                ['label' => 'Investitionsplanung · geplant', 'icon' => 'trending-up', 'url' => url('#')],
            ],
        ],
        [
            // Navi-Audit v3: NEU — internes Thema (Wissen/Feedback aus System & Benutzer hierher; Team-Chat entfernt).
            'title' => 'Wissen',
            'etage' => 'Kommunikation',
            'items' => [
                ['label' => 'Handbuch', 'icon' => 'circle-help', 'url' => $safeRoute('knowledge.base'), 'count_key' => 'knowledge_base'],
                ['label' => 'KI-Lernbasis', 'icon' => 'book-open', 'url' => $safeRoute('admin.chat.learnings.index')],
                ['label' => 'News', 'icon' => 'megaphone', 'url' => url('/admin/breaking-news')],
                ['label' => 'Akademie', 'icon' => 'graduation-cap', 'url' => $safeRoute('chat.tutorials.index')],
                ['label' => 'Veranstaltungen · geplant', 'icon' => 'party-popper', 'url' => url('#')],
                ['label' => 'Feedback', 'icon' => 'info', 'url' => $safeRoute('system.feedback.index')],
            ],
        ],
        [
            // AUSBAU 2026-07-16 R3: Portale (playground: Kundenportal, Lieferantenportal, FreigabeApp, MobileStamp).
            'title' => 'Portale',
            'items' => [
                ['label' => 'Kundenzugang · geplant', 'icon' => 'globe', 'url' => url('#')],
                ['label' => 'Lieferantenzugang · geplant', 'icon' => 'truck', 'url' => url('#')],
                ['label' => 'Freigabe-App · geplant', 'icon' => 'check-check', 'url' => url('#')],
                ['label' => 'Mobile Stempel-App · geplant', 'icon' => 'smartphone', 'url' => url('#')],
            ],
        ],
        [
            // AUSBAU 2026-07-16 R3: KI-Zentrale (playground: KIAgentenZentrale, KIInsights, Automatisierungszentrale, Erkennung).
            'title' => 'KI & Automatisierung',
            'etage' => 'Administration',
            'items' => [
                ['label' => 'Agenten-Zentrale · geplant', 'icon' => 'bot', 'url' => url('#')],
                ['label' => 'Insights · geplant', 'icon' => 'sparkles', 'url' => url('#')],
                ['label' => 'Regeln & Abläufe · geplant', 'icon' => 'workflow', 'url' => url('#')],
                ['label' => 'Beleg-Erkennung · geplant', 'icon' => 'scan-text', 'url' => url('#')],
            ],
        ],
        [
            // Navi-Audit v3: ex „Phasen" — Konfigurationsflächen, gehören zur Administration (Umzug via Sortierung).
            'title' => 'Prozesse',
            'permission' => 'Administrator', // Rollen-Gate 2026-07-16 (Freischaltung steuert Navi)
            'items' => [
                // Lead-Phasen-Verwaltung (HTML-Seite lead-stages.manage; NICHT die JSON-API lead-stages.index)
                ['label' => 'Lead-Phasen', 'icon' => 'flag', 'url' => $safeRoute('lead-stages.manage', 'admin/lead-stages/manage'), 'active_routes' => ['/admin/lead-stages/manage']],
                ['label' => 'Arbeitsschritte', 'icon' => 'clock', 'url' => $safeRoute('task_phase.index'), 'count_key' => 'task_phases'],
                ['label' => 'Projekt-Struktur', 'icon' => 'flag', 'url' => $safeRoute('stages.index'), 'count_key' => 'stages'],
                ['label' => 'Checklisten', 'icon' => 'layout', 'url' => $safeRoute('product.formula.index'), 'count_key' => 'product_formulas'],
                ['label' => 'Formularbaukasten · geplant', 'icon' => 'blocks', 'url' => url('#')],
                ['label' => 'Textbausteine · geplant', 'icon' => 'text-select', 'url' => url('#')],
                ['label' => 'Gewerke & Leistungen · geplant', 'icon' => 'hammer', 'url' => url('#')],
                ['label' => 'Fristen · geplant', 'icon' => 'alarm-clock', 'url' => url('#')],
            ],
        ],
        [
            'title' => 'E-Mail',
            'permission' => 'Email',
            'items' => [
                ['label' => 'Konten', 'icon' => 'settings', 'url' => url('/email_configuration'), 'active_routes' => ['/email_configuration']],
                ['label' => 'Lead-Postfächer', 'icon' => 'settings-2', 'url' => $safeRoute('lead-email-accounts.index'), 'active_routes' => ['/lead-email-accounts']],
                ['label' => 'Domain-Filter', 'icon' => 'filter', 'url' => $safeRoute('lead.email.domain.filters.index'), 'active_routes' => ['/lead-email-domain-filters', '/lead/email/domain/filters']],
            ],
        ],
        [
            // 2026-07-16 (Yama): Import/Export zentral — echte Flächen, waren unsichtbar. DATANORM = Handwerks-Standard.
            'title' => 'Datenaustausch',
            'items' => [
                ['label' => 'Artikel', 'icon' => 'package', 'url' => $safeRoute('products.import.index')],
                ['label' => 'Artikel-Bilder', 'icon' => 'image', 'url' => $safeRoute('admin.products.images.csv-import.index')],
                ['label' => 'Leads', 'icon' => 'users', 'url' => $safeRoute('admin.leads.import')],
                ['label' => 'DATANORM', 'icon' => 'database', 'url' => $safeRoute('datanorm.form')],
                ['label' => 'Exporte · geplant', 'icon' => 'download', 'url' => url('#')],
            ],
        ],
        [
            'title' => 'System',
            'items' => [
                ['label' => 'Warnmeldungen', 'icon' => 'triangle-alert', 'url' => $safeRoute('admin.system-warning.index')],
                ['label' => 'Audit-Log · geplant', 'icon' => 'history', 'url' => url('#')],
                ['label' => 'Notiz-Kategorien', 'icon' => 'folder', 'url' => $safeRoute('note.category.view'), 'count_key' => 'note_categories'],
                // Commit 2 (P0): Nav-Gate gesetzt — GarbageController prueft item_id='Administrator'; Nav gleichzieht (is_admin bypass bleibt)
                ['label' => 'Datenbankbereinigung', 'icon' => 'trash-2', 'permission' => 'Administrator', 'url' => $safeRoute('admin.garbage.index')],
            ],
        ],
        [
            'title' => 'Benutzer',
            'permission' => 'Users',
            'items' => [
                ['label' => 'Administratoren', 'icon' => 'shield-user', 'url' => url('/admin_user'), 'count_key' => 'admin_users'],
                ['label' => 'Eingeschränkte Zugänge', 'icon' => 'user-lock', 'url' => url('/limit_user'), 'count_key' => 'limited_users'],
                ['label' => 'Berechtigungen', 'icon' => 'settings', 'url' => $safeRoute('user-rolls.index'), 'count_key' => 'user_roles'],
                // Navi-Audit v3: „Mein Profil" → Arbeitsbereich (persönlich, nicht Administration).
            ],
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | System Menu
    |--------------------------------------------------------------------------
    */
    $systemMenus = array_values(array_filter([
        // Navi-Audit v3: Wissensdatenbank in die Sektion „Kommunikation & Wissen" umgezogen (kein Doppel-Eintrag).
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

    // Themen-Etagen 2026-07-16: jede Sektion erbt ihre Etage von der zuletzt gesetzten — VOR dem
    // Rechte-Filter, damit die Etage bleibt, wenn die erste Sektion einer Etage weggefiltert wird.
    $currentEtage = null;
    foreach ($sidebarSections as $__i => $__sec) {
        if (!empty($__sec['etage'])) { $currentEtage = $__sec['etage']; }
        $sidebarSections[$__i]['_etage'] = $currentEtage;
    }
    unset($__i, $__sec);

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

    @include("admin.layouts.partials.zuletzt-besucht")
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

            @php $__etage = $section['_etage'] ?? null; @endphp
            @if($__etage && $__etage !== ($__prevEtage ?? null))
                @php $__prevEtage = $__etage; $__etageSlug = Str::slug($__etage); @endphp
                @once
                    <style>
                        /* Etagen: dezent & smart (Yamas Korrektur 2026-07-16) — klein, ruhiges Grau, kein harter Akzent. */
                        .sa-etage-toggle{display:flex;width:calc(100% - 12px);align-items:center;justify-content:space-between;margin:12px 6px 2px;padding:5px 10px;background:transparent;border:0;cursor:pointer;font-size:11.5px;font-weight:700;letter-spacing:.07em;text-transform:uppercase;color:#6b7280;border-radius:8px;}
                        .sa-etage-toggle:hover{background:rgba(148,163,184,.12);color:#374151;}
                        html.dark .sa-etage-toggle{color:#9ca3af;}
                        html.dark .sa-etage-toggle:hover{color:#d1d5db;}
                        .sa-etage-chevron{width:12px;height:12px;transition:transform .15s;}
                        .sa-etage-toggle.is-open .sa-etage-chevron{transform:rotate(180deg);}
                    </style>
                @endonce
                <button type="button" class="sa-etage-toggle" data-etage-toggle="{{ $__etageSlug }}" aria-expanded="false">
                    <span>{{ $__etage }}</span>
                    <i data-lucide="chevron-down" class="icon-sm sa-etage-chevron"></i>
                </button>
            @endif
            <div id="{{ $sectionId }}" class="sa-sidebar-section {{ $isDefaultOpen ? 'is-open' : 'is-collapsed' }}"
                data-sidebar-section data-section-default-open="{{ $isDefaultOpen ? '1' : '0' }}"
                data-etage-group="{{ Str::slug($section['_etage'] ?? '') }}">
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

{{-- Themen-Etagen: Auf-/Zuklappen mit Merken (localStorage). Standard: Heute + Etage der aktiven Seite. --}}
<script>
(function () {
    'use strict';
    var KEY = 'sa_etagen_open';
    function read() { try { var a = JSON.parse(localStorage.getItem(KEY) || 'null'); return Array.isArray(a) ? a : null; } catch (e) { return null; } }
    function write(a) { try { localStorage.setItem(KEY, JSON.stringify(a)); } catch (e) {} }
    function apply() {
        var open = read() || [];
        document.querySelectorAll('[data-etage-toggle]').forEach(function (btn) {
            var g = btn.getAttribute('data-etage-toggle');
            var isOpen = open.indexOf(g) !== -1;
            btn.classList.toggle('is-open', isOpen);
            btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            document.querySelectorAll('[data-etage-group="' + g + '"]').forEach(function (sec) {
                sec.style.display = isOpen ? '' : 'none';
            });
        });
    }
    function init() {
        if (read() === null) {
            var def = ['heute'];
            var act = document.querySelector('.sidebar-nav .nav-item.active');
            if (act) { var sec = act.closest('[data-etage-group]'); if (sec) { var g = sec.getAttribute('data-etage-group'); if (g && def.indexOf(g) === -1) def.push(g); } }
            write(def);
        }
        document.addEventListener('click', function (ev) {
            var btn = ev.target && ev.target.closest ? ev.target.closest('[data-etage-toggle]') : null;
            if (!btn) return;
            var g = btn.getAttribute('data-etage-toggle');
            var open = read() || []; var i = open.indexOf(g);
            if (i === -1) open.push(g); else open.splice(i, 1);
            write(open); apply();
        });
        apply();
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
</script>
