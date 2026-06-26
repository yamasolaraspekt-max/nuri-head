@extends('admin.layouts.app')
@section('title') PROZESS @stop

@section('style')
              <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css')}}">
              <link rel="stylesheet" href="{{ asset('css/dropzone.min.css')}}" />
              <meta name="csrf-token" content="{{ csrf_token() }}">

              {{-- ===== Early global cardId helper: must exist before kanban.js and inline Blade scripts ===== --}}


              <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">


              <link rel="stylesheet" href="{{ asset('css/kanban.css') }}?v={{ time() }}">
              <link rel="stylesheet" href="{{ asset('css/kanban-saved-filters.css') }}?v={{ time() }}">


              {{-- ===== Kanban UX Patch: collapsible next step + custom modal ===== --}}
              <style>
                .kb-next-step-preview.kb-next-step-collapsible {
                  position: relative;
                  overflow: hidden;
                  border: 1px solid #dbeafe;
                  background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
                  border-radius: 14px;
                  transition: box-shadow .18s ease, border-color .18s ease, transform .18s ease;
                  cursor: pointer;
                }

                .kb-next-step-preview.kb-next-step-collapsible:hover {
                  border-color: #74b2d4;
                  box-shadow: 0 10px 26px rgba(15, 23, 42, .10);
                  transform: translateY(-1px);
                }

                .kb-next-step-preview.kb-next-step-collapsible::after {
                  content: "Klicken zum Fixieren · 3 Sekunden halten zum Öffnen";
                  position: absolute;
                  left: 10px;
                  right: 10px;
                  top: 100%;
                  transform: translateY(8px);
                  z-index: 20;
                  padding: 7px 9px;
                  border-radius: 10px;
                  background: rgba(15, 23, 42, .94);
                  color: #fff;
                  font-size: 11px;
                  font-weight: 800;
                  line-height: 1.25;
                  opacity: 0;
                  pointer-events: none;
                  transition: opacity .16s ease, transform .16s ease;
                  text-align: center;
                  box-shadow: 0 12px 24px rgba(15, 23, 42, .22);
                }

                .kb-next-step-preview.kb-next-step-collapsible:hover::after {
                  opacity: 1;
                  transform: translateY(4px);
                }

                .kb-next-step-preview-head {
                  user-select: none;
                }

                .kb-next-step-preview.kb-next-step-collapsible .kb-next-step-preview-head {
                  position: relative;
                  padding-right: 28px;
                }

                .kb-next-step-preview.kb-next-step-collapsible .kb-next-step-preview-head::after {
                  content: "⌄";
                  position: absolute;
                  right: 4px;
                  top: 50%;
                  transform: translateY(-50%) rotate(0deg);
                  width: 22px;
                  height: 22px;
                  display: inline-flex;
                  align-items: center;
                  justify-content: center;
                  border-radius: 999px;
                  background: #eef7fb;
                  color: #334155;
                  font-size: 14px;
                  font-weight: 900;
                  transition: transform .18s ease, background .18s ease, color .18s ease;
                }

                .kb-next-step-preview.kb-next-step-collapsible.is-open .kb-next-step-preview-head::after,
                .kb-next-step-preview.kb-next-step-collapsible.is-pinned-open .kb-next-step-preview-head::after {
                  transform: translateY(-50%) rotate(180deg);
                  background: #93c21c;
                  color: #fff;
                }

                .kb-next-step-preview.kb-next-step-collapsible .kb-next-step-preview-line {
                  max-height: 0;
                  opacity: 0;
                  margin-top: 0;
                  transform: translateY(-4px);
                  overflow: hidden;
                  transition: max-height .22s ease, opacity .18s ease, margin-top .18s ease, transform .18s ease;
                }

                .kb-next-step-preview.kb-next-step-collapsible.is-open .kb-next-step-preview-line,
                .kb-next-step-preview.kb-next-step-collapsible.is-pinned-open .kb-next-step-preview-line {
                  max-height: 80px;
                  opacity: 1;
                  margin-top: 6px;
                  transform: translateY(0);
                }

                .kb-next-step-preview.kb-next-step-collapsible.is-hover-waiting {
                  box-shadow: 0 0 0 2px rgba(147, 194, 28, .18), 0 10px 26px rgba(15, 23, 42, .10);
                }

                .kb-next-step-preview.kb-next-step-collapsible.is-hover-waiting .kb-next-step-preview-head::before {
                  content: "";
                  position: absolute;
                  left: 0;
                  bottom: -5px;
                  width: 100%;
                  height: 3px;
                  border-radius: 999px;
                  background: linear-gradient(90deg, #93c21c 0%, #74b2d4 100%);
                  transform-origin: left center;
                  animation: kbNextStepHoverProgress 3s linear forwards;
                }

                @keyframes kbNextStepHoverProgress {
                  from { transform: scaleX(0); }
                  to { transform: scaleX(1); }
                }

                .kb-next-step-preview.kb-next-step-collapsible.is-pinned-open {
                  border-color: #93c21c;
                  box-shadow: 0 0 0 2px rgba(147, 194, 28, .18), 0 10px 26px rgba(15, 23, 42, .10);
                }

                .kb-next-step-preview.kb-next-step-collapsible.is-pinned-open::after {
                  content: "Fixiert geöffnet · Nochmals klicken zum Schließen";
                }

                .kb-next-step-preview-btn {
                  position: relative;
                  z-index: 3;
                }

                .kb-modal-backdrop {
                  position: fixed;
                  inset: 0;
                  z-index: 3000;
                  display: none;
                  align-items: center;
                  justify-content: center;
                  padding: 18px;
                  background: rgba(15, 23, 42, .46);
                  backdrop-filter: blur(5px);
                }

                .kb-modal-backdrop.is-open {
                  display: flex;
                }

                .kb-modal {
                  width: min(560px, 96vw);
                  max-height: min(86vh, 760px);
                  display: flex;
                  flex-direction: column;
                  overflow: hidden;
                  border-radius: 22px;
                  background: #fff;
                  box-shadow: 0 28px 80px rgba(15, 23, 42, .28);
                  transform: translateY(8px) scale(.98);
                  opacity: 0;
                  animation: kbModalIn .18s ease forwards;
                }

                .kb-modal--wide { width: min(860px, 96vw); }

                @keyframes kbModalIn {
                  to { transform: translateY(0) scale(1); opacity: 1; }
                }

                .kb-modal-head {
                  display: flex;
                  align-items: flex-start;
                  gap: 12px;
                  padding: 18px 18px 12px;
                  border-bottom: 1px solid #eef2f7;
                  background: linear-gradient(135deg, #f8fafc 0%, #eef7fb 100%);
                }

                .kb-modal-icon {
                  width: 42px;
                  height: 42px;
                  border-radius: 15px;
                  display: inline-flex;
                  align-items: center;
                  justify-content: center;
                  flex: 0 0 auto;
                  background: #e0f2fe;
                  color: #0369a1;
                  font-weight: 900;
                  font-size: 20px;
                }

                .kb-modal-icon.is-success { background: #ecfdf5; color: #15803d; }
                .kb-modal-icon.is-error { background: #fef2f2; color: #b91c1c; }
                .kb-modal-icon.is-warning { background: #fffbeb; color: #b45309; }
                .kb-modal-icon.is-question { background: #eef2ff; color: #4338ca; }

                .kb-modal-title {
                  margin: 0;
                  color: #0f172a;
                  font-size: 17px;
                  font-weight: 950;
                  line-height: 1.25;
                }

                .kb-modal-subtitle {
                  margin-top: 4px;
                  color: #64748b;
                  font-size: 12px;
                  font-weight: 700;
                }

                .kb-modal-close {
                  margin-left: auto;
                  width: 32px;
                  height: 32px;
                  border: 0;
                  border-radius: 12px;
                  background: rgba(255, 255, 255, .75);
                  color: #334155;
                  cursor: pointer;
                  font-size: 20px;
                  line-height: 1;
                }

                .kb-modal-body {
                  padding: 16px 18px;
                  overflow: auto;
                  color: #334155;
                  font-size: 14px;
                  line-height: 1.55;
                }

                .kb-modal-body textarea,
                .kb-modal-body input,
                .kb-modal-body select {
                  width: 100%;
                  border: 1px solid #dbeafe;
                  border-radius: 14px;
                  padding: 10px 12px;
                  outline: 0;
                  color: #0f172a;
                  background: #fff;
                }

                .kb-modal-body textarea:focus,
                .kb-modal-body input:focus,
                .kb-modal-body select:focus {
                  border-color: #74b2d4;
                  box-shadow: 0 0 0 3px rgba(116, 178, 212, .18);
                }

                .kb-modal-error {
                  display: none;
                  margin-top: 8px;
                  color: #b91c1c;
                  font-size: 12px;
                  font-weight: 800;
                }

                .kb-modal-error.is-visible { display: block; }

                .kb-modal-actions {
                  display: flex;
                  align-items: center;
                  justify-content: flex-end;
                  gap: 8px;
                  padding: 12px 18px 18px;
                  border-top: 1px solid #eef2f7;
                  background: #fff;
                }

                .kb-modal-btn {
                  border: 1px solid #dbeafe;
                  border-radius: 13px;
                  min-height: 38px;
                  padding: 0 14px;
                  font-size: 13px;
                  font-weight: 900;
                  cursor: pointer;
                  background: #fff;
                  color: #334155;
                }

                .kb-modal-btn:hover { background: #f8fafc; }

                .kb-modal-btn--primary {
                  background: #93c21c;
                  border-color: #93c21c;
                  color: #fff;
                  box-shadow: 0 8px 18px rgba(147, 194, 28, .25);
                }

                .kb-modal-btn--primary:hover { background: #82ad17; }

                .kb-modal-toast-wrap {
                  position: fixed;
                  right: 16px;
                  bottom: 16px;
                  z-index: 3100;
                  display: flex;
                  flex-direction: column;
                  gap: 8px;
                  pointer-events: none;
                }

                .kb-modal-toast {
                  min-width: 260px;
                  max-width: 380px;
                  border-radius: 16px;
                  background: #0f172a;
                  color: #fff;
                  padding: 11px 13px;
                  box-shadow: 0 18px 44px rgba(15, 23, 42, .28);
                  font-size: 13px;
                  font-weight: 800;
                  animation: kbToastIn .18s ease both;
                }

                @keyframes kbToastIn {
                  from { transform: translateY(8px); opacity: 0; }
                  to { transform: translateY(0); opacity: 1; }
                }

                /* ===== Saved filter actions dropdown menu - visibility fix ===== */
                .app-content,
                .content-wrapper,
                #basic-tabs-components,
                .pro-layout,
                .pro-main,
                .row,
                .col-sm-12 {
                  overflow: visible !important;
                }

                .kb-filter-mode-bar {
                  position: relative !important;
                  z-index: 8 !important;
                  overflow: visible !important;
                  isolation: isolate;
                }

                .kb-filter-mode-actions {
                  position: relative !important;
                  z-index: 99991 !important;
                  overflow: visible !important;
                }

                .kb-filter-menu-wrap {
                  position: relative !important;
                  flex: 0 0 auto;
                  display: inline-flex;
                  align-items: center;
                  z-index: 99992 !important;
                  overflow: visible !important;
                }

                .kb-filter-menu-trigger {
                  display: inline-flex !important;
                  align-items: center;
                  gap: 7px;
                  min-width: 116px;
                  justify-content: center;
                  position: relative;
                  z-index: 99993 !important;
                }

                .kb-filter-menu-trigger:disabled {
                  opacity: .55;
                  cursor: not-allowed;
                }

                .kb-filter-menu-chevron {
                  transition: transform .16s ease;
                }

                .kb-filter-menu-wrap.is-open .kb-filter-menu-chevron {
                  transform: rotate(180deg);
                }

                .kb-filter-menu {
                  position: absolute !important;
                  top: calc(100% + 10px) !important;
                  right: 0 !important;
                  left: auto !important;
                  z-index: 999999 !important;
                  min-width: 300px;
                  display: none;
                  padding: 8px;
                  border: 1px solid #dbeafe;
                  border-radius: 18px;
                  background: #ffffff;
                  box-shadow: 0 28px 80px rgba(15, 23, 42, .26);
                  transform-origin: top right;
                  animation: kbFilterMenuIn .14s ease both;
                  pointer-events: auto;
                }

                .kb-filter-menu::before {
                  content: "";
                  position: absolute;
                  top: -7px;
                  right: 24px;
                  width: 14px;
                  height: 14px;
                  background: #ffffff;
                  border-left: 1px solid #dbeafe;
                  border-top: 1px solid #dbeafe;
                  transform: rotate(45deg);
                  z-index: -1;
                }

                .kb-filter-menu-wrap.is-open .kb-filter-menu {
                  display: block !important;
                }

                .pro-tabs-shell,
                .pro-tabs-content-card,
                .kanban-zoom-card,
                .kanban-zoom-area,
                .kanban-container,
                #kanban,
                .column,
                .card {
                  position: relative;
                }

                .pro-tabs-shell,
                .pro-tabs-content-card,
                .kanban-zoom-card,
                .kanban-zoom-area,
                .kanban-container,
                #kanban {
                  z-index: 1 !important;
                }

                .column,
                .card {
                  z-index: auto !important;
                }

                .kb-filter-menu-wrap.is-open {
                  z-index: 999998 !important;
                }

                @keyframes kbFilterMenuIn {
                  from { opacity: 0; transform: translateY(-4px) scale(.98); }
                  to { opacity: 1; transform: translateY(0) scale(1); }
                }

                .kb-filter-menu-item {
                  width: 100%;
                  position: relative;
                  z-index: 1;
                  border: 0;
                  background: transparent;
                  padding: 10px 11px;
                  border-radius: 13px;
                  text-align: left;
                  cursor: pointer;
                  color: #0f172a;
                  transition: background .14s ease, transform .14s ease;
                }

                .kb-filter-menu-item:hover:not(:disabled) {
                  background: #f1f5f9;
                  transform: translateX(1px);
                }

                .kb-filter-menu-item:disabled {
                  opacity: .45;
                  cursor: not-allowed;
                  transform: none;
                }

                .kb-filter-menu-item span {
                  display: flex;
                  align-items: center;
                  gap: 8px;
                  font-size: 13px;
                  font-weight: 950;
                }

                .kb-filter-menu-item small {
                  display: block;
                  margin-top: 2px;
                  padding-left: 24px;
                  font-size: 11px;
                  font-weight: 700;
                  color: #64748b;
                  line-height: 1.35;
                }

                .kb-filter-menu-item.is-danger span,
                .kb-filter-menu-item.is-danger i {
                  color: #dc2626;
                }

                .kb-filter-menu-item.is-danger:hover:not(:disabled) {
                  background: #fef2f2;
                }

                .kb-filter-menu-divider {
                  height: 1px;
                  margin: 6px 4px;
                  background: #e2e8f0;
                }



                /* ===== Hard fix: keep legacy duplicated action buttons out of the top bar ===== */
                .kb-filter-mode-actions > #kbUpdateCurrentFilter,
                .kb-filter-mode-actions > #kbRenameCurrentFilter,
                .kb-filter-mode-actions > #kbSetDefaultFilter,
                .kb-filter-mode-actions > #kbDeleteCurrentFilter {
                  display: none !important;
                }

                #kbSavedFilterMenu .kb-filter-menu-item {
                  display: block !important;
                  float: none !important;
                  position: relative !important;
                  margin: 0 !important;
                }

                #kbSavedFilterMenu {
                  flex-direction: column !important;
                }

                .kb-filter-menu-wrap.is-open #kbSavedFilterMenu {
                  display: flex !important;
                }

                @media (max-width: 768px) {
                  .kb-filter-menu {
                    right: auto !important;
                    left: 0 !important;
                    min-width: min(300px, 88vw);
                  }

                  .kb-filter-menu::before {
                    right: auto;
                    left: 24px;
                  }
                }


                /* Old Kanban appointment UI removed: appointments/reports load only in Termin Bericht */
                #ap-backdrop,
                #ap-drawer,
                #ap-calendar-wrap,
                #ap-fullcalendar,
                #ap-card-view,
                #ap-form,
                #ap-tab-calendar,
                #ap-tab-form,
                [data-ap-close],
                [data-ap-count],
                [data-menu="termin"],
                #kbFormCreateAppointment,
                #kbAppointmentOptions {
                  display: none !important;
                  visibility: hidden !important;
                  pointer-events: none !important;
                }

                /* =========================================================
               Aufgabe Sidebar - Modern List View + Details + Comments
               Put at bottom of public/css/kanban.css
               ========================================================= */

            :root {
              --pt-primary: #93c21c;
              --pt-primary-dark: #7daa17;
              --pt-blue: #74b2d4;
              --pt-dark: #0f172a;
              --pt-muted: #64748b;
              --pt-border: #dbeafe;
              --pt-soft: #f8fafc;
              --pt-soft-blue: #eef7fb;
              --pt-danger: #dc2626;
              --pt-warning: #f59e0b;
              --pt-success: #16a34a;
              --pt-radius: 18px;
              --pt-shadow: 0 18px 45px rgba(15, 23, 42, .12);
            }

            /* Main drawer upgrade */
            #pt-drawer.drawer,
            #ptDrawer.drawer,
            .pt-drawer {
              width: min(980px, 96vw) !important;
              max-width: 96vw !important;
              background: #f8fafc !important;
              border-left: 1px solid rgba(219, 234, 254, .9);
              box-shadow: -28px 0 80px rgba(15, 23, 42, .22) !important;
            }

            #pt-drawer .drawer-header,
            #ptDrawer .drawer-header,
            .pt-drawer .drawer-header {
              min-height: 74px;
              padding: 14px 18px !important;
              background: linear-gradient(135deg, #ffffff 0%, #eef7fb 100%);
              border-bottom: 1px solid #dbeafe !important;
            }

            #pt-drawer .drawer-header h5,
            #ptDrawer .drawer-header h5,
            .pt-drawer .drawer-header h5 {
              margin: 0;
              font-size: 18px;
              font-weight: 950;
              color: var(--pt-dark);
              display: flex;
              align-items: center;
              gap: 9px;
            }

            #pt-drawer .drawer-header h5::before,
            #ptDrawer .drawer-header h5::before,
            .pt-drawer .drawer-header h5::before {
              content: "✓";
              width: 38px;
              height: 38px;
              border-radius: 14px;
              background: linear-gradient(135deg, var(--pt-primary), #c7e86a);
              color: #fff;
              display: inline-flex;
              align-items: center;
              justify-content: center;
              box-shadow: 0 10px 22px rgba(147, 194, 28, .28);
            }

            #pt-drawer .drawer-body,
            #ptDrawer .drawer-body,
            .pt-drawer .drawer-body {
              padding: 0 !important;
              overflow: hidden !important;
              display: flex;
              flex-direction: column;
            }

            /* Context header */
            .pt-context-bar,
            #ptContextBar {
              display: flex;
              align-items: center;
              gap: 10px;
              padding: 12px 16px;
              background: #fff;
              border-bottom: 1px solid #e5eef8;
              overflow-x: auto;
            }

            .pt-context-chip {
              display: inline-flex;
              align-items: center;
              gap: 7px;
              min-height: 32px;
              padding: 0 11px;
              border-radius: 999px;
              background: #f1f5f9;
              border: 1px solid #e2e8f0;
              color: #334155;
              font-size: 12px;
              font-weight: 900;
              white-space: nowrap;
            }

            .pt-context-chip i,
            .pt-context-chip .feather {
              width: 14px;
              height: 14px;
              color: var(--pt-blue);
            }

            .pt-context-chip.is-product {
              background: #f7fbef;
              border-color: rgba(147, 194, 28, .35);
              color: #365314;
            }

            .pt-context-chip.is-customer {
              background: #eef7fb;
              border-color: rgba(116, 178, 212, .35);
            }

            /* Toolbar */
            .pt-list-toolbar,
            .pt-task-toolbar,
            #ptListToolbar {
              display: flex;
              align-items: center;
              justify-content: space-between;
              gap: 12px;
              padding: 13px 16px;
              background: #ffffff;
              border-bottom: 1px solid #e5eef8;
            }

            .pt-list-toolbar-title {
              display: flex;
              flex-direction: column;
              gap: 2px;
            }

            .pt-list-toolbar-title strong {
              font-size: 15px;
              font-weight: 950;
              color: var(--pt-dark);
            }

            .pt-list-toolbar-title small {
              color: var(--pt-muted);
              font-size: 11px;
              font-weight: 800;
            }

            .pt-list-toolbar-actions {
              display: flex;
              align-items: center;
              gap: 8px;
            }

            #ptk-search,
            .pt-task-search {
              width: min(320px, 42vw);
              min-height: 38px;
              border: 1px solid #dbeafe;
              border-radius: 14px;
              background: #f8fafc;
              color: var(--pt-dark);
              padding: 0 13px;
              font-size: 13px;
              font-weight: 800;
              outline: 0;
            }

            #ptk-search:focus,
            .pt-task-search:focus {
              border-color: var(--pt-blue);
              background: #fff;
              box-shadow: 0 0 0 4px rgba(116, 178, 212, .16);
            }

            .pt-refresh-btn,
            .pt-add-btn,
            .pt-action-btn {
              border: 1px solid #dbeafe;
              background: #fff;
              color: #334155;
              min-height: 38px;
              padding: 0 12px;
              border-radius: 13px;
              display: inline-flex;
              align-items: center;
              gap: 7px;
              cursor: pointer;
              font-size: 12px;
              font-weight: 950;
              transition: .15s ease;
            }

            .pt-refresh-btn:hover,
            .pt-add-btn:hover,
            .pt-action-btn:hover {
              transform: translateY(-1px);
              background: #eef7fb;
              border-color: var(--pt-blue);
            }

            .pt-add-btn,
            .pt-action-btn.is-primary {
              background: var(--pt-primary);
              border-color: var(--pt-primary);
              color: #fff;
              box-shadow: 0 10px 18px rgba(147, 194, 28, .22);
            }

            .pt-add-btn:hover,
            .pt-action-btn.is-primary:hover {
              background: var(--pt-primary-dark);
              color: #fff;
            }

            /* Main list/detail layout */
            .pt-list-panel {
              height: calc(100vh - 150px);
              display: grid;
              grid-template-columns: 380px minmax(0, 1fr);
              gap: 0;
              background: #f8fafc;
              overflow: hidden;
            }

            .pt-list-left,
            .pt-task-list-pane {
              min-width: 0;
              border-right: 1px solid #dbeafe;
              background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
              display: flex;
              flex-direction: column;
              overflow: hidden;
            }

            .pt-list-right,
            .pt-task-detail-pane {
              min-width: 0;
              background: #f8fafc;
              display: flex;
              flex-direction: column;
              overflow: hidden;
            }

            /* Filters */
            .pt-list-filters {
              display: flex;
              align-items: center;
              gap: 7px;
              padding: 10px 12px;
              background: #fff;
              border-bottom: 1px solid #edf2f7;
              overflow-x: auto;
            }

            .pt-list-pill,
            .pt-filter-pill {
              border: 1px solid #e2e8f0;
              background: #fff;
              color: #475569;
              height: 30px;
              padding: 0 10px;
              border-radius: 999px;
              font-size: 11px;
              font-weight: 950;
              white-space: nowrap;
              cursor: pointer;
              transition: .15s ease;
            }

            .pt-list-pill:hover,
            .pt-filter-pill:hover {
              background: #f1f5f9;
              color: var(--pt-dark);
            }

            .pt-list-pill.is-active,
            .pt-filter-pill.is-active {
              background: #ecfccb;
              border-color: rgba(147, 194, 28, .6);
              color: #365314;
            }

            /* Task list */
            .pt-task-list,
            #ptTaskList {
              padding: 12px;
              overflow-y: auto;
              display: flex;
              flex-direction: column;
              gap: 10px;
            }

            .pt-task-card,
            .pt-list-item {
              border: 1px solid #e2e8f0;
              border-left: 5px solid var(--pt-blue);
              background: #fff;
              border-radius: 18px;
              padding: 12px;
              box-shadow: 0 8px 20px rgba(15, 23, 42, .06);
              cursor: pointer;
              transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
              position: relative;
              overflow: hidden;
            }

            .pt-task-card:hover,
            .pt-list-item:hover {
              transform: translateY(-2px);
              box-shadow: 0 14px 32px rgba(15, 23, 42, .10);
              border-color: #bfdbfe;
            }

            .pt-task-card.is-active,
            .pt-list-item.is-active {
              border-color: var(--pt-primary);
              border-left-color: var(--pt-primary);
              background: linear-gradient(135deg, #ffffff 0%, #f7fbef 100%);
              box-shadow: 0 0 0 3px rgba(147, 194, 28, .16), 0 14px 32px rgba(15, 23, 42, .10);
            }

            .pt-task-card-head,
            .pt-list-item-head {
              display: flex;
              align-items: flex-start;
              justify-content: space-between;
              gap: 10px;
            }

            .pt-task-title,
            .pt-list-item-title {
              font-size: 14px;
              line-height: 1.25;
              font-weight: 950;
              color: var(--pt-dark);
              margin: 0;
            }

            .pt-task-subtitle,
            .pt-list-item-sub {
              margin-top: 4px;
              font-size: 12px;
              color: var(--pt-muted);
              line-height: 1.35;
              font-weight: 700;
            }

            .pt-task-description {
              margin-top: 8px;
              color: #475569;
              font-size: 12px;
              line-height: 1.45;
              max-height: 54px;
              overflow: hidden;
            }

            /* Status / priority badges */
            .pt-badge-row,
            .pt-task-meta {
              display: flex;
              flex-wrap: wrap;
              align-items: center;
              gap: 6px;
              margin-top: 9px;
            }

            .pt-badge,
            .pt-status-badge,
            .pt-priority-badge {
              display: inline-flex;
              align-items: center;
              gap: 5px;
              min-height: 24px;
              padding: 0 8px;
              border-radius: 999px;
              font-size: 10px;
              font-weight: 950;
              text-transform: uppercase;
              letter-spacing: .03em;
              background: #f1f5f9;
              color: #334155;
              border: 1px solid #e2e8f0;
            }

            .pt-status-open,
            .pt-status-start,
            .pt-status-new {
              background: #eff6ff;
              color: #1d4ed8;
              border-color: #bfdbfe;
            }

            .pt-status-on_progress,
            .pt-status-in_progress,
            .pt-status-progress {
              background: #fff7ed;
              color: #c2410c;
              border-color: #fed7aa;
            }

            .pt-status-completed,
            .pt-status-done {
              background: #ecfdf5;
              color: #047857;
              border-color: #bbf7d0;
            }

            .pt-status-pause {
              background: #fffbeb;
              color: #b45309;
              border-color: #fde68a;
            }

            .pt-priority-low {
              background: #f8fafc;
              color: #64748b;
            }

            .pt-priority-normal {
              background: #eef7fb;
              color: #0369a1;
            }

            .pt-priority-high {
              background: #fff7ed;
              color: #c2410c;
              border-color: #fed7aa;
            }

            .pt-priority-urgent {
              background: #fef2f2;
              color: #b91c1c;
              border-color: #fecaca;
            }

            /* Dates row */
            .pt-date-grid,
            .pt-task-dates {
              display: grid;
              grid-template-columns: repeat(2, minmax(0, 1fr));
              gap: 7px;
              margin-top: 10px;
            }

            .pt-date-chip {
              display: flex;
              align-items: center;
              gap: 6px;
              min-height: 30px;
              padding: 0 8px;
              border-radius: 11px;
              background: #f8fafc;
              border: 1px solid #e2e8f0;
              color: #475569;
              font-size: 11px;
              font-weight: 800;
            }

            .pt-date-chip i,
            .pt-date-chip .feather {
              width: 13px;
              height: 13px;
              color: var(--pt-blue);
            }

            .pt-date-chip.is-due {
              background: #fff7ed;
              border-color: #fed7aa;
              color: #9a3412;
            }

            .pt-date-chip.is-overdue {
              background: #fef2f2;
              border-color: #fecaca;
              color: #b91c1c;
            }

            /* Avatars */
            .pt-avatar-row,
            .pt-list-avatars {
              display: flex;
              align-items: center;
              margin-top: 9px;
              min-height: 28px;
            }

            .pt-list-avatar,
            .pt-avatar {
              width: 28px;
              height: 28px;
              border-radius: 999px;
              object-fit: cover;
              border: 2px solid #fff;
              background: #e2e8f0;
              margin-left: -7px;
              box-shadow: 0 2px 6px rgba(15, 23, 42, .12);
            }

            .pt-list-avatar:first-child,
            .pt-avatar:first-child {
              margin-left: 0;
            }

            .pt-avatar-more {
              min-width: 28px;
              height: 28px;
              border-radius: 999px;
              padding: 0 7px;
              margin-left: -7px;
              display: inline-flex;
              align-items: center;
              justify-content: center;
              background: #0f172a;
              color: #fff;
              font-size: 10px;
              font-weight: 950;
              border: 2px solid #fff;
            }

            /* Progress */
            .pt-progress-wrap {
              margin-top: 10px;
            }

            .pt-progress-head {
              display: flex;
              align-items: center;
              justify-content: space-between;
              color: #64748b;
              font-size: 11px;
              font-weight: 900;
              margin-bottom: 5px;
            }

            .pt-progress-bar {
              height: 8px;
              border-radius: 999px;
              background: #e2e8f0;
              overflow: hidden;
            }

            .pt-progress-fill {
              height: 100%;
              width: 0%;
              border-radius: 999px;
              background: linear-gradient(90deg, var(--pt-primary), var(--pt-blue));
            }

            /* Empty/loading/error */
            .pt-empty,
            .pt-loading,
            .pt-error {
              border: 1px dashed #cbd5e1;
              background: #fff;
              border-radius: 18px;
              padding: 24px 18px;
              text-align: center;
              color: #64748b;
              font-size: 13px;
              font-weight: 850;
              line-height: 1.5;
            }

            .pt-empty::before {
              content: "📋";
              display: block;
              font-size: 30px;
              margin-bottom: 7px;
            }

            .pt-loading::before {
              content: "";
              width: 28px;
              height: 28px;
              border-radius: 999px;
              border: 3px solid #e2e8f0;
              border-top-color: var(--pt-primary);
              display: block;
              margin: 0 auto 10px;
              animation: ptSpin .8s linear infinite;
            }

            .pt-error {
              color: #b91c1c;
              border-color: #fecaca;
              background: #fef2f2;
            }

            @keyframes ptSpin {
              to { transform: rotate(360deg); }
            }

            /* Detail pane */
            .pt-task-detail,
            #ptTaskDetail {
              height: 100%;
              overflow-y: auto;
              padding: 16px;
            }

            .pt-detail-placeholder {
              height: 100%;
              min-height: 420px;
              border: 1px dashed #cbd5e1;
              border-radius: 24px;
              background: linear-gradient(135deg, #fff 0%, #f8fafc 100%);
              display: flex;
              flex-direction: column;
              align-items: center;
              justify-content: center;
              text-align: center;
              color: #64748b;
              padding: 24px;
            }

            .pt-detail-placeholder-icon {
              width: 68px;
              height: 68px;
              border-radius: 24px;
              background: #eef7fb;
              color: var(--pt-blue);
              display: inline-flex;
              align-items: center;
              justify-content: center;
              margin-bottom: 14px;
              font-size: 28px;
            }

            .pt-detail-card {
              border: 1px solid #dbeafe;
              border-radius: 24px;
              background: #fff;
              box-shadow: var(--pt-shadow);
              overflow: hidden;
            }

            .pt-detail-head {
              padding: 18px;
              background: linear-gradient(135deg, #ffffff 0%, #eef7fb 100%);
              border-bottom: 1px solid #e5eef8;
            }

            .pt-detail-kicker {
              display: flex;
              align-items: center;
              gap: 7px;
              color: var(--pt-blue);
              font-size: 11px;
              font-weight: 950;
              text-transform: uppercase;
              letter-spacing: .08em;
              margin-bottom: 6px;
            }

            .pt-detail-title {
              margin: 0;
              color: var(--pt-dark);
              font-size: 20px;
              line-height: 1.25;
              font-weight: 950;
            }

            .pt-detail-desc {
              margin-top: 10px;
              color: #475569;
              line-height: 1.55;
              font-size: 13px;
            }

            .pt-detail-actions {
              display: flex;
              align-items: center;
              gap: 8px;
              margin-top: 14px;
              flex-wrap: wrap;
            }

            .pt-detail-body {
              padding: 16px 18px 18px;
              display: flex;
              flex-direction: column;
              gap: 14px;
            }

            .pt-section {
              border: 1px solid #e5eef8;
              background: #fff;
              border-radius: 18px;
              padding: 14px;
            }

            .pt-section-head {
              display: flex;
              align-items: center;
              justify-content: space-between;
              gap: 8px;
              margin-bottom: 11px;
            }

            .pt-section-title {
              margin: 0;
              color: var(--pt-dark);
              font-size: 13px;
              font-weight: 950;
              display: flex;
              align-items: center;
              gap: 7px;
              text-transform: uppercase;
              letter-spacing: .04em;
            }

            .pt-section-title i,
            .pt-section-title .feather {
              width: 15px;
              height: 15px;
              color: var(--pt-blue);
            }

            .pt-info-grid {
              display: grid;
              grid-template-columns: repeat(2, minmax(0, 1fr));
              gap: 10px;
            }

            .pt-info-box {
              border: 1px solid #e2e8f0;
              border-radius: 14px;
              background: #f8fafc;
              padding: 10px;
            }

            .pt-info-label {
              display: block;
              color: #64748b;
              font-size: 10px;
              font-weight: 950;
              text-transform: uppercase;
              letter-spacing: .06em;
              margin-bottom: 4px;
            }

            .pt-info-value {
              color: #0f172a;
              font-size: 13px;
              font-weight: 900;
              line-height: 1.35;
            }

            /* PersonalTaskKey list */
            .pt-key-list {
              display: flex;
              flex-direction: column;
              gap: 9px;
            }

            .pt-key-item {
              display: grid;
              grid-template-columns: 34px minmax(0, 1fr) auto;
              gap: 10px;
              align-items: start;
              border: 1px solid #e5eef8;
              background: #f8fafc;
              border-radius: 16px;
              padding: 10px;
              transition: .15s ease;
            }

            .pt-key-item:hover {
              background: #fff;
              border-color: #bfdbfe;
            }

            .pt-key-item.is-done,
            .pt-key-item.is-completed {
              background: #f0fdf4;
              border-color: #bbf7d0;
            }

            .pt-key-toggle {
              width: 28px;
              height: 28px;
              border-radius: 10px;
              border: 1px solid #cbd5e1;
              background: #fff;
              color: #64748b;
              cursor: pointer;
              display: inline-flex;
              align-items: center;
              justify-content: center;
              transition: .15s ease;
            }

            .pt-key-toggle:hover {
              border-color: var(--pt-primary);
              color: var(--pt-primary);
            }

            .pt-key-item.is-done .pt-key-toggle,
            .pt-key-item.is-completed .pt-key-toggle {
              background: var(--pt-success);
              border-color: var(--pt-success);
              color: #fff;
            }

            .pt-key-title {
              font-size: 13px;
              font-weight: 950;
              color: var(--pt-dark);
              line-height: 1.35;
            }

            .pt-key-desc {
              margin-top: 4px;
              color: #64748b;
              font-size: 12px;
              line-height: 1.45;
            }

            .pt-key-meta {
              display: flex;
              flex-wrap: wrap;
              gap: 6px;
              margin-top: 8px;
            }

            .pt-key-meta-chip {
              min-height: 23px;
              padding: 0 8px;
              border-radius: 999px;
              background: #fff;
              border: 1px solid #e2e8f0;
              color: #475569;
              font-size: 10px;
              font-weight: 900;
              display: inline-flex;
              align-items: center;
              gap: 5px;
            }

            .pt-key-time {
              color: #64748b;
              font-size: 11px;
              font-weight: 900;
              white-space: nowrap;
            }

            /* Comments / reports */
            .pt-comment-form {
              display: flex;
              flex-direction: column;
              gap: 8px;
            }

            .pt-comment-form textarea,
            .pt-reply-form textarea {
              width: 100%;
              min-height: 82px;
              border: 1px solid #dbeafe;
              border-radius: 16px;
              padding: 11px 12px;
              background: #f8fafc;
              color: var(--pt-dark);
              font-size: 13px;
              line-height: 1.45;
              outline: 0;
              resize: vertical;
            }

            .pt-comment-form textarea:focus,
            .pt-reply-form textarea:focus {
              background: #fff;
              border-color: var(--pt-blue);
              box-shadow: 0 0 0 4px rgba(116, 178, 212, .16);
            }

            .pt-comment-actions,
            .pt-reply-actions {
              display: flex;
              justify-content: flex-end;
              gap: 8px;
            }

            .pt-comments-list {
              display: flex;
              flex-direction: column;
              gap: 10px;
            }

            .pt-comment {
              display: flex;
              gap: 10px;
              align-items: flex-start;
            }

            .pt-comment-avatar {
              width: 34px;
              height: 34px;
              border-radius: 999px;
              object-fit: cover;
              background: #e2e8f0;
              border: 2px solid #fff;
              box-shadow: 0 2px 7px rgba(15, 23, 42, .12);
              flex: 0 0 auto;
            }

            .pt-comment-bubble {
              min-width: 0;
              flex: 1;
              border: 1px solid #e5eef8;
              border-radius: 16px;
              background: #f8fafc;
              padding: 10px 11px;
            }

            .pt-comment-head {
              display: flex;
              align-items: center;
              justify-content: space-between;
              gap: 8px;
              margin-bottom: 5px;
            }

            .pt-comment-author {
              color: var(--pt-dark);
              font-size: 12px;
              font-weight: 950;
            }

            .pt-comment-date {
              color: #94a3b8;
              font-size: 10px;
              font-weight: 800;
              white-space: nowrap;
            }

            .pt-comment-text {
              color: #334155;
              font-size: 13px;
              line-height: 1.48;
            }

            .pt-comment-text p {
              margin: 0 0 6px;
            }

            .pt-comment-text p:last-child {
              margin-bottom: 0;
            }

            .pt-comment-foot {
              display: flex;
              justify-content: flex-end;
              margin-top: 7px;
            }

            .pt-reply-btn {
              border: 0;
              background: transparent;
              color: var(--pt-blue);
              font-size: 11px;
              font-weight: 950;
              cursor: pointer;
              padding: 3px 6px;
              border-radius: 8px;
            }

            .pt-reply-btn:hover {
              background: #eef7fb;
              color: #0369a1;
            }

            .pt-replies {
              margin-top: 8px;
              margin-left: 30px;
              display: flex;
              flex-direction: column;
              gap: 8px;
            }

            .pt-replies .pt-comment-avatar {
              width: 28px;
              height: 28px;
            }

            .pt-replies .pt-comment-bubble {
              background: #fff;
            }

            /* Create task footer/form */
            .pt-create-panel,
            #ptCreatePanel {
              border-top: 1px solid #dbeafe;
              background: #fff;
              padding: 12px 16px;
            }

            .pt-create-toggle {
              width: 100%;
              border: 1px dashed #bfdbfe;
              background: #f8fafc;
              color: #334155;
              border-radius: 16px;
              min-height: 42px;
              font-size: 13px;
              font-weight: 950;
              cursor: pointer;
            }

            .pt-create-toggle:hover {
              background: #eef7fb;
              border-color: var(--pt-blue);
            }

            .pt-create-form {
              margin-top: 10px;
              display: grid;
              gap: 10px;
            }

            .pt-create-form input,
            .pt-create-form textarea,
            .pt-create-form select {
              width: 100%;
              border: 1px solid #dbeafe;
              border-radius: 14px;
              min-height: 38px;
              padding: 8px 11px;
              background: #f8fafc;
              color: var(--pt-dark);
              font-size: 13px;
              font-weight: 700;
              outline: 0;
            }

            .pt-create-form textarea {
              min-height: 84px;
              resize: vertical;
            }

            .pt-create-form input:focus,
            .pt-create-form textarea:focus,
            .pt-create-form select:focus {
              background: #fff;
              border-color: var(--pt-blue);
              box-shadow: 0 0 0 4px rgba(116, 178, 212, .16);
            }

            .pt-form-row {
              display: grid;
              grid-template-columns: repeat(2, minmax(0, 1fr));
              gap: 10px;
            }

            /* Buttons */
            .pt-btn {
              border: 1px solid #dbeafe;
              background: #fff;
              color: #334155;
              min-height: 36px;
              padding: 0 12px;
              border-radius: 13px;
              display: inline-flex;
              align-items: center;
              justify-content: center;
              gap: 7px;
              cursor: pointer;
              font-size: 12px;
              font-weight: 950;
              transition: .15s ease;
            }

            .pt-btn:hover {
              transform: translateY(-1px);
              background: #eef7fb;
              border-color: var(--pt-blue);
            }

            .pt-btn-primary {
              background: var(--pt-primary);
              border-color: var(--pt-primary);
              color: #fff;
              box-shadow: 0 10px 18px rgba(147, 194, 28, .22);
            }

            .pt-btn-primary:hover {
              background: var(--pt-primary-dark);
              color: #fff;
            }

            .pt-btn-danger {
              background: #fef2f2;
              border-color: #fecaca;
              color: #b91c1c;
            }

            .pt-btn-danger:hover {
              background: #fee2e2;
              border-color: #fca5a5;
            }

            /* Badges count */
            .pt-count-badge,
            #pt-count {
              display: inline-flex;
              align-items: center;
              justify-content: center;
              min-width: 24px;
              height: 22px;
              padding: 0 8px;
              border-radius: 999px;
              background: var(--pt-primary);
              color: #fff;
              font-size: 11px;
              font-weight: 950;
            }

            /* Scrollbar */
            .pt-task-list::-webkit-scrollbar,
            .pt-task-detail::-webkit-scrollbar,
            #ptTaskList::-webkit-scrollbar,
            #ptTaskDetail::-webkit-scrollbar {
              width: 8px;
            }

            .pt-task-list::-webkit-scrollbar-track,
            .pt-task-detail::-webkit-scrollbar-track,
            #ptTaskList::-webkit-scrollbar-track,
            #ptTaskDetail::-webkit-scrollbar-track {
              background: transparent;
            }

            .pt-task-list::-webkit-scrollbar-thumb,
            .pt-task-detail::-webkit-scrollbar-thumb,
            #ptTaskList::-webkit-scrollbar-thumb,
            #ptTaskDetail::-webkit-scrollbar-thumb {
              background: #cbd5e1;
              border-radius: 999px;
            }

            .pt-task-list::-webkit-scrollbar-thumb:hover,
            .pt-task-detail::-webkit-scrollbar-thumb:hover,
            #ptTaskList::-webkit-scrollbar-thumb:hover,
            #ptTaskDetail::-webkit-scrollbar-thumb:hover {
              background: #94a3b8;
            }

            /* Responsive */
            @media (max-width: 920px) {
              #pt-drawer.drawer,
              #ptDrawer.drawer,
              .pt-drawer {
                width: 100vw !important;
                max-width: 100vw !important;
              }

              .pt-list-panel {
                height: calc(100vh - 145px);
                grid-template-columns: 1fr;
              }

              .pt-list-left,
              .pt-task-list-pane {
                border-right: 0;
                border-bottom: 1px solid #dbeafe;
                max-height: 44vh;
              }

              .pt-list-right,
              .pt-task-detail-pane {
                min-height: 0;
              }

              #ptk-search,
              .pt-task-search {
                width: 100%;
              }

              .pt-list-toolbar,
              .pt-task-toolbar {
                align-items: stretch;
                flex-direction: column;
              }

              .pt-list-toolbar-actions {
                width: 100%;
              }

              .pt-refresh-btn,
              .pt-add-btn,
              .pt-action-btn {
                flex: 1;
              }

              .pt-info-grid,
              .pt-form-row,
              .pt-date-grid,
              .pt-task-dates {
                grid-template-columns: 1fr;
              }
            }

            @media (max-width: 560px) {
              .pt-context-bar {
                padding: 10px;
              }

              .pt-task-list,
              #ptTaskList,
              .pt-task-detail,
              #ptTaskDetail {
                padding: 10px;
              }

              .pt-detail-title {
                font-size: 17px;
              }

              .pt-key-item {
                grid-template-columns: 30px minmax(0, 1fr);
              }

              .pt-key-time {
                grid-column: 2;
              }

              .pt-comment {
                gap: 8px;
              }

              .pt-replies {
                margin-left: 14px;
              }
            }

                /* =========================================================
                   AUFGABE SIDEBAR V3 - Lucide, 2 tabs, collapsible comments
                   Inline inside this Blade as requested
                ========================================================= */
                .pt-lucide-drawer{
                  --ptx-primary:#93c21c;
                  --ptx-primary-dark:#7daa17;
                  --ptx-blue:#74b2d4;
                  --ptx-ink:#0f172a;
                  --ptx-text:#334155;
                  --ptx-muted:#64748b;
                  --ptx-border:#dbeafe;
                  --ptx-soft:#f8fafc;
                  --ptx-soft-blue:#eef7fb;
                  --ptx-danger:#dc2626;
                  --ptx-warning:#f59e0b;
                  --ptx-success:#16a34a;
                  --ptx-shadow:0 26px 80px rgba(15,23,42,.22);
                  width:min(1040px,96vw)!important;
                  max-width:96vw!important;
                  background:#f8fafc!important;
                  border-left:1px solid rgba(219,234,254,.95);
                  box-shadow:var(--ptx-shadow)!important;
                  color:var(--ptx-text);
                }

                .pt-lucide-drawer svg,
                .ptx-task-modal svg{width:18px;height:18px;stroke-width:2.25;}

                .pt-lucide-drawer .ptx-head{
                  display:flex;
                  align-items:center;
                  justify-content:space-between;
                  gap:14px;
                  padding:16px 18px;
                  background:linear-gradient(135deg,#fff 0%,#eef7fb 100%);
                  border-bottom:1px solid var(--ptx-border);
                }

                .ptx-head-left{display:flex;align-items:center;gap:13px;min-width:0;}
                .ptx-head-icon{
                  width:48px;height:48px;border-radius:18px;
                  display:inline-flex;align-items:center;justify-content:center;
                  color:#fff;background:linear-gradient(135deg,var(--ptx-primary),#c7e86a);
                  box-shadow:0 14px 28px rgba(147,194,28,.30);
                  flex:0 0 auto;
                }
                .ptx-head-icon svg{width:24px;height:24px;}
                .ptx-head-copy{min-width:0;}
                .ptx-kicker{font-size:11px;font-weight:950;letter-spacing:.08em;text-transform:uppercase;color:var(--ptx-blue);line-height:1.1;}
                .ptx-title-row{display:flex;align-items:center;gap:9px;margin-top:2px;}
                .ptx-title-row h2{margin:0;color:var(--ptx-ink);font-size:20px;font-weight:950;line-height:1.15;}
                .ptx-head-copy p{margin:4px 0 0;color:var(--ptx-muted);font-size:12px;font-weight:800;line-height:1.35;}
                .ptx-count{min-width:26px;height:24px;border-radius:999px;background:var(--ptx-primary);color:#fff;display:inline-flex;align-items:center;justify-content:center;font-size:12px;font-weight:950;padding:0 8px;}
                .ptx-head-actions{display:flex;align-items:center;gap:8px;}
                .ptx-icon-btn{width:40px;height:40px;border:1px solid #dbeafe;border-radius:14px;background:#fff;color:#334155;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;transition:.16s ease;}
                .ptx-icon-btn:hover{background:#eef7fb;border-color:var(--ptx-blue);transform:translateY(-1px);color:#0f172a;}

                .ptx-context-strip{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;padding:12px 16px;background:#fff;border-bottom:1px solid #e5eef8;}
                .ptx-context-card{display:flex;align-items:center;gap:10px;min-width:0;padding:10px 12px;border:1px solid #e2e8f0;border-radius:18px;background:#f8fafc;}
                .ptx-context-card>span{width:34px;height:34px;border-radius:13px;display:inline-flex;align-items:center;justify-content:center;background:#fff;color:var(--ptx-blue);flex:0 0 auto;box-shadow:0 4px 10px rgba(15,23,42,.06);}
                .ptx-context-card small{display:block;color:var(--ptx-muted);font-size:10px;font-weight:950;text-transform:uppercase;letter-spacing:.06em;}
                .ptx-context-card strong{display:block;color:var(--ptx-ink);font-size:13px;font-weight:950;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
                .ptx-context-card.is-product{background:#f7fbef;border-color:rgba(147,194,28,.35);}
                .ptx-context-card.is-customer{background:#eef7fb;border-color:rgba(116,178,212,.35);}

                .ptx-tabs{display:flex;gap:8px;padding:12px 16px 0;background:#fff;border-bottom:1px solid #e5eef8;}
                .ptx-tab{height:44px;border:1px solid #e2e8f0;border-bottom:0;border-radius:17px 17px 0 0;background:#f8fafc;color:#64748b;display:inline-flex;align-items:center;gap:8px;padding:0 16px;font-size:13px;font-weight:950;cursor:pointer;transition:.16s ease;}
                .ptx-tab:hover{background:#eef7fb;color:#0f172a;}
                .ptx-tab.is-active{background:#fff;color:#0f172a;border-color:var(--ptx-primary);box-shadow:0 -2px 0 var(--ptx-primary) inset;}
                .ptx-tab-panels{flex:1;min-height:0;display:flex;overflow:hidden;background:#f8fafc;}
                .ptx-tab-panel{display:none;flex:1;min-height:0;overflow:hidden;}
                .ptx-tab-panel.is-active{display:flex;flex-direction:column;}

                .ptx-toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:13px 16px;background:#fff;border-bottom:1px solid #e5eef8;}
                .ptx-search{position:relative;flex:1;min-width:220px;}
                .ptx-search svg{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--ptx-blue);width:17px;height:17px;}
                .ptx-search input{height:42px;border:1px solid var(--ptx-border)!important;border-radius:16px!important;background:#f8fafc!important;padding-left:42px!important;color:var(--ptx-ink)!important;font-size:13px!important;font-weight:800!important;outline:0!important;}
                .ptx-search input:focus{background:#fff!important;border-color:var(--ptx-blue)!important;box-shadow:0 0 0 4px rgba(116,178,212,.16)!important;}
                .ptx-filter-row{display:flex;align-items:center;gap:7px;overflow-x:auto;max-width:52%;padding-bottom:2px;}
                .ptx-filter{height:34px;border:1px solid #e2e8f0;border-radius:999px;background:#fff;color:#475569;display:inline-flex;align-items:center;gap:6px;padding:0 10px;font-size:11px;font-weight:950;white-space:nowrap;cursor:pointer;transition:.16s ease;}
                .ptx-filter b{min-width:20px;height:20px;border-radius:999px;background:#f1f5f9;color:#334155;display:inline-flex;align-items:center;justify-content:center;font-size:10px;}
                .ptx-filter:hover{background:#f1f5f9;color:#0f172a;}
                .ptx-filter.is-active{background:#ecfccb;border-color:rgba(147,194,28,.65);color:#365314;}
                .ptx-filter.is-active b{background:#365314;color:#fff;}

                .ptx-task-sortbar{display:flex;align-items:center;justify-content:space-between;gap:10px;padding:10px 16px;background:linear-gradient(135deg,#fff 0%,#f8fafc 100%);border-bottom:1px solid #eef2f7;}
                .ptx-task-sortbar strong{display:block;color:var(--ptx-ink);font-size:13px;font-weight:950;}
                .ptx-task-sortbar small{display:block;color:var(--ptx-muted);font-size:11px;font-weight:800;margin-top:2px;}
                .ptx-sort-hint{display:flex;align-items:center;gap:6px;color:var(--ptx-muted);font-size:11px;font-weight:900;white-space:nowrap;}

                .ptx-task-feed{flex:1;min-height:0;overflow:auto;padding:14px;background:#f8fafc;}
                .ptx-task-list{display:flex;flex-direction:column;gap:12px;}
                .ptx-task-date-group{display:flex;align-items:center;gap:8px;margin:8px 0 2px;color:var(--ptx-muted);font-size:11px;font-weight:950;text-transform:uppercase;letter-spacing:.06em;}
                .ptx-task-date-group:after{content:"";height:1px;background:#e2e8f0;flex:1;}

                .ptx-task-card,
                .pt-task-item,
                .pt-list-item{
                  border:1px solid #e2e8f0!important;
                  border-left:5px solid var(--ptx-blue)!important;
                  background:#fff!important;
                  border-radius:20px!important;
                  box-shadow:0 10px 26px rgba(15,23,42,.07)!important;
                  overflow:hidden!important;
                  transition:transform .16s ease,box-shadow .16s ease,border-color .16s ease!important;
                }
                .ptx-task-card:hover,.pt-task-item:hover,.pt-list-item:hover{transform:translateY(-2px);box-shadow:0 18px 40px rgba(15,23,42,.11)!important;border-color:#bfdbfe!important;}
                .ptx-task-card.is-open,.ptx-task-card.is-active,.pt-task-item.is-active,.pt-list-item.is-active{border-left-color:var(--ptx-primary)!important;border-color:rgba(147,194,28,.65)!important;box-shadow:0 0 0 3px rgba(147,194,28,.14),0 18px 40px rgba(15,23,42,.11)!important;background:linear-gradient(135deg,#fff 0%,#f7fbef 100%)!important;}

                .ptx-task-main,
                .pt-task-item-main{width:100%;border:0;background:transparent;display:grid;grid-template-columns:38px minmax(0,1fr) 28px;gap:10px;align-items:start;text-align:left;padding:13px;cursor:pointer;color:inherit;}
                .ptx-task-state{width:34px;height:34px;border-radius:14px;background:#eef7fb;color:var(--ptx-blue);display:inline-flex;align-items:center;justify-content:center;}
                .ptx-task-copy strong,.pt-task-item-main strong{display:block;color:var(--ptx-ink);font-size:14px;font-weight:950;line-height:1.28;}
                .ptx-task-copy small,.pt-task-item-main span{display:block;margin-top:4px;color:var(--ptx-muted);font-size:12px;font-weight:750;line-height:1.42;}
                .ptx-task-chevron{width:28px;height:28px;border-radius:999px;background:#f1f5f9;color:#64748b;display:inline-flex;align-items:center;justify-content:center;transition:.16s ease;}
                .ptx-task-card.is-open .ptx-task-chevron{transform:rotate(180deg);background:var(--ptx-primary);color:#fff;}

                .ptx-task-body{display:none;padding:0 13px 13px;}
                .ptx-task-card.is-open .ptx-task-body{display:block;}
                .ptx-task-meta-grid,.pt-task-meta-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:8px;padding-top:2px;}
                .ptx-meta-chip,.pt-task-meta-grid span{min-height:34px;border:1px solid #e2e8f0;border-radius:13px;background:#f8fafc;color:#475569;display:flex;align-items:center;gap:7px;padding:0 9px;font-size:11px;font-weight:900;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
                .ptx-meta-chip svg,.pt-task-meta-grid svg,.pt-task-meta-grid .feather{width:14px;height:14px;color:var(--ptx-blue);}
                .ptx-badge-row{display:flex;flex-wrap:wrap;gap:6px;margin-top:10px;}
                .ptx-badge,.pt-task-status,.pt-task-priority{display:inline-flex;align-items:center;gap:5px;min-height:24px;padding:0 8px;border-radius:999px;font-size:10px;font-weight:950;text-transform:uppercase;letter-spacing:.03em;border:1px solid #e2e8f0;background:#f1f5f9;color:#334155;}
                .pt-task-status--open,.ptx-status-open{background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;}
                .pt-task-status--on_progress,.pt-task-status--progress,.ptx-status-progress{background:#fff7ed;color:#c2410c;border-color:#fed7aa;}
                .pt-task-status--completed,.ptx-status-completed{background:#ecfdf5;color:#047857;border-color:#bbf7d0;}
                .pt-task-priority--urgent,.ptx-priority-urgent{background:#fef2f2;color:#b91c1c;border-color:#fecaca;}
                .pt-task-priority--high,.ptx-priority-high{background:#fff7ed;color:#c2410c;border-color:#fed7aa;}
                .pt-task-priority--normal,.ptx-priority-normal{background:#eef7fb;color:#0369a1;border-color:#bae6fd;}

                .ptx-key-list{display:flex;flex-direction:column;gap:8px;margin-top:12px;}
                .ptx-key-item,.pt-key-item{display:grid;grid-template-columns:30px minmax(0,1fr) auto;gap:10px;align-items:start;border:1px solid #e5eef8;background:#f8fafc;border-radius:16px;padding:10px;}
                .ptx-key-item.is-done,.pt-key-item.is-done,.pt-key-item.is-completed{background:#f0fdf4;border-color:#bbf7d0;}
                .ptx-key-toggle,.pt-key-toggle{width:28px;height:28px;border-radius:10px;border:1px solid #cbd5e1;background:#fff;color:#64748b;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;}
                .ptx-key-item.is-done .ptx-key-toggle,.pt-key-item.is-done .pt-key-toggle,.pt-key-item.is-completed .pt-key-toggle{background:var(--ptx-success);border-color:var(--ptx-success);color:#fff;}
                .ptx-key-title,.pt-key-title{font-size:13px;font-weight:950;color:var(--ptx-ink);line-height:1.35;}
                .ptx-key-desc,.pt-key-desc{margin-top:4px;color:#64748b;font-size:12px;line-height:1.45;}
                .ptx-key-time,.pt-key-time{font-size:11px;font-weight:900;color:#64748b;white-space:nowrap;}

                .ptx-comments-shell{margin-top:12px;border:1px solid #e5eef8;border-radius:18px;background:#fff;overflow:hidden;}
                .ptx-comments-toggle{width:100%;border:0;background:#f8fafc;color:#334155;display:flex;align-items:center;justify-content:space-between;gap:10px;min-height:42px;padding:0 12px;font-size:12px;font-weight:950;cursor:pointer;}
                .ptx-comments-toggle span{display:inline-flex;align-items:center;gap:7px;}
                .ptx-comments-shell.is-open .ptx-comments-toggle>svg:last-child{transform:rotate(180deg);}
                .ptx-comments-collapse{display:none;padding:12px;border-top:1px solid #e5eef8;background:#fff;}
                .ptx-comments-shell.is-open .ptx-comments-collapse{display:block;}
                .ptx-comments-list,.pt-comments-list{display:flex;flex-direction:column;gap:10px;margin-bottom:10px;}
                .ptx-comment,.pt-comment{display:flex;gap:10px;align-items:flex-start;}
                .ptx-comment-avatar,.pt-comment-avatar{width:34px;height:34px;border-radius:999px;object-fit:cover;background:#e2e8f0;border:2px solid #fff;box-shadow:0 2px 7px rgba(15,23,42,.12);flex:0 0 auto;}
                .ptx-comment-bubble,.pt-comment-bubble{min-width:0;flex:1;border:1px solid #e5eef8;border-radius:16px;background:#f8fafc;padding:10px 11px;}
                .ptx-comment-head,.pt-comment-head{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:5px;}
                .ptx-comment-author,.pt-comment-author{color:var(--ptx-ink);font-size:12px;font-weight:950;}
                .ptx-comment-date,.pt-comment-date{color:#94a3b8;font-size:10px;font-weight:800;white-space:nowrap;}
                .ptx-comment-text,.pt-comment-text{color:#334155;font-size:13px;line-height:1.48;}
                .ptx-comment-form,.pt-comment-form{display:flex;flex-direction:column;gap:8px;}
                .ptx-comment-form textarea,.pt-comment-form textarea{width:100%;min-height:82px;border:1px solid #dbeafe;border-radius:16px;padding:11px 12px;background:#f8fafc;color:var(--ptx-ink);font-size:13px;line-height:1.45;outline:0;resize:vertical;}
                .ptx-comment-form textarea:focus,.pt-comment-form textarea:focus{background:#fff;border-color:var(--ptx-blue);box-shadow:0 0 0 4px rgba(116,178,212,.16);}

                .ptx-empty,.pt-empty,.pt-empty-state,.pt-empty-detail{border:1px dashed #cbd5e1;background:#fff;border-radius:22px;padding:28px 18px;text-align:center;color:#64748b;font-size:13px;font-weight:850;line-height:1.5;display:flex;flex-direction:column;align-items:center;gap:6px;}
                .ptx-empty svg,.pt-empty svg,.pt-empty-state svg,.pt-empty-detail svg{width:34px;height:34px;color:var(--ptx-blue);}
                .ptx-empty strong,.pt-empty strong,.pt-empty-state strong,.pt-empty-detail strong{color:var(--ptx-ink);font-size:14px;font-weight:950;}

                .ptx-create-shell{flex:1;min-height:0;overflow:auto;padding:16px;background:#f8fafc;}
                .ptx-create-intro{display:flex;align-items:center;gap:13px;border:1px solid #dbeafe;border-radius:24px;background:linear-gradient(135deg,#fff 0%,#eef7fb 100%);padding:16px;margin-bottom:14px;box-shadow:0 10px 26px rgba(15,23,42,.06);}
                .ptx-create-icon{width:48px;height:48px;border-radius:18px;display:inline-flex;align-items:center;justify-content:center;background:var(--ptx-primary);color:#fff;box-shadow:0 14px 28px rgba(147,194,28,.28);}
                .ptx-create-icon svg{width:24px;height:24px;}
                .ptx-create-intro h3{margin:0;color:var(--ptx-ink);font-size:17px;font-weight:950;}
                .ptx-create-intro p{margin:4px 0 0;color:var(--ptx-muted);font-size:12px;font-weight:800;}
                .ptx-create-form{border:1px solid #dbeafe;border-radius:24px;background:#fff;padding:16px;box-shadow:0 12px 32px rgba(15,23,42,.08);}
                .ptx-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;}
                .ptx-form-group.is-wide{grid-column:1/-1;}
                .ptx-form-group label{display:block;margin-bottom:6px;color:#334155;font-size:11px;font-weight:950;text-transform:uppercase;letter-spacing:.05em;}
                .ptx-form-group input,.ptx-form-group textarea,.ptx-form-group select,.ptx-create-form .form-control{width:100%;border:1px solid #dbeafe!important;border-radius:15px!important;min-height:40px;padding:9px 11px;background:#f8fafc!important;color:var(--ptx-ink)!important;font-size:13px!important;font-weight:750!important;outline:0!important;}
                .ptx-form-group textarea{min-height:96px;resize:vertical;}
                .ptx-form-group input:focus,.ptx-form-group textarea:focus,.ptx-form-group select:focus,.ptx-create-form .form-control:focus{background:#fff!important;border-color:var(--ptx-blue)!important;box-shadow:0 0 0 4px rgba(116,178,212,.16)!important;}
                .ptx-form-options{display:flex;flex-wrap:wrap;gap:9px;margin:12px 0;}
                .ptx-check{display:inline-flex;align-items:center;gap:8px;border:1px solid #e2e8f0;border-radius:999px;background:#f8fafc;min-height:36px;padding:0 12px;color:#334155;font-size:12px;font-weight:900;cursor:pointer;}
                .ptx-check input{margin:0;}
                .ptx-check span{display:inline-flex;align-items:center;gap:6px;}
                .ptx-steps-box{margin-top:14px;border:1px solid #e5eef8;border-radius:20px;background:#f8fafc;padding:13px;}
                .ptx-steps-head{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:11px;}
                .ptx-steps-head strong{display:block;color:var(--ptx-ink);font-size:13px;font-weight:950;}
                .ptx-steps-head small{display:block;color:var(--ptx-muted);font-size:11px;font-weight:800;margin-top:2px;}
                .ptx-steps-list{display:flex;flex-direction:column;gap:10px;}
                .ptx-create-actions{display:flex;justify-content:flex-end;gap:9px;margin-top:15px;}
                .ptx-primary-btn,.ptx-secondary-btn{border:1px solid #dbeafe;border-radius:14px;min-height:38px;padding:0 13px;display:inline-flex;align-items:center;justify-content:center;gap:7px;font-size:12px;font-weight:950;cursor:pointer;transition:.16s ease;}
                .ptx-primary-btn{background:var(--ptx-primary);border-color:var(--ptx-primary);color:#fff;box-shadow:0 10px 20px rgba(147,194,28,.22);}
                .ptx-primary-btn:hover{background:var(--ptx-primary-dark);color:#fff;transform:translateY(-1px);}
                .ptx-secondary-btn{background:#fff;color:#334155;}
                .ptx-secondary-btn:hover{background:#eef7fb;border-color:var(--ptx-blue);transform:translateY(-1px);}

                .pt-panel-backdrop.show{opacity:1!important;pointer-events:auto!important;}
                .pt-lucide-drawer.open{transform:translateX(0)!important;}

                @media(max-width:991px){
                  .pt-lucide-drawer{width:100vw!important;max-width:100vw!important;}
                  .ptx-context-strip{grid-template-columns:1fr;}
                  .ptx-toolbar{align-items:stretch;flex-direction:column;}
                  .ptx-filter-row{max-width:100%;}
                  .ptx-task-meta-grid,.pt-task-meta-grid{grid-template-columns:repeat(2,minmax(0,1fr));}
                  .ptx-form-grid{grid-template-columns:1fr;}
                }
                @media(max-width:560px){
                  .ptx-head{align-items:flex-start!important;}
                  .ptx-head-left{align-items:flex-start;}
                  .ptx-title-row h2{font-size:17px;}
                  .ptx-tabs{gap:5px;padding-left:10px;padding-right:10px;}
                  .ptx-tab{flex:1;justify-content:center;padding:0 8px;font-size:12px;}
                  .ptx-task-meta-grid,.pt-task-meta-grid{grid-template-columns:1fr;}
                  .ptx-task-main,.pt-task-item-main{grid-template-columns:32px minmax(0,1fr) 24px;padding:11px;}
                  .ptx-create-shell,.ptx-task-feed{padding:10px;}
                }

          /* ==========================================================================
             Aufgabe Sidebar v3 inline override
             Works with the Lucide two-tab Blade and patched kanban.js renderer
             ========================================================================== */
          #pt-drawer.pt-lucide-drawer {
            width: min(1040px, 96vw) !important;
            max-width: 96vw !important;
            background: #f8fafc !important;
          }

          #pt-drawer .ptx-tab-panel {
            display: none;
          }

          #pt-drawer .ptx-tab-panel.is-active {
            display: block;
          }

          #pt-drawer .ptx-task-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 14px;
            overflow-y: auto;
            max-height: calc(100vh - 260px);
          }

          .ptx-task-card {
            border: 1px solid #dbeafe;
            border-left: 5px solid #74b2d4;
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .08);
            overflow: hidden;
            transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease;
          }

          .ptx-task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 48px rgba(15, 23, 42, .13);
            border-color: #bfdbfe;
          }

          .ptx-task-card.is-comments-open {
            border-left-color: #93c21c;
            box-shadow: 0 0 0 3px rgba(147, 194, 28, .13), 0 18px 44px rgba(15, 23, 42, .12);
          }

          .ptx-task-main {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr) auto;
            gap: 12px;
            align-items: flex-start;
            padding: 15px;
          }

          .ptx-task-state {
            width: 42px;
            height: 42px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #eef7fb;
            color: #0369a1;
          }

          .ptx-task-state svg {
            width: 21px;
            height: 21px;
          }

          .ptx-status-done,
          .ptx-task-state.ptx-status-done {
            background: #ecfdf5;
            color: #047857;
          }

          .ptx-status-in_progress,
          .ptx-task-state.ptx-status-in_progress {
            background: #fff7ed;
            color: #c2410c;
          }

          .ptx-status-paused,
          .ptx-task-state.ptx-status-paused {
            background: #fffbeb;
            color: #b45309;
          }

          .ptx-status-canceled,
          .ptx-task-state.ptx-status-canceled {
            background: #fef2f2;
            color: #b91c1c;
          }

          .ptx-task-copy {
            min-width: 0;
          }

          .ptx-task-topline,
          .ptx-task-meta,
          .ptx-avatar-row,
          .ptx-key-meta {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 7px;
          }

          .ptx-task-topline {
            margin-bottom: 8px;
          }

          .ptx-status-badge,
          .ptx-priority-badge,
          .ptx-task-id {
            display: inline-flex;
            align-items: center;
            min-height: 24px;
            padding: 0 9px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .04em;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            color: #334155;
          }

          .ptx-status-badge.ptx-status-open {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
          }

          .ptx-status-badge.ptx-status-in_progress {
            background: #fff7ed;
            color: #c2410c;
            border-color: #fed7aa;
          }

          .ptx-status-badge.ptx-status-done {
            background: #ecfdf5;
            color: #047857;
            border-color: #bbf7d0;
          }

          .ptx-priority-high,
          .ptx-priority-urgent {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
          }

          .ptx-task-copy h3 {
            margin: 0;
            color: #0f172a;
            font-size: 16px;
            font-weight: 950;
            line-height: 1.25;
          }

          .ptx-task-copy p {
            margin: 7px 0 0;
            color: #475569;
            font-size: 13px;
            line-height: 1.5;
          }

          .ptx-task-meta {
            margin-top: 11px;
          }

          .ptx-task-meta span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            min-height: 27px;
            padding: 0 9px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 11px;
            font-weight: 850;
          }

          .ptx-task-meta span.is-due {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #9a3412;
          }

          .ptx-task-meta svg {
            width: 14px;
            height: 14px;
          }

          .ptx-avatar-row {
            margin-top: 10px;
          }

          .ptx-avatar {
            width: 30px;
            height: 30px;
            border-radius: 999px;
            object-fit: cover;
            border: 2px solid #fff;
            background: #e2e8f0;
            box-shadow: 0 2px 7px rgba(15, 23, 42, .14);
            margin-left: -8px;
          }

          .ptx-avatar:first-child {
            margin-left: 0;
          }

          .ptx-avatar-fallback,
          .ptx-avatar-more {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #334155;
            font-size: 10px;
            font-weight: 950;
          }

          .ptx-avatar-more {
            background: #0f172a;
            color: #fff;
          }

          .ptx-muted {
            color: #94a3b8;
            font-size: 12px;
            font-weight: 800;
          }

          .ptx-task-actions {
            display: flex;
            align-items: center;
            gap: 6px;
          }

          .ptx-icon-action {
            width: 34px;
            height: 34px;
            border: 1px solid #dbeafe;
            border-radius: 13px;
            background: #fff;
            color: #334155;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: .15s ease;
          }

          .ptx-icon-action:hover {
            background: #eef7fb;
            border-color: #74b2d4;
            transform: translateY(-1px);
          }

          .ptx-icon-action.is-danger {
            color: #dc2626;
          }

          .ptx-task-card.is-comments-open .ptx-icon-action[data-pt-comments-toggle] svg {
            transform: rotate(180deg);
          }

          .ptx-task-details {
            display: none;
            border-top: 1px solid #e5eef8;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            padding: 14px 15px 16px;
          }

          .ptx-task-card.is-comments-open .ptx-task-details {
            display: grid;
            gap: 14px;
          }

          .ptx-task-section {
            border: 1px solid #e5eef8;
            background: #fff;
            border-radius: 18px;
            padding: 14px;
          }

          .ptx-task-section h4 {
            margin: 0 0 11px;
            display: flex;
            align-items: center;
            gap: 7px;
            color: #0f172a;
            font-size: 13px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .04em;
          }

          .ptx-task-section h4 svg {
            width: 16px;
            height: 16px;
            color: #74b2d4;
          }

          .ptx-key-list {
            display: grid;
            gap: 8px;
          }

          .ptx-key-row {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr);
            gap: 10px;
            align-items: flex-start;
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 15px;
            padding: 10px;
          }

          .ptx-key-row.is-done {
            background: #f0fdf4;
            border-color: #bbf7d0;
          }

          .ptx-key-check {
            width: 30px;
            height: 30px;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #64748b;
            border-radius: 11px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
          }

          .ptx-key-row.is-done .ptx-key-check {
            background: #16a34a;
            border-color: #16a34a;
            color: #fff;
          }

          .ptx-key-copy strong {
            color: #0f172a;
            font-size: 13px;
            font-weight: 950;
          }

          .ptx-key-copy span {
            display: block;
            margin-top: 4px;
            color: #64748b;
            font-size: 12px;
          }

          .ptx-key-meta {
            margin-top: 7px;
          }

          .ptx-key-meta small {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            min-height: 23px;
            padding: 0 8px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #475569;
            font-size: 10px;
            font-weight: 850;
          }

          .ptx-comment-form,
          .ptx-reply-form {
            display: grid;
            gap: 8px;
            margin-bottom: 12px;
          }

          .ptx-comment-form textarea,
          .ptx-reply-form textarea {
            width: 100%;
            min-height: 82px;
            border: 1px solid #dbeafe;
            border-radius: 16px;
            padding: 11px 12px;
            background: #f8fafc;
            color: #0f172a;
            outline: 0;
            resize: vertical;
            font-size: 13px;
            line-height: 1.45;
          }

          .ptx-comment-form textarea:focus,
          .ptx-reply-form textarea:focus {
            background: #fff;
            border-color: #74b2d4;
            box-shadow: 0 0 0 4px rgba(116, 178, 212, .16);
          }

          .ptx-btn {
            justify-self: end;
            border: 1px solid #dbeafe;
            border-radius: 13px;
            min-height: 36px;
            padding: 0 13px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: #fff;
            color: #334155;
            font-size: 12px;
            font-weight: 950;
            cursor: pointer;
          }

          .ptx-btn-primary {
            background: #93c21c;
            border-color: #93c21c;
            color: #fff;
            box-shadow: 0 10px 20px rgba(147, 194, 28, .22);
          }

          .ptx-comment-list,
          .ptx-replies {
            display: grid;
            gap: 10px;
          }

          .ptx-comment {
            display: grid;
            grid-template-columns: 34px minmax(0, 1fr);
            gap: 10px;
            align-items: flex-start;
          }

          .ptx-comment.is-reply {
            margin-left: 32px;
            grid-template-columns: 28px minmax(0, 1fr);
          }

          .ptx-comment-body {
            border: 1px solid #e5eef8;
            border-radius: 16px;
            background: #f8fafc;
            padding: 10px 11px;
          }

          .ptx-comment.is-reply .ptx-comment-body {
            background: #fff;
          }

          .ptx-comment-head {
            display: flex;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 5px;
          }

          .ptx-comment-head strong {
            color: #0f172a;
            font-size: 12px;
            font-weight: 950;
          }

          .ptx-comment-head span {
            color: #94a3b8;
            font-size: 10px;
            font-weight: 800;
          }

          .ptx-comment-text {
            color: #334155;
            font-size: 13px;
            line-height: 1.5;
          }

          .ptx-comment-text p {
            margin: 0 0 6px;
          }

          .ptx-reply-toggle {
            margin-top: 7px;
            border: 0;
            background: transparent;
            color: #0369a1;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 6px;
            border-radius: 9px;
            font-size: 11px;
            font-weight: 950;
            cursor: pointer;
          }

          .ptx-reply-toggle:hover {
            background: #eef7fb;
          }

          .ptx-empty,
          .ptx-empty-small {
            border: 1px dashed #cbd5e1;
            border-radius: 18px;
            background: #fff;
            color: #64748b;
            text-align: center;
            padding: 22px;
            display: grid;
            gap: 6px;
            justify-items: center;
            font-size: 13px;
            font-weight: 850;
          }

          .ptx-empty svg {
            width: 32px;
            height: 32px;
            color: #74b2d4;
          }

          .ptx-empty-small {
            padding: 12px;
            font-size: 12px;
          }

          .ptx-skeleton {
            height: 118px;
            border-radius: 22px;
            background: linear-gradient(90deg, #eef2f7 0%, #f8fafc 45%, #eef2f7 100%);
            background-size: 220% 100%;
            animation: ptxSkeleton 1.1s ease-in-out infinite;
          }

          @keyframes ptxSkeleton {
            0% { background-position: 100% 50%; }
            100% { background-position: 0 50%; }
          }

          @media (max-width: 760px) {
            .ptx-task-main {
              grid-template-columns: 36px minmax(0, 1fr);
            }

            .ptx-task-actions {
              grid-column: 2;
              justify-content: flex-end;
            }

            .ptx-task-list {
              max-height: calc(100vh - 300px);
              padding: 10px;
            }
          }

          /* =========================================================
             Aufgabe Sidebar Scroll Fix
             Add at bottom of inline Blade CSS
             ========================================================= */

          #pt-drawer,
          #ptDrawer,
          .pt-drawer {
            height: 100vh !important;
            max-height: 100vh !important;
            overflow: hidden !important;
          }

          #pt-drawer .drawer-body,
          #ptDrawer .drawer-body,
          .pt-drawer .drawer-body {
            height: calc(100vh - 74px) !important;
            max-height: calc(100vh - 74px) !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
            min-height: 0 !important;
          }

          /* Main Lucide task shell */
          .ptx-shell,
          .ptx-drawer-shell,
          .ptx-task-shell,
          .ptx-panel {
            height: 100% !important;
            min-height: 0 !important;
            overflow: hidden !important;
            display: flex !important;
            flex-direction: column !important;
          }

          /* Tab header should stay fixed */
          .ptx-tabs,
          .ptx-tab-nav,
          .ptx-task-tabs {
            flex: 0 0 auto !important;
          }

          /* Tab content area should take remaining height */
          .ptx-tab-content,
          .ptx-tabs-content,
          .ptx-body {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            overflow: hidden !important;
          }

          /* Each tab pane */
          .ptx-tab-pane,
          .ptx-tab-panel {
            height: 100% !important;
            min-height: 0 !important;
            overflow: hidden !important;
          }

          /* Active task tab */
          .ptx-tab-pane.is-active,
          .ptx-tab-panel.is-active,
          .ptx-tab-pane.active,
          .ptx-tab-panel.active {
            display: flex !important;
            flex-direction: column !important;
          }

          /* Search/filter/header area fixed */
          .ptx-task-toolbar,
          .ptx-task-head,
          .ptx-task-filterbar,
          .ptx-context-bar {
            flex: 0 0 auto !important;
          }

          /* This is the important part: task list scroll */
          #ptTaskList,
          #pt-list,
          .ptx-task-list,
          .ptx-tasks-list,
          .pt-task-list {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            max-height: none !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding-right: 10px !important;
            -webkit-overflow-scrolling: touch !important;
            overscroll-behavior: contain !important;
          }

          /* Task cards should not break scrolling */
          .ptx-task-card,
          .pt-task-card,
          .pt-list-item {
            flex: 0 0 auto !important;
          }

          /* Comments inside each task should also scroll if long */
          .ptx-task-comments,
          .ptx-comments-list,
          .pt-comments-list {
            max-height: 260px !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding-right: 8px !important;
            -webkit-overflow-scrolling: touch !important;
          }

          /* New task tab form scroll */
          #pt-form,
          .ptx-new-task-form,
          .ptx-create-form,
          .pt-create-form {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding-right: 8px !important;
            -webkit-overflow-scrolling: touch !important;
          }

          /* Nice scrollbar */
          #ptTaskList::-webkit-scrollbar,
          #pt-list::-webkit-scrollbar,
          .ptx-task-list::-webkit-scrollbar,
          .ptx-tasks-list::-webkit-scrollbar,
          .pt-task-list::-webkit-scrollbar,
          .ptx-task-comments::-webkit-scrollbar,
          .ptx-comments-list::-webkit-scrollbar,
          .pt-comments-list::-webkit-scrollbar,
          #pt-form::-webkit-scrollbar,
          .ptx-new-task-form::-webkit-scrollbar,
          .ptx-create-form::-webkit-scrollbar {
            width: 8px;
          }

          #ptTaskList::-webkit-scrollbar-track,
          #pt-list::-webkit-scrollbar-track,
          .ptx-task-list::-webkit-scrollbar-track,
          .ptx-tasks-list::-webkit-scrollbar-track,
          .pt-task-list::-webkit-scrollbar-track,
          .ptx-task-comments::-webkit-scrollbar-track,
          .ptx-comments-list::-webkit-scrollbar-track,
          .pt-comments-list::-webkit-scrollbar-track,
          #pt-form::-webkit-scrollbar-track,
          .ptx-new-task-form::-webkit-scrollbar-track,
          .ptx-create-form::-webkit-scrollbar-track {
            background: transparent;
          }

          #ptTaskList::-webkit-scrollbar-thumb,
          #pt-list::-webkit-scrollbar-thumb,
          .ptx-task-list::-webkit-scrollbar-thumb,
          .ptx-tasks-list::-webkit-scrollbar-thumb,
          .pt-task-list::-webkit-scrollbar-thumb,
          .ptx-task-comments::-webkit-scrollbar-thumb,
          .ptx-comments-list::-webkit-scrollbar-thumb,
          .pt-comments-list::-webkit-scrollbar-thumb,
          #pt-form::-webkit-scrollbar-thumb,
          .ptx-new-task-form::-webkit-scrollbar-thumb,
          .ptx-create-form::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
          }

          #ptTaskList::-webkit-scrollbar-thumb:hover,
          #pt-list::-webkit-scrollbar-thumb:hover,
          .ptx-task-list::-webkit-scrollbar-thumb:hover,
          .ptx-tasks-list::-webkit-scrollbar-thumb:hover,
          .pt-task-list::-webkit-scrollbar-thumb:hover,
          .ptx-task-comments::-webkit-scrollbar-thumb:hover,
          .ptx-comments-list::-webkit-scrollbar-thumb:hover,
          .pt-comments-list::-webkit-scrollbar-thumb:hover,
          #pt-form::-webkit-scrollbar-thumb:hover,
          .ptx-new-task-form::-webkit-scrollbar-thumb:hover,
          .ptx-create-form::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
          }

          /* Mobile fix */
          @media (max-width: 768px) {
            #pt-drawer,
            #ptDrawer,
            .pt-drawer {
              width: 100vw !important;
              max-width: 100vw !important;
            }

            #pt-drawer .drawer-body,
            #ptDrawer .drawer-body,
            .pt-drawer .drawer-body {
              height: calc(100vh - 64px) !important;
              max-height: calc(100vh - 64px) !important;
            }

            #ptTaskList,
            #pt-list,
            .ptx-task-list,
            .ptx-tasks-list,
            .pt-task-list {
              max-height: none !important;
            }

            .ptx-task-comments,
            .ptx-comments-list,
            .pt-comments-list {
              max-height: 180px !important;
            }
          }

          #ptTaskList,
        #pt-list,
        .ptx-task-list,
        .ptx-tasks-list,
        .pt-task-list {
          flex: 1 1 auto !important;
          min-height: 0 !important;
          overflow-y: auto !important;
        }
      /* =========================================================
         Aufgabe Comments Collapse + Scroll Fix
         Replace the previous .ptx-task-details CSS with this
         ========================================================= */

      /* Default: collapsed */
      .ptx-task-card .ptx-task-details {
        display: none !important;
        max-height: 0 !important;
        overflow: hidden !important;
        margin-top: 0 !important;
        padding: 0 !important;
        border: 0 !important;
      }

      /* Open: visible + scrollable */
      .ptx-task-card.is-comments-open .ptx-task-details,
      .ptx-task-card.is-open .ptx-task-details {
        display: block !important;
        max-height: 460px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        margin-top: 14px !important;
        padding: 12px !important;
        border-top: 1px dashed #dbeafe !important;
        background: #f8fafc !important;
        border-radius: 18px !important;
        -webkit-overflow-scrolling: touch !important;
        overscroll-behavior: contain !important;
      }

      /* Task card must not clip the opened details */
      .ptx-task-card,
      .pt-task-card,
      .pt-list-item {
        overflow: visible !important;
        max-height: none !important;
      }

      /* Comment list scrolls inside opened details */
      .ptx-task-card.is-comments-open .ptx-comment-list,
      .ptx-task-card.is-open .ptx-comment-list {
        display: flex !important;
        flex-direction: column !important;
        gap: 10px !important;
        max-height: 260px !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding: 4px 8px 4px 2px !important;
        margin-top: 10px !important;
      }

      /* Comment form stays clean */
      .ptx-task-card.is-comments-open .ptx-comment-form,
      .ptx-task-card.is-open .ptx-comment-form {
        background: #ffffff !important;
        border: 1px solid #dbeafe !important;
        border-radius: 16px !important;
        padding: 10px !important;
        margin-bottom: 10px !important;
      }

      /* Rotate collapse icon */
      .ptx-task-card.is-comments-open [data-pt-comments-toggle] svg,
      .ptx-task-card.is-open [data-pt-comments-toggle] svg {
        transform: rotate(180deg);
      }

      [data-pt-comments-toggle] svg {
        transition: transform .18s ease;
      }

      /* Scrollbar */
      .ptx-task-details::-webkit-scrollbar,
      .ptx-comment-list::-webkit-scrollbar {
        width: 8px;
      }

      .ptx-task-details::-webkit-scrollbar-thumb,
      .ptx-comment-list::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 999px;
      }

      .ptx-task-details::-webkit-scrollbar-thumb:hover,
      .ptx-comment-list::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
      }

      @media (max-width: 768px) {
        .ptx-task-card.is-comments-open .ptx-task-details,
        .ptx-task-card.is-open .ptx-task-details {
          max-height: 360px !important;
        }

        .ptx-task-card.is-comments-open .ptx-comment-list,
        .ptx-task-card.is-open .ptx-comment-list {
          max-height: 210px !important;
        }
      }

          </style>

          <style>
              /* ===== Kanban recent changes: collapsed by default ===== */
.kb-recent-toggle-wrap {
  position: sticky;
  top: 8px;
  z-index: 45;
  display: flex;
  justify-content: flex-end;
  margin: 0 0 10px;
  pointer-events: none;
}

.kb-recent-toggle {
  pointer-events: auto;
  border: 1px solid #dbeafe;
  background: #ffffff;
  color: #0f172a;
  border-radius: 999px;
  min-height: 38px;
  padding: 0 13px;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-size: 12px;
  font-weight: 950;
  box-shadow: 0 12px 30px rgba(15, 23, 42, .10);
  transition: transform .16s ease, box-shadow .16s ease, background .16s ease;
}

.kb-recent-toggle:hover {
  transform: translateY(-1px);
  background: #f8fafc;
  box-shadow: 0 16px 38px rgba(15, 23, 42, .14);
}

.kb-recent-toggle-count {
  min-width: 22px;
  height: 22px;
  border-radius: 999px;
  background: #93c21c;
  color: #ffffff;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 11px;
  font-weight: 950;
}

.kb-recent-toggle.is-hot {
  animation: kbRecentToggleHot 1.1s ease-in-out 3;
}

.kb-recent-toggle.is-stage-lead { box-shadow: 0 0 0 3px rgba(116, 178, 212, .18), 0 14px 34px rgba(15,23,42,.12); }
.kb-recent-toggle.is-stage-offer { box-shadow: 0 0 0 3px rgba(245, 158, 11, .20), 0 14px 34px rgba(15,23,42,.12); }
.kb-recent-toggle.is-stage-follow_up { box-shadow: 0 0 0 3px rgba(139, 92, 246, .18), 0 14px 34px rgba(15,23,42,.12); }
.kb-recent-toggle.is-stage-accepted { box-shadow: 0 0 0 3px rgba(34, 197, 94, .20), 0 14px 34px rgba(15,23,42,.12); }
.kb-recent-toggle.is-stage-deal { box-shadow: 0 0 0 3px rgba(147, 194, 28, .22), 0 14px 34px rgba(15,23,42,.12); }
.kb-recent-toggle.is-stage-project { box-shadow: 0 0 0 3px rgba(99, 102, 241, .20), 0 14px 34px rgba(15,23,42,.12); }
.kb-recent-toggle.is-stage-completed { box-shadow: 0 0 0 3px rgba(22, 163, 74, .22), 0 14px 34px rgba(15,23,42,.12); }
.kb-recent-toggle.is-stage-archive { box-shadow: 0 0 0 3px rgba(100, 116, 139, .18), 0 14px 34px rgba(15,23,42,.12); }
.kb-recent-toggle.is-stage-junk { box-shadow: 0 0 0 3px rgba(220, 38, 38, .18), 0 14px 34px rgba(15,23,42,.12); }

.kb-recent-panel {
  position: sticky;
  top: 54px;
  z-index: 44;
  display: none;
  margin: 0 0 14px;
  padding: 10px;
  border: 1px solid #dbeafe;
  border-radius: 18px;
  background: linear-gradient(135deg, rgba(255,255,255,.97), rgba(248,250,252,.97));
  box-shadow: 0 18px 45px rgba(15, 23, 42, .12);
  backdrop-filter: blur(10px);
}

.kb-recent-panel.is-open {
  display: block;
  animation: kbRecentPanelIn .18s ease both;
}

.kb-recent-panel-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 8px;
}

.kb-recent-panel-title {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  color: #0f172a;
  font-size: 13px;
  font-weight: 950;
}

.kb-recent-panel-title i {
  color: #93c21c;
}

.kb-recent-close {
  border: 0;
  background: #f1f5f9;
  color: #334155;
  width: 30px;
  height: 30px;
  border-radius: 11px;
  cursor: pointer;
}

.kb-recent-list {
  display: flex;
  gap: 8px;
  overflow-x: auto;
  padding-bottom: 2px;
}

.kb-recent-item {
  min-width: 300px;
  max-width: 410px;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 11px;
  border: 1px solid #e2e8f0;
  border-left: 5px solid var(--kb-recent-color, #93c21c);
  border-radius: 16px;
  background: #ffffff;
  box-shadow: 0 10px 26px rgba(15, 23, 42, .08);
  animation: kbRecentItemIn .22s ease both;
}

.kb-recent-item.is-new {
  animation: kbRecentItemIn .22s ease both, kbRecentItemGlow 1.25s ease-in-out 2;
}

.kb-recent-dot {
  width: 36px;
  height: 36px;
  border-radius: 14px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex: 0 0 auto;
  background: #eef7fb;
  color: #0f172a;
  font-size: 12px;
  font-weight: 950;
}

.kb-recent-body {
  min-width: 0;
  flex: 1;
}

.kb-recent-customer {
  color: #0f172a;
  font-size: 13px;
  font-weight: 950;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.kb-recent-route {
  margin-top: 3px;
  color: #64748b;
  font-size: 11px;
  font-weight: 850;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.kb-recent-route strong {
  color: #0f172a;
}

.kb-recent-time {
  margin-top: 3px;
  color: #94a3b8;
  font-size: 10px;
  font-weight: 850;
}

.kb-recent-jump {
  width: 34px;
  height: 34px;
  border: 0;
  border-radius: 13px;
  background: var(--kb-recent-color, #93c21c);
  color: #ffffff;
  cursor: pointer;
  box-shadow: 0 8px 18px rgba(15, 23, 42, .16);
}

.kb-recent-jump:hover {
  filter: brightness(.94);
}

.card.kb-live-moving {
  opacity: .65;
  transform: scale(.985);
}

.card.kb-live-changed {
  position: relative;
  z-index: 20 !important;
  animation: kbLiveChangedPulse 1.1s ease-in-out 3;
  box-shadow: 0 0 0 4px var(--kb-live-soft, rgba(147,194,28,.24)), 0 24px 55px rgba(15, 23, 42, .18) !important;
}

.card.kb-live-changed::before {
  content: "Gerade geändert";
  position: absolute;
  top: -12px;
  right: 12px;
  z-index: 4;
  padding: 5px 9px;
  border-radius: 999px;
  background: var(--kb-live-color, #93c21c);
  color: #fff;
  font-size: 10px;
  font-weight: 950;
  box-shadow: 0 8px 18px rgba(15, 23, 42, .18);
}

@keyframes kbRecentToggleHot {
  0%, 100% { transform: translateY(0) scale(1); }
  45% { transform: translateY(-2px) scale(1.035); }
}

@keyframes kbRecentPanelIn {
  from { opacity: 0; transform: translateY(-7px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes kbRecentItemIn {
  from { opacity: 0; transform: translateX(-10px) scale(.98); }
  to { opacity: 1; transform: translateX(0) scale(1); }
}

@keyframes kbRecentItemGlow {
  0%, 100% { box-shadow: 0 10px 26px rgba(15, 23, 42, .08); }
  50% { box-shadow: 0 0 0 4px rgba(147, 194, 28, .20), 0 18px 38px rgba(15, 23, 42, .14); }
}

@keyframes kbLiveChangedPulse {
  0%, 100% { transform: scale(1); }
  50% { transform: scale(1.026); }
}

          </style>
@endsection


@section('content')
@php
$canManageKanbanLeadStages = auth()->check() && \App\Models\UserRoll::query()
  ->where(function ($q) {
    $q->where('user_id', auth()->id());

    if (auth()->user() && auth()->user()->name) {
      $q->orWhere('user_id', auth()->user()->name);
    }
  })
  ->where('item_id', 'Administrator')
  ->where(function ($q) {
    $q->where('is_read', true)
      ->orWhere('is_add', true)
      ->orWhere('is_update', true)
      ->orWhere('is_delete', true);
  })
  ->exists();
@endphp

@php
$leadStageNamesForJs = $stageNames ?? [
  'lead' => 'Lead',
  'offer' => 'Angebot',
  'follow_up' => 'Nachfassen',
  'accepted' => 'Annehmen',
  'deal' => 'Auftrag',
  'project' => 'Montage',
  'completed' => 'Abschluss',
  'archive' => 'Archive',
  'junk' => 'Junk',
];

$leadStageMetaForJs = $stageMeta ?? [];

$kanbanStageNamesForJs = collect($leadStageNamesForJs)
  ->reject(fn($label, $key) => in_array(strtolower((string) $key), ['junk', 'ticket'], true))
  ->toArray();

$kanbanStageMetaForJs = collect($leadStageMetaForJs)
  ->reject(fn($meta, $key) => in_array(strtolower((string) $key), ['junk', 'ticket'], true))
  ->toArray();

$kanbanProductsForJs = collect($products ?? [])
  ->map(fn($product) => [
    'id' => $product->id ?? null,
    'name' => $product->article_group ?? $product->initial ?? ('Produkt #' . ($product->id ?? '')),
    'initial' => $product->initial ?? null,
  ])
  ->filter(fn($product) => !empty($product['id']))
  ->values()
  ->toArray();
@endphp

@php
$branchAddresses = $branchAddresses ?? collect();
$kanbanFilterSettings = $kanbanFilterSettings ?? collect();
@endphp
<div class="app-content"> 
  <div class="content-wrapper">   
     {{-- ======= MAIN SHELL ======= --}}
  <section id="basic-tabs-components">


<div class="pro-layout">
        <div class="pro-main">
            <div class="row">
                <div class="col-sm-12">

                    {{-- ===== Saved Kanban Filter Bar ===== --}}
                    <div class="kb-filter-mode-bar" id="kbFilterModeBar">
                        <div class="kb-filter-mode-copy">
                            <span class="kb-filter-mode-icon"><i class="feather icon-filter"></i></span>
                            <div>
                                <strong>Möchtest du meine Filter sehen oder alle Kunden?</strong> 
                            </div>
                        </div>

                        <div class="kb-filter-mode-actions">
                            <button type="button" class="kb-filter-mode-btn is-active" data-kb-filter-scope="all">
                                <i class="feather icon-users"></i>
                                Alle Kunden
                            </button>

                            <button type="button" class="kb-filter-mode-btn" data-kb-filter-scope="mine">
                                <i class="feather icon-user-check"></i>
                                Meine Filter
                            </button>

                            <select id="kbSavedFilterSelect" class="form-control kb-saved-filter-select">
                                <option value="">Gespeicherten Filter wählen…</option>
                                @foreach(($kanbanFilterSettings ?? collect()) as $filterSetting)
                                  <option value="{{ $filterSetting->id }}"
                                          data-default="{{ $filterSetting->is_default ? 1 : 0 }}"
                                          data-filters='@json($filterSetting->filters ?? [])'>
                                      {{ $filterSetting->is_default ? '★ ' : '' }}{{ $filterSetting->name }}
                                  </option>
                                @endforeach
                            </select>

                            <button type="button" class="kb-filter-mode-btn kb-filter-save-btn" id="kbSaveCurrentFilter">
                                <i class="feather icon-save"></i>
                                Filter speichern
                            </button>

                            <div class="kb-filter-menu-wrap" id="kbSavedFilterMenuWrap">
                                <button type="button"
                                        class="kb-filter-mode-btn kb-filter-menu-trigger"
                                        id="kbSavedFilterMenuBtn"
                                        disabled>
                                    <i class="feather icon-more-vertical"></i>
                                    Aktionen
                                    <i class="feather icon-chevron-down kb-filter-menu-chevron"></i>
                                </button>

                                <div class="kb-filter-menu" id="kbSavedFilterMenu" aria-hidden="true">
                                    <button type="button" class="kb-filter-menu-item" id="kbUpdateCurrentFilter" disabled>
                                        <span><i class="feather icon-refresh-cw"></i> Aktualisieren</span>
                                        <small>Aktuellen Filter überschreiben</small>
                                    </button>

                                    <button type="button" class="kb-filter-menu-item" id="kbRenameCurrentFilter" disabled>
                                        <span><i class="feather icon-edit-2"></i> Umbenennen</span>
                                        <small>Name des Filters ändern</small>
                                    </button>

                                    <button type="button" class="kb-filter-menu-item" id="kbSetDefaultFilter" disabled>
                                        <span><i class="feather icon-star"></i> Als Standard setzen</span>
                                        <small>Wird als bevorzugter Filter markiert</small>
                                    </button>

                                    <div class="kb-filter-menu-divider"></div>

                                    <button type="button" class="kb-filter-menu-item is-danger" id="kbDeleteCurrentFilter" disabled>
                                        <span><i class="feather icon-trash-2"></i> Löschen</span>
                                        <small>Diesen Filter dauerhaft entfernen</small>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Top Tabs Header --}}
                    <div class="pro-tabs-shell">
                        <div class="pro-tabs-topbar">

                            {{-- Navigation Tabs --}}
                            <ul class="nav nav-tabs pro-tabs-nav" role="tablist">

                                {{-- Kanban --}}
                                <li class="nav-item">
                                    <a class="nav-link active"
                                       id="home-tab"
                                       data-toggle="tab"
                                       href="#home"
                                       role="tab"
                                       aria-controls="home"
                                       aria-selected="true">
                                        <span class="pro-tab-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <rect x="3" y="3" width="7" height="7" rx="2"></rect>
                                                <rect x="14" y="3" width="7" height="7" rx="2"></rect>
                                                <rect x="3" y="14" width="7" height="7" rx="2"></rect>
                                                <rect x="14" y="14" width="7" height="7" rx="2"></rect>
                                            </svg>
                                        </span>

                                        <span class="pro-tab-text">
                                            <span class="pro-tab-title">
                                                Kanban
                                                <span class="pro-tab-count" id="tabCountKanban">
                                                    {{ $tabCounts['kanban'] ?? 0 }}
                                                </span>
                                            </span>
                                            <span class="pro-tab-sub">Board Ansicht</span>
                                        </span>
                                    </a>
                                </li>

                                {{-- Liste --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                       id="profile-tab"
                                       data-toggle="tab"
                                       href="#profile"
                                       role="tab"
                                       aria-controls="profile"
                                       aria-selected="false">
                                        <span class="pro-tab-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <line x1="8" y1="6" x2="21" y2="6"></line>
                                                <line x1="8" y1="12" x2="21" y2="12"></line>
                                                <line x1="8" y1="18" x2="21" y2="18"></line>
                                                <circle cx="4" cy="6" r="1.5"></circle>
                                                <circle cx="4" cy="12" r="1.5"></circle>
                                                <circle cx="4" cy="18" r="1.5"></circle>
                                            </svg>
                                        </span>

                                        <span class="pro-tab-text">
                                            <span class="pro-tab-title">
                                                Liste
                                                <span class="pro-tab-count" id="tabCountList">
                                                    {{ $tabCounts['list'] ?? 0 }}
                                                </span>
                                            </span>
                                            <span class="pro-tab-sub">Tabellen Ansicht</span>
                                        </span>
                                    </a>
                                </li>

                                {{-- Junk --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                       id="junk-tab"
                                       data-toggle="tab"
                                       href="#junk"
                                       role="tab"
                                       aria-controls="junk"
                                       aria-selected="false">
                                        <span class="pro-tab-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </span>

                                        <span class="pro-tab-text">
                                            <span class="pro-tab-title">
                                                Junk
                                                <span class="pro-tab-count" id="tabCountJunk">
                                                    {{ $tabCounts['junk'] ?? 0 }}
                                                </span>
                                            </span>
                                            <span class="pro-tab-sub">Archiv / Junk</span>
                                        </span>
                                    </a>
                                </li>

                                {{-- Ticket --}}
                                <li class="nav-item">
                                    <a class="nav-link"
                                       id="ticket-tab"
                                       data-toggle="tab"
                                       href="#ticket"
                                       role="tab"
                                       aria-controls="ticket"
                                       aria-selected="false">
                                        <span class="pro-tab-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M3 9a3 3 0 0 0 0 6v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2a3 3 0 0 0 0-6V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2z"></path>
                                                <path d="M9 9h6"></path>
                                                <path d="M9 13h6"></path>
                                                <path d="M9 17h3"></path>
                                            </svg>
                                        </span>

                                        <span class="pro-tab-text">
                                            <span class="pro-tab-title">
                                                Ticket
                                                <span class="pro-tab-count" id="tabCountTicket">
                                                    {{ $tabCounts['ticket'] ?? 0 }}
                                                </span>
                                            </span>
                                            <span class="pro-tab-sub">Service Fälle</span>
                                        </span>
                                    </a>
                                </li>

                            </ul>

                            {{-- Sort Dropdown --}}
                            <div class="pro-sort-box">
                                <span class="pro-sort-icon" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M3 6h12"></path>
                                        <path d="M3 12h8"></path>
                                        <path d="M3 18h6"></path>
                                        <path d="M18 6v12"></path>
                                        <path d="M15 15l3 3 3-3"></path>
                                    </svg>
                                </span>

                                <label for="listSortSelect" class="pro-sort-label">
                                    Sortieren
                                </label>

                                <select id="listSortSelect" class="custom-select custom-select-sm pro-sort-select">
                                    <optgroup label="Datum">
                                        <option value="created_at|desc" selected>Datum (neueste zuerst)</option>
                                        <option value="created_at|asc">Datum (älteste zuerst)</option>
                                    </optgroup>

                                    <optgroup label="Zuletzt aktualisiert">
                                        <option value="updated_at|desc">Aktualisiert (neueste)</option>
                                        <option value="updated_at|asc">Aktualisiert (älteste)</option>
                                    </optgroup>

                                    <optgroup label="Kunde">
                                        <option value="customer_lastname|asc">Kunde (A-Z)</option>
                                        <option value="customer_lastname|desc">Kunde (Z-A)</option>
                                    </optgroup>

                                    <optgroup label="Ort">
                                        <option value="city|asc">Ort (A-Z)</option>
                                        <option value="city|desc">Ort (Z-A)</option>
                                    </optgroup>

                                    <optgroup label="Status">
                                        <option value="status|asc">Status (A-Z)</option>
                                        <option value="status|desc">Status (Z-A)</option>
                                    </optgroup>
                                </select>

                                <button type="button" class="lsm-btn lsm-btn--phase" id="btnOpenStageManager" title="Phasen verwalten">
                                    <span class="lsm-btn-icon"><i class="feather icon-sliders"></i></span>
                                    <span>Phasen</span>
                                </button>

                                @if($canManageKanbanLeadStages)
                                @endif

                                <button type="button" class="lsm-btn lsm-btn--filter" id="btnOpenDrawer" title="Übersicht & Filter">
                                    <span class="lsm-btn-icon"><i class="feather icon-filter"></i></span>
                                    <span>Filter</span>
                                    <span id="filterBadge" class="rail-badge d-none">0</span>
                                </button>
                            </div>

                        </div>
                    </div>

                    {{-- Tabs Content --}}
                    <div class="pro-tabs-content-card">
                        <div class="tab-content">

                            {{-- Kanban --}}
                            <div class="tab-pane show active"
                                 id="home"
                                 aria-labelledby="home-tab"
                                 role="tabpanel">
                                <div class="kanban-zoom-card">
                                    <div class="kanban-zoom-toolbar" aria-label="Kanban Anzeige">
                                        <div class="kanban-zoom-left">
                                            <span class="kanban-zoom-title">Kanban Ansicht</span>
                                            <span class="kanban-zoom-sub">Größe anpassen, damit mehr Spalten sichtbar sind</span>
                                        </div>
 

                                        <div class="kanban-zoom-actions">
                                            <button type="button" class="kbz-btn" data-kb-zoom="1">100%</button>
                                            <button type="button" class="kbz-btn" data-kb-zoom="0.9">90%</button>
                                            <button type="button" class="kbz-btn" data-kb-zoom="0.8">80%</button>
                                            <button type="button" class="kbz-btn" data-kb-zoom="0.7">70%</button>
                                            <button type="button" class="kbz-btn kbz-btn--ghost" id="kbZoomOutBtn" title="Eine Stufe kleiner">
                                                <i class="feather icon-zoom-out"></i>
                                            </button>
                                            <button type="button" class="kbz-btn kbz-btn--ghost" id="kbZoomInBtn" title="Eine Stufe größer">
                                                <i class="feather icon-zoom-in"></i>
                                            </button>
                                            <select id="kbColumnWidthSelect" class="kbz-select" title="Spaltenbreite">
                                                <option value="normal">Normal</option>
                                                <option value="compact">Schmal</option>
                                                <option value="wide">Breit</option>
                                            </select>
                                            <select id="kbPerColumnLimitSelect" class="kbz-select" title="Einträge pro Spalte">
                                                <option value="10">10 pro Spalte</option>
                                                <option value="20" selected>20 pro Spalte</option>
                                                <option value="30">30 pro Spalte</option>
                                                <option value="50">50 pro Spalte</option>
                                                <option value="80">80 pro Spalte</option>
                                            </select>
                                            <label class="kbz-compact-toggle">
                                                <input type="checkbox" id="kbCompactToggle">
                                                <span>Kompakt</span>
                                            </label>
                                            <label class="kbz-compact-toggle" title="Wenn aus, bleibt jede Spalte grün (#93c21c)">
                                                <input type="checkbox" id="kbUseStageColorsToggle">
                                                <span>Spaltenfarbe nutzen</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="kanban-zoom-area" id="kanbanZoomArea">
                                        <div id="kanban" class="kanban-container"></div>
                                    </div>
                                </div>
                            </div>

                            {{-- List --}}
                            <div class="tab-pane"
                                 id="profile"
                                 aria-labelledby="profile-tab"
                                 role="tabpanel">
                                <div class="table-responsive p-0">
                                    <table class="table pro-list-table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th class="sortable active desc" data-sort="created_at">
                                                    <span><i class="feather icon-calendar"></i> Datum</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="customer_lastname">
                                                    <span><i class="feather icon-user"></i> Kunde</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="city">
                                                    <span><i class="feather icon-map-pin"></i> Ort</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="product">
                                                    <span><i class="feather icon-box"></i> Produkt</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="employee">
                                                    <span><i class="feather icon-users"></i> Mitarbeiter</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="updated_at">
                                                    <span><i class="feather icon-activity"></i> Status</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                                <th class="sortable" data-sort="status">
                                                    <span><i class="feather icon-layers"></i> Phase</span>
                                                    <i class="feather icon-chevron-down sort-icon"></i>
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody id="kanbanTableBody">
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">
                                                    Lade Daten…
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <div id="listPagination" class="d-flex justify-content-center mt-3"></div>
                                </div>
                            </div>

                            {{-- Junk --}}
                            <div class="tab-pane"
                                 id="junk"
                                 aria-labelledby="junk-tab"
                                 role="tabpanel">
                                @include('admin.kanban.partials.junk', ['junk' => $junk])
                            </div>

                            {{-- Ticket --}}
                            <div class="tab-pane"
                                 id="ticket"
                                 aria-labelledby="ticket-tab"
                                 role="tabpanel">
                                @include('admin.kanban.partials.ticket', [
  'tickets' => $tickets ?? null,
  'total' => $tabCounts['ticket'] ?? 0
])
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>
</section>

    {{-- ======= SINGLE DRAWER: Übersicht & Filter ======= --}}
    <div class="drawer-backdrop" id="drawerBackdrop"></div>
    <aside class="drawer kb-filter-drawer" id="sideDrawer" role="dialog" aria-modal="true" aria-labelledby="drawerTitle">
      <div class="drawer-header">
        <div class="d-flex align-items-center">
          <i class="feather icon-sliders mr-2"></i>
          <h5 id="drawerTitle" class="mb-0">Übersicht &amp; Filter</h5>
          <span id="tabFilterCount" class="tab-badge-inline d-none ml-2">0</span>
        </div>
        <div class="d-flex align-items-center">
          <button class="btn btn-sm btn-outline-secondary mr-1" id="btnClearFilters"><i class="feather icon-rotate-ccw"></i> Alles löschen</button>
          <button class="btn btn-sm btn-primary" id="btnApplyFilters"><i class="feather icon-check-circle"></i> Anwenden</button>
          <button class="btn btn-sm btn-outline-secondary ml-1" data-close-drawer><i class="feather icon-x"></i></button>
        </div>
      </div>

      {{-- Chips summary of active filters --}}
      <div class="px-3 pt-2">
        <div id="activeFilterChips" class="chips"></div>
      </div>

      <div class="drawer-body">
        <!-- SUMMARY (top) -->
        <div id="view-summary" class="mb-1">
          <div class="row text-center" id="summaryStats" style="justify-content:center">
            <div id="cardEmployees" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Verantwortliche</strong>
                <div id="totalEmployees" class="h4">{{ $totalEmployees ?? 0 }}</div>
              </div>
            </div>
            <div id="cardProducts" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Produkt</strong>
                <div id="totalProduct" class="h4">{{ $totalProducts ?? 0 }}</div>
              </div>
            </div>
            <div id="cardCustomers" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Kunde</strong>
                <div id="totalCustomer" class="h4">{{ $totalCustomers ?? 0 }}</div>
              </div>
            </div>
            <div id="cardAnfragen" class="col-6 col-md-6 summary-card mb-1">
              <div class="border rounded py-2" style="border:1px solid #8fc63f!important">
                <strong class="text-primary">Nachfrage</strong>
                <div id="totalAnfrage" class="h4">{{ ($tabCounts['kanban'] ?? 0) }}</div>
              </div>
            </div>

            <div id="cardOffen" class="col-12 summary-card mb-2">
              <div class="border rounded py-2 bg-orange text-white" style="background:#f49f43;color:white!important;">
                <strong>Offen</strong>
                <div id="statusOffen" class="h4 text-white">
                  {{ $statusCounts['offen'] ?? 0 }} <small>({{ $statusPercentages['offen'] ?? 0 }}%)</small>
                </div>
              </div>
            </div>

            <div id="cardZusage" class="col-6 summary-card">
              <div class="border rounded py-2 bg-primary text-white">
                <strong>Zusage</strong>
                <div id="statusZusage" class="h4 text-white">
                  {{ $statusCounts['zusage'] ?? 0 }} <small>({{ $statusPercentages['zusage'] ?? 0 }}%)</small>
                </div>
              </div>
            </div>

            <div id="cardAbsage" class="col-6 summary-card">
              <div class="border rounded py-2 bg-danger text-white">
                <strong>Absage</strong>
                <div id="statusAbsage" class="h4 text-white">
                  {{ $statusCounts['absage'] ?? 0 }} <small>({{ $statusPercentages['absage'] ?? 0 }}%)</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <hr class="my-2">

        <!-- FILTER (below summary) -->
        <div id="view-filter">
          <form id="kanbanFilterForm" class="row align-items-end g-2">
            <div class="col-md-6">
              <label for="customerFilter" class="form-label d-flex align-items-center">
                Kunde <span class="badge badge-secondary ml-2 d-none" id="countCustomers">{{ $totalCustomers ?? 0 }}</span>
              </label>
              <select name="customer[]" id="customerFilter" class="form-control select2 multiple" multiple data-placeholder="Mehrere Kunden suchen…">
                {{-- Multiple Select2: leer lassen = Alle Kunden --}}
                @foreach ($customers as $customer)
                  <option value="{{ $customer->id }}">{{ $customer->name }} {{ $customer->lastname }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="stageFilter" class="form-label">Phase</label>
              <select name="stage" id="stageFilter" class="form-control select2 stage-color-select">
                <option value="">Alle Phasen</option>
                @foreach(($stageNames ?? []) as $key => $label)
                  @php
  $stageKey = strtolower((string) $key);
  $meta = $stageMeta[$key] ?? [];
  $color = $meta['color'] ?? '#93c21c';
  $icon = $meta['icon'] ?? 'circle';
                  @endphp
                  @if(!in_array($stageKey, ['junk', 'ticket'], true))
                    <option value="{{ $key }}" data-color="{{ $color }}" data-icon="{{ $icon }}">{{ $label }}</option>
                  @endif
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="leadAgeFilter" class="form-label">Lead-Alter (Farbe)</label>
              <select name="lead_age" id="leadAgeFilter" class="form-control select2">
                <option value="">Alle Zeiten</option>
                <option value="green">🟢 Neu (< 24h)</option>
                <option value="orange">🟠 Letzter Tag (24 - 48h)</option>
                <option value="red">🔴 Überfällig (> 48h)</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="branchFilter" class="form-label d-flex align-items-center">
                Unternehmen
                <span class="badge badge-secondary ml-2 d-none" id="countBranches">{{ count($branches ?? []) }}</span>
              </label>

              <select name="branch" id="branchFilter" class="form-control select2">
                <option value="">Alle Unternehmen</option>
                @foreach (($branches ?? []) as $b)
                  <option value="{{ $b->id }}" data-color="{{ $b->color ?? '#93c21c' }}">
                    {{ $b->branch }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="branchAddressFilter" class="form-label d-flex align-items-center">
                Unternehmensadresse
                <span class="badge badge-secondary ml-2 d-none" id="countBranchAddresses">{{ count($branchAddresses ?? []) }}</span>
              </label>

              <select name="branch_address" id="branchAddressFilter" class="form-control select2" data-placeholder="Unternehmensadresse wählen…">
                <option value="">Alle Adressen</option>
                @foreach (($branchAddresses ?? []) as $address)
                  @php
  $addressLabel = trim(($address->name ? $address->name . ' · ' : '') . ($address->full_address ?: trim(($address->street ?? '') . ', ' . ($address->postcode ?? '') . ' ' . ($address->city ?? ''))));
                  @endphp
                  <option value="{{ $address->id }}" data-branch-id="{{ $address->branch_id }}">
                    {{ $addressLabel ?: ('Adresse #' . $address->id) }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="employeeFilter" class="form-label d-flex align-items-center">
                Mitarbeiter <span class="badge badge-secondary ml-2 d-none" id="countEmployees">{{ $totalEmployees ?? 0 }}</span>
              </label>
             <select name="employee" id="employeeFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($employees as $employee)
                  <option value="{{ $employee->id }}">
                    {{ $employee->name }} {{ $employee->lastname }}
                  </option>
                @endforeach
              </select>

            </div>

            <div class="col-md-6">
              <label for="departmentFilter" class="form-label d-flex align-items-center">
                Abteilung <span class="badge badge-secondary ml-2 d-none" id="countDepartments">{{ $totalDepartments ?? 0 }}</span>
              </label>
              <select name="department" id="departmentFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($departments as $department)
                  <option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="productFilter" class="form-label d-flex align-items-center">
                Produkt <span class="badge badge-secondary ml-2 d-none" id="countProducts">{{ $totalProducts ?? 0 }}</span>
              </label>
              <select name="product" id="productFilter" class="form-control select2">
                <option value="">Alle</option>
                @foreach ($products as $product)
                  <option value="{{ $product->id }}">{{ $product->article_group }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label for="interestFilter" class="form-label">Interesse</label>
              <select name="interest" id="interestFilter" class="form-control select2">
                <option value="">Alle Interessen</option>
                <option value="interest">Kaufinteresse</option>
                <option value="intent">Kaufabsicht</option>
                <option value="option">Kaufoption</option>
              </select>
            </div>

            <div class="col-md-6">
              <label for="dateFrom" class="form-label">Von (Datum)</label>
              <input type="date" name="date_from" id="dateFrom" class="form-control" />
            </div>

            <div class="col-md-6">
              <label for="dateTo" class="form-label">Bis (Datum)</label>
              <input type="date" name="date_to" id="dateTo" class="form-control" />
            </div>


           <div class="col-12">
              <hr class="my-2">
              <label class="form-label mb-3 font-weight-bold text-dark">
                  <i class="feather icon-layout mr-1"></i> Spalten Sichtbarkeit
              </label>

              <div class="d-flex flex-wrap" id="columnTogglesContainer">
                  @foreach(($kanbanStageNamesForJs ?? ['lead' => 'Lead', 'offer' => 'Angebot', 'deal' => 'Auftrag', 'project' => 'Montage', 'completed' => 'Abschluss', 'archive' => 'Archiv']) as $key => $label)
                    <div class="custom-control custom-checkbox mr-3 mb-2">
                        <input type="checkbox" 
                              class="custom-control-input col-toggle-checkbox" 
                              id="toggleCol_{{ $key }}" 
                              value="{{ $key }}"
                              {{ $key !== 'archive' ? 'checked' : '' }}>

                        <label class="custom-control-label d-flex align-items-center" for="toggleCol_{{ $key }}" style="cursor: pointer; user-select: none;">
                            {{-- Icon for ON --}}
                            <span class="toggle-icon-on mr-1">
                                <i class="feather icon-eye text-success"></i>
                            </span>
                            {{-- Icon for OFF --}}
                            <span class="toggle-icon-off mr-1">
                                <i class="feather icon-eye-off text-muted"></i>
                            </span>

                            {{-- Text Label --}}
                            <span class="toggle-label-text">{{ $label }}</span>
                        </label>
                    </div>
                  @endforeach
              </div>
          </div>

            <div class="col-12 small text-muted mt-2">
              Tipp: <kbd>Enter</kbd> = Anwenden, <kbd>Esc</kbd> = Schließen.
            </div>
          </form>
        </div>
      </div>
    </aside>


    {{-- ======= UNDERPHASE SIDEBAR: opens when clicking Unterphasen of a Hauptphase ======= --}}
    <div class="kb-understage-sidebar-backdrop" id="kbUnderstageSidebarBackdrop" data-understage-close></div>
    <aside class="kb-understage-sidebar" id="kbUnderstageSidebar" aria-hidden="true">
      <div class="kb-understage-sidebar-head">
        <div>
          <div class="kb-understage-sidebar-title">
            <i class="feather icon-git-branch"></i>
            <span id="kbUnderstageSidebarTitle">Unterphasen</span>
          </div>
          <div class="kb-understage-sidebar-subtitle" id="kbUnderstageSidebarSubtitle">
            Hauptphase auswählen, um die Unterphasen hier zu sehen. Das Haupt-Kanban bleibt im Hintergrund unverändert.
          </div>
        </div>
        <div class="kb-understage-sidebar-actions">
          <button type="button" class="kb-understage-refresh" id="kbUnderstageRefresh">
            <i class="feather icon-refresh-cw"></i> Neu laden
          </button>
          <button type="button" class="kb-understage-close" data-understage-close>
            <i class="feather icon-x"></i> Schließen
          </button>
        </div>
      </div>
      <div class="kb-understage-sidebar-body">
        <div class="kb-understage-board" id="kbUnderstageBoard">
          <div class="kb-understage-sidebar-empty">Noch keine Hauptphase ausgewählt.</div>
        </div>
      </div>
    </aside>

    {{-- ======= DYNAMIC LEAD STAGE MANAGER MODAL ======= --}}
    <div id="leadStageManagerModal" class="lsm-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="leadStageManagerTitle">
      <div class="lsm-backdrop" data-lsm-close></div>
      <div class="lsm-panel" tabindex="-1">
        <div class="lsm-head">
          <div class="lsm-title">
            <span class="lsm-title-icon"><i class="feather icon-sliders"></i></span>
            <div>
              <h5 id="leadStageManagerTitle">Pipeline-Phasen verwalten</h5>
              <p>Standard-Phasen können umbenannt werden. Löschen ist nur möglich, wenn keine Daten darin sind.</p>
            </div>
          </div>
          <button type="button" class="btn btn-sm btn-outline-secondary" data-lsm-close aria-label="Schließen">
            <i class="feather icon-x"></i>
          </button>
        </div>

        <div class="lsm-body">
          <div class="lsm-grid">
            <div class="lsm-card">
              <div class="lsm-card-head">
                <strong id="lsmFormTitle">Neue Phase</strong>
                <button type="button" class="btn btn-sm btn-light border" id="lsmResetForm">
                  <i class="feather icon-refresh-cw"></i> Neu
                </button>
              </div>
              <div class="lsm-card-body">
                <form id="leadStageForm" class="lsm-form" autocomplete="off">
                  @csrf
                  <input type="hidden" id="lsmStageId">

                  <div class="lsm-form-group lsm-form-group--full">
                    <label>Name der Phase</label>
                    <input type="text" id="lsmStageName" class="form-control" maxlength="80" placeholder="z.B. Beratung" required>
                  </div>

                  <div class="lsm-form-grid">
                    <div class="lsm-form-group">
                      <label>Farbe</label>
                      <div class="lsm-color-input-wrap">
                        <input type="color" id="lsmStageColor" class="form-control" value="#74b2d4">
                        <span id="lsmStageColorText">#74b2d4</span>
                      </div>
                    </div>
                    <div class="lsm-form-group">
                      <label>Icon</label>
                      <select id="lsmStageIcon" class="form-control" style="width:100%"></select>
                    </div>
                  </div>

                  <div class="lsm-toggle-grid">
                    <label class="lsm-toggle-card" for="lsmStageActive">
                      <input type="checkbox" id="lsmStageActive" checked>
                      <span class="lsm-toggle-icon"><i class="feather icon-eye"></i></span>
                      <span>
                        <strong>Aktiv</strong>
                        <small>Phase im Board und Filter anzeigen</small>
                      </span>
                    </label>

                    <label class="lsm-toggle-card" for="lsmStageClosed">
                      <input type="checkbox" id="lsmStageClosed">
                      <span class="lsm-toggle-icon"><i class="feather icon-lock"></i></span>
                      <span>
                        <strong>Geschlossene Phase</strong>
                        <small>Zählt als abgeschlossen / beendet</small>
                      </span>
                    </label>
                  </div>

                  <button type="submit" class="btn btn-primary btn-block lsm-save-btn">
                    <i class="feather icon-save"></i> Speichern
                  </button>
                </form>

                 
              </div>
            </div>

            <div class="lsm-card">
              <div class="lsm-card-head">
                <strong>Alle Phasen</strong>
                <div class="d-flex align-items-center" style="gap:8px;">
                  <button type="button" class="btn btn-sm btn-outline-secondary" id="lsmReloadStages">
                    <i class="feather icon-refresh-cw"></i> Laden
                  </button>
                  <button type="button" class="btn btn-sm btn-outline-primary" id="lsmSaveOrder">
                    <i class="feather icon-list"></i> Sortierung speichern
                  </button>
                </div>
              </div>

              <div class="lsm-stage-row lsm-stage-head">
                <div><span class="lsm-drag-handle"><i class="feather icon-move"></i></span></div>
                <div>Phase</div>
                <div>Key</div>
                <div>Daten</div>
                <div>Aktion</div>
              </div>
              <div id="leadStagesList">
                <div class="lsm-empty">Phasen werden geladen…</div>
              </div>
            </div>
          </div>
        </div>


        {{-- ======= RELATED SUBSTAGE CONFIGURATION DRAWER INSIDE PHASE CONFIG ======= --}}
        <aside class="lsm-substage-drawer" id="lsmSubstageDrawer" aria-hidden="true">
          <div class="lsm-substage-head">
            <div>
              <h5 class="lsm-substage-title" id="lsmSubstageTitle">Unterphasen</h5>
              <div class="lsm-substage-subtitle" id="lsmSubstageSubtitle">
                Wähle eine Hauptphase, um die zugehörigen Unterphasen hier direkt zu konfigurieren.
              </div>
              <span class="lsm-substage-open-note"><i class="feather icon-info"></i> Direkt an der ausgewählten Phase konfigurieren</span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="lsmCloseSubstageDrawer" aria-label="Unterphasen schließen">
              <i class="feather icon-x"></i>
            </button>
          </div>

          <div class="lsm-substage-body">
            <div class="lsm-substage-create">
              <div class="lsm-substage-create-title">Neue Unterphase für ausgewählte Hauptphase</div>
              <input type="hidden" id="lsmSubstageStageId">
              <div class="lsm-substage-form-grid">
                <input type="text" id="lsmSubstageName" class="form-control" placeholder="Name, z.B. Rückruf vereinbart">
                <input type="text" id="lsmSubstageKey" class="form-control" placeholder="Key auto">
                <input type="color" id="lsmSubstageColor" class="form-control" value="#93c21c" title="Farbe">
                <input type="text" id="lsmSubstageIcon" class="form-control" value="list" placeholder="Icon">
              </div>
              <div class="d-flex align-items-center justify-content-between flex-wrap mt-2" style="gap:8px;">
                <label class="mb-0 small font-weight-bold text-muted d-inline-flex align-items-center" style="gap:6px;">
                  <input type="checkbox" id="lsmSubstageActive" checked> Aktiv
                </label>
                <button type="button" class="lsm-mini-btn primary" id="lsmCreateSubstage">
                  <i class="feather icon-plus"></i> Unterphase erstellen
                </button>
              </div>
            </div>

            <div class="lsm-substage-list-card">
              <div class="d-flex align-items-center justify-content-between mb-1" style="gap:8px;">
                <div class="lsm-substage-list-title mb-0">Unterphasen dieser Hauptphase</div>
                <button type="button" class="lsm-mini-btn blue" id="lsmSaveSubstageOrder">
                  <i class="feather icon-list"></i> Sortierung speichern
                </button>
              </div>
              <div id="lsmSubstageList">
                <div class="lsm-substage-empty">Noch keine Hauptphase ausgewählt.</div>
              </div>
            </div>
          </div>
        </aside>

        <div class="lsm-footer">
          <div class="small text-muted">
            Nach Änderungen wird die Seite automatisch neu geladen, damit Kanban-Spalten, Filter und Listen synchron bleiben.
          </div>
          <button type="button" class="btn btn-light border" data-lsm-close>Schließen</button>
        </div>
      </div>
    </div>


    {{-- Old kbsa stage-admin modal removed.
         The active/default-safe manager is #leadStageManagerModal above.
         Protected/default phases may be edited/hidden there; only deletion is protected. --}}



    <!-- Live Feed Modal Backdrop -->
      <div id="liveFeedModalBackdrop" class="lfm-backdrop" style="display:none;"></div>

      <!-- Live Feed Modal -->
      <div id="liveFeedModal"
          class="lfm-shell"
          role="dialog"
          aria-modal="true"
          aria-labelledby="liveFeedModalTitle"
          style="display:none;">

        <div class="lfm-header">
          <div>
            <h3 id="liveFeedModalTitle" class="lfm-title">Aktivitäten</h3>
            <div class="lfm-subtitle" id="liveFeedModalSubtitle">Kunde</div>
          </div>

          <div class="lfm-header-right">
            <div class="lfm-filters" id="liveFeedTypeFilters">
              <button type="button" class="lfm-filter-btn is-active" data-type="all">
                Alle
              </button>
              <button type="button" class="lfm-filter-btn" data-type="task">
                Aufgaben
              </button>
              <button type="button" class="lfm-filter-btn" data-type="ticket">
                Tickets
              </button>
            </div>

            <button type="button"
                    class="lfm-icon-btn"
                    id="liveFeedModalClose"
                    aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </div>
        </div>

        <div class="lfm-body">
          <div class="lfm-meta">
            <span class="lfm-pill" id="liveFeedModalCount">0 Einträge</span>
            <span class="lfm-pill muted">
              <i class="feather icon-clock"></i>
              nach Nähe zu jetzt sortiert
            </span>
          </div>

          <div class="lfm-list" id="liveFeedModalList">
            <!-- Dynamisch gefüllt -->
          </div>
        </div>
      </div>

 

      <!-- Lead History Drawer -->
      <div id="lh-drawer" class="lh-root" aria-hidden="true" role="dialog" aria-labelledby="lh-title">
        <div class="lh-backdrop" data-lh-close></div>

        <aside class="lh-panel" tabindex="-1">
          <header class="lh-header">
            <h5 id="lh-title" class="mb-0">
              <i class="feather icon-activity mr-2"></i>
              <span id="lh-title-text">Verlauf</span>
            </h5>
            <button class="btn btn-sm btn-outline-secondary" data-lh-close aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </header>

          <section class="lh-body">
            <div class="row no-gutters">
              <div class="col-lg-7 pr-lg-2 border-right">
                <div class="p-3">
                  <h6 class="text-muted mb-3"><i class="feather icon-trending-up mr-1"></i> Phasenverlauf</h6>
                  <ul id="lh-timeline" class="lh-timeline list-unstyled mb-0"></ul>
                </div>
              </div>
              <div class="col-lg-5 pl-lg-2">
                <div class="p-3">
                  <h6 class="text-muted mb-3"><i class="feather icon-list mr-1"></i> Aktivitäten & Notizen</h6>
                  <div id="lh-activities" class="lh-list list-group"></div>
                </div>
              </div>
            </div>
          </section>
        </aside>
      </div>
 
      <div id="notesBackdrop" class="notes-backdrop"></div>
      <aside id="notesDrawer"
             class="notes-drawer kb-customer-panel-drawer"
             role="dialog"
             aria-modal="true"
             aria-labelledby="notesTitle"
             data-customer-id=""
             data-alternative-id=""
             data-product-id=""
             data-lead-product-list-id="">
        <div class="notes-head kb-cp-head">
          <div class="notes-title kb-cp-title">
            <span class="kb-cp-head-icon">
              <i class="feather icon-message-square"></i>
            </span>
            <div>
              <div class="kb-cp-title-line">
                <span id="notesTitle">Kunden Kommunikation</span>
                <span id="notesCountBadge" class="badge badge-secondary" data-count="0">0</span>
              </div>
              <div class="kb-cp-subtitle">Notizen, Kunden Bericht und Termin Bericht an einem Ort.</div>
            </div>
          </div>

          <div class="kb-cp-head-actions">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-notes-close aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </div>
        </div>

        <div class="notes-tabs kb-cp-tabs">
          <button type="button" class="notes-tab notes-tab--active" data-notes-tab="notes" aria-selected="true">
            <i class="feather icon-message-square mr-25"></i>
            Notizen
            <span id="tabBadgeNotes" class="badge badge-light ml-25">0</span>
          </button>

          <button type="button" class="notes-tab" data-notes-tab="customerReport" aria-selected="false">
            <i class="feather icon-file-text mr-25"></i>
            Kunden Bericht
            <span id="tabBadgeCustomerReport" class="badge badge-light ml-25">0</span>
          </button>

          <button type="button" class="notes-tab" data-notes-tab="report" aria-selected="false">
            <i class="feather icon-calendar mr-25"></i>
            Termin Bericht
            <span id="tabBadgeTerminReport" class="badge badge-light ml-25">0</span>
          </button>
        </div>

        <div class="notes-body kb-cp-body">
          <div id="notesList" class="kb-note-chat" data-notes-panel="notes" aria-live="polite">
            <div class="kb-empty-state">
              Notizen werden geladen…
            </div>
          </div>

          <div id="customerReportList" class="d-none" data-notes-panel="customerReport">
            <div class="kb-panel-header">
              <div>
                <div class="kb-panel-header-title">
                  <span class="kb-panel-icon"><i class="feather icon-file-text"></i></span>
                  <span>Kunden Bericht</span>
                </div>
                <div class="kb-panel-header-sub">
                  Aktuelle Berichte zu diesem Kunden, Objekt und Produkt. Neueste Berichte werden oben angezeigt.
                </div>
              </div>

              <button type="button" class="btn btn-primary kb-btn-brand kb-new-customer-report">
                <i class="feather icon-plus"></i>
                Bericht hinzufügen
              </button>
            </div>

            <div id="kbCustomerReportContent">
              <div class="kb-empty-state">Kunden Bericht wird geladen…</div>
            </div>
          </div>

          <div id="notesReport" class="d-none" data-notes-panel="report">
            <div class="kb-panel-header">
              <div>
                <div class="kb-panel-header-title">
                  <span class="kb-panel-icon"><i class="feather icon-calendar"></i></span>
                  <span>Termin Bericht</span>
                </div>
                <div class="kb-panel-header-sub">
                  Termine werden als Kalender-Klappkarten angezeigt. Jeder Termin zeigt, ob ein Bericht vorhanden ist.
                  <span id="kbTerminOpenInfo" class="ml-50"></span>
                </div>
              </div>
            </div>

            <div id="kbTerminReportContent">
              <div class="kb-empty-state">Termin Bericht wird geladen…</div>
            </div>
          </div>
        </div>

        <div class="notes-foot kb-note-composer-foot">
          <form id="notesForm" class="notes-composer kb-chatgpt-composer" autocomplete="off">
            <div class="kb-chatgpt-editor-wrap">
              <div id="noteEditor" class="notes-quill kb-chatgpt-editor"></div>
              <input type="hidden" id="noteText">
            </div>

            <button class="btn btn-primary kb-chatgpt-send" type="submit" title="Notiz senden">
              <i class="feather icon-send"></i>
            </button>
          </form>

          <input type="hidden" id="notesCustomerId">
          <input type="hidden" id="notesAlternativeId">
          <input type="hidden" id="notesProductId">
          <input type="hidden" id="notesLeadProductListId">
        </div>
      </aside>

      <div id="kbReportModalBackdrop" class="kb-report-modal-backdrop" aria-hidden="true">
        <div class="kb-report-modal" role="dialog" aria-modal="true" aria-labelledby="kbReportModalTitleText">
          <div class="kb-report-modal-head">
            <div class="kb-report-modal-title">
              <span id="kbReportModalIcon"><i class="feather icon-file-text"></i></span>
              <div>
                <div id="kbReportModalTitleText">Bericht hinzufügen</div>
                <small class="text-muted">Speichern wird direkt mit dem Kanban Customer Panel verbunden.</small>
              </div>
            </div>

            <button type="button" class="kb-report-modal-close" data-kb-report-close aria-label="Schließen">
              <i class="feather icon-x"></i>
            </button>
          </div>

          <form id="kbReportForm" autocomplete="off">
            @csrf
            <div class="kb-report-modal-body">
              <div id="kbReportAppointmentInfo" class="alert alert-info" hidden></div>

              <div class="kb-form-grid">
                <div class="kb-form-group">
                  <label>Titel</label>
                  <input type="text" name="title" class="kb-form-control" placeholder="Titel schreiben…">
                </div>

                <div class="kb-form-group">
                  <label>Datum</label>
                  <input type="date" name="report_date" class="kb-form-control" value="{{ now()->toDateString() }}">
                </div>
              </div>

              <div class="kb-form-grid">
                <div class="kb-form-group">
                  <label>Phase</label>
                  <select name="stage" class="kb-form-control">
                    <option value="">Phase wählen…</option>
                    <option value="lead">Lead</option>
                    <option value="offer">Angebot</option>
                    <option value="deal">Auftrag</option>
                    <option value="project">Montage</option>
                    <option value="completed">Abschluss</option>
                    <option value="Kunden Bericht">Kunden Bericht</option>
                  </select>
                </div>

                <div class="kb-form-group">
                  <label>Typ</label>
                  <input type="text" name="type" class="kb-form-control" value="Kunden Bericht" readonly>
                </div>
              </div>

              <div class="kb-form-group">
                <label>Bericht *</label>
                <textarea name="report" class="kb-form-control" rows="6" placeholder="Bericht schreiben…" required></textarea>
              </div>

              <div class="kb-form-grid">
                <div class="kb-form-group">
                  <label>Nächster Schritt</label>
                  <textarea name="next_step" class="kb-form-control" rows="3" placeholder="Optional, besonders für Termin Bericht…"></textarea>
                </div>

                <div class="kb-form-group">
                  <label>Fällig am</label>
                  <input type="date" name="due_date" class="kb-form-control">
                </div>
              </div>
            </div>

            <div class="kb-report-modal-foot">
              <button type="button" class="btn btn-outline-secondary" data-kb-report-close>Abbrechen</button>
              <button type="submit" class="btn btn-primary kb-btn-brand" data-kb-report-submit>
                <i class="feather icon-save"></i>
                Speichern
              </button>
            </div>
          </form>
        </div>
      </div>


      {{-- ======= AUFGABE SIDEBAR: Lucide two-tab task drawer ======= --}}
<div id="pt-backdrop" class="notes-backdrop pt-panel-backdrop" data-pt-close></div>

<aside id="pt-drawer"
       class="notes-drawer pt-panel-drawer pt-lucide-drawer"
       role="dialog"
       aria-modal="true"
       aria-labelledby="pt-title"
       data-customer-id=""
       data-alternative-id=""
       data-product-id=""
       data-lead-product-list-id="">

  <div class="ptx-head">
    <div class="ptx-head-left">
      <div class="ptx-head-icon"><i data-lucide="clipboard-check"></i></div>
      <div class="ptx-head-copy">
        <div class="ptx-kicker">Kundenbezogene Aufgaben</div>
        <div class="ptx-title-row">
          <h2 id="pt-title">Aufgaben</h2>
          <span id="pt-count" class="ptx-count">0</span>
        </div>
        <p>Aufgaben, Personal Task Keys, Mitarbeiter, Datum und Kommentare in einer sauberen Listenansicht.</p>
      </div>
    </div>

    <div class="ptx-head-actions">
      <button type="button" class="ptx-icon-btn" id="pt-refresh" title="Aufgaben neu laden">
        <i data-lucide="refresh-cw"></i>
      </button>
      <button type="button" class="ptx-icon-btn" data-pt-close title="Schließen">
        <i data-lucide="x"></i>
      </button>
    </div>
  </div>

  <div class="ptx-context-strip">
    <div class="ptx-context-card is-customer">
      <span><i data-lucide="user-round"></i></span>
      <div>
        <small>Kunde</small>
        <strong id="ptContextCustomer">Aktueller Kunde</strong>
      </div>
    </div>

    <div class="ptx-context-card is-product">
      <span><i data-lucide="package"></i></span>
      <div>
        <small>Produkt</small>
        <strong id="ptContextProduct">Produkt</strong>
      </div>
    </div>

    <div class="ptx-context-card is-object">
      <span><i data-lucide="map-pin"></i></span>
      <div>
        <small>Objekt</small>
        <strong id="ptContextObject">Objekt</strong>
      </div>
    </div>
  </div>

  <div class="ptx-tabs" role="tablist" aria-label="Aufgabe Tabs">
    <button type="button" class="ptx-tab is-active" data-pt-tab="tasks" role="tab" aria-selected="true">
      <i data-lucide="list-checks"></i>
      <span>Aufgaben</span>
    </button>
    <button type="button" class="ptx-tab" data-pt-tab="create" role="tab" aria-selected="false">
      <i data-lucide="plus-circle"></i>
      <span>Neue Aufgabe</span>
    </button>
  </div>

  <div class="ptx-tab-panels">
    {{-- TAB 1: Aufgaben + collapsible comments under each task --}}
    <section class="ptx-tab-panel is-active" id="pt-tab-tasks" data-pt-tab-panel="tasks" role="tabpanel">
      <div class="ptx-toolbar">
        <div class="ptx-search">
          <i data-lucide="search"></i>
          <input type="search"
                 id="ptk-search"
                 class="form-control"
                 placeholder="Aufgabe, Mitarbeiter, Key oder Kommentar suchen…"
                 autocomplete="off">
        </div>

        <div class="ptx-filter-row" id="pt-filter-pills">
          <button type="button" class="ptx-filter is-active" data-pt-filter-status="">
            <i data-lucide="layers"></i>
            Alle <b id="ptFilterAllCount">0</b>
          </button>
          <button type="button" class="ptx-filter" data-pt-filter-status="open">
            <i data-lucide="circle-dot"></i>
            Offen <b id="ptFilterOpenCount">0</b>
          </button>
          <button type="button" class="ptx-filter" data-pt-filter-status="on_progress">
            <i data-lucide="loader-circle"></i>
            In Arbeit <b id="ptFilterProgressCount">0</b>
          </button>
          <button type="button" class="ptx-filter" data-pt-filter-status="completed">
            <i data-lucide="check-circle-2"></i>
            Erledigt <b id="ptFilterCompletedCount">0</b>
          </button>
        </div>
      </div>

      <div class="ptx-task-sortbar">
        <div>
          <strong>Aufgaben nach Datum</strong>
          <small>Fällige und ältere Aufgaben werden sichtbar gruppiert.</small>
        </div>
        <div class="ptx-sort-hint">
          <i data-lucide="calendar-days"></i>
          <span>Start · Fällig · Erinnerung</span>
        </div>
      </div>

      <div id="pt-list" class="ptx-task-feed pt-list-view" data-pt-list="1" aria-live="polite">
        <div id="ptTaskList" class="ptx-task-list pt-task-list">
          <div class="ptx-empty">
            <i data-lucide="loader-circle"></i>
            <strong>Aufgaben werden geladen…</strong>
            <span>Öffne eine Kundenkarte, damit die passenden Aufgaben geladen werden.</span>
          </div>
        </div>
      </div>

      <template id="pt-task-item-template">
        <article class="ptx-task-card" data-pt-task-item="__TASK_ID__">
          <button type="button" class="ptx-task-main" data-pt-task-toggle="__TASK_ID__">
            <span class="ptx-task-state"><i data-lucide="circle-dot"></i></span>
            <span class="ptx-task-copy">
              <strong>Aufgabentitel</strong>
              <small>Beschreibung, Mitarbeiter und Datum</small>
            </span>
            <span class="ptx-task-chevron"><i data-lucide="chevron-down"></i></span>
          </button>

          <div class="ptx-task-body">
            <div class="ptx-task-meta-grid"></div>
            <div class="ptx-key-list"></div>

            <div class="ptx-comments-shell">
              <button type="button" class="ptx-comments-toggle" data-pt-comments-toggle>
                <span><i data-lucide="message-circle"></i> Kommentare / Bericht</span>
                <i data-lucide="chevron-down"></i>
              </button>
              <div class="ptx-comments-collapse" data-pt-comments-collapse>
                <div class="ptx-comments-list"></div>
                <form class="ptx-comment-form" data-pt-comment-form>
                  <textarea rows="3" placeholder="Kommentar oder Bericht zu dieser Aufgabe schreiben…"></textarea>
                  <button type="submit" class="ptx-primary-btn">
                    <i data-lucide="send"></i>
                    Kommentar speichern
                  </button>
                </form>
              </div>
            </div>
          </div>
        </article>
      </template>
    </section>

    {{-- TAB 2: Neue Aufgabe --}}
    <section class="ptx-tab-panel" id="pt-tab-create" data-pt-tab-panel="create" role="tabpanel">
      <div class="ptx-create-shell">
        <div class="ptx-create-intro">
          <div class="ptx-create-icon"><i data-lucide="clipboard-plus"></i></div>
          <div>
            <h3>Neue Aufgabe erstellen</h3>
            <p>Die Aufgabe wird automatisch mit dem aktuellen Kunden, Objekt und Produkt verbunden.</p>
          </div>
        </div>

        <form id="pt-form" class="ptx-create-form pt-create-form" autocomplete="off">
          @csrf

          <div class="ptx-form-grid">
            <div class="ptx-form-group is-wide">
              <label for="pt-task_title">Titel *</label>
              <input class="form-control" id="pt-task_title" name="task_title" placeholder="z. B. Kunde anrufen / Unterlagen prüfen…" required>
            </div>

            <div class="ptx-form-group">
              <label for="pt-priority">Priorität</label>
              <select class="form-control" id="pt-priority" name="priority">
                <option value="normal">Normal</option>
                <option value="high">Hoch</option>
                <option value="urgent">Dringend</option>
                <option value="low">Niedrig</option>
              </select>
            </div>

            <div class="ptx-form-group">
              <label for="pt-color">Farbe</label>
              <input type="color" class="form-control" id="pt-color" name="color" value="#93c21c">
            </div>

            <div class="ptx-form-group">
              <label for="pt-start_date">Startdatum</label>
              <input type="date" class="form-control" id="pt-start_date" name="start_date">
            </div>

            <div class="ptx-form-group">
              <label for="pt-due_date">Fällig am *</label>
              <input type="date" class="form-control" id="pt-due_date" name="due_date" required>
            </div>

            <div class="ptx-form-group">
              <label for="pt-due_time">Fällig um</label>
              <input type="time" class="form-control" id="pt-due_time" name="due_time">
            </div>

            <div class="ptx-form-group">
              <label for="pt-reminder_date">Erinnerung</label>
              <input type="date" class="form-control" id="pt-reminder_date" name="reminder_date">
            </div>

            <div class="ptx-form-group">
              <label for="pt-reminder_time">Erinnerungszeit</label>
              <input type="time" class="form-control" id="pt-reminder_time" name="reminder_time">
            </div>

            <div class="ptx-form-group">
              <label for="pt-total_day">Tage</label>
              <input type="number" min="0" step="0.5" class="form-control" id="pt-total_day" name="total_day" value="1">
            </div>

            <div class="ptx-form-group">
              <label for="pt-total_time">Stunden</label>
              <input type="number" min="0" step="0.25" class="form-control" id="pt-total_time" name="total_time" value="1">
            </div>

            <div class="ptx-form-group is-wide">
              <label for="pt-description">Beschreibung</label>
              <textarea class="form-control" id="pt-description" name="description" rows="4" placeholder="Beschreibung, Ziel oder interne Hinweise…"></textarea>
            </div>
          </div>

          <div class="ptx-form-options">
            <label class="ptx-check">
              <input type="checkbox" id="pt-public" name="public" value="1">
              <span><i data-lucide="globe-2"></i> Öffentlich sichtbar</span>
            </label>
            <label class="ptx-check">
              <input type="checkbox" id="pt-is_customer" name="is_customer" value="1" checked>
              <span><i data-lucide="user-round-check"></i> Kundenbezogene Aufgabe</span>
            </label>
          </div>

          <div id="pt-employee-wrap" class="ptx-form-group is-wide">
            <label for="pt-employee_ids">Mitarbeiter für die gesamte Aufgabe</label>
            <select id="pt-employee_ids" name="employee[]" class="form-control select2" multiple data-width="100%">
              @foreach ($employees as $e)
                <option value="{{ $e->id }}">{{ $e->lastname }} {{ $e->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="ptx-steps-box">
            <div class="ptx-steps-head">
              <div>
                <strong>Personal Task Keys / Arbeitsschritte</strong>
                <small>Optional: Schritte mit eigener Mitarbeiter-Zuordnung.</small>
              </div>
              <button type="button" class="ptx-secondary-btn" id="pt-add-step">
                <i data-lucide="plus"></i>
                Schritt hinzufügen
              </button>
            </div>
            <div id="pt-steps" class="ptx-steps-list"></div>
          </div>

          <div class="ptx-create-actions">
            <button type="button" class="ptx-secondary-btn" id="pt-reset-form">
              <i data-lucide="rotate-ccw"></i>
              Zurücksetzen
            </button>
            <button type="submit" class="ptx-primary-btn">
              <i data-lucide="save"></i>
              Aufgabe speichern
            </button>
          </div>
        </form>
      </div>
    </section>
  </div>

  {{-- Hidden context from Kanban card --}}
  <input type="hidden" id="pt-customer_id" name="customer_id">
  <input type="hidden" id="pt-alternative_id" name="alternative_id">
  <input type="hidden" id="pt-product_id" name="product_id">
  <input type="hidden" id="pt-lead_product_list_id" name="lead_product_list_id">
</aside>

  </div><!-- /content-wrapper -->
</div>


 
<div id="kbTaskModalBackdrop" class="kb-task-backdrop" aria-hidden="true"></div>

<div id="kbTaskModal" class="kb-task-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="kbTaskModalTitle">
  <div class="kb-task-modal-header">
    <div>
      <div class="kb-task-modal-title" id="kbTaskModalTitle">
        <i class="feather icon-list"></i>
        Aufgabenmanagement
      </div>
      <div class="kb-task-modal-sub" id="kbTaskContextText">Kunde • Objekt • Produkt • Stage</div>
    </div>

    <button type="button" class="kb-task-close" id="kbTaskModalClose" aria-label="Schließen">×</button>
  </div>

  <div class="kb-task-toolbar">
    <input type="search" id="kbTaskSearch" class="kb-task-search" placeholder="Aufgabe suchen …">

    <select id="kbTaskStatusFilter" class="kb-task-filter">
      <option value="">Alle Status</option>
      <option value="open">Offen</option>
      <option value="scheduled">Geplant</option>
      <option value="in_progress">In Bearbeitung</option>
      <option value="done">Erledigt</option>
      <option value="cancelled">Abgebrochen</option>
    </select>

    <button type="button" class="kb-task-primary" id="kbManualTaskBtn">
      <i class="feather icon-plus"></i>
      Manuelle Aufgabe
    </button>
  </div>

  <div id="kbTaskSequenceSummary" class="kb-task-sequence-summary">
    <div class="kb-task-seq-card">
      <div class="kb-task-seq-label"><i class="feather icon-log-in"></i> Seit Stage-Start</div>
      <div class="kb-task-seq-value" id="kbTaskSeqLanded">-</div>
      <div class="kb-task-seq-muted">Zeitpunkt, seit dem diese Karte in der aktuellen Stage liegt.</div>
    </div>
    <div class="kb-task-seq-card">
      <div class="kb-task-seq-label"><i class="feather icon-check-circle"></i> Vorherige Aufgabe</div>
      <div class="kb-task-seq-value" id="kbTaskSeqPrevious">-</div>
      <div class="kb-task-seq-muted">Letzte erledigte Aufgabe.</div>
    </div>
    <div class="kb-task-seq-card">
      <div class="kb-task-seq-label"><i class="feather icon-activity"></i> Aktuelle / eingehende Aufgabe</div>
      <div class="kb-task-seq-value" id="kbTaskSeqCurrent">-</div>
      <div class="kb-task-seq-muted">Nächste offene Aufgabe in dieser Stage/Sub-Stage.</div>
    </div>
    <div class="kb-task-seq-card">
      <div class="kb-task-seq-label"><i class="feather icon-arrow-right-circle"></i> Nächster Schritt</div>
      <div class="kb-task-seq-value" id="kbTaskSeqNext">-</div>
      <div class="kb-task-seq-muted">Folgeschritt gemäß Task-Phase-Sequenz.</div>
    </div>
  </div>

  <div class="kb-task-body">
    <div class="kb-task-column">
      <div class="kb-task-section-title">
        <i class="feather icon-layers"></i>
        Aufgaben aus aktueller Stage / Sub-Stage
      </div>
      <div id="kbTaskTemplates" class="kb-task-list">
        <div class="kb-task-empty">Aufgaben werden erst beim Öffnen geladen.</div>
      </div>
    </div>

    <div class="kb-task-column">
      <div class="kb-task-section-title">
        <i class="feather icon-check-square"></i>
        Erledigt / Nächste Aktion
      </div>
      <div id="kbTaskSaved" class="kb-task-list">
        <div class="kb-task-empty">Noch keine Aufgabe gespeichert.</div>
      </div>
    </div>
  </div>
</div>

<div id="kbTaskFormBackdrop" class="kb-task-form-backdrop" aria-hidden="true"></div>

<div id="kbTaskFormModal" class="kb-task-form-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="kbTaskFormTitle">
  <div class="kb-task-form-header">
    <strong id="kbTaskFormTitle">Aufgabe planen</strong>
    <button type="button" class="kb-task-close" id="kbTaskFormClose" aria-label="Schließen">×</button>
  </div>

  <form id="kbTaskForm" autocomplete="off">
    @csrf
    <input type="hidden" id="kbFormLeadProductListId">
    <input type="hidden" id="kbFormTaskPhaseId">
    <input type="hidden" id="kbFormPhaseActivityId">
    <input type="hidden" id="kbFormExistingTaskId">
    <input type="hidden" id="kbFormMode" value="manual">

    <div class="kb-task-field">
      <label>Titel</label>
      <input type="text" id="kbFormTitle" required placeholder="z. B. Kunde anrufen / Montage vorbereiten">
    </div>

    <div class="kb-task-field">
      <label>Beschreibung</label>
      <textarea id="kbFormDescription" rows="3" placeholder="Was muss erledigt werden?"></textarea>
    </div>

    <div class="kb-task-grid">
      <div class="kb-task-field">
        <label>Start</label>
        <input type="datetime-local" id="kbFormStart">
      </div>

      <div class="kb-task-field">
        <label>Ende</label>
        <input type="datetime-local" id="kbFormEnd">
      </div>
    </div>

    <div class="kb-task-grid">
      <div class="kb-task-field">
        <label>Geschätzte Minuten</label>
        <input type="number" id="kbFormMinutes" min="1" placeholder="z. B. 60">
      </div>

      <div class="kb-task-field">
        <label>Performer</label>
        <select id="kbFormPerformer" class="kb-task-select2"></select>
      </div>
    </div>

    <div class="kb-task-field">
      <label>
        <input type="checkbox" id="kbFormScheduled">
        Aufgabe ist geplant / terminiert
      </label>
    </div>

    <div class="kb-task-field">
      <label>Weitere Mitarbeiter, die diese Aufgabe machen können</label>
      <select id="kbFormEmployees" multiple class="kb-task-select2"></select>
    </div>

    <div class="kb-task-convert-box">
      <label class="kb-task-check">
          <input type="checkbox" id="kbFormCreatePersonalTask">
          <span>Auch als persönliche Aufgabe erstellen</span>
      </label>

      {{-- Appointment creation from task form removed. --}}
  </div>

    <div class="kb-task-field">
      <label>Interne Beschreibung / Ablauf</label>
      <textarea id="kbFormInternalNote" rows="3" placeholder="Interne Hinweise: wie die Arbeit gemacht werden soll"></textarea>
    </div>

    <div class="kb-task-form-actions">
      <button type="button" class="kb-task-secondary" id="kbTaskFormCancel">Abbrechen</button>
      <button type="submit" class="kb-task-primary">
        <i class="feather icon-save"></i>
        Speichern
      </button>
    </div>
  </form>
</div>
<div id="leadReminderToastWrap" class="lead-reminder-toast-wrap" aria-live="polite" aria-atomic="false"></div>

@endsection



 @section('script')
    @php
  $leadStageNamesForJs = $stageNames ?? [
    'lead' => 'Lead',
    'offer' => 'Angebot',
    'follow_up' => 'Nachfassen',
    'accepted' => 'Annehmen',
    'deal' => 'Auftrag',
    'project' => 'Montage',
    'completed' => 'Abschluss',
    'archive' => 'Archive',
    'junk' => 'Junk',
  ];

  $leadStageMetaForJs = $stageMeta ?? [];

  // Kanban columns must not show Junk/Ticket because those have their own tabs.
  $kanbanStageNamesForJs = collect($leadStageNamesForJs)
    ->reject(fn($label, $key) => in_array(strtolower((string) $key), ['junk', 'ticket'], true))
    ->toArray();

  $kanbanStageMetaForJs = collect($leadStageMetaForJs)
    ->reject(fn($meta, $key) => in_array(strtolower((string) $key), ['junk', 'ticket'], true))
    ->toArray();
    @endphp

              <script src="{{ asset('js/select2.min.js') }}"></script>
              <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
              <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
              <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
              <script async
                src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_KEY') }}&libraries=places&language=de&region=DE">

              </script>


              <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>



    <!-- Kanban Column Script  -->


    <!-- Aufgabe Script  -->


    @if($canManageKanbanLeadStages)
    @endif



              @php
  $kbRoute = function (string $name, array $params = [], string $fallback = '#') {
    try {
      return \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : url($fallback);
    } catch (\Throwable $e) {
      return url($fallback);
    }
  };

  $kanbanBootProductsForJs = collect($kanbanProductsForJs ?? $products ?? [])
    ->map(function ($product) {
      return [
        'id' => $product->id ?? null,
        'name' => $product->article_group ?? $product->initial ?? ('Produkt #' . ($product->id ?? '')),
        'initial' => $product->initial ?? null,
      ];
    })
    ->filter(function ($product) {
      return !empty($product['id']);
    })
    ->values()
    ->toArray();

  $kanbanBootBranchColorMap = collect($branches ?? [])
    ->mapWithKeys(function ($branch) {
      $name = mb_strtolower(trim((string) ($branch->branch ?? '')));
      $color = (string) ($branch->color ?? '#93c21c');

      return [$name => $color];
    })
    ->all();

  $kanbanExternalBoot = [
    'KANBAN_CUSTOMER_PANEL_ROUTES' => [
      'counts' => $kbRoute('kanban.customer-panel.counts', [], '/kanban/customer-panel/counts'),
      'customerReportsIndex' => $kbRoute('kanban.customer-panel.customer-reports.index', [], '/kanban/customer-reports'),
      'customerReportsStore' => $kbRoute('kanban.customer-panel.customer-reports.store', [], '/kanban/customer-reports'),
      'appointmentsIndex' => $kbRoute('kanban.customer-panel.appointments.index', [], '/kanban/appointments'),
      'appointmentReportsIndex' => $kbRoute('kanban.customer-panel.appointments.reports.index', ['appointment' => '__APPOINTMENT__'], '/kanban/appointments/__APPOINTMENT__/reports'),
      'appointmentReportsStore' => $kbRoute('kanban.customer-panel.appointments.reports.store', ['appointment' => '__APPOINTMENT__'], '/kanban/appointments/__APPOINTMENT__/reports'),
    ],
    'KANBAN_PERSONAL_TASK_PANEL_ROUTES' => [
      'tasks' => $kbRoute('kanban.personal-task-panel.tasks', [], '/kanban/personal-task-panel/tasks'),
      'show' => $kbRoute('kanban.personal-task-panel.tasks.show', ['task' => '__TASK__'], '/kanban/personal-task-panel/tasks/__TASK__'),
      'commentStore' => $kbRoute('kanban.personal-task-panel.tasks.comments.store', ['task' => '__TASK__'], '/kanban/personal-task-panel/tasks/__TASK__/comments'),
      'replyStore' => $kbRoute('kanban.personal-task-panel.comments.reply', ['comment' => '__COMMENT__'], '/kanban/personal-task-panel/comments/__COMMENT__/reply'),
      'keyToggle' => $kbRoute('kanban.personal-task-panel.keys.toggle', ['key' => '__KEY__'], '/kanban/personal-task-panel/keys/__KEY__/toggle'),
    ],
    'KANBAN_BOOT' => [
      'csrf' => csrf_token(),
      'authUserId' => auth()->user()->name ?? '',
      'employees' => $employees ?? [],
      'teams' => $teams ?? [],
      'leadStageNamesForJs' => $leadStageNamesForJs ?? [],
      'leadStageMetaForJs' => $leadStageMetaForJs ?? [],
      'kanbanStageNamesForJs' => $kanbanStageNamesForJs ?? [],
      'kanbanStageMetaForJs' => $kanbanStageMetaForJs ?? [],
      'kanbanProductsForJs' => $kanbanBootProductsForJs ?? [],
      'branchColorMap' => $kanbanBootBranchColorMap ?? [],
      'disableOldTerminDrawer' => true,
    ],
    'KANBAN_SAVED_FILTER_ROUTES' => [
      'index' => $kbRoute('kanban.filter-settings.index', [], '/lead/kanban/filter-settings'),
      'store' => $kbRoute('kanban.filter-settings.store', [], '/lead/kanban/filter-settings'),
      'update' => $kbRoute('kanban.filter-settings.update', ['setting' => '__ID__'], '/lead/kanban/filter-settings/__ID__'),
      'destroy' => $kbRoute('kanban.filter-settings.destroy', ['setting' => '__ID__'], '/lead/kanban/filter-settings/__ID__/delete'),
      'makeDefault' => $kbRoute('kanban.filter-settings.default', ['setting' => '__ID__'], '/lead/kanban/filter-settings/__ID__/default'),
      'customers' => $kbRoute('kanban.customers.search', [], '/lead/kanban/customers/search'),
      'branchAddresses' => $kbRoute('kanban.branch-addresses.index', [], '/lead/kanban/branch-addresses'),
    ],
  ];
              @endphp

              {{-- ===== Kanban external boot data: no executable inline JS ===== --}}
              <textarea id="kanban-external-boot" class="d-none" hidden>{!! json_encode($kanbanExternalBoot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</textarea>
              <script src="{{ asset('js/kanban-boot-loader.js') }}?v={{ time() }}"></script>
              <script src="{{ asset('js/kanban.js') }}?v={{ time() }}"></script>
              {{-- <script src="{{ asset('js/kanban-offer-bypass-patch.js') }}?v={{ time() }}"></script> --}}


              {{-- ===== Saved Kanban Filters Add-on ===== --}}

              <script src="{{ asset('js/kanban-saved-filters.js') }}?v={{ time() }}"></script>
              <script>
                document.addEventListener('DOMContentLoaded', function () {
                  function refreshLucide() {
                    if (window.lucide && typeof window.lucide.createIcons === 'function') {
                      window.lucide.createIcons();
                    }
                  }

                  document.addEventListener('click', function (event) {
                    var tab = event.target.closest('[data-pt-tab]');
                    if (tab) {
                      event.preventDefault();
                      var target = tab.getAttribute('data-pt-tab');
                      document.querySelectorAll('#pt-drawer [data-pt-tab]').forEach(function (btn) {
                        var active = btn.getAttribute('data-pt-tab') === target;
                        btn.classList.toggle('is-active', active);
                        btn.setAttribute('aria-selected', active ? 'true' : 'false');
                      });
                      document.querySelectorAll('#pt-drawer [data-pt-tab-panel]').forEach(function (panel) {
                        panel.classList.toggle('is-active', panel.getAttribute('data-pt-tab-panel') === target);
                      });
                      refreshLucide();
                      return;
                    }

                    var commentsToggle = event.target.closest('[data-pt-comments-toggle]');
                    if (commentsToggle) {
                      event.preventDefault();
                      commentsToggle.closest('.ptx-comments-shell')?.classList.toggle('is-open');
                      refreshLucide();
                      return;
                    }

                    var taskToggle = event.target.closest('[data-pt-task-toggle]');
                    if (taskToggle) {
                      event.preventDefault();
                      taskToggle.closest('.ptx-task-card')?.classList.toggle('is-open');
                      refreshLucide();
                    }
                  });

                  document.addEventListener('pt:tasks-rendered', refreshLucide);
                  refreshLucide();
                  setTimeout(refreshLucide, 250);
                  setTimeout(refreshLucide, 900);
                });
              </script>

@endsection
 