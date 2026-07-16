@extends('admin.layouts.app')

@section('title', 'Termin – Profil')

@section('style')
    {{-- Quill editor --}}
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

    <style>
        :root {
            --ap-bg: #f3f4f6;
            --ap-card: #ffffff;
            --ap-text: #111827;
            --ap-muted: #6b7280;
            --ap-border: #e5e7eb;
            --ap-primary: var(--sa-accent);
            --ap-primary-hover: var(--sa-accent-hover);
            --ap-primary-soft: var(--sa-accent-light);
            --ap-blue: #74b2d4;
            --ap-blue-soft: #eff6ff;
            --ap-success: #10b981;
            --ap-success-soft: #ecfdf5;
            --ap-warning: #f59e0b;
            --ap-warning-soft: #fffbeb;
            --ap-danger: #ef4444;
            --ap-danger-soft: #fef2f2;
            --ap-gray-soft: #f9fafb;
            --ap-shadow-sm: 0 1px 2px 0 rgb(0 0 0 / .05);
            --ap-shadow: 0 10px 25px -10px rgb(0 0 0 / .25), 0 4px 8px -4px rgb(0 0 0 / .12);
            --ap-radius: 16px;
            --ap-transition: all .2s ease-in-out;
        }

        body {
            background: var(--ap-bg);
        }

        .appointment-profile-page {
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ap-text);
            max-width: 2000px;
            margin: 18px auto 32px;
            padding: 0 18px 26px;
        }

        .ap-header {
            background: linear-gradient(135deg, #ffffff 0%, #ffffff 55%, var(--ap-primary-soft) 100%);
            border: 1px solid var(--ap-border);
            border-radius: 22px;
            box-shadow: var(--ap-shadow-sm);
            padding: 20px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 16px;
            position: relative;
            overflow: hidden;
        }

        .ap-header:before {
            content: "";
            position: absolute;
            width: 180px;
            height: 180px;
            right: -70px;
            top: -80px;
            border-radius: 50%;
            background: rgba(116, 178, 212, .18);
            pointer-events: none;
        }

        .ap-header-main {
            flex: 1;
            min-width: 0;
            position: relative;
            z-index: 1;
        }

        .ap-title {
            margin: 0 0 8px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 26px;
            font-weight: 900;
            letter-spacing: -.025em;
            line-height: 1.15;
            color: #111827;
        }

        .ap-title-color-dot {
            width: 14px;
            height: 14px;
            border-radius: 999px;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px var(--ap-border);
            flex: 0 0 auto;
        }

        .ap-subline {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 13px;
            color: var(--ap-muted);
            margin-bottom: 12px;
        }

        .ap-subline span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-height: 30px;
            padding: 6px 10px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--ap-border);
            box-shadow: var(--ap-shadow-sm);
        }

        .ap-badges-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px;
        }

        .ap-badge-status,
        .ap-badge-priority,
        .ap-time-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 32px;
            padding: 0 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 900;
            line-height: 1;
            gap: 7px;
            white-space: nowrap;
            border: 1px solid var(--ap-border);
            background: #fff;
        }

        .ap-badge-status {
            text-transform: capitalize;
        }

        .ap-badge-status--planned {
            background: var(--ap-blue-soft);
            border-color: #bfdbfe;
            color: #1d4ed8;
        }

        .ap-badge-status--in_progress {
            background: #ede9fe;
            border-color: #c4b5fd;
            color: #5b21b6;
        }

        .ap-badge-status--done {
            background: var(--ap-success-soft);
            border-color: #a7f3d0;
            color: #047857;
        }

        .ap-badge-status--archived {
            background: #f3f4f6;
            border-color: #d1d5db;
            color: #4b5563;
        }

        .ap-badge-status--junk {
            background: var(--ap-danger-soft);
            border-color: #fecaca;
            color: #b91c1c;
        }

        .ap-badge-priority {
            background: #fff;
            color: #4b5563;
            text-transform: capitalize;
        }

        .ap-badge-priority--veryhigh,
        .ap-badge-priority--high {
            background: var(--ap-warning-soft);
            border-color: #fde68a;
            color: #92400e;
        }

        .ap-badge-priority--medium {
            background: var(--ap-success-soft);
            border-color: #bbf7d0;
            color: #166534;
        }

        .ap-time-badge {
            background: #fff;
            color: #374151;
        }

        .ap-creator-badge {
            gap: 8px;
        }

        .ap-creator-avatar {
            width: 22px;
            height: 22px;
            border-radius: 999px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, .7);
            background: #e5e7eb;
            flex: 0 0 auto;
        }

        .ap-creator-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .ap-header-actions {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            min-width: 260px;
        }

        .ap-back-link {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--ap-muted);
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
        }

        .ap-back-link:hover {
            color: #111827;
            text-decoration: none;
        }

        .ap-header-actions>div {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end;
            width: 100%;
        }

        .ap-select,
        .ap-employee-select,
        .ap-date-input,
        .ap-inline-text-input,
        .ap-report-search-input {
            width: auto;
            min-height: 40px;
            border-radius: 10px;
            border: 1px solid var(--ap-border);
            background: #fff;
            padding: 9px 12px;
            font-size: 13px;
            color: #111827;
            outline: none;
            transition: var(--ap-transition);
        }

        .ap-select:focus,
        .ap-employee-select:focus,
        .ap-date-input:focus,
        .ap-inline-text-input:focus,
        .ap-report-search-input:focus,
        .ap-comment-form textarea:focus,
        .ap-report-comment-input:focus {
            border-color: var(--ap-primary);
            box-shadow: 0 0 0 3px var(--ap-primary-soft);
        }

        .ap-primary-btn,
        .ap-secondary-btn,
        .ap-report-comment-save-btn {
            min-height: 40px;
            border-radius: 10px;
            border: 1px solid transparent;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 900;
            cursor: pointer;
            transition: var(--ap-transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            white-space: nowrap;
        }

        .ap-primary-btn {
            background: var(--ap-primary);
            color: #fff;
            box-shadow: var(--ap-shadow-sm);
        }

        .ap-primary-btn:hover {
            background: var(--ap-primary-hover);
        }

        .ap-secondary-btn,
        .ap-report-comment-save-btn {
            background: #111827;
            color: #fff;
        }

        .ap-secondary-btn:hover,
        .ap-report-comment-save-btn:hover {
            background: #020617;
        }

        .ap-analytics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 16px;
        }

        .ap-stat {
            background: #fff;
            border: 1px solid var(--ap-border);
            border-radius: 16px;
            box-shadow: var(--ap-shadow-sm);
            padding: 16px;
            min-height: 92px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: var(--ap-transition);
        }

        .ap-stat:hover {
            transform: translateY(-1px);
            box-shadow: var(--ap-shadow);
            border-color: rgba(147, 194, 28, .55);
        }

        .ap-stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .ap-stat-icon.report {
            background: var(--ap-blue-soft);
            color: var(--ap-blue);
        }

        .ap-stat-icon.employee {
            background: var(--ap-primary-soft);
            color: var(--ap-primary);
        }

        .ap-stat-icon.comment {
            background: var(--ap-success-soft);
            color: var(--ap-success);
        }

        .ap-stat-icon.activity {
            background: var(--ap-warning-soft);
            color: #d97706;
        }

        .ap-stat-label {
            font-size: 11px;
            font-weight: 900;
            color: var(--ap-muted);
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .ap-stat-value {
            font-size: 24px;
            font-weight: 900;
            color: #111827;
            line-height: 1.1;
            margin-top: 4px;
        }

        .ap-stat-sub {
            font-size: 12px;
            color: var(--ap-muted);
            margin-top: 4px;
        }

        .ap-mobile-nav {
            display: none;
            gap: 8px;
            overflow-x: auto;
            padding: 2px 0 14px;
            margin-bottom: 2px;
            -webkit-overflow-scrolling: touch;
        }

        .ap-mobile-nav a {
            flex: 0 0 auto;
            text-decoration: none;
            color: #374151;
            background: #fff;
            border: 1px solid var(--ap-border);
            border-radius: 999px;
            padding: 9px 13px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .ap-mobile-nav a:hover {
            color: #111827;
            border-color: var(--ap-primary);
            text-decoration: none;
        }

        .ap-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(340px, .9fr);
            gap: 16px;
            align-items: start;
        }

        .ap-layout>div {
            min-width: 0;
        }

        .ap-side-sticky {
            position: sticky;
            top: 18px;
        }

        .ap-card {
            background: #fff;
            border: 1px solid var(--ap-border);
            border-radius: 16px;
            box-shadow: var(--ap-shadow-sm);
            padding: 16px;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .ap-card:hover {
            border-color: rgba(147, 194, 28, .45);
        }

        .ap-card-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
            color: #111827;
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .ap-card-title>span:first-child {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .ap-card-title>span:first-child:before {
            content: "";
            width: 9px;
            height: 9px;
            border-radius: 999px;
            background: var(--ap-primary);
            box-shadow: 0 0 0 4px var(--ap-primary-soft);
        }

        .ap-report-header-meta {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 12px;
            flex-wrap: wrap;
            flex: 1;
        }

        .ap-report-stats {
            display: flex;
            align-items: center;
            gap: 9px;
            flex-wrap: wrap;
        }

        .ap-report-count {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            padding: 0 10px;
            border-radius: 999px;
            background: var(--ap-blue-soft);
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 900;
        }

        .ap-report-author-avatars {
            display: inline-flex;
            align-items: center;
            padding-left: 4px;
        }

        .ap-report-author-avatar {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            overflow: hidden;
            border: 2px solid #fff;
            margin-left: -8px;
            background: #e5e7eb;
            box-shadow: 0 0 0 1px var(--ap-border);
        }

        .ap-report-author-avatar:first-child {
            margin-left: 0;
        }

        .ap-report-author-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .ap-report-search {
            min-width: 220px;
        }

        .ap-report-search-input {
            width: 100%;
            min-height: 36px;
        }

        .ap-report-editor-wrapper {
            border-radius: 14px;
            border: 1px solid var(--ap-border);
            background: #fff;
            overflow: hidden;
        }

        .ap-report-editor-wrapper .ql-toolbar {
            border: none;
            border-bottom: 1px solid var(--ap-border);
            background: #fafafa;
        }

        .ap-report-editor-wrapper .ql-container {
            border: none;
        }

        .ap-report-editor-wrapper .ql-editor {
            min-height: 120px;
            font-size: 14px;
            line-height: 1.6;
        }

        .ap-report-form-footer {
            margin-top: 12px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .ap-inline-inputs {
            display: flex;
            align-items: flex-end;
            gap: 8px;
            flex-wrap: wrap;
            flex: 1;
        }

        .ap-inline-text-input {
            min-width: 240px;
            flex: 1;
        }

        .ap-report-list,
        .ap-comment-list,
        .ap-timeline {
            margin-top: 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 560px;
            overflow-y: auto;
            padding-right: 4px;
        }

        .ap-timeline {
            max-height: 320px;
            list-style: none;
            padding-left: 0;
        }

        .ap-report-item,
        .ap-comment-item {
            background: var(--ap-gray-soft);
            border: 1px solid var(--ap-border);
            border-radius: 14px;
            padding: 12px;
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            gap: 10px;
            transition: var(--ap-transition);
        }

        .ap-report-item:hover,
        .ap-comment-item:hover {
            background: #fff;
            border-color: rgba(116, 178, 212, .65);
            box-shadow: var(--ap-shadow-sm);
        }

        .ap-avatar,
        .ap-avatar-small {
            border-radius: 999px;
            overflow: hidden;
            border: 1px solid var(--ap-border);
            background: #e5e7eb;
            flex: 0 0 auto;
        }

        .ap-avatar {
            width: 38px;
            height: 38px;
        }

        .ap-avatar-small {
            width: 26px;
            height: 26px;
            display: inline-flex;
        }

        .ap-avatar img,
        .ap-avatar-small img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .ap-report-meta,
        .ap-comment-meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 5px;
        }

        .ap-report-author,
        .ap-comment-author {
            font-size: 13px;
            font-weight: 900;
            color: #111827;
        }

        .ap-report-time,
        .ap-comment-time {
            font-size: 12px;
            color: #9ca3af;
            white-space: nowrap;
        }

        .ap-report-content,
        .ap-comment-text {
            font-size: 13px;
            color: #374151;
            line-height: 1.55;
        }

        .ap-comment-text {
            white-space: pre-line;
        }

        .ap-report-content p {
            margin: 0 0 6px;
        }

        .ap-report-next {
            margin-top: 10px;
            padding: 10px;
            border-radius: 12px;
            background: var(--ap-blue-soft);
            border: 1px dashed #bfdbfe;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            font-size: 12px;
            color: #1f2937;
        }

        .ap-report-next-label {
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #1d4ed8;
        }

        .ap-report-next-due {
            margin-left: auto;
            color: #b45309;
            font-weight: 800;
        }

        .ap-report-actions {
            margin-top: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .ap-reaction-btn {
            border-radius: 999px;
            border: 1px solid var(--ap-border);
            background: #fff;
            color: #374151;
            padding: 6px 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            font-size: 12px;
            font-weight: 900;
            transition: var(--ap-transition);
        }

        .ap-reaction-btn:hover,
        .ap-reaction-btn.is-active {
            background: var(--ap-blue-soft);
            border-color: #93c5fd;
            color: #1d4ed8;
        }

        .ap-report-comments {
            margin-top: 12px;
            padding-top: 10px;
            border-top: 1px dashed var(--ap-border);
        }

        .ap-report-comment-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            max-height: 150px;
            overflow-y: auto;
            margin-bottom: 10px;
        }

        .ap-report-comment-item {
            background: #fff;
            border: 1px solid var(--ap-border);
            border-radius: 12px;
            padding: 8px 10px;
            font-size: 12px;
        }

        .ap-report-comment-author {
            font-weight: 900;
            color: #111827;
        }

        .ap-report-comment-time {
            margin-left: 7px;
            color: #9ca3af;
            font-size: 11px;
        }

        .ap-report-comment-text {
            margin-top: 4px;
            color: #374151;
            white-space: pre-line;
        }

        .ap-report-comment-form {
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }

        .ap-report-comment-input {
            flex: 1;
            min-height: 42px;
            border-radius: 10px;
            border: 1px solid var(--ap-border);
            padding: 9px 10px;
            resize: vertical;
            font-size: 13px;
            outline: none;
        }

        .ap-icon-svg {
            width: 14px;
            height: 14px;
            display: inline-block;
            fill: currentColor;
        }

        .ap-timeline-item {
            display: grid;
            grid-template-columns: 18px minmax(0, 1fr);
            gap: 10px;
            background: var(--ap-gray-soft);
            border: 1px solid var(--ap-border);
            border-radius: 14px;
            padding: 12px;
            font-size: 13px;
        }

        .ap-timeline-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            margin-top: 4px;
            background: #94a3b8;
        }

        .ap-timeline-dot--due {
            background: #f97316;
            box-shadow: 0 0 0 4px rgba(249, 115, 22, .18);
        }

        .ap-timeline-dot--status {
            background: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, .18);
        }

        .ap-timeline-dot--created {
            background: #22c55e;
            box-shadow: 0 0 0 4px rgba(34, 197, 94, .18);
        }

        .ap-timeline-title {
            font-weight: 900;
            color: #111827;
        }

        .ap-timeline-text {
            color: #4b5563;
            margin-top: 3px;
        }

        .ap-timeline-meta {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 5px;
        }

        .ap-detail-row {
            display: grid;
            grid-template-columns: 135px minmax(0, 1fr);
            gap: 10px 14px;
            font-size: 13px;
        }

        .ap-detail-label {
            color: var(--ap-muted);
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .04em;
            font-size: 11px;
        }

        .ap-detail-value {
            color: #111827;
            font-weight: 650;
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .ap-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .ap-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 5px 9px 5px 5px;
            border-radius: 999px;
            background: var(--ap-primary-soft);
            border: 1px solid #d9f99d;
            color: #365314;
            font-size: 13px;
            font-weight: 900;
            transition: var(--ap-transition);
        }

        .ap-chip:hover {
            border-color: var(--ap-primary);
            background: #fff;
        }

        .ap-chip-remove {
            border: none;
            background: transparent;
            color: #64748b;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            padding: 0 2px;
        }

        .ap-employee-add-row {
            margin-top: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ap-employee-select {
            flex: 1;
            min-width: 0;
        }

        .ap-comment-form textarea {
            width: 100%;
            min-height: 96px;
            resize: vertical;
            border-radius: 14px;
            border: 1px solid var(--ap-border);
            padding: 12px;
            font-size: 14px;
            outline: none;
            background: #fff;
        }

        .text-right {
            text-align: right;
        }

        .mt-25 {
            margin-top: 10px;
        }

        .mt-50 {
            margin-top: 12px;
        }

        @media (max-width:1200px) {
            .ap-layout {
                grid-template-columns: 1fr;
            }

            .ap-side-sticky {
                position: static;
            }

            .ap-analytics {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width:760px) {
            .appointment-profile-page {
                margin: 0 auto 24px;
                padding: 10px 10px 24px;
            }

            .ap-header {
                border-radius: 18px;
                padding: 16px;
            }

            .ap-title {
                font-size: 21px;
            }

            .ap-header-actions {
                width: 100%;
                min-width: 0;
                align-items: stretch;
            }

            .ap-header-actions>div {
                justify-content: stretch;
            }

            .ap-header-actions .ap-select,
            .ap-header-actions .ap-secondary-btn,
            .ap-primary-btn,
            .ap-secondary-btn {
                width: 100%;
            }

            .ap-subline {
                gap: 7px;
            }

            .ap-subline span {
                width: 100%;
                justify-content: flex-start;
                border-radius: 12px;
            }

            .ap-badges-row>span {
                max-width: 100%;
                white-space: normal;
                justify-content: flex-start;
            }

            .ap-analytics {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .ap-stat {
                min-height: 78px;
                padding: 12px;
            }

            .ap-stat-icon {
                width: 42px;
                height: 42px;
                border-radius: 12px;
            }

            .ap-mobile-nav {
                display: flex;
            }

            .ap-card {
                padding: 13px;
                border-radius: 14px;
                margin-bottom: 12px;
            }

            .ap-card-title {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
            }

            .ap-report-header-meta {
                width: 100%;
                justify-content: space-between;
            }

            .ap-report-search {
                min-width: 0;
                width: 100%;
            }

            .ap-report-form-footer {
                align-items: stretch;
            }

            .ap-inline-inputs {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
            }

            .ap-inline-inputs input,
            .ap-inline-text-input,
            .ap-date-input {
                width: 100%;
                min-width: 0;
            }

            .ap-report-item,
            .ap-comment-item {
                grid-template-columns: 34px minmax(0, 1fr);
                padding: 10px;
            }

            .ap-avatar {
                width: 32px;
                height: 32px;
            }

            .ap-report-meta,
            .ap-comment-meta {
                flex-direction: column;
                gap: 2px;
            }

            .ap-report-next-due {
                margin-left: 0;
                width: 100%;
            }

            .ap-report-comment-form {
                flex-direction: column;
            }

            .ap-report-comment-save-btn {
                width: 100%;
            }

            .ap-detail-row {
                grid-template-columns: 1fr;
                gap: 3px 0;
            }

            .ap-detail-value {
                margin-bottom: 9px;
            }

            .ap-employee-add-row {
                flex-direction: column;
                align-items: stretch;
            }

            .ap-employee-select {
                width: 100%;
            }
        }

        @media (max-width:420px) {
            .ap-title {
                font-size: 19px;
            }

            .ap-stat-value {
                font-size: 21px;
            }

            .ap-report-list,
            .ap-comment-list,
            .ap-timeline {
                max-height: none;
                overflow: visible;
                padding-right: 0;
            }
        }

    /* =========================================================
    Customer + CRM Stage cards
    ========================================================= */
    .ap-crm-overview {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
    margin-bottom: 16px;
    }

    .ap-crm-card {
    background: #fff;
    border: 1px solid var(--ap-border);
    border-radius: 16px;
    box-shadow: var(--ap-shadow-sm);
    padding: 14px;
    min-height: 112px;
    overflow: hidden;
    position: relative;
    }

    .ap-crm-card:before {
    content: "";
    position: absolute;
    right: -34px;
    top: -34px;
    width: 98px;
    height: 98px;
    border-radius: 999px;
    background: rgba(116, 178, 212, .13);
    pointer-events: none;
    }

    .ap-crm-card.stage:before {
    background: rgba(147, 194, 28, .16);
    }

    .ap-crm-kicker {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 10px;
    font-weight: 900;
    letter-spacing: .07em;
    text-transform: uppercase;
    color: var(--ap-muted);
    margin-bottom: 8px;
    position: relative;
    z-index: 1;
    }

    .ap-crm-title {
    font-size: 16px;
    font-weight: 950;
    color: #111827;
    line-height: 1.25;
    position: relative;
    z-index: 1;
    word-break: break-word;
    }

    .ap-crm-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
    position: relative;
    z-index: 1;
    }

    .ap-crm-pill,
    .ap-stage-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    min-height: 28px;
    padding: 5px 9px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 900;
    border: 1px solid var(--ap-border);
    background: #fff;
    color: #334155;
    max-width: 100%;
    }

    .ap-stage-pill {
    border-left-width: 5px;
    background: linear-gradient(135deg, #ffffff, #f8fafc);
    color: #0f172a;
    }

    .ap-stage-pill.sub {
    opacity: .92;
    }

    .ap-crm-empty {
    color: #94a3b8;
    font-size: 13px;
    font-weight: 800;
    position: relative;
    z-index: 1;
    }

    .ap-stage-header-chip {
    border-left-width: 5px;
    background: #fff;
    color: #0f172a;
    }

    .ap-customer-mini {
    display: grid;
    grid-template-columns: 38px minmax(0, 1fr);
    gap: 10px;
    align-items: center;
    }

    .ap-customer-avatar {
    width: 38px;
    height: 38px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--ap-blue), var(--ap-primary));
    color: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 950;
    box-shadow: 0 10px 18px rgba(116, 178, 212, .22);
    }

    @media (max-width: 760px) {
    .ap-crm-overview {
    grid-template-columns: 1fr;
    }
    }


    </style>
@endsection
@php
$creatorUser = $appointment->createdBy;
$creatorEmp = $creatorUser?->employee;

$creatorName = $creatorEmp
    ? trim($creatorEmp->name . ' ' . $creatorEmp->lastname)
    : ($creatorUser->name ?? '–');

$creatorImg = ($creatorEmp && $creatorEmp->image)
    ? asset('images/employee/' . $creatorEmp->image)
    : asset('images/gender/male.png');
@endphp

@section('content')
    @php
$authUser = auth()->user();
$authEmployeeId = optional($authUser)->name ?? optional($authUser)->id;
$employeeMap = $allEmployees->keyBy('id');

$profileCustomer = $appointmentCustomer ?? $appointment->customer;
$profileCustomerName = trim((string) data_get($profileCustomer, 'name') . ' ' . (string) data_get($profileCustomer, 'lastname'));
$profileCustomerName = $profileCustomerName !== '' ? $profileCustomerName : null;
$profileCustomerPhone = data_get($profileCustomer, 'phone') ?: data_get($profileCustomer, 'telephone');
$profileCustomerEmail = data_get($profileCustomer, 'email');
$profileCustomerStreet = data_get($profileCustomer, 'street');
$profileCustomerPostcode = data_get($profileCustomer, 'postcode');
$profileCustomerCity = data_get($profileCustomer, 'city');
$profileCustomerType = data_get($profileCustomer, 'type') ?: data_get($appointment, 'contact_type');
$profileCustomerInitials = collect(explode(' ', $profileCustomerName ?: 'Kunde'))->filter()->map(fn($part) => mb_substr($part, 0, 1))->take(2)->implode('');

$profileStage = $appointmentStageContext ?? [];
$profileLeadProductListId = data_get($profileStage, 'lead_product_list_id');
$profileLeadStageName = data_get($profileStage, 'lead_stage_name') ?: optional($appointment->leadStage ?? null)->name;
$profileLeadStageColor = data_get($profileStage, 'lead_stage_color') ?: optional($appointment->leadStage ?? null)->color ?: '#74b2d4';
$profileLeadSubStageName = data_get($profileStage, 'lead_stage_sub_stage_name') ?: optional($appointment->leadStageSubStage ?? null)->name;
$profileLeadSubStageColor = data_get($profileStage, 'lead_stage_sub_stage_color') ?: optional($appointment->leadStageSubStage ?? null)->color ?: '#93c21c';
$profileProductName = data_get($profileStage, 'product_name') ?: data_get($profileStage, 'article_group');
$profileObjectName = data_get($profileStage, 'object_name');
    @endphp

    {{-- simple icon sprite for report comment button --}}
    <svg width="0" height="0" style="position:absolute;visibility:hidden">
        <symbol id="icon-comment" viewBox="0 0 20 20">
            <path
                d="M3 3h14a1 1 0 0 1 1 1v9.5a1 1 0 0 1-1 1H9l-3.5 3.5A1 1 0 0 1 4 17v-2H3a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" />
        </symbol>
    </svg>

    <div class="app-content">
        <div class="content-wrapper">

            <div class="content-body">
                <div class="appointment-profile-page" data-appointment-id="{{ $appointment->id }}"
                    data-status="{{ $appointment->status ?? 'planned' }}">

                    {{-- HEADER --}}
                    <div class="ap-header">
                        <div class="ap-header-main">
                            <div class="ap-title">
                                @if($appointment->color)
                                    <span class="ap-title-color-dot" style="background: {{ $appointment->color }};"></span>
                                @endif
                                <span>{{ $appointment->name }}</span>
                            </div>

                            <div class="ap-subline">
                                <span>
                                    <i class="fa fa-calendar"></i>
                                    {{ $appointment->start_date?->format('d.m.Y') }}
                                    @if($appointment->start_time)
                                        – {{ \Illuminate\Support\Str::of($appointment->start_time)->substr(0, 5) }}
                                    @endif
                                    @if($appointment->end_date || $appointment->end_time)
                                        &nbsp;→
                                        {{ $appointment->end_date?->format('d.m.Y') ?? '' }}
                                        @if($appointment->end_time)
                                            {{ \Illuminate\Support\Str::of($appointment->end_time)->substr(0, 5) }}
                                        @endif
                                    @endif
                                </span>

                                @if($profileCustomerName)
                                    <span>
                                        <i class="fa fa-user"></i>
                                        {{ $profileCustomerName }}
                                        @if($profileCustomerType)
                                            · {{ $profileCustomerType }}
                                        @endif
                                    </span>
                                @endif

                                @if($appointment->city || $appointment->street)
                                    <span>
                                        <i class="fa fa-map-marker"></i>
                                        {{ $appointment->street }}
                                        @if($appointment->postcode || $appointment->city)
                                            , {{ $appointment->postcode }} {{ $appointment->city }}
                                        @endif
                                    </span>
                                @endif
                            </div>

                            <div class="mt-50 ap-badges-row">

                                <span class="ap-badge-status ap-badge-status--{{ $appointment->status ?? 'planned' }}">
                                    {{ $appointment->status ?? 'planned' }}
                                </span>

                                @php
$priorityKey = strtolower(str_replace(' ', '', $appointment->priority ?? ''));
                                @endphp
                                @if($appointment->priority)
                                    <span class="ap-badge-priority ap-badge-priority--{{ $priorityKey }}">
                                        Priorität: {{ $appointment->priority }}
                                    </span>
                                @endif

                                @if($profileLeadStageName)
                                    <span class="ap-badge-priority ap-stage-header-chip"
                                        style="border-left-color: {{ $profileLeadStageColor }};">
                                        CRM Stage: {{ $profileLeadStageName }}
                                    </span>
                                @endif

                                @if($profileLeadSubStageName)
                                    <span class="ap-badge-priority ap-stage-header-chip"
                                        style="border-left-color: {{ $profileLeadSubStageColor }};">
                                        Sub Stage: {{ $profileLeadSubStageName }}
                                    </span>
                                @endif

                                @if($timeBadgeLabel)
                                    <span class="ap-time-badge">
                                        <i class="fa fa-clock-o"></i>
                                        Erstellt am: {{ $appointment->created_at?->format('d.m.Y H:i') }}
                                    </span>
                                @endif

                                <span class="ap-time-badge ap-creator-badge" title="Erstellt von {{ $creatorName }}">
                                    <span class="ap-creator-avatar">
                                        <img src="{{ $creatorImg }}" alt="{{ $creatorName }}">
                                    </span>
                                    <span>Erstellt von: {{ $creatorName }}</span>
                                </span>

                            </div>

                        </div>

                        <div class="ap-header-actions">
                            <a href="{{ route('customer.appointments.index') }}" class="ap-back-link">
                                <i class="fa fa-angle-left"></i> Zurück zur Übersicht
                            </a>

                            {{-- Status selector --}}
                            <div>
                                <select id="apStatusSelect" class="ap-select">
                                    @foreach(['planned' => 'Geplant', 'in_progress' => 'In Arbeit', 'done' => 'Erledigt', 'archived' => 'Archiviert', 'junk' => 'Junk'] as $key => $label)
                                        <option value="{{ $key }}" @selected(($appointment->status ?? 'planned') === $key)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button" class="ap-secondary-btn" id="apStatusSaveBtn">
                                    Status speichern
                                </button>
                            </div>
                        </div>
                    </div>

                    @php
$reportCountHeader = $reports->count();
$employeeCountHeader = $appointment->employees->count();
$commentCountHeader = $comments->count();
$activityCountHeader = collect($notificationItems ?? [])->count();
                    @endphp

                    <div class="ap-analytics">
                        <div class="ap-stat">
                            <div class="ap-stat-icon report">
                                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M4 5h16M4 12h16M4 19h10" />
                                </svg>
                            </div>
                            <div>
                                <div class="ap-stat-label">Reports</div>
                                <div class="ap-stat-value">{{ $reportCountHeader }}</div>
                                <div class="ap-stat-sub">Gespeicherte Berichte</div>
                            </div>
                        </div>

                        <div class="ap-stat">
                            <div class="ap-stat-icon employee">
                                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87" />
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75" />
                                </svg>
                            </div>
                            <div>
                                <div class="ap-stat-label">Team</div>
                                <div class="ap-stat-value">{{ $employeeCountHeader }}</div>
                                <div class="ap-stat-sub">Teilnehmende Mitarbeiter</div>
                            </div>
                        </div>

                        <div class="ap-stat">
                            <div class="ap-stat-icon comment">
                                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z" />
                                </svg>
                            </div>
                            <div>
                                <div class="ap-stat-label">Kommentare</div>
                                <div class="ap-stat-value">{{ $commentCountHeader }}</div>
                                <div class="ap-stat-sub">Interne Notizen</div>
                            </div>
                        </div>

                        <div class="ap-stat">
                            <div class="ap-stat-icon activity">
                                <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor"
                                    stroke-width="2">
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                                </svg>
                            </div>
                            <div>
                                <div class="ap-stat-label">Aktivitäten</div>
                                <div class="ap-stat-value">{{ $activityCountHeader }}</div>
                                <div class="ap-stat-sub">Timeline-Einträge</div>
                            </div>
                        </div>
                    </div>

                    <div class="ap-mobile-nav" aria-label="Termin Navigation">
                        <a href="#apReports">Reports</a>
                        <a href="#apCustomerStage">Kunde / CRM</a>
                        <a href="#apDetails">Details</a>
                        <a href="#apEmployees">Mitarbeiter</a>
                        <a href="#apComments">Kommentare</a>
                        <a href="#apNotifications">Aktivität</a>
                    </div>

                    {{-- MAIN LAYOUT --}}
                    <div class="ap-layout">
                        {{-- LEFT COLUMN: Reports + Notifications --}}
                        <div>
                            @php
$reportCount = $reports->count();
$reportAuthors = $reports->pluck('employee')->filter()->unique('id');
                            @endphp

                            {{-- REPORTS --}}
                            <div class="ap-card ap-panel" id="apReports">
                                <div class="ap-card-title">
                                    <span>Report</span>

                                    <div class="ap-report-header-meta">
                                        <div class="ap-report-stats">
                                            <span class="ap-report-count">
                                                <span id="apReportCountFiltered">{{ $reportCount }}</span> /
                                                <span id="apReportCountTotal">{{ $reportCount }}</span> Reports
                                            </span>

                                            <div class="ap-report-author-avatars">
                                                @foreach($reportAuthors as $author)
                                                                                        <div class="ap-report-author-avatar"
                                                                                            title="{{ $author->name }} {{ $author->lastname }}">
                                                                                            <img src="{{ $author->image
        ? asset('images/employee/' . $author->image)
        : asset('images/employee/default.png') }}"
                                                                                                alt="{{ $author->name }}">
                                                                                        </div>
                                                @endforeach
                                            </div>
                                        </div>

                                        <div class="ap-report-search">
                                            <input type="search" id="apReportSearchInput" class="ap-report-search-input"
                                                placeholder="Reports durchsuchen …">
                                        </div>
                                    </div>
                                </div>

                                {{-- Report form --}}
                                <form class="ap-report-form" id="apReportForm">
                                    @csrf

                                    <div class="ap-report-editor-wrapper">
                                        <div id="apReportEditor"></div>
                                    </div>

                                    <div class="ap-report-form-footer">
                                        <div class="ap-inline-inputs">
                                            <input type="date" name="report_date" id="apReportDate" class="ap-date-input"
                                                value="{{ now()->toDateString() }}">

                                            <input type="text" name="next_step" id="apNextStepInput"
                                                class="ap-inline-text-input" placeholder="Nächster Schritt kurz …">

                                            <input type="date" name="due_date" id="apDueDateInput" class="ap-date-input"
                                                placeholder="Fälligkeitsdatum">
                                        </div>

                                        <button type="button" class="ap-primary-btn" id="apReportSaveBtn">
                                            Speichern
                                        </button>
                                    </div>
                                </form>

                                {{-- Report list --}}
                                <div class="ap-report-list" id="apReportList">
                                    @foreach($reports as $r)
                                        @php
    $reaction = $r->reactionOf($authEmployeeId);
    $likes = $r->likes_count ?? 0;
    $dislikes = $r->dislikes_count ?? 0;
                                        @endphp
                                        <div class="ap-report-item" data-report-id="{{ $r->id }}">
                                            <div class="ap-avatar">
                                                <img src="{{ $r->employee->image ? asset('images/employee/' . $r->employee->image) : asset('images/employee/default.png') }}"
                                                    alt="{{ $r->employee->name }}">
                                            </div>
                                            <div>
                                                <div class="ap-report-meta">
                                                    <span class="ap-report-author">
                                                        {{ $r->employee->name }} {{ $r->employee->lastname }}
                                                    </span>
                                                    <span class="ap-report-time">
                                                        {{ $r->created_at->diffForHumans() }}
                                                    </span>
                                                </div>

                                                <div class="ap-report-content">
                                                    {!! clean($r->report) !!}
                                                </div>

                                                @if($r->next_step || $r->due_date)
                                                    <div class="ap-report-next">
                                                        <span class="ap-report-next-label">Next Step</span>
                                                        <span>{{ $r->next_step ?? '–' }}</span>

                                                        @if($r->due_date)
                                                            <span class="ap-report-next-due">
                                                                Fällig: {{ $r->due_date->format('d.m.Y') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif

                                                {{-- reactions --}}
                                                <div class="ap-report-actions" data-report-id="{{ $r->id }}"
                                                    data-reaction="{{ $reaction ?? '' }}">
                                                    <button type="button"
                                                        class="ap-reaction-btn ap-report-like-btn {{ $reaction === 'like' ? 'is-active' : '' }}">
                                                        <span>👍</span>
                                                        <span class="ap-reaction-count">{{ $likes }}</span>
                                                    </button>
                                                    <button type="button"
                                                        class="ap-reaction-btn ap-report-dislike-btn {{ $reaction === 'dislike' ? 'is-active' : '' }}">
                                                        <span>👎</span>
                                                        <span class="ap-reaction-count">{{ $dislikes }}</span>
                                                    </button>
                                                </div>

                                                {{-- comments per report --}}
                                                <div class="ap-report-comments" data-report-id="{{ $r->id }}">
                                                    <div class="ap-report-comment-list">
                                                        @foreach($r->comment_items ?? [] as $item)
                                                            @php
        $commentEmployee = isset($item['employee_id'])
            ? $employeeMap->get($item['employee_id'])
            : null;

        $authorName = $item['author_name']
            ?? ($commentEmployee
                ? $commentEmployee->name . ' ' . $commentEmployee->lastname
                : ('Mitarbeiter #' . ($item['employee_id'] ?? '')));
                                                            @endphp
                                                            <div class="ap-report-comment-item">
                                                                <div>
                                                                    <span class="ap-report-comment-author">
                                                                        {{ $authorName }}
                                                                    </span>
                                                                    @if(!empty($item['created_at_human']))
                                                                        <span class="ap-report-comment-time">
                                                                            {{ $item['created_at_human'] }}
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <div class="ap-report-comment-text">
                                                                    {{ $item['text'] ?? '' }}
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>

                                                    <div class="ap-report-comment-form">
                                                        <textarea class="ap-report-comment-input"
                                                            placeholder="Kommentar zu diesem Report …"></textarea>
                                                        <button type="button" class="ap-report-comment-save-btn">
                                                            <svg class="ap-icon-svg" aria-hidden="true">
                                                                <use xlink:href="#icon-comment"></use>
                                                            </svg>
                                                            <span>Speichern</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- NOTIFICATIONS --}}
                            <div class="ap-card ap-panel" id="apNotifications">
                                <div class="ap-card-title">
                                    <span>Aktivitäten / Benachrichtigungen</span>
                                </div>

                                <ul class="ap-timeline" id="apNotificationTimeline">
                                    @forelse($notificationItems as $n)
                                        @php
    $kind = $n['kind'] ?? 'generic';
    $dotClass = 'ap-timeline-dot';
    if ($kind === 'due')
        $dotClass .= ' ap-timeline-dot--due';
    elseif ($kind === 'status')
        $dotClass .= ' ap-timeline-dot--status';
    elseif ($kind === 'created')
        $dotClass .= ' ap-timeline-dot--created';
                                        @endphp
                                        <li class="ap-timeline-item">
                                            <div class="{{ $dotClass }}"></div>
                                            <div>
                                                <div class="ap-timeline-title">{{ $n['title'] }}</div>
                                                @if($n['message'])
                                                    <div class="ap-timeline-text">{{ $n['message'] }}</div>
                                                @endif
                                                <div class="ap-timeline-meta">{{ $n['created_at'] }}</div>
                                            </div>
                                        </li>
                                    @empty
                                        <li class="ap-timeline-item">
                                            <div class="ap-timeline-dot"></div>
                                            <div>
                                                <div class="ap-timeline-text">Noch keine Benachrichtigungen.</div>
                                            </div>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        {{-- RIGHT COLUMN: Details + Employees + Comments --}}
                        <div class="ap-side-sticky">
                            {{-- CUSTOMER + CRM STAGE --}}
                            <div class="ap-crm-overview" id="apCustomerStage">
                                <div class="ap-crm-card customer">
                                    <div class="ap-crm-kicker">
                                        <i class="fa fa-user"></i>
                                        Kunde / Kontakt
                                    </div>

                                    @if($profileCustomerName)
                                        <div class="ap-customer-mini">
                                            <span class="ap-customer-avatar">{{ $profileCustomerInitials }}</span>
                                            <div>
                                                <div class="ap-crm-title">{{ $profileCustomerName }}</div>
                                                <div class="ap-crm-meta">
                                                    @if($profileCustomerType)
                                                        <span class="ap-crm-pill">{{ $profileCustomerType }}</span>
                                                    @endif
                                                    @if($profileCustomerPhone)
                                                        <span class="ap-crm-pill"><i class="fa fa-phone"></i>
                                                            {{ $profileCustomerPhone }}</span>
                                                    @endif
                                                    @if($profileCustomerEmail)
                                                        <span class="ap-crm-pill"><i class="fa fa-envelope"></i>
                                                            {{ $profileCustomerEmail }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        @if($profileCustomerStreet || $profileCustomerPostcode || $profileCustomerCity)
                                            <div class="ap-crm-meta">
                                                <span class="ap-crm-pill">
                                                    <i class="fa fa-map-marker"></i>
                                                    {{ trim(($profileCustomerStreet ?? '') . ', ' . ($profileCustomerPostcode ?? '') . ' ' . ($profileCustomerCity ?? ''), ' ,') }}
                                                </span>
                                            </div>
                                        @endif
                                    @else
                                        <div class="ap-crm-empty">Kein Kunde/Kontakt verknüpft.</div>
                                    @endif
                                </div>

                                <div class="ap-crm-card stage">
                                    <div class="ap-crm-kicker">
                                        <i class="fa fa-random"></i>
                                        CRM Stage
                                    </div>

                                    @if($profileLeadStageName || $profileLeadSubStageName || $profileLeadProductListId)
                                        <div class="ap-crm-title">
                                            {{ $profileLeadStageName ?: 'Stage nicht gesetzt' }}
                                        </div>
                                        <div class="ap-crm-meta">
                                            @if($profileLeadStageName)
                                                <span class="ap-stage-pill"
                                                    style="border-left-color: {{ $profileLeadStageColor }};">
                                                    Stage: {{ $profileLeadStageName }}
                                                </span>
                                            @endif
                                            @if($profileLeadSubStageName)
                                                <span class="ap-stage-pill sub"
                                                    style="border-left-color: {{ $profileLeadSubStageColor }};">
                                                    Sub: {{ $profileLeadSubStageName }}
                                                </span>
                                            @endif
                                            @if($profileLeadProductListId)
                                                <span class="ap-crm-pill">Lead Product #{{ $profileLeadProductListId }}</span>
                                            @endif
                                            @if($profileProductName)
                                                <span class="ap-crm-pill">{{ $profileProductName }}</span>
                                            @endif
                                            @if($profileObjectName)
                                                <span class="ap-crm-pill">{{ $profileObjectName }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="ap-crm-empty">Noch keine CRM Stage/Sub Stage gespeichert.</div>
                                    @endif
                                </div>
                            </div>

                            {{-- DETAILS --}}
                            <div class="ap-card ap-panel" id="apDetails">
                                <div class="ap-card-title">
                                    <span>Details</span>
                                </div>
                                <div class="ap-detail-row">
                                    <div class="ap-detail-label">Art</div>
                                    <div class="ap-detail-value">
                                        {{ $appointment->appointment_type ?? '–' }}
                                    </div>

                                    <div class="ap-detail-label">Ausführung</div>
                                    <div class="ap-detail-value">
                                        {{ $appointment->execution_type ?? '–' }}
                                    </div>

                                    <div class="ap-detail-label">Kunde</div>
                                    <div class="ap-detail-value">
                                        {{ $profileCustomerName ?: '–' }}
                                        @if($profileCustomerType)
                                            · {{ $profileCustomerType }}
                                        @endif
                                    </div>

                                    <div class="ap-detail-label">CRM Stage</div>
                                    <div class="ap-detail-value">
                                        @if($profileLeadStageName || $profileLeadSubStageName)
                                            @if($profileLeadStageName)
                                                <span class="ap-stage-pill"
                                                    style="border-left-color: {{ $profileLeadStageColor }};">
                                                    {{ $profileLeadStageName }}
                                                </span>
                                            @endif
                                            @if($profileLeadSubStageName)
                                                <span class="ap-stage-pill sub"
                                                    style="border-left-color: {{ $profileLeadSubStageColor }};">
                                                    {{ $profileLeadSubStageName }}
                                                </span>
                                            @endif
                                        @else
                                            –
                                        @endif
                                    </div>

                                    <div class="ap-detail-label">Kontakt</div>
                                    <div class="ap-detail-value">
                                        {{ $appointment->phone ?? $profileCustomerPhone ?? '–' }}
                                        @if($appointment->email ?? $profileCustomerEmail)
                                            · {{ $appointment->email ?? $profileCustomerEmail }}
                                        @endif
                                    </div>

                                    <div class="ap-detail-label">Adresse</div>
                                    <div class="ap-detail-value">
                                        {{ $appointment->full_address
    ?? trim(($appointment->street ?? '') . ', ' . ($appointment->postcode ?? '') . ' ' . ($appointment->city ?? '')) ?: '–' }}
                                    </div>

                                    <div class="ap-detail-label">Beschreibung</div>
                                    <div class="ap-detail-value">
                                        {{ $appointment->note ?: '–' }}
                                    </div>

                                    <div class="ap-detail-label">Nächster Schritt</div>
                                    <div class="ap-detail-value">
                                        {{ $appointment->next_step ?? '–' }}
                                    </div>
                                </div>
                            </div>

                            {{-- EMPLOYEES --}}
                            <div class="ap-card ap-panel" id="apEmployees">
                                <div class="ap-card-title">
                                    <span>Teilnehmende Mitarbeiter</span>
                                </div>

                                <div class="ap-chip-list" id="apEmployeeChipList">
                                    @foreach($appointment->employees as $e)
                                        <div class="ap-chip" data-employee-id="{{ $e->id }}">
                                            <span class="ap-avatar-small">
                                                <img src="{{ $e->image ? asset('images/employee/' . $e->image) : asset('images/employee/default.png') }}"
                                                    alt="{{ $e->name }}">
                                            </span>
                                            <span>{{ $e->name }} {{ $e->lastname }}</span>
                                            <button type="button" class="ap-chip-remove apRemoveEmployeeBtn">
                                                &times;
                                            </button>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="ap-employee-add-row">
                                    <select id="apEmployeeSelect" class="ap-employee-select">
                                        <option value="">Mitarbeiter auswählen …</option>
                                        @foreach($allEmployees as $e)
                                            <option value="{{ $e->id }}">
                                                {{ $e->name }} {{ $e->lastname }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="ap-secondary-btn" id="apAddEmployeeBtn">
                                        Hinzufügen
                                    </button>
                                </div>
                            </div>

                            {{-- COMMENTS (global for appointment) --}}
                            <div class="ap-card ap-panel" id="apComments">
                                <div class="ap-card-title">
                                    <span>Kommentare</span>
                                </div>

                                <form class="ap-comment-form" id="apCommentForm">
                                    @csrf
                                    <textarea name="comment" id="apCommentText"
                                        placeholder="Kommentar hinzufügen …"></textarea>
                                    <div class="text-right mt-25">
                                        <button type="button" class="ap-secondary-btn" id="apCommentSaveBtn">
                                            Speichern
                                        </button>
                                    </div>
                                </form>

                                <div class="ap-comment-list" id="apCommentList">
                                    @foreach($comments as $c)
                                        <div class="ap-comment-item">
                                            <div class="ap-avatar">
                                                <img src="{{ $c->employee->image ? asset('images/employee/' . $c->employee->image) : asset('images/employee/default.png') }}"
                                                    alt="{{ $c->employee->name }}">
                                            </div>
                                            <div>
                                                <div class="ap-comment-meta">
                                                    <span class="ap-comment-author">
                                                        {{ $c->employee->name }} {{ $c->employee->lastname }}
                                                    </span>
                                                    <span class="ap-comment-time">
                                                        {{ $c->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <div class="ap-comment-text">
                                                    {{ $c->comment }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                        </div> {{-- /right column --}}
                    </div> {{-- /ap-layout --}}

                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    {{-- Quill --}}
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>

    <script>
        (function () {
            "use strict";

            const appointmentId = document.querySelector('.appointment-profile-page')?.dataset.appointmentId;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

            const routes = {
                updateStatus: "{{ url('customer/appointments') }}/" + appointmentId + "/status",
                addEmployee: "{{ url('customer/appointments') }}/" + appointmentId + "/employees",
                removeEmployeeBase: "{{ url('customer/appointments') }}/" + appointmentId + "/employees",
                storeReport: "{{ route('customer.appointments.reports.store', $appointment) }}",
                storeComment: "{{ route('customer.appointments.comments.store', $appointment) }}",
                notifications: "{{ route('customer.appointments.notifications', $appointment) }}",
                reactReportBase: "{{ url('customer/appointments/' . $appointment->id . '/reports') }}",
                reportCommentBase: "{{ url('customer/appointments/' . $appointment->id . '/reports') }}",
            };

            const employeeImageBaseUrl = "{{ asset('images/employee') }}";
            const employeeDefaultImageUrl = "{{ asset('images/employee/default.png') }}";

            const statusSelect = document.getElementById('apStatusSelect');
            const statusBtn = document.getElementById('apStatusSaveBtn');

            const reportForm = document.getElementById('apReportForm');
            const reportList = document.getElementById('apReportList');

            const reportDate = document.getElementById('apReportDate');
            const nextStepInput = document.getElementById('apNextStepInput');
            const dueDateInput = document.getElementById('apDueDateInput');
            const reportBtn = document.getElementById('apReportSaveBtn');

            const reportSearchInput = document.getElementById('apReportSearchInput');
            const reportCountFiltered = document.getElementById('apReportCountFiltered');

            const empSelect = document.getElementById('apEmployeeSelect');
            const empAddBtn = document.getElementById('apAddEmployeeBtn');
            const empChipList = document.getElementById('apEmployeeChipList');

            const commentText = document.getElementById('apCommentText');
            const commentBtn = document.getElementById('apCommentSaveBtn');
            const commentList = document.getElementById('apCommentList');

            const notifTimeline = document.getElementById('apNotificationTimeline');

            let quill = null;
            const reportEditorEl = document.getElementById('apReportEditor');
            if (reportEditorEl && window.Quill) {
                quill = new Quill('#apReportEditor', {
                    theme: 'snow',
                    modules: {
                        toolbar: [
                            ['bold', 'italic', 'underline'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            [{ 'header': [false, 3, 4] }],
                            ['link'],
                            ['clean']
                        ]
                    }
                });
            }

            function showToast(msg, type) {
                if (window.Swal) {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2500,
                        icon: type || 'success',
                        title: msg || 'Gespeichert',
                    });
                } else {
                    alert(msg);
                }
            }

            function applyReportFilter() {
                if (!reportList) return;

                const query = (reportSearchInput?.value || '').trim().toLowerCase();
                const items = reportList.querySelectorAll('.ap-report-item');

                let visible = 0;

                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    const match = !query || text.includes(query);

                    item.style.display = match ? 'grid' : 'none';
                    if (match) visible++;
                });

                if (reportCountFiltered) {
                    reportCountFiltered.textContent = visible;
                }
            }

            if (reportSearchInput) {
                reportSearchInput.addEventListener('input', applyReportFilter);
            }

            // STATUS UPDATE
            if (statusBtn && statusSelect) {
                statusBtn.addEventListener('click', async function () {
                    const status = statusSelect.value;
                    try {
                        const resp = await fetch(routes.updateStatus, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ status }),
                        });

                        if (!resp.ok) throw new Error();

                        showToast('Status aktualisiert.', 'success');
                    } catch (e) {
                        console.error(e);
                        showToast('Status konnte nicht gespeichert werden.', 'error');
                    }
                });
            }

            // ADD EMPLOYEE
            if (empAddBtn && empSelect) {
                empAddBtn.addEventListener('click', async function () {
                    const val = empSelect.value;
                    const text = empSelect.options[empSelect.selectedIndex]?.text || '';
                    if (!val) return;

                    try {
                        const resp = await fetch(routes.addEmployee, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ employee_id: val }),
                        });
                        if (!resp.ok) throw new Error();

                        const chip = document.createElement('div');
                        chip.className = 'ap-chip';
                        chip.dataset.employeeId = val;
                        chip.innerHTML = `
                                    <span class="ap-avatar-small">
                                        <img src="${employeeDefaultImageUrl}" alt="">
                                    </span>
                                    <span>${text}</span>
                                    <button type="button" class="ap-chip-remove apRemoveEmployeeBtn">&times;</button>
                                `;
                        empChipList.appendChild(chip);
                        showToast('Mitarbeiter hinzugefügt.', 'success');
                    } catch (e) {
                        console.error(e);
                        showToast('Mitarbeiter konnte nicht hinzugefügt werden.', 'error');
                    }
                });
            }

            // REMOVE EMPLOYEE (delegated)
            if (empChipList) {
                empChipList.addEventListener('click', async function (e) {
                    const btn = e.target.closest('.apRemoveEmployeeBtn');
                    if (!btn) return;
                    const chip = btn.closest('.ap-chip');
                    if (!chip) return;
                    const empId = chip.dataset.employeeId;
                    if (!empId) return;

                    try {
                        const resp = await fetch(routes.removeEmployeeBase + '/' + empId, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            }
                        });
                        if (!resp.ok) throw new Error();

                        chip.remove();
                        showToast('Mitarbeiter entfernt.', 'success');
                    } catch (err) {
                        console.error(err);
                        showToast('Mitarbeiter konnte nicht entfernt werden.', 'error');
                    }
                });
            }

            // STORE REPORT
            if (reportBtn && reportForm) {
                reportBtn.addEventListener('click', async function () {
                    let html = '';
                    if (quill) {
                        html = quill.root.innerHTML.trim();
                        if (html === '<p><br></p>') {
                            html = '';
                        }
                    }
                    if (!html) return;

                    const dateVal = reportDate?.value || '';
                    const nextStep = nextStepInput?.value.trim() || '';
                    const dueDate = dueDateInput?.value || '';

                    try {
                        const fd = new FormData();
                        fd.append('report', html);
                        if (dateVal) fd.append('report_date', dateVal);
                        if (nextStep) fd.append('next_step', nextStep);
                        if (dueDate) fd.append('due_date', dueDate);

                        const resp = await fetch(routes.storeReport, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: fd,
                        });

                        if (!resp.ok) throw new Error();

                        const data = await resp.json();
                        if (!data.success) throw new Error();

                        const r = data.report;

                        const div = document.createElement('div');
                        div.className = 'ap-report-item';
                        div.dataset.reportId = r.id;

                        const imgUrl = r.employee && r.employee.image
                            ? (employeeImageBaseUrl + '/' + r.employee.image)
                            : employeeDefaultImageUrl;

                        const hasNext = (r.next_step && r.next_step.length) || r.due_date;

                        div.innerHTML = `
                                    <div class="ap-avatar">
                                        <img src="${imgUrl}" alt="${r.employee.name}">
                                    </div>
                                    <div>
                                        <div class="ap-report-meta">
                                            <span class="ap-report-author">${r.employee.name} ${r.employee.lastname}</span>
                                            <span class="ap-report-time">${r.created_at}</span>
                                        </div>
                                        <div class="ap-report-content">${r.report}</div>
                                        ${hasNext
                                ? `<div class="ap-report-next">
                                                    <span class="ap-report-next-label">Next Step</span>
                                                    <span>${r.next_step ?? '–'}</span>
                                                    ${r.due_date
                                    ? `<span class="ap-report-next-due">Fällig: ${r.due_date}</span>`
                                    : ''
                                }
                                                </div>`
                                : ''
                            }
                                        <div class="ap-report-actions"
                                             data-report-id="${r.id}"
                                             data-reaction="${r.reaction || ''}">
                                            <button type="button"
                                                    class="ap-reaction-btn ap-report-like-btn ${r.reaction === 'like' ? 'is-active' : ''}">
                                                <span>👍</span>
                                                <span class="ap-reaction-count">${r.likes_count}</span>
                                            </button>
                                            <button type="button"
                                                    class="ap-reaction-btn ap-report-dislike-btn ${r.reaction === 'dislike' ? 'is-active' : ''}">
                                                <span>👎</span>
                                                <span class="ap-reaction-count">${r.dislikes_count}</span>
                                            </button>
                                        </div>

                                        <div class="ap-report-comments" data-report-id="${r.id}">
                                            <div class="ap-report-comment-list"></div>
                                            <div class="ap-report-comment-form">
                                                <textarea class="ap-report-comment-input"
                                                          placeholder="Kommentar zu diesem Report …"></textarea>
                                                <button type="button"
                                                        class="ap-report-comment-save-btn">
                                                    <svg class="ap-icon-svg" aria-hidden="true">
                                                        <use xlink:href="#icon-comment"></use>
                                                    </svg>
                                                    <span>Speichern</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                        reportList.prepend(div);
                        applyReportFilter();

                        if (quill) {
                            quill.setContents([]);
                        }
                        if (nextStepInput) nextStepInput.value = '';
                        if (dueDateInput) dueDateInput.value = '';

                        showToast('Report gespeichert.', 'success');
                    } catch (e) {
                        console.error(e);
                        showToast('Report konnte nicht gespeichert werden.', 'error');
                    }
                });
            }

            // REACT TO REPORT (like / dislike)
            if (reportList) {
                reportList.addEventListener('click', async function (e) {
                    const likeBtn = e.target.closest('.ap-report-like-btn');
                    const dislikeBtn = e.target.closest('.ap-report-dislike-btn');
                    const btn = likeBtn || dislikeBtn;
                    if (!btn) return;

                    const wrapper = btn.closest('.ap-report-actions');
                    if (!wrapper) return;

                    const reportId = wrapper.dataset.reportId;
                    if (!reportId) return;

                    const reaction = likeBtn ? 'like' : 'dislike';

                    try {
                        const resp = await fetch(routes.reactReportBase + '/' + reportId + '/react', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ reaction }),
                        });

                        if (!resp.ok) throw new Error();

                        const data = await resp.json();
                        if (!data.success) throw new Error();

                        const likeSpan = wrapper.querySelector('.ap-report-like-btn .ap-reaction-count');
                        const dislikeSpan = wrapper.querySelector('.ap-report-dislike-btn .ap-reaction-count');

                        if (likeSpan) likeSpan.textContent = data.likes_count;
                        if (dislikeSpan) dislikeSpan.textContent = data.dislikes_count;

                        wrapper.querySelectorAll('.ap-reaction-btn').forEach(function (b) {
                            b.classList.remove('is-active');
                        });
                        if (data.reaction === 'like' && wrapper.querySelector('.ap-report-like-btn')) {
                            wrapper.querySelector('.ap-report-like-btn').classList.add('is-active');
                        } else if (data.reaction === 'dislike' && wrapper.querySelector('.ap-report-dislike-btn')) {
                            wrapper.querySelector('.ap-report-dislike-btn').classList.add('is-active');
                        }
                    } catch (err) {
                        console.error(err);
                        showToast('Reaktion konnte nicht gespeichert werden.', 'error');
                    }
                });
            }

            // STORE GLOBAL COMMENT
            if (commentBtn) {
                commentBtn.addEventListener('click', async function () {
                    const text = commentText.value.trim();
                    if (!text) return;

                    try {
                        const fd = new FormData();
                        fd.append('comment', text);

                        const resp = await fetch(routes.storeComment, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                            body: fd,
                        });

                        if (!resp.ok) throw new Error();

                        const data = await resp.json();
                        if (!data.success) throw new Error();

                        const c = data.comment;
                        const imgUrl = c.employee && c.employee.image
                            ? (employeeImageBaseUrl + '/' + c.employee.image)
                            : employeeDefaultImageUrl;

                        const div = document.createElement('div');
                        div.className = 'ap-comment-item';
                        div.innerHTML = `
                                    <div class="ap-avatar">
                                        <img src="${imgUrl}" alt="${c.employee.name}">
                                    </div>
                                    <div>
                                        <div class="ap-comment-meta">
                                            <span class="ap-comment-author">${c.employee.name} ${c.employee.lastname}</span>
                                            <span class="ap-comment-time">${c.created_at}</span>
                                        </div>
                                        <div class="ap-comment-text">${c.comment}</div>
                                    </div>
                                `;
                        commentList.prepend(div);
                        commentText.value = '';
                        showToast('Kommentar gespeichert.', 'success');
                    } catch (e) {
                        console.error(e);
                        showToast('Kommentar konnte nicht gespeichert werden.', 'error');
                    }
                });
            }

            // COMMENT UNDER REPORT (delegated on reportList)
            if (reportList) {
                reportList.addEventListener('click', async function (e) {
                    const btn = e.target.closest('.ap-report-comment-save-btn');
                    if (!btn) return;

                    const wrapper = btn.closest('.ap-report-comments');
                    if (!wrapper) return;

                    const reportId = wrapper.dataset.reportId;
                    if (!reportId) return;

                    const textarea = wrapper.querySelector('.ap-report-comment-input');
                    if (!textarea) return;

                    const text = textarea.value.trim();
                    if (!text) return;

                    try {
                        const resp = await fetch(routes.reportCommentBase + '/' + reportId + '/comments', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ text }),
                        });

                        if (!resp.ok) throw new Error();

                        const data = await resp.json();
                        if (!data.success) throw new Error();

                        const items = data.commentItems || [];
                        const last = items[items.length - 1];

                        const listEl = wrapper.querySelector('.ap-report-comment-list');
                        if (last && listEl) {
                            const div = document.createElement('div');
                            div.className = 'ap-report-comment-item';
                            div.innerHTML = `
                                        <div>
                                            <span class="ap-report-comment-author">
                                                ${last.author_name || ('Mitarbeiter #' + (last.employee_id || ''))}
                                            </span>
                                            ${last.created_at_human
                                    ? `<span class="ap-report-comment-time">${last.created_at_human}</span>`
                                    : ''
                                }
                                        </div>
                                        <div class="ap-report-comment-text">${last.text || ''}</div>
                                    `;
                            listEl.appendChild(div);
                        }

                        textarea.value = '';
                        showToast('Kommentar gespeichert.', 'success');
                    } catch (err) {
                        console.error(err);
                        showToast('Kommentar konnte nicht gespeichert werden.', 'error');
                    }
                });
            }

            // REFRESH NOTIFICATIONS
            async function refreshNotifications() {
                if (!routes.notifications || !notifTimeline) return;
                try {
                    const resp = await fetch(routes.notifications, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!resp.ok) return;

                    const json = await resp.json();
                    const items = json.data || [];
                    notifTimeline.innerHTML = '';

                    if (!items.length) {
                        const li = document.createElement('li');
                        li.className = 'ap-timeline-item';
                        li.innerHTML = `
                                    <div class="ap-timeline-dot"></div>
                                    <div><div class="ap-timeline-text">Noch keine Benachrichtigungen.</div></div>
                                `;
                        notifTimeline.appendChild(li);
                        return;
                    }

                    items.forEach(n => {
                        let dotClass = 'ap-timeline-dot';
                        if (n.kind === 'due') dotClass += ' ap-timeline-dot--due';
                        else if (n.kind === 'status') dotClass += ' ap-timeline-dot--status';
                        else if (n.kind === 'created') dotClass += ' ap-timeline-dot--created';

                        const li = document.createElement('li');
                        li.className = 'ap-timeline-item';
                        li.innerHTML = `
                                    <div class="${dotClass}"></div>
                                    <div>
                                        <div class="ap-timeline-title">${n.title}</div>
                                        ${n.message ? `<div class="ap-timeline-text">${n.message}</div>` : ''}
                                        <div class="ap-timeline-meta">${n.created_at}</div>
                                    </div>
                                `;
                        notifTimeline.appendChild(li);
                    });
                } catch (e) {
                    console.error(e);
                }
            }

            setInterval(refreshNotifications, 60000);
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
                label: 'Terminliste',
                url: "{{ url('customer/appointments')}}",
            },

            {
                label: '{{ $appointment->name }}',
                url: "{{ url()->current() }}",
                clickable: false
            }
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush