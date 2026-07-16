{{-- OFFER_FOLDER_PRESENCE_EMPLOYEE_FIX_V1: users.name is employee id, presence names/avatars resolved from employees
--}}
@extends('admin.layouts.app')

@section('title', 'Ordner-Workspace')

@php
    $offer = $folder->offer;
    $detail = $folder->detail ?? $offer?->detail;

    $customerName = trim(
        ($offer?->customer?->firma ?? '') . ' ' .
        ($offer?->customer?->name ?? '') . ' ' .
        ($offer?->customer?->lastname ?? '')
    );

    $employeeName = trim(
        ($folder?->creator?->name ?? '') . ' ' .
        ($folder?->creator?->lastname ?? '')
    );

    $initialSections = is_array($detail?->sections) ? $detail->sections : [];
    $initialPlacedImages = is_array($detail?->placed_images) ? $detail->placed_images : [];

    $wizardParams = array_filter([
        'offer_id' => $offer?->id ?? $folder->offer_id,
        'offer_folder_id' => $folder->id,
        'customer_id' => $offer?->customer_id ?? $offer?->customer?->id ?? null,
        'alternative_id' => $offer?->alternative_id ?? $offer?->alternative?->id ?? null,
        'product_id' => $offer?->product_id ?? $offer?->product?->id ?? null,
    ], fn($value) => !is_null($value) && $value !== '');

    $wizardUrl = url('offers/wizard') . '?' . http_build_query($wizardParams);
    $initialAttachments = $folder->attachments ?? collect();

    $resolvedAgbTitle =
        $folderAgb['title']
        ?? $detail?->agb_title
        ?? $defaultAgb['title']
        ?? 'Allgemeine Geschäftsbedingungen';

    $resolvedAgbText =
        $folderAgb['text']
        ?? $detail?->agb_text
        ?? $defaultAgb['text']
        ?? '';

    /**
     * Offer-folder workflow columns must come from the normal Kanban main stages.
     * Source of truth: lead_stages -> lead_stage_sub_stages
     * offer/Angebot uses only the sub-stages of the offer main stage.
     * deal/Auftrag uses only the sub-stages of the deal main stage.
     */
    $resolveFolderWorkflowStage = function (string $documentStatus) {
        $documentStatus = strtolower(trim($documentStatus));

        $candidateKeys = $documentStatus === 'deal'
            ? ['deal', 'auftrag']
            : ['offer', 'angebot'];

        $stage = \App\Models\LeadStage::query()
            ->whereNull('deleted_at')
            ->where(function ($query) use ($candidateKeys) {
                $query->whereIn(\Illuminate\Support\Facades\DB::raw('LOWER(`key`)'), $candidateKeys)
                    ->orWhereIn(\Illuminate\Support\Facades\DB::raw('LOWER(`name`)'), $candidateKeys);
            })
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        if ($stage) {
            return $stage;
        }

        return \App\Models\LeadStage::query()
            ->whereNull('deleted_at')
            ->where(function ($query) use ($candidateKeys) {
                foreach ($candidateKeys as $candidateKey) {
                    $query->orWhereRaw('LOWER(`key`) LIKE ?', ['%' . $candidateKey . '%'])
                        ->orWhereRaw('LOWER(`name`) LIKE ?', ['%' . $candidateKey . '%']);
                }
            })
            ->orderByDesc('is_active')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();
    };

    $offerWorkflowMainStage = $resolveFolderWorkflowStage('offer');
    $dealWorkflowMainStage = $resolveFolderWorkflowStage('deal');

    $availableWorkflowMainStages = \App\Models\LeadStage::query()
        ->whereNull('deleted_at')
        ->orderBy('sort_order')
        ->orderBy('id')
        ->get(['id', 'key', 'name', 'color', 'icon', 'sort_order', 'is_active'])
        ->map(fn($stage) => [
            'id' => $stage->id,
            'key' => $stage->key,
            'name' => $stage->name,
            'label' => $stage->name,
            'color' => $stage->color,
            'icon' => $stage->icon,
            'sort_order' => $stage->sort_order,
            'is_active' => (bool) $stage->is_active,
        ])
        ->values();

    $mapWorkflowSubStagesForBlade = function ($stage, string $documentStatus) {
        if (!$stage) {
            return collect();
        }

        return \App\Models\LeadStageSubStage::query()
            ->where('lead_stage_id', $stage->id)
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn($subStage, $index) => [
                'id' => $subStage->id,
                'lead_stage_id' => $subStage->lead_stage_id,
                'lead_stage_sub_stage_id' => $subStage->id,
                'document_status' => $documentStatus,
                'key' => $subStage->key,
                'label' => $subStage->name,
                'name' => $subStage->name,
                'icon' => $subStage->icon,
                'color' => $subStage->color ?: '#93c21c',
                'position' => $subStage->sort_order ?: ($index + 1),
                'sort_order' => $subStage->sort_order ?: ($index + 1),
                'is_active' => (bool) $subStage->is_active,
                'is_default' => (bool) $subStage->is_default,
                'is_system' => false,
                'source' => 'lead_stage_sub_stages',
            ])
            ->values();
    };

    $initialOfferWorkflowSubStages = $mapWorkflowSubStagesForBlade($offerWorkflowMainStage, 'offer');
    $initialDealWorkflowSubStages = $mapWorkflowSubStagesForBlade($dealWorkflowMainStage, 'deal');

    /**
     * Presence user fallback.
     * Important in this project: users.name stores the employee id.
     * Echo presence callbacks sometimes return only the Laravel user id, so the Blade keeps
     * the current employee name/avatar available as a safe frontend fallback.
     */
    $authUser = auth()->user();
    $currentPresenceEmployeeId = null;

    if ($authUser && is_numeric($authUser->name ?? null)) {
        $currentPresenceEmployeeId = (int) $authUser->name;
    } elseif ($authUser && !empty($authUser->employee_id) && is_numeric($authUser->employee_id)) {
        $currentPresenceEmployeeId = (int) $authUser->employee_id;
    }

    $currentPresenceEmployee = $currentPresenceEmployeeId
        ? \App\Models\Employee::query()
            ->select(['id', 'name', 'lastname', 'email', 'image'])
            ->find($currentPresenceEmployeeId)
        : null;

    $currentPresenceName = trim(
        ($currentPresenceEmployee?->name ?? '') . ' ' .
        ($currentPresenceEmployee?->lastname ?? '')
    );

    if ($currentPresenceName === '') {
        $currentPresenceName = trim(($authUser->firstname ?? '') . ' ' . ($authUser->lastname ?? ''));
    }

    if ($currentPresenceName === '') {
        $currentPresenceName = $authUser?->username
            ?? $authUser?->email
            ?? ($currentPresenceEmployeeId ? ('Mitarbeiter #' . $currentPresenceEmployeeId) : ('Benutzer #' . ($authUser?->id ?? '')));
    }

    $currentPresenceAvatar = asset('images/gender/male.png');

    if ($currentPresenceEmployee && filled($currentPresenceEmployee->image)) {
        $currentPresenceAvatar = asset('images/employee/' . ltrim($currentPresenceEmployee->image, '/'));
    }

    $currentPresenceUserPayload = [
        'id' => $authUser?->id,
        'user_id' => $authUser?->id,
        'employee_id' => $currentPresenceEmployee?->id ?? $currentPresenceEmployeeId,
        'name' => $currentPresenceName,
        'avatar' => $currentPresenceAvatar,
        'email' => $currentPresenceEmployee?->email ?? $authUser?->email,
    ];

@endphp

@once
    @push('style')
        <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
        <style>
            :root {
                --of-bg: #f3f4f6;
                --of-card: #ffffff;
                --of-card-soft: #f8fafc;
                --of-text: #111827;
                --of-muted: #6b7280;
                --of-line: #e5e7eb;
                --of-primary: var(--sa-accent);
                --of-primary-hover: var(--sa-accent-hover);
                --of-primary-soft: var(--sa-accent-light);
                --of-blue: #2563eb;
                --of-success: #10b981;
                --of-success-soft: #ecfdf5;
                --of-warning: #f59e0b;
                --of-warning-soft: #fffbeb;
                --of-danger: #ef4444;
                --of-danger-hover: #dc2626;
                --of-danger-soft: #fef2f2;
                --of-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
                --of-shadow: 0 10px 25px -10px rgb(0 0 0 / .20), 0 4px 10px -4px rgb(0 0 0 / .08);
                --of-radius: 18px;
                --of-radius-lg: 24px;
                --of-transition: all .2s ease-in-out;
            }

            /* ========= Layout ========= */
            .of-wrap {
                font-family: Inter, system-ui, -apple-system, sans-serif;
                color: var(--of-text);
            }

            .of-header {
                margin: 18px;
                background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
                border: 1px solid var(--of-line);
                border-radius: var(--of-radius-lg);
                box-shadow: var(--of-shadow);
                overflow: hidden;
            }

            .of-header-inner {
                padding: 22px;
            }

            .of-shell {
                background: var(--of-card);
                border: 1px solid var(--of-line);
                border-radius: var(--of-radius-lg);
                box-shadow: var(--of-shadow);
                overflow: visible;
            }

            .of-shell-head {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 14px;
                flex-wrap: wrap;
                padding: 12px 14px;
                border-bottom: 1px solid var(--of-line);
                background: #fafafa;
                position: relative;
                z-index: 200;
                overflow: visible;
            }

            .of-shell-body {
                padding: 14px;
            }

            .of-panel {
                display: none;
            }

            .of-panel.active {
                display: block;
            }

            .of-grid-2 {
                display: grid;
                grid-template-columns: 420px minmax(0, 1fr);
                gap: 18px;
            }

            /* ========= Header / Banner ========= */
            .of-top,
            .of-banner-main,
            .of-banner-slim-main {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 18px;
                flex-wrap: wrap;
            }

            .of-head-left,
            .of-banner-slim-left {
                display: flex;
                align-items: flex-start;
                gap: 16px;
                min-width: 0;
                flex: 1;
            }

            .of-head-content,
            .of-head-content-slim,
            .of-kanban-brand-left {
                min-width: 0;
                flex: 1;
            }

            .of-icon-box {
                width: 68px;
                height: 68px;
                border-radius: 18px;
                background: linear-gradient(135deg, var(--of-primary-soft), #ffffff);
                border: 1px solid #d9ef9d;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                box-shadow: var(--of-shadow-sm);
            }

            .of-title {
                font-size: 30px;
                font-weight: 900;
                letter-spacing: -.03em;
                color: #111827;
                line-height: 1.1;
                margin: 0;
            }

            .of-title-row,
            .of-title-row-slim {
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .of-sub {
                color: var(--of-muted);
                font-size: 14px;
                margin-top: 8px;
                line-height: 1.7;
            }

            .of-banner,
            .of-banner-slim {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            .of-banner-inline-row {
                margin-top: 10px;
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .of-banner-stats {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 12px;
                padding-top: 14px;
                border-top: 1px solid var(--of-line);
            }

            .of-banner-stat {
                background: #fff;
                border: 1px solid #eef2f7;
                border-radius: 16px;
                padding: 12px 14px;
                min-width: 0;
            }

            .of-banner-stat-label {
                font-size: 11px;
                font-weight: 900;
                letter-spacing: .07em;
                text-transform: uppercase;
                color: #6b7280;
            }

            .of-banner-stat-value {
                margin-top: 6px;
                font-size: 20px;
                line-height: 1.15;
                font-weight: 900;
                color: #111827;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* ========= Compact / Slim ========= */
            .of-header-slim {
                margin: 12px;
                border-radius: 16px;
            }

            .of-header-inner-slim {
                padding: 12px 14px;
            }

            .of-banner-slim {
                gap: 10px;
            }

            .of-banner-slim-main {
                gap: 12px;
            }

            .of-banner-slim-left {
                gap: 10px;
            }

            .of-icon-box-slim {
                width: 42px;
                height: 42px;
                border-radius: 12px;
            }

            .of-icon-box-slim svg {
                width: 18px;
                height: 18px;
            }

            .of-title-slim {
                font-size: 18px;
                line-height: 1.05;
                margin: 0;
            }

            .of-sub-slim {
                margin-top: 0;
                font-size: 12px;
                line-height: 1.4;
            }

            .of-title-row-slim {
                gap: 8px;
                margin-bottom: 2px;
            }

            .of-meta-row-slim {
                margin-top: 0;
                gap: 6px;
            }

            .of-meta-row-slim .of-meta-pill {
                padding: 5px 8px;
                font-size: 10px;
                gap: 6px;
            }

            .of-doc-switch-slim {
                padding: 4px;
                gap: 4px;
                border-radius: 12px;
            }

            .of-doc-switch-note-slim {
                margin-top: 8px;
                padding: 7px 9px;
                font-size: 10px;
                line-height: 1.45;
            }

            .of-presence-slim {
                margin-top: 8px;
                padding: 6px 8px;
                gap: 8px;
                border-radius: 12px;
            }

            .of-actions-slim {
                display: flex;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
            }

            .of-actions-slim .of-btn {
                min-height: 34px;
                padding: 7px 10px;
                font-size: 11px;
                border-radius: 10px;
            }

            .of-actions-slim .of-btn svg {
                width: 14px;
                height: 14px;
            }

            .of-banner-stats-slim {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 8px;
                padding-top: 0;
                border-top: none;
            }

            .of-banner-stat-slim {
                padding: 9px 10px;
                border-radius: 12px;
            }

            .of-banner-stat-slim .of-banner-stat-label {
                font-size: 9px;
                letter-spacing: .05em;
            }

            .of-banner-stat-slim .of-banner-stat-value {
                margin-top: 3px;
                font-size: 14px;
                line-height: 1.05;
            }

            /* ========= Meta / Pills / Presence ========= */
            .of-meta-row {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 14px;
            }

            .of-meta-pill {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                border-radius: 999px;
                border: 1px solid var(--of-line);
                background: #fff;
                font-size: 12px;
                font-weight: 800;
                color: #374151;
                box-shadow: var(--of-shadow-sm);
            }

            .of-presence {
                margin-top: 14px;
                display: flex;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
                padding: 10px 12px;
                border: 1px solid var(--of-line);
                background: #fff;
                border-radius: 16px;
                box-shadow: var(--of-shadow-sm);
            }

            .of-presence-compact {
                margin-top: 10px;
                padding: 8px 12px;
            }

            .of-presence-label {
                font-size: 12px;
                font-weight: 900;
                color: #374151;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .of-presence-list {
                display: flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 10px;
            }

            .of-presence-empty {
                font-size: 12px;
                color: var(--of-muted);
                font-weight: 700;
            }

            .of-presence-user {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: #f8fafc;
                border: 1px solid var(--of-line);
                border-radius: 999px;
                padding: 6px 10px 6px 6px;
            }

            .of-presence-avatar-wrap {
                position: relative;
                width: 30px;
                height: 30px;
                flex: 0 0 auto;
            }

            .of-presence-avatar {
                width: 30px;
                height: 30px;
                border-radius: 999px;
                object-fit: cover;
                border: 2px solid #fff;
                display: block;
                background: #e5e7eb;
            }

            .of-presence-dot {
                position: absolute;
                right: -1px;
                bottom: -1px;
                width: 10px;
                height: 10px;
                border-radius: 999px;
                background: #10b981;
                border: 2px solid #fff;
            }

            .of-presence-name {
                font-size: 12px;
                font-weight: 800;
                color: #111827;
                max-width: 180px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* ========= Buttons / Badges ========= */
            .of-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            .of-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                border: none;
                background: var(--of-primary);
                color: #fff;
                padding: 8px 11px;
                border-radius: 10px;
                font-size: 11px;
                font-weight: 800;
                cursor: pointer;
                text-decoration: none;
                transition: var(--of-transition);
                box-shadow: var(--of-shadow-sm);
            }

            .of-btn:hover {
                background: var(--of-primary-hover);
                color: #fff;
            }

            .of-btn.soft {
                background: #fff;
                color: var(--of-text);
                border: 1px solid var(--of-line);
            }

            .of-btn.soft:hover {
                background: #f9fafb;
            }

            .of-btn.danger {
                color: #fff !important;
                background: var(--of-danger);
            }

            .of-btn.danger:hover {
                background: var(--of-danger-hover);
            }

            .of-btn[disabled] {
                opacity: .55;
                cursor: not-allowed;
                pointer-events: none;
            }

            .of-inline-actions {
                display: flex;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
            }

            .of-badge,
            .of-history-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 5px 8px;
                border-radius: 999px;
                background: #f8fafc;
                border: 1px solid var(--of-line);
                font-size: 10px;
                font-weight: 900;
                color: #4b5563;
            }

            .of-selected-badge {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                border-radius: 999px;
                background: var(--of-primary-soft);
                border: 1px solid #d9ef9d;
                color: #55720d;
                font-size: 12px;
                font-weight: 900;
            }

            /* ========= Tabs ========= */
            .of-tabs {
                display: flex;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
                position: relative;
                z-index: 210;
                overflow: visible;
            }

            .of-tab {
                border: 1px solid var(--of-line);
                background: #fff;
                color: #374151;
                border-radius: 10px;
                padding: 7px 10px;
                font-size: 11px;
                font-weight: 800;
                cursor: pointer;
                transition: var(--of-transition);
                display: inline-flex;
                align-items: center;
                gap: 6px;
                min-height: 36px;
                box-shadow: var(--of-shadow-sm);
            }

            .of-tab:hover {
                background: #f9fafb;
                border-color: #d1d5db;
                transform: translateY(-1px);
            }

            .of-tab.active {
                background: var(--of-primary-soft);
                border-color: #d8ec9d;
                color: #55720d;
            }

            .of-tab-icon {
                width: 14px;
                height: 14px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
            }

            .of-tab-icon svg {
                width: 14px;
                height: 14px;
            }

            .of-tab-label {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                line-height: 1;
            }

            .of-tab-count {
                min-width: 18px;
                height: 18px;
                padding: 0 5px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 10px;
                font-weight: 900;
                background: #f3f4f6;
                color: #374151;
                border: 1px solid #e5e7eb;
                line-height: 1;
            }

            .of-tab.active .of-tab-count {
                background: #fff;
                border-color: #d9ef9d;
                color: #55720d;
            }

            /* ========= Status Chips ========= */
            .of-status-chip,
            .of-doc-status-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 5px 9px;
                border-radius: 999px;
                font-size: 10px;
                font-weight: 900;
                border: 1px solid var(--of-line);
                white-space: nowrap;
                line-height: 1;
            }

            .of-status-chip-draft {
                background: #f3f4f6;
                color: #4b5563;
                border-color: #d1d5db;
            }

            .of-status-chip-pending {
                background: #fff7ed;
                color: #c2410c;
                border-color: #fdba74;
            }

            .of-status-chip-sent {
                background: #eff6ff;
                color: #74b2d4;
                border-color: #bfdbfe;
            }

            .of-status-chip-viewed {
                background: #ecfeff;
                color: #0f766e;
                border-color: #99f6e4;
            }

            .of-status-chip-negotiation {
                background: #fffbeb;
                color: #b45309;
                border-color: #fde68a;
            }

            .of-status-chip-revised {
                background: #f5f3ff;
                color: #6d28d9;
                border-color: #c4b5fd;
            }

            .of-status-chip-final {
                background: #ecfdf5;
                color: #047857;
            }

            .of-status-chip-cancel {
                background: #fef2f2;
                color: #b91c1c;
                border-color: #fecaca;
            }

            .of-status-chip-expired {
                background: #f3f4f6;
                color: #374151;
                border-color: #d1d5db;
            }

            .of-doc-status-badge {
                gap: 8px;
                padding: 6px 10px;
                font-size: 11px;
                background: #fff;
                color: #374151;
            }

            .of-doc-status-badge.offer {
                background: #eff6ff;
                border-color: #bfdbfe;
                color: #74b2d4;
            }

            .of-doc-status-badge.deal {
                background: #93c21c;
                color: #fff;
            }

            .of-doc-status-badge.auftrag {
                background: #ecfdf5;
                color: #047857;
            }

            /* ========= Document Switch ========= */
            .of-doc-switch-wrap {
                margin-top: 14px;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .of-doc-switch-label {
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .08em;
                color: #6b7280;
            }

            .of-doc-switch {
                display: inline-flex;
                align-items: center;
                flex-wrap: wrap;
                gap: 8px;
                padding: 8px;
                border: 1px solid var(--of-line);
                border-radius: 18px;
                background: #fff;
                box-shadow: var(--of-shadow-sm);
            }

            .of-doc-toggle {
                min-width: 86px;
                border: 1px solid var(--of-line);
                background: #fff;
                color: #374151;
                border-radius: 10px;
                padding: 7px 10px;
                font-size: 11px;
                font-weight: 900;
                cursor: pointer;
                transition: var(--of-transition);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            }

            .of-doc-toggle:hover {
                background: #f9fafb;
                border-color: #d1d5db;
            }

            .of-doc-toggle.active {
                background: var(--of-primary-soft);
                border-color: #d8ec9d;
                color: #55720d;
            }

            .of-doc-toggle.offer.active {
                background: #eff6ff;
                border-color: #bfdbfe;
                color: #74b2d4;
            }

            .of-doc-toggle.deal.active {
                background: #93c21c;
                color: #fff;
            }

            .of-doc-switch-note {
                font-size: 12px;
                line-height: 1.65;
                color: #6b7280;
                padding: 10px 12px;
                border: 1px dashed #d1d5db;
                border-radius: 14px;
                background: #fafafa;
            }

            .of-doc-switch-note.warning {
                background: #fffbeb;
                border-color: #fde68a;
                color: #92400e;
            }

            /* ========= Cards / Info ========= */
            .of-card {
                background: var(--of-card);
                overflow: visible;
            }

            .of-card-h,
            .of-card-title,
            .of-section-head,
            .of-material-detail-head {
                position: relative;
                z-index: 1;
            }

            .of-card-h {
                padding: 12px 14px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                background: #fafafa;
                flex-wrap: wrap;
            }

            .of-card-title {
                font-size: 14px;
                font-weight: 900;
                color: #111827;
                margin: 0;
            }

            .of-card-b {
                padding: 14px;
            }

            .of-info-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .of-info-item {
                display: flex;
                justify-content: space-between;
                gap: 14px;
                padding-bottom: 12px;
                border-bottom: 1px dashed #e5e7eb;
            }

            .of-info-item:last-child {
                border-bottom: none;
                padding-bottom: 0;
            }

            .of-info-key {
                color: #6b7280;
                font-size: 13px;
                font-weight: 800;
            }

            .of-info-val {
                color: #111827;
                font-size: 13px;
                font-weight: 900;
                text-align: right;
                word-break: break-word;
            }

            .of-cover {
                min-height: 180px;
                border: 1px dashed #d1d5db;
                border-radius: 14px;
                background: #fafafa;
                padding: 16px;
                color: #374151;
                font-size: 14px;
                line-height: 1.7;
            }

            .of-cover.empty {
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                color: #9ca3af;
            }

            .of-empty,
            .of-smart-empty {
                text-align: center;
                padding: 18px 16px;
                border: 1px dashed var(--of-line);
                border-radius: 14px;
                color: var(--of-muted);
                font-size: 12px;
                background: #fff;
            }

            /* ========= Overview / Status ========= */
            .of-stats {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 16px;
                margin-bottom: 18px;
            }

            .of-stat {
                background: var(--of-card);
                border: 1px solid var(--of-line);
                border-radius: 18px;
                box-shadow: var(--of-shadow-sm);
                padding: 18px;
            }

            .of-stat-label {
                font-size: 12px;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: var(--of-muted);
                font-weight: 900;
            }

            .of-stat-value {
                margin-top: 8px;
                font-size: 28px;
                line-height: 1.1;
                font-weight: 900;
                color: #111827;
            }

            .of-stat-sub {
                margin-top: 6px;
                font-size: 13px;
                color: #6b7280;
            }

            .of-status-overview {
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 10px;
                margin-top: 12px;
            }

            .of-status-card {
                border: 1px solid var(--of-line);
                border-radius: 14px;
                background: #fff;
                box-shadow: var(--of-shadow-sm);
                padding: 12px;
            }

            .of-status-name {
                font-size: 11px;
                color: #6b7280;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .06em;
            }

            .of-status-value {
                margin-top: 5px;
                font-size: 20px;
                font-weight: 900;
                color: #111827;
            }

            /* ========= Kanban / Workflow ========= */
            .of-kanban {
                display: grid;
                grid-template-columns: repeat(5, minmax(320px, 1fr));
                gap: 18px;
                align-items: start;
            }

            .of-col {
                background: linear-gradient(180deg, #f8fafc 0%, #f3f4f6 100%);
                border: 1px solid var(--of-line);
                border-radius: 22px;
                min-height: 420px;
                display: flex;
                flex-direction: column;
                overflow: hidden;
                box-shadow: var(--of-shadow-sm);
            }

            .of-col-h {
                padding: 16px 18px;
                border-bottom: 1px solid var(--of-line);
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
                background: #fff;
            }

            .of-col-name {
                font-size: 15px;
                font-weight: 900;
                color: #111827;
                letter-spacing: -.01em;
            }

            .of-col-count {
                min-width: 30px;
                height: 30px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: var(--of-primary-soft);
                border: 1px solid #d8ec9d;
                font-size: 12px;
                font-weight: 900;
                color: #55720d;
            }

            .of-col[data-status="draft"] .of-col-h {
                background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            }

            .of-col[data-status="draft"] .of-col-count {
                background: #f3f4f6;
                border-color: #d1d5db;
                color: #4b5563;
            }

            .of-col[data-status="sent"] .of-col-count {
                background: #eff6ff;
                border-color: #bfdbfe;
                color: #74b2d4;
            }

            .of-col[data-status="negotiation"] .of-col-count {
                background: #fffbeb;
                border-color: #fde68a;
                color: #b45309;
            }

            .of-col[data-status="final"] .of-col-count {
                background: #93c21c;
                color: #fff;
            }

            .of-col[data-status="cancel"] .of-col-count {
                background: #fef2f2;
                border-color: #fecaca;
                color: #b91c1c;
            }

            .of-list {
                padding: 16px;
                display: flex;
                flex-direction: column;
                gap: 14px;
                min-height: 220px;
                flex: 1;
            }

            .of-item {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 20px;
                padding: 0;
                box-shadow: 0 8px 24px rgba(15, 23, 42, .06);
                transition: var(--of-transition);
                cursor: grab;
                overflow: hidden;
            }

            .of-item:hover {
                border-color: #cbd5e1;
                transform: translateY(-2px);
                box-shadow: 0 12px 28px rgba(15, 23, 42, .10);
            }

            .of-kanban-offer {
                display: flex;
                flex-direction: column;
            }

            .of-kanban-offer-top {
                padding: 18px 18px 16px;
                border-bottom: 1px solid #eef2f7;
                background:
                    radial-gradient(circle at top right, rgba(147, 194, 28, .10), transparent 30%),
                    linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
            }

            .of-kanban-brand {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 14px;
                margin-bottom: 16px;
            }

            .of-kanban-company-mini,
            .of-kanban-block-label,
            .of-kanban-meta-label,
            .of-kanban-product-label,
            .of-workflow-box-label {
                font-size: 10px;
                font-weight: 900;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: .08em;
            }

            .of-kanban-doc-title {
                margin-top: 8px;
                font-size: 20px;
                line-height: 1.1;
                font-weight: 900;
                color: #7baa18;
                text-transform: uppercase;
            }

            .of-kanban-status-chip {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 7px 10px;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 900;
                border: 1px solid #e5e7eb;
                white-space: nowrap;
            }

            .of-kanban-status-chip.draft {
                background: #f3f4f6;
                color: #4b5563;
                border-color: #d1d5db;
            }

            .of-kanban-status-chip.sent {
                background: #eff6ff;
                color: #74b2d4;
                border-color: #bfdbfe;
            }

            .of-kanban-status-chip.negotiation {
                background: #fffbeb;
                color: #b45309;
                border-color: #fde68a;
            }

            .of-kanban-status-chip.final {
                background: #ecfdf5;
                color: #047857;
            }

            .of-kanban-status-chip.cancel {
                background: #fef2f2;
                color: #b91c1c;
                border-color: #fecaca;
            }

            .of-kanban-grid,
            .of-kanban-meta {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 14px;
            }

            .of-kanban-block,
            .of-kanban-meta-card {
                min-width: 0;
            }

            .of-kanban-block-value,
            .of-workflow-box-value {
                font-size: 13px;
                line-height: 1.65;
                color: #111827;
                font-weight: 800;
                word-break: break-word;
            }

            .of-kanban-body {
                padding: 16px 18px;
            }

            .of-kanban-meta {
                gap: 10px;
                margin-bottom: 14px;
            }

            .of-kanban-meta-card,
            .of-workflow-box {
                border: 1px solid #eef2f7;
                border-radius: 14px;
                background: #fafafa;
                padding: 12px;
            }

            .of-kanban-meta-value {
                margin-top: 6px;
                font-size: 14px;
                font-weight: 900;
                color: #111827;
                line-height: 1.4;
            }

            .of-kanban-product {
                border: 1px dashed #dbe3ea;
                border-radius: 14px;
                background: #fcfcfd;
                padding: 12px 14px;
            }

            .of-kanban-product-value {
                margin-top: 6px;
                font-size: 13px;
                font-weight: 900;
                color: #111827;
                line-height: 1.55;
            }

            .of-kanban-note,
            .of-workflow-note {
                margin-top: 12px;
                font-size: 12px;
                color: #6b7280;
                line-height: 1.65;
            }

            .of-item-actions {
                display: flex;
                justify-content: flex-end;
                gap: 8px;
                padding: 14px 18px 18px;
            }

            .of-item-action,
            .of-workflow-status-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                min-height: 36px;
                padding: 0 12px;
                border-radius: 10px;
                border: 1px solid var(--of-line);
                background: #fff;
                color: #111827;
                font-size: 12px;
                font-weight: 800;
                cursor: pointer;
                text-decoration: none;
                transition: var(--of-transition);
            }

            .of-item-action:hover,
            .of-workflow-status-btn:hover {
                background: #f8fafc;
                border-color: #cbd5e1;
            }

            .of-item-action.primary,
            .of-workflow-status-btn.active {
                background: var(--of-primary-soft);
                border-color: #d9ef9d;
                color: #55720d;
            }

            .of-item-action.primary:hover {
                background: #edf8d2;
            }

            .of-workflow-status-btn[disabled] {
                opacity: .45;
                cursor: not-allowed;
                pointer-events: none;
            }

            .of-workflow-list-wrap {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .of-workflow-list-card {
                border: 1px solid var(--of-line);
                border-radius: 22px;
                background: #fff;
                box-shadow: var(--of-shadow-sm);
                overflow: hidden;
            }

            .of-workflow-list-head {
                padding: 16px 18px;
                border-bottom: 1px solid var(--of-line);
                background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .of-workflow-list-head-left {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .of-workflow-list-title {
                font-size: 15px;
                font-weight: 900;
                color: #111827;
                margin: 0;
            }

            .of-workflow-list-sub {
                font-size: 12px;
                color: #6b7280;
                line-height: 1.6;
                margin-top: 4px;
            }

            .of-workflow-status-pill {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 7px 12px;
                border-radius: 999px;
                font-size: 12px;
                font-weight: 900;
                border: 1px solid var(--of-line);
                white-space: nowrap;
                line-height: 1;
            }

            .of-workflow-status-pill.draft {
                background: #f3f4f6;
                color: #4b5563;
                border-color: #d1d5db;
            }

            .of-workflow-status-pill.pending {
                background: #fff7ed;
                color: #c2410c;
                border-color: #fdba74;
            }

            .of-workflow-status-pill.sent {
                background: #eff6ff;
                color: #74b2d4;
                border-color: #bfdbfe;
            }

            .of-workflow-status-pill.viewed {
                background: #ecfeff;
                color: #0f766e;
                border-color: #99f6e4;
            }

            .of-workflow-status-pill.negotiation {
                background: #fffbeb;
                color: #b45309;
                border-color: #fde68a;
            }

            .of-workflow-status-pill.revised {
                background: #f5f3ff;
                color: #6d28d9;
                border-color: #c4b5fd;
            }

            .of-workflow-status-pill.final {
                background: #ecfdf5;
                color: #047857;
            }

            .of-workflow-status-pill.cancel {
                background: #fef2f2;
                color: #b91c1c;
                border-color: #fecaca;
            }

            .of-workflow-status-pill.expired {
                background: #f3f4f6;
                color: #374151;
                border-color: #d1d5db;
            }

            .of-workflow-list-body {
                padding: 18px;
            }

            .of-workflow-grid {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 14px;
                margin-bottom: 16px;
            }

            .of-workflow-note {
                border: 1px dashed #dbe3ea;
                border-radius: 16px;
                background: #fcfcfd;
                padding: 14px 16px;
                margin-bottom: 16px;
            }

            .of-workflow-actions {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                flex-wrap: wrap;
                padding-top: 14px;
                border-top: 1px solid #eef2f7;
            }

            .of-workflow-status-actions {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }

            /* ========= Stepper ========= */
            .of-stepper-wrap {
                display: flex;
                flex-direction: column;
                gap: 1px;
                min-width: 0;
            }

            .of-stepper-card {
                border: 1px solid var(--of-line);
                border-radius: 22px;
                background: #fff;
                box-shadow: var(--of-shadow-sm);
                overflow: hidden;
                min-width: 0;
            }

            .of-stepper-head {
                padding: 12px 14px 10px;
                border-bottom: 1px solid var(--of-line);
                background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 14px;
                flex-wrap: wrap;
            }

            .of-stepper-title {
                margin: 0;
                font-size: 14px;
                font-weight: 900;
                color: #111827;
            }

            .of-stepper-sub {
                margin-top: 6px;
                font-size: 11px;
                color: #6b7280;
                line-height: 1.45;
            }

            .of-stepper-body {
                padding: 12px 14px 14px;
                min-width: 0;
                overflow: hidden;
            }

            .of-stepper {
                display: flex;
                flex-wrap: nowrap;
                gap: 1px;
                align-items: center;
                width: max-content;
                min-width: 100%;
                overflow-x: auto;
                overflow-y: hidden;
                padding-bottom: 6px;
                scrollbar-width: thin;
                -webkit-overflow-scrolling: touch;
            }

            .of-step {
                position: relative;
                display: flex;
                align-items: center;
                justify-content: center;
                min-height: 38px;
                padding: 0 14px 0 12px;
                border: none;
                font-size: 10px;
                font-weight: 900;
                letter-spacing: .02em;
                text-transform: uppercase;
                cursor: pointer;
                transition: var(--of-transition);
                clip-path: polygon(0 0, calc(100% - 18px) 0, 100% 50%, calc(100% - 18px) 100%, 0 100%, 12px 50%);
                box-shadow: 0 6px 18px rgba(15, 23, 42, .08);
                flex: 0 0 auto;
                white-space: nowrap;
                background: #cfe09b;
                color: #3f4f18;
            }

            .of-step:first-child {
                clip-path: polygon(0 0, calc(100% - 18px) 0, 100% 50%, calc(100% - 18px) 100%, 0 100%);
                padding-left: 10px;
            }

            .of-step:hover {
                transform: translateY(-1px);
                filter: brightness(.98);
            }

            .of-step[disabled] {
                cursor: default;
                opacity: 1;
            }

            .of-step-label {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                white-space: nowrap;
            }

            .of-step-index {
                width: 18px;
                height: 18px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                background: rgba(255, 255, 255, .72);
                border: 1px solid rgba(255, 255, 255, .9);
                font-size: 10px;
                font-weight: 900;
                flex: 0 0 auto;
                color: #3f4f18;
            }

            .of-step.is-past {
                background: #74b2d4;
                color: #fff;
            }

            .of-step.is-past .of-step-index {
                background: rgba(255, 255, 255, .22);
                border-color: rgba(255, 255, 255, .35);
                color: #fff;
            }

            .of-step.is-current {
                background: #93c21c;
                color: #fff;
                outline: 3px solid rgba(147, 194, 28, .18);
                transform: translateY(-1px);
                box-shadow: 0 10px 24px rgba(147, 194, 28, .28);
            }

            .of-step.is-current .of-step-index {
                background: #fff;
                color: #6b8d12;
                border-color: #fff;
            }

            .of-step.is-future {
                background: #cfe09b;
                color: #4b5f1d;
            }

            .of-step.is-future .of-step-index {
                background: rgba(255, 255, 255, .8);
                border-color: rgba(255, 255, 255, .95);
                color: #4b5f1d;
            }

            .of-step-meta {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 8px;
                margin-top: 12px;
            }

            /* ========= Sections ========= */
            .of-sections {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
                gap: 16px;
            }

            .of-section-card {
                border: 1px solid var(--of-line);
                border-radius: 16px;
                background: #fff;
                box-shadow: var(--of-shadow-sm);
                overflow: hidden;
            }

            .of-section-head {
                padding: 14px 16px;
                border-bottom: 1px solid var(--of-line);
                background: #fafafa;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
            }

            .of-section-title {
                font-size: 14px;
                font-weight: 900;
                color: #111827;
            }

            .of-section-body {
                padding: 16px;
            }

            .of-section-desc {
                font-size: 13px;
                color: #6b7280;
                line-height: 1.6;
            }

            .of-section-stats {
                margin-top: 14px;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            /* ========= History ========= */
            .of-history-placeholder {
                min-height: 320px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                gap: 10px;
                border: 1px dashed var(--of-line);
                border-radius: 18px;
                background: #fafafa;
                color: #9ca3af;
                padding: 28px;
                text-align: center;
            }

            .of-history-list,
            .of-history-inline-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .of-history-item {
                position: relative;
                display: grid;
                grid-template-columns: 56px 1fr;
                gap: 14px;
                align-items: flex-start;
            }

            .of-history-dot-wrap {
                display: flex;
                justify-content: center;
                position: relative;
            }

            .of-history-dot-wrap::after {
                content: "";
                position: absolute;
                top: 32px;
                bottom: -18px;
                width: 2px;
                background: #e5e7eb;
            }

            .of-history-item:last-child .of-history-dot-wrap::after {
                display: none;
            }

            .of-history-dot {
                width: 16px;
                height: 16px;
                border-radius: 999px;
                background: var(--of-primary);
                border: 4px solid #f4fae7;
                box-shadow: 0 0 0 2px #d9ef9d;
                margin-top: 6px;
            }

            .of-history-card,
            .of-history-inline-item {
                border: 1px solid var(--of-line);
                border-radius: 16px;
                background: #fff;
                box-shadow: var(--of-shadow-sm);
                padding: 14px 16px;
            }

            .of-history-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
                flex-wrap: wrap;
            }

            .of-history-title,
            .of-history-inline-title {
                font-size: 13px;
                font-weight: 900;
                color: #111827;
                line-height: 1.45;
            }

            .of-history-date {
                font-size: 12px;
                font-weight: 800;
                color: #6b7280;
                white-space: nowrap;
            }

            .of-history-meta {
                margin-top: 8px;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }

            .of-history-text,
            .of-history-inline-sub {
                margin-top: 10px;
                font-size: 12px;
                line-height: 1.7;
                color: #374151;
            }

            /* ========= Tables / Material ========= */
            .of-table-wrap {
                width: 100%;
                overflow: auto;
                border: 1px solid var(--of-line);
                border-radius: 18px;
                background: #fff;
                box-shadow: var(--of-shadow-sm);
            }

            .of-table {
                width: 100%;
                border-collapse: collapse;
                min-width: 980px;
            }

            .of-table th,
            .of-table td {
                padding: 9px 10px;
                border-bottom: 1px solid #eef2f7;
                text-align: left;
                vertical-align: top;
                font-size: 12px;
            }

            .of-table th {
                background: #f8fafc;
                color: #374151;
                font-weight: 900;
                white-space: nowrap;
            }

            .of-table td {
                color: #111827;
            }

            .of-table tr:hover td {
                background: transparent;
            }

            .of-table .num {
                text-align: right;
                white-space: nowrap;
                font-variant-numeric: tabular-nums;
            }

            .of-table .muted {
                color: #6b7280;
            }

            .of-table-check {
                width: 42px;
                text-align: center !important;
            }

            .of-check {
                width: 18px;
                height: 18px;
                accent-color: var(--of-primary);
                cursor: pointer;
            }

            .of-table tr.is-selected td {
                background: #f4fae7 !important;
            }

            .of-table tr.is-selected:hover td {
                background: #edf8d2 !important;
            }

            .of-material-row-click {
                cursor: pointer;
            }

            .of-material-row-click:hover td {
                background: #f8fbf0 !important;
            }

            .of-mat-name {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .of-mat-title {
                font-weight: 900;
                color: #111827;
                line-height: 1.45;
            }

            .of-mat-meta {
                display: flex;
                flex-wrap: wrap;
                gap: 6px;
            }

            .of-mat-chip {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 8px;
                border-radius: 999px;
                background: #f8fafc;
                border: 1px solid #e5e7eb;
                font-size: 11px;
                font-weight: 800;
                color: #4b5563;
            }

            .of-mat-desc {
                color: #6b7280;
                font-size: 12px;
                line-height: 1.55;
                display: none;
            }

            .of-material-toolbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                flex-wrap: wrap;
                margin-bottom: 14px;
            }

            .of-material-toolbar-left,
            .of-material-toolbar-right {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            /* ========= Column Picker ========= */
            .of-colpicker {
                position: relative;
                z-index: 50;
            }

            .of-colpicker summary {
                list-style: none;
            }

            .of-colpicker summary::-webkit-details-marker {
                display: none;
            }

            .of-colpicker[open] {
                z-index: 9999;
            }

            .of-colpicker-menu {
                position: absolute;
                right: 0;
                top: calc(100% + 8px);
                width: 280px;
                max-height: 70vh;
                overflow: auto;
                background: #fff;
                border: 1px solid var(--of-line);
                border-radius: 16px;
                box-shadow: 0 20px 50px rgba(15, 23, 42, .18);
                padding: 12px;
                z-index: 99999;
            }

            .of-colpicker-grid {
                display: grid;
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .of-colpicker-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 8px 10px;
                border: 1px solid #eef2f7;
                border-radius: 12px;
                background: #fafafa;
                font-size: 12px;
                font-weight: 800;
                color: #374151;
            }

            .of-colpicker-item input {
                width: 16px;
                height: 16px;
                accent-color: var(--of-primary);
                cursor: pointer;
            }

            /* ========= Files ========= */
            .of-file-list {
                display: flex;
                flex-direction: column;
                gap: 12px;
            }

            .of-file-list.sortable-enabled .of-file-row {
                cursor: grab;
            }

            .of-file-row {
                border: 1px solid var(--of-line);
                border-radius: 16px;
                background: #fff;
                box-shadow: var(--of-shadow-sm);
                padding: 14px 16px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 14px;
                flex-wrap: wrap;
            }

            .of-file-left {
                min-width: 0;
                flex: 1;
            }

            .of-file-title {
                font-size: 14px;
                font-weight: 900;
                color: #111827;
                word-break: break-word;
            }

            .of-file-meta {
                margin-top: 6px;
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
            }

            .of-file-actions {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .of-file-preview {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                border-radius: 10px;
                border: 1px solid var(--of-line);
                background: #fff;
                text-decoration: none;
                color: #111827;
                font-weight: 800;
            }

            .of-file-type-badge.pdf {
                background: #eff6ff;
                border-color: #bfdbfe;
                color: #74b2d4;
            }

            .of-file-type-badge.image {
                background: #ecfdf5;
                color: #047857;
            }

            .of-dropzone-over {
                border-color: #93c21c !important;
                background: #f4fae7 !important;
            }

            /* ========= AGB Editor ========= */
            #agb-editor {
                min-height: 360px;
                border: 1px solid var(--of-line);
                border-radius: 12px;
                background: #fff;
            }

            #agb-editor .ql-toolbar.ql-snow {
                border: none;
                border-bottom: 1px solid var(--of-line);
                background: #f8fafc;
            }

            #agb-editor .ql-container.ql-snow {
                border: none;
                min-height: 300px;
                font-size: 14px;
                line-height: 1.7;
            }

            #agb-editor .ql-editor {
                min-height: 300px;
                color: #111827;
            }

            /* ========= Smart Sidebar ========= */
            .of-smart-side {
                position: fixed;
                top: 140px;
                right: 24px;
                width: 515px;
                max-width: calc(100vw - 32px);
                z-index: 10020;
                display: none;
            }

            .of-smart-side.show {
                display: block;
            }

            .of-smart-card {
                background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
                border: 1px solid var(--of-line);
                border-radius: 24px;
                box-shadow: 0 24px 70px rgba(15, 23, 42, .18);
                overflow: hidden;
            }

            .of-smart-head {
                padding: 18px 18px 14px;
                border-bottom: 1px solid var(--of-line);
                background:
                    radial-gradient(circle at top right, rgba(147, 194, 28, .15), transparent 34%),
                    linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
            }

            .of-smart-head-row {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
            }

            .of-smart-icon {
                width: 48px;
                height: 48px;
                border-radius: 16px;
                background: var(--of-primary-soft);
                border: 1px solid #d9ef9d;
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
            }

            .of-smart-title {
                font-size: 16px;
                font-weight: 900;
                color: #111827;
                line-height: 1.3;
                margin: 0;
            }

            .of-smart-sub {
                margin-top: 6px;
                font-size: 12px;
                color: #6b7280;
                line-height: 1.6;
            }

            .of-smart-close {
                width: 36px;
                height: 36px;
                border: none;
                border-radius: 12px;
                background: #fff;
                border: 1px solid var(--of-line);
                color: #6b7280;
                cursor: pointer;
                flex: 0 0 auto;
            }

            .of-smart-body {
                padding: 16px;
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .of-smart-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .of-smart-metric {
                border: 1px solid #eef2f7;
                border-radius: 18px;
                padding: 14px;
                background: #fff;
            }

            .of-smart-metric-label,
            .of-smart-compare-label {
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .05em;
                color: #6b7280;
            }

            .of-smart-metric-value {
                margin-top: 8px;
                font-size: 20px;
                font-weight: 900;
                color: #111827;
                line-height: 1.2;
            }

            .of-upload-box {
                display: none;
                margin-bottom: 16px;
            }

            .of-upload-box.show {
                display: block;
            }

            .of-upload-toggle-btn.active {
                background: var(--of-primary-soft);
                border-color: #d9ef9d;
                color: #55720d;
            }

            .of-smart-metric-value.success,
            .of-smart-compare-price.success,
            .of-smart-savings-value {
                color: #74b2d4;
            }

            .of-smart-metric-value.muted {
                color: #6b7280;
            }

            .of-smart-list {
                border: 1px solid #eef2f7;
                border-radius: 18px;
                background: #fff;
                overflow: hidden;
            }

            .of-smart-list-head {
                padding: 12px 14px;
                border-bottom: 1px solid #eef2f7;
                background: #fafafa;
                font-size: 12px;
                font-weight: 900;
                color: #374151;
            }

            .of-smart-list-body {
                max-height: 260px;
                overflow: auto;
            }

            .of-smart-row {
                padding: 12px 14px;
                border-bottom: 1px solid #f1f5f9;
            }

            .of-smart-row:last-child {
                border-bottom: none;
            }

            .of-smart-row-title,
            .of-smart-compare-name {
                font-size: 13px;
                font-weight: 800;
                color: #111827;
                line-height: 1.45;
            }

            .of-smart-row-sub,
            .of-smart-compare-sub {
                margin-top: 5px;
                font-size: 12px;
                color: #6b7280;
                line-height: 1.6;
            }

            .of-smart-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
            }

            .of-smart-compare {
                display: grid;
                grid-template-columns: 1fr auto 1fr;
                gap: 10px;
                align-items: stretch;
            }

            .of-smart-compare-card {
                border: 1px solid #eef2f7;
                border-radius: 18px;
                background: #fff;
                padding: 14px;
            }

            .of-smart-compare-price {
                margin-top: 10px;
                font-size: 18px;
                font-weight: 900;
                color: #111827;
            }

            .of-smart-compare-arrow {
                display: flex;
                align-items: center;
                justify-content: center;
                color: #93c21c;
                font-weight: 900;
                font-size: 18px;
            }

            .of-smart-row-head {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 10px;
            }

            .of-smart-row-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 5px 8px;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 900;
                white-space: nowrap;
                border: 1px solid #d9ef9d;
                background: var(--of-primary-soft);
                color: #55720d;
            }

            .of-smart-row-badge.same {
                border-color: #e5e7eb;
                background: #f8fafc;
                color: #6b7280;
            }

            .of-smart-savings-bar {
                border: 1px solid #d9ef9d;
                background: linear-gradient(135deg, #f4fae7 0%, #ffffff 100%);
                border-radius: 18px;
                padding: 14px 16px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                flex-wrap: wrap;
            }

            .of-smart-savings-value {
                font-size: 18px;
                font-weight: 900;
            }

            /* ========= Modals ========= */
            .of-modal-backdrop {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, .48);
                z-index: 9999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 24px;
                backdrop-filter: blur(6px);
            }

            .of-modal {
                width: min(1400px, 100%);
                max-height: 90vh;
                overflow: hidden;
                background: #fff;
                border-radius: 24px;
                box-shadow: 0 25px 80px rgba(0, 0, 0, .22);
                display: flex;
                flex-direction: column;
            }

            .of-modal-head {
                padding: 18px 20px;
                border-bottom: 1px solid var(--of-line);
                background: #fafafa;
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 16px;
            }

            .of-modal-body {
                padding: 20px;
                overflow: auto;
            }

            .of-modal-tabs {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                margin-bottom: 16px;
                padding-bottom: 14px;
                border-bottom: 1px solid var(--of-line);
            }

            .of-modal-tab {
                border: 1px solid var(--of-line);
                background: #fff;
                color: #374151;
                border-radius: 12px;
                padding: 10px 14px;
                font-size: 13px;
                font-weight: 800;
                cursor: pointer;
                transition: var(--of-transition);
                display: inline-flex;
                align-items: center;
                gap: 8px;
                box-shadow: var(--of-shadow-sm);
            }

            .of-modal-tab:hover {
                background: #f9fafb;
                border-color: #d1d5db;
            }

            .of-modal-tab.active {
                background: var(--of-primary-soft);
                border-color: #d8ec9d;
                color: #55720d;
            }

            .of-modal-tab-panel {
                display: none;
            }

            .of-modal-tab-panel.active {
                display: block;
            }

            /* ========= Comparison ========= */
            .of-compare-layout {
                display: grid;
                grid-template-columns: minmax(0, 1.25fr) minmax(460px, .75fr);
                gap: 18px;
                align-items: start;
            }

            .of-compare-left,
            .of-compare-right {
                min-width: 0;
            }

            .of-compare-stats {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 14px;
                margin-bottom: 16px;
            }

            .of-compare-card,
            .of-compare-chart {
                border: 1px solid var(--of-line);
                border-radius: 18px;
                padding: 16px;
                background: #fff;
                box-shadow: var(--of-shadow-sm);
                margin-bottom: 18px;
            }

            .of-chart-box {
                position: relative;
                width: 100%;
                height: 320px;
                min-height: 320px;
                max-height: 320px;
            }

            .of-chart-box canvas {
                display: block !important;
                width: 100% !important;
                height: 100% !important;
            }

            .of-compare-side {
                border: 1px solid var(--of-line);
                border-radius: 20px;
                background: #fff;
                box-shadow: var(--of-shadow-sm);
                overflow: hidden;
                position: sticky;
                top: 0;
                max-height: calc(90vh - 40px);
                display: flex;
                flex-direction: column;
            }

            .of-compare-side-head {
                padding: 16px 18px;
                border-bottom: 1px solid var(--of-line);
                background: #fafafa;
                flex: 0 0 auto;
            }

            .of-compare-search {
                margin-top: 12px;
                position: relative;
            }

            .of-compare-search input {
                width: 100%;
                height: 44px;
                border: 1px solid var(--of-line);
                border-radius: 12px;
                padding: 0 14px;
                outline: none;
                font-size: 13px;
                font-weight: 700;
                background: #fff;
                color: #111827;
            }

            .of-compare-search input:focus {
                border-color: #cfe09b;
                box-shadow: 0 0 0 4px rgba(147, 194, 28, .12);
            }

            .of-compare-side-body {
                padding: 16px;
                overflow: auto;
                display: flex;
                flex-direction: column;
                gap: 14px;
                min-height: 0;
                flex: 1 1 auto;
            }

            .of-dist-card {
                border: 1px solid var(--of-line);
                border-radius: 18px;
                background: #fff;
                box-shadow: var(--of-shadow-sm);
                overflow: hidden;
                display: flex;
                flex-direction: column;
            }

            .of-dist-card.is-best {
                border-color: #b7df56;
                box-shadow: 0 0 0 3px rgba(147, 194, 28, .10), var(--of-shadow-sm);
            }

            .of-dist-card.is-worst {
                border-color: #fecaca;
                box-shadow: 0 0 0 3px rgba(239, 68, 68, .07), var(--of-shadow-sm);
            }

            .of-dist-card-head {
                padding: 14px 16px;
                border-bottom: 1px solid var(--of-line);
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
                background: #fcfcfd;
                flex: 0 0 auto;
            }

            .of-dist-title {
                font-size: 15px;
                font-weight: 900;
                color: #111827;
                line-height: 1.35;
                word-break: break-word;
            }

            .of-dist-sub {
                margin-top: 4px;
                font-size: 12px;
                color: #6b7280;
                line-height: 1.55;
            }

            .of-dist-rank {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 6px 10px;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 900;
                white-space: nowrap;
                flex: 0 0 auto;
            }

            .of-dist-rank.best {
                background: var(--of-primary-soft);
                color: #55720d;
                border: 1px solid #d9ef9d;
            }

            .of-dist-rank.worst {
                background: var(--of-danger-soft);
                color: #b91c1c;
                border: 1px solid #fecaca;
            }

            .of-dist-card-body {
                padding: 14px 16px 16px;
                display: flex;
                flex-direction: column;
                gap: 12px;
                min-height: 0;
            }

            .of-dist-metrics {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
                flex: 0 0 auto;
            }

            .of-dist-metric {
                border: 1px solid #eef2f7;
                border-radius: 14px;
                padding: 10px 12px;
                background: #fafafa;
                min-width: 0;
            }

            .of-dist-metric-label {
                font-size: 11px;
                font-weight: 900;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: .04em;
            }

            .of-dist-metric-value {
                margin-top: 6px;
                font-size: 15px;
                font-weight: 900;
                color: #111827;
                word-break: break-word;
            }

            .of-dist-items {
                display: flex;
                flex-direction: column;
                gap: 8px;
                max-height: 230px;
                overflow: auto;
                padding-right: 4px;
                min-height: 0;
            }

            .of-dist-item {
                border: 1px solid #eef2f7;
                border-radius: 14px;
                padding: 12px;
                background: #fff;
            }

            .of-dist-item-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 10px;
            }

            .of-dist-item-name {
                font-size: 13px;
                font-weight: 800;
                color: #111827;
                line-height: 1.5;
                word-break: break-word;
                flex: 1 1 auto;
                min-width: 0;
            }

            .of-dist-item-sub {
                margin-top: 8px;
                font-size: 12px;
                color: #6b7280;
                line-height: 1.7;
                word-break: break-word;
            }

            .of-dist-actions {
                margin-top: 4px;
                display: flex;
                justify-content: flex-end;
                flex: 0 0 auto;
                background: #fff;
                padding-top: 6px;
            }

            .of-compare-filters {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                margin-top: 12px;
            }

            .of-filter-chip {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                border-radius: 999px;
                border: 1px solid #d9ef9d;
                background: var(--of-primary-soft);
                color: #55720d;
                font-size: 12px;
                font-weight: 900;
            }

            .of-filter-chip input {
                width: 16px;
                height: 16px;
                accent-color: var(--of-primary);
                cursor: pointer;
            }

            /* ========= Material Detail ========= */
            .of-material-details-modal .of-modal {
                width: min(1050px, 96vw);
            }

            .of-material-detail-grid {
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
                gap: 18px;
            }

            .of-material-detail-card {
                border: 1px solid var(--of-line);
                border-radius: 18px;
                background: #fff;
                box-shadow: var(--of-shadow-sm);
                overflow: hidden;
            }

            .of-material-detail-head {
                padding: 14px 16px;
                border-bottom: 1px solid var(--of-line);
                background: #fafafa;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 10px;
            }

            .of-material-detail-title {
                font-size: 14px;
                font-weight: 900;
                color: #111827;
            }

            .of-material-detail-body {
                padding: 16px;
            }

            .of-material-kv {
                display: grid;
                grid-template-columns: 140px 1fr;
                gap: 10px 14px;
            }

            .of-material-kv-label {
                font-size: 12px;
                font-weight: 900;
                color: #6b7280;
            }

            .of-material-kv-value {
                font-size: 13px;
                font-weight: 800;
                color: #111827;
                word-break: break-word;
            }

            .of-material-savings {
                margin-top: 14px;
                border: 1px solid #d9ef9d;
                background: var(--of-primary-soft);
                color: #55720d;
                border-radius: 16px;
                padding: 14px 16px;
                font-weight: 900;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                flex-wrap: wrap;
            }

            .of-material-option-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
                max-height: 420px;
                overflow: auto;
            }

            .of-material-option {
                border: 1px solid var(--of-line);
                border-radius: 16px;
                background: #fff;
                padding: 14px;
                cursor: pointer;
                transition: var(--of-transition);
            }

            .of-material-option:hover {
                border-color: #cdd5df;
                background: #fcfcfd;
            }

            .of-material-option.active {
                border-color: #b7df56;
                box-shadow: 0 0 0 3px rgba(147, 194, 28, .10);
                background: #f9fdf0;
            }

            .of-material-option-top {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 10px;
            }

            .of-material-option-name {
                font-size: 14px;
                font-weight: 900;
                color: #111827;
                line-height: 1.4;
            }

            .of-material-option-price {
                font-size: 14px;
                font-weight: 900;
                color: #111827;
                white-space: nowrap;
            }

            .of-material-option-sub {
                margin-top: 8px;
                font-size: 12px;
                color: #6b7280;
                line-height: 1.65;
            }

            /* ========= Toast ========= */
            .of-toast-wrap {
                position: fixed;
                right: 22px;
                bottom: 22px;
                z-index: 10050;
                display: flex;
                flex-direction: column;
                gap: 10px;
            }

            .of-toast {
                min-width: 320px;
                max-width: 440px;
                background: #111827;
                color: #fff;
                border-radius: 16px;
                box-shadow: 0 20px 50px rgba(15, 23, 42, .28);
                padding: 14px 16px;
                display: flex;
                gap: 12px;
                align-items: flex-start;
                animation: ofToastIn .22s ease;
            }

            .of-toast.success {
                background: linear-gradient(135deg, #0f172a 0%, #14532d 100%);
            }

            .of-toast-icon {
                width: 34px;
                height: 34px;
                border-radius: 999px;
                background: rgba(255, 255, 255, .12);
                display: flex;
                align-items: center;
                justify-content: center;
                flex: 0 0 auto;
            }

            .of-toast-title {
                font-size: 13px;
                font-weight: 900;
                margin: 0;
            }

            .of-toast-text {
                margin-top: 4px;
                font-size: 12px;
                color: rgba(255, 255, 255, .88);
                line-height: 1.6;
            }

            @keyframes ofToastIn {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* ========= Print ========= */
            .of-print-sheet {
                background: #fff;
                overflow: hidden;
            }

            .of-print-head {
                background: #fff;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 7px;
                flex-wrap: wrap;
            }

            .of-print-head-clean {
                padding: 20px 20px 14px;
                border-bottom: 1px solid var(--of-line);
                background: #fff;
            }

            .of-print-head-main,
            .of-print-head-main-clean,
            .of-print-product-head,
            .of-print-customer-block {
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .of-print-title {
                font-size: 20px;
                font-weight: 900;
                color: #111827;
                margin: 0;
            }

            .of-print-sub {
                margin-top: 6px;
                color: #6b7280;
                font-size: 13px;
                line-height: 1.6;
            }

            .of-print-doc-lines {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .of-print-doc-line {
                font-size: 13px;
                color: #374151;
                line-height: 1.5;
            }

            .of-print-meta {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 12px;
                padding: 18px 20px;
                border-bottom: 1px solid var(--of-line);
                background: #fff;
            }

            .of-print-meta-single {
                display: block;
                padding: 18px 20px 20px;
            }

            .of-print-stat {
                border: 1px solid #eef2f7;
                border-radius: 14px;
                padding: 14px;
                background: #fafafa;
            }

            .of-print-stat-label {
                font-size: 11px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: #6b7280;
            }

            .of-print-stat-value {
                margin-top: 8px;
                font-size: 20px;
                font-weight: 900;
                color: #111827;
            }

            .of-print-body {
                padding: 20px;
            }

            .of-print-only {
                display: none;
            }

            .of-print-product-big,
            .of-print-product-label {
                font-size: 34px;
                line-height: 1.05;
                font-weight: 900;
                color: #111827;
                letter-spacing: -0.03em;
            }

            .of-print-product-name-top {
                font-size: 20px;
                line-height: 1.35;
                font-weight: 700;
                color: #374151;
            }

            .of-print-customer-name {
                font-size: 18px;
                font-weight: 800;
                color: #111827;
                line-height: 1.4;
            }

            .of-print-address,
            .of-print-date-line {
                font-size: 14px;
                color: #4b5563;
                line-height: 1.6;
            }

            /* ========= Responsive ========= */
            @media(max-width:1700px) {
                .of-kanban {
                    grid-template-columns: repeat(3, minmax(320px, 1fr));
                }
            }

            @media(max-width:1200px) {
                .of-stats {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .of-grid-2,
                .of-compare-layout {
                    grid-template-columns: 1fr;
                }

                .of-banner-main,
                .of-banner-slim-main {
                    flex-direction: column;
                    align-items: stretch;
                }

                .of-actions,
                .of-actions-slim {
                    width: 100%;
                }

                .of-banner-stats {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .of-banner-stats-slim {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .of-smart-side {
                    position: static;
                    width: 100%;
                    max-width: none;
                    margin-top: 16px;
                }
            }

            @media(max-width:1180px) {
                .of-kanban {
                    grid-template-columns: repeat(2, minmax(320px, 1fr));
                }
            }

            @media(max-width:1100px) {
                .of-status-overview {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .of-workflow-grid {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .of-step-meta {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .of-step {
                    min-height: 44px;
                    font-size: 11px;
                    padding: 0 18px 0 15px;
                }
            }

            @media(max-width:960px) {
                .of-material-detail-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media(max-width:900px) {
                .of-print-meta {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media(max-width:760px) {

                .of-kanban,
                .of-compare-stats,
                .of-workflow-grid {
                    grid-template-columns: 1fr;
                }

                .of-header-inner-slim {
                    padding: 14px;
                }

                .of-banner-slim-left {
                    gap: 12px;
                }

                .of-icon-box-slim {
                    width: 46px;
                    height: 46px;
                }

                .of-title-slim {
                    font-size: 20px;
                }

                .of-banner-stats-slim {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .of-doc-toggle {
                    min-width: 96px;
                    padding: 8px 12px;
                }
            }

            @media(max-width:640px) {
                .of-wrap {
                    padding: 12px;
                }

                .of-stats,
                .of-status-overview {
                    grid-template-columns: 1fr;
                }

                .of-title {
                    font-size: 24px;
                }

                .of-title-slim {
                    font-size: 16px;
                }

                .of-banner-stats,
                .of-banner-stats-slim {
                    grid-template-columns: 1fr 1fr;
                }

                .of-banner-stat-value {
                    font-size: 17px;
                }

                .of-meta-row {
                    gap: 8px;
                }

                .of-meta-pill {
                    padding: 7px 10px;
                    font-size: 11px;
                }

                .of-stepper {
                    flex-direction: column;
                    align-items: stretch;
                }

                .of-step,
                .of-step:first-child {
                    width: 100%;
                    clip-path: none;
                    border-radius: 14px;
                    min-height: 34px;
                    padding: 10px 12px;
                    justify-content: flex-start;
                    font-size: 10px;
                }

                .of-step-meta {
                    grid-template-columns: 1fr;
                }

                .of-dist-metrics {
                    grid-template-columns: 1fr;
                }
            }

            @media(max-width:560px) {
                .of-print-meta {
                    grid-template-columns: 1fr;
                }
            }

            @media(max-width:520px) {
                .of-banner-stats-slim {
                    grid-template-columns: 1fr 1fr;
                }

                .of-actions-slim .of-btn {
                    flex: 1 1 auto;
                }
            }

            @media print {
                @page {
                    size: auto;
                    margin: 12mm;
                }

                html,
                body {
                    background: #fff !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    height: auto !important;
                    overflow: visible !important;
                }

                body * {
                    visibility: hidden !important;
                }

                #panel-uebersicht,
                #panel-kanban,
                #panel-material,
                #panel-labor,
                #panel-print-files,
                #panel-agb,
                #panel-historie,
                .of-header,
                .of-shell-head,
                .of-tabs,
                .of-tab,
                .of-actions,
                .of-btn,
                .of-inline-actions,
                .of-no-print,
                .of-smart-side,
                .of-modal-backdrop,
                .of-toast-wrap,
                #material-comparison-modal,
                #material-detail-modal,
                #status-reason-modal,
                #material-move-modal,
                #material-final-modal,
                #document-status-modal,
                #clone-prompt-modal,
                #version-prompt-modal {
                    display: none !important;
                }

                #panel-material-print,
                #panel-material-print * {
                    visibility: visible !important;
                }

                #panel-material-print {
                    display: block !important;
                    position: absolute !important;
                    top: 0 !important;
                    left: 0 !important;
                    width: 100% !important;
                    min-height: auto !important;
                    height: auto !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    background: #fff !important;
                    z-index: 999999 !important;
                }

                #panel-material-print,
                #panel-material-print .of-print-sheet,
                #panel-material-print .of-print-head,
                #panel-material-print .of-print-head-clean,
                #panel-material-print .of-print-body,
                #panel-material-print .of-table-wrap,
                #panel-material-print .of-card {
                    display: block !important;
                    background: #fff !important;
                    box-shadow: none !important;
                    border: none !important;
                    border-radius: 0 !important;
                    overflow: visible !important;
                }

                .of-wrap {
                    max-width: none !important;
                    width: 100% !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }

                .of-shell,
                .of-shell-body {
                    background: #fff !important;
                    box-shadow: none !important;
                    border: none !important;
                    border-radius: 0 !important;
                    overflow: visible !important;
                    padding: 0 !important;
                    margin: 0 !important;
                }

                .of-print-head-clean {
                    padding: 0 0 12px 0 !important;
                    border-bottom: 1px solid #d1d5db !important;
                }

                .of-print-meta-single {
                    padding: 12px 0 16px 0 !important;
                    border-bottom: 1px solid #d1d5db !important;
                }

                .of-print-body {
                    padding: 16px 0 0 0 !important;
                }

                .of-table-wrap {
                    border: none !important;
                    overflow: visible !important;
                }

                .of-table {
                    min-width: 0 !important;
                    width: 100% !important;
                    border-collapse: collapse !important;
                }

                .of-table thead {
                    display: table-header-group !important;
                }

                .of-table tr {
                    page-break-inside: avoid !important;
                    break-inside: avoid !important;
                }

                .of-table th,
                .of-table td {
                    background: #fff !important;
                    color: #000 !important;
                    border: 1px solid #d1d5db !important;
                    padding: 8px 10px !important;
                    font-size: 12px !important;
                }

                .of-table-check input {
                    width: 14px !important;
                    height: 14px !important;
                }

                .of-mat-chip,
                .of-badge {
                    background: #fff !important;
                    border: 1px solid #d1d5db !important;
                    color: #111 !important;
                }

                .of-mat-desc {
                    display: block !important;
                    color: #444 !important;
                }

                .of-print-product-label,
                .of-print-product-big {
                    font-size: 28px !important;
                    line-height: 1.1 !important;
                    color: #111 !important;
                }

                .of-print-product-name-top {
                    font-size: 16px !important;
                    color: #374151 !important;
                }

                .of-print-customer-name {
                    font-size: 14px !important;
                    color: #111 !important;
                }

                .of-print-address,
                .of-print-date-line,
                .of-print-doc-line {
                    font-size: 12px !important;
                    color: #374151 !important;
                }
            }

            .of-workflow-stage {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .of-workflow-mainline,
            .of-workflow-side-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                align-items: center;
            }

            .of-workflow-divider {
                display: flex;
                align-items: center;
                gap: 12px;
                margin: 2px 0;
            }

            .of-workflow-divider-line {
                flex: 1;
                height: 1px;
                background: linear-gradient(90deg, transparent, #d1d5db, transparent);
            }

            .of-workflow-divider-label {
                font-size: 11px;
                font-weight: 900;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: #6b7280;
                white-space: nowrap;
            }

            .of-flow-step {
                position: relative;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-height: 42px;
                padding: 0 16px;
                border: none;
                border-radius: 14px;
                font-size: 11px;
                font-weight: 900;
                letter-spacing: .02em;
                cursor: pointer;
                transition: var(--of-transition);
                box-shadow: 0 6px 18px rgba(15, 23, 42, .08);
                white-space: nowrap;
                background: #e9f2cb;
                color: #4b5f1d;
            }

            .of-flow-step:hover {
                transform: translateY(-1px);
                filter: brightness(.98);
            }

            .of-flow-step[disabled] {
                cursor: default;
                opacity: 1;
            }

            .of-flow-step.is-past {
                background: #74b2d4;
                color: #fff;
            }

            .of-flow-step.is-current {
                background: #93c21c;
                color: #fff;
                outline: 3px solid rgba(147, 194, 28, .18);
                box-shadow: 0 10px 24px rgba(147, 194, 28, .28);
            }

            .of-flow-step.is-future {
                background: #dce9b6;
                color: #52671f;
            }

            .of-flow-step-label {
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .of-flow-step-dot {
                width: 9px;
                height: 9px;
                border-radius: 999px;
                background: currentColor;
                opacity: .9;
                flex: 0 0 auto;
            }

            .of-flow-step.is-current .of-flow-step-dot,
            .of-flow-step.is-past .of-flow-step-dot {
                background: #fff;
            }

            .of-side-status-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                min-height: 40px;
                padding: 0 14px;
                border-radius: 12px;
                border: 1px solid var(--of-line);
                background: #fff;
                color: #374151;
                font-size: 12px;
                font-weight: 900;
                cursor: pointer;
                transition: var(--of-transition);
                box-shadow: var(--of-shadow-sm);
                white-space: nowrap;
            }

            .of-side-status-btn:hover {
                transform: translateY(-1px);
                border-color: #cbd5e1;
                background: #f8fafc;
            }

            .of-side-status-btn[disabled] {
                cursor: default;
                opacity: 1;
            }

            .of-side-status-btn.is-current {
                color: #fff;
                border-color: transparent;
                box-shadow: 0 10px 24px rgba(15, 23, 42, .12);
            }

            .of-side-status-btn.is-current.color-final,
            .of-side-status-btn.is-current.color-won {
                background: #10b981;
            }

            .of-side-status-btn.is-current.color-cancel,
            .of-side-status-btn.is-current.color-lost {
                background: #ef4444;
            }

            .of-side-status-btn.is-current.color-pending,
            .of-side-status-btn.is-current.color-onhold {
                background: #f59e0b;
            }

            .of-side-status-btn.is-current.color-expired {
                background: #6b7280;
            }

            .of-side-status-btn.is-current.color-rejected {
                background: #ef4444;
            }

            .of-side-status-btn:not(.is-current).color-final,
            .of-side-status-btn:not(.is-current).color-won {
                background: #cfe09b75;
                color: #93c21c;
            }

            .of-side-status-btn:not(.is-current).color-cancel,
            .of-side-status-btn:not(.is-current).color-lost {
                background: #fef2f2;
                color: #b91c1c;
                border-color: #fecaca;
            }

            .of-side-status-btn:not(.is-current).color-pending,
            .of-side-status-btn:not(.is-current).color-onhold {
                background: #fffbeb;
                color: #b45309;
                border-color: #fde68a;
            }

            .of-side-status-btn:not(.is-current).color-expired {
                background: #f3f4f6;
                color: #374151;
                border-color: #d1d5db;
            }

            .of-side-status-btn:not(.is-current).color-rejected {
                background: #fef2f2;
                color: #b91c1c;
                border-color: #fecaca;
            }

            .of-labor-options {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .of-labor-summary {
                display: grid;
                grid-template-columns: repeat(8, minmax(0, 1fr));
                gap: 12px;
                margin-bottom: 16px;
            }

            @media(max-width: 1400px) {
                .of-labor-summary {
                    grid-template-columns: repeat(4, minmax(0, 1fr));
                }
            }

            @media(max-width: 900px) {
                .of-labor-summary {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .of-labor-metrics {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media(max-width: 560px) {

                .of-labor-summary,
                .of-labor-metrics {
                    grid-template-columns: 1fr;
                }
            }

            .of-labor-summary-card {
                border: 1px solid var(--of-line);
                border-radius: 16px;
                background: #fff;
                padding: 14px;
                box-shadow: var(--of-shadow-sm);
            }

            .of-labor-summary-label {
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .06em;
                color: var(--of-muted);
            }

            .of-labor-summary-value {
                margin-top: 7px;
                font-size: 17px;
                font-weight: 900;
                color: var(--of-text);
                line-height: 1.25;
            }

            .of-labor-option-card {
                border: 1px solid var(--of-line);
                border-radius: 18px;
                background: #fff;
                overflow: hidden;
                box-shadow: var(--of-shadow-sm);
            }

            .of-labor-option-card.is-original {
                border-color: #d9ef9d;
                box-shadow: 0 0 0 3px rgba(147, 194, 28, .10), var(--of-shadow-sm);
            }

            .of-labor-option-head {
                padding: 14px 16px;
                background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
                border-bottom: 1px solid var(--of-line);
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 12px;
                flex-wrap: wrap;
            }

            .of-labor-option-title {
                font-size: 15px;
                font-weight: 900;
                color: var(--of-text);
            }

            .of-labor-option-sub {
                margin-top: 5px;
                font-size: 12px;
                font-weight: 750;
                color: var(--of-muted);
                line-height: 1.5;
            }

            .of-labor-option-body {
                padding: 14px 16px 16px;
            }

            .of-labor-metrics {
                display: grid;
                grid-template-columns: repeat(5, minmax(0, 1fr));
                gap: 10px;
            }

            .of-labor-metric {
                border: 1px solid #eef2f7;
                border-radius: 14px;
                background: #fafafa;
                padding: 11px 12px;
            }

            .of-labor-metric-label {
                font-size: 10px;
                font-weight: 900;
                color: var(--of-muted);
                text-transform: uppercase;
                letter-spacing: .05em;
            }

            .of-labor-metric-value {
                margin-top: 6px;
                font-size: 14px;
                font-weight: 900;
                color: var(--of-text);
            }

            .of-labor-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 6px 10px;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 900;
                border: 1px solid var(--of-line);
                background: #f8fafc;
                color: #374151;
            }

            .of-labor-badge.original {
                background: var(--of-primary-soft);
                border-color: #d9ef9d;
                color: #55720d;
            }

            .of-labor-action-btn {
                border: 1px solid #bfdbfe;
                background: #eff6ff;
                color: #74b2d4;
                border-radius: 999px;
                padding: 6px 10px;
                font-size: 11px;
                font-weight: 900;
                cursor: pointer;
                white-space: nowrap;
            }

            .of-labor-action-btn:hover {
                background: #dbeafe;
            }

            @media(max-width: 900px) {
                .of-labor-summary {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }

                .of-labor-metrics {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media(max-width: 560px) {

                .of-labor-summary,
                .of-labor-metrics {
                    grid-template-columns: 1fr;
                }
            }


            @media(max-width:640px) {

                .of-workflow-mainline,
                .of-workflow-side-actions {
                    flex-direction: column;
                    align-items: stretch;
                }

                .of-flow-step,
                .of-side-status-btn {
                    width: 100%;
                    justify-content: flex-start;
                }
            }


            /* ========= Lightbox Fix ========= */
            #lightbox-modal {
                z-index: 10500 !important;
                background: rgba(15, 23, 42, .82);
                padding: 24px;
            }

            #lightbox-modal .of-lightbox-shell {
                width: min(1100px, 96vw);
                height: min(86vh, 900px);
                max-height: 86vh;
                position: relative;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                overflow: visible !important;
                background: transparent;
                box-shadow: none;
                border-radius: 0;
            }

            #lightbox-content {
                width: 100%;
                height: 100%;
                min-height: 300px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #fff;
                border-radius: 18px;
                overflow: hidden;
                box-shadow: 0 25px 80px rgba(0, 0, 0, .35);
                position: relative;
                z-index: 1;
            }

            #lightbox-content img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
                display: block;
            }

            #lightbox-content iframe {
                width: 100%;
                height: 100%;
                border: 0;
                background: #fff;
            }

            .of-lightbox-btn {
                position: absolute;
                z-index: 10520;
                border: 1px solid rgba(229, 231, 235, .95);
                background: #fff;
                color: #111827;
                border-radius: 999px;
                cursor: pointer;
                font-weight: 900;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 12px 30px rgba(15, 23, 42, .28);
                transition: all .18s ease;
            }

            .of-lightbox-btn:hover {
                background: #f9fafb;
                transform: scale(1.04);
            }

            .of-lightbox-close {
                top: 12px;
                right: 12px;
                width: 42px;
                height: 42px;
                font-size: 20px;
            }

            .of-lightbox-prev,
            .of-lightbox-next {
                top: 50%;
                width: 46px;
                height: 46px;
                font-size: 24px;
            }

            .of-lightbox-prev {
                left: 12px;
                transform: translateY(-50%);
            }

            .of-lightbox-next {
                right: 12px;
                transform: translateY(-50%);
            }

            .of-lightbox-prev:hover,
            .of-lightbox-next:hover {
                transform: translateY(-50%) scale(1.04);
            }

            #lightbox-caption {
                margin-top: 12px;
                color: #fff;
                text-align: center;
                font-weight: 800;
                font-size: 13px;
                line-height: 1.5;
                max-width: 100%;
                word-break: break-word;
            }

            @media(max-width:640px) {
                #lightbox-modal {
                    padding: 12px;
                }

                #lightbox-modal .of-lightbox-shell {
                    width: 100%;
                    height: 84vh;
                    max-height: 84vh;
                }

                .of-lightbox-close {
                    top: 8px;
                    right: 8px;
                    width: 38px;
                    height: 38px;
                }

                .of-lightbox-prev,
                .of-lightbox-next {
                    width: 40px;
                    height: 40px;
                    font-size: 20px;
                }

                .of-lightbox-prev {
                    left: 8px;
                }

                .of-lightbox-next {
                    right: 8px;
                }
            }

            .of-file-analytics {
                display: grid;
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 12px;
            }

            .of-file-analytics-card {
                border: 1px solid var(--of-line);
                border-radius: 16px;
                background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
                box-shadow: var(--of-shadow-sm);
                padding: 14px 16px;
                min-width: 0;
            }

            .of-file-analytics-label {
                font-size: 11px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .06em;
                color: var(--of-muted);
            }

            .of-file-analytics-value {
                margin-top: 7px;
                font-size: 22px;
                line-height: 1.1;
                font-weight: 900;
                color: #111827;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .of-file-analytics-sub {
                margin-top: 6px;
                font-size: 12px;
                font-weight: 700;
                color: #6b7280;
            }



            /* ========= Team & Permission Concept ========= */
            .of-access-card {
                margin-top: 10px;
                border: 1px solid #d9ef9d;
                background: linear-gradient(135deg, #ffffff 0%, #f8fbef 100%);
                border-radius: 16px;
                box-shadow: var(--of-shadow-sm);
                overflow: hidden;
            }

            .of-access-head {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 12px;
                padding: 10px 12px;
                border-bottom: 1px solid rgba(217, 239, 157, .85);
                background: rgba(244, 250, 231, .65);
            }

            .of-access-title {
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 12px;
                font-weight: 900;
                color: #405313;
            }

            .of-access-sub {
                margin-top: 3px;
                font-size: 11px;
                line-height: 1.45;
                color: #6b7280;
                font-weight: 700;
            }

            .of-access-state {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                white-space: nowrap;
                padding: 6px 9px;
                border-radius: 999px;
                font-size: 10px;
                font-weight: 900;
                border: 1px solid var(--of-line);
                background: #fff;
                color: #374151;
            }

            .of-access-state.allowed {
                background: #ecfdf5;
                border-color: #bbf7d0;
                color: #047857;
            }

            .of-access-state.blocked {
                background: #fef2f2;
                border-color: #fecaca;
                color: #b91c1c;
            }

            .of-access-body {
                padding: 12px;
                display: grid;
                grid-template-columns: minmax(0, 1.2fr) minmax(260px, .8fr);
                gap: 12px;
                align-items: start;
            }

            .of-access-team {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                min-width: 0;
            }

            .of-access-person {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 10px 6px 6px;
                border: 1px solid var(--of-line);
                border-radius: 999px;
                background: #fff;
                max-width: 220px;
                box-shadow: var(--of-shadow-sm);
            }

            .of-access-avatar {
                width: 28px;
                height: 28px;
                border-radius: 999px;
                object-fit: cover;
                background: #e5e7eb;
                border: 2px solid #fff;
                flex: 0 0 auto;
            }

            .of-access-person-meta {
                min-width: 0;
                display: flex;
                flex-direction: column;
                line-height: 1.15;
            }

            .of-access-person-name {
                font-size: 12px;
                font-weight: 900;
                color: #111827;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .of-access-person-role {
                margin-top: 2px;
                font-size: 10px;
                font-weight: 800;
                color: #6b7280;
            }

            .of-access-empty {
                padding: 10px 12px;
                border: 1px dashed var(--of-line);
                border-radius: 14px;
                background: #fff;
                color: #6b7280;
                font-size: 12px;
                font-weight: 800;
            }

            .of-access-mode-box {
                border: 1px solid var(--of-line);
                background: #fff;
                border-radius: 14px;
                padding: 10px;
            }

            .of-access-mode-label {
                font-size: 10px;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .06em;
                color: #6b7280;
                margin-bottom: 8px;
            }

            .of-access-options {
                display: grid;
                grid-template-columns: 1fr;
                gap: 7px;
            }

            .of-access-option {
                display: flex;
                align-items: flex-start;
                gap: 8px;
                padding: 9px 10px;
                border: 1px solid var(--of-line);
                border-radius: 12px;
                background: #fafafa;
                cursor: pointer;
                transition: var(--of-transition);
            }

            .of-access-option:hover {
                border-color: #d9ef9d;
                background: #f8fbef;
            }

            .of-access-option input {
                margin-top: 2px;
                accent-color: var(--of-primary);
            }

            .of-access-option.is-active {
                border-color: #b7df56;
                background: var(--of-primary-soft);
                box-shadow: 0 0 0 3px rgba(147, 194, 28, .10);
            }

            .of-access-option-title {
                font-size: 12px;
                font-weight: 900;
                color: #111827;
            }

            .of-access-option-text {
                margin-top: 2px;
                font-size: 11px;
                line-height: 1.45;
                color: #6b7280;
                font-weight: 700;
            }

            .of-access-actions {
                margin-top: 9px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                flex-wrap: wrap;
            }

            .of-access-save {
                border: none;
                background: var(--of-primary);
                color: #fff;
                border-radius: 10px;
                padding: 7px 10px;
                font-size: 11px;
                font-weight: 900;
                cursor: pointer;
            }

            .of-access-save:hover {
                background: var(--of-primary-hover);
            }

            .of-access-save[disabled] {
                opacity: .55;
                cursor: not-allowed;
            }

            .of-access-help {
                font-size: 10px;
                font-weight: 800;
                color: #6b7280;
            }

            @media(max-width:900px) {
                .of-access-body {
                    grid-template-columns: 1fr;
                }

                .of-access-head {
                    flex-direction: column;
                }

                .of-access-state {
                    white-space: normal;
                }
            }

            @media(max-width:1000px) {
                .of-file-analytics {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media(max-width:560px) {
                .of-file-analytics {
                    grid-template-columns: 1fr;
                }
            }


            /* ========= User Friendly Team Modal + Side Navigation ========= */
            .of-header-tools-row {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                margin-top: 8px;
            }

            .of-team-inline {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                padding-left: 10px;
                border-left: 1px solid var(--of-line);
            }

            .of-team-inline-label {
                font-size: 11px;
                font-weight: 900;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: .05em;
            }

            .of-team-inline-list {
                display: flex;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
            }

            .of-team-inline-person {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 8px 4px 4px;
                border: 1px solid var(--of-line);
                border-radius: 999px;
                background: #fff;
                box-shadow: var(--of-shadow-sm);
                font-size: 11px;
                font-weight: 900;
                color: #111827;
            }

            .of-team-inline-avatar {
                width: 22px;
                height: 22px;
                border-radius: 999px;
                object-fit: cover;
                background: #e5e7eb;
                border: 1px solid #fff;
            }

            .of-team-inline-more {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 24px;
                height: 24px;
                padding: 0 7px;
                border-radius: 999px;
                background: #f3f4f6;
                border: 1px solid var(--of-line);
                font-size: 10px;
                font-weight: 900;
                color: #4b5563;
            }

            .of-access-pill {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                padding: 7px 10px;
                border-radius: 999px;
                border: 1px solid var(--of-line);
                background: #fff;
                color: #374151;
                font-size: 11px;
                font-weight: 900;
                box-shadow: var(--of-shadow-sm);
            }

            .of-access-pill.allowed {
                background: #ecfdf5;
                border-color: #bbf7d0;
                color: #047857;
            }

            .of-access-pill.blocked {
                background: #fef2f2;
                border-color: #fecaca;
                color: #b91c1c;
            }

            .of-team-permission-btn {
                background: #fff;
                color: #111827;
                border: 1px solid var(--of-line);
            }

            .of-team-permission-btn:hover {
                background: var(--of-primary-soft);
                border-color: #d9ef9d;
                color: #55720d;
            }

            .of-access-modal-panel {
                width: min(760px, 100%);
            }

            .of-access-modal-grid {
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(280px, .9fr);
                gap: 16px;
            }

            .of-access-modal-card {
                border: 1px solid var(--of-line);
                border-radius: 18px;
                background: #fff;
                box-shadow: var(--of-shadow-sm);
                padding: 16px;
            }

            .of-access-modal-card.soft {
                background: #f8fafc;
            }

            .of-access-modal-title {
                font-size: 14px;
                font-weight: 900;
                color: #111827;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .of-access-modal-text {
                font-size: 12px;
                line-height: 1.65;
                color: #6b7280;
                margin-bottom: 12px;
            }

            .of-workspace-layout {
                display: grid;
                grid-template-columns: 250px minmax(0, 1fr);
                align-items: stretch;
                min-height: 640px;
            }

            .of-workspace-layout>.of-shell-head {
                align-items: stretch;
                justify-content: flex-start;
                border-right: 1px solid var(--of-line);
                border-bottom: 0;
                padding: 14px;
                background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            }

            .of-workspace-layout>.of-shell-head .of-tabs {
                width: 100%;
                display: flex;
                flex-direction: column;
                align-items: stretch;
                gap: 8px;
            }

            .of-workspace-layout>.of-shell-head .of-tab {
                width: 100%;
                justify-content: flex-start;
                padding: 11px 12px;
                min-height: 44px;
                border-radius: 14px;
                box-shadow: none;
            }

            .of-workspace-layout>.of-shell-head .of-tab.active {
                background: #fff;
                border-color: #d9ef9d;
                box-shadow: 0 8px 24px rgba(147, 194, 28, .12);
            }

            .of-workspace-layout>.of-shell-body {
                min-width: 0;
                padding: 18px;
            }

            /* ========= Fixed collapsible workspace sidebar ========= */
            .of-workspace-layout {
                transition: grid-template-columns .22s ease;
            }

            .of-workspace-layout.sidebar-collapsed {
                grid-template-columns: 76px minmax(0, 1fr);
            }

            .of-workspace-layout.sidebar-collapsed>.of-shell-head {
                padding: 12px 10px;
            }

            .of-workspace-layout.sidebar-collapsed .of-sidebar-title,
            .of-workspace-layout.sidebar-collapsed .of-side-link-text,
            .of-workspace-layout.sidebar-collapsed .of-tab-label {
                display: none !important;
            }

            .of-workspace-layout.sidebar-collapsed .of-tab {
                justify-content: center !important;
                padding: 11px 8px !important;
            }

            .of-workspace-layout.sidebar-collapsed .of-tab-icon {
                width: 22px;
                height: 22px;
            }

            .of-workspace-layout.sidebar-collapsed .of-tab-icon svg {
                width: 20px;
                height: 20px;
            }

            .of-workspace-sidebar-inner {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 12px;
                min-height: 100%;
            }

            .of-sidebar-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                padding: 3px 2px 10px;
                border-bottom: 1px solid var(--of-line);
            }

            .of-sidebar-title {
                font-size: 12px;
                font-weight: 1000;
                letter-spacing: .06em;
                text-transform: uppercase;
                color: #6b7280;
                white-space: nowrap;
            }

            .of-sidebar-icon-row {
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .of-sidebar-icon-btn,
            a.of-sidebar-icon-btn {
                width: 34px;
                height: 34px;
                min-width: 34px;
                border-radius: 11px;
                border: 1px solid var(--of-line);
                background: #fff;
                color: #374151;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                cursor: pointer;
                transition: var(--of-transition);
            }

            .of-sidebar-icon-btn:hover,
            a.of-sidebar-icon-btn:hover {
                background: var(--of-primary-soft);
                border-color: #d9ef9d;
                color: #55720d;
            }

            .of-sidebar-quick-actions {
                display: flex;
                flex-direction: column;
                gap: 7px;
                padding-bottom: 10px;
                border-bottom: 1px solid var(--of-line);
            }

            a.of-side-action-link {
                min-height: 38px;
                border: 1px solid var(--of-line);
                background: #fff;
                border-radius: 13px;
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 8px 10px;
                color: #111827;
                text-decoration: none;
                font-size: 12px;
                font-weight: 900;
                transition: var(--of-transition);
            }

            a.of-side-action-link:hover {
                background: var(--of-primary-soft);
                border-color: #d9ef9d;
                color: #55720d;
            }

            .of-workspace-layout.sidebar-collapsed a.of-side-action-link {
                justify-content: center;
                padding: 8px;
            }

            .of-workspace-layout.sidebar-collapsed .of-sidebar-quick-actions {
                align-items: stretch;
            }


            @media(max-width:980px) {
                .of-access-modal-grid {
                    grid-template-columns: 1fr;
                }

                .of-workspace-layout {
                    grid-template-columns: 1fr;
                }

                .of-workspace-layout>.of-shell-head {
                    border-right: 0;
                    border-bottom: 1px solid var(--of-line);
                }

                .of-workspace-layout>.of-shell-head .of-tabs {
                    flex-direction: row;
                    overflow-x: auto;
                    align-items: center;
                }

                .of-workspace-layout>.of-shell-head .of-tab {
                    width: auto;
                    flex: 0 0 auto;
                }

                .of-workspace-layout.sidebar-collapsed {
                    grid-template-columns: 1fr;
                }

                .of-workspace-layout.sidebar-collapsed .of-tab-label,
                .of-workspace-layout.sidebar-collapsed .of-sidebar-title,
                .of-workspace-layout.sidebar-collapsed .of-side-link-text {
                    display: inline-flex !important;
                }

                .of-workspace-layout.sidebar-collapsed .of-tab {
                    justify-content: flex-start !important;
                    padding: 11px 12px !important;
                }

                .of-team-inline {
                    border-left: 0;
                    padding-left: 0;
                }
            }



            /* ========= Screenshot-style Offer/Auftrag Kanban ========= */
            .of-kanban-screen {
                background: #edf2f6;
                border: 1px solid #d9e2ea;
                border-radius: 14px;
                overflow: hidden;
                box-shadow: var(--of-shadow-sm);
            }

            .of-kanban-screen.is-fullscreen {
                position: fixed;
                inset: 0;
                z-index: 20000;
                border-radius: 0;
                border: 0;
                box-shadow: none;
                background: #edf2f6;
                display: flex;
                flex-direction: column;
            }

            .of-kanban-screen.is-fullscreen .of-kanban-screen-top {
                height: 54px;
                flex: 0 0 auto;
            }

            .of-kanban-screen.is-fullscreen .of-kanban-scroll {
                height: calc(100vh - 54px);
                min-height: 0;
                flex: 1 1 auto;
            }

            .of-kanban-screen.is-fullscreen .of-kanban-board,
            .of-kanban-screen.is-fullscreen .of-kanban-col-screen {
                min-height: calc(100vh - 54px);
            }

            body.of-kanban-fullscreen-open {
                overflow: hidden;
            }

            .of-kanban-screen-top {
                height: 44px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                padding: 6px 10px;
                background: #f3f8fc;
                border-bottom: 1px solid #d7e1ea;
            }

            .of-kanban-screen-title {
                font-size: 13px;
                font-weight: 900;
                color: #111827;
                line-height: 1.15;
            }

            .of-kanban-screen-sub {
                font-size: 10px;
                color: #64748b;
                font-weight: 800;
                margin-top: 2px;
            }

            .of-kanban-screen-actions {
                display: flex;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
                justify-content: flex-end;
            }

            .of-kanban-zoom-btn,
            .of-kanban-view-btn {
                height: 26px;
                min-width: 34px;
                border: 1px solid #d4dde6;
                background: #fff;
                color: #334155;
                border-radius: 8px;
                font-size: 11px;
                font-weight: 900;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 4px;
                padding: 0 8px;
            }

            .of-kanban-zoom-btn.active,
            .of-kanban-view-btn.active {
                background: #74b2d4;
                color: #fff;
                border-color: #74b2d4;
            }

            .of-kanban-toggle {
                height: 26px;
                display: inline-flex;
                align-items: center;
                gap: 5px;
                padding: 0 8px;
                border: 1px solid #d4dde6;
                border-radius: 8px;
                background: #fff;
                color: #334155;
                font-size: 11px;
                font-weight: 900;
                cursor: pointer;
                user-select: none;
            }

            .of-kanban-toggle input {
                width: 12px;
                height: 12px;
                accent-color: #93c21c;
            }

            .of-kanban-scroll {
                overflow: auto;
                width: 100%;
                min-height: 460px;
            }

            .of-kanban-board {
                --kanban-col-width: 270px;
                display: grid;
                grid-auto-flow: column;
                grid-auto-columns: var(--kanban-col-width);
                align-items: stretch;
                min-height: 460px;
                width: max-content;
                background: #f2f4f6;
            }

            .of-kanban-board.zoom-100 {
                --kanban-col-width: 270px;
            }

            .of-kanban-board.zoom-90 {
                --kanban-col-width: 244px;
            }

            .of-kanban-board.zoom-80 {
                --kanban-col-width: 218px;
            }

            .of-kanban-board.zoom-70 {
                --kanban-col-width: 194px;
            }

            .of-kanban-col-screen {
                min-height: 460px;
                border-right: 1px dashed #cbd5df;
                background: #f5f6f7;
                display: flex;
                flex-direction: column;
            }

            .of-kanban-col-head {
                background: #8fc21a;
                color: #fff;
                min-height: 32px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 8px;
                padding: 4px 7px;
                border-right: 1px solid rgba(255, 255, 255, .35);
                cursor: pointer;
                user-select: none;
            }

            .of-kanban-col-title {
                display: flex;
                align-items: center;
                gap: 7px;
                min-width: 0;
                font-size: 15px;
                font-weight: 1000;
                letter-spacing: .08em;
                text-transform: uppercase;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .of-kanban-col-title svg {
                width: 15px;
                height: 15px;
                flex: 0 0 auto;
            }

            .of-kanban-col-count {
                font-size: 11px;
                font-weight: 1000;
                min-width: 18px;
                text-align: right;
            }

            .of-kanban-col-filter {
                display: flex;
                align-items: center;
                gap: 5px;
                padding: 5px 7px;
                background: #f7fafc;
                border-bottom: 1px solid #dce4ec;
            }

            .of-kanban-col-filter input {
                width: 100%;
                height: 24px;
                border: 1px solid #cfd8e3;
                background: #fff;
                border-radius: 3px;
                padding: 0 7px;
                outline: none;
                font-size: 10px;
                color: #334155;
            }

            .of-kanban-col-filter button {
                width: 24px;
                height: 24px;
                border: 1px solid #cfd8e3;
                background: #fff;
                color: #64748b;
                border-radius: 3px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }

            .of-kanban-col-body {
                padding: 18px 9px;
                flex: 1;
            }

            .of-kanban-ticket {
                background: #fff;
                border: 1px solid #d9e0e7;
                border-radius: 5px;
                box-shadow: 0 4px 10px rgba(15, 23, 42, .12);
                overflow: hidden;
                position: relative;
                max-width: 100%;
                cursor: grab;
                user-select: none;
                transition: transform .16s ease, box-shadow .16s ease, opacity .16s ease;
            }

            .of-kanban-ticket:active {
                cursor: grabbing;
            }

            .of-kanban-ticket.is-dragging {
                opacity: .58;
                transform: rotate(1deg) scale(.985);
                box-shadow: 0 14px 26px rgba(15, 23, 42, .22);
            }

            .of-kanban-col-screen.is-drag-over .of-kanban-col-body {
                background: rgba(147, 194, 28, .10);
                outline: 2px dashed rgba(147, 194, 28, .55);
                outline-offset: -7px;
            }

            .of-kanban-col-screen.is-drag-over .of-kanban-empty-lane::after {
                content: "Hier ablegen";
                display: flex;
                min-height: 120px;
                align-items: center;
                justify-content: center;
                border: 1px dashed rgba(147, 194, 28, .65);
                border-radius: 8px;
                background: rgba(255, 255, 255, .7);
                color: #55720d;
                font-size: 12px;
                font-weight: 900;
            }

            .of-kanban-ticket::before {
                content: "";
                position: absolute;
                top: -1px;
                left: -1px;
                right: -1px;
                height: 3px;
                background: #8fc21a;
            }

            .of-kanban-ticket-menu {
                display: none;
            }

            .of-kanban-ticket-head {
                padding: 16px 12px 10px 14px;
                min-height: 72px;
            }

            .of-kanban-ticket-name {
                font-size: 16px;
                font-weight: 1000;
                letter-spacing: .04em;
                color: #1f2937;
                text-transform: uppercase;
                line-height: 1.25;
                word-break: break-word;
            }

            .of-kanban-ticket-meta {
                margin-top: 7px;
                display: flex;
                flex-direction: column;
                gap: 3px;
                font-size: 11px;
                color: #475569;
                line-height: 1.35;
            }

            .of-kanban-ticket-product {
                color: #7baa18;
                font-weight: 900;
            }

            .of-kanban-ticket-team {
                margin: 6px 10px 8px;
                display: flex;
                align-items: center;
                gap: 6px;
                flex-wrap: wrap;
            }

            .of-kanban-ticket-avatar {
                width: 24px;
                height: 24px;
                border-radius: 999px;
                border: 1px solid #fff;
                object-fit: cover;
                background: #e5e7eb;
                box-shadow: 0 1px 2px rgba(15, 23, 42, .12);
            }

            .of-kanban-ticket-team-pill {
                display: inline-flex;
                align-items: center;
                gap: 5px;
                height: 24px;
                padding: 0 8px;
                border: 1px solid #d8e2ec;
                border-radius: 999px;
                color: #334155;
                background: #f8fafc;
                font-size: 10px;
                font-weight: 900;
            }

            .of-kanban-ticket-date {
                margin: 0 10px 8px;
                border: 1px solid #d8e5f0;
                background: #eff7fd;
                color: #1f2937;
                border-radius: 7px;
                padding: 7px 9px;
                font-size: 11px;
                line-height: 1.45;
            }

            .of-kanban-ticket-date strong {
                font-weight: 1000;
            }

            .of-kanban-ticket-stats {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 6px;
                border-top: 1px solid #eef2f7;
                padding: 8px 10px 10px;
                background: #fbfcfd;
            }

            .of-kanban-ticket-stat {
                min-width: 0;
                border: 1px solid #e5edf5;
                background: #fff;
                border-radius: 7px;
                padding: 6px 7px;
                line-height: 1.2;
            }

            .of-kanban-ticket-stat-label {
                display: block;
                color: #64748b;
                font-size: 9px;
                font-weight: 1000;
                letter-spacing: .035em;
                text-transform: uppercase;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .of-kanban-ticket-stat-value {
                display: block;
                margin-top: 4px;
                color: #0f172a;
                font-size: 12px;
                font-weight: 1000;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .of-kanban-ticket-actions {
                display: flex;
                align-items: center;
                justify-content: space-between;
                border-top: 1px solid #eef2f7;
                padding: 8px 10px;
            }

            .of-kanban-ticket-actions button {
                width: 24px;
                height: 24px;
                border: 0;
                background: transparent;
                color: #8ba0b3;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0;
            }

            .of-kanban-ticket-actions button:hover {
                color: #55720d;
            }

            .of-kanban-empty-lane {
                height: 100%;
                min-height: 310px;
            }

            @media(max-width:980px) {
                .of-kanban-screen-top {
                    height: auto;
                    align-items: flex-start;
                    flex-direction: column;
                }

                .of-kanban-screen-actions {
                    justify-content: flex-start;
                }

                .of-kanban-board {
                    --kanban-col-width: 245px;
                }
            }


            /* ========= Customizable Kanban Manager ========= */
            .of-kanban-manager-btn {
                border: 1px solid #d9ef9d;
                background: #fff;
                color: #55720d;
                border-radius: 999px;
                padding: 6px 10px;
                font-size: 11px;
                font-weight: 900;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 6px;
            }

            .of-kanban-manager-btn:hover {
                background: var(--of-primary-soft);
            }

            .of-kanban-drop-indicator {
                height: 4px;
                background: #74b2d4;
                border-radius: 999px;
                margin: 8px 0;
                box-shadow: 0 0 0 4px rgba(116, 178, 212, .16);
                display: none;
            }

            .of-kanban-col-screen.is-drag-over .of-kanban-drop-indicator {
                display: block;
            }

            .of-kanban-position-preview {
                display: none;
                margin: 8px 0 0;
                padding: 7px 9px;
                border: 1px dashed #74b2d4;
                background: #eff6ff;
                color: #1e6091;
                border-radius: 10px;
                font-size: 11px;
                font-weight: 900;
            }

            .of-kanban-col-screen.is-drag-over .of-kanban-position-preview {
                display: block;
            }

            .of-kanban-col-screen.is-drag-over .of-kanban-col-body {
                background: #f8fbff;
                outline: 2px dashed rgba(116, 178, 212, .45);
                outline-offset: -5px;
            }

            .of-kanban-ticket.is-dragging {
                opacity: .55;
                transform: rotate(1deg) scale(.99);
            }

            .of-kanban-config-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
                margin-top: 12px;
            }

            .of-kanban-config-row {
                display: grid;
                grid-template-columns: 34px 1fr 112px 96px 90px;
                gap: 10px;
                align-items: center;
                padding: 10px;
                border: 1px solid var(--of-line);
                border-radius: 14px;
                background: #fff;
            }

            .of-kanban-config-row.is-disabled {
                opacity: .55;
                background: #f8fafc;
            }

            .of-kanban-drag-handle {
                width: 34px;
                height: 34px;
                border-radius: 10px;
                border: 1px solid var(--of-line);
                display: flex;
                align-items: center;
                justify-content: center;
                background: #f8fafc;
                cursor: grab;
                color: #6b7280;
                font-weight: 900;
            }

            .of-kanban-config-input {
                height: 36px;
                border: 1px solid var(--of-line);
                border-radius: 10px;
                padding: 0 10px;
                font-size: 12px;
                font-weight: 800;
                width: 100%;
                outline: none;
            }

            .of-kanban-config-input:focus {
                border-color: var(--of-primary);
                box-shadow: 0 0 0 3px var(--of-primary-soft);
            }

            .of-kanban-config-actions {
                display: flex;
                gap: 6px;
                justify-content: flex-end;
            }

            .of-kanban-mini-btn {
                height: 34px;
                border-radius: 10px;
                border: 1px solid var(--of-line);
                background: #fff;
                color: #111827;
                padding: 0 9px;
                font-size: 11px;
                font-weight: 900;
                cursor: pointer;
            }

            .of-kanban-mini-btn:hover {
                background: #f8fafc;
            }

            .of-kanban-mini-btn.danger {
                color: #b91c1c;
                border-color: #fecaca;
                background: #fef2f2;
            }

            .of-kanban-config-add {
                display: grid;
                grid-template-columns: 1fr 120px 90px auto;
                gap: 10px;
                align-items: end;
                margin-bottom: 14px;
                padding: 12px;
                border: 1px dashed #d9ef9d;
                border-radius: 16px;
                background: #fbfef3;
            }

            .of-kanban-config-label {
                font-size: 11px;
                font-weight: 900;
                color: #6b7280;
                text-transform: uppercase;
                letter-spacing: .05em;
                margin-bottom: 5px;
            }

            @media(max-width:900px) {
                .of-kanban-config-row {
                    grid-template-columns: 34px 1fr;
                }

                .of-kanban-config-actions {
                    justify-content: flex-start
                }

                .of-kanban-config-add {
                    grid-template-columns: 1fr
                }
            }
        </style>
    @endpush
@endonce

@section('content')
    <div class="of-wrap" id="folder-app" data-folder-id="{{ $folder->id }}"
        data-data-url="{{ route('admin.offers.folders.data', $folder) }}"
        data-document-status-url="{{ route('admin.offers.folders.document-status', $folder) }}"
        data-offer-id="{{ $offer?->id }}"
        data-offer-destroy-url="{{ $offer ? route('admin.offers.destroy', $offer->id) : '' }}"
        data-team-save-url="{{ $offer?->id ? url('/admin/offers/' . $offer->id . '/team') : '' }}"
        data-material-comparison-url="{{ route('admin.offers.folders.material-comparison', $folder) }}"
        data-material-status-url="{{ route('admin.offers.folders.material-order-status', $folder) }}"
        data-material-change-url="{{ route('admin.offers.folders.material-change', $folder) }}"
        data-kanban-move-url="{{ route('admin.offers.folders.kanban.move', $folder) }}"
        data-kanban-offer-stage-id="{{ $offerWorkflowMainStage?->id }}"
        data-kanban-deal-stage-id="{{ $dealWorkflowMainStage?->id }}"
        data-kanban-available-stages='@json($availableWorkflowMainStages)'
        data-kanban-offer-stages='@json($initialOfferWorkflowSubStages)'
        data-kanban-deal-stages='@json($initialDealWorkflowSubStages)'
        data-kanban-substage-index-url-template="{{ route('admin.kanban.stages.sub-stages.index', ['stage' => '__STAGE__']) }}"
        data-kanban-substage-store-url-template="{{ route('admin.kanban.stages.sub-stages.store', ['stage' => '__STAGE__']) }}"
        data-kanban-substage-update-url-template="{{ route('admin.kanban.sub-stages.update', ['subStage' => '__SUBSTAGE__']) }}"
        data-kanban-substage-delete-url-template="{{ route('admin.kanban.sub-stages.destroy', ['subStage' => '__SUBSTAGE__']) }}"
        data-kanban-substage-toggle-url-template="{{ route('admin.kanban.sub-stages.toggle', ['subStage' => '__SUBSTAGE__']) }}"
        data-kanban-substage-default-url-template="{{ route('admin.kanban.sub-stages.default', ['subStage' => '__SUBSTAGE__']) }}"
        data-kanban-substage-reorder-url-template="{{ route('admin.kanban.stages.sub-stages.reorder', ['stage' => '__STAGE__']) }}"
        data-agb-save-url="{{ route('admin.offers.folders.agb.save', $folder) }}"
        data-labor-qualification-options-url="{{ route('admin.offers.folders.labor-qualification-options', $folder) }}"
        data-attachments-upload-url="{{ route('admin.offers.folders.attachments.upload', $folder) }}"
        data-attachments-sort-url="{{ route('admin.offers.folders.attachments.sort', $folder) }}"
        data-material-final-url="{{ route('admin.offers.folders.material-final-status', $folder) }}"
        data-ids-request-price-url="{{ route('admin.offers.folders.ids.request-price', $folder) }}"
        data-presence-channel="offer-folder.{{ $folder->id }}"
        data-current-presence-user="{{ e(json_encode($currentPresenceUserPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) }}"
        data-current-user-id="{{ $authUser?->id }}"
        data-current-employee-id="{{ $currentPresenceEmployee?->id ?? $currentPresenceEmployeeId }}"
        data-employee-image-base="{{ asset('images/employee') }}"
        data-default-avatar="{{ asset('images/gender/male.png') }}">
        <div class="of-header of-header-slim">
            <div class="of-header-inner of-header-inner-slim">
                <div class="of-banner-slim">
                    <div class="of-banner-slim-main">
                        <div class="of-banner-slim-left">
                            <div class="of-icon-box of-icon-box-slim">
                                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="#6b8d12"
                                    stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"></path>
                                    <path d="M14 3v5h5"></path>
                                    <path d="M9 13h6"></path>
                                    <path d="M9 17h6"></path>
                                    <path d="M9 9h2"></path>
                                </svg>
                            </div>

                            <div class="of-head-content of-head-content-slim">
                                <div class="of-title-row of-title-row-slim">
                                    <h1 class="of-title of-title-slim">{{ $folder->name ?: 'Ordner' }}</h1>

                                    @php
                                        $workflowStatus = $folder->workflow_status ?? ($detail?->document_status === 'deal'
                                            ? ($folder->deal_status ?? 'open')
                                            : ($folder->offer_status ?? 'draft'));

                                        $workflowStatusLabel = $folder->workflow_status_label ?? ($detail?->document_status === 'deal'
                                            ? ($folder->deal_status_label ?? 'Offen')
                                            : ($folder->offer_status_label ?? 'Entwurf'));

                                        $statusChipClass = 'secondary';

                                        if (($detail?->document_status ?? 'offer') === 'deal') {
                                            $statusChipClass = match ($workflowStatus) {
                                                'auftrag_erhalten', 'open' => 'draft',
                                                'auftragspruefung', 'qualified' => 'sent',
                                                'vertrag_bestaetigung_versendet', 'proposal' => 'viewed',
                                                'anzahlung_offen', 'on_hold' => 'pending',
                                                'anzahlung_erhalten', 'material_bestellt', 'rechnung_erstellt' => 'sent',
                                                'materialplanung', 'abnahme_qualitaetskontrolle' => 'viewed',
                                                'material_vollstaendig_verfuegbar', 'rechnung_bezahlt' => 'final',
                                                'montage_terminplanung', 'montagetermin_bestaetigt' => 'revised',
                                                'in_ausfuehrung', 'negotiation' => 'negotiation',
                                                'montage_abgeschlossen', 'abgeschlossen', 'won' => 'final',
                                                'problem_reklamation', 'lost' => 'cancel',
                                                default => 'draft',
                                            };
                                        } else {
                                            $statusChipClass = match ($workflowStatus) {
                                                'lead_anfrage', 'draft' => 'draft',
                                                'erstkontakt', 'pending_approval' => 'pending',
                                                'beratung_geplant', 'angebot_versendet', 'sent' => 'sent',
                                                'beratung_durchgefuehrt', 'technische_pruefung', 'viewed' => 'viewed',
                                                'daten_unterlagen_fehlen', 'angebot_pausiert', 'expired' => 'expired',
                                                'angebot_in_erstellung' => 'revised',
                                                'rueckfrage_nachbearbeitung', 'revised' => 'revised',
                                                'warten_auf_entscheidung', 'negotiation' => 'negotiation',
                                                'angebot_angenommen', 'accepted' => 'final',
                                                'angebot_abgelehnt', 'rejected', 'cancelled' => 'cancel',
                                                default => 'draft',
                                            };
                                        }
                                    @endphp

                                    <span class="of-status-chip of-status-chip-{{ $statusChipClass }}"
                                        id="workflow-status-chip">
                                        <span id="workflow-status-label">{{ $workflowStatusLabel }}</span>
                                    </span>

                                    <span
                                        class="of-doc-status-badge {{ ($detail?->document_status === 'deal') ? 'deal' : 'offer' }}"
                                        id="document-status-badge">
                                        <span id="document-status-badge-label">
                                            {{ ($detail?->document_status === 'deal') ? 'Auftrag' : 'Angebot' }}
                                        </span>
                                    </span>
                                </div>

                                <div class="of-sub of-sub-slim">
                                    Angebot #{{ $offer?->id ?? $folder->offer_id ?? '-' }}
                                    · Kunde: {{ $customerName ?: 'Unbekannt' }}
                                    · Produkt: {{ $offer?->product?->article_group ?? 'Unbekannt' }}
                                </div>

                                <div class="of-banner-inline-row">
                                    <div class="of-doc-switch of-doc-switch-slim" id="document-status-switch">
                                        <button type="button"
                                            class="of-doc-toggle offer {{ ($detail?->document_status ?? 'offer') === 'offer' ? 'active' : '' }}"
                                            data-doc-status="offer">
                                            Angebot
                                        </button>

                                        <button type="button"
                                            class="of-doc-toggle deal {{ ($detail?->document_status ?? 'offer') === 'deal' ? 'active' : '' }}"
                                            data-doc-status="deal">
                                            Auftrag
                                        </button>
                                    </div>

                                    <div class="of-meta-row of-meta-row-slim">
                                        <span class="of-meta-pill">
                                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                            {{ $employeeName ?: 'Nicht zugewiesen' }}
                                        </span>

                                        <span class="of-meta-pill">
                                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                                                <path d="M16 2v4M8 2v4M3 10h18"></path>
                                            </svg>
                                            {{ optional($detail?->created_at ?? $folder->created_at)->format('d.m.Y H:i') }}
                                        </span>

                                        <span class="of-meta-pill">
                                            <svg viewBox="0 0 24 24" width="14" height="14" fill="none"
                                                stroke="currentColor" stroke-width="2">
                                                <path d="M12 20h9"></path>
                                                <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                            </svg>
                                            {{ optional($detail?->updated_at ?? $folder->updated_at)->format('d.m.Y H:i') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="of-doc-switch-note of-doc-switch-note-slim" id="document-status-note">
                                    Im Status <strong>Angebot</strong> und <strong>Auftrag</strong> sind Änderungen an
                                    Material, Bezug, Lager, Bestellen, Offen und Kommissionen erlaubt.
                                </div>

                                <div class="of-presence of-presence-compact of-presence-slim">
                                    <div class="of-presence-label">
                                        <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor"
                                            stroke-width="2">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                            <circle cx="9" cy="7" r="4"></circle>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                        </svg>
                                        Aktuell im Ordner
                                    </div>

                                    <div class="of-presence-list" id="presence-users">
                                        <div class="of-presence-empty">Keine weiteren Benutzer sichtbar.</div>
                                    </div>

                                    <div class="of-team-inline" aria-label="Angebotsteam">
                                        <span class="of-team-inline-label">Angebotsteam</span>
                                        <div class="of-team-inline-list" id="team-inline-members">
                                            <span class="of-presence-empty">Team wird geladen ...</span>
                                        </div>
                                        <span class="of-access-pill" id="team-inline-access-state">Prüfe Zugriff</span>
                                    </div>
                                </div>


                            </div>
                        </div>

                        <div class="of-actions of-actions-slim">
                            <a href="{{ $wizardUrl }}" class="of-btn" id="btn-load-offer">
                                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M3 12h6"></path>
                                    <path d="M15 12h6"></path>
                                    <path d="M12 3v6"></path>
                                    <path d="M12 15v6"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>
                                @if($detail && $detail->document_status === 'deal')
                                    Auftrag laden
                                @elseif($detail)
                                    Angebot laden
                                @else
                                    Neu erstellen
                                @endif
                            </a>

                            <button type="button" class="of-btn of-team-permission-btn" onclick="openTeamAccessModal()"
                                id="btn-team-access">
                                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                Team & Berechtigung
                            </button>

                            <a href="javascript:void(0)" class="of-btn soft"
                                onclick="openKanbanConfigModal(); return false;" id="btn-kanban-settings"
                                title="Angebot- und Auftrag-Kanban bearbeiten">
                                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path
                                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 .6 1.65 1.65 0 0 0-.33 1.82V22a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-.6-1 1.65 1.65 0 0 0-1.82-.33H2a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-.6A1.65 1.65 0 0 0 10.33 2H14a1.65 1.65 0 0 0 .33 1.82 1.65 1.65 0 0 0 .67.78 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9c.36.25.61.61.7 1H22a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-2.51 1z">
                                    </path>
                                </svg>
                                Kanban Einstellungen
                            </a>

                            @if($offer?->id)
                                <button type="button" class="of-btn danger" onclick="deleteOffer()">
                                    <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor"
                                        stroke-width="2">
                                        <path d="M3 6h18"></path>
                                        <path d="M8 6V4h8v2"></path>
                                        <path d="M19 6l-1 14H6L5 6"></path>
                                    </svg>
                                    Löschen
                                </button>
                            @endif

                            <a href="{{ route('admin.offers.index') }}" class="of-btn soft">
                                <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M15 18l-6-6 6-6"></path>
                                </svg>
                                Zurück
                            </a>
                        </div>
                    </div>

                    <div class="of-banner-stats of-banner-stats-slim">
                        <div class="of-banner-stat of-banner-stat-slim">
                            <div class="of-banner-stat-label">Netto</div>
                            <div class="of-banner-stat-value" id="stat-total-net">
                                {{ number_format((float) ($detail?->total_net ?? 0), 2, ',', '.') }} €
                            </div>
                        </div>

                        <div class="of-banner-stat of-banner-stat-slim">
                            <div class="of-banner-stat-label">Steuer</div>
                            <div class="of-banner-stat-value" id="stat-tax-rate">
                                {{ number_format((float) ($detail?->tax_rate ?? 19), 2, ',', '.') }} %
                            </div>
                        </div>

                        <div class="of-banner-stat of-banner-stat-slim">
                            <div class="of-banner-stat-label">Brutto</div>
                            <div class="of-banner-stat-value" id="stat-total-gross">
                                {{ number_format((float) ($detail?->total_gross ?? 0), 2, ',', '.') }} €
                            </div>
                        </div>

                        <div class="of-banner-stat of-banner-stat-slim">
                            <div class="of-banner-stat-label">Einträge</div>
                            <div class="of-banner-stat-value" id="stat-items-count">0</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="of-shell of-workspace-layout" id="of-workspace-layout">
            <div class="of-shell-head" id="of-workspace-sidebar">
                <div class="of-workspace-sidebar-inner">
                    <div class="of-sidebar-top">
                        <span class="of-sidebar-title">Workspace</span>
                        <div class="of-sidebar-icon-row">
                            <a href="javascript:void(0)" class="of-sidebar-icon-btn"
                                onclick="openKanbanConfigModal(); return false;" title="Kanban Einstellungen"
                                aria-label="Kanban Einstellungen">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path
                                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06A1.65 1.65 0 0 0 15 19.4a1.65 1.65 0 0 0-1 .6 1.65 1.65 0 0 0-.33 1.82V22a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.6 15a1.65 1.65 0 0 0-.6-1 1.65 1.65 0 0 0-1.82-.33H2a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.6a1.65 1.65 0 0 0 1-.6A1.65 1.65 0 0 0 10.33 2H14a1.65 1.65 0 0 0 .33 1.82 1.65 1.65 0 0 0 .67.78 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9c.36.25.61.61.7 1H22a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-2.51 1z">
                                    </path>
                                </svg>
                            </a>
                            <button type="button" class="of-sidebar-icon-btn" id="of-sidebar-toggle"
                                onclick="toggleWorkspaceSidebar(); return false;" title="Sidebar ein-/ausklappen"
                                aria-label="Sidebar ein-/ausklappen">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                                    <path d="M9 4v16"></path>
                                    <path d="M15 10l-3 2 3 2"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="of-sidebar-quick-actions">
                        <a href="javascript:void(0)" class="of-side-action-link"
                            onclick="openKanbanConfigModal(); return false;" title="Kanban Einstellungen">
                            <svg viewBox="0 0 24 24" width="17" height="17" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 6h16"></path>
                                <path d="M4 12h10"></path>
                                <path d="M4 18h7"></path>
                                <path d="M17 15l3 3 3-3"></path>
                            </svg>
                            <span class="of-side-link-text">Kanban Einstellungen</span>
                        </a>
                    </div>
                    <div class="of-tabs" id="workspace-tabs">
                        <button type="button" class="of-tab active" data-tab="uebersicht">
                            <span class="of-tab-icon">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M3 13h8V3H3z"></path>
                                    <path d="M13 21h8v-6h-8z"></path>
                                    <path d="M13 3h8v8h-8z"></path>
                                    <path d="M3 21h8v-4H3z"></path>
                                </svg>
                            </span>
                            <span class="of-tab-label">
                                Übersicht
                                <span class="of-tab-count" id="tab-count-uebersicht">1</span>
                            </span>
                        </button>

                        <button type="button" class="of-tab" data-tab="kanban">
                            <span class="of-tab-icon">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <rect x="3" y="4" width="6" height="16" rx="1"></rect>
                                    <rect x="15" y="4" width="6" height="10" rx="1"></rect>
                                    <rect x="9" y="4" width="6" height="6" rx="1"></rect>
                                </svg>
                            </span>
                            <span class="of-tab-label">
                                Kanban
                                <span class="of-tab-count" id="tab-count-kanban">1</span>
                            </span>
                        </button>

                        <button type="button" class="of-tab" data-tab="material">
                            <span class="of-tab-icon">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                                    </path>
                                    <path d="M3.3 7l8.7 5 8.7-5"></path>
                                    <path d="M12 22V12"></path>
                                </svg>
                            </span>
                            <span class="of-tab-label">
                                Materialliste
                                <span class="of-tab-count" id="tab-count-material">0</span>
                            </span>
                        </button>

                        <button type="button" class="of-tab" data-tab="labor">
                            <span class="of-tab-icon">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path
                                        d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.3-3.3a1 1 0 0 0 0-1.4L19.4 3a1 1 0 0 0-1.4 0z">
                                    </path>
                                    <path d="m16 2 6 6"></path>
                                    <path d="M8.7 15.3 3 21l5.7-5.7"></path>
                                    <path d="m14 7-8 8"></path>
                                    <path d="m5 14 5 5"></path>
                                </svg>
                            </span>
                            <span class="of-tab-label">
                                Lohnliste
                                <span class="of-tab-count" id="tab-count-labor">0</span>
                            </span>
                        </button>

                        <button type="button" class="of-tab" data-tab="material-print">
                            <span class="of-tab-icon">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2">
                                    </path>
                                    <rect x="6" y="14" width="12" height="8"></rect>
                                </svg>
                            </span>
                            <span class="of-tab-label">
                                Materialdruck
                                <span class="of-tab-count" id="tab-count-material-print">0</span>
                            </span>
                        </button>

                        <button type="button" class="of-tab" data-tab="print-files">
                            <span class="of-tab-icon">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                            </span>
                            <span class="of-tab-label">
                                Hochgeladene Datei

                                <span class="of-tab-count" id="tab-count-print-files">0</span>
                            </span>
                        </button>

                        <button type="button" class="of-tab" data-tab="agb">
                            <span class="of-tab-icon">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <path d="M14 2v6h6"></path>
                                    <path d="M8 13h8"></path>
                                    <path d="M8 17h8"></path>
                                    <path d="M8 9h3"></path>
                                </svg>
                            </span>
                            <span class="of-tab-label">
                                AGB
                                <span class="of-tab-count" id="tab-count-agb">1</span>
                            </span>
                        </button>

                        <button type="button" class="of-tab" data-tab="historie">
                            <span class="of-tab-icon">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M12 8v4l3 3"></path>
                                    <path d="M3.05 11a9 9 0 1 1 .5 4"></path>
                                    <path d="M3 16v5h5"></path>
                                </svg>
                            </span>
                            <span class="of-tab-label">
                                Historie
                                <span class="of-tab-count" id="tab-count-historie">0</span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="of-shell-body">
                <div class="of-panel active" id="panel-uebersicht">
                    <div class="of-grid-2">
                        <div class="of-card">
                            <div class="of-card-h">
                                <h3 class="of-card-title">Angebotsdaten</h3>
                            </div>

                            <div class="of-card-b">
                                <div class="of-info-list">
                                    <div class="of-info-item">
                                        <div class="of-info-key">Firma</div>
                                        <div class="of-info-val" id="info-company-name">{{ $detail?->company_name ?: '-' }}
                                        </div>
                                    </div>

                                    <div class="of-info-item">
                                        <div class="of-info-key">Markenmodus</div>
                                        <div class="of-info-val" id="info-brand-mode">{{ $detail?->brand_mode ?: 'Text' }}
                                        </div>
                                    </div>

                                    <div class="of-info-item">
                                        <div class="of-info-key">Markenfarbe</div>
                                        <div class="of-info-val" id="info-brand-color">{{ $detail?->brand_color ?: '-' }}
                                        </div>
                                    </div>

                                    <div class="of-info-item">
                                        <div class="of-info-key">Logo-URL</div>
                                        <div class="of-info-val" id="info-brand-logo">{{ $detail?->brand_logo_url ?: '-' }}
                                        </div>
                                    </div>

                                    <div class="of-info-item">
                                        <div class="of-info-key">Sektionen</div>
                                        <div class="of-info-val" id="info-sections-count">{{ count($initialSections) }}
                                        </div>
                                    </div>

                                    <div class="of-info-item">
                                        <div class="of-info-key">Platzierte Bilder</div>
                                        <div class="of-info-val" id="info-images-count">{{ count($initialPlacedImages) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="of-card">
                            <div class="of-card-h">
                                <h3 class="of-card-title">Decktext</h3>
                            </div>

                            <div class="of-card-b">
                                <div class="of-cover {{ blank($detail?->cover_text_html) && blank($detail?->cover_text) ? 'empty' : '' }}"
                                    id="cover-box">
                                    @if(!blank($detail?->cover_text_html))
                                        {!! $detail->cover_text_html !!}
                                    @elseif(!blank($detail?->cover_text))
                                        {!! nl2br(e($detail->cover_text)) !!}
                                    @else
                                        Kein Decktext vorhanden.
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="of-status-overview" style="display:none;">
                        <div class="of-status-card">
                            <div class="of-status-name">Entwurf</div>
                            <div class="of-status-value" id="status-card-draft">0</div>
                        </div>
                        <div class="of-status-card">
                            <div class="of-status-name">Gesendet</div>
                            <div class="of-status-value" id="status-card-sent">0</div>
                        </div>
                        <div class="of-status-card">
                            <div class="of-status-name">Verhandlung</div>
                            <div class="of-status-value" id="status-card-negotiation">0</div>
                        </div>
                        <div class="of-status-card">
                            <div class="of-status-name">Abgeschlossen</div>
                            <div class="of-status-value" id="status-card-final">0</div>
                        </div>
                        <div class="of-status-card">
                            <div class="of-status-name">Storniert</div>
                            <div class="of-status-value" id="status-card-cancel">0</div>
                        </div>
                    </div>
                </div>

                <div class="of-panel" id="panel-kanban">
                    <div class="of-card">
                        <div class="of-card-h">
                            <h3 class="of-card-title">Workflow Liste</h3>

                            <div class="of-inline-actions">
                                <span class="of-badge" id="kanban-list-badge">1 Eintrag</span>
                            </div>
                        </div>

                        <div class="of-card-b">
                            <div id="kanban-columns">
                                <div class="of-empty">Lade Workflow...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="of-panel" id="panel-material">
                    <div class="of-card">
                        <div class="of-card-h">
                            <h3 class="of-card-title">Materialliste</h3>

                            <div class="of-inline-actions">
                                <span class="of-badge" id="material-count-badge">0 Positionen</span>

                                <button type="button" class="of-btn soft" id="material-compare-btn"
                                    onclick="openMaterialComparisonModal()">
                                    Preisvergleich
                                </button>

                                <button type="button" class="of-btn soft" id="ids-request-price-btn"
                                    onclick="openIdsRequestPrice()">
                                    IDS / GC Preis anfragen
                                </button>

                                <button type="button" class="of-btn soft" onclick="switchTab('material-print')">
                                    Materialdruck öffnen
                                </button>
                            </div>
                        </div>

                        <div class="of-card-b">
                            <div class="of-tabs" style="margin-bottom:14px;">
                                <button type="button" class="of-tab active material-subtab-btn" data-material-filter="all">
                                    Alle <span class="of-tab-count" id="mat-subcount-all">0</span>
                                </button>
                                <button type="button" class="of-tab material-subtab-btn" data-material-filter="offen">
                                    Offen <span class="of-tab-count" id="mat-subcount-offen">0</span>
                                </button>
                                <button type="button" class="of-tab material-subtab-btn" data-material-filter="lager">
                                    Lager <span class="of-tab-count" id="mat-subcount-lager">0</span>
                                </button>
                                <button type="button" class="of-tab material-subtab-btn" data-material-filter="bestellen">
                                    Bestellen <span class="of-tab-count" id="mat-subcount-bestellen">0</span>
                                </button>

                                <button type="button" class="of-tab material-subtab-btn" data-material-filter="final">
                                    Kommissionen Materialliste <span class="of-tab-count" id="mat-subcount-final">0</span>
                                </button>
                            </div>

                            <div id="material-list-wrap">
                                <div class="of-empty">Lade Materialliste...</div>
                            </div>
                        </div>

                        <div id="smart-material-sidebar" class="of-smart-side">
                            <div class="of-smart-card">
                                <div class="of-smart-head">
                                    <div class="of-smart-head-row">
                                        <div style="display:flex; gap:12px; min-width:0;">
                                            <div class="of-smart-icon">
                                                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="#6b8d12"
                                                    stroke-width="2">
                                                    <path d="M12 3v18"></path>
                                                    <path d="M17 8l-5-5-5 5"></path>
                                                    <path d="M17 16l-5 5-5-5"></path>
                                                </svg>
                                            </div>
                                            <div style="min-width:0;">
                                                <h3 class="of-smart-title">Günstigste Alternative</h3>
                                                <div class="of-smart-sub">
                                                    Zeigt den besten Preis für die aktuell ausgewählten Materialpositionen.
                                                </div>
                                            </div>
                                        </div>

                                        <button type="button" class="of-smart-close"
                                            onclick="hideSmartMaterialSidebar()">×</button>
                                    </div>
                                </div>

                                <div class="of-smart-body" id="smart-material-sidebar-body">
                                    <div class="of-smart-empty">Bitte Materialpositionen auswählen.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="of-panel" id="panel-labor">
                    <div class="of-card">
                        <div class="of-card-h">
                            <h3 class="of-card-title">Lohnliste</h3>
                            <div class="of-inline-actions">
                                <span class="of-badge" id="labor-count-badge">0 Lohnzeilen</span>
                            </div>
                        </div>

                        <div class="of-card-b">
                            <div id="labor-list-wrap">
                                <div class="of-empty">Lade Lohnliste...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="of-panel" id="panel-material-print">
                    <div class="of-print-sheet">
                        <div class="of-print-head of-print-head-clean">
                            <div class="of-print-head-main of-print-head-main-clean">
                                <div class="of-print-product-head">
                                    <div class="of-print-product-label" id="print-list-title">
                                        Materialliste
                                    </div>

                                    <div class="of-print-product-name-top" id="print-product-name-top">
                                        {{ $offer?->product?->article_group ?? 'Unbekannt' }}
                                    </div>
                                </div>
                            </div>

                            <div class="of-inline-actions of-no-print">
                                <button type="button" class="of-btn" onclick="printMaterialSheet()">
                                    Material drucken
                                </button>
                            </div>
                        </div>

                        <div class="of-print-meta of-print-meta-single">
                            <div class="of-print-customer-block">
                                <div class="of-print-customer-name" id="print-customer-name">
                                    {{ trim(($offer?->customer?->name ?? '') . ' ' . ($offer?->customer?->lastname ?? '')) ?: ($offer?->customer?->firma ?? 'Unbekannt') }}
                                </div>

                                <div class="of-print-address" id="print-customer-address">
                                    @php
                                        $printAddressLine = trim(
                                            ($offer?->customer?->street ?? '') .
                                            (filled($offer?->customer?->street) && filled($offer?->customer?->postal_code) ? ', ' : '') .
                                            ($offer?->customer?->postal_code ?? '') . ' ' . ($offer?->customer?->city ?? '')
                                        );
                                    @endphp

                                    {{ $printAddressLine ?: '-' }}
                                </div>

                                <div class="of-print-date-line">
                                    <strong>Datum:</strong>
                                    <span id="print-date">{{ now()->format('d.m.Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="of-print-body">
                            <div id="material-print-wrap">
                                <div class="of-empty">Lade Druckansicht...</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="of-panel" id="panel-print-files">
                    <div class="of-card">
                        <div class="of-card-h">
                            <h3 class="of-card-title">PDF- und Bilddateien für den Druck</h3>

                            <div class="of-inline-actions">
                                <span class="of-badge" id="print-files-count-badge">0 Dateien</span>

                                <button type="button" class="of-btn of-upload-toggle-btn" id="toggle-upload-box-btn">
                                    Datei hochladen
                                </button>
                            </div>
                        </div>

                        <div class="of-card-b">
                            <div class="of-upload-box" id="upload-box-wrap">
                                <div class="of-card">
                                    <div class="of-card-b">
                                        <div id="attachment-dropzone"
                                            style="border:2px dashed #cbd5e1; border-radius:16px; padding:22px; background:#f8fafc; text-align:left;">
                                            <div style="margin-bottom:14px;">
                                                <label
                                                    style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:6px;">
                                                    Art des Dokuments
                                                </label>

                                                <select id="upload-doc-type"
                                                    style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:10px 14px; font-weight:700; background:#fff; margin-bottom:8px;">
                                                    <option value="Angebot Bestätigung">Angebot Bestätigung</option>
                                                    <option value="Auftragsbestätigung">Auftragsbestätigung</option>
                                                    <option value="custom">Eigener Status...</option>
                                                </select>

                                                <input type="text" id="upload-custom-type"
                                                    placeholder="Bitte eigenen Status eingeben"
                                                    style="display:none; width:100%; border:1px solid var(--of-line); border-radius:12px; padding:10px 14px; font-weight:700;">
                                            </div>

                                            <div style="margin-bottom:16px;">
                                                <label
                                                    style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:6px;">
                                                    Notiz / Grund (Optional)
                                                </label>

                                                <textarea id="upload-notice" rows="2"
                                                    placeholder="Warum laden Sie dieses Dokument hoch?"
                                                    style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:10px 14px; font-weight:700; resize:vertical;"></textarea>
                                            </div>

                                            <div style="text-align:center; padding-top:10px; border-top:1px solid #e2e8f0;">
                                                <div style="font-weight:900; color:#111827; margin-bottom:4px;">
                                                    Dateien hier hineinziehen oder klicken
                                                </div>

                                                <div class="of-sub" style="margin:0 0 14px 0;">
                                                    PDF, JPG, JPEG, PNG, WEBP (Auto-Upload)
                                                </div>

                                                <form id="print-files-upload-form">
                                                    <input type="file" id="print-files-input" name="files[]" multiple
                                                        accept=".pdf,.jpg,.jpeg,.png,.webp" style="display:none;">

                                                    <button type="button" class="of-btn soft" id="pick-files-btn">
                                                        Dateien auswählen
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="of-file-analytics" id="print-files-analytics" style="margin-bottom:16px;">
                                <div class="of-file-analytics-card">
                                    <div class="of-file-analytics-label">Dateien gesamt</div>
                                    <div class="of-file-analytics-value" id="analytics-files-total">0</div>
                                    <div class="of-file-analytics-sub">PDF + Bilder</div>
                                </div>

                                <div class="of-file-analytics-card">
                                    <div class="of-file-analytics-label">Bilder</div>
                                    <div class="of-file-analytics-value" id="analytics-images-total">0</div>
                                    <div class="of-file-analytics-sub">JPG, PNG, WEBP</div>
                                </div>

                                <div class="of-file-analytics-card">
                                    <div class="of-file-analytics-label">PDF-Dateien</div>
                                    <div class="of-file-analytics-value" id="analytics-pdfs-total">0</div>
                                    <div class="of-file-analytics-sub">Druck / Dokumente</div>
                                </div>

                                <div class="of-file-analytics-card">
                                    <div class="of-file-analytics-label">Speicher gesamt</div>
                                    <div class="of-file-analytics-value" id="analytics-size-total">0 B</div>
                                    <div class="of-file-analytics-sub">Alle Uploads</div>
                                </div>
                            </div>

                            <div style="margin-bottom:16px;">
                                <input type="text" id="attachment-search-input" placeholder="Dateien suchen ..."
                                    style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:700;">
                            </div>

                            <div id="print-files-list-wrap">
                                <div class="of-empty">Keine Druckdateien vorhanden.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="of-panel" id="panel-agb">
                    <div class="of-card">
                        <div class="of-card-h">
                            <h3 class="of-card-title">AGB für dieses Angebot</h3>
                            <div class="of-inline-actions">
                                <button type="button" class="of-btn" onclick="saveAgbForFolder()">AGB speichern</button>
                            </div>
                        </div>

                        <div class="of-card-b">
                            <div class="of-info-list" style="gap:16px;">
                                <div>
                                    <label class="of-info-key" style="display:block; margin-bottom:8px;">Titel</label>
                                    <input type="text" id="agb-title-input" value="{{ $resolvedAgbTitle }}"
                                        style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:700;">
                                </div>

                                <div>
                                    <label class="of-info-key" style="display:block; margin-bottom:8px;">AGB Text</label>
                                    <input type="hidden" id="agb-text-input" value="{{ e($resolvedAgbText) }}">
                                    <div id="agb-editor" style="background:#fff; border-radius:12px; overflow:hidden;">
                                        {!! $resolvedAgbText !!}
                                    </div>
                                </div>

                                <div class="of-sub">
                                    Dieser AGB-Text ist nur für diesen Angebotsordner gespeichert.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="of-panel" id="panel-historie">
                    <div class="of-card">
                        <div class="of-card-h">
                            <h3 class="of-card-title">Historie</h3>
                            <div class="of-inline-actions">
                                <span class="of-badge" id="history-count-badge">0 Einträge</span>
                            </div>
                        </div>

                        <div class="of-card-b">
                            <div id="history-list-wrap">
                                <div class="of-empty">Lade Historie...</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="of-modal-backdrop" id="material-comparison-modal" style="display:none;">
        <div class="of-modal">
            <div class="of-modal-head">
                <div>
                    <h3 class="of-card-title" style="margin:0;">Material Preisvergleich</h3>
                    <div class="of-sub" style="margin-top:6px;">Vergleich der ausgewählten Produkte über alle verfügbaren
                        Distributoren.</div>
                </div>

                <button type="button" class="of-btn soft" onclick="closeMaterialComparisonModal()">Schließen</button>
            </div>

            <div class="of-modal-body" id="material-comparison-body">
                <div class="of-empty">Keine Daten geladen.</div>
            </div>
        </div>
    </div>

    <div class="of-modal-backdrop" id="labor-qualification-modal" style="display:none; z-index:10120;">
        <div class="of-modal" style="width:min(980px,96vw);">
            <div class="of-modal-head">
                <div>
                    <h3 class="of-card-title" id="labor-qualification-modal-title" style="margin:0;">
                        Qualifikationsmöglichkeiten
                    </h3>

                    <div class="of-sub" id="labor-qualification-modal-sub" style="margin-top:6px;">
                        Welche Qualifikation kann diese Arbeit ausführen?
                    </div>
                </div>

                <button type="button" class="of-btn soft" onclick="closeLaborQualificationModal()">
                    Schließen
                </button>
            </div>

            <div class="of-modal-body" id="labor-qualification-modal-body">
                <div class="of-empty">Lade Möglichkeiten...</div>
            </div>
        </div>
    </div>

    <div class="of-modal-backdrop of-material-details-modal" id="material-detail-modal" style="display:none;">
        <div class="of-modal">
            <div class="of-modal-head">
                <div>
                    <h3 class="of-card-title" id="material-detail-title" style="margin:0;">Materialdetails</h3>
                    <div class="of-sub" id="material-detail-sub" style="margin-top:6px;">Preisvergleich und Historie</div>
                </div>

                <div class="of-inline-actions">
                    <button type="button" class="of-btn soft" onclick="closeMaterialDetailModal()">Schließen</button>
                </div>
            </div>

            <div class="of-modal-body">
                <div class="of-modal-tabs">
                    <button type="button" class="of-modal-tab active" data-material-modal-tab="vergleich">
                        Preisvergleich
                    </button>

                    <button type="button" class="of-modal-tab" data-material-modal-tab="historie">
                        Historie
                    </button>
                </div>

                <div class="of-modal-tab-panel active" id="material-modal-panel-vergleich">
                    <div id="material-detail-compare-body">
                        <div class="of-empty">Lade Vergleich...</div>
                    </div>
                </div>

                <div class="of-modal-tab-panel" id="material-modal-panel-historie">
                    <div id="material-detail-history-body">
                        <div class="of-empty">Lade Historie...</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="of-modal-backdrop" id="status-reason-modal" style="display:none; z-index:10060;">
        <div class="of-modal" style="width:min(560px,96vw);">
            <div class="of-modal-head">
                <div>
                    <h3 class="of-card-title" style="margin:0;">Statusänderung bestätigen</h3>
                    <div class="of-sub" id="status-reason-sub" style="margin-top:6px;">
                        Bitte geben Sie den Grund für die Statusänderung an.
                    </div>
                </div>

                <button type="button" class="of-btn soft" onclick="closeStatusReasonModal()">Schließen</button>
            </div>

            <div class="of-modal-body">
                <div style="display:flex; flex-direction:column; gap:14px;">
                    <div>
                        <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                            Neuer Status
                        </label>
                        <input type="text" id="status-reason-status-label" readonly
                            style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800; background:#f8fafc;">
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                            Grund *
                        </label>
                        <textarea id="status-reason-text" rows="5"
                            placeholder="Bitte schreiben Sie hier den Grund für die Statusänderung..."
                            style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:700; resize:vertical; min-height:140px;"></textarea>
                    </div>

                    <div id="status-reason-error" style="display:none; color:#b91c1c; font-size:12px; font-weight:800;">
                        Bitte geben Sie einen Grund ein.
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap;">
                        <button type="button" class="of-btn soft" onclick="closeStatusReasonModal()">Abbrechen</button>
                        <button type="button" class="of-btn" id="status-reason-confirm-btn">Status ändern</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="of-modal-backdrop" id="material-move-modal" style="display:none; z-index:10070;">
        <div class="of-modal" style="width:min(680px,96vw);">
            <div class="of-modal-head">
                <div>
                    <h3 class="of-card-title" id="material-move-modal-title" style="margin:0;">Menge verschieben</h3>
                    <div class="of-sub" id="material-move-modal-sub" style="margin-top:6px;">
                        Bitte geben Sie die Menge an, die verschoben werden soll.
                    </div>
                </div>

                <button type="button" class="of-btn soft" onclick="closeMaterialMoveModal()">Schließen</button>
            </div>

            <div class="of-modal-body">
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <div id="material-move-modal-summary" class="of-smart-list">
                        <div class="of-smart-list-head">Auswahl</div>
                        <div class="of-smart-list-body" style="padding:14px;">
                            Keine Auswahl vorhanden.
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                        <div>
                            <label
                                style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                                Zielstatus
                            </label>
                            <input type="text" id="material-move-target-label" readonly
                                style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800; background:#f8fafc;">
                        </div>

                        <div>
                            <label
                                style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                                Menge *
                            </label>
                            <input type="number" id="material-move-qty" min="0.0001" step="0.0001" placeholder="z. B. 10"
                                style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800;">
                        </div>
                    </div>

                    <div class="of-sub">
                        Hinweis: Bei mehreren markierten Positionen wird die eingegebene Menge auf **jede ausgewählte
                        Position** angewendet.
                    </div>

                    <div id="material-move-error" style="display:none; color:#b91c1c; font-size:12px; font-weight:800;">
                        Bitte eine gültige Menge größer als 0 eingeben.
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap;">
                        <button type="button" class="of-btn soft" onclick="closeMaterialMoveModal()">Abbrechen</button>
                        <button type="button" class="of-btn" id="material-move-confirm-btn">Verschieben</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="of-modal-backdrop" id="material-final-modal" style="display:none; z-index:10080;">
        <div class="of-modal" style="width:min(760px,96vw);">
            <div class="of-modal-head">
                <div>
                    <h3 class="of-card-title" id="material-final-modal-title" style="margin:0;">
                        Kommissionen Materialliste bestätigen
                    </h3>
                    <div class="of-sub" id="material-final-modal-sub" style="margin-top:6px;">
                        Bitte bestätigen Sie die finale Menge.
                    </div>
                </div>

                <button type="button" class="of-btn soft" onclick="closeMaterialFinalModal()">Schließen</button>
            </div>

            <div class="of-modal-body">
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <div id="material-final-modal-summary" class="of-smart-list">
                        <div class="of-smart-list-head">Auswahl</div>
                        <div class="of-smart-list-body" style="padding:14px;">
                            Keine Auswahl vorhanden.
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                        <div>
                            <label
                                style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                                Quellstatus
                            </label>
                            <input type="text" id="material-final-source-label" readonly
                                style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800; background:#f8fafc;">
                        </div>

                        <div>
                            <label
                                style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                                Verfügbar
                            </label>
                            <input type="text" id="material-final-available-label" readonly
                                style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800; background:#f8fafc;">
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                        <div>
                            <label
                                style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                                Final-Menge *
                            </label>
                            <input type="number" id="material-final-qty" min="0.0001" step="0.0001" placeholder="z. B. 10"
                                style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800;">
                        </div>

                        <div>
                            <label
                                style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                                Restmenge verschieben nach *
                            </label>
                            <select id="material-final-remaining-to"
                                style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800; background:#fff;"></select>
                        </div>
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                            Grund *
                        </label>
                        <textarea id="material-final-reason" rows="4" placeholder="z. B. Physisch bestätigt"
                            style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:700; resize:vertical; min-height:120px;">Physisch bestätigt</textarea>
                    </div>

                    <div id="material-final-error" style="display:none; color:#b91c1c; font-size:12px; font-weight:800;">
                        Bitte prüfen Sie Menge, Reststatus und Grund.
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap;">
                        <button type="button" class="of-btn soft" onclick="closeMaterialFinalModal()">Abbrechen</button>
                        <button type="button" class="of-btn" id="material-final-confirm-btn">Final bestätigen</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="of-modal-backdrop" id="document-status-modal" style="display:none; z-index:10090;">
        <div class="of-modal" style="width:min(720px,96vw);">
            <div class="of-modal-head">
                <div>
                    <h3 class="of-card-title" id="document-status-modal-title" style="margin:0;">
                        Dokumentstatus ändern
                    </h3>
                    <div class="of-sub" id="document-status-modal-sub" style="margin-top:6px;">
                        Bitte bestätigen Sie die Änderung.
                    </div>
                </div>

                <button type="button" class="of-btn soft" onclick="closeDocumentStatusModal()">Schließen</button>
            </div>

            <div class="of-modal-body">
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <div id="document-status-modal-warning" class="of-doc-switch-note warning" style="display:none;"></div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                            Änderung
                        </label>
                        <input type="text" id="document-status-modal-change" readonly
                            style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:800; background:#f8fafc;">
                    </div>

                    <div>
                        <label style="display:block; font-size:12px; font-weight:900; color:#6b7280; margin-bottom:8px;">
                            Grund *
                        </label>
                        <textarea id="document-status-modal-reason" rows="5"
                            placeholder="Bitte Grund für die Änderung eingeben ..."
                            style="width:100%; border:1px solid var(--of-line); border-radius:12px; padding:12px 14px; font-weight:700; resize:vertical; min-height:140px;"></textarea>
                    </div>

                    <div id="document-status-modal-error"
                        style="display:none; color:#b91c1c; font-size:12px; font-weight:800;">
                        Bitte einen Grund eingeben.
                    </div>

                    <div style="display:flex; justify-content:flex-end; gap:10px; flex-wrap:wrap;">
                        <button type="button" class="of-btn soft" onclick="closeDocumentStatusModal()">Abbrechen</button>
                        <button type="button" class="of-btn" id="document-status-modal-confirm-btn">Ändern</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="of-modal-backdrop" id="clone-prompt-modal" style="display:none; z-index:10100;">
        <div class="of-modal" style="width:min(500px,96vw);">
            <div class="of-modal-head">
                <div>
                    <h3 class="of-card-title" style="margin:0;">Angebot bearbeiten oder klonen?</h3>
                    <div class="of-sub" style="margin-top:6px;">
                        Dieses Angebot wurde bereits bearbeitet / versendet. Möchten Sie das aktuelle Angebot weiter ändern
                        oder lieber einen neuen Ordner als Kopie im selben Angebot erstellen?
                    </div>
                </div>
                <button type="button" class="of-btn soft"
                    onclick="document.getElementById('clone-prompt-modal').style.display='none'">Schließen</button>
            </div>
            <div class="of-modal-body">
                <p style="font-size:14px; margin-bottom: 20px; line-height: 1.6;">
                    Möchten Sie das aktuelle Angebot weiter verändern oder für weitere Anpassungen eine Kopie <b>als neuen
                        Ordner im selben Angebot</b> erstellen?
                </p>
                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <a href="{{ $wizardUrl }}" class="of-btn soft">Aktuelles ändern</a>
                    <button type="button" class="of-btn" id="btn-confirm-clone">Klonen (Neu) - Empfohlen</button>
                </div>
            </div>
        </div>
    </div>


    <div class="of-modal-backdrop" id="version-prompt-modal" style="display:none; z-index:10100;">
        <div class="of-modal" style="width:min(500px,96vw);">
            <div class="of-modal-head">
                <div>
                    <h3 class="of-card-title" style="margin:0;">Welche Version möchten Sie laden?</h3>
                    <div class="of-sub" style="margin-top:6px;">
                        Dieses Dokument befindet sich bereits in der Auftragsphase (Deal).
                    </div>
                </div>
                <button type="button" class="of-btn soft"
                    onclick="document.getElementById('version-prompt-modal').style.display='none'">Schließen</button>
            </div>
            <div class="of-modal-body">
                <p style="font-size:14px; margin-bottom: 20px; line-height: 1.6;">
                    Möchten Sie den aktuellen <b>Auftrag weiterbearbeiten</b> oder eine schreibgeschützte Momentaufnahme des
                    <b>ursprünglichen Angebots ansehen</b>?
                </p>
                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="of-btn soft" id="btn-load-snapshot">Angebot ansehen (Read-Only)</button>
                    <button type="button" class="of-btn" id="btn-load-current">Auftrag bearbeiten</button>
                </div>
            </div>
        </div>
    </div>

    <div class="of-modal-backdrop" id="lightbox-modal" style="display:none;">
        <div class="of-lightbox-shell">
            <button type="button" class="of-lightbox-btn of-lightbox-close" onclick="closeLightbox()"
                aria-label="Schließen">
                ✕
            </button>

            <button type="button" id="lightbox-prev" class="of-lightbox-btn of-lightbox-prev" onclick="lightboxPrev()"
                aria-label="Vorherige Datei">
                ❮
            </button>

            <div id="lightbox-content"></div>

            <button type="button" id="lightbox-next" class="of-lightbox-btn of-lightbox-next" onclick="lightboxNext()"
                aria-label="Nächste Datei">
                ❯
            </button>

            <div id="lightbox-caption"></div>
        </div>
    </div>



    {{-- Team & Berechtigung Modal --}}
    <div class="of-modal-backdrop" id="team-access-modal" style="display:none;">
        <div class="of-modal of-access-modal-panel">
            <div class="of-modal-head">
                <div>
                    <h3 class="of-card-title" style="font-size:18px;margin:0;">Team & Berechtigung</h3>
                    <div class="of-sub" style="margin-top:6px;">Legen Sie fest, wer diesen Ordner öffnen, erstellen und
                        bearbeiten darf.</div>
                </div>
                <button class="of-btn soft" type="button" onclick="closeTeamAccessModal()">Schließen</button>
            </div>

            <div class="of-modal-body">
                <div class="of-access-card" id="team-access-card" style="box-shadow:none;margin:0;">
                    <div class="of-access-head">
                        <div>
                            <div class="of-access-title">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                Zugriff für diesen Ordner
                            </div>
                            <div class="of-access-sub" id="team-access-summary">Lade Team und Zugriffsrechte ...</div>
                        </div>
                        <span class="of-access-state" id="team-access-state">Prüfe Zugriff</span>
                    </div>

                    <div class="of-access-modal-grid">
                        <div class="of-access-modal-card">
                            <div class="of-access-modal-title">Aktuelles Angebotsteam</div>
                            <div class="of-access-modal-text">Diese Personen kommen aus Kanban/Kundenprofil und sind für
                                dieses Angebot zuständig.</div>
                            <div class="of-access-team" id="team-access-members">
                                <div class="of-access-empty">Noch kein Team geladen.</div>
                            </div>
                        </div>

                        <div class="of-access-modal-card soft">
                            <div class="of-access-modal-title">Wer darf mit diesem Ordner arbeiten?</div>
                            <div class="of-access-options">
                                <label class="of-access-option" id="access-option-all">
                                    <input type="radio" name="offer_access_mode" value="all"
                                        onchange="saveTeamAccessMode('all')">
                                    <span>
                                        <span class="of-access-option-title">Alle Mitarbeiter</span>
                                        <span class="of-access-option-text">Alle berechtigten Mitarbeiter können diesen
                                            Ordner sehen, öffnen und bearbeiten.</span>
                                    </span>
                                </label>
                                <label class="of-access-option" id="access-option-team">
                                    <input type="radio" name="offer_access_mode" value="team_only"
                                        onchange="saveTeamAccessMode('team_only')">
                                    <span>
                                        <span class="of-access-option-title">Nur Angebotsteam</span>
                                        <span class="of-access-option-text">Nur die Teammitglieder dieses Angebots dürfen
                                            sehen, erstellen und bearbeiten.</span>
                                    </span>
                                </label>
                            </div>
                            <div class="of-access-actions">
                                <span class="of-access-help" id="team-access-help">Änderungen werden mit Kanban und
                                    Kundenprofil synchronisiert.</span>
                                <button type="button" class="of-access-save"
                                    onclick="refreshTeamAccessPanel()">Aktualisieren</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- Kanban Konfiguration Modal --}}
    <div class="of-modal-backdrop" id="kanban-config-modal" style="display:none;">
        <div class="of-modal" style="width:min(980px,96vw);">
            <div class="of-modal-head">
                <div>
                    <h3 class="of-card-title" style="font-size:18px;margin:0;">Kanban-Unterphasen konfigurieren</h3>
                    <div class="of-sub" style="margin-top:6px;">Diese Spalten kommen direkt aus
                        <strong>lead_stage_sub_stages</strong>. Änderungen hier ändern dieselben Unterphasen wie im
                        Haupt-Kanban.
                    </div>
                </div>
                <button class="of-btn soft" type="button" onclick="closeKanbanConfigModal()">Schließen</button>
            </div>
            <div class="of-modal-body">
                <div class="of-kanban-config-add">
                    <div>
                        <div class="of-kanban-config-label">Neue Unterphase</div>
                        <input id="kanban-new-label" class="of-kanban-config-input" placeholder="z.B. Interne Prüfung">
                    </div>
                    <div>
                        <div class="of-kanban-config-label">Icon</div>
                        <input id="kanban-new-icon" class="of-kanban-config-input" placeholder="check">
                    </div>
                    <div>
                        <div class="of-kanban-config-label">Farbe</div>
                        <input id="kanban-new-color" class="of-kanban-config-input" type="color" value="#93c21c"
                            style="padding:3px;">
                    </div>
                    <button type="button" class="of-btn" onclick="createKanbanStage()">Hinzufügen</button>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                        <div class="of-selected-badge" id="kanban-config-context">Workflow</div>
                        <button type="button" class="of-kanban-mini-btn" id="kanban-config-offer-btn"
                            onclick="switchKanbanConfigDocument('offer')">Angebot bearbeiten</button>
                        <button type="button" class="of-kanban-mini-btn" id="kanban-config-deal-btn"
                            onclick="switchKanbanConfigDocument('deal')">Auftrag bearbeiten</button>
                    </div>
                    <div class="of-sub" style="margin:0;">Reihenfolge mit dem Griff links ändern. Gespeichert wird in den
                        Kanban-Unterphasen.</div>
                </div>
                <div class="of-kanban-config-list" id="kanban-config-list">
                    <div class="of-empty">Kanban-Spalten werden geladen...</div>
                </div>
            </div>
        </div>
    </div>

    <div id="of-toast-wrap" class="of-toast-wrap"></div>
@endsection

@once
    @push('scripts')

        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        <script>
            window.setWorkspaceSidebarCollapsed = window.setWorkspaceSidebarCollapsed || function (collapsed) {
                const layout = document.getElementById('of-workspace-layout');
                const toggle = document.getElementById('of-sidebar-toggle');
                if (!layout) return;
                layout.classList.toggle('sidebar-collapsed', !!collapsed);
                if (toggle) {
                    toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                    toggle.classList.toggle('is-collapsed', !!collapsed);
                }
                try {
                    localStorage.setItem('offerWorkspaceSidebarCollapsed', collapsed ? '1' : '0');
                } catch (error) { }
            };

            window.toggleWorkspaceSidebar = window.toggleWorkspaceSidebar || function () {
                const layout = document.getElementById('of-workspace-layout');
                const isCollapsed = layout ? layout.classList.contains('sidebar-collapsed') : false;
                window.setWorkspaceSidebarCollapsed(!isCollapsed);
            };

            window.workspaceSwitchTabFallback = window.workspaceSwitchTabFallback || function (tab) {
                if (!tab) return false;
                const targetPanel = document.getElementById('panel-' + tab);
                if (!targetPanel) {
                    console.warn('Workspace panel not found:', 'panel-' + tab);
                    return false;
                }

                document.querySelectorAll('#workspace-tabs .of-tab[data-tab], .of-workspace-side-nav .of-tab[data-tab], .of-sidebar-nav .of-tab[data-tab], .of-tab[data-tab]').forEach(function (btn) {
                    btn.classList.toggle('active', btn.dataset.tab === tab);
                });

                document.querySelectorAll('.of-shell-body .of-panel[id^="panel-"], .of-panel[id^="panel-"]').forEach(function (panel) {
                    panel.classList.toggle('active', panel.id === 'panel-' + tab);
                });

                try { localStorage.setItem('offerWorkspaceActiveTab', tab); } catch (error) { }
                window.offerWorkspaceCurrentTab = tab;
                return true;
            };

            window.switchWorkspaceTab = window.switchWorkspaceTab || function (tab) {
                if (typeof window.switchTab === 'function') {
                    try {
                        window.switchTab(tab);
                        return true;
                    } catch (error) {
                        console.warn('Main switchTab failed, fallback used:', error);
                    }
                }
                return window.workspaceSwitchTabFallback(tab);
            };

            document.addEventListener('click', function (event) {
                const btn = event.target.closest('#workspace-tabs .of-tab[data-tab], .of-workspace-side-nav .of-tab[data-tab], .of-sidebar-nav .of-tab[data-tab]');
                if (!btn) return;
                event.preventDefault();
                event.stopPropagation();
                window.switchWorkspaceTab(btn.dataset.tab);
            }, true);

            document.addEventListener('DOMContentLoaded', function () {
                let collapsed = false;
                try { collapsed = localStorage.getItem('offerWorkspaceSidebarCollapsed') === '1'; } catch (error) { }
                window.setWorkspaceSidebarCollapsed(collapsed);

                let activeTab = 'uebersicht';
                try { activeTab = localStorage.getItem('offerWorkspaceActiveTab') || 'uebersicht'; } catch (error) { }
                if (!document.getElementById('panel-' + activeTab)) activeTab = 'uebersicht';
                window.workspaceSwitchTabFallback(activeTab);
            });

            (() => {
                const folderApp = document.getElementById('folder-app');
                if (!folderApp) return;

                const OFFER_STATUS_KEYS = [
                    'lead_anfrage',
                    'erstkontakt',
                    'beratung_geplant',
                    'beratung_durchgefuehrt',
                    'daten_unterlagen_fehlen',
                    'technische_pruefung',
                    'angebot_in_erstellung',
                    'angebot_versendet',
                    'rueckfrage_nachbearbeitung',
                    'warten_auf_entscheidung',
                    'angebot_angenommen',
                    'angebot_abgelehnt',
                    'angebot_pausiert'
                ];

                const DEAL_STATUS_KEYS = [
                    'auftrag_erhalten',
                    'auftragspruefung',
                    'vertrag_bestaetigung_versendet',
                    'anzahlung_offen',
                    'anzahlung_erhalten',
                    'materialplanung',
                    'material_bestellt',
                    'material_vollstaendig_verfuegbar',
                    'montage_terminplanung',
                    'montagetermin_bestaetigt',
                    'in_ausfuehrung',
                    'montage_abgeschlossen',
                    'abnahme_qualitaetskontrolle',
                    'rechnung_erstellt',
                    'rechnung_bezahlt',
                    'abgeschlossen',
                    'problem_reklamation'
                ];

                const OFFER_STATUS_LABELS = {
                    lead_anfrage: 'Lead / Anfrage',
                    erstkontakt: 'Erstkontakt',
                    beratung_geplant: 'Beratung geplant',
                    beratung_durchgefuehrt: 'Beratung durchgeführt',
                    daten_unterlagen_fehlen: 'Daten / Unterlagen fehlen',
                    technische_pruefung: 'Technische Prüfung',
                    angebot_in_erstellung: 'Angebot in Erstellung',
                    angebot_versendet: 'Angebot versendet',
                    rueckfrage_nachbearbeitung: 'Rückfrage / Nachbearbeitung',
                    warten_auf_entscheidung: 'Warten auf Entscheidung',
                    angebot_angenommen: 'Angebot angenommen',
                    angebot_abgelehnt: 'Angebot abgelehnt',
                    angebot_pausiert: 'Angebot pausiert',

                    // Backward compatibility for older saved folders
                    draft: 'Lead / Anfrage',
                    pending_approval: 'Angebot in Erstellung',
                    sent: 'Angebot versendet',
                    viewed: 'Rückfrage / Nachbearbeitung',
                    negotiation: 'Warten auf Entscheidung',
                    revised: 'Rückfrage / Nachbearbeitung',
                    accepted: 'Angebot angenommen',
                    rejected: 'Angebot abgelehnt',
                    expired: 'Angebot pausiert',
                    cancelled: 'Angebot abgelehnt'
                };

                const DEAL_STATUS_LABELS = {
                    auftrag_erhalten: 'Auftrag erhalten',
                    auftragspruefung: 'Auftragsprüfung',
                    vertrag_bestaetigung_versendet: 'Vertrag / Bestätigung versendet',
                    anzahlung_offen: 'Anzahlung offen',
                    anzahlung_erhalten: 'Anzahlung erhalten',
                    materialplanung: 'Materialplanung',
                    material_bestellt: 'Material bestellt',
                    material_vollstaendig_verfuegbar: 'Material vollständig verfügbar',
                    montage_terminplanung: 'Montage / Terminplanung',
                    montagetermin_bestaetigt: 'Montagetermin bestätigt',
                    in_ausfuehrung: 'In Ausführung',
                    montage_abgeschlossen: 'Montage abgeschlossen',
                    abnahme_qualitaetskontrolle: 'Abnahme / Qualitätskontrolle',
                    rechnung_erstellt: 'Rechnung erstellt',
                    rechnung_bezahlt: 'Rechnung bezahlt',
                    abgeschlossen: 'Abgeschlossen',
                    problem_reklamation: 'Problem / Reklamation',

                    // Backward compatibility for older saved folders
                    open: 'Auftrag erhalten',
                    qualified: 'Auftragsprüfung',
                    proposal: 'Vertrag / Bestätigung versendet',
                    negotiation: 'In Ausführung',
                    won: 'Abgeschlossen',
                    lost: 'Problem / Reklamation',
                    on_hold: 'Anzahlung offen'
                };

                const DOCUMENT_STATUS_LABELS = {
                    offer: 'Angebot',
                    deal: 'Auftrag'
                };

                const OFFER_MAIN_FLOW_KEYS = OFFER_STATUS_KEYS;

                const OFFER_SIDE_STATUS_KEYS = [];

                const DEAL_MAIN_FLOW_KEYS = DEAL_STATUS_KEYS;

                const DEAL_SIDE_STATUS_KEYS = [];

                function getWorkflowMainFlowKeys() {
                    return getDocumentStatus() === 'deal'
                        ? DEAL_MAIN_FLOW_KEYS
                        : OFFER_MAIN_FLOW_KEYS;
                }

                function getWorkflowSideStatusKeys() {
                    return getDocumentStatus() === 'deal'
                        ? DEAL_SIDE_STATUS_KEYS
                        : OFFER_SIDE_STATUS_KEYS;
                }

                function getWorkflowDocumentKey(documentStatus = getDocumentStatus()) {
                    return documentStatus === 'deal' ? 'deal' : 'offer';
                }

                function normalizeKanbanStage(stage) {
                    if (!stage) return null;
                    const key = String(stage.key || '').trim().toLowerCase();
                    if (!key) return null;

                    return {
                        id: stage.id || stage.lead_stage_sub_stage_id || null,
                        lead_stage_id: stage.lead_stage_id || null,
                        lead_stage_sub_stage_id: stage.lead_stage_sub_stage_id || stage.id || null,
                        document_status: getWorkflowDocumentKey(stage.document_status || getDocumentStatus()),
                        key,
                        label: String(stage.label || stage.name || stage.title || key).trim(),
                        name: String(stage.name || stage.label || stage.title || key).trim(),
                        icon: stage.icon || null,
                        color: stage.color || '#93c21c',
                        position: Number(stage.position || stage.sort_order || 0),
                        sort_order: Number(stage.sort_order || stage.position || 0),
                        is_active: stage.is_active !== false && stage.active !== false,
                        is_system: Boolean(stage.is_system),
                        is_default: Boolean(stage.is_default),
                        source: stage.source || 'lead_stage_sub_stages',
                    };
                }

                function setKanbanStagesFromPayload(stages, documentStatus = getDocumentStatus()) {
                    const doc = getWorkflowDocumentKey(documentStatus);
                    const list = safeArray(stages).map(normalizeKanbanStage).filter(Boolean);
                    state.kanbanStages[doc] = list.sort((a, b) => Number(a.position || 0) - Number(b.position || 0));
                }

                function getConfiguredKanbanStages(documentStatus = getDocumentStatus(), includeInactive = false) {
                    const doc = getWorkflowDocumentKey(documentStatus);
                    const configured = safeArray(state.kanbanStages?.[doc]).filter(Boolean);

                    if (configured.length) {
                        return configured.filter(stage => includeInactive || stage.is_active !== false);
                    }

                    const keys = doc === 'deal' ? DEAL_STATUS_KEYS : OFFER_STATUS_KEYS;
                    const labels = doc === 'deal' ? DEAL_STATUS_LABELS : OFFER_STATUS_LABELS;
                    return keys.map((key, index) => ({
                        id: null,
                        document_status: doc,
                        key,
                        label: labels[key] || key,
                        icon: null,
                        color: '#93c21c',
                        position: index + 1,
                        is_active: true,
                        is_system: false,
                        is_default: index === 0,
                    }));
                }

                function getWorkflowStatusKeys() {
                    return getConfiguredKanbanStages().map(stage => stage.key);
                }

                function getWorkflowStatusLabels() {
                    const labels = getDocumentStatus() === 'deal'
                        ? { ...DEAL_STATUS_LABELS }
                        : { ...OFFER_STATUS_LABELS };

                    getConfiguredKanbanStages(getDocumentStatus(), true).forEach(stage => {
                        labels[stage.key] = stage.label;
                    });

                    return labels;
                }

                function buildWorkflowStatusLabel(status) {
                    const labels = getWorkflowStatusLabels();
                    return labels[String(status || '').toLowerCase()] || status || '-';
                }

                function buildStatusLabel(status) {
                    return buildWorkflowStatusLabel(status);
                }


                function replaceUrlTemplate(template, replacements = {}) {
                    let url = String(template || '');
                    Object.entries(replacements).forEach(([key, value]) => {
                        url = url.replaceAll(`__${key}__`, encodeURIComponent(String(value ?? '')));
                    });
                    return url;
                }

                function getAvailableWorkflowMainStages() {
                    try {
                        const raw = folderApp.dataset.kanbanAvailableStages || '[]';
                        const parsed = JSON.parse(raw);
                        return Array.isArray(parsed) ? parsed : [];
                    } catch (error) {
                        return [];
                    }
                }

                function getManualWorkflowStageId(documentStatus = getDocumentStatus()) {
                    const doc = getWorkflowDocumentKey(documentStatus);
                    try {
                        return localStorage.getItem(`offerFolderWorkflowMainStage.${doc}`) || '';
                    } catch (error) {
                        return '';
                    }
                }

                function setManualWorkflowStageId(documentStatus, stageId) {
                    const doc = getWorkflowDocumentKey(documentStatus);
                    const value = String(stageId || '').trim();
                    if (doc === 'deal') {
                        folderApp.dataset.kanbanDealStageId = value;
                    } else {
                        folderApp.dataset.kanbanOfferStageId = value;
                    }
                    try {
                        localStorage.setItem(`offerFolderWorkflowMainStage.${doc}`, value);
                    } catch (error) { }
                }

                function getWorkflowMainStageId(documentStatus = getDocumentStatus()) {
                    const doc = getWorkflowDocumentKey(documentStatus);
                    const detected = doc === 'deal'
                        ? String(folderApp.dataset.kanbanDealStageId || '').trim()
                        : String(folderApp.dataset.kanbanOfferStageId || '').trim();
                    return detected || getManualWorkflowStageId(doc);
                }

                function getSubStageIndexUrl(documentStatus = getDocumentStatus()) {
                    const stageId = getWorkflowMainStageId(documentStatus);
                    if (!stageId) return '';
                    return replaceUrlTemplate(folderApp.dataset.kanbanSubstageIndexUrlTemplate, { STAGE: stageId });
                }

                function getSubStageStoreUrl(documentStatus = getDocumentStatus()) {
                    const stageId = getWorkflowMainStageId(documentStatus);
                    if (!stageId) return '';
                    return replaceUrlTemplate(folderApp.dataset.kanbanSubstageStoreUrlTemplate, { STAGE: stageId });
                }

                function getSubStageReorderUrl(documentStatus = getDocumentStatus()) {
                    const stageId = getWorkflowMainStageId(documentStatus);
                    if (!stageId) return '';
                    return replaceUrlTemplate(folderApp.dataset.kanbanSubstageReorderUrlTemplate, { STAGE: stageId });
                }

                function getSubStageUpdateUrl(subStageId) {
                    return replaceUrlTemplate(folderApp.dataset.kanbanSubstageUpdateUrlTemplate, { SUBSTAGE: subStageId });
                }

                function getSubStageDeleteUrl(subStageId) {
                    return replaceUrlTemplate(folderApp.dataset.kanbanSubstageDeleteUrlTemplate, { SUBSTAGE: subStageId });
                }

                function getSubStageToggleUrl(subStageId) {
                    return replaceUrlTemplate(folderApp.dataset.kanbanSubstageToggleUrlTemplate, { SUBSTAGE: subStageId });
                }

                function getSubStageDefaultUrl(subStageId) {
                    return replaceUrlTemplate(folderApp.dataset.kanbanSubstageDefaultUrlTemplate, { SUBSTAGE: subStageId });
                }

                function renderWorkflowStageSelector(doc, message = '') {
                    const stages = getAvailableWorkflowMainStages();
                    const options = stages.map(stage => `
                                                                <option value="${esc(stage.id)}">${esc(stage.name || stage.label || stage.key || ('Stage #' + stage.id))} ${stage.key ? '(' + esc(stage.key) + ')' : ''}</option>
                                                            `).join('');

                    return `
                                                                <div class="of-empty" style="text-align:left;">
                                                                    <div style="font-weight:900;color:#111827;margin-bottom:8px;">
                                                                        ${esc(message || (doc === 'deal'
                        ? 'Die Hauptphase für Auftrag wurde nicht automatisch erkannt.'
                        : 'Die Hauptphase für Angebot wurde nicht automatisch erkannt.'))}
                                                                    </div>
                                                                    <div style="font-size:12px;line-height:1.6;color:#6b7280;margin-bottom:12px;">
                                                                        Bitte wählen Sie, welche Hauptphase als Quelle für diese Unterphasen verwendet werden soll.
                                                                        Danach werden die Unterphasen direkt aus <strong>lead_stage_sub_stages</strong> geladen.
                                                                    </div>
                                                                    <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
                                                                        <select id="workflow-main-stage-select" class="of-kanban-config-input" style="max-width:360px;">
                                                                            <option value="">Hauptphase auswählen...</option>
                                                                            ${options}
                                                                        </select>
                                                                        <button type="button" class="of-btn" onclick="saveWorkflowMainStageSelection()">Verwenden</button>
                                                                    </div>
                                                                </div>
                                                            `;
                }

                window.saveWorkflowMainStageSelection = async function () {
                    const select = document.getElementById('workflow-main-stage-select');
                    const doc = getWorkflowDocumentKey(state.kanbanConfigDocument || getDocumentStatus());
                    const stageId = String(select?.value || '').trim();
                    if (!stageId) {
                        showCustomToast('Fehlt', 'Bitte wählen Sie eine Hauptphase.', 'error');
                        return;
                    }
                    setManualWorkflowStageId(doc, stageId);
                    setKanbanStagesFromPayload([], doc);
                    await renderKanbanConfigModal();
                    await loadKanbanStages(doc, false).catch(() => []);
                    renderKanban();
                    showCustomToast('Gespeichert', 'Diese Hauptphase wird jetzt für den Ordner-Workflow verwendet.');
                };

                async function loadKanbanStages(documentStatus = getDocumentStatus(), includeInactive = false) {
                    const doc = getWorkflowDocumentKey(documentStatus);
                    const base = getSubStageIndexUrl(doc);

                    if (!base) {
                        const error = new Error(doc === 'deal'
                            ? 'Die Hauptphase für Auftrag wurde nicht erkannt.'
                            : 'Die Hauptphase für Angebot wurde nicht erkannt.');
                        error.needs_stage_mapping = true;
                        throw error;
                    }

                    const params = new URLSearchParams();
                    if (includeInactive) params.set('include_inactive', '1');

                    try {
                        const json = await fetchJson(`${base}${params.toString() ? '?' + params.toString() : ''}`);
                        if (!json.success) throw new Error(json.message || 'Kanban-Unterphasen konnten nicht geladen werden.');

                        const stages = json.stages || json.sub_stages || json.subStages || [];
                        setKanbanStagesFromPayload(stages, doc);
                    } catch (error) {
                        const existingStages = getConfiguredKanbanStages(doc, includeInactive);
                        if (existingStages.length) {
                            console.warn('Kanban-Unterphasen wurden aus Blade-Daten geladen, weil die JSON-Route nicht antwortet.', error);
                            return existingStages;
                        }
                        throw error;
                    }

                    return getConfiguredKanbanStages(doc, includeInactive);
                }

                window.openKanbanConfigModal = async function (documentStatus = null) {
                    const modal = document.getElementById('kanban-config-modal');
                    if (!modal) return;
                    state.kanbanConfigDocument = getWorkflowDocumentKey(documentStatus || getDocumentStatus());
                    modal.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                    await renderKanbanConfigModal();
                };

                window.switchKanbanConfigDocument = async function (documentStatus) {
                    state.kanbanConfigDocument = getWorkflowDocumentKey(documentStatus);
                    await renderKanbanConfigModal();
                };

                window.closeKanbanConfigModal = function () {
                    const modal = document.getElementById('kanban-config-modal');
                    if (modal) modal.style.display = 'none';
                    document.body.style.overflow = '';
                };

                async function renderKanbanConfigModal() {
                    const list = document.getElementById('kanban-config-list');
                    const context = document.getElementById('kanban-config-context');
                    if (!list) return;

                    const doc = getWorkflowDocumentKey(state.kanbanConfigDocument || getDocumentStatus());
                    state.kanbanConfigDocument = doc;
                    const mainStageId = getWorkflowMainStageId(doc);

                    if (context) {
                        context.textContent = doc === 'deal'
                            ? `Auftrag-Unterphasen${mainStageId ? ' · Stage #' + mainStageId : ''}`
                            : `Angebot-Unterphasen${mainStageId ? ' · Stage #' + mainStageId : ''}`;
                    }

                    const offerBtn = document.getElementById('kanban-config-offer-btn');
                    const dealBtn = document.getElementById('kanban-config-deal-btn');
                    if (offerBtn) offerBtn.classList.toggle('primary', doc === 'offer');
                    if (dealBtn) dealBtn.classList.toggle('primary', doc === 'deal');

                    if (!mainStageId) {
                        list.innerHTML = renderWorkflowStageSelector(doc);
                        return;
                    }

                    list.innerHTML = '<div class="of-empty">Unterphasen werden geladen...</div>';
                    try {
                        await loadKanbanStages(doc, true);
                    } catch (error) {
                        if (error.needs_stage_mapping) {
                            list.innerHTML = renderWorkflowStageSelector(doc, error.message || 'Hauptphase fehlt.');
                        } else {
                            list.innerHTML = `<div class="of-empty">${esc(error.message || error)}</div>`;
                        }
                        return;
                    }

                    const stages = getConfiguredKanbanStages(doc, true);
                    if (!stages.length) {
                        list.innerHTML = `
                                                                    <div class="of-empty" style="text-align:left;">
                                                                        <strong>Keine Unterphasen gefunden.</strong><br>
                                                                        Erstelle unten eine neue Unterphase. Sie wird direkt in <code>lead_stage_sub_stages</code> gespeichert.
                                                                    </div>
                                                                `;
                        return;
                    }

                    list.innerHTML = stages.map(stage => `
                                                                <div class="of-kanban-config-row ${stage.is_active ? '' : 'is-disabled'}" data-stage-id="${esc(stage.id || '')}" data-stage-key="${esc(stage.key)}">
                                                                    <div class="of-kanban-drag-handle" title="Ziehen zum Sortieren">⋮⋮</div>
                                                                    <input class="of-kanban-config-input" value="${esc(stage.label)}" data-stage-field="name" placeholder="Unterphase">
                                                                    <input class="of-kanban-config-input" value="${esc(stage.icon || '')}" data-stage-field="icon" placeholder="Icon">
                                                                    <input class="of-kanban-config-input" type="color" value="${esc(stage.color || '#93c21c')}" data-stage-field="color" style="padding:3px;">
                                                                    <div class="of-kanban-config-actions">
                                                                        <button type="button" class="of-kanban-mini-btn" onclick="saveKanbanStageRow(this)">Save</button>
                                                                        <button type="button" class="of-kanban-mini-btn" onclick="toggleKanbanStageRow(this)">${stage.is_active ? 'Aus' : 'Ein'}</button>
                                                                        <button type="button" class="of-kanban-mini-btn" onclick="makeKanbanStageDefault(this)" ${stage.is_default ? 'disabled title="Bereits Standard"' : ''}>Default</button>
                                                                        <button type="button" class="of-kanban-mini-btn danger" onclick="deleteKanbanStageRow(this)">Del</button>
                                                                    </div>
                                                                </div>
                                                            `).join('');

                    if (window.Sortable) {
                        Sortable.create(list, {
                            handle: '.of-kanban-drag-handle',
                            animation: 150,
                            onEnd: saveKanbanStageOrder,
                        });
                    }
                }

                window.createKanbanStage = async function () {
                    const labelEl = document.getElementById('kanban-new-label');
                    const iconEl = document.getElementById('kanban-new-icon');
                    const colorEl = document.getElementById('kanban-new-color');
                    const doc = getWorkflowDocumentKey(state.kanbanConfigDocument || getDocumentStatus());
                    const label = String(labelEl?.value || '').trim();
                    if (!label) {
                        showCustomToast('Fehlt', 'Bitte geben Sie einen Namen für die Unterphase ein.', 'error');
                        return;
                    }

                    const url = getSubStageStoreUrl(doc);
                    if (!url) {
                        const list = document.getElementById('kanban-config-list');
                        if (list) list.innerHTML = renderWorkflowStageSelector(doc);
                        return;
                    }

                    try {
                        const json = await fetchJson(url, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                            body: JSON.stringify({
                                name: label,
                                icon: iconEl?.value || null,
                                color: colorEl?.value || '#93c21c',
                                is_active: true,
                            })
                        });

                        if (!json.success) throw new Error(json.message || 'Unterphase konnte nicht erstellt werden.');
                        if (labelEl) labelEl.value = '';
                        if (iconEl) iconEl.value = '';
                        await loadKanbanStages(doc, true);
                        await renderKanbanConfigModal();
                        renderKanban();
                        showCustomToast('Gespeichert', 'Unterphase wurde erstellt.');
                    } catch (error) {
                        showCustomToast('Fehler', error.message || 'Unterphase konnte nicht erstellt werden.', 'error');
                    }
                };

                function getKanbanStageRowPayload(btn) {
                    const row = btn.closest('[data-stage-id]');
                    if (!row) return null;
                    const payload = {};
                    row.querySelectorAll('[data-stage-field]').forEach(input => {
                        payload[input.dataset.stageField] = input.value;
                    });
                    return { row, id: row.dataset.stageId, payload };
                }

                window.saveKanbanStageRow = async function (btn) {
                    const data = getKanbanStageRowPayload(btn);
                    if (!data?.id) return;
                    const doc = getWorkflowDocumentKey(state.kanbanConfigDocument || getDocumentStatus());
                    try {
                        const json = await fetchJson(getSubStageUpdateUrl(data.id), {
                            method: 'PUT',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                            body: JSON.stringify(data.payload)
                        });
                        if (!json.success) throw new Error(json.message || 'Unterphase konnte nicht gespeichert werden.');
                        await loadKanbanStages(doc, true);
                        await renderKanbanConfigModal();
                        renderKanban();
                        showCustomToast('Gespeichert', 'Unterphase wurde aktualisiert.');
                    } catch (error) {
                        showCustomToast('Fehler', error.message || 'Unterphase konnte nicht gespeichert werden.', 'error');
                    }
                };

                window.toggleKanbanStageRow = async function (btn) {
                    const data = getKanbanStageRowPayload(btn);
                    if (!data?.id) return;
                    const doc = getWorkflowDocumentKey(state.kanbanConfigDocument || getDocumentStatus());
                    try {
                        const json = await fetchJson(getSubStageToggleUrl(data.id), {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                        });
                        if (!json.success) throw new Error(json.message || 'Status konnte nicht geändert werden.');
                        await loadKanbanStages(doc, true);
                        await renderKanbanConfigModal();
                        renderKanban();
                    } catch (error) {
                        showCustomToast('Fehler', error.message || 'Status konnte nicht geändert werden.', 'error');
                    }
                };

                window.makeKanbanStageDefault = async function (btn) {
                    const data = getKanbanStageRowPayload(btn);
                    if (!data?.id) return;
                    const doc = getWorkflowDocumentKey(state.kanbanConfigDocument || getDocumentStatus());
                    try {
                        const json = await fetchJson(getSubStageDefaultUrl(data.id), {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                        });
                        if (!json.success) throw new Error(json.message || 'Standard konnte nicht gesetzt werden.');
                        await loadKanbanStages(doc, true);
                        await renderKanbanConfigModal();
                        renderKanban();
                    } catch (error) {
                        showCustomToast('Fehler', error.message || 'Standard konnte nicht gesetzt werden.', 'error');
                    }
                };

                window.deleteKanbanStageRow = async function (btn) {
                    const data = getKanbanStageRowPayload(btn);
                    if (!data?.id) return;
                    if (!confirm('Diese Unterphase wirklich löschen?')) return;
                    const doc = getWorkflowDocumentKey(state.kanbanConfigDocument || getDocumentStatus());

                    try {
                        const json = await fetchJson(getSubStageDeleteUrl(data.id), {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': getCsrfToken() }
                        });
                        if (!json.success) throw new Error(json.message || 'Unterphase konnte nicht gelöscht werden.');
                        await loadKanbanStages(doc, true);
                        await renderKanbanConfigModal();
                        renderKanban();
                    } catch (error) {
                        showCustomToast('Fehler', error.message || 'Unterphase konnte nicht gelöscht werden.', 'error');
                    }
                };

                async function saveKanbanStageOrder() {
                    const doc = getWorkflowDocumentKey(state.kanbanConfigDocument || getDocumentStatus());
                    const url = getSubStageReorderUrl(doc);
                    if (!url) return;

                    const items = Array.from(document.querySelectorAll('#kanban-config-list [data-stage-id]'))
                        .map((row, index) => ({
                            id: Number(row.dataset.stageId || 0),
                            sort_order: (index + 1) * 10,
                        }))
                        .filter(item => item.id > 0);

                    if (!items.length) return;

                    try {
                        const json = await fetchJson(url, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': getCsrfToken() },
                            body: JSON.stringify({ items })
                        });
                        if (!json.success) throw new Error(json.message || 'Reihenfolge konnte nicht gespeichert werden.');
                        await loadKanbanStages(doc, true);
                        renderKanban();
                    } catch (error) {
                        showCustomToast('Fehler', error.message || 'Reihenfolge konnte nicht gespeichert werden.', 'error');
                    }
                }

                function getStatusVisualClass(status) {
                    const key = String(status || '').toLowerCase();

                    const map = {
                        draft: 'draft',
                        pending_approval: 'pending',
                        sent: 'sent',
                        viewed: 'viewed',
                        negotiation: 'negotiation',
                        revised: 'revised',
                        accepted: 'final',
                        rejected: 'cancel',
                        expired: 'expired',
                        cancelled: 'cancel',

                        lead_anfrage: 'draft',
                        erstkontakt: 'pending',
                        beratung_geplant: 'sent',
                        beratung_durchgefuehrt: 'viewed',
                        daten_unterlagen_fehlen: 'expired',
                        technische_pruefung: 'viewed',
                        angebot_in_erstellung: 'revised',
                        angebot_versendet: 'sent',
                        rueckfrage_nachbearbeitung: 'revised',
                        warten_auf_entscheidung: 'negotiation',
                        angebot_angenommen: 'final',
                        angebot_abgelehnt: 'cancel',
                        angebot_pausiert: 'expired',

                        open: 'draft',
                        qualified: 'sent',
                        proposal: 'viewed',
                        won: 'final',
                        lost: 'cancel',
                        on_hold: 'pending',
                        auftrag_erhalten: 'draft',
                        auftragspruefung: 'sent',
                        vertrag_bestaetigung_versendet: 'viewed',
                        anzahlung_offen: 'pending',
                        anzahlung_erhalten: 'sent',
                        materialplanung: 'viewed',
                        material_bestellt: 'sent',
                        material_vollstaendig_verfuegbar: 'final',
                        montage_terminplanung: 'revised',
                        montagetermin_bestaetigt: 'sent',
                        in_ausfuehrung: 'negotiation',
                        montage_abgeschlossen: 'final',
                        abnahme_qualitaetskontrolle: 'viewed',
                        rechnung_erstellt: 'sent',
                        rechnung_bezahlt: 'final',
                        abgeschlossen: 'final',
                        problem_reklamation: 'cancel'
                    };

                    return map[key] || 'draft';
                }

                function normalizeWorkflowStatusForDocument(rawStatus, documentStatus = getDocumentStatus()) {
                    const doc = getWorkflowDocumentKey(documentStatus);
                    const raw = String(rawStatus || '').trim().toLowerCase();

                    /*
                     * Important:
                     * The folder Kanban no longer uses the old fixed offer/deal status arrays as source of truth.
                     * Its real columns are LeadStageSubStage records loaded into state.kanbanStages.
                     *
                     * Before this fix, old folder statuses like "draft" were mapped to static keys such as
                     * "lead_anfrage". If your real Angebot sub-stages use different keys, the current card
                     * did not match any rendered column, so the Kanban looked empty.
                     */
                    const configuredStages = safeArray(state.kanbanStages?.[doc]).filter(Boolean);
                    const configuredKeys = configuredStages.map(stage => String(stage.key || '').toLowerCase()).filter(Boolean);

                    if (configuredKeys.includes(raw)) {
                        return raw;
                    }

                    const offerMap = {
                        draft: 'lead_anfrage',
                        entwurf: 'lead_anfrage',
                        pending_approval: 'angebot_in_erstellung',
                        sent: 'angebot_versendet',
                        viewed: 'rueckfrage_nachbearbeitung',
                        negotiation: 'warten_auf_entscheidung',
                        revised: 'rueckfrage_nachbearbeitung',
                        accepted: 'angebot_angenommen',
                        final: 'angebot_angenommen',
                        rejected: 'angebot_abgelehnt',
                        cancel: 'angebot_abgelehnt',
                        cancelled: 'angebot_abgelehnt',
                        expired: 'angebot_pausiert',
                        offer: 'lead_anfrage'
                    };

                    const dealMap = {
                        open: 'auftrag_erhalten',
                        draft: 'auftrag_erhalten',
                        qualified: 'auftragspruefung',
                        proposal: 'vertrag_bestaetigung_versendet',
                        negotiation: 'in_ausfuehrung',
                        won: 'abgeschlossen',
                        final: 'abgeschlossen',
                        lost: 'problem_reklamation',
                        cancel: 'problem_reklamation',
                        cancelled: 'problem_reklamation',
                        on_hold: 'anzahlung_offen',
                        deal: 'auftrag_erhalten',
                        auftrag: 'auftrag_erhalten'
                    };

                    const mapped = doc === 'deal' ? dealMap[raw] : offerMap[raw];

                    if (configuredKeys.length) {
                        if (mapped && configuredKeys.includes(mapped)) {
                            return mapped;
                        }

                        const defaultStage = configuredStages.find(stage => stage.is_default) || configuredStages[0];

                        return String(defaultStage?.key || configuredKeys[0] || raw || '').toLowerCase();
                    }

                    const fallbackKeys = doc === 'deal' ? DEAL_STATUS_KEYS : OFFER_STATUS_KEYS;

                    if (fallbackKeys.includes(raw)) {
                        return raw;
                    }

                    return mapped && fallbackKeys.includes(mapped) ? mapped : fallbackKeys[0];
                }

                function getWorkflowStatus() {
                    if (getDocumentStatus() === 'deal') {
                        return normalizeWorkflowStatusForDocument(state.folder?.deal_status || state.folder?.workflow_status || state.folder?.status || 'auftrag_erhalten', 'deal');
                    }
                    return normalizeWorkflowStatusForDocument(state.folder?.offer_status || state.folder?.workflow_status || state.folder?.status || 'lead_anfrage', 'offer');
                }

                function getDocumentStatus() {
                    const raw = String(
                        state.detail?.document_status ||
                        state.folder?.document_status ||
                        state.offer?.document_status ||
                        'offer'
                    ).toLowerCase();

                    // In this UI, Auftrag is rendered with the deal workflow columns.
                    return raw === 'auftrag' ? 'deal' : raw;
                }

                function getPrintCustomerName() {
                    const customer = state.offer?.customer || {};

                    const fullName = [
                        customer.name || '',
                        customer.lastname || ''
                    ].join(' ').replace(/\s+/g, ' ').trim();

                    return fullName || customer.firma || 'Unbekannt';
                }

                function getPrintCustomerAddress() {
                    const customer = state.offer?.customer || {};

                    const street = customer.street || customer.address || '';
                    const cityLine = [
                        customer.postal_code || customer.zip || '',
                        customer.city || ''
                    ].join(' ').replace(/\s+/g, ' ').trim();

                    return [street, cityLine].filter(Boolean).join(', ') || '-';
                }

                function getPrintListTitle(filter) {
                    const key = String(filter || 'all').toLowerCase();

                    const map = {
                        offen: 'Offenliste',
                        lager: 'Lagerliste',
                        bestellen: 'Bestellliste',
                        final: 'Kommissionen Materialliste',
                        all: 'Materialliste'
                    };

                    return map[key] || 'Materialliste';
                }
                function getDocumentStatusLabel(status) {
                    return DOCUMENT_STATUS_LABELS[String(status || 'offer').toLowerCase()] || 'Angebot';
                }

                function isExecutionDocumentStatus() {
                    const status = getDocumentStatus();
                    return status === 'offer' || status === 'deal';
                }

                const initialAttachments = @json($initialAttachments);

                const state = {
                    folder: @json($folder),
                    offer: @json($offer),
                    detail: @json($detail),
                    sections: [],
                    distributors: {},
                    currentTab: 'uebersicht',
                    materialFilter: 'all',
                    materialTableCols: {
                        image: true,
                        position: true,
                        article_no: true,
                        distributor_article_no: true,
                        distributor: true,
                        type: false,
                        status: false,
                        qty: true,
                        qty_total: true,
                        unit: true,
                        ek_price: false,
                        ek_total: false,
                        unit_price: false,
                        total: false,
                        margin: false,
                        db_total: false
                    },
                    materialMove: {
                        rows: [],
                        moveTo: null,
                        mode: 'single'
                    },

                    materialFinal: {
                        rows: [],
                        sourceStatus: null,
                        availableQty: 0
                    },


                    presenceUsers: [],
                    comparisonCharts: [],
                    attachments: Array.isArray(initialAttachments) ? initialAttachments : [],
                    teamAccess: null,
                    kanbanStages: { offer: [], deal: [] },
                    kanbanInitialStagesLoaded: false,
                    kanbanConfigDocument: null,
                    smartSidebar: {
                        visible: false,
                        summary: null
                    },
                    materialDetail: {
                        rowIndex: null,
                        rowData: null,
                        comparison: null,
                        selectedOption: null
                    }
                };


                function bootInitialKanbanStagesFromDataset() {
                    if (!state || state.kanbanInitialStagesLoaded) return;
                    state.kanbanInitialStagesLoaded = true;

                    try {
                        const offerStages = JSON.parse(folderApp.dataset.kanbanOfferStages || '[]');
                        if (Array.isArray(offerStages) && offerStages.length) {
                            setKanbanStagesFromPayload(offerStages, 'offer');
                        }
                    } catch (error) { }

                    try {
                        const dealStages = JSON.parse(folderApp.dataset.kanbanDealStages || '[]');
                        if (Array.isArray(dealStages) && dealStages.length) {
                            setKanbanStagesFromPayload(dealStages, 'deal');
                        }
                    } catch (error) { }
                }

                bootInitialKanbanStagesFromDataset();

                function getOfferWorkflowStatus() {
                    return String(
                        state.folder?.offer_status ||
                        state.folder?.workflow_status ||
                        state.offer?.offer_status ||
                        state.offer?.status ||
                        state.folder?.status ||
                        'draft'
                    ).toLowerCase();
                }

                function isOfferLockedByWorkflow() {
                    const documentStatus = getDocumentStatus();
                    const offerWorkflowStatus = getOfferWorkflowStatus();

                    if (documentStatus !== 'offer') {
                        return false;
                    }

                    return ['angebot_angenommen', 'angebot_abgelehnt', 'accepted', 'cancelled'].includes(offerWorkflowStatus);
                }

                function getOfferLockReason() {
                    const status = getOfferWorkflowStatus();

                    if (status === 'angebot_angenommen' || status === 'accepted') {
                        return 'Dieses Angebot ist abgeschlossen und gesperrt, weil der Status auf „Angebot angenommen“ steht.';
                    }

                    if (status === 'angebot_abgelehnt' || status === 'cancelled') {
                        return 'Dieses Angebot ist gesperrt, weil der Status auf „Angebot abgelehnt“ steht.';
                    }

                    return '';
                }

                let documentStatusResolver = null;

                function openDocumentStatusModal(fromStatus, toStatus) {
                    const modal = document.getElementById('document-status-modal');
                    const title = document.getElementById('document-status-modal-title');
                    const sub = document.getElementById('document-status-modal-sub');
                    const change = document.getElementById('document-status-modal-change');
                    const reason = document.getElementById('document-status-modal-reason');
                    const warning = document.getElementById('document-status-modal-warning');
                    const error = document.getElementById('document-status-modal-error');
                    const confirmBtn = document.getElementById('document-status-modal-confirm-btn');

                    if (!modal || !change || !reason || !confirmBtn) {
                        return Promise.resolve(null);
                    }

                    const fromLabel = getDocumentStatusLabel(fromStatus);
                    const toLabel = getDocumentStatusLabel(toStatus);

                    title.textContent = 'Dokumentstatus ändern';
                    sub.textContent = `Bitte bestätigen Sie die Änderung von "${fromLabel}" auf "${toLabel}".`;
                    change.value = `${fromLabel} → ${toLabel}`;
                    reason.value = '';
                    if (error) error.style.display = 'none';

                    if (fromStatus !== 'offer' && toStatus === 'offer') {
                        warning.style.display = 'block';
                        warning.innerHTML = `
                                                                                Achtung: Beim Zurückwechseln auf <strong>Angebot</strong> wird der aktuelle Vorgang fachlich als
                                                                                <strong>storniert</strong> behandelt. Zusätzlich muss backend-seitig ein neues Angebot mit neuer ID
                                                                                erzeugt werden, wenn Sie wirklich eine neue Angebotsnummer möchten.
                                                                            `;
                    } else {
                        warning.style.display = 'none';
                        warning.innerHTML = '';
                    }

                    modal.style.display = 'flex';

                    return new Promise(resolve => {
                        documentStatusResolver = resolve;

                        confirmBtn.onclick = () => {
                            const value = String(reason.value || '').trim();
                            if (!value) {
                                if (error) error.style.display = 'block';
                                reason.focus();
                                return;
                            }

                            closeDocumentStatusModal({
                                from_status: fromStatus,
                                to_status: toStatus,
                                reason: value,
                                revert_to_offer: fromStatus !== 'offer' && toStatus === 'offer'
                            });
                        };
                    });
                }

                function closeDocumentStatusModal(result = null) {
                    const modal = document.getElementById('document-status-modal');
                    const reason = document.getElementById('document-status-modal-reason');
                    const error = document.getElementById('document-status-modal-error');

                    if (modal) modal.style.display = 'none';
                    if (reason) reason.value = '';
                    if (error) error.style.display = 'none';

                    if (typeof documentStatusResolver === 'function') {
                        documentStatusResolver(result);
                    }

                    documentStatusResolver = null;
                }

                window.closeDocumentStatusModal = closeDocumentStatusModal;




                async function changeDocumentStatusRequest(targetStatus) {
                    const currentStatus = getDocumentStatus();
                    const url = folderApp.dataset.documentStatusUrl;

                    if (!url) {
                        showCustomToast('Fehler', 'Route für Dokumentstatus nicht gefunden.', 'error');
                        return;
                    }

                    if (targetStatus === currentStatus) {
                        return;
                    }

                    const modalResult = await openDocumentStatusModal(currentStatus, targetStatus);
                    if (!modalResult) {
                        renderDocumentStatusToggle();
                        return;
                    }

                    try {
                        const json = await fetchJson(url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify({
                                document_status: modalResult.to_status,
                                reason: modalResult.reason,
                                revert_to_offer: modalResult.revert_to_offer ? 1 : 0
                            })
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Dokumentstatus konnte nicht geändert werden.');
                        }

                        // 🟢 IF BACKEND RETURNS A REDIRECT (BECAUSE IT CLONED), GO TO NEW CLONE
                        if (json.redirect_url) {
                            window.location.href = json.redirect_url;
                            return;
                        }

                        await loadFolderData();

                        showCustomToast(
                            'Dokumentstatus geändert',
                            `Status wurde auf "${getDocumentStatusLabel(modalResult.to_status)}" gesetzt.`
                        );
                    } catch (error) {
                        renderDocumentStatusToggle();
                        showCustomToast('Fehler', error.message || 'Dokumentstatus konnte nicht geändert werden.', 'error');
                    }
                }


                function renderOfferLockState() {
                    const locked = isOfferLockedByWorkflow();
                    const reason = getOfferLockReason();

                    const loadBtn = document.getElementById('btn-load-offer');
                    const note = document.getElementById('document-status-note');

                    // Angebot laden must stay usable
                    if (loadBtn) {
                        loadBtn.disabled = false;
                        loadBtn.style.pointerEvents = '';
                        loadBtn.style.opacity = '';
                        loadBtn.title = '';
                    }

                    // document toggle can stay usable too unless you want only this blocked
                    document.querySelectorAll('.of-doc-toggle').forEach(btn => {
                        btn.disabled = false;
                        btn.style.pointerEvents = '';
                        btn.style.opacity = '';
                        btn.title = '';
                    });

                    const access = getTeamAccess();
                    if (state.teamAccess && access.canEdit === false) {
                        if (loadBtn) {
                            loadBtn.dataset.accessAllowed = '0';
                            loadBtn.title = 'Nur Teammitglieder dürfen dieses Angebot bearbeiten.';
                        }
                        document.querySelectorAll('.of-doc-toggle').forEach(btn => {
                            btn.disabled = true;
                            btn.style.pointerEvents = 'none';
                            btn.style.opacity = '.55';
                            btn.title = 'Keine Bearbeitungsberechtigung.';
                        });
                    }

                    if (note) {
                        if (locked) {
                            note.className = 'of-doc-switch-note warning of-doc-switch-note-slim';
                            note.innerHTML = `
                                                                                    <strong>Hinweis:</strong> ${esc(reason)}
                                                                                    Sie können den Ordner weiterhin öffnen, ansehen, klonen und Material-/Lohnlisten prüfen.
                                                                                `;
                        } else {
                            note.className = 'of-doc-switch-note of-doc-switch-note-slim';
                            note.innerHTML = `
                                                                                    Im Status <strong>Angebot</strong> und <strong>Auftrag</strong> sind Änderungen an Material, Bezug, Lager, Bestellen, Offen und Kommissionen erlaubt.
                                                                                `;
                        }
                    }
                }

                function renderDocumentStatusToggle() {
                    const status = getDocumentStatus();
                    const badge = document.getElementById('document-status-badge');
                    const badgeLabel = document.getElementById('document-status-badge-label');
                    const note = document.getElementById('document-status-note');

                    document.querySelectorAll('.of-doc-toggle').forEach(btn => {
                        btn.classList.toggle('active', btn.dataset.docStatus === status);
                    });

                    if (badge) {
                        badge.classList.remove('offer', 'deal');
                        badge.classList.add(status);
                    }

                    if (badgeLabel) {
                        badgeLabel.textContent = getDocumentStatusLabel(status);
                    }

                    if (note) {
                        if (status === 'offer') {
                            note.className = 'of-doc-switch-note';
                            note.innerHTML = `
                                                                                    Im Status <strong>Angebot</strong> sind Materialänderungen, Preisvergleich,
                                                                                    Lager / Bestellen / Offen und Kommissionen erlaubt.
                                                                                `;
                        } else if (status === 'deal') {
                            note.className = 'of-doc-switch-note';
                            note.innerHTML = `
                                                                                    Im Status <strong>Auftrag</strong> sind Materialänderungen, Preisvergleich,
                                                                                    Lager / Bestellen / Offen und Kommissionen ebenfalls erlaubt.
                                                                                `;
                        }
                    }
                    renderOfferLockState();
                }
                let agbQuill = null;

                function getRowTotalQty(row) {
                    return Number(row.qty_total || row.qty || 0);
                }

                function getRowAllocation(row) {
                    const total = getRowTotalQty(row);

                    const raw = row.stock_allocation && typeof row.stock_allocation === 'object'
                        ? row.stock_allocation
                        : null;

                    let allocation = {
                        offen: 0,
                        lager: 0,
                        bestellen: 0,
                        final: 0
                    };

                    if (raw) {
                        allocation.offen = Number(raw.offen || 0);
                        allocation.lager = Number(raw.lager || 0);
                        allocation.bestellen = Number(raw.bestellen || 0);
                        allocation.final = Number(raw.final || 0);
                    } else {
                        const status = String(row.order_status || 'offen').toLowerCase();
                        if (allocation.hasOwnProperty(status)) {
                            allocation[status] = total;
                        } else {
                            allocation.offen = total;
                        }
                    }

                    const sum = allocation.offen + allocation.lager + allocation.bestellen + allocation.final;

                    if (sum <= 0 && total > 0) {
                        allocation.offen = total;
                    }

                    if (sum > total) {
                        const factor = total / sum;
                        allocation.offen *= factor;
                        allocation.lager *= factor;
                        allocation.bestellen *= factor;
                        allocation.final *= factor;
                    }

                    const roundedSum = allocation.offen + allocation.lager + allocation.bestellen + allocation.final;
                    const diff = total - roundedSum;

                    if (Math.abs(diff) > 0.0001) {
                        allocation.offen += diff;
                    }

                    return allocation;
                }

                function getRowQtyForFilter(row, filter) {
                    const allocation = getRowAllocation(row);

                    if (filter === 'all') {
                        return getRowTotalQty(row);
                    }

                    return Number(allocation[filter] || 0);
                }

                function safeHistory(value) {
                    if (Array.isArray(value)) return value;
                    return [];
                }

                function normalizeMaterialHistoryEntries(rawHistory) {
                    const list = Array.isArray(rawHistory) ? rawHistory : [];

                    const statusLabelMap = {
                        offen: 'Offen',
                        lager: 'Lager',
                        bestellen: 'Bestellen',
                        final: 'Final',
                        draft: 'Entwurf',
                        sent: 'Gesendet',
                        negotiation: 'Verhandlung',
                        cancel: 'Storniert'
                    };

                    const normalizeStatus = (value) => {
                        const key = String(value || '').trim().toLowerCase();
                        return statusLabelMap[key] || (value ? String(value) : '-');
                    };

                    const normalizeType = (entry) => {
                        const raw = String(
                            entry?.type ||
                            entry?.action ||
                            entry?.event ||
                            entry?.kind ||
                            ''
                        ).trim().toLowerCase();

                        const map = {
                            created: 'Erstellt',
                            create: 'Erstellt',
                            moved: 'Verschoben',
                            move: 'Verschoben',
                            status_changed: 'Status geändert',
                            allocation_changed: 'Verteilung geändert',
                            final_confirmed: 'Finale bestätigt',
                            distributor_changed: 'Lieferant geändert',
                            updated: 'Aktualisiert',
                            update: 'Aktualisiert'
                        };

                        return map[raw] || (raw ? raw.replaceAll('_', ' ') : 'Änderung');
                    };

                    const normalizeUserName = (entry) => {
                        const rawName =
                            entry?.changed_by_name ||
                            entry?.user_name ||
                            entry?.employee_name ||
                            entry?.changed_by?.name ||
                            entry?.changed_by?.full_name ||
                            entry?.creator_name ||
                            entry?.by ||
                            '';

                        const first =
                            entry?.changed_by?.name ||
                            entry?.user?.name ||
                            '';

                        const last =
                            entry?.changed_by?.lastname ||
                            entry?.user?.lastname ||
                            '';

                        const combined = `${first} ${last}`.replace(/\s+/g, ' ').trim();

                        const name = String(rawName || combined || '').trim();
                        return name || 'Unbekannt';
                    };

                    return list
                        .map((entry, index) => {
                            const qty =
                                entry?.qty ??
                                entry?.move_qty ??
                                entry?.final_qty ??
                                entry?.quantity ??
                                entry?.amount ??
                                0;

                            const fromRaw =
                                entry?.from_status ??
                                entry?.from ??
                                entry?.old_status ??
                                entry?.source_status ??
                                '';

                            const toRaw =
                                entry?.to_status ??
                                entry?.to ??
                                entry?.new_status ??
                                entry?.target_status ??
                                '';

                            return {
                                _index: index,
                                type_label: normalizeType(entry),
                                from_label: normalizeStatus(fromRaw),
                                to_label: normalizeStatus(toRaw),
                                from_raw: fromRaw || '',
                                to_raw: toRaw || '',
                                qty: Number(qty || 0),
                                reason: String(
                                    entry?.reason ||
                                    entry?.note ||
                                    entry?.message ||
                                    entry?.comment ||
                                    ''
                                ).trim(),
                                changed_by_name: normalizeUserName(entry),
                                created_at:
                                    entry?.created_at ||
                                    entry?.changed_at ||
                                    entry?.date ||
                                    entry?.datetime ||
                                    entry?.at ||
                                    null,
                                raw: entry
                            };
                        })
                        .sort((a, b) => {
                            const aTime = a.created_at ? new Date(a.created_at).getTime() : 0;
                            const bTime = b.created_at ? new Date(b.created_at).getTime() : 0;
                            return bTime - aTime;
                        });
                }

                function switchMaterialModalTab(tab) {
                    document.querySelectorAll('[data-material-modal-tab]').forEach(btn => {
                        btn.classList.toggle('active', btn.dataset.materialModalTab === tab);
                    });

                    document.querySelectorAll('.of-modal-tab-panel').forEach(panel => {
                        panel.classList.toggle('active', panel.id === `material-modal-panel-${tab}`);
                    });
                }

                function initMaterialModalTabs() {
                    document.querySelectorAll('[data-material-modal-tab]').forEach(btn => {
                        if (btn.dataset.ready === '1') return;

                        btn.addEventListener('click', () => {
                            switchMaterialModalTab(btn.dataset.materialModalTab || 'vergleich');
                        });

                        btn.dataset.ready = '1';
                    });
                }

                function buildMaterialHistoryHtml(materialHistory) {
                    if (!materialHistory.length) {
                        return `<div class="of-empty">Keine Material-Historie vorhanden.</div>`;
                    }

                    return `
                                                                            <div class="of-history-inline-list">
                                                                                ${materialHistory.map(entry => `
                                                                                    <div class="of-history-inline-item">
                                                                                        <div class="of-history-inline-title">
                                                                                            ${esc(entry.type_label)}
                                                                                        </div>

                                                                                        <div class="of-history-inline-sub">
                                                                                            ${entry.from_raw ? `Von: <strong>${esc(entry.from_label)}</strong>` : 'Von: <strong>-</strong>'}
                                                                                            ${entry.to_raw ? ` · Nach: <strong>${esc(entry.to_label)}</strong>` : ''}
                                                                                            ${entry.qty > 0 ? ` · Menge: <strong>${esc(entry.qty.toFixed(2))}</strong>` : ''}
                                                                                            <br>
                                                                                            Grund: <strong>${esc(entry.reason || '-')}</strong>
                                                                                            <br>
                                                                                            Benutzer: <strong>${esc(entry.changed_by_name)}</strong>
                                                                                            <br>
                                                                                            Datum: <strong>${esc(formatDateTimeValue(entry.created_at))}</strong>
                                                                                        </div>
                                                                                    </div>
                                                                                `).join('')}
                                                                            </div>
                                                                        `;
                }


                function normalizeHistoryEntries() {
                    const folderHistory = safeHistory(state.folder?.history).map(entry => ({
                        ...entry,
                        __source: 'folder'
                    }));

                    const detailHistory = safeHistory(state.detail?.biography_data).map(entry => ({
                        ...entry,
                        __source: 'detail'
                    }));

                    const merged = [...folderHistory, ...detailHistory];

                    return merged
                        .map((entry, index) => {
                            const rawChangedByName =
                                entry?.changed_by_name ??
                                entry?.user_name ??
                                entry?.employee_name ??
                                entry?.changed_by?.name ??
                                entry?.creator_name ??
                                '';

                            const changedById =
                                entry?.changed_by_id ||
                                entry?.user_id ||
                                entry?.employee_id ||
                                entry?.changed_by?.id ||
                                null;

                            let changedByName = String(rawChangedByName || '').trim();

                            if (!changedByName || /^\d+$/.test(changedByName)) {
                                if (
                                    Number(changedById) &&
                                    Number(changedById) === Number(state.folder?.creator?.id)
                                ) {
                                    changedByName = [
                                        state.folder?.creator?.name || '',
                                        state.folder?.creator?.lastname || ''
                                    ].join(' ').replace(/\s+/g, ' ').trim();
                                }
                            }

                            if (!changedByName) {
                                changedByName = 'Unbekannt';
                            }

                            const fromStatus = normalisiereStatus(
                                entry?.from_status ||
                                entry?.old_status ||
                                entry?.previous_status ||
                                entry?.from ||
                                ''
                            );

                            const toStatus = normalisiereStatus(
                                entry?.to_status ||
                                entry?.new_status ||
                                entry?.status ||
                                entry?.to ||
                                state.folder?.status ||
                                'draft'
                            );

                            const reason =
                                entry?.reason ||
                                entry?.reason_text ||
                                entry?.note ||
                                entry?.message ||
                                '';

                            const action =
                                entry?.action ||
                                entry?.type ||
                                (fromStatus && toStatus && fromStatus !== toStatus ? 'status_changed' : 'updated');

                            const createdAt =
                                entry?.created_at ||
                                entry?.date ||
                                entry?.datetime ||
                                entry?.changed_at ||
                                entry?.at ||
                                null;

                            return {
                                _index: index,
                                action,
                                from_status: fromStatus,
                                to_status: toStatus,
                                reason,
                                changed_by_name: changedByName,
                                changed_by_id: changedById,
                                created_at: createdAt,
                                source: entry.__source || null
                            };
                        })
                        .sort((a, b) => {
                            const aTime = a.created_at ? new Date(a.created_at).getTime() : 0;
                            const bTime = b.created_at ? new Date(b.created_at).getTime() : 0;
                            return bTime - aTime;
                        });
                }

                function buildHistoryTitle(entry) {
                    if (entry.action === 'document_status_changed') {
                        return `Dokumentstatus geändert: ${getDocumentStatusLabel(entry.from_status || 'offer')} → ${getDocumentStatusLabel(entry.to_status || 'offer')}`;
                    }

                    if (entry.action === 'document_reverted_to_offer') {
                        return 'Zurück auf Angebot';
                    }

                    if (entry.action === 'status_changed' || (entry.from_status && entry.to_status && entry.from_status !== entry.to_status)) {
                        return `Status geändert: ${buildStatusLabel(entry.from_status || 'draft')} → ${buildStatusLabel(entry.to_status || 'draft')}`;
                    }

                    if (entry.action === 'folder_created') return 'Ordner erstellt';
                    if (entry.action === 'folder_updated') return 'Ordner aktualisiert';
                    if (entry.action === 'offer_loaded') return 'Angebot geladen';
                    if (entry.action === 'material_changed') return 'Material geändert';
                    if (entry.action === 'attachments_uploaded') return 'Dateien hochgeladen';
                    if (entry.action === 'attachment_deleted') return 'Datei gelöscht';

                    return 'Änderung gespeichert';
                }
                function renderHistory() {
                    const wrap = document.getElementById('history-list-wrap');
                    const badge = document.getElementById('history-count-badge');
                    if (!wrap) return;

                    const entries = normalizeHistoryEntries();

                    if (badge) {
                        badge.textContent = `${entries.length} Einträge`;
                    }

                    if (!entries.length) {
                        wrap.innerHTML = `
                                                                                <div class="of-empty">
                                                                                    Noch keine Historie vorhanden.
                                                                                </div>
                                                                            `;
                        renderTabCounts();
                        return;
                    }

                    wrap.innerHTML = `
                                                                            <div class="of-history-list">
                                                                                ${entries.map(entry => `
                                                                                    <div class="of-history-item">
                                                                                        <div class="of-history-dot-wrap">
                                                                                            <div class="of-history-dot"></div>
                                                                                        </div>

                                                                                        <div class="of-history-card">
                                                                                            <div class="of-history-top">
                                                                                                <div class="of-history-title">
                                                                                                    ${esc(buildHistoryTitle(entry))}
                                                                                                </div>

                                                                                                <div class="of-history-date">
                                                                                                    ${esc(formatDateTimeValue(entry.created_at))}
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="of-history-meta">
                                                                                                <span class="of-history-badge">
                                                                                                    Benutzer: ${esc(entry.changed_by_name || 'Unbekannt')}
                                                                                                </span>

                                                                                                ${entry.to_status ? `
                                                                                                    <span class="of-history-badge">
                                                                                                        Neuer Status: ${esc(buildStatusLabel(entry.to_status))}
                                                                                                    </span>
                                                                                                ` : ''}

                                                                                                ${entry.from_status && entry.from_status !== entry.to_status ? `
                                                                                                    <span class="of-history-badge">
                                                                                                        Vorher: ${esc(buildStatusLabel(entry.from_status))}
                                                                                                    </span>
                                                                                                ` : ''}
                                                                                            </div>

                                                                                            <div class="of-history-text">
                                                                                                ${entry.reason && String(entry.reason).trim()
                            ? esc(entry.reason)
                            : 'Kein Grund hinterlegt.'}
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                `).join('')}
                                                                            </div>
                                                                        `;

                    renderTabCounts();
                }

                function esc(v) {
                    return String(v ?? '')
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                }

                function formatDateValue(value) {
                    if (!value) return '-';

                    try {
                        const date = new Date(value);
                        if (Number.isNaN(date.getTime())) return '-';

                        return date.toLocaleDateString('de-DE', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        });
                    } catch (e) {
                        return '-';
                    }
                }

                function formatDateTimeValue(value) {
                    if (!value) return '-';

                    try {
                        const date = new Date(value);
                        if (Number.isNaN(date.getTime())) return '-';

                        return date.toLocaleDateString('de-DE', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric'
                        }) + ', ' + date.toLocaleTimeString('de-DE', {
                            hour: '2-digit',
                            minute: '2-digit'
                        });
                    } catch (e) {
                        return '-';
                    }
                }

                function getOfferCustomerLines() {
                    const customer = state.offer?.customer || {};

                    const line1 = [
                        customer.firma || '',
                        customer.name || '',
                        customer.lastname || ''
                    ].join(' ').replace(/\s+/g, ' ').trim();

                    const line2 = customer.street || customer.address || '';
                    const line3 = [
                        customer.postal_code || customer.zip || '',
                        customer.city || ''
                    ].join(' ').trim();

                    return [line1 || 'Unbekannt', line2, line3].filter(Boolean);
                }

                function getContactName() {
                    const creator = state.folder?.creator || state.offer?.creator || {};
                    const full = [
                        creator.name || '',
                        creator.lastname || ''
                    ].join(' ').replace(/\s+/g, ' ').trim();

                    return full || 'Nicht zugewiesen';
                }

                function getContactPhone() {
                    const creator = state.folder?.creator || state.offer?.creator || {};
                    return creator.phone || creator.tel || creator.mobile || '-';
                }

                function getContactEmail() {
                    const creator = state.folder?.creator || state.offer?.creator || {};
                    return creator.email || '-';
                }

                function getProductLabel() {
                    return state.offer?.product?.article_group
                        || state.offer?.product?.product
                        || 'Unbekannt';
                }

                function getObjectLabel() {
                    const alternative = state.offer?.alternative || {};
                    const parts = [
                        alternative.street || '',
                        alternative.city || ''
                    ].filter(Boolean);

                    return parts.length ? parts.join(', ') : '-';
                }


                function safeArray(value) {
                    return Array.isArray(value) ? value : [];
                }

                function setText(id, value) {
                    const el = document.getElementById(id);
                    if (el) el.textContent = value;
                }

                function money(value) {
                    const n = Number(value || 0);
                    return new Intl.NumberFormat('de-DE', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(n) + ' €';
                }

                function getMaterialCols() {
                    return state.materialTableCols || {};
                }

                function toggleMaterialColumn(col) {
                    if (!state.materialTableCols || !(col in state.materialTableCols)) return;
                    state.materialTableCols[col] = !state.materialTableCols[col];
                    renderMaterialList();
                }

                function setMaterialColumnPreset(mode) {
                    if (mode === 'standard') {
                        state.materialTableCols = {
                            image: true,
                            position: true,
                            article_no: true,
                            distributor_article_no: true,
                            distributor: true,
                            type: false,
                            status: false,
                            qty: true,
                            qty_total: true,
                            unit: true,
                            ek_price: false,
                            ek_total: false,
                            unit_price: false,
                            total: false,
                            margin: false,
                            db_total: false
                        };
                    } else if (mode === 'all') {
                        state.materialTableCols = {
                            image: true,
                            position: true,
                            article_no: true,
                            distributor_article_no: true,
                            distributor: true,
                            type: true,
                            status: true,
                            qty: true,
                            qty_total: true,
                            unit: true,
                            ek_price: true,
                            ek_total: true,
                            unit_price: true,
                            total: true,
                            margin: true,
                            db_total: true
                        };
                    }

                    renderMaterialList();
                }

                window.setMaterialColumnPreset = setMaterialColumnPreset;

                function getRowImage(row) {
                    return row?.image || row?.img || row?.image_url || row?.product_image || row?.photo || '';
                }

                function materialPickerKeepOpen(event) {
                    event.stopPropagation();
                }

                window.materialPickerKeepOpen = materialPickerKeepOpen;


                window.toggleMaterialColumn = toggleMaterialColumn;

                function materialColEnabled(col) {
                    return !!getMaterialCols()[col];
                }

                function formatBytes(bytes) {
                    const value = Number(bytes || 0);
                    if (value < 1024) return `${value} B`;
                    if (value < 1024 * 1024) return `${(value / 1024).toFixed(1)} KB`;
                    return `${(value / (1024 * 1024)).toFixed(2)} MB`;
                }
                function getAttachmentAnalytics(files) {
                    const list = safeArray(files);

                    return list.reduce((summary, file) => {
                        const type = String(file.file_type || '').toLowerCase();
                        const mime = String(file.mime_type || '').toLowerCase();
                        const name = String(file.original_name || file.title || '').toLowerCase();

                        const isImage =
                            type === 'image' ||
                            mime.startsWith('image/') ||
                            /\.(jpg|jpeg|png|webp|gif|bmp|svg)$/i.test(name);

                        const isPdf =
                            type === 'pdf' ||
                            mime.includes('pdf') ||
                            /\.pdf$/i.test(name);

                        summary.total += 1;
                        summary.size += Number(file.file_size || 0);

                        if (isImage) summary.images += 1;
                        if (isPdf) summary.pdfs += 1;

                        return summary;
                    }, {
                        total: 0,
                        images: 0,
                        pdfs: 0,
                        size: 0
                    });
                }

                function renderAttachmentAnalytics(files) {
                    const analytics = getAttachmentAnalytics(files);

                    setText('analytics-files-total', String(analytics.total));
                    setText('analytics-images-total', String(analytics.images));
                    setText('analytics-pdfs-total', String(analytics.pdfs));
                    setText('analytics-size-total', formatBytes(analytics.size));
                }

                function stripHtml(value) {
                    const div = document.createElement('div');
                    div.innerHTML = String(value || '');
                    return (div.textContent || div.innerText || '').trim();
                }

                function getCsrfToken() {
                    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                }

                async function fetchJson(url, options = {}) {
                    const response = await fetch(url, {
                        ...options,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            ...(options.headers || {})
                        }
                    });

                    const text = await response.text();
                    let json = {};

                    try {
                        json = text ? JSON.parse(text) : {};
                    } catch (error) {
                        throw new Error(`Ungültige Server-Antwort (${response.status})`);
                    }

                    if (!response.ok) {
                        throw new Error(json.message || `HTTP-Fehler ${response.status}`);
                    }

                    return json;
                }

                function showCustomToast(title, text, type = 'success') {
                    const wrap = document.getElementById('of-toast-wrap');
                    if (!wrap) return;

                    const toast = document.createElement('div');
                    toast.className = `of-toast ${type}`;
                    toast.innerHTML = `
                                                                            <div class="of-toast-icon">
                                                                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2">
                                                                                    <path d="M20 6 9 17l-5-5"></path>
                                                                                </svg>
                                                                            </div>
                                                                            <div>
                                                                                <div class="of-toast-title">${esc(title)}</div>
                                                                                <div class="of-toast-text">${esc(text)}</div>
                                                                            </div>
                                                                        `;

                    wrap.appendChild(toast);

                    setTimeout(() => {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateY(8px)';
                        toast.style.transition = 'all .2s ease';
                        setTimeout(() => toast.remove(), 220);
                    }, 3200);
                }

                function normalisiereStatus(status) {
                    const raw = String(status || '').trim().toLowerCase();

                    const map = {
                        draft: 'draft',
                        entwurf: 'draft',

                        sent: 'sent',
                        gesendet: 'sent',

                        negotiation: 'negotiation',
                        verhandlung: 'negotiation',

                        final: 'final',
                        abgeschlossen: 'final',
                        abgeschlosseen: 'final',

                        cancel: 'cancel',
                        storniert: 'cancel'
                    };

                    return map[raw] || 'draft';
                }

                function getFolderStatus() {
                    return getWorkflowStatus();
                }

                function distributorName(distributorId) {
                    if (!distributorId) return '-';
                    return state.distributors?.[String(distributorId)] || state.distributors?.[Number(distributorId)] || `Lieferant #${distributorId}`;
                }

                function isContainerMaterialRow(row) {
                    if (!row) return true;

                    const itemType = String(row.item_type || '').toLowerCase();
                    const level = String(row.level || '').toLowerCase();
                    const hierarchyLevel = Number(row.hierarchy_level || 0);

                    if (itemType === 'master_set') return true;
                    if (itemType === 'section') return true;
                    if (level.includes('hauptposition')) return true;

                    if (hierarchyLevel <= 1) return true;

                    return false;
                }

                function isStatusEditableMaterialRow(row) {
                    if (!row) return false;
                    if (isContainerMaterialRow(row)) return false;

                    const hasProductId = Number(row.product_id || 0) > 0;
                    const hasComponentId = Number(row.component_id || 0) > 0;
                    const hasArticleNo = String(row.article_no || '').trim() !== '';

                    return hasProductId || hasComponentId || hasArticleNo;
                }


                function buildCoverHtml(detail) {
                    const html = detail?.cover_text_html;
                    const text = detail?.cover_text;

                    if (html && String(html).trim() !== '') {
                        return { html, isEmpty: false };
                    }

                    if (text && String(text).trim() !== '') {
                        return {
                            html: esc(String(text)).replace(/\n/g, '<br>'),
                            isEmpty: false
                        };
                    }

                    return {
                        html: 'Kein Decktext vorhanden.',
                        isEmpty: true
                    };
                }

                function switchTab(tab) {
                    if (!tab) return;

                    const targetPanel = document.getElementById(`panel-${tab}`);

                    if (!targetPanel) {
                        console.warn(`Tab panel not found: panel-${tab}`);
                        return;
                    }

                    state.currentTab = tab;

                    document.querySelectorAll('#workspace-tabs .of-tab[data-tab], .of-workspace-side-nav .of-tab[data-tab], .of-sidebar-nav .of-tab[data-tab], .of-tab[data-tab]').forEach(btn => {
                        btn.classList.toggle('active', btn.dataset.tab === tab);
                    });

                    document.querySelectorAll('.of-shell-body .of-panel[id^="panel-"], .of-panel[id^="panel-"]').forEach(panel => {
                        panel.classList.toggle('active', panel.id === `panel-${tab}`);
                    });

                    if (tab === 'print-files') {
                        renderPrintFiles();
                    }

                    if (tab === 'agb') {
                        initAgbEditor();
                        syncAgbInputs();
                    }

                    if (tab === 'historie') {
                        renderHistory();
                    }

                    if (tab === 'material-print') {
                        renderMaterialList();
                    }
                }

                window.switchTab = switchTab;

                let statusReasonResolver = null;
                let pendingKanbanRevert = null;

                function openStatusReasonModal(newStatus, oldStatus) {
                    const modal = document.getElementById('status-reason-modal');
                    const label = document.getElementById('status-reason-status-label');
                    const sub = document.getElementById('status-reason-sub');
                    const text = document.getElementById('status-reason-text');
                    const error = document.getElementById('status-reason-error');
                    const confirmBtn = document.getElementById('status-reason-confirm-btn');

                    if (!modal || !label || !text || !confirmBtn) {
                        return Promise.resolve(null);
                    }

                    label.value = `${buildStatusLabel(oldStatus)} → ${buildStatusLabel(newStatus)}`;
                    sub.textContent = `Bitte geben Sie den Grund für die Statusänderung von "${buildStatusLabel(oldStatus)}" auf "${buildStatusLabel(newStatus)}" an.`;
                    text.value = '';
                    text.focus();
                    if (error) error.style.display = 'none';

                    modal.style.display = 'flex';

                    return new Promise((resolve) => {
                        statusReasonResolver = resolve;

                        const submit = () => {
                            const value = String(text.value || '').trim();

                            if (!value) {
                                if (error) error.style.display = 'block';
                                text.focus();
                                return;
                            }

                            closeStatusReasonModal(value);
                        };

                        const handleKeydown = (e) => {
                            if (e.key === 'Escape') {
                                document.removeEventListener('keydown', handleKeydown);
                                closeStatusReasonModal(null);
                            }

                            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                                submit();
                            }
                        };

                        document.addEventListener('keydown', handleKeydown);

                        confirmBtn.onclick = () => {
                            document.removeEventListener('keydown', handleKeydown);
                            submit();
                        };

                        modal.dataset.keydownBound = '1';
                    });
                }

                function closeStatusReasonModal(result = null) {
                    const modal = document.getElementById('status-reason-modal');
                    const text = document.getElementById('status-reason-text');
                    const error = document.getElementById('status-reason-error');

                    if (modal) modal.style.display = 'none';
                    if (text) text.value = '';
                    if (error) error.style.display = 'none';

                    if (typeof statusReasonResolver === 'function') {
                        statusReasonResolver(result);
                    }

                    statusReasonResolver = null;
                }

                window.closeStatusReasonModal = closeStatusReasonModal;

                let materialMoveResolver = null;

                function getMoveStatusLabel(status) {
                    const map = {
                        offen: 'Offen',
                        lager: 'Lager',
                        bestellen: 'Bestellen'
                    };

                    return map[String(status || '').toLowerCase()] || 'Offen';
                }

                function openMaterialMoveModal(rows, moveTo, mode = 'single') {
                    const modal = document.getElementById('material-move-modal');
                    const title = document.getElementById('material-move-modal-title');
                    const sub = document.getElementById('material-move-modal-sub');
                    const summary = document.querySelector('#material-move-modal-summary .of-smart-list-body');
                    const targetLabel = document.getElementById('material-move-target-label');
                    const qtyInput = document.getElementById('material-move-qty');
                    const error = document.getElementById('material-move-error');
                    const confirmBtn = document.getElementById('material-move-confirm-btn');

                    if (!modal || !targetLabel || !qtyInput || !confirmBtn || !summary) {
                        return Promise.resolve(null);
                    }

                    const safeRows = Array.isArray(rows) ? rows.filter(Boolean) : [];
                    state.materialMove.rows = safeRows;
                    state.materialMove.moveTo = moveTo;
                    state.materialMove.mode = mode;

                    title.textContent = mode === 'bulk' ? 'Mengen gesammelt verschieben' : 'Menge verschieben';
                    sub.textContent = mode === 'bulk'
                        ? `Sie verschieben die Menge für ${safeRows.length} markierte Position(en) nach "${getMoveStatusLabel(moveTo)}".`
                        : `Bitte geben Sie die Menge an, die nach "${getMoveStatusLabel(moveTo)}" verschoben werden soll.`;

                    targetLabel.value = getMoveStatusLabel(moveTo);
                    qtyInput.value = '';
                    if (error) error.style.display = 'none';

                    summary.innerHTML = safeRows.length
                        ? safeRows.map(row => `
                                                                                <div style="padding:10px 0; border-bottom:1px solid #eef2f7;">
                                                                                    <div style="font-weight:900; color:#111827;">${esc(row.name || '-')}</div>
                                                                                    <div class="of-sub" style="margin-top:4px;">
                                                                                        Pos.: ${esc(row.position_no || '-')}
                                                                                        · Art.-Nr.: ${esc(row.article_no || '-')}
                                                                                        · Gesamtmenge: ${esc(Number(getRowTotalQty(row)).toFixed(2))}
                                                                                    </div>
                                                                                </div>
                                                                            `).join('')
                        : 'Keine Auswahl vorhanden.';

                    modal.style.display = 'flex';

                    setTimeout(() => qtyInput.focus(), 30);

                    return new Promise(resolve => {
                        materialMoveResolver = resolve;

                        confirmBtn.onclick = () => {
                            const qty = Number(qtyInput.value || 0);

                            if (!(qty > 0)) {
                                if (error) error.style.display = 'block';
                                qtyInput.focus();
                                return;
                            }

                            const sourceStatus = (state.materialFilter && state.materialFilter !== 'all')
                                ? state.materialFilter
                                : null;

                            closeMaterialMoveModal({
                                move_qty: qty,
                                move_to: moveTo,
                                source_status: sourceStatus,
                                rows: safeRows,
                                mode
                            });
                        };
                    });
                }

                function closeMaterialMoveModal(result = null) {
                    const modal = document.getElementById('material-move-modal');
                    const qtyInput = document.getElementById('material-move-qty');
                    const error = document.getElementById('material-move-error');

                    if (modal) modal.style.display = 'none';
                    if (qtyInput) qtyInput.value = '';
                    if (error) error.style.display = 'none';

                    state.materialMove = {
                        rows: [],
                        moveTo: null,
                        mode: 'single'
                    };

                    if (typeof materialMoveResolver === 'function') {
                        materialMoveResolver(result);
                    }

                    materialMoveResolver = null;
                }

                window.closeMaterialMoveModal = closeMaterialMoveModal;

                let materialFinalResolver = null;

                function getFinalStatusLabel(status) {
                    const map = {
                        offen: 'Offen',
                        lager: 'Lager',
                        bestellen: 'Bestellen',
                        final: 'Final'
                    };

                    return map[String(status || '').toLowerCase()] || 'Unbekannt';
                }

                function openMaterialFinalModal(rows, sourceStatus) {
                    const modal = document.getElementById('material-final-modal');
                    const title = document.getElementById('material-final-modal-title');
                    const sub = document.getElementById('material-final-modal-sub');
                    const summary = document.querySelector('#material-final-modal-summary .of-smart-list-body');
                    const sourceLabel = document.getElementById('material-final-source-label');
                    const availableLabel = document.getElementById('material-final-available-label');
                    const qtyInput = document.getElementById('material-final-qty');
                    const remainingSelect = document.getElementById('material-final-remaining-to');
                    const reasonInput = document.getElementById('material-final-reason');
                    const error = document.getElementById('material-final-error');
                    const confirmBtn = document.getElementById('material-final-confirm-btn');

                    if (!modal || !summary || !sourceLabel || !availableLabel || !qtyInput || !remainingSelect || !reasonInput || !confirmBtn) {
                        return Promise.resolve(null);
                    }

                    const safeRows = Array.isArray(rows) ? rows.filter(Boolean) : [];
                    if (!safeRows.length) return Promise.resolve(null);

                    const firstRow = safeRows[0];
                    const allocation = getRowAllocation(firstRow);
                    const availableQty = Number(allocation[sourceStatus] || 0);

                    if (!(availableQty > 0)) {
                        showCustomToast('Keine Menge verfügbar', `In "${getFinalStatusLabel(sourceStatus)}" ist keine Menge vorhanden.`, 'error');
                        return Promise.resolve(null);
                    }

                    state.materialFinal.rows = safeRows;
                    state.materialFinal.sourceStatus = sourceStatus;
                    state.materialFinal.availableQty = availableQty;

                    const remainingOptions = ['offen', 'lager', 'bestellen'].filter(v => v !== sourceStatus);

                    title.textContent = safeRows.length > 1 ? 'Final List gesammelt bestätigen' : 'Final List bestätigen';
                    sub.textContent = safeRows.length > 1
                        ? `Sie bestätigen ${safeRows.length} markierte Position(en) aus "${getFinalStatusLabel(sourceStatus)}" für die Final List.`
                        : `Bitte bestätigen Sie die finale Menge aus "${getFinalStatusLabel(sourceStatus)}".`;

                    sourceLabel.value = getFinalStatusLabel(sourceStatus);
                    availableLabel.value = availableQty.toFixed(2);

                    qtyInput.value = availableQty.toFixed(2);
                    reasonInput.value = 'Physisch bestätigt';
                    error.style.display = 'none';

                    remainingSelect.innerHTML = remainingOptions.map(status => `
                                                                            <option value="${status}">${getFinalStatusLabel(status)}</option>
                                                                        `).join('');

                    summary.innerHTML = safeRows.map(row => {
                        const rowAllocation = getRowAllocation(row);
                        const rowAvailable = Number(rowAllocation[sourceStatus] || 0);

                        return `
                                                                                <div style="padding:10px 0; border-bottom:1px solid #eef2f7;">
                                                                                    <div style="font-weight:900; color:#111827;">${esc(row.name || '-')}</div>
                                                                                    <div class="of-sub" style="margin-top:4px;">
                                                                                        Pos.: ${esc(row.position_no || '-')}
                                                                                        · Art.-Nr.: ${esc(row.article_no || '-')}
                                                                                        · Verfügbar in ${esc(getFinalStatusLabel(sourceStatus))}: ${esc(rowAvailable.toFixed(2))}
                                                                                        · Gesamtmenge: ${esc(Number(getRowTotalQty(row)).toFixed(2))}
                                                                                    </div>
                                                                                </div>
                                                                            `;
                    }).join('');

                    modal.style.display = 'flex';

                    setTimeout(() => qtyInput.focus(), 30);

                    return new Promise(resolve => {
                        materialFinalResolver = resolve;

                        confirmBtn.onclick = () => {
                            const finalQty = Number(String(qtyInput.value || '0').replace(',', '.'));
                            const remainingTo = String(remainingSelect.value || '').trim().toLowerCase();
                            const reason = String(reasonInput.value || '').trim();

                            if (!(finalQty > 0) || finalQty > availableQty || !remainingOptions.includes(remainingTo) || !reason) {
                                error.style.display = 'block';
                                return;
                            }

                            closeMaterialFinalModal({
                                rows: safeRows,
                                source_status: sourceStatus,
                                final_qty: finalQty,
                                remaining_to: remainingTo,
                                reason
                            });
                        };
                    });
                }

                function closeMaterialFinalModal(result = null) {
                    const modal = document.getElementById('material-final-modal');
                    const qtyInput = document.getElementById('material-final-qty');
                    const reasonInput = document.getElementById('material-final-reason');
                    const error = document.getElementById('material-final-error');
                    const remainingSelect = document.getElementById('material-final-remaining-to');

                    if (modal) modal.style.display = 'none';
                    if (qtyInput) qtyInput.value = '';
                    if (reasonInput) reasonInput.value = 'Physisch bestätigt';
                    if (remainingSelect) remainingSelect.innerHTML = '';
                    if (error) error.style.display = 'none';

                    state.materialFinal = {
                        rows: [],
                        sourceStatus: null,
                        availableQty: 0
                    };

                    if (typeof materialFinalResolver === 'function') {
                        materialFinalResolver(result);
                    }

                    materialFinalResolver = null;
                }

                window.closeMaterialFinalModal = closeMaterialFinalModal;


                function printMaterialSheet() {
                    state.currentTab = 'material-print';
                    switchTab('material-print');

                    setTimeout(() => {
                        window.print();
                    }, 120);
                }
                window.printMaterialSheet = printMaterialSheet;

                function initAgbEditor() {
                    const editorEl = document.getElementById('agb-editor');
                    const hiddenInput = document.getElementById('agb-text-input');

                    if (!editorEl || !hiddenInput) return;
                    if (typeof Quill === 'undefined') return;
                    if (agbQuill) return;

                    agbQuill = new Quill(editorEl, {
                        theme: 'snow',
                        placeholder: 'AGB hier eingeben ...',
                        modules: {
                            toolbar: [
                                [{ header: [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                [{ align: [] }],
                                ['blockquote'],
                                ['link'],
                                ['clean']
                            ]
                        }
                    });

                    agbQuill.root.innerHTML = hiddenInput.value || '';

                    agbQuill.on('text-change', () => {
                        hiddenInput.value = agbQuill.root.innerHTML;
                    });
                }

                function setAgbEditorHtml(html) {
                    const hiddenInput = document.getElementById('agb-text-input');
                    if (hiddenInput) hiddenInput.value = html || '';

                    if (agbQuill) {
                        agbQuill.root.innerHTML = html || '';
                    }
                }

                function syncAgbInputs() {
                    const titleInput = document.getElementById('agb-title-input');

                    const folderAgb = window.folderAgb || {};
                    const defaultAgb = window.folderDefaultAgb || {};
                    const detail = state.detail || {};

                    const title =
                        folderAgb.title ||
                        detail.agb_title ||
                        defaultAgb.title ||
                        'Allgemeine Geschäftsbedingungen';

                    const text =
                        folderAgb.text ||
                        detail.agb_text ||
                        defaultAgb.text ||
                        '';

                    if (titleInput) titleInput.value = title;
                    setAgbEditorHtml(text);
                }

                async function saveAgbForFolder() {
                    const url = folderApp.dataset.agbSaveUrl;
                    const offerId = folderApp.dataset.offerId || '';

                    if (!url) {
                        alert('Keine AGB-URL gefunden.');
                        return;
                    }

                    try {
                        if (agbQuill) {
                            const hiddenInput = document.getElementById('agb-text-input');
                            if (hiddenInput) {
                                hiddenInput.value = agbQuill.root.innerHTML;
                            }
                        }

                        const payload = {
                            offer_id: offerId || null,
                            agb_title: document.getElementById('agb-title-input')?.value || '',
                            agb_text: document.getElementById('agb-text-input')?.value || ''
                        };

                        const json = await fetchJson(url, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify(payload)
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'AGB konnte nicht gespeichert werden.');
                        }

                        state.detail = json.detail || state.detail;

                        window.folderAgb = json.agb || {
                            title: payload.agb_title,
                            text: payload.agb_text
                        };

                        syncAgbInputs();

                        showCustomToast(
                            'AGB gespeichert',
                            'Die AGB für dieses Angebot wurden gespeichert.'
                        );
                    } catch (error) {
                        alert(error.message || 'AGB konnte nicht gespeichert werden.');
                    }
                }

                window.saveAgbForFolder = saveAgbForFolder;

                window.saveAgbForFolder = saveAgbForFolder;

                function getAllStructureCounts() {
                    let total = 0;

                    safeArray(state.sections).forEach(section => {
                        safeArray(section?.items).forEach(item => {
                            total++;

                            safeArray(item?.subItems).forEach(sub => {
                                total++;
                                if (sub?.kind === 'labor' && Array.isArray(sub?.labor_rows)) {
                                    total += sub.labor_rows.length;
                                }
                            });
                        });
                    });

                    return total;
                }

                function getStructureRows() {
                    const materialRows = [];
                    const laborRows = [];

                    safeArray(state.sections).forEach((section, sectionIndex) => {
                        const sectionTitle = section?.title || `Sektion ${sectionIndex + 1}`;
                        const sectionNo = `${sectionIndex + 1}`;

                        const sectionQty = Number(section?.config?.qty || 1);
                        const sectionUnit = String(section?.config?.unit || '').toLowerCase();
                        const sectionMultiplier = sectionUnit === 'set' ? sectionQty : 1;

                        safeArray(section?.items).forEach((item, itemIndex) => {
                            const itemNo = `${sectionNo}.${itemIndex + 1}`;
                            const parentTitle = item?.name || item?.title || `Position ${itemIndex + 1}`;
                            const parentQty = Number(item?.qty || 0);
                            const parentHasChildren = safeArray(item?.subItems).length > 0;

                            if (item?.kind !== 'labor') {
                                const qty = Number(item?.qty || 0);
                                const qtyTotal = qty * sectionMultiplier;

                                const unitPrice = Number(item?.price || item?.rate || 0);
                                const ekPrice = Number(item?.purchase_price || item?.ek || 0);
                                const vkTotal = Number(item?.total ?? (qty * unitPrice)) * sectionMultiplier;
                                const ekTotal = (qty * ekPrice) * sectionMultiplier;
                                const dbTotal = vkTotal - ekTotal;
                                const marginPercent = ekTotal > 0 ? ((vkTotal - ekTotal) / ekTotal) * 100 : 0;

                                materialRows.push({
                                    position_no: itemNo,
                                    hierarchy_level: 1,
                                    section_title: sectionTitle,
                                    parent_title: parentTitle,
                                    level: 'Hauptposition',
                                    type_label: item?.kind === 'labor' ? 'Lohn' : 'Artikel',
                                    status_label: item?.lineType || item?.status || 'standard',
                                    order_status: item?.order_status || 'offen',
                                    stock_allocation: item?.stock_allocation || null,
                                    material_history: Array.isArray(item?.material_history) ? item.material_history : [],
                                    product_id: item?.product_id ?? item?.productId ?? item?.product?.id ?? null,
                                    component_id: item?.component_id ?? null,
                                    distributor_price_id: item?.distributor_price_id || null,
                                    article_no: item?.article_no || '-',
                                    distributor_article_no: item?.distributor_article_no || '-',
                                    name: parentTitle,
                                    image: item?.img || item?.image || item?.image_url || item?.product_image || '',
                                    description: stripHtml(item?.desc_html || item?.desc || item?.description || ''),
                                    qty: qty,
                                    qty_total: qtyTotal,
                                    unit: item?.unit || item?.measure || '-',
                                    unit_price: unitPrice,
                                    ek_price: ekPrice,
                                    total: vkTotal,
                                    ek_total: ekTotal,
                                    db_total: dbTotal,
                                    margin_percent: marginPercent,
                                    distributor_id: item?.distributor_id || null,
                                    distributor_name: item?.distributor_name || distributorName(item?.distributor_id),
                                    supplier_article_no: item?.distributor_article_no || '-',
                                    item_type: item?.item_type || 'Position',
                                    depth: Number(item?.depth || 0),
                                    section_multiplier: sectionMultiplier,
                                    parent_qty: 1,
                                    is_container: parentHasChildren || String(item?.item_type || '').toLowerCase() === 'master_set',
                                    has_children: parentHasChildren
                                });
                            }

                            safeArray(item?.subItems).forEach((subItem, subIndex) => {
                                const subItemNo = `${itemNo}.${subIndex + 1}`;

                                if (subItem?.kind === 'labor') {
                                    const laborRowsData = safeArray(subItem?.labor_rows);

                                    if (laborRowsData.length) {
                                        laborRowsData.forEach((row, rowIndex) => {
                                            laborRows.push({
                                                position_no: `${subItemNo}.${rowIndex + 1}`,
                                                section_title: sectionTitle,
                                                parent_title: parentTitle,
                                                labor_title: subItem?.name || 'Arbeitsleistung',

                                                qualification_id: row?.qualification_id || null,
                                                qualification_name: row?.qualification_name || `Lohnzeile ${rowIndex + 1}`,

                                                qty: Number(row?.qty || 0),
                                                unit: row?.unit || subItem?.unit || 'Std.',

                                                ek: Number(row?.ek || 0),
                                                margin_percent: Number(row?.margin_percent || row?.marginPercent || 0),
                                                rate: Number(row?.rate || 0),

                                                total: Number(row?.total ?? (Number(row?.qty || 0) * Number(row?.rate || 0)))
                                            });
                                        });
                                    } else {
                                        laborRows.push({
                                            position_no: subItemNo,
                                            section_title: sectionTitle,
                                            parent_title: parentTitle,
                                            labor_title: subItem?.name || 'Arbeitsleistung',

                                            qualification_id: subItem?.qualification_id || null,
                                            qualification_name: subItem?.qualification_name || subItem?.name || 'Arbeitsleistung',

                                            qty: Number(subItem?.qty || 0),
                                            unit: subItem?.unit || subItem?.measure || 'Std.',

                                            ek: Number(subItem?.ek || subItem?.purchase_price || 0),
                                            margin_percent: Number(subItem?.margin_percent || subItem?.marginPercent || 0),
                                            rate: Number(subItem?.rate || subItem?.price || 0),

                                            total: Number(subItem?.total ?? (Number(subItem?.qty || 0) * Number(subItem?.rate || subItem?.price || 0)))
                                        });
                                    }

                                    return;
                                }

                                const qty = Number(subItem?.qty || 0);
                                const parentQtyFactor = parentQty > 0 ? parentQty : 1;
                                const qtyTotal = qty * parentQtyFactor * sectionMultiplier;

                                const unitPrice = Number(subItem?.price || subItem?.rate || 0);
                                const ekPrice = Number(subItem?.purchase_price || subItem?.ek || 0);
                                const vkTotal = Number(subItem?.total ?? (qty * unitPrice)) * parentQtyFactor * sectionMultiplier;
                                const ekTotal = (qty * ekPrice) * parentQtyFactor * sectionMultiplier;
                                const dbTotal = vkTotal - ekTotal;
                                const marginPercent = ekTotal > 0 ? ((vkTotal - ekTotal) / ekTotal) * 100 : 0;
                                const subHasChildren = safeArray(subItem?.subItems).length > 0;

                                materialRows.push({
                                    position_no: subItemNo,
                                    hierarchy_level: 2,
                                    section_title: sectionTitle,
                                    parent_title: parentTitle,
                                    level: subItem?.isChildNode ? 'Unterartikel' : 'Komponente',
                                    type_label: subItem?.kind === 'labor' ? 'Lohn' : 'Artikel',
                                    status_label: subItem?.lineType || subItem?.status || 'standard',
                                    order_status: subItem?.order_status || 'offen',
                                    stock_allocation: subItem?.stock_allocation || null,
                                    material_history: Array.isArray(subItem?.material_history) ? subItem.material_history : [],
                                    product_id: subItem?.product_id ?? subItem?.productId ?? subItem?.product?.id ?? null,
                                    component_id: subItem?.component_id ?? null,
                                    distributor_price_id: subItem?.distributor_price_id || null,
                                    article_no: subItem?.article_no || '-',
                                    distributor_article_no: subItem?.distributor_article_no || '-',
                                    name: subItem?.name || subItem?.title || `Unterposition ${subIndex + 1}`,
                                    image: subItem?.img || subItem?.image || subItem?.image_url || subItem?.product_image || '',
                                    description: stripHtml(subItem?.desc_html || subItem?.desc || subItem?.description || ''),
                                    qty: qty,
                                    qty_total: qtyTotal,
                                    unit: subItem?.unit || subItem?.measure || '-',
                                    unit_price: unitPrice,
                                    ek_price: ekPrice,
                                    total: vkTotal,
                                    ek_total: ekTotal,
                                    db_total: dbTotal,
                                    margin_percent: marginPercent,
                                    distributor_id: subItem?.distributor_id || null,
                                    distributor_name: subItem?.distributor_name || distributorName(subItem?.distributor_id),
                                    supplier_article_no: subItem?.distributor_article_no || '-',
                                    item_type: subItem?.item_type || 'Komponente',
                                    depth: Number(subItem?.depth || 0),
                                    section_multiplier: sectionMultiplier,
                                    parent_qty: parentQtyFactor,
                                    is_container: false,
                                    has_children: subHasChildren
                                });

                                safeArray(subItem?.subItems).forEach((childItem, childIndex) => {
                                    if (childItem?.kind === 'labor') return;

                                    const childNo = `${subItemNo}.${childIndex + 1}`;
                                    const childQty = Number(childItem?.qty || 0);
                                    const childQtyTotal = childQty * qty * parentQtyFactor * sectionMultiplier;

                                    const childUnitPrice = Number(childItem?.price || childItem?.rate || 0);
                                    const childEkPrice = Number(childItem?.purchase_price || childItem?.ek || 0);
                                    const childVkTotal = Number(childItem?.total ?? (childQty * childUnitPrice)) * qty * parentQtyFactor * sectionMultiplier;
                                    const childEkTotal = (childQty * childEkPrice) * qty * parentQtyFactor * sectionMultiplier;
                                    const childDbTotal = childVkTotal - childEkTotal;
                                    const childMarginPercent = childEkTotal > 0 ? ((childVkTotal - childEkTotal) / childEkTotal) * 100 : 0;

                                    materialRows.push({
                                        position_no: childNo,
                                        hierarchy_level: 3,
                                        section_title: sectionTitle,
                                        parent_title: subItem?.name || parentTitle,
                                        level: 'Unterkomponente',
                                        type_label: childItem?.kind === 'labor' ? 'Lohn' : 'Artikel',
                                        status_label: childItem?.lineType || childItem?.status || 'standard',
                                        stock_allocation: childItem?.stock_allocation || null,
                                        order_status: childItem?.order_status || 'offen',
                                        material_history: Array.isArray(childItem?.material_history) ? childItem.material_history : [],
                                        product_id: childItem?.product_id ?? childItem?.productId ?? childItem?.product?.id ?? null,
                                        component_id: childItem?.component_id ?? null,
                                        distributor_price_id: childItem?.distributor_price_id || null,
                                        article_no: childItem?.article_no || '-',
                                        distributor_article_no: childItem?.distributor_article_no || '-',
                                        name: childItem?.name || childItem?.title || `Unterkomponente ${childIndex + 1}`,
                                        image: childItem?.img || childItem?.image || childItem?.image_url || childItem?.product_image || '',
                                        description: stripHtml(childItem?.desc_html || childItem?.desc || childItem?.description || ''),
                                        qty: childQty,
                                        qty_total: childQtyTotal,
                                        unit: childItem?.unit || childItem?.measure || '-',
                                        unit_price: childUnitPrice,
                                        ek_price: childEkPrice,
                                        total: childVkTotal,
                                        ek_total: childEkTotal,
                                        db_total: childDbTotal,
                                        margin_percent: childMarginPercent,
                                        distributor_id: childItem?.distributor_id || null,
                                        distributor_name: childItem?.distributor_name || distributorName(childItem?.distributor_id),
                                        supplier_article_no: childItem?.distributor_article_no || '-',
                                        item_type: childItem?.item_type || 'Unterkomponente',
                                        depth: Number(childItem?.depth || 0),
                                        section_multiplier: sectionMultiplier,
                                        parent_qty: qty * parentQtyFactor,
                                        is_container: false,
                                        has_children: false
                                    });
                                });
                            });
                        });
                    });

                    return { materialRows, laborRows };
                }
                function getTabMetrics() {
                    const { materialRows, laborRows } = getStructureRows();

                    const realMaterialRows = materialRows.filter(row => !isContainerMaterialRow(row));

                    return {
                        overview: 1,
                        kanban: 1,
                        material: realMaterialRows.length,
                        labor: laborRows.length,
                        materialPrint: realMaterialRows.length,
                        historie: normalizeHistoryEntries().length,
                        printFiles: safeArray(state.attachments).length,
                        agb: 1
                    };
                }

                function renderTabCounts() {
                    const metrics = getTabMetrics();

                    setText('tab-count-uebersicht', String(metrics.overview));
                    setText('tab-count-kanban', String(metrics.kanban));
                    setText('tab-count-material', String(metrics.material));
                    setText('tab-count-labor', String(metrics.labor));
                    setText('tab-count-material-print', String(metrics.materialPrint));
                    setText('tab-count-historie', String(metrics.historie));
                    setText('tab-count-print-files', String(metrics.printFiles));
                    setText('tab-count-agb', String(metrics.agb));
                }

                function renderStats() {
                    const detail = state.detail || {};
                    const currentStatus = getFolderStatus();

                    setText('stat-total-net', money(detail.total_net || 0));
                    setText('stat-tax-rate', new Intl.NumberFormat('de-DE', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(Number(detail.tax_rate || 19)) + ' %');
                    setText('stat-total-gross', money(detail.total_gross || 0));
                    setText('stat-items-count', String(getAllStructureCounts()));

                    setText('info-company-name', detail.company_name || '-');
                    setText('info-brand-mode', detail.brand_mode || 'Text');
                    setText('info-brand-color', detail.brand_color || '-');
                    setText('info-brand-logo', detail.brand_logo_url || '-');
                    setText('info-sections-count', String(safeArray(state.sections).length || 0));
                    setText('info-images-count', String(safeArray(detail.placed_images).length || 0));

                    const coverBox = document.getElementById('cover-box');
                    if (coverBox) {
                        const cover = buildCoverHtml(detail);
                        coverBox.innerHTML = cover.html;
                        coverBox.classList.toggle('empty', cover.isEmpty);
                    }

                    if (getDocumentStatus() === 'deal') {
                        setText('status-card-draft', currentStatus === 'open' ? '1' : '0');
                        setText('status-card-sent', currentStatus === 'proposal' ? '1' : '0');
                        setText('status-card-negotiation', currentStatus === 'negotiation' ? '1' : '0');
                        setText('status-card-final', currentStatus === 'won' ? '1' : '0');
                        setText('status-card-cancel', currentStatus === 'lost' ? '1' : '0');
                    } else {
                        setText('status-card-draft', currentStatus === 'draft' ? '1' : '0');
                        setText('status-card-sent', currentStatus === 'sent' ? '1' : '0');
                        setText('status-card-negotiation', currentStatus === 'negotiation' ? '1' : '0');
                        setText('status-card-final', currentStatus === 'accepted' ? '1' : '0');
                        setText('status-card-cancel', currentStatus === 'cancelled' ? '1' : '0');
                    }
                    renderDocumentStatusToggle();
                    renderTabCounts();
                    renderOfferLockState();
                }

                function readJsonDatasetValue(key, fallback = {}) {
                    try {
                        const raw = folderApp?.dataset?.[key] || '';
                        return raw ? JSON.parse(raw) : fallback;
                    } catch (error) {
                        console.warn(`Ungültiges JSON im data-${key}-Attribut`, error);
                        return fallback;
                    }
                }

                function isBlankPresenceName(value) {
                    const name = String(value || '').trim();
                    return !name
                        || /^user\s*#?\s*$/i.test(name)
                        || /^benutzer\s*#?\s*$/i.test(name)
                        || /^user\s*#?0?$/i.test(name)
                        || /^benutzer\s*#?0?$/i.test(name);
                }

                function normalizePresenceAvatar(image) {
                    const defaultAvatar = folderApp.dataset.defaultAvatar || '';
                    const employeeImageBase = folderApp.dataset.employeeImageBase || '';
                    const value = String(image || '').trim();

                    if (!value) return defaultAvatar;
                    if (/^https?:\/\//i.test(value) || value.startsWith('/')) return value;

                    return employeeImageBase
                        ? `${employeeImageBase}/${value.replace(/^\/+/, '')}`
                        : defaultAvatar;
                }

                function currentPresenceUser() {
                    return readJsonDatasetValue('currentPresenceUser', {});
                }

                function presenceIdentityKey(user) {
                    const employeeId = Number(user?.employee_id || user?.employee?.id || 0);
                    const userId = Number(user?.user_id || user?.id || 0);
                    if (employeeId > 0) return `employee:${employeeId}`;
                    if (userId > 0) return `user:${userId}`;
                    return `name:${String(user?.name || user?.employee_name || '').toLowerCase()}`;
                }

                function normalizePresenceUser(user = {}) {
                    const current = currentPresenceUser();
                    const defaultAvatar = folderApp.dataset.defaultAvatar || '';

                    const userId = Number(user?.user_id || user?.id || 0);
                    const employeeId = Number(user?.employee_id || user?.employee?.id || 0);
                    const currentUserId = Number(current?.user_id || current?.id || folderApp.dataset.currentUserId || 0);
                    const currentEmployeeId = Number(current?.employee_id || folderApp.dataset.currentEmployeeId || 0);

                    const isCurrentUser = Boolean(
                        (currentUserId && userId && currentUserId === userId)
                        || (currentEmployeeId && employeeId && currentEmployeeId === employeeId)
                    );

                    const directName = user?.employee_name
                        || user?.full_name
                        || user?.name_full
                        || user?.name
                        || '';

                    let name = String(directName || '').trim();

                    if (isBlankPresenceName(name)) {
                        if (isCurrentUser && current?.name) {
                            name = current.name;
                        } else if (employeeId) {
                            name = `Mitarbeiter #${employeeId}`;
                        } else if (userId) {
                            name = `Benutzer #${userId}`;
                        } else {
                            name = 'Benutzer';
                        }
                    }

                    let avatar = user?.avatar
                        || user?.employee_avatar
                        || user?.employee_image
                        || user?.image
                        || user?.employee?.image
                        || '';

                    if ((!avatar || avatar === defaultAvatar) && isCurrentUser && current?.avatar) {
                        avatar = current.avatar;
                    }

                    return {
                        ...user,
                        id: userId || user?.id || current?.id || null,
                        user_id: userId || user?.user_id || null,
                        employee_id: employeeId || (isCurrentUser ? currentEmployeeId : null),
                        name,
                        avatar: normalizePresenceAvatar(avatar),
                    };
                }

                function uniquePresenceUsers(users) {
                    const seen = new Set();

                    return safeArray(users)
                        .map(normalizePresenceUser)
                        .filter(user => {
                            const key = presenceIdentityKey(user);
                            if (seen.has(key)) return false;
                            seen.add(key);
                            return true;
                        });
                }

                function renderPresenceUsers() {
                    const el = document.getElementById('presence-users');
                    if (!el) return;

                    const users = uniquePresenceUsers(state.presenceUsers);
                    const defaultAvatar = folderApp.dataset.defaultAvatar || '';

                    if (!users.length) {
                        el.innerHTML = `<div class="of-presence-empty">Keine weiteren Benutzer sichtbar.</div>`;
                        return;
                    }

                    el.innerHTML = users.map(user => `
                                <div class="of-presence-user">
                                    <div class="of-presence-avatar-wrap">
                                        <img
                                            src="${esc(user.avatar || defaultAvatar)}"
                                            alt="${esc(user.name || 'Benutzer')}"
                                            class="of-presence-avatar"
                                            onerror="this.src='${esc(defaultAvatar)}'"
                                        >
                                        <span class="of-presence-dot"></span>
                                    </div>
                                    <span class="of-presence-name">${esc(user.name || 'Benutzer')}</span>
                                </div>
                            `).join('');
                }


                function normalizeTeamAccessPayload(payload = {}) {
                    const raw = payload?.team_access || payload?.access || payload || {};
                    const possibleTeams = [
                        raw.team_members,
                        raw.team,
                        raw.teams,
                        raw.authorized_employees,
                        raw.members,
                        payload?.team_members,
                        payload?.team,
                        payload?.teams,
                        state.offer?.lead_product_list?.teams,
                        state.offer?.leadProductList?.teams,
                        state.offer?.teams,
                        state.folder?.lead_product_list?.teams,
                        state.folder?.leadProductList?.teams,
                    ];

                    let team = possibleTeams.find(item => Array.isArray(item) && item.length);

                    if (!team) {
                        const asString = possibleTeams.find(item => typeof item === 'string' && item.trim() !== '');
                        if (asString) {
                            try {
                                const parsed = JSON.parse(asString);
                                if (Array.isArray(parsed)) team = parsed;
                            } catch (error) {
                                team = [];
                            }
                        }
                    }

                    team = uniqueTeamMembers(team);

                    const mode = String(
                        raw.access_mode ||
                        raw.offer_team_access_mode ||
                        payload?.access_mode ||
                        payload?.offer_team_access_mode ||
                        'all'
                    ).toLowerCase();

                    return {
                        ...raw,
                        access_mode: mode === 'team_only' ? 'team_only' : 'all',
                        team: team,
                        teams: team,
                        team_members: team,
                        can_view: raw.can_view !== false,
                        can_edit: raw.can_edit !== false,
                        can_create_offer: raw.can_create_offer !== false && raw.can_create !== false,
                        current_employee_id: Number(raw.current_employee_id || raw.employee_id || payload?.current_employee_id || 0),
                        message: raw.message || raw.note || payload?.message || ''
                    };
                }

                function getTeamAccess() {
                    const access = normalizeTeamAccessPayload(state.teamAccess || {});
                    const team = uniqueTeamMembers(access.team || access.teams || access.team_members || []);

                    return {
                        mode: access.access_mode === 'team_only' ? 'team_only' : 'all',
                        team: team,
                        canView: access.can_view !== false,
                        canEdit: access.can_edit !== false,
                        canCreate: access.can_create_offer !== false && access.can_create !== false,
                        currentEmployeeId: Number(access.current_employee_id || access.employee_id || 0),
                        message: access.message || access.note || ''
                    };
                }

                async function loadOfferTeamAccess(force = false) {
                    const url = folderApp.dataset.teamSaveUrl;
                    if (!url) return;

                    // Do not skip only because a team array exists in folder data.
                    // The offer endpoint is the source of truth for access_mode/can_edit.
                    if (!force && state.teamAccessLoaded === true) return;

                    try {
                        const json = await fetchJson(url, { method: 'GET' });
                        if (json && json.success !== false) {
                            state.teamAccess = normalizeTeamAccessPayload(json.team_access || json);
                            state.teamAccessLoaded = true;
                        }
                    } catch (error) {
                        console.warn('Team konnte nicht geladen werden:', error);
                    }
                }

                function getAccessPersonName(person) {
                    const direct = person?.employee_name || person?.full_name || person?.name_full;
                    if (direct) return direct;
                    const name = [person?.name, person?.lastname].filter(Boolean).join(' ').trim();
                    return name || (person?.employee_id ? `Mitarbeiter #${person.employee_id}` : 'Mitarbeiter');
                }

                function getAccessPersonImage(person) {
                    const defaultAvatar = folderApp.dataset.defaultAvatar || '';
                    const image = person?.employee_image || person?.image || person?.avatar || person?.employee?.image || '';
                    if (!image) return defaultAvatar;
                    if (/^https?:\/\//i.test(image) || image.startsWith('/')) return image;
                    return `${@json(asset('images/employee'))}/${image}`;
                }

                function getAccessPersonId(person) {
                    const rawId = person?.employee_id ?? person?.id ?? person?.employee?.id ?? null;
                    return Number(rawId || 0);
                }

                function uniqueTeamMembers(team) {
                    const seen = new Set();

                    return safeArray(team)
                        .filter(Boolean)
                        .filter(person => {
                            const employeeId = getAccessPersonId(person);
                            const fallbackKey = [
                                getAccessPersonName(person),
                                person?.employee_email || person?.email || '',
                                getAccessPersonImage(person)
                            ].join('|').toLowerCase();

                            const key = employeeId ? `id:${employeeId}` : `fallback:${fallbackKey}`;

                            if (seen.has(key)) {
                                return false;
                            }

                            seen.add(key);
                            return true;
                        });
                }


                function openTeamAccessModal() {
                    const modal = document.getElementById('team-access-modal');
                    if (!modal) return;
                    modal.style.display = 'flex';
                    renderTeamAccessPanel();
                }
                window.openTeamAccessModal = openTeamAccessModal;

                function closeTeamAccessModal() {
                    const modal = document.getElementById('team-access-modal');
                    if (modal) modal.style.display = 'none';
                }
                window.closeTeamAccessModal = closeTeamAccessModal;

                function renderTeamInlineSummary() {
                    const access = getTeamAccess();
                    const list = document.getElementById('team-inline-members');
                    const stateEl = document.getElementById('team-inline-access-state');
                    if (list) {
                        if (!access.team.length) {
                            list.innerHTML = `<span class="of-presence-empty">Kein Team zugeordnet</span>`;
                        } else {
                            const visible = access.team.slice(0, 4);
                            const rest = access.team.length - visible.length;
                            list.innerHTML = visible.map(person => `
                                                                                    <span class="of-team-inline-person" title="${esc(getAccessPersonName(person))}">
                                                                                        <img class="of-team-inline-avatar" src="${esc(getAccessPersonImage(person))}" onerror="this.src='${esc(folderApp.dataset.defaultAvatar || '')}'" alt="">
                                                                                        <span>${esc(getAccessPersonName(person))}</span>
                                                                                    </span>
                                                                                `).join('') + (rest > 0 ? `<span class="of-team-inline-more">+${rest}</span>` : '');
                        }
                    }
                    if (stateEl) {
                        stateEl.className = `of-access-pill ${access.canEdit ? 'allowed' : 'blocked'}`;
                        stateEl.textContent = access.canEdit ? 'Sie dürfen bearbeiten' : 'Nur Ansicht';
                    }
                }

                function renderTeamAccessPanel() {
                    renderTeamInlineSummary();
                    const card = document.getElementById('team-access-card');
                    const access = getTeamAccess();
                    if (!card) {
                        renderAccessControlledButtons();
                        return;
                    }
                    const stateEl = document.getElementById('team-access-state');
                    const summaryEl = document.getElementById('team-access-summary');
                    const membersEl = document.getElementById('team-access-members');
                    const helpEl = document.getElementById('team-access-help');
                    const allOption = document.getElementById('access-option-all');
                    const teamOption = document.getElementById('access-option-team');

                    document.querySelectorAll('input[name="offer_access_mode"]').forEach(input => {
                        input.checked = input.value === access.mode;
                        input.disabled = !access.canEdit;
                    });

                    allOption?.classList.toggle('is-active', access.mode === 'all');
                    teamOption?.classList.toggle('is-active', access.mode === 'team_only');

                    if (stateEl) {
                        stateEl.className = `of-access-state ${access.canEdit ? 'allowed' : 'blocked'}`;
                        stateEl.textContent = access.canEdit ? 'Sie dürfen bearbeiten' : 'Nur Ansicht';
                    }

                    if (summaryEl) {
                        if (access.mode === 'team_only') {
                            summaryEl.innerHTML = access.canEdit
                                ? 'Dieser Ordner ist auf das Angebotsteam beschränkt. Sie sind im Team und dürfen bearbeiten.'
                                : 'Dieser Ordner ist auf das Angebotsteam beschränkt. Sie können die Inhalte ansehen, aber nicht bearbeiten.';
                        } else {
                            summaryEl.innerHTML = 'Dieser Ordner ist für alle berechtigten Mitarbeiter sichtbar und bearbeitbar.';
                        }
                    }

                    if (membersEl) {
                        if (!access.team.length) {
                            membersEl.innerHTML = `<div class="of-access-empty">Noch kein Angebotsteam zugewiesen. Bei „Nur Angebotsteam“ sollte zuerst ein Team in Kanban/Kundenprofil/Angebot gesetzt werden.</div>`;
                        } else {
                            membersEl.innerHTML = access.team.map(person => `
                                                                                    <div class="of-access-person" title="${esc(getAccessPersonName(person))}">
                                                                                        <img class="of-access-avatar" src="${esc(getAccessPersonImage(person))}" onerror="this.src='${esc(folderApp.dataset.defaultAvatar || '')}'" alt="">
                                                                                        <span class="of-access-person-meta">
                                                                                            <span class="of-access-person-name">${esc(getAccessPersonName(person))}</span>
                                                                                            <span class="of-access-person-role">${Number(person.employee_id || person.id) === access.currentEmployeeId ? 'Sie' : 'Teammitglied'}</span>
                                                                                        </span>
                                                                                    </div>
                                                                                `).join('');
                        }
                    }

                    if (helpEl) {
                        helpEl.textContent = access.canEdit
                            ? 'Sie können die Sichtbarkeit ändern. Das Team selbst wird zentral über Angebot/Kanban verwaltet.'
                            : 'Sie haben keine Bearbeitungsrechte für diese Einstellung.';
                    }

                    renderAccessControlledButtons();
                }

                function renderAccessControlledButtons() {
                    const access = getTeamAccess();
                    const editable = access.canEdit;
                    const loadBtn = document.getElementById('btn-load-offer');

                    const selectors = [
                        '.of-doc-toggle',
                        '[onclick="deleteOffer()"]',
                        '[data-action="upload-attachment"]',
                        '[data-action="save-agb"]'
                    ];

                    if (loadBtn) {
                        loadBtn.dataset.accessAllowed = editable ? '1' : '0';
                        loadBtn.classList.toggle('soft', !editable);
                        loadBtn.title = editable ? '' : 'Nur Teammitglieder dürfen dieses Angebot bearbeiten.';
                    }

                    selectors.forEach(selector => {
                        document.querySelectorAll(selector).forEach(el => {
                            el.disabled = !editable;
                            el.style.pointerEvents = editable ? '' : 'none';
                            el.style.opacity = editable ? '' : '.55';
                            el.title = editable ? '' : 'Keine Bearbeitungsberechtigung.';
                        });
                    });
                }

                async function refreshTeamAccessPanel() {
                    state.teamAccessLoaded = false;
                    await loadOfferTeamAccess(true);
                    renderTeamAccessPanel();
                }
                window.refreshTeamAccessPanel = refreshTeamAccessPanel;

                async function saveTeamAccessMode(mode) {
                    mode = mode === 'team_only' ? 'team_only' : 'all';
                    const access = getTeamAccess();

                    if (!access.canEdit) {
                        renderTeamAccessPanel();
                        showCustomToast('Keine Berechtigung', 'Nur berechtigte Mitarbeiter können die Sichtbarkeit ändern.', 'error');
                        return;
                    }

                    const url = folderApp.dataset.teamSaveUrl;
                    if (!url) {
                        renderTeamAccessPanel();
                        showCustomToast('Route fehlt', 'Die Route zum Speichern der Team-Berechtigung ist nicht gesetzt.', 'error');
                        return;
                    }

                    const employeeIds = access.team
                        .map(person => Number(person.employee_id || person.id || 0))
                        .filter(Boolean);

                    document.querySelectorAll('input[name="offer_access_mode"]').forEach(input => {
                        input.disabled = true;
                    });

                    try {
                        const json = await fetchJson(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify({
                                employee_ids: employeeIds,
                                stage: String(state.detail?.document_status || state.folder?.document_status || 'offer'),
                                offer_team_access_mode: mode,
                                access_mode: mode
                            })
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Berechtigung konnte nicht gespeichert werden.');
                        }

                        state.teamAccess = normalizeTeamAccessPayload(json.team_access || json);
                        state.teamAccessLoaded = true;

                        renderTeamAccessPanel();
                        showCustomToast('Berechtigung gespeichert', mode === 'team_only'
                            ? 'Nur das Angebotsteam darf diesen Ordner bearbeiten.'
                            : 'Alle berechtigten Mitarbeiter dürfen diesen Ordner bearbeiten.'
                        );
                    } catch (error) {
                        state.teamAccessLoaded = false;
                        await loadOfferTeamAccess(true);
                        renderTeamAccessPanel();
                        showCustomToast('Fehler', error.message || 'Berechtigung konnte nicht gespeichert werden.', 'error');
                    }
                }
                window.saveTeamAccessMode = saveTeamAccessMode;


                function renderKanban() {
                    const kanbanRoot = document.getElementById('kanban-columns');
                    const badge = document.getElementById('kanban-list-badge');
                    if (!kanbanRoot) return;

                    let currentStatus = getFolderStatus();
                    const labels = getWorkflowStatusLabels();
                    const allStatusKeys = getWorkflowStatusKeys();
                    if (!allStatusKeys.includes(currentStatus)) {
                        currentStatus = normalizeWorkflowStatusForDocument(currentStatus);
                    }
                    const customerLines = getOfferCustomerLines();
                    const productLabel = getProductLabel();
                    const objectLabel = getObjectLabel();
                    const access = (typeof getTeamAccess === 'function') ? getTeamAccess() : { team: [] };

                    const createdAtRaw = state.detail?.created_at || state.folder?.created_at || state.offer?.created_at;
                    const updatedAtRaw = state.detail?.updated_at || state.folder?.updated_at || state.offer?.updated_at;
                    const createdAt = formatDateTimeValue(createdAtRaw);
                    const updatedAt = formatDateTimeValue(updatedAtRaw);
                    const net = money(state.detail?.total_net || 0);
                    const gross = money(state.detail?.total_gross || 0);
                    const tabMetrics = (typeof getTabMetrics === 'function') ? getTabMetrics() : { material: 0, labor: 0 };
                    const materialListCount = Number(tabMetrics.material || 0);
                    const laborListCount = Number(tabMetrics.labor || 0);
                    const uploadedDate = createdAt && createdAt !== '-' ? createdAt : formatDateTimeValue(state.folder?.created_at || state.offer?.created_at || state.detail?.created_at);
                    const documentLabel = getDocumentStatus() === 'deal' ? 'Auftrag' : 'Angebot';
                    const customerName = customerLines[0] || 'Unbekannter Kunde';
                    const locationLine = customerLines.slice(1).join(', ') || objectLabel || '-';

                    if (badge) {
                        badge.textContent = `${allStatusKeys.length} Spalten · 1 Eintrag`;
                    }

                    const iconForStatus = (status) => {
                        const s = String(status || '');
                        if (s.includes('angebot') || ['sent', 'proposal', 'accepted', 'rejected'].includes(s)) {
                            return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>`;
                        }
                        if (s.includes('montage') || s.includes('ausfuehrung')) {
                            return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.8-3.8a6 6 0 0 1-7.9 7.9l-6.6 6.6a2 2 0 0 1-2.8-2.8l6.6-6.6a6 6 0 0 1 7.9-7.9l-4 3.6z"/></svg>`;
                        }
                        if (s.includes('auftrag') || s.includes('material') || s.includes('rechnung')) {
                            return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.3 7 12 12l8.7-5"/></svg>`;
                        }
                        if (s.includes('abschluss') || s.includes('abgeschlossen') || s.includes('bezahlt') || s.includes('angenommen') || s === 'won') {
                            return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><path d="M4 22V15"/></svg>`;
                        }
                        return `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>`;
                    };

                    const buildTeamHtml = () => {
                        const team = safeArray(access.team || []);
                        if (!team.length) {
                            return `<span class="of-kanban-ticket-team-pill">Teams 0</span>`;
                        }

                        const visible = team.slice(0, 3).map(person => `
                                                                                <img
                                                                                    class="of-kanban-ticket-avatar"
                                                                                    src="${esc(getAccessPersonImage(person))}"
                                                                                    onerror="this.src='${esc(folderApp.dataset.defaultAvatar || '')}'"
                                                                                    title="${esc(getAccessPersonName(person))}"
                                                                                    alt=""
                                                                                >
                                                                            `).join('');

                        return `${visible}<span class="of-kanban-ticket-team-pill">Teams ${team.length}</span>`;
                    };

                    const ticketHtml = `
                                                                            <div class="of-kanban-ticket" draggable="true" data-kanban-ticket="1" data-current-status="${esc(currentStatus)}" title="Karte ziehen und in eine andere Spalte fallen lassen">
                                                                                <div class="of-kanban-ticket-head">
                                                                                    <div class="of-kanban-ticket-name">${esc(customerName)}</div>
                                                                                    <div class="of-kanban-ticket-meta">
                                                                                        <span>⌂ ${esc(locationLine)}</span>
                                                                                        <span class="of-kanban-ticket-product">▣ ${esc(productLabel)}</span>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="of-kanban-ticket-team">
                                                                                    ${buildTeamHtml()}
                                                                                </div>

                                                                                <div class="of-kanban-ticket-date">
                                                                                    <div><strong>Seit:</strong> ${esc(createdAt)}</div>
                                                                                    <div><strong>Geändert:</strong> ${esc(updatedAt)}</div>
                                                                                    <div><strong>Netto:</strong> ${esc(net)} · <strong>Brutto:</strong> ${esc(gross)}</div>
                                                                                </div>

                                                                                <div class="of-kanban-ticket-stats">
                                                                                    <button type="button" class="of-kanban-ticket-stat" onclick="switchTab('material')" title="Materialliste öffnen">
                                                                                        <span class="of-kanban-ticket-stat-label">Materialliste</span>
                                                                                        <span class="of-kanban-ticket-stat-value">${materialListCount}</span>
                                                                                    </button>
                                                                                    <button type="button" class="of-kanban-ticket-stat" onclick="switchTab('labor')" title="Lohnliste öffnen">
                                                                                        <span class="of-kanban-ticket-stat-label">Lohnliste</span>
                                                                                        <span class="of-kanban-ticket-stat-value">${laborListCount}</span>
                                                                                    </button>
                                                                                    <span class="of-kanban-ticket-stat" title="Hochgeladen am">
                                                                                        <span class="of-kanban-ticket-stat-label">Hochgeladen</span>
                                                                                        <span class="of-kanban-ticket-stat-value">${esc(uploadedDate)}</span>
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        `;

                    const configuredStages = (typeof getConfiguredKanbanStages === 'function')
                        ? getConfiguredKanbanStages(getDocumentStatus(), true)
                        : allStatusKeys.map((key, i) => ({ key, color: '#93c21c', position: i + 1 }));

                    const columnsHtml = allStatusKeys.map((status, index) => {
                        const stageConfig = configuredStages.find(stage => stage.key === status) || {};
                        const isCurrent = status === currentStatus;
                        const title = labels[status] || status;
                        const count = isCurrent ? 1 : 0;
                        const placeholder = `${title} suchen...`;
                        const stageColor = stageConfig.color || '#93c21c';

                        return `
                                                                                <div class="of-kanban-col-screen" data-kanban-column="${esc(status)}" data-kanban-position="${index + 1}" style="--kanban-stage-color:${esc(stageColor)};">
                                                                                    <button
                                                                                        type="button"
                                                                                        class="of-kanban-col-head"
                                                                                        data-workflow-status="${esc(status)}"
                                                                                        ${isCurrent ? 'disabled' : ''}
                                                                                        title="${esc(title)}"
                                                                                    >
                                                                                        <span class="of-kanban-col-title">
                                                                                            <span>${esc(title)}</span>
                                                                                        </span>
                                                                                        <span class="of-kanban-col-count">${count}</span>
                                                                                    </button>

                                                                                    <div class="of-kanban-col-filter">
                                                                                        <input type="text" placeholder="${esc(placeholder)}" aria-label="${esc(placeholder)}">
                                                                                        <button type="button" title="Suchen">⌄</button>
                                                                                    </div>

                                                                                    <div class="of-kanban-col-body" data-kanban-dropzone="1" data-workflow-status="${esc(status)}" data-kanban-position="${index + 1}">
                                                                                        <div class="of-kanban-drop-indicator"></div>
                                                                                        <div class="of-kanban-position-preview">Ablegen auf Position ${index + 1}: ${esc(title)}</div>
                                                                                        ${isCurrent ? ticketHtml : '<div class="of-kanban-empty-lane"></div>'}
                                                                                    </div>
                                                                                </div>
                                                                            `;
                    }).join('');

                    kanbanRoot.innerHTML = `
                                                                            <div class="of-kanban-screen">
                                                                                <div class="of-kanban-screen-top">
                                                                                    <div>
                                                                                        <div class="of-kanban-screen-title">Kanban Ansicht</div>
                                                                                        <div class="of-kanban-screen-sub">${documentLabel} · Karte per Drag & Drop verschieben</div>
                                                                                    </div>

                                                                                    <div class="of-kanban-screen-actions">
                                                                                        <button type="button" class="of-kanban-view-btn" onclick="window.toggleOfferKanbanFullscreen(this)">Vollbild</button>
                                                                                        <a href="javascript:void(0)" class="of-kanban-manager-btn" onclick="openKanbanConfigModal()">Kanban Einstellungen</a>
                                                                                        <button type="button" class="of-kanban-zoom-btn active" onclick="window.setOfferKanbanZoom(100, this)">100%</button>
                                                                                        <button type="button" class="of-kanban-zoom-btn" onclick="window.setOfferKanbanZoom(90, this)">90%</button>
                                                                                        <button type="button" class="of-kanban-zoom-btn" onclick="window.setOfferKanbanZoom(80, this)">80%</button>
                                                                                        <button type="button" class="of-kanban-zoom-btn" onclick="window.setOfferKanbanZoom(70, this)">70%</button>
                                                                                        <label class="of-kanban-toggle"><input type="checkbox" onchange="document.getElementById('offer-kanban-board')?.classList.toggle('compact', this.checked)"> Kompakt</label>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="of-kanban-scroll">
                                                                                    <div class="of-kanban-board zoom-100" id="offer-kanban-board">
                                                                                        ${columnsHtml}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        `;

                    initKanbanSortable();
                }

                window.setOfferKanbanZoom = function (zoom, btn) {
                    const board = document.getElementById('offer-kanban-board');
                    if (!board) return;

                    board.classList.remove('zoom-100', 'zoom-90', 'zoom-80', 'zoom-70');
                    board.classList.add(`zoom-${zoom}`);

                    document.querySelectorAll('.of-kanban-zoom-btn').forEach(item => item.classList.remove('active'));
                    if (btn) btn.classList.add('active');
                };

                window.toggleOfferKanbanFullscreen = function (btn) {
                    const screen = document.querySelector('.of-kanban-screen');
                    if (!screen) return;

                    const isFullscreen = screen.classList.toggle('is-fullscreen');
                    document.body.classList.toggle('of-kanban-fullscreen-open', isFullscreen);

                    if (btn) {
                        btn.textContent = isFullscreen ? 'Vollbild schließen' : 'Vollbild';
                        btn.classList.toggle('active', isFullscreen);
                    }
                };

                document.addEventListener('keydown', function (event) {
                    if (event.key !== 'Escape') return;
                    const screen = document.querySelector('.of-kanban-screen.is-fullscreen');
                    if (!screen) return;
                    screen.classList.remove('is-fullscreen');
                    document.body.classList.remove('of-kanban-fullscreen-open');
                    document.querySelectorAll('.of-kanban-screen-actions .of-kanban-view-btn').forEach(btn => {
                        if ((btn.textContent || '').includes('Vollbild')) {
                            btn.textContent = 'Vollbild';
                            btn.classList.remove('active');
                        }
                    });
                });

                async function saveKanbanMove(payload) {
                    const url = folderApp.dataset.kanbanMoveUrl;

                    return await fetchJson(url, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: JSON.stringify(payload)
                    });
                }

                function initKanbanSortable() {
                    const moveToStatus = async (targetStatus, options = {}) => {
                        const zielStatus = String(targetStatus || '').trim().toLowerCase();
                        const alterStatus = getFolderStatus();
                        const allowedStatuses = getWorkflowStatusKeys();
                        const sourceLabel = buildStatusLabel(alterStatus);
                        const targetLabel = buildStatusLabel(zielStatus);

                        if (isOfferLockedByWorkflow()) {
                            showCustomToast('Gesperrt', getOfferLockReason(), 'error');
                            return false;
                        }

                        if (!zielStatus || !allowedStatuses.includes(zielStatus)) {
                            await loadFolderData();
                            return false;
                        }

                        if (zielStatus === alterStatus) {
                            return false;
                        }

                        const reasonText = await openStatusReasonModal(zielStatus, alterStatus);

                        if (!reasonText || !String(reasonText).trim()) {
                            await loadFolderData();
                            return false;
                        }

                        try {
                            const json = await saveKanbanMove({
                                status: zielStatus,
                                reason: String(reasonText).trim()
                            });

                            if (!json.success) {
                                throw new Error(json.message || 'Status konnte nicht gespeichert werden.');
                            }

                            state.folder = json.folder || state.folder;
                            renderStats();
                            renderKanban();
                            renderHistory();

                            showCustomToast(
                                options.fromDrag ? 'Verschoben' : 'Status aktualisiert',
                                `Von "${sourceLabel}" nach "${targetLabel}" verschoben.`
                            );

                            return true;
                        } catch (error) {
                            await loadFolderData();
                            showCustomToast('Fehler', error.message || 'Status konnte nicht gespeichert werden.', 'error');
                            return false;
                        }
                    };

                    document.querySelectorAll('[data-workflow-status]').forEach(btn => {
                        if (btn.dataset.clickReady === '1') return;

                        btn.addEventListener('click', async function () {
                            await moveToStatus(this.dataset.workflowStatus, { fromDrag: false });
                        });

                        btn.dataset.clickReady = '1';
                    });

                    const ticket = document.querySelector('[data-kanban-ticket="1"]');
                    const columns = Array.from(document.querySelectorAll('[data-kanban-column]'));
                    const dropzones = Array.from(document.querySelectorAll('[data-kanban-dropzone]'));

                    if (!ticket || ticket.dataset.dragReady === '1') return;

                    ticket.addEventListener('dragstart', function (event) {
                        if (isOfferLockedByWorkflow()) {
                            event.preventDefault();
                            showCustomToast('Gesperrt', getOfferLockReason(), 'error');
                            return;
                        }

                        const current = getFolderStatus();
                        event.dataTransfer.effectAllowed = 'move';
                        event.dataTransfer.setData('text/plain', current);
                        event.dataTransfer.setData('application/x-offer-folder-status', current);
                        this.classList.add('is-dragging');
                    });

                    ticket.addEventListener('dragend', function () {
                        this.classList.remove('is-dragging');
                        columns.forEach(column => column.classList.remove('is-drag-over'));
                    });

                    dropzones.forEach(zone => {
                        if (zone.dataset.dropReady === '1') return;

                        zone.addEventListener('dragover', function (event) {
                            event.preventDefault();
                            event.dataTransfer.dropEffect = 'move';
                            const column = this.closest('[data-kanban-column]');
                            columns.forEach(item => item.classList.toggle('is-drag-over', item === column));
                            const position = Number(this.dataset.kanbanPosition || column?.dataset.kanbanPosition || 0);
                            const targetStatus = String(this.dataset.workflowStatus || '').trim().toLowerCase();
                            const preview = this.querySelector('.of-kanban-position-preview');
                            if (preview) {
                                preview.textContent = `Ablegen auf Position ${position}: ${buildStatusLabel(targetStatus)}`;
                            }
                        });

                        zone.addEventListener('dragleave', function (event) {
                            const column = this.closest('[data-kanban-column]');
                            if (column && !column.contains(event.relatedTarget)) {
                                column.classList.remove('is-drag-over');
                            }
                        });

                        zone.addEventListener('drop', async function (event) {
                            event.preventDefault();
                            columns.forEach(column => column.classList.remove('is-drag-over'));

                            const targetStatus = String(this.dataset.workflowStatus || '').trim().toLowerCase();
                            const currentStatus = getFolderStatus();

                            if (!targetStatus || targetStatus === currentStatus) {
                                return;
                            }

                            await moveToStatus(targetStatus, { fromDrag: true });
                        });

                        zone.dataset.dropReady = '1';
                    });

                    ticket.dataset.dragReady = '1';
                }

                function getSelectedMaterialRows(requireProductId = true) {
                    const { materialRows } = getStructureRows();
                    const selectedIndexes = Array.from(document.querySelectorAll('.material-row-check:checked'))
                        .map(cb => Number(cb.dataset.rowIndex))
                        .filter(index => !Number.isNaN(index));

                    let rows = selectedIndexes
                        .map(index => materialRows[index])
                        .filter(Boolean)
                        .filter(row => !isContainerMaterialRow(row));

                    if (requireProductId) {
                        rows = rows.filter(row => isStatusEditableMaterialRow(row));
                    }

                    return rows;
                }

                function hideSmartMaterialSidebar() {
                    const el = document.getElementById('smart-material-sidebar');
                    if (el) el.classList.remove('show');
                    state.smartSidebar.visible = false;
                }

                function showSmartMaterialSidebar() {
                    const el = document.getElementById('smart-material-sidebar');
                    if (el) el.classList.add('show');
                    state.smartSidebar.visible = true;
                }

                window.hideSmartMaterialSidebar = hideSmartMaterialSidebar;

                function buildCheapestSelectionFromComparison(data, selectedRows) {
                    const items = Array.isArray(data?.items) ? data.items : [];

                    let currentTotal = 0;
                    let cheapestTotal = 0;
                    const cheapestRows = [];

                    items.forEach((item, index) => {
                        const currentRow = selectedRows[index] || {};
                        const options = Array.isArray(item?.options) ? item.options : [];

                        const qtyTotal = Number(currentRow?.qty_total || currentRow?.qty || 0);

                        // ALWAYS use EK / purchase price per unit
                        const currentEkUnit = Number(
                            currentRow?.ek_price ??
                            currentRow?.purchase_price ??
                            0
                        );

                        const currentEkTotal = Number((currentEkUnit * qtyTotal).toFixed(2));
                        currentTotal += currentEkTotal;

                        const currentOption =
                            options.find(opt => Boolean(opt?.is_current)) || null;

                        // cheapest must be based on EK / unit -> purchase_price
                        const cheapest = [...options].sort((a, b) => {
                            const aEk = Number(a?.purchase_price ?? a?.effective_price ?? a?.price ?? Number.MAX_SAFE_INTEGER);
                            const bEk = Number(b?.purchase_price ?? b?.effective_price ?? b?.price ?? Number.MAX_SAFE_INTEGER);
                            return aEk - bEk;
                        })[0] || null;

                        const alternativeOptions = options.filter(opt => {
                            if (!currentOption) return false;

                            return Number(opt?.distributor_price_id || 0) !== Number(currentOption?.distributor_price_id || 0);
                        });

                        const hasAlternativeDistributor = alternativeOptions.length > 0;

                        const targetEkUnit = Number(
                            cheapest?.purchase_price ??
                            cheapest?.effective_price ??
                            cheapest?.price ??
                            currentEkUnit
                        );

                        const cheapestLineTotal = Number((targetEkUnit * qtyTotal).toFixed(2));
                        cheapestTotal += cheapestLineTotal;

                        const changed =
                            hasAlternativeDistributor &&
                            !!cheapest &&
                            (
                                Number(currentRow?.distributor_id || 0) !== Number(cheapest?.distributor_id || 0) ||
                                Number(currentRow?.distributor_price_id || 0) !== Number(cheapest?.distributor_price_id || 0) ||
                                Number(currentEkUnit.toFixed(4)) !== Number(targetEkUnit.toFixed(4))
                            );

                        cheapestRows.push({
                            product_id: currentRow?.product_id || null,
                            component_id: currentRow?.component_id || null,
                            article_no: currentRow?.article_no || '',
                            distributor_article_no: currentRow?.distributor_article_no || '',
                            name: currentRow?.name || '',
                            qty: Number(currentRow?.qty || 0),
                            qty_total: qtyTotal,
                            unit: currentRow?.unit || '',

                            current_distributor_id: currentRow?.distributor_id || null,
                            current_distributor_name: currentRow?.distributor_name || distributorName(currentRow?.distributor_id),
                            current_distributor_price_id: currentRow?.distributor_price_id || null,
                            current_ek_price: currentEkUnit,
                            current_ek_total: currentEkTotal,

                            target_distributor_id: hasAlternativeDistributor ? (cheapest?.distributor_id || null) : null,
                            target_distributor_name: hasAlternativeDistributor
                                ? (cheapest?.distributor_name || distributorName(cheapest?.distributor_id))
                                : null,
                            target_distributor_price_id: hasAlternativeDistributor ? (cheapest?.distributor_price_id || null) : null,
                            target_ek_price: hasAlternativeDistributor ? targetEkUnit : null,
                            target_ek_total: hasAlternativeDistributor ? cheapestLineTotal : null,

                            target_article_no: hasAlternativeDistributor
                                ? (cheapest?.article_no || currentRow?.distributor_article_no || currentRow?.article_no || '-')
                                : null,

                            availability: hasAlternativeDistributor
                                ? (cheapest?.availability || currentRow?.availability || '-')
                                : null,

                            changed,
                            has_alternative_distributor: hasAlternativeDistributor,
                            comparison_message: hasAlternativeDistributor
                                ? null
                                : (item?.comparison_message || 'Für dieses Produkt gibt es keinen zweiten Lieferantenpreis.')
                        });
                    });

                    return {
                        count: selectedRows.length,
                        current_total: Number(currentTotal.toFixed(2)),
                        cheapest_total: Number(cheapestTotal.toFixed(2)),
                        savings: Number((currentTotal - cheapestTotal).toFixed(2)),
                        rows: cheapestRows
                    };
                }

                function renderSmartMaterialSidebar(summary) {
                    const root = document.getElementById('smart-material-sidebar-body');
                    if (!root) return;

                    const rawRows = Array.isArray(summary?.rows) ? summary.rows : [];

                    const rows = rawRows.map(row => {
                        const qtyTotal = Number(row?.qty_total || row?.qty || 0);

                        const currentOption = row?.current_option || null;
                        const bestOption = row?.best_option || null;

                        const currentDistributorName =
                            currentOption?.distributor_name ||
                            row?.current_distributor_name ||
                            distributorName(row?.current_distributor_id) ||
                            '-';

                        const targetDistributorName =
                            bestOption?.distributor_name ||
                            row?.target_distributor_name ||
                            distributorName(row?.target_distributor_id) ||
                            '-';

                        const currentEkUnit = Number(
                            currentOption?.purchase_price ??
                            row?.current_ek_price ??
                            row?.ek_price ??
                            row?.purchase_price ??
                            0
                        );

                        const currentTotalEk = Number(
                            currentOption?.line_total ??
                            row?.current_ek_total ??
                            (currentEkUnit * qtyTotal) ??
                            0
                        );

                        const hasAlternativeDistributor = row?.has_alternative_distributor === true;

                        const targetEkUnit = hasAlternativeDistributor
                            ? Number(
                                bestOption?.purchase_price ??
                                bestOption?.effective_price ??
                                bestOption?.price ??
                                row?.target_ek_price ??
                                0
                            )
                            : null;

                        const targetTotalEk = hasAlternativeDistributor
                            ? Number(
                                bestOption?.line_total ??
                                row?.target_ek_total ??
                                (targetEkUnit * qtyTotal) ??
                                0
                            )
                            : null;

                        const changed = hasAlternativeDistributor && (
                            Number(currentOption?.distributor_id || row?.current_distributor_id || 0) !== Number(bestOption?.distributor_id || row?.target_distributor_id || 0) ||
                            Number(currentOption?.distributor_price_id || row?.current_distributor_price_id || 0) !== Number(bestOption?.distributor_price_id || row?.target_distributor_price_id || 0) ||
                            Number(currentEkUnit.toFixed(4)) !== Number((targetEkUnit || 0).toFixed(4))
                        );

                        return {
                            ...row,
                            current_option: currentOption,
                            best_option: bestOption,
                            current_distributor_name: currentDistributorName,
                            target_distributor_name: targetDistributorName,
                            qty_total: qtyTotal,
                            current_ek_price: currentEkUnit,
                            current_ek_total: Number(currentTotalEk.toFixed(2)),
                            target_ek_price: targetEkUnit,
                            target_ek_total: targetTotalEk !== null ? Number(targetTotalEk.toFixed(2)) : null,
                            has_alternative_distributor: hasAlternativeDistributor,
                            changed
                        };
                    });

                    const comparableRows = rows.filter(row => row.has_alternative_distributor);
                    const currentTotal = rows.reduce((sum, row) => sum + Number(row.current_ek_total || 0), 0);
                    const cheapestTotal = rows.reduce((sum, row) => {
                        return sum + Number(
                            row.has_alternative_distributor
                                ? (row.target_ek_total || row.current_ek_total || 0)
                                : (row.current_ek_total || 0)
                        );
                    }, 0);

                    const savingsTotal = Number((currentTotal - cheapestTotal).toFixed(2));
                    const changedRows = rows.filter(row => row.changed);

                    root.innerHTML = `
                                                                            <div class="of-smart-grid">
                                                                                <div class="of-smart-metric">
                                                                                    <div class="of-smart-metric-label">Vorher (EK)</div>
                                                                                    <div class="of-smart-metric-value">${esc(money(currentTotal || 0))}</div>
                                                                                </div>

                                                                                <div class="of-smart-metric">
                                                                                    <div class="of-smart-metric-label">Nachher (EK)</div>
                                                                                    <div class="of-smart-metric-value">${esc(money(cheapestTotal || 0))}</div>
                                                                                </div>

                                                                                <div class="of-smart-metric">
                                                                                    <div class="of-smart-metric-label">Ersparnis</div>
                                                                                    <div class="of-smart-metric-value success">${esc(money(savingsTotal || 0))}</div>
                                                                                </div>

                                                                                <div class="of-smart-metric">
                                                                                    <div class="of-smart-metric-label">Umstellungen</div>
                                                                                    <div class="of-smart-metric-value muted">${changedRows.length} / ${comparableRows.length}</div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="of-smart-savings-bar">
                                                                                <div>
                                                                                    <div class="of-smart-metric-label" style="margin-bottom:4px;">Gesamtvergleich</div>
                                                                                    <div class="of-smart-row-sub" style="margin-top:0;">
                                                                                        Aktueller Einkauf gegenüber vorgeschlagener günstigster Alternative
                                                                                    </div>
                                                                                </div>
                                                                                <div class="of-smart-savings-value">${esc(money(savingsTotal || 0))}</div>
                                                                            </div>

                                                                            <div class="of-smart-list">
                                                                                <div class="of-smart-list-head">Vorher / Nachher je Position</div>
                                                                                <div class="of-smart-list-body">
                                                                                    ${rows.length
                            ? rows.map(row => `
                                                                                                <div class="of-smart-row">
                                                                                                    <div class="of-smart-row-head">
                                                                                                        <div class="of-smart-row-title">${esc(row.name || 'Material')}</div>
                                                                                                        <span class="of-smart-row-badge ${row.changed ? '' : 'same'}">
                                                                                                            ${row.has_alternative_distributor
                                    ? (row.changed ? 'Wechsel empfohlen' : 'Bereits optimal')
                                    : 'Kein Vergleich möglich'
                                }
                                                                                                        </span>
                                                                                                    </div>

                                                                                                    <div class="of-smart-row-sub" style="margin-bottom:10px;">
                                                                                                        Art.-Nr.: ${esc(row.article_no || '-')}
                                                                                                    </div>

                                                                                                    ${row.has_alternative_distributor
                                    ? `
                                                                                                                <div class="of-smart-compare">
                                                                                                                    <div class="of-smart-compare-card">
                                                                                                                        <div class="of-smart-compare-label">Vorher (EK)</div>
                                                                                                                        <div class="of-smart-compare-name">${esc(row.current_distributor_name || '-')}</div>
                                                                                                                        <div class="of-smart-compare-sub">
                                                                                                                            EK / Einheit: ${esc(money(row.current_ek_price || 0))}
                                                                                                                        </div>
                                                                                                                        <div class="of-smart-compare-price">
                                                                                                                            ${esc(money(row.current_ek_total || 0))}
                                                                                                                        </div>
                                                                                                                    </div>

                                                                                                                    <div class="of-smart-compare-arrow">→</div>

                                                                                                                    <div class="of-smart-compare-card">
                                                                                                                        <div class="of-smart-compare-label">Nachher (EK)</div>
                                                                                                                        <div class="of-smart-compare-name">${esc(row.target_distributor_name || '-')}</div>
                                                                                                                        <div class="of-smart-compare-sub">
                                                                                                                            EK / Einheit: ${esc(money(row.target_ek_price || 0))}
                                                                                                                        </div>
                                                                                                                        <div class="of-smart-compare-price success">
                                                                                                                            ${esc(money(row.target_ek_total || 0))}
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                                <div class="of-smart-row-sub" style="margin-top:10px;">
                                                                                                                    Ersparnis für diese Position:
                                                                                                                    <strong>${esc(money((row.current_ek_total || 0) - (row.target_ek_total || 0)))}</strong>
                                                                                                                    · Verfügbarkeit: ${esc(row.best_option?.availability || row.availability || '-')}
                                                                                                                </div>
                                                                                                            `
                                    : `
                                                                                                                <div class="of-smart-compare-card">
                                                                                                                    <div class="of-smart-compare-label">Aktueller EK</div>
                                                                                                                    <div class="of-smart-compare-name">${esc(row.current_distributor_name || '-')}</div>
                                                                                                                    <div class="of-smart-compare-sub">
                                                                                                                        EK / Einheit: ${esc(money(row.current_ek_price || 0))}
                                                                                                                    </div>
                                                                                                                    <div class="of-smart-compare-price">
                                                                                                                        ${esc(money(row.current_ek_total || 0))}
                                                                                                                    </div>
                                                                                                                </div>

                                                                                                                <div class="of-smart-row-sub" style="margin-top:10px;">
                                                                                                                    <strong>${esc(row.comparison_message || 'Für dieses Produkt gibt es keinen zweiten Lieferantenpreis.')}</strong>
                                                                                                                </div>
                                                                                                            `
                                }

                                                                                                    ${row.changed && row.has_alternative_distributor
                                    ? `
                                                                                                                <div style="margin-top:12px; display:flex; justify-content:flex-end;">
                                                                                                                    <button
                                                                                                                        type="button"
                                                                                                                        class="of-btn"
                                                                                                                        onclick="applySuggestedSingleChange(${Number(row.product_id)}, ${Number(row.current_distributor_id || 0)}, ${Number(row.current_distributor_price_id || 0)}, ${Number(row.target_distributor_id || 0)}, '${String(row.article_no || '').replace(/'/g, "\\'")}', '${String(row.name || '').replace(/'/g, "\\'")}', '${String(row.unit || '').replace(/'/g, "\\'")}', ${Number(row.qty || 0)})"
                                                                                                                    >
                                                                                                                        Jetzt übernehmen
                                                                                                                    </button>
                                                                                                                </div>
                                                                                                            `
                                    : ''
                                }
                                                                                                </div>
                                                                                            `).join('')
                            : `<div class="of-smart-empty">Keine Daten vorhanden.</div>`
                        }
                                                                                </div>
                                                                            </div>

                                                                            <div class="of-smart-actions">
                                                                                <button type="button" class="of-btn" onclick="confirmApplyCheapestAlternative()" ${changedRows.length ? '' : 'disabled'}>
                                                                                    Alle Vorschläge übernehmen
                                                                                </button>

                                                                                <button type="button" class="of-btn soft" onclick="refreshSmartMaterialSidebar()">
                                                                                    Neu berechnen
                                                                                </button>
                                                                            </div>
                                                                        `;

                    showSmartMaterialSidebar();
                }
                async function refreshSmartMaterialSidebar() {
                    const selectedRows = getSelectedMaterialRows(true);

                    if (!selectedRows.length) {
                        hideSmartMaterialSidebar();
                        return;
                    }

                    const root = document.getElementById('smart-material-sidebar-body');
                    if (root) {
                        root.innerHTML = `<div class="of-smart-empty">Günstigste Alternative wird berechnet...</div>`;
                    }

                    showSmartMaterialSidebar();

                    try {
                        const url = folderApp.dataset.materialComparisonUrl;
                        const json = await fetchJson(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify({
                                items: selectedRows.map(row => ({
                                    product_id: row.product_id,
                                    name: row.name,
                                    qty: row.qty,
                                    unit: row.unit,
                                    article_no: row.article_no,
                                    current_distributor_id: row.distributor_id,
                                    current_distributor_price_id: row.distributor_price_id,
                                    current_price: row.ek_price
                                }))
                            })
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Preisvergleich konnte nicht geladen werden.');
                        }

                        const summary = buildCheapestSelectionFromComparison(json, selectedRows);
                        state.smartSidebar.summary = summary;
                        renderSmartMaterialSidebar(summary);
                    } catch (error) {
                        if (root) {
                            root.innerHTML = `<div class="of-smart-empty">${esc(error.message || 'Berechnung fehlgeschlagen.')}</div>`;
                        }
                    }
                }

                window.refreshSmartMaterialSidebar = refreshSmartMaterialSidebar;

                async function confirmApplyCheapestAlternative() {
                    const summary = state.smartSidebar.summary;

                    if (!summary || !Array.isArray(summary.rows) || !summary.rows.length) {
                        alert('Keine Alternativen vorhanden.');
                        return;
                    }

                    const changedRows = summary.rows.filter(row => row.changed);

                    if (!changedRows.length) {
                        alert('Es gibt keine günstigeren Alternativen zum Übernehmen.');
                        return;
                    }

                    const confirmed = window.confirm(
                        `Möchten Sie wirklich ${changedRows.length} Materialposition(en) auf die günstigste Alternative umstellen?\n\n` +
                        `Vorher: ${money(summary.current_total)}\n` +
                        `Nachher: ${money(summary.cheapest_total)}\n` +
                        `Ersparnis: ${money(summary.savings)}`
                    );

                    if (!confirmed) return;

                    await applyCheapestAlternativeBulk(changedRows);
                }

                window.confirmApplyCheapestAlternative = confirmApplyCheapestAlternative;

                async function applyCheapestAlternativeBulk(rows) {
                    const url = folderApp.dataset.materialChangeUrl;
                    if (!url) {
                        alert('Keine Änderungs-URL gefunden.');
                        return;
                    }

                    if (isOfferLockedByWorkflow()) {
                        showCustomToast('Gesperrt', getOfferLockReason(), 'error');
                        return;
                    }

                    try {
                        const payload = {
                            items: rows.map(row => ({
                                product_id: row.product_id,
                                article_no: row.article_no,
                                name: row.name,
                                qty: row.qty,
                                unit: row.unit,
                                current_distributor_id: row.current_distributor_id,
                                current_distributor_price_id: row.current_distributor_price_id,
                                target_distributor_id: row.target_distributor_id
                            }))
                        };

                        const json = await fetchJson(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify(payload)
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Bulk-Änderung konnte nicht übernommen werden.');
                        }

                        await loadFolderData();
                        hideSmartMaterialSidebar();

                        showCustomToast(
                            'Günstigste Alternative übernommen',
                            `${rows.length} Materialposition(en) wurden erfolgreich aktualisiert.`
                        );
                    } catch (error) {
                        alert(error.message || 'Bulk-Änderung konnte nicht übernommen werden.');
                    }
                }

                async function applySuggestedSingleChange(productId, currentDistributorId, currentDistributorPriceId, targetDistributorId, articleNo, name, unit, qty) {
                    const url = folderApp.dataset.materialChangeUrl;
                    if (!url) {
                        alert('Keine Änderungs-URL gefunden.');
                        return;
                    }

                    try {
                        const payload = {
                            items: [{
                                product_id: Number(productId),
                                article_no: articleNo || '',
                                name: name || '',
                                qty: Number(qty || 0),
                                unit: unit || '',
                                current_distributor_id: currentDistributorId ? Number(currentDistributorId) : null,
                                current_distributor_price_id: currentDistributorPriceId ? Number(currentDistributorPriceId) : null,
                                target_distributor_id: Number(targetDistributorId)
                            }]
                        };

                        const json = await fetchJson(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify(payload)
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Änderung konnte nicht übernommen werden.');
                        }

                        await loadFolderData();
                        await refreshSmartMaterialSidebar();

                        showCustomToast(
                            'Position aktualisiert',
                            `${name || 'Materialposition'} wurde auf die vorgeschlagene Alternative umgestellt.`
                        );
                    } catch (error) {
                        alert(error.message || 'Änderung konnte nicht übernommen werden.');
                    }
                }

                window.applySuggestedSingleChange = applySuggestedSingleChange;



                async function openMaterialComparisonModal() {
                    const allSelected = getSelectedMaterialRows(false);


                    if (!allSelected.length) {
                        alert('Bitte wählen Sie zuerst mindestens eine Materialposition aus.');
                        return;
                    }

                    const selectedRows = getSelectedMaterialRows(true);

                    if (!selectedRows.length) {
                        alert('Die ausgewählten Positionen sind manuelle Einträge oder Sets ohne Katalogverknüpfung.');
                        return;
                    }



                    const url = folderApp.dataset.materialComparisonUrl;
                    if (!url) {
                        alert('Keine Vergleichs-URL gefunden.');
                        return;
                    }

                    const modal = document.getElementById('material-comparison-modal');
                    const body = document.getElementById('material-comparison-body');

                    if (!modal || !body) return;

                    modal.style.display = 'flex';
                    body.innerHTML = `<div class="of-empty">Vergleichsdaten werden geladen...</div>`;

                    try {
                        const payload = {
                            items: selectedRows.map(row => ({
                                product_id: parseInt(row.product_id, 10),
                                name: row.name,
                                qty: parseFloat(row.qty || 0),
                                unit: row.unit,
                                article_no: row.article_no,
                                current_distributor_id: row.distributor_id ? parseInt(row.distributor_id, 10) : null,
                                current_distributor_price_id: row.distributor_price_id ? parseInt(row.distributor_price_id, 10) : null,
                                current_price: parseFloat(row.unit_price || 0)
                            }))
                        };

                        const json = await fetchJson(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify(payload)
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Vergleich konnte nicht geladen werden.');
                        }

                        renderMaterialComparisonModal(json);
                    } catch (error) {
                        body.innerHTML = `<div class="of-empty">${esc(error.message || 'Vergleich konnte nicht geladen werden.')}</div>`;
                    }
                }

                window.openMaterialComparisonModal = openMaterialComparisonModal;

                function openIdsRequestPrice() {
                    const url = folderApp.dataset.idsRequestPriceUrl;

                    if (!url) {
                        showCustomToast('Fehler', 'IDS-Route wurde nicht gefunden.', 'error');
                        return;
                    }

                    const selectedRows = getSelectedMaterialRows(false);

                    if (!selectedRows.length) {
                        const confirmed = window.confirm(
                            'Es wurden keine Materialpositionen ausgewählt. Soll die IDS-Anfrage für die gesamte Materialliste geöffnet werden?'
                        );

                        if (!confirmed) return;

                        window.open(url, '_blank');
                        return;
                    }

                    const params = new URLSearchParams();

                    selectedRows.forEach((row, index) => {
                        params.append(`items[${index}][product_id]`, row.product_id || '');
                        params.append(`items[${index}][component_id]`, row.component_id || '');
                        params.append(`items[${index}][article_no]`, row.article_no || '');
                        params.append(`items[${index}][distributor_article_no]`, row.distributor_article_no || '');
                        params.append(`items[${index}][name]`, row.name || '');
                        params.append(`items[${index}][qty]`, row.qty_total || row.qty || 0);
                        params.append(`items[${index}][unit]`, row.unit || '');
                    });

                    window.open(`${url}?${params.toString()}`, '_blank');
                }

                window.openIdsRequestPrice = openIdsRequestPrice;


                function closeMaterialComparisonModal() {
                    const modal = document.getElementById('material-comparison-modal');
                    if (modal) modal.style.display = 'none';

                    if (Array.isArray(state.comparisonCharts)) {
                        state.comparisonCharts.forEach(chart => {
                            try { chart.destroy(); } catch (e) { }
                        });
                    }

                    state.comparisonCharts = [];
                }

                window.closeMaterialComparisonModal = closeMaterialComparisonModal;

                function initDistributorSearch() {
                    const input = document.getElementById('distributor-search-input');
                    const cards = document.querySelectorAll('#distributor-card-list .of-dist-card');
                    if (!input) return;

                    input.addEventListener('input', function () {
                        const query = String(this.value || '').trim().toLowerCase();

                        cards.forEach(card => {
                            const haystack = String(card.dataset.distributorSearch || '');
                            card.style.display = !query || haystack.includes(query) ? '' : 'none';
                        });
                    });
                }

                async function refreshMaterialComparisonSidebar() {
                    const selectedRows = getSelectedMaterialRows(true);

                    if (!selectedRows.length) {
                        hideSmartMaterialSidebar();
                        return;
                    }

                    const root = document.getElementById('smart-material-sidebar-body');
                    if (root) {
                        root.innerHTML = `<div class="of-smart-empty">Preisvergleich wird geladen...</div>`;
                    }

                    showSmartMaterialSidebar();

                    try {
                        const url = folderApp.dataset.materialComparisonUrl;

                        const payload = {
                            items: selectedRows.map(row => ({
                                product_id: Number(row.product_id),
                                name: row.name,
                                qty: Number(row.qty || 0),
                                unit: row.unit,
                                article_no: row.article_no,
                                current_distributor_id: row.distributor_id ? Number(row.distributor_id) : null,
                                current_distributor_price_id: row.distributor_price_id ? Number(row.distributor_price_id) : null,
                                current_price: Number(row.ek_price || 0)
                            }))
                        };

                        console.log('Sidebar comparison payload:', payload);

                        const json = await fetchJson(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify(payload)
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Preisvergleich konnte nicht geladen werden.');
                        }

                        renderAllDistributorOptionsInSidebar(json, selectedRows);
                    } catch (error) {
                        if (root) {
                            root.innerHTML = `<div class="of-smart-empty">${esc(error.message || 'Preisvergleich fehlgeschlagen.')}</div>`;
                        }
                    }
                }

                function renderAllDistributorOptionsInSidebar(data, selectedRows) {
                    const root = document.getElementById('smart-material-sidebar-body');
                    if (!root) return;

                    const items = Array.isArray(data?.items) ? data.items : [];
                    const summary = Array.isArray(data?.summary) ? data.summary : [];

                    root.innerHTML = `
                                                                            <div class="of-smart-list">
                                                                                <div class="of-smart-list-head">Alle möglichen Distributor-Preise</div>
                                                                                <div class="of-smart-list-body" style="max-height:520px;">
                                                                                    ${summary.length
                            ? summary.map(distributor => {
                                const distributorItems = items.map(item => {
                                    const option = (item.options || []).find(
                                        opt => Number(opt.distributor_id) === Number(distributor.distributor_id)
                                    );

                                    if (!option) return '';

                                    return `
                                                                                                        <div class="of-smart-row" style="border-bottom:1px solid #eef2f7;">
                                                                                                            <div class="of-smart-row-title">${esc(item.product_name || '-')}</div>
                                                                                                            <div class="of-smart-row-sub">
                                                                                                                Art.-Nr.: ${esc(option.article_no || '-')}
                                                                                                                <br>Menge: ${esc(item.qty)} ${esc(item.unit || '')}
                                                                                                                <br>Preis: ${esc(money(option.price || 0))}
                                                                                                                <br>EK: ${esc(money(option.purchase_price || 0))}
                                                                                                                <br>Effektiv: ${esc(money(option.effective_price || 0))}
                                                                                                                <br>Gesamt: ${esc(money(option.line_total || 0))}
                                                                                                                <br>Verfügbarkeit: ${esc(option.availability || '-')}
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    `;
                                }).filter(Boolean).join('');

                                return `
                                                                                                    <div class="of-smart-list" style="margin-bottom:12px;">
                                                                                                        <div class="of-smart-list-head">
                                                                                                            ${esc(distributor.distributor_name)} · Gesamt: ${esc(money(distributor.total_effective || 0))}
                                                                                                        </div>
                                                                                                        <div class="of-smart-list-body">
                                                                                                            ${distributorItems || `<div class="of-smart-empty">Keine passenden Artikel.</div>`}
                                                                                                        </div>
                                                                                                    </div>
                                                                                                `;
                            }).join('')
                            : `<div class="of-smart-empty">Keine Distributorpreise gefunden.</div>`
                        }
                                                                                </div>
                                                                            </div>
                                                                        `;

                    showSmartMaterialSidebar();
                }
                async function applyDistributorChange(distributorId, distributorNameText) {
                    const selectedRows = getSelectedMaterialRows(true);

                    if (!selectedRows.length) {
                        alert('Bitte zuerst Materialpositionen auswählen.');
                        return;
                    }

                    if (isOfferLockedByWorkflow()) {
                        showCustomToast('Gesperrt', getOfferLockReason(), 'error');
                        return;
                    }

                    const url = folderApp.dataset.materialChangeUrl;
                    if (!url) {
                        alert('Keine Änderungs-URL gefunden.');
                        return;
                    }

                    try {
                        const payload = {
                            distributor_id: distributorId,
                            items: selectedRows.map(row => ({
                                product_id: row.product_id,
                                article_no: row.article_no,
                                name: row.name,
                                qty: row.qty,
                                unit: row.unit,
                                current_distributor_id: row.distributor_id,
                                current_distributor_price_id: row.distributor_price_id
                            }))
                        };

                        const json = await fetchJson(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify(payload)
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Distributor konnte nicht übernommen werden.');
                        }

                        closeMaterialComparisonModal();
                        await loadFolderData();

                        showCustomToast(
                            'Distributor übernommen',
                            `${distributorNameText} wurde in der Angebotsvorlage und im Ordner aktualisiert.`
                        );
                    } catch (error) {
                        alert(error.message || 'Distributor konnte nicht übernommen werden.');
                    }
                }

                window.applyDistributorChange = applyDistributorChange;

                function renderComparisonCharts(summary) {
                    if (typeof Chart === 'undefined') return;

                    if (Array.isArray(state.comparisonCharts)) {
                        state.comparisonCharts.forEach(chart => {
                            try { chart.destroy(); } catch (e) { }
                        });
                    }

                    state.comparisonCharts = [];

                    const totalCanvas = document.getElementById('comparison-chart-total');
                    const termsCanvas = document.getElementById('comparison-chart-terms');
                    if (!totalCanvas || !termsCanvas) return;

                    const labels = summary.map(row => row.distributor_name);
                    const totals = summary.map(row => Number(row.total_effective || 0));
                    const paymentTerms = summary.map(row => Number(row.avg_payment_terms || 0));
                    const cashDiscounts = summary.map(row => Number(row.avg_cash_discount || 0));
                    const availability = summary.map(row => Number(row.availability_ratio || 0));

                    const totalChart = new Chart(totalCanvas, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{ label: 'Gesamtpreis', data: totals }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: false
                        }
                    });

                    const termsChart = new Chart(termsCanvas, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [
                                { label: 'Zahlungsziel (Tage)', data: paymentTerms },
                                { label: 'Skonto %', data: cashDiscounts },
                                { label: 'Verfügbarkeit %', data: availability }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: false
                        }
                    });

                    state.comparisonCharts.push(totalChart, termsChart);
                }

                function initComparisonFilters(summary) {
                    const availabilityInput = document.getElementById('filter-availability');
                    const skontoInput = document.getElementById('filter-skonto');
                    const paymentTermsInput = document.getElementById('filter-payment-terms');

                    const applyFilters = () => {
                        const filteredSummary = summary.map(row => ({
                            ...row,
                            avg_payment_terms: paymentTermsInput?.checked ? row.avg_payment_terms : 0,
                            avg_cash_discount: skontoInput?.checked ? row.avg_cash_discount : 0,
                            availability_ratio: availabilityInput?.checked ? row.availability_ratio : 0
                        }));

                        renderComparisonCharts(filteredSummary);

                        document.querySelectorAll('.of-dist-card').forEach(card => {
                            const paymentEl = card.querySelector('[data-metric="payment_terms"]');
                            const skontoEl = card.querySelector('[data-metric="skonto"]');
                            const availabilityEl = card.querySelector('[data-metric="availability"]');

                            if (paymentEl) paymentEl.style.display = paymentTermsInput?.checked ? '' : 'none';
                            if (skontoEl) skontoEl.style.display = skontoInput?.checked ? '' : 'none';
                            if (availabilityEl) availabilityEl.style.display = availabilityInput?.checked ? '' : 'none';
                        });
                    };

                    [availabilityInput, skontoInput, paymentTermsInput].forEach(input => {
                        if (input) input.addEventListener('change', applyFilters);
                    });

                    applyFilters();
                }

                function renderMaterialComparisonModal(data) {
                    const body = document.getElementById('material-comparison-body');
                    if (!body) return;

                    const summary = Array.isArray(data.summary) ? data.summary : [];
                    const items = Array.isArray(data.items) ? data.items : [];

                    const bestDistributorId = summary.length ? summary[0].distributor_id : null;
                    const worstDistributorId = summary.length ? summary[summary.length - 1].distributor_id : null;

                    body.innerHTML = `
                                                                            <div class="of-compare-layout">
                                                                                <div class="of-compare-left">
                                                                                    <div class="of-compare-stats">
                                                                                        <div class="of-compare-card">
                                                                                            <div class="of-stat-label">Ausgewählte Produkte</div>
                                                                                            <div class="of-stat-value">${items.length}</div>
                                                                                            <div class="of-stat-sub">Verglichene Materialpositionen</div>
                                                                                        </div>

                                                                                        <div class="of-compare-card">
                                                                                            <div class="of-stat-label">Bester Preis</div>
                                                                                            <div class="of-stat-value">${summary.length ? esc(money(summary[0].total_effective)) : '-'}</div>
                                                                                            <div class="of-stat-sub">${summary.length ? esc(summary[0].distributor_name) : 'Keine Daten'}</div>
                                                                                        </div>

                                                                                        <div class="of-compare-card">
                                                                                            <div class="of-stat-label">Schlechtester Preis</div>
                                                                                            <div class="of-stat-value">${summary.length ? esc(money(summary[summary.length - 1].total_effective)) : '-'}</div>
                                                                                            <div class="of-stat-sub">${summary.length ? esc(summary[summary.length - 1].distributor_name) : 'Keine Daten'}</div>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="of-compare-chart">
                                                                                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-bottom:14px;">
                                                                                            <div>
                                                                                                <h4 class="of-card-title" style="margin:0 0 6px 0;">Gesamtpreis nach Distributor</h4>
                                                                                                <div class="of-sub" style="margin:0;">Preisvergleich aller verfügbaren Anbieter.</div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="of-chart-box">
                                                                                            <canvas id="comparison-chart-total"></canvas>
                                                                                        </div>
                                                                                    </div>

                                                                                    <div class="of-compare-chart">
                                                                                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-bottom:14px;">
                                                                                            <div>
                                                                                                <h4 class="of-card-title" style="margin:0 0 6px 0;">Zahlungsziel / Skonto / Verfügbarkeit</h4>
                                                                                                <div class="of-sub" style="margin:0;">Aktiviere oder deaktiviere die Kriterien für den Vergleich.</div>
                                                                                            </div>

                                                                                            <div class="of-compare-filters">
                                                                                                <label class="of-filter-chip">
                                                                                                    <input type="checkbox" id="filter-availability" checked>
                                                                                                    Verfügbarkeit
                                                                                                </label>
                                                                                                <label class="of-filter-chip">
                                                                                                    <input type="checkbox" id="filter-skonto" checked>
                                                                                                    Skonto
                                                                                                </label>
                                                                                                <label class="of-filter-chip">
                                                                                                    <input type="checkbox" id="filter-payment-terms" checked>
                                                                                                    Zahlungsziel
                                                                                                </label>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="of-chart-box">
                                                                                            <canvas id="comparison-chart-terms"></canvas>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="of-compare-right">
                                                                                    <div class="of-compare-side">
                                                                                        <div class="of-compare-side-head">
                                                                                            <div>
                                                                                                <h4 class="of-card-title" style="margin:0;">Distributor auswählen</h4>
                                                                                                <div class="of-sub" style="margin-top:6px;">
                                                                                                    Wähle einen Anbieter und übernehme ihn direkt in die Angebotsvorlage.
                                                                                                </div>
                                                                                            </div>

                                                                                            <div class="of-compare-search">
                                                                                                <input type="text" id="distributor-search-input" placeholder="Distributor suchen ...">
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="of-compare-side-body" id="distributor-card-list">
                                                                                            ${summary.map(row => {
                        const isBest = Number(row.distributor_id) === Number(bestDistributorId);
                        const isWorst = Number(row.distributor_id) === Number(worstDistributorId);

                        const matchingItems = items.map(item => {
                            const option = (item.options || []).find(opt => Number(opt.distributor_id) === Number(row.distributor_id));
                            if (!option) return '';

                            return `
                                                                                                        <div class="of-dist-item">
                                                                                                            <div class="of-dist-item-top">
                                                                                                                <div class="of-dist-item-name">${esc(item.product_name)}</div>
                                                                                                                <div class="of-badge">${esc(money(option.line_total))}</div>
                                                                                                            </div>
                                                                                                            <div class="of-dist-item-sub">
                                                                                                                <div><strong>Art.-Nr.:</strong> ${esc(option.article_no || '-')}</div>
                                                                                                                <div><strong>Menge:</strong> ${esc(item.qty)} ${esc(item.unit)}</div>
                                                                                                                <div><strong>Preis:</strong> ${esc(money(option.price))}</div>
                                                                                                                <div><strong>EK:</strong> ${esc(money(option.purchase_price))}</div>
                                                                                                                <div><strong>Verfügbarkeit:</strong> ${esc(option.availability || '-')}</div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    `;
                        }).filter(Boolean).join('');

                        return `
                                                                                                    <div class="of-dist-card ${isBest ? 'is-best' : ''} ${isWorst ? 'is-worst' : ''}" data-distributor-search="${esc((row.distributor_name || '').toLowerCase())}">
                                                                                                        <div class="of-dist-card-head">
                                                                                                            <div>
                                                                                                                <div class="of-dist-title">${esc(row.distributor_name)}</div>
                                                                                                                <div class="of-dist-sub">Vergleich für ${items.length} ausgewählte Positionen</div>
                                                                                                            </div>
                                                                                                            ${isBest ? '<span class="of-dist-rank best">Bester Preis</span>' : (isWorst ? '<span class="of-dist-rank worst">Höchster Preis</span>' : '')}
                                                                                                        </div>

                                                                                                        <div class="of-dist-card-body">
                                                                                                            <div class="of-dist-metrics">
                                                                                                                <div class="of-dist-metric" data-metric="total">
                                                                                                                    <div class="of-dist-metric-label">Gesamtpreis</div>
                                                                                                                    <div class="of-dist-metric-value">${esc(money(row.total_effective))}</div>
                                                                                                                </div>

                                                                                                                <div class="of-dist-metric" data-metric="payment_terms">
                                                                                                                    <div class="of-dist-metric-label">Zahlungsziel</div>
                                                                                                                    <div class="of-dist-metric-value">${esc(row.avg_payment_terms)} Tage</div>
                                                                                                                </div>

                                                                                                                <div class="of-dist-metric" data-metric="skonto">
                                                                                                                    <div class="of-dist-metric-label">Skonto</div>
                                                                                                                    <div class="of-dist-metric-value">${esc(row.avg_cash_discount)} %</div>
                                                                                                                </div>

                                                                                                                <div class="of-dist-metric" data-metric="availability">
                                                                                                                    <div class="of-dist-metric-label">Verfügbarkeit</div>
                                                                                                                    <div class="of-dist-metric-value">${esc(row.availability_ratio)} %</div>
                                                                                                                </div>
                                                                                                            </div>

                                                                                                            <div class="of-dist-items">
                                                                                                                ${matchingItems || '<div class="of-empty">Keine passenden Artikel für diesen Distributor.</div>'}
                                                                                                            </div>

                                                                                                            <div class="of-dist-actions">
                                                                                                                <button type="button" class="of-btn" data-distributor-id="${Number(row.distributor_id)}" data-distributor-name="${esc(row.distributor_name || '')}">
                                                                                                                    Übernehmen
                                                                                                                </button>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                `;
                    }).join('')}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        `;

                    document.querySelectorAll('#distributor-card-list [data-distributor-id]').forEach(btn => {
                        btn.addEventListener('click', () => {
                            applyDistributorChange(
                                Number(btn.dataset.distributorId),
                                String(btn.dataset.distributorName || '')
                            );
                        });
                    });

                    initDistributorSearch();
                    initComparisonFilters(summary);
                    renderComparisonCharts(summary);
                }

                function closeMaterialDetailModal() {
                    const modal = document.getElementById('material-detail-modal');
                    const compareBody = document.getElementById('material-detail-compare-body');
                    const historyBody = document.getElementById('material-detail-history-body');

                    state.materialDetail = {
                        rowIndex: null,
                        rowData: null,
                        comparison: null,
                        selectedOption: null
                    };

                    if (modal) modal.style.display = 'none';
                    if (compareBody) compareBody.innerHTML = `<div class="of-empty">Lade Vergleich...</div>`;
                    if (historyBody) historyBody.innerHTML = `<div class="of-empty">Lade Historie...</div>`;

                    switchMaterialModalTab('vergleich');
                }

                window.closeMaterialDetailModal = closeMaterialDetailModal;

                async function applySingleMaterialOption(option) {
                    const row = state.materialDetail.rowData;
                    if (!row || !option) {
                        alert('Keine Materialdaten vorhanden.');
                        return;
                    }

                    const url = folderApp.dataset.materialChangeUrl;
                    if (!url) {
                        alert('Keine Änderungs-URL gefunden.');
                        return;
                    }

                    if (isOfferLockedByWorkflow()) {
                        showCustomToast('Gesperrt', getOfferLockReason(), 'error');
                        return;
                    }

                    try {
                        const payload = {
                            distributor_id: Number(option.distributor_id),
                            items: [{
                                product_id: row.product_id,
                                article_no: row.article_no,
                                name: row.name,
                                qty: row.qty,
                                unit: row.unit,
                                current_distributor_id: row.distributor_id,
                                current_distributor_price_id: row.distributor_price_id
                            }]
                        };

                        const json = await fetchJson(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify(payload)
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Material konnte nicht aktualisiert werden.');
                        }

                        closeMaterialDetailModal();
                        await loadFolderData();

                        showCustomToast(
                            'Material aktualisiert',
                            `${option.distributor_name || 'Distributor'} wurde übernommen.`
                        );
                    } catch (error) {
                        alert(error.message || 'Material konnte nicht aktualisiert werden.');
                    }
                }

                window.applySingleMaterialOption = applySingleMaterialOption;

                function renderMaterialDetailModal(row, comparisonData) {
                    const modal = document.getElementById('material-detail-modal');
                    const title = document.getElementById('material-detail-title');
                    const sub = document.getElementById('material-detail-sub');
                    const compareBody = document.getElementById('material-detail-compare-body');
                    const historyBody = document.getElementById('material-detail-history-body');

                    if (!modal || !title || !sub || !compareBody || !historyBody) return;

                    const item = Array.isArray(comparisonData?.items) ? comparisonData.items[0] : null;
                    const options = Array.isArray(item?.options) ? item.options : [];
                    const materialHistory = normalizeMaterialHistoryEntries(row?.material_history);

                    const qtyTotal = Number(row?.qty_total || row?.qty || 0);

                    title.textContent = row.name || 'Materialdetails';
                    sub.textContent = `${row.article_no || '-'} · ${row.section_title || '-'} · ${row.parent_title || '-'}`;

                    historyBody.innerHTML = buildMaterialHistoryHtml(materialHistory);

                    if (!options.length) {
                        compareBody.innerHTML = `
                                                                                <div class="of-empty">Keine Preisvergleichsdaten für diese Position gefunden.</div>
                                                                            `;

                        initMaterialModalTabs();
                        switchMaterialModalTab('historie');
                        modal.style.display = 'flex';
                        return;
                    }

                    const currentOption =
                        options.find(opt =>
                            Number(opt?.distributor_price_id || 0) === Number(row?.distributor_price_id || 0)
                        ) ||
                        options.find(opt =>
                            Number(opt?.distributor_id || 0) === Number(row?.distributor_id || 0)
                        ) ||
                        null;

                    const sortedOptions = [...options].sort((a, b) => {
                        const aEk = Number(a?.purchase_price ?? a?.effective_price ?? a?.price ?? Number.MAX_SAFE_INTEGER);
                        const bEk = Number(b?.purchase_price ?? b?.effective_price ?? b?.price ?? Number.MAX_SAFE_INTEGER);
                        return aEk - bEk;
                    });

                    const cheapestOption = sortedOptions[0] || null;

                    const hasAlternativeDistributor = options.length > 1;

                    // Current values MUST come from matched option first, not from row
                    const currentEkUnit = Number(
                        currentOption?.purchase_price ??
                        row?.ek_price ??
                        row?.purchase_price ??
                        0
                    );

                    const currentVkUnit = Number(
                        currentOption?.price ??
                        row?.unit_price ??
                        0
                    );

                    const currentLineTotal = Number(
                        currentOption?.line_total ??
                        (currentEkUnit * qtyTotal) ??
                        0
                    );

                    const cheapestEkUnit = Number(
                        cheapestOption?.purchase_price ??
                        cheapestOption?.effective_price ??
                        cheapestOption?.price ??
                        0
                    );

                    const cheapestVkUnit = Number(
                        cheapestOption?.price ??
                        0
                    );

                    const cheapestLineTotal = Number(
                        cheapestOption?.line_total ??
                        (cheapestEkUnit * qtyTotal) ??
                        0
                    );

                    const savings = Number((currentLineTotal - cheapestLineTotal).toFixed(2));

                    compareBody.innerHTML = `
                                                                            <div class="of-material-detail-grid">
                                                                                <div class="of-material-detail-card">
                                                                                    <div class="of-material-detail-head">
                                                                                        <div class="of-material-detail-title">Aktueller Stand</div>
                                                                                        <span class="of-badge">Aktuell</span>
                                                                                    </div>
                                                                                    <div class="of-material-detail-body">
                                                                                        <div class="of-material-kv">
                                                                                            <div class="of-material-kv-label">Material</div>
                                                                                            <div class="of-material-kv-value">${esc(row.name || '-')}</div>

                                                                                            <div class="of-material-kv-label">Art.-Nr.</div>
                                                                                            <div class="of-material-kv-value">${esc(row.article_no || '-')}</div>

                                                                                            <div class="of-material-kv-label">Lieferant</div>
                                                                                            <div class="of-material-kv-value">${esc(currentOption?.distributor_name || row.distributor_name || '-')}</div>

                                                                                            <div class="of-material-kv-label">Lieferant-Nr.</div>
                                                                                            <div class="of-material-kv-value">${esc(currentOption?.article_no || row.distributor_article_no || '-')}</div>

                                                                                            <div class="of-material-kv-label">Menge</div>
                                                                                            <div class="of-material-kv-value">${esc(qtyTotal)} ${esc(row.unit || '')}</div>

                                                                                            <div class="of-material-kv-label">VK / Einheit</div>
                                                                                            <div class="of-material-kv-value">${esc(money(currentVkUnit || 0))}</div>

                                                                                            <div class="of-material-kv-label">EK / Einheit</div>
                                                                                            <div class="of-material-kv-value">${esc(money(currentEkUnit || 0))}</div>

                                                                                            <div class="of-material-kv-label">EK Gesamt</div>
                                                                                            <div class="of-material-kv-value">${esc(money(currentLineTotal || 0))}</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="of-material-detail-card">
                                                                                    <div class="of-material-detail-head">
                                                                                        <div class="of-material-detail-title">Günstigste Alternative</div>
                                                                                        <span class="of-badge">${hasAlternativeDistributor ? 'Empfehlung' : 'Keine Alternative'}</span>
                                                                                    </div>
                                                                                    <div class="of-material-detail-body">
                                                                                        ${hasAlternativeDistributor && cheapestOption
                            ? `
                                                                                                    <div class="of-material-kv">
                                                                                                        <div class="of-material-kv-label">Lieferant</div>
                                                                                                        <div class="of-material-kv-value">${esc(cheapestOption?.distributor_name || '-')}</div>

                                                                                                        <div class="of-material-kv-label">Art.-Nr.</div>
                                                                                                        <div class="of-material-kv-value">${esc(cheapestOption?.article_no || '-')}</div>

                                                                                                        <div class="of-material-kv-label">Verfügbarkeit</div>
                                                                                                        <div class="of-material-kv-value">${esc(cheapestOption?.availability || '-')}</div>

                                                                                                        <div class="of-material-kv-label">VK / Einheit</div>
                                                                                                        <div class="of-material-kv-value">${esc(money(cheapestVkUnit || 0))}</div>

                                                                                                        <div class="of-material-kv-label">EK / Einheit</div>
                                                                                                        <div class="of-material-kv-value">${esc(money(cheapestEkUnit || 0))}</div>

                                                                                                        <div class="of-material-kv-label">EK Gesamt</div>
                                                                                                        <div class="of-material-kv-value">${esc(money(cheapestLineTotal || 0))}</div>
                                                                                                    </div>

                                                                                                    <div class="of-material-savings">
                                                                                                        <span>Vorher (EK): ${esc(money(currentLineTotal || 0))}</span>
                                                                                                        <span>Nachher (EK): ${esc(money(cheapestLineTotal || 0))}</span>
                                                                                                        <span>Ersparnis: ${esc(money(savings || 0))}</span>
                                                                                                    </div>
                                                                                                `
                            : `
                                                                                                    <div class="of-empty" style="margin:0;">
                                                                                                        Für dieses Produkt gibt es keinen zweiten Lieferantenpreis.
                                                                                                    </div>
                                                                                                `
                        }
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="of-material-detail-card" style="margin-top:18px;">
                                                                                <div class="of-material-detail-head">
                                                                                    <div class="of-material-detail-title">Alle verfügbaren Alternativen</div>
                                                                                    <span class="of-badge">${options.length} Optionen</span>
                                                                                </div>
                                                                                <div class="of-material-detail-body">
                                                                                    <div class="of-material-option-list">
                                                                                        ${options.map(option => {
                            const optionEkUnit = Number(
                                option?.purchase_price ??
                                option?.effective_price ??
                                option?.price ??
                                0
                            );

                            const optionVkUnit = Number(option?.price ?? 0);
                            const optionLineTotal = Number(
                                option?.line_total ??
                                (optionEkUnit * qtyTotal) ??
                                0
                            );

                            const isCurrent =
                                Number(option?.distributor_price_id || 0) === Number(currentOption?.distributor_price_id || 0) ||
                                Number(option?.distributor_id || 0) === Number(currentOption?.distributor_id || 0);

                            const isBest =
                                Number(option?.distributor_price_id || 0) === Number(cheapestOption?.distributor_price_id || 0) ||
                                Number(option?.distributor_id || 0) === Number(cheapestOption?.distributor_id || 0);

                            return `
                                                                                                <div class="of-material-option ${isBest ? 'active' : ''}">
                                                                                                    <div class="of-material-option-top">
                                                                                                        <div class="of-material-option-name">
                                                                                                            ${esc(option.distributor_name || 'Distributor')}
                                                                                                            ${isCurrent ? ' · Aktuell' : ''}
                                                                                                            ${isBest ? ' · Beste Wahl' : ''}
                                                                                                        </div>
                                                                                                        <div class="of-material-option-price">${esc(money(optionLineTotal || 0))}</div>
                                                                                                    </div>
                                                                                                    <div class="of-material-option-sub">
                                                                                                        Art.-Nr.: ${esc(option.article_no || '-')}
                                                                                                        <br>VK / Einheit: ${esc(money(optionVkUnit || 0))}
                                                                                                        <br>EK / Einheit: ${esc(money(optionEkUnit || 0))}
                                                                                                        <br>Verfügbarkeit: ${esc(option.availability || '-')}
                                                                                                    </div>
                                                                                                    <div style="margin-top:12px; display:flex; justify-content:flex-end;">
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="of-btn ${isCurrent ? 'soft' : ''}"
                                                                                                            data-single-option='${JSON.stringify(option).replaceAll("'", '&#039;')}'
                                                                                                            ${isCurrent ? 'disabled' : ''}
                                                                                                        >
                                                                                                            ${isCurrent ? 'Aktuell gesetzt' : 'Übernehmen'}
                                                                                                        </button>
                                                                                                    </div>
                                                                                                </div>
                                                                                            `;
                        }).join('')}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        `;

                    compareBody.querySelectorAll('[data-single-option]').forEach(btn => {
                        btn.addEventListener('click', () => {
                            try {
                                const raw = btn.getAttribute('data-single-option').replaceAll('&#039;', "'");
                                const option = JSON.parse(raw);
                                applySingleMaterialOption(option);
                            } catch (e) {
                                alert('Option konnte nicht gelesen werden.');
                            }
                        });
                    });

                    initMaterialModalTabs();
                    switchMaterialModalTab('vergleich');
                    modal.style.display = 'flex';
                }

                async function openMaterialDetailModal(rowIndex) {
                    const { materialRows } = getStructureRows();
                    const row = materialRows[rowIndex];

                    if (!row) return;

                    if (!row.product_id) {
                        alert('Für diese Position ist kein Preisvergleich verfügbar.');
                        return;
                    }

                    state.materialDetail.rowIndex = rowIndex;
                    state.materialDetail.rowData = row;

                    const modal = document.getElementById('material-detail-modal');
                    const compareBody = document.getElementById('material-detail-compare-body');
                    const historyBody = document.getElementById('material-detail-history-body');
                    const title = document.getElementById('material-detail-title');
                    const sub = document.getElementById('material-detail-sub');

                    if (title) title.textContent = row.name || 'Materialdetails';
                    if (sub) sub.textContent = 'Preisvergleich wird geladen...';
                    if (compareBody) compareBody.innerHTML = `<div class="of-empty">Vergleichsdaten werden geladen...</div>`;
                    if (historyBody) historyBody.innerHTML = `<div class="of-empty">Historie wird geladen...</div>`;
                    switchMaterialModalTab('vergleich');
                    if (modal) modal.style.display = 'flex';

                    try {
                        const url = folderApp.dataset.materialComparisonUrl;
                        const json = await fetchJson(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify({
                                items: [{
                                    product_id: row.product_id,
                                    name: row.name,
                                    qty: row.qty,
                                    unit: row.unit,
                                    article_no: row.article_no,
                                    current_distributor_id: row.distributor_id,
                                    current_distributor_price_id: row.distributor_price_id,
                                    current_price: row.ek_price
                                }]
                            })
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Preisvergleich konnte nicht geladen werden.');
                        }

                        state.materialDetail.comparison = json;
                        renderMaterialDetailModal(row, json);
                    } catch (error) {
                        if (compareBody) {
                            compareBody.innerHTML = `<div class="of-empty">${esc(error.message || 'Vergleich konnte nicht geladen werden.')}</div>`;
                        }

                        if (historyBody) {
                            const materialHistory = normalizeMaterialHistoryEntries(row?.material_history);
                            historyBody.innerHTML = buildMaterialHistoryHtml(materialHistory);
                        }

                        initMaterialModalTabs();
                        switchMaterialModalTab('historie');
                    }
                }

                window.openMaterialDetailModal = openMaterialDetailModal;

                function updateMaterialSelectionState() {
                    const rowChecks = Array.from(document.querySelectorAll('.material-row-check'));
                    const checkedRows = rowChecks.filter(cb => cb.checked);
                    const allChecked = rowChecks.length > 0 && checkedRows.length === rowChecks.length;

                    rowChecks.forEach(cb => {
                        const row = cb.closest('tr');
                        if (row) row.classList.toggle('is-selected', cb.checked);
                    });

                    const selectAllA = document.getElementById('material-select-all');
                    const selectAllB = document.getElementById('material-select-all-head');
                    const selectedInfo = document.getElementById('material-selected-info');

                    if (selectAllA) selectAllA.checked = allChecked;
                    if (selectAllB) selectAllB.checked = allChecked;
                    if (selectedInfo) selectedInfo.textContent = `${checkedRows.length} ausgewählt`;

                    if (checkedRows.length) {
                        refreshSmartMaterialSidebar();
                    } else {
                        hideSmartMaterialSidebar();
                    }
                }


                function initMaterialStatusListeners() {
                    const { materialRows } = getStructureRows();

                    document.querySelectorAll('.material-move-btn').forEach(btn => {
                        btn.addEventListener('click', async function (e) {
                            e.stopPropagation();

                            const rowIndex = Number(this.dataset.rowIndex);
                            const moveTo = String(this.dataset.moveTo || 'offen');
                            const row = materialRows[rowIndex];

                            if (!row) {
                                await loadFolderData();
                                return;
                            }

                            if (!isStatusEditableMaterialRow(row)) {
                                renderMaterialList();
                                return;
                            }

                            const result = await openMaterialMoveModal([row], moveTo, 'single');
                            if (!result) return;

                            await updateMaterialOrderStatus(
                                result.rows,
                                result.move_to,
                                result.move_qty,
                                result.source_status
                            );
                        });
                    });

                    document.querySelectorAll('.material-final-btn').forEach(btn => {
                        btn.addEventListener('click', async function (e) {
                            e.stopPropagation();

                            const rowIndex = Number(this.dataset.rowIndex);
                            const sourceStatus = String(this.dataset.sourceStatus || '').toLowerCase();
                            const row = materialRows[rowIndex];

                            if (!row) {
                                await loadFolderData();
                                return;
                            }

                            if (!isStatusEditableMaterialRow(row)) {
                                renderMaterialList();
                                return;
                            }

                            if (!['lager', 'bestellen'].includes(sourceStatus)) {
                                showCustomToast('Nicht möglich', 'Final ist nur aus Lager oder Bestellen möglich.', 'error');
                                return;
                            }

                            const result = await openMaterialFinalModal([row], sourceStatus);
                            if (!result) return;

                            await updateMaterialFinalStatus(
                                result.rows,
                                result.source_status,
                                result.final_qty,
                                result.remaining_to,
                                result.reason
                            );
                        });
                    });

                    const bulkSelect = document.getElementById('bulk-status-select');
                    const bulkApply = document.getElementById('bulk-status-apply');

                    if (bulkSelect && bulkApply) {
                        bulkSelect.addEventListener('click', e => e.stopPropagation());

                        bulkSelect.addEventListener('change', () => {
                            bulkApply.style.display = bulkSelect.value ? 'inline-flex' : 'none';
                        });

                        bulkApply.addEventListener('click', async (e) => {
                            e.stopPropagation();

                            const selectedRows = getSelectedMaterialRows(true);

                            if (!selectedRows.length) {
                                alert('Bitte wählen Sie zuerst echte Produktpositionen aus.');
                                return;
                            }

                            const moveTo = bulkSelect.value;
                            if (!moveTo) {
                                alert('Bitte zuerst ein Ziel auswählen.');
                                return;
                            }

                            const result = await openMaterialMoveModal(selectedRows, moveTo, 'bulk');
                            if (!result) return;

                            await updateMaterialOrderStatus(
                                result.rows,
                                result.move_to,
                                result.move_qty,
                                result.source_status
                            );

                            bulkSelect.value = '';
                            bulkApply.style.display = 'none';
                        });
                    }
                }
                function setAllMaterialRows(checked) {
                    document.querySelectorAll('.material-row-check').forEach(cb => {
                        cb.checked = checked;
                    });

                    updateMaterialSelectionState();
                }

                function initMaterialSelection() {
                    const selectAllA = document.getElementById('material-select-all');
                    const selectAllB = document.getElementById('material-select-all-head');
                    const rowChecks = document.querySelectorAll('.material-row-check');

                    if (selectAllA) {
                        selectAllA.addEventListener('change', function () {
                            setAllMaterialRows(this.checked);
                        });
                    }

                    if (selectAllB) {
                        selectAllB.addEventListener('change', function () {
                            setAllMaterialRows(this.checked);
                        });
                    }

                    rowChecks.forEach(cb => {
                        cb.addEventListener('change', updateMaterialSelectionState);

                        const row = cb.closest('tr');
                        if (row) {
                            row.addEventListener('click', function (e) {
                                if (e.target.closest('input, button, a, label, select, option, textarea')) return;

                                const rowIndex = Number(cb.dataset.rowIndex);
                                if (!Number.isNaN(rowIndex)) {
                                    openMaterialDetailModal(rowIndex);
                                }
                            });
                        }
                    });

                    updateMaterialSelectionState();
                }

                async function updateMaterialFinalStatus(rowsToUpdate, sourceStatus, finalQty, remainingTo, reason) {
                    const url = folderApp.dataset.materialFinalUrl;
                    if (!url) {
                        showCustomToast('Fehler', 'Route für Final-Update nicht gefunden.', 'error');
                        return;
                    }

                    if (isOfferLockedByWorkflow()) {
                        showCustomToast('Gesperrt', getOfferLockReason(), 'error');
                        return;
                    }

                    const validRows = (Array.isArray(rowsToUpdate) ? rowsToUpdate : []).filter(row => isStatusEditableMaterialRow(row));

                    if (!validRows.length) {
                        showCustomToast('Fehler', 'Keine gültigen Produktpositionen ausgewählt.', 'error');
                        return;
                    }

                    const qty = Number(finalQty || 0);
                    if (!(qty > 0)) {
                        showCustomToast('Fehler', 'Bitte eine gültige Final-Menge größer als 0 eingeben.', 'error');
                        return;
                    }

                    try {
                        const payload = {
                            items: validRows.map(row => ({
                                product_id: Number(row.product_id || 0) || null,
                                component_id: Number(row.component_id || 0) || null,
                                article_no: row.article_no || '',
                                source_status: sourceStatus,
                                final_qty: qty,
                                remaining_to: remainingTo,
                                reason: reason || ''
                            }))
                        };

                        const json = await fetchJson(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify(payload)
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Finale Materialliste konnte nicht aktualisiert werden.');
                        }

                        await loadFolderData();

                        showCustomToast(
                            'Final List aktualisiert',
                            `${validRows.length} Position(en) wurden final bestätigt.`
                        );
                    } catch (error) {
                        showCustomToast(
                            'Fehler',
                            error.message || 'Finale Materialliste konnte nicht aktualisiert werden.',
                            'error'
                        );
                        await loadFolderData();
                    }
                }

                function renderMaterialList() {
                    const wrap = document.getElementById('material-list-wrap');
                    const printWrap = document.getElementById('material-print-wrap');
                    const badge = document.getElementById('material-count-badge');

                    if (!wrap || !printWrap) return;

                    const { materialRows } = getStructureRows();
                    const baseCols = getMaterialCols();
                    const documentStatus = getDocumentStatus();
                    const isOfferStatus = documentStatus === 'offer';
                    const isDealStatus = documentStatus === 'deal';

                    const cols = {
                        ...baseCols,
                        bezug: !isOfferStatus
                    };

                    const numberFormatter = new Intl.NumberFormat('de-DE', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    const formatQty = (value) => numberFormatter.format(Number(value || 0));

                    const filterLabelMap = {
                        all: 'Alle',
                        offen: 'Offen',
                        lager: 'Lager',
                        bestellen: 'Bestellen',
                        final: 'Kommissionen Materialliste'
                    };

                    const normalizedRows = materialRows.map((row, originalIndex) => ({
                        ...row,
                        __originalIndex: originalIndex,
                        order_status: row?.order_status || 'offen',
                    }));

                    const realRows = normalizedRows.filter(row => !isContainerMaterialRow(row));

                    const activeFilterBefore = state.materialFilter || 'all';

                    if (!['all', 'offen', 'lager', 'bestellen', 'final'].includes(activeFilterBefore)) {
                        state.materialFilter = 'all';
                    }

                    if (isOfferStatus && state.materialFilter !== 'all') {
                        state.materialFilter = 'all';
                    }

                    const activeFilter = state.materialFilter || 'all';
                    const allowExecutionActions = (isOfferStatus || isDealStatus);
                    const showBezugColumn = cols.bezug === true;

                    const qtyOffen = realRows.reduce((sum, row) => sum + getRowQtyForFilter(row, 'offen'), 0);
                    const qtyLager = realRows.reduce((sum, row) => sum + getRowQtyForFilter(row, 'lager'), 0);
                    const qtyBestellen = realRows.reduce((sum, row) => sum + getRowQtyForFilter(row, 'bestellen'), 0);
                    const qtyFinal = realRows.reduce((sum, row) => sum + getRowQtyForFilter(row, 'final'), 0);

                    const filteredRows = realRows.filter(row => {
                        if (activeFilter === 'all') return true;
                        return getRowQtyForFilter(row, activeFilter) > 0;
                    });

                    const quantityTotalAll = realRows.reduce((sum, row) => sum + getRowTotalQty(row), 0);
                    const quantityTotalFiltered = filteredRows.reduce((sum, row) => {
                        return sum + Number(
                            activeFilter === 'all'
                                ? getRowTotalQty(row)
                                : getRowQtyForFilter(row, activeFilter)
                        );
                    }, 0);

                    const compareBtn = document.getElementById('material-compare-btn');
                    if (compareBtn) {
                        compareBtn.disabled = !allowExecutionActions;
                        compareBtn.title = allowExecutionActions
                            ? ''
                            : 'Preisvergleich ist für diesen Dokumentstatus nicht erlaubt.';
                    }

                    const subCountAll = document.getElementById('mat-subcount-all');
                    const subCountOffen = document.getElementById('mat-subcount-offen');
                    const subCountLager = document.getElementById('mat-subcount-lager');
                    const subCountBestellen = document.getElementById('mat-subcount-bestellen');
                    const subCountFinal = document.getElementById('mat-subcount-final');

                    if (subCountAll) subCountAll.textContent = formatQty(quantityTotalAll);
                    if (subCountOffen) subCountOffen.textContent = formatQty(qtyOffen);
                    if (subCountLager) subCountLager.textContent = formatQty(qtyLager);
                    if (subCountBestellen) subCountBestellen.textContent = formatQty(qtyBestellen);
                    if (subCountFinal) subCountFinal.textContent = formatQty(qtyFinal);

                    document.querySelectorAll('.material-subtab-btn').forEach(btn => {
                        const filter = btn.dataset.materialFilter || 'all';
                        const shouldHide = isOfferStatus && filter !== 'all';

                        btn.style.display = shouldHide ? 'none' : '';
                        btn.classList.toggle('active', filter === activeFilter);
                    });

                    if (badge) {
                        badge.innerHTML = `
                                                                                ${formatQty(quantityTotalFiltered)} / ${formatQty(quantityTotalAll)} Gesamtmenge
                                                                                <span style="margin-left:8px; color:#6b7280;">Ansicht: ${esc(filterLabelMap[activeFilter] || 'Alle')}</span>
                                                                                <span style="color:#10b981; margin-left:8px;">Lager: ${formatQty(qtyLager)}</span> |
                                                                                <span style="color:#ef4444;">Bestellen: ${formatQty(qtyBestellen)}</span> |
                                                                                <span style="color:#f59e0b;">Offen: ${formatQty(qtyOffen)}</span> |
                                                                                <span style="color:#2563eb; margin-left:8px;">Kommissionen Materialliste: ${formatQty(qtyFinal)}</span>
                                                                            `;
                    }

                    if (!normalizedRows.length) {
                        wrap.innerHTML = `<div class="of-empty">Keine Materialpositionen vorhanden.</div>`;
                        printWrap.innerHTML = `<div class="of-empty">Keine Materialpositionen vorhanden.</div>`;

                        setText('print-material-count', '0');
                        setText('print-material-qty-total', '0,00');

                        renderTabCounts();
                        hideSmartMaterialSidebar();
                        return;
                    }

                    const buildImageCell = (row) => {
                        const imageUrl = getRowImage(row);

                        if (!cols.image) return '';

                        return `
                                                                                <td style="width:76px;">
                                                                                    ${imageUrl
                                ? `<img src="${esc(imageUrl)}" style="width:52px;height:52px;object-fit:cover;border-radius:12px;border:1px solid #e5e7eb;">`
                                : `<div style="width:52px;height:52px;border-radius:12px;border:1px solid #e5e7eb;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:11px;font-weight:800;">Kein Bild</div>`
                            }
                                                                                </td>
                                                                            `;
                    };

                    function buildStatusSelect(row, rowIndex) {
                        if (!showBezugColumn) return '';

                        if (!isStatusEditableMaterialRow(row)) {
                            return `
                                                                                    <td>
                                                                                        <span class="of-badge" style="opacity:.75;">
                                                                                            Ordner / Gruppe
                                                                                        </span>
                                                                                    </td>
                                                                                `;
                        }

                        const allocation = getRowAllocation(row);
                        const currentFilter = state.materialFilter || 'all';

                        const canFinalizeFromCurrentTab =
                            (currentFilter === 'lager' || currentFilter === 'bestellen') &&
                            Number(allocation[currentFilter] || 0) > 0;

                        return `
                                                                                <td>
                                                                                    <div style="display:flex; flex-direction:column; gap:8px; min-width:220px;">
                                                                                        <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                                                                            <button
                                                                                                type="button"
                                                                                                class="of-btn soft material-move-btn"
                                                                                                data-row-index="${rowIndex}"
                                                                                                data-move-to="offen"
                                                                                                style="padding:6px 10px; font-size:12px;"
                                                                                            >
                                                                                                Offen (${Number(allocation.offen || 0).toFixed(2)})
                                                                                            </button>

                                                                                            <button
                                                                                                type="button"
                                                                                                class="of-btn soft material-move-btn"
                                                                                                data-row-index="${rowIndex}"
                                                                                                data-move-to="lager"
                                                                                                style="padding:6px 10px; font-size:12px;"
                                                                                            >
                                                                                                Lager (${Number(allocation.lager || 0).toFixed(2)})
                                                                                            </button>

                                                                                            <button
                                                                                                type="button"
                                                                                                class="of-btn soft material-move-btn"
                                                                                                data-row-index="${rowIndex}"
                                                                                                data-move-to="bestellen"
                                                                                                style="padding:6px 10px; font-size:12px;"
                                                                                            >
                                                                                                Bestellen (${Number(allocation.bestellen || 0).toFixed(2)})
                                                                                            </button>

                                                                                            ${canFinalizeFromCurrentTab
                                ? `
                                                                                                        <button
                                                                                                            type="button"
                                                                                                            class="of-btn soft material-final-btn"
                                                                                                            data-row-index="${rowIndex}"
                                                                                                            data-source-status="${currentFilter}"
                                                                                                            style="padding:6px 10px; font-size:12px;"
                                                                                                        >
                                                                                                            Final (${Number(allocation.final || 0).toFixed(2)})
                                                                                                        </button>
                                                                                                    `
                                : ''
                            }
                                                                                        </div>

                                                                                        <div class="of-sub" style="margin:0;">
                                                                                            Status: ${esc(String(row.order_status || 'offen'))}
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                            `;
                    }

                    const buildRow = (row) => {
                        const cells = [];
                        const rowIndex = row.__originalIndex;
                        const hierarchyPadding = Math.max(0, (Number(row.hierarchy_level || 1) - 1) * 22);
                        const visibleQtyTotal = activeFilter === 'all'
                            ? getRowTotalQty(row)
                            : getRowQtyForFilter(row, activeFilter);

                        cells.push(`
                                                                                <td class="of-table-check">
                                                                                    ${isContainerMaterialRow(row)
                                ? ''
                                : `<input type="checkbox" class="of-check material-row-check" data-row-index="${rowIndex}">`
                            }
                                                                                </td>
                                                                            `);

                        if (cols.image) {
                            cells.push(buildImageCell(row));
                        }

                        if (cols.position) {
                            cells.push(`<td style="white-space:nowrap;font-weight:900;">${esc(row.position_no || '-')}</td>`);
                        }

                        cells.push(`
                                                                                <td>
                                                                                    <div class="of-mat-name" style="padding-left:${hierarchyPadding}px;">
                                                                                        <div class="of-mat-title">${esc(row.name || '-')}</div>
                                                                                        <div class="of-mat-meta">
                                                                                            <span class="of-mat-chip">${esc(row.level || '-')}</span>
                                                                                            ${row.parent_title && row.parent_title !== row.name ? `<span class="of-mat-chip">${esc(row.parent_title)}</span>` : ''}
                                                                                            ${row.section_title ? `<span class="of-mat-chip">${esc(row.section_title)}</span>` : ''}
                                                                                        </div>
                                                                                        ${row.description ? `<div class="of-mat-desc">${esc(row.description)}</div>` : ''}
                                                                                    </div>
                                                                                </td>
                                                                            `);

                        if (cols.article_no) {
                            cells.push(`<td>${esc(row.article_no || '-')}</td>`);
                        }

                        if (cols.distributor_article_no) {
                            cells.push(`<td>${esc(row.distributor_article_no || '-')}</td>`);
                        }

                        if (cols.distributor) {
                            cells.push(`<td>${esc(row.distributor_name || '-')}</td>`);
                        }

                        if (cols.type) {
                            cells.push(`<td>${esc(row.type_label || '-')}</td>`);
                        }

                        if (showBezugColumn) {
                            cells.push(buildStatusSelect(row, rowIndex));
                        }

                        if (cols.qty) {
                            cells.push(`<td class="num">${esc(Number(row.qty || 0).toFixed(2))}</td>`);
                        }

                        if (cols.qty_total) {
                            cells.push(`<td class="num">${esc(Number(visibleQtyTotal || 0).toFixed(2))}</td>`);
                        }

                        if (cols.unit) {
                            cells.push(`<td>${esc(row.unit || '-')}</td>`);
                        }

                        if (cols.ek_price) {
                            cells.push(`<td class="num">${esc(money(row.ek_price || 0))}</td>`);
                        }

                        if (cols.ek_total) {
                            cells.push(`<td class="num">${esc(money(row.ek_total || 0))}</td>`);
                        }

                        if (cols.unit_price) {
                            cells.push(`<td class="num">${esc(money(row.unit_price || 0))}</td>`);
                        }

                        if (cols.total) {
                            cells.push(`<td class="num">${esc(money(row.total || 0))}</td>`);
                        }

                        return `<tr class="of-material-row-click" data-material-row="${rowIndex}">${cells.join('')}</tr>`;
                    };

                    const th = [];
                    th.push(`<th class="of-table-check"><input type="checkbox" class="of-check" id="material-select-all-head"></th>`);
                    if (cols.image) th.push(`<th>Bild</th>`);
                    if (cols.position) th.push(`<th>Pos.</th>`);
                    th.push(`<th>Material</th>`);
                    if (cols.article_no) th.push(`<th>Hersteller-Nr.</th>`);
                    if (cols.distributor_article_no) th.push(`<th>Lieferant-Nr.</th>`);
                    if (cols.distributor) th.push(`<th>Lieferant</th>`);
                    if (cols.type) th.push(`<th>Typ</th>`);
                    if (showBezugColumn) th.push(`<th>Bezug</th>`);
                    if (cols.qty) th.push(`<th class="num">Menge</th>`);
                    if (cols.qty_total) th.push(`<th class="num">Gesamtmenge</th>`);
                    if (cols.unit) th.push(`<th>Einheit</th>`);
                    if (cols.ek_price) th.push(`<th class="num">EK / Einheit</th>`);
                    if (cols.ek_total) th.push(`<th class="num">EK gesamt</th>`);
                    if (cols.unit_price) th.push(`<th class="num">VK / Einheit</th>`);
                    if (cols.total) th.push(`<th class="num">VK gesamt</th>`);

                    const currentFilterLabel = filterLabelMap[activeFilter] || 'Alle';

                    const materialTableHtml = `
                                                                            <div class="of-material-toolbar">
                                                                                <div class="of-material-toolbar-left" style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                                                                                    <label class="of-selected-badge" style="${allowExecutionActions ? '' : 'opacity:.6; pointer-events:none;'}">
                                                                                        <input type="checkbox" class="of-check" id="material-select-all" ${allowExecutionActions ? '' : 'disabled'}>
                                                                                        Alle auswählen
                                                                                    </label>

                                                                                    <span class="of-selected-badge" id="material-selected-info">0 ausgewählt</span>

                                                                                    <span class="of-badge">
                                                                                        Ansicht: ${esc(currentFilterLabel)}
                                                                                    </span>

                                                                                    <span class="of-badge">
                                                                                        Gesamtmenge: ${esc(formatQty(quantityTotalFiltered))}
                                                                                    </span>

                                                                                    ${showBezugColumn
                            ? `
                                                                                                <select id="bulk-status-select" class="of-btn soft" style="height:36px;">
                                                                                                    <option value="" selected disabled>Markierte ändern in...</option>
                                                                                                    <option value="lager">Lager</option>
                                                                                                    <option value="bestellen">Bestellen</option>
                                                                                                    <option value="offen">Offen</option>
                                                                                                </select>

                                                                                                <button type="button" class="of-btn" id="bulk-status-apply" style="height:36px; display:none;">
                                                                                                    Anwenden
                                                                                                </button>
                                                                                            `
                            : `
                                                                                                <span class="of-badge" style="background:#eff6ff;border-color:#bfdbfe;color:#74b2d4;">
                                                                                                    Bezug ist im Angebot ausgeblendet
                                                                                                </span>
                                                                                            `
                        }
                                                                                </div>

                                                                                <div class="of-material-toolbar-right">
                                                                                   <details class="of-colpicker" id="material-colpicker">
                                                                                        <summary class="of-btn soft">Spalten</summary>
                                                                                        <div class="of-colpicker-menu" onclick="materialPickerKeepOpen(event)">
                                                                                            <div style="display:flex; gap:8px; margin-bottom:10px; flex-wrap:wrap;">
                                                                                                <button type="button" class="of-btn soft" style="padding:6px 10px; font-size:12px;" onclick="setMaterialColumnPreset('standard')">
                                                                                                    Standard
                                                                                                </button>
                                                                                                <button type="button" class="of-btn soft" style="padding:6px 10px; font-size:12px;" onclick="setMaterialColumnPreset('all')">
                                                                                                    Alle
                                                                                                </button>
                                                                                            </div>

                                                                                            <div class="of-colpicker-grid">
                                                                                                <label class="of-colpicker-item">
                                                                                                    <span>Bild</span>
                                                                                                    <input type="checkbox" ${cols.image ? 'checked' : ''} onchange="toggleMaterialColumn('image')">
                                                                                                </label>

                                                                                                <label class="of-colpicker-item">
                                                                                                    <span>Position</span>
                                                                                                    <input type="checkbox" ${cols.position ? 'checked' : ''} onchange="toggleMaterialColumn('position')">
                                                                                                </label>

                                                                                                <label class="of-colpicker-item">
                                                                                                    <span>Hersteller-Nr.</span>
                                                                                                    <input type="checkbox" ${cols.article_no ? 'checked' : ''} onchange="toggleMaterialColumn('article_no')">
                                                                                                </label>

                                                                                                <label class="of-colpicker-item">
                                                                                                    <span>Lieferant-Nr.</span>
                                                                                                    <input type="checkbox" ${cols.distributor_article_no ? 'checked' : ''} onchange="toggleMaterialColumn('distributor_article_no')">
                                                                                                </label>

                                                                                                <label class="of-colpicker-item">
                                                                                                    <span>Lieferant</span>
                                                                                                    <input type="checkbox" ${cols.distributor ? 'checked' : ''} onchange="toggleMaterialColumn('distributor')">
                                                                                                </label>

                                                                                                <label class="of-colpicker-item">
                                                                                                    <span>Typ</span>
                                                                                                    <input type="checkbox" ${cols.type ? 'checked' : ''} onchange="toggleMaterialColumn('type')">
                                                                                                </label>

                                                                                                ${isDealStatus
                            ? `
                                                                                                            <label class="of-colpicker-item">
                                                                                                                <span>Bezug</span>
                                                                                                                <input type="checkbox" checked disabled>
                                                                                                            </label>
                                                                                                        `
                            : ''
                        }

                                                                                                <label class="of-colpicker-item">
                                                                                                    <span>Menge</span>
                                                                                                    <input type="checkbox" ${cols.qty ? 'checked' : ''} onchange="toggleMaterialColumn('qty')">
                                                                                                </label>

                                                                                                <label class="of-colpicker-item">
                                                                                                    <span>Gesamtmenge</span>
                                                                                                    <input type="checkbox" ${cols.qty_total ? 'checked' : ''} onchange="toggleMaterialColumn('qty_total')">
                                                                                                </label>

                                                                                                <label class="of-colpicker-item">
                                                                                                    <span>Einheit</span>
                                                                                                    <input type="checkbox" ${cols.unit ? 'checked' : ''} onchange="toggleMaterialColumn('unit')">
                                                                                                </label>

                                                                                                <label class="of-colpicker-item">
                                                                                                    <span>EK / Einheit</span>
                                                                                                    <input type="checkbox" ${cols.ek_price ? 'checked' : ''} onchange="toggleMaterialColumn('ek_price')">
                                                                                                </label>

                                                                                                <label class="of-colpicker-item">
                                                                                                    <span>EK gesamt</span>
                                                                                                    <input type="checkbox" ${cols.ek_total ? 'checked' : ''} onchange="toggleMaterialColumn('ek_total')">
                                                                                                </label>

                                                                                                <label class="of-colpicker-item">
                                                                                                    <span>VK / Einheit</span>
                                                                                                    <input type="checkbox" ${cols.unit_price ? 'checked' : ''} onchange="toggleMaterialColumn('unit_price')">
                                                                                                </label>

                                                                                                <label class="of-colpicker-item">
                                                                                                    <span>VK gesamt</span>
                                                                                                    <input type="checkbox" ${cols.total ? 'checked' : ''} onchange="toggleMaterialColumn('total')">
                                                                                                </label>
                                                                                            </div>
                                                                                        </div>
                                                                                    </details>
                                                                                </div>
                                                                            </div>

                                                                            ${filteredRows.length
                            ? `
                                                                                        <div class="of-table-wrap">
                                                                                            <table class="of-table" id="material-table">
                                                                                                <thead>
                                                                                                    <tr>${th.join('')}</tr>
                                                                                                </thead>
                                                                                                <tbody>
                                                                                                    ${filteredRows.map(row => buildRow(row)).join('')}
                                                                                                </tbody>
                                                                                            </table>
                                                                                        </div>
                                                                                    `
                            : `
                                                                                        <div class="of-empty">
                                                                                            Keine Materialpositionen in der Ansicht „${esc(currentFilterLabel)}“ vorhanden.
                                                                                        </div>
                                                                                    `
                        }
                                                                        `;

                    wrap.innerHTML = materialTableHtml;

                    const printRowsBase = normalizedRows.filter(row => !isContainerMaterialRow(row));
                    const printRows = activeFilter === 'all'
                        ? printRowsBase
                        : printRowsBase.filter(row => getRowQtyForFilter(row, activeFilter) > 0);

                    const printRowsHtml = printRows.length
                        ? `
                                                                                <div class="of-table-wrap">
                                                                                    <table class="of-table">
                                                                                        <thead>
                                                                                            <tr>
                                                                                                <th>Pos.</th>
                                                                                                <th>Bild</th>
                                                                                                <th>Material</th>
                                                                                                <th>Hersteller-Nr.</th>
                                                                                                <th>Lieferant-Nr.</th>
                                                                                                <th>Menge</th>
                                                                                                <th>Gesamtmenge</th>
                                                                                                <th>Einheit</th>
                                                                                                ${showBezugColumn ? '<th>Bezug</th>' : ''}
                                                                                                <th class="of-table-check">✓</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody>
                                                                                            ${printRows.map(row => {
                            const imageUrl = getRowImage(row);
                            const hierarchyPadding = Math.max(0, (Number(row.hierarchy_level || 1) - 1) * 22);
                            const printQtyTotal = activeFilter === 'all'
                                ? getRowTotalQty(row)
                                : getRowQtyForFilter(row, activeFilter);

                            return `
                                                                                                    <tr>
                                                                                                        <td style="white-space:nowrap;font-weight:900;">${esc(row.position_no || '-')}</td>
                                                                                                        <td style="width:76px;">
                                                                                                            ${imageUrl
                                    ? `<img src="${esc(imageUrl)}" style="width:52px;height:52px;object-fit:cover;border-radius:12px;border:1px solid #e5e7eb;">`
                                    : `<div style="width:52px;height:52px;border-radius:12px;border:1px solid #e5e7eb;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:11px;font-weight:800;">Kein Bild</div>`
                                }
                                                                                                        </td>
                                                                                                        <td>
                                                                                                            <div class="of-mat-name" style="padding-left:${hierarchyPadding}px;">
                                                                                                                <div class="of-mat-title">${esc(row.name || '-')}</div>
                                                                                                                <div class="of-mat-meta">
                                                                                                                    <span class="of-mat-chip">${esc(row.level || '-')}</span>
                                                                                                                    ${row.parent_title && row.parent_title !== row.name ? `<span class="of-mat-chip">${esc(row.parent_title)}</span>` : ''}
                                                                                                                </div>
                                                                                                                ${row.description ? `<div class="of-mat-desc">${esc(row.description)}</div>` : ''}
                                                                                                            </div>
                                                                                                        </td>
                                                                                                        <td>${esc(row.article_no || '-')}</td>
                                                                                                        <td>${esc(row.distributor_article_no || '-')}</td>
                                                                                                        <td class="num">${esc(Number(row.qty || 0).toFixed(2))}</td>
                                                                                                        <td class="num">${esc(Number(printQtyTotal || 0).toFixed(2))}</td>
                                                                                                        <td>${esc(row.unit || '-')}</td>
                                                                                                        ${showBezugColumn
                                    ? `<td>${esc(
                                        activeFilter === 'all'
                                            ? (row.order_status || 'offen')
                                            : activeFilter
                                    )}</td>`
                                    : ''
                                }
                                                                                                        <td class="of-table-check">
                                                                                                            <input type="checkbox" class="of-check">
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                `;
                        }).join('')}
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            `
                        : `<div class="of-empty">Keine Materialpositionen vorhanden.</div>`;

                    printWrap.innerHTML = printRowsHtml;

                    setText('print-list-title', getPrintListTitle(activeFilter));
                    setText('print-product-name-top', getProductLabel());
                    setText('print-customer-name', getPrintCustomerName());
                    setText('print-customer-address', getPrintCustomerAddress());
                    setText('print-date', formatDateValue(new Date()));

                    if (filteredRows.length) {
                        initMaterialSelection();

                        if (showBezugColumn) {
                            initMaterialStatusListeners();
                        }
                    } else {
                        hideSmartMaterialSidebar();
                    }

                    renderTabCounts();
                }
                async function updateMaterialOrderStatus(rowsToUpdate, moveTo, moveQty, sourceStatus = null) {
                    const url = folderApp.dataset.materialStatusUrl;
                    if (!url) {
                        alert('Fehler: Route für Status-Update nicht gefunden.');
                        return;
                    }



                    const validRows = (Array.isArray(rowsToUpdate) ? rowsToUpdate : []).filter(row => isStatusEditableMaterialRow(row));

                    if (!validRows.length) {
                        alert('Keine gültigen Produktpositionen ausgewählt.');
                        return;
                    }

                    const qty = Number(moveQty || 0);
                    if (!(qty > 0)) {
                        alert('Bitte eine gültige Menge größer als 0 eingeben.');
                        return;
                    }

                    const allSelects = Array.from(document.querySelectorAll('.material-status-select'));
                    const bulkSelect = document.getElementById('bulk-status-select');
                    const bulkApply = document.getElementById('bulk-status-apply');

                    try {
                        allSelects.forEach(el => el.disabled = true);
                        if (bulkSelect) bulkSelect.disabled = true;
                        if (bulkApply) bulkApply.disabled = true;

                        const payload = {
                            items: validRows.map(row => ({
                                product_id: Number(row.product_id || 0) || null,
                                component_id: Number(row.component_id || 0) || null,
                                article_no: row.article_no || '',
                                move_to: moveTo,
                                move_qty: qty,
                                source_status: sourceStatus
                            }))
                        };

                        const json = await fetchJson(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify(payload)
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Materialverteilung konnte nicht aktualisiert werden.');
                        }

                        await loadFolderData();

                        showCustomToast(
                            'Materialverteilung aktualisiert',
                            `${validRows.length} Position(en) wurden nach "${getMoveStatusLabel(moveTo)}" verschoben.`
                        );
                    } catch (error) {
                        alert('Fehler beim Aktualisieren der Materialverteilung: ' + (error.message || 'Unbekannter Fehler'));
                        await loadFolderData();
                    } finally {
                        allSelects.forEach(el => el.disabled = false);
                        if (bulkSelect) bulkSelect.disabled = false;
                        if (bulkApply) bulkApply.disabled = false;
                    }
                }

                function renderLaborList() {
                    const wrap = document.getElementById('labor-list-wrap');
                    const badge = document.getElementById('labor-count-badge');

                    if (!wrap) return;

                    const { laborRows } = getStructureRows();

                    const laborAnalytics = laborRows.reduce((summary, row) => {
                        const qty = Number(row.qty || 0);
                        const ekPerHour = Number(row.ek || 0);
                        const vkPerHour = Number(row.rate || 0);

                        const ekTotal = Number(row.ek_total ?? (qty * ekPerHour));
                        const vkTotal = Number(row.total ?? (qty * vkPerHour));

                        summary.positions += 1;
                        summary.totalTime += qty;
                        summary.totalCost += ekTotal;
                        summary.totalSell += vkTotal;

                        return summary;
                    }, {
                        positions: 0,
                        totalTime: 0,
                        totalCost: 0,
                        totalSell: 0
                    });

                    laborAnalytics.profit = laborAnalytics.totalSell - laborAnalytics.totalCost;
                    laborAnalytics.avgVk = laborAnalytics.totalTime > 0
                        ? laborAnalytics.totalSell / laborAnalytics.totalTime
                        : 0;
                    laborAnalytics.avgEk = laborAnalytics.totalTime > 0
                        ? laborAnalytics.totalCost / laborAnalytics.totalTime
                        : 0;
                    laborAnalytics.marginPercent = laborAnalytics.totalSell > 0
                        ? (laborAnalytics.profit / laborAnalytics.totalSell) * 100
                        : 0;

                    if (badge) {
                        badge.textContent =
                            `${laborAnalytics.positions} Lohnzeilen · ` +
                            `${laborAnalytics.totalTime.toFixed(2)} Std. · ` +
                            `${money(laborAnalytics.totalSell)}`;
                    }

                    if (!laborRows.length) {
                        wrap.innerHTML = `
                                                                                <div class="of-labor-summary">
                                                                                    <div class="of-labor-summary-card">
                                                                                        <div class="of-labor-summary-label">Positionen</div>
                                                                                        <div class="of-labor-summary-value">0</div>
                                                                                    </div>

                                                                                    <div class="of-labor-summary-card">
                                                                                        <div class="of-labor-summary-label">Zeit gesamt</div>
                                                                                        <div class="of-labor-summary-value">0,00 Std.</div>
                                                                                    </div>

                                                                                    <div class="of-labor-summary-card">
                                                                                        <div class="of-labor-summary-label">VK Gesamt</div>
                                                                                        <div class="of-labor-summary-value">${money(0)}</div>
                                                                                    </div>

                                                                                    <div class="of-labor-summary-card">
                                                                                        <div class="of-labor-summary-label">Kosten / EK</div>
                                                                                        <div class="of-labor-summary-value">${money(0)}</div>
                                                                                    </div>
                                                                                </div>

                                                                                <div class="of-empty">Keine Lohnpositionen vorhanden.</div>
                                                                            `;

                        renderTabCounts();
                        return;
                    }

                    wrap.innerHTML = `
                                                                            <div class="of-labor-summary">
                                                                                <div class="of-labor-summary-card">
                                                                                    <div class="of-labor-summary-label">Positionen</div>
                                                                                    <div class="of-labor-summary-value">
                                                                                        ${esc(laborAnalytics.positions)}
                                                                                    </div>
                                                                                </div>

                                                                                <div class="of-labor-summary-card">
                                                                                    <div class="of-labor-summary-label">Zeit gesamt</div>
                                                                                    <div class="of-labor-summary-value">
                                                                                        ${esc(laborAnalytics.totalTime.toFixed(2))} Std.
                                                                                    </div>
                                                                                </div>

                                                                                <div class="of-labor-summary-card">
                                                                                    <div class="of-labor-summary-label">VK Gesamt</div>
                                                                                    <div class="of-labor-summary-value">
                                                                                        ${esc(money(laborAnalytics.totalSell))}
                                                                                    </div>
                                                                                </div>

                                                                                <div class="of-labor-summary-card">
                                                                                    <div class="of-labor-summary-label">Kosten / EK</div>
                                                                                    <div class="of-labor-summary-value">
                                                                                        ${esc(money(laborAnalytics.totalCost))}
                                                                                    </div>
                                                                                </div>

                                                                                <div class="of-labor-summary-card">
                                                                                    <div class="of-labor-summary-label">Gewinn</div>
                                                                                    <div class="of-labor-summary-value" style="color:${laborAnalytics.profit >= 0 ? '#10b981' : '#ef4444'};">
                                                                                        ${esc(money(laborAnalytics.profit))}
                                                                                    </div>
                                                                                </div>

                                                                                <div class="of-labor-summary-card">
                                                                                    <div class="of-labor-summary-label">Marge</div>
                                                                                    <div class="of-labor-summary-value">
                                                                                        ${esc(laborAnalytics.marginPercent.toFixed(2))} %
                                                                                    </div>
                                                                                </div>

                                                                                <div class="of-labor-summary-card">
                                                                                    <div class="of-labor-summary-label">Ø VK / h</div>
                                                                                    <div class="of-labor-summary-value">
                                                                                        ${esc(money(laborAnalytics.avgVk))}
                                                                                    </div>
                                                                                </div>

                                                                                <div class="of-labor-summary-card">
                                                                                    <div class="of-labor-summary-label">Ø EK / h</div>
                                                                                    <div class="of-labor-summary-value">
                                                                                        ${esc(money(laborAnalytics.avgEk))}
                                                                                    </div>
                                                                                </div>
                                                                            </div>

                                                                            <div class="of-table-wrap">
                                                                                <table class="of-table">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Pos.</th>
                                                                                            <th>Sektion</th>
                                                                                            <th>Hauptposition</th>
                                                                                            <th>Leistung</th>
                                                                                            <th>Qualifikation</th>
                                                                                            <th class="num">Menge</th>
                                                                                            <th>Einheit</th>
                                                                                            <th class="num">EK / h</th>
                                                                                            <th class="num">EK Gesamt</th>
                                                                                            <th class="num">VK / h</th>
                                                                                            <th class="num">VK Gesamt</th>
                                                                                            <th class="num">Gewinn</th>
                                                                                            <th class="num">Marge</th>
                                                                                            <th>Optionen</th>
                                                                                        </tr>
                                                                                    </thead>

                                                                                    <tbody>
                                                                                        ${laborRows.map((row, index) => {
                        const qty = Number(row.qty || 0);
                        const ekPerHour = Number(row.ek || 0);
                        const vkPerHour = Number(row.rate || 0);

                        const ekTotal = Number(row.ek_total ?? (qty * ekPerHour));
                        const vkTotal = Number(row.total ?? (qty * vkPerHour));
                        const profit = vkTotal - ekTotal;
                        const marginPercent = vkTotal > 0 ? (profit / vkTotal) * 100 : 0;

                        return `
                                                                                                <tr>
                                                                                                    <td style="white-space:nowrap;font-weight:900;">
                                                                                                        ${esc(row.position_no || '-')}
                                                                                                    </td>

                                                                                                    <td>${esc(row.section_title || '-')}</td>
                                                                                                    <td>${esc(row.parent_title || '-')}</td>
                                                                                                    <td>${esc(row.labor_title || '-')}</td>

                                                                                                    <td>
                                                                                                        <div style="font-weight:900;color:#111827;">
                                                                                                            ${esc(row.qualification_name || '-')}
                                                                                                        </div>

                                                                                                        ${row.qualification_id
                                ? `<div class="of-sub" style="margin:3px 0 0;">ID: ${esc(row.qualification_id)}</div>`
                                : `<div class="of-sub" style="margin:3px 0 0;color:#b91c1c;">Keine Qualifikation-ID</div>`
                            }
                                                                                                    </td>

                                                                                                    <td class="num">${esc(qty.toFixed(2))}</td>
                                                                                                    <td>${esc(row.unit || 'Std.')}</td>

                                                                                                    <td class="num">${esc(money(ekPerHour))}</td>
                                                                                                    <td class="num">${esc(money(ekTotal))}</td>

                                                                                                    <td class="num">${esc(money(vkPerHour))}</td>
                                                                                                    <td class="num">${esc(money(vkTotal))}</td>

                                                                                                    <td class="num" style="font-weight:900;color:${profit >= 0 ? '#10b981' : '#ef4444'};">
                                                                                                        ${esc(money(profit))}
                                                                                                    </td>

                                                                                                    <td class="num">
                                                                                                        ${esc(marginPercent.toFixed(2))} %
                                                                                                    </td>

                                                                                                    <td>
                                                                                                        ${row.qualification_id
                                ? `
                                                                                                                    <button
                                                                                                                        type="button"
                                                                                                                        class="of-labor-action-btn"
                                                                                                                        data-labor-option-index="${index}"
                                                                                                                    >
                                                                                                                        Wer kann das machen?
                                                                                                                    </button>
                                                                                                                `
                                : `
                                                                                                                    <span class="of-badge">
                                                                                                                        Keine Optionen
                                                                                                                    </span>
                                                                                                                `
                            }
                                                                                                    </td>
                                                                                                </tr>
                                                                                            `;
                    }).join('')}
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        `;

                    document.querySelectorAll('[data-labor-option-index]').forEach(btn => {
                        btn.addEventListener('click', () => {
                            const index = Number(btn.dataset.laborOptionIndex);
                            openLaborQualificationModal(index);
                        });
                    });

                    renderTabCounts();
                }

                function initAttachmentSearch() {
                    const input = document.getElementById('attachment-search-input');
                    if (!input || input.dataset.ready === '1') return;

                    input.addEventListener('input', () => {
                        renderPrintFiles();
                    });

                    input.dataset.ready = '1';
                }

                function initUploadBoxToggle() {
                    const btn = document.getElementById('toggle-upload-box-btn');
                    const box = document.getElementById('upload-box-wrap');

                    if (!btn || !box || btn.dataset.ready === '1') return;

                    const setState = (open) => {
                        box.classList.toggle('show', open);
                        btn.classList.toggle('active', open);
                        btn.textContent = open ? 'Upload schließen' : 'Datei hochladen';
                    };

                    btn.addEventListener('click', () => {
                        const isOpen = box.classList.contains('show');
                        setState(!isOpen);

                        if (!isOpen) {
                            setTimeout(() => {
                                box.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                            }, 50);
                        }
                    });

                    btn.dataset.ready = '1';
                }

                function initAttachmentDropzone() {
                    const dz = document.getElementById('attachment-dropzone');
                    const input = document.getElementById('print-files-input');
                    const pickBtn = document.getElementById('pick-files-btn');
                    const docTypeSelect = document.getElementById('upload-doc-type');
                    const customTypeInput = document.getElementById('upload-custom-type');

                    if (!dz || !input) return;

                    // Toggle custom type input
                    if (docTypeSelect) {
                        docTypeSelect.addEventListener('change', (e) => {
                            customTypeInput.style.display = e.target.value === 'custom' ? 'block' : 'none';
                        });
                    }

                    if (pickBtn && !pickBtn.dataset.ready) {
                        pickBtn.addEventListener('click', () => input.click());
                        pickBtn.dataset.ready = '1';
                    }

                    // AUTO UPLOAD when picking via button
                    input.addEventListener('change', (e) => {
                        if (e.target.files && e.target.files.length > 0) {
                            triggerAutoUpload(e.target.files);
                        }
                    });

                    ['dragenter', 'dragover'].forEach(evt => {
                        dz.addEventListener(evt, e => {
                            e.preventDefault(); e.stopPropagation();
                            dz.classList.add('of-dropzone-over');
                        });
                    });

                    ['dragleave', 'drop'].forEach(evt => {
                        dz.addEventListener(evt, e => {
                            e.preventDefault(); e.stopPropagation();
                            dz.classList.remove('of-dropzone-over');
                        });
                    });

                    // AUTO UPLOAD when dropping
                    dz.addEventListener('drop', e => {
                        const files = e.dataTransfer?.files;
                        if (files && files.length > 0) {
                            triggerAutoUpload(files);
                        }
                    });
                }


                async function triggerAutoUpload(files) {
                    const url = folderApp.dataset.attachmentsUploadUrl;
                    const offerId = folderApp.dataset.offerId || '';

                    const docTypeSelect = document.getElementById('upload-doc-type');
                    const customTypeInput = document.getElementById('upload-custom-type');
                    const noticeInput = document.getElementById('upload-notice');

                    let documentType = docTypeSelect ? docTypeSelect.value : '';
                    if (documentType === 'custom') {
                        documentType = customTypeInput ? customTypeInput.value : 'Eigener Status';
                    }
                    const notice = noticeInput ? noticeInput.value : '';

                    try {
                        const formData = new FormData();
                        if (offerId) formData.append('offer_id', offerId);
                        formData.append('document_type', documentType);
                        formData.append('notice', notice);

                        Array.from(files).forEach(file => {
                            formData.append('files[]', file);
                        });

                        // Small loading UX
                        const pickBtn = document.getElementById('pick-files-btn');
                        if (pickBtn) pickBtn.textContent = "Lädt hoch...";

                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: formData
                        });

                        const json = await response.json();
                        if (pickBtn) pickBtn.textContent = "Dateien auswählen";

                        if (!response.ok || !json.success) {
                            throw new Error(json.message || 'Dateien konnten nicht hochgeladen werden.');
                        }

                        state.attachments = safeArray(json.attachments);

                        // Reset form optionally
                        if (noticeInput) noticeInput.value = '';
                        if (customTypeInput) customTypeInput.value = '';

                        renderPrintFiles();
                        showCustomToast('Upload erfolgreich', 'Die Dateien wurden hochgeladen.');
                    } catch (error) {
                        alert(error.message || 'Dateien konnten nicht hochgeladen werden.');
                    }
                }
                async function deleteAttachment(attachmentId) {
                    const folderId = folderApp.dataset.folderId;
                    const urlTemplate = `/admin/offers/folders/${folderId}/attachments/${attachmentId}`;

                    const confirmed = window.confirm('Soll diese Datei wirklich gelöscht werden?');
                    if (!confirmed) return;

                    try {
                        const json = await fetchJson(urlTemplate, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken()
                            }
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Datei konnte nicht gelöscht werden.');
                        }

                        state.attachments = safeArray(json.attachments);
                        renderPrintFiles();

                        showCustomToast('Datei gelöscht', 'Die Datei wurde entfernt.');
                    } catch (error) {
                        alert(error.message || 'Datei konnte nicht gelöscht werden.');
                    }
                }

                window.deleteAttachment = deleteAttachment;

                async function saveAttachmentOrder(ids) {
                    const url = folderApp.dataset.attachmentsSortUrl;

                    const json = await fetchJson(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: JSON.stringify({ ids })
                    });

                    if (!json.success) {
                        throw new Error(json.message || 'Sortierung konnte nicht gespeichert werden.');
                    }

                    state.attachments = safeArray(json.attachments);
                    renderPrintFiles();
                }

                function initAttachmentSorting() {
                    const list = document.getElementById('attachment-sortable-list');
                    if (!list || list.dataset.sortableReady === '1') return;
                    if (typeof Sortable === 'undefined') return;

                    new Sortable(list, {
                        animation: 180,
                        ghostClass: 'of-drag-ghost',
                        chosenClass: 'of-drag-chosen',
                        dragClass: 'of-drag-chosen',
                        onEnd: async function () {
                            try {
                                const ids = Array.from(list.querySelectorAll('[data-attachment-id]'))
                                    .map(el => Number(el.dataset.attachmentId))
                                    .filter(Boolean);

                                await saveAttachmentOrder(ids);
                            } catch (error) {
                                alert(error.message || 'Sortierung konnte nicht gespeichert werden.');
                                renderPrintFiles();
                            }
                        }
                    });

                    list.dataset.sortableReady = '1';
                }

                function getAttachmentNotice(file) {
                    return String(
                        file.notice ||
                        file.note ||
                        file.reason ||
                        file.upload_notice ||
                        file.description ||
                        ''
                    ).trim();
                }

                function getAttachmentDocumentType(file) {
                    return String(
                        file.document_type ||
                        file.doc_type ||
                        file.type_label ||
                        ''
                    ).trim();
                }

                function renderPrintFiles() {
                    const wrap = document.getElementById('print-files-list-wrap');
                    const badge = document.getElementById('print-files-count-badge');
                    const q = (document.getElementById('attachment-search-input')?.value || '').trim().toLowerCase();

                    if (!wrap) return;

                    const files = safeArray(state.attachments)
                        .slice()
                        .sort((a, b) => Number(a.sort_order || 0) - Number(b.sort_order || 0));

                    const filtered = files.filter(file => {
                        const text = [
                            file.title || '',
                            file.original_name || '',
                            file.mime_type || '',
                            file.file_type || '',
                            getAttachmentDocumentType(file),
                            getAttachmentNotice(file)
                        ].join(' ').toLowerCase();
                        return !q || text.includes(q);
                    });

                    if (badge) {
                        badge.textContent = `${files.length} Dateien`;
                    }

                    renderAttachmentAnalytics(files);

                    if (!files.length) {
                        wrap.innerHTML = `<div class="of-empty">Keine Druckdateien vorhanden.</div>`;
                        renderTabCounts();
                        return;
                    }

                    // Create a global array for the lightbox to iterate over
                    window.lightboxFiles = filtered;

                    wrap.innerHTML = `
                                                                <div class="of-file-list sortable-enabled" id="attachment-sortable-list">
                                                                    ${filtered.map((file, index) => {

                        // Determine thumbnail content
                        let thumbnailHtml = '';
                        if (file.file_type === 'image') {
                            thumbnailHtml = `<img src="${esc(file.file_url)}" style="width:100%; height:100%; object-fit:cover; border-radius: 8px;">`;
                        } else if (file.file_type === 'pdf') {
                            thumbnailHtml = `<div style="background:#eff6ff; color:#2563eb; width:100%; height:100%; display:flex; align-items:center; justify-content:center; border-radius:8px; font-weight:900; font-size: 10px;">PDF</div>`;
                        } else {
                            thumbnailHtml = `<div style="background:#f3f4f6; color:#6b7280; width:100%; height:100%; display:flex; align-items:center; justify-content:center; border-radius:8px; font-weight:900; font-size: 10px;">FILE</div>`;
                        }

                        const noticeText = getAttachmentNotice(file);
                        const documentTypeText = getAttachmentDocumentType(file);

                        return `
                                                                                    <div class="of-file-row" data-attachment-id="${file.id}">
                                                                                        <div style="width: 64px; height: 64px; flex-shrink: 0; cursor:pointer;" onclick="openLightbox(${index})">
                                                                                            ${thumbnailHtml}
                                                                                        </div>

                                                                                        <div class="of-file-left" style="margin-left: 14px;">
                                                                                            <div class="of-file-title" style="cursor:pointer; color: var(--of-blue);" onclick="openLightbox(${index})">
                                                                                                ${index + 1}. ${esc(file.title || file.original_name || 'Datei')}
                                                                                            </div>
                                                                                            <div class="of-file-meta">
                                                                                                <span class="of-badge of-file-type-badge ${esc(file.file_type || 'other')}">
                                                                                                    ${esc((file.file_type || 'other').toUpperCase())}
                                                                                                </span>

                                                                                                ${documentTypeText ? `
                                                                                                    <span class="of-badge" style="background:var(--of-primary-soft); color:var(--of-primary); border-color:#d9ef9d;">
                                                                                                        ${esc(documentTypeText)}
                                                                                                    </span>
                                                                                                ` : ''}

                                                                                                <span class="of-badge">${esc(formatBytes(file.file_size || 0))}</span>
                                                                                            </div>

                                                                                            ${noticeText ? `
                                                                                                <div style="
                                                                                                    margin-top:10px;
                                                                                                    padding:9px 11px;
                                                                                                    border:1px dashed #d1d5db;
                                                                                                    border-radius:12px;
                                                                                                    background:#fffbeb;
                                                                                                    color:#92400e;
                                                                                                    font-size:12px;
                                                                                                    line-height:1.55;
                                                                                                    font-weight:700;
                                                                                                ">
                                                                                                    <strong>Notiz:</strong> ${esc(noticeText)}
                                                                                                </div>
                                                                                            ` : ''}
                                                                                        </div>

                                                                                        <div class="of-file-actions">
                                                                                            <button type="button" class="of-btn soft" onclick="openLightbox(${index})">Vorschau</button>
                                                                                            <button type="button" class="of-btn danger" onclick="deleteAttachment(${Number(file.id)})">Löschen</button>
                                                                                        </div>
                                                                                    </div>
                                                                                    `
                    }).join('')}
                                                                            </div>
                                                                        `;

                    initAttachmentSorting();
                    renderTabCounts();
                }

                async function deleteOffer() {
                    const destroyUrl = folderApp.dataset.offerDestroyUrl;

                    if (!destroyUrl) {
                        alert('Keine Lösch-URL für das Angebot gefunden.');
                        return;
                    }

                    const bestaetigt = window.confirm('Möchten Sie dieses Angebot wirklich löschen?');
                    if (!bestaetigt) return;

                    try {
                        const json = await fetchJson(destroyUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': getCsrfToken()
                            }
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Angebot konnte nicht gelöscht werden.');
                        }

                        window.location.href = @json(route('admin.offers.index'));
                    } catch (error) {
                        alert(error.message || 'Angebot konnte nicht gelöscht werden.');
                    }
                }

                window.deleteOffer = deleteOffer;

                async function loadFolderData() {
                    try {
                        const url = folderApp.dataset.dataUrl;
                        const json = await fetchJson(url);

                        if (!json.success) {
                            throw new Error(json.message || 'Daten konnten nicht geladen werden.');
                        }

                        state.folder = json.folder || state.folder;
                        state.folder.document_status = json.document_status || state.folder.document_status;
                        state.folder.offer_status = json.offer_status || state.folder.offer_status;
                        state.folder.deal_status = json.deal_status || state.folder.deal_status;
                        state.offer = json.offer || state.offer;
                        state.detail = json.detail || state.detail;
                        state.sections = safeArray(state.detail?.sections);
                        state.distributors = json.distributors || {};
                        state.attachments = safeArray(json.attachments || []);
                        state.teamAccess = normalizeTeamAccessPayload(json.team_access || json.access || json || state.teamAccess || {});
                        state.teamAccessLoaded = !!(json.team_access || json.access);
                        await loadOfferTeamAccess(false);
                        if (json.kanban_stage_context?.main_stage?.id) {
                            setManualWorkflowStageId(getDocumentStatus(), json.kanban_stage_context.main_stage.id);
                        }
                        setKanbanStagesFromPayload(json.kanban_stages || json.workflow_stages || json.sub_stages || [], getDocumentStatus());

                        if (json.agb) {
                            window.folderAgb = json.agb;
                        }

                        renderDocumentStatusToggle();
                        renderStats();
                        renderKanban();
                        renderMaterialList();
                        renderLaborList();
                        renderHistory();
                        renderPrintFiles();
                        renderDocumentStatusToggle();
                        renderTabCounts();
                        syncAgbInputs();
                        renderOfferLockState();
                        renderTeamAccessPanel();
                    } catch (error) {
                        console.error(error);
                        const kanbanRoot = document.getElementById('kanban-columns');
                        if (kanbanRoot) {
                            kanbanRoot.innerHTML = `<div class="of-empty">Daten konnten nicht geladen werden: ${esc(error.message || error)}</div>`;
                        }
                    }
                }
                function initPresenceChannel() {
                    if (typeof window.Echo === 'undefined') {
                        console.warn('Echo ist nicht verfügbar. Presence-Liste wird nicht geladen.');
                        return;
                    }

                    const channelName = folderApp.dataset.presenceChannel;
                    if (!channelName) return;

                    try {
                        window.Echo.join(channelName)
                            .here((users) => {
                                state.presenceUsers = safeArray(users);
                                renderPresenceUsers();
                            })
                            .joining((user) => {
                                const normalized = normalizePresenceUser(user);
                                const key = presenceIdentityKey(normalized);
                                const exists = state.presenceUsers.some(u => presenceIdentityKey(normalizePresenceUser(u)) === key);
                                if (!exists) {
                                    state.presenceUsers.push(normalized);
                                    renderPresenceUsers();
                                }
                            })
                            .leaving((user) => {
                                const key = presenceIdentityKey(normalizePresenceUser(user));
                                state.presenceUsers = state.presenceUsers.filter(u => presenceIdentityKey(normalizePresenceUser(u)) !== key);
                                renderPresenceUsers();
                            })
                            .error((error) => {
                                console.error('Presence-Fehler:', error);
                            });
                    } catch (error) {
                        console.error('Presence-Channel konnte nicht initialisiert werden:', error);
                    }
                }


                function initFolderRealtimeConsistencyChannel() {
                    if (typeof window.Echo === 'undefined' || window.__folderRealtimeConsistencyBooted) {
                        return;
                    }

                    window.__folderRealtimeConsistencyBooted = true;

                    const currentFolderId = Number(state?.folder?.id || folderApp?.dataset?.folderId || @json($folder->id));
                    const currentOfferId = Number(state?.offer?.id || @json($offer?->id ?? $folder->offer_id));

                    const shouldReload = (event) => {
                        const folderId = Number(event?.folder_id || event?.folder?.id || 0);
                        const offerId = Number(event?.offer_id || event?.offer?.id || 0);
                        const type = String(event?.type || event?.action || '').toLowerCase();

                        return folderId === currentFolderId
                            || offerId === currentOfferId
                            || type.includes('kanban_offer_consistency')
                            || type.includes('accepted_from_kanban')
                            || type.includes('auto_cancelled_by_kanban')
                            || type.includes('sub_stage_synced_from_kanban');
                    };

                    const handle = async (event) => {
                        if (!shouldReload(event)) return;
                        try {
                            await loadFolderData();
                        } catch (error) {
                            console.warn('Ordner-Realtime konnte nicht aktualisieren:', error);
                        }
                    };

                    try {
                        window.Echo.channel('offers')
                            .listen('OffersChanged', handle)
                            .listen('.OffersChanged', handle)
                            .listen('OfferFolderUpdated', handle)
                            .listen('.OfferFolderUpdated', handle);
                    } catch (error) {
                        console.warn('Offer-Realtime-Channel nicht verfügbar:', error);
                    }
                }

                function closeLaborQualificationModal() {
                    const modal = document.getElementById('labor-qualification-modal');
                    const body = document.getElementById('labor-qualification-modal-body');

                    if (modal) modal.style.display = 'none';
                    if (body) body.innerHTML = `<div class="of-empty">Lade Möglichkeiten...</div>`;
                }

                window.closeLaborQualificationModal = closeLaborQualificationModal;

                async function openLaborQualificationModal(laborRowIndex) {
                    const { laborRows } = getStructureRows();
                    const row = laborRows[laborRowIndex];

                    if (!row) {
                        showCustomToast('Fehler', 'Lohnzeile wurde nicht gefunden.', 'error');
                        return;
                    }

                    if (!row.qualification_id) {
                        showCustomToast('Fehler', 'Diese Lohnzeile hat keine Qualifikation-ID.', 'error');
                        return;
                    }

                    const url = folderApp.dataset.laborQualificationOptionsUrl;

                    if (!url) {
                        showCustomToast('Fehler', 'Route für Qualifikationsmöglichkeiten fehlt.', 'error');
                        return;
                    }

                    const modal = document.getElementById('labor-qualification-modal');
                    const title = document.getElementById('labor-qualification-modal-title');
                    const sub = document.getElementById('labor-qualification-modal-sub');
                    const body = document.getElementById('labor-qualification-modal-body');

                    if (!modal || !body) return;

                    if (title) {
                        title.textContent = `Wer kann „${row.qualification_name || 'diese Arbeit'}“ ausführen?`;
                    }

                    if (sub) {
                        sub.textContent = `${row.labor_title || 'Arbeitsleistung'} · ${row.parent_title || '-'} · ${Number(row.qty || 0).toFixed(2)} ${row.unit || 'Std.'}`;
                    }

                    body.innerHTML = `<div class="of-empty">Qualifikationsmöglichkeiten werden geladen...</div>`;
                    modal.style.display = 'flex';

                    try {
                        const json = await fetchJson(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': getCsrfToken()
                            },
                            body: JSON.stringify({
                                required_qualification_id: Number(row.qualification_id),
                                qty: Number(row.qty || 1),
                                unit: row.unit || 'Std.',
                                current_rate: Number(row.rate || 0),
                                current_ek: Number(row.ek || 0),
                                margin_percent: Number(row.margin_percent || 0),
                                labor_title: row.labor_title || '',
                                parent_title: row.parent_title || '',
                                section_title: row.section_title || ''
                            })
                        });

                        if (!json.success) {
                            throw new Error(json.message || 'Möglichkeiten konnten nicht geladen werden.');
                        }

                        renderLaborQualificationOptions(json);
                    } catch (error) {
                        body.innerHTML = `
                                                                            <div class="of-empty">
                                                                                ${esc(error.message || 'Qualifikationsmöglichkeiten konnten nicht geladen werden.')}
                                                                            </div>
                                                                        `;
                    }
                }

                window.openLaborQualificationModal = openLaborQualificationModal;

                function renderLaborQualificationOptions(data) {
                    const body = document.getElementById('labor-qualification-modal-body');
                    if (!body) return;

                    const options = Array.isArray(data.options) ? data.options : [];
                    const labor = data.labor || {};
                    const required = data.required_qualification || {};

                    if (!options.length) {
                        body.innerHTML = `
                                                                            <div class="of-empty">
                                                                                Keine erlaubten Qualifikationen gefunden.
                                                                            </div>
                                                                        `;
                        return;
                    }

                    const originalOption = options.find(option => option.is_original) || options[0];
                    const cheapestOption = [...options].sort((a, b) => Number(a.sell_total || 0) - Number(b.sell_total || 0))[0];

                    body.innerHTML = `
                                                                        <div class="of-labor-summary">
                                                                            <div class="of-labor-summary-card">
                                                                                <div class="of-labor-summary-label">Benötigte Qualifikation</div>
                                                                                <div class="of-labor-summary-value">${esc(required.name || '-')}</div>
                                                                            </div>

                                                                            <div class="of-labor-summary-card">
                                                                                <div class="of-labor-summary-label">Arbeitszeit</div>
                                                                                <div class="of-labor-summary-value">${esc(Number(labor.qty || 0).toFixed(2))} ${esc(labor.unit || 'Std.')}</div>
                                                                            </div>

                                                                            <div class="of-labor-summary-card">
                                                                                <div class="of-labor-summary-label">Aktueller Satz</div>
                                                                                <div class="of-labor-summary-value">${esc(money(labor.current_rate || 0))}</div>
                                                                            </div>

                                                                            <div class="of-labor-summary-card">
                                                                                <div class="of-labor-summary-label">Günstigste Option</div>
                                                                                <div class="of-labor-summary-value">
                                                                                    ${esc(cheapestOption?.qualification_name || '-')}
                                                                                    <br>
                                                                                    <span style="font-size:13px;color:#6b7280;">
                                                                                        ${esc(money(cheapestOption?.sell_total || 0))}
                                                                                    </span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="of-labor-options">
                                                                            ${options.map(option => {
                        const isOriginal = !!option.is_original;
                        const isCheapest = cheapestOption && Number(cheapestOption.qualification_id) === Number(option.qualification_id);

                        const timeDiff = Number(option.difference_to_original_hours || 0);

                        return `
                                                                                    <div class="of-labor-option-card ${isOriginal ? 'is-original' : ''}">
                                                                                        <div class="of-labor-option-head">
                                                                                            <div>
                                                                                                <div class="of-labor-option-title">
                                                                                                    ${esc(option.qualification_name || '-')}
                                                                                                </div>

                                                                                                <div class="of-labor-option-sub">
                                                                                                    ${isOriginal
                                ? 'Originale Qualifikation aus der Lohnzeile.'
                                : `Kann ${esc(option.required_qualification_name || required.name || '-')} Arbeit ausführen.`
                            }
                                                                                                </div>
                                                                                            </div>

                                                                                            <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                                                                                ${isOriginal ? `<span class="of-labor-badge original">Aktuell</span>` : ''}
                                                                                                ${isCheapest ? `<span class="of-labor-badge original">Günstigste Option</span>` : ''}
                                                                                                <span class="of-labor-badge">Erlaubt</span>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="of-labor-option-body">
                                                                                            <div class="of-labor-metrics">
                                                                                                <div class="of-labor-metric">
                                                                                                    <div class="of-labor-metric-label">Zeitfaktor</div>
                                                                                                    <div class="of-labor-metric-value">
                                                                                                        ${esc(Number(option.efficiency_factor || 1).toFixed(2))}
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="of-labor-metric">
                                                                                                    <div class="of-labor-metric-label">Effektive Zeit</div>
                                                                                                    <div class="of-labor-metric-value">
                                                                                                        ${esc(Number(option.effective_hours || 0).toFixed(2))} ${esc(labor.unit || 'Std.')}
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="of-labor-metric">
                                                                                                    <div class="of-labor-metric-label">Zeitunterschied</div>
                                                                                                    <div class="of-labor-metric-value">
                                                                                                        ${timeDiff > 0 ? '+' : ''}${esc(timeDiff.toFixed(2))} ${esc(labor.unit || 'Std.')}
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="of-labor-metric">
                                                                                                    <div class="of-labor-metric-label">EK / h</div>
                                                                                                    <div class="of-labor-metric-value">
                                                                                                        ${esc(money(option.ek_per_hour || 0))}
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="of-labor-metric">
                                                                                                    <div class="of-labor-metric-label">VK / h</div>
                                                                                                    <div class="of-labor-metric-value">
                                                                                                        ${esc(money(option.sell_per_hour || 0))}
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="of-labor-metric">
                                                                                                    <div class="of-labor-metric-label">EK Gesamt</div>
                                                                                                    <div class="of-labor-metric-value">
                                                                                                        ${esc(money(option.ek_total || 0))}
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="of-labor-metric">
                                                                                                    <div class="of-labor-metric-label">VK Gesamt</div>
                                                                                                    <div class="of-labor-metric-value">
                                                                                                        ${esc(money(option.sell_total || 0))}
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="of-labor-metric">
                                                                                                    <div class="of-labor-metric-label">Kostenfaktor</div>
                                                                                                    <div class="of-labor-metric-value">
                                                                                                        ${esc(Number(option.cost_factor || 1).toFixed(2))}
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="of-labor-metric">
                                                                                                    <div class="of-labor-metric-label">Marge</div>
                                                                                                    <div class="of-labor-metric-value">
                                                                                                        ${labor.margin_percent !== null && labor.margin_percent !== undefined
                                ? `${esc(Number(labor.margin_percent || 0).toFixed(2))} %`
                                : '-'
                            }
                                                                                                    </div>
                                                                                                </div>

                                                                                                <div class="of-labor-metric">
                                                                                                    <div class="of-labor-metric-label">Bewertung</div>
                                                                                                    <div class="of-labor-metric-value">
                                                                                                        ${isOriginal
                                ? 'Aktuelle Wahl'
                                : isCheapest
                                    ? 'Günstig'
                                    : 'Alternative'
                            }
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                `;
                    }).join('')}
                                                                        </div>
                                                                    `;
                }

                document.addEventListener('DOMContentLoaded', async () => {
                    state.sections = safeArray(state.detail?.sections);

                    renderStats();
                    renderKanban();
                    renderMaterialList();
                    renderLaborList();
                    renderHistory();
                    renderPresenceUsers();
                    renderTeamAccessPanel();
                    renderTabCounts();
                    window.switchWorkspaceTab((() => { try { return localStorage.getItem('offerWorkspaceActiveTab') || 'uebersicht'; } catch (e) { return 'uebersicht'; } })());
                    initAttachmentSearch();
                    initAttachmentDropzone();
                    initUploadBoxToggle();
                    initAgbEditor();
                    syncAgbInputs();


                    // ----------------------------------------------------
                    // ADD THIS JAVASCRIPT AT THE END OF THE DOMContentLoaded BLOCK
                    // ----------------------------------------------------
                    const btnLoadOffer = document.getElementById('btn-load-offer');
                    const setWorkspaceSidebarCollapsed = window.setWorkspaceSidebarCollapsed;
                    window.toggleWorkspaceSidebar = window.toggleWorkspaceSidebar || function () {
                        const layout = document.getElementById('of-workspace-layout');
                        setWorkspaceSidebarCollapsed(!layout?.classList.contains('sidebar-collapsed'));
                    };

                    function bindWorkspaceSidebar() {
                        const layout = document.getElementById('of-workspace-layout');
                        const tabs = document.getElementById('workspace-tabs');
                        const saved = (() => {
                            try { return localStorage.getItem('offerWorkspaceSidebarCollapsed') === '1'; } catch (error) { return false; }
                        })();
                        setWorkspaceSidebarCollapsed(saved);

                        if (tabs && tabs.dataset.bound !== '1') {
                            tabs.addEventListener('click', function (event) {
                                const btn = event.target.closest('.of-tab[data-tab]');
                                if (!btn || !tabs.contains(btn)) return;
                                event.preventDefault();
                                event.stopPropagation();
                                window.switchWorkspaceTab(btn.dataset.tab);
                            });
                            tabs.dataset.bound = '1';
                        }
                    }

                    bindWorkspaceSidebar();
                    loadOfferTeamAccess(false).then(() => renderTeamAccessPanel());

                    const clonePromptModal = document.getElementById('clone-prompt-modal');
                    const versionPromptModal = document.getElementById('version-prompt-modal');
                    const btnConfirmClone = document.getElementById('btn-confirm-clone');
                    const btnLoadSnapshot = document.getElementById('btn-load-snapshot');
                    const btnLoadCurrent = document.getElementById('btn-load-current');

                    const OFFER_STATUSES_REQUIRE_CLONE_PROMPT = [
                        'sent', 'viewed', 'negotiation', 'revised', 'accepted'
                    ];



                    const teamAccessModal = document.getElementById('team-access-modal');
                    if (teamAccessModal) {
                        teamAccessModal.addEventListener('click', function (e) {
                            if (e.target === teamAccessModal) closeTeamAccessModal();
                        });
                    }

                    const laborQualificationModal = document.getElementById('labor-qualification-modal');

                    if (laborQualificationModal) {
                        laborQualificationModal.addEventListener('click', function (e) {
                            if (e.target === laborQualificationModal) {
                                closeLaborQualificationModal();
                            }
                        });
                    }

                    window.lightboxFiles = [];
                    let currentLightboxIndex = 0;

                    window.openLightbox = function (index) {
                        if (!window.lightboxFiles || window.lightboxFiles.length === 0) return;

                        currentLightboxIndex = index;
                        const modal = document.getElementById('lightbox-modal');

                        updateLightboxContent();
                        modal.style.display = 'flex';
                    }

                    window.closeLightbox = function () {
                        const modal = document.getElementById('lightbox-modal');
                        modal.style.display = 'none';
                        document.getElementById('lightbox-content').innerHTML = ''; // clear iframe memory
                    }

                    window.lightboxNext = function () {
                        if (currentLightboxIndex < window.lightboxFiles.length - 1) {
                            currentLightboxIndex++;
                            updateLightboxContent();
                        }
                    }

                    window.lightboxPrev = function () {
                        if (currentLightboxIndex > 0) {
                            currentLightboxIndex--;
                            updateLightboxContent();
                        }
                    }

                    function updateLightboxContent() {
                        const file = window.lightboxFiles[currentLightboxIndex];
                        const contentDiv = document.getElementById('lightbox-content');
                        const captionDiv = document.getElementById('lightbox-caption');
                        const btnNext = document.getElementById('lightbox-next');
                        const btnPrev = document.getElementById('lightbox-prev');

                        // Handle Buttons visibility
                        btnPrev.style.display = currentLightboxIndex === 0 ? 'none' : 'block';
                        btnNext.style.display = currentLightboxIndex === window.lightboxFiles.length - 1 ? 'none' : 'block';

                        // Handle Content
                        if (file.file_type === 'image') {
                            contentDiv.innerHTML = `<img src="${esc(file.file_url)}" style="max-width:100%; max-height:100%; object-fit:contain;">`;
                        } else if (file.file_type === 'pdf') {
                            contentDiv.innerHTML = `<iframe src="${esc(file.file_url)}" style="width:100%; height:100%; border:none;"></iframe>`;
                        } else {
                            contentDiv.innerHTML = `<div style="text-align:center; padding: 40px;"><h3>Vorschau nicht verfügbar</h3><a href="${esc(file.file_url)}" target="_blank" class="of-btn">Datei herunterladen</a></div>`;
                        }

                        // Handle Caption
                        const docType = getAttachmentDocumentType(file)
                            ? `[${getAttachmentDocumentType(file)}] `
                            : '';

                        const notice = getAttachmentNotice(file)
                            ? ` · Notiz: ${getAttachmentNotice(file)}`
                            : '';

                        captionDiv.textContent = `${currentLightboxIndex + 1} / ${window.lightboxFiles.length} - ${docType}${file.original_name || file.title || 'Datei'}${notice}`;
                    }
                    function getCurrentOfferWorkflowStatus() {
                        const possibleStatuses = [
                            state.folder?.offer_status,
                            state.folder?.workflow_status,
                            state.offer?.offer_status,
                            state.offer?.status,
                            state.folder?.status
                        ];

                        for (const value of possibleStatuses) {
                            const normalized = String(value || '').trim().toLowerCase();
                            if (normalized) return normalized;
                        }
                        return 'draft';
                    }

                    function shouldShowClonePromptBeforeLoad() {
                        const documentStatus = String(
                            state.detail?.document_status ||
                            state.folder?.document_status ||
                            'offer'
                        ).toLowerCase();

                        if (documentStatus !== 'offer') return false;

                        const workflowStatus = getCurrentOfferWorkflowStatus();
                        return OFFER_STATUSES_REQUIRE_CLONE_PROMPT.includes(workflowStatus);
                    }

                    function openClonePromptModal() {
                        if (clonePromptModal) clonePromptModal.style.display = 'flex';
                    }

                    function closeClonePromptModal() {
                        if (clonePromptModal) clonePromptModal.style.display = 'none';
                    }
                    window.closeClonePromptModal = closeClonePromptModal;

                    if (clonePromptModal) {
                        clonePromptModal.addEventListener('click', function (e) {
                            if (e.target === clonePromptModal) closeClonePromptModal();
                        });
                    }

                    // Close Version Prompt Modal on outside click
                    if (versionPromptModal) {
                        versionPromptModal.addEventListener('click', function (e) {
                            if (e.target === versionPromptModal) versionPromptModal.style.display = 'none';
                        });
                    }



                    // 1. Intercept "Angebot laden" click
                    // Helper to handle loading logic after version is decided
                    function executeLoadOffer(loadSnapshot = false) {
                        // 1. Build the target URL
                        let targetUrl = btnLoadOffer.href;
                        if (loadSnapshot) {
                            targetUrl += '&load_snapshot=1';
                        }

                        // 2. Check if we need to show the Clone Prompt first
                        if (shouldShowClonePromptBeforeLoad()) {
                            // Update the "Aktuelles ändern" button in the clone modal to use our target URL
                            const cloneEditBtn = clonePromptModal.querySelector('a.of-btn.soft');
                            if (cloneEditBtn) cloneEditBtn.href = targetUrl;

                            openClonePromptModal();
                        } else {
                            // 3. Otherwise, directly navigate to the Editor
                            window.location.href = targetUrl;
                        }
                    }


                    // 1. Intercept the Main Button click
                    if (btnLoadOffer && !btnLoadOffer.dataset.ready) {
                        btnLoadOffer.addEventListener('click', function (e) {
                            e.preventDefault(); // Stop default navigation

                            const access = getTeamAccess();
                            if (state.teamAccess && access.canEdit === false) {
                                showCustomToast('Nur Ansicht', 'Dieses Angebot ist auf das Team beschränkt. Sie dürfen es ansehen, aber nicht bearbeiten.', 'error');
                                return;
                            }

                            const docStatus = String(state.detail?.document_status || 'offer').toLowerCase();

                            // If it's a deal, ask which version they want
                            if (docStatus === 'deal') {
                                versionPromptModal.style.display = 'flex';
                            } else {
                                // Normal Offer behavior
                                executeLoadOffer(false);
                            }
                        });
                        btnLoadOffer.dataset.ready = '1';
                    }

                    // 2. User chooses "Angebot ansehen" (Snapshot Read-Only)
                    if (btnLoadSnapshot) {
                        btnLoadSnapshot.addEventListener('click', function () {
                            versionPromptModal.style.display = 'none';

                            // Redirect to the Editor URL with the snapshot flag
                            window.location.href = btnLoadOffer.href + '&load_snapshot=1';
                        });
                    }

                    // 3. User chooses "Auftrag bearbeiten" (Current Deal Sections)
                    if (btnLoadCurrent) {
                        btnLoadCurrent.addEventListener('click', function () {
                            versionPromptModal.style.display = 'none';
                            // Pass false so it loads the active sections, not the snapshot
                            executeLoadOffer(false);
                        });
                    }

                    // 2. User chooses "Ursprüngliches Angebot" (Snapshot)
                    if (btnLoadSnapshot) {
                        btnLoadSnapshot.addEventListener('click', function () {
                            versionPromptModal.style.display = 'none';

                            // Redirect to the Editor URL and attach the trigger
                            window.location.href = btnLoadOffer.href + '&load_snapshot=1';
                        });
                    }

                    // 3. User chooses "Aktueller Auftrag" (Current Sections)
                    if (btnLoadCurrent) {
                        btnLoadCurrent.addEventListener('click', function () {
                            versionPromptModal.style.display = 'none';
                            executeLoadOffer(false);
                        });
                    }

                    // Existing Clone Confirm Logic
                    if (btnConfirmClone && !btnConfirmClone.dataset.ready) {
                        btnConfirmClone.addEventListener('click', async function () {
                            this.disabled = true;
                            this.textContent = 'Klone...';

                            try {
                                const cloneUrl = `/admin/offers/folders/${state.folder.id}/clone`;

                                const json = await fetchJson(cloneUrl, {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': getCsrfToken()
                                    },
                                    body: JSON.stringify({
                                        name: (state.folder?.name || 'Ordner') + ' (Kopie)',
                                        clone_everything: true,
                                        create_new_offer: false
                                    })
                                });

                                if (!json.success) {
                                    throw new Error(json.message || 'Klonen fehlgeschlagen.');
                                }

                                closeClonePromptModal();

                                if (json.redirect_url) {
                                    window.location.href = json.redirect_url;
                                    return;
                                }

                                if (json.folder_id) {
                                    window.location.href = `/admin/offers/folders/${json.folder_id}`;
                                    return;
                                }

                                throw new Error('Neue Ordner-ID wurde nicht zurückgegeben.');
                            } catch (error) {
                                alert('Fehler beim Klonen: ' + (error.message || 'Unbekannter Fehler'));
                                this.disabled = false;
                                this.textContent = 'Klonen (Neu) - Empfohlen';
                            }
                        });

                        btnConfirmClone.dataset.ready = '1';
                    }
                    const statusReasonModal = document.getElementById('status-reason-modal');
                    if (statusReasonModal) {
                        statusReasonModal.addEventListener('click', function (e) {
                            if (e.target === statusReasonModal) {
                                closeStatusReasonModal(null);
                            }
                        });
                    }
                    const uploadForm = document.getElementById('print-files-upload-form');
                    if (uploadForm) {
                        uploadForm.addEventListener('submit', function (e) {
                            e.preventDefault();

                            const input = document.getElementById('print-files-input');

                            if (input?.files && input.files.length > 0) {
                                triggerAutoUpload(input.files);
                            }
                        });
                    }
                    document.querySelectorAll('.of-tab[data-tab]').forEach(btn => {
                        btn.addEventListener('click', () => {
                            window.switchWorkspaceTab(btn.dataset.tab);
                        });
                    });

                    const materialFinalModal = document.getElementById('material-final-modal');
                    if (materialFinalModal) {
                        materialFinalModal.addEventListener('click', function (e) {
                            if (e.target === materialFinalModal) {
                                closeMaterialFinalModal(null);
                            }
                        });
                    }

                    const materialDetailModal = document.getElementById('material-detail-modal');
                    if (materialDetailModal) {
                        materialDetailModal.addEventListener('click', function (e) {
                            if (e.target === materialDetailModal) {
                                closeMaterialDetailModal();
                            }
                        });
                    }

                    document.querySelectorAll('.material-subtab-btn').forEach(btn => {
                        btn.addEventListener('click', () => {
                            state.materialFilter = btn.dataset.materialFilter || 'all';

                            document.querySelectorAll('.material-subtab-btn').forEach(x => {
                                x.classList.toggle('active', x === btn);
                            });

                            renderMaterialList();
                        });
                    });

                    const comparisonModal = document.getElementById('material-comparison-modal');
                    if (comparisonModal) {
                        comparisonModal.addEventListener('click', function (e) {
                            if (e.target === comparisonModal) {
                                closeMaterialComparisonModal();
                            }
                        });
                    }


                    document.querySelectorAll('.of-doc-toggle').forEach(btn => {
                        btn.addEventListener('click', async () => {
                            const targetStatus = String(btn.dataset.docStatus || 'offer').toLowerCase();
                            await changeDocumentStatusRequest(targetStatus);
                        });
                    });

                    const documentStatusModal = document.getElementById('document-status-modal');
                    if (documentStatusModal) {
                        documentStatusModal.addEventListener('click', function (e) {
                            if (e.target === documentStatusModal) {
                                closeDocumentStatusModal(null);
                            }
                        });
                    }


                    const materialMoveModal = document.getElementById('material-move-modal');
                    if (materialMoveModal) {
                        materialMoveModal.addEventListener('click', function (e) {
                            if (e.target === materialMoveModal) {
                                closeMaterialMoveModal(null);
                            }
                        });
                    }

                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') {
                            closeMaterialComparisonModal();
                            closeMaterialDetailModal();
                            closeMaterialMoveModal(null);
                            closeMaterialFinalModal(null);
                            closeLaborQualificationModal();
                        }
                    });

                    initPresenceChannel();
                    initFolderRealtimeConsistencyChannel();
                    await loadFolderData();
                    syncAgbInputs();
                });
            })();
        </script>
    @endpush
@endonce



@push('scripts')
    <script>
        window.GlobalBreadcrumbs = [
            {
                label: 'Dashboard',
                url: "{{ url('/') }}"
            },
            {
                label: 'Kundenliste',
                url: "{{ url('new_lead_view') }}",
            },
            {
                label: 'Angebotliste',
                url: "{{ url('admin/offers') }}",
                clickable: false
            },
            {
                label: '{{ $customerName ?: 'Unbekannt' }} - {{ $offer?->product?->article_group ?? 'Unbekannt' }}',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush