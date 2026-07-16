@extends('admin.layouts.app')
@section('title', 'Feinaufmaß Kanban')

@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    $authEmployeeId = (string) (auth()->user()->name ?? auth()->id());
    $allMeasurements = collect($measurements ?? []);
    $allEmployees = collect($employees ?? []);

    $statusColumns = [
        'not_assigned' => ['label' => 'Nicht geplant', 'color' => '#94a3b8', 'icon' => 'clock'],
        'appointment_created' => ['label' => 'Termin geplant', 'color' => '#74b2d4', 'icon' => 'calendar'],
        'task_created' => ['label' => 'Termin & Aufgabe', 'color' => '#93c21c', 'icon' => 'check-circle'],
        'completed' => ['label' => 'Abgeschlossen', 'color' => '#10b981', 'icon' => 'check'],
        'canceled' => ['label' => 'Abgebrochen', 'color' => '#ef4444', 'icon' => 'x-circle'],
    ];

    $employeeLookup = $allEmployees->mapWithKeys(function ($employee) {
        $name = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
        if ($name === '') {
            $name = $employee->full_name ?? $employee->display_name ?? $employee->email ?? ('Mitarbeiter #' . $employee->id);
        }

        $image = $employee->image ?? $employee->profile_image ?? $employee->picture ?? $employee->photo ?? null;
        $imageUrl = $image
            ? (Str::startsWith($image, ['http://', 'https://', 'data:']) ? $image : asset('images/employee/' . ltrim($image, '/')))
            : asset('images/icons/placeholder.svg');

        return [
            (string) $employee->id => [
                'id' => (string) $employee->id,
                'name' => $name,
                'image' => $imageUrl,
                'status' => $employee->status ?? 'Active',
            ]
        ];
    });

    $measurementRows = $allMeasurements->map(function ($measurement) use ($employeeLookup, $statusColumns) {
        $customer = $measurement->customer ?? null;
        $alternative = $measurement->alternative ?? null;
        $product = $measurement->product ?? null;
        $offer = $measurement->offer ?? null;

        $customerName = trim(($customer->firma ?? '') . ' ' . ($customer->name ?? '') . ' ' . ($customer->lastname ?? '')) ?: 'Unbekannter Kunde';
        $productName = $measurement->product_label
            ?? $product->article_group
            ?? $product->product
            ?? $product->name
            ?? $product->title
            ?? 'Produkt';

        $address = $alternative->full_address
            ?? trim(($alternative->street ?? '') . ' ' . ($alternative->postcode ?? '') . ' ' . ($alternative->city ?? ''));
        if (trim($address) === '') {
            $address = trim(($customer->street ?? '') . ' ' . ($customer->postcode ?? '') . ' ' . ($customer->city ?? ''));
        }

        $status = $measurement->assignment_status ?: 'not_assigned';
        if (!isset($statusColumns[$status])) {
            $status = 'not_assigned';
        }

        $responsibleId = (string) ($measurement->responsible_employee_id ?? '');
        $responsible = $responsibleId !== '' ? ($employeeLookup[$responsibleId] ?? null) : null;

        $team = collect();
        if ($responsible) {
            $team->push(array_merge($responsible, ['role' => 'Verantwortlich']));
        }

        $appointmentEmployees = collect(optional($measurement->appointmentRecord ?? $measurement->appointment ?? null)->employees ?? []);
        foreach ($appointmentEmployees as $employee) {
            $id = (string) ($employee->id ?? $employee->employee_id ?? '');
            if ($id !== '' && isset($employeeLookup[$id]) && !$team->contains('id', $id)) {
                $team->push(array_merge($employeeLookup[$id], ['role' => 'Termin']));
            }
        }

        $taskEmployees = collect(optional($measurement->personalTaskRecord ?? $measurement->personalTask ?? null)->employees ?? []);
        foreach ($taskEmployees as $employee) {
            $id = (string) ($employee->id ?? $employee->employee_id ?? '');
            if ($id !== '' && isset($employeeLookup[$id]) && !$team->contains('id', $id)) {
                $team->push(array_merge($employeeLookup[$id], ['role' => 'Aufgabe']));
            }
        }

        $creatorId = (string) ($measurement->created_by ?? '');
        if ($team->isEmpty() && $creatorId !== '' && isset($employeeLookup[$creatorId])) {
            $team->push(array_merge($employeeLookup[$creatorId], ['role' => 'Erstellt']));
        }

        $scheduledDate = $measurement->scheduled_start_date ? Carbon::parse($measurement->scheduled_start_date)->format('d.m.Y') : null;
        $scheduledEndDate = $measurement->scheduled_end_date ? Carbon::parse($measurement->scheduled_end_date)->format('d.m.Y') : null;
        $startTime = $measurement->scheduled_start_time ? substr((string) $measurement->scheduled_start_time, 0, 5) : null;
        $endTime = $measurement->scheduled_end_time ? substr((string) $measurement->scheduled_end_time, 0, 5) : null;

        return [
            'id' => (int) $measurement->id,
            'deal_measurement_id' => (int) $measurement->id,
            'deal_id' => (int) ($measurement->deal_id ?? 0),
            'customer_id' => (int) ($measurement->customer_id ?? 0),
            'alternative_id' => (int) ($measurement->alternative_id ?? 0),
            'product_id' => (int) ($measurement->product_id ?? 0),
            'measurement_no' => $measurement->measurement_no ?? ('FM-' . $measurement->id),
            'order_number' => $measurement->order_number ?? '-',
            'offer_no' => $measurement->offer_no ?? optional($offer)->offer_no ?? '-',
            'customer_name' => $customerName,
            'product_name' => $productName,
            'address' => trim($address) ?: 'Adresse nicht hinterlegt',
            'status' => $status,
            'status_label' => $statusColumns[$status]['label'],
            'assignment_description' => $measurement->assignment_description ?: ($measurement->note ?: ''),
            'note' => $measurement->note ?: '',
            'responsible_employee_id' => $responsibleId,
            'responsible_name' => $responsible['name'] ?? 'Nicht zugewiesen',
            'scheduled_date' => $scheduledDate,
            'scheduled_end_date' => $scheduledEndDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'appointment_id' => $measurement->appointment_id ?? null,
            'personal_task_id' => $measurement->personal_task_id ?? null,
            'materials_done' => (int) ($measurement->materials_approved_count ?? 0),
            'materials_total' => (int) ($measurement->materials_total_count ?? $measurement->items_count ?? 0),
            'team' => $team->values()->all(),
            'show_url' => Route::has('deal.measurements.show') ? route('deal.measurements.show', $measurement) : '#',
            'assign_url' => Route::has('deal.measurements.assign-work') ? route('deal.measurements.assign-work', $measurement) : '#',
            'status_url' => Route::has('deal.measurements.kanban.update-status') ? route('deal.measurements.kanban.update-status', $measurement) : '#',
            'note_url' => Route::has('deal-measurements.notes.store') ? route('deal-measurements.notes.store', $measurement) : '#',
        ];
    })->values();

    $totalCount = $measurementRows->count();
    $myCount = $measurementRows->filter(fn($row) => collect($row['team'])->pluck('id')->contains($authEmployeeId))->count();
    $plannedCount = $measurementRows->whereIn('status', ['appointment_created', 'task_created'])->count();
    $openTaskCount = $measurementRows->where('status', 'task_created')->count();
    $completedCount = $measurementRows->where('status', 'completed')->count();
