@extends('admin.layouts.app')

@section('title', 'Termine – Übersicht')

@section('style')
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">  
 
<style>
    :root {
        --app-green: #93c21c;
        --app-green-soft: #cfe09b;
        --app-blue: #74b2d4;
        --app-blue-soft: #e3effb;
        --app-black: #000000;
        --app-white: #ffffff;

        --radius-lg: 14px;
        --radius-xl: 18px;
        --shadow-soft: 0 14px 40px rgba(0, 0, 0, 0.08);
    }

    body {
        background: #f3f4f6;
    }

    .appointments-page {
        width: 100%;
        max-width: 100%;
        margin: 0;
        padding: 1.5rem 1.5rem 2.5rem;
        background: var(--app-white);
        box-sizing: border-box;
        border-radius: 0;
    }

    .appointments-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.2rem;
        flex-wrap: wrap;
    }

    .appointments-title {
        font-size: 1.6rem;
        font-weight: 600;
        color: var(--app-black);
        margin: 0 0 0.15rem;
    }

    .appointments-subtitle {
        font-size: 0.9rem;
        color: #4b5563;
        margin: 0;
    }

    .appointments-header-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .btn-primary-gradient {
        border-radius: 999px;
        border: none;
        padding: 0.55rem 1.3rem;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: linear-gradient(135deg, var(--app-green), var(--app-blue));
        color: var(--app-white);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        transition: transform 0.12s ease, box-shadow 0.12s ease, filter 0.12s ease;
    }

    .btn-primary-gradient:hover {
        transform: translateY(-1px);
        filter: brightness(1.03);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.15);
    }

    .btn-primary-gradient:active {
        transform: translateY(0);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.16);
    }

    /* Analytics-Karten */

    .analytics-row {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.2rem;
    }

    @media (min-width: 768px) {
        .analytics-row {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    @media (min-width: 1180px) {
        .analytics-row {
            grid-template-columns: repeat(7, minmax(0, 1fr));
        }
    }

    .analytics-card {
        background: var(--app-white);
        border-radius: var(--radius-lg);
        border: 1px solid rgba(148, 163, 184, 0.55);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        padding: 0.7rem 0.85rem;
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .analytics-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6b7280;
        font-weight: 600;
    }

    .analytics-value {
        font-size: 1.2rem;
        font-weight: 700;
        color: #111827;
    }

    .analytics-sub {
        font-size: 0.7rem;
        color: #9ca3af;
    }

    .analytics-card--accent-all {
        border-color: var(--app-blue);
    }

    .analytics-card--accent-mine {
        border-color: var(--app-green);
    }

    .analytics-card--accent-delayed {
        border-color: #ef4444;
    }

    .analytics-card--accent-due {
        border-color: #facc15;
    }

    .analytics-card--accent-archived {
        border-color: #9ca3af;
    }

    .analytics-card--accent-junk {
        border-color: #e11d48;
    }

    .analytics-card--accent-deleted {
        border-color: #6b7280;
    }

    /* Tabs & Ansicht-Umschalter */

    .appointments-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        align-items: center;
        margin-bottom: 1rem;
    }

    .appointments-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .tab-button {
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.5);
        padding: 0.3rem 0.9rem;
        font-size: 0.8rem;
        font-weight: 500;
        background: rgba(255, 255, 255, 0.9);
        color: #111827;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
        white-space: nowrap;
    }

        /* Reports badge */

    .report-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.7);
        background: #f9fafb;
        font-size: 0.68rem;
        padding: 0.2rem 0.6rem;
        cursor: pointer;
        color: #374151;
        white-space: nowrap;
    }

    .report-pill:hover {
        background: #e5f4ff;
        border-color: #60a5fa;
        color: #1d4ed8;
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
    }

    .report-pill-icon {
        font-size: 0.8rem;
    }

    .report-pill-counts {
        display: inline-flex;
        gap: 0.25rem;
        align-items: center;
    }

    .report-pill-counts span {
        display: inline-flex;
        align-items: center;
        gap: 0.1rem;
    }

    /* Report modal */

    .report-modal {
        position: fixed;
        inset: 0;
        z-index: 50;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .report-modal.is-open {
        display: flex;
    }

    .report-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(4px);
    }

    .report-modal-dialog {
        position: relative;
        z-index: 1;
        width: min(1100px, 96vw);
        height: min(80vh, 900px);
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 28px 90px rgba(15, 23, 42, 0.5);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .report-modal-header {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        background: linear-gradient(135deg, #0f172a, #111827);
        color: #e5e7eb;
    }

    .report-modal-title {
        font-size: 0.95rem;
        font-weight: 600;
        margin: 0;
    }

    .report-modal-close {
        border: none;
        background: none;
        font-size: 1.4rem;
        line-height: 1;
        cursor: pointer;
        color: #9ca3af;
    }

    .report-modal-close:hover {
        color: #f9fafb;
    }

    .report-modal-body {
        flex: 1;
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(0, 1.2fr);
        gap: 0;
        overflow: hidden;
    }

    @media (max-width: 900px) {
        .report-modal-body {
            grid-template-columns: minmax(0, 1fr);
            grid-template-rows: 1.1fr 1.1fr;
        }
    }

    .report-modal-column {
        padding: 0.85rem 1rem;
        overflow-y: auto;
        border-right: 1px solid #e5e7eb;
    }

    .report-modal-column:last-child {
        border-right: none;
        border-left: 1px solid #e5e7eb;
    }

    .report-modal-section-title {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #6b7280;
        margin-bottom: 0.45rem;
    }

    .report-modal-form-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.45rem;
        margin-bottom: 0.45rem;
    }

    .report-modal-input,
    .report-modal-textarea {
        width: 100%;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        padding: 0.35rem 0.55rem;
        font-size: 0.78rem;
        resize: vertical;
        box-sizing: border-box;
        background: #ffffff;
    }

    .report-modal-textarea {
        min-height: 70px;
    }

    .report-modal-input:focus,
    .report-modal-textarea:focus {
        border-color: #74b2d4; 
        outline: none;
    }

    .report-modal-btn {
        border-radius: 999px;
        border: none;
        padding: 0.35rem 0.9rem;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        background: #74b2d4;
        color: #f9fafb; 
    }

    .report-modal-btn:hover {
        filter: brightness(1.05);
    }

    .report-modal-list {
        margin-top: 0.45rem;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .report-modal-item {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        padding: 0.5rem 0.6rem;
        font-size: 0.78rem;
    }

    .report-modal-item-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 0.4rem;
        margin-bottom: 0.2rem;
    }

    .report-modal-item-author {
        font-weight: 600;
        color: #111827;
    }

    .report-modal-item-time {
        font-size: 0.7rem;
        color: #9ca3af;
    }

    .report-modal-item-body {
        color: #4b5563;
    }

    .report-modal-item-next {
        margin-top: 0.25rem;
        padding: 0.25rem 0.4rem;
        border-radius: 9px;
        background: #eff6ff;
        border: 1px dashed #bfdbfe;
        font-size: 0.74rem;
    }

    .report-modal-comment-item {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        background: #ffffff;
        padding: 0.45rem 0.55rem;
        font-size: 0.78rem;
    }

    .report-modal-comment-header {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 0.4rem;
        margin-bottom: 0.1rem;
    }

    .report-modal-comment-author {
        font-weight: 600;
        color: #111827;
    }

    .report-modal-comment-time {
        font-size: 0.7rem;
        color: #9ca3af;
    }

    .report-modal-comment-body {
        color: #4b5563;
        white-space: pre-line;
    }


    .tab-button:hover {
        background: #f3f4f6;
        box-shadow: 0 2px 8px rgba(148, 163, 184, 0.35);
    }

    .tab-button--active {
        background: var(--app-black);
        color: var(--app-white);
        border-color: var(--app-black);
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.35);
    }

    .view-switch-group {
        margin-left: auto;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .view-switch {
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.7);
        padding: 0.35rem 0.9rem;
        font-size: 0.75rem;
        font-weight: 500;
        cursor: pointer;
        background: #f9fafb;
        color: #111827;
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .view-switch--active {
        background: var(--app-blue);
        color: var(--app-black);
        border-color: var(--app-blue);
        box-shadow: 0 3px 10px rgba(55, 65, 81, 0.35);
    }

    /* Filterbereich */

    .appointments-filters {
        display: grid;
        grid-template-columns: repeat(1, minmax(0, 1fr));
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        background: var(--app-blue-soft);
        border-radius: var(--radius-xl);
        padding: 0.9rem;
        border: 1px solid rgba(148, 163, 184, 0.4);
    }

    @media (min-width: 720px) {
        .appointments-filters {
            grid-template-columns: minmax(0, 2.2fr) repeat(3, minmax(0, 1fr));
        }
    }

    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        min-width: 0;
    }

    .filter-label {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #4b5563;
    }

    .filter-input,
    .filter-select {
        border-radius: 999px;
        border: 1px solid rgba(148, 163, 184, 0.75);
        padding: 0.45rem 0.8rem;
        font-size: 0.8rem;
        background: var(--app-white);
        color: #111827;
        outline: none;
        width: 100%;
        box-sizing: border-box;
        min-height: 2.3rem;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .filter-input:focus,
    .filter-select:focus {
        border-color: var(--app-blue);
        box-shadow: 0 0 0 1px rgba(116, 178, 212, 0.55);
    }

    .filter-input::placeholder {
        color: #94a3b8;
    }

    .filter-group--row {
        display: flex;
        gap: 0.4rem;
    }

    /* Kanban */

    .kanban-grid {
        display: grid;
        gap: 1rem;
    }

    @media (min-width: 900px) {
        .kanban-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }

    @media (min-width: 640px) and (max-width: 899px) {
        .kanban-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .kanban-column {
        display: flex;
        flex-direction: column;
        background: #ffffff;
        border-radius: var(--radius-xl);
        border: 1px solid rgba(148, 163, 184, 0.55);
        min-height: 110px;
        overflow: hidden;
    }

    .kanban-column-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.6rem 0.8rem;
        background:#93c21c2e;;
        border-bottom: 1px solid rgba(148, 163, 184, 0.55);
    }

    .kanban-column-title {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.09em;
        color: #1f2933;
    }

    .kanban-column-count {
        font-size: 0.7rem;
        color: #6b7280;
        padding: 0.15rem 0.55rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.85);
    }

    .kanban-droppable {
        flex: 1;
        padding: 0.55rem 0.65rem 0.7rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .kanban-card {
        background: var(--app-white);
        border-radius: var(--radius-lg);
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.10);
        border: 1px solid rgba(148, 163, 184, 0.5);
        padding: 0.65rem 0.75rem;
        font-size: 0.75rem;
        cursor: grab;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        transition: box-shadow 0.12s ease, transform 0.12s ease, border-color 0.12s ease;
    }

    .kanban-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.18);
        border-color: var(--app-blue);
    }

    .kanban-card.is-dragging {
        opacity: 0.55;
        transform: scale(0.98);
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.25);
    }

    .kanban-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.4rem;
    }

    .kanban-card-title {
        font-weight: 600;
        font-size: 0.85rem;
        color: #111827;
        max-width: 180px;
        word-break: break-word;
    }

    .kanban-card-subtitle {
        font-size: 0.7rem;
        color: #6b7280;
        max-width: 180px;
        word-break: break-word;
    }

    .kanban-card-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.65rem;
        color: #6b7280;
        gap: 0.25rem;
    }

    .kanban-card-footer {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        align-items: center;
        margin-top: 0.2rem;
    }

    .employee-pill {
        padding: 0.08rem 0.5rem;
        border-radius: 999px;
        font-size: 0.65rem;
        background: rgba(226, 232, 240, 0.95);
        color: #111827;
    }

    .btn-link-sm {
        font-size: 0.65rem;
        border: none;
        background: none;
        padding: 0;
        color: #6b7280;
        cursor: pointer;
        text-decoration: underline;
    }

    .btn-link-sm:hover {
        color: #111827;
    }

    .kanban-card-customer {
        border: none;
        background: #e0f2fe;
        padding: 0.05rem 0.5rem;
        border-radius: 999px;
        font-size: 0.7rem;
        color: #74b2d4;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        text-decoration: none;
        border: 1px solid #bfdbfe;
    }

    .kanban-card-customer:hover {
        background: #bfdbfe;
    }


    .employee-avatar-wrapper {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 999px;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.8);
        background: #e5e7eb;
        box-shadow: 0 4px 10px rgba(15, 23, 42, 0.18);
    }

    .employee-avatar {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* Customer Profile Modal */

    .customer-modal {
        position: fixed;
        inset: 0;
        z-index: 40;
        display: none;
        align-items: center;
        justify-content: center;
    }

    .customer-modal.is-open {
        display: flex;
    }

    .customer-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(4px);
    }

    .customer-modal-dialog {
        position: relative;
        z-index: 1;
        width: min(1759px, 96vw);
        height: min(80vh, 900px);
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 24px 80px rgba(15, 23, 42, 0.4);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .customer-modal-header {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
    }

    .customer-modal-title {
        font-size: 0.95rem;
        font-weight: 600;
        color: #111827;
        margin: 0;
    }

    .customer-modal-close {
        border: none;
        background: none;
        font-size: 1.4rem;
        line-height: 1;
        cursor: pointer;
        color: #4b5563;
    }

    .customer-modal-close:hover {
        color: #111827;
    }

    .customer-modal-body {
        flex: 1;
        padding: 0;
    }

    .customer-modal-body iframe {
        width: 100%;
        height: 100%;
        border: none;
    }



    /* Badges */

    .priority-badge {
        padding: 0.1rem 0.55rem;
        border-radius: 999px;
        font-size: 0.65rem;
        border-width: 1px;
        border-style: solid;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .priority-badge--urgent {
        border-color: #b91c1c;
        background: #fee2e2;
        color: #7f1d1d;
    }

    .priority-badge--high {
        border-color: #c05621;
        background: #ffedd5;
        color: #9a3412;
    }

    .priority-badge--normal {
        border-color: var(--app-green);
        background: var(--app-green-soft);
        color: #1f2933;
    }

    .priority-badge--low {
        border-color: #9ca3af;
        background: #e5e7eb;
        color: #374151;
    }

    .priority-badge--default {
        border-color: #d1d5db;
        background: #f9fafb;
        color: #4b5563;
    }

    .status-badge {
        padding: 0.12rem 0.7rem;
        border-radius: 999px;
        border-width: 1px;
        border-style: solid;
        font-size: 0.7rem;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .status-badge--planned {
        border-color: var(--app-blue);
        background: #e0f2fe;
        color: #1d4ed8;
    }

    .status-badge--in_progress {
        border-color: #4f46e5;
        background: #e0e7ff;
        color: #3730a3;
    }

    .status-badge--done {
        border-color: var(--app-green);
        background: var(--app-green-soft);
        color: #14532d;
    }

    .status-badge--archived {
        border-color: #9ca3af;
        background: #f3f4f6;
        color: #4b5563;
    }

    .status-badge--junk {
        border-color: #e11d48;
        background: #ffe4e6;
        color: #9f1239;
    }

    .status-badge--default {
        border-color: #d1d5db;
        background: #f9fafb;
        color: #4b5563;
    }

    /* Listenansicht */

    .appointments-list-container {
        border-radius: var(--radius-xl);
        border: 1px solid rgba(148, 163, 184, 0.5);
        overflow-x: auto;
        background: var(--app-white);
        box-shadow: var(--shadow-soft);
    }

    .appointments-table {
        min-width: 100%;
        border-collapse: collapse;
        font-size: 0.8rem;
    }

    .appointments-table thead {
        background: var(--app-blue-soft);
    }

    .appointments-table th {
        text-align: left;
        padding: 0.5rem 0.75rem;
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #4b5563;
        letter-spacing: 0.08em;
        white-space: nowrap;
    }

    .appointments-table td {
        padding: 0.55rem 0.75rem;
        vertical-align: top;
        border-top: 1px solid #e5e7eb;
        color: #111827;
    }

    .appointments-table tr:hover td {
        background: #f9fafb;
    }

    .appointments-table-title {
        font-weight: 600;
        font-size: 0.85rem;
        color: #111827;
        margin-bottom: 0.1rem;
    }

    .appointments-table-subtitle {
        font-size: 0.7rem;
        color: #6b7280;
    }

    .table-actions {
        text-align: right;
        white-space: nowrap;
    }

    .btn-table {
        border: none;
        background: none;
        font-size: 0.72rem;
        cursor: pointer;
        padding: 0;
        margin-left: 0.3rem;
    }

    .btn-table--edit {
        color: #4b5563;
    }

    .btn-table--edit:hover {
        color: #111827;
    }

    .btn-table--archive {
        color: #b45309;
    }

    .btn-table--archive:hover {
        color: #92400e;
    }

    .btn-table--delete {
        color: #b91c1c;
    }

    .btn-table--delete:hover {
        color: #991b1b;
    }

        /* Util */
    .appointment-drawer {
        position: fixed;
        inset: 0;
        z-index: 12;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.18s ease-out;
    }

    .appointment-drawer.is-open {
        pointer-events: auto;
        opacity: 1;
    }

    .appointment-drawer-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(4px);
    }

    .appointment-drawer-panel {
        position: absolute;
        top: 0;
        right: 0;
        height: 100%;
        width: min(1200px, 100%);
        background: #ffffff;
        box-shadow: -24px 0 70px rgba(15, 23, 42, 0.25);
        border-radius: 24px 0 0 24px;
        display: flex;
        flex-direction: column;
        transform: translateX(100%);
        transition: transform 0.22s ease-out;
    }

    .appointment-drawer.is-open .appointment-drawer-panel {
        transform: translateX(0);
    }

    .appointment-drawer-panel .card-body {
        padding: 1rem 1.25rem 1rem;
        overflow-y: auto;
    }

    .appointment-drawer-panel .modal-body {
        max-height: calc(100vh - 150px);
        overflow-y: auto;
    }

    .appointment-drawer-panel .modal-footer {
        border-top: 1px solid #e5e7eb;
        padding: 0.75rem 1.25rem 1rem;
    }

    .is-hidden {
        display: none !important;
    }

    .appointments-table th.sortable {
        cursor: pointer;
        position: relative;
        user-select: none;
    }

    .appointments-table th.sortable::after {
        content: '';
        position: absolute;
        right: 6px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 0.6rem;
        color: #9ca3af;
    }

    .appointments-table th.sortable.is-sorted-asc::after {
        content: '▲';
    }

    .appointments-table th.sortable.is-sorted-desc::after {
        content: '▼';
    }


    /* Notification ticker (Breaking News style) */
.app-notification-bar {
    margin-top: 0.4rem;
    display: flex;
    align-items: stretch;
    background: white;
    border-radius: 999px;
    padding: 0.2rem;
    box-shadow: 0 14px 40px rgba(15, 23, 42, 0.35);
    overflow: hidden;
    position: relative;
}

.app-notification-bar.is-hidden {
    display: none;
}

.app-notification-pill {
    display: flex;
    align-items: center;
    gap: 0.35rem;
    background: rgba(250, 250, 250, 0.96);
    color: #166534;
    border-radius: 999px;
    padding: 0.35rem 0.8rem;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.14em;
    box-shadow: 0 6px 20px rgba(15, 23, 42, 0.16);
}

.app-notification-pill-icon {
    display: inline-flex;
    width: 18px;
    height: 18px;
    border-radius: 999px;
    align-items: center;
    justify-content: center;
    background: #a3e635;
    box-shadow: 0 0 0 2px #bbf7d0;
    font-size: 0.8rem;
}

.app-notification-pill-label {
    white-space: nowrap;
}

.app-notification-track {
    flex: 1;
    display: flex;
    align-items: center;
    padding: 0.3rem 0.9rem;
    margin-left: 0.4rem;
    margin-right: 0.4rem;
    border-radius: 999px;
    background: radial-gradient(circle at 0% 0%, #111827 0, #020617 55%, #020617 100%);
    color: #e5e7eb;
    min-width: 0;
    gap: 0.65rem;
}

.app-notification-type-badge {
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: 0.12em;
    padding: 0.18rem 0.6rem;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, 0.6);
    background: rgba(15, 23, 42, 0.85);
    white-space: nowrap;
}

.app-notification-type-badge--due {
    border-color: #f97316;
    background: rgba(248, 113, 113, 0.08);
    color: #fed7aa;
}

.app-notification-main {
    display: flex;
    flex-direction: column;
    min-width: 0;
    flex: 1;
    gap: 0.05rem;
}

.app-notification-title {
    font-size: 0.82rem;
    font-weight: 600;
    color: #e5e7eb;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.app-notification-message {
    font-size: 0.78rem;
    color: #9ca3af;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.app-notification-meta {
    font-size: 0.7rem;
    color: #9ca3af;
    white-space: nowrap;
}

/* Icon-Styling im Report-Modal */
.icon-report,
.icon-comment,
.icon-followup {
    width: 14px;
    height: 14px;
    display: inline-block;
    flex-shrink: 0;
}

/* Abstand in Section-Titeln (SVG + Text) */
.section-title-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 0.35rem;
}

/* Label-Styling im Formularbereich */
.report-modal-label {
    display: block;
    font-size: 0.72rem;
    font-weight: 500;
    color: #4b5563;
    margin-bottom: 0.15rem;
}

/* Zeile für "Nächster Schritt" + "Fällig bis" */
.report-modal-form-row--followup {
    margin-top: 0.35rem;
    display: grid;
    grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
    gap: 0.45rem;
}

/* allgemeine Field-Wrapper im Modal */
.report-modal-field {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
}

/* Button-Row im Modal (Report & Kommentar speichern) */
.report-modal-actions {
    margin-top: 0.45rem;
    text-align: right;
}

/* Listen-Titel (Reports / Kommentare) */
.report-modal-section-title--list {
    margin-top: 0.7rem;
}

/* Follow-up-Block in den bestehenden Reports */
.report-modal-item-followup {
    margin-top: 0.3rem;
    padding: 0.35rem 0.5rem;
    border-radius: 9px;
    background: #eff6ff;
    border: 1px dashed #bfdbfe;
    font-size: 0.74rem;
}

.report-modal-item-followup-title {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-weight: 600;
    color: #1d4ed8;
    margin-bottom: 0.15rem;
}

.report-modal-item-followup-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.35rem;
}

