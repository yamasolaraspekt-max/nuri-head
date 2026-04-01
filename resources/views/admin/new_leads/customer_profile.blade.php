@extends('admin.layouts.app')
@section('title')
KUNDE PROFILE
@endsection

@section('style')
 
<link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
<meta name="csrf-token" content="{{ csrf_token() }}">
 <!-- In your main Blade layout (e.g. admin.layouts.app or similar) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css">
<link href="https://unpkg.com/cropperjs/dist/cropper.min.css" rel="stylesheet">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

<style>
    #mainContent {
        /* ensures the main section can shrink and scroll if it is a flex item */
        min-height: 0;
    }
    .text-muted {
        color: #6b6b6b !important;
    }

    #neighbor-wrapper {
        height: 100%;
    }

    .neighbor-scroll {
        /* adjust 140px to your header/top-nav height */
        max-height: calc(100vh - 140px);
        overflow-y: auto;
        padding-right: 4px;
    }

    .neighbor-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .neighbor-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .neighbor-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    #neighbor-list .neighbor-item {
        transition: background-color .18s ease, transform .12s ease, box-shadow .18s ease;
        border-radius: 8px;
    }

    #neighbor-list .neighbor-item:hover {
        background: #f9fdf3;
        transform: translateY(-1px);
    }

    #neighbor-list .neighbor-item-active {
        background: #f2ffe0 !important;
        box-shadow: inset 0 0 0 1px rgba(147, 194, 28, 0.55);
    }

    .note-card {
    position: relative;
}

 
    /* main note actions (top-right of main note body) */
    .note-card .note-actions-main {
        top: 4px;
        right: 6px;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-2px);
        transition:
            opacity 0.15s ease,
            transform 0.15s ease,
            visibility 0.15s ease;
    }

    /* reply actions (top-right of each reply) */
    .note-card .reply-item .reply-actions {
        top: 4px;
        right: 6px;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-2px);
        transition:
            opacity 0.15s ease,
            transform 0.15s ease,
            visibility 0.15s ease;
    }

    /* show main note buttons when hovering the whole note card */
    .note-card:hover .note-actions-main {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0);
    }

    /* show reply buttons when hovering the reply row */
    .note-card .reply-item:hover .reply-actions {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0);
    }


</style>

<style>
    /* 1. Allow the menu to pop out of the container */
    .house-block {
        position: relative; 
        overflow: visible !important; 
    }

    /* 2. Menu Styles */
    .kebab-menu {
        display: none; /* Hidden by default */
        position: absolute;
        top: 100%;
        right: 0;
        z-index: 9999; /* Very high z-index */
        min-width: 200px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        opacity: 0;
        transform: scale(0.95);
        transition: opacity 0.1s ease, transform 0.1s ease;
    }

    /* 3. Visible State */
    .kebab-menu.is-open {
        display: block !important;
        opacity: 1 !important;
        transform: scale(1) !important;
    }
    
    /* 4. Button Style */
    .kebab-btn {
        cursor: pointer;
        position: relative;
        z-index: 50;
    }
</style>

 <style>


    /* ---------------------- Generic card image ---------------------- */
    .card-img-top.lazy {
        object-fit: cover;
        height: 180px;
        width: 100%;
    }

    /* ================================================================
       PRODUCT CARDS (Dashboard)
       ================================================================ */

    /* Wrapper for product cards inside one object */
    .product-card-wrapper {
        display: flex;
        flex-wrap: wrap;
        margin: -0.5rem;           /* control global spacing */
        width: 100%;
    }

    /* Single product card */
    .custom-responsive-card {
        flex: 1 1 100%;
        max-width: 100%;
        margin: 0.5rem;
        border-radius: 12px;
        overflow: visible;
        border: 1px solid #e5e7eb;
        background-color: #ffffff;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.08);
    }

    .custom-responsive-card .card-body {
        display: flex;
        flex-direction: column;
        padding: 0.75rem 0.9rem;
        position: relative;
    }

    /* 2 cards per row on medium screens */
    @media (min-width: 768px) {
        .custom-responsive-card {
            flex: 1 1 calc(50% - 1rem);
            max-width: calc(50% - 1rem);
        }
    }

    /* 3 cards per row on larger screens */
    @media (min-width: 1200px) {
        .custom-responsive-card {
            flex: 1 1 calc(50% - 1rem);
            max-width: calc(50% - 1rem);
        }
    }

    /* Card header */
    .product-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .product-card-main {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .product-symbol {
        width: 42px;
        height: 42px;
        font-size: 14px;
        border-radius: 999px;
    }

    .product-meta {
        font-size: 11px;
        line-height: 1.3;
    }

    .product-card-actions {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 0.25rem;
    }

    @media (max-width: 576px) {
        .product-card-header {
            flex-direction: column;
            align-items: flex-start;
        }
        .product-card-actions {
            margin-top: 0.5rem;
        }
    }

    /* Version label (top right under header) */
    .product-version-label {
        font-size: 11px;
        text-align: right;
        margin-top: 0.25rem;
    }

    /* Time summary row */
    .time-summary-row {
        font-size: 11px;
        margin-top: 0.25rem;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.25rem 0.75rem;
    }

    .time-summary-row small span {
        font-weight: 600;
    }

    /* Progress row */
    .product-progress-row {
        display: flex;
        align-items: center;
        margin-top: 0.35rem;
    }

    .product-progress-row .progress {
        height: 8px;
        flex: 1 1 auto;
    }

    .product-progress-row .progress-count {
        font-size: 11px;
        min-width: 42px;
        text-align: right;
        margin-left: 0.35rem;
    }

    /* Stage icons row (compact, no huge gaps) */
    .stage-icons-row {
        margin-top: 0.45rem;
        padding-top: 0.25rem;
        border-top: 1px dashed #e5e7eb;
    }

    .stage-icons-row .stage-icons {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        padding-bottom: 4px;
        gap: 6px;
    }

    .stage-icons-row .stage-icon {
        flex: 0 0 auto;
        font-size: 14px;
        padding: 8px;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: transform 0.2s ease, background-color 0.2s ease;
        cursor: pointer;
    }

    .stage-icons-row .stage-icon:hover {
        transform: scale(1.05);
        background: #e2e6ea;
    }

    .stage-icons-row .stage-icons::-webkit-scrollbar {
        height: 4px;
    }
    .stage-icons-row .stage-icons::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
    }

    /* Kebab menu per card (only one definition) */
     

    /* ensure header wrapping menu does not clip it */
    .house-header {
        overflow: visible !important;
    }

    /* Version badge inside card */
    #product_version_details {
        background: transparent;
        width: 50px;
        text-align: center;
        color: #686868;
        font-size: 11px;
    }

    /* Product card body + section divider */
        .product-card-body {
            display: flex;
            flex-direction: column;
        }

        /* subtle divider between sections inside the product card */
        .product-section-divider {
            border: 0;
            border-top: 1px solid #edf2f7; /* very light grey/blue */
            margin: 0.55rem 0;
        }


    /* ================================================================
       PHASE TABLE
       ================================================================ */
    .phase-table th,
    .phase-table td {
        vertical-align: middle;
        font-size: 14px;
    }

    .phase-table .bg-success {
        background-color: #cce5b1 !important;
        color: #2c3e50;
    }

    .phase-table td {
        padding: 8px 10px;
    }

    /* ================================================================
       SCROLLABLE SECTION CONTENT
       ================================================================ */
    .section-content {
        max-height: calc(100vh - 120px);
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 10px;
        scroll-behavior: smooth;
        background: white;
    }

    .section-content::-webkit-scrollbar {
        width: 8px;
    }

    .section-content::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .section-content::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 4px;
    }

    .section-content::-webkit-scrollbar-thumb:hover {
        background: #999;
    }

    /* ================================================================
       KANBAN
       ================================================================ */
    .kanban-board {
        overflow-x: auto;
        padding-bottom: 10px;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
    }

    .kanban-board .kanban-header,
    .kanban-board .kanban-body {
        display: flex;
        flex-wrap: nowrap;
    }

    .kanban-column {
        width: 320px;
        min-width: 300px;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .kanban-title {
        background: #8fc73e;
        color: white;
        font-weight: bold;
        text-align: center;
        padding: 12px;
        border-radius: 6px 6px 0 0;
        font-size: 15px;
    }

    .kanban-dropzone {
        min-height: 350px;
        background-color: #f9fafb;
        padding: 10px;
        border: 1px dashed #ccc;
        border-top: none;
        border-radius: 0 0 6px 6px;
        transition: background 0.3s ease;
    }

    .kanban-dropzone:hover {
        background-color: #f1f5f9;
    }

    .kanban-card {
        background: #fff;
        border-left: 4px solid transparent;
        transition:
            border-color 0.2s ease,
            transform 0.2s ease,
            box-shadow 0.2s ease;
        margin-bottom: 10px;
        border-radius: 8px;
        padding: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.07);
        position: relative;
        cursor: grab;
    }

    .kanban-card:hover {
        transform: scale(1.02);
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    }

    .kanban-card.junk {
        opacity: 0.6;
        border-left-color: #d1d5db;
        cursor: not-allowed;
    }

    @media (max-width: 576px) {
        .kanban-column {
            width: 90vw;
        }
        .kanban-card {
            padding: 12px;
        }
        .kanban-title {
            font-size: 14px;
        }
    }

    .kanban-card .disable-pointer {
        pointer-events: none;
    }

    .kanban-card .rounded-circle {
        object-fit: cover;
        border: 1px solid #ddd;
    }

    .kanban-card i {
        opacity: 0.6;
        transition: opacity 0.2s ease;
        cursor: pointer;
    }

    .kanban-card i:hover {
        opacity: 1;
    }

    /* SweetAlert2 always on top */
    .swal2-container{
    z-index: 2147483647 !important; /* max practical */
    }
    .swal2-popup{
    z-index: 2147483647 !important;
    }

    .kanban-card .initial-badge {
        position: absolute;
        top: 8px;
        right: 10px;
        background-color: #f1f5f9;
        color: #666;
        font-weight: bold;
        font-size: 11px;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* ================================================================
       NOTES & RIGHT PANEL / LAYOUT
       ================================================================ */
    #note-scroll-wrapper {
        display: flex;
        flex-direction: column !important;
        height: 100%;
        padding-bottom: 60px;
        scroll-behavior: smooth;
    }

    .note-card .card-body {
        background-color: #f1f0f0;
    }

    .note-card .dropdown-menu {
        font-size: 0.875rem;
    }

    .note-card img {
        object-fit: cover;
    }

    #note-container {
        max-height: 80%;
    }

    .modal-body {
        padding: 0;
        overflow: hidden;
    }

    .note-composer {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
        max-width: 500px;
        max-height: 0;
        overflow: hidden;
        padding: 0;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        z-index: 1050;
        transition: max-height 0.3s ease, padding 0.3s ease;
    }

    .note-composer.open {
        max-height: 300px;
        padding: 1rem;
    }

    .note-composer .submit-wrapper {
        display: none;
    }

    .note-composer.open .submit-wrapper {
        display: block;
    }

    .note-backdrop {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 100vw;
        background: rgba(0, 0, 0, 0.4);
        z-index: 1049;
    }

    #note-list {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .note-reply {
        transition: opacity 0.4s ease;
    }

    .reply-container .card {
        background: #f8f9fa;
        border-left: 2px solid #ccc;
        margin-top: 0.5rem;
    }

    .animated-reply {
        animation: pulse 0.4s ease;
    }

    .reply-wrapper {
        overflow-anchor: auto;
        scroll-margin-bottom: 80px;
    }

    @keyframes pulse {
        0%   { transform: scale(1); background-color: #e7f6ff; }
        50%  { transform: scale(1.015); background-color: #d4f1ff; }
        100% { transform: scale(1); background-color: inherit; }
    }

    .panel-toggle-btn {
        position: absolute;
        top: -40px;
        z-index: 9999;
        background: white;
        border: 1px solid #ccc;
        padding: 6px 10px;
        border-radius: 4px;
        color: #8fc73e;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .delete-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        background: #dc3545;
        border: none;
        color: white;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        font-size: 13px;
        cursor: pointer;
        padding: 0;
        line-height: 20px;
        text-align: center;
    }

    .fade-in {
        opacity: 1;
    }

    .fade-out {
        opacity: 0 !important;
        transition: opacity 0.4s ease-out;
    }

    /* do not kill shadows on special cards, use default none only if needed elsewhere */
    .card {
        box-shadow: none;
    }

    .custom-responsive-card.card {
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.08) !important;
    }

    .main-fullscreen {
        width: 100% !important;
        flex: 1 1 auto;
        padding: 1rem !important;
    }

    .main-fullscreen-mode .customerSidebar,
    .main-fullscreen-mode .right-panel {
        display: none !important;
    }

    .main-fullscreen-mode .contentStation {
        width: 100% !important;
        flex: 1 1 auto !important;
        max-width: 100% !important;
        padding: 1rem !important;
    }

    .contentStation {
        transition: all 0.3s ease;
        flex: 1 1 auto;
        min-width: 0;
    }

    .right-panel {
        width: 360px;
        flex-shrink: 0;
        background: #f6f6f6;
        transition: all 0.3s ease;
        border-left: 1px solid #ccc;
    }

    .right-panel.fullscreen {
        position: relative;
        width: 100% !important;
        max-width: 100%;
        flex: 1 1 auto;
        z-index: 999;
        background: #fff;
        transition: all 0.3s ease;
    }

    .right-panel.fullscreen .note-scroll-wrapper {
        height: calc(100vh - 120px);
        overflow-y: auto;
    }

    .customerSidebar {
        width: 300px;
        flex-shrink: 0;
        transition: width 0.3s ease;
    }

    .customerSidebar.minimized {
        width: 60px;
        padding: 1rem 0.5rem;
    }

    .layout {
        overflow: hidden;
    }

    #halfDoneModal,
    #suggestEmployeesModal,
    #editSuggestedEmployeeModal {
        z-index: 100000 !important;
    }

    #halfDoneModal .modal-backdrop,
    .modal-backdrop.show:nth-of-type(2) {
        z-index: 100000;
    }

    #deletedNotesModalBody .card-body {
        background: white !important;
    }

    #deletedNotesModalBody .btn-success {
        margin-bottom: 0 !important;
    }

    /* ================================================================
       PHASE SIDEBAR / DRAWER
       ================================================================ */
    .card .collapse.show {
        background-color: #ffffff;
    }

    .card-header.active-stage {
        background-color: #c0d8ea !important;
        color: black !important;
    }

    #phaseSidebar .phase-sidebar-body .card-header {
        padding: 1rem !important;
    }

    #phaseSidebar .phase-sidebar-header h5 {
        font-size: 24px;
        font-weight: bold;
        color: white;
    }

    .phase-sidebar {
        position: fixed;
        top: 0;
        right: -100%;
        width: 100%;
        height: 100%;
        background: #fff;
        box-shadow: -4px 0 8px rgba(0, 0, 0, 0.1);
        z-index: 1050;
        transition: right 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .phase-sidebar.open {
        right: 0;
    }

    .phase-sidebar-header {
        padding: 1rem;
        background: #2c3e4f;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .phase-sidebar-body {
        padding: 1rem;
        overflow-y: auto;
        flex: 1;
    }

    .closePhase {
        position: absolute;
        right: 20px;
        margin-top: 38px;
        border-radius: 50% !important;
        padding: 0 !important;
        font-size: 35px;
        width: 40px;
        height: 40px;
    }

    .close-btn {
        background: transparent;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
    }

    #phaseProductInitial {
        padding: 10px;
        font-size: 20px;
        border-radius: 50%;
        color: #cfe09b;
        font-weight: bold;
        background-color: #ffffff !important;
        width: 50px;
        height: 50px;
        place-content: center;
    }

    .upload-icons,
    .file-icons {
        font-size: 22px;
        color: #cfdf9b;
        transition: 0.2s ease;
    }

    .upload-icons:hover,
    .file-icons:hover {
        color: #dc3545;
        transform: scale(1.1);
        cursor: pointer;
    }

    .entry-col .badge {
        font-size: 10px;
        padding: 2px 6px;
        margin-bottom: 2px;
    }

    .badge-dark {
        background-color: #343a40;
        color: #fff;
        font-weight: 600;
        font-size: 11px;
    }

    #next_phase_station button {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }

    #toggleNewNoteBtn,
    #btnToggleRightPanelFullscreen,
    #loadAllDeletedNotes {
        margin-right: 5px;
        margin-left: 1px;
    }

    /* ================================================================
       REPORT SIDEBAR
       ================================================================ */
    .report-sidebar {
        position: fixed;
        top: 0;
        right: -100%;
        width: 80%;
        height: 100%;
        background: #fff;
        z-index: 1050;
        transition: all 0.4s ease-in-out;
        box-shadow: -2px 0 6px rgba(0,0,0,0.2);
    }

    .report-sidebar.open {
        right: 0;
    }

    .timeline .timeline-item {
        border-left: 3px solid #add33e;
        margin-left: 10px;
        padding-left: 15px;
        margin-bottom: 20px;
        position: relative;
        background: #f9f9f9;
        border-radius: 5px;
    }

    .timeline .timeline-item::before {
        content: "";
        position: absolute;
        left: -10px;
        top: 0;
        background: #add33e;
        border-radius: 50%;
        width: 10px;
        height: 10px;
    }

    .timeline .timeline-item:hover {
        background: #eefbe0;
    }

    .report-header button i {
        font-size: 16px;
    }

    .toggle-report-form {
        background-color: #93c119 !important;
        color: white;
    }

    .report-form-modal {
        position: fixed;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.3);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1080;
    }

    .report-form-modal .modal-content {
        width: 500px;
        max-height: 95%;
        overflow-y: auto;
        border-radius: 8px;
    }

    .bg-warning-light {
        background-color: #fff3cd !important;
        transition: background-color 0.4s;
    }

    .reply-preview {
        background-color: #f0f8e8;
        padding: 5px 10px;
        border-left: 3px solid #93c21c;
        border-radius: 4px;
    }

    .edit-suggested-employee img {
        transition: box-shadow 0.3s ease, transform 0.2s ease;
    }

    .edit-suggested-employee:hover img {
        box-shadow: 0 0 6px rgba(0, 123, 255, 0.7);
        transform: scale(1.1);
        cursor: pointer;
    }

    .duration-wrapper input {
        padding: 2px 4px;
        font-size: 13px;
    }

    /* Total purchase */
    .total-purchase-trigger {
        cursor: pointer;
    }
    .total-purchase-trigger:hover .tp-display {
        text-decoration: underline;
    }

    /* ================================================================
       GENERIC RIGHT DRAWER (nx-drawer)
       ================================================================ */
    .nx-drawer {
        position: fixed;
        inset: 0;
        z-index: 1100;
        pointer-events: none;
    }

    .nx-drawer:not(.is-open) {
        visibility: hidden;
    }

    .nx-drawer.is-open {
        pointer-events: auto;
        visibility: visible;
    }

    .nx-drawer-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .nx-drawer-panel {
        position: absolute;
        top: 0;
        right: 0;
        height: 100%;
        width: 780px;
        max-width: 100%;
        background: #ffffff;
        box-shadow: -8px 0 24px rgba(15, 23, 42, 0.18);
        transform: translateX(100%);
        transition: transform 0.25s ease;
        display: flex;
        flex-direction: column;
    }

    .nx-drawer.is-open .nx-drawer-backdrop {
        opacity: 1;
    }

    .nx-drawer.is-open .nx-drawer-panel {
        transform: translateX(0);
    }

    .nx-drawer-header {
        padding: 10px 14px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .nx-drawer-title {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }

    .nx-drawer-close {
        border: none;
        background: transparent;
        font-size: 20px;
        line-height: 1;
        padding: 0 4px;
        cursor: pointer;
    }

    .nx-drawer-body {
        padding: 10px 14px 14px;
        overflow-y: auto;
    }

    .nx-drawer-footer {
        padding: 10px 14px;
        border-top: 1px solid #e5e7eb;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    #suggestEmployeesDrawer .form-group label,
    #editSuggestedEmployeeDrawer .form-group label {
        font-size: 12px;
        font-weight: 600;
        color: #374151;
    }
    .swal2-popup.swal2-bgs-popup {
    padding: 1.5rem 1.75rem;
    }
    .swal2-bgs-popup .swal2-title {
        font-size: 1.1rem;
    }

</style>

<style>
    .customer-drawer {
        position: fixed;
        inset: 0;
        z-index: 1050;
        pointer-events: none;
    }

    .customer-drawer-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.4);
        opacity: 0;
        transition: opacity .2s ease-out;
    }

    .customer-drawer-panel {
        position: absolute;
        top: 0;
        right: 0;
        width: 420px;
        max-width: 100%;
        height: 100%;
        background: #ffffff;
        box-shadow: -10px 0 30px rgba(15, 23, 42, 0.2);
        transform: translateX(100%);
        transition: transform .25s ease-out;
        display: flex;
        flex-direction: column;
    }

    .customer-drawer-header {
        padding: 12px 16px;
        border-bottom: 1px solid #e5e7eb;
    }

    .customer-drawer-body {
        padding: 16px;
        overflow-y: auto;
        flex: 1;
    }

    .customer-drawer.open {
        pointer-events: auto;
    }

    .customer-drawer.open .customer-drawer-panel {
        transform: translateX(0);
    }

    .customer-drawer.open .customer-drawer-backdrop {
        opacity: 1;
    }
</style>

<style>
  /* ===== Custom modal (no Bootstrap JS needed) ===== */
  .cmodal{position:fixed;inset:0;display:none;z-index:9999}
  .cmodal.is-open{display:block}
  .cmodal__backdrop{position:absolute;inset:0;background:rgba(0,0,0,.55)}
  .cmodal__dialog{
    position:relative;
    width:min(1547px, calc(100% - 32px));
    margin:48px auto;
    background:#fff;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 25px 80px rgba(0,0,0,.35);
    transform:translateY(10px);
    opacity:.98;
  }
  .cmodal.is-open .cmodal__dialog{transform:none;opacity:1}
  .cmodal__header{display:flex;align-items:center;justify-content:space-between;padding:14px 16px}
  .cmodal__title{margin:0;font-size:16px;font-weight:600}
  .cmodal__close{
    border:0;background:transparent;font-size:22px;line-height:1;cursor:pointer;
    width:36px;height:36px;border-radius:10px;
  }
  .cmodal__close:hover{background:rgba(0,0,0,.06)}
  .cmodal__body{padding:14px 16px;max-height:calc(100vh - 200px);overflow:auto}
  .cmodal__footer{display:flex;gap:10px;justify-content:flex-end;padding:14px 16px;border-top:1px solid rgba(0,0,0,.08)}
  body.cmodal-open{overflow:hidden}

  /* keep select2 dropdown inside modal above backdrop */
  .select2-container{z-index:10000}
</style>
<style>
    /* 1. Ensure the outer layout stays fixed to viewport height */
    .layout {
        display: flex;
        width: 100%;
        height: 100vh; /* Force full screen height */
        overflow: hidden; /* Prevent body scroll */
    }

    /* 2. The container holding the main content area */
    .contentStation {
        display: flex;        /* Enable Flexbox */
        flex-direction: column; 
        flex: 1;              /* Take remaining width */
        min-width: 0;         /* Flexbox fix for nested scrolling */
        height: 100%;         /* Fill vertical space */
        overflow: hidden;     /* Stop this specific container from scrolling */
    }

    /* 3. The wrapper div that holds #mainContent */
    .main-content {
        flex: 1;              /* Grow to fill available space */
        height: 100%;         /* Enforce height limit */
        overflow-y: auto;     /* ✅ ENABLE SCROLL HERE */
        overflow-x: hidden;   /* Hide horizontal scroll */
        padding: 1rem;
        position: relative;
    }

    /* 4. The specific ID you asked about */
    #mainContent {
        width: 100%;
        min-height: min-content; /* Ensure it takes up space */
        padding-bottom: 100px;   /* Extra space at bottom so you can scroll past the last item */
    }
    
    /* Optional: Custom Scrollbar for better visibility */
    .main-content::-webkit-scrollbar {
        width: 8px;
    }
    .main-content::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 4px;
    }
</style>

<style>
    /* Drawer Overlay (Fixed position) */
    #objDrawerRoot {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 10000; /* Above everything */
        display: none;
    }
    
    #objDrawerRoot.is-visible {
        display: block;
    }

    /* Dark Backdrop */
    #objDrawerBackdrop {
        position: absolute;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    #objDrawerRoot.is-visible #objDrawerBackdrop {
        opacity: 1;
    }

    /* The White Panel */
    #objDrawerPanel {
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        max-width: 900px;
        height: 100%;
        background-color: #fff;
        box-shadow: -10px 0 25px rgba(0,0,0,0.1);
        transform: translateX(100%);
        transition: transform 0.3s cubic-bezier(0.25, 1, 0.5, 1);
        display: flex;
        flex-direction: column;
    }

    #objDrawerRoot.is-visible #objDrawerPanel {
        transform: translateX(0);
    }

    .drawer-hidden { display: none !important; }

    /* Custom styles to mimic the "Modern" look using standard CSS */
    .obj-card {
        border: 1px solid #eaeaea;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: all 0.2s;
    }
    .obj-card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eaeaea;
        padding: 12px;
        border-radius: 12px 12px 0 0;
    }
    .obj-dropzone {
        background-color: #f1f5f9;
        min-height: 120px;
        padding: 10px;
        border-radius: 0 0 12px 12px;
    }
    .prod-card {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        padding: 10px;
        margin-bottom: 8px;
        cursor: grab;
    }
    .prod-card:active {
        cursor: grabbing;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    #serialsOverlay { pointer-events: auto; }
    #serialsOverlay .sn-dialog, #serialsOverlay input { pointer-events: auto; }

</style>

<style>
    /* ... existing styles ... */

    /* Product Initials Badge */
    .product-badge-group {
        display: flex;
        gap: 4px;
        margin-top: 4px;
        flex-wrap: wrap;
    }
    
    .product-mini-badge {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background-color: #f8f9fa;
        border: 1px solid #d1d5db;
        color: #374151;
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: help;
        transition: all 0.2s ease;
    }

    .product-mini-badge:hover {
        background-color: #eef2ff;
        border-color: #6366f1;
        color: #6366f1;
        transform: scale(1.1);
    }

    /* Collapse Arrow Animation */
    .collapse-icon {
        transition: transform 0.3s ease;
    }
    .collapse-icon.collapsed {
        transform: rotate(-90deg);
    }
    
    /* Make header clickable for collapse */
    .house-header {
        cursor: pointer;
        user-select: none;
    }
    
    /* Ensure dropdown buttons don't trigger collapse */
    .kebab-wrap, .house-img img {
        cursor: pointer; 
    }


    /* Interactive Badges in Object Header */
    .product-mini-badge {
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #d1d5db;
        opacity: 0.5; /* Default inactive state */
        background: #f3f4f6;
        color: #9ca3af;
    }

    .product-mini-badge.active {
        opacity: 1;
        background: #fff;
        border-color: #93c119; /* Your brand green */
        color: #93c119;
        font-weight: bold;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transform: scale(1.05);
    }

    .product-mini-badge:hover {
        transform: scale(1.1);
    }

    /* New Product Navigation Bar */
    .product-quick-nav {
        display: flex;
        justify-content: space-around;
        background: #f8fafc;
        border-top: 1px solid #e5e7eb;
        padding: 8px 4px;
        margin-top: 10px;
        border-radius: 0 0 12px 12px;
    }

    .nav-item-btn {
        border: none;
        background: transparent;
        color: #64748b;
        font-size: 11px;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        cursor: pointer;
        transition: color 0.2s;
        padding: 4px 8px;
        border-radius: 6px;
    }

    .nav-item-btn:hover {
        color: #0f172a;
        background: #e2e8f0;
    }

    .nav-item-btn i {
        font-size: 16px;
        margin-bottom: 2px;
    }
</style>
 
<style>
 /* Action Dropdown Styling */
    .kebab-wrap {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10000; /* Ensure it is above the locked overlay */
    }

    .aktion-btn {
        background: #ffffff;
        border: 1px solid #d1d5db;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
    }

    .aktion-btn:hover {
        background: #f9fafb;
    }

    .kebab-menu {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        min-width: 220px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        z-index: 10001;
        padding: 5px;
    }

    .kebab-menu.is-open {
        display: block !important;
    }

    .kebab-item {
        display: flex;
        align-items: center;
        width: 100%;
        padding: 8px 12px;
        font-size: 13px;
        color: #374151;
        border-radius: 6px;
        border: none;
        background: transparent;
        text-align: left;
        gap: 10px;
        text-decoration: none !important;
    }

    .kebab-item:hover {
        background: #f3f4f6;
        color: #111827;
    }

    /* Note Containers */
    .note-title-view, .note-desc-view {
        padding: 4px;
        border-radius: 4px;
        transition: background 0.2s;
    }

    .note-title-view:hover, .note-desc-view:hover {
        background: #f3f4f6;
    }
</style>

<style>
    /* Custom Serial Numbers Modal (No Bootstrap) */
    .sn-overlay{
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    padding: 18px;
    z-index: 99999;
    background: rgba(0,0,0,.55);
    }

    .sn-overlay.is-open{ display:flex; }

    .sn-dialog{
    width: min(920px, 100%);
    max-height: calc(100vh - 40px);
    overflow: hidden;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 30px 80px rgba(0,0,0,.35);
    transform: translateY(10px);
    opacity: 0;
    animation: snIn .14s ease-out forwards;
    }

    @keyframes snIn{
    to { transform: translateY(0); opacity: 1; }
    }

    .sn-header{
    display:flex;
    align-items:center;
    justify-content: space-between;
    padding: 14px 16px;
    border-bottom: 1px solid rgba(0,0,0,.08);
    }

    .sn-title{ margin:0; font-size: 16px; font-weight: 700; }

    .sn-close{
    width: 36px;
    height: 36px;
    border: 0;
    border-radius: 10px;
    background: rgba(0,0,0,.06);
    font-size: 22px;
    line-height: 1;
    cursor: pointer;
    }

    .sn-body{
    padding: 14px 16px;
    overflow: auto;
    max-height: calc(100vh - 180px);
    }

    .sn-info{
    background: rgba(13,110,253,.10);
    border: 1px solid rgba(13,110,253,.20);
    color: #0b2e6f;
    border-radius: 10px;
    padding: 10px 12px;
    margin-bottom: 12px;
    display:flex;
    gap: 8px;
    flex-wrap: wrap;
    align-items:center;
    }

    .sn-sep{ opacity: .7; }

    .sn-table-wrap{ width: 100%; overflow:auto; border-radius: 10px; border: 1px solid rgba(0,0,0,.08); }
    .sn-table{ width: 100%; border-collapse: collapse; }
    .sn-table th, .sn-table td{ padding: 10px; border-bottom: 1px solid rgba(0,0,0,.06); }
    .sn-table thead th{ background: rgba(0,0,0,.03); font-weight: 700; }

    .sn-footer{
    display:flex;
    justify-content: flex-end;
    gap: 10px;
    padding: 12px 16px;
    border-top: 1px solid rgba(0,0,0,.08);
    background: #fff;
    }

    /* serial overlay (custom, not bootstrap) */
    #serialsOverlay.sn-overlay{
    position: fixed;
    inset: 0;
    z-index: 20000;              /* higher than bootstrap modal (1050) */
    display: none;
    align-items: center;
    justify-content: center;
    background: rgba(0,0,0,.55);
    pointer-events: auto;
    }

    #serialsOverlay.sn-overlay.is-open{ display:flex; }

    #serialsOverlay .sn-dialog{
    width: min(920px, 95vw);
    max-height: 85vh;
    overflow: auto;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 20px 60px rgba(0,0,0,.35);
    pointer-events: auto;
    }

    #serialsOverlay input,
    #serialsOverlay button{
    pointer-events: auto;
    user-select: text;
    }

    /* disable click capture by open bootstrap modal while serial overlay is open */
    body.sn-open .modal.show{
    pointer-events: none;
    }


</style>
 <style>
/* =========================
CUSTOM OVERLAYS (ADD / EDIT / SERIALS)
========================= */
.cp-overlay{
  position:fixed;
  inset:0;
  display:none;
  align-items:center;
  justify-content:center;
  padding:24px;
  background:rgba(0,0,0,.55);
  z-index:20000; /* must be higher than select2 + everything */
}

.cp-overlay.is-open{ display:flex; }

.cp-dialog{
  width:min(980px, 96vw);
  max-height:90vh;
  overflow:auto;
  background:#fff;
  border-radius:14px;
  box-shadow:0 25px 70px rgba(0,0,0,.35);
  outline:none;
}

.cp-header{
  position:sticky;
  top:0;
  background:#fff;
  z-index:2;
  display:flex;
  align-items:center;
  justify-content:space-between;
  padding:14px 16px;
  border-bottom:1px solid rgba(0,0,0,.08);
}

.cp-title{ margin:0; font-size:16px; font-weight:600; }

.cp-close{
  width:34px;
  height:34px;
  border-radius:10px;
  border:1px solid rgba(0,0,0,.12);
  background:#fff;
  font-size:22px;
  line-height:1;
  cursor:pointer;
}

.cp-body{ padding:14px 16px; }
.cp-footer{
  position:sticky;
  bottom:0;
  background:#fff;
  z-index:2;
  display:flex;
  justify-content:flex-end;
  gap:10px;
  padding:12px 16px;
  border-top:1px solid rgba(0,0,0,.08);
}

.sn-info{
  display:flex;
  align-items:center;
  gap:10px;
  padding:10px 12px;
  border:1px solid rgba(0,0,0,.08);
  border-radius:12px;
  background:rgba(0,0,0,.03);
  margin-bottom:12px;
}
.sn-sep{ opacity:.6; }
.sn-table-wrap{ overflow:auto; border:1px solid rgba(0,0,0,.08); border-radius:12px; }
.sn-table{ width:100%; border-collapse:collapse; }
.sn-table th, .sn-table td{ padding:10px; border-bottom:1px solid rgba(0,0,0,.06); }
.sn-table thead th{ position:sticky; top:0; background:#fff; z-index:1; }
.serial-input{ width:100%; }

html.cp-modal-open, body.cp-modal-open{ overflow:hidden; }

/* Select2 z-index fixes inside overlays */
.cp-overlay .select2-container{ z-index:20010; }
.select2-dropdown{ z-index:20020 !important; } /* must be above overlay dialog */
</style>

 
@endsection

@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0">KUNDENPROFIL</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ url('/new_lead_view') }}">Kunde</a></li>
                                <li class="breadcrumb-item active">{{ $customer->name }} {{ $customer->lastname }}</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-body"> 
            <!-- Insert the full HTML layout from above here (customer-nav, layout, sidebar, main-content, right-panel) -->
            @include('admin.new_leads.layouts.profile') 
        </div>


        <div class="modal fade" id="newProductModal" tabindex="-1" role="dialog" aria-labelledby="newProductModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <form method="POST" action="{{ route('lead_product_lists.bulk.store') }}">
                @csrf
                <input type="hidden" name="customer_id" id="product_customer_id">
                <input type="hidden" name="alternative_id" id="product_alternative_id">


                <div class="modal-content">
                    <div class="modal-header">
                    <h5 class="modal-title">Neues Produkt hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    </div>
                    <div class="modal-body">
                    @include('admin.new_leads.layouts.new_product_form')
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="saveProductRows" class="btn btn-success float-right mt-2">
                            <i class="feather icon-save"></i> Speichern
                        </button>

                    </div>
                </div>
                </form>
            </div>
        </div>

        <!-- Report Slider for each customer product  -->  
           <div id="reportSidebar" class="report-sidebar " style="display:none;">
                <div class="report-header d-flex justify-content-between align-items-center p-3 border-bottom">
                    <h5 class="mb-0">Kundenprozessbericht</h5>
                    <div>
                        <button class="btn btn-sm btn-primary mr-1 toggle-report-form"><i class="fa fa-plus"></i></button>
                        <button class="btn btn-sm btn-danger close-report-sidebar"><i class="fa fa-times"></i></button>
                    </div>
                </div>

                <div id="reportList" class="p-3 timeline overflow-auto" style="height: calc(100% - 60px);"></div>

                <!-- 🔻 FORM AS MODAL OVERLAY -->
                <div id="reportFormContainer" class="report-form-modal" style="display:none;">
                    <div class="modal-content bg-white p-4 ">
                        <form id="reportForm">
                            <input type="hidden" name="product_id">
                            <input type="hidden" name="customer_id">
                            <input type="hidden" name="alternative_id">
                            <div class="form-group">
                                <label>Datum</label>
                                <input type="date" name="report_date" class="form-control" required value="{{ now()->format('Y-m-d') }}">

                            </div>
                            <div class="form-group">
                                <label>Stage</label>
                                <select name="stage" class="form-control" required>
                                    <option value="">-- auswählen --</option>
                                        @php
                                            $stageLabels = [
                                                'lead'      => 'Anfrage',
                                                'offer'     => 'Angebot',
                                                'deal'      => 'Vertrag',
                                                'project'   => 'Projekt',
                                                'complete'  => 'Abgeschlossen',
                                                'review'    => 'Überprüfung',
                                                'archive'   => 'Archiviert',
                                                'ticket'    => 'Ticket',
                                                'pause'     => 'Pausiert',
                                                'cancel'    => 'Storniert',
                                            ];
                                        @endphp

                                        @foreach($stageLabels as $stage => $label)
                                            <option value="{{ $stage }}">{{ $label }}</option>
                                        @endforeach

                                </select>
                            </div>
                            <div class="form-group">
                                <label>Bericht</label>
                                <div id="quill-editor" style="height: 150px;"></div>
                                <input type="hidden" name="report">
                            </div>
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-success">Speichern</button>
                                <button type="button" class="btn btn-light close-report-form">Abbrechen</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
 <!-- Report Slider for each customer product  --> 


        <div id="commentSidebar" class="report-sidebar shadow" style="display:none;">
            <div class="d-flex justify-content-between align-items-center p-2 border-bottom">
                <h5 class="mb-0">Kommentare</h5>
                <div>
                    <button class="btn btn-sm btn-success mr-2 open-comment-form"><i class="fa fa-plus"></i></button>
                    <button class="btn btn-sm btn-danger close-comment-sidebar">×</button>
                </div>
            </div>
            <div id="commentContent" class="p-3 overflow-auto" style="height: calc(100% - 50px);"></div>

            <!-- 🔻 Modal Form -->
                <div id="commentFormModal" class="report-form-modal" style="display:none;">
                    <div class="modal-content bg-white p-3 shadow" style="width: 90%; max-width: 500px; position:relative;">
                        <button type="button" class="btn btn-sm btn-danger close-comment-form"
                                style="position:absolute; top:8px; right:8px; line-height:1;">×</button>

                        <form id="newCommentForm" class="mt-3">
                            <input type="hidden" name="report_id" id="report_id">
                            <input type="hidden" name="parent_id" id="parent_id">
                            <div id="commentMeta"></div>
                            <div id="quotedComment" class="alert alert-light py-2 px-3" style="display:none;"></div>

                            <textarea name="comment" class="form-control" rows="3" placeholder="Kommentieren..." required></textarea>

                            <div class="d-flex justify-content-end mt-2">
                                <button type="button" class="btn btn-light mr-2 close-comment-form">Abbrechen</button>
                                <button type="submit" class="btn btn-primary">Senden</button>
                            </div>
                        </form>
                    </div>
                </div>


        </div>

        @php
                 $allEmployees = DB::table('employees')->select('id', 'name', 'lastname', 'image')->where('status', 'Active')->get();

        @endphp

            @php
        $docTypes = [
            'customer' => 'Kunde',
            'montage' => 'Montage',
            'Reklamation' => 'Reklamation',
            'Rechnung' => 'Rechnung',
            'Auftrag' => 'Auftrag',
            'AuftragBeshtitgung' => 'Auftragsbestätigung',
            'Angebot' => 'Angebot',
            'Wartung' => 'Wartung',
            'Ticket' => 'Ticket',
            'end' => 'Abgeschlossen',
            'Other' => 'Sonstiges'
        ];
    @endphp

 
        {{-- ================= SUGGEST EMPLOYEES DRAWER ================= --}}
            <div id="suggestEmployeesDrawer" class="nx-drawer">
                <div class="nx-drawer-backdrop" data-drawer-close></div>

                <div class="nx-drawer-panel">
                    <div class="nx-drawer-header">
                        <div class="nx-drawer-title">Mitarbeiter vorschlagen</div>
                        <button type="button" class="nx-drawer-close" data-drawer-close aria-label="Schließen">
                            &times;
                        </button>
                    </div>

                    <form id="suggestEmployeesForm">
                        @csrf
                        <input type="hidden" name="customer_id">
                        <input type="hidden" name="alternative_id">
                        <input type="hidden" name="product_id">
                        <input type="hidden" name="phase_id">

                        <div class="nx-drawer-body">
                            <div id="employeeRows"></div>
                        </div>

                        <div class="nx-drawer-footer">
                            <button type="submit" class="btn btn-success">Speichern</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ================= EDIT SUGGESTED EMPLOYEE DRAWER ================= --}}
            <div id="editSuggestedEmployeeDrawer" class="nx-drawer">
                <div class="nx-drawer-backdrop" data-drawer-close></div>

                <div class="nx-drawer-panel">
                    <div class="nx-drawer-header">
                        <div class="nx-drawer-title">Mitarbeiter bearbeiten</div>
                        <button type="button" class="nx-drawer-close" data-drawer-close aria-label="Schließen">
                            &times;
                        </button>
                    </div>

                    <form id="editSuggestedEmployeeForm">
                        @csrf
                        <input type="hidden" name="suggestion_id">
                        <input type="hidden" name="customer_id">
                        <input type="hidden" name="alternative_id">
                        <input type="hidden" name="product_id">
                        <input type="hidden" name="phase_id">

                        <div class="nx-drawer-body">
                            <div class="form-group">
                                <label>Mitarbeiter</label>
                                <select class="form-control select2" name="employee_id" required>
                                    <option value="">Mitarbeiter wählen</option>
                                    @foreach($allEmployees as $emp)
                                        <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Abteilung</label>
                                <select class="form-control" name="department_id" required>
                                    <option value="">Abteilung wählen</option>
                                    <!-- JS will populate -->
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Rolle</label>
                                <select class="form-control" name="role" required>
                                    <option value="team">Teammitglied</option>
                                    <option value="leader">Teamleiter</option>
                                    <option value="representative">Vertreter</option>
                                    <option value="monteur">Monteur</option>
                                    <option value="obermonteur">Obermonteur</option>
                                    <option value="helper">Helfer</option>
                                    <option value="innendienst">Innendienst</option>
                                    <option value="aussendienst">Außendienst</option>
                                    <option value="bauleiter">Bauleiter</option>
                                    <option value="buchhaltung">Buchhaltung</option>
                                    <option value="techniker">Techniker</option>
                                    <option value="controller">Kontroller</option>
                                </select>
                            </div>
                        </div>

                        <div class="nx-drawer-footer">
                            <button type="submit" class="btn btn-success">Speichern</button>
                            <button type="button" class="btn btn-danger" id="deleteSuggestedEmployee">Löschen</button>
                        </div>
                    </form>
                </div>
            </div>


        <!-- Modal for Partial -->
        <div class="modal fade" id="halfDoneModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <form id="halfDoneForm" class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Teilweise erledigt</h5>
                        <button type="button" class="close" data-bs-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body p-3">
                        <input type="hidden" name="activity_id">
                        <input type="hidden" name="phase_id">
                        <input type="hidden" name="is_done" value="half">

                        <div class="form-group">
                            <label>Fertigstellungsgrad</label>
                            <select name="percent" class="form-control" required>
                                <option value="">Wählen...</option>
                                <option value="0">0%</option>
                                <option value="25">25%</option>
                                <option value="50">50%</option>
                                <option value="75">75%</option>
                                <option value="100">100%</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Begründung</label>
                            <textarea name="reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Speichern</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Abbrechen</button>
                    </div>
                </form>
            </div>
        </div>
 
       <div class="modal fade" id="doneHistoryModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Verlaufsdetails</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Schließen">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="doneHistoryContent">
                            <p class="text-muted">Lade Verlauf...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            {{-- CUSTOMER EDIT DRAWER --}}
            @include('admin.new_leads.partials.customerEditDrawer')

            {{-- Fix Google Places dropdown z-index --}}
            <style>
                .pac-container {
                    z-index: 99999 !important;
                }
            </style>

    </div>
</div>


 {{-- Kontaktpersonen Modal – Custom JS, no Bootstrap --}}
<div id="customerContactPeopleModal" class="ccp-modal-backdrop">
    <div class="ccp-modal-panel">
        <div class="ccp-modal-header">
            <div class="ccp-modal-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                <span>Kontaktpersonen</span>
            </div>

            <button type="button" class="ccp-modal-close-btn" aria-label="Schließen">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="ccp-modal-body">
            <div class="ccp-modal-top-bar">
                <h6 class="mb-0">Übersicht</h6>
                <button type="button" class="btn btn-sm btn-primary" id="addContactPersonBtn">
                    <i class="feather icon-plus me-1"></i> Neu
                </button>
            </div>

            <div class="ccp-modal-table-wrap">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Beziehung</th>
                            <th>Name</th>
                            <th>Telefon</th>
                            <th>E-Mail</th>
                            <th>Status</th>
                            <th class="text-end">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody id="contactPeopleTableBody">
                        {{-- filled by JS --}}
                    </tbody>
                </table>
            </div>

            <div class="ccp-modal-form-wrap">
                <h6 id="contactPersonFormTitle">Kontaktperson hinzufügen</h6>

                <form id="contactPersonForm">
                    <input type="hidden" id="contactPersonId" name="id">
                    <input type="hidden" id="contactPersonCustomerId" value="{{ $customer->id }}">

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Beziehung</label>
                            <input type="text" class="form-control" name="relation" id="cpRelation"
                                   placeholder="z.B. Ehepartner, Sohn, Hausmeister">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Vorname</label>
                            <input type="text" class="form-control" name="name" id="cpName">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Nachname</label>
                            <input type="text" class="form-control" name="lastname" id="cpLastname">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Telefon (mobil)</label>
                            <input type="text" class="form-control" name="phone" id="cpPhone">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Büro</label>
                            <input type="text" class="form-control" name="office" id="cpOffice">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Privat</label>
                            <input type="text" class="form-control" name="home" id="cpHome">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">E-Mail</label>
                            <input type="email" class="form-control" name="email" id="cpEmail">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" id="cpStatus">
                                <option value="Published">Aktiv / Published</option>
                                <option value="Archived">Archiviert</option>
                            </select>
                        </div>
                    </div>

                    <div class="ccp-modal-actions">
                        <button type="button" class="btn btn-light ccp-modal-close-btn">Schließen</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="feather icon-save me-1"></i> Speichern
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

 <div id="addProductOverlay" class="cp-overlay" aria-hidden="true">
  <div class="cp-dialog" role="dialog" aria-modal="true" aria-labelledby="addProductTitle">

    {{-- Header --}}
    <div class="cp-header">
      <div class="d-flex align-items-center justify-content-between w-100">
        <div>
          <h5 id="addProductTitle" class="cp-title mb-0">Neues Produkt hinzufügen</h5>
          <small class="text-muted">Details & Dateien</small>
        </div>
        <button type="button" class="cp-close" data-cp-close="addProductOverlay" aria-label="Close">&times;</button>
      </div>
    </div>

    {{-- Body --}}
    <div class="cp-body">

      {{-- Hidden: becomes available after store success --}}
      <input type="hidden" id="add_product_info_id" value="">

      {{-- Tabs --}}
      <ul class="nav nav-tabs px-2 pt-2" role="tablist" style="border-bottom:1px solid rgba(0,0,0,.08);">
        <li class="nav-item">
          <a class="nav-link active" id="add-tab-details-link" data-toggle="tab" href="#addTabDetails" role="tab" aria-controls="addTabDetails" aria-selected="true">
            <i class="feather icon-info mr-50"></i> Details
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="add-tab-gallery-link" data-toggle="tab" href="#addTabGallery" role="tab" aria-controls="addTabGallery" aria-selected="false">
            <i class="feather icon-image mr-50"></i> Galerie
          </a>
        </li>
      </ul>

      <div class="tab-content p-2">

        {{-- =========================
        DETAILS TAB (ADD)
        ========================= --}}
        <div class="tab-pane fade show active" id="addTabDetails" role="tabpanel" aria-labelledby="add-tab-details-link">
          <div class="row g-2 p-2">

            <div class="col-md-6">
              <label class="mb-25">Anzahl</label>
              <input type="number" value="1" min="1" id="product_count" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Produkt auswählen</label>
              <select id="customer_product_info" class="form-control select2" style="width:100%;"></select>
            </div>

            <div class="col-md-6">
              <label class="mb-25">Hersteller</label>
              <input type="text" id="manufacturer_note" class="form-control" placeholder="z.B. Bosch">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Seriennummer</label>
              <div class="input-group">
                <input type="text" id="serial_number" class="form-control" placeholder="z.B. SN-001">
                <div class="input-group-append">
                  <button class="btn btn-outline-primary" type="button" id="btnSerialsAdd" disabled title="Mehrere Seriennummern">
                    <i class="feather icon-list"></i>
                  </button>
                </div>
              </div>
              <input type="hidden" id="serial_numbers_json" value="">
              <small class="text-muted d-block mt-25" id="serialsHintAdd" style="display:none;"></small>
            </div>

            <div class="col-md-6">
              <label class="mb-25">Installationsdatum</label>
              <input type="date" id="installation_date" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Installationsort</label>
              <input type="text" id="installation_location" class="form-control" placeholder="z.B. Keller / Technikraum">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Bei uns gekauft</label>
              <select id="purchased_from_us" class="form-control">
                <option value="1" selected>Ja</option>
                <option value="0">Nein</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="mb-25">Kaufdatum</label>
              <input type="date" id="purchase_date" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Rechnung/Referenz</label>
              <input type="text" id="invoice_reference" class="form-control" placeholder="z.B. RE-2026-001">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Garantie bis</label>
              <input type="date" id="warranty_until" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Gewährleistung bis</label>
              <input type="date" id="guarantee_until" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Bild vorhanden</label>
              <select id="image_available" class="form-control">
                <option value="1">Ja</option>
                <option value="0" selected>Nein</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="mb-25">Installiert von</label>
              <select id="installed_by" class="form-control js-employee-select" style="width:100%;">
                <option value=""></option>
                @foreach($employees as $emp)
                  @php($fullName = trim($emp->name . ' ' . $emp->lastname))
                  <option value="{{ $fullName }}">{{ $fullName }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="mb-25">Abteilung</label>
              <select id="department_id" class="form-control"></select>
            </div>

            <div class="col-md-12">
              <label class="mb-25">Notizen</label>
              <textarea id="notes_note" class="form-control" rows="3" placeholder="Zusätzliche Informationen..."></textarea>
            </div>

          </div>
        </div>

        {{-- =========================
        GALLERY TAB (ADD) - LOCKED UNTIL SAVED
        ========================= --}}
        <div class="tab-pane fade" id="addTabGallery" role="tabpanel" aria-labelledby="add-tab-gallery-link">
          <div class="p-2">

            <div id="addGalleryLocked" class="alert alert-info mb-2" style="border-radius:10px;">
              <div class="d-flex align-items-start">
                <i class="feather icon-lock mr-1 mt-25"></i>
                <div>
                  <div class="font-weight-bold">Galerie ist gesperrt</div>
                  <div>Bitte zuerst das Produkt speichern – danach kannst du Bilder & PDF hochladen.</div>
                </div>
              </div>
            </div>

            <div id="addGalleryControls" class="d-none">

              <div class="row g-2 align-items-end">
                <div class="col-md-6">
                  <label class="mb-25">Dateien auswählen (Bilder / PDF)</label>
                  <input type="file" id="add_gallery_files" class="form-control" multiple accept="image/*,application/pdf">
                 </div>

                <div class="col-md-3">
                  <label class="mb-25">Typ</label>
                  <select id="add_gallery_type" class="form-control">
                    <option value="" selected>Alle</option>
                    <option value="image">Bilder</option>
                    <option value="pdf">PDF</option>
                  </select>
                </div>

                <div class="col-md-3">
                  <button type="button" class="btn btn-primary btn-block" id="btnAddGalleryUpload">
                    <i class="feather icon-upload mr-50"></i> Upload
                  </button>
                </div>

                <div class="col-md-12">
                  <label class="mb-25">Suchen</label>
                  <input type="text" id="add_gallery_search" class="form-control" placeholder="Suchen nach Dateiname...">
                </div>
              </div>

              <div class="mt-2" id="addGalleryEmpty" style="display:none;">
                <div class="alert alert-secondary mb-0" style="border-radius:10px;">
                  Noch keine Dateien vorhanden.
                </div>
              </div>

              <div class="row mt-2" id="addGalleryGrid"></div>
            </div>
          </div>
        </div>

      </div>
    </div>

    {{-- Footer --}}
    <div class="cp-footer" style="display:flex; gap:10px; justify-content:flex-end;">
      <button type="button" class="btn btn-secondary" data-cp-close="addProductOverlay">Schließen</button>
      <button type="button" class="btn btn-outline-secondary" id="btnAddNew">
        <i class="feather icon-rotate-ccw mr-50"></i> Neu
      </button>
      <button type="button" class="btn btn-primary" id="btnAddSave">
        <i class="feather icon-save mr-50"></i> Speichern
      </button>
    </div>

  </div>
</div>



{{-- =========================================================
EDIT PRODUCT (CUSTOM) + GALLERY TAB (FULL REWRITE)
- Tabs: Details + Galerie
- Gallery supports upload, search, filter
========================================================= --}}
<div id="editProductOverlay" class="cp-overlay" aria-hidden="true">
  <div class="cp-dialog" role="dialog" aria-modal="true" aria-labelledby="editProductTitle">

    {{-- Header --}}
    <div class="cp-header">
      <div class="d-flex align-items-center justify-content-between w-100">
        <div>
          <h5 id="editProductTitle" class="cp-title mb-0">Produkt ansehen / bearbeiten</h5>
          <small class="text-muted">Details & Dateien</small>
        </div>
        <button type="button" class="cp-close" data-cp-close="editProductOverlay" aria-label="Close">&times;</button>
      </div>
    </div>

    {{-- Body --}}
    <div class="cp-body">
      <input type="hidden" id="edit_id">

      {{-- Tabs --}}
      <ul class="nav nav-tabs px-2 pt-2" role="tablist" style="border-bottom:1px solid rgba(0,0,0,.08);">
        <li class="nav-item">
          <a class="nav-link active" id="edit-tab-details-link" data-toggle="tab" href="#editTabDetails" role="tab" aria-controls="editTabDetails" aria-selected="true">
            <i class="feather icon-info mr-50"></i> Details
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link" id="edit-tab-gallery-link" data-toggle="tab" href="#editTabGallery" role="tab" aria-controls="editTabGallery" aria-selected="false">
            <i class="feather icon-image mr-50"></i> Galerie
          </a>
        </li>
      </ul>

      <div class="tab-content p-2">

        {{-- =========================
        DETAILS TAB (EDIT)
        ========================= --}}
        <div class="tab-pane fade show active" id="editTabDetails" role="tabpanel" aria-labelledby="edit-tab-details-link">
          <div class="row g-2 p-2">

            <div class="col-md-6">
              <label class="mb-25">Anzahl</label>
              <input type="number" min="1" id="edit_product_count" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Produkt auswählen</label>
              <select id="edit_product_name" class="form-control select2" style="width:100%;"></select>
            </div>

            <div class="col-md-6">
              <label class="mb-25">Hersteller</label>
              <input type="text" id="edit_manufacturer" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Seriennummer</label>
              <div class="input-group">
                <input type="text" id="edit_serial_number" class="form-control" placeholder="z.B. SN-001">
                <div class="input-group-append">
                  <button class="btn btn-outline-primary" type="button" id="btnSerialsEdit" disabled title="Mehrere Seriennummern">
                    <i class="feather icon-list"></i>
                  </button>
                </div>
              </div>
              <input type="hidden" id="edit_serial_numbers_json" value="">
              <small class="text-muted d-block mt-25" id="serialsHintEdit" style="display:none;"></small>
            </div>

            <div class="col-md-6">
              <label class="mb-25">Installationsdatum</label>
              <input type="date" id="edit_installation_date" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Installationsort</label>
              <input type="text" id="edit_installation_location" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Bei uns gekauft</label>
              <select id="edit_purchased_from_us" class="form-control">
                <option value="1">Ja</option>
                <option value="0">Nein</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="mb-25">Kaufdatum</label>
              <input type="date" id="edit_purchase_date" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Rechnung/Referenz</label>
              <input type="text" id="edit_invoice_reference" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Garantie bis</label>
              <input type="date" id="edit_warranty_until" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Gewährleistung bis</label>
              <input type="date" id="edit_guarantee_until" class="form-control">
            </div>

            <div class="col-md-6">
              <label class="mb-25">Bild vorhanden</label>
              <select id="edit_image_available" class="form-control">
                <option value="1">Ja</option>
                <option value="0">Nein</option>
              </select>
            </div>

            <div class="col-md-6">
              <label class="mb-25">Installiert von</label>
              <select id="edit_installed_by" class="form-control js-employee-select" style="width:100%;">
                <option value=""></option>
                @foreach($employees as $emp)
                  @php($fullName = trim($emp->name . ' ' . $emp->lastname))
                  <option value="{{ $fullName }}">{{ $fullName }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="mb-25">Abteilung</label>
              <select id="edit_department_id" class="form-control"></select>
            </div>

            <div class="col-md-12">
              <label class="mb-25">Notizen</label>
              <textarea id="edit_notes" class="form-control" rows="3"></textarea>
            </div>

          </div>
        </div>

        {{-- =========================
        GALLERY TAB (EDIT)
        ========================= --}}
        <div class="tab-pane fade" id="editTabGallery" role="tabpanel" aria-labelledby="edit-tab-gallery-link">
          <div class="p-2">

            <div class="row g-2 align-items-end">
              <div class="col-md-6">
                <label class="mb-25">Dateien auswählen (Bilder / PDF)</label>
                <input type="file" id="edit_gallery_files" class="form-control" multiple accept="image/*,application/pdf">
               </div>

              <div class="col-md-3">
                <label class="mb-25">Typ</label>
                <select id="edit_gallery_type" class="form-control">
                  <option value="" selected>Alle</option>
                  <option value="image">Bilder</option>
                  <option value="pdf">PDF</option>
                </select>
              </div>

              <div class="col-md-3">
                <button type="button" class="btn btn-primary btn-block" id="btnEditGalleryUpload">
                  <i class="feather icon-upload mr-50"></i> Upload
                </button>
              </div>

              <div class="col-md-12">
                <label class="mb-25">Suchen</label>
                <input type="text" id="edit_gallery_search" class="form-control" placeholder="Suchen nach Dateiname...">
              </div>
            </div>

            <div class="mt-2" id="editGalleryEmpty" style="display:none;">
              <div class="alert alert-secondary mb-0" style="border-radius:10px;">
                Noch keine Dateien vorhanden.
              </div>
            </div>

            <div class="row mt-2" id="editGalleryGrid"></div>

          </div>
        </div>

      </div>
    </div>

    {{-- Footer --}}
    <div class="cp-footer" style="display:flex; justify-content:space-between; align-items:center;">
      <div>
        <button type="button" id="btnToggleEditLock" class="btn btn-outline-secondary" title="Bearbeitung entsperren">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
          </svg>
        </button>
      </div>

      <div style="display:flex; gap:10px;">
        <button type="button" class="btn btn-secondary" data-cp-close="editProductOverlay">Schließen</button>
        <button type="button" id="btnEditSave" class="btn btn-primary" disabled>
          <i class="feather icon-save mr-50"></i> Speichern
        </button>
      </div>
    </div>

  </div>
</div>

<!-- =========================
SERIALS (CUSTOM) – already custom
========================= -->
<div id="serialsOverlay" class="cp-overlay" aria-hidden="true">
  <div class="cp-dialog" role="dialog" aria-modal="true" aria-labelledby="snTitle">
    <div class="cp-header">
      <h5 id="snTitle" class="cp-title">Seriennummern verwalten</h5>
      <button type="button" class="cp-close" id="snCloseBtn" aria-label="Close">&times;</button>
    </div>

    <div class="cp-body">
      <div class="sn-info">
        <strong id="serialsModalProductName">Produkt</strong>
        <span class="sn-sep">–</span>
        <span>Anzahl: <strong id="serialsModalCount">1</strong></span>
      </div>

      <div class="sn-table-wrap">
        <table class="sn-table">
          <thead>
            <tr>
              <th style="width:90px;">Pos.</th>
              <th>Seriennummer</th>
            </tr>
          </thead>
          <tbody id="serialsModalBody"></tbody>
        </table>
      </div>
    </div>

    <div class="cp-footer">
      <button type="button" class="btn btn-secondary" id="snCancelBtn">Schließen</button>
      <button type="button" class="btn btn-primary" id="btnSerialsModalSave">Übernehmen</button>
    </div>
  </div>
</div>

  <!-- ===== Add Products Modal (custom) ===== -->

  <div class="cmodal" id="addCustomerProductModal" aria-hidden="true">
  <div class="cmodal__backdrop" data-modal-close></div>

  <div class="cmodal__dialog" role="dialog" aria-modal="true" aria-labelledby="addCustomerProductTitle">
    <form id="addCustomerProductForm" action="{{ route('lead.products.save') }}" method="POST">
      @csrf
      <input type="hidden" name="customer_id" id="modal_customer_id">
      <input type="hidden" name="alternative_id" id="modal_alternative_id">

      <div class="cmodal__header bg-primary text-white">
        <h5 class="cmodal__title" id="addCustomerProductTitle">Produkt hinzufügen</h5>
        <button type="button" class="cmodal__close text-white" data-modal-close aria-label="Close">×</button>
      </div>

      <div class="cmodal__body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Produkt</th>
              <th>Dienstleistung</th>
              <th>Abteilung</th>
              <th>Innendienst</th>
              <th>Außendienst</th>
              <th>Interesse</th>
              <th>Realisierungszeit</th>
              <th>Aktion</th>
            </tr>
          </thead>
          <tbody id="existingProductRows"></tbody>
          <tbody id="modalNewRows"></tbody>
        </table>

        <button type="button" class="btn btn-sm btn-success" id="modalAddRow">+ Neue Zeile</button>
      </div>

      <div class="cmodal__footer">
        <button type="button" class="btn btn-outline-secondary" data-modal-close>Abbrechen</button>
        <button type="submit" class="btn btn-primary">Speichern</button>
      </div>
    </form>
  </div>
</div>

<!-- ===== Edit Product Modal (custom) ===== -->
<div class="cmodal" id="editCustomerProduct" aria-hidden="true">
  <div class="cmodal__backdrop" data-modal-close></div>

  <div class="cmodal__dialog" role="dialog" aria-modal="true" aria-labelledby="editCustomerProductTitle">
    <form id="editCustomerProductForm" method="POST" action="{{ route('lead.products.update') }}">
      @csrf
      <input type="hidden" name="id" id="edit_product_id">

      <div class="cmodal__header bg-warning text-white">
        <h5 class="cmodal__title" id="editCustomerProductTitle">Produkt bearbeiten</h5>
        <button type="button" class="cmodal__close text-white" data-modal-close aria-label="Close">×</button>
      </div>

      <div class="cmodal__body">
        <div class="form-group">
          <label>Produkt</label>
          <select name="product_id" id="edit_product" class="form-control">
            <option value="">Wählen...</option>
            @foreach($new_products as $p)
              <option value="{{ $p->id }}">{{ $p->article_group }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label>Dienstleistung</label>
          <select name="service_id" id="edit_service" class="form-control">
            <option value="">Wählen...</option>
          </select>
        </div>

        <div class="form-group">
          <label>Abteilung</label>
          <select name="department_id" id="edit_department" class="form-control">
            <option value="">Wählen...</option>
            @foreach($departments as $d)
              <option value="{{ $d->id }}">{{ $d->department_name }}</option>
            @endforeach
          </select>
        </div>

        <div class="form-group">
          <label>Innendienst</label>
          <select name="employee_id" id="edit_employee" class="form-control">
            <option value="">Wählen...</option>
          </select>
        </div>

        <div class="form-group">
          <label>Außendienst</label>
          <select name="field_employee" id="edit_field_employee" class="form-control">
            <option value="">Wählen...</option>
          </select>
        </div>

        <div class="form-group">
          <label>Interesse</label>
          <select name="interest" id="edit_interest" class="form-control"></select>
        </div>

        <div class="form-group">
          <label>Realisierungszeit</label>
          <select name="realization_time" id="edit_realization_time" class="form-control"></select>
        </div>
      </div>

      <div class="cmodal__footer">
        <button type="button" class="btn btn-secondary" data-modal-close>Abbrechen</button>
        <button type="submit" class="btn btn-warning">Aktualisieren</button>
      </div>
    </form>
  </div>
</div>

<div id="objDrawerRoot">
    <div id="objDrawerBackdrop"></div>

    <aside id="objDrawerPanel">
        <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom">
            <div>
                <h5 class="mb-0 font-weight-bold">Produkte zu Objekten zuordnen</h5>
                <small id="drawerCustomerLine" class="text-muted"></small>
            </div>

            <div class="d-flex gap-2">
                <button id="btnDrawerOpenCreate" class="btn btn-dark btn-sm mr-2">
                    <i class="feather icon-plus"></i> Neues Objekt
                </button>
                <button id="btnDrawerClose" class="btn btn-light btn-sm">
                    Schließen
                </button>
            </div>
        </div>

        <div id="drawerCreatePanel" class="border-bottom bg-light p-3 drawer-hidden">
            <div class="row">
                <div class="col-md-6">
                    <div class="row mb-2">
                        <div class="col-6">
                            <label class="small text-muted">Objektname</label>
                            <input id="co_object_name" class="form-control form-control-sm" placeholder="z.B. EFH Musterstraße" />
                        </div>
                        <div class="col-6">
                            <label class="small text-muted">Anfrage Datum</label>
                            <input id="co_request_date" type="date" class="form-control form-control-sm" />
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="small text-muted">Adresse (Google Maps)</label>
                        <input id="co_address_search" class="form-control form-control-sm" placeholder="Adresse suchen…" />
                        <input id="co_full_address" type="hidden" />
                        <input id="co_street" type="hidden" />
                        <input id="co_postcode" type="hidden" />
                        <input id="co_city" type="hidden" />
                        <input id="co_lat" type="hidden" />
                        <input id="co_lon" type="hidden" />
                    </div>
                    <div class="row mb-2">
                        <div class="col-6">
                            <label class="small text-muted">Ziel</label>
                            <input id="co_objective" class="form-control form-control-sm" placeholder="z.B. Angebot" />
                        </div>
                        <div class="col-6">
                            <label class="small text-muted">Notiz</label>
                            <input id="co_note" class="form-control form-control-sm" placeholder="Optional" />
                        </div>
                    </div>
                    <div class="d-flex align-items-center mt-3">
                        <button id="btnCreateObjectSave" class="btn btn-success btn-sm mr-2">Speichern</button>
                        <button id="btnCreateObjectCancel" class="btn btn-secondary btn-sm">Abbrechen</button>
                        <span id="createObjectMsg" class="ml-2 small text-muted"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div id="co_map" style="width: 100%; height: 260px; border-radius: 8px; border: 1px solid #ccc;"></div>
                    <div class="mt-1 small text-muted">Marker verschieben oder in Karte klicken.</div>
                </div>
            </div>
        </div>

        <div class="flex-grow-1 p-3" style="overflow-y: auto;">
            <div id="drawerLoading" class="text-center text-muted mt-5">
                <div class="spinner-border text-primary mb-2" role="status"></div>
                <div>Lade Daten...</div>
            </div>

            <div id="drawerObjectsGrid" class="drawer-hidden">
                <div class="row" id="drawerObjectsCols"></div>
            </div>
        </div>
    </aside>
</div>


@endsection

@section('script')

 
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script src="https://unpkg.com/feather-icons"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
<script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
<script src="https://unpkg.com/cropperjs/dist/cropper.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/de.js'></script>
 
 

<script
  src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo&libraries=places"
  async defer></script>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script>
window.__OBJ_MODAL = {
  endpoints: {
    tree: "{{ route('ajax.customer.objectProductTree', ['customer' => '___ID___']) }}",
    createObject: "{{ route('ajax.customer.createObject', ['customer' => '___ID___']) }}",
    moveProduct: "{{ route('ajax.leadProduct.move', ['leadProduct' => '___ID___']) }}",

    // ✅ NEW
    deleteObject: "{{ route('ajax.object.delete', ['object' => '___ID___']) }}",
    deleteProduct: "{{ route('ajax.leadProduct.delete', ['leadProduct' => '___ID___']) }}",
  },
  phaseLabels: {
    complete: 'Komplett',
    montage: 'Montage',
    repair: 'Reparatur',
    maintenance: 'Wartung',
    offer: 'Angebot',
    product: 'Produkt',
  }
};

</script>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        feather.replace();
    });
</script> 

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.getElementById('customerSidebar');
        const main = document.getElementById('mainContent');
        let sidebarManuallyExpanded = false;


     

        // ✅ Force-close all collapses on initial load
        document.querySelectorAll('.product-list, .sub-nav').forEach(el => {
            el.classList.remove('show');
            el.setAttribute('aria-expanded', 'false');
        });

        function collapseAll() {
            document.querySelectorAll('.product-list').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.sub-nav').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('.project-link').forEach(el => el.classList.remove('active'));
        }

        function minimizeSidebar() {
            sidebar.classList.add('minimized');
            document.querySelector('.contentStation')?.classList.add('expanded');
            collapseAll();
        }

        function expandSidebar() {
            sidebar.classList.remove('minimized');
            document.querySelector('.contentStation')?.classList.remove('expanded');
            sidebarManuallyExpanded = true;
            feather.replace();
        }

        function autoToggleSidebar() {
            if (!sidebarManuallyExpanded) {
                window.innerWidth < 992 ? minimizeSidebar() : expandSidebar();
            }
        }

        autoToggleSidebar();
        
        window.addEventListener('resize', () => {
            autoToggleSidebar();
            hideRightPanelOnMobile();
        });



        function hideRightPanelOnMobile() {
            const rightPanel = document.querySelector('.right-panel');
            const mainContent = document.querySelector('.contentStation');

            if (window.innerWidth < 992) {
                if (rightPanel) rightPanel.classList.add('right-hidden');
                if (mainContent) mainContent.classList.remove('main-hidden');
            } else {
                if (rightPanel) rightPanel.classList.remove('right-hidden');
            }
        }
        hideRightPanelOnMobile();

        window.togglecustomerSidebar = () => {
            sidebar.classList.contains('minimized') ? expandSidebar() : minimizeSidebar();
        };

        document.addEventListener('click', function (e) {
            const shouldExpand =
                e.target.closest('.object-header') ||
                e.target.closest('.project-link') ||
                e.target.closest('.sub-nav button') ||
                e.target.closest('.dashboard-btn');

            if (sidebar.classList.contains('minimized') && shouldExpand) {
                expandSidebar();
            }
        });


        window.toggleObject = (id) => {
            const target = document.getElementById(id);
            if (!target) return;

            const isOpen = target.style.display === 'block';

            // Collapse all other objects
            document.querySelectorAll('.product-list').forEach(el => el.style.display = 'none');

            if (!isOpen) {
                target.style.display = 'block';
                console.log(`[Object] ${id} → now open`);
            } else {
                target.style.display = 'none';
                console.log(`[Object] ${id} → now closed`);
            }

            // Reset all sub-sections
            document.querySelectorAll('.sub-nav').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.project-link').forEach(el => el.classList.remove('active'));
        };

        window.toggleProduct = (productKey) => {
            const subNav = document.getElementById(productKey);
            const clicked = document.querySelector(`.project-link[data-product-key="${productKey}"]`);
            const parentId = productKey.match(/product(\d+)_\d+/)?.[1];
            const parentObjectList = document.getElementById(`object${parentId}`);
            const title = document.getElementById('note_title');
            if (title) title.textContent = 'NOTIZEN';

            if (!subNav || !clicked || !parentObjectList) return;

            const isOpen = subNav.style.display === 'block';

            // Collapse all sub-navs
            document.querySelectorAll('.sub-nav').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.project-link').forEach(el => el.classList.remove('active'));

            if (!isOpen) {
                parentObjectList.style.display = 'block';
                subNav.style.display = 'block';
                clicked.classList.add('active');
                console.log(`[Product] ${productKey} was closed → now open`);

                const customerId = clicked.dataset.objectCustomerId;
                const alternativeId = clicked.dataset.objectAlternativeId;
                const productId = clicked.dataset.objectProduct;
                const noteList = document.getElementById('note-list');

                if (noteList && customerId && alternativeId && productId) {
                    noteList.innerHTML = '<div class="text-muted">Lade Notizen...</div>';
                    fetch(`/customer-notes/${customerId}/${alternativeId}/${productId}`)
                        .then(res => res.text())
                        .then(html => {
                            noteList.innerHTML = html;
                            feather.replace();
                            if (typeof initNoteListeners === 'function') initNoteListeners();
                        })
                        .catch(() => {
                            noteList.innerHTML = '<div class="text-danger">Fehler beim Laden.</div>';
                        });
                }
            } else {
                console.log(`[Product] ${productKey} was open → now closed`);
            }
        };
 
        // ✅ Auto-bind click to all project links
        document.querySelectorAll('.project-link[data-product-key]').forEach(link => {
            link.addEventListener('click', function () {
                const productKey = this.dataset.productKey;
                toggleProduct(productKey);
            });
        });
    });


</script> 
<!-- Maximize Toggle Buttons  -->

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('mainContentToggle');
        const layout = document.querySelector('.layout');
        const sidebar = document.getElementById('customerSidebar');
        const rightPanel = document.querySelector('.right-panel');
        const content = document.querySelector('.contentStation');
        const icon = btn.querySelector('i');

        btn.addEventListener('click', () => {
            const isFullscreen = layout.classList.toggle('main-fullscreen-mode');

            // Toggle visibility
            sidebar.style.display = isFullscreen ? 'none' : '';
            rightPanel.style.display = isFullscreen ? 'none' : '';
            content.classList.toggle('main-fullscreen', isFullscreen);

            // Change icon class
            icon.classList.remove('icon-maximize-2', 'icon-minimize-2');
            icon.classList.add(isFullscreen ? 'icon-minimize-2' : 'icon-maximize-2');
        });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const toggleBtn = document.getElementById('btnToggleRightPanelFullscreen');
        const rightPanel = document.querySelector('.right-panel');
        const mainContent = document.querySelector('.contentStation');
        const sidebar = document.getElementById('customerSidebar');
        const icon = toggleBtn.querySelector('i');

        toggleBtn.addEventListener('click', () => {
            const isFullscreen = rightPanel.classList.toggle('fullscreen');

            // Toggle visibility of side and main content
            sidebar.style.display = isFullscreen ? 'none' : '';
            mainContent.style.display = isFullscreen ? 'none' : '';

            // Toggle icon
            icon.classList.remove('icon-maximize-2', 'icon-minimize-2');
            icon.classList.add(isFullscreen ? 'icon-minimize-2' : 'icon-maximize-2');
        });
    });
</script>

<!-- Maximize Toggle Buttons  --> 
<script>
    function loadSectionPartial(customer_id, alternative_id, product_id, section) {
        const url = `/customer/partial/${customer_id}/${alternative_id}/${product_id}/${section}`;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Fehler beim Laden des Abschnitts');
                return response.text();
            })
            .then(html => {
                document.getElementById('mainContent').innerHTML = html;

                // Optional: load extra sub-sections (partial wrappers)
            })
            .catch(error => {
                document.getElementById('mainContent').innerHTML =
                    `<div class="alert alert-danger">${error.message}</div>`;
            });
    }
    
</script>
 

<script>
  function showTab(step) {
      document.querySelectorAll('.tab-pane').forEach((pane, idx) => {
          pane.classList.remove('active');
          if (idx === step - 1) pane.classList.add('active');
      });

      document.querySelectorAll('.wizard-step').forEach((stepEl, idx) => {
          stepEl.classList.remove('active');
          if (idx === step - 1) stepEl.classList.add('active');
      });
      updateProgressCounts();

  }


    function navigateTab(direction) {
        const steps = document.querySelectorAll('.wizard-step');
        let currentIndex = [...steps].findIndex(step => step.classList.contains('active'));
        let nextIndex = currentIndex + direction;
        if (nextIndex >= 0 && nextIndex < steps.length) {
            showTab(nextIndex + 1);
        }
    }

    function updateProgressCounts() {
        const forms = document.querySelectorAll('form.partial-form');

        forms.forEach(form => {
            const section = form.dataset.section;
            const counterEl = document.getElementById(`step${getStepIndex(section)}-count`);
            if (!counterEl) return;

            const tabPane = form.closest('.tab-pane');
            const wasHidden = tabPane && !tabPane.classList.contains('active');

            // 🧠 Temporarily show hidden tab to count inputs
            if (wasHidden) {
                tabPane.classList.add('temporary-visible');
                tabPane.classList.add('active');
            }

            const inputs = form.querySelectorAll('input, select, textarea');
            let total = 0;
            let filled = 0;

            inputs.forEach(input => {
                const type = input.type;
                const isHidden = input.offsetParent === null; // skip visually hidden (e.g., display: none)
                if (input.name === '_token' || isHidden) return;

                total++;

                if (['checkbox', 'radio'].includes(type)) {
                    if (input.checked) filled++;
                } else {
                    const val = input.value?.trim();
                    if (val !== '') filled++;
                }
            });

            counterEl.textContent = `(${filled}/${total})`;

            // 🔄 Re-hide tab if it was not active before
            if (wasHidden) {
                tabPane.classList.remove('active');
                tabPane.classList.remove('temporary-visible');
            }
        });

        function getStepIndex(section) {
            const map = ['object_data', 'roof_info', 'heating_info', 'e_mobility', 'energy_usage'];
            return map.indexOf(section) + 1;
        }
    }

 
        function loadFullAlternativeObject(button) {
                const customerId    = button.dataset.customerId;
                const alternativeId = button.dataset.alternativeId;
                const productId     = button.dataset.productId;

                const url = `/customer/alternative/partials/${customerId}/${alternativeId}/${productId}/objekt`;

                const mainContent = document.getElementById('mainContent');
                mainContent.innerHTML = `<div class="text-center py-4">Lade Objektdaten...</div>`;

                fetch(url)
                    .then(response => {
                        if (!response.ok) throw new Error('Fehler beim Laden des Objekts');
                        return response.text();
                    })
                    .then(html => {
                        // ✅ Inject the new content
                        mainContent.innerHTML = html;

                        // ✅ Replace feather icons
                        if (typeof feather !== 'undefined') feather.replace();

                        // ✅ Re-initialize power calculator
                        initPowerCalculatorWithIDs(mainContent);

                        // ✅ Recalculate progress indicators
                        updateProgressCounts();
                    })
                    .catch(error => {
                        mainContent.innerHTML = `<div class="alert alert-danger">${error.message}</div>`;
                        console.error('❌ Fehler beim Laden:', error);
                    });
            }



    function initPowerCalculatorWithIDs(context = document) {
        const $household    = $(context).find('#power_household_input');
        const $heatpump     = $(context).find('#power_heatpump_input');
        const $electricCar  = $(context).find('#power_electric_car_input');
        const $other        = $(context).find('#power_other_input');
        const $total        = $(context).find('#power_total');        // Display for user (with comma)
        const $totalHidden  = $(context).find('#power_total_hidden');  // Actual value (with dot)

        function parseInput($el) {
            const val = $el.val().trim().replace(',', '.');
            const num = parseFloat(val);
            if (isNaN(num) || num < 0) {
                $el.addClass('is-invalid');
                return 0;
            } else {
                $el.removeClass('is-invalid');
                return num;
            }
        }

        function updateTotal() {
            const h  = parseInput($household);
            const wp = parseInput($heatpump);
            const ev = parseInput($electricCar);
            const o  = parseInput($other);

            const total = h + wp + ev + o;

            // 👁️ Display total with comma
            $total.val(total.toFixed(2).replace('.', ','));

            // 🧠 Hidden input for DB (with dot)
            $totalHidden.val(total.toFixed(2));

            // ℹ️ kWh / year
            let $year = $(context).find('#power_total_year');
            if (!$year.length) {
                $year = $('<small id="power_total_year" class="form-text text-muted"></small>').insertAfter($total);
            }
            const yearly = total * 365;
            $year.text('≈ ' + yearly.toLocaleString('de-DE') + ' kWh / Jahr');
        }


        $household.add($heatpump).add($electricCar).add($other)
            .off('input.powercalc')
            .on('input.powercalc', updateTotal);

        updateTotal();
    }


    $(document).ready(function () {
        // 🔁 Initial progress calculation when page loads
        updateProgressCounts();

        // 🔄 Recalculate on any form input change
        $(document).on('input change', 'form.partial-form input, form.partial-form select, form.partial-form textarea', updateProgressCounts);
    });



</script>
 
<script> 
    document.addEventListener('DOMContentLoaded', function () {
        const electricCarSelect = document.getElementById('electric_car');
        const electricCarPlan = document.getElementById('electric_car_plan');

        if (electricCarSelect) {
            electricCarSelect.addEventListener('change', function () {
                if (this.value === 'Geplant') {
                    electricCarPlan.style.display = 'block';
                } else {
                    electricCarPlan.style.display = 'none';
                }
            });
        }
    }); 
</script>


<!-- Saving the alternative data : -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.body.addEventListener('submit', function (e) {
        if (e.target.classList.contains('partial-form')) {
            e.preventDefault();

            const form = e.target;
            const section = form.dataset.section;
            const id = form.dataset.id;

            const formData = new FormData(form);
            formData.append('id', id);

            fetch(`/new_lead_profile/alternative/object/save`, {
              method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Fehler beim Speichern von ' + section);
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Gespeichert',
                    text: `Abschnitt "${section}" erfolgreich gespeichert.`
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    text: error.message
                });
            });
        }
    });
});
</script> 

<!-- Loading the checklists  -->
<script>
    function loadChecklist(button) {
        const customerId = button.dataset.customerId;
        const alternativeId = button.dataset.alternativeId;
        const productId = button.dataset.productId;
        const leadProductListId = button.dataset.productListId;

        const container = document.getElementById('mainContent');
        container.innerHTML = '<div class="p-3 text-center">Checkliste wird geladen...</div>';

        fetch('/lead-product-checklist/init', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                customer_id: customerId,
                alternative_id: alternativeId,
                product_id: productId,
                lead_product_list_id: leadProductListId,
            })
        })
        .then(async res => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                container.innerHTML = `<pre style="color:red">${text}</pre>`;
                throw e;
            }
        })
        .then(data => {
            if (data.success) {
                container.innerHTML = `
                    <form id="customerChecklistForm" class="form-preview bg-white p-4">
                        <input type="hidden" name="lead_product_list_id" value="${leadProductListId}">
                        <input type="hidden" name="customer_id" value="${customerId}">
                        <input type="hidden" name="alternative_id" value="${alternativeId}">
                        <input type="hidden" name="product_id" value="${productId}">
                        ${data.html}
                        <button type="submit" class="btn btn-primary mt-3">Speichern</button>
                    </form>
                `;

                attachChecklistEvents();
            } else {
                container.innerHTML = '<div class="alert alert-danger">Fehler beim Laden.</div>';
            }
        })
        .catch(err => {
            console.error('Fetch failed:', err);
            container.innerHTML = '<div class="alert alert-danger">Ein Fehler ist aufgetreten.</div>';
        });
    }

    function attachChecklistEvents() {
        const form = document.querySelector('#customerChecklistForm');
        if (!form) return;

        form.addEventListener('submit', submitChecklist);
        form.addEventListener('input', () => {
            evaluateFormulas();
            updateProgressBars();
        });

        evaluateFormulas();
        updateProgressBars();
    }

    function submitChecklist(e) {
        e.preventDefault();

        const form = document.getElementById('customerChecklistForm');
        const filledValues = {};
        const leadProductListId = form.querySelector('[name="lead_product_list_id"]').value;
        const customerId = form.querySelector('[name="customer_id"]').value;
        const alternativeId = form.querySelector('[name="alternative_id"]').value;
        const productId = form.querySelector('[name="product_id"]').value;

        form.querySelectorAll('input, select, textarea').forEach(input => {
            if (!input.name || input.classList.contains('formula-field')) return;
            const name = input.name.replace(/\[\]$/, '');

            if (input.type === 'checkbox') {
                filledValues[name] = input.checked ? 1 : 0;
            } else if (input.name.endsWith('[]')) {
                if (!Array.isArray(filledValues[name])) filledValues[name] = [];
                filledValues[name].push(input.value);
            } else {
                filledValues[name] = input.value;
            }
        });

        fetch('/lead-product-checklist/save', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                lead_product_list_id: leadProductListId,
                filled_values: filledValues,
                customer_id: customerId,
                alternative_id: alternativeId,
                product_id: productId
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Gespeichert', 'Die Daten wurden erfolgreich gespeichert.', 'success');
            } else {
                Swal.fire('Fehler', data.message || 'Daten konnten nicht gespeichert werden.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Fehler', 'Ein Fehler ist aufgetreten.', 'error');
        });
    }

function evaluateFormulas() {
    const values = {};

    document.querySelectorAll('#customerChecklistForm input, #customerChecklistForm select, #customerChecklistForm textarea')
        .forEach(input => {
            if (!input.name || input.classList.contains('formula-field')) return;
            values[input.name] = input.type === 'checkbox' ? (input.checked ? 1 : 0) : input.value;
        });

    document.querySelectorAll('.formula-field').forEach(field => {
        const formula = field.dataset.formula;
        const result = evaluateFormula(formula, values);
        field.value = isNaN(result) ? 'Fehler' : result;
    });
}

function evaluateFormula(formula, values) {
    try {
        const fns = { add, sub, mul, div, round, min, max, toNum };
        const valKeys = Object.keys(values).filter(key => /^[a-zA-Z_][a-zA-Z0-9_]*$/.test(key));
        const valVals = valKeys.map(k => toNum(values[k]));

        const fnKeys = Object.keys(fns);
        const fnVals = Object.values(fns);

        const fn = new Function(...fnKeys, ...valKeys, `return ${formula}`);
        return fn(...fnVals, ...valVals);
    } catch (e) {
        console.warn('Formula error:', formula, e);
        return 'Fehler';
    }
}

function toNum(val) {
    return val === '' || val == null || isNaN(val) ? 0 : Number(val);
}
function add(a, b) { return toNum(a) + toNum(b); }
function sub(a, b) { return toNum(a) - toNum(b); }
function mul(a, b) { return toNum(a) * toNum(b); }
function div(a, b) { const d = toNum(b); return d === 0 ? 0 : toNum(a) / d; }
function round(v, p = 0) { return Math.round(toNum(v) * 10 ** p) / 10 ** p; }
function min(...args) { return Math.min(...args.map(toNum)); }
function max(...args) { return Math.max(...args.map(toNum)); }

function updateProgressBars() {
    document.querySelectorAll('.accordion-section').forEach(section => {
        const inputs = section.querySelectorAll('input, select, textarea');
        let total = 0;
        let filled = 0;

        inputs.forEach(input => {
            if (!input.name || input.classList.contains('formula-field')) return;
            total++;
            const isFilled = input.type === 'checkbox' ? input.checked : input.value !== '';
            if (isFilled) filled++;
        });

        const percent = total > 0 ? Math.round((filled / total) * 100) : 0;
        const bar = section.querySelector('.progress-bar');

        if (bar) {
            bar.style.width = percent + '%';
            bar.setAttribute('aria-valuenow', percent);
            bar.innerText = percent + '%';
        }
    });
}
</script> 
  
<!-- Document and PIcutre :start -->
 <script>
 /**
 * Document Management Module
 * Handles Tabs, Filtering, View Toggling, and AJAX CRUD operations for documents.
 */
(() => {
  "use strict";

  // --- Constants & Configuration ---
  const CONFIG = {
    ROOT_ID: "docsShell",
    LS_VIEW_KEY: "docs.view.mode",
    ENDPOINTS: {
      load: "/document/load",
      delete: (id) => `/document/delete/${id}`,
      rename: "/document/rename",
    },
    TAB_LINKS: "#galleryTabs .nav-link",
    TAB_PANES: "#galleryTabsContent .tab-pane",
    SEARCH_INPUT: "#searchImage",
    STAGE_FILTER: "#stageFilter",
  };

  // --- State Management ---
  const state = {
    glb: null,
    lastActiveButton: null,
  };

  // --- Utility Functions ---
  const getCsrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || "";
  const $ = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  const getActivePane = (root) => 
    $(`${CONFIG.TAB_PANES}.active`, root) || $("#allTab", root);

  // --- Core Functionality ---

  /**
   * Switches between Grid and List view
   */
  const setView = (view, root = document) => {
    const shell = document.getElementById(CONFIG.ROOT_ID);
    if (!shell) return;

    const normalizedView = view === "list" ? "list" : "grid";
    shell.dataset.view = normalizedView;
    localStorage.setItem(CONFIG.LS_VIEW_KEY, normalizedView);

    $$(".view-btn", root).forEach(btn => {
      btn.classList.toggle("is-active", btn.dataset.view === normalizedView);
    });
  };

  /**
   * Filters the visible items based on search keyword and stage selection
   */
  const applyFilters = (root = document) => {
    const keyword = ($(CONFIG.SEARCH_INPUT, root)?.value || "").trim().toLowerCase();
    const stage = ($(CONFIG.STAGE_FILTER, root)?.value || "").trim();
    const pane = getActivePane(root);
    
    if (!pane) return;

    $$(".gallery-item", pane).forEach(item => {
      const name = (item.dataset.name || "").toLowerCase();
      const type = (item.dataset.type || "").toLowerCase();
      const dateRaw = item.dataset.date || "";
      const date = dateRaw ? new Date(dateRaw).toLocaleDateString("de-DE").toLowerCase() : "";

      const matchesSearch = !keyword || `${name} ${type} ${date}`.includes(keyword);
      const matchesStage = !stage || item.dataset.stage === stage;

      item.style.display = (matchesSearch && matchesStage) ? "" : "none";
    });
  };

  /**
   * Initializes GLightbox instance
   */
  const initLightbox = (root = document) => {
    if (typeof GLightbox === "undefined") return;
    
    if (state.glb) {
      state.glb.destroy();
    }

    state.glb = GLightbox({
      selector: `#${CONFIG.ROOT_ID} .glightbox`,
      loop: true,
      openEffect: "zoom",
      closeEffect: "fade"
    });
  };

  // --- UI Initializers ---

  const initTabs = (root) => {
    const tabs = $$(CONFIG.TAB_LINKS, root);
    const panes = $$(CONFIG.TAB_PANES, root);

    tabs.forEach(tab => {
      tab.addEventListener("click", (e) => {
        e.preventDefault();
        const targetId = tab.getAttribute("data-tab") || tab.getAttribute("href");
        const targetPane = $(targetId, root);

        if (!targetPane) return;

        tabs.forEach(t => t.classList.remove("active"));
        panes.forEach(p => p.classList.remove("show", "active"));

        tab.classList.add("active");
        targetPane.classList.add("show", "active");

        applyFilters(root);
        initLightbox(root);
      });
    });
  };

  const initControls = (root) => {
    // View Toggle Buttons
    $$(".view-btn", root).forEach(btn => {
      btn.onclick = () => setView(btn.dataset.view, root);
    });

    // Search and Stage Filter
    const searchInput = $(CONFIG.SEARCH_INPUT, root);
    const stageFilter = $(CONFIG.STAGE_FILTER, root);

    if (searchInput) searchInput.oninput = () => applyFilters(root);
    if (stageFilter) stageFilter.onchange = () => applyFilters(root);
  };

  // --- Public API / Window Methods ---

  /**
   * Loads documents from server via AJAX
   */
  window.loadDocuments = async function(button, opts = {}) {
    const container = document.getElementById("mainContent");
    if (!container || !button.dataset) return;

    state.lastActiveButton = button;
    const { customerId, alternativeId, productId, productListId } = button.dataset;

    // Capture current UI state to restore after fetch
    const shell = document.getElementById(CONFIG.ROOT_ID);
    const currentUiState = {
      tab: opts.keepTab !== false ? ($(`${CONFIG.TAB_LINKS}.active`)?.getAttribute("data-tab") || "#allTab") : "#allTab",
      search: opts.keepFilters !== false ? ($(CONFIG.SEARCH_INPUT)?.value || "") : "",
      stage: opts.keepFilters !== false ? ($(CONFIG.STAGE_FILTER)?.value || "") : "",
      view: opts.keepView !== false ? (shell?.dataset.view || localStorage.getItem(CONFIG.LS_VIEW_KEY) || "grid") : "grid"
    };

    container.innerHTML = `<div class="p-3 text-center"><i class="fa fa-spinner fa-spin"></i> Dokumente werden geladen...</div>`;

    try {
      const response = await fetch(CONFIG.ENDPOINTS.load, {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": getCsrf() },
        body: JSON.stringify({ customer_id: customerId, alternative_id: alternativeId, product_id: productId, product_list_id: productListId })
      });

      const html = await response.text();
      container.innerHTML = html;

      // Update hidden product ID if necessary
      const productHiddenInput = document.getElementById("image_product");
      if (productHiddenInput) productHiddenInput.value = productId;

      const newRoot = document.getElementById(CONFIG.ROOT_ID) || document;

      // Re-initialize UI
      initTabs(newRoot);
      initControls(newRoot);
      initLightbox(newRoot);
      if (typeof initDropzone === "function") initDropzone(button);

      // Restore UI State
      setView(currentUiState.view, newRoot);
      if ($(CONFIG.SEARCH_INPUT, newRoot)) $(CONFIG.SEARCH_INPUT, newRoot).value = currentUiState.search;
      if ($(CONFIG.STAGE_FILTER, newRoot)) $(CONFIG.STAGE_FILTER, newRoot).value = currentUiState.stage;

      const savedTab = $(`${CONFIG.TAB_LINKS}[data-tab="${currentUiState.tab}"], ${CONFIG.TAB_LINKS}[href="${currentUiState.tab}"]`, newRoot);
      if (savedTab) savedTab.click(); else applyFilters(newRoot);

    } catch (error) {
      console.error("Fetch error:", error);
      container.innerHTML = `<div class="text-danger p-3">Fehler beim Laden der Dokumente</div>`;
    }
  };

  /**
   * Deletes a specific document
   */
  window.deleteDocument = async function(id, element) {
    const result = await Swal.fire({
      title: "Löschen bestätigen",
      text: "Willst du dieses Dokument wirklich löschen?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Ja, löschen",
      cancelButtonText: "Abbrechen"
    });

    if (!result.isConfirmed) return;

    try {
      const response = await fetch(CONFIG.ENDPOINTS.delete(id), {
        method: "DELETE",
        headers: { "X-CSRF-TOKEN": getCsrf() }
      });
      const data = await response.json();

      if (!data.success) throw new Error();

      element?.closest(".gallery-item")?.remove();
      Swal.fire({ icon: "success", title: "Gelöscht", timer: 900, showConfirmButton: false });
    } catch (error) {
      Swal.fire({ icon: "error", title: "Fehler", text: "Löschen fehlgeschlagen." });
    }
  };

  /**
   * Renames a specific document
   */
  window.renameDocument = async function(id, newName) {
    try {
      const response = await fetch(CONFIG.ENDPOINTS.rename, {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": getCsrf() },
        body: JSON.stringify({ id, image_name: newName })
      });
      const data = await response.json();

      if (!data.success) throw new Error();
      Swal.fire({ icon: "success", title: "Umbenannt", timer: 900, showConfirmButton: false });
    } catch (error) {
      Swal.fire({ icon: "error", title: "Fehler", text: "Fehler beim Umbenennen" });
    }
  };

  // Add this inside your script block so the HTML button can "see" it
   window.editDocumentDetails = function(id, currentName, currentStage) {
        // 1. Define the types for the dropdown
        const types = {
            'customer': 'Kunde',
            'montage': 'Montage',
            'Reklamation': 'Reklamation',
            'Rechnung': 'Rechnung',
            'Auftrag': 'Auftrag',
            'AuftragBeshtitgung': 'Auftragsbestätigung',
            'Angebot': 'Angebot',
            'Wartung': 'Wartung',
            'Ticket': 'Ticket',
            'end': 'Abgeschlossen',
            'Other': 'Sonstiges'
        };

        let optionsHtml = '';
        for (let key in types) {
            optionsHtml += `<option value="${key}" ${currentStage === key ? 'selected' : ''}>${types[key]}</option>`;
        }

        // 2. Open the SweetAlert2 Modal
        Swal.fire({
            title: 'Datei bearbeiten',
            html: `
                <div style="text-align:left;">
                    <label style="font-weight:bold; font-size:12px;">Dateiname</label>
                    <input id="swal-edit-name" class="swal2-input" value="${currentName}" style="margin-top:5px; width:100%;">
                    <label style="font-weight:bold; font-size:12px; margin-top:10px; display:block;">Kategorie / Typ</label>
                    <select id="swal-edit-stage" class="swal2-input" style="display:block; width:100%; margin-top:5px;">
                        ${optionsHtml}
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Speichern',
            cancelButtonText: 'Abbrechen',
            preConfirm: () => {
                const name = document.getElementById('swal-edit-name').value;
                const stage = document.getElementById('swal-edit-stage').value;
                if (!name) return Swal.showValidationMessage('Name darf nicht leer sein');
                return { id, name, stage };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // 3. Send the Update request
                fetch("{{ route('document.updateDetails') }}", {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id: result.value.id,
                        image_name: result.value.name,
                        stage: result.value.stage
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Aktualisiert', timer: 1000, showConfirmButton: false });

                        // 4. THE FIX: Explicitly find the IDs to reload the gallery
                        // We grab the values from the hidden fields in your document manager
                        const productId = document.getElementById('image_product').value;
                        const customerId = document.querySelector('input[name="customer_id"]').value;
                        const alternativeId = document.querySelector('input[name="alternative_id"]').value;

                        // Re-run the main loader function
                        if (typeof window.loadDocuments === 'function') {
                            window.loadDocuments({
                                dataset: {
                                    customerId: customerId,
                                    alternativeId: alternativeId,
                                    productId: productId
                                }
                            }, { keepTab: true });
                        }
                    }
                })
                .catch(err => {
                    console.error("Update failed:", err);
                    Swal.fire('Fehler', 'Die Daten konnten nicht aktualisiert werden.', 'error');
                });
            }
        });
    };
  // --- DOM Ready Entry Point ---
  document.addEventListener("DOMContentLoaded", () => {
    const root = document.getElementById(CONFIG.ROOT_ID) || document;
    initTabs(root);
    initControls(root);
    initLightbox(root);
    applyFilters(root);
  });
})();
</script>

 <script>
        // PDF Viewer Logic
        window.openPdfViewer = function(url, title) {
            document.getElementById('pdfViewerTitle').textContent = title || 'Dokument';
            document.getElementById('pdfViewerIframe').src = url;
            
            const modal = document.getElementById('pdfViewerModal');
            modal.classList.add('is-open');
            modal.setAttribute('aria-hidden', 'false');
            document.body.classList.add('cmodal-open');
        };

        window.closePdfViewer = function() {
            const modal = document.getElementById('pdfViewerModal');
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            
            if (!document.querySelectorAll('.cmodal.is-open').length) {
                document.body.classList.remove('cmodal-open');
            }

            setTimeout(() => {
                document.getElementById('pdfViewerIframe').src = '';
            }, 300);
        };
        
        // Ensure Escape key also closes the PDF viewer
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('pdfViewerModal').classList.contains('is-open')) {
                closePdfViewer();
            }
        });
    </script>

 <script>
(() => {
  "use strict";

  // -----------------------------
  // Helpers
  // -----------------------------
  const $ = (sel, root = document) => root.querySelector(sel);

  const getCsrf = () =>
    $('meta[name="csrf-token"]')?.getAttribute("content") || "";

  const normalizeError = (msg) => {
    if (msg == null) return "Upload fehlgeschlagen.";
    if (typeof msg === "string") return msg;
    if (Array.isArray(msg)) return msg.filter(Boolean).join("\n") || "Upload fehlgeschlagen.";
    if (typeof msg === "object") {
      // Dropzone sometimes passes an object
      try { return JSON.stringify(msg); } catch (_) {}
    }
    return String(msg);
  };

  const parseLaravel422 = (xhr) => {
    try {
      const j = JSON.parse(xhr?.responseText || "{}");

      // typical Laravel JSON: { message, errors: { field: [..] } }
      if (j?.errors && typeof j.errors === "object") {
        const lines = [];
        Object.keys(j.errors).forEach((k) => {
          const arr = j.errors[k];
          if (Array.isArray(arr)) lines.push(...arr);
        });
        if (lines.length) return lines.join("\n");
      }

      if (j?.message) return String(j.message);
      return "Validierung fehlgeschlagen.";
    } catch (_) {
      return "Validierung fehlgeschlagen.";
    }
  };

  const swalOk = (title) => {
    if (typeof Swal === "undefined") return;
    Swal.fire({
      target: document.body,
      icon: "success",
      title,
      timer: 900,
      showConfirmButton: false,
    });
  };

  const swalErr = (title, text) => {
    if (typeof Swal === "undefined") return;
    Swal.fire({
      target: document.body,
      icon: "error",
      title,
      text: text || "Upload fehlgeschlagen.",
    });
  };

  const swalWarn = (title, text) => {
    if (typeof Swal === "undefined") return;
    Swal.fire({
      target: document.body,
      icon: "warning",
      title,
      text: text || "",
    });
  };

  // -----------------------------
  // Dropzone init
  // -----------------------------
  function initDropzone(button) {
    if (typeof Dropzone === "undefined") return;

    const form = document.getElementById("documentDropzone");
    if (!form) return;

    Dropzone.autoDiscover = false;

    // destroy previous instance
    if (window.__docsDz) {
      try { window.__docsDz.destroy(); } catch (_) {}
      window.__docsDz = null;
    }

    const dz = new Dropzone(form, {
      url: form.getAttribute("action"),
      paramName: "file",

      // client-side limit (MB)
      maxFilesize: 50,
      maxFiles: 1,

      acceptedFiles: ".jpg,.jpeg,.png,.webp,.gif,.pdf,.doc,.docx,.xls,.xlsx",
      headers: {
        "X-CSRF-TOKEN": getCsrf(),
        "X-Requested-With": "XMLHttpRequest",
        "Accept": "application/json",
      },

      clickable: true,
      previewsContainer: false,
      previewTemplate: "<div></div>",

      timeout: 0, // no client timeout for large files

      // IMPORTANT in Blade: use @{{ }} to avoid Blade parsing
      dictFileTooBig: "Datei ist zu groß (@{{filesize}}MB). Max: @{{maxFilesize}}MB",
      dictInvalidFileType: "Ungültiger Dateityp.",
      dictResponseError: "Serverfehler: @{{statusCode}}",

      sending: (file, xhr, formData) => {
        const cid = form.querySelector('input[name="customer_id"]')?.value || "";
        const aid = form.querySelector('input[name="alternative_id"]')?.value || "";
        const pid = form.querySelector('input[name="product_id"]')?.value || "";
        const stg = form.querySelector('select[name="stage"]')?.value || "customer";

        formData.append("customer_id", cid);
        formData.append("alternative_id", aid);
        formData.append("product_id", pid);
        formData.append("stage", stg);
      },

      success: () => {
        if (typeof window.loadDocuments === "function" && button) {
          window.loadDocuments(button, { keepTab: true, keepFilters: true, keepView: true });
        }
        swalOk("Upload OK");
      },
    });

    // show errors (client + server)
    dz.on("error", (file, msg, xhr) => {
      let text = normalizeError(msg);

      if (xhr) {
        if (xhr.status === 413) {
          text = "Upload zu groß: Server-Limit (413). Prüfe Nginx client_max_body_size.";
        } else if (xhr.status === 422) {
          text = parseLaravel422(xhr);
        } else if (xhr.status >= 500) {
          text = "Serverfehler (500+). Prüfe Nginx/PHP/Laravel Logs.";
        }
      }

      swalErr("Upload fehlgeschlagen", text);
      try { dz.removeFile(file); } catch (_) {}
    });

    dz.on("maxfilesexceeded", (file) => {
      swalWarn("Nur 1 Datei", "Bitte nur eine Datei pro Upload.");
      try { dz.removeFile(file); } catch (_) {}
    });

    window.__docsDz = dz;
  }

  // expose
  window.initDropzone = initDropzone;
})();
</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const lazyImages = [].slice.call(document.querySelectorAll("img.lazy"));
        if ("IntersectionObserver" in window) {
            let lazyImageObserver = new IntersectionObserver(function (entries, observer) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        let img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove("lazy");
                        lazyImageObserver.unobserve(img);
                    }
                });
            });

            lazyImages.forEach(function (lazyImage) {
                lazyImageObserver.observe(lazyImage);
            });
        }
    }); 
</script> 
<!-- Document and PIcutre :end -->

<script>
        // 🔥 GLOBAL STORE FOR MAP INSTANCES
    window.googleMapsInstances = {};

    // 👉 INIT MAP ON SIDEBAR OPEN
    function openSidebarGallery(triggerEl) {
        const objectId = triggerEl.dataset.alternativeId;
        const address = triggerEl.dataset.address;
        const sidebar = document.getElementById('sidebarGallery' + objectId);
        if (!sidebar) return;

        sidebar.classList.add('active');

        // Load gallery images
        fetch(`/load-images/${objectId}`)
            .then(res => res.json())
            .then(images => {
                const gallery = document.getElementById('galleryImages' + objectId);
                if (!gallery) return;

                gallery.innerHTML = '';

                if (Array.isArray(images) && images.length > 0) {
                    images.forEach(img => {
                        appendImageToGallery(objectId, img.image);
                    });
                    GLightbox({ selector: '.glightbox' });
                } else {
                    gallery.innerHTML = '<p class="text-muted">Keine Bilder vorhanden.</p>';
                }
            })
            .catch(err => {
                console.warn("Image loading failed:", err);
            });

        // Delay map init so sidebar is visible
        setTimeout(() => initGoogleMap(objectId, address), 300);
    } 
    function closeSidebarGallery(objectId) {
        document.getElementById('sidebarGallery' + objectId)?.classList.remove('active');
    }

    // ✅ INIT GOOGLE MAP
    function initGoogleMap(objectId, address) {
        const mapDiv = document.getElementById('mapContainer' + objectId);
        if (!mapDiv) return;

        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ address }, (results, status) => {
            if (status === 'OK') {
                const map = new google.maps.Map(mapDiv, {
                    center: results[0].geometry.location,
                    zoom: 18,
                    mapTypeId: 'roadmap',
                });
                new google.maps.Marker({ position: results[0].geometry.location, map });

                window.googleMapsInstances[objectId] = map;

                // Handle map type switching
                const select = document.getElementById('screenshotMode' + objectId);
                if (select) {
                    select.addEventListener('change', () => {
                        const type = select.value;
                        if (type !== 'streetview') map.setMapTypeId(type);
                    });
                }
            } else {
                Swal.fire('Fehler', 'Adresse nicht gefunden.', 'error');
            }
        });
    }

    // 🎯 TRIGGER SCREENSHOT
    function triggerScreenshot(customerId, alternativeId) {
        const mode = document.getElementById('screenshotMode' + alternativeId)?.value || 'roadmap';
        if (mode === 'streetview') {
            captureStreetViewScreenshot(customerId, alternativeId);
        } else {
            captureStaticMapScreenshot(customerId, alternativeId);
        }
    }

    // 📷 STATIC MAP SCREENSHOT (Google Static Maps API)
    function captureStaticMapScreenshot(customerId, alternativeId) {
        const map = window.googleMapsInstances[alternativeId];
        if (!map) return Swal.fire('Fehler', 'Karte nicht geladen.', 'error');

        const center = map.getCenter();
        const zoom = map.getZoom();
        const type = map.getMapTypeId();

        const staticUrl = `https://maps.googleapis.com/maps/api/staticmap?center=${center.lat()},${center.lng()}&zoom=${zoom}&size=800x400&maptype=${type}&key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo`;

        fetch(staticUrl)
            .then(res => res.blob())
            .then(blob => uploadScreenshot(blob, customerId, alternativeId, 'map_screenshot.png'));
    }

    // 📷 STREET VIEW SCREENSHOT (Google Street View API)
    function captureStreetViewScreenshot(customerId, alternativeId) {
        const map = window.googleMapsInstances[alternativeId];
        if (!map) return Swal.fire('Fehler', 'Karte nicht geladen.', 'error');

        const svService = new google.maps.StreetViewService();
        const panorama = map.getStreetView();
        const pov = panorama.getPov();
        const position = panorama.getPosition();

        if (!position) {
            Swal.fire('⚠️ Street View nicht aktiv', '', 'warning');
            return;
        }

        const lat = position.lat();
        const lng = position.lng();
        const heading = pov.heading || 0;
        const pitch = pov.pitch || 0;

        const metaUrl = `https://maps.googleapis.com/maps/api/streetview/metadata?location=${lat},${lng}&key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo`;
        fetch(metaUrl)
            .then(res => res.json())
            .then(meta => {
                if (meta.status !== 'OK') {
                    Swal.fire('Kein Street View Bild verfügbar', '', 'warning');
                    return;
                }

                const viewUrl = `https://maps.googleapis.com/maps/api/streetview?size=800x400&location=${lat},${lng}&fov=90&heading=${heading}&pitch=${pitch}&key=AIzaSyBsEupm9-Dxg6B2Pts7pWnVsjXyt76Mwzo`;
                fetch(viewUrl)
                    .then(res => res.blob())
                    .then(blob => uploadScreenshot(blob, customerId, alternativeId, 'streetview_screenshot.png'));
            });
    }

    // 📨 UPLOAD SCREENSHOT TO SERVER
    function uploadScreenshot(blob, customerId, alternativeId, filename) {
        const formData = new FormData();
        formData.append('image', blob, filename);
        formData.append('customer_id', customerId);
        formData.append('alternative_id', alternativeId);
        formData.append('status', 'screenshot');

        fetch('/save-screenshot', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        })
            .then(res => res.json())
            .then(result => {
                if (result.success && result.image) {
                    Swal.fire('✅ Screenshot gespeichert!', '', 'success');
                    appendImageToGallery(alternativeId, result.image);
                } else {
                    Swal.fire('Fehler beim Speichern', '', 'error');
                }
            });
    }

    function appendImageToGallery(alternativeId, imagePath) {
        const gallery = document.getElementById('galleryImages' + alternativeId);
        if (!gallery) return;

        // If your route is Route::get('/secure-image/{filename}', ...)
        const encoded = encodeURIComponent(imagePath);
        const fullUrl = `${window.location.origin}/secure-image/file/${encoded}`;

        const wrapper = document.createElement('div');
        wrapper.className = 'screenshot-item d-inline-block position-relative m-1';
        wrapper.style.width = '90px';

        const link = document.createElement('a');
        link.href = fullUrl;
        link.className = 'glightbox';
        link.setAttribute('data-gallery', `object-gallery-${alternativeId}`);
        link.setAttribute('data-title', 'Screenshot');

        const img = document.createElement('img');
        img.src = fullUrl;
        img.className = 'img-thumbnail';
        img.style = 'width: 90px; height: 60px; object-fit: cover;';
        link.appendChild(img);

        const delBtn = document.createElement('button');
        delBtn.className = 'btn btn-sm btn-danger position-absolute';
        delBtn.style = 'top: -5px; right: -5px; padding: 2px 5px; fontSize: 12px;';
        delBtn.textContent = 'x';
        // pass the SAME filename you used to build the URL
        delBtn.onclick = () => deleteScreenshot(imagePath, wrapper);

        wrapper.appendChild(link);
        wrapper.appendChild(delBtn);
        gallery.appendChild(wrapper);

        // Re-init GLightbox safely
        if (window._glightbox) window._glightbox.destroy();
        window._glightbox = GLightbox({ selector: '.glightbox' });
    }

    function deleteScreenshot(filename, wrapperElement) {
        Swal.fire({
            title: 'Bild löschen?',
            text: 'Dieses Bild wird dauerhaft entfernt.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen',
            cancelButtonText: 'Abbrechen'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('/delete-screenshot', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ image: filename })
                })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            wrapperElement.classList.add('fade-out');
                            setTimeout(() => wrapperElement.remove(), 400);
                            Swal.fire('✅ Gelöscht!', '', 'success');
                        } else {
                            Swal.fire('❌ Fehler beim Löschen.', '', 'error');
                        }
                    });
            }
        });
    }
 
</script> 
<script>
    function loadkanban(customerId, alternativeId, productId, employeeId) {
        const container = document.getElementById('mainContent');
        container.innerHTML = '<div class="p-3 text-center">Kanban wird geladen...</div>';

        fetch(`/customer/process/kanban/${customerId}/${alternativeId}/${productId}/${employeeId}`)
            .then(response => response.json())
            .then(data => {
                return fetch('/customer/process/kanban/view', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ leads: data })
                });
            })
            .then(res => res.text())
            .then(html => {
                container.innerHTML = html;
                feather.replace();
                initKanbanSortable();
            })
            .catch(err => {
                container.innerHTML = `<div class="alert alert-danger">Fehler beim Laden des Kanban-Boards.</div>`;
                console.error(err);
            });
    }

    function initKanbanSortable(onStageChangeSuccess = () => {}) {
        const dropzones = document.querySelectorAll('.kanban-dropzone');
        const borderColors = {
            lead: '#fcd34d',
            offer: '#93c5fd',
            deal: '#34d399',
            project: '#60a5fa',
            ticket: '#fca5a5',
            completed: '#a3e635',
            junk: '#d1d5db'
        };

        dropzones.forEach((zone, index) => {
            new Sortable(zone, {
                group: 'kanban',
                animation: 150,
                onEnd: function (evt) {
                    const el = evt.item;
                    const fromStage = evt.from.closest('.kanban-column')?.dataset.stage;
                    const toStage = evt.to.closest('.kanban-column')?.dataset.stage;

                    if (!toStage || fromStage === toStage) return;

                    const customerId = el.dataset.customerId;
                    const alternativeId = el.dataset.alternativeId;
                    const productId = el.dataset.productId;
                    const employeeId = el.dataset.employeeId || 0;
                    const service = el.dataset.service;
                    const serviceId = el.dataset.serviceId || 0;
                    const departmentId = el.dataset.departmentId || 0;

                    if (!customerId || !alternativeId || !productId || !service || !toStage) {
                        console.error('[Kanban Error] Missing data');
                        return;
                    }

                    Swal.fire({
                        title: 'Notiz zum Statuswechsel',
                        input: 'textarea',
                        inputPlaceholder: 'Gib eine Beschreibung oder Notiz ein...',
                        showCancelButton: true,
                        confirmButtonText: 'Speichern',
                        cancelButtonText: 'Abbrechen',
                        inputAttributes: {
                            autocapitalize: 'on'
                        },
                        showLoaderOnConfirm: true,
                        preConfirm: (description) => {
                            const url = `/lead/kanban/${customerId}/${alternativeId}/${productId}/${employeeId}/${service}/${toStage}/${serviceId}/${departmentId}`;
                            return fetch(url, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({ description })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (!data.success) throw new Error(data.message || 'Fehler beim Speichern');
                                return data;
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`❌ ${error.message}`);
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then(result => {
                        if (result.isConfirmed) {
                            // ✅ Style and update
                            el.style.borderLeftColor = borderColors[toStage] || '#ccc';
                            const stageLabel = el.querySelector('.kanban-stage-label');
                            if (stageLabel) {
                                const stageMap = {
                                    lead: 'Kunde',
                                    offer: 'Angebot',
                                    deal: 'Auftrag',
                                    project: 'Montage',
                                    completed: 'Abgeschlossen',
                                    ticket: 'Ticket',
                                    junk: 'Junk'
                                };
                                stageLabel.textContent = stageMap[toStage] || toStage;
                            }

                            Swal.fire({
                                icon: 'success',
                                title: 'Status geändert',
                                text: result.value.message || 'Erfolgreich aktualisiert.',
                                timer: 1200,
                                showConfirmButton: false,
                                toast: true,
                                position: 'top-end'
                            });

                            onStageChangeSuccess(el, toStage);
                        } else {
                            // ❌ Move back if canceled
                            evt.from.appendChild(el);
                        }
                    });
                }
            });
        });
    }
</script>  
    

<!-- Ticket systems  --> 
<script> 
    const context = document.getElementById('filterContext');
    const customer_id = context.dataset.customer;
    const alternative_id = context.dataset.alternative;
    const product_id = context.dataset.product;

    function LoadCustomerTicket(customerId, alternativeId, productId, tab) {
        const container = document.getElementById("mainContent");
        container.innerHTML = '<div class="p-4 text-center">Tickets werden geladen...</div>';

        fetch("/customer/tickets/load", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                customer_id: customerId,
                alternative_id: alternativeId,
                product_id: productId,
                tab: tab
            })
        })
        .then(res => res.text())
        .then(html => {
            container.innerHTML = html;
            feather.replace();
            initKanbanSortable(); // ✅ re-initialize sortable
        });
    }
    function filterTickets() {
        const date = document.getElementById('filterDate').value;
        const status = document.getElementById('filterStatus').value;
        const employee = document.getElementById('filterEmployee').value;

        // ✅ use correctly defined vars
        const context = document.getElementById('filterContext');
        const customer_id = context.dataset.customer;
        const alternative_id = context.dataset.alternative;
        const product_id = context.dataset.product;

        fetch('/customer/tickets/load', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                customer_id,
                alternative_id,
                product_id,
                date,
                status,
                employee
            })
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('mainContent').innerHTML = html;
            feather.replace();
            initKanbanSortable();
        });
    }
 
    function initKanbanSortable() {
        document.querySelectorAll('.kanban-dropzone').forEach(zone => {
            new Sortable(zone, {
                group: 'tickets',
                animation: 150,
                onAdd: function (evt) {
                    const ticketId = evt.item.dataset.id;
                    const newStatus = evt.to.closest('.kanban-column').dataset.status;

                    fetch('/ticket/status/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ id: ticketId, status: newStatus })
                    }).then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            console.log(`Ticket #${ticketId} → ${newStatus}`);
                        }
                    });
                }
            });
        });
    } 
    // Optional: Call this once if Kanban is rendered on page load
    document.addEventListener('DOMContentLoaded', initKanbanSortable);
</script>

<script>
    $(document).ready(function() {
        // 1. Fix for Bootstrap 4 Focus Stealing
        // This stops Bootstrap from stealing focus when you click inputs in your custom overlay
        $.fn.modal.Constructor.prototype._enforceFocus = function() {}; 

        // 2. Fix for Bootstrap 5 (if you update later)
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        
        // 3. Specific Event Listener for your Overlay
        // This stops the focusin event from bubbling up to Bootstrap
        $(document).on('focusin', function(e) {
            if ($(e.target).closest('#serialsOverlay').length) {
                e.stopPropagation();
            }
        });
    });
</script>
  
<!-- save operation of Product  -->
 <script>
/**
 * Customer Products + Gallery – FULL SCRIPT (No page reload)
 * Fixes:
 *  - leadProduct global always available (no "leadProduct is not defined")
 *  - serial_numbers key aligned with controller (NOT serial_numbers_json)
 *  - serial modal uses {pos, serial, product_name} and remains backward compatible
 *  - removes duplicate saveSerials() definition
 *  - avoids inline-onclick dependency by exposing window.leadProduct and window.CustomerProducts.leadProduct
 *
 * Requires:
 * - jQuery
 * - Select2
 * - SweetAlert2 (Swal)
 */

(function () {
  'use strict';

  // ------------------------------------------------------------
  // State (persistent across blade injections)
  // ------------------------------------------------------------
  window.__CP_STATE__ = window.__CP_STATE__ || {
    currentCid: null,
    currentAid: null,
    currentPid: null,
    cachedProducts: [],
    cachedDepartments: [],
    serialMode: 'add',
    booted: false,

    // gallery
    addSavedProductInfoId: null,
    editProductInfoId: null,
    addMediaCache: [],
    editMediaCache: [],
  };
  const S = window.__CP_STATE__;

  // ------------------------------------------------------------
  // API (adjust if routes differ)
  // ------------------------------------------------------------
  const CP = {
    api: {
      leadBlade: (cid, aid, pid) => `/lead-product/${cid}/${aid}/${pid}`,
      loadData:  (pid) => `/customer/load/product/${pid}`,

      store: `/lead-product/store`,
      show:  (id) => `/lead-product/${id}`,
      update:(id) => `/lead-product/update/${id}`,
      del:   (id) => `/lead-product/delete/${id}`,

      // gallery
      mediaIndex: (productInfoId) => `/lead-product/media/${productInfoId}`,
      mediaUpload:(productInfoId) => `/lead-product/media/${productInfoId}/upload`,
      mediaDelete:(mediaId)       => `/lead-product/media/file/${mediaId}`,
    }
  };

  // ------------------------------------------------------------
  // Helpers
  // ------------------------------------------------------------
  const csrf = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  const stripHtml = (html) => {
    const div = document.createElement('div');
    div.innerHTML = html ?? '';
    return div.textContent || div.innerText || '';
  };

  const escHtml = (s) =>
    String(s ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');

  const isTruthy = (v) => Number(v) === 1 || v === true || v === '1';

  const swalLoading = (title = 'Loading...') => {
    if (!window.Swal) return;
    Swal.fire({ title, allowOutsideClick: false, didOpen: () => Swal.showLoading() });
  };
  const closeSwal = () => { try { window.Swal?.close?.(); } catch(e) {} };

  const toast = (icon, title, text) => {
    window.Swal?.fire?.({
      icon: icon || 'success',
      title: title || '',
      text: text || '',
      timer: 1300,
      showConfirmButton: false,
      toast: true,
      position: 'top-end',
    });
  };

  const $ovAdd  = () => $('#addProductOverlay');
  const $ovEdit = () => $('#editProductOverlay');
  const $ovSn   = () => $('#serialsOverlay');

  function setBodyLock() {
    const anyOpen = $('.cp-overlay.is-open').length > 0;
    document.documentElement.classList.toggle('cp-modal-open', anyOpen);
    document.body.classList.toggle('cp-modal-open', anyOpen);
  }

  function closeAllSelect2() {
    $('select').each(function () {
      const $s = $(this);
      if ($s.data('select2')) {
        try { $s.select2('close'); } catch(e) {}
      }
    });
  }

  function openOverlay($el) {
    if (!$el || !$el.length) return;
    closeAllSelect2();
    $el.addClass('is-open').attr('aria-hidden', 'false');
    setBodyLock();
    setTimeout(() => {
      const $f = $el.find('input,select,textarea,button').filter(':visible').not(':disabled').first();
      if ($f.length) $f.trigger('focus');
    }, 30);
  }

  function closeOverlay($el) {
    if (!$el || !$el.length) return;
    closeAllSelect2();
    $el.removeClass('is-open').attr('aria-hidden', 'true');
    setBodyLock();
  }

  // overlay: click outside closes
  $(document).off('mousedown.cpOverlay').on('mousedown.cpOverlay', '.cp-overlay.is-open', function (e) {
    if (e.target === this) closeOverlay($(this));
  });

  // overlay: data-cp-close
  $(document).off('click.cpClose').on('click.cpClose', '[data-cp-close]', function () {
    const id = $(this).attr('data-cp-close');
    closeOverlay($('#' + id));
  });

  // overlay: ESC closes top-most
  $(document).off('keydown.cpEsc').on('keydown.cpEsc', function (e) {
    if (e.key !== 'Escape') return;
    if ($ovSn().hasClass('is-open')) return closeOverlay($ovSn());
    if ($ovEdit().hasClass('is-open')) return closeOverlay($ovEdit());
    if ($ovAdd().hasClass('is-open')) return closeOverlay($ovAdd());
  });

  // ------------------------------------------------------------
  // Select2 (products)
  // ------------------------------------------------------------
  function formatProduct(p) {
    if (!p || !p.id) return p?.text || '';
    const $el = $(p.element);
    const brand = $el.data('brand') || '';
    const desc  = stripHtml($el.data('description') || '');
    const img   = $el.data('image') || '/images/icons/placeholder.svg';

    const $row = $('<div class="d-flex align-items-center" style="gap:10px;"/>');
    $row.append($('<img/>', { src: img, width: 28, height: 28, class: 'rounded' }));
    const $text = $('<div style="min-width:0;"/>');
    $text.append($('<div style="font-weight:600;"/>').text(p.text));
    if (brand) $text.append($('<small class="text-muted d-block"/>').text(brand));
    if (desc)  $text.append($('<small class="text-muted d-block"/>').text(desc));
    $row.append($text);
    return $row;
  }

  function formatProductSelection(p) {
    if (!p || !p.id) return p?.text || '';
    const $el = $(p.element);
    const brand = $el.data('brand') || '';
    const $wrap = $('<span/>').text(p.text);
    if (brand) $wrap.append($('<small class="text-muted ml-1"/>').text(`(${brand})`));
    return $wrap;
  }

  function ensureEmptyOption($select) {
    if (!$select || !$select.length) return;
    const hasEmpty = $select.find('option').filter(function () {
      return String($(this).attr('value') ?? '') === '';
    }).length > 0;
    if (!hasEmpty) $select.prepend(new Option('', '', false, false));
  }

  function safeDestroySelect2($select) {
    if (!$select || !$select.length) return;
    if (!$select.data('select2')) return;
    try { $select.select2('close'); } catch(e) {}
    try { $select.select2('destroy'); } catch(e) {}
  }

  function initProductSelect2($select, $overlay, placeholder) {
    if (!$select || !$select.length) return;

    ensureEmptyOption($select);
    safeDestroySelect2($select);

    const $parent = $overlay.find('.cp-dialog').first();

    $select.select2({
      width: '100%',
      allowClear: true,
      placeholder: placeholder || 'Bitte wählen',
      closeOnSelect: true,
      templateResult: formatProduct,
      templateSelection: formatProductSelection,
      dropdownParent: $parent.length ? $parent : $(document.body),
    });

    $select.off('select2:select.cpClose').on('select2:select.cpClose', function () {
      try { $(this).select2('close'); } catch(e) {}
    });
  }

  function initEmployeeSelect2($overlay) {
    const $sels = $overlay.find('.js-employee-select');
    if (!$sels.length) return;

    $sels.each(function () {
      const $el = $(this);
      if ($el.data('select2')) return;

      ensureEmptyOption($el);
      const $parent = $overlay.find('.cp-dialog').first();

      $el.select2({
        tags: true,
        width: '100%',
        allowClear: true,
        closeOnSelect: true,
        placeholder: 'Mitarbeiter wählen oder Namen eingeben',
        dropdownParent: $parent.length ? $parent : $(document.body),
      });

      $el.off('select2:select.cpClose').on('select2:select.cpClose', function () {
        try { $(this).select2('close'); } catch(e) {}
      });
    });
  }

  function getInstalledBy($select) {
    if ($select.data('select2')) {
      const d = $select.select2('data');
      if (d && d.length) return (d[0].text || '').trim();
    }
    return ($select.val() || '').trim();
  }

  // ------------------------------------------------------------
  // Table row render/patch
  // ------------------------------------------------------------
  function rowHtml(p) {
    const purchasedTxt = isTruthy(p.purchased_from_us) ? 'Ja' : 'Nein';
    const imageTxt     = isTruthy(p.image_available) ? 'Ja' : 'Nein';

    return `
      <tr data-id="${escHtml(p.id)}" style="cursor:pointer;">
        <td><p class="m-0 p-0">${escHtml(p.product_count ?? '')}</p></td>
        <td>
          <p class="m-0 p-0">${escHtml(p.product_name ?? '')}</p>
          <small><p class="m-0 p-0">S.Nr: ${escHtml(p.serial_number ?? '-')}</p></small>
        </td>
        <td>${escHtml(p.manufacturer ?? '')}</td>
        <td>
          <p class="m-0 p-0">${escHtml(p.installation_date ?? 'unbekannt')}</p>
          <small><p class="m-0 p-0">${escHtml(p.installation_location ?? 'Installationsort unbekannt')}</p></small>
        </td>
        <td>
          <p class="m-0 p-0">${escHtml(p.purchase_date ?? '—')}</p>
          <small><p class="m-0 p-0">${purchasedTxt}</p></small>
        </td>
        <td>
          <small>
            <p class="m-0 p-0"><strong>Rechnung/Referenz:</strong> ${escHtml(p.invoice_reference ?? 'unbekannt')}</p>
            <p class="m-0 p-0"><strong>Garantie bis:</strong> ${escHtml(p.warranty_until ?? 'unbekannt')}</p>
            <p class="m-0 p-0"><strong>Gewährleistung bis:</strong> ${escHtml(p.guarantee_until ?? 'unbekannt')}</p>
            <p class="m-0 p-0"><strong>Bild vorhanden:</strong> ${imageTxt}</p>
          </small>
        </td>
        <td>${escHtml(p.installed_by ?? '—')}</td>
        <td>${escHtml(p.department_name ?? '—')}</td>
        <td class="text-right">
          <button class="btn btn-icon btn-icon rounded-circle btn-primary mr-1 mb-1 waves-effect waves-light action-btn"
                  onclick="event.stopPropagation(); window.CustomerProducts.editProduct(${Number(p.id)})">
            <i class="feather icon-edit"></i>
          </button>
          <button class="btn btn-icon btn-icon rounded-circle btn-danger mr-1 mb-1 waves-effect waves-light action-btn"
                  onclick="event.stopPropagation(); window.CustomerProducts.deleteProduct(${Number(p.id)})">
            <i class="feather icon-trash"></i>
          </button>
        </td>
      </tr>
    `;
  }

  function appendRow(p) {
    $('#productTableBody').append(rowHtml(p));
    window.feather?.replace?.();
  }

  function upsertRow(p) {
    const $row = $(`#productTableBody tr[data-id="${p.id}"]`);
    if ($row.length) $row.replaceWith(rowHtml(p));
    else appendRow(p);
    window.feather?.replace?.();
  }

  // ------------------------------------------------------------
  // Build selects
  // ------------------------------------------------------------
  function buildProductOptions($select, products) {
    $select.empty();
    $select.append(new Option('', '', false, false));
    (products || []).forEach((p) => {
      const image = p?.images?.[0]?.image ? `/uploads/${p.images[0].image}` : '/images/icons/placeholder.svg';
      const brand = p?.brand?.name || '';
      const desc  = stripHtml(p?.short_description || '');

      const opt = new Option(p.product, p.id, false, false);
      opt.setAttribute('data-brand', brand);
      opt.setAttribute('data-description', desc);
      opt.setAttribute('data-image', image);
      $select.append(opt);
    });
  }

  function buildDepartmentOptions($select, depts) {
    $select.empty();
    $select.append(new Option('', '', false, false));
    (depts || []).forEach((d) => $select.append(new Option(d.department_name, d.id, false, false)));
  }

  // ------------------------------------------------------------
  // Gallery
  // ------------------------------------------------------------
  function bytesToSize(bytes) {
    const b = Number(bytes || 0);
    if (!b) return '';
    const units = ['B','KB','MB','GB'];
    let i = 0, v = b;
    while (v >= 1024 && i < units.length - 1) { v /= 1024; i++; }
    return `${v.toFixed(i === 0 ? 0 : 1)} ${units[i]}`;
  }

  function isImageMime(mime) {
    return String(mime || '').startsWith('image/');
  }

  function mediaCardHtml(m, mode) {
    const name = escHtml(m.original_name || 'file');
    const size = escHtml(bytesToSize(m.size));
    const url  = escHtml(m.url || '#');
    const id   = Number(m.id);

    const preview = isImageMime(m.mime)
      ? `<img src="${url}" alt="${name}" style="width:100%; height:140px; object-fit:cover; border-radius:10px;">`
      : `<div style="width:100%; height:140px; border-radius:10px; display:flex; align-items:center; justify-content:center; border:1px dashed rgba(0,0,0,.15);">
           <div style="text-align:center;">
             <i class="feather icon-file-text" style="font-size:28px;"></i>
             <div style="font-size:12px; opacity:.75; margin-top:6px;">PDF/Dokument</div>
           </div>
         </div>`;

    return `
      <div class="col-md-3 mb-2 cp-media-item" data-mid="${id}" data-name="${escHtml((m.original_name || '').toLowerCase())}">
        <div class="card" style="border-radius:14px; overflow:hidden;">
          <div class="p-2">${preview}</div>
          <div class="px-2 pb-2">
            <div class="d-flex justify-content-between align-items-start" style="gap:10px;">
              <div style="min-width:0;">
                <div style="font-weight:600; font-size:13px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">${name}</div>
                <div style="font-size:12px; opacity:.75;">${size}</div>
              </div>
              <div class="d-flex" style="gap:6px;">
                <a class="btn btn-sm btn-outline-primary" href="${url}" target="_blank" title="Öffnen">
                  <i class="feather icon-external-link"></i>
                </a>
                <button type="button" class="btn btn-sm btn-outline-danger btnMediaDelete" data-mode="${mode}" data-id="${id}" title="Löschen">
                  <i class="feather icon-trash"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  function renderMediaGrid(mode, media) {
    const list = Array.isArray(media) ? media : [];
    const $grid = mode === 'add' ? $('#addGalleryGrid') : $('#editGalleryGrid');
    $grid.html(list.map(m => mediaCardHtml(m, mode)).join('') || `<div class="col-12"><div class="text-muted">Keine Dateien vorhanden.</div></div>`);
    window.feather?.replace?.();
  }

  function filterMediaGrid(mode) {
    const q = (mode === 'add' ? $('#add_gallery_search').val() : $('#edit_gallery_search').val()) || '';
    const s = q.trim().toLowerCase();
    const $grid = mode === 'add' ? $('#addGalleryGrid') : $('#editGalleryGrid');
    $grid.find('.cp-media-item').each(function () {
      const name = ($(this).data('name') || '');
      $(this).toggle(!s || name.includes(s));
    });
  }

  async function loadMedia(mode, productInfoId) {
    if (!productInfoId) return;
    try {
      const res = await $.getJSON(CP.api.mediaIndex(productInfoId));
      const media = res?.media || [];
      if (mode === 'add') S.addMediaCache = media;
      else S.editMediaCache = media;
      renderMediaGrid(mode, media);
      filterMediaGrid(mode);
    } catch (e) {
      console.error(e);
      window.Swal?.fire?.('Fehler', 'Galerie konnte nicht geladen werden.', 'error');
    }
  }

  async function uploadMedia(mode) {
    const productInfoId = mode === 'add' ? S.addSavedProductInfoId : S.editProductInfoId;
    if (!productInfoId) return window.Swal?.fire?.('Info', 'Bitte zuerst das Produkt speichern.', 'info');

    const $input = mode === 'add' ? $('#add_gallery_files') : $('#edit_gallery_files');
    const files = $input[0]?.files ? Array.from($input[0].files) : [];
    if (!files.length) return toast('info', 'Keine Datei', 'Bitte Dateien auswählen.');

    const fd = new FormData();
    fd.append('_token', csrf());
    files.forEach(f => fd.append('files[]', f));

    const $btn = mode === 'add' ? $('#btnAddGalleryUpload') : $('#btnEditGalleryUpload');
    $btn.prop('disabled', true);

    try {
      const res = await $.ajax({
        url: CP.api.mediaUpload(productInfoId),
        type: 'POST',
        data: fd,
        processData: false,
        contentType: false,
      });

      const newMedia = res?.media || [];
      if (mode === 'add') {
        S.addMediaCache = [...newMedia, ...(S.addMediaCache || [])];
        renderMediaGrid('add', S.addMediaCache);
      } else {
        S.editMediaCache = [...newMedia, ...(S.editMediaCache || [])];
        renderMediaGrid('edit', S.editMediaCache);
      }

      $input.val('');
      filterMediaGrid(mode);
      toast('success', 'Upload', 'Dateien wurden hochgeladen.');
    } catch (e) {
      console.error(e);
      window.Swal?.fire?.('Fehler', 'Upload fehlgeschlagen.', 'error');
    } finally {
      $btn.prop('disabled', false);
    }
  }

  async function deleteMedia(mode, mediaId) {
    const ok = await window.Swal?.fire?.({
      title: 'Datei löschen?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ja, löschen',
    });
    if (!ok?.isConfirmed) return;

    try {
      await $.ajax({
        url: CP.api.mediaDelete(mediaId),
        type: 'DELETE',
        data: { _token: csrf() },
      });

      if (mode === 'add') {
        S.addMediaCache = (S.addMediaCache || []).filter(x => Number(x.id) !== Number(mediaId));
        renderMediaGrid('add', S.addMediaCache);
      } else {
        S.editMediaCache = (S.editMediaCache || []).filter(x => Number(x.id) !== Number(mediaId));
        renderMediaGrid('edit', S.editMediaCache);
      }

      filterMediaGrid(mode);
      toast('success', 'Gelöscht', 'Datei wurde entfernt.');
    } catch (e) {
      console.error(e);
      window.Swal?.fire?.('Fehler', 'Konnte Datei nicht löschen.', 'error');
    }
  }

  function resetAddGalleryUI() {
    S.addSavedProductInfoId = null;
    S.addMediaCache = [];
    $('#addGalleryGrid').empty();
    $('#add_gallery_search').val('');
    $('#add_gallery_files').val('');
    $('#addGalleryLocked').removeClass('d-none');
    $('#addGalleryControls').addClass('d-none');
  }

  function unlockAddGallery(productInfoId) {
    S.addSavedProductInfoId = Number(productInfoId);
    $('#addGalleryLocked').addClass('d-none');
    $('#addGalleryControls').removeClass('d-none');
    loadMedia('add', S.addSavedProductInfoId);
  }

  // ------------------------------------------------------------
  // Wire after blade injection
  // ------------------------------------------------------------
  function wireAfterBladeLoad() {
    // Row click -> edit
    $('#productTableBody')
      .off('click.cpRow')
      .on('click.cpRow', 'tr', function (e) {
        if ($(e.target).closest('button, a, .action-btn').length) return;
        const id = $(this).data('id');
        if (id) window.CustomerProducts.editProduct(id);
      });

    initEmployeeSelect2($ovAdd());
    initEmployeeSelect2($ovEdit());

    // Add product select
    const $addProd = $('#customer_product_info');
    buildProductOptions($addProd, S.cachedProducts);
    initProductSelect2($addProd, $ovAdd(), 'Produkt auswählen');
    $addProd.off('change.cpAddFill').on('change.cpAddFill', function () {
      const opt = this.options[this.selectedIndex];
      $('#manufacturer_note').val(opt?.getAttribute('data-brand') || '');
      $('#notes_note').val(opt?.getAttribute('data-description') || '');
    });

    buildDepartmentOptions($('#department_id'), S.cachedDepartments);

    // Edit product select
    const $editProd = $('#edit_product_name');
    buildProductOptions($editProd, S.cachedProducts);
    initProductSelect2($editProd, $ovEdit(), 'Produkt auswählen');
    $editProd.off('change.cpEditFill').on('change.cpEditFill', function () {
      const opt = this.options[this.selectedIndex];
      $('#edit_manufacturer').val(opt?.getAttribute('data-brand') || '');
      if (!$('#edit_notes').val()) $('#edit_notes').val(opt?.getAttribute('data-description') || '');
    });

    buildDepartmentOptions($('#edit_department_id'), S.cachedDepartments);

    resetAddGalleryUI();
    updateSerialButtons();
  }

  // ------------------------------------------------------------
  // Lock/unlock edit
  // ------------------------------------------------------------
  const ICON_LOCKED =
    '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-lock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>';
  const ICON_UNLOCKED =
    '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-unlock"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1-9.9-1"></path></svg>';

  function toggleEditLock(forceLocked = null) {
    const $ov = $ovEdit();
    const $inputs = $ov.find('input, select, textarea');
    const $save = $('#btnEditSave');
    const $lock = $('#btnToggleEditLock');

    let shouldLock;
    if (forceLocked !== null) shouldLock = !!forceLocked;
    else shouldLock = !$inputs.first().prop('disabled');

    $inputs.prop('disabled', shouldLock);
    $save.prop('disabled', shouldLock);
    $lock.html(shouldLock ? ICON_LOCKED : ICON_UNLOCKED);

    updateSerialButtons();
  }

  $(document).off('click.cpLock').on('click.cpLock', '#btnToggleEditLock', () => toggleEditLock(null));

  // ------------------------------------------------------------
  // Serials modal (JSON keys aligned)
  // ------------------------------------------------------------
  function parseSerialJson(raw) {
    if (Array.isArray(raw)) return raw;
    try {
      const v = JSON.parse(raw || '[]');
      return Array.isArray(v) ? v : [];
    } catch {
      return [];
    }
  }

  function serialCount(mode) {
    return Math.max(1, Number((mode === 'edit' ? $('#edit_product_count').val() : $('#product_count').val()) || 1));
  }

  function serialName(mode) {
    const name = (mode === 'edit'
      ? $('#edit_product_name option:selected').text()
      : $('#customer_product_info option:selected').text()) || '';
    return (name || 'Produkt').trim();
  }

  function jsonInput(mode) {
    return mode === 'edit' ? $('#edit_serial_numbers_json') : $('#serial_numbers_json');
  }

  function mainInput(mode) {
    return mode === 'edit' ? $('#edit_serial_number') : $('#serial_number');
  }

  function hintEl(mode) {
    return mode === 'edit' ? $('#serialsHintEdit') : $('#serialsHintAdd');
  }

  function updateSerialButtons() {
    const addC = Number($('#product_count').val() || 1);
    $('#btnSerialsAdd').prop('disabled', !(addC > 1));

    const editC = Number($('#edit_product_count').val() || 1);
    const locked = $('#btnEditSave').prop('disabled') === true;
    $('#btnSerialsEdit').prop('disabled', locked || !(editC > 1));
  }

  function renderSerialRows(mode) {
    const count = serialCount(mode);
    const existing = parseSerialJson(jsonInput(mode).val());
    const seed = (mainInput(mode).val() || '').trim();

    if (!existing.length && seed) existing.push({ pos: 1, serial: seed, product_name: serialName(mode) });

    $('#serialsModalProductName').text(serialName(mode));
    $('#serialsModalCount').text(String(count));

    const rows = [];
    for (let i = 1; i <= count; i++) {
      const found = existing.find(x => Number(x.pos) === i);
      const val = (found?.serial ?? found?.serial_number ?? ''); // backward compat
      rows.push(`
        <tr>
          <td style="width:90px;">${i}</td>
          <td>
            <input type="text"
                   class="form-control form-control-sm serial-input"
                   data-pos="${i}"
                   value="${escHtml(val)}"
                   placeholder="z.B. SN-${String(i).padStart(3, '0')}"
                   autocomplete="off">
          </td>
        </tr>
      `);
    }
    $('#serialsModalBody').html(rows.join(''));
  }

  function openSerials(mode) {
    S.serialMode = mode;
    renderSerialRows(mode);
    openOverlay($ovSn());
    setTimeout(() => $('#serialsModalBody').find('input.serial-input').first().trigger('focus'), 50);
  }

  function saveSerials() {
    const mode = S.serialMode || 'add';
    const count = serialCount(mode);
    const pn = serialName(mode);

    const arr = [];
    $('#serialsModalBody').find('input.serial-input').each(function () {
      arr.push({
        pos: Number($(this).data('pos')),
        serial: ($(this).val() || '').trim(),
        product_name: pn,
      });
    });

    jsonInput(mode).val(JSON.stringify(arr));

    const firstNonEmpty = arr.find(x => x.serial) || arr.find(x => x.pos === 1) || null;
    mainInput(mode).val(firstNonEmpty?.serial || '');

    const filled = arr.filter(x => x.serial).length;
    hintEl(mode).text(`${filled}/${count} Seriennummern gespeichert`).show();

    closeOverlay($ovSn());
  }

  $(document).off('click.cpOpenSerialAdd').on('click.cpOpenSerialAdd', '#btnSerialsAdd', function () {
    if ($(this).prop('disabled')) return;
    openSerials('add');
  });

  $(document).off('click.cpOpenSerialEdit').on('click.cpOpenSerialEdit', '#btnSerialsEdit', function () {
    if ($(this).prop('disabled')) return;
    openSerials('edit');
  });

  $(document).off('click.cpSerialClose').on('click.cpSerialClose', '#snCloseBtn, #snCancelBtn', () => closeOverlay($ovSn()));
  $(document).off('click.cpSerialSave').on('click.cpSerialSave', '#btnSerialsModalSave', saveSerials);
  $(document).off('input.cpSerialCount change.cpSerialCount').on('input.cpSerialCount change.cpSerialCount', '#product_count, #edit_product_count', updateSerialButtons);

  // ------------------------------------------------------------
  // Open/Close overlays
  // ------------------------------------------------------------
  function openAdd() {
    openOverlay($ovAdd());
    updateSerialButtons();
    if (!S.addSavedProductInfoId) resetAddGalleryUI();
  }
  function closeAdd() { closeOverlay($ovAdd()); }
  function openEdit() { openOverlay($ovEdit()); updateSerialButtons(); }
  function closeEdit() { closeOverlay($ovEdit()); }

  // ------------------------------------------------------------
  // Load blade + cache data
  // IMPORTANT: expose globally so inline onclick="leadProduct(this)" works
  // ------------------------------------------------------------
  async function leadProduct(buttonOrEvent) {
    const btn = buttonOrEvent?.currentTarget ? buttonOrEvent.currentTarget : buttonOrEvent;
    const $btn = $(btn);

    S.currentCid = $btn.data('customer-id');
    S.currentAid = $btn.data('alternative-id');
    S.currentPid = $btn.data('product-id');

    swalLoading('Loading...');

    try {
      const html = await $.ajax({ url: CP.api.leadBlade(S.currentCid, S.currentAid, S.currentPid), method: 'GET' });
      $('#mainContent').html(html);
      closeSwal();
      window.feather?.replace?.();

      const data = await fetch(CP.api.loadData(S.currentPid), { credentials: 'same-origin' }).then(r => r.json());
      S.cachedProducts = data.products || [];
      S.cachedDepartments = data.departments || [];

      wireAfterBladeLoad();
    } catch (e) {
      closeSwal();
      console.error(e);
      window.Swal?.fire?.('Error', 'Failed to load product info.', 'error');
    }
  }

  // ------------------------------------------------------------
  // ADD (NO reload)
  // ------------------------------------------------------------
  async function addProduct() {
    const $btn = $('#btnAddSave');
    $btn.prop('disabled', true);

    const installedBy = getInstalledBy($('#installed_by'));
    const selectedProductName = $('#customer_product_info option:selected').text();

    const payload = {
      _token: csrf(),
      customer_id: S.currentCid,
      alternative_id: S.currentAid,
      product_id: S.currentPid,

      products: $('#customer_product_info').val(),
      product_name: selectedProductName,
      product_count: $('#product_count').val(),

      manufacturer: $('#manufacturer_note').val(),
      serial_number: $('#serial_number').val(),

      // ✅ controller expects "serial_numbers"
      serial_numbers: $('#serial_numbers_json').val(),

      installation_date: $('#installation_date').val(),
      installation_location: $('#installation_location').val(),

      purchased_from_us: $('#purchased_from_us').val(),
      purchase_date: $('#purchase_date').val(),

      invoice_reference: $('#invoice_reference').val(),
      warranty_until: $('#warranty_until').val(),
      guarantee_until: $('#guarantee_until').val(),

      image_available: $('#image_available').val(),
      installed_by: installedBy,
      department_id: $('#department_id').val(),
      notes: $('#notes_note').val(),
    };

    try {
      const res = await $.post(CP.api.store, payload);
      upsertRow(res);

      if (res?.id) {
        unlockAddGallery(res.id);
        try { $('a[href="#addTabGallery"]').tab('show'); } catch (e) {}
      }

      toast('success', 'Gespeichert!', 'Produkt wurde hinzugefügt.');
    } catch (e) {
      console.error(e);
      window.Swal?.fire?.('Error', 'Fehler beim Speichern', 'error');
    } finally {
      $btn.prop('disabled', false);
    }
  }

  function resetAddForm() {
    $('#product_count').val('1');
    $('#manufacturer_note, #serial_number, #installation_location, #invoice_reference, #notes_note').val('');
    $('#installation_date, #purchase_date, #warranty_until, #guarantee_until').val('');
    $('#purchased_from_us, #image_available').val('0');
    $('#department_id').val('');
    $('#serial_numbers_json').val('');
    $('#serialsHintAdd').hide().text('');

    const $prodSel = $('#customer_product_info');
    if ($prodSel.data('select2')) $prodSel.val('').trigger('change'); else $prodSel.val('');

    const $emp = $('#installed_by');
    if ($emp.data('select2')) $emp.val('').trigger('change'); else $emp.val('');

    resetAddGalleryUI();
    try { $('a[href="#addTabDetails"]').tab('show'); } catch (e) {}
    updateSerialButtons();
    toast('info', 'Neu', 'Formular zurückgesetzt.');
  }

  // ------------------------------------------------------------
  // EDIT: load item -> open overlay + load gallery
  // ------------------------------------------------------------
  async function editProduct(id) {
    try {
      const item = await $.getJSON(CP.api.show(id));
      if (!item) return window.Swal?.fire?.('Fehler', 'Produktdaten nicht gefunden.', 'error');

      S.editProductInfoId = Number(item.id);

      $('#edit_id').val(item.id);
      $('#edit_product_count').val(item.product_count ?? 1);

      $('#edit_serial_number').val(item.serial_number ?? '');

      const sn = item.serial_numbers ?? item.serial_numbers_json ?? [];
      $('#edit_serial_numbers_json').val(Array.isArray(sn) ? JSON.stringify(sn) : (sn || ''));

      $('#serialsHintEdit').hide().text('');

      $('#edit_installation_date').val(item.installation_date ?? '');
      $('#edit_installation_location').val(item.installation_location ?? '');
      $('#edit_purchased_from_us').val(isTruthy(item.purchased_from_us) ? '1' : '0');
      $('#edit_purchase_date').val(item.purchase_date ?? '');
      $('#edit_invoice_reference').val(item.invoice_reference ?? '');
      $('#edit_warranty_until').val(item.warranty_until ?? '');
      $('#edit_guarantee_until').val(item.guarantee_until ?? '');
      $('#edit_image_available').val(isTruthy(item.image_available) ? '1' : '0');

      $('#edit_manufacturer').val(item.manufacturer ?? '');
      $('#edit_notes').val(stripHtml(item.notes ?? ''));

      // product select
      if (item.products !== null && item.products !== undefined) {
        $('#edit_product_name').val(String(item.products)).trigger('change');
      } else {
        $('#edit_product_name').val('').trigger('change');
      }

      // department
      $('#edit_department_id').val(item.department_id ? String(item.department_id) : '');

      // installed_by tag
      const $emp = $('#edit_installed_by');
      const name = (item.installed_by || '').trim();
      if ($emp.data('select2')) {
        $emp.val('').trigger('change');
        if (name) {
          const exists = $emp.find('option').filter(function () { return $(this).text().trim() === name; }).length > 0;
          if (!exists) $emp.append(new Option(name, name, true, true));
          $emp.val(name).trigger('change');
        }
      } else {
        $emp.val(name);
      }

      toggleEditLock(true);
      openEdit();
      updateSerialButtons();

      await loadMedia('edit', S.editProductInfoId);
    } catch (e) {
      console.error(e);
      window.Swal?.fire?.('Fehler', 'Produkt konnte nicht geladen werden.', 'error');
    }
  }

  // ------------------------------------------------------------
  // UPDATE (NO reload)
  // ------------------------------------------------------------
  async function updateProduct() {
    const id = $('#edit_id').val();
    if (!id) return;

    const $btn = $('#btnEditSave');
    $btn.prop('disabled', true);

    const installedBy = getInstalledBy($('#edit_installed_by'));
    const selectedProductId = $('#edit_product_name').val();
    const selectedProductName = $('#edit_product_name option:selected').text();

    const payload = {
      _token: csrf(),
      products: selectedProductId,
      product_name: selectedProductName,
      product_count: $('#edit_product_count').val(),

      manufacturer: $('#edit_manufacturer').val(),
      serial_number: $('#edit_serial_number').val(),

      // ✅ controller expects "serial_numbers"
      serial_numbers: $('#edit_serial_numbers_json').val(),

      installation_date: $('#edit_installation_date').val(),
      installation_location: $('#edit_installation_location').val(),

      purchased_from_us: $('#edit_purchased_from_us').val(),
      purchase_date: $('#edit_purchase_date').val(),

      invoice_reference: $('#edit_invoice_reference').val(),
      warranty_until: $('#edit_warranty_until').val(),
      guarantee_until: $('#edit_guarantee_until').val(),

      image_available: $('#edit_image_available').val(),
      installed_by: installedBy,
      department_id: $('#edit_department_id').val(),
      notes: $('#edit_notes').val(),
    };

    try {
      const res = await $.ajax({
        url: CP.api.update(id),
        type: 'PUT',
        data: payload,
      });

      upsertRow(res);
      closeEdit();
      toast('success', 'Gespeichert', 'Produkt erfolgreich aktualisiert.');
    } catch (e) {
      console.error(e);
      window.Swal?.fire?.('Fehler', 'Konnte Produkt nicht aktualisieren.', 'error');
    } finally {
      $btn.prop('disabled', false);
    }
  }

  // ------------------------------------------------------------
  // DELETE (NO reload)
  // ------------------------------------------------------------
  async function deleteProduct(id) {
    const result = await window.Swal?.fire?.({
      title: 'Löschen?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ja, löschen',
    });
    if (!result?.isConfirmed) return;

    try {
      await $.ajax({
        url: CP.api.del(id),
        type: 'DELETE',
        data: { _token: csrf() },
      });
      $(`#productTableBody tr[data-id="${id}"]`).remove();
      window.Swal?.fire?.('Gelöscht', 'Eintrag wurde entfernt', 'success');
    } catch (e) {
      console.error(e);
      window.Swal?.fire?.('Fehler', 'Konnte nicht gelöscht werden', 'error');
    }
  }

  // ------------------------------------------------------------
  // Delegated handlers (blade gets replaced)
  // ------------------------------------------------------------
  $(document).off('click.cpAddSave').on('click.cpAddSave', '#btnAddSave', addProduct);
  $(document).off('click.cpAddNew').on('click.cpAddNew', '#btnAddNew', resetAddForm);
  $(document).off('click.cpEditSave').on('click.cpEditSave', '#btnEditSave', updateProduct);

  // gallery upload
  $(document).off('click.cpAddGalleryUpload').on('click.cpAddGalleryUpload', '#btnAddGalleryUpload', () => uploadMedia('add'));
  $(document).off('click.cpEditGalleryUpload').on('click.cpEditGalleryUpload', '#btnEditGalleryUpload', () => uploadMedia('edit'));

  // gallery search
  $(document).off('input.cpAddGallerySearch').on('input.cpAddGallerySearch', '#add_gallery_search', () => filterMediaGrid('add'));
  $(document).off('input.cpEditGallerySearch').on('input.cpEditGallerySearch', '#edit_gallery_search', () => filterMediaGrid('edit'));

  // gallery delete
  $(document).off('click.cpMediaDelete').on('click.cpMediaDelete', '.btnMediaDelete', function () {
    const mode = $(this).data('mode');
    const id = $(this).data('id');
    if (id) deleteMedia(mode, id);
  });

  // reload media when tab opens
  $(document).off('shown.bs.tab.cpGalleryTab').on('shown.bs.tab.cpGalleryTab', 'a[data-toggle="tab"]', function (e) {
    const target = $(e.target).attr('href');
    if (target === '#addTabGallery' && S.addSavedProductInfoId) loadMedia('add', S.addSavedProductInfoId);
    if (target === '#editTabGallery' && S.editProductInfoId) loadMedia('edit', S.editProductInfoId);
  });

  // ------------------------------------------------------------
  // Public API + global functions (fix "leadProduct is not defined")
  // ------------------------------------------------------------
  window.CustomerProducts = window.CustomerProducts || {};
  window.CustomerProducts = Object.assign(window.CustomerProducts, {
    leadProduct,
    openAdd,
    closeAdd,
    addProduct,
    editProduct,
    updateProduct,
    deleteProduct,
    toggleEditLock,
    openSerials,

    loadMedia,
    uploadMedia,
    deleteMedia,
  });

  // ✅ Critical: inline onclick="leadProduct(this)" needs this
  window.leadProduct = leadProduct;

  // optional legacy globals
  window.addProduct = addProduct;
  window.editProduct = editProduct;
  window.deleteProduct = deleteProduct;
  window.toggleEditLock = toggleEditLock;

  // ------------------------------------------------------------
  // Boot once
  // ------------------------------------------------------------
  function boot() {
    if (S.booted) return;
    S.booted = true;
    updateSerialButtons();
  }

  $(document).ready(boot);

})();
</script>


<!-- Adding new Roof  -->
<script>
    console.log('✅ addNewRoofEditProfile is defined here');

    let roofIndex = {{ isset($roofs) ? count($roofs) : 0 }};

    function addNewRoofEditProfile() {
        console.log('📦 Called addNewRoofEditProfile');
        fetch(`/admin/roofs/partial-edit-profile/${roofIndex}`)
            .then(res => res.text())
            .then(html => {
                const wrapper = document.getElementById('roof-wrapper');
                const newDiv = document.createElement('div');
                newDiv.innerHTML = html;
                wrapper.appendChild(newDiv);
                roofIndex++;
            })
            .catch(err => console.error('Fehler beim Laden des neuen Daches:', err));
    }
</script>

 
<!-- Customer Product List  -->
<script>
    const PLACEHOLDER_IMAGE = "{{ asset('images/icons/placeholder.svg') }}";
    const EMPLOYEE_IMAGE = "{{ asset('images/employee/') }}";
    const GENDER = "{{ asset('images/gender/male.png') }}";
</script>

<script>
    window.AppData = {
        customerStages: @json($customerStages ?? []),
        productInitials: @json($productInitials ?? []),
    };
</script>

 <!-- Loading dashboard and it is objects -->
  <script>
/**
 * ✅ COMPLETE SCRIPT (your full script) + ✅ FieldEmployee avatar in card header
 * What was missing before: we add:
 *  - field employee image html (prod.field_employee)
 *  - fallback to prod.fieldEmployee / prod.fieldEmployee_id if your API still returns those names
 *
 * IMPORTANT (Backend): ensure API returns:
 *    field_employee: { name, lastname, image }  OR null
 * (You already created it in dashboardLoad().)
 */

(() => {
  "use strict";

  // ------------------------------------------------------------
  // GLOBALS / HELPERS
  // ------------------------------------------------------------
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
  const STATUS_MAP_DE = {
    open: 'Offen', lead: 'Anfrage (Lead)', offer: 'Angebot',
    deal: 'Auftrag', project: 'Projekt', archive: 'Archiv',
    junk: 'Junk', feedback: 'Feedback', completed: 'Abgeschlossen',
    ticket: 'Ticket', pause: 'Pausiert', cancel: 'Storniert'
  };

  const escapeHtml = (s) => String(s ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');

  function closeAllKebabMenus(exceptMenu = null) {
    document.querySelectorAll('.kebab-menu.is-open').forEach(m => {
      if (exceptMenu && m === exceptMenu) return;
      m.classList.remove('is-open');
      m.setAttribute('hidden', '');
    });
    document.querySelectorAll('.kebab-btn[aria-expanded="true"]').forEach(b => {
      b.setAttribute('aria-expanded', 'false');
    });
  }

  function closeKebabMenu(menuEl) {
    if (!menuEl) return;
    menuEl.classList.remove('is-open');
    menuEl.setAttribute('hidden', '');
    const id = menuEl.id;
    if (id) {
      const btn = document.querySelector(`.kebab-btn[aria-controls="${CSS.escape(id)}"]`);
      if (btn) btn.setAttribute('aria-expanded', 'false');
    }
  }

  // ------------------------------------------------------------
  // KEBAB MENU (DELEGATED)
  // ------------------------------------------------------------
  function handleGlobalClicks(e) {
    const toggleBtn = e.target.closest('.kebab-btn');
    const menuItem  = e.target.closest('.kebab-item');
    const menuEl    = e.target.closest('.kebab-menu');

    // A) Toggle menu open/close
    if (toggleBtn) {
      e.preventDefault();
      e.stopPropagation();

      const targetId = toggleBtn.getAttribute('aria-controls');
      if (!targetId) return;

      const targetMenu = document.getElementById(targetId);
      if (!targetMenu) return;

      const willOpen = targetMenu.hasAttribute('hidden') || !targetMenu.classList.contains('is-open');

      closeAllKebabMenus(targetMenu);

      if (willOpen) {
        targetMenu.classList.add('is-open');
        targetMenu.removeAttribute('hidden');
        toggleBtn.setAttribute('aria-expanded', 'true');
      } else {
        closeKebabMenu(targetMenu);
      }
      return;
    }

    // B) Menu item click
    if (menuItem) {
      const parentMenu = menuItem.closest('.kebab-menu');
      const action = menuItem.dataset.action || '';

      if (action) {
        e.preventDefault();
        e.stopPropagation();
      }

      closeKebabMenu(parentMenu);

      if (action === 'open-history') {
        const productId     = menuItem.dataset.productId;
        const customerId    = menuItem.dataset.customerId;
        const alternativeId = menuItem.dataset.alternativeId;
        if (productId && customerId && alternativeId) {
          openProductHistory(productId, customerId, alternativeId);
        }
      }

      if (action === 'delete-card') {
        const leadProductId = menuItem.dataset.id;
        if (leadProductId) deleteProductCard(leadProductId);
      }

      if (action === 'reset-cache') {
        if (typeof resetAllSubNavs === 'function') resetAllSubNavs();
      }

      return;
    }

    // C) Click outside
    if (!menuEl) closeAllKebabMenus();
  }

  function initKebabMenuListener() {
    document.removeEventListener('click', handleGlobalClicks, true);
    document.addEventListener('click', handleGlobalClicks, true);

    document.removeEventListener('keydown', handleEscapeClose);
    document.addEventListener('keydown', handleEscapeClose);
  }

  function handleEscapeClose(e) {
    if (e.key === 'Escape') closeAllKebabMenus();
  }

  // ------------------------------------------------------------
  // DASHBOARD NAV / COLLAPSE
  // ------------------------------------------------------------
  window.toggleSection = function (objectId, event) {
    if (event?.target?.closest?.('.kebab-wrap')) return;

    const wrapper = document.getElementById(`wrapper-product-${objectId}`);
    const icon    = document.getElementById(`icon-collapse-${objectId}`);

    if (!wrapper) return;

    const isHidden = wrapper.style.display === 'none';
    wrapper.style.display = isHidden ? 'flex' : 'none';
    if (icon) icon.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(-90deg)';
  };

  // ------------------------------------------------------------
  // NOTES
  // ------------------------------------------------------------
  window.saveNoteField = async function (el, field, customerId, altId, productId) {
    try {
      if (!el) return;

      const wrap = el.closest('.note-field');
      if (!wrap) return;

      const value = String(el.value ?? '');
      el.disabled = true;

      const res = await fetch('/save-customer-card-note', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          ...(CSRF ? { 'X-CSRF-TOKEN': CSRF } : {})
        },
        body: JSON.stringify({
          field,
          value,
          customer_id: customerId,
          alternative_id: altId,
          product_id: productId
        })
      });

      let data = null;
      try { data = await res.json(); } catch (_) {}

      if (!res.ok) throw new Error(data?.message || 'Speichern fehlgeschlagen.');

      const safe = escapeHtml(value).replace(/\n/g, '<br>');
      wrap.innerHTML = `
        <div class="note-view d-flex justify-content-between align-items-start gap-2">
          <div class="note-value text-black" style="white-space:normal; word-break:break-word;">
            ${safe || '<span class="text-muted">–</span>'}
          </div>
          <i class="feather icon-edit text-primary cursor-pointer"
             data-customer-id="${escapeHtml(customerId)}"
             data-alternative-id="${escapeHtml(altId)}"
             data-product-id="${escapeHtml(productId)}"
             onclick="toggleNoteEdit(this, '${field}')"></i>
        </div>
      `;

      if (window.feather) feather.replace();
    } catch (err) {
      console.error(err);
      if (window.Swal) Swal.fire('Fehler', err.message || 'Fehler beim Speichern.', 'error');
    } finally {
      if (el) el.disabled = false;
    }
  };

  window.toggleNoteEdit = function (icon, field) {
    const wrap = icon?.closest?.('.note-field');
    if (!wrap) return;

    const customerId = icon.dataset.customerId;
    const altId      = icon.dataset.alternativeId;
    const productId  = icon.dataset.productId;

    const currentText = wrap.querySelector('.note-value')?.innerText ?? '';

    if (field === 'title') {
      wrap.innerHTML = `
        <input type="text"
               class="form-control note-input"
               value="${escapeHtml(currentText)}"
               onblur="saveNoteField(this, 'title', ${Number(customerId)}, ${Number(altId)}, ${Number(productId)})">
      `;
    } else {
      wrap.innerHTML = `
        <textarea class="form-control note-input"
                  onblur="saveNoteField(this, 'description', ${Number(customerId)}, ${Number(altId)}, ${Number(productId)})">${escapeHtml(currentText)}</textarea>
      `;
    }

    const input = wrap.querySelector('.note-input');
    if (input) {
      input.focus();
      if (input.setSelectionRange) {
        const len = input.value.length;
        input.setSelectionRange(len, len);
      }
    }
  };

  // ------------------------------------------------------------
  // HISTORY + DELETE
  // ------------------------------------------------------------
  function translateStage(stage) {
    const map = {
      offer: 'Angebot',
      deal: 'Auftrag',
      project: 'Montage',
      complete: 'Abschluss',
      completed: 'Abschluss',
      ticket: 'Ticket',
      evaluation: 'Auswertung',
      archive: 'Archiv',
      lead: 'Lead',
      pause: 'Pause',
      junk: 'Junk',
      cancel: 'Storniert'
    };
    return map[stage] || stage;
  }

  function openProductHistory(product_id, customer_id, alternative_id) {
    fetch(`/lead-product/stage-history/${customer_id}/${alternative_id}/${product_id}`)
      .then(res => res.json())
      .then(data => {
        if (!Array.isArray(data.history) || data.history.length === 0) {
          return Swal.fire('Keine Daten', 'Es gibt keine Verlaufsdaten für dieses Produkt.', 'info');
        }

        const timelineItems = data.history.map(entry => {
          const date = new Date(entry.changed_at).toLocaleString('de-DE');
          const user = data.users?.[entry.changed_by] || `Mitarbeiter ID: ${entry.changed_by}`;
          const stage = translateStage(entry.stage);
          const note = entry.description ? `<div class="text-muted mt-1">${escapeHtml(entry.description)}</div>` : '';
          return `
            <div class="timeline-item mb-3">
              <div><strong><i class="feather icon-map-pin"></i> ${escapeHtml(stage)}</strong>
                <small class="text-muted">(${escapeHtml(date)})</small>
              </div>
              <div><i class="feather icon-user"></i> ${escapeHtml(user)}</div>
              ${note}
              <hr class="my-1">
            </div>
          `;
        }).reverse().join('');

        Swal.fire({
          title: 'Phasenverlauf',
          html: `<div style="max-height: 400px; overflow-y: auto; text-align: left;">${timelineItems}</div>`,
          width: 650,
          confirmButtonText: 'Schließen'
        }).then(() => {
          if (window.feather) feather.replace();
        });
      })
      .catch(err => {
        console.error(err);
        Swal.fire('Fehler', 'Verlauf konnte nicht geladen werden.', 'error');
      });
  }

  function deleteProductCard(leadProductId) {
    Swal.fire({
      title: 'Bist du sicher?',
      text: 'Dieses Produkt wird dauerhaft gelöscht.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Ja, löschen!',
      cancelButtonText: 'Abbrechen'
    }).then((result) => {
      if (!result.isConfirmed) return;

      fetch(`/lead-product-lists/${leadProductId}`, {
        method: 'DELETE',
        headers: {
          ...(CSRF ? { 'X-CSRF-TOKEN': CSRF } : {}),
          'Content-Type': 'application/json'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (!data?.success) throw new Error(data?.message || 'Löschen fehlgeschlagen.');
        Swal.fire({ icon: 'success', title: 'Gelöscht!', timer: 1200, showConfirmButton: false });
        setTimeout(() => location.reload(), 1300);
      })
      .catch(err => Swal.fire('Fehler', err.message, 'error'));
    });
  }

  // ------------------------------------------------------------
  // SHOW DASHBOARD
  // ------------------------------------------------------------
  window.showDashboard = (el = null) => {
    if (!el || !el.dataset) el = document.querySelector('.dashboard-btn');

    const customerId    = el?.dataset?.customerId;
    const alternativeId = el?.dataset?.alternativeId;

    if (!customerId || !alternativeId) {
      return Swal.fire('Fehler', 'Kunde oder Alternative fehlt.', 'error');
    }

    const noteList = document.getElementById('note-list');
    if (noteList) {
      noteList.innerHTML = '<div class="text-muted">Lade allgemeine Notizen...</div>';

      fetch(`/customer-notes/${customerId}/${alternativeId}/general`)
        .then(res => res.text())
        .then(html => {
          noteList.innerHTML = html;
          if (window.feather) feather.replace();
          if (typeof initNoteListeners === 'function') initNoteListeners();

          noteList.dataset.customerId    = customerId;
          noteList.dataset.alternativeId = alternativeId;
          noteList.dataset.productId     = '';
          noteList.dataset.noteType      = 'general';

          const title = document.getElementById('note_title');
          if (title) title.textContent = 'ALLGEMEIN';
        })
        .catch(() => {
          noteList.innerHTML = '<div class="text-danger">Fehler beim Laden.</div>';
        });
    }

    const mainContent = document.getElementById('mainContent');
    if (mainContent) {
      mainContent.innerHTML = '<div class="text-muted p-2">Dashboard wird geladen...</div>';

      fetch(`/dashboard/customer/${customerId}/alternative/${alternativeId}`)
        .then(res => res.text())
        .then(html => {
          mainContent.innerHTML = html;
          if (window.feather) feather.replace();
          if (typeof initDashboardListeners === 'function') initDashboardListeners();
          if (typeof window.loadDashboard === 'function') window.loadDashboard();
        })
        .catch(() => {
          mainContent.innerHTML = '<div class="text-danger p-2">Fehler beim Laden des Dashboards.</div>';
        });
    }
  };

  
/* 1. Helper function for toggling product cards */
window.toggleProductFilter = function(badge, targetId) {
  // Toggle the active class on the badge for visual feedback
  badge.classList.toggle('active');

  const target = document.getElementById(targetId);
  if (target) {
    // Check if jQuery is available for smooth animation, otherwise standard JS
    if (window.jQuery) {
      if ($(target).is(':visible')) {
        $(target).slideUp();
      } else {
        $(target).slideDown();
      }
    } else {
      target.style.display = (target.style.display === 'none') ? 'block' : 'none';
    }
  }
};

/* 2. Main Dashboard Loader */
window.loadDashboard = () => {
  const container = document.getElementById('dashboard');
  if (!container) return console.error("❌ #dashboard not found.");

  const customerId = container.dataset.id;
  if (!customerId) {
    container.innerHTML = "<p class='text-danger'>❗ No customer ID provided.</p>";
    return;
  }

  const queryParams = new URLSearchParams({
    customer_id: customerId,
    product_id: window.selectedProductId || '',
    service_id: window.selectedServiceId || '',
    stage: window.selectedStage || '',
    alternative_id: window.selectedAltId || ''
  });

  const services = {
    complete: 'Komplettlösung',
    montage: 'Montage',
    product: 'Produkt',
    plan: 'Planung',
    maintenance: 'Wartung',
    repair: 'Reparatur',
    emergency: 'Notdienst',
    others: 'Sonstiges'
  };
  const interests = {
    intent: 'Kaufabsicht',
    interest: 'Kaufinteresse',
    option: 'Kaufoption'
  };

  function minsToHoursStr(mins, signed = false) {
    if (mins === null || mins === undefined || isNaN(mins)) return '--';
    const sign = mins < 0 ? '-' : (signed && mins > 0 ? '+' : '');
    const hours = Math.abs(mins) / 60;
    const hStr = hours.toLocaleString('de-DE', {
      minimumFractionDigits: 0,
      maximumFractionDigits: 1
    });
    return `${sign}${hStr} Std.`;
  }

  function pickTimeSummary(prod, currentPhaseId) {
    const phases = prod?.time_summary?.phases || {};
    const phaseTs = currentPhaseId ? phases[String(currentPhaseId)] || phases[currentPhaseId] : null;
    return phaseTs || prod?.time_summary?.total || null;
  }

  function computeIconForSummary(ts) {
    if (!ts) return {
      name: 'minus-circle',
      cls: 'text-muted'
    };
    const diff = Number(ts.diff_minutes);
    if (Number.isNaN(diff)) return {
      name: 'minus-circle',
      cls: 'text-muted'
    };
    if (diff > 0) return {
      name: 'thumbs-down',
      cls: 'text-danger'
    };
    if (diff < 0) return {
      name: 'thumbs-up',
      cls: 'text-success'
    };
    return {
      name: 'check-circle',
      cls: 'text-secondary'
    };
  }

  function highlightStageIcons(currentStage, root) {
    if (!root) return;
    root.querySelectorAll('i[data-stage]').forEach(icon => {
      icon.classList.remove('active');
      icon.style.color = '#ccc';
    });
    const activeIcon = root.querySelector(`i[data-stage="${currentStage}"]`);
    if (activeIcon) {
      activeIcon.classList.add('active');
      activeIcon.style.color = '#fff';
      activeIcon.style.backgroundColor = '#73b1d4';
    }
  }

  function translateStage(stage) {
    const map = {
      offer: 'Angebot',
      deal: 'Auftrag',
      project: 'Montage',
      complete: 'Abschluss',
      completed: 'Abschluss',
      ticket: 'Ticket',
      evaluation: 'Auswertung',
      archive: 'Archiv',
      lead: 'Lead',
      pause: 'Pause',
      junk: 'Junk',
      cancel: 'Storniert'
    };
    return map[stage] || stage;
  }

  fetch(`/api/objects-with-products?${queryParams}`)
    .then(res => res.json())
    .then(data => {
      container.innerHTML = '';

      if (!Array.isArray(data) || data.length === 0) {
        container.innerHTML = "<p class='text-warning'>⚠️ Keine Objekte gefunden.</p>";
        return;
      }

      const row = document.createElement('div');
      row.className = 'd-flex flex-wrap';

      data.forEach(object => {
        const block = document.createElement('div');
        block.className = 'house-block mb-0 position-relative';
        block.style.overflow = 'visible';

        const firstProduct = object.products?.[0];

        // --- 1. BADGES GENERATION (With Toggle Logic) ---
        let badgesHtml = '';
        if (object.products?.length) {
          badgesHtml += '<div class="product-badge-group">';
          object.products.forEach(p => {
            const pName = p.initial || p.article_group || '??';
            const initial = pName.substring(0, 2).toUpperCase();
            const statusRaw = (p.status || 'open').toLowerCase();
            const statusDE = STATUS_MAP_DE[statusRaw] || p.status || '';
            const tooltip = `${pName} - Status: ${statusDE}`;

            // Unique ID for targeting the card
            const targetCardId = `card-prod-${object.id}-${p.product_id}`;

            // Added onclick to toggle filter
            // Removed 'active' class by default so they look inactive initially
            badgesHtml += `<span class="product-mini-badge" 
                                 style="cursor:pointer;"
                                 onclick="event.stopPropagation(); toggleProductFilter(this, '${targetCardId}')"
                                 title="${escapeHtml(tooltip)}" 
                                 data-toggle="tooltip">${escapeHtml(initial)}</span>`;
          });
          badgesHtml += '</div>';
        }

        // Toolbar menu
        const menuToolbar = document.createElement('div');
        menuToolbar.className = 'd-flex justify-content-end mb-1';
        menuToolbar.style.position = 'relative';
        menuToolbar.style.zIndex = '100';

        menuToolbar.innerHTML = `
            <div class="kebab-wrap" style="position: relative;">
              <button class="kebab-btn btn btn-sm btn-white border shadow-sm text-dark font-weight-bold"
                      type="button"
                      aria-haspopup="true"
                      aria-expanded="false"
                      aria-controls="kebab-menu-object-${object.id}">
                Optionen <i class="feather icon-chevron-down ml-1"></i>
              </button>

              <div id="kebab-menu-object-${object.id}" class="kebab-menu shadow" hidden>
                <a class="kebab-item" href="/new_object/${object.customer_id}">
                  <i class="feather icon-home text-primary"></i> Neues Objekt
                </a>
                <a class="kebab-item" href="/new_lead_edit/${object.customer_id}/${object.id}">
                  <i class="feather icon-edit text-warning"></i> Objekt bearbeiten
                </a>
                <button class="kebab-item addNewProduct" type="button"
                        data-id="${object.customer_id || ''}"
                        data-alternative-id="${object.id || ''}">
                  <i class="feather icon-plus-circle text-success"></i> Neues Produkt
                </button>
                <button class="kebab-item openObjectProductDrawer" type="button"
                        data-id="${object.customer_id}"
                        data-alternative-id="${object.id}">
                  <i class="feather icon-shuffle text-info"></i> Produkte verschieben
                </button>
                <a class="kebab-item" href="/customer_profit/${object.customer_id}/${object.id}/${firstProduct?.product_id || ''}/${firstProduct?.section_id || ''}">
                  <i class="feather icon-bar-chart text-secondary"></i> Wirtschaftlichkeitsberechnung
                </a>
                <button class="kebab-item" type="button" data-action="reset-cache">
                  <i class="feather icon-refresh-cw text-danger"></i> Cache leeren
                </button>
              </div>
            </div>
          `;

        // Header (Removed Collapse Functionality)
        const header = document.createElement('div');
        header.className = 'house-header d-flex align-items-center p-2 border mb-0 bg-white rounded';
        // Removed onclick="toggleSection..." and cursor pointer for collapse
        // Retained cursor default or pointer if you want it to feel clickable but do nothing, set to default here to imply no collapse.
        header.style.cursor = 'default';

        header.innerHTML = `
            <div class="mr-2 text-muted">
               </div>
            <div class="house-img mr-2">
              <img src="${object.screenshot_image?.src || '/images/icons/placeholder.svg'}"
                   style="width: 100px; height:60px; object-fit: cover; cursor: pointer; border-radius:4px;"
                   onclick="event.stopPropagation(); openSidebarGallery(this)"
                   data-customer-id="${object.screenshot_image?.customer_id || ''}"
                   data-alternative-id="${object.screenshot_image?.alternative_id || ''}"
                   data-address="${escapeHtml(object.screenshot_image?.address || '')}">
            </div>
            <div class="flex-grow-1">
              <div class="font-weight-bold text-primary" style="font-size: 1.05rem;">
                ${escapeHtml(object.object_name || 'Objekt')}
              </div>
              <div class="text-muted small">${escapeHtml(object.street || '')}, ${escapeHtml(object.postcode || '')} ${escapeHtml(object.city || '')}</div>
              ${badgesHtml}
            </div>
          `;

        // Products container
        const productRow = document.createElement('div');
        productRow.className = 'product-card-wrapper';
        productRow.id = `wrapper-product-${object.id}`;

        const stageOrder = ['lead', 'offer', 'deal', 'project', 'completed', 'archive', 'ticket', 'junk', 'cancel'];
        const icons = {
          lead: 'fa fa-rocket',
          offer: 'feather icon-file-text',
          deal: 'fa fa-euro',
          project: 'fa fa-wrench',
          completed: 'feather icon-check-circle',
          archive: 'feather icon-package',
          ticket: 'feather icon-life-buoy',
          junk: 'feather icon-slash',
          cancel: 'feather icon-x-circle'
        };
        const titles = {
          lead: 'Lead',
          offer: 'Angebot',
          deal: 'Auftrag',
          project: 'Montage',
          completed: 'Abgeschlossen',
          archive: 'Archiv',
          ticket: 'Ticket',
          junk: 'Junk',
          cancel: 'Abgesagt'
        };

        (object.products || []).forEach(prod => {
          const latest = prod.history || {};
          const currentPhaseId = latest.phase_id || '';
          const currentActivityId = latest.activity_id || '';
          const carouselDivId = `next_phase_station_${prod.product_id}_${currentPhaseId}`;

          const total = Number(prod.progress?.total) || 0;
          const done = Number(prod.progress?.done) || 0;
          const progress = Number(prod.progress?.value) ?? (total > 0 ? Math.round((done / total) * 100) : 0);
          const progressClass = progress === 100 ? 'bg-primary' : (progress === 0 ? 'bg-secondary' : 'bg-warning');

          const stageKey = (prod.stage_history || []).at(-1)?.stage || prod.stage || '';
          const stageText = translateStage(stageKey);

          const blockedStages = ['junk', 'cancel', 'pause', 'absage'];
          const currentStage = prod.stage?.toLowerCase?.() || '';
          const isBlocked = blockedStages.includes(currentStage);

          const markedImg = latest.marked_by_image ? `${EMPLOYEE_IMAGE}/${latest.marked_by_image}` : GENDER;
          const doneImg = latest.done_by_image ? `${EMPLOYEE_IMAGE}/${latest.done_by_image}` : GENDER;

          const note = (object.card_notes || []).find(n =>
            n.product_id == prod.product_id &&
            n.customer_id == object.customer_id &&
            n.alternative_id == object.id
          );
          const noteTitle = note?.title ?? '';
          const noteDescription = note?.description ?? '';

          // Unique ID for the card
          const uniqueCardId = `card-prod-${object.id}-${prod.product_id}`;
          const plId = prod.p_list_id || prod.p_id || ''; // Needed for task loading

          const card = document.createElement('div');
          // Hide by default
          card.style.display = 'none';
          card.id = uniqueCardId;
          card.className = 'product-status-card card custom-responsive-card mb-2 mt-1';

          const pName = prod.initial || prod.article_group || '??';
          const initialToDisplay = String(pName).substring(0, 2).toUpperCase();

          const activeStage = (prod.stage_history || []).at(-1)?.stage || prod.stage || 'lead';
          const currentIndex = stageOrder.indexOf(activeStage);

          const stageIconsId = `stage-icons-${prod.product_id}-${object.id}`;
          const stageIconsHTML = stageOrder.map((st, index) => {
            let bgColor = '#e9e9e9',
              textColor = '#164194';
            if (index < currentIndex) {
              bgColor = '#93c21c';
              textColor = '#fff5f5';
            } else if (index === currentIndex) {
              bgColor = '#c0d8ea';
              textColor = '#000000';
            }

            return `
                <i class="${icons[st]} stage-icon"
                   data-stage="${st}"
                   data-product-id="${prod.product_id}"
                   data-customer-id="${object.customer_id}"
                   data-alternative-id="${object.id}"
                   title="${escapeHtml(titles[st])}"
                   style="background:${bgColor}; color:${textColor}; font-size:14px; padding:10px; border-radius:50%;
                          box-shadow:0 1px 3px rgba(0,0,0,0.1); margin: 0 4px; transition:transform 0.2s ease; cursor:pointer;">
                </i>
              `;
          }).join('');

          const ts = pickTimeSummary(prod, currentPhaseId);
          let planStr = '--',
            actualStr = '--',
            diffStr = '--',
            percentStr = '--',
            iconName = 'minus-circle',
            iconClass = 'text-muted';
          if (ts) {
            planStr = ts.plan_hm ?? minsToHoursStr(ts.plan_minutes);
            actualStr = ts.actual_hm ?? minsToHoursStr(ts.actual_minutes);
            diffStr = ts.diff_hm ?? minsToHoursStr(ts.diff_minutes, true);
            percentStr = ts.percent_str ?? '--';
            const iconInfo = computeIconForSummary(ts);
            iconName = ts.status_icon || iconInfo.name;
            iconClass = ts.status_icon_class || iconInfo.cls;
          }

          const internalImg = prod.employee?.image ? `${EMPLOYEE_IMAGE}/${prod.employee.image}` : GENDER;

          const fieldEmp = prod.field_employee || prod.fieldEmployee || null;
          const fieldImg = (fieldEmp?.image ? `${EMPLOYEE_IMAGE}/${fieldEmp.image}` : (fieldEmp ? GENDER : ''));
          const fieldName = fieldEmp ? `${fieldEmp?.name || ''} ${fieldEmp?.lastname || ''}`.trim() : '';
          const fieldHtml = fieldEmp ? `
              <img src="${fieldImg}"
                   class="rounded-circle"
                   style="width:24px; height:24px; object-fit:cover; margin-left:-12px; border:2px solid #fff;"
                   title="Außendienst: ${escapeHtml(fieldName || 'Unbekannt')}"
                   data-toggle="tooltip">
            ` : '';

          // --- 2. NAVIGATION BAR (ICONS ONLY) ---
          const topNavBarHtml = `
            <div class="product-top-nav d-flex justify-content-around align-items-center p-2 border-bottom">
                 <button class="btn btn-sm btn-icon btn-white border shadow-sm" title="Aufgabe"
                         onclick="setActiveSubNav(this); loadTask(this)"
                         data-customer-id="${object.customer_id}"
                         data-alternative-id="${object.id}"
                         data-product-id="${prod.product_id}"
                         data-product-list-id="${plId}">
                     <i class="feather icon-clipboard text-primary"></i>
                 </button>

                 <button class="btn btn-sm btn-icon btn-white border shadow-sm" title="Termin"
                         onclick="setActiveSubNav(this); loadCalendar(${object.customer_id}, ${object.id}, ${prod.product_id})">
                     <i class="feather icon-calendar text-warning"></i>
                 </button>

                 <button class="btn btn-sm btn-icon btn-white border shadow-sm" title="Rechnung"
                         onclick="setActiveSubNav(this); loadInvoice(${object.customer_id}, ${object.id}, ${prod.product_id})">
                     <i class="feather icon-file-text text-success"></i>
                 </button>

                 <button class="btn btn-sm btn-icon btn-white border shadow-sm" title="Auftrag"
                         onclick="setActiveSubNav(this); loadSectionPartial(${object.customer_id}, ${object.id}, ${prod.product_id}, 'auftraege')">
                     <i class="feather icon-briefcase text-info"></i>
                 </button>

                 <button class="btn btn-sm btn-icon btn-white border shadow-sm" title="Angebot"
                        onclick="setActiveSubNav(this); loadAngebotPartial(${object.customer_id}, ${object.id}, ${prod.product_id})">
                    <i class="feather icon-file text-secondary"></i>
                </button>
            </div>
          `;

          card.innerHTML = `
              ${topNavBarHtml}

              <div class="card-body p-2 position-relative product-card-body" ${isBlocked ? 'style="pointer-events: none; opacity: 0.4;"' : ''}>

                ${isBlocked ? `
                  <div class="locked-overlay text-center p-2" style="
                      position:absolute; inset:0; background: rgba(255,255,255,0.9);
                      z-index:10; display:flex; flex-direction:column; align-items:center; justify-content:center;
                      pointer-events:auto;">
                    <i class="feather icon-lock text-danger mb-2"
                       style="font-size:24px; cursor:pointer;"
                       onclick="changeProductStage(${prod.product_id}, ${object.customer_id}, ${object.id})"></i>
                    <div class="text-black small text-center" style="font-size:14px; line-height:1.4;">
                      🚫 Dieses Projekt befindet sich im Status<br>
                      "<strong>${escapeHtml(stageText)}</strong>" und ist derzeit gesperrt.
                    </div>
                  </div>
                ` : ''}

                <div class="kebab-wrap" style="position:absolute; top:4px; right:6px; z-index:20; pointer-events:auto;">
                  <button class="kebab-btn btn btn-sm btn-icon"
                          type="button"
                          aria-haspopup="menu"
                          aria-expanded="false"
                          aria-controls="kebab-card-${prod.product_id}-${object.id}">
                    <i class="feather icon-more-vertical"></i>
                  </button>

                  <div id="kebab-card-${prod.product_id}-${object.id}" class="kebab-menu shadow" role="menu" hidden>
                    <button class="kebab-item" type="button" role="menuitem"
                            data-action="open-history"
                            data-product-id="${prod.product_id}"
                            data-customer-id="${object.customer_id}"
                            data-alternative-id="${object.id}">
                      <i class="feather icon-clock mr-1"></i> Verlauf
                    </button>
                    <a class="kebab-item" role="menuitem"
                       href="/customer_profit/${object.customer_id}/${object.id}/${prod.product_id}/${prod.service_id}">
                      <i class="feather icon-bar-chart mr-1"></i> Wirtschaftlichkeitsberechnung
                    </a>
                    <button class="kebab-item text-danger" type="button" role="menuitem"
                            data-action="delete-card"
                            data-id="${prod.id}">
                      <i class="feather icon-trash-2 mr-1"></i> Löschen
                    </button>
                  </div>
                </div>

                <div class="product-card-header">
                  <div class="product-card-main">
                    <div class="rounded-circle bg-primary text-white font-weight-bold d-flex align-items-center justify-content-center product-symbol">
                      ${escapeHtml(initialToDisplay)}
                    </div>

                    <img src="${internalImg}" class="rounded-circle"
                         style="width:24px; height:24px; object-fit:cover; margin-left:-12px; border:2px solid #fff;"
                         title="Innendienst: ${escapeHtml(`${prod.employee?.name || 'Unbekannt'} ${prod.employee?.lastname || ''}`.trim())}"
                         data-toggle="tooltip">

                    ${fieldHtml}

                    <div class="product-meta ml-1">
                      <div>${escapeHtml(prod.department?.name || 'Keine Abteilung')}</div>
                      <div>${escapeHtml(services[prod.service] || '')}</div>
                      <div>${escapeHtml(interests[prod.interest] || '')}</div>
                    </div>
                  </div>

                  <div class="product-card-actions" style="margin-top:28px;">
                    <button class="btn btn-icon rounded-circle open-report-modal"
                            style="background:#93c21c !important; padding:10px !important"
                            data-toggle="tooltip"
                            data-original-title="Kundenprozessbericht"
                            data-product-id="${prod.product_id}"
                            data-stage="${stageKey}"
                            data-service-id="${prod.service_id}"
                            data-customer-id="${object.customer_id}"
                            data-alternative-id="${object.id}">
                      <i class="fa fa-clipboard" style="font-size:16px; color:white;"></i>
                    </button>

                    <button class="btn btn-icon rounded-circle open-phase-modal"
                            style="background:#73b1d4 !important; padding:10px !important"
                            data-toggle="tooltip"
                            data-original-title="Checkliste für Kundenaufgaben"
                            data-product-id="${prod.product_id}"
                            data-stage="${stageKey}"
                            data-service-id="${prod.service_id}"
                            data-customer-id="${object.customer_id}"
                            data-alternative-id="${object.id}">
                      <i class="feather icon-clipboard" style="font-size:16px; color:white;"></i>
                    </button>
                  </div>
                </div>

                <div class="product-version-label">
                  <span>${escapeHtml(prod.initial_with_version || 'NA-V?')}</span>
                </div>

                <div class="mt-2">
                  <div class="d-flex align-items-center mb-1">
                    <strong class="text-primary">Start: <span class="text-black">${escapeHtml(latest.changed_at || '–')}</span></strong>
                  </div>

                  <div class="time-summary-row">
                    <small><span class="text-primary">Plan:</span> ${escapeHtml(planStr)}</small>
                    <small><span class="text-primary">Ist:</span> ${escapeHtml(actualStr)}</small>
                    <small><span class="text-primary">Diff:</span> ${escapeHtml(diffStr)}</small>
                    <small><span class="text-primary">Abw.:</span> ${escapeHtml(percentStr)}</small>
                    <i class="feather icon-${escapeHtml(iconName)} ${escapeHtml(iconClass)}" title="Zeitstatus"></i>
                  </div>

                  <div class="product-progress-row">
                    <div class="progress" title="${done} von ${total} erledigt">
                      <div class="progress-bar ${progressClass}" style="width:${progress}%"></div>
                    </div>
                    <div class="progress-count text-muted">${done}/${total}</div>
                  </div>
                </div>

                <div class="stage-icons-row">
                  <div class="stage-icons" id="${stageIconsId}">${stageIconsHTML}</div>
                </div>

                <hr class="product-section-divider">

                <div>
                  <strong class="text-primary">Projekttitel</strong><br>
                  <div class="note-field" data-field="title">
                    ${noteTitle
                      ? `
                        <div class="note-view d-flex justify-content-between align-items-start gap-2">
                          <div class="note-value text-black">${escapeHtml(noteTitle)}</div>
                          <i class="feather icon-edit text-primary cursor-pointer"
                             data-customer-id="${object.customer_id}"
                             data-alternative-id="${object.id}"
                             data-product-id="${prod.product_id}"
                             onclick="toggleNoteEdit(this, 'title')"></i>
                        </div>
                      `
                      : `
                        <input type="text" class="form-control note-input" placeholder="Titel eingeben"
                               onblur="saveNoteField(this, 'title', ${object.customer_id}, ${object.id}, ${prod.product_id})">
                      `
                    }
                  </div>
                </div>

                <hr class="product-section-divider">

                <div>
                  <strong class="text-primary">Projektbeschreibung</strong><br>
                  <div class="note-field" data-field="description">
                    ${noteDescription
                      ? `
                        <div class="note-view d-flex justify-content-between align-items-start gap-2">
                          <div class="note-value text-black">${escapeHtml(noteDescription).replace(/\n/g,'<br>')}</div>
                          <i class="feather icon-edit text-primary cursor-pointer"
                             data-customer-id="${object.customer_id}"
                             data-alternative-id="${object.id}"
                             data-product-id="${prod.product_id}"
                             onclick="toggleNoteEdit(this, 'description')"></i>
                        </div>
                      `
                      : `
                        <textarea class="form-control note-input" placeholder="Beschreibung eingeben"
                                  onblur="saveNoteField(this, 'description', ${object.customer_id}, ${object.id}, ${prod.product_id})"></textarea>
                      `
                    }
                  </div>
                </div>

                <hr class="product-section-divider">

                <div>
                  <strong class="text-primary">Phase</strong><br>
                  ${escapeHtml(stageText)}
                  <i class="feather icon-edit ml-1 allow-edit-stage primary float-right"
                     onclick="changeProductStage(${prod.product_id}, ${object.customer_id}, ${object.id}, ${prod.service_id})"></i>
                </div>

                <hr class="product-section-divider">

                <div><strong class="text-primary">Arbeitsschritt</strong><br>${escapeHtml(latest.phase_name || '–')}</div>
                <hr class="product-section-divider">
                <div><strong class="text-primary">Aufgabe</strong><br>${escapeHtml(latest.activity_title || '–')}</div>
                <hr class="product-section-divider">

                <div>
                  <strong class="text-primary">Zuständig</strong><br>${escapeHtml(latest.done_by_name || '–')}
                  <img src="${doneImg}" class="rounded-circle ml-1"
                       style="width:40px; height:40px; object-fit:cover; float:right; top:-12px; position:relative;">
                </div>

                <hr class="product-section-divider">

                <div>
                  <strong class="text-primary">Erledigt am</strong><br>${escapeHtml(latest.marked_by_name || '–')}<br>
                  ${escapeHtml(latest.changed_at || '–')}
                  <img src="${markedImg}" class="rounded-circle ml-1"
                       style="width:40px; height:40px; object-fit:cover; float:right; top:-26px; position:relative;">
                </div>

                <hr class="product-section-divider">

                <div>
                  <strong class="text-primary">Nächster Schritt</strong><br>
                  <div id="${carouselDivId}" class="activity-carousel-loader"
                       data-product-id="${prod.product_id}"
                       data-phase-id="${currentPhaseId}"
                       data-activity-id="${currentActivityId}">
                    <i class="fa fa-hourglass-half"></i>
                  </div>
                </div>

              </div>
            `;

          productRow.appendChild(card);

          // Stage icons click
          setTimeout(() => {
            const iconsRoot = card.querySelector(`#${CSS.escape(stageIconsId)}`);
            highlightStageIcons(stageKey, iconsRoot);
            card.querySelectorAll(`#${CSS.escape(stageIconsId)} i`).forEach(iconEl => {
              iconEl.addEventListener('click', () => {
                if (typeof confirmAndChangeStage === 'function') {
                  confirmAndChangeStage(
                    iconEl.dataset.productId,
                    iconEl.dataset.customerId,
                    iconEl.dataset.alternativeId,
                    iconEl.dataset.stage
                  );
                }
              });
            });
          }, 10);

          // Carousel
          if (currentPhaseId && currentActivityId) {
            fetch(`/activity/carousel?phase_id=${currentPhaseId}&activity_id=${currentActivityId}&product_id=${prod.product_id}`)
              .then(res => res.text())
              .then(html => {
                const el = document.getElementById(carouselDivId);
                if (el) el.innerHTML = html;
              })
              .catch(() => {
                const el = document.getElementById(carouselDivId);
                if (el) el.innerHTML = '<span class="text-danger">Fehler</span>';
              });
          }
        });

        block.appendChild(menuToolbar);
        block.appendChild(header);
        block.appendChild(productRow);
        row.appendChild(block);
      });

      container.appendChild(row);

      if (window.feather) feather.replace();
      if (window.$ && $.fn.tooltip) $('[data-toggle="tooltip"]').tooltip();
    })
    .catch(err => {
      console.error("❌ Fetch failed:", err);
      container.innerHTML = "<p class='text-danger'>🚨 Fehler beim Laden des Dashboards.</p>";
    });
};

document.addEventListener("DOMContentLoaded", () => {
  console.log("🚀 Dashboard Script Loaded");
  initKebabMenuListener();
  window.loadDashboard();
});

window.toggleNoteEdit = function (icon, field) {
    // 1. Find the parent wrapper using the class from your HTML
    const wrap = icon.closest('.note-field');
    if (!wrap) {
        console.error("note-field wrapper not found!");
        return;
    }

    // 2. Get the IDs from the clicked icon
    const customerId = icon.getAttribute('data-customer-id');
    const altId      = icon.getAttribute('data-alternative-id');
    const productId  = icon.getAttribute('data-product-id');

    // 3. Extract the current text safely
    const currentText = wrap.querySelector('.note-value')?.innerText?.trim() ?? '';

    // 4. Swap to input/textarea
    if (field === 'title') {
      wrap.innerHTML = `
        <input type="text"
               class="form-control note-input"
               value="${escapeHtml(currentText)}"
               onblur="saveNoteField(this, '${field}', '${customerId}', '${altId}', '${productId}')">
      `;
    } else {
      wrap.innerHTML = `
        <textarea class="form-control note-input"
                  onblur="saveNoteField(this, '${field}', '${customerId}', '${altId}', '${productId}')">${escapeHtml(currentText)}</textarea>
      `;
    }

    // 5. Automatically focus the input so the user can start typing immediately
    const input = wrap.querySelector('.note-input');
    if (input) {
      input.focus();
      if (input.setSelectionRange) {
        const len = input.value.length;
        input.setSelectionRange(len, len);
      }
    }
};

window.loadActivityCarousel = function (phaseId, activityId, productId) {
  const containerId = `next_phase_station_${productId}_${phaseId}`;
  const container = document.getElementById(containerId);
  if (!container) return;

  container.innerHTML = '<i class="fa fa-hourglass-half" ></i>';
  fetch(
    `/activity/carousel?phase_id=${phaseId}&activity_id=${activityId}&product_id=${productId}`,
  )
    .then((res) => res.text())
    .then((html) => {
      container.innerHTML = html;
    })
    .catch(() => {
      container.innerHTML = '<span class="text-danger">Fehler</span>';
    });
};

function parseJSON(json) {
  try {
    return typeof json === "string" ? JSON.parse(json) : json;
  } catch (err) {
    console.warn("⚠️ JSON parse error:", json);
    return [];
  }
}

function getInitials(fullName) {
  if (!fullName) return "–";
  const parts = fullName.trim().split(" ");
  return (parts[0]?.charAt(0) || "") + (parts[1]?.charAt(0) || "");
}

function translateStage(stage) {
  const map = {
    offer: "Angebot",
    deal: "Auftrag",
    project: "Montage",
    complete: "Abschluss",
    completed: "Abschluss",
    ticket: "Ticket",
    evaluation: "Auswertung",
    archive: "Archiv",
    lead: "Lead",
    pause: "Pause",
    junk: "Junk",
  };
  return map[stage] || stage;
}

$(document).on("click", ".open-phase-modal", async function () {
  const $el = $(this);
  const sidebar = document.getElementById("phaseSidebar");
  const sidebarBody = sidebar.querySelector(".phase-sidebar-body");

  const productId = $el.data("product-id");
  const serviceId = $el.data("service-id");
  const stage = $el.data("stage");
  const customerId = $el.data("customer-id");
  const alternativeId = $el.data("alternative-id");

  // ✔ use jQuery for the row + icon
  const productInitial =
    $el.closest(".entry-row").find(".icon").text().trim() || "—";

  try {
    // 🔍 Step 1: Check if customer_stages already exist
    const checkResponse = await $.get("/check-customer-stage", {
      customer_id: customerId,
      alternative_id: alternativeId,
      product_id: productId,
    });

    if (!checkResponse.exists) {
      // ➕ Step 2: Initialize default stages
      await $.post("/initialize-customer-stage", {
        customer_id: customerId,
        alternative_id: alternativeId,
        product_id: productId,
        _token: $('meta[name="csrf-token"]').attr("content"),
      });

      await Swal.fire({
        title: "Standardphasen hinzugefügt!",
        text: "Die Phasen wurden erfolgreich eingerichtet.",
        icon: "success",
        timer: 1200,
        showConfirmButton: false,
      });
    }

    // 📦 Step 3: Load the phase modal/sidebar
    $("#phaseProductInitial").text(productInitial);
    sidebarBody.dataset.customerId = customerId;
    sidebarBody.dataset.alternativeId = alternativeId;
    sidebarBody.dataset.productId = productId;
    sidebarBody.dataset.serviceId = serviceId;

    sidebar.classList.add("open");
    sidebarBody.innerHTML = "<p>Lade...</p>";

    const historyResponse = await $.get("/modal/history", {
      product_id: productId,
      service_id: serviceId,
      stage,
      customer_id: customerId,
      alternative_id: alternativeId,
    });

    // inject HTML
    sidebarBody.innerHTML = historyResponse;

    // ✅ initialize custom accordion on the freshly loaded content
    if (typeof window.initPhaseAccordion === "function") {
      window.initPhaseAccordion(sidebarBody);
    }

    // optional: scroll to active
    const activeHead = sidebarBody.querySelector(".phase-stage-head.is-active");
    if (activeHead) {
      activeHead.scrollIntoView({ behavior: "smooth", block: "start" });
    }

    // ✅ Validation
    initActivityValidation();
  } catch (error) {
    console.error(error);
    Swal.fire({
      title: "Fehler!",
      text: "Es gab ein Problem beim Laden oder Initialisieren der Phase.",
      icon: "error",
    });
  }
});

function initActivityValidation() {
  const currentUserId = "{{ auth()->user()->name }}";

  $(".history-checkbox")
    .off("change")
    .on("change", function (e) {
      const checkbox = this;
      const selectedDoneBy = $(checkbox)
        .closest("tr")
        .find(".done-by-select")
        .val();

      if (!selectedDoneBy || selectedDoneBy !== currentUserId) {
        e.preventDefault();
        checkbox.checked = false;

        Swal.fire({
          icon: "warning",
          title: "Nicht erlaubt",
          text: "Nur der zugewiesene Mitarbeiter darf diese Aufgabe als erledigt markieren.",
        });
      }
    });

  $(".done-by-select").each(function () {
    const select = $(this);
    const row = select.closest("tr");
    const checkbox = row.find(".history-checkbox");

    if (checkbox.prop("checked")) {
      select.prop("disabled", true);

      if (!select.next(".unlock-icon").length) {
        const lock = $(
          '<i class="feather icon-lock ml-1 text-danger cursor-pointer unlock-icon" title="Entsperren?"></i>',
        );
        select.after(lock);

        lock.on("click", function () {
          Swal.fire({
            title: "Passwort erforderlich",
            input: "password",
            inputLabel: "Gib dein Passwort ein",
            inputAttributes: {
              autocapitalize: "off",
              autocomplete: "off",
            },
            showCancelButton: true,
            confirmButtonText: "Bestätigen",
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
              return fetch("/verify-unlock", {
                method: "POST",
                headers: {
                  "Content-Type": "application/json",
                  "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                body: JSON.stringify({
                  password,
                  required_role: "Customer",
                }),
              }).then((res) => {
                if (!res.ok) throw new Error("Fehlgeschlagen");
                return res.json();
              });
            },
            allowOutsideClick: () => !Swal.isLoading(),
          }).then((result) => {
            if (result.isConfirmed && result.value.success) {
              Swal.fire({
                title: "Entsperrt!",
                icon: "success",
              });
              select.prop("disabled", false);
              lock.remove();
            } else {
              Swal.fire({
                icon: "error",
                title: "Nicht erlaubt",
                text: result.value.message || "Zugriff verweigert.",
              });
            }
          });
        });
      }
    }
  });

  $('[data-toggle="tooltip"]').tooltip();
}

// ✅ Close sidebar on ESC (safe + no fake DOMContentLoaded)
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") window.closePhaseSidebar();
});

// ✅ Make it global so inline onclick="closePhaseSidebar()" also works
window.closePhaseSidebar = function closePhaseSidebar() {
  const sidebar = document.getElementById("phaseSidebar");
  if (!sidebar) return;

  sidebar.classList.remove("open");

  // ❌ DO NOT dispatch DOMContentLoaded
  // ✅ If you need to re-init something after close, call it directly here:
  // setTimeout(() => {
  //   if (typeof window.initPhaseAccordion === "function") window.initPhaseAccordion(document);
  // }, 300);
};

$(document).on("click", ".change_stage", function () {
  const $btn = $(this);
  const customer_id = $btn.data("customer-id");
  const alternative_id = $btn.data("alternative-id");
  const product_id = $btn.data("product-id");
  const stage = $btn.data("stage");
  const service = $btn.data("service");
  const service_id = $btn.data("service-id");
  const employee_id = $btn.data("employee-id");
  const department_id = $btn.data("department-id");

  Swal.fire({
    title: "Notiz zur Phase: " + stage.toUpperCase(),
    html: `<div id="quillEditor" style="height: 200px;"></div>`,
    showCancelButton: true,
    confirmButtonText: "Speichern",
    didOpen: () => {
      const quill = new Quill("#quillEditor", { theme: "snow" });
      window.currentQuill = quill;
    },
    preConfirm: () => {
      const description = window.currentQuill.root.innerHTML;

      return fetch(
        `/lead/kanban/${customer_id}/${alternative_id}/${product_id}/${employee_id}/${service}/${stage}/${service_id}/${department_id}`,
        {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
          },
          body: JSON.stringify({
            description,
          }),
        },
      )
        .then((response) => {
          if (!response.ok)
            throw new Error("Backend-Fehler beim Statuswechsel");
          return response.json();
        })
        .then((response) => {
          if (!response.success)
            throw new Error(response.message || "Fehler beim Speichern");
          Swal.fire(
            "Erfolgreich!",
            "Phase und Notiz gespeichert.",
            "success",
          ).then(() => location.reload());
        })
        .catch((err) => {
          Swal.showValidationMessage(`Fehler: ${err.message}`);
        });
    },
  });
});

function deleteProductCard(leadProductId) {
  Swal.fire({
    title: "Bist du sicher?",
    text: "Dieses Produkt wird dauerhaft gelöscht.",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "Ja, löschen!",
    cancelButtonText: "Abbrechen",
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/lead-product-lists/${leadProductId}`, {
        method: "DELETE",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
            .content,
          "Content-Type": "application/json",
        },
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            Swal.fire({
              icon: "success",
              title: "Gelöscht!",
              text: "Das Produkt wurde erfolgreich gelöscht.",
              timer: 1200,
              showConfirmButton: false,
            });

            // Refresh page after short delay
            setTimeout(() => {
              location.reload();
            }, 1300);
          } else {
            throw new Error(data.message || "Löschen fehlgeschlagen.");
          }
        })
        .catch((err) => {
          Swal.fire("Fehler", err.message, "error");
        });
    }
  });
}

(function () {
  /**
   * Initialize the phase accordion inside a root element.
   * Usage:
   *   initPhaseAccordion(document);            // full page
   *   initPhaseAccordion(sidebarBodyElement);  // after AJAX load in sidebar
   */
  function initPhaseAccordion(root) {
    if (!root) root = document;

    const accordion = root.querySelector('[data-nx-accordion="stages"]');
    if (!accordion) return;

    const cards = accordion.querySelectorAll(".phase-stage-card");
    const panels = accordion.querySelectorAll(".phase-stage-panel");

    // Check if any panel is already marked as open
    const hasExplicitOpen = Array.from(panels).some(
      (p) => p.classList.contains("is-open") || p.classList.contains("show"),
    );

    cards.forEach((card, index) => {
      const head = card.querySelector(".phase-stage-head");
      const panel = card.querySelector(".phase-stage-panel");
      const toggle = card.querySelector(".phase-stage-toggle");

      if (!head || !panel) return;

      // avoid double binding
      if (head.dataset.phaseAccordionBound === "1") return;
      head.dataset.phaseAccordionBound = "1";

      const icon = toggle ? toggle.querySelector("i") : null;

      const initiallyOpen =
        panel.classList.contains("is-open") ||
        panel.classList.contains("show") ||
        (!hasExplicitOpen && index === 0); // fallback: open first card if nothing marked

      const isActionClick = (event) => {
        // prevent toggling when clicking action buttons inside the head
        return !!event.target.closest(
          ".change_stages, .suggest-employees-btn, .edit-suggested-employee",
        );
      };

      function applyState(isOpen) {
        if (isOpen) {
          panel.classList.add("is-open");
          panel.classList.remove("show"); // clean old bootstrap class
          panel.style.maxHeight = panel.scrollHeight + "px";

          head.classList.add("is-active");
          head.setAttribute("aria-expanded", "true");
          if (toggle) toggle.setAttribute("aria-expanded", "true");
          if (icon) {
            icon.classList.remove("icon-chevron-down");
            icon.classList.add("icon-chevron-up");
          }
        } else {
          // smooth transition: set current height first
          panel.style.maxHeight = panel.scrollHeight + "px";
          void panel.offsetHeight; // force reflow

          panel.classList.remove("is-open", "show");
          panel.style.maxHeight = "0px";

          head.classList.remove("is-active");
          head.setAttribute("aria-expanded", "false");
          if (toggle) toggle.setAttribute("aria-expanded", "false");
          if (icon) {
            icon.classList.remove("icon-chevron-up");
            icon.classList.add("icon-chevron-down");
          }
        }
      }

      // initial state
      applyState(initiallyOpen);

      function togglePanel(event) {
        if (event && isActionClick(event)) return;
        const isOpen = panel.classList.contains("is-open");
        applyState(!isOpen);
      }

      // click on whole header
      head.addEventListener("click", togglePanel);

      // click on small chevron button
      if (toggle) {
        toggle.addEventListener("click", function (e) {
          e.stopPropagation();
          togglePanel(e);
        });
      }

      // keep height in sync on resize
      window.addEventListener("resize", function () {
        if (panel.classList.contains("is-open")) {
          panel.style.maxHeight = panel.scrollHeight + "px";
        }
      });
    });

    // sync status pills with radios
    accordion.querySelectorAll(".status-pill-group").forEach((group) => {
      group.addEventListener("change", function (e) {
        if (!e.target.matches(".status-option")) return;
        group
          .querySelectorAll(".status-pill")
          .forEach((p) => p.classList.remove("is-active"));
        const label = e.target.closest(".status-pill");
        if (label) label.classList.add("is-active");
      });
    });
  }

  // expose to global
  window.initPhaseAccordion = initPhaseAccordion;

  // auto-init for accordions already in DOM on full page
  document.addEventListener("DOMContentLoaded", function () {
    initPhaseAccordion(document);
  });
})();


})();
</script>



<script>
    document.addEventListener('DOMContentLoaded', () => {
        const csrf = '{{ csrf_token() }}';
        const noteList = document.getElementById('note-list');
        const scrollWrapper = document.getElementById('note-scroll-wrapper');
        const searchInput = document.getElementById('searchNote');

        // ============================================================
        // 1. PROJECT SELECTION (CLICKING A CARD)
        // ============================================================
        document.querySelectorAll('.project-link').forEach(link => {
            link.addEventListener('click', async function (e) {
                e.preventDefault();

                // Visual active state
                document.querySelectorAll('.project-link').forEach(el => el.classList.remove('active'));
                this.classList.add('active');

                // --- CRITICAL DATA EXTRACTION ---
                const objectCustomerId = this.dataset.objectCustomerId;
                const objectAlternativeId = this.dataset.objectAlternativeId;
                
                // 1. Unique ID (For finding the specific note list)
                const uniqueProductId = this.dataset.plId || this.dataset.objectProduct; 
                
                // 2. Generic ID (For database Foreign Key compliance)
                const genericProductId = this.dataset.objectProduct;

                noteList.innerHTML = '<div class="text-muted p-3">Lade Notizen...</div>';

                try {
                    // Fetch using the Unique ID
                    const res = await fetch(`/customer-notes/${objectCustomerId}/${objectAlternativeId}/${uniqueProductId}`);
                    if (!res.ok) throw new Error('Bad response');
                    
                    const html = await res.text();
                    noteList.innerHTML = html;

                    // --- STORE CONTEXT FOR NEW NOTES ---
                    noteList.dataset.customerId    = objectCustomerId;
                    noteList.dataset.alternativeId = objectAlternativeId;
                    
                    // Store BOTH IDs so submitNote() can use them
                    noteList.dataset.uniqueId      = uniqueProductId;  // lead_product_list_id
                    noteList.dataset.genericId     = genericProductId; // product_id
                    
                    noteList.dataset.noteType      = 'product';

                    // Re-init UI helpers
                    if(window.feather) window.feather.replace();
                    initNoteListeners();

                    // Scroll to top
                    if (scrollWrapper) {
                        setTimeout(() => {
                            scrollWrapper.scrollTo({ top: 0, behavior: 'smooth' });
                        }, 100);
                    }
                } catch (err) {
                    console.error("Load Error:", err);
                    noteList.innerHTML = '<div class="text-danger p-3">Fehler beim Laden der Notizen.</div>';
                }
            });
        });

        // Auto-load first project on page load
        const firstProject = document.querySelector('.project-link');
        if (firstProject) firstProject.click();


        // ============================================================
        // 2. COMPOSER UI LOGIC (Fixes Double Opening)
        // ============================================================
        
        // Strict Close Function
        window.closeComposer = () => {
            const composer = document.getElementById('newNoteComposer');
            const backdrop = document.getElementById('noteBackdrop');
            const input = document.getElementById('newNoteText');

            if (composer) composer.classList.remove('open');
            if (backdrop) backdrop.style.display = 'none';
            if (input) input.value = '';
        };

        // Smart Toggle Function
        window.toggleNewNoteArea = (event) => {
            if (event) event.stopPropagation(); // Stop bubbling

            const composer = document.getElementById('newNoteComposer');
            const backdrop = document.getElementById('noteBackdrop');
            
            if (!composer || !backdrop) return;

            const isOpen = composer.classList.contains('open');

            if (isOpen) {
                window.closeComposer();
            } else {
                composer.classList.add('open');
                backdrop.style.display = 'block';
                setTimeout(() => document.getElementById('newNoteText')?.focus(), 100);
            }
        };

        // Bind backdrop click to close only
        const backdrop = document.getElementById('noteBackdrop');
        if (backdrop) {
            backdrop.onclick = (e) => {
                e.stopPropagation();
                window.closeComposer();
            };
        }


        // ============================================================
        // 3. NEW NOTE SUBMISSION (Fixes Duplicate Submission)
        // ============================================================
        window.submitNote = async () => {
            const input = document.getElementById('newNoteText');
            const container = document.getElementById('note-list');
            // Find button inside the composer
            const btn = document.querySelector('#newNoteComposer button');

            const text = (input?.value || '').trim();

            if (!text) return Swal.fire('Hinweis', 'Bitte eine Notiz eingeben.', 'warning');
            if (!container) return;

            // 1. DISABLE BUTTON IMMEDIATELY
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Senden...';
            }

            // 2. RETRIEVE IDs
            const customerId = container.dataset.customerId;
            const alternativeId = container.dataset.alternativeId;
            const uniqueId = container.dataset.uniqueId || null;
            const genericId = container.dataset.genericId || null;

            if (!customerId || !alternativeId) {
                if(btn) btn.disabled = false;
                return Swal.fire('Fehler', 'Kunde oder Alternative fehlt.', 'error');
            }

            // 3. CONSTRUCT PAYLOAD
            const body = {
                customer_id: customerId,
                alternative_id: alternativeId,
                
                // Send Unique ID to specific column
                lead_product_list_id: uniqueId, 
                // Send Generic ID to product_id column (Fixes SQL Error)
                product_id: genericId,          
                
                type: container.dataset.noteType || 'general',
                description: text,
                priority: 'normal',
                color: '#cfe09b'
            };

            try {
                const res = await fetch('/customer-notes/store', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(body)
                });

                if (!res.ok) throw new Error('save failed');
                
                const html = await res.text();
                container.insertAdjacentHTML('afterbegin', html);

                // Re-init UI
                if(window.feather) window.feather.replace();
                initNoteListeners();

                // Close Modal
                window.closeComposer();

            } catch (err) {
                console.error(err);
                Swal.fire('Fehler', 'Notiz konnte nicht gespeichert werden.', 'error');
            } finally {
                // 4. RE-ENABLE BUTTON
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="feather icon-send me-1"></i> Send';
                }
            }
        };


        // ============================================================
        // 4. UTILITIES (Search, Delete, Reply, Edit)
        // ============================================================

        // Search Filter
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                const query = searchInput.value.toLowerCase().trim();
                noteList?.querySelectorAll('.note-card').forEach(card => {
                    const content = card.querySelector('.note-description')?.innerText.toLowerCase() || '';
                    card.style.display = content.includes(query) ? '' : 'none';
                });
            });
        }

        // Delete Note
        window.deleteNote = id => {
            Swal.fire({
                title: 'Bist du sicher?',
                text: 'Die Notiz wird in den Papierkorb verschoben.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja, löschen!',
                cancelButtonText: 'Abbrechen'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/customer-notes/delete/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrf }
                    }).then(() => {
                        document.querySelector(`.note-card[data-id="${id}"]`)?.remove();
                    });
                }
            });
        };

        // Post Reply
        window.postReply = (parentId, input) => {
            const text = (input?.value || '').trim();
            if (!text) return;

            const btn = input.closest('.input-group')?.querySelector('button');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status"></span>`;
            }

            fetch(`/customer-notes/${parentId}/reply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ text })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const card = input.closest('.note-card');
                    let wrapper = card?.querySelector('.reply-wrapper');
                    
                    if (!wrapper && card) wrapper = createReplyContainer(card);
                    if (!wrapper) return;

                    const temp = document.createElement('div');
                    temp.innerHTML = data.reply;
                    const newReply = temp.firstElementChild;

                    if (newReply) {
                        newReply.style.opacity = 0;
                        wrapper.appendChild(newReply);
                        
                        requestAnimationFrame(() => {
                            newReply.style.transition = 'opacity 0.3s ease';
                            newReply.style.opacity = 1;
                            newReply.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        });
                    }

                    input.value = '';
                    if(window.feather) window.feather.replace();
                }
            })
            .catch(() => Swal.fire('Fehler', 'Antwort konnte nicht gesendet werden.', 'error'))
            .finally(() => {
                if(btn) {
                    btn.disabled = false;
                    btn.textContent = 'Senden';
                }
            });
        };

        // Delete Reply
        window.deleteReply = function (id) {
            Swal.fire({
                title: 'Bist du sicher?',
                text: 'Diese Antwort wird endgültig gelöscht.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Löschen'
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/customer-notes/reply/${id}/delete`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': csrf }
                    }).then(() => {
                        document.querySelector(`.reply-item[data-id="${id}"]`)?.remove();
                    });
                }
            });
        };

        function createReplyContainer(card) {
            const container = document.createElement('div');
            container.className = 'reply-wrapper ms-4 mt-2';
            const cardBody = card.querySelector('.card-body');
            const inputGroup = cardBody?.querySelector('.input-group');
            
            if (cardBody) {
                inputGroup ? cardBody.insertBefore(container, inputGroup) : cardBody.appendChild(container);
            }
            return container;
        }

        // Inline Listeners (Description, Color, Priority)
        function initNoteListeners() {
            // Description Auto-Save
            document.querySelectorAll('.inline-edit-description').forEach(input => {
                input.oninput = () => {
                    const id = input.dataset.id;
                    fetch(`/customer-notes/inline-update/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({ field: 'description', value: input.value })
                    });
                };
            });

            // Color Picker
            document.querySelectorAll('.inline-edit-color').forEach(input => {
                input.oninput = () => {
                    const id = input.dataset.id;
                    const value = input.value;
                    const card = document.querySelector(`.note-card[data-id="${id}"]`);
                    if (card) card.style.borderRightColor = value;

                    fetch(`/customer-notes/inline-update/${id}`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ field: 'color', value })
                    });
                };
            });
        }

        // Priority Handler
        let priorityHandlerBound = false;
        function bindPriorityHandlerOnce() {
            if (priorityHandlerBound) return;
            priorityHandlerBound = true;

            document.addEventListener('click', function (e) {
                const item = e.target.closest('.priority-item');
                if (!item) return;

                const { id, value } = item.dataset;

                document.querySelectorAll(`.priority-item[data-id="${id}"]`).forEach(i =>
                    i.classList.remove('active', 'fw-bold', 'text-primary', 'text-danger', 'text-warning')
                );
                item.classList.add('active', 'fw-bold');
                if (value === 'low') item.classList.add('text-primary');
                else if (value === 'high') item.classList.add('text-danger');
                else item.classList.add('text-warning');

                fetch(`/customer-notes/inline-update/${id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                    body: JSON.stringify({ field: 'priority', value })
                });
            });
        }
        bindPriorityHandlerOnce();

        // Edit Reply Text
        window.editReply = function (replyId) {
            const card = document.querySelector(`.reply-item[data-id="${replyId}"]`);
            if (!card) return;

            const textDiv = card.querySelector('.reply-text');
            const oldText = textDiv?.textContent?.trim() || '';

            Swal.fire({
                title: 'Antwort bearbeiten',
                input: 'textarea',
                inputValue: oldText,
                showCancelButton: true,
                confirmButtonText: 'Speichern',
                showLoaderOnConfirm: true,
                preConfirm: (newText) => {
                    if (!newText.trim()) return Swal.showValidationMessage('Darf nicht leer sein.');
                    
                    return fetch(`/customer-notes/reply/${replyId}/update`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ description: newText.trim() })
                    }).then(res => {
                        if (!res.ok) throw new Error('Fehler');
                        return res.json();
                    });
                }
            }).then(result => {
                if (result.isConfirmed && result.value?.success) {
                    textDiv.textContent = result.value.updated_description;
                    Swal.fire({ icon: 'success', title: 'Gespeichert', timer: 1000, showConfirmButton: false });
                }
            });
        };

        // Edit Main Note Text
        window.editNote = function (noteId) {
            const card = document.querySelector(`.note-card[data-id="${noteId}"]`);
            if (!card) return;

            const textDiv = card.querySelector('.note-description');
            const oldText = textDiv?.textContent?.trim() || '';

            Swal.fire({
                title: 'Notiz bearbeiten',
                input: 'textarea',
                inputValue: oldText,
                showCancelButton: true,
                confirmButtonText: 'Speichern',
                showLoaderOnConfirm: true,
                preConfirm: (newText) => {
                    if (!newText.trim()) return Swal.showValidationMessage('Darf nicht leer sein.');

                    return fetch(`/customer-notes/${noteId}/update`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                        body: JSON.stringify({ description: newText.trim() })
                    }).then(res => {
                        if (!res.ok) throw new Error('Fehler');
                        return res.json();
                    });
                }
            }).then(result => {
                if (result.isConfirmed && result.value?.success) {
                    textDiv.textContent = result.value.updated_description;
                    Swal.fire({ icon: 'success', title: 'Gespeichert', timer: 1000, showConfirmButton: false });
                }
            });
        };

        // Restore Deleted
        window.restoreDeletedNote = async function (id) {
            try {
                const res = await fetch(`/notes/restore/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf }
                });
                const data = await res.json();
                if (data.success) {
                    document.querySelector(`.note-card[data-id="${id}"]`)?.remove();
                    Swal.fire({ icon: 'success', title: 'Wiederhergestellt', timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                }
            } catch {
                Swal.fire('Fehler', 'Konnte nicht wiederhergestellt werden.', 'error');
            }
        };

        // Force Delete (Admin)
        window.permanentlyDeleteNote = function (id) {
            Swal.fire({
                title: 'Admin Auth',
                html: `<input type="text" id="adminUser" class="swal2-input" placeholder="User"><input type="password" id="adminPass" class="swal2-input" placeholder="Pass">`,
                confirmButtonText: 'Löschen',
                showCancelButton: true,
                preConfirm: () => {
                    const user = document.getElementById('adminUser').value;
                    const pass = document.getElementById('adminPass').value;
                    if (!user || !pass) return Swal.showValidationMessage('Daten fehlen');
                    return { user, pass };
                }
            }).then(result => {
                if (result.isConfirmed) {
                    fetch(`/notes/delete-permanent/${id}`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                        body: JSON.stringify(result.value)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            document.querySelector(`.note-card[data-id="${id}"]`)?.remove();
                            Swal.fire('Gelöscht!', '', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Fehler', data.message, 'error');
                        }
                    });
                }
            });
        };

        // Modal Loaders
        window.openDeletedNotesModal = async function(noteId) {
            $('#deletedNotesModal').modal('show');
            const el = document.getElementById('deletedNotesModalBody');
            el.innerHTML = 'Lade...';
            try {
                const res = await fetch(`/notes/deleted/${noteId}`);
                const data = await res.json();
                el.innerHTML = data.html || 'Keine Daten.';
                if(window.feather) window.feather.replace();
            } catch { el.innerHTML = 'Fehler.'; }
        };

        window.loadAllDeletedNotes = async function () {
            $('#noteDeletedModalWrapper').modal('show');
            const el = document.getElementById('noteDeletedModalBody');
            el.innerHTML = 'Lade...';
            try {
                const res = await fetch(`/notes/deleted-all`);
                const data = await res.json();
                el.innerHTML = data.html || 'Keine Daten.';
                if(window.feather) window.feather.replace();
            } catch { el.innerHTML = 'Fehler.'; }
        };
    });
</script>
 
<script>

    function postStageChange(customerId, alternativeId, productId, stage, noteHtml) {
        const url = `/lead-product/change-stage/${customerId}/${alternativeId}/${productId}`;

        const payload = {
            stage: stage,
            description: noteHtml
        };

        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },
            body: JSON.stringify(payload)
        }).then(res => {
            if (!res.ok) {
                throw new Error('Backend-Fehler beim Statuswechsel');
            }
            return res.json();
        });
    }

    function changeProductStage(productId, customerId, alternativeId) {
        Swal.fire({
            title: 'Phase wirklich ändern?',
            text: 'Ihre Aufgabenhistorie wird anschließend entsprechend Ihrer letzten Aktivität dargestellt.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, weiter',
            cancelButtonText: 'Abbrechen'
        }).then(confirmRes => {
            if (!confirmRes.isConfirmed) return;

            Swal.fire({
                title: 'Phase auswählen',
                input: 'select',
                inputOptions: {
                    'lead': 'Lead',
                    'offer': 'Angebot',
                    'deal': 'Auftrag',
                    'project': 'Montage',
                    'completed': 'Abgeschlossen',
                    'archive': 'Archiv',
                    'pause': 'Pause',
                    'junk': 'Junk',
                    'cancel': 'Absage'
                },
                inputPlaceholder: 'Neue Phase wählen...',
                showCancelButton: true,
                confirmButtonText: 'Weiter',
            }).then(result => {
                if (!result.isConfirmed || !result.value) return;

                const selectedStage = result.value;

                Swal.fire({
                    title: 'Notiz zur Phase',
                    html: '<div id="quill-editor-modal" style="height:200px;"></div>',
                    showCancelButton: true,
                    confirmButtonText: 'Speichern',
                    willOpen: () => {
                        setTimeout(() => {
                            const modalQuill = new Quill('#quill-editor-modal', { theme: 'snow' });
                            Swal.__quillInstance = modalQuill;
                        }, 10);
                    },
                    preConfirm: () => {
                        return Swal.__quillInstance ? Swal.__quillInstance.root.innerHTML : '';
                    }
                }).then(({ isConfirmed, value: note }) => {
                    if (!isConfirmed || !note) return;

                    postStageChange(customerId, alternativeId, productId, selectedStage, note)
                        .then(res => {
                            if (res.success) {
                                Swal.fire('Gespeichert!', res.message || 'Phase erfolgreich geändert.', 'success')
                                    .then(() => location.reload());
                            } else {
                                Swal.fire('Fehler', res.message || 'Unbekannter Fehler', 'error');
                            }
                        })
                        .catch(err => {
                            Swal.fire('Fehler', err.message || 'Verbindungsfehler beim Speichern.', 'error');
                        });
                });
            });
        });
    }

    window.quill = new Quill('#quill-editor', { theme: 'snow' });

    function confirmAndChangeStage(productId, customerId, alternativeId, selectedStage) {
        Swal.fire({
            title: 'Notiz zur Phase',
            html: '<div id="quill-editor-modal" style="height:200px;"></div>',
            showCancelButton: true,
            confirmButtonText: 'Speichern',
            willOpen: () => {
                setTimeout(() => {
                    const modalQuill = new Quill('#quill-editor-modal', { theme: 'snow' });
                    Swal.__quillInstance = modalQuill;
                }, 10);
            },
            preConfirm: () => {
                return Swal.__quillInstance ? Swal.__quillInstance.root.innerHTML : '';
            }
        }).then(({ isConfirmed, value: note }) => {
            if (!isConfirmed || !note) return;

            postStageChange(customerId, alternativeId, productId, selectedStage, note)
                .then(res => {
                    if (res.success) {
                        Swal.fire('Gespeichert!', res.message || 'Phase erfolgreich geändert.', 'success')
                            .then(() => location.reload());
                    } else {
                        Swal.fire('Fehler', res.message || 'Unbekannter Fehler', 'error');
                    }
                })
                .catch(() => {
                    Swal.fire('Fehler', 'Verbindungsfehler beim Speichern.', 'error');
                });
        });
    }

</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sidebar = document.querySelector('.phase-sidebar-body');
        if (!sidebar) return;

        // Use MutationObserver to wait for dataset to be populated
        const observer = new MutationObserver(() => {
            const { customerId, alternativeId, productId, serviceId } = sidebar.dataset;

            if (customerId && alternativeId && productId && serviceId) {
                observer.disconnect(); // Stop watching once loaded

                const globalData = {
                    customer_id: customerId,
                    alternative_id: alternativeId,
                    product_id: productId,
                    section_id: serviceId
                };

                console.log('✅ Sidebar data loaded:', globalData);
                initPhaseActivityListeners(globalData);
            }
        });

        observer.observe(sidebar, {
            attributes: true,
            attributeFilter: ['data-customer-id', 'data-alternative-id', 'data-product-id', 'data-service-id']
        });
    });

    function initPhaseActivityListeners(globalData) {
    // ---------- helpers ----------
    const fmtMins = (mins) => {
        const s = mins < 0 ? '-' : '';
        mins = Math.abs(mins|0);
        const h = String(Math.floor(mins/60)).padStart(2,'0');
        const m = String(mins%60).padStart(2,'0');
        return `${s}${h}:${m}`;
    };
    const thumb = (diff) => diff > 0
        ? '<i class="feather icon-thumbs-down text-danger" title="Über Plan"></i>'
        : '<i class="feather icon-thumbs-up text-success"  title="Im/Unter Plan"></i>';

    // ---------- DOM fallback calculators ----------
    const toMins = (str) => {
        if (!str) return 0;
        const m = String(str).trim().match(/(\d{1,2}):(\d{2})(?::(\d{2}))?/);
        if (!m) return 0;
        const h = parseInt(m[1]||'0',10);
        const i = parseInt(m[2]||'0',10);
        const s = parseInt(m[3]||'0',10);
        return h*60 + i + Math.floor(s/60);
    };

    function readRowMinutes(row) {
        const planInput = row.querySelector('input[data-type="plan_time"]');
        const planDisp  = row.querySelector('.duration-display');
        const isInput   = row.querySelector('input[data-type="is_time"]');

        const plan = planInput?.value
        ? toMins(planInput.value)
        : planDisp?.textContent
            ? toMins((planDisp.textContent.match(/(\d{1,2}:\d{2}(?::\d{2})?)/)||[])[1])
            : 0;

        const actual = isInput?.value ? toMins(isInput.value) : 0;
        return { plan, actual };
    }

    function computePhaseFromDOM(phaseId) {
        let plan=0, actual=0, cap=0;
        document.querySelectorAll(`tr[data-phase-id="${phaseId}"]`).forEach(tr=>{
        const { plan:p, actual:a } = readRowMinutes(tr);
        plan += p; actual += a; cap += Math.min(a, p);
        });
        const diff = actual - plan;
        const percent = plan > 0 ? Math.round((cap/plan)*100) : 0;
        return { plan_minutes:plan, actual_minutes:actual, diff_minutes:diff, weighted_percent:percent };
    }

    function computeTotalFromDOM() {
        let plan=0, actual=0, cap=0;
        document.querySelectorAll('.phase-sidebar-body tr[data-phase-id]').forEach(tr=>{
        const { plan:p, actual:a } = readRowMinutes(tr);
        plan += p; actual += a; cap += Math.min(a, p);
        });
        const diff = actual - plan;
        const percent = plan > 0 ? Math.round((cap/plan)*100) : 0;
        return { plan_minutes:plan, actual_minutes:actual, diff_minutes:diff, weighted_percent:percent, latest_done_date:null };
    }

    // ---------- appliers ----------
    function applyPhaseSummary(phaseId, phaseData) {
        const wp    = Number.isFinite(+phaseData?.weighted_percent) ? +phaseData.weighted_percent : 0;
        const plan  = +phaseData?.plan_minutes   || 0;
        const actual= +phaseData?.actual_minutes || 0;
        const diff  = +phaseData?.diff_minutes   || 0;

        const $targets = $(`#phase-summary-${phaseId}, #phase-summary-${phaseId}-card, [data-phase-id="${phaseId}"]`);
        if (!$targets.length) return;

        $targets.each(function () {
        const $ph = $(this);
        $ph.find('.plan').text(fmtMins(plan));
        $ph.find('.is').text(fmtMins(actual));
        $ph.find('.diff').text(fmtMins(diff));
        $ph.find('.percent').text(`${wp}%`);
        $ph.find('.smiley').html(thumb(diff));

        const $card = $ph.closest('.stage-card');
        const bar = $card.find('.nx-head .progress .progress-bar')[0];
        if (bar) { bar.style.width = wp + '%'; bar.textContent = wp + '%'; }
        });
    }

    function applyTotalSummary(totalData) {
        const $tot = $('#total-summary');
        if (!$tot.length) return;

        const wp    = Number.isFinite(+totalData?.weighted_percent) ? +totalData.weighted_percent : 0;
        const plan  = +totalData?.plan_minutes   || 0;
        const actual= +totalData?.actual_minutes || 0;
        const diff  = +totalData?.diff_minutes   || 0;

        $tot.find('.plan').text(fmtMins(plan));
        $tot.find('.is').text(fmtMins(actual));
        $tot.find('.diff').text(fmtMins(diff));
        $tot.find('.percent').text(`${wp}%`);
        $tot.find('.smiley').html(thumb(diff));

        if (totalData?.latest_done_date) {
        const [Y, M, D] = totalData.latest_done_date.split('-');
        $('#total_end').text(`${D}.${M}.${Y}`);
        }
    }

    // ---------- smart updaters (DOM first, server only if data) ----------
    const phaseCache = new Map();
    let totalCache = null;

    // ---------- smart updaters (SERVER first, DOM as fallback) ----------
    function updatePhaseSummary(phaseId) {
    const pid = String(phaseId).match(/^(\d+)/)?.[1];
    if (!pid) return;

    // 1) SERVER FIRST
    const params = {
        customer_id: +globalData.customer_id || 0,
        alternative_id: +globalData.alternative_id || 0,
        product_id: +globalData.product_id || 0,
        phase_id: +pid || null,
        t: Date.now() // cache-buster
    };

    $.get('/ajax/times-summary', params, function (data) {
        const srv = {
        plan_minutes: +data?.plan || 0,
        actual_minutes: +data?.is || 0,
        diff_minutes: (+data?.is || 0) - (+data?.plan || 0),
        weighted_percent: Number.isFinite(+data?.weighted_percent) ? +data.weighted_percent : 0
        };
        const serverHasData = (srv.plan_minutes + srv.actual_minutes) > 0 || srv.weighted_percent > 0;

        if (serverHasData) {
        applyPhaseSummary(pid, srv);
        } else {
        // 2) DOM FALLBACK
        applyPhaseSummary(pid, computePhaseFromDOM(pid));
        }
    }).fail(() => {
        // 2) DOM FALLBACK on error
        applyPhaseSummary(pid, computePhaseFromDOM(pid));
    });
    }

    function updateTotalSummary() {
    // 1) SERVER FIRST
    const params = {
        customer_id: +globalData.customer_id || 0,
        alternative_id: +globalData.alternative_id || 0,
        product_id: +globalData.product_id || 0,
        t: Date.now()
    };

    $.get('/ajax/times-summary', params, function (data) {
        const srv = {
        plan_minutes: +data?.plan || 0,
        actual_minutes: +data?.is || 0,
        diff_minutes: (+data?.is || 0) - (+data?.plan || 0),
        weighted_percent: Number.isFinite(+data?.weighted_percent) ? +data.weighted_percent : 0,
        latest_done_date: data?.end ? data.end.split('.').reverse().join('-') : null
                };
                const serverHasData = (srv.plan_minutes + srv.actual_minutes) > 0 || srv.weighted_percent > 0;

                if (serverHasData) {
                applyTotalSummary(srv);
                } else {
                // 2) DOM FALLBACK
                applyTotalSummary(computeTotalFromDOM());
                }
            }).fail(() => {
                // 2) DOM FALLBACK on error
                applyTotalSummary(computeTotalFromDOM());
            });
            }

    // Back-compat wrapper (so existing calls still work)
    function updateTimeSummary({ phase_id = null } = {}, containerSelector) {
        if (phase_id != null) {
        const pid = String(phase_id).match(/^(\d+)/)?.[1];
        if (pid) updatePhaseSummary(pid);
        } else if (containerSelector === '#total-summary') {
        updateTotalSummary();
        }
    }

    // ---------- save (pre-apply DOM, then POST, then apply server) ----------

    // ===== Helpers (loaded once) =================================================
    (function(){
    if (window.__diffRealtimeLoaded) return;
    window.__diffRealtimeLoaded = true;

    window.timeToMinutes = function (str) {
        if (!str) return null;
        const s = String(str).trim();
        const m = s.match(/^(-)?(\d{1,2}):(\d{2})(?::\d{2})?$/);
        if (!m) return null;
        let mins = parseInt(m[2],10) * 60 + parseInt(m[3],10);
        if (m[1]) mins = -mins;
        return mins;
    };
    window.fmtSigned = function (mins) {
        if (mins === null || Number.isNaN(mins)) return '';
        const neg = mins < 0;
        mins = Math.abs(mins);
        const h = Math.floor(mins/60);
        const i = mins % 60;
        return (neg?'-':'') + String(h).padStart(2,'0') + ':' + String(i).padStart(2,'0') + ':00';
    };
    window.clampPct = (n) => Math.max(-999, Math.min(999, n));

    // Ensure there's a dedicated <span class="d-time-text"> to set the middle diff text
    window.ensureDTimeText = function ($cell) {
        let $span = $cell.find('.d-time-text');
        if ($span.length) return $span;
        const $pctP = $cell.find('.d-percent-cell').closest('p').first();
        const $shareP = $cell.find('.d-share-cell').closest('p').last();
        // Insert a middle line if not exists
        const $middle = $('<p class="mb-0 mt-0"><span class="d-time-text"></span></p>');
        if ($pctP.length) {
        $middle.insertAfter($pctP);
        } else if ($shareP.length) {
        $middle.insertBefore($shareP);
        } else {
        $cell.append($middle);
        }
        return $cell.find('.d-time-text');
    };

    // Paint a single row (Δ, %, share)
    window.updateRowDiffUI = function ($row, opts = {}) {
        const planStr = opts.planStr ?? $row.find('input[data-type="plan_time"]').val() ?? '';
        const isStr   = opts.isStr   ?? $row.find('input[data-type="is_time"]').val() ?? '';

        const planM = timeToMinutes(planStr);
        const isM   = timeToMinutes(isStr);

        // Prefer explicit override from server
        let diffM = (typeof opts.diffM === 'number') ? opts.diffM : null;
        if (diffM === null && planM !== null && isM !== null) diffM = isM - planM;

        const $cell = $row.find('.d-time-cell');
        const $timeText = ensureDTimeText($cell);
        const $pct  = $row.find('.d-percent-cell');

        // Reset classes
        $cell.removeClass('text-danger text-success text-muted');
        $pct.removeClass('text-danger text-success text-muted');

        // Δ time text + color
        if (diffM === null) {
        $timeText.text('');
        $cell.addClass('text-muted');
        } else {
        $timeText.text(fmtSigned(diffM));
        if (diffM > 0) $cell.addClass('text-danger');
        else if (diffM < 0) $cell.addClass('text-success');
        else $cell.addClass('text-muted');
        }

        // % vs plan
        if (diffM === null || planM === null || planM <= 0) {
        $pct.text('-').addClass('text-muted');
        } else {
        const pct = clampPct((diffM / planM) * 100);
        const rounded = Math.round(pct);
                $pct.text((pct > 0 ? '+' : '') + rounded + '%')
                    .addClass(pct > 0 ? 'text-danger' : (pct < 0 ? 'text-success' : 'text-muted'));
                }

                // Update phase variance share after row changed
                const phaseId = $row.data('phase-id');
                if (phaseId != null) updatePhaseVarianceShare(String(phaseId));
            };

            // Recompute ".d-share-cell" in a phase
            window.updatePhaseVarianceShare = function (phaseId) {
                const $rows = $(`tr[data-phase-id="${phaseId}"]`);
                const absDiffs = [];

                // Collect |diff|
                $rows.each(function(){
                const $r = $(this);
                const txt = $r.find('.d-time-text').text().trim();
                let dm = timeToMinutes(txt);
                if (dm === null) {
                    const plan = $r.find('input[data-type="plan_time"]').val() || '';
                    const ist  = $r.find('input[data-type="is_time"]').val() || '';
                    const pm = timeToMinutes(plan), im = timeToMinutes(ist);
                    if (pm !== null && im !== null) dm = im - pm;
                }
                if (dm !== null) absDiffs.push(Math.abs(dm));
                });
                const total = absDiffs.reduce((a,b)=>a+b,0);

                // Write shares
                $rows.each(function(){
                const $r = $(this);
                const $share = $r.find('.d-share-cell');
                if (!$share.length) return;

                const txt = $r.find('.d-time-text').text().trim();
                let dm = timeToMinutes(txt);
                if (dm === null) {
                    const plan = $r.find('input[data-type="plan_time"]').val() || '';
                    const ist  = $r.find('input[data-type="is_time"]').val() || '';
                    const pm = timeToMinutes(plan), im = timeToMinutes(ist);
                    if (pm !== null && im !== null) dm = im - pm;
                }

                $share.removeClass('text-danger text-success text-muted');
                if (dm === null || total === 0) {
                    $share.text('-').addClass('text-muted');
                } else {
                    const share = Math.round((Math.abs(dm) / total) * 100);
                    $share.text(share + '%').addClass('text-muted'); // neutral styling
                }
                });
            };
            })();

            // ===== Your function (realtime + server) =====================================
            window.sendHistoryUpdate = function (incomingData, row) {
            const $row = $(row);
            if (!$row || !$row.length) return;

            const payload = {
                activity_id:    incomingData.activity_id  ?? $row.data('activity-id'),
                phase_id:       incomingData.phase_id     ?? $row.data('phase-id'),
                customer_id:    incomingData.customer_id  ?? (window.globalData?.customer_id),
                alternative_id: incomingData.alternative_id ?? (window.globalData?.alternative_id),
                product_id:     incomingData.product_id   ?? (window.globalData?.product_id),
                section_id:     incomingData.section_id   ?? (window.globalData?.section_id),
                is_done:        incomingData.is_done      ?? null,
                done_by:        $row.find('select.done-by-select').val() || null,
                done_date:      $row.find('input[type="date"]').val() || null,
                plan_time:      $row.find('input[data-type="plan_time"]').val() || null,
                is_time:        $row.find('input[data-type="is_time"]').val() || null,
                notes:          $row.find('textarea.note-textarea').val() || null,
                done_reason:    incomingData.done_reason  ?? null
            };

            // === Realtime: paint row immediately (Δ, %, share) ===
            updateRowDiffUI($row, { planStr: payload.plan_time, isStr: payload.is_time });

            // Keep your instant phase/total preview if available
            const phaseIdNum = String(payload.phase_id||'').match(/^(\d+)/)?.[1];
            if (phaseIdNum && typeof window.applyPhaseSummary === 'function' && typeof window.computePhaseFromDOM === 'function') {
                window.applyPhaseSummary(phaseIdNum, window.computePhaseFromDOM(phaseIdNum));
            }
            if (typeof window.applyTotalSummary === 'function' && typeof window.computeTotalFromDOM === 'function') {
                window.applyTotalSummary(window.computeTotalFromDOM());
            }

            $row.addClass('table-warning');

            return fetch("{{ route('ajax.save.customer.history') }}", {
                method: "POST",
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(res => {
                $row.removeClass('table-warning');

                if (res.success) {
                // initials + done_date
                const $initials = $row.find('.mark-by-cell');
                if ($initials.length && res.initials) $initials.text(res.initials);
                const $dateInput = $row.find('input[type="date"]');
                if ($dateInput.length && !($dateInput.val()) && res.done_date) $dateInput.val(res.done_date);

                // Reconcile with server-authoritative diff if provided
                if (typeof res.d_minutes === 'number' || res.d_time) {
                    updateRowDiffUI($row, {
                    planStr: payload.plan_time,
                    isStr:   payload.is_time,
                    diffM:   (typeof res.d_minutes === 'number') ? res.d_minutes : timeToMinutes(res.d_time)
                    });
                } else {
                    updateRowDiffUI($row); // keep realtime values
                }

                // Server summaries (if provided)
                if (res.summaries) {
                    const { phase, total, phase_id } = res.summaries;
                    if (phase && phase_id && typeof window.applyPhaseSummary === 'function') window.applyPhaseSummary(phase_id, phase);
                    if (total && typeof window.applyTotalSummary === 'function') window.applyTotalSummary(total);
                } else {
                    if (phaseIdNum && typeof window.updatePhaseSummary === 'function') window.updatePhaseSummary(phaseIdNum);
                    if (typeof window.updateTotalSummary === 'function') window.updateTotalSummary();
                }

                // Update phase variance shares after server confirmation
                if (phaseIdNum) updatePhaseVarianceShare(phaseIdNum);

                $row.addClass('table-success');
                setTimeout(() => $row.removeClass('table-success'), 1200);
                return true;
                } else {
                console.error("❌ Fehler beim Speichern:", res);
                alert("❌ Speichern fehlgeschlagen");
                return false;
                }
            })
            .catch(err => {
                $row.removeClass('table-warning');
                console.error("❌ AJAX Fehler:", err);
                alert("❌ AJAX Fehler");
                return false;
            });
            };
    // ---------- events ----------



    $(document).on('change blur',
        '.done-by-select, input[type="date"], input[data-type="is_time"], input[data-type="plan_time"], .note-textarea',
        function () {
        const $row = $(this).closest('tr');
        const activityId = $row.data('activity-id');
        const phaseId = $row.data('phase-id');
        const is_done = $row.find('.status-option:checked').val() ?? null;

        const data = { activity_id: activityId, phase_id: phaseId, ...globalData, is_done };

        sendHistoryUpdate(data, $row).then(success => {
            if (success) {
            updatePhaseSummary(String(phaseId).match(/^(\d+)/)?.[1]);
            updateTotalSummary();
            }
        });
        }
    );

    

    // ---------- initial paint (unique phases) ----------
    const phaseIds = new Set();
    document.querySelectorAll('.phase-summary[id^="phase-summary-"]').forEach(el => {
        const m = el.id.match(/^phase-summary-(\d+)/);
        if (m) phaseIds.add(m[1]);
    });
    phaseIds.forEach(pid => updatePhaseSummary(pid));
    updateTotalSummary();
    } 
</script>
 
<script>
    $(document).ready(function () {

        // Toggle to edit
        $(document).on('click', '.edit-duration-btn', function () {
            const wrapper = $(this).closest('.duration-wrapper');
            wrapper.find('.duration-display').addClass('d-none');
            wrapper.find('.duration-edit').removeClass('d-none');
        });

        // Save via AJAX
        $(document).on('click', '.save-duration-btn', function () {
            const wrapper = $(this).closest('.duration-wrapper');
            const activityId = wrapper.data('activity-id');
            const duration = wrapper.find('.duration-input').val();

            if (!duration) {
                toastr.error('Bitte geben Sie eine gültige Zeit ein');
                return;
            }

            $.ajax({
                url: '/phase-activities/update-duration',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    activity_id: activityId,
                    duration: duration
                },
                success: function (response) {
                    if (response.success) {
                        wrapper.find('.duration-display').html(duration + ' <i class="feather icon-edit text-primary ml-1 edit-duration-btn" style="cursor: pointer;"></i>');
                        wrapper.find('.duration-display').removeClass('d-none');
                        wrapper.find('.duration-edit').addClass('d-none');
                        toastr.success('Dauer aktualisiert');
                    } else {
                        toastr.error('Fehler beim Speichern');
                    }
                },
                error: function () {
                    toastr.error('Serverfehler');
                }
            });
        });

    });
</script>

<script>
    $(document).ready(function () {
        $('body').on('click', '.show-done-history', function () {
            const activityId = $(this).data('activity-id');
            const phaseId = $(this).data('phase-id');

            $('#doneHistoryContent').html('<p class="text-muted">Lade Verlauf...</p>');
            $('#doneHistoryModal').modal('show');

            $.get(`/ajax/get-done-history`, {
                activity_id: activityId,
                phase_id: phaseId
            }).done(function (data) {
                if (data.success && data.history.length > 0) {
                    let html = '<ul class="list-group">';
                    data.history.forEach((entry, i) => {
                        html += `
                            <li class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>${entry.marked_by_name}</strong> 
                                        – ${entry.is_done === '1' ? 'Abgeschlossen' : (entry.is_done === 'half' ? 'Teilweise' : 'Nicht erledigt')}
                                        ${entry.done_reason?.percent ? `(${entry.done_reason.percent}%)` : ''}
                                    </div>
                                    <div><small>${entry.changed_at}</small></div>
                                </div>
                                ${entry.done_reason?.reason ? `<div class="text-muted mt-1">${entry.done_reason.reason}</div>` : ''}
                            </li>`;
                    });
                    html += '</ul>';
                    $('#doneHistoryContent').html(html);
                } else {
                    $('#doneHistoryContent').html('<p class="text-muted">Keine Verlaufsdaten gefunden.</p>');
                }
            }).fail(function () {
                $('#doneHistoryContent').html('<p class="text-danger">Fehler beim Laden des Verlaufs.</p>');
            });
        });
    });
</script> 
<script>
    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip({
            html: true,
            container: 'body' // Prevents clipping inside modals or containers
        });
    });

</script>


<script>
    function uploadActivityFile(input) {
        const form = input.closest('form');
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': form.querySelector('input[name=_token]').value
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.closest('label').classList.add('selected');

                Swal.fire({
                    icon: 'success',
                    title: 'Upload Erfolgreich',
                    text: '📎 ' + data.filename,
                    timer: 2000,
                    showConfirmButton: false
                });

                // Optional: reload sidebar or part of UI
                // if (typeof reloadSidebar === 'function') reloadSidebar();

            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    text: data.message || 'Upload fehlgeschlagen.'
                });
            }
        })
        .catch(err => {
            console.error("Upload error", err);
            Swal.fire({
                icon: 'error',
                title: 'Upload-Fehler',
                text: err.message || 'Ein unerwarteter Fehler ist aufgetreten.'
            });
        });
    }
</script> 

 <!-- Task Script:start  -->
 
<script>
/**
 * FULL TASK SCRIPT (FINAL, SINGLE SCRIPT)
 * - Custom centered modal (NO Bootstrap dependency)
 * - Always closes modal after save (hard close)
 * - Loads kanban via fetch into #mainContent
 * - Sortable drag & drop
 * - Search filter
 * - Notes toggle/load/post
 * - Select2 for employees + step employees with avatars
 * - Schritte panel toggle (custom) + fallback if #collapseTaskKeys exists (no bootstrap)
 * - When Schritte open: hide top employee/controller; sync step employees into both employee[] + controller[]
 * - Dynamic steps add/remove + rename indexes + total duration + time calc
 *
 * Requirements: jQuery, Select2, Sortable, Swal, meta csrf token
 */

(() => {
  'use strict';

  // =========================
  // CONFIG
  // =========================
  const UI = {
    containerId: 'mainContent',

    spinnerHtml: `<div class="text-center p-3"><span class="spinner-border text-primary"></span></div>`,
    errorHtml:   `<div class="alert alert-danger">Fehler beim Laden der Aufgaben</div>`,

    viewUrl: `/load/task/view`,
    updateStatusUrl: (taskId) => `/personal_task/update_status/${taskId}`,

    noteStoreUrl: `/ajax/task_note/store`,
    noteListUrl:  (taskId) => `/ajax/task_note/list/${taskId}`,

    storeUrl: "{{ route('personal.task.customer.store') }}",
  };

  // =========================
  // STATE
  // =========================
  window.lastTaskContext = window.lastTaskContext || {
    customerId: null,
    alternativeId: null,
    productId: null,
    productListId: null,
  };

  const sortableInstances = new WeakMap();

  // =========================
  // HELPERS
  // =========================
  const qs  = (sel, root = document) => root.querySelector(sel);
  const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const $doc = $(document);

  function csrf() {
    return qs('meta[name="csrf-token"]')?.content || '';
  }

  function container() {
    return document.getElementById(UI.containerId);
  }

  function showSpinner(el) { el.innerHTML = UI.spinnerHtml; }
  function showError(el, err) { console.error(err); el.innerHTML = UI.errorHtml; }

  function buildUrl(params) {
    const url = new URL(UI.viewUrl, window.location.origin);
    Object.entries(params).forEach(([k, v]) => url.searchParams.set(k, v ?? ''));
    return url.toString();
  }

  function setCreateBtnContext(rootEl, ctx) {
    const btn = rootEl.querySelector('.create_new_task');
    if (!btn) return;
    btn.setAttribute('data-customer-id', ctx.customerId ?? '');
    btn.setAttribute('data-alternative-id', ctx.alternativeId ?? '');
    btn.setAttribute('data-product-id', ctx.productId ?? '');
    btn.setAttribute('data-product-list-id', ctx.productListId ?? '');
  }

  // =========================
  // MODAL (CENTERED, HARD OPEN/CLOSE)
  // =========================
  function ensureOverlayModal() {
    const el = document.getElementById('taskModal');
    if (!el) return null;

    // force custom overlay behavior regardless of bootstrap classes
    el.classList.add('tm-overlay');
    el.setAttribute('role', 'dialog');
    el.setAttribute('aria-modal', 'true');

    return el;
  }

  function cleanupBootstrapArtifacts() {
    document.body.classList.remove('modal-open');
    qsa('.modal-backdrop').forEach(b => b.remove());
  }

  function openModal() {
    const el = ensureOverlayModal();
    if (el) {
      el.classList.add('is-open');
      el.classList.remove('fade', 'show');
      el.style.display = '';                 // let CSS handle display:flex via .is-open
      el.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      cleanupBootstrapArtifacts();
      setTimeout(() => qs('#task_title')?.focus(), 0);
      return;
    }

    // fallback legacy custom modal (.new_task)
    const legacy = qs('.new_task');
    if (legacy) {
      legacy.classList.add('active');
      document.body.style.overflow = 'hidden';
      setTimeout(() => qs('#task_title')?.focus(), 0);
    }
  }

  function closeModal() {
    const el = document.getElementById('taskModal');
    if (el) {
      el.classList.remove('is-open', 'show');
      el.style.display = 'none';             // hard hide to guarantee close
      el.setAttribute('aria-hidden', 'true');
      el.removeAttribute('aria-modal');
    }

    qsa('.new_task.active').forEach(m => m.classList.remove('active'));

    document.body.style.overflow = '';
    cleanupBootstrapArtifacts();

    // close color menu if exists
    qs('#tmColorBtn')?.classList.remove('is-open');
  }

  function bindModalCloseUX() {
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeModal();
    });

    document.addEventListener('click', (e) => {
      const el = document.getElementById('taskModal');

      // click on overlay closes
      if (el && el.classList.contains('tm-overlay') && el.classList.contains('is-open') && e.target === el) {
        closeModal();
        return;
      }

      // close buttons
      if (e.target.closest('.tm-close') || e.target.closest('.close_task_window')) {
        closeModal();
      }
    });
  }

  // =========================
  // LOAD TASKS VIEW (KANBAN)
  // =========================
  async function loadTasks(ctx) {
    const el = container();
    if (!el) return;

    window.lastTaskContext = { ...ctx };
    showSpinner(el);

    try {
      const url = buildUrl({
        customer_id: ctx.customerId,
        alternative_id: ctx.alternativeId,
        product_id: ctx.productId,
        product_list_id: ctx.productListId,
      });

      const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const html = await res.text();

      el.innerHTML = html;
      setCreateBtnContext(el, ctx);

      initSortable();
      initSelect2All(document);
    } catch (err) {
      showError(el, err);
    }
  }

  // Backwards-compatible
  window.loadTask = function (btn) {
    const ctx = {
      customerId: btn.getAttribute('data-customer-id'),
      alternativeId: btn.getAttribute('data-alternative-id'),
      productId: btn.getAttribute('data-product-id'),
      productListId: btn.getAttribute('data-product-list-id'),
    };
    loadTasks(ctx);
  };

  window.loadTaskData = function (customerId, alternativeId, productId, productListId) {
    loadTasks({ customerId, alternativeId, productId, productListId });
  };

  // =========================
  // SORTABLE
  // =========================
  function initSortable() {
    qsa('.kanban-column').forEach(col => {
      if (sortableInstances.has(col)) return;

      const inst = new Sortable(col, {
        group: 'kanban',
        animation: 150,
        onEnd: async (evt) => {
          try {
            const taskId = evt.item?.dataset?.taskId;
            const newStatus = evt.to?.dataset?.status;
            if (!taskId || !newStatus) return;

            await fetch(UI.updateStatusUrl(taskId), {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf(),
                'X-Requested-With': 'XMLHttpRequest',
              },
              body: JSON.stringify({ status: newStatus })
            });
          } catch (e) {
            console.error(e);
          }
        }
      });

      sortableInstances.set(col, inst);
    });
  }
  window.initSortable = initSortable;

  // =========================
  // SEARCH
  // =========================
  window.filterTasks = function () {
    const term = (qs('#taskSearchInput')?.value || '').toLowerCase();
    qsa('.task-card').forEach(card => {
      const title = (card.dataset.title || '').toLowerCase();
      card.style.display = title.includes(term) ? '' : 'none';
    });
  };

  // =========================
  // NOTES
  // =========================
  window.toggleTaskNote = function (taskId) {
    const wrapper = document.getElementById(`task-note-wrapper-${taskId}`);
    if (!wrapper) return;

    const hidden = getComputedStyle(wrapper).display === 'none';
    wrapper.style.display = hidden ? 'block' : 'none';
    if (hidden) loadTaskNotes(taskId);
  };

  window.submitTaskNote = async function (event, taskId) {
    event.preventDefault();

    const form = event.target;
    const comment = form?.comment?.value?.trim();
    if (!comment) return;

    try {
      await fetch(UI.noteStoreUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf(),
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ task_id: taskId, comment })
      });

      form.reset();
      loadTaskNotes(taskId);
    } catch (e) {
      console.error(e);
    }
  };

  async function loadTaskNotes(taskId) {
    try {
      const res = await fetch(UI.noteListUrl(taskId), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const html = await res.text();
      const target = document.getElementById(`comment-list-${taskId}`);
      if (target) target.innerHTML = html;
    } catch (e) {
      console.error(e);
    }
  }
  window.loadTaskNotes = loadTaskNotes;

  // =========================
  // SELECT2 (AVATARS)
  // =========================
  function avatarFallback() {
    return 'data:image/svg+xml;utf8,' + encodeURIComponent(
      `<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64">
        <rect width="64" height="64" rx="12" fill="#c0d8ea"/>
        <circle cx="32" cy="26" r="12" fill="#74b2d4"/>
        <path d="M14 56c3-12 13-18 18-18s15 6 18 18" fill="#74b2d4"/>
      </svg>`
    );
  }

  function optImage(optionEl) {
    const img = $(optionEl).data('image');
    return img || avatarFallback();
  }

  function formatEmployee(option) {
    if (!option.id) return option.text;
    const img = optImage(option.element);
    const text = option.text || '';
    return `
      <div style="display:flex;align-items:center;gap:10px;">
        <img src="${img}" style="width:22px;height:22px;border-radius:999px;object-fit:cover;">
        <span style="font-weight:800;">${text}</span>
      </div>
    `;
  }

  function initSelect2($el) {
    if (!$el || !$el.length) return;
    if ($el.data('select2')) return;

    $el.select2({
      width: '100%',
      placeholder: 'Mitarbeiter auswählen',
      closeOnSelect: false,
      templateResult: formatEmployee,
      templateSelection: (opt) => opt.text,
      escapeMarkup: m => m
    });
  }

  function initSelect2All(root = document) {
    if (!window.jQuery || !$.fn.select2) return;

    initSelect2($('#employee'));
    initSelect2($('#controller'));

    $(root).find('select[name^="key"][name$="[employee_id][]"]').each(function () {
      initSelect2($(this));
    });
  }

  // =========================
  // SCHRITTE PANEL (CUSTOM + FALLBACK)
  // =========================
  function stepsOpen() {
    const tm = document.getElementById('tmSteps');
    if (tm && tm.classList.contains('is-open')) return true;

    const legacy = document.getElementById('collapseTaskKeys');
    if (legacy && legacy.classList.contains('show')) return true;

    return false;
  }

  function setTopSectionsVisibility() {
    const hide = stepsOpen();
    const emp  = document.getElementById('task_employee_section');
    const ctrl = document.getElementById('task_controller_section'); // optional wrapper id

    if (emp)  emp.style.display  = hide ? 'none' : '';
    if (ctrl) ctrl.style.display = hide ? 'none' : '';
  }

  function collectStepEmployeeIds() {
    const ids = new Set();
    $('select[name^="key"][name$="[employee_id][]"]').each(function () {
      const vals = $(this).val() || [];
      vals.forEach(v => ids.add(String(v)));
    });
    return Array.from(ids);
  }

  function syncStepsToTop() {
    if (!stepsOpen()) return;

    const ids = collectStepEmployeeIds();
    if (!ids.length) return;

    if ($('#employee').length)   $('#employee').val(ids).trigger('change.select2');
    if ($('#controller').length) $('#controller').val(ids).trigger('change.select2');
  }

  function toggleStepsPanel() {
    const tm = document.getElementById('tmSteps');
    if (tm) {
      tm.classList.toggle('is-open');
      setTopSectionsVisibility();
      syncStepsToTop();
      initSelect2All(document);
      return;
    }

    // legacy collapse without bootstrap js
    const legacy = document.getElementById('collapseTaskKeys');
    if (legacy) {
      const isOpen = legacy.classList.toggle('show');
      legacy.style.display = isOpen ? 'block' : 'none';
      setTopSectionsVisibility();
      syncStepsToTop();
      initSelect2All(document);
    }
  }

  // Toggle clicks
  $doc.on('click', '#tmSteps [data-toggle="tm-collapse"]', function (e) {
    e.preventDefault();
    toggleStepsPanel();
  });

  $doc.on('click', '[data-target="#collapseTaskKeys"], [data-bs-target="#collapseTaskKeys"]', function (e) {
    // if bootstrap collapse is present, let it handle; otherwise we handle
    const hasBootstrapCollapse = !!(window.bootstrap && window.bootstrap.Collapse);
    if (hasBootstrapCollapse) return;
    e.preventDefault();
    toggleStepsPanel();
  });

  // Sync when steps select changes
  $doc.on('change', 'select[name^="key"][name$="[employee_id][]"]', function () {
    syncStepsToTop();
  });

  // =========================
  // COLOR MENU (OPTIONAL)
  // =========================
  function bindColorMenu() {
    const btn  = qs('#tmColorBtn');
    const menu = qs('#tmColorMenu');
    const sw   = qs('#tmSwatch');
    const inp  = qs('#color');

    if (!btn || !menu || !sw || !inp) return;

    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      btn.classList.toggle('is-open');
    });

    menu.addEventListener('click', (e) => {
      const item = e.target.closest('.tm-menu-item');
      if (!item) return;
      const hex = item.getAttribute('data-color');
      inp.value = hex;
      sw.style.background = hex;
      btn.classList.remove('is-open');
    });

    document.addEventListener('click', () => btn.classList.remove('is-open'));
  }

  // =========================
  // STEPS TABLE: ADD/REMOVE/REINDEX + TOTAL DURATION
  // =========================
  function buildEmployeeOptionsHtml() {
    const list = window.employeeOptions || [];
    if (list.length) {
      return list.map(emp => {
        const img = emp.image || avatarFallback();
        const name = `${emp.name || ''} ${emp.lastname || ''}`.trim();
        return `<option value="${emp.id}" data-image="${img}">${name}</option>`;
      }).join('');
    }

    // fallback: clone options from #employee
    const $base = $('#employee option');
    return $base.map(function () {
      const val = this.value;
      const img = $(this).data('image') || avatarFallback();
      const text = $(this).text();
      return `<option value="${val}" data-image="${img}">${text}</option>`;
    }).get().join('');
  }

  function ensureDurationClass() {
    $('#key_task tbody input[name$="[duration]"]').addClass('task-duration');
  }

  function updateRowIndexes() {
    const $rows = $('#key_task tbody tr');
    $rows.each(function (index) {
      $(this).find('td:first').text(index + 1);

      $(this).find('input, textarea, select').each(function () {
        const $f = $(this);
        const name = $f.attr('name');
        if (!name) return;

        $f.attr('name', name.replace(/\[\d+]/, `[${index}]`));
      });
    });
  }

  function updateTotalDuration() {
    let total = 0;
    $('.task-duration').each(function () {
      total += parseInt($(this).val(), 10) || 0;
    });

    const allowed = parseInt($('#total_time').val(), 10) || 0;
    const diff = allowed - total;

    if ($('#key_total_time').length) {
      $('#key_total_time').text(diff >= 0 ? `${diff} Std` : `Überschreitung um ${Math.abs(diff)} Std!`);
    }

    if (allowed > 0 && total > allowed) {
      Swal.fire({
        icon: "error",
        title: "⚠ Zeitüberschreitung!",
        text: `Die gesamte Dauer der Aufgaben beträgt ${total} Stunden, überschreitet jedoch die geplanten ${allowed} Stunden.`,
      });
    }
  }

  $doc.on('click', '.add-task-steps', function () {
    const $tbody = $('#key_task tbody');
    if (!$tbody.length) return;

    const idx = $tbody.find('tr').length;
    const employeeOptionsHtml = buildEmployeeOptionsHtml();

    const newRow = `
      <tr>
        <td><strong>${idx + 1}</strong></td>
        <td><input type="text" name="key[${idx}][task]" class="tm-input" placeholder="Schritt…"></td>
        <td><input type="number" name="key[${idx}][duration]" class="tm-input task-duration" placeholder="Std"></td>
        <td>
          <select name="key[${idx}][employee_id][]" class="tm-select employee-select" multiple style="width:100%">
            ${employeeOptionsHtml}
          </select>
        </td>
        <td><textarea name="key[${idx}][key_description]" class="tm-textarea" style="min-height:44px" placeholder="Beschreibung…"></textarea></td>
        <td style="white-space:nowrap;">
          <button type="button" class="tm-iconbtn add-task-steps" title="Hinzufügen"><i class="fa fa-plus"></i></button>
          <button type="button" class="tm-iconbtn remove-task-steps" title="Entfernen"><i class="fa fa-minus"></i></button>
        </td>
      </tr>
    `;

    const $row = $(newRow);
    $tbody.append($row);

    initSelect2($row.find('select[name^="key"][name$="[employee_id][]"]'));

    ensureDurationClass();
    updateRowIndexes();
    updateTotalDuration();
    syncStepsToTop();
  });

  $doc.on('click', '.remove-task-steps', function () {
    const $tbody = $('#key_task tbody');
    const $rows = $tbody.find('tr');

    if ($rows.length <= 1) {
      Swal.fire({ icon: "warning", title: "Achtung", text: "Es muss mindestens ein Aufgabenschritt vorhanden sein." });
      return;
    }

    const $tr = $(this).closest('tr');
    $tr.find('select').each(function () {
      const $s = $(this);
      if ($s.data('select2')) $s.select2('destroy');
    });
    $tr.remove();

    ensureDurationClass();
    updateRowIndexes();
    updateTotalDuration();
    syncStepsToTop();
  });

  $doc.on('input', '.task-duration', function () {
    updateTotalDuration();
  });

  // =========================
  // TIME CALC (START/DUE/DUE_TIME)
  // =========================
  function bindTimeCalc() {
    const startDateInput = document.getElementById("start_date");
    const dueDateInput   = document.getElementById("due_date");
    const dueTimeInput   = document.getElementById("due_time");
    const totalDayInput  = document.getElementById("total_day");
    const totalTimeInput = document.getElementById("total_time");

    if (!startDateInput || !dueDateInput || !totalDayInput || !totalTimeInput) return;

    function calculate() {
      const startDateVal = startDateInput.value;
      const dueDateVal   = dueDateInput.value;

      const startDate = new Date(startDateVal);
      const dueDate   = new Date(dueDateVal);

      if (!startDateVal || !dueDateVal || isNaN(startDate) || isNaN(dueDate)) {
        totalDayInput.value = "";
        totalTimeInput.value = "";
        return;
      }

      const workHoursPerDay = 24;
      let totalDays = 0;
      let totalWorkingHours = 0;

      const temp = new Date(startDate);
      while (temp <= dueDate) {
        const day = temp.getDay();
        if (day !== 0 && day !== 6) {
          totalDays++;
          totalWorkingHours += workHoursPerDay;
        }
        temp.setDate(temp.getDate() + 1);
      }

      if (dueTimeInput && dueTimeInput.value) {
        const [dueHour, dueMinute] = dueTimeInput.value.split(":").map(Number);
        const remainingHours = dueHour + (dueMinute > 0 ? 1 : 0);
        totalWorkingHours -= workHoursPerDay;
        totalWorkingHours += remainingHours;
      }

      totalDayInput.value = totalDays;
      totalTimeInput.value = totalWorkingHours;

      updateTotalDuration();
    }

    startDateInput.addEventListener("change", calculate);
    dueDateInput.addEventListener("change", calculate);
    if (dueTimeInput) dueTimeInput.addEventListener("change", calculate);

    calculate();
  }

  // =========================
  // SUBMIT TASK FORM
  // =========================
  function validateForm() {
    const errors = [];
    const taskTitle = ($('#task_title').val() || '').trim();
    const dueDate   = ($('#due_date').val() || '').trim();

    if (!taskTitle) errors.push('Bitte geben Sie einen Aufgabentitel ein.');
    if (!dueDate)   errors.push('Bitte wählen Sie ein Fälligkeitsdatum.');

    return errors;
  }

  function submitTaskForm(closeAfterSave) {
    const errors = validateForm();
    if (errors.length) {
      Swal.fire({ icon: 'warning', title: 'Formular ungültig', html: errors.join('<br>') });
      return;
    }

    // ensure step employees copied to employee/controller
    syncStepsToTop();

    const formData = $('#task_form').serialize();

    $.ajax({
      type: 'POST',
      url: UI.storeUrl,
      data: formData,
      success: function () {
        // HARD CLOSE FIRST (guarantees modal disappears)
        closeModal();

        Swal.fire({
          icon: 'success',
          title: 'Erfolgreich gespeichert!',
          text: 'Die Aufgabe wurde erfolgreich gespeichert.',
        }).then(() => {
          const { customerId, alternativeId, productId, productListId } = window.lastTaskContext || {};
          if (customerId && window.loadTaskData) window.loadTaskData(customerId, alternativeId, productId, productListId);
        });

        if (!closeAfterSave) $('#task_form')[0].reset();
      },
      error: function (xhr) {
        const msg = xhr.responseJSON?.message || 'Ein Fehler ist aufgetreten.';
        Swal.fire({ icon: 'error', title: 'Fehler', text: msg });
      }
    });
  }

  // =========================
  // GLOBAL EVENTS (OPEN/CLOSE/SAVE)
  // =========================
  document.addEventListener('click', (e) => {
    // OPEN
    const createBtn = e.target.closest('.create_new_task');
    if (createBtn) {
      const customerId    = createBtn.dataset.customerId || createBtn.getAttribute('data-customer-id') || '';
      const alternativeId = createBtn.dataset.alternativeId || createBtn.getAttribute('data-alternative-id') || '';
      const productId     = createBtn.dataset.productId || createBtn.getAttribute('data-product-id') || '';

      const c = document.getElementById('select_customer_id');
      const a = document.getElementById('select_alternative_id');
      const p = document.getElementById('select_product_id');

      if (c) c.value = customerId;
      if (a) a.value = alternativeId;
      if (p) p.value = productId;

      openModal();

      setTimeout(() => {
        initSelect2All(document);
        ensureDurationClass();
        setTopSectionsVisibility();
        bindTimeCalc();
        updateTotalDuration();
      }, 0);

      return;
    }

    // SAVE
    if (e.target.closest('.save-task-close')) {
      submitTaskForm(true);
      return;
    }
    if (e.target.closest('.save-task-continue')) {
      submitTaskForm(false);
      return;
    }
  });

  // =========================
  // INIT
  // =========================
  document.addEventListener('DOMContentLoaded', () => {
    bindModalCloseUX();
    bindColorMenu();

    initSortable();
    initSelect2All(document);

    ensureDurationClass();
    setTopSectionsVisibility();
    bindTimeCalc();
    updateTotalDuration();
  });

})();
</script>

 <!-- Task Script : End  -->
<!-- Deadline Script Toggle: end  -->
<script>
    $(document).ready(function () {
        const $select = $('#customerLeadProductSelect');
        const $switch = $('#customerSwitch');
        const $container = $('#customerSelectContainer');

        // Initialize Select2
        $select.select2({
            placeholder: 'Kunde suchen...',
            ajax: {
                url: '{{ route("lead.product.list.ajax") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return { q: params.term };
                },
                processResults: function (data) {
                    return {
                        results: data.results.map(function (item) {
                            return {
                                id: item.id,
                                text: item.text,
                                html: item.html,
                                alternative_id: item.alternative_id,
                                product_id: item.product_id
                            };
                        })
                    };
                },
                cache: true
            },
            templateResult: function (data) {
                if (data.loading) return data.text;
                return $(data.html);
            },
            templateSelection: function (data) {
                // Fill hidden fields when selected
                if (data.alternative_id) {
                    $('#select_alternative_id').val(data.alternative_id);
                }
                if (data.product_id) {
                    $('#select_product_id').val(data.product_id);
                }
                return data.text;
            },
            escapeMarkup: function (markup) {
                return markup;
            }
        });

        // Show/hide section based on switch
        $switch.on('change', function () {
            if ($(this).is(':checked')) {
                $container.slideDown();
                $(this).val(1);
            } else {
                $container.slideUp();
                $select.val(null).trigger('change');
                $('#select_alternative_id').val('');
                $('#select_product_id').val('');
                $(this).val(0);
            }
        });

        // Trigger correct state on page load
        if ($switch.is(':checked')) {
            $container.show();
        } else {
            $container.hide();
        }
    });
</script>

<script>
    // 🔁 This function initializes the power calculator
    function initPowerCalculator(context = document) {
        const $fields = $(context).find('input[name="power_household"], input[name="power_heatpump"], input[name="power_electric_car"], input[name="power_other"]');
        const $totalField = $(context).find('#power_total');

        function validateNumber(input) {
            const val = input.val().trim().replace(',', '.');
            const num = parseFloat(val);
            if (isNaN(num) || num < 0) {
                input.addClass('is-invalid');
                return 0;
            } else {
                input.removeClass('is-invalid');
                return num;
            }
        }

        function calculateAndDisplay() {
            const household   = validateNumber($(context).find('input[name="power_household"]'));
            const heatpump    = validateNumber($(context).find('input[name="power_heatpump"]'));
            const electricCar = validateNumber($(context).find('input[name="power_electric_car"]'));
            const other       = validateNumber($(context).find('input[name="power_other"]'));

            const total = household + heatpump + electricCar + other;

            // Update main total field
            $totalField.val(total.toFixed(2).replace('.', ','));

            // Show annual consumption (optional badge)
            let $yearInfo = $(context).find('#power_total_year');
            if (!$yearInfo.length) {
                $yearInfo = $('<small id="power_total_year" class="form-text text-muted"></small>').insertAfter($totalField);
            }
            const yearly = total * 365;
            $yearInfo.text('≈ ' + yearly.toFixed(0).toLocaleString('de-DE') + ' kWh / Jahr');
        }

        // Bind calculation on input change
        $fields.off('input.powercalc').on('input.powercalc', calculateAndDisplay);

        // Initial run
        calculateAndDisplay();
    }

    // 🧲 Example usage after tab loads partial content
    $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
        const target = $($(e.target).attr('href'));
        if (target.length) {
            initPowerCalculator(target);
        }
    });

    // Also call once in case tab is active on page load
    $(document).ready(function () {
        const activeTabPane = $('.tab-pane.active');
        if (activeTabPane.length) {
            initPowerCalculator(activeTabPane);
        }
    });
</script> 
<!-- Custoemr Note Save Title and Description in Profile  -->
 <script>
    function openNoteEditor() {
        const container = document.getElementById('noteContainer');

        const customerId = container.dataset.customer;
        const alternativeId = container.dataset.alternative;
        const productId = container.dataset.product;
        const existingTitle = container.dataset.title || '';
        const existingDescription = container.dataset.description || '';

        Swal.fire({
            title: 'Notiz bearbeiten',
            html: `
                <input type="text" id="noteTitleInput" class="swal2-input" placeholder="Titel" value="${existingTitle}">
                <textarea id="noteDescriptionInput" class="swal2-textarea" placeholder="Beschreibung" rows="4">${existingDescription}</textarea>
            `,
            showCancelButton: true,
            confirmButtonText: 'Speichern',
            cancelButtonText: 'Abbrechen',
            focusConfirm: false,
            preConfirm: () => {
                const title = document.getElementById('noteTitleInput').value;
                const description = document.getElementById('noteDescriptionInput').value;

                if (!title.trim() && !description.trim()) {
                    Swal.showValidationMessage('Titel oder Beschreibung erforderlich');
                    return false;
                }

                return { title, description };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                saveNote(customerId, alternativeId, productId, result.value.title, result.value.description);
            }
        });
    }

    function saveNote(customerId, alternativeId, productId, title, description) {
        const data = new FormData();
        data.append('customer_id', customerId);
        data.append('alternative_id', alternativeId);
        data.append('product_id', productId);
        data.append('title', title);
        data.append('description', description);

        fetch("{{ route('customer_card_notes.store') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: data
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const newTitle = data.note.title || 'Ohne Titel';
                const newDesc = (data.note.description || '').replace(/\n/g, "<br>");

                // Update the view with new content and new data attributes
                const html = `
                    <div id="noteView" onclick="openNoteEditor()" style="cursor: pointer;">
                        <h5 class="fw-bold mb-2" id="noteTitle">${newTitle}</h5>
                        <div id="noteDescription">${newDesc}</div>
                    </div>
                `;

                const noteContainer = document.getElementById('noteContainer');
                noteContainer.innerHTML = html;
                noteContainer.dataset.title = data.note.title;
                noteContainer.dataset.description = data.note.description;

                Swal.fire('Gespeichert!', 'Die Notiz wurde gespeichert.', 'success');
            } else {
                Swal.fire('Fehler', data.message || 'Fehler beim Speichern', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            Swal.fire('Fehler', 'Netzwerkfehler beim Speichern.', 'error');
        });
    }
</script>

<script>
    function loadStages(customer_id, alternative_id, product_id, section_id, version = null) {
    $.ajax({
        url: '/ajax/load-stages',
        method: 'GET',
        data: { customer_id, alternative_id, product_id, section_id, version },
        success: function (response) {
        $('#mainContent').html(response);
        if (window.feather) feather.replace();
        },
        error: function (xhr) {
        console.error(xhr.responseText || xhr.statusText);
        Swal.fire('Fehler', 'Fehler beim Laden der Stufen', 'error');
        }
    });
    }

    $(function(){
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // Save selected version → persist + reload
    $(document).on('submit', '#stageVersionForm', function(e){
        e.preventDefault();
        const fd = $(this).serializeArray().reduce((a,i)=> (a[i.name]=i.value, a), {});
        Swal.fire({title:'Speichere...', allowOutsideClick:false, didOpen:() => Swal.showLoading()});

        $.post('/ajax/save-customer-stage', $(this).serialize())
        .done(function(res){
            Swal.close();
            if (res && res.success) {
            Swal.fire('Gespeichert',
                (res.saved_rows !== undefined
                ? `Version gespeichert (${res.saved_rows} Zeilen).`
                : 'Version erfolgreich gespeichert.'),
                'success'
            );
            loadStages(fd.customer_id, fd.alternative_id, fd.product_id, fd.section_id, fd.version);
            } else {
            Swal.fire('Hinweis','Unerwartete Antwort vom Server.','warning');
            }
        })
        .fail(function(xhr){
            Swal.close();
            let msg = 'Speichern fehlgeschlagen';
            if (xhr.status === 422 && xhr.responseJSON?.errors) {
            msg += '<br>' + Object.values(xhr.responseJSON.errors).map(a=>a.join('<br>')).join('<br>');
            }
            Swal.fire({title:'Fehler', html:msg, icon:'error'});
        });
    });

    // Version change → preview that version immediately
    $(document).on('change', '#versionSelect', function(){
        const version       = $(this).val();
        const customer_id   = $('input[name="customer_id"]').val();
        const alternative_id= $('input[name="alternative_id"]').val();
        const product_id    = $('input[name="product_id"]').val();
        const section_id    = $('input[name="section_id"]').val();

        Swal.fire({title:'Lade Version...', allowOutsideClick:false, didOpen:() => Swal.showLoading()});
        loadStages(customer_id, alternative_id, product_id, section_id, version);
        Swal.close();
    });
    });
</script> 

<!-- Loading the Calender  -->

<script>
/**
 * Loads the calendar partial into #mainContent (AJAX)
 * and initializes FullCalendar + custom modal logic AFTER injection.
 */
function loadCalendar(cid, aid, pid) {
  const url = `/customer/calendar/view?cid=${cid}&aid=${aid}&pid=${pid}`;
  const mainContent = document.getElementById('mainContent');
  if (!mainContent) return;

  // Spinner (you can style as you want)
  mainContent.innerHTML = `
    <div class="d-flex justify-content-center align-items-center" style="height: 400px;">
      <div class="text-center">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="mt-2 text-muted">Lade Kalender...</div>
      </div>
    </div>`;

  fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(res => {
      if (!res.ok) throw new Error('Netzwerkfehler');
      return res.text();
    })
    .then(html => {
      // 1) Inject partial
      mainContent.innerHTML = html;

      // 2) Icons
      if (typeof feather !== 'undefined') feather.replace();

      // 3) Init FullCalendar (RETURN instance!)
      const cal = initFullCalendar(cid);

      // 4) Init custom modal + select2 AFTER partial is in DOM
      if (window.CalendarAppointments && typeof window.CalendarAppointments.initAfterPartialLoad === 'function') {
        window.CalendarAppointments.initAfterPartialLoad(cal);
      }
    })
    .catch(err => {
      console.error(err);
      mainContent.innerHTML = `<div class="alert alert-danger m-3">❌ Fehler beim Laden des Kalenders.</div>`;
    });
}
 
</script>
<script>
function initFullCalendar(customerId) {
  const el = document.getElementById('fullCalendar');
  if (!el || typeof FullCalendar === 'undefined') return null;

  const calendar = new FullCalendar.Calendar(el, {
    locale: 'de',
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
    },
    height: 'auto',
    navLinks: true,
    editable: false,
    dayMaxEvents: true,

    events: {
      url: '/ajax/calendar-events',
      method: 'GET',
      extraParams: { customer_id: customerId },
      failure: function() {
        alert('Fehler beim Laden der Termine!');
      }
    },

    dateClick: function(info) {
      if (window.CalendarAppointments) CalendarAppointments.open(info.dateStr);
    },

    eventClick: function(info) {
      alert('Termin: ' + info.event.title);
    }
  });

  calendar.render();
  return calendar;
}
</script>


<script>
/** Backward compatibility for old onclick="openAppointmentModal()" */
window.openAppointmentModal = function(dateStr){
  if (window.CalendarAppointments) window.CalendarAppointments.open(dateStr);
};
</script>


 <script>
(function(){
  "use strict";

  const SELECT_ID = '#calApp_employee_select';
  const MODAL_ID  = '#createAppModal';
  const FORM_ID   = '#calApp_form';

  const qs = (s, el=document) => el.querySelector(s);

  function openModal(dateStr){
    const modal = qs(MODAL_ID);
    if(!modal) return;

    // reset form
    const form = qs(FORM_ID);
    if(form) form.reset();

    // dates
    const today = new Date().toISOString().split('T')[0];
    const target = dateStr || today;
    const start = qs('#calApp_start_date');
    const end   = qs('#calApp_end_date');
    if(start) start.value = target;
    if(end)   end.value = target;

    // select2 reset
    if (window.jQuery && jQuery(SELECT_ID).length){
      jQuery(SELECT_ID).val(null).trigger('change');
    }

    modal.classList.add('is-open');
    modal.setAttribute('aria-hidden','false');

    if (typeof feather !== 'undefined') feather.replace();
  }

  function closeModal(){
    const modal = qs(MODAL_ID);
    if(!modal) return;
    modal.classList.remove('is-open');
    modal.setAttribute('aria-hidden','true');
  }

  function bindModalClose(){
    const modal = qs(MODAL_ID);
    if(!modal) return;

    // click backdrop / close buttons
    modal.addEventListener('click', (e) => {
      if (e.target.matches('[data-xmodal-close]')) closeModal();
    });

    // ESC
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });
  }

  function formatEmployeeAvatar(emp){
    if (!emp.id) return emp.text;

    let imgUrl = "{{ asset('images/gender/male.png') }}";
    if (emp.image && emp.image !== '') {
      imgUrl = "{{ asset('images/employee') }}/" + emp.image;
    }

    return $(
      '<div style="display:flex;align-items:center;gap:8px;">' +
        '<img src="'+imgUrl+'" style="width:24px;height:24px;object-fit:cover;border-radius:999px;border:1px solid #ddd;">' +
        '<span>'+ (emp.text || '') +'</span>' +
      '</div>'
    );
  }

  function initEmployeeSelect2(){
    if (!window.jQuery) return;
    const $sel = jQuery(SELECT_ID);
    const $modal = jQuery(MODAL_ID);
    if(!$sel.length || !$modal.length) return;

    // Prevent double init when calendar is loaded multiple times
    if ($sel.data('select2')) $sel.select2('destroy');

    $sel.select2({
      dropdownParent: $modal,   // works with custom modal too
      placeholder: "Mitarbeiter suchen...",
      allowClear: true,
      width: '100%',

      // Option A (recommended): since you already preload <option> from $calenderEmployees,
      // you can skip AJAX completely -> always works.
      // If you still want AJAX, uncomment below and remove preloaded options.

      /*
      ajax: {
        url: "{{ route('get.employees.all') }}",
        dataType: 'json',
        delay: 250,
        data: params => ({ q: params.term }),
        processResults: data => ({
          results: (data || []).map(item => ({
            id: item.emp_id,
            text: (item.name || '') + ' ' + (item.lastname || ''),
            image: item.image || ''
          }))
        }),
        cache: true
      },
      templateResult: formatEmployeeAvatar,
      templateSelection: formatEmployeeAvatar
      */
    });
  }

 function initAjaxForm(calendarInstance){
    const form = qs(FORM_ID);
    if(!form) return;

    if (form.dataset.bound === "1") return; // prevent duplicate binding
    form.dataset.bound = "1";

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const btn = form.querySelector('button[type="submit"]');
      const original = btn ? btn.innerHTML : '';
      if(btn){
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Speichern...';
      }

      try {
        // IMPORTANT: this will include select name="employee[]" values IF select exists + selected
        const formData = new FormData(form);

        // Debug (temporary): see if employees exist
        // console.log('employee[]', formData.getAll('employee[]'));

        const res = await fetch(form.action, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: formData
        });

        const data = await res.json();
        if(!(data && (data.success || data.id))) throw new Error(data?.message || 'Fehler beim Speichern');

        closeModal();
        if(typeof Swal !== 'undefined') Swal.fire('Gespeichert!', 'Termin wurde erfolgreich angelegt.', 'success');
        if(calendarInstance) calendarInstance.refetchEvents();

      } catch (err) {
        console.error(err);
        if(typeof Swal !== 'undefined') Swal.fire('Fehler', 'Termin konnte nicht gespeichert werden.', 'error');
      } finally {
        if(btn){
          btn.disabled = false;
          btn.innerHTML = original;
        }
      }
    });
  }

  // Expose a single, non-duplicated function
  window.CalendarAppointments = {
    open: openModal,
    close: closeModal,
    initAfterPartialLoad: function(calendarInstance){
      bindModalClose();
      initEmployeeSelect2();
      initAjaxForm(calendarInstance);
      if (typeof feather !== 'undefined') feather.replace();
    }
  };
})();
</script>

<!-- Loading the Calender  -->


<script>
    function showFullNote(el) {
        const text = el.dataset.note || 'Keine Notiz vorhanden.';
        
        Swal.fire({
            title: 'Kundennotiz',
            html: `<div style="white-space: pre-wrap; text-align: left;">${text}</div>`,
            icon: 'info',
            confirmButtonText: 'Schließen',
            width: '600px',
        });
    }
</script>

<!-- Customer Report  -->
 <script>
        let editingReportId = null; 
        let quill; 
        document.addEventListener('DOMContentLoaded', function () {
            quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Bericht schreiben...',
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{ list: 'ordered' }, { list: 'bullet' }],
                        ['link']
                    ]
                }
            });
        }); 
        // Reset + open sidebar
        $(document).on('click', '.open-report-modal', function () {
            const sidebar = $('#reportSidebar');

            const product_id = $(this).data('product-id');
            const stage = $(this).data('stage');
            const customer_id = $(this).data('customer-id');
            const alternative_id = $(this).data('alternative-id');

            sidebar.addClass('open').show();

            $('#reportForm input[name=product_id]').val(product_id);
            $('#reportForm input[name=customer_id]').val(customer_id);
            $('#reportForm input[name=alternative_id]').val(alternative_id);
            $('#reportForm select[name=stage]').val(stage);
            $('#reportForm')[0].reset();
            quill.root.innerHTML = '';
            $('#reportFormContainer').hide();
            $('#reportForm button[type=submit]').text('Speichern');
            editingReportId = null;

            loadReports(product_id, customer_id, alternative_id);
        }); 
    // Replace loadReports to prepend new report
        function loadReports(product_id, customer_id, alternative_id) {
            $.get('/customer-reports/list', {
                product_id,
                customer_id,
                alternative_id
            }, function (html) {
                $('#reportList').html(html);
            });
        } 
        $(document).on('click', '.edit-report', function () {
            const id = $(this).data('id');
            editingReportId = id;

            $.get('/customer-reports/show/' + id, function (data) {
                $('#reportForm input[name=report_date]').val(data.date);
                $('#reportForm select[name=stage]').val(data.stage);
                quill.root.innerHTML = data.report;

                $('#reportForm button[type=submit]').text('Aktualisieren');

                // Show modal form
                $('#reportFormContainer').fadeIn();
            });
        }); 
        $(document).on('click', '.close-report-sidebar', function () {
            $('#reportSidebar').removeClass('open');
            setTimeout(() => $('#reportSidebar').hide(), 300);
        });
 
        // Open form modal
        $(document).on('click', '.toggle-report-form', function () {
            $('#reportFormContainer').fadeIn();
        });

        // Close form modal
        $(document).on('click', '.close-report-form', function () {
            editingReportId = null;
            $('#reportForm')[0].reset();
            quill.root.innerHTML = '';
            $('#reportForm button[type=submit]').text('Speichern');
            $('#reportFormContainer').fadeOut();
        });
 
        // Submit create/update
        $(document).off('submit', '#reportForm').on('submit', '#reportForm', function (e) {
            e.preventDefault();

            const form = $(this);
            const isUpdate = editingReportId !== null;

            // Set report content from Quill
            form.find('[name="report"]').val(quill.root.innerHTML);

            // Prepare URL and method
            const url = isUpdate
                ? `/customer-reports/update/${editingReportId}`
                : '/customer-reports/store';

            const formData = form.serializeArray();

            // If it's an update, spoof _method = PUT for Laravel
            if (isUpdate) {
                formData.push({ name: '_method', value: 'PUT' });
            }

            $.ajax({
                type: 'POST',
                url: url,
                data: $.param(formData),
                success: function () {
                    const product_id = form.find('[name=product_id]').val();
                    const customer_id = form.find('[name=customer_id]').val();
                    const alternative_id = form.find('[name=alternative_id]').val();

                    // Reload reports
                    loadReports(product_id, customer_id, alternative_id);

                    // Reset form and UI
                    editingReportId = null;
                    form[0].reset();
                    quill.root.innerHTML = '';
                    $('#reportForm button[type=submit]').text('Speichern');

                    // Close modal form
                    $('#reportFormContainer').fadeOut();
                }
            });
        });
 
        $(document).on('click', '.delete-report', function () {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Bist du sicher?',
                text: "Der Bericht wird gelöscht.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja, löschen',
                cancelButtonText: 'Abbrechen'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/customer-reports/delete/${id}`,
                        type: 'POST', // ✅ change to POST
                        data: {
                            _method: 'DELETE', // ✅ method spoofing for Laravel
                            _token: $('meta[name="csrf-token"]').attr('content') // ✅ add CSRF token
                        },
                        success: function () {
                            const form = $('#reportForm');
                            loadReports(
                                form.find('[name=product_id]').val(),
                                form.find('[name=customer_id]').val(),
                                form.find('[name=alternative_id]').val()
                            );
                        }
                    });
                }
            });
        });
 
 </script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let replyingToText = '';

        // 🔁 Get current report ID from multiple sources
        function getReportId() {
            return $('#report_id').val() || $('.open-comment-sidebar').data('report-id') || null;
        }

        // 🔄 Reload comment list
        function reloadComments(reportId) {
            if (!reportId) return;
            $.get(`/customer-report-comments/${reportId}`, function (html) {
                $('#commentContent').hide().html(html).fadeIn();
            });
        }

        // 🧼 Clear comment form
        function clearCommentForm() {
            $('#newCommentForm')[0].reset();
            $('#quotedComment').hide().html('');
            $('#parent_id').val('');
            $('#newCommentForm input[name="_method"]').remove();
            $('#newCommentForm input[name="comment_id"]').remove();
        }


        // 🧭 Scroll to a comment
        function scrollToComment(id) {
            const target = $(`#comment-${id}`);
            if (!target.length) return;
            $('#commentContent').animate({
                scrollTop: target.position().top + $('#commentContent').scrollTop() - 60
            }, 400);
            target.addClass('bg-warning-light');
            setTimeout(() => target.removeClass('bg-warning-light'), 2000);
        }

        // 🔓 Open sidebar
        $(document).on('click', '.open-comment-sidebar', function () {
            const reportId = $(this).data('report-id');
            $('#report_id').val(reportId);
            $('#commentSidebar').addClass('open').show();
            reloadComments(reportId);
        });

        // ❌ Close sidebar
        $(document).on('click', '.close-comment-sidebar', function () {
            $('#commentSidebar').removeClass('open').hide();
            $('#commentFormModal').hide();
        });

        // ➕ Open new comment form
        $(document).on('click', '.open-comment-form', function () {
            $('#report_id').val($(this).data('report-id') || getReportId());
            clearCommentForm();
            $('#commentFormModal').fadeIn();
        });

        // ↩️ Reply
        $(document).on('click', '.reply-comment', function () {
            const commentId = $(this).data('id');
            const body = $(this).data('body');

            replyingToText = body;
            $('#quotedComment').html(`<small><i>Antwort auf:</i><br> ${body}</small>`).show();
            $('#parent_id').val(commentId);
            $('#report_id').val(getReportId());
            $('#commentFormModal').fadeIn();

            scrollToComment(commentId);
        });

        // ✏️ Edit comment
        $(document).on('click', '.edit-comment', function () {
            const id = $(this).data('id');
            const text = $(this).data('body');

            clearCommentForm();
            $('textarea[name="comment"]').val(text);
            $('#newCommentForm').append(`<input type="hidden" name="_method" value="PUT">`);
            $('#newCommentForm').append(`<input type="hidden" name="comment_id" value="${id}">`);
            $('#commentFormModal').fadeIn();
        });

        // ❌ Close form
        $(document).on('click', '.close-comment-form', function () {
            $('#commentFormModal').fadeOut();
            clearCommentForm();
        });

        // 💾 Submit comment (create or update)
        $(document).on('submit', '#newCommentForm', function (e) {
            e.preventDefault();

            const form = $(this);
            const isUpdate = form.find('input[name="comment_id"]').length > 0;
            const url = isUpdate
                ? `/customer-report-comments/${form.find('input[name="comment_id"]').val()}`
                : `/customer-report-comments`;

            $.ajax({
                type: 'POST',
                url,
                data: form.serialize(),
                success: function () {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: isUpdate ? 'Kommentar aktualisiert' : 'Kommentar hinzugefügt',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    clearCommentForm();
                    $('#commentFormModal').fadeOut();
                    reloadComments(getReportId());
                }
            });
        });

        // 🗑️ Delete comment
        $(document).on('click', '.delete-comment', function () {
            const id = $(this).data('id');
            const reportId = getReportId();

            Swal.fire({
                title: 'Kommentar löschen?',
                text: 'Das kann nicht rückgängig gemacht werden.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ja, löschen',
                cancelButtonText: 'Abbrechen'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: `/customer-report-comments/${id}`,
                    type: 'POST',
                    data: {
                        _method: 'DELETE',
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function () {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Kommentar gelöscht',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        reloadComments(reportId);
                    }
                });
            });
        });

        // 🔁 Scroll to quoted
        $(document).on('click', '.scroll-to-comment', function () {
            scrollToComment($(this).data('target'));
        });

        function closeCommentForm() {
            $('#commentFormModal').fadeOut();
            clearCommentForm();
        }

        // Close from buttons
        $(document).on('click', '.close-comment-form', function () {
            closeCommentForm();
        });

        // Close when clicking the dimmed overlay (outside modal-content)
        $(document).on('click', '#commentFormModal', function (e) {
            if (e.target === this) { // click on backdrop
                closeCommentForm();
            }
        });

        // Close on ESC key
        $(document).on('keyup', function (e) {
            if (e.key === 'Escape' && $('#commentFormModal').is(':visible')) {
                closeCommentForm();
            }
        });

    });
</script>
  

  <script>
    $(document).ready(function () {
        // 🧠 Modal Trigger on Avatar
        

        // 🖼️ Select2 Image Formatter
        function formatEmpWithImg(emp) {
            if (!emp.id) return emp.text;
            const img = $(emp.element).data('image') || '/images/gender/users.png';
            return $(`<span class="d-flex align-items-center">
                        <img src="${img}" class="rounded-circle mr-2" style="width: 26px; height: 26px; object-fit: cover;">
                        ${emp.text}
                    </span>`);
        }

        // 📦 Init all Select2s with images
        $('.employeeSelect, .employeeDone').select2({
            templateResult: formatEmpWithImg,
            templateSelection: formatEmpWithImg,
            escapeMarkup: m => m
        });

        // 🔔 Toastr config
        toastr.options = {
            closeButton: true,
            progressBar: true,
            timeOut: 3000
        };

        $(document).on('click', '.change_stages', function () {
        const $btn = $(this); 
        const oldData = {
            customerId: $btn.data('customer-id'),
            alternativeId: $btn.data('alternative-id'),
            productId: $btn.data('product-id'),
            oldStageId: $btn.data('stage'),
            oldPhaseId: $btn.data('phase-id'),
            serviceId: $btn.data('service-id'),
            service: $btn.data('service'),
            employeeId: $btn.data('employee-id'),
            departmentId: $btn.data('department-id'),
        };

        console.log('🧾 [Button Clicked] OLD Stage Data:', oldData);

        Swal.fire({
            title: 'Achtung!',
            html: `<b>Wenn du die Phase änderst, werden alle zugehörigen Aktivitäten möglicherweise entfernt oder ausgeblendet!</b><br><br>Willst du fortfahren?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, fortfahren',
            cancelButtonText: 'Abbrechen',
        }).then(result => {
            if (result.isConfirmed) {
                console.log('📦 Fetching stage/version options for product:', oldData.productId);

                $.get('/get-stages-and-versions', {
                    product_id: oldData.productId
                }).done(data => {
                    console.log('✅ [GET] Stage/Version Options:', data);
                    showStageVersionModal(data, oldData);
                }).fail(() => {
                    console.error('❌ Fehler beim Laden der verfügbaren Phasen.');
                    toastr.error('Fehler beim Laden der verfügbaren Phasen.');
                });
            }
        });
    });

    function showStageVersionModal(data, oldData) {
        console.log('📥 [Modal Init] Using OLD context:', oldData);

        const currentKey = `${oldData.oldStageId}`;
        let options = '';

        data.forEach(stage => {
            const key = `${stage.id}|${stage.version}|${stage.phase_id || ''}|${stage.task_id || ''}`;
            const selected = stage.id == oldData.oldStageId ? 'selected' : '';
            options += `<option value="${key}" ${selected}>
                            ${stage.stage} (Version ${stage.version})
                            - PhaseID: ${stage.phase_id ?? '–'} 
                            - TaskID: ${stage.task_id ?? '–'}
                        </option>`;
        });

        Swal.fire({
            title: 'Neue Version dieser Phase auswählen',
            html: `<select id="stageVersionSelect" class="form-control">${options}</select>`,
            confirmButtonText: 'Speichern',
            preConfirm: () => {
                const value = document.getElementById('stageVersionSelect').value;
                console.log('📌 [Modal] Selected option value:', value);
                if (!value) {
                    Swal.showValidationMessage('Bitte eine Version auswählen');
                }
                return value;
            }
        }).then(result => {
            if (result.isConfirmed) {
                const [newStageId, newVersion, newPhaseId, newTaskId] = result.value.split('|');

                const payload = {
                    _token: $('meta[name="csrf-token"]').attr('content'),

                    customer_id: oldData.customerId,
                    alternative_id: oldData.alternativeId,
                    product_id: oldData.productId,

                    old_stage_id: oldData.oldStageId,
                    old_phase_id: oldData.oldPhaseId,

                    stage_id: newStageId,
                    version: newVersion,
                    selected_phase_id: newPhaseId || null,
                    selected_task_id: newTaskId || null,
                };

                console.log('📤 [POST /update-single-customer-stage] Payload:', payload);

                $.post('/update-single-customer-stage', payload)
                    .done(() => {
                        toastr.success('Phase erfolgreich aktualisiert.');
                        location.reload();
                    })
                    .fail(() => {
                        console.error('❌ Fehler beim Aktualisieren der Phase.');
                        toastr.error('Fehler beim Aktualisieren der Phase.');
                    });
            }
        });
    }

});
</script> 

<script>
    $(document).ready(function () {

        // Toggle to edit
        $(document).on('click', '.edit-duration-btn', function () {
            const wrapper = $(this).closest('.duration-wrapper');
            wrapper.find('.duration-display').addClass('d-none');
            wrapper.find('.duration-edit').removeClass('d-none');
        });


    

    

    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>

 <script>
    (function(){
    const WRAP = '.kebab-wrap';
    const BTN  = '.kebab-btn';
    const MENU = '.kebab-menu';
    const ITEM = '.kebab-item';

    function closeAll() {
        document.querySelectorAll(MENU).forEach(m => { m.hidden = true; m.classList.remove('is-open'); });
        document.querySelectorAll(BTN).forEach(b => b.setAttribute('aria-expanded','false'));
    }

    function getMenu(btn){
        const wrap = btn.closest(WRAP);
        return wrap ? wrap.querySelector(MENU) : null;
    }

    function openMenu(btn){
        const menu = getMenu(btn);
        if (!menu) return;
        closeAll();
        btn.setAttribute('aria-expanded','true');
        menu.hidden = false;
        requestAnimationFrame(() => menu.classList.add('is-open'));

        // keep in viewport (simple right/left flip if overflowing)
        const r = menu.getBoundingClientRect();
        if (r.right > window.innerWidth) {
        menu.style.left = '0';
        menu.style.right = 'auto';
        } else {
        menu.style.left = 'auto';
        menu.style.right = '0';
        }

        // focus first item for a11y
        const first = menu.querySelector(ITEM);
        if (first) first.focus();
    }

    // Toggle on button click
    document.addEventListener('click', (e) => {
        const btn = e.target.closest(BTN);
        if (btn) {
        e.preventDefault();
        const menu = getMenu(btn);
        const isOpen = menu && !menu.hidden;
        isOpen ? closeAll() : openMenu(btn);
        return;
        }
    
    // Item actions (delegated)
            const item = e.target.closest(ITEM);
            if (item && item.closest(MENU)) {
            // prevent "#" jumping
            if (item.tagName === 'A' && item.getAttribute('href') === '#') e.preventDefault();

            if (item.dataset.action === 'open-new-product') {
                if (typeof openNewProductModal === 'function') openNewProductModal(item);
                closeAll(); return;
            }
            if (item.dataset.action === 'reset-cache') {
                if (typeof resetAllSubNavs === 'function') resetAllSubNavs();
                closeAll(); return;
            }
            // 🔽 add these:
            if (item.dataset.action === 'open-history') {
                openProductHistory?.(
                Number(item.dataset.productId),
                Number(item.dataset.customerId),
                Number(item.dataset.alternativeId)
                );
                closeAll(); return;
            }
            if (item.dataset.action === 'delete-card') {
                if (!window.confirm('Karte wirklich löschen?')) { closeAll(); return; }
                deleteProductCard?.(Number(item.dataset.id));
                closeAll(); return;
            }

            // default: close
            closeAll(); return;
            }


        // Click outside closes
        if (!e.target.closest(WRAP)) closeAll();
    });

    // Keyboard support: Esc closes; Up/Down navigate items
    document.addEventListener('keydown', (e) => {
        const openMenuEl = document.querySelector(`${MENU}:not([hidden])`);
        if (!openMenuEl) return;

        if (e.key === 'Escape') { closeAll(); return; }

        const items = Array.from(openMenuEl.querySelectorAll(ITEM));
        if (!items.length) return;

        const idx = items.indexOf(document.activeElement);
        if (e.key === 'ArrowDown') {
        e.preventDefault();
        const next = items[(idx + 1 + items.length) % items.length];
        next?.focus();
        }
        if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prev = items[(idx - 1 + items.length) % items.length];
        prev?.focus();
        }
        if (e.key === 'Enter' && document.activeElement?.click) {
        e.preventDefault();
        document.activeElement.click();
        }
    });
    })();
</script>


<!-- Purchase Status Script  -->
 <script>
    document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.badge-trigger');
    if (!btn || btn.disabled) return;

    const id = btn.dataset.customerId;
    const body = document.getElementById('purchaseModalBody');
    body.innerHTML = '<div class="text-muted">Laden…</div>';

    try {
        const res = await fetch(`{{ url('/customers') }}/${id}/purchase-summary`);
        const html = await res.text();
        body.innerHTML = html;
    } catch (err) {
        body.innerHTML = '<div class="text-danger">Fehler beim Laden.</div>';
        console.error(err);
    }

    $('#purchaseModal').modal('show');
    });
</script>


<script>
document.addEventListener('DOMContentLoaded', () => {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

    /* --------- UMSATZ MODAL (edit total_purchase) ---------- */

    function ensureUmsatzModal() {
        let el = document.getElementById('updateUmsatzModal');
        if (!el) {
            const tpl = `
            <div class="modal fade" id="updateUmsatzModal" tabindex="-1" role="dialog" aria-hidden="true">
              <div class="modal-dialog modal-sm" role="document">
                <form id="updateUmsatzForm" class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Umsatz bearbeiten</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <input type="hidden" id="um-customer-id" name="customer_id">
                    <div class="mb-2 small text-muted" id="um-context"></div>
                    <div class="form-group">
                      <label for="um-total">Gesamtumsatz (EUR)</label>
                      <input type="text" class="form-control" id="um-total" name="total_purchase"
                             placeholder="z. B. 12.345,67">
                      <small class="form-text text-muted">
                        Dezimal mit Komma oder Punkt möglich.
                      </small>
                      <div class="invalid-feedback" id="um-error"></div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Abbrechen</button>
                    <button class="btn btn-primary" type="submit">Speichern</button>
                  </div>
                </form>
              </div>
            </div>`;
            document.body.insertAdjacentHTML('beforeend', tpl);
            el = document.getElementById('updateUmsatzModal');
        }
        return el;
    }

    function getModalParts() {
        ensureUmsatzModal();
        const modalEl = document.getElementById('updateUmsatzModal');
        return {
            modal:  modalEl ? $('#updateUmsatzModal') : null,
            form:   modalEl?.querySelector('#updateUmsatzForm') || null,
            input:  modalEl?.querySelector('#um-total') || null,
            idField:modalEl?.querySelector('#um-customer-id') || null,
            errBox: modalEl?.querySelector('#um-error') || null,
            ctxBox: modalEl?.querySelector('#um-context') || null,
        };
    }

    // open modal from any .total-purchase-trigger
    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('.total-purchase-trigger');
        if (!trigger) return;

        const { modal, input, idField, errBox, ctxBox } = getModalParts();
        if (!modal || !input || !idField) {
            console.error('UpdateUmsatz modal missing.');
            return;
        }

        const customerId   = trigger.dataset.customerId;
        const totalRaw     = trigger.dataset.totalPurchaseRaw || '0';
        const customerName = trigger.dataset.customerName || ('Kunde #' + customerId);

        idField.value = customerId;
        input.value   = String(totalRaw).replace(/\./g, ',');
        input.classList.remove('is-invalid');
        if (errBox) errBox.textContent = '';
        if (ctxBox) ctxBox.textContent = `Lead #${customerId} – ${customerName}`;

        modal.modal('show');
        setTimeout(() => input.focus(), 120);
    });

    // submit total_purchase
    document.addEventListener('submit', async (e) => {
        if (!e.target.matches('#updateUmsatzForm')) return;
        e.preventDefault();

        const { modal, input, idField, errBox } = getModalParts();
        if (!input || !idField) return;

        input.classList.remove('is-invalid');
        if (errBox) errBox.textContent = '';

        const id  = idField.value;
        const val = input.value;

        try {
            const res  = await fetch(`{{ url('/customers') }}/${id}/total-purchase`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ total_purchase: val })
            });

            const data = await res.json();

            if (!res.ok || !data.ok) {
                input.classList.add('is-invalid');
                if (errBox) errBox.textContent = data.message || 'Fehler beim Speichern.';
                return;
            }

            const col = document.querySelector(`.total-purchase-trigger[data-customer-id="${CSS.escape(id)}"]`);
            if (col) {
                col.dataset.totalPurchaseRaw = data.amount;
                const display = col.querySelector('.tp-display');
                if (display) display.textContent = `Gesamt: ${data.formatted} EUR`;
            }

            if (modal && typeof modal.one === 'function') {
                modal.one('hidden.bs.modal', () => window.location.reload());
                modal.modal('hide');
            } else {
                window.location.reload();
            }
        } catch (err) {
            input.classList.add('is-invalid');
            if (errBox) errBox.textContent = 'Netzwerkfehler.';
            console.error(err);
        }
    });

    /* --------- PRICE HISTORY DRAWER ---------- */

    const phBackdrop = document.getElementById('priceHistoryBackdrop');
    const phBody     = document.getElementById('priceHistoryContent');
    const phName     = document.getElementById('phCustomerName');
    const phDate     = document.getElementById('phPurchaseDate');
    const phTotal    = document.getElementById('phTotalPurchase');

    function closeDrawer() {
        phBackdrop?.classList.remove('is-open');
    }

    document.addEventListener('click', (e) => {
        if (e.target.closest('.ph-close-btn')) closeDrawer();
        if (e.target === phBackdrop) closeDrawer();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeDrawer();
    });

    async function openPriceHistoryDrawer(customerId, customerName) {
        if (!phBackdrop || !phBody) return;

        phBackdrop.classList.add('is-open');
        if (phName) phName.textContent = customerName || ('Kunde #' + customerId);
        if (phDate) phDate.textContent = '–';
        if (phTotal) phTotal.textContent = '–';

        phBody.innerHTML = '<div class="ph-loading">Lade Preisverlauf …</div>';

        try {
            const url = `{{ route('customers.price-history', ':id') }}`.replace(':id', customerId);
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) throw new Error('HTTP ' + res.status);

            const data = await res.json();

            if (phDate && data.purchase_date) {
                phDate.textContent = data.purchase_date;
            }
            if (phTotal && data.total_purchase_formatted) {
                phTotal.textContent = data.total_purchase_formatted;
            }

            const entries = data.entries || [];
            if (!entries.length) {
                phBody.innerHTML = '<div class="ph-empty">Noch keine Preisänderungen vorhanden.</div>';
                return;
            }

            const fragment = document.createDocumentFragment();

            entries.forEach((entry) => {
                const card = document.createElement('div');
                card.className = 'ph-entry';

                // header: date + employee
                const head = document.createElement('div');
                head.className = 'ph-entry-head';
                head.innerHTML = `
                    <span>${entry.changed_at || ''}</span>
                    <span>${entry.user_name || ''}</span>
                `;

                // title line: article_group + object_name
                const title = document.createElement('div');
                title.className = 'ph-entry-title';
                const titleParts = [];
                if (entry.product_name) {
                    titleParts.push(entry.product_name);          // article_group
                } else if (entry.product_label) {
                    titleParts.push(entry.product_label);
                }
                if (entry.alternative_name) {
                    titleParts.push(entry.alternative_name);     // object_name
                }
                title.textContent = titleParts.join(' • ') || 'Produkt';

                // sub meta: address + initial
                const metaTop = document.createElement('div');
                metaTop.className = 'ph-entry-meta';
                const metaTopParts = [];
                if (entry.alternative_address) {
                    metaTopParts.push(`Objekt: ${entry.alternative_address}`);
                }
                if (entry.product_initial) {
                    metaTopParts.push(`Kürzel: ${entry.product_initial}`);
                }
                metaTop.textContent = metaTopParts.join('  |  ');

                // prices
                const prices = document.createElement('div');
                prices.className = 'ph-entry-prices';
                prices.innerHTML = `
                    <span>Alt: ${entry.old_price_formatted || '-'}</span>
                    <span>Neu: ${entry.new_price_formatted || '-'}</span>
                `;

                // optional IDs (small)
                const metaBottom = document.createElement('div');
                metaBottom.className = 'ph-entry-meta';
                metaBottom.style.opacity = '0.7';
                metaBottom.textContent =
                    `Lead-ID: ${entry.customer_id}  |  Alt-ID: ${entry.alternative_id ?? '-'}  |  Prod-ID: ${entry.product_id ?? '-'}`;

                card.appendChild(head);
                card.appendChild(title);
                card.appendChild(metaTop);
                card.appendChild(prices);
                card.appendChild(metaBottom);

                fragment.appendChild(card);
            });

            phBody.innerHTML = '';
            phBody.appendChild(fragment);

        } catch (err) {
            console.error(err);
            phBody.innerHTML = '<div class="ph-error">Fehler beim Laden des Preisverlaufs.</div>';
        }
    }

    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-price-info');
        if (!btn) return;
        const id   = btn.dataset.customerId;
        const name = btn.dataset.customerName || '';
        openPriceHistoryDrawer(id, name);
    });
});
</script>

 <!-- Adding Product  -->
<script>
document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.badge-trigger');
    if (!btn || btn.disabled) return;

    const id   = btn.dataset.customerId;
    const body = document.getElementById('purchaseModalBody');
    const modalEl = document.getElementById('purchaseModal');

    if (!body || !modalEl) {
        console.warn('purchaseModal / purchaseModalBody not found in DOM.');
        return;
    }

    body.innerHTML = '<div class="text-muted">Laden…</div>';

    try {
        const res  = await fetch(`{{ url('/customers') }}/${id}/purchase-summary`);
        const html = await res.text();
        body.innerHTML = html;
    } catch (err) {
        body.innerHTML = '<div class="text-danger">Fehler beim Laden.</div>';
        console.error(err);
    }

    $('#purchaseModal').modal('show');
});
</script>


<script>
(function(){
  // Reuse helpers if you already defined them
  const openSet = new Set(JSON.parse(localStorage.getItem('nx-stage-open') || '[]'));
  const writeSet = (set)=>{ try{ localStorage.setItem('nx-stage-open', JSON.stringify([...set])); }catch(e){} };

  function setOpen(panel, head, open){
    if(!panel) return;
    if(open){
      panel.style.maxHeight = panel.scrollHeight + 'px';
      panel.classList.add('is-open');
      head?.setAttribute('aria-expanded','true');
      openSet.add(panel.id);
    }else{
      panel.style.maxHeight = panel.scrollHeight + 'px';
      requestAnimationFrame(()=>{
        panel.style.maxHeight = '0px';
        panel.classList.remove('is-open');
        head?.setAttribute('aria-expanded','false');
        openSet.delete(panel.id);
      });
    }
    writeSet(openSet);
  }

  function closeSiblings(currentPanel, scope){
    if(!scope) return;
    scope.querySelectorAll('.nx-panel.is-open').forEach(p=>{
      if(p!==currentPanel){
        const h = scope.querySelector('.nx-head[data-nx-panel="'+p.id+'"]') || document.querySelector('.nx-head[data-nx-panel="'+p.id+'"]');
        setOpen(p, h, false);
      }
    });
  }

  function toggleBy(panel, head){
    if(!panel){ console.warn('[NX] panel not found'); return; }
    const nowOpen = panel.classList.contains('is-open');
    const scope = head?.closest('[data-nx-accordion]') || panel.closest('[data-nx-accordion]');
    if(!nowOpen && scope) closeSiblings(panel, scope);
    setOpen(panel, head, !nowOpen);
  }

  // 🔧 Global delegated handler for ANY .nx-toggle (inside or outside header)
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('.nx-toggle');
    if(!btn) return;
    e.preventDefault();
    e.stopPropagation();

    // 1) Prefer explicit aria-controls on the button
    const explicitId = btn.getAttribute('aria-controls');
    let panel = explicitId ? document.getElementById(explicitId) : null;

    // 2) Find nearest header to sync aria-expanded
    let head = btn.closest('.nx-head');

    // 3) If no explicit panel, resolve via header's data-nx-panel or nearest card
    if(!panel){
      if(head && head.dataset.nxPanel){
        panel = document.getElementById(head.dataset.nxPanel);
      }else{
        const card = btn.closest('.nx-card');
        const headerInCard = card ? card.querySelector('.nx-head[data-nx-panel]') : null;
        if(headerInCard){
          head = headerInCard;
          panel = document.getElementById(headerInCard.dataset.nxPanel);
        }
      }
    }

    toggleBy(panel, head);
  }, true);
})();
</script>

<script>
(function ($) {
    const HISTORY_STATUS_URL = '{{ route("ajax.save.customer.history") }}';

    /**
     * Build the base payload from the current row
     */
    function buildBasePayload($row) {
        return {
            _token: '{{ csrf_token() }}',
            activity_id:    $row.data('activity-id'),
            phase_id:       $row.data('phase-id'),
            customer_id:    $row.data('customer-id'),
            alternative_id: $row.data('alternative-id'),
            product_id:     $row.data('product-id'),
            section_id:     $row.data('service-id'),
            done_by:        $row.find('select.done-by-select').val() || null,
            done_date:      $row.find('input[type="date"]').val() || null,
            plan_time:      $row.find('input[data-type="plan_time"]').val() || null,
            is_time:        $row.find('input[data-type="is_time"]').val() || null,
            notes:          $row.find('textarea.note-textarea').val() || null,
        };
    }

    /**
     * Send status update to the backend
     *
     * statusValue: "open" | "half" | "1"
     * doneReason: {percent, reason} or null
     */
    function sendStatusUpdate($row, statusValue, doneReason = null) {
        const payload = buildBasePayload($row);

        if (statusValue === '1') {
            payload.is_done = '1';
        } else if (statusValue === 'half') {
            payload.is_done = 'half';
        } else {
            // "open" -> not done
            payload.is_done = null;
        }

        if (doneReason) {
            // Laravel will parse this as done_reason[percent], done_reason[reason]
            payload.done_reason = {
                percent: doneReason.percent,
                reason:  doneReason.reason
            };
        } else {
            payload.done_reason = null;
        }

        return $.ajax({
            url: HISTORY_STATUS_URL,
            method: 'POST',
            data: payload
        });
    }

    // ---------- Click on pill: toggle its radio ----------
    $(document).on('click', '.status-pill', function (e) {
        // If the click was directly on the input, do nothing special
        if ($(e.target).is('input')) return;

        const $label = $(this);
        const $radio = $label.find('.status-option');

        if (!$radio.prop('checked')) {
            $radio.prop('checked', true).trigger('change');
        }
    });

    // ---------- Radio changed (Offen / Teilweise / Komplett) ----------
    $(document).on('change', '.status-option', function () {
        const $radio      = $(this);
        const statusValue = $radio.val(); // "open" | "half" | "1"
        const $row        = $radio.closest('tr');

        // 1) Visuell markieren
        const $group = $radio.closest('.status-pill-group');
        $group.find('.status-pill').removeClass('is-active');
        $radio.closest('.status-pill').addClass('is-active');

        // 2) "half" => Modal öffnen und Speichern erst bei Submit
        if (statusValue === 'half') {
            const $modal = $('#halfDoneModal');

            // Reset Formular
            $modal.find('select[name="percent"]').val('');
            $modal.find('textarea[name="reason"]').val('');

            // Hidden-IDs setzen
            $modal.find('input[name="activity_id"]').val($row.data('activity-id'));
            $modal.find('input[name="phase_id"]').val($row.data('phase-id'));

            // Das Row-Element im Modal merken
            $modal.data('row', $row);

            $modal.modal('show');
            return;
        }

        // 3) "open" oder "1" -> direkt speichern
        sendStatusUpdate($row, statusValue, null)
            .fail(function () {
                alert('Status konnte nicht gespeichert werden.');
            });
    });

    // ---------- Submit des "Teilweise erledigt" Modals ----------
    $('#halfDoneForm').on('submit', function (e) {
        e.preventDefault();

        const $form  = $(this);
        const $modal = $('#halfDoneModal');
        const $row   = $modal.data('row');

        if (!$row || !$row.length) {
            alert('Fehler: Zeile nicht gefunden.');
            return;
        }

        const activityId = $form.find('input[name="activity_id"]').val();
        const phaseId    = $form.find('input[name="phase_id"]').val();
        const percent    = $form.find('select[name="percent"]').val();
        const reason     = $form.find('textarea[name="reason"]').val();

        if (!percent) {
            alert('Bitte einen Fertigstellungsgrad wählen.');
            return;
        }

        const doneReason = { percent, reason };

        sendStatusUpdate($row, 'half', doneReason)
            .done(function (res) {
                $modal.modal('hide');
                // Optional: UI noch weiter aktualisieren (z.B. kleine Anzeige des Prozentsatzes)
            })
            .fail(function () {
                alert('Teilweise-Status konnte nicht gespeichert werden.');
            });
    });

})(jQuery);
</script>

<script>
document.addEventListener('change', function (e) {
    if (!e.target.classList.contains('status-option')) return;

    const radio = e.target;
    const name  = radio.getAttribute('name');

    // Remove active from all pills in this group
    document.querySelectorAll('input[name="' + name + '"]').forEach(function (input) {
        const pill = input.closest('.status-pill');
        if (pill) pill.classList.remove('is-active');
    });

    // Add active to the selected pill
    const activePill = radio.closest('.status-pill');
    if (activePill) {
        activePill.classList.add('is-active');
    }
});
</script>
 <script>
(function ($) {
    // =========================================================================
    // DRAWER HELPERS
    // =========================================================================
    function openDrawer(selector) {
        $(selector).addClass('is-open');
    }

    function closeDrawer(selector) {
        $(selector).removeClass('is-open');
    }

    $(document).on('click', '[data-drawer-close]', function () {
        $(this).closest('.nx-drawer').removeClass('is-open');
    });

    // =========================================================================
    // SELECT2 INSIDE SUGGEST-EMPLOYEES DRAWER
    // =========================================================================
    function initDrawerSelect2() {
        const $drawer = $('#suggestEmployeesDrawer');
        const $panel  = $drawer.find('.nx-drawer-panel');
        if (!$panel.length) return;

        // Preserve current values per select index
        const values = [];
        $drawer.find('.employeeSelect').each(function (idx) {
            values[idx] = $(this).val();
        });

        // Destroy old instances to avoid double init / layout glitches
        $drawer.find('.employeeSelect').each(function () {
            if ($(this).data('select2')) {
                $(this).select2('destroy');
            }
        });

        // Re-init all with proper parent + full width
        $drawer.find('.employeeSelect').each(function (idx) {
            $(this).select2({
                dropdownParent: $panel,
                width: '100%'
            });

            // Restore previous selection if any
            if (values[idx]) {
                $(this).val(values[idx]).trigger('change');
            }
        });
    }

    // =========================================================================
    // HELPER: LOAD DEPARTMENTS FOR EMPLOYEE
    // =========================================================================
    function loadDepartmentsForEmployee(employeeId, $departmentSelect, selectedDepartmentId = null) {
        $departmentSelect.html('<option value="">Lade Abteilungen...</option>');

        if (!employeeId) {
            $departmentSelect.html('<option value="">Abteilung wählen</option>');
            return;
        }

        $.get(`/get/employee-departments/${employeeId}`, function (res) {
            $departmentSelect.empty();

            if (!res || res.length === 0) {
                $departmentSelect.append(
                    '<option value="">❗ Dieser Mitarbeiter ist keiner Abteilung zugewiesen</option>'
                );
                return;
            }

            $departmentSelect.append('<option value="">Abteilung wählen</option>');
            res.forEach(d => {
                const label = `${d.department_name} (${d.main === 'yes' ? 'Haupt' : 'Neben'}) - ${d.position ?? 'Position unbekannt'}`;
                const selected = selectedDepartmentId && Number(selectedDepartmentId) === Number(d.department_id)
                    ? 'selected'
                    : '';
                $departmentSelect.append(
                    `<option value="${d.department_id}" ${selected}>${label}</option>`
                );
            });
        }).fail(function () {
            $departmentSelect.html('<option value="">Fehler beim Laden der Abteilungen</option>');
        });
    }

    // =========================================================================
    // SUGGEST EMPLOYEES DRAWER (CREATE)
    // =========================================================================
    let rowCount = 0;

    function rebuildRowButtons() {
        const $rows = $('#employeeRows .employee-row');
        if (!$rows.length) return;

        $rows.each(function (index) {
            const $col = $(this).find('.btn-col');
            if (index === 0) {
                // first row => only plus
                $col.html(`
                    <button type="button" class="btn btn-icon rounded-circle btn-warning add-row">
                        <i class="feather icon-plus"></i>
                    </button>
                `);
            } else {
                // others => only minus
                $col.html(`
                    <button type="button" class="btn btn-icon rounded-circle btn-danger remove-row">
                        <i class="feather icon-minus"></i>
                    </button>
                `);
            }
        });
    }

    function addEmployeeRow() {
        const row = rowCount++;
        const $rowsContainer = $('#employeeRows');

        const newRow = `
        <div class="employee-row row mb-2" data-row="${row}">
            <div class="col-md-3">
                <label>Position</label>
                <select class="form-control roleSelect" name="suggestions[${row}][role]" required>
                    <option value="">Rolle wählen</option>
                    <option value="team">Teammitglied</option>
                    <option value="leader">Teamleiter</option>
                    <option value="representative">Vertreter</option>
                    <option value="monteur">Monteur</option>
                    <option value="obermonteur">Obermonteur</option>
                    <option value="helper">Helfer</option>
                    <option value="innendienst">Innendienst</option>
                    <option value="aussendienst">Außendienst</option>
                    <option value="bauleiter">Bauleiter</option>
                    <option value="buchhaltung">Buchhaltung</option>
                    <option value="techniker">Techniker</option>
                    <option value="controller">Kontroller</option>
                </select>
            </div>
            <div class="col-md-4">
                <label>Mitarbeiter</label>
                <select class="form-control select2 employeeSelect" name="suggestions[${row}][employee_id]" data-row="${row}" required>
                    <option value="">Mitarbeiter wählen</option>
                    @foreach($allEmployees as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->lastname }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label>Abteilung</label>
                <select class="form-control departmentSelect" name="suggestions[${row}][department_id]" required>
                    <option value="">Abteilung wählen</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end justify-content-between btn-col">
                <!-- will be rebuilt -->
            </div>
        </div>
        `;

        $rowsContainer.append(newRow);
        rebuildRowButtons();

        // Re-init all Select2 inside drawer so both existing and new rows render correctly
        initDrawerSelect2();
    }

    // open create drawer
    $(document).on('click', '.suggest-employees-btn', function () {
        const $drawer = $('#suggestEmployeesDrawer');

        $drawer.find('[name=customer_id]').val($(this).data('customer-id'));
        $drawer.find('[name=alternative_id]').val($(this).data('alternative-id'));
        $drawer.find('[name=product_id]').val($(this).data('product-id'));
        $drawer.find('[name=phase_id]').val($(this).data('phase-id'));

        $('#employeeRows').empty();
        rowCount = 0;
        addEmployeeRow();

        openDrawer('#suggestEmployeesDrawer');

        // After drawer is visible, init Select2 once more so widths are correct
        setTimeout(initDrawerSelect2, 10);
    });

    // add row
    $(document).on('click', '.add-row', function () {
        addEmployeeRow();
    });

    // remove row
    $(document).on('click', '.remove-row', function () {
        $(this).closest('.employee-row').remove();
        rebuildRowButtons();
        initDrawerSelect2();
    });

    // employee change => duplicate check + departments
    $(document).on('change', '.employeeSelect', function () {
        const currentRow = $(this).data('row');
        const selectedEmployee = $(this).val();
        const selectedRole = $(`select[name="suggestions[${currentRow}][role]"]`).val();
        const $departmentSelect = $(`select[name="suggestions[${currentRow}][department_id]"]`);

        // duplicate check for same employee + role
        let duplicate = false;
        $('.employeeSelect').each(function () {
            const row = $(this).data('row');
            if (row === currentRow) return;

            const empId = $(this).val();
            const roleVal = $(`select[name="suggestions[${row}][role]"]`).val();

            if (empId && empId === selectedEmployee && roleVal === selectedRole) {
                duplicate = true;
                return false;
            }
        });

        if (duplicate) {
            Swal.fire({
                icon: 'warning',
                title: 'Mitarbeiter bereits gewählt',
                text: 'Dieser Mitarbeiter wurde bereits mit derselben Rolle hinzugefügt.'
            });
            $(this).val('').trigger('change');
            $departmentSelect.html('<option value="">Abteilung wählen</option>');
            return;
        }

        loadDepartmentsForEmployee(selectedEmployee, $departmentSelect);
    });

    // role change => re-check duplicate via employee change
    $(document).on('change', '.roleSelect', function () {
        const row = $(this).closest('.employee-row').data('row');
        $(`select[name="suggestions[${row}][employee_id]"]`).trigger('change');
    });

    // submit create form
    $('#suggestEmployeesForm').on('submit', function (e) {
        e.preventDefault();
        const form = $(this);

        $.ajax({
            method: 'POST',
            url: "{{ route('suggest.employees.store') }}",
            data: form.serialize()
        }).done(function () {
            Swal.fire('Gespeichert!', '', 'success');
            closeDrawer('#suggestEmployeesDrawer');
            location.reload();
        }).fail(function (xhr) {
            const msg = xhr.responseJSON?.message || 'Fehler beim Speichern.';
            Swal.fire('Fehler', msg, 'error');
        });
    });

    // =========================================================================
    // EDIT SUGGESTED EMPLOYEE DRAWER
    // =========================================================================
    const UPDATE_SUGGEST_URL = "{{ route('suggest.employees.update') }}";
    const DELETE_SUGGEST_URL = "{{ url('/suggest-employees') }}";

    // open edit drawer
    $(document).on('click', '.edit-suggested-employee', function () {
        const $btn    = $(this);
        const $drawer = $('#editSuggestedEmployeeDrawer');

        const suggestionId = $btn.data('suggestion-id');
        const employeeId   = $btn.data('employee-id');
        const role         = $btn.data('role');
        const departmentId = $btn.data('department-id');
        const customerId   = $btn.data('customer-id');
        const alternativeId= $btn.data('alternative-id');
        const productId    = $btn.data('product-id');
        const phaseId      = $btn.data('phase-id');

        $drawer.find('[name=suggestion_id]').val(suggestionId);
        $drawer.find('[name=customer_id]').val(customerId);
        $drawer.find('[name=alternative_id]').val(alternativeId);
        $drawer.find('[name=product_id]').val(productId);
        $drawer.find('[name=phase_id]').val(phaseId);

        const $employeeSelect   = $drawer.find('select[name=employee_id]');
        const $roleSelect       = $drawer.find('select[name=role]');
        const $departmentSelect = $drawer.find('select[name=department_id]');

        $employeeSelect.val(employeeId);
        $roleSelect.val(role || 'team');

        // departments for selected employee, with preselected department
        loadDepartmentsForEmployee(employeeId, $departmentSelect, departmentId);

        // init select2 just for edit drawer
        const $editPanel = $drawer.find('.nx-drawer-panel');
        if ($employeeSelect.data('select2')) {
            $employeeSelect.select2('destroy');
        }
        $employeeSelect.select2({
            dropdownParent: $editPanel,
            width: '100%'
        });

        openDrawer('#editSuggestedEmployeeDrawer');
    });

    // submit edit form
    $('#editSuggestedEmployeeForm').on('submit', function (e) {
        e.preventDefault();
        const $form = $(this);
        const suggestionId = $form.find('[name=suggestion_id]').val();

        if (!suggestionId) {
            Swal.fire('Fehler', 'Suggestion ID fehlt.', 'error');
            return;
        }

        $.ajax({
            method: 'POST',
            url: UPDATE_SUGGEST_URL,
            data: $form.serialize()
        }).done(function () {
            Swal.fire('Gespeichert!', '', 'success');
            closeDrawer('#editSuggestedEmployeeDrawer');
            location.reload();
        }).fail(function (xhr) {
            const msg = xhr.responseJSON?.message || 'Fehler beim Speichern.';
            Swal.fire('Fehler', msg, 'error');
        });
    });

    // delete suggestion
    $('#deleteSuggestedEmployee').on('click', function () {
        const $form = $('#editSuggestedEmployeeForm');
        const suggestionId = $form.find('[name=suggestion_id]').val();

        if (!suggestionId) {
            Swal.fire('Fehler', 'Suggestion ID fehlt.', 'error');
            return;
        }

        Swal.fire({
            title: 'Löschen?',
            text: 'Dieser Vorschlag wird endgültig entfernt.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ja, löschen',
            cancelButtonText: 'Abbrechen'
        }).then((result) => {
            if (!result.isConfirmed) return;

            const url = DELETE_SUGGEST_URL + '/' + suggestionId;

            $.ajax({
                method: 'POST',
                url: url,
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE'
                }
            }).done(function () {
                Swal.fire('Gelöscht!', '', 'success');
                closeDrawer('#editSuggestedEmployeeDrawer');
                location.reload();
            }).fail(function (xhr) {
                const msg = xhr.responseJSON?.message || 'Fehler beim Löschen.';
                Swal.fire('Fehler', msg, 'error');
            });
        });
    });

    })(jQuery);
</script>

<!-- Customer Edit  -->

    <script>
"use strict";

/* =============================================================================
  CONFIG
============================================================================= */
const loadUrlTemplate   = @json(route('new-leads.ajax-load-basic',   ['id' => '__ID__']));
const updateUrlTemplate = @json(route('new-leads.ajax-update-basic', ['id' => '__ID__']));
const CSRF_TOKEN        = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

/* =============================================================================
  STATE
============================================================================= */
let autocompleteAddress = null;
let autocompleteCity    = null;
let autocompleteStreet  = null;

let editMap             = null;
let editMarker          = null;
let select2Booted       = false;

/* =============================================================================
  DOM HELPERS  (IMPORTANT: do NOT use "$" name)
============================================================================= */
const qs  = (sel, root = document) => root.querySelector(sel);
const qsa = (sel, root = document) => Array.from(root.querySelectorAll(sel));

const show = (el) => { if (el) el.style.display = 'block'; };
const hide = (el) => { if (el) el.style.display = 'none'; };
const setHTML = (el, v) => { if (el) el.innerHTML = v ?? ''; };
const setText = (el, v) => { if (el) el.textContent = v ?? ''; };

const isFiniteNumber = (v) => Number.isFinite(Number(v));

/* =============================================================================
  SELECT2 HELPERS (jQuery stays as "$")
============================================================================= */
function hasSelect2() {
  return !!(window.jQuery && jQuery.fn && jQuery.fn.select2);
}

function initSelect2(selector, opts = {}) {
  if (!hasSelect2()) return;
  const $el = jQuery(selector);
  if (!$el.length) return;
  if ($el.data('select2')) return;

  const placeholder = $el.data('placeholder') || $el.attr('data-placeholder') || 'Bitte wählen';

  $el.select2(Object.assign({
    width: '100%',
    placeholder,
    allowClear: true
  }, opts));
}

function bootSelect2Once() {
  if (select2Booted) return;
  if (!hasSelect2()) return;

  initSelect2('#edit_title',          { tags: true });
  initSelect2('#edit_academic_title', { tags: true });
  initSelect2('#edit_source',         { tags: true });
  initSelect2('#edit_branch',         { tags: false });

  select2Booted = true;
}

function setSelectValue(selectorOrEl, value) {
  const el = (typeof selectorOrEl === 'string') ? qs(selectorOrEl) : selectorOrEl;
  if (!el) return;

  const v = (value ?? '').toString().trim();

  if (!hasSelect2() || !jQuery(el).data('select2')) {
    el.value = v;
    return;
  }

  const $el = jQuery(el);

  if (!v) {
    $el.val(null).trigger('change');
    return;
  }

  const exists = $el.find('option').filter(function(){ return jQuery(this).val() === v; }).length > 0;
  if (!exists) $el.append(new Option(v, v, true, true));

  $el.val(v).trigger('change');
}

/* =============================================================================
  DRAWER OPEN/CLOSE
============================================================================= */
function openCustomerDrawer() {
  const drawer = qs('#customerEditDrawer');
  if (!drawer) return;

  drawer.classList.add('open');

  setTimeout(() => {
    bootSelect2Once();
    initEditMapIfNeeded();
    initCustomerAddressAutocompletes();
    refreshGoogleMapSize();
  }, 80);
}

function closeCustomerDrawer() {
  const drawer = qs('#customerEditDrawer');
  if (!drawer) return;
  drawer.classList.remove('open');
}

function bindDrawerCloseUX() {
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeCustomerDrawer();
  });

  document.addEventListener('click', (e) => {
    const overlay = e.target.closest('#customerEditDrawer .drawer-overlay');
    if (overlay) closeCustomerDrawer();
  });
}

/* =============================================================================
  GOOGLE MAP
============================================================================= */
function initEditMapIfNeeded() {
  const el = qs('#edit_map');
  if (!el) return;
  if (typeof google === 'undefined' || !google.maps) return;

  if (!editMap) {
    editMap = new google.maps.Map(el, {
      center: { lat: 52.52, lng: 13.405 },
      zoom: 12,
      mapTypeControl: false,
      streetViewControl: false,
      fullscreenControl: true
    });

    editMarker = new google.maps.Marker({
      map: editMap,
      draggable: false
    });
  }
}

function refreshGoogleMapSize() {
  if (!editMap) return;
  if (typeof google === 'undefined' || !google.maps) return;

  google.maps.event.trigger(editMap, 'resize');

  const pos = editMarker?.getPosition?.();
  if (pos) editMap.setCenter(pos);
}

function updateEditMap(lat, lng) {
  initEditMapIfNeeded();
  if (!editMap || !editMarker) return;
  if (!isFiniteNumber(lat) || !isFiniteNumber(lng)) return;

  const pos = { lat: Number(lat), lng: Number(lng) };
  editMap.setCenter(pos);
  editMap.setZoom(16);
  editMarker.setPosition(pos);
}

/* =============================================================================
  GOOGLE PLACES
============================================================================= */
function fillFromPlace(place) {
  if (!place || !place.address_components) return;

  let street = '', streetNumber = '', postcode = '', city = '';

  place.address_components.forEach((component) => {
    const types = component.types || [];
    if (types.includes('route')) street = component.long_name;
    if (types.includes('street_number')) streetNumber = component.long_name;
    if (types.includes('postal_code')) postcode = component.long_name;
    if (types.includes('locality') || types.includes('postal_town')) city = component.long_name;
  });

  const fullStreet = (street + ' ' + streetNumber).trim();

  if (qs('#edit_street'))   qs('#edit_street').value   = fullStreet;
  if (qs('#edit_postcode')) qs('#edit_postcode').value = postcode;
  if (qs('#edit_city'))     qs('#edit_city').value     = city;

  if (place.geometry) {
    const lat = place.geometry.location.lat();
    const lng = place.geometry.location.lng();

    if (qs('#edit_latitude'))  qs('#edit_latitude').value  = lat;
    if (qs('#edit_longitude')) qs('#edit_longitude').value = lng;

    updateEditMap(lat, lng);
  }
}

function initCustomerAddressAutocompletes() {
  if (typeof google === 'undefined' || !google.maps || !google.maps.places) return;

  const searchInput = qs('#edit_address_search');
  const streetInput = qs('#edit_street');
  const cityInput   = qs('#edit_city');

  if (searchInput && !autocompleteAddress) {
    autocompleteAddress = new google.maps.places.Autocomplete(searchInput, {
      types: ['geocode'],
      componentRestrictions: { country: 'de' },
      fields: ['address_components', 'geometry']
    });
    autocompleteAddress.addListener('place_changed', () => fillFromPlace(autocompleteAddress.getPlace()));
  }

  if (streetInput && !autocompleteStreet) {
    autocompleteStreet = new google.maps.places.Autocomplete(streetInput, {
      types: ['address'],
      componentRestrictions: { country: 'de' },
      fields: ['address_components', 'geometry']
    });
    autocompleteStreet.addListener('place_changed', () => {
      const place = autocompleteStreet.getPlace();
      fillFromPlace(place);
      if (qs('#edit_address_search')) qs('#edit_address_search').value = streetInput.value;
    });
  }

  if (cityInput && !autocompleteCity) {
    autocompleteCity = new google.maps.places.Autocomplete(cityInput, {
      types: ['(cities)'],
      componentRestrictions: { country: 'de' },
      fields: ['address_components', 'geometry']
    });
    autocompleteCity.addListener('place_changed', () => {
      const place = autocompleteCity.getPlace();
      if (!place || !place.address_components) return;

      let city = '';
      place.address_components.forEach((component) => {
        const types = component.types || [];
        if (types.includes('locality') || types.includes('postal_town')) city = component.long_name;
      });

      if (qs('#edit_city')) qs('#edit_city').value = city || qs('#edit_city').value;

      if (place.geometry) {
        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();

        if (qs('#edit_latitude'))  qs('#edit_latitude').value  = lat;
        if (qs('#edit_longitude')) qs('#edit_longitude').value = lng;

        updateEditMap(lat, lng);
      }
    });
  }
}

/* =============================================================================
  FILL FORM
============================================================================= */
function fillCustomerForm(data) {
  const c = data.customer || {};

  if (qs('#edit_customer_id')) qs('#edit_customer_id').value = c.id ?? '';

  setSelectValue('#edit_title',          c.title || '');
  setSelectValue('#edit_academic_title', c.academic_title || '');
  setSelectValue('#edit_source',         c.source || '');

  if (qs('#edit_name'))     qs('#edit_name').value     = c.name || '';
  if (qs('#edit_lastname')) qs('#edit_lastname').value = c.lastname || '';
  if (qs('#edit_firma'))    qs('#edit_firma').value    = c.firma || '';

  if (qs('#edit_street'))   qs('#edit_street').value   = c.street || '';
  if (qs('#edit_postcode')) qs('#edit_postcode').value = c.postcode || '';
  if (qs('#edit_city'))     qs('#edit_city').value     = c.city || '';

  if (qs('#edit_latitude'))  qs('#edit_latitude').value  = c.latitude || '';
  if (qs('#edit_longitude')) qs('#edit_longitude').value = c.longitude || '';

  if (qs('#edit_phone'))     qs('#edit_phone').value     = c.phone || '';
  if (qs('#edit_telephone')) qs('#edit_telephone').value = c.telephone || '';
  if (qs('#edit_email'))     qs('#edit_email').value     = c.email || '';

  // branch options
  const branchEl = qs('#edit_branch');
  if (branchEl) {
    branchEl.innerHTML = '<option value="">– Bitte wählen –</option>';
    const branchId = c.branch ?? c.branch_id ?? null;

    (data.branches || []).forEach((b) => {
      const opt = document.createElement('option');
      opt.value = b.id;
      opt.text  = (b.branch || '') + (b.city ? ` (${b.city})` : '');
      if (branchId !== null && Number(branchId) === Number(b.id)) opt.selected = true;
      branchEl.appendChild(opt);
    });

    setSelectValue('#edit_branch', branchId ? String(branchId) : '');
  }

  if (c.latitude && c.longitude) updateEditMap(c.latitude, c.longitude);
}

/* =============================================================================
  AJAX LOAD
============================================================================= */
async function loadCustomerForEdit(id) {
  const url = loadUrlTemplate.replace('__ID__', id);

  try {
    const res = await fetch(url, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    });

    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw data;

    openCustomerDrawer();
    setTimeout(() => {
      fillCustomerForm(data);
      refreshGoogleMapSize();
    }, 60);

  } catch (err) {
    console.error('Load customer error:', err);
    alert('Kundendaten konnten nicht geladen werden.');
  }
}

/* =============================================================================
  AJAX SAVE + AUTO RELOAD
============================================================================= */
async function saveCustomerBasic(e) {
  e.preventDefault();

  const form = qs('#customerEditForm');
  if (!form) return;

  const id = qs('#edit_customer_id')?.value;
  if (!id) return;

  const url = updateUrlTemplate.replace('__ID__', id);

  const errorsBox  = qs('#customerEditErrors');
  const successBox = qs('#customerEditSuccess');

  hide(errorsBox);
  hide(successBox);
  setHTML(errorsBox, '');

  try {
    const formData = new FormData(form);

    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      body: formData
    });

    const data = await res.json().catch(() => ({}));
    if (!res.ok) throw data;

    show(successBox);
    setText(successBox, 'Änderungen gespeichert. Seite wird neu geladen…');

    setTimeout(() => closeCustomerDrawer(), 200);
    setTimeout(() => window.location.reload(), 650);

  } catch (err) {
    show(errorsBox);

    if (err && err.errors) {
      const msgs = [];
      Object.keys(err.errors).forEach((field) => {
        (err.errors[field] || []).forEach((msg) => msgs.push(msg));
      });
      setHTML(errorsBox, msgs.join('<br>'));
    } else {
      setText(errorsBox, 'Fehler beim Speichern. Bitte versuchen Sie es erneut.');
    }
  }
}

/* =============================================================================
  CLICK BINDING
============================================================================= */
function bindCustomerEditButtons() {
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.customer-edit-trigger');
    if (!btn) return;

    e.preventDefault();
    const id = btn.getAttribute('data-customer-id');
    if (!id) return;

    loadCustomerForEdit(id);
  });
}

/* =============================================================================
  INIT
============================================================================= */
document.addEventListener('DOMContentLoaded', () => {
  bindCustomerEditButtons();
  bindDrawerCloseUX();

  const form = qs('#customerEditForm');
  if (form) form.addEventListener('submit', saveCustomerBasic);

  bootSelect2Once();
});

/* =============================================================================
  Google callback (if you use callback=initMap)
============================================================================= */
function initMap() {
  initEditMapIfNeeded();
  initCustomerAddressAutocompletes();
}
</script>


<script>
    const neighborRoutes = {
        html:  '{{ route('new_leads.neighbor') }}',
        data:  '{{ route('new_leads.neighbor.data') }}'
    };

    function loadNeighbor(btn) {
        const cid  = btn.dataset.customerId;
        const aid  = btn.dataset.alternativeId || '';
        const pid  = btn.dataset.productId || '';
        const plid = btn.dataset.productListId || '';

        const url = neighborRoutes.html
            + '?customer_id=' + encodeURIComponent(cid)
            + '&alternative_id=' + encodeURIComponent(aid)
            + '&product_id=' + encodeURIComponent(pid)
            + '&product_list_id=' + encodeURIComponent(plid);

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        })
            .then(function (response) {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.text();
            })
            .then(function (html) {
                const main = document.getElementById('mainContent');
                if (!main) return;
                main.innerHTML = html;

                if (typeof initNeighborMap === 'function') {
                    initNeighborMap();
                }
            })
            .catch(function (error) {
                console.error(error);
                alert('Nachbarschaft konnte nicht geladen werden.');
            });
    }

    function initNeighborMap() {
        const wrapper = document.getElementById('neighbor-wrapper');
        if (!wrapper) return;

        const hasCoords = wrapper.dataset.baseLat && wrapper.dataset.baseLng;
        if (!hasCoords) return;

        const baseLat = parseFloat(wrapper.dataset.baseLat);
        const baseLng = parseFloat(wrapper.dataset.baseLng);
        let radiusKm  = parseFloat(wrapper.dataset.radius || '3');
        const leadId  = wrapper.dataset.leadId;
        const altId   = wrapper.dataset.altId || '';

        let neighbors = [];
        try {
            neighbors = JSON.parse(wrapper.dataset.neighbors || '[]');
        } catch (e) {
            console.error('Failed to parse neighbors JSON', e);
        }

        if (typeof google === 'undefined' || !google.maps) {
            console.error('Google Maps API not loaded yet.');
            return;
        }

        const mapEl = document.getElementById('neighbor-map');
        if (!mapEl) return;

        const center = { lat: baseLat, lng: baseLng };

        const map = new google.maps.Map(mapEl, {
            center: center,
            zoom: getZoomForRadius(radiusKm),
            mapTypeId: 'roadmap',
            gestureHandling: 'greedy',
            scrollwheel: true
        });

        // Base marker (aktueller Kunde / Objekt)
        new google.maps.Marker({
            position: center,
            map: map,
            label: 'X',
            title: 'Aktueller Kunde / Objekt'
        });

        let circle = new google.maps.Circle({
            map: map,
            center: center,
            radius: radiusKm * 1000,
            fillOpacity: 0.08,
            strokeOpacity: 0.4,
        });

        const markers = [];

        function clearMarkers() {
            markers.forEach(function (m) { m.setMap(null); });
            markers.length = 0;
        }

        function highlightNeighborItem(neighborId) {
            const listEl = document.getElementById('neighbor-list');
            if (!listEl) return;

            listEl.querySelectorAll('.neighbor-item')
                .forEach(el => el.classList.remove('neighbor-item-active'));

            const target = listEl.querySelector('[data-neighbor-id="' + neighborId + '"]');
            if (target) {
                target.classList.add('neighbor-item-active');
                target.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
            }
        }

        function focusNeighborMarker(marker, neighborId) {
            if (!marker) return;
            map.panTo(marker.getPosition());
            marker.setAnimation(google.maps.Animation.BOUNCE);
            setTimeout(function () { marker.setAnimation(null); }, 800);
            highlightNeighborItem(neighborId);
        }

        function renderMarkers(data) {
            clearMarkers();
            data.forEach(function (n) {
                if (!n.lat || !n.lng) return;

                const marker = new google.maps.Marker({
                    position: { lat: parseFloat(n.lat), lng: parseFloat(n.lng) },
                    map: map,
                    title: (n.name || '') + (n.distance_km ? ' (' + n.distance_km + ' km)' : '')
                });

                marker.__neighborId = n.id;
                marker.addListener('click', function () {
                    focusNeighborMarker(marker, n.id);
                });

                markers.push(marker);
            });
        }

        renderMarkers(neighbors);
        bindNeighborListClicks();

        const radiusInput = document.getElementById('radiusRange');
        const radiusLabel = document.getElementById('radiusLabel');

        function updateRadius(newRadius) {
            radiusKm = newRadius;
            circle.setRadius(radiusKm * 1000);
            map.setZoom(getZoomForRadius(radiusKm));

            if (radiusLabel) {
                radiusLabel.textContent = radiusKm.toFixed(1).replace('.', ',') + ' km';
            }

            const url = neighborRoutes.data
                + '?customer_id=' + encodeURIComponent(leadId)
                + '&alternative_id=' + encodeURIComponent(altId)
                + '&radius=' + encodeURIComponent(radiusKm);

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(function (payload) {
                    renderMarkers(payload.neighbors || []);

                    const listEl = document.getElementById('neighbor-list');
                    if (listEl && payload.html_list !== undefined) {
                        listEl.innerHTML = payload.html_list;
                    }

                    bindNeighborListClicks();
                })
                .catch(function (error) {
                    console.error(error);
                });
        }

        function bindNeighborListClicks() {
            const listEl = document.getElementById('neighbor-list');
            if (!listEl) return;

            listEl.querySelectorAll('.neighbor-item').forEach(function (itemEl) {
                itemEl.addEventListener('click', function () {
                    const neighborId = itemEl.dataset.neighborId;
                    const marker = markers.find(m => String(m.__neighborId) === String(neighborId));
                    if (marker) {
                        focusNeighborMarker(marker, neighborId);
                    }
                });
            });
        }

        if (radiusInput) {
            radiusInput.value = radiusKm;
            radiusInput.addEventListener('input', function (e) {
                const val = parseFloat(e.target.value);
                if (!isNaN(val)) {
                    updateRadius(val);
                }
            });
        }
    }

    function getZoomForRadius(radiusKm) {
        if (radiusKm <= 1)  return 15;
        if (radiusKm <= 2)  return 14;
        if (radiusKm <= 5)  return 13;
        if (radiusKm <= 10) return 12;
        if (radiusKm <= 20) return 11;
        return 10;
    }
</script>

 

 
<script>
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.price-edit-trigger');
        if (!btn) return;

        const plId = btn.dataset.plId;
        if (!plId) {
            console.error('lead_product_lists id (data-pl-id) fehlt.');
            return;
        }

        const currentPrice = btn.dataset.currentPrice || '0';

        Swal.fire({
            title: 'Preis ändern',
            input: 'number',
            inputLabel: 'Neuer Preis in EUR',
            inputValue: currentPrice,
            inputAttributes: {
                step: '0.01',
                min: '0'
            },
            showCancelButton: true,
            confirmButtonText: 'Speichern',
            cancelButtonText: 'Abbrechen',

            // responsive width, nice alignment
            width: 'min(420px, 90vw)',
            scrollbarPadding: false,
            backdrop: true,

            didOpen: (modalEl) => {
                const inputEl = modalEl.querySelector('input.swal2-input');
                if (inputEl) {
                    inputEl.style.textAlign = 'center';
                    inputEl.focus();
                    inputEl.select();
                }
            },

            preConfirm: (value) => {
                if (value === '' || value === null || isNaN(value) || Number(value) < 0) {
                    Swal.showValidationMessage('Bitte einen gültigen Preis eingeben.');
                    return false;
                }
                return Number(value).toFixed(2);
            }
        }).then((result) => {
            if (!result.isConfirmed) return;

            const price = result.value;

            fetch('{{ route("leadProduct.updatePrice") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    id: plId,
                    price: price
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Fehler beim Speichern');
                }

                // Button / Preis aktualisieren
                btn.dataset.currentPrice = data.price_raw;
                if (data.price_latest) {
                    btn.dataset.priceLatest = data.price_latest; // falls du Tooltip etc. nutzt
                }

                const priceSpan = btn.querySelector('.price-value');
                if (priceSpan) {
                    priceSpan.textContent = data.price_formatted;
                }

                // Umsatz oben aktualisieren
                const totalNode = document.getElementById('customerTotalPurchase');
                if (totalNode && data.total_purchase_formatted) {
                    totalNode.textContent = 'Gesamt: ' + data.total_purchase_formatted;
                    totalNode.dataset.totalPurchaseRaw = data.total_purchase_raw ?? '';
                }

                // Optional: Status / Datum irgendwo anzeigen (falls passende Elemente existieren)
                if (data.purchase_status) {
                    const statusNode = document.getElementById('customerPurchaseStatus');
                    if (statusNode) {
                        statusNode.textContent = data.purchase_status;
                    }
                }
                if (data.purchase_date) {
                    const dateNode = document.getElementById('customerPurchaseDate');
                    if (dateNode) {
                        dateNode.textContent = data.purchase_date;
                    }
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Gespeichert',
                    text: 'Preis wurde aktualisiert.',
                    timer: 1500,
                    showConfirmButton: false
                });
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Fehler',
                    text: 'Preis konnte nicht gespeichert werden.'
                });
            });
        });
    });
</script>
  <script>
document.addEventListener('DOMContentLoaded', function () {
    const ptBackdrop       = document.getElementById('projectTimeBackdrop');
    const ptCloseBtn       = document.getElementById('ptCloseBtn');
    const ptBody           = document.getElementById('ptBody');
    const ptTimeline       = document.getElementById('ptTimeline');
    const ptRequestForm    = document.getElementById('ptRequestForm');
    const ptRequestMessage = document.getElementById('ptRequestMessage');
    const ptRequestHistory = document.getElementById('ptRequestHistory');

    const ptProductTitle   = document.getElementById('ptProductTitle');
    const ptBaseTime       = document.getElementById('ptBaseTime');
    const ptExtraTime      = document.getElementById('ptExtraTime');
    const ptTotalBudget    = document.getElementById('ptTotalBudget');
    const ptUsedTime       = document.getElementById('ptUsedTime');
    const ptRemainingTime  = document.getElementById('ptRemainingTime');
    const ptDurationLabel  = document.getElementById('ptDurationLabel');
    const ptStatusBadge    = document.getElementById('ptStatusBadge');

    // Info fields
    const ptCustomerName     = document.getElementById('ptCustomerName');
    const ptCustomerAddress  = document.getElementById('ptCustomerAddress');
    const ptAlternativeLabel = document.getElementById('ptAlternativeLabel');
    const ptAlternativeInfo  = document.getElementById('ptAlternativeInfo');
    const ptProductName      = document.getElementById('ptProductName');
    const ptProductInfo      = document.getElementById('ptProductInfo');

    const ptCustomerId    = document.getElementById('ptCustomerId');
    const ptAlternativeId = document.getElementById('ptAlternativeId');
    const ptProductId     = document.getElementById('ptProductId');
    const ptSectionId     = document.getElementById('ptSectionId');

    let ptPieChart = null;
    const projectTimeCache = {};

    function cacheKey(customerId, alternativeId, productId) {
        return String(customerId || '') + '|' + String(alternativeId || '') + '|' + String(productId || '');
    }

    function hmToMinutes(hm) {
        if (!hm) return 0;
        const parts = String(hm).split(':');
        const h = parseInt(parts[0], 10) || 0;
        const m = parseInt(parts[1], 10) || 0;
        return h * 60 + m;
    }

    function renderProjectTimeChart(project) {
        if (!window.Chart) return;

        const used      = hmToMinutes(project.used_hm);
        const remaining = hmToMinutes(project.remaining_hm);
        const ctx       = document.getElementById('ptPieChart');
        if (!ctx) return;

        if (ptPieChart) {
            ptPieChart.destroy();
        }

        ptPieChart = new Chart(ctx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Verbraucht', 'Rest'],
                datasets: [{
                    data: [used, Math.max(remaining, 0)],
                    backgroundColor: ['#ef4444', '#10b981'],
                    borderWidth: 0
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const value = context.raw || 0;
                                const hrs = Math.floor(value / 60);
                                const min = value % 60;
                                return `${context.label}: ${hrs}h ${min}m`;
                            }
                        }
                    }
                }
            }
        });
    }

    function updateCardMetricTime(customerId, alternativeId, productId, project) {
        const selector =
            `.project-metric--time[data-customer-id="${customerId}"][data-alternative-id="${alternativeId}"][data-product-id="${productId}"],` +
            `.project-metric--time[data-customer-id="${customerId}"][data-object-alternative-id="${alternativeId}"][data-product-id="${productId}"]`;

        const buttons = document.querySelectorAll(selector);
        if (!buttons.length) return;

        const used  = project.used_hm || '00:00';
        const total = project.total_budget_hm || project.base_hm || '--:--';
        const rest  = project.remaining_hm || '–:–';

        buttons.forEach(btn => {
            const valueEl = btn.querySelector('.js-project-time-display');
            if (valueEl) {
                valueEl.textContent = `${rest} h`;
            }

            btn.setAttribute(
                'title',
                `Verbraucht: ${used} / Budget: ${total} – Rest: ${rest}`
            );
        });
    }

    function renderProjectTimeDom(customerId, alternativeId, productId, data, openDrawer) {
        const p = data.project || {};

        // Drawer open
        if (openDrawer && ptBackdrop) {
            ptBackdrop.classList.add('is-open');
        }

        // Header title
        if (ptProductTitle) {
            ptProductTitle.textContent = (p.customer || '-') + ' – ' + (p.product || '-');
        }

        // Info cards
        if (ptCustomerName) {
            ptCustomerName.textContent = p.customer || '-';
        }
        if (ptCustomerAddress) {
            ptCustomerAddress.textContent = p.customer_address || p.customer_city || '–';
        }

        if (ptAlternativeLabel) {
            ptAlternativeLabel.textContent = p.alternative || p.alternative_label || '–';
        }
        if (ptAlternativeInfo) {
            ptAlternativeInfo.textContent = p.alternative_info || '';
        }

        if (ptProductName) {
            ptProductName.textContent = p.product || '-';
        }
        if (ptProductInfo) {
            ptProductInfo.textContent = p.product_info || p.article_group || '';
        }

        if (ptStatusBadge) {
            const statusLabel = p.status_label || p.status || '–';
            ptStatusBadge.textContent = statusLabel;
        }

        // Zeitwerte
        if (ptBaseTime)      ptBaseTime.textContent      = p.base_hm || '--:--';
        if (ptExtraTime)     ptExtraTime.textContent     = p.extra_hm || '00:00';
        if (ptTotalBudget)   ptTotalBudget.textContent   = p.total_budget_hm || '--:--';
        if (ptUsedTime)      ptUsedTime.textContent      = p.used_hm || '00:00';
        if (ptRemainingTime) ptRemainingTime.textContent = p.remaining_hm || '00:00';

        // Zeitraum
        if (ptDurationLabel) {
            if (p.start_date && p.end_date) {
                ptDurationLabel.textContent = (p.start_date === p.end_date)
                    ? (p.start_date + ' (1 Tag)')
                    : (p.start_date + ' – ' + p.end_date + ' (' + (p.duration_days || '?') + ' Tage)');
            } else {
                ptDurationLabel.textContent = 'Keine Daten';
            }
        }

        // Chart + card metric
        renderProjectTimeChart(p);
        updateCardMetricTime(customerId, alternativeId, productId, p);

        // TIMELINE
        if (ptTimeline) {
            const timeline = data.timeline || [];
            if (!timeline.length) {
                ptTimeline.innerHTML = '<div class="ph-empty">Keine Aufgabenzeiten erfasst.</div>';
            } else {
                let html = '';
                timeline.forEach(item => {
                    html += `
                        <div class="pt-node">
                            <div class="pt-node-head">
                                <span class="pt-node-date">${item.date || '-'}</span>
                                <span class="pt-node-time">${item.hm || '00:00'} h</span>
                            </div>
                            <div class="pt-node-body">
                                ${item.notes ? item.notes : ''}
                            </div>
                        </div>
                    `;
                });
                ptTimeline.innerHTML = html;
            }
        }

        // REQUEST HISTORY
        if (ptRequestHistory) {
            const requests = data.requests || [];
            if (!requests.length) {
                ptRequestHistory.innerHTML = '<div class="ph-empty">Keine Zeit-Erweiterungen.</div>';
            } else {
                let html = '';
                requests.forEach(req => {
                    const badge =
                        req.status === 'approved' ? '<span class="badge badge-success badge-pill">Genehmigt</span>' :
                        req.status === 'rejected' ? '<span class="badge badge-danger badge-pill">Abgelehnt</span>' :
                                                    '<span class="badge badge-warning badge-pill">Offen</span>';

                    html += `
                        <div class="ph-entry mb-50">
                            <div class="ph-entry-head d-flex justify-content-between">
                                <div class="ph-entry-title">
                                    ${badge} &nbsp; <strong>${req.extra_hm || '00:00'} h</strong>
                                </div>
                                <div class="text-xs text-muted">${req.created_at || ''}</div>
                            </div>
                            <div class="ph-entry-meta text-xs">
                                <strong>Beantragt von:</strong> ${req.requested_by || '-'}<br>
                                <strong>Begründung:</strong> ${req.reason || '-'}<br>
                                ${req.approved_by ? '<strong>Bearbeitet von:</strong> ' + req.approved_by + '<br>' : ''}
                                ${req.answer ? '<strong>Antwort:</strong> ' + req.answer : ''}
                            </div>
                        </div>
                    `;
                });
                ptRequestHistory.innerHTML = html;
            }
        }

        if (window.feather) feather.replace();
    }

    function loadProjectTimeData(customerId, alternativeId, productId, openDrawer) {
        if (!customerId || !productId) return;

        const key = cacheKey(customerId, alternativeId, productId);

        // If cached → no request
        if (projectTimeCache[key]) {
            renderProjectTimeDom(customerId, alternativeId, productId, projectTimeCache[key], openDrawer);
            return;
        }

        const url = '{{ route('project-time.show') }}'
            + '?customer_id=' + encodeURIComponent(customerId)
            + '&alternative_id=' + encodeURIComponent(alternativeId || '')
            + '&product_id=' + encodeURIComponent(productId);

        if (openDrawer && ptTimeline) {
            ptTimeline.innerHTML = '<div class="ph-loading">Lade Zeitdaten...</div>';
            if (ptRequestHistory) ptRequestHistory.innerHTML = '';
            if (ptRequestMessage) ptRequestMessage.textContent = '';
        }

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => {
                projectTimeCache[key] = data;
                renderProjectTimeDom(customerId, alternativeId, productId, data, openDrawer);
            })
            .catch(err => {
                console.error(err);
                if (ptTimeline) {
                    ptTimeline.innerHTML = '<div class="ph-error text-danger">Fehler beim Laden der Projektzeit.</div>';
                }
            });
    }

    function openProjectTimeDrawer(customerId, alternativeId, productId) {
        loadProjectTimeData(customerId, alternativeId, productId, true);
    }

    function closeProjectTimeDrawer() {
        if (ptBackdrop) {
            ptBackdrop.classList.remove('is-open');
        }
    }

    // Delegation for open button
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('.project-time-trigger');
        if (trigger) {
            const customerId    = trigger.getAttribute('data-customer-id');
            const alternativeId = trigger.getAttribute('data-object-alternative-id') || trigger.getAttribute('data-alternative-id');
            const productId     = trigger.getAttribute('data-object-product') || trigger.getAttribute('data-product-id');

            openProjectTimeDrawer(customerId, alternativeId, productId);
        }

        if (e.target === ptBackdrop) {
            closeProjectTimeDrawer();
        }
    });

    if (ptCloseBtn) {
        ptCloseBtn.addEventListener('click', closeProjectTimeDrawer);
    }

    // REQUEST FORM submit
    if (ptRequestForm) {
        ptRequestForm.addEventListener('submit', function (e) {
            e.preventDefault();
            if (ptRequestMessage) {
                ptRequestMessage.textContent = 'Sende Anfrage...';
            }

            const formData = new FormData(ptRequestForm);

            fetch('{{ route('project-time.request') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token') || document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(async res => {
                const data = await res.json();
                if (!res.ok) {
                    throw data;
                }
                if (ptRequestMessage) {
                    ptRequestMessage.textContent = data.message || 'Anfrage gespeichert.';
                }

                // Invalidate cache for this product and reload
                const key = cacheKey(ptCustomerId.value, ptAlternativeId.value, ptProductId.value);
                delete projectTimeCache[key];

                loadProjectTimeData(
                    ptCustomerId.value,
                    ptAlternativeId.value,
                    ptProductId.value,
                    true
                );
            })
            .catch(err => {
                console.error(err);
                const msg = err && err.message ? err.message : 'Fehler beim Senden der Anfrage.';
                if (ptRequestMessage) {
                    ptRequestMessage.textContent = msg;
                }
            });
        });
    }

    // PRELOAD FOR ALL PROJECT-TIME BUTTONS ON CUSTOMER PROFILE LOAD
    const timeTriggers = document.querySelectorAll('.project-time-trigger');
    timeTriggers.forEach(trigger => {
        const customerId    = trigger.getAttribute('data-customer-id');
        const alternativeId = trigger.getAttribute('data-object-alternative-id') || trigger.getAttribute('data-alternative-id');
        const productId     = trigger.getAttribute('data-object-product') || trigger.getAttribute('data-product-id');

        // Load data in background, do not open drawer
        loadProjectTimeData(customerId, alternativeId, productId, false);
    });
});
</script>


<!-- Feed Script  -->
  <script>
(function () {
  "use strict";

  const FEED_ENDPOINT = "/lead/customer-feed"; // /{id}?limit=10

  const KIND_LABELS = {
    product:     "Produkt",
    appointment: "Termin",
    task:        "Aufgabe",
    ticket:      "Ticket",
    history:     "Historie"
  };

  const KIND_PILL_CLASS = {
    product:     "feed-modal-icon-pill--product",
    appointment: "feed-modal-icon-pill--appointment",
    task:        "feed-modal-icon-pill--task",
    ticket:      "feed-modal-icon-pill--ticket",
    history:     "feed-modal-icon-pill--history"
  };

  const KIND_DEFAULT_ICON = {
    product:     "icon-package",
    appointment: "icon-calendar",
    task:        "icon-check-square",
    ticket:      "icon-life-buoy",
    history:     "icon-activity"
  };

  function escapeHtml(str) {
    if (!str) return "";
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;")
      .replace(/'/g, "&#039;");
  }

  async function fetchCustomerFeed(customerId, limit) {
    const url = FEED_ENDPOINT + "/" + encodeURIComponent(customerId) + "?limit=" + (limit || 10);

    const res = await fetch(url, {
      headers: {
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest"
      },
      credentials: "same-origin"
    });

    if (!res.ok) {
      throw new Error("Feed lädt nicht");
    }

    let json;
    try {
      json = await res.json();
    } catch (e) {
      throw new Error("Antwort ist kein gültiges JSON");
    }

    if (!json || json.success === false) {
      const msg = (json && (json.error || json.message)) || "Feed Fehler";
      throw new Error(msg);
    }

    const items = Array.isArray(json.items) ? json.items : [];
    return items;
  }

  function applyItemToDOM(root, item, index, total) {
    const emptyLine   = root.querySelector("[data-feed-empty]");
    const line        = root.querySelector("[data-feed-line]");
    const titleEl     = root.querySelector("[data-feed-title]");
    const textEl      = root.querySelector("[data-feed-text]");
    const pillEl      = root.querySelector("[data-feed-pill]");
    const timeEl      = root.querySelector("[data-feed-time]");
    const counterEl   = root.querySelector("[data-feed-counter]");
    const iconNode    = root.querySelector(".cfs-icon i, .live-feed-icon i");
    const errorEl     = root.querySelector("[data-feed-error]");

    if (errorEl) {
      errorEl.textContent = "";
      errorEl.classList.add("d-none");
    }

    if (emptyLine) emptyLine.classList.add("d-none");
    if (line)      line.classList.remove("d-none");

    if (titleEl) titleEl.textContent = item.title || "Aktivität";
    if (textEl)  textEl.textContent  = item.text || "";
    if (pillEl)  pillEl.textContent  = item.pill || "Info";
    if (timeEl)  timeEl.textContent  = item.time || "–";

    if (counterEl) {
      counterEl.textContent = total > 1 ? (index + 1) + " / " + total : "";
    }

    if (root) {
      root.dataset.kind = item.kind || "";
      root.classList.remove("is-empty");
      root.style.display = "flex";
    }

    if (iconNode) {
      const base    = "feather";
      const iconCls = item.icon || KIND_DEFAULT_ICON[item.kind] || "icon-activity";
      iconNode.className = base + " " + iconCls;
    }
  }

  function showEmptyState(root, errorMessage) {
    const emptyLine   = root.querySelector("[data-feed-empty]");
    const line        = root.querySelector("[data-feed-line]");
    const counterEl   = root.querySelector("[data-feed-counter]");
    const errorEl     = root.querySelector("[data-feed-error]");

    if (line) line.classList.add("d-none");
    if (counterEl) counterEl.textContent = "";

    if (errorMessage && errorEl) {
      errorEl.textContent = errorMessage;
      errorEl.classList.remove("d-none");
      if (emptyLine) emptyLine.classList.add("d-none");
    } else {
      if (errorEl) {
        errorEl.textContent = "";
        errorEl.classList.add("d-none");
      }
      if (emptyLine) emptyLine.classList.remove("d-none");
    }

    root.classList.add("is-empty");
    root.style.display = "flex";
  }

  function parseGermanDateTime(str) {
    if (!str) return 0;
    const parts = String(str).split(" ");
    const datePart = parts[0] || "";
    const timePart = parts[1] || "";
    const dBits = datePart.split(".");
    if (dBits.length < 3) return 0;
    const day = parseInt(dBits[0], 10) || 1;
    const month = (parseInt(dBits[1], 10) || 1) - 1;
    const year = parseInt(dBits[2], 10) || 1970;

    let hour = 0, min = 0;
    if (timePart) {
      const tBits = timePart.split(":");
      hour = parseInt(tBits[0], 10) || 0;
      min  = parseInt(tBits[1], 10) || 0;
    }

    return new Date(year, month, day, hour, min, 0, 0).getTime();
  }

  function openFeedModal(root) {
    const state = root._feedState;
    if (!state || !state.items || !state.items.length) return;

    const modal = document.getElementById("customerFeedModal");
    if (!modal) return;

    const titleEl    = modal.querySelector("[data-feed-modal-title]");
    const subTitleEl = modal.querySelector("[data-feed-modal-subtitle]");
    const listEl     = modal.querySelector("[data-feed-modal-list]");
    const emptyEl    = modal.querySelector("[data-feed-modal-empty]");
    const countEl    = modal.querySelector("[data-feed-modal-count]");
    const searchEl   = modal.querySelector("[data-feed-modal-search]");
    const sortEl     = modal.querySelector("[data-feed-modal-sort]");
    const kindBtns   = modal.querySelectorAll("[data-feed-modal-kind]");

    const customerTitle = root.getAttribute("data-customer-title") || "";

    if (titleEl)    titleEl.textContent    = "Aktivitäten";
    if (subTitleEl) subTitleEl.textContent = customerTitle;

    const items = state.items.map(function (it) {
      const copy = Object.assign({}, it);
      copy._sortAt = parseGermanDateTime(copy.time);
      return copy;
    });

    const modalState = {
      items: items,
      filtered: [],
      kind: "all",
      search: "",
      sort: (sortEl && sortEl.value) ? sortEl.value : "desc"
    };
    modal._feedModalState = modalState;

    function renderList() {
      if (!listEl || !emptyEl || !countEl) return;

      let filtered = modalState.items.slice();

      if (modalState.kind !== "all") {
        filtered = filtered.filter(function (it) {
          return it.kind === modalState.kind;
        });
      }

      if (modalState.search) {
        const q = modalState.search.toLowerCase();
        filtered = filtered.filter(function (it) {
          const t = (it.title || "").toLowerCase();
          const tx = (it.text || "").toLowerCase();
          const p = (it.pill || "").toLowerCase();
          return t.indexOf(q) !== -1 || tx.indexOf(q) !== -1 || p.indexOf(q) !== -1;
        });
      }

      filtered.sort(function (a, b) {
        if (modalState.sort === "asc") {
          return (a._sortAt || 0) - (b._sortAt || 0);
        }
        return (b._sortAt || 0) - (a._sortAt || 0);
      });

      modalState.filtered = filtered;

      if (!filtered.length) {
        listEl.innerHTML = "";
        emptyEl.classList.remove("d-none");
        countEl.textContent = "Anzahl: 0";
        return;
      }

      emptyEl.classList.add("d-none");
      countEl.textContent = "Anzahl: " + filtered.length;

      var html = "";
      filtered.forEach(function (item) {
        const kind = item.kind || "";
        const icon = item.icon || KIND_DEFAULT_ICON[kind] || "icon-activity";
        const kindLabel = KIND_LABELS[kind] || "Aktivität";
        const pillCls   = KIND_PILL_CLASS[kind] || "feed-modal-icon-pill--history";

        const employees = Array.isArray(item.employees) ? item.employees : [];

        let avatarsHtml = "";
        if (employees.length) {
          avatarsHtml += '<div class="feed-modal-avatars">';
          employees.slice(0, 5).forEach(function (emp) {
            const img = emp.image || "";
            if (img) {
              avatarsHtml += '<div class="feed-modal-avatar"><img src="' + escapeHtml(img) + '" alt=""></div>';
            }
          });
          if (employees.length > 5) {
            avatarsHtml += '<span class="feed-modal-avatars-more">+' + (employees.length - 5) + '</span>';
          }
          avatarsHtml += "</div>";
        }

        html += ''
          + '<div class="feed-modal-item" data-kind="' + escapeHtml(kind) + '">'
          + '  <div class="feed-modal-item-icon">'
          + '    <div class="feed-modal-icon-pill ' + pillCls + '">'
          + '      <i class="feather ' + escapeHtml(icon) + '"></i>'
          + '    </div>'
          + '  </div>'
          + '  <div class="feed-modal-item-main">'
          + '    <div class="feed-modal-item-header">'
          + '      <div class="feed-modal-item-title">' + escapeHtml(item.title || "Aktivität") + '</div>'
          + '      <div class="feed-modal-item-time">'
          + '        <i class="feather icon-clock"></i>'
          +          escapeHtml(item.time || "–")
          + '      </div>'
          + '    </div>'
          + '    <div class="feed-modal-item-text">'
          +        escapeHtml(item.text || "")
          + '    </div>'
          +      avatarsHtml
          + '    <div class="feed-modal-item-meta">'
          + '      <span class="badge badge-light feed-modal-pill">'
          +          escapeHtml(item.pill || "Info")
          + '      </span>'
          + '      <span class="feed-modal-kind-label">'
          +          kindLabel
          + '      </span>'
          + '    </div>'
          + '  </div>'
          + '</div>';
      });

      listEl.innerHTML = html;
    }

    if (searchEl) {
      searchEl.value = "";
      searchEl.oninput = function () {
        modalState.search = this.value || "";
        renderList();
      };
    }

    if (sortEl) {
      sortEl.onchange = function () {
        modalState.sort = this.value || "desc";
        renderList();
      };
    }

    if (kindBtns && kindBtns.length) {
      kindBtns.forEach(function (btn) {
        btn.addEventListener("click", function () {
          const kind = this.getAttribute("data-feed-modal-kind") || "all";
          modalState.kind = kind;

          kindBtns.forEach(function (b) {
            b.classList.remove("active");
          });
          this.classList.add("active");

          renderList();
        });
      });
    }

    renderList();

    if (window.jQuery && typeof jQuery !== "undefined" && typeof jQuery.fn.modal === "function") {
      jQuery("#customerFeedModal").modal("show");
    } else {
      modal.style.display = "block";
      modal.classList.add("show");
    }
  }

  function initFeed(root) {
    if (!root || root._feedInitialized) return;
    root._feedInitialized = true;

    const customerId = root.getAttribute("data-customer-id");
    if (!customerId) {
      showEmptyState(root);
      return;
    }

    const limitAttr = root.getAttribute("data-feed-limit") || "10";
    const limit     = parseInt(limitAttr, 10) || 10;

    const state = {
      items: [],
      index: 0,
      playing: false,
      timer: null
    };
    root._feedState = state;

    const btnPrev   = root.querySelector("[data-feed-prev]");
    const btnNext   = root.querySelector("[data-feed-next]");
    const btnToggle = root.querySelector("[data-feed-toggle]");
    const btnExpand = root.querySelector("[data-feed-expand]");
    const iconPause = root.querySelector("[data-feed-icon-pause]");
    const iconPlay  = root.querySelector("[data-feed-icon-play]");

    function updateToggleIcons() {
      if (!btnToggle) return;
      if (state.playing) {
        if (iconPause) iconPause.classList.remove("d-none");
        if (iconPlay)  iconPlay.classList.add("d-none");
      } else {
        if (iconPause) iconPause.classList.add("d-none");
        if (iconPlay)  iconPlay.classList.remove("d-none");
      }
    }

    function show(index) {
      if (!state.items.length) {
        showEmptyState(root);
        return;
      }
      const total = state.items.length;
      const idx   = (index % total + total) % total;
      state.index = idx;

      const item = state.items[idx];
      applyItemToDOM(root, item, idx, total);
    }

    function stop() {
      if (state.timer) {
        clearInterval(state.timer);
        state.timer = null;
      }
      state.playing = false;
      updateToggleIcons();
    }

    function start() {
      if (state.playing || state.items.length <= 1) {
        state.playing = false;
        updateToggleIcons();
        return;
      }
      state.playing = true;
      updateToggleIcons();
      state.timer = setInterval(function () {
        show(state.index + 1);
      }, 6000);
    }

    if (btnPrev) {
      btnPrev.addEventListener("click", function () {
        if (!state.items.length) return;
        stop();
        show(state.index - 1);
      });
    }

    if (btnNext) {
      btnNext.addEventListener("click", function () {
        if (!state.items.length) return;
        stop();
        show(state.index + 1);
      });
    }

    if (btnToggle) {
      btnToggle.addEventListener("click", function () {
        if (!state.items.length) return;
        if (state.playing) {
          stop();
        } else {
          start();
        }
      });
    }

    if (btnExpand) {
      btnExpand.addEventListener("click", function () {
        if (!state.items.length) return;
        openFeedModal(root);
      });
    }

    fetchCustomerFeed(customerId, limit)
      .then(function (items) {
        state.items = items || [];
        root.setAttribute("data-feed-count", String(state.items.length || 0));

        if (!state.items.length) {
          showEmptyState(root);
          return;
        }

        show(0);
        start();
      })
      .catch(function (err) {
        showEmptyState(root, err && err.message ? err.message : null);
      });
  }

  function bootstrapCustomerFeeds() {
    const roots = Array.prototype.slice.call(
      document.querySelectorAll(".customer-live-feed[data-feed-root][data-customer-id]")
    );
    if (!roots.length) return;

    let i = 0;
    const BATCH = 5;

    (function pump() {
      const slice = roots.slice(i, i + BATCH);
      i += BATCH;
      slice.forEach(initFeed);
      if (i < roots.length) {
        setTimeout(pump, 80);
      }
    })();
  }

  document.addEventListener("DOMContentLoaded", bootstrapCustomerFeeds);
})();
</script>
   
<script>
(function ($) {
    const modalEl = document.getElementById('customerContactPeopleModal');

    let currentCustomerId = {{ (int) $customer->id }};
    let currentEditId     = null;

    function openContactModal() {
        if (modalEl) {
            modalEl.classList.add('is-open');
        }
    }

    function closeContactModal() {
        if (modalEl) {
            modalEl.classList.remove('is-open');
        }
        resetForm();
    }

    // close via X and "Schließen" button
    $(document).on('click', '.ccp-modal-close-btn', function () {
        closeContactModal();
    });

    // click on backdrop closes modal
    if (modalEl) {
        modalEl.addEventListener('click', function (e) {
            if (e.target === modalEl) {
                closeContactModal();
            }
        });
    }

    function getIndexUrl(customerId) {
        return "{{ route('customers.contact-people.index', ['customer' => '___CID___']) }}"
            .replace('___CID___', customerId);
    }

    function getStoreUrl(customerId) {
        return "{{ route('customers.contact-people.store', ['customer' => '___CID___']) }}"
            .replace('___CID___', customerId);
    }

    function getUpdateUrl(id) {
        return "{{ route('customer-contact-people.update', ['person' => '___PID___']) }}"
            .replace('___PID___', id);
    }

    function getDeleteUrl(id) {
        return "{{ route('customer-contact-people.destroy', ['person' => '___PID___']) }}"
            .replace('___PID___', id);
    }

    // CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    });

    // open modal from icon
    $(document).on('click', '.cn-contact-people-trigger', function () {
        const cid = $(this).data('customer-id');
        currentCustomerId = cid || currentCustomerId;
        loadContactPeople(currentCustomerId);
        resetForm();
        openContactModal();
    });

    // add new
    $('#addContactPersonBtn').on('click', function () {
        resetForm();
        $('#contactPersonFormTitle').text('Kontaktperson hinzufügen');
    });

    // edit
    $(document).on('click', '.cp-edit-btn', function () {
        const id     = $(this).data('id');
        const rowStr = $(this).attr('data-row') || '{}';
        let rowData  = {};

        try {
            rowData = JSON.parse(rowStr);
        } catch (e) {
            console.warn('Cannot parse row data', e);
        }

        currentEditId = id;
        $('#contactPersonFormTitle').text('Kontaktperson bearbeiten');

        $('#contactPersonId').val(id);
        $('#cpRelation').val(rowData.relation || '');
        $('#cpName').val(rowData.name || '');
        $('#cpLastname').val(rowData.lastname || '');
        $('#cpPhone').val(rowData.phone || '');
        $('#cpOffice').val(rowData.office || '');
        $('#cpHome').val(rowData.home || '');
        $('#cpEmail').val(rowData.email || '');
        $('#cpStatus').val(rowData.status || 'Published');
    });

    // delete
    $(document).on('click', '.cp-delete-btn', function () {
        const id = $(this).data('id');

        if (!confirm('Kontaktperson wirklich löschen?')) {
            return;
        }

        $.ajax({
            url: getDeleteUrl(id),
            type: 'DELETE',
            success: function (res) {
                if (res.success) {
                    loadContactPeople(currentCustomerId);
                }
            }
        });
    });

    // submit form
    $('#contactPersonForm').on('submit', function (e) {
        e.preventDefault();

        const id  = $('#contactPersonId').val();
        const cid = $('#contactPersonCustomerId').val();

        const data = {
            relation: $('#cpRelation').val(),
            name: $('#cpName').val(),
            lastname: $('#cpLastname').val(),
            phone: $('#cpPhone').val(),
            office: $('#cpOffice').val(),
            home: $('#cpHome').val(),
            email: $('#cpEmail').val(),
            status: $('#cpStatus').val()
        };

        if (!id) {
            // create
            $.post(getStoreUrl(cid), data, function (res) {
                if (res.success) {
                    resetForm();
                    loadContactPeople(cid);
                } else {
                    console.warn(res.errors || res);
                }
            }).fail(function (xhr) {
                console.error(xhr.responseJSON || xhr.responseText);
            });
        } else {
            // update
            $.ajax({
                url: getUpdateUrl(id),
                method: 'PUT',
                data: data,
                success: function (res) {
                    if (res.success) {
                        resetForm();
                        loadContactPeople(cid);
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseJSON || xhr.responseText);
                }
            });
        }
    });

    function resetForm() {
        currentEditId = null;
        $('#contactPersonId').val('');
        $('#cpRelation').val('');
        $('#cpName').val('');
        $('#cpLastname').val('');
        $('#cpPhone').val('');
        $('#cpOffice').val('');
        $('#cpHome').val('');
        $('#cpEmail').val('');
        $('#cpStatus').val('Published');
    }

    function loadContactPeople(customerId) {
        $.get(getIndexUrl(customerId), function (res) {
            if (!res.success) return;

            const rows = res.data || [];
            renderTable(rows);
            renderPreview(rows, customerId);
        });
    }

    function renderTable(rows) {
        const tbody = $('#contactPeopleTableBody');
        tbody.empty();

        if (!rows.length) {
            tbody.append(
                '<tr><td colspan="6" class="text-muted text-center py-3">Keine Kontaktpersonen vorhanden.</td></tr>'
            );
            return;
        }

        rows.forEach(function (row) {
            const jsonRow = JSON.stringify(row).replace(/"/g, '&quot;');

            tbody.append(`
                <tr>
                    <td>${row.relation || ''}</td>
                    <td>${row.full_name || ''}</td>
                    <td>${row.phone || ''}</td>
                    <td>${row.email || ''}</td>
                    <td>${row.status || ''}</td>
                    <td class="text-end">
                        <button type="button"
                                class="btn btn-xs btn-outline-secondary cp-edit-btn me-1"
                                data-id="${row.id}"
                                data-row="${jsonRow}">
                            <i class="feather icon-edit-2"></i>
                        </button>
                        <button type="button"
                                class="btn btn-xs btn-outline-danger cp-delete-btn"
                                data-id="${row.id}">
                            <i class="feather icon-trash-2"></i>
                        </button>
                    </td>
                </tr>
            `);
        });
    }

    function renderPreview(rows, customerId) {
        const previewEl = $('#contactPeoplePreview-' + customerId);
        const badgeEl   = $('#contactPeopleCountBadge-' + customerId);

        badgeEl.text(rows.length);

        if (!rows.length) {
            previewEl.html('<span class="text-muted small">Keine Kontaktpersonen</span>');
            return;
        }

        const firstTwo = rows.slice(0, 2)
            .map(r => `${r.full_name || 'ohne Name'} (${r.relation || 'n/a'})`);

        let text = firstTwo.join(' · ');
        if (rows.length > 2) {
            text += ` · +${rows.length - 2} weitere`;
        }

        previewEl.html(`<span class="small text-muted"><i class="feather icon-users me-1"></i>${text}</span>`);
    }

    // initial preview load
    loadContactPeople(currentCustomerId);

})(jQuery);
</script>

<script>
(() => {
  'use strict';

  /* ===== Boot data from Blade ===== */
const STAGE        = @json($stage ?? 'lead');
const SERVICES    = @json($new_services);
const PRODUCTS    = @json($new_products);
   const  DEPARTMENTS = @json($departments);
  const CSRF         = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

  const EMP_IMG_DIR  = "{{ asset('images/employee') }}";
  const DEFAULT_AVA  = "{{ asset('images/gender/male.png') }}";

  let modalRowIndex  = 0;

  /* ===== Labels ===== */
  const SERVICE_LABEL = {
    complete:'Komplettlösung', montage:'Montage', product:'Produkt', plan:'Planung',
    maintenance:'Wartung', repair:'Reparatur', reclaim:'Reklamation',
    emergency:'Notdienst', others:'Sonstiges'
  };
  const INTEREST_LABEL = { intent:'Kaufabsicht', interest:'Kaufinteresse', option:'Kaufoption' };
  const REALIZATION_LABEL = { soon:'Schnellstmöglich', '3':'3 Monate', '6':'6 Monate', other:'Sonstiges' };

  const tService      = k => SERVICE_LABEL[(k||'').toLowerCase()] || (k||'');
  const tInterest     = v => INTEREST_LABEL[v] || '-';
  const tRealization  = v => REALIZATION_LABEL[String(v)] || '-';
  const empImg        = f => f ? `${EMP_IMG_DIR}/${f}` : DEFAULT_AVA;

  const $existing = $('#existingProductRows');
  const $newRows  = $('#modalNewRows');

  const debounce = (fn, ms) => { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a), ms); }; };
  const autoPickIfSingle = ($select) => {
    const opts = $select.find('option[value!=""]');
    if (!$select.val() && opts.length === 1) $select.val(opts.first().val()).trigger('change');
  };

  /* ===== Custom modal helpers ===== */
  const $addModal  = $('#addCustomerProductModal');
  const $editModal = $('#editCustomerProduct');

  function openModal($m){
    $m.addClass('is-open').attr('aria-hidden','false');
    document.body.classList.add('cmodal-open');
    // focus first focusable
    const el = $m.find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])').filter(':visible').first();
    if (el.length) el.trigger('focus');
  }

  function closeModal($m){
    $m.removeClass('is-open').attr('aria-hidden','true');
    if (!$('.cmodal.is-open').length) document.body.classList.remove('cmodal-open');
  }

  // Close: backdrop + any [data-modal-close]
  $(document).on('click', '[data-modal-close]', function(){
    const $m = $(this).closest('.cmodal');
    if ($m.length) closeModal($m);
  });

  // Close: ESC
  $(document).on('keydown', function(e){
    if (e.key === 'Escape') {
      const $m = $('.cmodal.is-open').last();
      if ($m.length) closeModal($m);
    }
  });

  /* ===== Select2 template for employees ===== */
  function employeeTpl(opt){
    if (!opt.id) return opt.text;
    const $el = $(opt.element);
    const img = $el.data('img') ? `${EMP_IMG_DIR}/${$el.data('img')}` : DEFAULT_AVA;
    const pos = $el.data('positions') || '';
    return $(`
      <div style="display:flex;align-items:center;gap:8px;">
        <img src="${img}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
        <div><strong>${opt.text}</strong><br><small>${pos}</small></div>
      </div>
    `);
  }

  function s2($el, opts){
    if (!$el || !$el.length) return;
    if ($el.data('select2')) $el.select2('destroy');
    $el.select2(opts);
  }

  const addDropdownParent  = $('#addCustomerProductModal .cmodal__dialog');
  const editDropdownParent = $('#editCustomerProduct .cmodal__dialog');

  /* ===== Open Add modal ===== */
  $(document).on('click', '.addNewProduct', function () {
    const customerId    = $(this).data('id');
    const alternativeId = $(this).data('alternative-id');

    $('#modal_customer_id').val(customerId);
    $('#modal_alternative_id').val(alternativeId);

    $existing.empty();
    $newRows.empty();
    modalRowIndex = 0;

    // load saved rows
    $.get(`/lead/get/products/${customerId}/${alternativeId}`, (rows = []) => {
      $existing.empty();
      rows.forEach(row => {
        const svcKey = row.service_phase_section
          || (SERVICES.find(s => String(s.id) === String(row.service_id))?.phase_section)
          || row.service_id;

        $existing.append(`
          <tr>
            <td>${row.article_group || '-'}</td>
            <td>${tService(svcKey)}</td>
            <td>${row.department_name || '-'}</td>
            <td>
              <div style="display:flex;align-items:center;gap:8px;">
                <img src="${empImg(row.image)}" style="width:32px;height:32px;border-radius:50%;">
                <span>${(`${row.name??''} ${row.lastname??''}`).trim() || '-'}</span>
              </div>
            </td>
            <td>
              <div style="display:flex;align-items:center;gap:8px;">
                <img src="${empImg(row.feimage)}" style="width:32px;height:32px;border-radius:50%;">
                <span>${(`${row.fename??''} ${row.felastname??''}`).trim() || '-'}</span>
              </div>
            </td>
            <td>${tInterest(row.interest)}</td>
            <td>${tRealization(row.realization_time)}</td>
            <td>
              <button type="button" class="btn btn-sm btn-warning edit-product"
                data-id="${row.id}"
                data-product-id="${row.product_id}"
                data-service-id="${row.service_id ?? ''}"
                data-department-id="${row.department_id ?? ''}"
                data-employee-id="${row.employee_id ?? ''}"
                data-field-employee="${row.field_employee_id ?? row.field_employee ?? ''}"
                data-interest="${row.interest ?? ''}"
                data-realization-time="${row.realization_time ?? ''}">
                <i class="feather icon-edit"></i>
              </button>
              <button type="button" class="btn btn-sm btn-danger delete-product"
                data-id="${row.id}"
                data-customer-id="${row.customer_id}"
                data-alternative-id="${row.alternative_id}"
                data-product-id="${row.product_id}">
                <i class="feather icon-trash"></i>
              </button>
            </td>
          </tr>
        `);
      });
    });

    openModal($addModal);
    addRow();
  });

  /* ===== Add new editable row ===== */
  $('#modalAddRow').on('click', addRow);

  function addRow(){
    modalRowIndex++;
    $newRows.append(rowTemplate(modalRowIndex));
    initRow(modalRowIndex);
  }

  function rowTemplate(i){
    return `
      <tr data-index="${i}">
        <td>
          <select class="form-control product-select" data-index="${i}" name="product_id[]" style="width:100%">
            <option value="">Produkt wählen</option>
            ${PRODUCTS.map(p => `<option value="${p.id}" data-img="${p.image||''}">${p.article_group}</option>`).join('')}
          </select>
        </td>
        <td>
          <select class="form-control service-select" data-index="${i}" name="service_id[]" style="width:100%">
            <option value="">Service wählen</option>
          </select>
        </td>
        <td>
          <select class="form-control department-select" data-index="${i}" name="department_id[]" style="width:100%">
            <option value="">Abteilung wählen</option>
            ${DEPARTMENTS.map(d => `<option value="${d.id}">${d.department_name}</option>`).join('')}
          </select>
        </td>
        <td>
          <select class="form-control employee-select" data-index="${i}" name="employee_id[]" style="width:100%">
            <option value="">Innendienst wählen</option>
          </select>
        </td>
        <td>
          <select class="form-control field-employee-select" data-index="${i}" name="field_employee[]" style="width:100%">
            <option value="">Außendienst wählen</option>
          </select>
        </td>
        <td>
          <select class="form-control interest-select" data-index="${i}" name="interest[]" style="width:100%">
            <option value="intent" selected>Kaufabsicht</option>
            <option value="interest">Kaufinteresse</option>
            <option value="option">Kaufoption</option>
          </select>
        </td>
        <td>
          <select class="form-control realization-select" data-index="${i}" name="realization_time[]" style="width:100%">
            <option value="">Bitte auswählen</option>
            <option value="soon" selected>Schnellstmöglich</option>
            <option value="3">3 Monate</option>
            <option value="6">6 Monate</option>
            <option value="other">Sonstiges</option>
          </select>
        </td>
        <td>
          <button type="button" class="btn btn-sm btn-outline-danger removeRow">
            <i class="feather icon-trash"></i>
          </button>
        </td>
      </tr>`;
  }

  /* ===== Per-row select2 + logic ===== */
  function initRow(i){
    const q = cls => $(`.${cls}[data-index="${i}"]`);
    const $prod = q('product-select');
    const $serv = q('service-select');
    const $dept = q('department-select');
    const $emp  = q('employee-select');
    const $femp = q('field-employee-select');

    // basic select2 inside modal
    [$prod,$serv,$dept,q('interest-select'),q('realization-select')].forEach($el => {
      s2($el, { width:'100%', dropdownParent: addDropdownParent });
    });

    // employees template
    [$emp,$femp].forEach($el => {
      s2($el, {
        width:'100%',
        dropdownParent: addDropdownParent,
        templateResult: employeeTpl,
        templateSelection: o => o.text,
        escapeMarkup: m => m
      });
    });

    $prod.off('change.modal').on('change.modal', () => onProductChanged(i));
    $serv.off('change.modal').on('change.modal', debounce(() => loadEmployees(i), 120));
    $dept.off('change.modal').on('change.modal', debounce(() => loadEmployees(i), 120));
  }

  async function onProductChanged(i){
    const $prod = $(`.product-select[data-index="${i}"]`);
    const $serv = $(`.service-select[data-index="${i}"]`);
    const $dept = $(`.department-select[data-index="${i}"]`);

    const pid  = $prod.val();

    // services for selected product
    const list = SERVICES.filter(s => String(s.product_id) === String(pid));
    $serv.empty().append('<option value="">Service wählen</option>');
    list.forEach(s => $serv.append(`<option value="${s.id}">${tService(s.phase_section)}</option>`));
    $serv.trigger('change');

    if (!pid) { renderBothEmployees(i, [], []); return; }

    try {
      const resp = await $.post('{{ route("inquiry.department.employees") }}', {
        _token: CSRF,
        product_id: pid,
        stage: STAGE
      });

      const suggestion = resp || {};
      let internalEmployees = [];
      let externalEmployees = [];

      if (Array.isArray(suggestion)) {
        internalEmployees = suggestion;
        externalEmployees = suggestion;
      } else {
        internalEmployees = suggestion.internal_employees || suggestion.employees || [];
        externalEmployees = suggestion.external_employees || suggestion.employees || [];
      }

      if (suggestion.department_id) {
        const did = String(suggestion.department_id);
        if (!$dept.find(`option[value="${did}"]`).length) {
          $dept.append(`<option value="${did}">${DEPARTMENTS.find(d=>String(d.id)===did)?.department_name || ('Abt. '+did)}</option>`);
        }
        $dept.val(did).trigger('change.select2');
      }

      if (suggestion.service_id) {
        const sid = String(suggestion.service_id);
        if (!$serv.find(`option[value="${sid}"]`).length) {
          const svc = SERVICES.find(s=>String(s.id)===sid);
          $serv.append(`<option value="${sid}">${tService(svc?.phase_section || '') || 'Service '+sid}</option>`);
        }
        $serv.val(sid).trigger('change.select2');
      }

      if (internalEmployees.length || externalEmployees.length) {
        renderBothEmployees(i, internalEmployees, externalEmployees);
      } else {
        autoPickIfSingle($serv);
        autoPickIfSingle($dept);
        loadEmployees(i);
      }

    } catch(e) {
      autoPickIfSingle($serv);
      autoPickIfSingle($dept);
      loadEmployees(i);
    }
  }

  function renderBothEmployees(i, internalList, externalList){
    const $emp  = $(`.employee-select[data-index="${i}"]`);
    const $femp = $(`.field-employee-select[data-index="${i}"]`);

    const render = ($select, placeholder, list) => {
      $select.empty().append(`<option value="">${placeholder}</option>`);
      (list || []).forEach(e => {
        $select.append(
          `<option value="${e.id}"
                   data-img="${e.image||''}"
                   data-positions="${(e.positions||[]).join(', ')}">
            ${e.name} ${e.lastname}
           </option>`
        );
      });
      s2($select, {
        width:'100%',
        dropdownParent: addDropdownParent,
        templateResult: employeeTpl,
        templateSelection: o => o.text,
        escapeMarkup: m => m
      });
      autoPickIfSingle($select);
    };

    render($emp,  'Innendienst wählen', internalList);
    render($femp, 'Außendienst wählen', externalList);
  }

  function loadEmployees(i){
    const $prod = $(`.product-select[data-index="${i}"]`);
    const $serv = $(`.service-select[data-index="${i}"]`);
    const $dept = $(`.department-select[data-index="${i}"]`);

    const pid = $prod.val();
    const sid = $serv.val();
    const did = $dept.val();

    if (!pid) { renderBothEmployees(i, [], []); return; }

    $.post('{{ route("inquiry.department.employees") }}', {
      _token: CSRF,
      product_id: pid,
      service_id: sid || null,
      department_id: did || null,
      stage: STAGE
    }, (resp = {}) => {
      let internalEmployees = [];
      let externalEmployees = [];

      if (Array.isArray(resp)) {
        internalEmployees = resp;
        externalEmployees = resp;
      } else {
        if (resp.department_id && String(resp.department_id) !== String(did || '')) {
          $dept.val(String(resp.department_id)).trigger('change.select2');
        }
        if (resp.service_id && String(resp.service_id) !== String(sid || '')) {
          $serv.val(String(resp.service_id)).trigger('change.select2');
        }
        internalEmployees = resp.internal_employees || resp.employees || [];
        externalEmployees = resp.external_employees || resp.employees || [];
      }

      renderBothEmployees(i, internalEmployees, externalEmployees);
    }).fail(() => {
      renderBothEmployees(i, [], []);
      Swal.fire({ icon:'error', title:'Fehler', text:'Mitarbeiter konnten nicht geladen werden.' });
    });
  }

  /* ===== Remove new row ===== */
  $(document).on('click', '.removeRow', function(){ $(this).closest('tr').remove(); });

  /* ===== Submit Add form ===== */
  $('#addCustomerProductForm').on('submit', async function (e) {
    e.preventDefault();

    if ($newRows.find('tr').length === 0) {
      return Swal.fire({ icon:'warning', title:'Hinweis', text:'Bitte mindestens eine Zeile hinzufügen.' });
    }

    const $btn = $(this).find('button[type="submit"]');
    const old  = $btn.html();
    $btn.prop('disabled', true).html('Speichern…');

    try {
      const res  = await fetch($(this).attr('action'), {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF },
        body: new FormData(this)
      });

      const txt = await res.text();
      let data; try{ data = JSON.parse(txt); } catch{ data = { ok: res.ok, message: txt || '' }; }

      if (!res.ok) {
        if (res.status === 422 && data?.errors) {
          const list = Object.values(data.errors).flat().join('\n');
          return Swal.fire({ icon:'error', title:'Validierung', text: list || 'Bitte Eingaben prüfen.' });
        }
        return Swal.fire({ icon:'error', title:'Fehler', text: data?.message || `HTTP ${res.status}` });
      }

      Swal.fire({ icon:'success', title:'Gespeichert', text: data?.message || 'Produkte erfolgreich gespeichert.' })
        .then(() => {
          $newRows.empty();

          const cid = $('#modal_customer_id').val();
          const aid = $('#modal_alternative_id').val();

          if (cid && aid) {
            $.get(`/lead/get/products/${cid}/${aid}`, (rows = []) => {
              $existing.empty();
              rows.forEach(row => {
                const svcKey = row.service_phase_section
                  || (SERVICES.find(s => String(s.id) === String(row.service_id))?.phase_section)
                  || row.service_id;

                $existing.append(`
                  <tr>
                    <td>${row.article_group || '-'}</td>
                    <td>${tService(svcKey)}</td>
                    <td>${row.department_name || '-'}</td>
                    <td>
                      <div style="display:flex;align-items:center;gap:8px;">
                        <img src="${empImg(row.image)}" style="width:32px;height:32px;border-radius:50%;">
                        <span>${(`${row.name??''} ${row.lastname??''}`).trim() || '-'}</span>
                      </div>
                    </td>
                    <td>
                      <div style="display:flex;align-items:center;gap:8px;">
                        <img src="${empImg(row.feimage)}" style="width:32px;height:32px;border-radius:50%;">
                        <span>${(`${row.fename??''} ${row.felastname??''}`).trim() || '-'}</span>
                      </div>
                    </td>
                    <td>${tInterest(row.interest)}</td>
                    <td>${tRealization(row.realization_time)}</td>
                    <td>
                      <button type="button" class="btn btn-sm btn-warning edit-product"
                        data-id="${row.id}"
                        data-product-id="${row.product_id}"
                        data-service-id="${row.service_id ?? ''}"
                        data-department-id="${row.department_id ?? ''}"
                        data-employee-id="${row.employee_id ?? ''}"
                        data-field-employee="${row.field_employee_id ?? row.field_employee ?? ''}"
                        data-interest="${row.interest ?? ''}"
                        data-realization-time="${row.realization_time ?? ''}">
                        <i class="feather icon-edit"></i>
                      </button>
                      <button type="button" class="btn btn-sm btn-danger delete-product"
                        data-id="${row.id}"
                        data-customer-id="${row.customer_id}"
                        data-alternative-id="${row.alternative_id}"
                        data-product-id="${row.product_id}">
                        <i class="feather icon-trash"></i>
                      </button>
                    </td>
                  </tr>
                `);
              });
            });
          }
        });

    } catch (err) {
      Swal.fire({ icon:'error', title:'Fehler', text: err?.message || 'Unerwarteter Fehler.' });
    } finally {
      $btn.prop('disabled', false).html(old);
    }
  });

  /* ===== Edit modal (opens from existing table) ===== */
  function fillEditStaticSelects(){
    const $interest = $('#edit_interest');
    const $real     = $('#edit_realization_time');

    if (!$interest.find('option').length) {
      $interest.append(`<option value="intent">Kaufabsicht</option>`)
               .append(`<option value="interest">Kaufinteresse</option>`)
               .append(`<option value="option">Kaufoption</option>`);
    }
    if (!$real.find('option').length) {
      $real.append(`<option value="soon">Schnellstmöglich</option>`)
           .append(`<option value="3">3 Monate</option>`)
           .append(`<option value="6">6 Monate</option>`)
           .append(`<option value="other">Sonstiges</option>`);
    }

    ['#edit_product','#edit_service','#edit_department','#edit_interest','#edit_realization_time'].forEach(sel => {
      s2($(sel), { width:'100%', dropdownParent: editDropdownParent });
    });

    // employee selects with template
    s2($('#edit_employee'), {
      width:'100%',
      dropdownParent: editDropdownParent,
      templateResult: employeeTpl,
      templateSelection: o => o.text,
      escapeMarkup: m => m
    });
    s2($('#edit_field_employee'), {
      width:'100%',
      dropdownParent: editDropdownParent,
      templateResult: employeeTpl,
      templateSelection: o => o.text,
      escapeMarkup: m => m
    });
  }

  function setEditServices(pid, selectedSid){
    const $serv = $('#edit_service');
    $serv.empty().append('<option value="">Wählen...</option>');
    SERVICES.filter(s => String(s.product_id) === String(pid))
      .forEach(s => $serv.append(`<option value="${s.id}">${tService(s.phase_section)}</option>`));
    $serv.val(selectedSid || '').trigger('change.select2');
  }

  function renderEditEmployees(internalList, externalList, selectedEmp, selectedFemp){
    const $emp  = $('#edit_employee');
    const $femp = $('#edit_field_employee');

    const render = ($select, placeholder, list, selected) => {
      $select.empty().append(`<option value="">${placeholder}</option>`);
      (list || []).forEach(e => {
        $select.append(
          `<option value="${e.id}"
                   data-img="${e.image||''}"
                   data-positions="${(e.positions||[]).join(', ')}">
            ${e.name} ${e.lastname}
           </option>`
        );
      });
      $select.val(selected || '').trigger('change.select2');
    };

    render($emp, 'Wählen...', internalList, selectedEmp);
    render($femp,'Wählen...', externalList, selectedFemp);

    // re-apply template select2 (keeps avatars)
    fillEditStaticSelects();
  }

  async function loadEditEmployees(pid, sid, did, selectedEmp, selectedFemp){
    try {
      const resp = await $.post('{{ route("inquiry.department.employees") }}', {
        _token: CSRF,
        product_id: pid,
        service_id: sid || null,
        department_id: did || null,
        stage: STAGE
      });

      let internalEmployees = [];
      let externalEmployees = [];
      if (Array.isArray(resp)) {
        internalEmployees = resp;
        externalEmployees = resp;
      } else {
        internalEmployees = resp.internal_employees || resp.employees || [];
        externalEmployees = resp.external_employees || resp.employees || [];
      }

      renderEditEmployees(internalEmployees, externalEmployees, selectedEmp, selectedFemp);
    } catch(e) {
      renderEditEmployees([], [], selectedEmp, selectedFemp);
    }
  }

  $(document).on('click', '.edit-product', function(){
    const id   = $(this).data('id');
    const pid  = $(this).data('product-id');
    const sid  = $(this).data('service-id');
    const did  = $(this).data('department-id');
    const emp  = $(this).data('employee-id');
    const femp = $(this).data('field-employee');

    $('#edit_product_id').val(id);

    fillEditStaticSelects();

    $('#edit_product').val(pid).trigger('change.select2');
    setEditServices(pid, sid);
    $('#edit_department').val(did || '').trigger('change.select2');

    $('#edit_interest').val($(this).data('interest') || 'intent').trigger('change.select2');
    $('#edit_realization_time').val($(this).data('realization-time') || 'soon').trigger('change.select2');

    openModal($editModal);

    loadEditEmployees(pid, sid, did, emp, femp);
  });

  // When product/service/department changes inside edit modal, refresh dependent lists
  $(document).on('change', '#edit_product', function(){
    const pid = $(this).val();
    setEditServices(pid, null);
    loadEditEmployees(pid, $('#edit_service').val(), $('#edit_department').val(), $('#edit_employee').val(), $('#edit_field_employee').val());
  });
  $(document).on('change', '#edit_service, #edit_department', debounce(function(){
    loadEditEmployees($('#edit_product').val(), $('#edit_service').val(), $('#edit_department').val(), $('#edit_employee').val(), $('#edit_field_employee').val());
  }, 120));

})();
</script>


<!-- Handling the customer History tab  -->
 <script>
    // Add this to your script section
    /* ==========================================
   GLOBAL HISTORY LOGIC 
   ========================================== */

// 1. Main Load Function (Triggered by your button)
function loadHistory(cid, aid, pid) {
    const $mainContent = $('#mainContent');

    // Show Loader
    $mainContent.html(`
        <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    `);

    // AJAX Call
    let url = `/new-leads/${cid}/history-feed`;
    let params = { 
        alternative_id: aid, 
        product_id: pid 
    };

    $.ajax({
        url: url,
        type: 'GET',
        data: params,
        success: function(response) {
            $mainContent.html(response);
            
            // Re-init Feather icons if using feather
            if (typeof feather !== 'undefined') feather.replace();
        },
        error: function(xhr) {
            console.error('History load failed', xhr);
            $mainContent.html('<div class="alert alert-danger m-4">Fehler beim Laden der Historie.</div>');
        }
    });
}

// 2. Debounce Function for Search
function debounceHistory(func, wait) {
    let timeout;
    return function() {
        const context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
    };
}

// 3. EVENT DELEGATION (This works even on dynamically loaded content)

// Handle Input Typing
$(document).on('input', '#historySearchText', debounceHistory(function() {
    performHistoryRefresh();
}, 600));

// Handle Date Change
$(document).on('change', '#historySearchDate', function() {
    performHistoryRefresh();
});

// Handle Reset Button
$(document).on('click', '#btnResetHistory', function() {
    $('#historySearchText').val('');
    $('#historySearchDate').val('');
    performHistoryRefresh();
});

// 4. The Refresh Logic
function performHistoryRefresh() {
    // We read the IDs from the data-attributes we put in the HTML wrapper
    const $wrapper = $('.history-wrapper');
    const cid = $wrapper.data('current-cid');
    const aid = $wrapper.data('current-aid');
    const pid = $wrapper.data('current-pid');
    
    const text = $('#historySearchText').val();
    const date = $('#historySearchDate').val();

    // Select the container to swap
    // We want to replace just the body if possible, OR the whole wrapper. 
    // Replacing the whole wrapper is easier to keep consistent.
    const $container = $('#mainContent'); 

    // Visual feedback (optional opacity change)
    $wrapper.css('opacity', '0.6');

    $.ajax({
        url: `/new-leads/${cid}/history-feed`,
        type: 'GET',
        data: {
            alternative_id: aid,
            product_id: pid,
            search_text: text,
            search_date: date
        },
        success: function(response) {
            $container.html(response);
            if (typeof feather !== 'undefined') feather.replace();
        },
        error: function() {
            alert('Fehler beim Filtern.');
            $wrapper.css('opacity', '1');
        }
    });
}
// Helper to handle the "Active" state of your nav buttons (optional)
function setActiveSubNav(btn) {
    // Remove active class from all buttons with .nav-section-btn
    $('.nav-section-btn').removeClass('active btn-secondary').addClass('btn-outline-secondary');
    
    // Add active class to clicked button
    $(btn).removeClass('btn-outline-secondary').addClass('active btn-secondary');
}
 </script>

 <!-- Loading Invice script:  -->
<script>
  window.NEW_LEADS = window.NEW_LEADS || {};
  window.NEW_LEADS.invoicePanelUrl = {!! json_encode(
    route('admin.new_leads.invoices.panel', [
      'customer' => '__CID__',
      'alternative' => '__AID__',
      'product' => '__PID__',
    ])
  ) !!};
</script>


 <script>
  // loadInvoice(cid, aid, pid) -> loads the invoices partial into #mainContent via AJAX (JSON {ok, html})
  (function () {
    "use strict";

    const mainEl = document.getElementById("mainContent");
    if (!mainEl) return;

    const csrf =
      document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

    // Provide this in Blade (recommended):
    // window.NEW_LEADS.invoicePanelUrl = "{{ route('admin.new_leads.invoices.panel', ['customer'=>'__CID__','alternative'=>'__AID__','product'=>'__PID__']) }}";
    const urlTpl = String(window.NEW_LEADS?.invoicePanelUrl || "");

    let activeAbort = null;

    function buildUrl(cid, aid, pid) {
      if (!urlTpl) throw new Error("invoicePanelUrl template missing (window.NEW_LEADS.invoicePanelUrl).");
      return urlTpl
        .replace("__CID__", encodeURIComponent(cid))
        .replace("__AID__", encodeURIComponent(aid))
        .replace("__PID__", encodeURIComponent(pid));
    }

    function skeleton() {
      return `
        <div style="padding:18px;">
          <div style="display:flex;gap:12px;align-items:center;justify-content:space-between;flex-wrap:wrap;">
            <div style="min-width:0;">
              <div style="height:14px;width:220px;background:#e2e8f0;border-radius:999px;"></div>
              <div style="height:10px;width:360px;background:#eef2f7;border-radius:999px;margin-top:10px;"></div>
            </div>
            <div style="height:38px;width:140px;background:#eef2f7;border-radius:12px;"></div>
          </div>

          <div style="margin-top:14px;border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;background:#fff;">
            <div style="padding:14px;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
              <div style="height:10px;width:260px;background:#e2e8f0;border-radius:999px;"></div>
            </div>

            <div style="padding:14px;display:grid;gap:10px;">
              ${Array.from({ length: 7 }).map(() => `
                <div style="display:flex;gap:12px;align-items:center;">
                  <div style="height:12px;width:90px;background:#eef2f7;border-radius:999px;"></div>
                  <div style="height:12px;flex:1;background:#eef2f7;border-radius:999px;"></div>
                  <div style="height:12px;width:120px;background:#eef2f7;border-radius:999px;"></div>
                </div>
              `).join("")}
            </div>
          </div>
        </div>
      `;
    }

    function renderError(msg) {
      mainEl.innerHTML = `
        <div style="padding:18px;">
          <div style="border:1px solid rgba(220,38,38,.25);background:rgba(220,38,38,.06);border-radius:16px;padding:14px;">
            <div style="font-weight:900;color:#991b1b;">Fehler</div>
            <div style="margin-top:6px;color:#7f1d1d;font-weight:700;">${String(msg || "Request failed")}</div>
          </div>
        </div>
      `;
    }

    async function loadInvoice(cid, aid, pid) {
      // cancel previous request
      if (activeAbort) activeAbort.abort();
      activeAbort = new AbortController();

      mainEl.innerHTML = skeleton();

      try {
        const url = buildUrl(cid, aid, pid);

        const res = await fetch(url, {
          method: "GET",
          headers: {
            "Accept": "application/json",
            "X-Requested-With": "XMLHttpRequest",
            "X-CSRF-TOKEN": csrf,
          },
          signal: activeAbort.signal,
        });

        const ct = (res.headers.get("content-type") || "").toLowerCase();
        const j = ct.includes("application/json") ? await res.json() : null;

        if (!res.ok || !j || j.ok !== true) {
          throw new Error(j?.message || `Request failed (${res.status})`);
        }

        // Smooth swap
        mainEl.style.opacity = "0.25";
        mainEl.innerHTML = j.html || "";
        requestAnimationFrame(() => {
          mainEl.style.transition = "opacity .12s ease";
          mainEl.style.opacity = "1";
        });
      } catch (e) {
        if (String(e?.name) === "AbortError") return;
        renderError(e?.message || "Load failed");
      }
    }

    // Expose globally for your inline onclick
    window.loadInvoice = loadInvoice;
  })();
</script>


<!-- Customer Modal product exchange script  -->
 <script>
(() => {
  "use strict";

  // ============================================================
  // 1) CONFIG
  // ============================================================
  const CONFIG = window.__OBJ_MODAL || {};
  const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  const DOM = {
    root: document.getElementById('objDrawerRoot'),
    backdrop: document.getElementById('objDrawerBackdrop'),
    panel: document.getElementById('objDrawerPanel'),
    customerLine: document.getElementById('drawerCustomerLine'),
    loading: document.getElementById('drawerLoading'),
    grid: document.getElementById('drawerObjectsGrid'),
    cols: document.getElementById('drawerObjectsCols'),
    createPanel: document.getElementById('drawerCreatePanel'),
    createMsg: document.getElementById('createObjectMsg'),
    inputs: {
      name: document.getElementById('co_object_name'),
      date: document.getElementById('co_request_date'),
      address: document.getElementById('co_address_search'),
      fullAddress: document.getElementById('co_full_address'),
      street: document.getElementById('co_street'),
      postcode: document.getElementById('co_postcode'),
      city: document.getElementById('co_city'),
      lat: document.getElementById('co_lat'),
      lon: document.getElementById('co_lon'),
      objective: document.getElementById('co_objective'),
      note: document.getElementById('co_note'),
      map: document.getElementById('co_map'),
    }
  };

  const STATE = {
    customerId: null,
    objectsData: [],
    sortableInstances: [],
    map: null,
    marker: null,
    geocoder: null,
    autocomplete: null,
  };

  // ============================================================
  // 2) SVG ICONS (inline)
  // ============================================================
  const Icons = {
    trash: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M9 3h6l1 2h4v2H4V5h4l1-2z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M7 9v10a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M10 12v6M14 12v6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>`,
    x: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M18 6 6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>`,
    arrowRight: `<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M5 12h12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      <path d="m13 6 6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>`,
    warning: `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
      <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"
        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
      <path d="M12 9v4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
      <path d="M12 17h.01" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
    </svg>`,
  };

  // ============================================================
  // 3) HELPERS
  // ============================================================
  const Utils = {
    esc: (s) => String(s ?? '').replace(/[&<>"']/g, m => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[m])),
    url: (tpl, id) => String(tpl).replace('___ID___', String(id)),
    http: async (url, method = 'GET', data = null) => {
      const options = {
        method,
        headers: {
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          ...(CSRF_TOKEN ? { 'X-CSRF-TOKEN': CSRF_TOKEN } : {}),
          ...(data ? { 'Content-Type': 'application/json' } : {})
        },
        credentials: 'same-origin',
        body: data ? JSON.stringify(data) : null
      };

      const res = await fetch(url, options);
      const json = await res.json().catch(() => ({}));
      if (!res.ok) throw new Error(json?.message || `Request failed: ${res.status}`);
      return json;
    },
    ensureSwal: () => {
      if (!window.Swal) {
        throw new Error('SweetAlert2 (Swal) not loaded on this page.');
      }
    }
  };

  // ============================================================
  // 4) RENDERER (Bootstrap HTML + SVG buttons)
  // ============================================================
  const Renderer = {
    productCard: (p) => {
      const phaseKey = p.phase_section_key || '';
      const phaseLabel = CONFIG.phaseLabels?.[phaseKey] || (phaseKey || '—');
      const name = p.product_name || `Produkt #${p.product_id}`;
      const price = p.price ? Number(p.price).toFixed(2) + ' €' : '';

      return `
        <div class="prod-card shadow-sm" data-product-item="1" data-lead-product-id="${p.id}">
          <div class="d-flex justify-content-between align-items-start">
            <div style="overflow:hidden;">
              <div class="font-weight-bold text-dark text-truncate" style="max-width: 180px;" title="${Utils.esc(name)}">
                ${Utils.esc(name)}
              </div>
              <div class="small text-muted mt-1">
                <span class="badge badge-light border">${Utils.esc(phaseLabel)}</span>
                <span class="badge badge-light border">${Utils.esc(p.status || 'open')}</span>
              </div>
            </div>

            <div class="d-flex align-items-start" style="gap:8px;">
              <div class="small text-dark font-weight-bold" style="white-space: nowrap;">
                ${Utils.esc(price)}
              </div>

              <button type="button"
                class="btn btn-sm btn-outline-danger py-0 px-2 d-inline-flex align-items-center justify-content-center"
                data-action="delete-product"
                data-product-id="${p.id}"
                title="Produkt löschen"
                aria-label="Produkt löschen">
                ${Icons.x}
              </button>
            </div>
          </div>
        </div>
      `;
    },

    objectCard: (obj) => {
      const name = obj.object_name || `Objekt #${obj.id}`;
      const address = obj.full_address
        ? `<div class="small text-muted text-truncate" title="${Utils.esc(obj.full_address)}">${Utils.esc(obj.full_address)}</div>`
        : '';
      const date = obj.request_date ? Utils.esc(obj.request_date) : '';
      const mainBadge = obj.main ? `<span class="badge badge-warning ml-2">HAUPT</span>` : '';

      const productsHtml = (obj.products && obj.products.length > 0)
        ? obj.products.map(Renderer.productCard).join('')
        : `<div class="small text-muted font-italic text-center p-3 border rounded" style="border-style: dashed !important;">Keine Produkte</div>`;

      return `
        <div class="col-12 col-md-6 col-lg-4 mb-4">
          <div class="obj-card h-100 d-flex flex-column" data-object-card="${obj.id}">
            <div class="obj-card-header">
              <div class="d-flex justify-content-between align-items-start">
                <div style="overflow:hidden; padding-right:10px;">
                  <div class="font-weight-bold text-dark text-truncate" title="${Utils.esc(name)}">
                    ${Utils.esc(name)} ${mainBadge}
                  </div>
                  ${address}
                </div>

                <div class="d-flex flex-column align-items-end" style="gap:6px;">
                  <div class="small text-muted text-nowrap">${date}</div>
                  <button type="button"
                    class="btn btn-sm btn-outline-danger py-0 px-2 d-inline-flex align-items-center justify-content-center"
                    data-action="delete-object"
                    data-object-id="${obj.id}"
                    title="Objekt löschen"
                    aria-label="Objekt löschen">
                    ${Icons.trash}
                  </button>
                </div>
              </div>
            </div>

            <div class="obj-dropzone flex-grow-1" data-dropzone="${obj.id}">
              ${productsHtml}
            </div>
          </div>
        </div>
      `;
    }
  };

  // ============================================================
  // 5) DRAWER
  // ============================================================
  const Drawer = {
    open: async (customerId) => {
      STATE.customerId = customerId;
      DOM.root.classList.add('is-visible');
      await DataManager.loadTree(customerId);
    },
    close: () => {
      DOM.root.classList.remove('is-visible');
      Drawer.toggleCreatePanel(false);
      DOM.createMsg.textContent = '';
    },
    toggleCreatePanel: (show = null) => {
      const isHidden = DOM.createPanel.classList.contains('drawer-hidden');
      const shouldShow = show !== null ? show : isHidden;

      if (shouldShow) {
        DOM.createPanel.classList.remove('drawer-hidden');
        if (!DOM.inputs.date.value) DOM.inputs.date.value = new Date().toISOString().split('T')[0];
        MapController.init();
      } else {
        DOM.createPanel.classList.add('drawer-hidden');
        DOM.createMsg.textContent = '';
      }
    }
  };

  // ============================================================
  // 6) DATA MANAGER (SweetAlert2 delete flows)
  // ============================================================
  const DataManager = {
    loadTree: async (customerId) => {
      DOM.loading.classList.remove('drawer-hidden');
      DOM.grid.classList.add('drawer-hidden');
      DOM.cols.innerHTML = '';
      DragDrop.destroy();

      try {
        const result = await Utils.http(Utils.url(CONFIG.endpoints.tree, customerId));
        const data = result.data;

        STATE.objectsData = data.objects || [];

        const c = data.customer;
        DOM.customerLine.textContent = [c.customer_no, c.firma, c.name].filter(Boolean).join(' · ');

        DOM.cols.innerHTML = (data.objects || []).map(Renderer.objectCard).join('');

        DOM.loading.classList.add('drawer-hidden');
        DOM.grid.classList.remove('drawer-hidden');

        DragDrop.init();
      } catch (error) {
        DOM.cols.innerHTML = `<div class="alert alert-danger m-3">Fehler beim Laden: ${Utils.esc(error.message)}</div>`;
        DOM.loading.classList.add('drawer-hidden');
        DOM.grid.classList.remove('drawer-hidden');
      }
    },

    createObject: async () => {
      if (!STATE.customerId) return;

      DOM.createMsg.textContent = 'Speichern...';
      DOM.createMsg.className = 'ml-2 small text-muted';

      try {
        const payload = {
          object_name: DOM.inputs.name.value,
          request_date: DOM.inputs.date.value || null,
          full_address: DOM.inputs.fullAddress.value || null,
          street: DOM.inputs.street.value || null,
          postcode: DOM.inputs.postcode.value || null,
          city: DOM.inputs.city.value || null,
          lat: DOM.inputs.lat.value || null,
          lon: DOM.inputs.lon.value || null,
          objective: DOM.inputs.objective.value || null,
          note: DOM.inputs.note.value || null,
        };

        const result = await Utils.http(Utils.url(CONFIG.endpoints.createObject, STATE.customerId), 'POST', payload);

        DOM.cols.insertAdjacentHTML('beforeend', Renderer.objectCard(result.object));
        DragDrop.init();

        DOM.createMsg.textContent = 'Gespeichert ✅';
        DOM.createMsg.className = 'ml-2 small text-success';
        setTimeout(() => Drawer.toggleCreatePanel(false), 800);

      } catch (error) {
        DOM.createMsg.textContent = error.message || 'Fehler.';
        DOM.createMsg.className = 'ml-2 small text-danger';
      }
    },

    moveProduct: async (leadProductId, toObjectId, itemElement, senderList, oldIndex) => {
      try {
        await Utils.http(Utils.url(CONFIG.endpoints.moveProduct, leadProductId), 'POST', { to_alternative_id: toObjectId });
      } catch (error) {
        if (window.Swal) {
          Swal.fire({ icon: 'error', title: 'Fehler', text: error.message || 'Fehler beim Verschieben.' });
        } else {
          alert(error.message || 'Fehler beim Verschieben.');
        }
        senderList.insertBefore(itemElement, senderList.children[oldIndex]);
      }
    },

    deleteProduct: async (leadProductId) => {
      if (!leadProductId) return;
      Utils.ensureSwal();

      const r = await Swal.fire({
        icon: 'warning',
        title: 'Produkt löschen?',
        html: `<div class="text-left">Dieses Produkt wird entfernt.</div>`,
        showCancelButton: true,
        confirmButtonText: 'Löschen',
        cancelButtonText: 'Abbrechen',
        reverseButtons: true,
      });

      if (!r.isConfirmed) return;

      try {
        Swal.fire({ title: 'Lösche…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        await Utils.http(Utils.url(CONFIG.endpoints.deleteProduct, leadProductId), 'POST');
        await DataManager.loadTree(STATE.customerId);
        Swal.fire({ icon: 'success', title: 'Gelöscht', timer: 900, showConfirmButton: false });
      } catch (e) {
        Swal.fire({ icon: 'error', title: 'Fehler', text: e.message || 'Fehler beim Löschen.' });
      }
    },

    deleteObject: async (objectId) => {
      if (!objectId) return;
      Utils.ensureSwal();

      const objects = STATE.objectsData || [];
      const obj = objects.find(o => Number(o.id) === Number(objectId));
      const prodCount = obj?.products?.length || 0;

      // Prevent removing last object when user wants move (we handle it)
      const otherObjects = objects.filter(o => Number(o.id) !== Number(objectId));
      const optionsHtml = otherObjects.map(o => {
        const label = Utils.esc(o.object_name || `Objekt #${o.id}`);
        return `<option value="${o.id}">${label}</option>`;
      }).join('');

      // Build dynamic dialog
      const hasOthers = otherObjects.length > 0;

      const html = prodCount > 0
        ? `
          <div class="text-left">
            <div class="d-flex align-items-center mb-2" style="gap:8px;">
              <span style="color:#d9534f">${Icons.warning}</span>
              <div><b>${prodCount}</b> Produkte sind in diesem Objekt.</div>
            </div>

            <div class="mt-3">
              <div class="custom-control custom-radio">
                <input type="radio" id="delModeMove" name="delMode" class="custom-control-input" ${hasOthers ? 'checked' : ''} ${hasOthers ? '' : 'disabled'}>
                <label class="custom-control-label" for="delModeMove">
                  Produkte verschieben ${hasOthers ? '' : '(kein anderes Objekt vorhanden)'}
                </label>
              </div>

              <div class="mt-2 ml-4">
                <select id="moveTarget" class="form-control form-control-sm" ${hasOthers ? '' : 'disabled'}>
                  ${optionsHtml}
                </select>
              </div>

              <div class="custom-control custom-radio mt-3">
                <input type="radio" id="delModeDelete" name="delMode" class="custom-control-input" ${hasOthers ? '' : 'checked'}>
                <label class="custom-control-label" for="delModeDelete">
                  Produkte löschen (mit Objekt)
                </label>
              </div>
            </div>
          </div>
        `
        : `
          <div class="text-left">Objekt wirklich löschen?</div>
        `;

      const r = await Swal.fire({
        icon: 'warning',
        title: 'Objekt löschen?',
        html,
        showCancelButton: true,
        confirmButtonText: `Löschen`,
        cancelButtonText: 'Abbrechen',
        reverseButtons: true,
        preConfirm: () => {
          if (prodCount > 0) {
            const modeMove = document.getElementById('delModeMove');
            const modeDelete = document.getElementById('delModeDelete');
            const moveTarget = document.getElementById('moveTarget');

            const wantsMove = modeMove?.checked && !modeMove?.disabled;
            const wantsDelete = modeDelete?.checked;

            if (wantsMove) {
              const toId = moveTarget?.value;
              if (!toId) {
                Swal.showValidationMessage('Bitte Ziel-Objekt auswählen.');
                return false;
              }
              return { delete_products: false, move_to_alternative_id: Number(toId) };
            }

            if (wantsDelete) {
              return { delete_products: true, move_to_alternative_id: null };
            }

            Swal.showValidationMessage('Bitte eine Option wählen.');
            return false;
          }

          return { delete_products: true, move_to_alternative_id: null };
        }
      });

      if (!r.isConfirmed) return;

      try {
        Swal.fire({ title: 'Lösche…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        await Utils.http(Utils.url(CONFIG.endpoints.deleteObject, objectId), 'POST', r.value);
        await DataManager.loadTree(STATE.customerId);
        Swal.fire({ icon: 'success', title: 'Objekt gelöscht', timer: 900, showConfirmButton: false });
      } catch (e) {
        Swal.fire({ icon: 'error', title: 'Fehler', text: e.message || 'Fehler beim Löschen.' });
      }
    }
  };

  // ============================================================
  // 7) DRAG & DROP
  // ============================================================
  const DragDrop = {
    init: () => {
      const zones = DOM.root.querySelectorAll('[data-dropzone]');
      zones.forEach(zone => {
        const sortable = new Sortable(zone, {
          group: 'productsMove',
          animation: 150,
          draggable: '[data-product-item]',
          ghostClass: 'bg-light',
          onEnd: (evt) => {
            const item = evt.item;
            const leadProductId = parseInt(item.getAttribute('data-lead-product-id'), 10);
            const toObjectId = parseInt(evt.to.getAttribute('data-dropzone'), 10);
            const fromObjectId = parseInt(evt.from.getAttribute('data-dropzone'), 10);
            if (leadProductId && toObjectId && fromObjectId !== toObjectId) {
              DataManager.moveProduct(leadProductId, toObjectId, item, evt.from, evt.oldIndex);
            }
          }
        });
        STATE.sortableInstances.push(sortable);
      });
    },
    destroy: () => {
      STATE.sortableInstances.forEach(s => { try { s.destroy(); } catch(e){} });
      STATE.sortableInstances = [];
    }
  };

  // ============================================================
  // 8) MAP CONTROLLER
  // ============================================================
  const MapController = {
    init: () => {
      if (STATE.map || !window.google?.maps) return;

      STATE.geocoder = new google.maps.Geocoder();
      const defaultLoc = { lat: 50.1109, lng: 8.6821 };

      STATE.map = new google.maps.Map(DOM.inputs.map, {
        center: defaultLoc, zoom: 12, streetViewControl: false, mapTypeControl: false
      });

      STATE.marker = new google.maps.Marker({ map: STATE.map, position: defaultLoc, draggable: true });
      STATE.map.addListener('click', (e) => MapController.handleLocationUpdate(e.latLng));
      STATE.marker.addListener('dragend', (e) => MapController.handleLocationUpdate(e.latLng));
      MapController.initAutocomplete();
    },

    initAutocomplete: () => {
      STATE.autocomplete = new google.maps.places.Autocomplete(DOM.inputs.address, {
        fields: ['formatted_address', 'geometry', 'address_components']
      });

      STATE.autocomplete.addListener('place_changed', () => {
        const place = STATE.autocomplete.getPlace();
        if (!place.geometry) return;

        STATE.map.setCenter(place.geometry.location);
        STATE.map.setZoom(15);
        STATE.marker.setPosition(place.geometry.location);

        MapController.handleLocationUpdate(place.geometry.location, false);
        DOM.inputs.fullAddress.value = place.formatted_address || '';
        MapController.fillAddressComponents(place.address_components || []);
      });
    },

    handleLocationUpdate: (latLng, doReverse = true) => {
      DOM.inputs.lat.value = String(latLng.lat());
      DOM.inputs.lon.value = String(latLng.lng());
      STATE.marker.setPosition(latLng);
      if (doReverse) MapController.reverseGeocode(latLng);
    },

    reverseGeocode: (latLng) => {
      if (!STATE.geocoder) return;
      STATE.geocoder.geocode({ location: latLng }, (res, status) => {
        if (status === 'OK' && res[0]) {
          DOM.inputs.fullAddress.value = res[0].formatted_address;
          MapController.fillAddressComponents(res[0].address_components || []);
        }
      });
    },

    fillAddressComponents: (components) => {
      const get = (t) => components.find(c => c.types?.includes(t))?.long_name || '';
      DOM.inputs.street.value = [get('route'), get('street_number')].filter(Boolean).join(' ');
      DOM.inputs.postcode.value = get('postal_code');
      DOM.inputs.city.value = get('locality') || get('postal_town');
    }
  };

  // ============================================================
  // 9) EVENTS
  // ============================================================
  document.getElementById('btnDrawerClose')?.addEventListener('click', Drawer.close);
  DOM.backdrop?.addEventListener('click', Drawer.close);
  document.getElementById('btnDrawerOpenCreate')?.addEventListener('click', () => Drawer.toggleCreatePanel());
  document.getElementById('btnCreateObjectCancel')?.addEventListener('click', () => Drawer.toggleCreatePanel(false));
  document.getElementById('btnCreateObjectSave')?.addEventListener('click', DataManager.createObject);

  // Open drawer
  document.addEventListener('click', (e) => {
    const btn = e.target.closest('.openObjectProductDrawer');
    if (!btn) return;
    e.preventDefault();
    const id = parseInt(btn.getAttribute('data-id') || '0', 10);
    if (id) Drawer.open(id);
  });

  // Delete actions (delegated) — inside IIFE so DataManager is in scope ✅
  document.addEventListener('click', (e) => {
    const delObj = e.target.closest('[data-action="delete-object"]');
    if (delObj) {
      e.preventDefault();
      const objectId = parseInt(delObj.getAttribute('data-object-id') || '0', 10);
      if (objectId) DataManager.deleteObject(objectId);
      return;
    }

    const delProd = e.target.closest('[data-action="delete-product"]');
    if (delProd) {
      e.preventDefault();
      const pid = parseInt(delProd.getAttribute('data-product-id') || '0', 10);
      if (pid) DataManager.deleteProduct(pid);
      return;
    }
  });

})();
</script>

 
<script>
  /**
 * Loads the Angebote Blade partial via AJAX and injects it into #mainContent
 */
function loadAngebotPartial(customer_id, alternative_id, product_id) {
    const mainContent = document.getElementById('mainContent');
    
    // 1. Show a loading spinner
    mainContent.innerHTML = `
        <div class="d-flex justify-content-center align-items-center" style="height: 300px;">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-2 text-muted" style="font-weight: 500;">Lade Angebote...</div>
            </div>
        </div>`;

    // 2. Build the URL (Hardcoding 'angebote' as the section parameter for the route)
    const url = `/customer/partial/${customer_id}/${alternative_id}/${product_id}/angebote`;

    // 3. Fetch the HTML
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Netzwerkfehler beim Laden der Angebote');
        return response.text();
    })
    .then(html => {
        // 4. Inject the returned HTML
        mainContent.innerHTML = html;

        // 5. Re-initialize UI components
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        if (window.jQuery && $.fn.tooltip) {
            $('[data-toggle="tooltip"]').tooltip();
        }
    })
    .catch(error => {
        console.error(error);
        mainContent.innerHTML = `
            <div class="alert alert-danger m-3 d-flex align-items-center">
                <i class="feather icon-alert-circle mr-2" style="font-size: 20px;"></i>
                Fehler: ${error.message}
            </div>`;
    });
}
</script>
@endsection


 