@endphp

@once
    @push('style')
        <style>
            :root {
                --fm-bg: #f3f4f6;
                --fm-card: #fff;
                --fm-text: #111827;
                --fm-muted: #6b7280;
                --fm-border: #e5e7eb;
                --fm-primary: var(--sa-accent);
                --fm-primary-dark: var(--sa-accent-hover);
                --fm-blue: #74b2d4;
                --fm-success: #10b981;
                --fm-warning: #f59e0b;
                --fm-danger: #ef4444;
                --fm-soft: #f8fafc;
                --fm-shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
                --fm-radius: 16px;
                --fm-transition: all .2s ease-in-out;
            }

            .fm-wrap {
                font-family: Inter, system-ui, -apple-system, sans-serif;
                color: var(--fm-text);
            }

            .fm-titlebar {
                display: flex;
                align-items: flex-end;
                justify-content: space-between;
                gap: 14px;
                flex-wrap: wrap;
                margin-bottom: 18px;
            }

            .fm-title {
                font-size: 28px;
                font-weight: 900;
                letter-spacing: -.03em;
                color: #111827;
                margin: 0;
            }

            .fm-sub {
                font-size: 13px;
                color: var(--fm-muted);
                margin-top: 4px;
                line-height: 1.5;
            }

            .fm-breadcrumb {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
                margin-top: 10px;
                font-size: 13px;
                color: var(--fm-muted);
            }

            .fm-breadcrumb a {
                font-weight: 800;
                color: var(--fm-muted);
                text-decoration: none;
            }

            .fm-breadcrumb a:hover {
                color: #111827;
            }

            .fm-breadcrumb .current {
                font-weight: 900;
                color: #111827;
            }

            .fm-actions {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .fm-btn {
                border: 0;
                background: var(--fm-primary);
                color: #fff !important;
                border-radius: 12px;
                padding: 10px 14px;
                font-weight: 900;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                text-decoration: none !important;
                cursor: pointer;
                transition: var(--fm-transition);
                min-height: 42px;
            }

            .fm-btn:hover {
                background: var(--fm-primary-dark);
                color: #fff !important;
            }

            .fm-btn.soft {
                background: #fff;
                color: #111827 !important;
                border: 1px solid var(--fm-border);
            }

            .fm-btn.soft:hover {
                background: #f9fafb;
            }

            .fm-btn.blue {
                background: var(--fm-blue);
            }

            .fm-btn.danger {
                background: var(--fm-danger);
            }

            .fm-stats {
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 14px;
                margin-bottom: 18px;
            }

            @media(max-width:1200px) {
                .fm-stats {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media(max-width:700px) {
                .fm-stats {
                    grid-template-columns: 1fr;
                }
            }

            .fm-stat {
                background: #fff;
                border: 1px solid var(--fm-border);
                border-radius: 18px;
                padding: 16px;
                box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
                display: flex;
                align-items: center;
                gap: 12px;
                min-height: 92px;
            }

            .fm-stat-icon {
                width: 48px;
                height: 48px;
                border-radius: 15px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
            }

            .fm-stat-icon.total {
                background: #eff6ff;
                color: var(--fm-blue);
            }

            .fm-stat-icon.mine {
                background: #f4fae7;
                color: #55720d;
            }

            .fm-stat-icon.planned {
                background: #ecfeff;
                color: #0891b2;
            }

            .fm-stat-icon.task {
                background: #fffbeb;
                color: #b45309;
            }

            .fm-stat-icon.done {
                background: #ecfdf5;
                color: #047857;
            }

            .fm-stat-label {
                font-size: 11px;
                font-weight: 900;
                color: var(--fm-muted);
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .fm-stat-value {
                font-size: 25px;
                font-weight: 950;
                color: #111827;
                line-height: 1.1;
                margin-top: 4px;
            }

            .fm-stat-sub {
                font-size: 12px;
                color: var(--fm-muted);
                margin-top: 4px;
            }

            .fm-toolbar {
                background: #fff;
                border: 1px solid var(--fm-border);
                border-radius: 18px;
                padding: 14px 16px;
                box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
                display: flex;
                align-items: end;
                justify-content: space-between;
                gap: 14px;
                flex-wrap: wrap;
                margin-bottom: 16px;
            }

            .fm-toolbar-left,
            .fm-toolbar-right {
                display: flex;
                align-items: end;
                gap: 12px;
                flex-wrap: wrap;
            }

            .fm-toolbar-left {
                flex: 1;
            }

            .fm-field {
                display: flex;
                flex-direction: column;
                gap: 6px;
                min-width: 170px;
            }

            .fm-field.search {
                flex: 1;
                min-width: 260px;
            }

            .fm-label {
                font-size: 11px;
                font-weight: 900;
                color: var(--fm-muted);
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .fm-input,
            .fm-select,
            .fm-textarea {
                width: 100%;
                border: 1px solid var(--fm-border);
                background: #f9fafb;
                border-radius: 11px;
                padding: 10px 12px;
                outline: none;
                transition: var(--fm-transition);
                font-size: 14px;
                color: #111827;
            }

            .fm-input:focus,
            .fm-select:focus,
            .fm-textarea:focus {
                background: #fff;
                border-color: var(--fm-primary);
                box-shadow: 0 0 0 3px #f4fae7;
            }

            .fm-textarea {
                min-height: 120px;
                resize: vertical;
                line-height: 1.55;
            }

            .fm-view-switch {
                display: inline-flex;
                background: #fff;
                border: 1px solid var(--fm-border);
                border-radius: 12px;
                padding: 4px;
                gap: 4px;
            }

            .fm-view-btn {
                border: 0;
                background: transparent;
                color: var(--fm-muted);
                border-radius: 9px;
                padding: 8px 11px;
                font-weight: 900;
                display: inline-flex;
                align-items: center;
                gap: 7px;
                cursor: pointer;
            }

            .fm-view-btn.active {
                background: #f4fae7;
                color: #55720d;
            }

            .fm-panel {
                display: none;
            }

            .fm-panel.active {
                display: block;
            }

            .fm-card-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
                gap: 14px;
            }

            @media(max-width:700px) {
                .fm-card-grid {
                    grid-template-columns: 1fr;
                }
            }

            .fm-measure-card {
                background: #fff;
                border: 1px solid var(--fm-border);
                border-radius: 18px;
                box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
                overflow: hidden;
                transition: var(--fm-transition);
                min-width: 0;
            }

            .fm-measure-card:hover {
                border-color: var(--fm-primary);
                box-shadow: var(--fm-shadow);
                transform: translateY(-2px);
            }

            .fm-measure-card.is-kanban-card {
                border-radius: 16px;
                box-shadow: 0 1px 2px rgba(15, 23, 42, .05);
            }

            .fm-measure-card.is-kanban-card:hover {
                transform: none;
            }

            .fm-card-top {
                padding: 15px;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 12px;
                border-bottom: 1px solid #f1f5f9;
                min-width: 0;
            }

            .is-kanban-card .fm-card-top {
                padding: 11px 12px;
                gap: 8px;
            }

            .fm-card-title {
                font-size: 15px;
                font-weight: 950;
                color: #111827;
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .is-kanban-card .fm-card-title {
                font-size: 13px;
            }

            .fm-card-sub {
                font-size: 12px;
                color: var(--fm-muted);
                margin-top: 3px;
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .is-kanban-card .fm-card-sub {
                font-size: 11px;
            }

            .fm-status-pill {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 10px;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 950;
                white-space: nowrap;
            }

            .fm-card-body {
                padding: 15px;
                display: flex;
                flex-direction: column;
                gap: 12px;
                min-width: 0;
            }

            .is-kanban-card .fm-card-body {
                padding: 11px 12px;
                gap: 8px;
            }

            .fm-card-details {
                display: flex;
                flex-direction: column;
                gap: 8px;
                min-width: 0;
            }

            .is-kanban-card .fm-card-details {
                display: none;
                margin-top: 2px;
                padding-top: 8px;
                border-top: 1px dashed #e2e8f0;
            }

            .is-kanban-card.is-expanded .fm-card-details {
                display: flex;
            }

            .fm-compact-line {
                display: flex;
                align-items: center;
                gap: 7px;
                min-width: 0;
                color: #374151;
                font-size: 12px;
                line-height: 1.4;
            }

            .fm-compact-line i {
                width: 14px;
                height: 14px;
                color: var(--fm-blue);
                flex: 0 0 auto;
            }

            .fm-compact-text {
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .fm-info-row {
                display: flex;
                align-items: flex-start;
                gap: 9px;
                color: #374151;
                font-size: 13px;
                line-height: 1.45;
                min-width: 0;
            }

            .fm-info-row i {
                margin-top: 1px;
                color: var(--fm-blue);
            }

            .fm-team {
                display: flex;
                align-items: center;
                gap: 8px;
                flex-wrap: wrap;
                min-width: 0;
            }

            .is-kanban-card .fm-team {
                flex-wrap: nowrap;
                overflow: hidden;
            }

            .fm-avatar {
                width: 30px;
                height: 30px;
                border-radius: 999px;
                object-fit: cover;
                border: 2px solid #fff;
                box-shadow: 0 4px 10px rgba(15, 23, 42, .15);
                background: #f1f5f9;
            }

            .fm-team-chip {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                padding: 5px 9px;
                border-radius: 999px;
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                color: #334155;
                font-size: 11px;
                font-weight: 900;
                min-width: 0;
                max-width: 100%;
            }

            .fm-team-chip span:not(.fm-team-role) {
                min-width: 0;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .is-kanban-card .fm-team-chip {
                padding: 4px 7px;
                font-size: 10px;
                max-width: 100%;
            }

            .fm-team-role {
                font-size: 9px;
                text-transform: uppercase;
                color: #64748b;
                font-weight: 950;
            }


            .fm-link-strip {
                display: flex;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
                min-width: 0;
            }

            .fm-link-pill {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                min-height: 25px;
                border-radius: 999px;
                border: 1px solid #e2e8f0;
                background: #ffffff;
                color: #475569;
                font-size: 10px;
                font-weight: 950;
                padding: 3px 8px;
                white-space: nowrap;
            }

            .fm-link-pill.measurement {
                color: #55720d;
                background: #f4fae7;
                border-color: #d9ef9d;
            }

            .fm-link-pill.appointment {
                color: #2563eb;
                background: #eff6ff;
                border-color: #bfdbfe;
            }

            .fm-link-pill.task {
                color: #b45309;
                background: #fffbeb;
                border-color: #fde68a;
            }

            .fm-link-pill.muted {
                color: #94a3b8;
                background: #f8fafc;
            }

            .fm-kanban-action-row {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 6px;
                margin-top: 2px;
            }

            .fm-kanban-action-btn {
                min-height: 36px;
                border-radius: 11px;
                border: 1px solid var(--fm-border);
                background: #ffffff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                color: #334155;
                cursor: pointer;
                transition: var(--fm-transition);
                text-decoration: none !important;
                font-size: 11px;
                font-weight: 950;
                white-space: nowrap;
            }

            .fm-kanban-action-btn i,
            .fm-kanban-action-btn svg {
                width: 15px;
                height: 15px;
                flex: 0 0 auto;
            }

            .fm-kanban-action-btn.note {
                color: #b45309;
                background: #fffbeb;
                border-color: #fde68a;
            }

            .fm-kanban-action-btn.plan {
                color: #2563eb;
                background: #eff6ff;
                border-color: #bfdbfe;
            }

            .fm-kanban-action-btn.open {
                color: #55720d;
                background: #f4fae7;
                border-color: #d9ef9d;
            }

            .fm-kanban-action-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 8px 18px rgba(15, 23, 42, .08);
            }

            .fm-kanban-more-row {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                margin-top: 2px;
            }

            .fm-mini-meta {
                font-size: 10px;
                font-weight: 900;
                color: #64748b;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }

            .fm-card-actions {
                padding: 13px 15px;
                background: #fafafa;
                border-top: 1px solid #f1f5f9;
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 8px;
                flex-wrap: wrap;
            }

            .is-kanban-card .fm-card-actions {
                padding: 9px 10px;
                gap: 6px;
                justify-content: space-between;
            }

            .fm-detail-toggle {
                min-height: 34px;
                padding: 0 10px;
                border-radius: 10px;
                border: 1px solid #e2e8f0;
                background: #fff;
                color: #475569;
                font-size: 11px;
                font-weight: 950;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                cursor: pointer;
            }

            .fm-detail-toggle:hover {
                background: #f8fafc;
                color: #111827;
            }

            .fm-icon-btn {
                width: 38px;
                height: 38px;
                border-radius: 11px;
                border: 1px solid var(--fm-border);
                background: #fff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #64748b;
                cursor: pointer;
                transition: var(--fm-transition);
                text-decoration: none !important;
            }

            .fm-icon-btn:hover {
                background: #f9fafb;
                color: #111827;
            }

            .fm-icon-btn.primary {
                color: var(--fm-primary);
                background: #f4fae7;
                border-color: #d9ef9d;
            }

            .fm-icon-btn.blue {
                color: #2563eb;
                background: #eff6ff;
                border-color: #bfdbfe;
            }

            .fm-icon-btn.warning {
                color: #b45309;
                background: #fffbeb;
                border-color: #fde68a;
            }

            .fm-list {
                background: #fff;
                border: 1px solid var(--fm-border);
                border-radius: 18px;
                box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
                overflow: hidden;
            }

            .fm-list-head,
            .fm-list-row {
                display: grid;
                grid-template-columns: 110px minmax(220px, 1.3fr) minmax(180px, 1fr) 170px 190px 150px 190px;
                gap: 14px;
                align-items: center;
                padding: 14px 16px;
            }

            .fm-list-head {
                font-size: 11px;
                font-weight: 950;
                color: var(--fm-muted);
                text-transform: uppercase;
                letter-spacing: .06em;
                background: #fafafa;
                border-bottom: 1px solid var(--fm-border);
            }

            .fm-list-row {
                border-bottom: 1px solid #f1f5f9;
            }

            .fm-list-row:last-child {
                border-bottom: 0;
            }

            @media(max-width:1300px) {
                .fm-list-head {
                    display: none
                }

                .fm-list-row {
                    grid-template-columns: 1fr;
                }

                .fm-cell:before {
                    content: attr(data-label);
                    display: block;
                    font-size: 10px;
                    font-weight: 950;
                    color: var(--fm-muted);
                    text-transform: uppercase;
                    letter-spacing: .06em;
                    margin-bottom: 4px;
                }
            }

            /* =========================================================
                               KANBAN SCROLL FIX
                               - The board scrolls horizontally.
                               - EACH column has its own vertical scroll.
                               - Column header stays visible.
                               ========================================================= */
            .fm-wrap {
                min-height: 0;
            }

            .fm-panel[data-panel="kanban"].active {
                display: flex;
                flex-direction: column;
                min-height: 0;
            }

            .fm-kanban {
                display: grid;
                grid-auto-flow: column;
                grid-auto-columns: minmax(380px, 440px);
                gap: 14px;
                overflow-x: auto;
                overflow-y: hidden;
                padding: 2px 4px 16px;
                height: calc(100vh - 310px);
                min-height: 560px;
                max-height: calc(100vh - 190px);
                scroll-snap-type: x proximity;
                align-items: stretch;
            }

            .fm-kanban-col {
                width: auto;
                min-width: 0;
                height: 100%;
                max-height: 100%;
                background: #f8fafc;
                border: 1px solid var(--fm-border);
                border-radius: 18px;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                min-height: 0;
                scroll-snap-align: start;
            }

            .fm-kanban-head {
                padding: 12px 13px;
                border-bottom: 1px solid var(--fm-border);
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
                background: #fff;
                border-radius: 18px 18px 0 0;
                position: relative;
                z-index: 2;
                flex: 0 0 auto;
            }

            .fm-kanban-title {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 14px;
                font-weight: 950;
            }

            .fm-kanban-count {
                min-width: 26px;
                height: 26px;
                border-radius: 999px;
                background: #fff;
                border: 1px solid var(--fm-border);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 12px;
                font-weight: 950;
                color: #111827;
            }

            .fm-kanban-body {
                padding: 12px;
                display: flex;
                flex-direction: column;
                gap: 10px;
                overflow-y: auto !important;
                overflow-x: hidden;
                min-height: 0;
                flex: 1 1 auto;
                height: 100%;
                max-height: none;
                overscroll-behavior: contain;
                -webkit-overflow-scrolling: touch;
            }

            .fm-kanban-body::after {
                content: '';
                display: block;
                min-height: 8px;
                flex: 0 0 8px;
            }

            .fm-kanban-body::-webkit-scrollbar,
            .fm-kanban::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            .fm-kanban-body::-webkit-scrollbar-thumb,
            .fm-kanban::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 999px;
            }

            @media(max-width: 900px) {
                .fm-kanban {
                    grid-auto-columns: minmax(88vw, 92vw);
                    height: calc(100vh - 285px);
                    min-height: 520px;
                    max-height: calc(100vh - 150px);
                }

                .fm-titlebar,
                .fm-toolbar {
                    align-items: stretch;
                }

                .fm-actions,
                .fm-toolbar-left,
                .fm-toolbar-right,
                .fm-view-switch {
                    width: 100%;
                }

                .fm-btn,
                .fm-view-btn {
                    flex: 1;
                    justify-content: center;
                }
            }

            @media(max-width: 560px) {
                .fm-kanban {
                    display: flex;
                    flex-direction: column;
                    overflow: visible;
                    height: auto;
                    max-height: none;
                    min-height: 0;
                    padding-bottom: 0;
                    gap: 16px;
                }

                .fm-kanban-col {
                    height: min(72vh, 620px);
                    max-height: 620px;
                    min-height: 360px;
                }

                .fm-kanban-body {
                    overflow-y: auto !important;
                    max-height: none;
                    min-height: 0;
                    flex: 1 1 auto;
                }
            }

            .fm-kanban-body.is-over {
                background: #f4fae7;
            }

            .fm-empty {
                padding: 40px;
                border: 1px dashed var(--fm-border);
                border-radius: 18px;
                background: #fff;
                text-align: center;
                color: var(--fm-muted);
            }

            .fm-modal-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, .55);
                z-index: 1200;
                display: none;
                align-items: center;
                justify-content: center;
                padding: 18px;
            }

            .fm-modal-backdrop.show {
                display: flex;
            }

            .fm-modal {
                width: min(760px, 100%);
                background: #fff;
                border-radius: 22px;
                box-shadow: 0 28px 80px rgba(15, 23, 42, .25);
                overflow: hidden;
            }

            .fm-modal-head {
                padding: 16px 18px;
                border-bottom: 1px solid var(--fm-border);
                background: #fafafa;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }

            .fm-modal-title {
                font-size: 17px;
                font-weight: 950;
                margin: 0;
            }

            .fm-modal-body {
                padding: 18px;
                max-height: 72vh;
                overflow-y: auto;
            }

            .fm-modal-foot {
                padding: 14px 18px;
                border-top: 1px solid var(--fm-border);
                background: #fafafa;
                display: flex;
                align-items: center;
                justify-content: flex-end;
                gap: 10px;
                flex-wrap: wrap;
            }

            .fm-form-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px;
            }

            @media(max-width:760px) {
                .fm-form-grid {
                    grid-template-columns: 1fr;
                }
            }

            .fm-toast-wrap {
                position: fixed;
                right: 20px;
                bottom: 20px;
                z-index: 9999;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .fm-toast {
                min-width: 280px;
                max-width: 380px;
                background: #fff;
                border: 1px solid var(--fm-border);
                border-left: 5px solid var(--fm-primary);
                border-radius: 16px;
                box-shadow: var(--fm-shadow);
                padding: 13px 14px;
                display: flex;
                gap: 10px;
                animation: fmToastIn .22s ease-out;
            }

            .fm-toast.error {
                border-left-color: var(--fm-danger);
            }

            .fm-toast.success {
                border-left-color: var(--fm-success);
            }

            .fm-toast-title {
                font-weight: 950;
                font-size: 13px;
                color: #111827;
            }

            .fm-toast-message {
                font-size: 12px;
                color: #64748b;
                margin-top: 3px;
                white-space: pre-wrap;
                line-height: 1.45;
            }

            @keyframes fmToastIn {
                from {
                    opacity: 0;
                    transform: translateX(18px)
                }

                to {
                    opacity: 1;
                    transform: translateX(0)
                }
            }
        </style>
    @endpush
@endonce

@section('content')
    <div class="fm-wrap" data-auth-employee-id="{{ $authEmployeeId }}">
        <div class="fm-titlebar">
            <div>
                <h1 class="fm-title">Feinaufmaß Aufgaben</h1>
                <div class="fm-sub">Alle Feinaufmaße mit Team, Termin, Aufgabe, Notizen und Kanban-Status.</div>
                <div class="fm-breadcrumb">
                    <a href="{{ url('/employee_dashboard') }}">Dashboard</a><span>›</span>
                    <a href="{{ url('deal_details') }}">Aufträge</a><span>›</span>
                    <span class="current">Feinaufmaß Kanban</span>
                </div>
            </div>
            <div class="fm-actions">
                <a href="{{ url('deal_details') }}" class="fm-btn soft"><i data-lucide="arrow-left"></i> Aufträge</a>
                <a href="{{ Route::has('deal.measurements.index') ? route('deal.measurements.index') : '#' }}"
                    class="fm-btn blue"><i data-lucide="ruler"></i> Feinaufmaß Liste</a>
                <button type="button" class="fm-btn" data-view="kanban"><i data-lucide="columns-3"></i> Kanban
                    öffnen</button>
            </div>
        </div>

        <div class="fm-stats">
            <div class="fm-stat">
                <div class="fm-stat-icon total"><i data-lucide="clipboard-list"></i></div>
                <div>
                    <div class="fm-stat-label">Gesamt</div>
                    <div class="fm-stat-value">{{ $totalCount }}</div>
                    <div class="fm-stat-sub">Alle Feinaufmaße</div>
                </div>
            </div>
            <div class="fm-stat">
                <div class="fm-stat-icon mine"><i data-lucide="user-check"></i></div>
                <div>
                    <div class="fm-stat-label">Meine Aufgaben</div>
                    <div class="fm-stat-value">{{ $myCount }}</div>
                    <div class="fm-stat-sub">Ich bin im Team</div>
                </div>
            </div>
            <div class="fm-stat">
                <div class="fm-stat-icon planned"><i data-lucide="calendar-check"></i></div>
                <div>
                    <div class="fm-stat-label">Geplant</div>
                    <div class="fm-stat-value">{{ $plannedCount }}</div>
                    <div class="fm-stat-sub">Termin oder Aufgabe</div>
                </div>
            </div>
            <div class="fm-stat">
                <div class="fm-stat-icon task"><i data-lucide="list-checks"></i></div>
                <div>
                    <div class="fm-stat-label">Aufgaben</div>
                    <div class="fm-stat-value">{{ $openTaskCount }}</div>
                    <div class="fm-stat-sub">Persönliche Tasks</div>
                </div>
            </div>
            <div class="fm-stat">
                <div class="fm-stat-icon done"><i data-lucide="check-circle"></i></div>
                <div>
                    <div class="fm-stat-label">Fertig</div>
                    <div class="fm-stat-value">{{ $completedCount }}</div>
                    <div class="fm-stat-sub">Abgeschlossen</div>
                </div>
            </div>
        </div>

        <div class="fm-toolbar">
            <div class="fm-toolbar-left">
                <div class="fm-field search"><label class="fm-label">Suche</label><input type="text" class="fm-input"
                        id="fmSearch" placeholder="Kunde, Ort, Aufmaß-Nr., Auftrag, Produkt suchen..."></div>
                <div class="fm-field"><label class="fm-label">Status</label><select class="fm-select" id="fmStatusFilter">
                        <option value="">Alle Status</option>@foreach($statusColumns as $key => $col)<option
                        value="{{ $key }}">{{ $col['label'] }}</option>@endforeach
                    </select></div>
                <div class="fm-field"><label class="fm-label">Mitarbeiter</label><select class="fm-select"
                        id="fmEmployeeFilter">
                        <option value="">Alle Mitarbeiter</option>
                        <option value="__mine">Meine Aufgaben</option>@foreach($employeeLookup as $employee)<option
                        value="{{ $employee['id'] }}">{{ $employee['name'] }}</option>@endforeach
                    </select></div>
            </div>
            <div class="fm-toolbar-right">
                <div class="fm-view-switch">
                    <button type="button" class="fm-view-btn active" data-view="kanban"><i data-lucide="columns-3"></i>
                        Kanban</button>
                    <button type="button" class="fm-view-btn" data-view="cards"><i data-lucide="layout-grid"></i>
                        Karten</button>
                    <button type="button" class="fm-view-btn" data-view="list"><i data-lucide="list"></i> Liste</button>
                </div>
            </div>
        </div>

        <div class="fm-panel active" data-panel="kanban">
            <div class="fm-kanban" id="fmKanban">
                @foreach($statusColumns as $statusKey => $column)
                    <section class="fm-kanban-col" data-column="{{ $statusKey }}">
                        <div class="fm-kanban-head">
                            <div class="fm-kanban-title" style="color:{{ $column['color'] }}"><i
                                    data-lucide="{{ $column['icon'] }}"></i> {{ $column['label'] }}</div>
                            <span class="fm-kanban-count" data-count="{{ $statusKey }}">0</span>
                        </div>
                        <div class="fm-kanban-body" data-dropzone="{{ $statusKey }}"></div>
                    </section>
                @endforeach
            </div>
        </div>

        <div class="fm-panel" data-panel="cards">
            <div class="fm-card-grid" id="fmCards"></div>
        </div>

        <div class="fm-panel" data-panel="list">
            <div class="fm-list">
                <div class="fm-list-head">
                    <div>Aufmaß</div>
                    <div>Kunde / Objekt</div>
                    <div>Produkt</div>
                    <div>Team</div>
                    <div>Termin</div>
                    <div>Status</div>
                    <div>Aktionen</div>
                </div>
                <div id="fmListRows"></div>
            </div>
        </div>

        <div class="fm-empty" id="fmEmpty" style="display:none;">Keine Feinaufmaße für diesen Filter gefunden.</div>
    </div>

    <div class="fm-modal-backdrop" id="fmAssignModal">
        <div class="fm-modal">
            <div class="fm-modal-head">
                <h3 class="fm-modal-title">Feinaufmaß planen / bearbeiten</h3><button type="button" class="fm-icon-btn"
                    data-close-modal><i data-lucide="x"></i></button>
            </div>
            <form id="fmAssignForm">
                @csrf
                <input type="hidden" name="measurement_id" id="fmAssignMeasurementId">
                <input type="hidden" name="deal_measurement_id" id="fmAssignDealMeasurementId">
                <input type="hidden" name="deal_id" id="fmAssignDealId">
                <input type="hidden" name="customer_id" id="fmAssignCustomerId">
                <input type="hidden" name="alternative_id" id="fmAssignAlternativeId">
                <input type="hidden" name="product_id" id="fmAssignProductId">
                <div class="fm-modal-body">
                    <div class="fm-form-grid">
                        <div><label class="fm-label">Mitarbeiter</label><select class="fm-select" name="employee_id"
                                id="fmAssignEmployee" required>
                                <option value="">Bitte wählen</option>@foreach($employeeLookup as $employee)<option
                                value="{{ $employee['id'] }}">{{ $employee['name'] }}</option>@endforeach
                            </select></div>
                        <div><label class="fm-label">Priorität</label><select class="fm-select" name="task_priority">
                                <option value="normal">Normal</option>
                                <option value="high">Hoch</option>
                                <option value="urgent">Dringend</option>
                            </select></div>
                        <div><label class="fm-label">Startdatum</label><input class="fm-input" type="date" name="start_date"
                                id="fmAssignStartDate" required></div>
                        <div><label class="fm-label">Enddatum</label><input class="fm-input" type="date" name="end_date"
                                id="fmAssignEndDate"></div>
                        <div><label class="fm-label">Startzeit</label><input class="fm-input" type="time" name="start_time"
                                id="fmAssignStartTime" required></div>
                        <div><label class="fm-label">Endzeit</label><input class="fm-input" type="time" name="end_time"
                                id="fmAssignEndTime" required></div>
                    </div>
                    <div style="margin-top:14px;"><label class="fm-label">Beschreibung / Arbeitsanweisung</label><textarea
                            class="fm-textarea" name="description" id="fmAssignDescription"
                            placeholder="Was soll beim Feinaufmaß erledigt werden?"></textarea></div>
                    <label
                        style="display:flex;align-items:center;gap:10px;margin-top:12px;font-weight:900;color:#374151;"><input
                            type="checkbox" name="create_task" value="1" id="fmAssignCreateTask"> Zusätzlich als persönliche
                        Aufgabe erstellen</label>
                </div>
                <div class="fm-modal-foot"><button type="button" class="fm-btn soft"
                        data-close-modal>Abbrechen</button><button type="submit" class="fm-btn"><i data-lucide="save"></i>
                        Speichern</button></div>
            </form>
        </div>
    </div>

    <div class="fm-modal-backdrop" id="fmNoteModal">
        <div class="fm-modal">
            <div class="fm-modal-head">
                <h3 class="fm-modal-title">Notiz schreiben</h3><button type="button" class="fm-icon-btn" data-close-modal><i
                        data-lucide="x"></i></button>
            </div>
            <form id="fmNoteForm">
                @csrf
                <input type="hidden" name="measurement_id" id="fmNoteMeasurementId">
                <div class="fm-modal-body"><label class="fm-label">Interne Notiz</label><textarea class="fm-textarea"
                        name="note" id="fmNoteText" required placeholder="Neue Notiz zum Feinaufmaß..."></textarea></div>
                <div class="fm-modal-foot"><button type="button" class="fm-btn soft"
                        data-close-modal>Abbrechen</button><button type="submit" class="fm-btn"><i
                            data-lucide="message-square-plus"></i> Notiz speichern</button></div>
            </form>
        </div>
    </div>

    <div class="fm-toast-wrap" id="fmToastWrap"></div>
@endsection

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script>
            window.FeinaufmassRows = @json($measurementRows);
            window.FeinaufmassColumns = @json($statusColumns);
            window.FeinaufmassEmployeeLookup = @json($employeeLookup);
            (function () {
                'use strict';
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                const authEmployeeId = document.querySelector('.fm-wrap')?.dataset.authEmployeeId || '';
                let rows = Array.isArray(window.FeinaufmassRows) ? window.FeinaufmassRows : [];
                let currentView = 'kanban';

                const $ = (s, c = document) => c.querySelector(s);
                const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));
                const esc = v => String(v ?? '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m]));

                function toast(type, title, message) {
                    const wrap = $('#fmToastWrap'); if (!wrap) return;
                    const el = document.createElement('div'); el.className = `fm-toast ${type || ''}`;
                    el.innerHTML = `<div style="width:34px;height:34px;border-radius:12px;display:flex;align-items:center;justify-content:center;background:${type === 'error' ? '#fef2f2' : '#ecfdf5'};color:${type === 'error' ? '#b91c1c' : '#047857'};font-weight:900;">${type === 'error' ? '!' : '✓'}</div><div style="flex:1;"><div class="fm-toast-title">${esc(title)}</div><div class="fm-toast-message">${esc(message || '')}</div></div><button type="button" style="border:0;background:transparent;color:#94a3b8;font-size:18px;" onclick="this.closest('.fm-toast').remove()">×</button>`;
                    wrap.appendChild(el); setTimeout(() => { try { el.remove(); } catch (e) { } }, 4500);
                }

                async function fetchJson(url, options = {}) {
                    if (!url || url === '#') throw new Error('Route fehlt. Bitte Route im web.php hinzufügen.');
                    const response = await fetch(url, { ...options, headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf, ...(options.headers || {}) } });
                    const text = await response.text();
                    let data = {};
                    try { data = text ? JSON.parse(text) : {}; } catch (e) { throw new Error(text || 'Ungültige Serverantwort.'); }
                    if (!response.ok || data.success === false) {
                        let msg = data.message || 'Serverfehler.';
                        if (data.errors) { const details = Object.values(data.errors).flat().join('\n'); if (details) msg += '\n' + details; }
                        throw new Error(msg);
                    }
                    return data;
                }

                function teamHtml(row) {
                    const team = Array.isArray(row.team) ? row.team : [];
                    if (!team.length) return '<span class="fm-team-chip">Nicht zugewiesen</span>';
                    return team.map(member => `<span class="fm-team-chip" title="${esc(member.name)}"><img class="fm-avatar" src="${esc(member.image)}" alt=""><span>${esc(member.name)}</span><span class="fm-team-role">${esc(member.role || 'Team')}</span></span>`).join('');
                }

                function statusHtml(status) {
                    const col = window.FeinaufmassColumns?.[status] || window.FeinaufmassColumns?.not_assigned || { label: status, color: '#94a3b8' };
                    return `<span class="fm-status-pill" style="background:${esc(col.color)}1A;color:${esc(col.color)};border:1px solid ${esc(col.color)}33;">${esc(col.label)}</span>`;
                }

                function linkHtml(row) {
                    const measurementId = row.deal_measurement_id || row.id || '';
                    const appointment = row.appointment_id
                        ? `<span class="fm-link-pill appointment" title="Kalendertermin verknüpft"><i data-lucide="calendar-check"></i> Termin #${esc(row.appointment_id)}</span>`
                        : `<span class="fm-link-pill muted" title="Kein Kalendertermin"><i data-lucide="calendar-x"></i> Kein Termin</span>`;
                    const task = row.personal_task_id
                        ? `<span class="fm-link-pill task" title="Persönliche Aufgabe verknüpft"><i data-lucide="list-checks"></i> Aufgabe #${esc(row.personal_task_id)}</span>`
                        : `<span class="fm-link-pill muted" title="Keine Aufgabe"><i data-lucide="list-x"></i> Keine Aufgabe</span>`;
                    return `<div class="fm-link-strip"><span class="fm-link-pill measurement" title="Feinaufmaß ID"><i data-lucide="ruler"></i> FM #${esc(measurementId)}</span>${appointment}${task}</div>`;
                }

                function cardHtml(row, compact = false) {
                    const noteText = row.note || row.assignment_description || '';
                    const cardClass = compact ? 'fm-measure-card is-kanban-card' : 'fm-measure-card';
                    const terminText = `${row.scheduled_date ? esc(row.scheduled_date) : 'Kein Termin'}${row.start_time ? ' · ' + esc(row.start_time) : ''}${row.end_time ? ' - ' + esc(row.end_time) : ''}`;
                    return `<article class="${cardClass}" data-card-id="${row.id}" data-status="${esc(row.status)}" data-search="${esc([row.measurement_no, row.order_number, row.offer_no, row.customer_name, row.product_name, row.address, row.responsible_name].join(' ').toLowerCase())}" data-team="${esc((row.team || []).map(t => t.id).join(','))}">
                                    <div class="fm-card-top">
                                        <div style="min-width:0;flex:1;">
                                            <div class="fm-card-title">${esc(row.measurement_no)}</div>
                                            <div class="fm-card-sub">Auftrag ${esc(row.order_number || '-')} · Angebot ${esc(row.offer_no || '-')}</div>
                                        </div>
                                        <div style="flex:0 0 auto;">${statusHtml(row.status)}</div>
                                    </div>
                                    <div class="fm-card-body">
                                        <div class="fm-compact-line"><i data-lucide="user"></i><div class="fm-compact-text"><strong>${esc(row.customer_name)}</strong></div></div>
                                        <div class="fm-compact-line"><i data-lucide="calendar"></i><div class="fm-compact-text">${terminText}</div></div>
                                        <div class="fm-team">${teamHtml(row)}</div>
                                        ${linkHtml(row)}

                                        ${compact ? `
                                            <div class="fm-kanban-action-row">
                                                <button type="button" class="fm-kanban-action-btn note" data-note-id="${row.id}" title="Notiz schreiben"><i data-lucide="message-square-plus"></i> Notiz</button>
                                                <button type="button" class="fm-kanban-action-btn plan" data-assign-id="${row.id}" title="Termin planen"><i data-lucide="calendar-plus"></i> Planen</button>
                                                <a href="${esc(row.show_url)}" class="fm-kanban-action-btn open" title="Feinaufmaß öffnen"><i data-lucide="external-link"></i> Öffnen</a>
                                            </div>
                                            <div class="fm-kanban-more-row">
                                                <button type="button" class="fm-detail-toggle" data-toggle-card-details><i data-lucide="chevron-down"></i> Details anzeigen</button>
                                                <span class="fm-mini-meta">${esc(row.product_name)}</span>
                                            </div>
                                        ` : ''}

                                        <div class="fm-card-details">
                                            <div class="fm-info-row"><i data-lucide="map-pin"></i><div>${esc(row.address)}</div></div>
                                            <div class="fm-info-row"><i data-lucide="package"></i><div>${esc(row.product_name)}</div></div>
                                            ${noteText ? `<div style="background:#f8fafc;border:1px dashed #e2e8f0;border-radius:14px;padding:10px;font-size:12px;color:#475569;line-height:1.55;max-height:${compact ? '92px' : 'none'};overflow:auto;">${esc(noteText).slice(0, compact ? 260 : 520)}</div>` : ''}
                                        </div>
                                    </div>
                                    ${compact ? '' : `<div class="fm-card-actions"><span></span><span style="display:flex;gap:6px;align-items:center;"><button type="button" class="fm-icon-btn warning" data-note-id="${row.id}" title="Notiz"><i data-lucide="message-square-plus"></i></button><button type="button" class="fm-icon-btn blue" data-assign-id="${row.id}" title="Planen"><i data-lucide="calendar-plus"></i></button><a href="${esc(row.show_url)}" class="fm-icon-btn primary" title="Öffnen"><i data-lucide="external-link"></i></a></span></div>`}
                                </article>`;
                }

                function listHtml(row) {
                    return `<div class="fm-list-row" data-card-id="${row.id}" data-status="${esc(row.status)}" data-search="${esc([row.measurement_no, row.order_number, row.offer_no, row.customer_name, row.product_name, row.address, row.responsible_name].join(' ').toLowerCase())}" data-team="${esc((row.team || []).map(t => t.id).join(','))}">
                                    <div class="fm-cell" data-label="Aufmaß"><strong>${esc(row.measurement_no)}</strong><div style="font-size:12px;color:#64748b;">#${row.id}</div></div>
                                    <div class="fm-cell" data-label="Kunde / Objekt"><strong>${esc(row.customer_name)}</strong><div style="font-size:12px;color:#64748b;">${esc(row.address)}</div></div>
                                    <div class="fm-cell" data-label="Produkt">${esc(row.product_name)}</div>
                                    <div class="fm-cell" data-label="Team"><div class="fm-team">${teamHtml(row)}</div></div>
                                    <div class="fm-cell" data-label="Termin">${row.scheduled_date ? esc(row.scheduled_date) : 'Kein Termin'} ${row.start_time ? '<br><span style="font-size:12px;color:#64748b;">' + esc(row.start_time) + ' - ' + esc(row.end_time || '') + '</span>' : ''}<div style="margin-top:6px;">${linkHtml(row)}</div></div>
                                    <div class="fm-cell" data-label="Status">${statusHtml(row.status)}</div>
                                    <div class="fm-cell" data-label="Aktionen"><div class="fm-card-actions" style="padding:0;background:transparent;border:0;justify-content:flex-start;"><button type="button" class="fm-icon-btn warning" data-note-id="${row.id}"><i data-lucide="message-square-plus"></i></button><button type="button" class="fm-icon-btn blue" data-assign-id="${row.id}"><i data-lucide="calendar-plus"></i></button><a href="${esc(row.show_url)}" class="fm-icon-btn primary"><i data-lucide="external-link"></i></a></div></div>
                                </div>`;
                }

                function filteredRows() {
                    const q = ($('#fmSearch')?.value || '').toLowerCase().trim();
                    const status = $('#fmStatusFilter')?.value || '';
                    const employee = $('#fmEmployeeFilter')?.value || '';
                    return rows.filter(row => {
                        const searchText = [row.measurement_no, row.order_number, row.offer_no, row.customer_name, row.product_name, row.address, row.responsible_name].join(' ').toLowerCase();
                        if (q && !searchText.includes(q)) return false;
                        if (status && row.status !== status) return false;
                        if (employee) {
                            const ids = (row.team || []).map(t => String(t.id));
                            const wanted = employee === '__mine' ? authEmployeeId : employee;
                            if (!ids.includes(String(wanted))) return false;
                        }
                        return true;
                    });
                }

                function render() {
                    const list = filteredRows();
                    const empty = $('#fmEmpty'); if (empty) empty.style.display = list.length ? 'none' : 'block';
                    const cards = $('#fmCards'); if (cards) cards.innerHTML = list.map(r => cardHtml(r)).join('');
                    const listRows = $('#fmListRows'); if (listRows) listRows.innerHTML = list.map(r => listHtml(r)).join('');
                    $$('[data-dropzone]').forEach(zone => { zone.innerHTML = ''; zone.classList.remove('is-over'); });
                    Object.keys(window.FeinaufmassColumns || {}).forEach(key => { const c = $(`[data-count="${key}"]`); if (c) c.textContent = '0'; });
                    list.forEach(row => { const zone = $(`[data-dropzone="${row.status}"]`); if (zone) zone.insertAdjacentHTML('beforeend', cardHtml(row, true)); });
                    Object.keys(window.FeinaufmassColumns || {}).forEach(key => { const c = $(`[data-count="${key}"]`); const zone = $(`[data-dropzone="${key}"]`); if (c && zone) c.textContent = zone.querySelectorAll('[data-card-id]').length; });
                    if (window.lucide) window.lucide.createIcons();
                    initSortable();
                }

                let sortables = [];
                function initSortable() {
                    sortables.forEach(s => { try { s.destroy(); } catch (e) { } }); sortables = [];
                    $$('[data-dropzone]').forEach(zone => {
                        sortables.push(new Sortable(zone, {
                            group: 'feinaufmass-kanban', animation: 160, ghostClass: 'opacity-50',
                            onStart: () => zone.classList.add('is-over'),
                            onEnd: async function (evt) {
                                $$('[data-dropzone]').forEach(z => z.classList.remove('is-over'));
                                const id = evt.item?.dataset.cardId;
                                const newStatus = evt.to?.dataset.dropzone;
                                const row = rows.find(r => String(r.id) === String(id));
                                if (!id || !newStatus || !row || row.status === newStatus) { render(); return; }
                                const oldStatus = row.status;
                                row.status = newStatus;
                                try {
                                    await fetchJson(row.status_url, { method: 'POST', body: new URLSearchParams({ assignment_status: newStatus }) });
                                    toast('success', 'Status geändert', `${row.measurement_no} wurde verschoben.`);
                                } catch (err) {
                                    row.status = oldStatus; toast('error', 'Fehler', err.message);
                                }
                                render();
                            }
                        }));
                    });
                }

                function openAssign(id) {
                    const row = rows.find(r => String(r.id) === String(id)); if (!row) return;
                    $('#fmAssignMeasurementId').value = row.id;
                    $('#fmAssignDealMeasurementId').value = row.deal_measurement_id || row.id;
                    $('#fmAssignDealId').value = row.deal_id || '';
                    $('#fmAssignCustomerId').value = row.customer_id || '';
                    $('#fmAssignAlternativeId').value = row.alternative_id || '';
                    $('#fmAssignProductId').value = row.product_id || '';
                    $('#fmAssignEmployee').value = row.responsible_employee_id || '';
                    $('#fmAssignStartDate').value = row.scheduled_date ? row.scheduled_date.split('.').reverse().join('-') : '';
                    $('#fmAssignEndDate').value = row.scheduled_end_date ? row.scheduled_end_date.split('.').reverse().join('-') : ($('#fmAssignStartDate').value || '');
                    $('#fmAssignStartTime').value = row.start_time || '';
                    $('#fmAssignEndTime').value = row.end_time || '';
                    $('#fmAssignDescription').value = row.assignment_description || '';
                    $('#fmAssignCreateTask').checked = !!row.personal_task_id || row.status === 'task_created';
                    $('#fmAssignModal').classList.add('show');
                    if (window.lucide) window.lucide.createIcons();
                }

                function openNote(id) {
                    const row = rows.find(r => String(r.id) === String(id)); if (!row) return;
                    $('#fmNoteMeasurementId').value = row.id;
                    $('#fmNoteText').value = '';
                    $('#fmNoteModal').classList.add('show');
                }

                document.addEventListener('click', e => {
                    const detailsBtn = e.target.closest('[data-toggle-card-details]');
                    if (detailsBtn) {
                        const card = detailsBtn.closest('.fm-measure-card');
                        if (card) {
                            card.classList.toggle('is-expanded');
                            const open = card.classList.contains('is-expanded');
                            detailsBtn.innerHTML = open ? '<i data-lucide="chevron-up"></i> Weniger' : '<i data-lucide="chevron-down"></i> Details';
                            if (window.lucide) window.lucide.createIcons();
                        }
                        return;
                    }
                    const viewBtn = e.target.closest('[data-view]');
                    if (viewBtn) {
                        currentView = viewBtn.dataset.view;
                        $$('.fm-view-btn').forEach(b => b.classList.toggle('active', b.dataset.view === currentView));
                        $$('.fm-panel').forEach(p => p.classList.toggle('active', p.dataset.panel === currentView));
                        return;
                    }
                    const assign = e.target.closest('[data-assign-id]'); if (assign) { openAssign(assign.dataset.assignId); return; }
                    const note = e.target.closest('[data-note-id]'); if (note) { openNote(note.dataset.noteId); return; }
                    if (e.target.closest('[data-close-modal]') || e.target.classList.contains('fm-modal-backdrop')) { $$('.fm-modal-backdrop').forEach(m => m.classList.remove('show')); }
                });

                ['input', 'change'].forEach(ev => {
                    $('#fmSearch')?.addEventListener(ev, render);
                    $('#fmStatusFilter')?.addEventListener(ev, render);
                    $('#fmEmployeeFilter')?.addEventListener(ev, render);
                });

                $('#fmAssignForm')?.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const id = $('#fmAssignMeasurementId').value;
                    const row = rows.find(r => String(r.id) === String(id)); if (!row) return;
                    const btn = this.querySelector('button[type="submit"]'); if (btn) btn.disabled = true;
                    try {
                        const fd = new FormData(this); if (!fd.has('create_task')) fd.append('create_task', '0');
                        fd.set('measurement_id', row.id);
                        fd.set('deal_measurement_id', row.deal_measurement_id || row.id);
                        fd.set('deal_id', row.deal_id || '');
                        fd.set('customer_id', row.customer_id || '');
                        fd.set('alternative_id', row.alternative_id || '');
                        fd.set('product_id', row.product_id || '');
                        const data = await fetchJson(row.assign_url, { method: 'POST', body: fd });
                        row.responsible_employee_id = fd.get('employee_id');
                        row.scheduled_date = fd.get('start_date') ? fd.get('start_date').split('-').reverse().join('.') : null;
                        row.scheduled_end_date = fd.get('end_date') ? fd.get('end_date').split('-').reverse().join('.') : row.scheduled_date;
                        row.start_time = fd.get('start_time'); row.end_time = fd.get('end_time'); row.assignment_description = fd.get('description') || '';
                        row.status = fd.get('create_task') === '1' ? 'task_created' : 'appointment_created';
                        if (data.appointment_id) row.appointment_id = data.appointment_id;
                        if (data.personal_task_id) row.personal_task_id = data.personal_task_id;
                        const employee = window.FeinaufmassEmployeeLookup?.[String(row.responsible_employee_id)] || null;
                        if (employee) {
                            row.responsible_name = employee.name;
                            const role = fd.get('create_task') === '1' ? 'Aufgabe' : 'Termin';
                            const teamWithoutEmployee = Array.isArray(row.team) ? row.team.filter(t => String(t.id) !== String(employee.id)) : [];
                            row.team = [{ ...employee, role }, ...teamWithoutEmployee];
                        }
                        $('#fmAssignModal').classList.remove('show'); toast('success', 'Gespeichert', data.message || 'Feinaufmaß wurde geplant.'); render();
                    } catch (err) { toast('error', 'Fehler', err.message); }
                    finally { if (btn) btn.disabled = false; }
                });

                $('#fmNoteForm')?.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const id = $('#fmNoteMeasurementId').value;
                    const row = rows.find(r => String(r.id) === String(id)); if (!row) return;
                    const noteText = $('#fmNoteText').value.trim(); if (!noteText) { toast('error', 'Fehler', 'Bitte eine Notiz schreiben.'); return; }
                    const btn = this.querySelector('button[type="submit"]'); if (btn) btn.disabled = true;
                    try {
                        const fd = new FormData(); fd.append('note', noteText);
                        const data = await fetchJson(row.note_url, { method: 'POST', body: fd });
                        row.note = noteText;
                        $('#fmNoteModal').classList.remove('show'); toast('success', 'Notiz gespeichert', data.message || 'Notiz wurde gespeichert.'); render();
                    } catch (err) { toast('error', 'Fehler', err.message); }
                    finally { if (btn) btn.disabled = false; }
                });

                document.addEventListener('keydown', e => { if (e.key === 'Escape') $$('.fm-modal-backdrop').forEach(m => m.classList.remove('show')); });
                render();
            })();
        </script>
    @endpush
@endonce

@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            { label: 'Dashboard', url: "{{ url('/employee_dashboard') }}" },
            { label: 'Aufträge', url: "{{ url('deal_details') }}" },
            { label: 'Feinaufmaß Kanban', url: "{{ url()->current() }}", clickable: false }
        ];
        if (window.setGlobalBreadcrumbs) { window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs); }
    </script>
@endpush