.followup-col {
    display: flex;
    flex-direction: column;
    gap: 0.05rem;
}

.followup-label {
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #6b7280;
}

.followup-value {
    font-size: 0.76rem;
    font-weight: 500;
    color: #111827;
}


.app-notification-controls {
    display: flex;
    align-items: center;
    gap: 0.1rem;
}

.app-notification-control {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    border: 1px solid rgba(15, 23, 42, 0.6);
    background: rgba(15, 23, 42, 0.96);
    color: #e5e7eb;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    cursor: pointer;
    padding: 0;
    transition: transform 0.08s ease, box-shadow 0.12s ease, background 0.12s ease;
}

.app-notification-control:hover {
    transform: translateY(-1px);
    background: rgba(15, 23, 42, 1);
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.4);
}

.app-notification-control:active {
    transform: translateY(0);
    box-shadow: 0 3px 10px rgba(15, 23, 42, 0.35);
}

.app-notification-bar.is-fade-in {
    animation: notifFadeIn 0.25s ease-out;
}

@keyframes notifFadeIn {
    from { opacity: 0; transform: translateY(-2px); }
    to   { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
    .app-notification-bar {
        flex-direction: column;
        align-items: stretch;
        border-radius: 18px;
    }
    .app-notification-track {
        margin: 0.35rem 0.15rem 0.2rem;
        border-radius: 12px;
    }
}



    @media (max-width: 599px) {
        .appointments-header {
            align-items: flex-start;
        }
        .view-switch-group {
            width: 100%;
            justify-content: flex-start;
        }
    }
</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
             
        </div>

        <div class="content-body">
            <div class="appointments-page">
                {{-- Kopfbereich --}}
                <div class="appointments-header">
                    <div>
                        <h1 class="appointments-title">Termine</h1>
                        <p class="appointments-subtitle">Kanban & Listenansicht mit Filtern, Suche und Verknüpfungen</p>
                    </div>
                    <div class="appointments-header-actions">
                        <button id="btnCreateAppointment" class="btn-primary-gradient">
                            <span>+ Neuer Termin</span>
                        </button>
                    </div>
                </div>

                {{-- Analytics-Karten --}}
                <div class="analytics-row">
                    <div class="analytics-card analytics-card--accent-all">
                        <div class="analytics-label">Alle</div>
                        <div class="analytics-value" id="analytics-all">0</div>
                        <div class="analytics-sub">Gesamt</div>
                    </div>
                    <div class="analytics-card analytics-card--accent-mine">
                        <div class="analytics-label">Meine</div>
                        <div class="analytics-value" id="analytics-mine">0</div>
                        <div class="analytics-sub">Mir zugeordnet</div>
                    </div>
                    <div class="analytics-card analytics-card--accent-delayed">
                        <div class="analytics-label">Überfällig</div>
                        <div class="analytics-value" id="analytics-delayed">0</div>
                        <div class="analytics-sub">Vergangene Termine offen</div>
                    </div>
                    <div class="analytics-card analytics-card--accent-due">
                        <div class="analytics-label">Heute fällig</div>
                        <div class="analytics-value" id="analytics-due">0</div>
                        <div class="analytics-sub">Start heute</div>
                    </div>
                    <div class="analytics-card analytics-card--accent-archived">
                        <div class="analytics-label">Archiv</div>
                        <div class="analytics-value" id="analytics-archived">0</div>
                        <div class="analytics-sub">Archiviert</div>
                    </div>
                    <div class="analytics-card analytics-card--accent-junk">
                        <div class="analytics-label">Junk</div>
                        <div class="analytics-value" id="analytics-junk">0</div>
                        <div class="analytics-sub">Nicht relevant</div>
                    </div>
                    <div class="analytics-card analytics-card--accent-deleted">
                        <div class="analytics-label">Gelöscht</div>
                        <div class="analytics-value" id="analytics-deleted">0</div>
                        <div class="analytics-sub">Papierkorb (aktueller Tab)</div>
                    </div>
                </div>

                {{-- Notification Ticker --}}
                    <div id="appointmentNotificationBar" class="app-notification-bar is-hidden mb-2">
                        <div class="app-notification-pill">
                            <span class="app-notification-pill-icon">⚡</span> 
                        </div>

                        <div class="app-notification-track">
                            <div class="app-notification-type-badge" id="notifKindBadge">Aufgabe</div>
                            <div class="app-notification-main">
                                <div class="app-notification-title" id="notifTitle">–</div>
                                <div class="app-notification-message" id="notifMessage">–</div>
                            </div>
                            <div class="app-notification-meta" id="notifMeta">–</div>
                        </div>

                        <div class="app-notification-controls">
                            <button class="app-notification-control" data-notif-action="prev" title="Vorherige">
                                <span>&lt;</span>
                            </button>
                            <button class="app-notification-control" data-notif-action="toggle" title="Pause">
                                <span id="notifPlayPauseIcon">❚❚</span>
                            </button>
                            <button class="app-notification-control" data-notif-action="next" title="Nächste">
                                <span>&gt;</span>
                            </button>
                        </div>
                    </div>


                {{-- Tabs + Ansicht --}}
                <div class="appointments-toolbar">
                    <div class="appointments-tabs">
                        @php
                            $tabs = [
                                'all'      => 'Alle',
                                'mine'     => 'Meine Termine',
                                'delayed'  => 'Überfällig',
                                'due'      => 'Heute fällig',
                                'archived' => 'Archiv',
                                'junk'     => 'Junk',
                                'deleted'  => 'Gelöscht',
                            ];
                        @endphp
                        @foreach($tabs as $key => $label)
                            <button
                                class="tab-button {{ $key === 'all' ? 'tab-button--active' : '' }}"
                                data-tab="{{ $key }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    <div class="view-switch-group">
                        <button id="btnViewKanban"
                                class="view-switch view-switch--active"
                                data-view="kanban">
                            Kanban
                        </button>
                        <button id="btnViewList"
                                class="view-switch"
                                data-view="list">
                            Liste
                        </button>
                    </div>
                </div>

                {{-- Filter --}}
                <div class="appointments-filters">
                    <div class="filter-group">
                        <label class="filter-label">Suche</label>
                        <input id="filterSearch" type="text"
                               class="filter-input"
                               placeholder="Titel, Kunde, Ort, Telefon, E-Mail …">
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Von-Datum</label>
                        <input id="filterFromDate" type="date"
                               class="filter-input">
                    </div>

                    <div class="filter-group">
                        <label class="filter-label">Bis-Datum</label>
                        <input id="filterToDate" type="date"
                               class="filter-input">
                    </div>

                    <div class="filter-group filter-group--row">
                        <div class="filter-group" style="flex:1">
                            <label class="filter-label">Mitarbeiter</label>
                            <select id="filterEmployee" class="filter-select">
                                <option value="">Alle</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">
                                        {{ trim($employee->title.' '.$employee->name.' '.$employee->lastname) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-group" style="flex:1">
                            <label class="filter-label">Betrieb</label>
                            <select id="filterBranch" class="filter-select">
                                <option value="">Alle</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->branch }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Kanban-Ansicht --}}
                <div id="kanbanView" class="kanban-grid">
                    @php
                        $columns = [
                            'planned'     => 'Geplant',
                            'in_progress' => 'In Arbeit',
                            'done'        => 'Erledigt',
                            'archived'    => 'Archiviert',
                        ];
                    @endphp

                    @foreach($columns as $status => $label)
                        <div class="kanban-column" data-status="{{ $status }}">
                            <div class="kanban-column-header">
                                <span class="kanban-column-title">{{ $label }}</span>
                                <span class="kanban-column-count" data-count="{{ $status }}">0</span>
                            </div>
                            <div class="kanban-droppable" data-status="{{ $status }}">
                                {{-- Karten per JS --}}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Listen-Ansicht --}}
                <div id="listView" class="is-hidden" style="margin-top: 1.5rem;">
                    <div class="appointments-list-container">
                        <table class="appointments-table">
                            <thead>
                                <tr>
                                    <th class="sortable" data-sort="name">Titel</th>
                                    <th class="sortable" data-sort="start_date">Zeitraum</th>
                                    <th class="sortable" data-sort="customer_name">Kunde/Kontakt</th>
                                    <th class="sortable" data-sort="city">Ort</th>
                                    <th class="sortable" data-sort="employees">Mitarbeiter</th>
                                    <th class="sortable" data-sort="status">Status</th>
                                    <th class="sortable" data-sort="priority">Priorität</th>
                                    <th class="table-actions">Aktionen</th>
                                </tr>
                                </thead>


                            <tbody id="appointmentsTableBody">
                                {{-- Zeilen per JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- GROSSER TERMIN-FORMULAR-CARD --}}
                <div id="appointmentDrawer" class="appointment-drawer">
                    <div id="appointmentDrawerBackdrop" class="appointment-drawer-backdrop"></div>

                    <div class="appointment-drawer-panel cards new_task_card new_task">
                        <div class="card-header d-flex align-items-center justify-content-between" style="border-bottom:1px solid #e5e7eb;">
                            <h3 class="title mb-0" style="font-size:1.1rem;font-weight:600;color:#111827;">
                                TERMIN ERSTELLEN / BEARBEITEN
                            </h3>
                            <button type="button" class="btn btn-sm btn-outline-secondary close_task_window">
                                <i class="feather icon-x"></i>
                            </button>
                        </div>

                        <div class="card-body">
                            <form id="task-store-form">
                                @csrf
                                <input type="hidden" name="id" id="appointment_id">
                                <input type="hidden" name="contact_mode" id="contact_mode" value="new">
                                <input type="hidden" name="contact_type" id="contact_type">

                                <div class="modal-body">

                                    {{-- SECTION: Kontakt --}}
                                    <div class="section-title">Kontakt</div>
                                    <div class="section-box">
                                        <div class="form-row">
                                            <div class="col-md-12 mb-1">
                                                <label>Typ</label><br>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input contact-type-toggle" type="radio"
                                                        name="contact_mode_radio" id="newContact" value="new" checked>
                                                    <label class="form-check-label" for="newContact">Neu</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input contact-type-toggle" type="radio"
                                                        name="contact_mode_radio" id="selectContact" value="select">
                                                    <label class="form-check-label" for="selectContact">Kontakt</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            {{-- New contact --}}
                                            <div class="col-md-12 contact-name-block">
                                                <label for="name">Kunde/Kontakt *</label>
                                                <input type="text" id="name" class="form-control name" name="name">
                                            </div>

                                            {{-- Existing contact (Select2 remote) --}}
                                            <div class="col-md-12 contact-select-block d-none">
                                                <label for="customer_id">Kunde/Kontakt *</label>
                                                <select name="customer_id" id="customer_id" class="contact_list" style="width:100%"></select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- SECTION: Termin-Daten --}}
                                    <div class="section-title">Termin</div>
                                    <div class="section-box">
                                        <div class="form-row">
                                            <div class="col-md-10 col-10 mb-1">
                                                <label for="appointment_type">Art des Termins</label>
                                                <input type="text"
                                                    class="form-control"
                                                    id="appointment_type"
                                                    name="appointment_type"
                                                    value="{{ old('appointment_type') }}">
                                            </div>
                                            <div class="col-md-2 col-2 mb-1 d-flex align-items-end">
                                                <input type="hidden" name="color" id="color" value="#8fc73e">
                                                <div class="btn-group dropup dropdown-icon-wrapper w-100" id="color_drop_down">
                                                    <button type="button" class="btn btn-light btn-block" data-toggle="dropdown"
                                                            aria-haspopup="true" aria-expanded="true">
                                                        <i class="fa fa-square" id="colorIcon" style="color:#8fc73e;"></i>
                                                    </button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <span class="dropdown-item" data-value="#8fc73e"><i class="fa fa-square" style="color:#8fc73e;"></i> Grün</span>
                                                        <span class="dropdown-item" data-value="#ff0000"><i class="fa fa-square" style="color:#ff0000;"></i> Rot</span>
                                                        <span class="dropdown-item" data-value="#0000ff"><i class="fa fa-square" style="color:#0000ff;"></i> Blau</span>
                                                        <span class="dropdown-item" data-value="#ffff00"><i class="fa fa-square" style="color:#ffff00;"></i> Gelb</span>
                                                        <span class="dropdown-item" data-value="#ff00ff"><i class="fa fa-square" style="color:#ff00ff;"></i> Magenta</span>
                                                        <span class="dropdown-item" data-value="#00ffff"><i class="fa fa-square" style="color:#00ffff;"></i> Cyan</span>
                                                        <span class="dropdown-item" data-value="#000000"><i class="fa fa-square" style="color:#000000;"></i> Schwarz</span>
                                                        <span class="dropdown-item" data-value="#808080"><i class="fa fa-square" style="color:#808080;"></i> Grau</span>
                                                        <span class="dropdown-item" data-value="#ffa500"><i class="fa fa-square" style="color:#ffa500;"></i> Orange</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-12 mb-1">
                                                <label for="start_date">Startdatum *</label>
                                                <input type="date" id="start_date" class="form-control" name="start_date">
                                            </div>
                                            <div class="col-md-6 col-12 mb-1">
                                                <label for="end_date">Enddatum *</label>
                                                <input type="date" id="end_date" class="form-control" name="end_date">
                                            </div>

                                            <div class="col-md-4 col-12 mb-1">
                                                <label for="start_time">Startzeit *</label>
                                                <input type="time" id="start_time" class="form-control" name="start_time">
                                            </div>
                                            <div class="col-md-4 col-12 mb-1">
                                                <label for="end_time">Endzeit</label>
                                                <input type="time" id="end_time" class="form-control" name="end_time">
                                            </div>
                                            <div class="col-md-4 col-12 mb-1">
                                                <label for="total_time">Dauer (Minuten)</label>
                                                <input type="number" id="total_time" class="form-control" name="total_time">
                                            </div>

                                            <div class="col-md-6 col-12 mb-1">
                                                <label for="priority">Priorität</label>
                                                <select name="priority" class="form-control" id="priority">
                                                    <option value="normal">Keiner</option>
                                                    <option value="medium">Medium</option>
                                                    <option value="high">Hoch</option>
                                                    <option value="very high">Sehr wichtig</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- SECTION: Teilnehmer --}}
                                    <div class="section-title">Teilnehmer</div>
                                    <div class="section-box" id="participantsBlock">
                                        <select name="employee[]" id="employee" class="employee" multiple style="width:100%">
                                            @foreach ($employees as $emp)
                                                <option value="{{ $emp->id }}" data-image="{{ asset('images/employee/'.$emp->image) }}">
                                                    {{ $emp->name }} {{ $emp->lastname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    {{-- SECTION: Ort & Kontakt --}}
                                    <div class="section-title">Ort & Kontakt</div>
                                    <div class="section-box">
                                        <div class="form-row">
                                            <div class="col-md-6 mb-1" id="intern" style="display:none;">
                                                <label for="branch_address_id">Adresse (Betrieb)</label>
                                                <select name="branch_address_id" id="branch_address_id" class="form-control">
                                                    <option></option>
                                                    @foreach ($branch_addresses as $address)
                                                        <option value="{{ $address->id }}"
                                                                data-street="{{ $address->street }}"
                                                                data-latitude="{{ $address->latitude }}"
                                                                data-longitude="{{ $address->longitude }}"
                                                                data-city="{{ $address->city }}"
                                                                data-postcode="{{ $address->postcode }}">
                                                            {{ $address->branch_initial }} - {{ $address->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-1" id="extern">
                                                <label for="full_address">Adresse</label>
                                                <input id="full_address" type="text" class="form-control form-element"
                                                    placeholder="Adresse eingeben" name="full_address">

                                                <input type="hidden" id="street-input" name="street">
                                                <input type="hidden" id="city-input" name="city">
                                                <input type="hidden" id="latitude-input" name="latitude">
                                                <input type="hidden" id="longitude-input" name="longitude">
                                                <input type="hidden" id="postal_code-input" name="postcode">
                                            </div>

                                            <div class="col-md-6 mb-1">
                                                <label for="execution_type">Ort des Termins</label>
                                                <select name="execution_type" id="execution_type" class="form-control">
                                                    <option value="external" selected>Extern</option>
                                                    <option value="internal">Intern</option>
                                                    <option value="online">Online</option>
                                                    <option value="telephone">Telefon</option>
                                                </select>
                                            </div>

                                            <div class="col-md-6 mb-1">
                                                <label for="phone">Telefon</label>
                                                <input type="text" class="form-control phone" name="phone" id="phone" value="{{ old('phone') }}">
                                            </div>

                                            <div class="col-md-6 mb-1">
                                                <label for="email">E-Mail <small>Optional</small></label>
                                                <input type="email" class="form-control email" name="email" id="email" value="{{ old('email') }}">
                                            </div>

                                            <div class="col-md-6 mb-1" id="link_section" style="display:none;">
                                                <label for="link">Link (Online-Termin)</label>
                                                <input type="text" class="form-control" id="link" name="link" value="{{ old('link') }}">
                                            </div>

                                            <div class="col-md-6 mb-1">
                                                <label for="branch_id">Betrieb</label>
                                                <select name="branch_id" id="branch_id" class="selectables" style="width:100%">
                                                    <option></option>
                                                    @foreach($branches as $br)
                                                        <option value="{{ $br->id }}">{{ $br->branch }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-12 mb-1">
                                                <label for="description">Beschreibung</label>
                                                <textarea name="description" class="form-control" id="description" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                </div>{{-- /modal-body --}}

                                <div class="modal-footer d-flex justify-content-between">
                                    <button type="button" class="btn btn-danger btn-sm close_task_window">
                                        <i class="feather icon-x"></i> Abbrechen
                                    </button>
                                    <button type="button" class="btn btn-primary btn-sm save-task">
                                        <i class="feather icon-save"></i> Speichern
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- /Formular-Card --}}
            </div>
        </div>
    </div>
</div>


{{-- Customer Profile Modal --}}
<div id="customerProfileModal" class="customer-modal">
    <div class="customer-modal-backdrop"></div>
    <div class="customer-modal-dialog">
        <div class="customer-modal-header">
            <h3 class="customer-modal-title" id="customerModalTitle">Kunde</h3>
            <button type="button" class="customer-modal-close" id="customerModalClose">&times;</button>
        </div>
        <div class="customer-modal-body">
            <iframe id="customerProfileFrame" src="" loading="lazy"></iframe>
        </div>
    </div>
</div>

 <div id="appointmentReportModal" class="report-modal">
    <div class="report-modal-backdrop"></div>
    <div class="report-modal-dialog">
        <div class="report-modal-header">
            <h3 class="report-modal-title" id="reportModalTitle">
                <svg class="icon-report" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M6 2h8l4 4v16H6z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                    <path d="M14 2v4h4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                </svg>
                <span>Reports &amp; Kommentare</span>
            </h3>
            <button type="button" class="report-modal-close" id="reportModalClose" aria-label="Schließen">
                &times;
            </button>
        </div>

        <div class="report-modal-body">
            {{-- LEFT: Reports --}}
            <div class="report-modal-column">
                <div class="report-modal-section-title">
                    <span class="section-title-icon">
                        <svg class="icon-report" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M6 2h8l4 4v16H6z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            <path d="M14 2v4h4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span>Neuer Report</span>
                </div>

                <label class="report-modal-label" for="reportModalReportText">Report / Besuchsbericht</label>
                <textarea id="reportModalReportText"
                          class="report-modal-textarea"
                          placeholder="Report / Besuchsbericht …"></textarea>

                <div class="report-modal-form-row">
                    <div class="report-modal-field">
                        <label class="report-modal-label" for="reportModalReportDate">Berichtsdatum</label>
                        <input id="reportModalReportDate"
                               type="date"
                               class="report-modal-input"
                               placeholder="Datum">
                    </div>
                </div>

                <div class="report-modal-form-row report-modal-form-row--followup">
                    <div class="report-modal-field">
                        <label class="report-modal-label" for="reportModalNextStep">Nächster Schritt</label>
                        <input id="reportModalNextStep"
                               type="text"
                               class="report-modal-input"
                               placeholder="z.&nbsp;B. Angebot senden, Rückruf, Termin vereinbaren …">
                    </div>
                    <div class="report-modal-field">
                        <label class="report-modal-label" for="reportModalDueDate">Fällig bis</label>
                        <input id="reportModalDueDate"
                               type="date"
                               class="report-modal-input"
                               placeholder="Fällig bis">
                    </div>
                </div>

                <div class="report-modal-actions">
                    <button type="button" class="report-modal-btn" id="reportModalSaveReport">
                        <svg class="icon-report" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M6 2h8l4 4v16H6z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            <path d="M10 13h4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M12 11v4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <span>Report speichern</span>
                    </button>
                </div>

                <div class="report-modal-section-title report-modal-section-title--list">
                    <span class="section-title-icon">
                        <svg class="icon-report" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M6 2h8l4 4v16H6z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            <path d="M9 11h6M9 15h4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span>Vorhandene Reports</span>
                </div>

                <div id="reportModalReportList" class="report-modal-list">
                    {{-- per JS --}}
                </div>
            </div>

            {{-- RIGHT: Kommentare --}}
            <div class="report-modal-column">
                <div class="report-modal-section-title">
                    <span class="section-title-icon">
                        <svg class="icon-comment" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 5h16v10H8l-4 4z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            <path d="M9 10h6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span>Neuer Kommentar</span>
                </div>

                <label class="report-modal-label" for="reportModalCommentText">Kommentar</label>
                <textarea id="reportModalCommentText"
                          class="report-modal-textarea"
                          placeholder="Kommentar hinzufügen …"></textarea>

                <div class="report-modal-actions">
                    <button type="button" class="report-modal-btn" id="reportModalSaveComment">
                        <svg class="icon-comment" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 5h16v10H8l-4 4z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            <path d="M9 10h6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                        <span>Kommentar speichern</span>
                    </button>
                </div>

                <div class="report-modal-section-title report-modal-section-title--list">
                    <span class="section-title-icon">
                        <svg class="icon-comment" viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 5h16v10H8l-4 4z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            <path d="M9 10h6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span>Kommentare</span>
                </div>

                <div id="reportModalCommentList" class="report-modal-list">
                    {{-- per JS --}}
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
 
@section('script')
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        // Globale Variablen aus Blade ins JS
        window.CURRENT_EMPLOYEE_ID   = {{ (int) (auth()->user()->employee_id ?? 0) }};
        window.FAVORITE_EMPLOYEE_IDS = @json($favorite_employee_ids ?? []);

        // Basis-Pfad für Mitarbeiterbilder -> asset('images/employee') + '/'
        window.EMPLOYEE_IMAGE_BASE = "{{ asset('images/employee') }}/";

        // Routen
        window.ROUTE = Object.assign({}, window.ROUTE || {}, {
            appointmentsData: "{{ route('customer.appointments.data') }}",
            appointmentsStore: "{{ route('customer.appointments.store') }}",
            appointmentsBase: "{{ url('customer/appointments') }}",
            contactList: "{{ route('get.contact.list') }}",
            customerProfileBase: "{{ url('new_lead_profile') }}",

            // Base für Reports + Kommentare
            appointmentReportsBase: "{{ url('customer/appointments') }}",
            appointmentCommentsBase: "{{ url('customer/appointments') }}",

            // Ticker
            appointmentsNotifications: "{{ route('customer.appointments.notificationsTicker') }}",
        });
    </script>

    <script>
        (function () {
            "use strict";

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            // Drawer
            const drawer         = document.getElementById('appointmentDrawer');
            const drawerBackdrop = document.getElementById('appointmentDrawerBackdrop');
            const closeButtons   = document.querySelectorAll('.close_task_window');
            const btnCreate      = document.getElementById('btnCreateAppointment');

            // Filter / Views / Tabs
            const filterSearch   = document.getElementById('filterSearch');
            const filterFromDate = document.getElementById('filterFromDate');
            const filterToDate   = document.getElementById('filterToDate');
            const filterEmployee = document.getElementById('filterEmployee');
            const filterBranch   = document.getElementById('filterBranch');

            const kanbanView = document.getElementById('kanbanView');
            const listView   = document.getElementById('listView');
            const tableBody  = document.getElementById('appointmentsTableBody');

            const tabButtons  = document.querySelectorAll('.tab-button');
            const viewButtons = document.querySelectorAll('.view-switch');

            const analyticsAll      = document.getElementById('analytics-all');
            const analyticsMine     = document.getElementById('analytics-mine');
            const analyticsDelayed  = document.getElementById('analytics-delayed');
            const analyticsDue      = document.getElementById('analytics-due');
            const analyticsArchived = document.getElementById('analytics-archived');
            const analyticsJunk     = document.getElementById('analytics-junk');
            const analyticsDeleted  = document.getElementById('analytics-deleted');

            let currentView      = 'kanban';
            let currentTab       = 'all';
            let currentSort      = 'start_date';
            let currentDirection = 'asc';

            let appointmentsCache = [];

            // -------------------------------------------------
            // SVG helpers (report + comment)
            // -------------------------------------------------

            function svgReportIcon() {
                return `
                    <svg class="icon-report" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M6 2h8l4 4v16H6z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                        <path d="M14 2v4h4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                        <path d="M9 11h6M9 15h4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                `;
            }

            function svgCommentIcon() {
                return `
                    <svg class="icon-comment" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 5h16v10H8l-4 4z" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                        <path d="M9 10h6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                    </svg>
                `;
            }

            function reportPillInnerHtml(reportsCount, commentsCount) {
                return `
                    <span class="report-pill-count">
                        ${svgReportIcon()}
                        <span class="report-pill-count-text">
                            <strong>${reportsCount}</strong> Reports
                        </span>
                    </span>
                    <span class="report-pill-count">
                        ${svgCommentIcon()}
                        <span class="report-pill-count-text">
                            <strong>${commentsCount}</strong> Kommentare
                        </span>
                    </span>
                `;
            }

            // -------------------------------------------------
            // Generic helpers
            // -------------------------------------------------

            function escapeHtml(str) {
                return (str || '').replace(/[&<>"']/g, function (m) {
                    return ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    })[m];
                });
            }

            function priorityClass(p) {
                switch (p) {
                    case 'very high':
                    case 'urgent': return 'priority-badge--urgent';
                    case 'high':   return 'priority-badge--high';
                    case 'medium': return 'priority-badge--normal';
                    case 'normal': return 'priority-badge--low';
                    default:       return 'priority-badge--default';
                }
            }

            function statusClass(s) {
                switch (s) {
                    case 'planned':     return 'status-badge--planned';
                    case 'in_progress': return 'status-badge--in_progress';
                    case 'done':        return 'status-badge--done';
                    case 'archived':    return 'status-badge--archived';
                    case 'junk':        return 'status-badge--junk';
                    case 'deleted':     return 'status-badge--default';
                    default:            return 'status-badge--default';
                }
            }

            // Avatar-HTML, jetzt korrekt mit asset('images/employee/' + image)
            function employeeAvatarsHtml(employees) {
                if (!Array.isArray(employees) || !employees.length) return '';

                return employees.map(function (e) {
                    const rawImage = e.avatar_url || e.image_url || e.image || '';
                    let avatarUrl;

                    if (rawImage) {
                        // Wenn schon absolute URL oder führender Slash, so lassen
                        if (/^https?:\/\//i.test(rawImage) || rawImage.startsWith('/')) {
                            avatarUrl = rawImage;
                        } else {
                            // nur Dateiname -> an EMPLOYEE_IMAGE_BASE anhängen
                            avatarUrl = (window.EMPLOYEE_IMAGE_BASE || '') + rawImage;
                        }
                    } else {
                        avatarUrl = (window.EMPLOYEE_IMAGE_BASE || '') + 'default.png';
                    }

                    const fullName  = e.full_name || [e.name || '', e.lastname || ''].filter(Boolean).join(' ');
                    const safeName  = escapeHtml(fullName || 'Mitarbeiter');

                    return `
                        <span class="employee-avatar-wrapper" title="${safeName}">
                            <img src="${avatarUrl}" alt="${safeName}" class="employee-avatar">
                        </span>
                    `;
                }).join('');
            }

            // -------------------------------------------------
            // Report / comment count helpers
            // -------------------------------------------------

            function getReportCountFromAppointment(a) {
                if (!a) return 0;
                if (typeof a.reports_count === 'number')              return a.reports_count;
                if (typeof a.report_count === 'number')               return a.report_count;
                if (typeof a.reports === 'number')                    return a.reports;
                if (Array.isArray(a.reports))                         return a.reports.length;
                if (typeof a.appointment_reports_count === 'number')  return a.appointment_reports_count;
                return 0;
            }

            function getCommentCountFromAppointment(a) {
                if (!a) return 0;
                if (typeof a.comments_count === 'number')             return a.comments_count;
                if (typeof a.comment_count === 'number')              return a.comment_count;
                if (typeof a.comments === 'number')                   return a.comments;
                if (Array.isArray(a.comments))                        return a.comments.length;
                if (typeof a.appointment_comments_count === 'number') return a.appointment_comments_count;
                return 0;
            }

            // -------------------------------------------------
            // Sorting helpers
            // -------------------------------------------------

            function compareForSort(a, b, key) {
                const dir = (currentDirection === 'desc') ? -1 : 1;
                let va = '';
                let vb = '';

                switch (key) {
                    case 'name':
                        va = (a.name || '').toLowerCase();
                        vb = (b.name || '').toLowerCase();
                        break;
                    case 'customer_name':
                        va = (a.customer_name || '').toLowerCase();
                        vb = (b.customer_name || '').toLowerCase();
                        break;
                    case 'city':
                        va = (a.city || '').toLowerCase();
                        vb = (b.city || '').toLowerCase();
                        break;
                    case 'status':
                        va = (a.status || '').toLowerCase();
                        vb = (b.status || '').toLowerCase();
                        break;
                    case 'priority':
                        va = (a.priority || '').toLowerCase();
                        vb = (b.priority || '').toLowerCase();
                        break;
                    case 'employees':
                        va = (((a.employees || [])[0]?.full_name) || '').toLowerCase();
                        vb = (((b.employees || [])[0]?.full_name) || '').toLowerCase();
                        break;
                    case 'start_date':
                    case 'end_date':
                        va = a[key] || '';
                        vb = b[key] || '';
                        break;
                    default:
                        va = (a[key] ?? '').toString().toLowerCase();
                        vb = (b[key] ?? '').toString().toLowerCase();
                }

                if (va < vb) return -1 * dir;
                if (va > vb) return  1 * dir;
                return 0;
            }

            function getSortedAppointments() {
                const arr = appointmentsCache.slice();
                if (!currentSort) return arr;
                return arr.sort((a, b) => compareForSort(a, b, currentSort));
            }

            // -------------------------------------------------
            // Drawer
            // -------------------------------------------------

            function openDrawer() {
                if (!drawer) return;
                drawer.classList.add('is-open');
                document.body.style.overflow = 'hidden';
            }

            function closeDrawer() {
                if (!drawer) return;
                drawer.classList.remove('is-open');
                document.body.style.overflow = '';
            }

            closeButtons.forEach(btn => btn.addEventListener('click', closeDrawer));
            if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeDrawer);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && drawer && drawer.classList.contains('is-open')) {
                    closeDrawer();
                }
            });

            if (btnCreate) {
                btnCreate.addEventListener('click', function () {
                    resetFormForCreate();
                    openDrawer();
                });
            }

            function showError(message) {
                if (window.Swal) {
                    Swal.fire({ icon: 'error', title: 'Fehler', text: message || 'Es ist ein Fehler aufgetreten.' });
                } else {
                    alert(message || 'Es ist ein Fehler aufgetreten.');
                }
            }

            function showSuccess(message) {
                if (window.Swal) {
                    Swal.fire({ icon: 'success', title: 'Erfolgreich', text: message || 'Der Termin wurde gespeichert.' });
                } else {
                    alert(message || 'Der Termin wurde gespeichert.');
                }
            }

            function resetFormForCreate() {
                const form = document.getElementById('task-store-form');
                if (form) form.reset();

                const idField = document.getElementById('appointment_id');
                if (idField) idField.value = '';

                $('#contact_mode').val('new');
                $('.contact-name-block').removeClass('d-none');
                $('.contact-select-block').addClass('d-none');

                if ($.fn.select2) {
                    $('#customer_id').val(null).trigger('change');
                    $('#employee').val(null).trigger('change');
                    $('#branch_id').val(null).trigger('change');
                }

                $('#execution_type').val('external').trigger('change');
            }

            function openFormForEdit(appointment) {
                const form = document.getElementById('task-store-form');
                if (!form) return;

                form.reset();

                $('#appointment_id').val(appointment.id || '');

                $('#appointment_type').val(appointment.appointment_type || '');
                $('#start_date').val(appointment.start_date || '');
                $('#end_date').val(appointment.end_date || '');
                $('#start_time').val(appointment.start_time || '');
                $('#end_time').val(appointment.end_time || '');
                $('#total_time').val(appointment.total_time || '');
                $('#priority').val(appointment.priority || 'normal');

                if (appointment.color) {
                    $('#color').val(appointment.color);
                    $('#colorIcon').css('color', appointment.color);
                }

                $('#execution_type').val(appointment.execution_type || 'external').trigger('change');
                $('#link').val(appointment.link || '');
                $('#description').val(appointment.description || '');
                $('#phone').val(appointment.phone || '');
                $('#email').val(appointment.email || '');

                const addr = appointment.full_address
                    ? appointment.full_address
                    : [appointment.street, appointment.postcode, appointment.city].filter(Boolean).join(', ');

                $('#full_address').val(addr);
                $('#street-input').val(appointment.street || '');
                $('#postal_code-input').val(appointment.postcode || '');
                $('#city-input').val(appointment.city || '');
                $('#latitude-input').val(appointment.latitude || '');
                $('#longitude-input').val(appointment.longitude || '');

                if ($.fn.select2 && appointment.branch_id) {
                    $('#branch_id').val(String(appointment.branch_id)).trigger('change');
                } else {
                    $('#branch_id').val(appointment.branch_id || '');
                }

                if (appointment.branch_address_id) {
                    $('#branch_address_id').val(String(appointment.branch_address_id));
                }

                if ($.fn.select2 && Array.isArray(appointment.employees)) {
                    const ids = appointment.employees.map(e => e.id);
                    $('#employee').val(ids.map(String)).trigger('change');
                }

                if (appointment.customer_id) {
                    $('#contact_mode').val('select');
                    $('#selectContact').prop('checked', true);
                    $('.contact-name-block').addClass('d-none');
                    $('.contact-select-block').removeClass('d-none');

                    if ($.fn.select2) {
                        const optionText = appointment.customer_name
                            || ('Kontakt #' + appointment.customer_id);
                        const option = new Option(optionText, appointment.customer_id, true, true);
                        $('#customer_id').append(option).trigger('change');
                    } else {
                        $('#customer_id').val(String(appointment.customer_id));
                    }

                    $('#name').val(appointment.customer_name || '');
                } else {
                    $('#contact_mode').val('new');
                    $('#newContact').prop('checked', true);
                    $('.contact-name-block').removeClass('d-none');
                    $('.contact-select-block').addClass('d-none');

                    $('#name').val(appointment.name || '');
                    $('#customer_id').val(null).trigger('change');
                }

                openDrawer();
            }

            // -------------------------------------------------
            // Customer modal
            // -------------------------------------------------

            const customerModal          = document.getElementById('customerProfileModal');
            const customerModalFrame     = document.getElementById('customerProfileFrame');
            const customerModalTitle     = document.getElementById('customerModalTitle');
            const customerModalClose     = document.getElementById('customerModalClose');
            const customerModalBackdrop  = customerModal ? customerModal.querySelector('.customer-modal-backdrop') : null;

            function openCustomerModal(customerId, customerName) {
                if (!customerModal || !customerModalFrame) return;
                if (!customerId) return;

                const base = (window.ROUTE && window.ROUTE.customerProfileBase) ? window.ROUTE.customerProfileBase : '/new_lead_profile';
                const url  = base.replace(/\/$/, '') + '/' + encodeURIComponent(customerId);

                customerModalFrame.src = url;
                if (customerModalTitle) {
                    customerModalTitle.textContent = customerName ? ('Kunde: ' + customerName) : 'Kundenprofil';
                }
                customerModal.classList.add('is-open');
            }

            function closeCustomerModal() {
                if (!customerModal) return;
                customerModal.classList.remove('is-open');
                if (customerModalFrame) {
                    customerModalFrame.src = '';
                }
            }

            if (customerModalClose)    customerModalClose.addEventListener('click', closeCustomerModal);
            if (customerModalBackdrop) customerModalBackdrop.addEventListener('click', closeCustomerModal);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && customerModal && customerModal.classList.contains('is-open')) {
                    closeCustomerModal();
                }
            });

            // -------------------------------------------------
            // Report / Kommentar Modal
            // -------------------------------------------------

            const reportModal          = document.getElementById('appointmentReportModal');
            const reportModalBackdrop  = reportModal ? reportModal.querySelector('.report-modal-backdrop') : null;
            const reportModalClose     = document.getElementById('reportModalClose');
            const reportModalTitleText = document.getElementById('reportModalTitleText');

            const reportTextEl         = document.getElementById('reportModalReportText');
            const reportDateEl         = document.getElementById('reportModalReportDate');
            const reportDueDateEl      = document.getElementById('reportModalDueDate');
            const reportNextStepEl     = document.getElementById('reportModalNextStep');
            const reportSaveBtn        = document.getElementById('reportModalSaveReport');
            const reportListEl         = document.getElementById('reportModalReportList');

            const commentTextEl        = document.getElementById('reportModalCommentText');
            const commentSaveBtn       = document.getElementById('reportModalSaveComment');
            const commentListEl        = document.getElementById('reportModalCommentList');

            let currentReportAppointmentId = null;

            function openReportModal(appointmentId, title) {
                if (!reportModal) return;
                currentReportAppointmentId = appointmentId;

                if (reportModalTitleText) {
                    reportModalTitleText.textContent = title
                        ? ('Reports – ' + title)
                        : 'Reports & Kommentare';
                }

                if (reportTextEl)    reportTextEl.value = '';
                if (reportDateEl)    reportDateEl.value = '';
                if (reportDueDateEl) reportDueDateEl.value = '';
                if (reportNextStepEl) reportNextStepEl.value = '';
                if (commentTextEl)   commentTextEl.value = '';

                loadReportsAndComments(appointmentId);

                reportModal.classList.add('is-open');
                document.body.style.overflow = 'hidden';
            }

            function closeReportModal() {
                if (!reportModal) return;
                reportModal.classList.remove('is-open');
                document.body.style.overflow = '';
                currentReportAppointmentId = null;
            }

            if (reportModalClose)   reportModalClose.addEventListener('click', closeReportModal);
            if (reportModalBackdrop) reportModalBackdrop.addEventListener('click', closeReportModal);
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && reportModal && reportModal.classList.contains('is-open')) {
                    closeReportModal();
                }
            });

            function normalizeReportsResponse(json) {
                let reports  = [];
                let comments = [];

                if (!json) {
                    return { reports, comments };
                }

                if (Array.isArray(json)) {
                    reports = json;
                    return { reports, comments };
                }

                if (Array.isArray(json.reports)) {
                    reports = json.reports;
                } else if (Array.isArray(json.data)) {
                    reports = json.data;
                } else if (Array.isArray(json.items)) {
                    reports = json.items;
                }

                if (Array.isArray(json.comments)) {
                    comments = json.comments;
                } else if (Array.isArray(json.appointment_comments)) {
                    comments = json.appointment_comments;
                }

                return { reports, comments };
            }

            async function loadReportsAndComments(appointmentId) {
                if (!window.ROUTE || !window.ROUTE.appointmentReportsBase) return;
                const base = window.ROUTE.appointmentReportsBase.replace(/\/$/, '');
                const url  = base + '/' + encodeURIComponent(appointmentId) + '/reports';

                try {
                    const resp = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!resp.ok) return;

                    const json = await resp.json();
                    const normalized = normalizeReportsResponse(json);
                    const reports  = normalized.reports;
                    const comments = normalized.comments;

                    renderReportModalReports(reports);
                    renderReportModalComments(comments);
                    updateReportBadges(appointmentId, reports.length, comments.length);
                } catch (e) {
                    console.error(e);
                }
            }

           function getReportAuthor(r) {
            if (!r) return 'Mitarbeiter';

            if (r.employee) {
                const emp = r.employee;
                if (emp.full_name) return emp.full_name;
                const n = [emp.name || '', emp.lastname || ''].filter(Boolean).join(' ');
                if (n) return n;
            }

            if (r.employee_name)   return r.employee_name;
            if (r.author_name)     return r.author_name;
            if (r.created_by_name) return r.created_by_name;
            if (r.created_by)      return r.created_by;

            return 'Mitarbeiter';
        }

        function getReportCreatedAt(r) {
            if (!r) return '';
            return (
                r.created_at_human ||
                r.created_at ||
                ''
            );
        }

        function getReportNextStep(r) {
            return r?.next_step || '';
        }

        function getReportDueDate(r) {
            return r?.due_date || '';
        }

        function renderReportModalReports(reports) {
            if (!reportListEl) return;
            reportListEl.innerHTML = '';

            reports.forEach(r => {
                const div = document.createElement('div');
                div.className = 'report-modal-item';

                const author   = getReportAuthor(r);
                const created  = getReportCreatedAt(r);
                const reportHtml = r.report_html || r.report || '';

                const nextStep = getReportNextStep(r);
                const dueDate  = getReportDueDate(r);

                div.innerHTML = `
                    <div class="report-modal-item-header">
                        <span class="report-modal-item-author">${escapeHtml(author)}</span>
                        <span class="report-modal-item-time">${escapeHtml(created)}</span>
                    </div>

                    <div class="report-modal-item-body">
                        ${reportHtml}
                    </div>

                    <div class="report-modal-item-followup">
                        <div class="report-modal-item-followup-title">
                            <svg class="icon-followup" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M5 12h14" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>Follow-up</span>
                        </div>
                        <div class="report-modal-item-followup-row">
                            <div class="followup-col">
                                <span class="followup-label">Nächster Schritt</span>
                                <span class="followup-value">${escapeHtml(nextStep || '–')}</span>
                            </div>
                            <div class="followup-col">
                                <span class="followup-label">Fällig bis</span>
                                <span class="followup-value">${escapeHtml(dueDate || '–')}</span>
                            </div>
                        </div>
                    </div>
                `;

                reportListEl.appendChild(div);
            });
        }

            function getReportCreatedAt(r) {
                if (!r) return '';
                return (
                    r.created_at_human ||
                    r.created_at_formatted ||
                    r.created_diff ||
                    r.created_at ||
                    ''
                );
            }

            function getReportNextStep(r) {
                if (!r) return '';
                return (
                    r.next_step ||
                    r.next_action ||
                    r.follow_up ||
                    r.followup ||
                    r.next ||
                    ''
                );
            }

            function getReportDueDate(r) {
                if (!r) return '';
                return (
                    r.due_date ||
                    r.next_step_due ||
                    r.next_due ||
                    r.due_to ||
                    ''
                );
            }

           function renderReportModalReports(reports) {
                if (!reportListEl) return;
                reportListEl.innerHTML = '';

                reports.forEach(r => {
                    const div = document.createElement('div');
                    div.className = 'report-modal-item';

                    const author   = getReportAuthor(r);
                    const created  = getReportCreatedAt(r);

                    // Text des Reports
                    const reportHtml = r.report_html || r.report || '';

                    // Next-Step / Fällig-bis (immer anzeigen, mit „–“ wenn leer)
                    const nextStep = getReportNextStep(r);
                    const dueDate  = getReportDueDate(r);

                    div.innerHTML = `
                        <div class="report-modal-item-header">
                            <span class="report-modal-item-author">${escapeHtml(author)}</span>
                            <span class="report-modal-item-time">${escapeHtml(created)}</span>
                        </div>

                        <div class="report-modal-item-body">
                            ${reportHtml}
                        </div>

                        <div class="report-modal-item-followup">
                            <div class="report-modal-item-followup-title">
                                <svg class="icon-followup" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M5 12h14" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                                    <path d="M13 6l6 6-6 6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Follow-up</span>
                            </div>
                            <div class="report-modal-item-followup-row">
                                <div class="followup-col">
                                    <span class="followup-label">Nächster Schritt</span>
                                    <span class="followup-value">${escapeHtml(nextStep || '–')}</span>
                                </div>
                                <div class="followup-col">
                                    <span class="followup-label">Fällig bis</span>
                                    <span class="followup-value">${escapeHtml(dueDate || '–')}</span>
                                </div>
                            </div>
                        </div>
                    `;

                    reportListEl.appendChild(div);
                });
            }

           function renderReportModalComments(comments) {
                if (!commentListEl) return;
                commentListEl.innerHTML = '';

                comments.forEach(c => {
                    const div = document.createElement('div');
                    div.className = 'report-modal-comment-item';

                    const author = getReportAuthor(c); // nutzt dieselbe Helper-Logik
                    const created = getReportCreatedAt(c);
                    const text = c.comment || c.text || c.body || '';

                    div.innerHTML = `
                        <div class="report-modal-comment-header">
                            <span class="report-modal-comment-author">${escapeHtml(author)}</span>
                            <span class="report-modal-comment-time">${escapeHtml(created)}</span>
                        </div>
                        <div class="report-modal-comment-body">
                            ${escapeHtml(text)}
                        </div>
                    `;
                    commentListEl.appendChild(div);
                });
            }

            function updateReportBadges(appointmentId, reportCount, commentCount) {
                const selector = '.report-pill.js-open-reports[data-id="' + appointmentId + '"]';
                document.querySelectorAll(selector).forEach(btn => {
                    const countsEl = btn.querySelector('.report-pill-counts');
                    if (countsEl) {
                        countsEl.innerHTML = reportPillInnerHtml(reportCount, commentCount);
                    }
                });

                const item = appointmentsCache.find(a => String(a.id) === String(appointmentId));
                if (item) {
                    item.reports_count  = reportCount;
                    item.comments_count = commentCount;
                }
            }

            if (reportSaveBtn) {
                reportSaveBtn.addEventListener('click', async function () {
                    if (!currentReportAppointmentId) return;
                    const text    = (reportTextEl?.value || '').trim();
                    const rDate   = reportDateEl?.value || '';
                    const dueDate = reportDueDateEl?.value || '';
                    const next    = (reportNextStepEl?.value || '').trim();

                    if (!text) return;

                    const base = window.ROUTE.appointmentReportsBase.replace(/\/$/, '');
                    const url  = base + '/' + encodeURIComponent(currentReportAppointmentId) + '/reports';

                    try {
                        const fd = new FormData();
                        fd.append('report', text);
                        if (rDate)   fd.append('report_date', rDate);
                        if (dueDate) fd.append('due_date', dueDate);
                        if (next)    fd.append('next_step', next);

                        const resp = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: fd
                        });

                        if (!resp.ok) {
                            showError('Report konnte nicht gespeichert werden.');
                            return;
                        }

                        if (reportTextEl)    reportTextEl.value = '';
                        if (reportDateEl)    reportDateEl.value = '';
                        if (reportDueDateEl) reportDueDateEl.value = '';
                        if (reportNextStepEl) reportNextStepEl.value = '';

                        await loadReportsAndComments(currentReportAppointmentId);
                        showSuccess('Report gespeichert.');
                    } catch (e) {
                        console.error(e);
                        showError('Fehler beim Speichern des Reports.');
                    }
                });
            }

            if (commentSaveBtn) {
                commentSaveBtn.addEventListener('click', async function () {
                    if (!currentReportAppointmentId) return;
                    const text = (commentTextEl?.value || '').trim();
                    if (!text) return;

                    const base = window.ROUTE.appointmentCommentsBase.replace(/\/$/, '');
                    const url  = base + '/' + encodeURIComponent(currentReportAppointmentId) + '/comments';

                    try {
                        const fd = new FormData();
                        fd.append('comment', text);

                        const resp = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: fd
                        });

                        if (!resp.ok) {
                            showError('Kommentar konnte nicht gespeichert werden.');
                            return;
                        }

                        if (commentTextEl) commentTextEl.value = '';

                        await loadReportsAndComments(currentReportAppointmentId);
                        showSuccess('Kommentar gespeichert.');
                    } catch (e) {
                        console.error(e);
                        showError('Fehler beim Speichern des Kommentars.');
                    }
                });
            }

            // -------------------------------------------------
            // Save appointment
            // -------------------------------------------------

            const saveBtn = document.querySelector('.save-task');
            if (saveBtn) {
                saveBtn.addEventListener('click', async function () {
                    const form = document.getElementById('task-store-form');
                    if (!form) return;

                    const formData = new FormData(form);
                    const id = formData.get('id');

                    let url;
                    let method = 'POST';

                    if (id) {
                        url = window.ROUTE.appointmentsBase + '/' + id;
                        formData.append('_method', 'PUT');
                    } else {
                        url = window.ROUTE.appointmentsStore;
                    }

                    try {
                        const resp = await fetch(url, {
                            method,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: formData
                        });

                        const contentType = resp.headers.get('content-type') || '';
                        let data = null;
                        if (contentType.indexOf('application/json') !== -1) {
                            data = await resp.json().catch(() => null);
                        }

                        if (!resp.ok) {
                            let message = 'Fehler beim Speichern des Termins.';
                            if (resp.status === 422 && data && data.errors) {
                                const messages = [];
                                Object.values(data.errors).forEach(arr => {
                                    arr.forEach(msg => messages.push(msg));
                                });
                                if (messages.length) message = messages.join('\n');
                            } else if (data && data.message) {
                                message = data.message;
                            }
                            showError(message);
                            return;
                        }

                        showSuccess('Der Termin wurde gespeichert.');
                        closeDrawer();
                        await loadAppointments();
                    } catch (e) {
                        console.error(e);
                        showError('Es ist ein Fehler beim Speichern aufgetreten.');
                    }
                });
            }

            // -------------------------------------------------
            // AJAX Load appointments
            // -------------------------------------------------

            function buildQuery() {
                const params = new URLSearchParams();
                params.set('view', currentView);
                params.set('tab', currentTab);
                params.set('sort', currentSort);
                params.set('direction', currentDirection);

                if (filterSearch && filterSearch.value.trim())   params.set('search', filterSearch.value.trim());
                if (filterFromDate && filterFromDate.value)      params.set('from_date', filterFromDate.value);
                if (filterToDate && filterToDate.value)          params.set('to_date', filterToDate.value);
                if (filterEmployee && filterEmployee.value)      params.set('employee_id', filterEmployee.value);
                if (filterBranch && filterBranch.value)          params.set('branch_id', filterBranch.value);

                return params.toString();
            }

            async function loadAppointments() {
                if (!window.ROUTE || !window.ROUTE.appointmentsData) return;

                const query = buildQuery();
                const url   = window.ROUTE.appointmentsData + '?' + query;

                try {
                    const response = await fetch(url, { headers: { 'Accept': 'application/json' } });

                    if (!response.ok) {
                        showError('Termine konnten nicht geladen werden.');
                        return;
                    }

                    const json = await response.json();
                    appointmentsCache = json.data || json || [];

                    renderKanban();
                    renderList();
                    updateColumnCounts();
                    updateAnalytics();
                    updateHeaderSortState();
                } catch (e) {
                    console.error(e);
                    showError('Fehler beim Laden der Termine.');
                }
            }

            // -------------------------------------------------
            // Kanban + List rendering
            // -------------------------------------------------

            function renderKanban() {
                document.querySelectorAll('.kanban-droppable').forEach(col => {
                    col.innerHTML = '';
                });

                const list = getSortedAppointments();

                list.forEach(a => {
                    const status = a.status || 'planned';
                    const col = document.querySelector('.kanban-droppable[data-status="' + status + '"]');
                    if (!col) return;

                    const card = document.createElement('div');
                    card.className = 'kanban-card';
                    card.dataset.id = a.id;

                    const customerName = a.customer_name || '';
                    const customerId   = a.customer_id || null;

                    const customerHtml = customerName && customerId
                        ? `<button type="button"
                                class="kanban-card-customer js-open-customer"
                                data-customer-id="${customerId}">
                                ${escapeHtml(customerName)}
                           </button>`
                        : (customerName ? escapeHtml(customerName) : '');

                    const reportsCount  = getReportCountFromAppointment(a);
                    const commentsCount = getCommentCountFromAppointment(a);

                    card.innerHTML = `
                        <div class="kanban-card-header">
                            <div>
                                <div class="kanban-card-title">
                                    ${escapeHtml(a.name || a.appointment_type || '')}
                                </div>
                                <div class="kanban-card-subtitle">
                                    ${customerHtml}
                                </div>
                            </div>
                            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.2rem;">
                                ${a.priority ? `<span class="priority-badge ${priorityClass(a.priority)}">${escapeHtml(a.priority)}</span>` : ''}
                                <button class="btn-link-sm js-appointment-profile" data-id="${a.id}">Profil</button>
                                <button class="btn-link-sm js-appointment-edit" data-id="${a.id}">Bearbeiten</button>
                            </div>
                        </div>
                        <div class="kanban-card-meta">
                            <span>${a.start_date || ''}${a.end_date ? ' → ' + a.end_date : ''}</span>
                            <span>${a.city || ''}</span>
                        </div>
                        <div class="kanban-card-footer">
                            ${employeeAvatarsHtml(a.employees)}
                            <button type="button"
                                    class="report-pill js-open-reports"
                                    data-id="${a.id}"
                                    data-title="${escapeHtml(a.name || a.appointment_type || '')}">
                                <span class="report-pill-counts">
                                    ${reportPillInnerHtml(reportsCount, commentsCount)}
                                </span>
                            </button>
                        </div>
                    `;

                    col.appendChild(card);
                });

                setupKanbanDragAndDrop();
            }

            function renderList() {
                if (!tableBody) return;
                tableBody.innerHTML = '';

                const list = getSortedAppointments();

                list.forEach(a => {
                    const tr = document.createElement('tr');

                    const customerName = a.customer_name || '';
                    const customerId   = a.customer_id || null;

                    const customerHtml = customerName && customerId
                        ? `<button type="button"
                                class="kanban-card-customer js-open-customer"
                                data-customer-id="${customerId}">
                                ${escapeHtml(customerName)}
                           </button>`
                        : escapeHtml(customerName);

                    const reportsCount  = getReportCountFromAppointment(a);
                    const commentsCount = getCommentCountFromAppointment(a);

                    tr.innerHTML = `
                        <td>
                            <div class="appointments-table-title">
                                ${escapeHtml(a.name || a.appointment_type || '')}
                            </div>
                            <div class="appointments-table-subtitle">
                                ${customerHtml}
                            </div>
                            <div style="margin-top:0.25rem;">
                                <button type="button"
                                        class="report-pill js-open-reports"
                                        data-id="${a.id}"
                                        data-title="${escapeHtml(a.name || a.appointment_type || '')}">
                                    <span class="report-pill-counts">
                                        ${reportPillInnerHtml(reportsCount, commentsCount)}
                                    </span>
                                </button>
                            </div>
                        </td>
                        <td>
                            ${a.start_date || ''}${a.end_date ? ' → ' + a.end_date : ''}
                        </td>
                        <td>
                            ${customerHtml}
                        </td>
                        <td>
                            ${[a.street, a.postcode, a.city].filter(Boolean).join(', ')}
                        </td>
                        <td>
                            ${employeeAvatarsHtml(a.employees)}
                        </td>
                        <td>
                            <span class="status-badge ${statusClass(a.status)}">
                                ${escapeHtml(a.status || '')}
                            </span>
                        </td>
                        <td>
                            ${escapeHtml(a.priority || '')}
                        </td>
                        <td class="table-actions">
                            <button class="btn-table btn-table--edit js-appointment-profile" data-id="${a.id}">
                                Profil
                            </button>
                            <button class="btn-table btn-table--edit js-appointment-edit" data-id="${a.id}">
                                Bearbeiten
                            </button>
                        </td>
                    `;

                    tableBody.appendChild(tr);
                });
            }

            // -------------------------------------------------
            // Kanban Drag & Drop
            // -------------------------------------------------

            function getDragAfterElement(container, y) {
                const draggableElements = [...container.querySelectorAll('.kanban-card:not(.is-dragging)')];

                return draggableElements.reduce((closest, child) => {
                    const box = child.getBoundingClientRect();
                    const offset = y - box.top - box.height / 2;
                    if (offset < 0 && offset > closest.offset) {
                        return { offset: offset, element: child };
                    } else {
                        return closest;
                    }
                }, { offset: Number.NEGATIVE_INFINITY, element: null }).element;
            }

            async function updateAppointmentStatus(id, status) {
                try {
                    const url = window.ROUTE.appointmentsBase + '/' + id + '/status';
                    const resp = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ status: status })
                    });

                    if (!resp.ok) {
                        showError('Status konnte nicht aktualisiert werden.');
                        return;
                    }

                    const item = appointmentsCache.find(a => String(a.id) === String(id));
                    if (item) {
                        item.status = status;
                    }
                    updateColumnCounts();
                    updateAnalytics();
                } catch (e) {
                    console.error(e);
                    showError('Fehler beim Aktualisieren des Status.');
                }
            }

            function setupKanbanDragAndDrop() {
                const cards   = document.querySelectorAll('.kanban-card');
                const columns = document.querySelectorAll('.kanban-droppable');
                let draggedId = null;

                cards.forEach(card => {
                    card.setAttribute('draggable', 'true');

                    card.addEventListener('dragstart', (e) => {
                        draggedId = card.dataset.id;
                        card.classList.add('is-dragging');
                        if (e.dataTransfer) {
                            e.dataTransfer.effectAllowed = 'move';
                            e.dataTransfer.setData('text/plain', draggedId);
                        }
                    });

                    card.addEventListener('dragend', () => {
                        card.classList.remove('is-dragging');
                        draggedId = null;
                    });
                });

                columns.forEach(col => {
                    col.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        const afterElement = getDragAfterElement(col, e.clientY);
                        const dragging = document.querySelector('.kanban-card.is-dragging');
                        if (!dragging) return;

                        if (afterElement == null) {
                            col.appendChild(dragging);
                        } else {
                            col.insertBefore(dragging, afterElement);
                        }
                    });

                    col.addEventListener('drop', async (e) => {
                        e.preventDefault();
                        const newStatus = col.dataset.status;
                        const id = draggedId;
                        draggedId = null;
                        const dragging = document.querySelector('.kanban-card.is-dragging');
                        if (dragging) dragging.classList.remove('is-dragging');
                        if (!id || !newStatus) return;

                        await updateAppointmentStatus(id, newStatus);
                    });
                });
            }

            // -------------------------------------------------
            // Analytics + counts
            // -------------------------------------------------

            function updateColumnCounts() {
                const counts = {};
                appointmentsCache.forEach(a => {
                    const status = a.status || 'planned';
                    counts[status] = (counts[status] || 0) + 1;
                });

                document.querySelectorAll('[data-count]').forEach(el => {
                    const status = el.getAttribute('data-count');
                    el.textContent = counts[status] || 0;
                });
            }

            function updateAnalytics() {
                const today = new Date();
                const todayStr = today.toISOString().slice(0, 10);

                let all = 0, mine = 0, delayed = 0, due = 0, archived = 0, junk = 0, deleted = 0;

                appointmentsCache.forEach(a => {
                    all++;
                    const status = a.status || '';
                    const start = a.start_date || null;
                    const end   = a.end_date || a.start_date || null;

                    if ((a.employees || []).some(e => String(e.id) === String(window.CURRENT_EMPLOYEE_ID))) {
                        mine++;
                    }

                    if (status === 'archived') archived++;
                    if (status === 'junk')     junk++;
                    if (status === 'deleted')  deleted++;

                    if (start && !['done', 'archived', 'canceled', 'junk'].includes(status)) {
                        if (start <= todayStr && end && end < todayStr) {
                            delayed++;
                        }
                        if (start <= todayStr && (!end || end >= todayStr)) {
                            if (start === todayStr || (!end || end === todayStr)) {
                                due++;
                            }
                        }
                    }
                });

                analyticsAll.textContent      = all;
                analyticsMine.textContent     = mine;
                analyticsDelayed.textContent  = delayed;
                analyticsDue.textContent      = due;
                analyticsArchived.textContent = archived;
                analyticsJunk.textContent     = junk;
                analyticsDeleted.textContent  = deleted;
            }

            // -------------------------------------------------
            // List header sorting
            // -------------------------------------------------

            const sortableHeaders = document.querySelectorAll('.appointments-table th.sortable');

            function updateHeaderSortState() {
                sortableHeaders.forEach(th => {
                    th.classList.remove('is-sorted-asc', 'is-sorted-desc');
                    const key = th.dataset.sort;
                    if (!key) return;
                    if (key === currentSort) {
                        th.classList.add(currentDirection === 'asc' ? 'is-sorted-asc' : 'is-sorted-desc');
                    }
                });
            }

            sortableHeaders.forEach(th => {
                th.addEventListener('click', () => {
                    const key = th.dataset.sort;
                    if (!key) return;

                    if (currentSort === key) {
                        currentDirection = (currentDirection === 'asc') ? 'desc' : 'asc';
                    } else {
                        currentSort = key;
                        currentDirection = 'asc';
                    }

                    updateHeaderSortState();
                    renderList();
                    renderKanban();
                });
            });

            // -------------------------------------------------
            // Tabs / view / filters
            // -------------------------------------------------

            tabButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    tabButtons.forEach(b => b.classList.remove('tab-button--active'));
                    btn.classList.add('tab-button--active');

                    currentTab = btn.dataset.tab || 'all';
                    loadAppointments();
                });
            });

            viewButtons.forEach(btn => {
                btn.addEventListener('click', () => {
                    viewButtons.forEach(b => b.classList.remove('view-switch--active'));
                    btn.classList.add('view-switch--active');

                    currentView = btn.dataset.view || 'kanban';

                    if (currentView === 'kanban') {
                        kanbanView.classList.remove('is-hidden');
                        listView.classList.add('is-hidden');
                    } else {
                        kanbanView.classList.add('is-hidden');
                        listView.classList.remove('is-hidden');
                    }
                });
            });

            [filterSearch, filterFromDate, filterToDate, filterEmployee, filterBranch].forEach(el => {
                if (!el) return;
                if (el === filterSearch) {
                    el.addEventListener('keyup', () => loadAppointments());
                } else {
                    el.addEventListener('change', () => loadAppointments());
                }
            });

            // -------------------------------------------------
            // Delegated clicks (customer, reports, edit/profile)
            // -------------------------------------------------

            document.addEventListener('click', function (e) {
                const customerBtn = e.target.closest('.js-open-customer');
                if (customerBtn) {
                    const customerId   = customerBtn.dataset.customerId;
                    const customerName = customerBtn.textContent.trim();
                    openCustomerModal(customerId, customerName);
                    return;
                }

                const reportBtn = e.target.closest('.js-open-reports');
                if (reportBtn) {
                    const id    = reportBtn.dataset.id;
                    const title = reportBtn.dataset.title || '';
                    if (id) {
                        openReportModal(id, title);
                    }
                    return;
                }

                const profileBtn = e.target.closest('.js-appointment-profile');
                if (profileBtn) {
                    const id = profileBtn.dataset.id;
                    if (id && window.ROUTE && window.ROUTE.appointmentsBase) {
                        const base = window.ROUTE.appointmentsBase.replace(/\/$/, '');
                        const url  = base + '/' + encodeURIComponent(id);
                        window.location.href = url;
                    }
                    return;
                }

                const editBtn = e.target.closest('.js-appointment-edit');
                if (!editBtn) return;

                const id = editBtn.dataset.id;
                if (!id) return;

                const appointment = appointmentsCache.find(a => String(a.id) === String(id));
                if (!appointment) {
                    showError('Termin konnte nicht geladen werden.');
                    return;
                }
                openFormForEdit(appointment);
            });

            // -------------------------------------------------
            // Notification ticker
            // -------------------------------------------------

            const notifBar          = document.getElementById('appointmentNotificationBar');
            const notifTitleEl      = document.getElementById('notifTitle');
            const notifMessageEl    = document.getElementById('notifMessage');
            const notifMetaEl       = document.getElementById('notifMeta');
            const notifKindBadgeEl  = document.getElementById('notifKindBadge');
            const notifPlayPauseEl  = document.getElementById('notifPlayPauseIcon');

            const notifState = {
                items: [],
                index: 0,
                timer: null,
                playing: true,
                interval: 8000
            };

            async function loadAppointmentNotifications() {
                if (!window.ROUTE || !window.ROUTE.appointmentsNotifications) return;

                try {
                    const resp = await fetch(window.ROUTE.appointmentsNotifications + '?limit=10', {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!resp.ok) return;

                    const json = await resp.json();
                    notifState.items = json.data || json || [];
                    if (!Array.isArray(notifState.items)) notifState.items = [];
                    notifState.index = 0;
                    renderNotificationBar();
                    setupNotificationAuto();
                } catch (e) {
                    console.error(e);
                }
            }

            function renderNotificationBar() {
                if (!notifBar) return;

                if (!notifState.items.length) {
                    notifBar.classList.add('is-hidden');
                    clearInterval(notifState.timer);
                    return;
                }

                const item = notifState.items[notifState.index];

                notifBar.classList.remove('is-hidden');

                const title   = item.title || 'Termin';
                const message = item.message || '';
                const meta    = item.created_at || '';
                const kind    = item.kind || 'generic';

                notifTitleEl.textContent   = title;
                notifMessageEl.textContent = message;
                notifMetaEl.textContent    = meta;

                notifKindBadgeEl.textContent = (kind === 'due')
                    ? 'Heute fällig'
                    : (kind === 'status' ? 'Status' : 'Aufgabe');

                notifKindBadgeEl.classList.toggle('app-notification-type-badge--due', kind === 'due');

                notifBar.classList.add('is-fade-in');
                setTimeout(() => notifBar.classList.remove('is-fade-in'), 250);

                if (item.appointment_id && window.ROUTE && window.ROUTE.appointmentsBase) {
                    notifBar.onclick = function (e) {
                        if (e.target.closest('.app-notification-control')) return;
                        const url = window.ROUTE.appointmentsBase.replace(/\/$/, '') + '#' + item.appointment_id;
                        window.location.href = url;
                    };
                } else {
                    notifBar.onclick = null;
                }
            }

            function notifNext() {
                if (!notifState.items.length) return;
                notifState.index = (notifState.index + 1) % notifState.items.length;
                renderNotificationBar();
            }

            function notifPrev() {
                if (!notifState.items.length) return;
                notifState.index = (notifState.index - 1 + notifState.items.length) % notifState.items.length;
                renderNotificationBar();
            }

            function setupNotificationAuto() {
                clearInterval(notifState.timer);
                if (!notifState.playing || !notifState.items.length) return;
                notifState.timer = setInterval(notifNext, notifState.interval);
            }

            document.querySelectorAll('[data-notif-action]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const action = btn.getAttribute('data-notif-action');
                    if (action === 'prev') {
                        notifPrev();
                    } else if (action === 'next') {
                        notifNext();
                    } else if (action === 'toggle') {
                        notifState.playing = !notifState.playing;
                        notifPlayPauseEl.textContent = notifState.playing ? '❚❚' : '▶';
                        setupNotificationAuto();
                    }
                    e.stopPropagation();
                });
            });

            // -------------------------------------------------
            // Select2 + init
            // -------------------------------------------------

            $(function () {
                if (!$.fn.select2) {
                    console.error('Select2 not found – prüfe, ob js/select2.min.js nach jQuery geladen wird.');
                } else {
                    $('#employee').select2({
                        placeholder: 'Teilnehmer auswählen',
                        width: '100%'
                    });
                    if (window.FAVORITE_EMPLOYEE_IDS && window.FAVORITE_EMPLOYEE_IDS.length) {
                        $('#employee').val(window.FAVORITE_EMPLOYEE_IDS.map(String)).trigger('change');
                    }

                    $('#branch_id').select2({
                        placeholder: 'Betrieb auswählen',
                        width: '100%'
                    });

                    $('#customer_id').select2({
                        placeholder: 'Kontakt auswählen',
                        allowClear: true,
                        width: '100%',
                        ajax: {
                            url: window.ROUTE.contactList,
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                return { search: params.term || '' };
                            },
                            processResults: function (data) {
                                var rows = [];
                                if (data && Array.isArray(data.data)) {
                                    rows = data.data;
                                } else if (Array.isArray(data)) {
                                    rows = data;
                                }
                                return {
                                    results: rows.map(function (item) {
                                        var id = (item.main_id != null) ? item.main_id : item.id;
                                        var text = ((item.name || '') + ' ' + (item.lastname || '')).trim();
                                        if (!text) text = 'Kontakt #' + id;
                                        if (item.type) text += ' – ' + item.type;

                                        return {
                                            id: id,
                                            text: text,
                                            raw: item
                                        };
                                    })
                                };
                            },
                            cache: true
                        }
                    }).on('select2:select', function (e) {
                        const selected = e.params.data || {};
                        const raw      = selected.raw || selected;

                        $('#contact_type').val(raw.type || '');
                        $('#name').val([raw.name, raw.lastname].filter(Boolean).join(' '));

                        if (raw.phone) $('#phone').val(raw.phone);
                        if (raw.email) $('#email').val(raw.email);

                        if (raw.street || raw.city || raw.postcode) {
                            const addr = [raw.street, raw.postcode, raw.city].filter(Boolean).join(', ');
                            $('#full_address').val(addr);

                            $('#street-input').val(raw.street || '');
                            $('#city-input').val(raw.city || '');
                            $('#postal_code-input').val(raw.postcode || '');
                            $('#latitude-input').val(raw.latitude || '');
                            $('#longitude-input').val(raw.longitude || '');
                        }
                    });
                }

                $('.contact-type-toggle').on('change', function () {
                    const mode = $(this).val();
                    $('#contact_mode').val(mode);

                    if (mode === 'new') {
                        $('.contact-name-block').removeClass('d-none');
                        $('.contact-select-block').addClass('d-none');
                    } else {
                        $('.contact-name-block').addClass('d-none');
                        $('.contact-select-block').removeClass('d-none');
                    }
                });

                $('#execution_type').on('change', function () {
                    const v = $(this).val();
                    if (v === 'internal') {
                        $('#intern').show();
                        $('#extern').hide();
                    } else {
                        $('#intern').hide();
                        $('#extern').show();
                    }
                    if (v === 'online') {
                        $('#link_section').show();
                    } else {
                        $('#link_section').hide();
                    }
                }).trigger('change');

                $('#color_drop_down .dropdown-item').on('click', function () {
                    const val = $(this).data('value');
                    $('#color').val(val);
                    $('#colorIcon').css('color', val);
                });

                loadAppointments();
                loadAppointmentNotifications();
            });

            window.openAppointmentForEdit = openFormForEdit;
        })();
    </script>

    {{-- Google Places für full_address --}}
    <script>
        function initAddressAutocomplete() {
            var input = document.getElementById('full_address');
            if (!input || !window.google || !google.maps || !google.maps.places) {
                return;
            }

            var autocomplete = new google.maps.places.Autocomplete(input, {
                types: ['geocode'],
                componentRestrictions: { country: 'de' }
            });

            autocomplete.addListener('place_changed', function () {
                var place = autocomplete.getPlace();
                if (!place || !place.address_components) return;

                var street = '', streetNumber = '', city = '', postcode = '';

                place.address_components.forEach(function (component) {
                    var types = component.types || [];
                    if (types.indexOf('route') > -1)         street       = component.long_name;
                    if (types.indexOf('street_number') > -1) streetNumber = component.long_name;
                    if (types.indexOf('locality') > -1)      city         = component.long_name;
                    if (types.indexOf('postal_code') > -1)   postcode     = component.long_name;
                });

                var streetFull = [street, streetNumber].filter(Boolean).join(' ');
                var full       = [streetFull, postcode, city].filter(Boolean).join(', ');

                $('#full_address').val(full);
                $('#street-input').val(streetFull);
                $('#city-input').val(city);
                $('#postal_code-input').val(postcode);

                if (place.geometry && place.geometry.location) {
                    $('#latitude-input').val(place.geometry.location.lat());
                    $('#longitude-input').val(place.geometry.location.lng());
                }
            });
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places&language=de&callback=initAddressAutocomplete" async defer></script>
@endsection
