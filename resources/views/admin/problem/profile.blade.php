@extends('admin.layouts.app')

@section('title', 'Ticket Profil')

@section('style')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        use Illuminate\Support\Str;
        use Carbon\Carbon;
        use App\Models\MainAppointment;
        use App\Models\Employee;
        use App\Models\ProblemComment;
        use Illuminate\Support\Facades\DB;

        $statusOptions = [
            'offen' => 'Offen',
            'process' => 'In Arbeit',
            'end' => 'Abgeschlossen',
            'junk' => 'Papierkorb',
        ];

        $errorTypesShort = [
            'complaint' => 'Reklamation',
            'emergency_service' => 'Notdienst',
            'repair' => 'Reparatur',
            'maintenance' => 'Wartung',
            'malfunction' => 'Störung',
            'installation' => 'Installation',
            'configuration_error' => 'Konfiguration',
            'system_outage' => 'Systemausfall',
            'security_issue' => 'Security',
            'user_error' => 'Bedienfehler',
            'network_problem' => 'Netzwerk',
            'software_bug' => 'Software',
            'hardware_defect' => 'Hardware',
            'spare_part_request' => 'Ersatzteil',
            'timeout' => 'Timeout',
            'communication_failure' => 'Kommunikation',
            'power_outage' => 'Stromausfall',
            'update_failure' => 'Update',
            'access_issue' => 'Zugriff',
            'other' => 'Sonstiges',
        ];

        $errorTypeIcons = [
            'complaint' => 'fa-exclamation-circle',
            'emergency_service' => 'fa-bolt',
            'repair' => 'fa-tools',
            'maintenance' => 'fa-sync-alt',
            'malfunction' => 'fa-bug',
            'installation' => 'fa-plug',
            'configuration_error' => 'fa-sliders-h',
            'system_outage' => 'fa-server',
            'security_issue' => 'fa-shield-alt',
            'user_error' => 'fa-user-times',
            'network_problem' => 'fa-network-wired',
            'software_bug' => 'fa-code',
            'hardware_defect' => 'fa-microchip',
            'spare_part_request' => 'fa-cogs',
            'timeout' => 'fa-hourglass-half',
            'communication_failure' => 'fa-comments-slash',
            'power_outage' => 'fa-plug',
            'update_failure' => 'fa-sync',
            'access_issue' => 'fa-lock',
            'other' => 'fa-ellipsis-h',
        ];

        $problemStatus = (string) ($problem->status ?? 'offen');
        $currentStatusLabel = $statusOptions[$problemStatus] ?? ucfirst($problemStatus ?: 'Offen');
        $ticketTypeKey = (string) ($problem->error_type ?? 'other');
        $ticketTypeLabel = $errorTypesShort[$ticketTypeKey] ?? ucfirst(str_replace(['_', '-'], ' ', $ticketTypeKey));
        $ticketIcon = $errorTypeIcons[$ticketTypeKey] ?? 'fa-tag';

        $customerModel = $problem->customer ?? null;
        $alternativeModel = $problem->alternative ?? null;
        $productModel = $problem->product ?? null;
        $firstContactModel = $problem->firstContact ?? null;

        $customerCompany = trim((string) ($problem->firma ?? $customerModel?->firma ?? ''));
        $customerFirstName = trim((string) ($problem->name ?? $customerModel?->name ?? ''));
        $customerLastName = trim((string) ($problem->lastname ?? $customerModel?->lastname ?? ''));
        $customerFullName = trim($customerFirstName . ' ' . $customerLastName);
        $customerNo = trim((string) ($problem->customer_no ?? $customerModel?->customer_no ?? ''));

        if ($customerCompany !== '' && $customerFullName !== '') {
            $customerName = $customerCompany . ' - ' . $customerFullName;
        } elseif ($customerCompany !== '') {
            $customerName = $customerCompany;
        } elseif ($customerFullName !== '') {
            $customerName = $customerFullName;
        } elseif ($customerNo !== '') {
            $customerName = '#' . $customerNo;
        } else {
            $customerName = 'Kunde #' . ($problem->customer_id ?? $problem->id);
        }

        $initialSource = $customerFullName ?: $customerCompany ?: ('T' . ($problem->id ?? ''));
        $initialParts = preg_split('/\s+/', trim($initialSource));
        $initials = strtoupper(Str::substr($initialParts[0] ?? 'T', 0, 1) . Str::substr($initialParts[1] ?? '', 0, 1));

        $problemText = (string) ($problem->problem ?? '');
        $solutionText = (string) ($problem->solution ?? '');
        $creatorName = trim(($problem->fname ?? $firstContactModel?->name ?? '') . ' ' . ($problem->flastname ?? $firstContactModel?->lastname ?? '')) ?: '—';

        $street = $problem->street ?? $alternativeModel?->street ?? $customerModel?->street ?? '';
        $postcode = $problem->postcode ?? $alternativeModel?->postcode ?? $customerModel?->postcode ?? '';
        $city = $problem->alt_city ?? $problem->city ?? $alternativeModel?->city ?? $customerModel?->city ?? '';
        $addressText = trim(($street ? $street . ', ' : '') . ($postcode ? $postcode . ' ' : '') . ($city ?: ''), ', ');
        $ticketDate = !empty($problem->date) ? Carbon::parse($problem->date)->format('d.m.Y') : '—';
        $updatedDate = !empty($problem->updated_at) ? Carbon::parse($problem->updated_at)->format('d.m.Y H:i') : '—';

        $ticketReports = collect($ticketReports ?? []);
        $tasks = collect($tasks ?? []);
        $responsibles = collect($responsibles ?? []);
        $ticketImages = collect($ticketImages ?? $images ?? []);
        $ticketFiles = collect($ticketFiles ?? []);
        $doneTasks = (int) ($doneTasks ?? $tasks->where('is_done', 1)->count());
        $totalTasks = (int) ($totalTasks ?? $tasks->count());
        $progressPercent = (int) ($progressPercent ?? ($totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0));

        $customerId = $problem->customer_id ?? null;
        $alternativeId = $problem->alternative_id ?? null;
        $productId = $problem->product_id ?? null;
        $authEmployeeId = (int) (auth()->user()->name ?? 0);
        $googleMapsKey = (string) config('services.google.maps_key', '');

        $ticketAppointments = collect($ticketAppointments ?? MainAppointment::query()
            ->with(['employees', 'createdBy', 'changedBy'])
            ->where('problem_id', $problem->id)
            ->orderByRaw('COALESCE(start_date, created_at) DESC')
            ->orderByRaw('COALESCE(start_time, "00:00") ASC')
            ->get());

        $commentCount = (int) ($commentCount ?? ProblemComment::where('ticket_id', $problem->id)->count());
        $appointmentCount = $ticketAppointments->count();
        $galleryCount = $ticketImages->count() + $ticketFiles->flatten(1)->count();

        $ticketCommentsHistory = collect($ticketCommentsHistory ?? ProblemComment::with('employee')
            ->where('ticket_id', $problem->id)
            ->latest()
            ->limit(25)
            ->get());

        $historyEvents = collect();

        if (!empty($problem->created_at)) {
            $historyEvents->push([
                'type' => 'created',
                'icon' => 'fa-plus-circle',
                'color' => 'green',
                'title' => 'Ticket erstellt',
                'text' => 'Ticket wurde angelegt.',
                'time' => Carbon::parse($problem->created_at),
            ]);
        }

        if (!empty($problem->date)) {
            $historyEvents->push([
                'type' => 'ticket_date',
                'icon' => 'fa-calendar-day',
                'color' => 'blue',
                'title' => 'Ticketdatum gesetzt',
                'text' => Carbon::parse($problem->date)->format('d.m.Y'),
                'time' => Carbon::parse($problem->date),
            ]);
        }

        if (!empty($problem->progress_date)) {
            $historyEvents->push([
                'type' => 'progress',
                'icon' => 'fa-spinner',
                'color' => 'orange',
                'title' => 'In Bearbeitung',
                'text' => 'Ticket wurde in Bearbeitung gesetzt.',
                'time' => Carbon::parse($problem->progress_date),
            ]);
        }

        if (!empty($problem->edit_date)) {
            $historyEvents->push([
                'type' => 'edited',
                'icon' => 'fa-edit',
                'color' => 'blue',
                'title' => 'Ticket bearbeitet',
                'text' => 'Ticketdaten wurden aktualisiert.',
                'time' => Carbon::parse($problem->edit_date),
            ]);
        }

        if (!empty($problem->end_date)) {
            $historyEvents->push([
                'type' => 'closed',
                'icon' => 'fa-check-circle',
                'color' => 'green',
                'title' => 'Ticket abgeschlossen',
                'text' => 'Ticket wurde abgeschlossen.',
                'time' => Carbon::parse($problem->end_date),
            ]);
        }

        foreach ($ticketReports->take(8) as $historyReport) {
            $historyEvents->push([
                'type' => 'report',
                'icon' => 'fa-file-alt',
                'color' => 'blue',
                'title' => 'Bericht erstellt',
                'text' => $historyReport->title ?? 'Bericht',
                'time' => $historyReport->created_at ? Carbon::parse($historyReport->created_at) : now(),
            ]);
        }

        foreach ($ticketCommentsHistory->take(8) as $historyComment) {
            $historyEmployee = trim((optional($historyComment->employee)->name ?? '') . ' ' .
                (optional($historyComment->employee)->lastname ?? ''));
            $historyEvents->push([
                'type' => 'comment',
                'icon' => 'fa-comment-dots',
                'color' => 'orange',
                'title' => 'Kommentar',
                'text' => ($historyEmployee ?: 'Mitarbeiter') . ': ' . Str::limit(strip_tags($historyComment->comment ?? ''), 70),
                'time' => $historyComment->created_at ? Carbon::parse($historyComment->created_at) : now(),
            ]);
        }

        foreach ($ticketAppointments->take(8) as $historyAppointment) {
            $historyEvents->push([
                'type' => 'appointment',
                'icon' => 'fa-calendar-check',
                'color' => 'green',
                'title' => 'Termin geplant',
                'text' => trim(($historyAppointment->name ?? 'Termin') . ' · ' .
                    optional($historyAppointment->start_date)->format('d.m.Y') . ' ' . substr(
                    (string) $historyAppointment->start_time,
                    0,
                    5
                )),
                'time' => $historyAppointment->created_at ? Carbon::parse($historyAppointment->created_at) : now(),
            ]);
        }

        foreach ($tasks->take(8) as $historyTask) {
            $historyEvents->push([
                'type' => 'task',
                'icon' => !empty($historyTask->is_done) ? 'fa-check' : 'fa-tasks',
                'color' => !empty($historyTask->is_done) ? 'green' : 'blue',
                'title' => !empty($historyTask->is_done) ? 'Aufgabe erledigt' : 'Aufgabe',
                'text' => $historyTask->title ?? $historyTask->name ?? 'Aufgabe',
                'time' => !empty($historyTask->updated_at) ? Carbon::parse($historyTask->updated_at) : (!empty($historyTask->created_at)
                    ? Carbon::parse($historyTask->created_at) : now()),
            ]);
        }

        $historyEvents = $historyEvents
            ->sortByDesc(fn($event) => $event['time'] ?? now())
            ->values()
            ->take(30);

        $localEmployees = collect($appointmentEmployees ?? Employee::query()
            ->select('id', 'name', 'lastname', 'image', 'daily_start_time', 'daily_end_time')
            ->orderBy('name')
            ->limit(80)
            ->get());

        $ticketEmployees = collect($ticketEmployees ?? DB::table('employee_problem')
            ->join('employees', 'employees.id', '=', 'employee_problem.employee_id')
            ->where('employee_problem.problem_id', $problem->id)
            ->select(
                'employees.id',
                'employees.name',
                'employees.lastname',
                'employees.image',
                'employees.email',
                'employees.phone'
            )
            ->orderBy('employees.name')
            ->get());

        $ticketEmployeeIds = $ticketEmployees->pluck('id')->map(fn($id) => (int) $id)->values()->all();
        $ticketEmployeeCount = $ticketEmployees->count();

        $routes = [
            'back' => route('problem.view'),
            'edit' => route('problem.edit', $problem->id),
            'statusUpdate' => route('ticket.updateStatus', $problem->id),
            'typeUpdate' => route('ticket.updateType', $problem->id),

            'taskStore' => route('ticketTasks.store'),
            'taskLoad' => route('ticketTasks.load', $problem->id),
            'taskBase' => url('/ticket-tasks'),

            'reportStore' => route('ticket-reports.store'),
            'reportBase' => url('/ticket-reports'),
            'reportCommentStore' => route('ticket-reports.comments.store'),

            'commentsStore' => route('comments.store'),
            'commentsFetch' => route('comments.fetch', $problem->id),
            'commentsBase' => url('/ticket/comments'),

            'galleryList' => route('ticket.image.list', $problem->id),
            'galleryUpload' => route('ticket.image.upload'),
            'galleryDeleteBase' => url('/ticket-image/delete'),

            'fileUpload' => route('ticket.upload'),
            'fileList' => route('ticket.files.index', $problem->id),
            'fileDeleteBase' => url('/ticket/file'),

            'appointmentIndex' => route('ticket.appointments.index', $problem->id),
            'appointmentStore' => route('ticket.appointments.store', $problem->id),
            'appointmentCheck' => route('ticket.appointments.check', $problem->id),
            'appointmentBase' => url('/tickets/' . $problem->id . '/appointments'),
            'appointmentEmployeeSearch' => route('ticket.appointments.employees.search'),

            'ticketEmployeesIndex' => route('ticket.employees.index', $problem->id),
            'ticketEmployeesSync' => route('ticket.employees.sync', $problem->id),
            'ticketEmployeesSearch' => route('ticket.employees.search'),
        ];

        $boot = [
            'ticketId' => (int) $problem->id,
            'ticketNo' => (string) ($problem->ticket_no ?? $problem->id),
            'csrf' => csrf_token(),
            'routes' => $routes,
            'statusOptions' => $statusOptions,
            'errorTypesShort' => $errorTypesShort,
            'errorTypeIcons' => $errorTypeIcons,
            'currentType' => $ticketTypeKey,
            'customerId' => $customerId,
            'alternativeId' => $alternativeId,
            'productId' => $productId,
            'authEmployeeId' => $authEmployeeId,
            'defaultAddress' => $addressText,
            'googleMapsKey' => $googleMapsKey,
            'ticketEmployees' => $ticketEmployees->map(function ($employee) {
                return [
                    'id' => (int) $employee->id,
                    'name' => trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? '')),
                    'image' => !empty($employee->image) ? asset('images/employee/' . $employee->image) : asset('images/gender/male.png'),
                    'email' => $employee->email ?? null,
                    'phone' => $employee->phone ?? null,
                ];
            })->values(),
            'ticketEmployeeIds' => $ticketEmployeeIds,
            'defaultAppointmentTitle' => 'Ticket - ' . $customerName . ' - ' . ($problem->ticket_no ?? ('#' . $problem->id)),
        ];

        $bootJson = json_encode($boot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS |
            JSON_HEX_AMP | JSON_HEX_QUOT);
    @endphp

    <style>
        :root {
            --tp-bg: #f4f6f9;
            --tp-card: #fff;
            --tp-border: #e5e7eb;
            --tp-border2: #cbd5e1;
            --tp-text: #111827;
            --tp-muted: #6b7280;
            --tp-soft: #f9fafb;
            --tp-primary: #93c21c;
            --tp-primary2: #7baa18;
            --tp-blue: #2563eb;
            --tp-green: #10b981;
            --tp-orange: #f59e0b;
            --tp-red: #ef4444;
            --tp-shadow: 0 16px 36px rgba(15, 23, 42, .08);
            --tp-radius: 22px;
        }

        body {
            background: var(--tp-bg)
        }

        .content-body {
            padding: 0
        }

        .ticket-app {
            max-width: 1740px;
            margin: 0 auto;
            padding: 18px;
            color: var(--tp-text)
        }

        .ticket-toolbar {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            align-items: flex-start;
            flex-wrap: wrap;
            margin-bottom: 16px
        }

        .ticket-toolbar h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 900;
            letter-spacing: -.03em
        }

        .ticket-toolbar p {
            margin: 5px 0 0;
            color: var(--tp-muted);
            font-size: 13px
        }

        .ticket-toolbar-actions,
        .ticket-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap
        }

        .ticket-btn,
        .ticket-btn-soft,
        .ticket-icon-btn {
            border: 0;
            text-decoration: none;
            cursor: pointer;
            font: inherit;
            transition: .16s ease
        }

        .ticket-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--tp-primary);
            color: #fff;
            border-radius: 13px;
            padding: 11px 15px;
            font-weight: 900;
            font-size: 13px
        }

        .ticket-btn:hover {
            background: var(--tp-primary2);
            color: #fff;
            text-decoration: none
        }

        .ticket-btn:disabled {
            opacity: .65;
            cursor: not-allowed
        }

        .ticket-btn-soft {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #fff;
            color: var(--tp-text);
            border: 1px solid var(--tp-border);
            border-radius: 13px;
            padding: 11px 15px;
            font-weight: 900;
            font-size: 13px
        }

        .ticket-btn-soft:hover {
            background: #f3f4f6;
            color: var(--tp-text);
            text-decoration: none
        }

        .ticket-btn-danger {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca
        }

        .ticket-icon-btn {
            width: 38px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--tp-border);
            border-radius: 12px;
            background: #fff;
            color: #374151
        }

        .ticket-layout {
            display: grid;
            grid-template-columns: 340px minmax(0, 1fr);
            gap: 16px;
            align-items: start
        }

        .ticket-sidebar {
            position: sticky;
            top: 92px;
            background: #fff;
            border: 1px solid var(--tp-border);
            border-radius: var(--tp-radius);
            padding: 15px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04)
        }

        .ticket-side-profile {
            border: 1px solid #eef2f7;
            background: linear-gradient(180deg, #fff, #fbfcfd);
            border-radius: 20px;
            padding: 14px;
            margin-bottom: 12px
        }

        .ticket-side-main {
            display: flex;
            gap: 12px;
            align-items: center
        }

        .ticket-avatar {
            width: 64px;
            height: 64px;
            border-radius: 20px;
            background: linear-gradient(135deg, #111827, #374151);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 900;
            flex: 0 0 auto
        }

        .ticket-side-name {
            min-width: 0
        }

        .ticket-side-name h2 {
            margin: 0 0 6px;
            font-size: 18px;
            font-weight: 900;
            line-height: 1.15;
            word-break: break-word
        }

        .ticket-pill-row {
            display: flex;
            gap: 6px;
            flex-wrap: wrap
        }

        .ticket-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border-radius: 999px;
            border: 1px solid var(--tp-border);
            background: #f9fafb;
            color: #374151;
            padding: 6px 9px;
            font-size: 11px;
            font-weight: 900
        }

        .ticket-pill.blue {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #dbeafe
        }

        .ticket-pill.green {
            background: #ecfdf5;
            color: #047857;
            border-color: #bbf7d0
        }

        .ticket-pill.orange {
            background: #fffbeb;
            color: #b45309;
            border-color: #fde68a
        }

        .ticket-side-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-top: 12px
        }

        .ticket-status-box {
            grid-column: 1/-1;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--tp-border);
            background: #fff;
            border-radius: 13px;
            padding: 0 10px;
            min-height: 42px
        }

        .ticket-status-box select {
            border: 0;
            background: transparent;
            outline: none;
            width: 100%;
            font-weight: 900;
            font-size: 13px
        }

        .ticket-side-analytics {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            margin-top: 12px
        }

        .ticket-side-stat {
            background: #f9fafb;
            border: 1px solid #eef2f7;
            border-radius: 15px;
            padding: 10px
        }

        .ticket-side-stat .k {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--tp-muted);
            font-weight: 900
        }

        .ticket-side-stat .v {
            font-size: 18px;
            font-weight: 900;
            margin-top: 3px;
            line-height: 1.1
        }

        .ticket-side-problem {
            margin-top: 12px;
            border: 1px solid #eef2f7;
            border-radius: 16px;
            background: #f9fafb;
            padding: 10px;
            cursor: pointer
        }

        .ticket-side-problem .k {
            font-size: 10px;
            text-transform: uppercase;
            color: var(--tp-muted);
            font-weight: 900;
            letter-spacing: .06em
        }

        .ticket-side-problem .v {
            margin-top: 5px;
            font-size: 13px;
            font-weight: 800;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden
        }

        .ticket-team-box {
            margin-top: 12px;
            border: 1px solid #eef2f7;
            border-radius: 18px;
            background: #fff;
            padding: 12px
        }

        .ticket-team-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px
        }

        .ticket-team-head strong {
            font-size: 13px;
            font-weight: 900
        }

        .ticket-team-avatars {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 7px
        }

        .ticket-team-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 0 0 1px #e5e7eb;
            background: #f3f4f6
        }

        .ticket-team-mini {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 6px 9px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            color: #374151
        }

        .ticket-team-mini img {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            object-fit: cover
        }

        .ticket-team-empty {
            font-size: 12px;
            color: #6b7280;
            font-weight: 800;
            background: #f9fafb;
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            padding: 10px;
            text-align: center
        }

        .ticket-team-select-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            border: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 14px;
            padding: 10px;
            margin-bottom: 8px
        }

        .ticket-team-select-row img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover
        }

        .ticket-team-select-name {
            font-weight: 900;
            font-size: 13px
        }

        .ticket-team-select-sub {
            font-size: 11px;
            color: #6b7280;
            font-weight: 700
        }

        .ticket-nav {
            display: grid;
            gap: 8px
        }

        .ticket-nav-btn {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1px solid var(--tp-border);
            background: #fff;
            color: #374151;
            padding: 12px;
            border-radius: 15px;
            font-weight: 900;
            font-size: 13px;
            cursor: pointer;
            text-align: left
        }

        .ticket-nav-btn:hover {
            background: #f3f4f6
        }

        .ticket-nav-btn.active {
            background: #111827;
            color: #fff;
            border-color: #111827
        }

        .ticket-nav-count {
            margin-left: auto;
            min-width: 24px;
            height: 24px;
            border-radius: 999px;
            background: #f3f4f6;
            color: #111827;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 7px;
            font-size: 11px
        }

        .ticket-nav-btn.active .ticket-nav-count {
            background: rgba(255, 255, 255, .16);
            color: #fff
        }

        .ticket-main {
            min-width: 0;
            display: grid;
            gap: 16px
        }

        .ticket-card {
            background: #fff;
            border: 1px solid var(--tp-border);
            border-radius: var(--tp-radius);
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
            overflow: hidden
        }

        .ticket-card-body {
            padding: 18px
        }

        .ticket-panel {
            display: none
        }

        .ticket-panel.active {
            display: block
        }

        .ticket-section-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 14px
        }

        .ticket-section-title {
            margin: 0;
            font-size: 20px;
            font-weight: 900;
            letter-spacing: -.02em
        }

        .ticket-section-subtitle {
            margin-top: 4px;
            color: var(--tp-muted);
            font-size: 13px
        }

        .ticket-inner-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 15px
        }

        .ticket-inner-tab {
            border: 0;
            border-radius: 999px;
            background: #f3f4f6;
            color: #374151;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 900;
            cursor: pointer
        }

        .ticket-inner-tab.active {
            background: #111827;
            color: #fff
        }

        .ticket-inner-pane {
            display: none
        }

        .ticket-inner-pane.active {
            display: block
        }

        .ticket-info-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 14px
        }

        .ticket-info-card {
            background: #f9fafb;
            border: 1px solid #eef2f7;
            border-radius: 18px;
            padding: 15px
        }

        .ticket-info-card h5 {
            margin: 0 0 12px;
            font-size: 15px;
            font-weight: 900
        }

        .ticket-info-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px dashed #e5e7eb
        }

        .ticket-info-row:last-child {
            border-bottom: 0
        }

        .ticket-info-key {
            color: var(--tp-muted);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em
        }

        .ticket-info-val {
            text-align: right;
            font-size: 14px;
            font-weight: 800;
            word-break: break-word
        }

        .ticket-problem-box {
            background: #fff;
            border: 1px solid var(--tp-border);
            border-radius: 18px;
            padding: 16px;
            line-height: 1.65;
            font-size: 14px
        }

        .ticket-form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px
        }

        .ticket-field {
            display: flex;
            flex-direction: column;
            gap: 7px
        }

        .ticket-field.full {
            grid-column: 1/-1
        }

        .ticket-field label {
            font-size: 13px;
            font-weight: 900
        }

        .ticket-field input,
        .ticket-field select,
        .ticket-field textarea {
            width: 100%;
            border: 1px solid var(--tp-border);
            border-radius: 13px;
            padding: 11px 12px;
            outline: none;
            background: #fff;
            font: inherit
        }

        .ticket-field input:focus,
        .ticket-field select:focus,
        .ticket-field textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .08)
        }

        .ticket-table-wrap {
            width: 100%;
            overflow: auto;
            border: 1px solid var(--tp-border);
            border-radius: 18px;
            background: #fff
        }

        .ticket-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 820px
        }

        .ticket-table th,
        .ticket-table td {
            padding: 12px;
            border-bottom: 1px solid #eef2f7;
            text-align: left;
            vertical-align: middle;
            font-size: 14px
        }

        .ticket-table th {
            background: #f9fafb;
            color: var(--tp-muted);
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 900
        }

        .ticket-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 999px;
            padding: 6px 10px;
            background: #f3f4f6;
            color: #374151;
            font-size: 12px;
            font-weight: 900
        }

        .ticket-badge.green {
            background: #ecfdf5;
            color: #047857
        }

        .ticket-badge.orange {
            background: #fffbeb;
            color: #b45309
        }

        .ticket-badge.red {
            background: #fef2f2;
            color: #b91c1c
        }

        .ticket-report-list {
            display: grid;
            gap: 12px;
            margin-top: 16px
        }

        .ticket-report-card {
            border: 1px solid var(--tp-border);
            border-radius: 18px;
            background: #fff;
            padding: 15px
        }

        .ticket-report-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 10px
        }

        .ticket-report-title {
            margin: 0;
            font-size: 16px;
            font-weight: 900
        }

        .ticket-report-meta {
            color: var(--tp-muted);
            font-size: 12px;
            font-weight: 800
        }

        .ticket-report-actions {
            display: flex;
            gap: 7px;
            flex-wrap: wrap
        }

        .ticket-report-content {
            line-height: 1.6;
            color: #374151;
            white-space: pre-wrap
        }

        .ticket-chat-shell {
            border: 1px solid var(--tp-border);
            border-radius: 20px;
            background: #f8fafc;
            overflow: hidden
        }

        .ticket-chat-list {
            height: 520px;
            overflow: auto;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px
        }

        .ticket-chat-bubble {
            max-width: 78%;
            display: flex;
            gap: 9px;
            align-items: flex-end
        }

        .ticket-chat-bubble.mine {
            margin-left: auto;
            flex-direction: row-reverse
        }

        .ticket-chat-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            object-fit: cover;
            background: #e5e7eb
        }

        .ticket-chat-message {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 18px 18px 18px 4px;
            padding: 10px 12px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04)
        }

        .ticket-chat-bubble.mine .ticket-chat-message {
            background: #ecfdf5;
            border-color: #bbf7d0;
            border-radius: 18px 18px 4px 18px
        }

        .ticket-chat-name {
            font-size: 11px;
            font-weight: 900;
            color: #374151;
            margin-bottom: 4px
        }

        .ticket-chat-text {
            font-size: 14px;
            line-height: 1.55;
            white-space: pre-wrap
        }

        .ticket-chat-time {
            font-size: 10px;
            color: var(--tp-muted);
            margin-top: 5px;
            text-align: right
        }

        .ticket-chat-form {
            border-top: 1px solid var(--tp-border);
            background: #fff;
            padding: 12px;
            display: flex;
            gap: 10px
        }

        .ticket-chat-form textarea {
            flex: 1;
            border: 1px solid var(--tp-border);
            border-radius: 15px;
            padding: 11px;
            resize: none;
            min-height: 46px;
            max-height: 110px
        }

        .ticket-media-layout {
            display: grid;
            grid-template-columns: 360px minmax(0, 1fr);
            gap: 14px
        }

        .ticket-media-side {
            display: grid;
            gap: 12px
        }

        .ticket-date-box,
        .ticket-dropzone-box {
            border: 1px solid var(--tp-border);
            border-radius: 18px;
            background: #fff;
            padding: 14px
        }

        .ticket-date-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px
        }

        .ticket-date-mini {
            background: #f9fafb;
            border: 1px solid #eef2f7;
            border-radius: 14px;
            padding: 10px
        }

        .ticket-date-mini span {
            display: block;
            font-size: 10px;
            color: var(--tp-muted);
            font-weight: 900;
            text-transform: uppercase
        }

        .ticket-date-mini strong {
            display: block;
            margin-top: 4px;
            font-size: 14px
        }

        .ticket-gallery-tools {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 14px
        }

        .ticket-gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px
        }

        .ticket-gallery-card {
            border: 1px solid var(--tp-border);
            border-radius: 18px;
            overflow: hidden;
            background: #fff;
            position: relative
        }

        .ticket-gallery-thumb {
            height: 180px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #374151
        }

        .ticket-gallery-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block
        }

        .ticket-file-icon {
            font-size: 42px;
            color: #ef4444
        }

        .ticket-gallery-caption {
            padding: 10px;
            font-size: 13px;
            font-weight: 800;
            word-break: break-word
        }

        .ticket-gallery-actions {
            display: flex;
            gap: 6px;
            padding: 0 10px 10px
        }

        .ticket-empty {
            border: 1px dashed #cbd5e1;
            border-radius: 18px;
            padding: 24px;
            text-align: center;
            color: var(--tp-muted);
            background: #fff
        }

        .dropzone {
            border: 2px dashed #cbd5e1 !important;
            border-radius: 18px !important;
            background: #f8fafc !important;
            padding: 18px !important;
            min-height: 160px !important
        }

        .dropzone .dz-message {
            font-weight: 900;
            color: #374151
        }

        .ticket-calendar-grid {
            display: grid;
            grid-template-columns: .85fr 1.15fr;
            gap: 14px;
            align-items: start
        }

        .ticket-calendar-box {
            border: 1px solid var(--tp-border);
            border-radius: 18px;
            padding: 16px;
            background: #fff
        }

        .ticket-appointment-list {
            display: grid;
            gap: 12px
        }

        .ticket-appointment-row {
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            gap: 14px;
            padding: 14px;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: #fff;
            margin-bottom: 12px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04)
        }

        .ticket-appointment-main {
            display: flex;
            gap: 14px;
            min-width: 0
        }

        .ticket-appointment-date {
            min-width: 120px;
            padding: 10px;
            border-radius: 14px;
            background: #f9fafb;
            display: flex;
            flex-direction: column;
            gap: 4px
        }

        .ticket-appointment-date span {
            font-size: 12px;
            color: #6b7280;
            font-weight: 700
        }

        .ticket-appointment-content h5 {
            margin: 0 0 4px;
            font-size: 15px;
            font-weight: 900
        }

        .ticket-appointment-content p {
            margin: 0 0 8px;
            color: #6b7280;
            font-size: 13px
        }

        .ticket-employee-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px
        }

        .ticket-employee-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 8px;
            border-radius: 999px;
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            font-size: 12px;
            font-weight: 800
        }

        .ticket-employee-pill img {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            object-fit: cover
        }

        .ticket-appointment-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-shrink: 0
        }

        .ticket-type-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px
        }

        .ticket-type-card {
            border: 1px solid var(--tp-border);
            background: #fff;
            border-radius: 18px;
            padding: 14px;
            cursor: pointer;
            transition: .16s ease
        }

        .ticket-type-card.active {
            border-color: #2563eb;
            background: #eff6ff
        }

        .ticket-type-card i {
            width: 38px;
            height: 38px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            margin-bottom: 10px
        }

        .ticket-type-card.active i {
            background: #dbeafe;
            color: #2563eb
        }

        .ticket-type-name {
            font-size: 13px;
            font-weight: 900
        }

        .ticket-modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 18px;
            z-index: 10050
        }

        .ticket-modal.show {
            display: flex
        }

        .ticket-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(17, 24, 39, .6);
            backdrop-filter: blur(3px)
        }

        .ticket-modal-dialog {
            position: relative;
            width: 100%;
            max-width: 980px;
            max-height: 92vh;
            overflow: auto;
            background: #fff;
            border-radius: 22px;
            border: 1px solid #d7e2f0;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .28);
            z-index: 2
        }

        .ticket-modal-header {
            position: sticky;
            top: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-bottom: 1px solid var(--tp-border);
            background: #f8fafc;
            z-index: 3
        }

        .ticket-modal-title {
            font-size: 16px;
            font-weight: 900
        }

        .ticket-modal-close {
            width: 36px;
            height: 36px;
            border: 1px solid var(--tp-border);
            border-radius: 12px;
            background: #fff;
            cursor: pointer
        }

        .ticket-modal-body {
            padding: 18px;
            line-height: 1.7
        }

        .ticket-modal-body img {
            max-width: 100%;
            border-radius: 16px
        }

        .ticket-modal-body iframe {
            width: 100%;
            height: 74vh;
            border: 0;
            border-radius: 16px
        }

        .ticket-hidden {
            display: none !important
        }

        .select2-container {
            width: 100% !important
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 48px !important;
            border: 1px solid var(--tp-border) !important;
            border-radius: 13px !important;
            padding: 4px 6px !important
        }

        .ticket-top-nav-wrap {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(244, 246, 249, .92);
            backdrop-filter: blur(10px);
            border: 1px solid var(--tp-border);
            border-radius: 20px;
            padding: 10px;
            margin-bottom: 16px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .06)
        }

        .ticket-top-nav {
            display: flex !important;
            gap: 8px;
            overflow-x: auto;
            padding-bottom: 2px;
            scrollbar-width: thin
        }

        .ticket-top-nav .ticket-nav-btn {
            width: auto;
            min-width: max-content;
            white-space: nowrap;
            flex: 0 0 auto;
            padding: 11px 13px
        }

        .ticket-history-card {
            margin-top: 12px;
            border: 1px solid var(--tp-border);
            border-radius: 20px;
            background: #fff;
            padding: 14px;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04)
        }

        .ticket-history-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px
        }

        .ticket-history-head h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 900
        }

        .ticket-history-head p {
            margin: 3px 0 0;
            color: var(--tp-muted);
            font-size: 12px;
            font-weight: 700
        }

        .ticket-history-count {
            min-width: 28px;
            height: 28px;
            border-radius: 999px;
            background: #111827;
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 900;
            padding: 0 8px
        }

        .ticket-history-list {
            display: grid;
            gap: 10px;
            max-height: 520px;
            overflow: auto;
            padding-right: 3px
        }

        .ticket-history-item {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr);
            gap: 10px;
            position: relative
        }

        .ticket-history-item:not(:last-child)::after {
            content: "";
            position: absolute;
            left: 16px;
            top: 34px;
            bottom: -10px;
            width: 2px;
            background: #e5e7eb
        }

        .ticket-history-icon {
            width: 34px;
            height: 34px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #eff6ff;
            color: #2563eb;
            z-index: 1;
            font-size: 13px
        }

        .ticket-history-item.green .ticket-history-icon {
            background: #ecfdf5;
            color: #047857
        }

        .ticket-history-item.orange .ticket-history-icon {
            background: #fffbeb;
            color: #b45309
        }

        .ticket-history-item.red .ticket-history-icon {
            background: #fef2f2;
            color: #b91c1c
        }

        .ticket-history-content {
            min-width: 0;
            border: 1px solid #eef2f7;
            background: #f9fafb;
            border-radius: 15px;
            padding: 10px
        }

        .ticket-history-title {
            font-size: 13px;
            font-weight: 900;
            color: #111827
        }

        .ticket-history-text {
            font-size: 12px;
            color: #374151;
            margin-top: 4px;
            line-height: 1.45;
            word-break: break-word
        }

        .ticket-history-time {
            font-size: 11px;
            color: var(--tp-muted);
            font-weight: 800;
            margin-top: 6px
        }

        @media(max-width:1200px) {
            .ticket-layout {
                grid-template-columns: 310px minmax(0, 1fr)
            }

            .ticket-gallery-grid,
            .ticket-type-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }

            .ticket-media-layout,
            .ticket-calendar-grid {
                grid-template-columns: 1fr
            }
        }

        @media(max-width:980px) {
            .ticket-app {
                padding: 10px
            }

            .ticket-layout {
                grid-template-columns: 1fr
            }

            .ticket-sidebar {
                position: static
            }

            .ticket-nav {
                display: flex;
                gap: 8px;
                overflow: auto;
                padding-bottom: 4px
            }

            .ticket-nav-btn {
                flex: 0 0 180px
            }

            .ticket-info-grid {
                grid-template-columns: 1fr
            }
        }

        @media(max-width:640px) {
            .ticket-top-nav {
                display: flex !important;
                overflow-x: auto
            }

            .ticket-top-nav .ticket-nav-btn {
                flex: 0 0 auto
            }

            .ticket-toolbar {
                flex-direction: column
            }

            .ticket-toolbar-actions,
            .ticket-actions {
                width: 100%
            }

            .ticket-btn,
            .ticket-btn-soft {
                width: 100%
            }

            .ticket-card-body {
                padding: 14px
            }

            .ticket-form-grid,
            .ticket-gallery-grid,
            .ticket-type-grid,
            .ticket-date-grid {
                grid-template-columns: 1fr
            }

            .ticket-chat-list {
                height: 430px
            }

            .ticket-chat-bubble {
                max-width: 94%
            }

            .ticket-chat-form {
                flex-direction: column
            }

            .ticket-info-row {
                flex-direction: column
            }

            .ticket-info-val {
                text-align: left
            }

            .ticket-appointment-row,
            .ticket-appointment-main {
                flex-direction: column
            }

            .ticket-appointment-actions {
                width: 100%
            }

            .ticket-appointment-actions .ticket-btn-soft {
                flex: 1
            }

            .ticket-nav {
                display: grid;
                grid-template-columns: 1fr;
                overflow: visible
            }

            .ticket-nav-btn {
                flex: auto
            }

            .ticket-side-actions {
                grid-template-columns: 1fr
            }
        }

        .ticket-report-modal-body {
            display: grid;
            gap: 14px
        }

        .ticket-report-zoom-title {
            font-size: 24px;
            font-weight: 900;
            margin: 0 0 8px;
            letter-spacing: -.02em
        }

        .ticket-report-zoom-meta {
            color: var(--tp-muted);
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 16px
        }

        .ticket-report-zoom-content {
            font-size: 16px;
            line-height: 1.8;
            white-space: pre-wrap;
            background: #fff;
            border: 1px solid var(--tp-border);
            border-radius: 18px;
            padding: 18px
        }

        .ticket-report-create-head {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 14px
        }

        .ticket-modal-dialog.ticket-report-modal-dialog {
            max-width: 760px
        }

        .ticket-modal-dialog.ticket-report-zoom-dialog {
            max-width: 980px
        }

        .ticket-rich-content,
        .ticket-problem-box,
        .ticket-report-content,
        .ticket-report-zoom-content {
            overflow-wrap: anywhere;
        }

        .ticket-rich-content img,
        .ticket-problem-box img,
        .ticket-report-content img,
        .ticket-report-zoom-content img {
            max-width: 100%;
            height: auto;
            border-radius: 14px;
            display: block;
            margin: 10px 0;
        }

        .ticket-rich-content table,
        .ticket-problem-box table,
        .ticket-report-content table,
        .ticket-report-zoom-content table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            overflow: auto;
            display: block;
        }

        .ticket-rich-content p,
        .ticket-problem-box p,
        .ticket-report-content p,
        .ticket-report-zoom-content p {
            margin-bottom: .75rem;
        }
    </style>
