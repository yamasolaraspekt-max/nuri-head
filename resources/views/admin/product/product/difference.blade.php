@extends('admin.layouts.app')

@section('title', 'Produktvergleich')

@section('style')
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

  <style>
    :root {
      --card-bg: #ffffff;
              --text-main: #1f2937;
              --text-muted: #6b7280;
              --border: #e5e7eb;
              --primary: var(--sa-accent);
              --primary-hover: var(--sa-accent-hover);
              --primary-light: var(--sa-accent-light);
              --blue: #74b2d4;
              --blue-light: #eff6ff;
              --success: #10b981;
              --success-light: #ecfdf5;
              --warning: #f59e0b;
              --warning-light: #fffbeb;
              --danger: #ef4444;
              --danger-light: #fef2f2;
              --purple: #7c3aed;
              --purple-light: #f5f3ff;
              --gray-light: #f3f4f6;
              --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
              --shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
              --radius: 14px;
              --transition: all .2s ease-in-out;
          }

          .oc-wrap {
              font-family: Inter, system-ui, -apple-system, sans-serif;
              color: var(--text-main);
          }

          .oc-header {
              margin-bottom: 18px;
          }

          .oc-titlebar {
              display: flex;
              align-items: flex-end;
              justify-content: space-between;
              gap: 12px;
              margin-bottom: 16px;
              flex-wrap: wrap;
          }

          .oc-title {
              font-size: 26px;
              font-weight: 900;
              letter-spacing: -.025em;
              color: #111827;
              text-transform: uppercase;
          }

          .oc-sub {
              font-size: 14px;
              color: var(--text-muted);
              margin-top: 4px;
              line-height: 1.55;
          }

          .oc-breadcrumb {
              display: flex;
              align-items: center;
              flex-wrap: wrap;
              gap: 8px;
              margin-top: 10px;
              font-size: 13px;
              color: var(--text-muted);
          }

          .oc-breadcrumb a {
              color: var(--text-muted);
              text-decoration: none;
              font-weight: 700;
          }

          .oc-breadcrumb span.current {
              color: #111827;
              font-weight: 900;
          }

          .oc-badge {
              display: inline-flex;
              align-items: center;
              justify-content: center;
              padding: 6px 10px;
              border-radius: 999px;
              font-size: 12px;
              font-weight: 900;
              line-height: 1;
              gap: 6px;
              white-space: nowrap;
          }

          .oc-badge.blue { background: var(--blue-light); color: var(--blue); }
          .oc-badge.green { background: var(--success-light); color: var(--success); }
          .oc-badge.orange { background: var(--warning-light); color: #b45309; }
          .oc-badge.red { background: var(--danger-light); color: #b91c1c; }
          .oc-badge.purple { background: var(--purple-light); color: var(--purple); }
          .oc-badge.soft { background: #f3f4f6; color: #4b5563; border: 1px solid var(--border); }

          .oc-btn {
              background: var(--primary);
              color: #fff;
              border: none;
              padding: 10px 16px;
              border-radius: 10px;
              font-weight: 900;
              cursor: pointer;
              transition: var(--transition);
              display: inline-flex;
              align-items: center;
              gap: 8px;
              text-decoration: none;
              white-space: nowrap;
          }

          .oc-btn:hover {
              background: var(--primary-hover);
              color: #fff;
              text-decoration: none;
          }

          .oc-btn:disabled {
              opacity: .55;
              cursor: not-allowed;
          }

          .oc-btn-soft {
              background: #fff;
              color: var(--text-main);
              border: 1px solid var(--border);
              padding: 10px 14px;
              border-radius: 10px;
              font-weight: 800;
              cursor: pointer;
              transition: var(--transition);
              text-decoration: none;
              display: inline-flex;
              align-items: center;
              gap: 8px;
              white-space: nowrap;
          }

          .oc-btn-soft:hover {
              background: #f9fafb;
              color: var(--text-main);
              text-decoration: none;
          }

          .oc-btn-small {
              padding: 7px 10px;
              font-size: 12px;
              border-radius: 9px;
          }

          .oc-analytics {
              display: grid;
              grid-template-columns: repeat(5, minmax(0, 1fr));
              gap: 14px;
              margin-bottom: 18px;
          }

          @media(max-width:1200px) {
              .oc-analytics {
                  grid-template-columns: repeat(2, minmax(0, 1fr));
              }
          }

          @media(max-width:700px) {
              .oc-analytics {
                  grid-template-columns: 1fr;
              }
          }

          .oc-stat {
              background: var(--card-bg);
              border: 1px solid var(--border);
              border-radius: 16px;
              padding: 16px;
              box-shadow: var(--shadow-sm);
              display: flex;
              align-items: center;
              gap: 12px;
              min-height: 92px;
          }

          .oc-stat-icon {
              width: 48px;
              height: 48px;
              border-radius: 14px;
              display: flex;
              align-items: center;
              justify-content: center;
              flex: 0 0 auto;
              font-weight: 900;
          }

          .oc-stat-icon.total { background: var(--blue-light); color: var(--blue); }
          .oc-stat-icon.published { background: var(--success-light); color: var(--success); }
          .oc-stat-icon.unpublished { background: var(--warning-light); color: #d97706; }
          .oc-stat-icon.type { background: var(--gray-light); color: #6b7280; }
          .oc-stat-icon.price { background: var(--purple-light); color: var(--purple); }

          .oc-stat-label {
              font-size: 11px;
              font-weight: 800;
              color: var(--text-muted);
              text-transform: uppercase;
              letter-spacing: .06em;
          }

          .oc-stat-value {
              font-size: 24px;
              font-weight: 900;
              color: #111827;
              line-height: 1.1;
              margin-top: 4px;
          }

          .oc-stat-sub {
              font-size: 12px;
              color: var(--text-muted);
              margin-top: 4px;
          }

          .oc-warning-panel {
              display: grid;
              grid-template-columns: auto 1fr;
              gap: 12px;
              background: #fffbeb;
              border: 1px solid #fde68a;
              color: #92400e;
              border-radius: 16px;
              padding: 14px 16px;
              margin-bottom: 16px;
          }

          .oc-warning-icon {
              width: 38px;
              height: 38px;
              border-radius: 14px;
              background: var(--warning);
              color: #fff;
              display: inline-flex;
              align-items: center;
              justify-content: center;
              font-weight: 900;
          }

          .oc-warning-title {
              font-weight: 950;
              color: #78350f;
              margin: 0 0 4px;
              font-size: 14px;
          }

          .oc-warning-text {
              font-size: 13px;
              line-height: 1.55;
              margin: 0;
              font-weight: 700;
          }

          .oc-toolbar {
              background: var(--card-bg);
              border: 1px solid var(--border);
              border-radius: var(--radius);
              padding: 14px 16px;
              display: flex;
              flex-wrap: wrap;
              gap: 14px;
              align-items: flex-end;
              justify-content: space-between;
              margin-bottom: 16px;
              box-shadow: var(--shadow-sm);
          }

          .oc-toolbar-left,
          .oc-toolbar-right {
              display: flex;
              align-items: flex-end;
              gap: 12px;
              flex-wrap: wrap;
          }

          .oc-toolbar-left {
              flex: 1;
          }

          .oc-filter-block {
              display: flex;
              flex-direction: column;
              gap: 6px;
              min-width: 170px;
          }

          .oc-filter-block.search {
              flex: 1;
              min-width: 280px;
          }

          .oc-filter-label {
              font-size: 11px;
              font-weight: 800;
              color: var(--text-muted);
              text-transform: uppercase;
              letter-spacing: .06em;
          }

          .oc-input,
          .oc-select {
              width: 100%;
              border-radius: 10px;
              border: 1px solid var(--border);
              background: #f9fafb;
              padding: 10px 12px;
              font-size: 14px;
              outline: none;
              transition: var(--transition);
          }

          .oc-input.search {
              padding-left: 36px;
              background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
              background-repeat: no-repeat;
              background-position: 10px center;
              background-size: 16px;
          }

          .oc-input:focus,
          .oc-select:focus {
              background: #fff;
              border-color: var(--primary);
              box-shadow: 0 0 0 3px var(--primary-light);
          }

          .oc-layout {
              display: grid;
              grid-template-columns: minmax(0, .98fr) minmax(0, 1.52fr);
              gap: 18px;
          }

          @media(max-width: 1100px) {
              .oc-layout {
                  grid-template-columns: 1fr;
              }
          }

          .oc-card {
              background: #fff;
              border: 1px solid var(--border);
              border-radius: 16px;
              box-shadow: var(--shadow-sm);
              overflow: hidden;
          }

          .oc-card-head {
              display: flex;
              align-items: center;
              justify-content: space-between;
              gap: 12px;
              padding: 16px 18px;
              border-bottom: 1px solid var(--border);
              background: #fafafa;
              flex-wrap: wrap;
          }

          .oc-card-title {
              font-size: 16px;
              font-weight: 900;
              color: #111827;
              margin: 0;
          }

          .oc-card-sub {
              font-size: 12px;
              color: var(--text-muted);
              margin-top: 4px;
              line-height: 1.45;
          }

          .oc-card-body {
              padding: 16px;
          }

          .oc-selected-panel {
              display: none;
              border: 1px solid var(--border);
              background: #f9fafb;
              border-radius: 16px;
              padding: 12px;
              margin-bottom: 14px;
          }

          .oc-selected-panel.active {
              display: block;
          }

          .oc-selected-head {
              display: flex;
              align-items: center;
              justify-content: space-between;
              gap: 10px;
              margin-bottom: 10px;
              flex-wrap: wrap;
          }

          .oc-selected-title {
              font-size: 13px;
              color: #111827;
              font-weight: 950;
          }

          .oc-selected-list {
              display: flex;
              flex-wrap: wrap;
              gap: 8px;
          }

          .oc-selected-chip {
              display: inline-flex;
              align-items: center;
              gap: 8px;
              padding: 7px 9px;
              border-radius: 999px;
              border: 1px solid var(--border);
              background: white;
              max-width: 100%;
          }

          .oc-selected-chip img {
              width: 26px;
              height: 26px;
              border-radius: 8px;
              object-fit: contain;
              border: 1px solid var(--border);
              background: #fff;
              padding: 2px;
          }

          .oc-selected-chip-name {
              max-width: 220px;
              overflow: hidden;
              white-space: nowrap;
              text-overflow: ellipsis;
              font-size: 12px;
              font-weight: 900;
              color: #111827;
          }

          .oc-selected-chip-remove {
              border: none;
              background: #f3f4f6;
              color: #6b7280;
              border-radius: 999px;
              width: 20px;
              height: 20px;
              cursor: pointer;
              font-size: 12px;
              font-weight: 950;
              line-height: 1;
          }

          .oc-table-search {
              margin-bottom: 12px;
          }

          .oc-table-wrap {
              max-height: 520px;
              overflow: auto;
              border-radius: 14px;
              border: 1px solid var(--border);
              background: #fff;
          }

          .oc-table {
              width: 100%;
              border-collapse: collapse;
              font-size: 13px;
          }

          .oc-table thead {
              background: #f3f4f6;
              position: sticky;
              top: 0;
              z-index: 2;
          }

          .oc-table th,
          .oc-table td {
              padding: 10px 12px;
              border-bottom: 1px solid var(--border);
              vertical-align: middle;
              white-space: nowrap;
          }

          .oc-table th {
              font-size: 11px;
              text-transform: uppercase;
              letter-spacing: .06em;
              color: var(--text-muted);
              font-weight: 900;
          }

          .oc-table tbody tr:hover {
              background: #f9fafb;
          }

          .oc-product-title-cell {
              display: flex;
              align-items: center;
              gap: 10px;
              min-width: 280px;
          }

          .oc-product-img {
              width: 48px;
              height: 48px;
              border-radius: 13px;
              border: 1px solid var(--border);
              object-fit: contain;
              background: #fff;
              padding: 4px;
              cursor: pointer;
              flex: 0 0 auto;
          }

          .oc-product-img-placeholder {
              width: 48px;
              height: 48px;
              border-radius: 13px;
              border: 1px dashed var(--border);
              background: #f9fafb;
              color: #9ca3af;
              display: inline-flex;
              align-items: center;
              justify-content: center;
              font-size: 11px;
              font-weight: 900;
              flex: 0 0 auto;
          }

          .oc-product-name {
              font-weight: 900;
              color: #111827;
              margin-bottom: 3px;
              max-width: 260px;
              overflow: hidden;
              text-overflow: ellipsis;
              white-space: nowrap;
          }

          .oc-product-sub {
              font-size: 12px;
              color: var(--text-muted);
              max-width: 260px;
              overflow: hidden;
              text-overflow: ellipsis;
              white-space: nowrap;
          }

          .oc-actions-row {
              display: flex;
              justify-content: space-between;
              align-items: center;
              gap: 11px;
              flex-wrap: wrap;
              margin-top: 14px;
          }

          .oc-note {
              font-size: 12px;
              color: #9ca3af;
              margin-top: 12px;
              line-height: 1.45;
          }

          .oc-summary-grid {
              display: none;
              grid-template-columns: repeat(4, minmax(0, 1fr));
              gap: 12px;
              margin-bottom: 14px;
          }

          @media(max-width: 1300px) {
              .oc-summary-grid {
                  grid-template-columns: repeat(2, minmax(0, 1fr));
              }
          }

          @media(max-width: 700px) {
              .oc-summary-grid {
                  grid-template-columns: 1fr;
              }
          }

          .oc-summary-card {
              border: 1px solid var(--border);
              border-radius: 14px;
              padding: 13px 14px;
              background: #fff;
          }

          .oc-summary-label {
              font-size: 11px;
              font-weight: 900;
              color: var(--text-muted);
              text-transform: uppercase;
              letter-spacing: .06em;
          }

          .oc-summary-value {
              font-size: 18px;
              font-weight: 950;
              color: #111827;
              margin-top: 5px;
          }

          .oc-summary-sub {
              color: var(--text-muted);
              font-size: 12px;
              margin-top: 4px;
              line-height: 1.45;
          }

          .oc-chart-grid {
              display: none;
              grid-template-columns: minmax(0, 1.35fr) minmax(0, .85fr);
              gap: 14px;
              margin-bottom: 16px;
          }

          @media(max-width: 1100px) {
              .oc-chart-grid {
                  grid-template-columns: 1fr;
              }
          }

          .oc-chart-box {
              border: 1px solid var(--border);
              border-radius: 16px;
              background: #fff;
              padding: 14px;
          }

          .oc-chart-title {
              font-size: 14px;
              font-weight: 950;
              margin: 0 0 3px;
              color: #111827;
          }

          .oc-chart-sub {
              font-size: 12px;
              color: var(--text-muted);
              margin-bottom: 12px;
          }

          .oc-comparison-placeholder {
              font-size: 14px;
              color: var(--text-muted);
              padding: 18px;
              border-radius: 12px;
              border: 1px dashed var(--border);
              background: #f9fafb;
              line-height: 1.55;
          }

          .oc-comparison-wrap {
              max-height: 560px;
              overflow: auto;
              border-radius: 14px;
              border: 1px solid var(--border);
              background: #fff;
          }

          .oc-comparison-table {
              width: 100%;
              border-collapse: collapse;
              font-size: 13px;
          }

          .oc-comparison-table th,
          .oc-comparison-table td {
              padding: 10px 12px;
              border-bottom: 1px solid var(--border);
              border-right: 1px solid var(--border);
              vertical-align: middle;
          }

          .oc-comparison-table th:first-child,
          .oc-comparison-table td:first-child {
              background: #f9fafb;
              position: sticky;
              left: 0;
              z-index: 2;
              min-width: 180px;
          }

          .oc-comparison-table thead th {
              background: #f3f4f6;
              position: sticky;
              top: 0;
              z-index: 3;
              font-size: 11px;
              text-transform: uppercase;
              letter-spacing: .06em;
              color: var(--text-muted);
              font-weight: 900;
          }

          .oc-comparison-distributor-row td {
              background: #eef2ff !important;
              font-weight: 900;
              color: #3730a3;
          }

          .oc-comparison-diff {
              outline: 2px solid rgba(248, 113, 113, .9);
              outline-offset: -2px;
              background: #fef2f2 !important;
          }

          .oc-comparison-best {
              outline: 2px solid rgba(16, 185, 129, .8);
              outline-offset: -2px;
              background: #ecfdf5 !important;
          }

          .oc-comparison-metric {
              font-weight: 900;
              font-size: 12px;
              color: #374151;
          }

          .oc-comparison-product-head {
              display: flex;
              align-items: center;
              gap: 8px;
              min-width: 230px;
          }

          .oc-comparison-product-head img {
              width: 38px;
              height: 38px;
              border-radius: 10px;
              border: 1px solid var(--border);
              object-fit: contain;
              background: #fff;
              padding: 3px;
              cursor: pointer;
          }

          .oc-pagination {
              margin-top: 16px;
              background: #fff;
              border: 1px solid var(--border);
              border-radius: 14px;
              padding: 14px 16px;
              box-shadow: var(--shadow-sm);
          }

          .select2-container {
              width: 100% !important;
          }

          .select2-container .select2-selection--single {
              height: 42px;
              border-radius: 10px;
              border-color: var(--border);
              background: #f9fafb;
          }

          .select2-container--default .select2-selection--single .select2-selection__rendered {
              line-height: 40px;
              font-size: 14px;
              padding-left: 12px;
              color: var(--text-main);
          }

          .select2-container--default .select2-selection--single .select2-selection__arrow {
              height: 40px;
          }

          .oc-toast-wrap {
              position: fixed;
              right: 22px;
              bottom: 22px;
              z-index: 99999;
              display: flex;
              flex-direction: column;
              gap: 10px;
              pointer-events: none;
          }

          .oc-toast {
              pointer-events: auto;
              min-width: 310px;
              max-width: 440px;
              background: #fff;
              border: 1px solid var(--border);
              border-radius: 16px;
              box-shadow: var(--shadow);
              padding: 13px 14px;
              display: flex;
              gap: 10px;
              align-items: flex-start;
              animation: ocToastIn .25s ease forwards;
          }

          @keyframes ocToastIn {
              from { transform: translateX(100%); opacity: 0; }
              to { transform: translateX(0); opacity: 1; }
          }

          .oc-toast-ic {
              width: 34px;
              height: 34px;
              border-radius: 12px;
              display: flex;
              align-items: center;
              justify-content: center;
              flex: 0 0 auto;
              font-weight: 950;
          }

          .oc-toast-ic.ok { background: var(--success-light); color: var(--success); }
          .oc-toast-ic.warn { background: var(--warning-light); color: var(--warning); }
          .oc-toast-ic.bad { background: var(--danger-light); color: var(--danger); }
          .oc-toast-ic.info { background: var(--blue-light); color: var(--blue); }

          .oc-toast-ttl {
              font-weight: 950;
              font-size: 13px;
              margin: 0;
              color: #111827;
          }

          .oc-toast-msg {
              font-size: 12px;
              color: #374151;
              margin: 4px 0 0;
              line-height: 1.4;
          }

          .oc-toast-x {
              margin-left: auto;
              background: transparent;
              border: none;
              cursor: pointer;
              color: var(--text-muted);
              font-weight: 900;
          }

          .oc-modal-backdrop {
              position: fixed;
              inset: 0;
              background: rgba(15, 23, 42, .68);
              z-index: 99998;
              display: none;
              align-items: center;
              justify-content: center;
              padding: 22px;
          }

          .oc-modal-backdrop.open {
              display: flex;
          }

          .oc-modal {
              width: 100%;
              max-width: 880px;
              background: #fff;
              border-radius: 22px;
              border: 1px solid var(--border);
              box-shadow: var(--shadow);
              overflow: hidden;
          }

          .oc-modal-head {
              display: flex;
              justify-content: space-between;
              gap: 12px;
              align-items: center;
              padding: 15px 18px;
              border-bottom: 1px solid var(--border);
              background: #fafafa;
          }

          .oc-modal-title {
              font-size: 16px;
              font-weight: 950;
              color: #111827;
          }

          .oc-modal-body {
              padding: 18px;
              max-height: 78vh;
              overflow: auto;
          }

          .oc-detail-grid {
              display: grid;
              grid-template-columns: 260px minmax(0, 1fr);
              gap: 18px;
          }

          @media(max-width: 800px) {
              .oc-detail-grid {
                  grid-template-columns: 1fr;
              }
          }

          .oc-detail-image {
              width: 100%;
              height: 260px;
              object-fit: contain;
              border-radius: 18px;
              border: 1px solid var(--border);
              background: #fff;
              padding: 10px;
          }

          .oc-detail-image-placeholder {
              height: 260px;
              border-radius: 18px;
              border: 1px dashed var(--border);
              background: #f9fafb;
              color: #9ca3af;
              display: flex;
              align-items: center;
              justify-content: center;
              font-weight: 950;
          }

          .oc-detail-title {
              font-size: 22px;
              font-weight: 950;
              color: #111827;
              margin: 0 0 8px;
              line-height: 1.25;
          }

          .oc-detail-sub {
              color: var(--text-muted);
              font-size: 13px;
              line-height: 1.5;
              margin-bottom: 12px;
          }

          .oc-detail-facts {
              display: grid;
              grid-template-columns: repeat(2, minmax(0, 1fr));
              gap: 10px;
              margin-top: 14px;
          }

          @media(max-width: 650px) {
              .oc-detail-facts {
                  grid-template-columns: 1fr;
              }
          }

          .oc-detail-fact {
              border: 1px solid var(--border);
              border-radius: 14px;
              padding: 10px 12px;
              background: #fafafa;
          }

          .oc-detail-label {
              font-size: 10px;
              text-transform: uppercase;
              letter-spacing: .06em;
              color: var(--text-muted);
              font-weight: 950;
              margin-bottom: 4px;
          }

          .oc-detail-value {
              font-size: 13px;
              color: #111827;
              font-weight: 850;
              word-break: break-word;
          }

          .oc-detail-actions {
              display: flex;
              flex-wrap: wrap;
              gap: 8px;
              margin-top: 16px;
          }
      </style>
@endsection

@section('content')
  <div class="oc-toast-wrap" id="toast-wrap"></div>

  <div class="oc-modal-backdrop" id="productDetailModal">
      <div class="oc-modal">
          <div class="oc-modal-head">
              <div class="oc-modal-title">Produktdetails</div>
              <button type="button" class="oc-btn-soft" onclick="closeProductDetailModal()">Schließen</button>
          </div>

          <div class="oc-modal-body">
              <div class="oc-detail-grid">
                  <div id="detailImageBox"></div>

                  <div>
                      <h2 class="oc-detail-title" id="detailTitle">—</h2>
                      <div class="oc-detail-sub" id="detailSubtitle">—</div>

                      <div style="display:flex;gap:7px;flex-wrap:wrap;" id="detailBadges"></div>

                      <div class="oc-detail-facts">
                          <div class="oc-detail-fact">
                              <div class="oc-detail-label">Produkt-ID</div>
                              <div class="oc-detail-value" id="detailId">—</div>
                          </div>

                          <div class="oc-detail-fact">
                              <div class="oc-detail-label">Hersteller</div>
                              <div class="oc-detail-value" id="detailBrand">—</div>
                          </div>

                          <div class="oc-detail-fact">
                              <div class="oc-detail-label">Artikelgruppe</div>
                              <div class="oc-detail-value" id="detailGroup">—</div>
                          </div>

                          <div class="oc-detail-fact">
                              <div class="oc-detail-label">EAN</div>
                              <div class="oc-detail-value" id="detailEan">—</div>
                          </div>

                          <div class="oc-detail-fact">
                              <div class="oc-detail-label">Hersteller-Nr.</div>
                              <div class="oc-detail-value" id="detailArticleNo">—</div>
                          </div>

                          <div class="oc-detail-fact">
                              <div class="oc-detail-label">SKU</div>
                              <div class="oc-detail-value" id="detailSku">—</div>
                          </div>

                          <div class="oc-detail-fact">
                              <div class="oc-detail-label">Modell</div>
                              <div class="oc-detail-value" id="detailModel">—</div>
                          </div>

                          <div class="oc-detail-fact">
                              <div class="oc-detail-label">Lieferantenpreise</div>
                              <div class="oc-detail-value" id="detailPrices">—</div>
                          </div>
                      </div>

                      <div class="oc-detail-actions">
                          <a href="#" target="_blank" class="oc-btn" id="detailOpenLink">Produkt öffnen</a>
                          <button type="button" class="oc-btn-soft" id="detailSelectButton">Für Vergleich auswählen</button>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>

  @php
    $productImageUrl = function ($product) {
      if (!empty($product->main_image_url)) {
        return $product->main_image_url;
      }

      if (!empty($product->firstImage?->image)) {
        return asset('images/products/' . ltrim($product->firstImage->image, '/'));
      }

      return null;
    };
  @endphp

  <div class="oc-wrap">
      <div class="oc-header">
          <div class="oc-titlebar">
              <div>
                  <div class="oc-title">Produkt-Preisvergleich</div>
                  <div class="oc-sub">
                      Vergleiche Produkte nach Lieferantenpreisen, IDS-Importpreisen, Einkaufspreis, Verkaufspreis, Verfügbarkeit, Preisunterschied und Produktbild.
                  </div>

                  <div class="oc-breadcrumb">
                      <a href="{{ url('/employee_dashboard') }}">Dashboard</a>
                      <span>›</span>
                      <span class="current">Produktvergleich</span>
                  </div>
              </div>

              <div style="display:flex;gap:8px;flex-wrap:wrap;">
                  <span class="oc-badge blue">IDS-fähig über distributor_prices</span>
                  <span class="oc-badge green">Produktdetails Modal</span>
                  <span class="oc-badge soft">Beta</span>
              </div>
          </div>
      </div>

      <div class="oc-warning-panel">
          <div class="oc-warning-icon">!</div>
          <div>
              <p class="oc-warning-title">Hinweis zum Vergleich</p>
              <p class="oc-warning-text">
                  Die Auswertung basiert auf <strong>distributor_prices</strong>. Produkte aus IDS werden automatisch berücksichtigt,
                  sobald sie korrekt mit <strong>product_id</strong>, <strong>distributor_id</strong> und Preis gespeichert wurden.
                  Wenn ein Produkt bei einem Lieferanten keinen Preis hat, wird es im Vergleich als fehlend angezeigt.
              </p>
          </div>
      </div>

      <div class="oc-analytics">
          <div class="oc-stat">
              <div class="oc-stat-icon total">#</div>
              <div>
                  <div class="oc-stat-label">Gefundene Produkte</div>
                  <div class="oc-stat-value">{{ $products->total() }}</div>
                  <div class="oc-stat-sub">Treffer gesamt</div>
              </div>
          </div>

          <div class="oc-stat">
              <div class="oc-stat-icon published">M</div>
              <div>
                  <div class="oc-stat-label">Marken</div>
                  <div class="oc-stat-value">{{ $brands->count() }}</div>
                  <div class="oc-stat-sub">Verfügbare Hersteller</div>
              </div>
          </div>

          <div class="oc-stat">
              <div class="oc-stat-icon unpublished">A</div>
              <div>
                  <div class="oc-stat-label">Artikelgruppen</div>
                  <div class="oc-stat-value">{{ $articleGroups->count() }}</div>
                  <div class="oc-stat-sub">Verfügbare Gruppen</div>
              </div>
          </div>

          <div class="oc-stat">
              <div class="oc-stat-icon type">L</div>
              <div>
                  <div class="oc-stat-label">Lieferanten</div>
                  <div class="oc-stat-value">{{ $distributors->count() }}</div>
                  <div class="oc-stat-sub">Hinterlegte Lieferanten</div>
              </div>
          </div>

          <div class="oc-stat">
              <div class="oc-stat-icon price">€</div>
              <div>
                  <div class="oc-stat-label">Auswahl</div>
                  <div class="oc-stat-value" id="selected-count-stat">0</div>
                  <div class="oc-stat-sub">Produkte ausgewählt</div>
              </div>
          </div>
      </div>

      <form method="GET" action="{{ route('admin.products.difference') }}" class="oc-toolbar">
          <div class="oc-toolbar-left">
              <div class="oc-filter-block search">
                  <label class="oc-filter-label">Suche</label>
                  <input
                      type="text"
                      name="search"
                      value="{{ $search }}"
                      class="oc-input search"
                      placeholder="Name, Modell, EAN, SKU oder Artikel-Nr."
                  >
              </div>

              <div class="oc-filter-block">
                  <label class="oc-filter-label">Marke</label>
                  <select name="brand_id" class="oc-select js-select2">
                      <option value="">Alle Marken</option>
                      @foreach($brands as $brand)
                        <option value="{{ $brand->id }}" @selected($brand->id == $brandId)>{{ $brand->name }}</option>
                      @endforeach
                  </select>
              </div>

              <div class="oc-filter-block">
                  <label class="oc-filter-label">Artikelgruppe</label>
                  <select name="article_group_id" class="oc-select js-select2">
                      <option value="">Alle Gruppen</option>
                      @foreach($articleGroups as $group)
                        <option value="{{ $group->id }}" @selected($group->id == $articleGroupId)>{{ $group->article_group }}</option>
                      @endforeach
                  </select>
              </div>

              <div class="oc-filter-block">
                  <label class="oc-filter-label">Lieferant</label>
                  <select name="distributor_id" class="oc-select js-select2" id="filter-distributor-id">
                      <option value="">Alle Lieferanten</option>
                      @foreach($distributors as $dist)
                        <option value="{{ $dist->id }}" @selected($dist->id == $distributorId)>{{ $dist->name ?: $dist->short_name }}</option>
                      @endforeach
                  </select>
              </div>
          </div>

          <div class="oc-toolbar-right">
              <button type="submit" class="oc-btn">Filter anwenden</button>
              <a href="{{ route('admin.products.difference') }}" class="oc-btn-soft">Zurücksetzen</a>
          </div>
      </form>

      <div class="oc-toolbar">
          <div class="oc-toolbar-left">
              <div class="oc-filter-block">
                  <label class="oc-filter-label">Preisart für Vergleich</label>
                  <select id="price-field-select" class="oc-select">
                      <option value="purchase_price">Einkaufspreis vergleichen</option>
                      <option value="price">Verkaufspreis vergleichen</option>
                  </select>
              </div>

              <div class="oc-filter-block">
                  <label class="oc-filter-label">Vergleichsmodus</label>
                  <select id="compare-mode-select" class="oc-select">
                      <option value="all">Alle gewählten Produkte</option>
                      <option value="same_brand">Hinweis, wenn Hersteller abweicht</option>
                      <option value="same_ean">Hinweis, wenn EAN abweicht</option>
                  </select>
              </div>
          </div>

          <div class="oc-toolbar-right">
              <button type="button" class="oc-btn-soft" id="show-warning-btn">Hinweise anzeigen</button>
          </div>
      </div>

      <div class="oc-layout">
          <div class="oc-card">
              <div class="oc-card-head">
                  <div>
                      <h3 class="oc-card-title">Produktliste</h3>
                      <div class="oc-card-sub">
                          Wähle ein oder mehrere Produkte aus. Klicke auf Details, um Produktbild und Stammdaten zu sehen.
                      </div>
                  </div>

                  <div style="font-size:12px;color:#6b7280;text-align:right;">
                      Seite: {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }} von {{ $products->total() }}
                  </div>
              </div>

              <div class="oc-card-body">
                  <div class="oc-selected-panel" id="selected-products-panel">
                      <div class="oc-selected-head">
                          <div class="oc-selected-title">Ausgewählte Produkte für den Vergleich</div>
                          <button type="button" class="oc-btn-soft oc-btn-small" id="clear-selection-top-btn">Auswahl löschen</button>
                      </div>

                      <div class="oc-selected-list" id="selected-products-list"></div>
                  </div>

                  <input
                      type="text"
                      id="product-table-search"
                      class="oc-input search oc-table-search"
                      placeholder="In dieser Liste suchen …"
                  >

                  <div class="oc-table-wrap">
                      <table class="oc-table product-table">
                          <thead>
                              <tr>
                                  <th style="width:32px;">
                                      <input type="checkbox" id="select-all-page">
                                  </th>
                                  <th>Produkt</th>
                                  <th>Marke</th>
                                  <th>Gruppe</th>
                                  <th>EAN</th>
                                  <th>Hersteller-Nr.</th>
                                  <th>Preise</th>
                                  <th>Aktion</th>
                              </tr>
                          </thead>

                          <tbody>
                              @forelse($products as $product)
                                @php
                                  $imageUrl = $productImageUrl($product);
                                  $productUrl = url('product_details/' . $product->id);
                                @endphp

                                <tr
                                    data-name="{{ strtolower($product->product ?? '') }}"
                                    data-brand="{{ strtolower(optional($product->brand)->name ?? '') }}"
                                    data-ean="{{ strtolower($product->ean ?? '') }}"
                                >
                                    <td>
                                        <input
                                            type="checkbox"
                                            class="js-product-checkbox"
                                            value="{{ $product->id }}"
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->product }}"
                                            data-brand="{{ optional($product->brand)->name }}"
                                            data-group="{{ optional($product->articleGroup)->article_group }}"
                                            data-ean="{{ $product->ean }}"
                                            data-article_no="{{ $product->article_no }}"
                                            data-sku="{{ $product->sku }}"
                                            data-model="{{ $product->model }}"
                                            data-prices="{{ $product->distributor_prices_count ?? 0 }}"
                                            data-image="{{ $imageUrl }}"
                                            data-url="{{ $productUrl }}"
                                        >
                                    </td>

                                    <td>
                                        <div class="oc-product-title-cell">
                                            @if($imageUrl)
                                              <img
                                                  src="{{ $imageUrl }}"
                                                  class="oc-product-img js-open-product-detail"
                                                  data-id="{{ $product->id }}"
                                                  data-name="{{ $product->product }}"
                                                  data-brand="{{ optional($product->brand)->name }}"
                                                  data-group="{{ optional($product->articleGroup)->article_group }}"
                                                  data-ean="{{ $product->ean }}"
                                                  data-article_no="{{ $product->article_no }}"
                                                  data-sku="{{ $product->sku }}"
                                                  data-model="{{ $product->model }}"
                                                  data-prices="{{ $product->distributor_prices_count ?? 0 }}"
                                                  data-image="{{ $imageUrl }}"
                                                  data-url="{{ $productUrl }}"
                                                  alt="{{ $product->product }}"
                                              >
                                            @else
                                              <span class="oc-product-img-placeholder">IMG</span>
                                            @endif

                                            <div>
                                                <div class="oc-product-name">{{ $product->product }}</div>
                                                <div class="oc-product-sub">{{ $product->model ?? '—' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>{{ optional($product->brand)->name ?? '—' }}</td>
                                    <td>{{ optional($product->articleGroup)->article_group ?? '—' }}</td>
                                    <td>{{ $product->ean ?? '—' }}</td>
                                    <td>{{ $product->article_no ?? '—' }}</td>

                                    <td>
                                        @if(isset($product->distributor_prices_count))
                                          <span class="oc-badge blue">{{ $product->distributor_prices_count }} Preise</span>
                                        @else
                                          <span class="oc-badge soft">—</span>
                                        @endif
                                    </td>

                                    <td>
                                        <button
                                            type="button"
                                            class="oc-btn-soft oc-btn-small js-open-product-detail"
                                            data-id="{{ $product->id }}"
                                            data-name="{{ $product->product }}"
                                            data-brand="{{ optional($product->brand)->name }}"
                                            data-group="{{ optional($product->articleGroup)->article_group }}"
                                            data-ean="{{ $product->ean }}"
                                            data-article_no="{{ $product->article_no }}"
                                            data-sku="{{ $product->sku }}"
                                            data-model="{{ $product->model }}"
                                            data-prices="{{ $product->distributor_prices_count ?? 0 }}"
                                            data-image="{{ $imageUrl }}"
                                            data-url="{{ $productUrl }}"
                                        >
                                            Details
                                        </button>
                                    </td>
                                </tr>
                              @empty
                                <tr>
                                    <td colspan="8" class="text-center" style="padding:16px;">
                                        Keine Produkte gefunden.
                                    </td>
                                </tr>
                              @endforelse
                          </tbody>
                      </table>
                  </div>

                  <div class="oc-actions-row">
                      <div style="font-size:12px;color:#6b7280;">
                          Ausgewählt: <strong id="selected-count">0</strong> Produkte
                      </div>

                      <div style="display:flex;gap:8px;flex-wrap:wrap;">
                          <button type="button" class="oc-btn-soft" id="clear-selection-btn">Auswahl löschen</button>
                          <button type="button" class="oc-btn" id="compare-btn" disabled>Vergleichen</button>
                      </div>
                  </div>

                  <div class="oc-note">
                      Tipp: Wähle ein Produkt für Lieferantenvergleich. Wähle mehrere ähnliche Produkte für Varianten- und Preisvergleich.
                  </div>

                  @if(method_exists($products, 'links') && $products->hasPages())
                    <div class="oc-pagination">
                        <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap:12px;">
                            <div style="font-size:12px;color:#6b7280;">
                                Zeige <strong>{{ $products->firstItem() ?? 0 }}</strong>
                                bis <strong>{{ $products->lastItem() ?? 0 }}</strong>
                                von <strong>{{ $products->total() }}</strong> Einträgen
                            </div>

                            <div>
                                {{ $products->appends(request()->query())->onEachSide(1)->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                  @endif
              </div>
          </div>

          <div>
              <div class="oc-summary-grid" id="summary-section">
                  <div class="oc-summary-card">
                      <div class="oc-summary-label">Günstigster Preis</div>
                      <div class="oc-summary-value" id="summary-cheapest">—</div>
                      <div class="oc-summary-sub" id="summary-cheapest-sub">Noch kein Vergleich</div>
                  </div>

                  <div class="oc-summary-card">
                      <div class="oc-summary-label">Höchster Preis</div>
                      <div class="oc-summary-value" id="summary-highest">—</div>
                      <div class="oc-summary-sub" id="summary-highest-sub">Noch kein Vergleich</div>
                  </div>

                  <div class="oc-summary-card">
                      <div class="oc-summary-label">Durchschnitt</div>
                      <div class="oc-summary-value" id="summary-average">—</div>
                      <div class="oc-summary-sub">Alle gefundenen Preise</div>
                  </div>

                  <div class="oc-summary-card">
                      <div class="oc-summary-label">Preisabstand</div>
                      <div class="oc-summary-value" id="summary-difference">—</div>
                      <div class="oc-summary-sub" id="summary-difference-sub">Differenz min/max</div>
                  </div>
              </div>

              <div class="oc-chart-grid" id="chart-section">
                  <div class="oc-chart-box">
                      <h3 class="oc-chart-title">Preisvergleich Balkendiagramm</h3>
                      <div class="oc-chart-sub">Preis pro Produkt und Lieferant</div>
                      <canvas id="priceBarChart" height="150"></canvas>
                  </div>

                  <div class="oc-chart-box">
                      <h3 class="oc-chart-title">Lieferanten-Durchschnitt</h3>
                      <div class="oc-chart-sub">Durchschnittspreis je Lieferant</div>
                      <canvas id="pricePieChart" height="150"></canvas>
                  </div>
              </div>

              <div class="oc-card">
                  <div class="oc-card-head">
                      <div>
                          <h3 class="oc-card-title">Vergleich</h3>
                          <div class="oc-card-sub">
                              Tabellenansicht mit Hervorhebung von Preisunterschieden, günstigstem Preis, Verfügbarkeit und Status.
                          </div>
                      </div>

                      <div style="text-align:right;">
                          <div style="font-size:12px;color:#6b7280;margin-bottom:6px;">
                              Lieferantenfilter:
                              <strong>
                                  @if($distributorId)
                                    {{ optional($distributors->firstWhere('id', (int) $distributorId))->name }}
                                  @else
                                    Alle
                                  @endif
                              </strong>
                          </div>

                          <div id="comparison-global-min"></div>
                      </div>
                  </div>

                  <div class="oc-card-body">
                      <div id="comparison-warning-area"></div>

                      <div id="comparison-content">
                          <div class="oc-comparison-placeholder">
                              Noch keine Produkte ausgewählt. Wähle mindestens ein Produkt aus und klicke auf <strong>„Vergleichen“</strong>.
                              Die ausgewählten Produkte werden links oberhalb der Tabelle angezeigt.
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
@endsection

@section('script')
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <script>
      (function () {
          const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
          const compareUrl = "{{ route('admin.products.difference.compare') }}";

          const selectedIds = new Set();
          const selectedMeta = new Map();

          let priceBarChart = null;
          let pricePieChart = null;
          let modalCurrentProductId = null;

          const checkboxes = document.querySelectorAll('.js-product-checkbox');
          const selectAll = document.getElementById('select-all-page');
          const selectedCountEl = document.getElementById('selected-count');
          const selectedCountStatEl = document.getElementById('selected-count-stat');
          const compareBtn = document.getElementById('compare-btn');
          const clearSelectionBtn = document.getElementById('clear-selection-btn');
          const clearSelectionTopBtn = document.getElementById('clear-selection-top-btn');
          const comparisonContent = document.getElementById('comparison-content');
          const filterDistributorSelect = document.getElementById('filter-distributor-id');
          const globalMinEl = document.getElementById('comparison-global-min');
          const tableSearch = document.getElementById('product-table-search');
          const priceFieldSelect = document.getElementById('price-field-select');
          const compareModeSelect = document.getElementById('compare-mode-select');
          const warningArea = document.getElementById('comparison-warning-area');
          const summarySection = document.getElementById('summary-section');
          const chartSection = document.getElementById('chart-section');
          const selectedPanel = document.getElementById('selected-products-panel');
          const selectedList = document.getElementById('selected-products-list');

          window.closeProductDetailModal = function () {
              document.getElementById('productDetailModal')?.classList.remove('open');
          };

          function openProductDetailModal(data) {
              modalCurrentProductId = String(data.id || '');

              const imageBox = document.getElementById('detailImageBox');
              const image = data.image || '';

              if (image) {
                  imageBox.innerHTML = `<img src="${escapeAttr(image)}" class="oc-detail-image" alt="${escapeAttr(data.name || 'Produktbild')}">`;
              } else {
                  imageBox.innerHTML = `<div class="oc-detail-image-placeholder">Kein Bild</div>`;
              }

              document.getElementById('detailTitle').textContent = data.name || 'Unbenanntes Produkt';
              document.getElementById('detailSubtitle').textContent = data.model || 'Kein Modell hinterlegt';
              document.getElementById('detailId').textContent = data.id || '—';
              document.getElementById('detailBrand').textContent = data.brand || '—';
              document.getElementById('detailGroup').textContent = data.group || '—';
              document.getElementById('detailEan').textContent = data.ean || '—';
              document.getElementById('detailArticleNo').textContent = data.article_no || '—';
              document.getElementById('detailSku').textContent = data.sku || '—';
              document.getElementById('detailModel').textContent = data.model || '—';
              document.getElementById('detailPrices').textContent = (data.prices || 0) + ' Lieferantenpreise';

              const badges = document.getElementById('detailBadges');
              badges.innerHTML = `
                  <span class="oc-badge blue">${escapeHtml(data.prices || 0)} Preise</span>
                  ${data.brand ? `<span class="oc-badge green">${escapeHtml(data.brand)}</span>` : ''}
                  ${data.ean ? `<span class="oc-badge soft">EAN ${escapeHtml(data.ean)}</span>` : ''}
                  ${selectedIds.has(String(data.id)) ? `<span class="oc-badge orange">Bereits ausgewählt</span>` : ''}
              `;

              const openLink = document.getElementById('detailOpenLink');
              openLink.href = data.url || '#';

              const selectButton = document.getElementById('detailSelectButton');
              selectButton.textContent = selectedIds.has(String(data.id))
                  ? 'Aus Vergleich entfernen'
                  : 'Für Vergleich auswählen';

              document.getElementById('productDetailModal')?.classList.add('open');
          }

          document.getElementById('detailSelectButton')?.addEventListener('click', function () {
              if (!modalCurrentProductId) {
                  return;
              }

              const cb = document.querySelector('.js-product-checkbox[value="' + cssEscape(modalCurrentProductId) + '"]');

              if (!cb) {
                  toast('warn', 'Nicht auf dieser Seite', 'Dieses Produkt kann auf dieser Seite nicht direkt ausgewählt werden.');
                  return;
              }

              cb.checked = !selectedIds.has(modalCurrentProductId);
              cb.dispatchEvent(new Event('change', { bubbles: true }));

              const detailBtn = document.querySelector('.js-open-product-detail[data-id="' + cssEscape(modalCurrentProductId) + '"]');

              if (detailBtn) {
                  openProductDetailModal(detailBtn.dataset);
              }
          });

          document.querySelectorAll('.js-open-product-detail').forEach(btn => {
              btn.addEventListener('click', function () {
                  openProductDetailModal(this.dataset);
              });
          });

          document.getElementById('productDetailModal')?.addEventListener('click', function (event) {
              if (event.target === this) {
                  closeProductDetailModal();
              }
          });

          function toast(kind, title, msg) {
              const wrap = document.getElementById('toast-wrap');
              if (!wrap) return;

              const icons = {
                  ok: '✓',
                  warn: '!',
                  bad: '×',
                  info: 'i'
              };

              const el = document.createElement('div');
              el.className = 'oc-toast';
              el.innerHTML = `
                  <div class="oc-toast-ic ${kind}">${icons[kind] || icons.info}</div>
                  <div style="flex:1;">
                      <p class="oc-toast-ttl">${escapeHtml(title)}</p>
                      <p class="oc-toast-msg">${escapeHtml(msg)}</p>
                  </div>
                  <button class="oc-toast-x" type="button">×</button>
              `;

              el.querySelector('.oc-toast-x').addEventListener('click', () => el.remove());
              wrap.appendChild(el);

              setTimeout(() => {
                  try { el.remove(); } catch (e) {}
              }, 5000);
          }

          function updateSelectedCount() {
              const count = selectedIds.size;

              selectedCountEl.textContent = count;
              selectedCountStatEl.textContent = count;
              compareBtn.disabled = count < 1;

              if (count === 1) {
                  compareBtn.textContent = 'Lieferantenpreise vergleichen';
              } else {
                  compareBtn.textContent = 'Produkte vergleichen';
              }

              renderSelectedProducts();
          }

          function renderSelectedProducts() {
              if (!selectedPanel || !selectedList) {
                  return;
              }

              if (!selectedMeta.size) {
                  selectedPanel.classList.remove('active');
                  selectedList.innerHTML = '';
                  return;
              }

              selectedPanel.classList.add('active');

              selectedList.innerHTML = Array.from(selectedMeta.entries()).map(([id, meta]) => {
                  return `
                      <div class="oc-selected-chip">
                          ${meta.image ? `<img src="${escapeAttr(meta.image)}" alt="">` : `<span class="oc-product-img-placeholder" style="width:26px;height:26px;font-size:9px;">IMG</span>`}
                          <span class="oc-selected-chip-name" title="${escapeAttr(meta.name)}">${escapeHtml(meta.name || 'Produkt #' + id)}</span>
                          <button type="button" class="oc-selected-chip-remove" data-remove-id="${escapeAttr(id)}">×</button>
                      </div>
                  `;
              }).join('');

              selectedList.querySelectorAll('[data-remove-id]').forEach(btn => {
                  btn.addEventListener('click', function () {
                      const id = this.dataset.removeId;
                      const cb = document.querySelector('.js-product-checkbox[value="' + cssEscape(id) + '"]');

                      if (cb) {
                          cb.checked = false;
                          cb.dispatchEvent(new Event('change', { bubbles: true }));
                      } else {
                          selectedIds.delete(id);
                          selectedMeta.delete(id);
                          updateSelectedCount();
                      }
                  });
              });
          }

          function syncPageCheckboxes() {
              checkboxes.forEach(cb => {
                  cb.checked = selectedIds.has(cb.value);
              });

              const allOnPageSelected = [...checkboxes].length > 0 &&
                  [...checkboxes].every(cb => selectedIds.has(cb.value));

              if (selectAll) {
                  selectAll.checked = allOnPageSelected;
              }
          }

          function addSelection(cb) {
              selectedIds.add(cb.value);

              selectedMeta.set(cb.value, {
                  id: cb.dataset.id || cb.value,
                  name: cb.dataset.name || '',
                  brand: cb.dataset.brand || '',
                  group: cb.dataset.group || '',
                  ean: cb.dataset.ean || '',
                  article_no: cb.dataset.article_no || '',
                  sku: cb.dataset.sku || '',
                  model: cb.dataset.model || '',
                  prices: cb.dataset.prices || 0,
                  image: cb.dataset.image || '',
                  url: cb.dataset.url || ''
              });
          }

          function removeSelection(cb) {
              selectedIds.delete(cb.value);
              selectedMeta.delete(cb.value);
          }

          checkboxes.forEach(cb => {
              cb.addEventListener('change', function () {
                  if (this.checked) {
                      addSelection(this);
                      toast('info', 'Produkt ausgewählt', this.dataset.name || 'Produkt wurde zur Auswahl hinzugefügt.');
                  } else {
                      removeSelection(this);
                  }

                  updateSelectedCount();
                  syncPageCheckboxes();
                  runSelectionWarnings();
              });
          });

          if (selectAll) {
              selectAll.addEventListener('change', function () {
                  if (this.checked) {
                      checkboxes.forEach(cb => {
                          cb.checked = true;
                          addSelection(cb);
                      });

                      toast('ok', 'Auswahl erweitert', 'Alle Produkte auf dieser Seite wurden ausgewählt.');
                  } else {
                      checkboxes.forEach(cb => {
                          cb.checked = false;
                          removeSelection(cb);
                      });

                      toast('warn', 'Auswahl entfernt', 'Alle Produkte auf dieser Seite wurden entfernt.');
                  }

                  updateSelectedCount();
                  runSelectionWarnings();
              });
          }

          function clearSelection() {
              selectedIds.clear();
              selectedMeta.clear();
              updateSelectedCount();
              syncPageCheckboxes();
              hideChartsAndSummary();

              comparisonContent.innerHTML = `
                  <div class="oc-comparison-placeholder">
                      Auswahl wurde gelöscht. Wähle mindestens ein Produkt aus und starte den Vergleich erneut.
                  </div>
              `;

              warningArea.innerHTML = '';
              toast('warn', 'Auswahl gelöscht', 'Die Produktauswahl wurde zurückgesetzt.');
          }

          clearSelectionBtn?.addEventListener('click', clearSelection);
          clearSelectionTopBtn?.addEventListener('click', clearSelection);

          if (tableSearch) {
              tableSearch.addEventListener('keyup', function () {
                  const term = this.value.toLowerCase();

                  document.querySelectorAll('.product-table tbody tr').forEach(row => {
                      const text = row.innerText.toLowerCase();
                      row.style.display = text.includes(term) ? '' : 'none';
                  });
              });
          }

          document.getElementById('show-warning-btn')?.addEventListener('click', function () {
              toast('info', 'Vergleichshinweise', 'Wähle ein Produkt für Lieferantenvergleich oder mehrere ähnliche Produkte für Variantenvergleich. IDS-Preise werden über distributor_prices berücksichtigt.');
          });

          function runSelectionWarnings() {
              const mode = compareModeSelect?.value || 'all';

              if (selectedMeta.size <= 1) {
                  warningArea.innerHTML = '';
                  return;
              }

              const brands = [...selectedMeta.values()].map(v => v.brand || '').filter(Boolean);
              const eans = [...selectedMeta.values()].map(v => v.ean || '').filter(Boolean);

              const uniqueBrands = [...new Set(brands)];
              const uniqueEans = [...new Set(eans)];

              let warnings = [];

              if (mode === 'same_brand' && uniqueBrands.length > 1) {
                  warnings.push('Die ausgewählten Produkte haben unterschiedliche Hersteller. Prüfe, ob sie wirklich vergleichbar sind.');
              }

              if (mode === 'same_ean' && uniqueEans.length > 1) {
                  warnings.push('Die ausgewählten Produkte haben unterschiedliche EANs. Wahrscheinlich sind es nicht exakt gleiche Produkte.');
              }

              if (!warnings.length) {
                  warningArea.innerHTML = '';
                  return;
              }

              warningArea.innerHTML = warnings.map(w => `
                  <div class="oc-warning-panel" style="margin-bottom:12px;">
                      <div class="oc-warning-icon">!</div>
                      <div>
                          <p class="oc-warning-title">Vergleichswarnung</p>
                          <p class="oc-warning-text">${escapeHtml(w)}</p>
                      </div>
                  </div>
              `).join('');
          }

          compareModeSelect?.addEventListener('change', runSelectionWarnings);

          function escapeHtml(str) {
              if (str === null || str === undefined) return '';

              return String(str)
                  .replace(/&/g, '&amp;')
                  .replace(/</g, '&lt;')
                  .replace(/>/g, '&gt;')
                  .replace(/"/g, '&quot;')
                  .replace(/'/g, '&#39;');
          }

          function escapeAttr(str) {
              return escapeHtml(str);
          }

          function cssEscape(value) {
              if (window.CSS && CSS.escape) {
                  return CSS.escape(value);
              }

              return String(value).replace(/"/g, '\\"');
          }

          function money(value) {
              if (value === null || value === undefined || value === '') return '—';
              return Number(value).toFixed(2) + ' €';
          }

          function percent(value) {
              if (value === null || value === undefined || value === '') return '—';
              return Number(value).toFixed(2) + ' %';
          }

          function renderGlobalMin(summary) {
              if (!summary || !summary.cheapest) {
                  globalMinEl.innerHTML = '';
                  return;
              }

              globalMinEl.innerHTML = `
                  <span class="oc-badge green">
                      Günstigster: ${escapeHtml(summary.cheapest.distributor)} · ${money(summary.cheapest.price)}
                  </span>
              `;
          }

          function renderSummary(summary) {
              if (!summary || !summary.price_count) {
                  hideChartsAndSummary();
                  return;
              }

              summarySection.style.display = 'grid';

              document.getElementById('summary-cheapest').textContent = money(summary.cheapest?.price);
              document.getElementById('summary-cheapest-sub').textContent =
                  summary.cheapest ? `${summary.cheapest.distributor} · ${summary.cheapest.product}` : '—';

              document.getElementById('summary-highest').textContent = money(summary.highest?.price);
              document.getElementById('summary-highest-sub').textContent =
                  summary.highest ? `${summary.highest.distributor} · ${summary.highest.product}` : '—';

              document.getElementById('summary-average').textContent = money(summary.average);
              document.getElementById('summary-difference').textContent = money(summary.difference);
              document.getElementById('summary-difference-sub').textContent =
                  summary.percent_difference !== null && summary.percent_difference !== undefined
                      ? `Min/Max Abstand: ${summary.percent_difference}%`
                      : 'Differenz min/max';
          }

          function hideChartsAndSummary() {
              summarySection.style.display = 'none';
              chartSection.style.display = 'none';

              if (priceBarChart) {
                  priceBarChart.destroy();
                  priceBarChart = null;
              }

              if (pricePieChart) {
                  pricePieChart.destroy();
                  pricePieChart = null;
              }
          }

          function renderCharts(data) {
              if (!data.charts || !data.charts.bar || !data.charts.bar.labels.length) {
                  chartSection.style.display = 'none';
                  return;
              }

              chartSection.style.display = 'grid';

              const barCtx = document.getElementById('priceBarChart');
              const pieCtx = document.getElementById('pricePieChart');

              if (priceBarChart) priceBarChart.destroy();
              if (pricePieChart) pricePieChart.destroy();

              priceBarChart = new Chart(barCtx, {
                  type: 'bar',
                  data: {
                      labels: data.charts.bar.labels,
                      datasets: data.charts.bar.datasets
                  },
                  options: {
                      responsive: true,
                      plugins: {
                          legend: { display: true },
                          tooltip: {
                              callbacks: {
                                  label: function(context) {
                                      return context.dataset.label + ': ' + money(context.raw);
                                  }
                              }
                          }
                      },
                      scales: {
                          y: {
                              beginAtZero: true,
                              ticks: {
                                  callback: function(value) {
                                      return value + ' €';
                                  }
                              }
                          }
                      }
                  }
              });

              pricePieChart = new Chart(pieCtx, {
                  type: 'pie',
                  data: {
                      labels: data.charts.pie.labels,
                      datasets: [{
                          label: 'Durchschnittspreis',
                          data: data.charts.pie.data
                      }]
                  },
                  options: {
                      responsive: true,
                      plugins: {
                          tooltip: {
                              callbacks: {
                                  label: function(context) {
                                      return context.label + ': ' + money(context.raw);
                                  }
                              }
                          }
                      }
                  }
              });
          }

          function renderComparison(data) {
              if (!data.products || data.products.length === 0) {
                  comparisonContent.innerHTML =
                      '<div class="oc-comparison-placeholder">Keine Daten zum Vergleich gefunden.</div>';
                  renderGlobalMin(null);
                  hideChartsAndSummary();
                  return;
              }

              renderGlobalMin(data.summary);
              renderSummary(data.summary);

              const products = data.products;
              const grid = data.grid || [];

              if (!grid.length) {
                  comparisonContent.innerHTML = `
                      <div class="oc-comparison-placeholder">
                          Für die ausgewählten Produkte wurden keine Lieferantenpreise gefunden.
                          Prüfe, ob für diese Produkte Einträge in <strong>distributor_prices</strong> vorhanden sind.
                      </div>
                  `;

                  toast('warn', 'Keine Preise gefunden', 'Für die Auswahl wurden keine Lieferantenpreise gefunden.');
                  return;
              }

              let html = '';
              html += '<div class="oc-comparison-wrap">';
              html += '<table class="oc-comparison-table">';
              html += '<thead>';
              html += '<tr>';
              html += '<th>Merkmal</th>';

              products.forEach(p => {
                  html += '<th>';
                  html += '<div class="oc-comparison-product-head">';
                  if (p.image_url) {
                      html += `<img src="${escapeAttr(p.image_url)}" alt="" onclick="openProductImageFromComparison('${escapeAttr(p.image_url)}', '${escapeAttr(p.name)}')">`;
                  }
                  html += '<div>';
                  html += '<div style="font-weight:950;">' + escapeHtml(p.name) + '</div>';
                  html += '<div style="font-size:11px;color:#6b7280;margin-top:3px;">' + escapeHtml(p.article_no || p.ean || '—') + '</div>';
                  html += '</div>';
                  html += '</div>';
                  html += '</th>';
              });

              html += '</tr>';
              html += '</thead>';
              html += '<tbody>';

              function metricRow(label, key) {
                  html += '<tr>';
                  html += '<td class="oc-comparison-metric">' + escapeHtml(label) + '</td>';

                  const values = products.map(p => p[key] ?? '—');
                  const allEqual = values.length > 0 && values.every(v => v === values[0]);

                  products.forEach(p => {
                      const value = p[key] ?? '—';
                      const cellClasses = (!allEqual && value !== '—') ? 'oc-comparison-diff' : '';
                      html += '<td class="' + cellClasses + '">' + escapeHtml(value) + '</td>';
                  });

                  html += '</tr>';
              }

              metricRow('Modell', 'model');
              metricRow('Hersteller-Nr.', 'article_no');
              metricRow('SKU', 'sku');
              metricRow('EAN', 'ean');
              metricRow('Marke', 'brand');
              metricRow('Artikelgruppe', 'article_group');
              metricRow('Farbe', 'color');
              metricRow('Mengeneinheit', 'measure_unit');
              metricRow('Verpackungseinheit', 'package_unit');
              metricRow('Preiseinheit', 'price_unit');

              grid.forEach(row => {
                  html += '<tr class="oc-comparison-distributor-row">';
                  html += '<td colspan="' + (products.length + 1) + '">';
                  html += 'Lieferant: ' + escapeHtml(row.distributor.name);

                  if (row.price_diff && row.price_diff.min_price !== null && row.price_diff.difference !== null) {
                      html += ' &nbsp;&nbsp; <span style="font-size:12px;color:#4b5563;">';
                      html += 'Preisunterschied: ';
                      html += money(row.price_diff.min_price) + ' – ';
                      html += money(row.price_diff.max_price);
                      html += ' · Δ ' + money(row.price_diff.difference);

                      if (row.price_diff.percent_diff) {
                          html += ' · ' + row.price_diff.percent_diff + '%';
                      }

                      html += '</span>';
                  }

                  html += '</td>';
                  html += '</tr>';

                  ['selected_price', 'price', 'purchase_price', 'discount_price', 'discount_percent', 'availability', 'price_date', 'supplier_article_no', 'status'].forEach(metric => {
                      const labels = {
                          selected_price: 'Vergleichspreis',
                          price: 'Verkaufspreis',
                          purchase_price: 'Einkaufspreis',
                          discount_price: 'Rabattpreis',
                          discount_percent: 'Rabatt %',
                          availability: 'Verfügbarkeit',
                          price_date: 'Preisdatum',
                          supplier_article_no: 'Lieferanten-Nr.',
                          status: 'Status'
                      };

                      html += '<tr>';
                      html += '<td class="oc-comparison-metric">' + escapeHtml(labels[metric] || metric) + '</td>';

                      const numericValues = row.products
                          .map(pr => pr[metric])
                          .filter(v => v !== null && v !== undefined && v !== '' && !isNaN(Number(v)))
                          .map(Number);

                      const minValue = numericValues.length ? Math.min(...numericValues) : null;

                      const values = row.products.map(pr => {
                          let v = pr[metric];

                          if (['selected_price', 'price', 'purchase_price', 'discount_price'].includes(metric)) {
                              return money(v);
                          }

                          if (metric === 'discount_percent') {
                              return v !== null && v !== undefined && v !== '' ? percent(v) : '—';
                          }

                          return (v === null || v === undefined || v === '') ? '—' : v;
                      });

                      const allEqual = values.length > 0 && values.every(v => v === values[0]);

                      row.products.forEach((pr, idx) => {
                          const v = values[idx];
                          let cellClasses = '';

                          if (!allEqual && v !== '—') {
                              cellClasses = 'oc-comparison-diff';
                          }

                          if (metric === 'selected_price' && pr[metric] !== null && Number(pr[metric]) === minValue) {
                              cellClasses = 'oc-comparison-best';
                          }

                          html += '<td class="' + cellClasses + '">' + escapeHtml(v) + '</td>';
                      });

                      html += '</tr>';
                  });
              });

              html += '</tbody>';
              html += '</table>';
              html += '</div>';

              comparisonContent.innerHTML = html;
          }

          window.openProductImageFromComparison = function(image, title) {
              openProductDetailModal({
                  name: title,
                  image: image,
                  id: '',
                  brand: '',
                  group: '',
                  ean: '',
                  article_no: '',
                  sku: '',
                  model: '',
                  prices: '',
                  url: '#'
              });
          };

          compareBtn?.addEventListener('click', function () {
              if (selectedIds.size < 1) {
                  toast('warn', 'Keine Auswahl', 'Bitte wähle mindestens ein Produkt aus.');
                  return;
              }

              runSelectionWarnings();

              const distributorId = filterDistributorSelect ? filterDistributorSelect.value : '';
              const priceField = priceFieldSelect?.value || 'purchase_price';

              comparisonContent.innerHTML =
                  '<div class="oc-comparison-placeholder">Daten werden geladen ...</div>';

              globalMinEl.innerHTML = '';
              hideChartsAndSummary();

              fetch(compareUrl, {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': csrfToken,
                      'Accept': 'application/json',
                  },
                  body: JSON.stringify({
                      product_ids: Array.from(selectedIds),
                      distributor_id: distributorId || null,
                      price_field: priceField
                  }),
              })
              .then(resp => {
                  if (!resp.ok) {
                      throw new Error('HTTP ' + resp.status);
                  }

                  return resp.json();
              })
              .then(data => {
                  renderCharts(data);
                  renderComparison(data);

                  const count = data.summary?.price_count || 0;

                  if (count > 0) {
                      toast('ok', 'Vergleich geladen', count + ' Preiswerte wurden gefunden.');
                  } else {
                      toast('warn', 'Keine Preise', 'Für die Auswahl wurden keine Preise gefunden.');
                  }
              })
              .catch(() => {
                  comparisonContent.innerHTML =
                      '<div class="oc-comparison-placeholder">Fehler beim Laden der Vergleichsdaten.</div>';

                  hideChartsAndSummary();
                  renderGlobalMin(null);
                  toast('bad', 'Fehler', 'Die Vergleichsdaten konnten nicht geladen werden.');
              });
          });

          updateSelectedCount();
          syncPageCheckboxes();

          if (window.jQuery) {
              jQuery(function ($) {
                  $('.js-select2').select2({
                      width: '100%',
                      allowClear: true,
                      placeholder: 'Bitte auswählen'
                  });
              });
          }
      })();
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
              label: 'Produktvergleich',
              url: "{{ url()->current() }}",
              clickable: false
          }
      ];

      if (window.setGlobalBreadcrumbs) {
          window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
      }
  </script>
@endpush