@endsection

@section('content')
    <div class="app-content">
        <div class="content-wrapper">
            <div class="content-body">
                <div class="ticket-app" data-ticket-id="{{ $problem->id }}">
                    <div class="ticket-toolbar">
                        <div>
                            <h1>Ticket Profil</h1>
                            <p>Kundeninfo, aktuelles Problem und Verlauf links; schnelle Modul-Navigation oben.</p>
                        </div>
                        <div class="ticket-toolbar-actions">
                            <a href="{{ $routes['back'] }}" class="ticket-btn-soft"><i class="fa fa-arrow-left"></i>
                                Zurück</a>
                            <a href="{{ $routes['edit'] }}" class="ticket-btn-soft"><i class="fa fa-edit"></i>
                                Bearbeiten</a>
                            <button type="button" class="ticket-btn" id="topOpenNewReportModalBtn"><i
                                    class="fa fa-plus"></i> Bericht erstellen</button>
                        </div>
                    </div>

                    <div class="ticket-top-nav-wrap">
                        <div class="ticket-top-nav ticket-nav">
                            <button type="button" class="ticket-nav-btn active" data-panel-target="ticket-overview-panel"><i
                                    class="fa fa-home"></i> Übersicht <span class="ticket-nav-count">1</span></button>
                            <button type="button" class="ticket-nav-btn" data-panel-target="ticket-employees-panel"><i
                                    class="fa fa-users"></i> Ticket Team <span class="ticket-nav-count"
                                    data-count-key="ticketEmployees">{{ $ticketEmployeeCount ?? 0 }}</span></button>
                            <button type="button" class="ticket-nav-btn" data-panel-target="ticket-tasks-panel"><i
                                    class="fa fa-tasks"></i> Aufgaben <span class="ticket-nav-count js-total-tasks">{{
        $totalTasks }}</span></button>
                            <button type="button" class="ticket-nav-btn" data-panel-target="ticket-reports-panel"><i
                                    class="fa fa-file-alt"></i> Berichte <span class="ticket-nav-count"
                                    data-count-key="reports">{{ $ticketReports->count() }}</span></button>
                            <button type="button" class="ticket-nav-btn" data-panel-target="ticket-comments-panel"><i
                                    class="fa fa-comments"></i> Chat <span class="ticket-nav-count"
                                    data-count-key="comments">{{ $commentCount }}</span></button>
                            <button type="button" class="ticket-nav-btn" data-panel-target="ticket-media-panel"><i
                                    class="fa fa-paperclip"></i> Daten & Galerie <span class="ticket-nav-count"
                                    data-count-key="gallery">{{ $galleryCount }}</span></button>
                            <button type="button" class="ticket-nav-btn" data-panel-target="ticket-types-panel"><i
                                    class="fa fa-tags"></i> Ticket-Typen <span class="ticket-nav-count">20</span></button>
                            <button type="button" class="ticket-nav-btn" data-panel-target="ticket-calendar-panel"><i
                                    class="fa fa-calendar-alt"></i> Termine <span class="ticket-nav-count"
                                    data-count-key="appointments">{{ $appointmentCount }}</span></button>
                        </div>
                    </div>

                    <div class="ticket-layout">
                        <aside class="ticket-sidebar">
                            <div class="ticket-side-profile">
                                <div class="ticket-side-main">
                                    <div class="ticket-avatar">{{ $initials ?: 'T' }}</div>
                                    <div class="ticket-side-name">
                                        <h2>{{ $customerName }}</h2>
                                        <div class="ticket-pill-row">
                                            <span class="ticket-pill blue">#{{ $problem->ticket_no ?? $problem->id }}</span>
                                            <span class="ticket-pill orange" id="sideStatusPill">{{ $currentStatusLabel
                                                    }}</span>
                                            <span class="ticket-pill"><i class="fa {{ $ticketIcon }}"></i> <span
                                                    id="sideTypeLabel">{{ $ticketTypeLabel }}</span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="ticket-side-actions">
                                    <div class="ticket-status-box">
                                        <i class="fa fa-exchange-alt"></i>
                                        <select id="problemStatusSelect">
                                            @foreach($statusOptions as $value => $label)
                                                                                <option value="{{ $value }}" @if($problemStatus === $value) selected @endif>{{
                                                $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if(!empty($problem->phone))
                                        <a class="ticket-btn-soft" href="tel:{{ $problem->phone }}"><i class="fa fa-phone"></i>
                                            Anrufen</a>
                                    @endif
                                    @if(!empty($problem->email))
                                        <a class="ticket-btn-soft" href="mailto:{{ $problem->email }}"><i
                                                class="fa fa-envelope"></i> E-Mail</a>
                                    @endif
                                </div>

                                <div class="ticket-side-analytics">
                                    <div class="ticket-side-stat">
                                        <div class="k">Aufgaben</div>
                                        <div class="v"><span class="js-done-tasks">{{ $doneTasks }}</span>/<span
                                                class="js-total-tasks">{{ $totalTasks }}</span></div>
                                    </div>
                                    <div class="ticket-side-stat">
                                        <div class="k">Fortschritt</div>
                                        <div class="v"><span class="js-progress-percent">{{ $progressPercent }}</span>%
                                        </div>
                                    </div>
                                    <div class="ticket-side-stat">
                                        <div class="k">Berichte</div>
                                        <div class="v" id="reportCount">{{ $ticketReports->count() }}</div>
                                    </div>
                                    <div class="ticket-side-stat">
                                        <div class="k">Chat</div>
                                        <div class="v" id="commentCount">{{ $commentCount }}</div>
                                    </div>
                                    <div class="ticket-side-stat">
                                        <div class="k">Team</div>
                                        <div class="v" id="ticketEmployeeCount">{{ $ticketEmployeeCount }}</div>
                                    </div>
                                    <div class="ticket-side-stat">
                                        <div class="k">Termine</div>
                                        <div class="v" id="appointmentCount">{{ $appointmentCount }}</div>
                                    </div>
                                    <div class="ticket-side-stat">
                                        <div class="k">Dateien</div>
                                        <div class="v" id="galleryCount">{{ $galleryCount }}</div>
                                    </div>
                                </div>

                                <div class="ticket-side-problem" data-detail-title="Aktuelles Problem"
                                    data-detail-html="{{ e($problemText ?: 'Kein Problemtext vorhanden.') }}">
                                    <div class="k">Aktuelles Problem</div>
                                    <div class="v">{{ Str::limit(strip_tags($problemText), 120) ?: 'Kein Problemtext
                                            vorhanden.' }}</div>
                                </div>
                            </div>

                            <div class="ticket-history-card">
                                <div class="ticket-history-head">
                                    <div>
                                        <h3>Ticket Verlauf</h3>
                                        <p>Was am Ticket passiert ist</p>
                                    </div>
                                    <span class="ticket-history-count">{{ $historyEvents->count() }}</span>
                                </div>

                                <div class="ticket-history-list">
                                    @forelse($historyEvents as $event)
                                        <div class="ticket-history-item {{ $event['color'] ?? 'blue' }}">
                                            <div class="ticket-history-icon"><i
                                                    class="fa {{ $event['icon'] ?? 'fa-circle' }}"></i></div>
                                            <div class="ticket-history-content">
                                                <div class="ticket-history-title">{{ $event['title'] ?? 'Aktion' }}</div>
                                                <div class="ticket-history-text">{{ $event['text'] ?? '' }}</div>
                                                <div class="ticket-history-time">
                                                    @if(!empty($event['time']))
                                                                                        {{ $event['time']->format('d.m.Y H:i') }} · {{
                                                        $event['time']->diffForHumans() }}
                                                    @else
                                                        —
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="ticket-empty">Noch keine Historie vorhanden.</div>
                                    @endforelse
                                </div>
                            </div>
                        </aside>

                        <main class="ticket-main">
                            <section class="ticket-card">
                                <div class="ticket-card-body">

                                    <div class="ticket-panel active" id="ticket-overview-panel">
                                        <div class="ticket-section-head">
                                            <div>
                                                <h3 class="ticket-section-title">Übersicht</h3>
                                                <div class="ticket-section-subtitle">Das große Hero-Grid wurde entfernt. Die
                                                    wichtigsten Zahlen sind oben in der Sidebar.</div>
                                            </div>
                                        </div>

                                        <div class="ticket-inner-tabs">
                                            <button type="button" class="ticket-inner-tab active"
                                                data-inner-target="overview-problem">Problem</button>
                                            <button type="button" class="ticket-inner-tab"
                                                data-inner-target="overview-details">Details</button>
                                            <button type="button" class="ticket-inner-tab"
                                                data-inner-target="overview-people">Mitarbeiter</button>
                                            <button type="button" class="ticket-inner-tab"
                                                data-inner-target="overview-solution">Lösung</button>
                                        </div>

                                        <div class="ticket-inner-pane active" id="overview-problem">
                                            <div class="ticket-problem-box ticket-rich-content">{!! $problemText ?: '<span
                                                        class="text-muted">Kein Problemtext vorhanden.</span>' !!}</div>
                                        </div>

                                        <div class="ticket-inner-pane" id="overview-details">
                                            <div class="ticket-info-grid">
                                                <div class="ticket-info-card">
                                                    <h5>Ticketdaten</h5>
                                                    <div class="ticket-info-row">
                                                        <div class="ticket-info-key">Ticket-Nr.</div>
                                                        <div class="ticket-info-val">#{{ $problem->ticket_no ?? $problem->id
                                                                }}</div>
                                                    </div>
                                                    <div class="ticket-info-row">
                                                        <div class="ticket-info-key">Kunde</div>
                                                        <div class="ticket-info-val">{{ $customerName }}</div>
                                                    </div>
                                                    <div class="ticket-info-row">
                                                        <div class="ticket-info-key">Firma</div>
                                                        <div class="ticket-info-val">{{ $problem->firma ?: '—' }}</div>
                                                    </div>
                                                    <div class="ticket-info-row">
                                                        <div class="ticket-info-key">Telefon</div>
                                                        <div class="ticket-info-val">{{ $problem->phone ?: '—' }}</div>
                                                    </div>
                                                    <div class="ticket-info-row">
                                                        <div class="ticket-info-key">E-Mail</div>
                                                        <div class="ticket-info-val">{{ $problem->email ?: '—' }}</div>
                                                    </div>
                                                </div>
                                                <div class="ticket-info-card">
                                                    <h5>Status & Objekt</h5>
                                                    <div class="ticket-info-row">
                                                        <div class="ticket-info-key">Status</div>
                                                        <div class="ticket-info-val" id="problemStatusSmallText">{{
        $currentStatusLabel }}</div>
                                                    </div>
                                                    <div class="ticket-info-row">
                                                        <div class="ticket-info-key">Typ</div>
                                                        <div class="ticket-info-val" id="detailTypeLabel">{{
        $ticketTypeLabel }}</div>
                                                    </div>
                                                    <div class="ticket-info-row">
                                                        <div class="ticket-info-key">Priorität</div>
                                                        <div class="ticket-info-val">{{ $problem->priority ?? '—' }}</div>
                                                    </div>
                                                    <div class="ticket-info-row">
                                                        <div class="ticket-info-key">Adresse</div>
                                                        <div class="ticket-info-val">{{ $addressText ?: '—' }}</div>
                                                    </div>
                                                    <div class="ticket-info-row">
                                                        <div class="ticket-info-key">Produkt</div>
                                                        <div class="ticket-info-val">{{ $problem->article_group ??
        $problem->product ?? '—' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="ticket-inner-pane" id="overview-people">
                                            @if($responsibles->count())
                                                <div class="ticket-info-grid">
                                                    @foreach($responsibles as $res)
                                                                                        <div class="ticket-info-card">
                                                                                            <h5>{{ trim(($res->rname ?? $res->name ?? '') . ' ' . ($res->rlastname
                                                        ?? $res->lastname ?? '')) ?: 'Mitarbeiter' }}</h5>
                                                                                            <div class="ticket-info-row">
                                                                                                <div class="ticket-info-key">Rolle</div>
                                                                                                <div class="ticket-info-val">Zuständig</div>
                                                                                            </div>
                                                                                            <div class="ticket-info-row">
                                                                                                <div class="ticket-info-key">ID</div>
                                                                                                <div class="ticket-info-val">{{ $res->id ?? $res->employee_id ?? '—'
                                                                                                                                                }}</div>
                                                                                            </div>
                                                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <div class="ticket-empty">Keine zuständigen Mitarbeiter hinterlegt.</div>
                                            @endif
                                        </div>

                                        <div class="ticket-inner-pane" id="overview-solution">
                                            <div class="ticket-problem-box ticket-rich-content">{!! $solutionText ?: '<span
                                                        class="text-muted">Noch keine Lösung hinterlegt.</span>' !!}</div>
                                        </div>
                                    </div>


                                    <div class="ticket-panel" id="ticket-employees-panel">
                                        <div class="ticket-section-head">
                                            <div>
                                                <h3 class="ticket-section-title">Ticket Team</h3>
                                                <div class="ticket-section-subtitle">Alle Mitarbeiter, die in diesem Ticket
                                                    sind. Du kannst Mitarbeiter hinzufügen oder entfernen.</div>
                                            </div>
                                            <button type="button" class="ticket-btn" id="openTicketEmployeeModalBtnPanel">
                                                <i class="fa fa-user-plus"></i> Mitarbeiter verwalten
                                            </button>
                                        </div>

                                        <div class="ticket-info-card">
                                            <h5>Aktuelle Mitarbeiter</h5>
                                            <div class="ticket-team-avatars" id="ticketPanelEmployeeList">
                                                @forelse($ticketEmployees as $employee)
                                                    @php
                                                        $employeeAvatar = !empty($employee->image) ? asset('images/employee/' .
                                                            $employee->image) : asset('images/gender/male.png');
                                                        $employeeName = trim(($employee->name ?? '') . ' ' . ($employee->lastname ??
                                                            ''));
                                                    @endphp
                                                    <span class="ticket-team-mini" title="{{ $employeeName }}">
                                                        <img src="{{ $employeeAvatar }}" alt="">
                                                        <span>{{ $employeeName ?: ('#' . $employee->id) }}</span>
                                                    </span>
                                                @empty
                                                    <div class="ticket-team-empty">Noch kein Mitarbeiter im Ticket.</div>
                                                @endforelse
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ticket-panel" id="ticket-tasks-panel">
                                        <div class="ticket-section-head">
                                            <div>
                                                <h3 class="ticket-section-title">Aufgaben</h3>
                                                <div class="ticket-section-subtitle">Aufgaben zum Ticket verwalten.</div>
                                            </div>
                                            <button type="button" class="ticket-btn" id="showTaskFormBtn"><i
                                                    class="fa fa-plus"></i> Aufgabe erstellen</button>
                                        </div>

                                        <form id="ticketTaskForm" class="ticket-form-grid ticket-hidden">
                                            @csrf
                                            <input type="hidden" name="ticket_id" value="{{ $problem->id }}">
                                            <div class="ticket-field"><label>Titel</label><input type="text" name="title"
                                                    required></div>
                                            <div class="ticket-field"><label>Status</label><select name="status">
                                                    <option value="open">Offen</option>
                                                    <option value="process">In Arbeit</option>
                                                    <option value="done">Erledigt</option>
                                                </select></div>
                                            <div class="ticket-field"><label>Fällig am</label><input type="date"
                                                    name="due_date"></div>
                                            <div class="ticket-field"><label>Priorität</label><select name="priority">
                                                    <option value="normal">Normal</option>
                                                    <option value="high">Hoch</option>
                                                    <option value="urgent">Dringend</option>
                                                </select></div>
                                            <div class="ticket-field full"><label>Beschreibung</label><textarea
                                                    name="description" rows="3"></textarea></div>
                                            <div class="ticket-field full"><button type="submit" class="ticket-btn"><i
                                                        class="fa fa-save"></i> Aufgabe speichern</button></div>
                                        </form>

                                        <div class="ticket-table-wrap" style="margin-top:14px">
                                            <table class="ticket-table" id="ticketTaskTable">
                                                <thead>
                                                    <tr>
                                                        <th>Status</th>
                                                        <th>Titel</th>
                                                        <th>Priorität</th>
                                                        <th>Fällig</th>
                                                        <th>Aktion</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($tasks as $task)
                                                                                                <tr data-task-row="{{ $task->id }}">
                                                                                                    <td><span
                                                                                                            class="ticket-badge {{ !empty($task->is_done) ? 'green' : 'orange' }}">{{
                                                        !empty($task->is_done) ? 'Erledigt' : ($task->status ??
                                                            'Offen') }}</span></td>
                                                                                                    <td>{{ $task->title ?? $task->name ?? 'Aufgabe' }}</td>
                                                                                                    <td>{{ $task->priority ?? 'normal' }}</td>
                                                                                                    <td>{{ !empty($task->due_date) ?
                                                        Carbon::parse($task->due_date)->format('d.m.Y') : '—' }}</td>
                                                                                                    <td><button type="button" class="ticket-btn-soft js-task-done"
                                                                                                            data-task-id="{{ $task->id }}"><i
                                                                                                                class="fa fa-check"></i></button></td>
                                                                                                </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="5">
                                                                <div class="ticket-empty">Keine Aufgaben vorhanden.</div>
                                                            </td>
                                                        </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <div class="ticket-panel" id="ticket-reports-panel">
                                        <div class="ticket-section-head">
                                            <div>
                                                <h3 class="ticket-section-title">Berichte</h3>
                                                <div class="ticket-section-subtitle">Neue Berichte öffnen in einem Modal.
                                                    Jeder Bericht kann im Zoom-Modus gelesen werden.</div>
                                            </div>
                                            <button type="button" class="ticket-btn" id="openNewReportModalBtn"><i
                                                    class="fa fa-plus"></i> Neuer Bericht</button>
                                        </div>

                                        <div class="ticket-report-list" id="ticketReportList">
                                            @forelse($ticketReports as $report)
                                                                                <div class="ticket-report-card" data-report-id="{{ $report->id }}">
                                                                                    <div class="ticket-report-head">
                                                                                        <div>
                                                                                            <h4 class="ticket-report-title">{{ $report->title }}</h4>
                                                                                            <div class="ticket-report-meta">{{ optional($report->employee)->name
                                                                                                                                        }}
                                                                                                {{ optional($report->employee)->lastname }} · {{
                                                optional($report->created_at)->diffForHumans() }}</div>
                                                                                        </div>
                                                                                        <div class="ticket-report-actions">
                                                                                            <button type="button" class="ticket-btn-soft js-report-view"
                                                                                                data-report-id="{{ $report->id }}"
                                                                                                data-title="{{ e($report->title) }}"
                                                                                                data-report="{{ e($report->report) }}"
                                                                                                data-meta="{{ e(trim((optional($report->employee)->name ?? '') . ' ' . (optional($report->employee)->lastname ?? '')) . ' · ' . optional($report->created_at)->diffForHumans()) }}"><i
                                                                                                    class="fa fa-search-plus"></i> Zoom</button>
                                                                                            <button type="button" class="ticket-btn-soft js-report-like"
                                                                                                data-report-id="{{ $report->id }}"><i
                                                                                                    class="fa fa-thumbs-up"></i> <span>{{ (int) ($report->likes
                                                ?? 0) }}</span></button>
                                                                                            <button type="button" class="ticket-btn-soft js-report-edit"
                                                                                                data-report-id="{{ $report->id }}"
                                                                                                data-title="{{ e($report->title) }}"
                                                                                                data-report="{{ e($report->report) }}"><i
                                                                                                    class="fa fa-edit"></i></button>
                                                                                            <button type="button"
                                                                                                class="ticket-btn-soft ticket-btn-danger js-report-delete"
                                                                                                data-report-id="{{ $report->id }}"><i
                                                                                                    class="fa fa-trash"></i></button>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="ticket-report-content">{!! $report->report !!}</div>
                                                                                </div>
                                            @empty
                                                <div class="ticket-empty" id="ticketReportEmpty">Noch keine Berichte vorhanden.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="ticket-panel" id="ticket-comments-panel">
                                        <div class="ticket-section-head">
                                            <div>
                                                <h3 class="ticket-section-title">Ticket Chat</h3>
                                                <div class="ticket-section-subtitle">Kommentare als Chatroom. Speichert per
                                                    AJAX und lädt live nach.</div>
                                            </div>
                                            <button type="button" class="ticket-btn-soft" id="reloadCommentsBtn"><i
                                                    class="fa fa-sync"></i> Neu laden</button>
                                        </div>
                                        <div class="ticket-chat-shell">
                                            <div class="ticket-chat-list" id="ticketChatList">
                                                <div class="ticket-empty">Kommentare werden geladen...</div>
                                            </div>
                                            <form id="ticketChatForm" class="ticket-chat-form" method="post"
                                                action="{{ $routes['commentsStore'] }}">
                                                @csrf
                                                <input type="hidden" name="ticket_id" value="{{ $problem->id }}">
                                                <textarea name="comment" id="ticketChatInput"
                                                    placeholder="Nachricht schreiben..." required></textarea>
                                                <button type="submit" class="ticket-btn" id="ticketChatSendBtn"><i
                                                        class="fa fa-paper-plane"></i></button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="ticket-panel" id="ticket-media-panel">
                                        <div class="ticket-section-head">
                                            <div>
                                                <h3 class="ticket-section-title">Daten & Galerie</h3>
                                                <div class="ticket-section-subtitle">Datum, Ticketdaten, Bilder und PDFs an
                                                    einem Ort.</div>
                                            </div>
                                            <button type="button" class="ticket-btn-soft" id="reloadGalleryBtn"><i
                                                    class="fa fa-sync"></i> Neu laden</button>
                                        </div>

                                        <div class="ticket-media-layout">
                                            <div class="ticket-media-side">
                                                <div class="ticket-date-box">
                                                    <h5 style="font-weight:900;margin:0 0 10px">Datum & Status</h5>
                                                    <div class="ticket-date-grid">
                                                        <div class="ticket-date-mini"><span>Ticketdatum</span><strong>{{
        $ticketDate }}</strong></div>
                                                        <div class="ticket-date-mini"><span>Aktualisiert</span><strong>{{
        $updatedDate }}</strong></div>
                                                        <div class="ticket-date-mini"><span>Status</span><strong
                                                                id="mediaStatusText">{{ $currentStatusLabel }}</strong>
                                                        </div>
                                                        <div class="ticket-date-mini"><span>Typ</span><strong
                                                                id="mediaTypeText">{{ $ticketTypeLabel }}</strong></div>
                                                    </div>
                                                </div>

                                                <div class="ticket-dropzone-box">
                                                    <h5 style="font-weight:900;margin:0 0 10px">Upload</h5>
                                                    <form action="{{ $routes['galleryUpload'] }}" class="dropzone"
                                                        id="ticketDropzone">
                                                        @csrf
                                                        <input type="hidden" name="ticket_id" value="{{ $problem->id }}">
                                                        <input type="hidden" name="stage" value="{{ $ticketTypeKey }}">
                                                        <div class="dz-message">
                                                            Bilder oder PDF hier ablegen<br>
                                                            <small>JPG, PNG, PDF, DOCX, XLSX</small>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>

                                            <div>
                                                <div class="ticket-gallery-tools">
                                                    <span class="ticket-badge"><i class="fa fa-paperclip"></i> <span
                                                            id="galleryCountInline">{{ $galleryCount }}</span>
                                                        Dateien</span>
                                                    <span class="ticket-badge orange">Bilder und PDF werden hier zusammen
                                                        angezeigt</span>
                                                </div>
                                                <div class="ticket-gallery-grid" id="ticketGalleryGrid">
                                                    <div class="ticket-empty">Galerie wird geladen...</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="ticket-panel" id="ticket-types-panel">
                                        <div class="ticket-section-head">
                                            <div>
                                                <h3 class="ticket-section-title">Ticket-Typen</h3>
                                                <div class="ticket-section-subtitle">Typ ändern ohne Reload.</div>
                                            </div>
                                        </div>
                                        <div class="ticket-type-grid">
                                            @foreach($errorTypesShort as $key => $label)
                                                <button type="button"
                                                    class="ticket-type-card {{ $ticketTypeKey === $key ? 'active' : '' }}"
                                                    data-type-key="{{ $key }}" data-type-label="{{ $label }}">
                                                    <i class="fa {{ $errorTypeIcons[$key] ?? 'fa-tag' }}"></i>
                                                    <div class="ticket-type-name">{{ $label }}</div>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="ticket-panel" id="ticket-calendar-panel">
                                        <div class="ticket-section-head">
                                            <div>
                                                <h3 class="ticket-section-title">Ticket-Terminplan</h3>
                                                <div class="ticket-section-subtitle">Ticket-Termine sind direkt mit
                                                    main_appointments verbunden.</div>
                                            </div>
                                        </div>
                                        <div class="ticket-calendar-grid">
                                            <div class="ticket-calendar-box">
                                                <form id="ticketAppointmentForm">
                                                    @csrf
                                                    <input type="hidden" name="appointment_id">
                                                    <input type="hidden" name="customer_id" value="{{ $customerId }}">
                                                    <input type="hidden" name="product_id" value="{{ $productId }}">
                                                    <input type="hidden" id="ticketTravelSummary" name="travel_summary">
                                                    <input type="hidden" name="latitude">
                                                    <input type="hidden" name="longitude">
                                                    <input type="hidden" name="street">
                                                    <input type="hidden" name="postcode">
                                                    <input type="hidden" name="city">

                                                    <div class="ticket-form-grid">
                                                        <div class="ticket-field full"><label>Titel</label><input
                                                                type="text" name="name"
                                                                value="{{ $boot['defaultAppointmentTitle'] }}"
                                                                placeholder="{{ $boot['defaultAppointmentTitle'] }}">
                                                        </div>
                                                        <div class="ticket-field"><label>Startdatum</label><input
                                                                type="date" name="start_date" required></div>
                                                        <div class="ticket-field"><label>Enddatum</label><input type="date"
                                                                name="end_date"></div>
                                                        <div class="ticket-field"><label>Startzeit</label><input type="time"
                                                                name="start_time" required></div>
                                                        <div class="ticket-field"><label>Endzeit</label><input type="time"
                                                                name="end_time" required></div>
                                                        <div class="ticket-field"><label>Status</label><select
                                                                name="status">
                                                                <option value="planned">Geplant</option>
                                                                <option value="process">In Arbeit</option>
                                                                <option value="done">Erledigt</option>
                                                                <option value="cancelled">Abgesagt</option>
                                                            </select></div>
                                                        <div class="ticket-field"><label>Priorität</label><select
                                                                name="priority">
                                                                <option value="normal">Normal</option>
                                                                <option value="high">Hoch</option>
                                                                <option value="urgent">Dringend</option>
                                                            </select></div>
                                                        <div class="ticket-field full"><label>Mitarbeiter</label><select
                                                                id="ticketAppointmentEmployees" name="employee_ids[]"
                                                                multiple></select></div>
                                                        <div class="ticket-field full"><label>Adresse</label><input
                                                                id="ticketAppointmentAddress" type="text"
                                                                name="full_address" value="{{ $addressText }}"></div>
                                                        <div class="ticket-field full">
                                                            <div id="ticketTravelSummaryBox" class="ticket-badge orange">
                                                                Anfahrt wird nach Adressauswahl berechnet.</div>
                                                        </div>
                                                        <div class="ticket-field full"><label>Notiz</label><textarea
                                                                name="note" rows="3"></textarea></div>
                                                        <div class="ticket-field full">
                                                            <div id="ticketAppointmentConflictBox" class="ticket-hidden">
                                                            </div>
                                                        </div>
                                                        <div class="ticket-field full"
                                                            style="display:flex;gap:8px;flex-direction:row;flex-wrap:wrap">
                                                            <button type="submit" id="ticketAppointmentSaveBtn"
                                                                class="ticket-btn"><i class="fa fa-save"></i> Ticket
                                                                speichern</button>
                                                            <button type="button" id="ticketAppointmentForceSaveBtn"
                                                                class="ticket-btn-soft ticket-btn-danger ticket-hidden">Trotzdem
                                                                speichern</button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="ticket-calendar-box">
                                                <div class="ticket-appointment-list" id="ticketAppointmentList">
                                                    <div class="ticket-empty">Tickets werden geladen...</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </section>
                        </main>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="ticket-modal" id="ticketEmployeeModal">
        <div class="ticket-modal-backdrop" data-close-modal></div>
        <div class="ticket-modal-dialog">
            <div class="ticket-modal-header">
                <div class="ticket-modal-title">Ticket Team verwalten</div>
                <button type="button" class="ticket-modal-close" data-close-modal>×</button>
            </div>
            <div class="ticket-modal-body">
                <form id="ticketEmployeeForm">
                    @csrf
                    <div class="ticket-field full">
                        <label>Mitarbeiter auswählen</label>
                        <select id="ticketEmployeeSelect" name="employee_ids[]" multiple></select>
                    </div>
                    <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap">
                        <button type="submit" class="ticket-btn" id="ticketEmployeeSaveBtn">
                            <i class="fa fa-save"></i> Team speichern
                        </button>
                        <button type="button" class="ticket-btn-soft" data-close-modal>Abbrechen</button>
                    </div>
                </form>
                <div class="ticket-section-subtitle" style="margin-top:12px">
                    Diese Mitarbeiter werden in <code>employee_problem</code> mit diesem Ticket verbunden.
                </div>
            </div>
        </div>
    </div>

    <div class="ticket-modal" id="ticketDetailModal">
        <div class="ticket-modal-backdrop" data-close-modal></div>
        <div class="ticket-modal-dialog">
            <div class="ticket-modal-header">
                <div class="ticket-modal-title" id="ticketDetailModalTitle">Details</div>
                <button type="button" class="ticket-modal-close" data-close-modal>×</button>
            </div>
            <div class="ticket-modal-body" id="ticketDetailModalBody"></div>
        </div>
    </div>

    <div class="ticket-modal" id="ticketReportModal">
        <div class="ticket-modal-backdrop" data-close-modal></div>
        <div class="ticket-modal-dialog ticket-report-modal-dialog">
            <div class="ticket-modal-header">
                <div class="ticket-modal-title" id="ticketReportModalTitle">Neuer Bericht</div>
                <button type="button" class="ticket-modal-close" data-close-modal>×</button>
            </div>
            <div class="ticket-modal-body">
                <form id="ticketReportForm" class="ticket-form-grid" method="post" action="{{ $routes['reportStore'] }}">
                    @csrf
                    <input type="hidden" name="ticket_id" value="{{ $problem->id }}">
                    <input type="hidden" name="customer_id" value="{{ $customerId }}">
                    <input type="hidden" name="alternative_id" value="{{ $alternativeId }}">
                    <input type="hidden" name="product_id" value="{{ $productId }}">
                    <input type="hidden" name="language" value="de">
                    <input type="hidden" name="report_id" id="ticketReportEditId">
                    <div class="ticket-field full"><label>Titel</label><input type="text" name="title"
                            id="ticketReportTitle" required></div>
                    <div class="ticket-field"><label>Sprache</label><select name="language">
                            <option value="de">Deutsch</option>
                            <option value="en">English</option>
                        </select></div>
                    <div class="ticket-field full"><label>Bericht</label><textarea name="report" id="ticketReportText"
                            rows="8" required></textarea></div>
                    <div class="ticket-field full" style="display:flex;gap:8px;flex-direction:row;flex-wrap:wrap">
                        <button type="submit" class="ticket-btn" id="ticketReportSubmitBtn"><i class="fa fa-save"></i>
                            Bericht speichern</button>
                        <button type="button" class="ticket-btn-soft ticket-hidden"
                            id="ticketReportCancelEditBtn">Bearbeitung abbrechen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="ticket-modal" id="ticketReportViewModal">
        <div class="ticket-modal-backdrop" data-close-modal></div>
        <div class="ticket-modal-dialog ticket-report-zoom-dialog">
            <div class="ticket-modal-header">
                <div class="ticket-modal-title">Bericht anzeigen</div>
                <button type="button" class="ticket-modal-close" data-close-modal>×</button>
            </div>
            <div class="ticket-modal-body">
                <h2 class="ticket-report-zoom-title" id="ticketReportViewTitle"></h2>
                <div class="ticket-report-zoom-meta" id="ticketReportViewMeta"></div>
                <div class="ticket-report-zoom-content" id="ticketReportViewContent"></div>
            </div>
        </div>
    </div>

    <div class="ticket-modal" id="ticketMediaModal">
        <div class="ticket-modal-backdrop" data-close-modal></div>
        <div class="ticket-modal-dialog">
            <div class="ticket-modal-header">
                <div class="ticket-modal-title" id="ticketMediaModalTitle">Datei</div>
                <button type="button" class="ticket-modal-close" data-close-modal>×</button>
            </div>
            <div class="ticket-modal-body" id="ticketMediaModalBody"></div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    @if($googleMapsKey !== '')
        <script
            src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsKey }}&libraries=places&callback=initTicketGoogleAppointmentTools"
            async defer></script>
    @endif

    <script>
        window.TICKET_PROFILE_BOOT = {!! $bootJson ?: '{}'!!};
        window.TICKET_ID = {{ (int) $problem->id }};
    </script>

    <script>
        (function () {
            'use strict';


            let ticketReportQuill = null;

            function initTicketReportQuill() {
                const textarea = document.getElementById('ticketReportText');
                if (!textarea || ticketReportQuill || !window.Quill) return;

                const editor = document.createElement('div');
                editor.id = 'ticketReportQuillEditor';
                editor.innerHTML = textarea.value || '';
                textarea.style.display = 'none';
                textarea.parentNode.insertBefore(editor, textarea.nextSibling);

                ticketReportQuill = new Quill(editor, {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{ 'header': [2, 3, false] }],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            ['link', 'blockquote', 'code-block'],
                            ['clean']
                        ]
                    }
                });

                ticketReportQuill.on('text-change', function () {
                    textarea.value = ticketReportQuill.root.innerHTML;
                });
            }

            function setTicketReportEditorHtml(html) {
                const textarea = document.getElementById('ticketReportText');
                if (textarea) textarea.value = html || '';
                if (ticketReportQuill) {
                    ticketReportQuill.root.innerHTML = html || '';
                }
            }

            function getTicketReportEditorHtml() {
                const textarea = document.getElementById('ticketReportText');
                if (ticketReportQuill && textarea) {
                    textarea.value = ticketReportQuill.root.innerHTML;
                }
                return textarea ? textarea.value : '';
            }


            const boot = window.TICKET_PROFILE_BOOT || {};
            const routes = boot.routes || {};
            const csrf = boot.csrf || document.querySelector('meta[name="csrf-token"]')?.content || '';
            const authEmployeeId = parseInt(boot.authEmployeeId || 0);
            let ticketAppointmentsCache = [];
            let availabilityTimer = null;

            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, function (char) {
                    return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
                });
            }

            function toast(message, type) {
                if (window.toastr) {
                    toastr[type === 'danger' ? 'error' : type](message);
                    return;
                }
                console.log(type || 'info', message);
            }

            function updateCounter(key, value) {
                document.querySelectorAll('[data-count-key="' + key + '"]').forEach(el => el.textContent = value);
                if (key === 'reports') document.getElementById('reportCount') && (document.getElementById('reportCount').textContent = value);
                if (key === 'comments') document.getElementById('commentCount') && (document.getElementById('commentCount').textContent = value);
                if (key === 'appointments') document.getElementById('appointmentCount') && (document.getElementById('appointmentCount').textContent = value);
                if (key === 'ticketEmployees') document.getElementById('ticketEmployeeCount') && (document.getElementById('ticketEmployeeCount').textContent = value);
                if (key === 'gallery') {
                    document.getElementById('galleryCount') && (document.getElementById('galleryCount').textContent = value);
                    document.getElementById('galleryCountInline') && (document.getElementById('galleryCountInline').textContent = value);
                }
            }

            function setActivePanel(panelId) {
                document.querySelectorAll('.ticket-panel').forEach(panel => panel.classList.remove('active'));
                document.getElementById(panelId)?.classList.add('active');

                document.querySelectorAll('.ticket-nav-btn').forEach(btn => {
                    btn.classList.toggle('active', btn.dataset.panelTarget === panelId);
                });
            }

            document.addEventListener('click', function (e) {
                const panelBtn = e.target.closest('[data-panel-target], [data-open-panel]');
                if (panelBtn) {
                    e.preventDefault();
                    setActivePanel(panelBtn.dataset.panelTarget || panelBtn.dataset.openPanel);
                    return;
                }

                const innerBtn = e.target.closest('[data-inner-target]');
                if (innerBtn) {
                    const panel = innerBtn.closest('.ticket-panel');
                    panel.querySelectorAll('.ticket-inner-tab').forEach(btn => btn.classList.remove('active'));
                    panel.querySelectorAll('.ticket-inner-pane').forEach(pane => pane.classList.remove('active'));
                    innerBtn.classList.add('active');
                    panel.querySelector('#' + innerBtn.dataset.innerTarget)?.classList.add('active');
                    return;
                }

                const detail = e.target.closest('[data-detail-title]');
                if (detail) {
                    openDetailModal(detail.dataset.detailTitle || 'Details', detail.dataset.detailHtml || '');
                    return;
                }

                if (e.target.closest('[data-close-modal]')) {
                    closeModals();
                }
            });

            function openDetailModal(title, html) {
                document.getElementById('ticketDetailModalTitle').textContent = title;
                document.getElementById('ticketDetailModalBody').innerHTML = html;
                document.getElementById('ticketDetailModal').classList.add('show');
            }

            function openMediaModal(title, bodyHtml) {
                document.getElementById('ticketMediaModalTitle').textContent = title;
                document.getElementById('ticketMediaModalBody').innerHTML = bodyHtml;
                document.getElementById('ticketMediaModal').classList.add('show');
            }

            function closeModals() {
                document.querySelectorAll('.ticket-modal').forEach(m => m.classList.remove('show'));
            }

            function openReportModal(mode) {
                const title = mode === 'edit' ? 'Bericht bearbeiten' : 'Neuer Bericht';
                const titleEl = document.getElementById('ticketReportModalTitle');
                if (titleEl) titleEl.textContent = title;
                document.getElementById('ticketReportModal')?.classList.add('show');
                setTimeout(() => document.getElementById('ticketReportTitle')?.focus(), 80);
            }

            function openReportViewModal(title, report, meta) {
                document.getElementById('ticketReportViewTitle').textContent = title || 'Bericht';
                document.getElementById('ticketReportViewMeta').textContent = meta || '';
                document.getElementById('ticketReportViewContent').textContent = report || '';
                document.getElementById('ticketReportViewModal')?.classList.add('show');
            }

            /*
            |--------------------------------------------------------------------------
            | Status update
            |--------------------------------------------------------------------------
            */
            document.getElementById('problemStatusSelect')?.addEventListener('change', async function () {
                const status = this.value;
                try {
                    const response = await fetch(routes.statusUpdate, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ status })
                    });
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(data.message || 'Status konnte nicht gespeichert werden.');
                    const label = (boot.statusOptions || {})[status] || status;
                    ['sideStatusPill', 'problemStatusSmallText', 'mediaStatusText'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.textContent = label;
                    });
                    toast('Status wurde aktualisiert.', 'success');
                } catch (error) {
                    toast(error.message, 'danger');
                }
            });

            document.getElementById('openNewReportModalBtn')?.addEventListener('click', function () {
                clearReportForm();
                openReportModal('create');
            });

            document.getElementById('topOpenNewReportModalBtn')?.addEventListener('click', function () {
                setActivePanel('ticket-reports-panel');
                clearReportForm();
                openReportModal('create');
            });

            /*
            |--------------------------------------------------------------------------
            | Reports - realtime AJAX, no reload
            |--------------------------------------------------------------------------
            */
            const reportForm = document.getElementById('ticketReportForm');
            reportForm?.addEventListener('submit', async function (e) {
                e.preventDefault();
                e.stopPropagation();

                const btn = document.getElementById('ticketReportSubmitBtn');
                const editId = document.getElementById('ticketReportEditId').value;
                getTicketReportEditorHtml();
                const fd = new FormData(reportForm);
                btn.disabled = true;

                let url = routes.reportStore;
                if (editId) {
                    url = routes.reportBase + '/' + editId;
                    fd.append('_method', 'PUT');
                }

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: fd
                    });
                    const data = await response.json();
                    if (!response.ok || !data.success) throw new Error(data.message || 'Bericht konnte nicht gespeichert werden.');

                    const report = data.data || data.report;
                    upsertReportCard(report);
                    clearReportForm();
                    closeModals();
                    toast(data.message || 'Bericht gespeichert.', 'success');
                } catch (error) {
                    toast(error.message, 'danger');
                } finally {
                    btn.disabled = false;
                }
            });

            function clearReportForm() {
                document.getElementById('ticketReportEditId').value = '';
                document.getElementById('ticketReportTitle').value = '';
                setTicketReportEditorHtml('');
                document.getElementById('ticketReportSubmitBtn').innerHTML = '<i class="fa fa-save"></i> Bericht speichern';
                document.getElementById('ticketReportCancelEditBtn').classList.add('ticket-hidden');
                const titleEl = document.getElementById('ticketReportModalTitle');
                if (titleEl) titleEl.textContent = 'Neuer Bericht';
            }

            document.getElementById('ticketReportCancelEditBtn')?.addEventListener('click', clearReportForm);

            function upsertReportCard(report) {
                if (!report) return;

                document.getElementById('ticketReportEmpty')?.remove();

                const html = `
                    <div class="ticket-report-head">
                        <div>
                            <h4 class="ticket-report-title">${escapeHtml(report.title)}</h4>
                            <div class="ticket-report-meta">${escapeHtml(report.employee_name || 'N/A')} · ${escapeHtml(report.created_at_human || report.report_date || '')}</div>
                        </div>
                        <div class="ticket-report-actions">
                            <button type="button" class="ticket-btn-soft js-report-view" data-report-id="${report.id}" data-title="${escapeHtml(report.title)}" data-report="${report.report || ''}" data-meta="${escapeHtml((report.employee_name || 'N/A') + ' · ' + (report.created_at_human || report.report_date || ''))}"><i class="fa fa-search-plus"></i> Zoom</button>
                            <button type="button" class="ticket-btn-soft js-report-like" data-report-id="${report.id}"><i class="fa fa-thumbs-up"></i> <span>${report.likes || 0}</span></button>
                            <button type="button" class="ticket-btn-soft js-report-edit" data-report-id="${report.id}" data-title="${escapeHtml(report.title)}" data-report="${report.report || ''}"><i class="fa fa-edit"></i></button>
                            <button type="button" class="ticket-btn-soft ticket-btn-danger js-report-delete" data-report-id="${report.id}"><i class="fa fa-trash"></i></button>
                        </div>
                    </div>
                    <div class="ticket-report-content">${report.report || ""}</div>
                `;

                let card = document.querySelector(`.ticket-report-card[data-report-id="${report.id}"]`);
                if (!card) {
                    card = document.createElement('div');
                    card.className = 'ticket-report-card';
                    card.dataset.reportId = report.id;
                    document.getElementById('ticketReportList').prepend(card);
                    updateCounter('reports', document.querySelectorAll('.ticket-report-card').length);
                }

                card.innerHTML = html;
            }

            document.addEventListener('click', async function (e) {
                const view = e.target.closest('.js-report-view');
                if (view) {
                    openReportViewModal(view.dataset.title || 'Bericht', view.dataset.report || '', view.dataset.meta || '');
                    return;
                }

                const edit = e.target.closest('.js-report-edit');
                if (edit) {
                    document.getElementById('ticketReportEditId').value = edit.dataset.reportId;
                    document.getElementById('ticketReportTitle').value = edit.dataset.title || '';
                    setTicketReportEditorHtml(edit.dataset.report || '');
                    document.getElementById('ticketReportSubmitBtn').innerHTML = '<i class="fa fa-save"></i> Bericht aktualisieren';
                    document.getElementById('ticketReportCancelEditBtn').classList.remove('ticket-hidden');
                    setActivePanel('ticket-reports-panel');
                    openReportModal('edit');
                    return;
                }

                const del = e.target.closest('.js-report-delete');
                if (del) {
                    if (!confirm('Bericht wirklich löschen?')) return;
                    const id = del.dataset.reportId;
                    try {
                        const response = await fetch(routes.reportBase + '/' + id, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            body: new URLSearchParams({ _method: 'DELETE' })
                        });
                        const data = await response.json();
                        if (!response.ok || !data.success) throw new Error(data.message || 'Bericht konnte nicht gelöscht werden.');
                        document.querySelector(`.ticket-report-card[data-report-id="${id}"]`)?.remove();
                        updateCounter('reports', document.querySelectorAll('.ticket-report-card').length);
                        toast(data.message || 'Bericht gelöscht.', 'success');
                    } catch (error) { toast(error.message, 'danger'); }
                    return;
                }

                const like = e.target.closest('.js-report-like');
                if (like) {
                    const id = like.dataset.reportId;
                    try {
                        const response = await fetch(routes.reportBase + '/' + id + '/like', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await response.json();
                        if (data.success) like.querySelector('span').textContent = data.likes;
                    } catch (error) { toast('Like konnte nicht gespeichert werden.', 'danger'); }
                }
            });

            /*
            |--------------------------------------------------------------------------
            | Chat comments
            |--------------------------------------------------------------------------
            */
            async function loadComments() {
                const list = document.getElementById('ticketChatList');
                list.innerHTML = '<div class="ticket-empty">Kommentare werden geladen...</div>';

                try {
                    const response = await fetch(routes.commentsFetch, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                    const comments = await response.json();
                    renderComments(Array.isArray(comments) ? comments : []);
                } catch (error) {
                    list.innerHTML = '<div class="ticket-empty">Kommentare konnten nicht geladen werden.</div>';
                }
            }

            function renderComments(comments) {
                const list = document.getElementById('ticketChatList');
                if (!comments.length) {
                    list.innerHTML = '<div class="ticket-empty">Noch keine Kommentare vorhanden.</div>';
                    updateCounter('comments', 0);
                    return;
                }

                list.innerHTML = comments.map(renderCommentBubble).join('');
                updateCounter('comments', comments.length);
                list.scrollTop = list.scrollHeight;
            }

            function renderCommentBubble(comment) {
                const employee = comment.employee || comment.commented_by || {};
                const employeeId = parseInt(comment.employee_id || employee.id || 0);
                const mine = employeeId === authEmployeeId;
                const name = `${employee.name || ''} ${employee.lastname || ''}`.trim() || 'Mitarbeiter';
                const img = employee.image
                    ? (String(employee.image).startsWith('http') ? employee.image : '/images/employee/' + employee.image)
                    : '/images/gender/male.png';
                const time = comment.created_at_human || comment.created_at || '';

                return `
                    <div class="ticket-chat-bubble ${mine ? 'mine' : ''}" data-comment-id="${comment.id}">
                        <img class="ticket-chat-avatar" src="${img}" alt="">
                        <div class="ticket-chat-message">
                            <div class="ticket-chat-name">${escapeHtml(name)}</div>
                            <div class="ticket-chat-text">${escapeHtml(comment.comment || '')}</div>
                            <div class="ticket-chat-time">${escapeHtml(time)}</div>
                        </div>
                    </div>
                `;
            }

            document.getElementById('ticketChatForm')?.addEventListener('submit', async function (e) {
                e.preventDefault();
                e.stopPropagation();

                const input = document.getElementById('ticketChatInput');
                const btn = document.getElementById('ticketChatSendBtn');
                const text = input.value.trim();
                if (!text) return;

                const fd = new FormData(this);
                btn.disabled = true;

                try {
                    const response = await fetch(routes.commentsStore, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: fd
                    });
                    const data = await response.json();
                    if (!response.ok || !data.success) throw new Error(data.message || data.error || 'Kommentar konnte nicht gespeichert werden.');

                    input.value = '';
                    await loadComments();
                } catch (error) {
                    toast(error.message, 'danger');
                } finally {
                    btn.disabled = false;
                }
            });

            document.getElementById('reloadCommentsBtn')?.addEventListener('click', loadComments);

            /*
            |--------------------------------------------------------------------------
            | Dropzone gallery + PDFs
            |--------------------------------------------------------------------------
            */
            Dropzone.autoDiscover = false;

            function initDropzone() {
                const form = document.getElementById('ticketDropzone');
                if (!form || form.dropzone) return;

                new Dropzone(form, {
                    url: routes.galleryUpload,
                    paramName: 'file',
                    maxFilesize: 12,
                    acceptedFiles: 'image/*,application/pdf,.pdf,.doc,.docx,.xls,.xlsx',
                    headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest' },
                    uploadMultiple: false,
                    parallelUploads: 2,
                    timeout: 120000,
                    params: {
                        ticket_id: boot.ticketId,
                        stage: boot.currentType || 'ticket'
                    },
                    success: function () {
                        loadGallery();
                    },
                    error: function (file, message) {
                        toast(typeof message === 'string' ? message : 'Upload fehlgeschlagen.', 'danger');
                    }
                });
            }

            async function loadGallery() {
                const grid = document.getElementById('ticketGalleryGrid');
                grid.innerHTML = '<div class="ticket-empty">Galerie wird geladen...</div>';

                try {
                    const response = await fetch(routes.galleryList, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await response.json();
                    const files = Array.isArray(data) ? data : (data.files || data.images || []);
                    renderGallery(files);
                } catch (error) {
                    grid.innerHTML = '<div class="ticket-empty">Galerie konnte nicht geladen werden.</div>';
                }
            }

            function fileUrl(file) {
                return file.url || file.file_url || file.image_url || (file.image ? ('/storage/' + String(file.image).replace(/^public\//, '')) : '#');
            }

            function isImageFile(file) {
                const type = String(file.file_type || file.mime_type || '').toLowerCase();
                const name = String(file.image_name || file.name || file.image || '').toLowerCase();
                return type.startsWith('image/') || /\.(jpg|jpeg|png|gif|webp|bmp|svg)$/.test(name);
            }

            function isPdfFile(file) {
                const type = String(file.file_type || file.mime_type || '').toLowerCase();
                const name = String(file.image_name || file.name || file.image || '').toLowerCase();
                return type.includes('pdf') || name.endsWith('.pdf');
            }

            function renderGallery(files) {
                const grid = document.getElementById('ticketGalleryGrid');

                if (!files.length) {
                    grid.innerHTML = '<div class="ticket-empty">Noch keine Dateien hochgeladen.</div>';
                    updateCounter('gallery', 0);
                    return;
                }

                grid.innerHTML = files.map(file => {
                    const url = fileUrl(file);
                    const name = file.image_name || file.name || 'Datei';
                    const image = isImageFile(file);
                    const pdf = isPdfFile(file);
                    const thumb = image
                        ? `<img src="${url}" alt="${escapeHtml(name)}">`
                        : `<div class="ticket-file-icon"><i class="fa ${pdf ? 'fa-file-pdf' : 'fa-file'}"></i></div>`;

                    return `
                        <div class="ticket-gallery-card" data-file-id="${file.id}">
                            <div class="ticket-gallery-thumb js-open-file" data-url="${escapeHtml(url)}" data-name="${escapeHtml(name)}" data-is-image="${image ? '1' : '0'}" data-is-pdf="${pdf ? '1' : '0'}">${thumb}</div>
                            <div class="ticket-gallery-caption">${escapeHtml(name)}</div>
                            <div class="ticket-gallery-actions">
                                <button type="button" class="ticket-btn-soft js-open-file" data-url="${escapeHtml(url)}" data-name="${escapeHtml(name)}" data-is-image="${image ? '1' : '0'}" data-is-pdf="${pdf ? '1' : '0'}"><i class="fa fa-eye"></i></button>
                                <button type="button" class="ticket-btn-soft ticket-btn-danger js-delete-file" data-file-id="${file.id}"><i class="fa fa-trash"></i></button>
                            </div>
                        </div>
                    `;
                }).join('');

                updateCounter('gallery', files.length);
            }

            document.getElementById('reloadGalleryBtn')?.addEventListener('click', loadGallery);

            document.addEventListener('click', async function (e) {
                const open = e.target.closest('.js-open-file');
                if (open) {
                    const url = open.dataset.url;
                    const name = open.dataset.name || 'Datei';
                    const image = open.dataset.isImage === '1';
                    const pdf = open.dataset.isPdf === '1';

                    if (image) {
                        openMediaModal(name, `<img src="${url}" alt="${escapeHtml(name)}">`);
                    } else if (pdf) {
                        openMediaModal(name, `<iframe src="${url}"></iframe>`);
                    } else {
                        openMediaModal(name, `<p>Diese Datei kann nicht direkt angezeigt werden.</p><a class="ticket-btn" href="${url}" target="_blank">Datei öffnen</a>`);
                    }
                    return;
                }

                const del = e.target.closest('.js-delete-file');
                if (del) {
                    if (!confirm('Datei wirklich löschen?')) return;
                    try {
                        const response = await fetch(routes.galleryDeleteBase + '/' + del.dataset.fileId, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            body: new URLSearchParams({ _method: 'DELETE' })
                        });
                        const data = await response.json();
                        if (!response.ok || !data.success) throw new Error(data.message || 'Datei konnte nicht gelöscht werden.');
                        await loadGallery();
                    } catch (error) { toast(error.message, 'danger'); }
                }
            });

            /*
            |--------------------------------------------------------------------------
            | Ticket type update
            |--------------------------------------------------------------------------
            */
            document.addEventListener('click', async function (e) {
                const card = e.target.closest('.ticket-type-card');
                if (!card) return;

                const error_type = card.dataset.typeKey;
                try {
                    const response = await fetch(routes.typeUpdate, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({ error_type })
                    });
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) throw new Error(data.message || 'Typ konnte nicht gespeichert werden.');

                    document.querySelectorAll('.ticket-type-card').forEach(el => el.classList.remove('active'));
                    card.classList.add('active');
                    ['sideTypeLabel', 'detailTypeLabel', 'mediaTypeText'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.textContent = card.dataset.typeLabel || error_type;
                    });
                    toast('Tickettyp wurde aktualisiert.', 'success');
                } catch (error) {
                    toast(error.message, 'danger');
                }
            });


            /*
            |--------------------------------------------------------------------------
            | Ticket Team - employee_problem AJAX
            |--------------------------------------------------------------------------
            */
            function formatTicketEmployeeOption(employee) {
                if (!employee.id) return employee.text;
                const img = employee.image || '/images/gender/male.png';
                const name = employee.name || employee.text || '';
                return `<span style="display:flex;align-items:center;gap:8px;">
                    <img src="${img}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;">
                    <span><strong>${escapeHtml(name)}</strong></span>
                </span>`;
            }

            function initTicketEmployeeSelect() {
                const $select = $('#ticketEmployeeSelect');
                if (!$select.length || !$.fn.select2) return;

                $select.select2({
                    width: '100%',
                    placeholder: 'Mitarbeiter suchen und auswählen',
                    allowClear: true,
                    multiple: true,
                    dropdownParent: $('#ticketEmployeeModal'),
                    ajax: {
                        url: routes.ticketEmployeesSearch || routes.appointmentEmployeeSearch,
                        dataType: 'json',
                        delay: 180,
                        data: params => ({ q: params.term || '' }),
                        processResults: data => data
                    },
                    templateResult: formatTicketEmployeeOption,
                    templateSelection: formatTicketEmployeeOption,
                    escapeMarkup: markup => markup
                });

                seedTicketEmployeeSelect(boot.ticketEmployees || []);
            }

            function seedTicketEmployeeSelect(employees) {
                const $select = $('#ticketEmployeeSelect');
                if (!$select.length) return;

                $select.empty();

                (employees || []).forEach(emp => {
                    const option = new Option(emp.name || emp.text || ('#' + emp.id), emp.id, true, true);
                    option.dataset.image = emp.image || '';
                    $select.append(option);
                });

                $select.trigger('change');
            }

            function openTicketEmployeeModal() {
                seedTicketEmployeeSelect(boot.ticketEmployees || []);
                document.getElementById('ticketEmployeeModal')?.classList.add('show');
                setTimeout(() => $('#ticketEmployeeSelect').select2('open'), 160);
            }

            document.getElementById('openTicketEmployeeModalBtn')?.addEventListener('click', openTicketEmployeeModal);
            document.getElementById('openTicketEmployeeModalBtnPanel')?.addEventListener('click', openTicketEmployeeModal);

            document.getElementById('ticketEmployeeForm')?.addEventListener('submit', async function (e) {
                e.preventDefault();
                const btn = document.getElementById('ticketEmployeeSaveBtn');
                const ids = $('#ticketEmployeeSelect').val() || [];
                const fd = new FormData();
                ids.forEach(id => fd.append('employee_ids[]', id));

                btn.disabled = true;

                try {
                    const response = await fetch(routes.ticketEmployeesSync, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: fd
                    });

                    const data = await response.json();
                    if (!response.ok || !data.success) throw new Error(data.message || 'Ticket-Team konnte nicht gespeichert werden.');

                    boot.ticketEmployees = data.employees || [];
                    boot.ticketEmployeeIds = boot.ticketEmployees.map(e => e.id);
                    renderTicketEmployees(boot.ticketEmployees);
                    updateCounter('ticketEmployees', boot.ticketEmployees.length);
                    closeModals();
                    toast(data.message || 'Ticket-Team gespeichert.', 'success');
                } catch (error) {
                    toast(error.message, 'danger');
                } finally {
                    btn.disabled = false;
                }
            });

            async function loadTicketEmployees() {
                if (!routes.ticketEmployeesIndex) return;

                try {
                    const response = await fetch(routes.ticketEmployeesIndex, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await response.json();
                    if (data.success) {
                        boot.ticketEmployees = data.employees || [];
                        boot.ticketEmployeeIds = boot.ticketEmployees.map(e => e.id);
                        renderTicketEmployees(boot.ticketEmployees);
                        updateCounter('ticketEmployees', boot.ticketEmployees.length);
                    }
                } catch (error) { }
            }

            function renderTicketEmployees(employees) {
                const html = employees && employees.length
                    ? employees.map(emp => `
                        <span class="ticket-team-mini" title="${escapeHtml(emp.name || '')}">
                            <img src="${emp.image || '/images/gender/male.png'}" alt="">
                            <span>${escapeHtml(emp.name || ('#' + emp.id))}</span>
                        </span>
                    `).join('')
                    : '<div class="ticket-team-empty">Noch kein Mitarbeiter im Ticket.</div>';

                ['ticketSidebarEmployeeList', 'ticketPanelEmployeeList'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.innerHTML = html;
                });
            }

            /*
                |--------------------------------------------------------------------------
                | Appointment AJAX
                |--------------------------------------------------------------------------
                */
            function initEmployeeSelect2() {
                const $select = $('#ticketAppointmentEmployees');
                if (!$select.length || !$.fn.select2) return;

                $select.select2({
                    width: '100%',
                    placeholder: 'Mitarbeiter auswählen',
                    allowClear: true,
                    ajax: {
                        url: routes.appointmentEmployeeSearch,
                        dataType: 'json',
                        delay: 180,
                        data: params => ({ q: params.term || '' }),
                        processResults: data => data
                    },
                    templateResult: formatEmployee,
                    templateSelection: formatEmployee,
                    escapeMarkup: markup => markup
                });

                $select.on('change', scheduleAvailabilityCheck);
            }

            function formatEmployee(employee) {
                if (!employee.id) return employee.text;
                const img = employee.image || '/images/gender/male.png';
                const name = employee.name || employee.text || '';
                const work = employee.daily_start_time && employee.daily_end_time ? `<small style="display:block;color:#6b7280;">${employee.daily_start_time} - ${employee.daily_end_time}</small>` : '';
                return `<span style="display:flex;align-items:center;gap:8px;"><img src="${img}" style="width:28px;height:28px;border-radius:50%;object-fit:cover;"><span><strong>${escapeHtml(name)}</strong>${work}</span></span>`;
            }

            function collectAppointmentPayload(forceSave) {
                const form = document.getElementById('ticketAppointmentForm');
                const fd = new FormData(form);
                fd.set('force_save', forceSave ? '1' : '0');
                fd.delete('employee_ids[]');
                ($('#ticketAppointmentEmployees').val() || []).forEach(id => fd.append('employee_ids[]', id));
                fd.set('travel_summary', document.getElementById('ticketTravelSummary')?.value || '');
                return fd;
            }

            function scheduleAvailabilityCheck() {
                clearTimeout(availabilityTimer);
                availabilityTimer = setTimeout(checkAvailabilityRealtime, 350);
            }

            async function checkAvailabilityRealtime() {
                const box = document.getElementById('ticketAppointmentConflictBox');
                const form = document.getElementById('ticketAppointmentForm');
                const employees = $('#ticketAppointmentEmployees').val() || [];
                if (!form || !box || !form.start_date.value || !form.start_time.value || !form.end_time.value || employees.length === 0) {
                    box.classList.add('ticket-hidden');
                    return;
                }

                box.classList.remove('ticket-hidden');
                box.innerHTML = '<div class="ticket-badge orange">Verfügbarkeit wird geprüft...</div>';

                try {
                    const response = await fetch(routes.appointmentCheck, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: collectAppointmentPayload(false)
                    });
                    const data = await response.json();
                    renderAppointmentConflicts(data);
                } catch (error) {
                    box.innerHTML = '<div class="ticket-badge red">Verfügbarkeit konnte nicht geprüft werden.</div>';
                }
            }

            function renderAppointmentConflicts(data) {
                const box = document.getElementById('ticketAppointmentConflictBox');
                const forceBtn = document.getElementById('ticketAppointmentForceSaveBtn');

                if (!data.has_conflicts) {
                    box.innerHTML = '<div class="ticket-badge green">Alle Mitarbeiter sind verfügbar.</div>';
                    forceBtn.classList.add('ticket-hidden');
                    return;
                }

                const items = (data.conflicts || []).map(c => `<li><strong>${escapeHtml(c.employee_name || '')}</strong>: ${escapeHtml(c.message || '')}</li>`).join('');
                box.innerHTML = `<div class="ticket-date-box" style="border-color:#fecaca;background:#fff7f7"><strong>Terminkonflikte gefunden</strong><ul style="margin:8px 0 0">${items}</ul></div>`;
                forceBtn.classList.remove('ticket-hidden');
            }

            document.getElementById('ticketAppointmentForm')?.addEventListener('submit', function (e) {
                e.preventDefault();
                saveAppointment(false);
            });

            document.getElementById('ticketAppointmentForceSaveBtn')?.addEventListener('click', function () {
                saveAppointment(true);
            });

            ['start_date', 'end_date', 'start_time', 'end_time'].forEach(name => {
                document.querySelector(`#ticketAppointmentForm [name="${name}"]`)?.addEventListener('change', scheduleAvailabilityCheck);
            });

            async function saveAppointment(forceSave) {
                const form = document.getElementById('ticketAppointmentForm');
                const id = form.appointment_id.value;
                const btn = document.getElementById('ticketAppointmentSaveBtn');
                const fd = collectAppointmentPayload(forceSave);
                let url = routes.appointmentStore;

                if (id) {
                    url = routes.appointmentBase + '/' + id;
                    fd.append('_method', 'PUT');
                }

                btn.disabled = true;

                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        body: fd
                    });
                    const data = await response.json();

                    if (response.status === 409 && data.requires_force) {
                        renderAppointmentConflicts({ has_conflicts: true, conflicts: data.conflicts || [] });
                        return;
                    }

                    if (!response.ok || !data.success) throw new Error(data.message || 'Ticket konnte nicht gespeichert werden.');

                    form.reset();
                    if (form.name) form.name.value = boot.defaultAppointmentTitle || '';
                    $('#ticketAppointmentEmployees').val(null).trigger('change');
                    document.getElementById('ticketAppointmentForceSaveBtn').classList.add('ticket-hidden');
                    document.getElementById('ticketAppointmentConflictBox').classList.add('ticket-hidden');
                    await loadAppointments();
                    toast((data.message || 'Ticket gespeichert.').replace(/Termin/g, 'Ticket'), 'success');
                } catch (error) {
                    toast(error.message, 'danger');
                } finally {
                    btn.disabled = false;
                }
            }

            async function loadAppointments() {
                const list = document.getElementById('ticketAppointmentList');
                list.innerHTML = '<div class="ticket-empty">Ticket-Termine werden geladen...</div>';

                try {
                    const response = await fetch(routes.appointmentIndex, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await response.json();
                    ticketAppointmentsCache = data.appointments || [];
                    renderAppointments(ticketAppointmentsCache);
                } catch (error) {
                    list.innerHTML = '<div class="ticket-empty">Ticket-Termine konnten nicht geladen werden.</div>';
                }
            }

            function renderAppointments(appointments) {
                const list = document.getElementById('ticketAppointmentList');
                if (!appointments.length) {
                    list.innerHTML = '<div class="ticket-empty">Noch keine Ticket-Termine geplant.</div>';
                    updateCounter('appointments', 0);
                    return;
                }

                list.innerHTML = appointments.map(a => {
                    const employees = (a.employees || []).map(emp => `<span class="ticket-employee-pill"><img src="${emp.image}" alt="">${escapeHtml(emp.name)}</span>`).join('');
                    return `
                        <div class="ticket-appointment-row" data-appointment-id="${a.id}">
                            <div class="ticket-appointment-main">
                                <div class="ticket-appointment-date"><strong>${escapeHtml(a.start_date || '')}</strong><span>${escapeHtml(a.start_time || '')} - ${escapeHtml(a.end_time || '')}</span></div>
                                <div class="ticket-appointment-content">
                                    <h5>${escapeHtml(a.name || 'Ticket')}</h5>
                                    <p>${escapeHtml(a.full_address || '')}</p>
                                    <div class="ticket-employee-list">${employees}</div>
                                </div>
                            </div>
                            <div class="ticket-appointment-actions">
                                <button type="button" class="ticket-btn-soft js-edit-appointment" data-id="${a.id}">Bearbeiten</button>
                                <button type="button" class="ticket-btn-soft ticket-btn-danger js-delete-appointment" data-id="${a.id}">Löschen</button>
                            </div>
                        </div>`;
                }).join('');

                updateCounter('appointments', appointments.length);
            }

            document.addEventListener('click', async function (e) {
                const edit = e.target.closest('.js-edit-appointment');
                if (edit) {
                    const a = ticketAppointmentsCache.find(item => parseInt(item.id) === parseInt(edit.dataset.id));
                    if (!a) return;
                    const form = document.getElementById('ticketAppointmentForm');
                    form.appointment_id.value = a.id;
                    form.name.value = a.name || '';
                    form.note.value = a.note || '';
                    form.start_date.value = a.start_date || '';
                    form.end_date.value = a.end_date || a.start_date || '';
                    form.start_time.value = a.start_time || '';
                    form.end_time.value = a.end_time || '';
                    form.status.value = a.status || 'planned';
                    form.priority.value = a.priority || 'normal';
                    form.full_address.value = a.full_address || '';
                    form.street.value = a.street || '';
                    form.postcode.value = a.postcode || '';
                    form.city.value = a.city || '';
                    form.latitude.value = a.latitude || '';
                    form.longitude.value = a.longitude || '';

                    const $select = $('#ticketAppointmentEmployees');
                    $select.empty();
                    (a.employees || []).forEach(emp => $select.append(new Option(emp.name, emp.id, true, true)));
                    $select.trigger('change');
                    setActivePanel('ticket-calendar-panel');
                    return;
                }

                const del = e.target.closest('.js-delete-appointment');
                if (del) {
                    if (!confirm('Ticket-Termin wirklich löschen?')) return;
                    try {
                        const response = await fetch(routes.appointmentBase + '/' + del.dataset.id, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            body: new URLSearchParams({ _method: 'DELETE' })
                        });
                        const data = await response.json();
                        if (!response.ok || !data.success) throw new Error(data.message || 'Termin konnte nicht gelöscht werden.');
                        await loadAppointments();
                    } catch (error) { toast(error.message, 'danger'); }
                }
            });

            /*
            |--------------------------------------------------------------------------
            | Google autocomplete + driving time
            |--------------------------------------------------------------------------
            */
            window.initTicketGoogleAppointmentTools = function () {
                const input = document.getElementById('ticketAppointmentAddress');
                if (!input || !window.google || !google.maps || !google.maps.places) return;

                const autocomplete = new google.maps.places.Autocomplete(input, {
                    fields: ['address_components', 'formatted_address', 'geometry'],
                    componentRestrictions: { country: ['de'] }
                });

                autocomplete.addListener('place_changed', function () {
                    const place = autocomplete.getPlace();
                    if (!place || !place.geometry) return;

                    document.querySelector('[name="full_address"]').value = place.formatted_address || '';
                    document.querySelector('[name="latitude"]').value = place.geometry.location.lat();
                    document.querySelector('[name="longitude"]').value = place.geometry.location.lng();

                    let streetNumber = '', route = '', postalCode = '', city = '';
                    (place.address_components || []).forEach(component => {
                        const types = component.types || [];
                        if (types.includes('street_number')) streetNumber = component.long_name;
                        if (types.includes('route')) route = component.long_name;
                        if (types.includes('postal_code')) postalCode = component.long_name;
                        if (types.includes('locality')) city = component.long_name;
                        if (!city && types.includes('postal_town')) city = component.long_name;
                    });

                    document.querySelector('[name="street"]').value = `${route} ${streetNumber}`.trim();
                    document.querySelector('[name="postcode"]').value = postalCode;
                    document.querySelector('[name="city"]').value = city;

                    calculateTravelTime(place.geometry.location);
                });
            };

            function calculateTravelTime(destinationLatLng) {
                const summaryInput = document.getElementById('ticketTravelSummary');
                const summaryBox = document.getElementById('ticketTravelSummaryBox');

                if (!navigator.geolocation || !window.google || !google.maps) return;
                summaryBox.innerHTML = 'Anfahrt wird berechnet...';

                navigator.geolocation.getCurrentPosition(function (position) {
                    const origin = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
                    const service = new google.maps.DistanceMatrixService();

                    service.getDistanceMatrix({
                        origins: [origin],
                        destinations: [destinationLatLng],
                        travelMode: google.maps.TravelMode.DRIVING,
                        unitSystem: google.maps.UnitSystem.METRIC
                    }, function (response, status) {
                        if (status !== 'OK') {
                            summaryBox.innerHTML = 'Anfahrt konnte nicht berechnet werden.';
                            return;
                        }

                        const result = response.rows?.[0]?.elements?.[0];
                        if (!result || result.status !== 'OK') {
                            summaryBox.innerHTML = 'Keine Route gefunden.';
                            return;
                        }

                        const summary = `${result.duration.text} · ${result.distance.text}`;
                        summaryInput.value = summary;
                        summaryBox.innerHTML = `Anfahrt: <strong>${summary}</strong>`;
                    });
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Init
            |--------------------------------------------------------------------------
            */
            document.addEventListener('DOMContentLoaded', function () {
                initTicketReportQuill();
                initEmployeeSelect2();
                initTicketEmployeeSelect();
                loadTicketEmployees();
                initDropzone();
                loadComments();
                loadGallery();
                loadAppointments();
                const appointmentForm = document.getElementById('ticketAppointmentForm');
                if (appointmentForm && appointmentForm.name && !appointmentForm.name.value) {
                    appointmentForm.name.value = boot.defaultAppointmentTitle || '';
                }
                setTimeout(window.initTicketGoogleAppointmentTools, 600);
            });
        })();

        /*
        |--------------------------------------------------------------------------
        | Ticket task fix: ticket_tasks uses ticket_id, not problem_id
        |--------------------------------------------------------------------------
        */
        document.getElementById('ticketTaskForm')?.addEventListener('submit', function () {
            const form = this;
            let ticketInput = form.querySelector('[name="ticket_id"]');

            if (!ticketInput) {
                ticketInput = document.createElement('input');
                ticketInput.type = 'hidden';
                ticketInput.name = 'ticket_id';
                form.appendChild(ticketInput);
            }

            ticketInput.value = String((window.TICKET_BOOT && window.TICKET_BOOT.ticketId) || '{{ $problem->id }}');
        });

    </script>
@endsection