@extends('admin.layouts.app')

@section('title', 'Kundenhistorie')

@section('style')
    <style>
        :root {
            --lh-bg: #f6f8fb;
            --lh-card: #fff;
            --lh-text: #1f2937;
            --lh-muted: #64748b;
            --lh-border: #dbe8f1;
            --lh-blue: #74b2d4;
            --lh-green: #93c21c;
            --lh-green-soft: #cfe09b;
            --lh-orange: #f8ac00;
            --lh-pink: #e50656;
            --lh-shadow: 0 18px 45px rgba(15, 23, 42, .08);
            --lh-radius: 22px;
        }

        .lh-app {
            min-height: calc(100vh - 70px);
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            gap: 18px;
            background: var(--lh-bg);
            padding: 18px;
            color: var(--lh-text)
        }

        .lh-sidebar {
            position: sticky;
            top: 12px;
            height: calc(100vh - 95px);
            background: var(--lh-card);
            border: 1px solid var(--lh-border);
            border-radius: 24px;
            box-shadow: var(--lh-shadow);
            padding: 14px;
            overflow: auto;
            transition: .22s ease;
            z-index: 20
        }

        .lh-app.is-sidebar-collapsed {
            grid-template-columns: 86px minmax(0, 1fr)
        }

        .lh-app.is-sidebar-collapsed .lh-brand div,
        .lh-app.is-sidebar-collapsed .lh-side-group summary span,
        .lh-app.is-sidebar-collapsed .lh-side-label,
        .lh-app.is-sidebar-collapsed .lh-side-number {
            display: none
        }

        .lh-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(116, 178, 212, .16), rgba(207, 224, 155, .28));
            margin-bottom: 12px
        }

        .lh-brand-icon {
            width: 42px;
            height: 42px;
            border-radius: 15px;
            background: var(--lh-blue);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0
        }

        .lh-brand strong {
            display: block;
            font-weight: 950;
            color: #2b6f91
        }

        .lh-brand small {
            color: var(--lh-muted);
            font-weight: 700
        }

        .lh-sidebar-toggle {
            margin-left: auto;
            border: 0;
            background: #fff;
            border-radius: 12px;
            width: 34px;
            height: 34px;
            color: var(--lh-blue)
        }

        .lh-side-group {
            border: 1px solid var(--lh-border);
            border-radius: 18px;
            margin-bottom: 10px;
            background: #fff;
            overflow: hidden
        }

        .lh-side-group summary {
            list-style: none;
            cursor: pointer;
            padding: 11px 12px;
            font-size: 12px;
            font-weight: 950;
            color: var(--lh-muted);
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-transform: uppercase;
            letter-spacing: .05em
        }

        .lh-side-group summary::-webkit-details-marker {
            display: none
        }

        .lh-side-items {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 0 8px 9px
        }

        .lh-side-link {
            border: 0;
            background: transparent;
            border-radius: 14px;
            min-height: 42px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            color: var(--lh-text);
            font-weight: 850;
            text-align: left;
            width: 100%
        }

        .lh-side-link:hover,
        .lh-side-link.active {
            background: rgba(116, 178, 212, .14);
            color: #2b6f91
        }

        .lh-side-number {
            font-size: 10px;
            font-weight: 950;
            color: var(--lh-orange);
            min-width: 22px
        }

        .lh-side-link i {
            width: 17px;
            height: 17px;
            flex: 0 0 auto
        }

        .lh-main {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 14px
        }

        .lh-hero {
            background: #fff;
            border: 1px solid var(--lh-border);
            border-radius: 28px;
            box-shadow: var(--lh-shadow);
            padding: 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px
        }

        .lh-hero-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0
        }

        .lh-avatar {
            width: 62px;
            height: 62px;
            border-radius: 22px;
            background: linear-gradient(135deg, var(--lh-blue), var(--lh-green));
            color: #fff;
            font-weight: 950;
            font-size: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0
        }

        .lh-kicker {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--lh-muted);
            font-weight: 950
        }

        .lh-hero h1 {
            margin: 0;
            color: #2b6f91;
            font-size: 25px;
            font-weight: 950
        }

        .lh-hero p {
            margin: 4px 0 0;
            color: var(--lh-muted);
            display: flex;
            align-items: center;
            gap: 6px
        }

        .lh-hero-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-end
        }

        .lh-action-btn {
            min-height: 38px;
            border: 1px solid var(--lh-border);
            background: #fff;
            border-radius: 999px;
            padding: 8px 13px;
            color: #2b6f91;
            font-weight: 850;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 7px
        }

        .lh-action-btn.primary {
            background: var(--lh-blue);
            border-color: var(--lh-blue);
            color: #fff
        }

        .lh-action-btn:hover {
            text-decoration: none;
            color: #1d4f6d;
            background: rgba(116, 178, 212, .12)
        }

        .lh-analytics-row {
            display: grid;
            grid-template-columns: repeat(9, minmax(0, 1fr));
            gap: 10px
        }

        .lh-stat {
            background: #fff;
            border: 1px solid var(--lh-border);
            border-radius: 18px;
            padding: 13px;
            display: flex;
            align-items: center;
            gap: 9px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .04);
            min-width: 0
        }

        .lh-stat i {
            color: var(--lh-blue);
            width: 19px;
            height: 19px
        }

        .lh-stat strong {
            font-size: 20px;
            font-weight: 950;
            color: #1e293b
        }

        .lh-stat span {
            font-size: 11px;
            font-weight: 850;
            color: var(--lh-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .lh-searchbar {
            position: relative;
            background: #fff;
            border: 1px solid var(--lh-border);
            border-radius: 20px;
            padding: 10px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .04)
        }

        .lh-search-input {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .lh-search-input i {
            color: var(--lh-blue)
        }

        .lh-search-input input {
            border: 0;
            outline: 0;
            width: 100%;
            font-size: 14px;
            font-weight: 700;
            color: var(--lh-text)
        }

        .lh-search-results {
            display: none;
            position: absolute;
            top: calc(100% + 8px);
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid var(--lh-border);
            border-radius: 18px;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .16);
            z-index: 1000;
            padding: 8px;
            max-height: 370px;
            overflow: auto
        }

        .lh-search-results.is-open {
            display: block
        }

        .lh-search-results button {
            width: 100%;
            border: 0;
            background: #fff;
            border-radius: 14px;
            padding: 10px;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 2px
        }

        .lh-search-results button:hover {
            background: rgba(116, 178, 212, .12)
        }

        .lh-search-results strong {
            font-size: 13px;
            color: #1f2937
        }

        .lh-search-results small {
            font-size: 11px;
            color: var(--lh-muted)
        }

        .lh-search-empty {
            padding: 12px;
            color: var(--lh-muted);
            font-weight: 800
        }

        .lh-panel {
            display: none
        }

        .lh-panel.active {
            display: block
        }

        .lh-section-title {
            display: flex;
            align-items: end;
            gap: 10px;
            margin: 8px 0 14px
        }

        .lh-section-title span {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            background: var(--lh-blue);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 950
        }

        .lh-section-title h2 {
            margin: 0;
            color: #2b6f91;
            font-weight: 950
        }

        .lh-section-title small {
            color: var(--lh-muted);
            font-weight: 800;
            margin-bottom: 4px
        }

        .lh-card-grid {
            display: grid;
            gap: 14px
        }

        .lh-card-grid.two {
            grid-template-columns: repeat(2, minmax(0, 1fr))
        }

        .lh-card-grid.three {
            grid-template-columns: repeat(3, minmax(0, 1fr))
        }

        .lh-card,
        .lh-object-card,
        .lh-report-group {
            background: #fff;
            border: 1px solid var(--lh-border);
            border-radius: var(--lh-radius);
            box-shadow: var(--lh-shadow);
            padding: 16px;
            min-width: 0
        }

        .lh-card-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 12px
        }

        .lh-card-head h3 {
            margin: 0;
            color: #2b6f91;
            font-weight: 950;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px
        }

        .lh-card-head small {
            color: var(--lh-muted);
            font-weight: 800
        }

        .lh-data-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px
        }

        .lh-data-grid.wide {
            grid-template-columns: repeat(4, minmax(0, 1fr))
        }

        .lh-data-cell {
            border: 1px solid var(--lh-border);
            background: #f9fbfd;
            border-radius: 16px;
            padding: 10px;
            min-width: 0
        }

        .lh-data-cell span {
            display: flex;
            align-items: center;
            gap: 6px;
            color: var(--lh-muted);
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase
        }

        .lh-data-cell strong {
            display: block;
            margin-top: 5px;
            color: #1f2937;
            font-size: 13px;
            word-break: break-word
        }

        .lh-data-cell.is-empty {
            opacity: .55
        }

        .lh-text-box {
            margin-top: 12px;
            border: 1px solid var(--lh-border);
            border-radius: 16px;
            background: #f9fbfd;
            padding: 12px;
            color: #374151;
            font-weight: 650;
            line-height: 1.55;
            white-space: pre-wrap
        }

        .lh-text-box.small {
            font-size: 12px;
            padding: 9px
        }

        .lh-empty {
            border: 1px dashed var(--lh-border);
            background: #fff;
            border-radius: 18px;
            padding: 22px;
            text-align: center;
            color: var(--lh-muted);
            font-weight: 850
        }

        .lh-empty.small {
            padding: 12px;
            font-size: 12px
        }

        .lh-mini-list {
            display: flex;
            flex-direction: column;
            gap: 8px
        }

        .lh-mini-list.compact {
            margin-top: 10px
        }

        .lh-mini-row {
            border: 1px solid var(--lh-border);
            background: #fff;
            border-radius: 14px;
            padding: 9px 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            color: var(--lh-text);
            font-size: 13px
        }

        .lh-mini-row span {
            display: flex;
            align-items: center;
            gap: 7px;
            min-width: 0
        }

        .lh-mini-row strong {
            color: #2b6f91;
            white-space: nowrap
        }

        .lh-mini-row.lh-jump {
            width: 100%;
            cursor: pointer;
            text-align: left
        }

        .lh-mini-row:hover {
            background: rgba(116, 178, 212, .09)
        }

        .lh-progress-hero {
            display: flex;
            align-items: center;
            gap: 14px
        }

        .lh-progress-circle {
            --p: 0;
            width: 86px;
            height: 86px;
            border-radius: 50%;
            background: conic-gradient(var(--lh-green) calc(var(--p)*1%), #e8eef3 0);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative
        }

        .lh-progress-circle:after {
            content: "";
            position: absolute;
            inset: 9px;
            border-radius: 50%;
            background: #fff
        }

        .lh-progress-circle strong {
            position: relative;
            z-index: 2;
            color: #2b6f91;
            font-size: 20px
        }

        .lh-object-summary-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 14px
        }

        .lh-object-summary-card {
            background: #fff;
            border: 1px solid var(--lh-border);
            border-radius: 18px;
            padding: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .04)
        }

        .lh-object-summary-card i {
            color: var(--lh-blue)
        }

        .lh-object-summary-card strong {
            font-size: 22px;
            font-weight: 950
        }

        .lh-object-summary-card span {
            color: var(--lh-muted);
            font-weight: 850
        }

        .lh-object-card {
            margin-bottom: 16px
        }

        .lh-object-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 14px
        }

        .lh-object-count {
            font-size: 11px;
            color: var(--lh-orange);
            font-weight: 950;
            text-transform: uppercase
        }

        .lh-object-head h3 {
            margin: 2px 0;
            color: #2b6f91;
            font-weight: 950
        }

        .lh-object-head p {
            margin: 0;
            color: var(--lh-muted);
            display: flex;
            align-items: center;
            gap: 6px
        }

        .lh-object-progress {
            min-width: 190px;
            text-align: right
        }

        .lh-progressbar {
            height: 8px;
            background: #e8eef3;
            border-radius: 999px;
            overflow: hidden;
            margin-bottom: 7px
        }

        .lh-progressbar span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, var(--lh-blue), var(--lh-green))
        }

        .lh-object-progress strong,
        .lh-object-progress small {
            display: block
        }

        .lh-object-progress small {
            color: var(--lh-muted);
            font-weight: 800
        }

        .lh-collapse {
            border: 1px solid var(--lh-border);
            border-radius: 18px;
            margin-top: 10px;
            background: #fff;
            overflow: hidden
        }

        .lh-collapse summary {
            list-style: none;
            cursor: pointer;
            padding: 13px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            font-weight: 950;
            color: #2b6f91
        }

        .lh-collapse summary::-webkit-details-marker {
            display: none
        }

        .lh-collapse summary span {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .lh-collapse summary em {
            font-style: normal;
            background: rgba(116, 178, 212, .15);
            color: #2b6f91;
            border-radius: 999px;
            padding: 4px 9px;
            font-size: 12px
        }

        .lh-collapse[open] {
            padding-bottom: 12px
        }

        .lh-collapse[open]>.lh-data-grid,
        .lh-collapse[open]>.lh-product-grid,
        .lh-collapse[open]>.lh-split,
        .lh-collapse[open]>.lh-text-box {
            margin: 0 12px 0
        }

        .lh-collapse.nested {
            box-shadow: none;
            border-radius: 14px
        }

        .lh-product-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px
        }

        .lh-product-card,
        .lh-mini-card {
            border: 1px solid var(--lh-border);
            border-radius: 17px;
            background: #f9fbfd;
            padding: 13px
        }

        .lh-product-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px
        }

        .lh-product-head strong {
            color: #1f2937;
            font-weight: 950
        }

        .lh-product-head span {
            border-radius: 999px;
            background: rgba(147, 194, 28, .15);
            color: #5f850f;
            padding: 4px 9px;
            font-size: 11px;
            font-weight: 950
        }

        .lh-flow {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px
        }

        .lh-flow span {
            font-size: 11px;
            border-radius: 999px;
            border: 1px solid var(--lh-border);
            padding: 5px 9px;
            background: #fff;
            color: var(--lh-muted);
            font-weight: 850
        }

        .lh-flow span.done {
            background: rgba(116, 178, 212, .13);
            color: #2b6f91;
            border-color: rgba(116, 178, 212, .35)
        }

        .lh-chip-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 10px
        }

        .lh-chip-row span {
            border: 1px solid var(--lh-border);
            background: #fff;
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 11px;
            font-weight: 850;
            color: #374151
        }

        .lh-split {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px
        }

        .lh-split h4 {
            margin: 0 0 8px;
            color: #2b6f91
        }

        .lh-chat-bubble,
        .lh-chat-message {
            border: 1px solid var(--lh-border);
            background: #fff;
            border-radius: 18px;
            padding: 12px;
            margin-bottom: 10px
        }

        .lh-chat-message {
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            gap: 12px
        }

        .lh-chat-avatar {
            width: 42px;
            height: 42px;
            border-radius: 15px;
            background: var(--lh-blue);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 950
        }

        .lh-chat-bubble strong,
        .lh-chat-message strong {
            color: #2b6f91
        }

        .lh-chat-bubble p,
        .lh-chat-message p {
            margin: 5px 0;
            color: #374151;
            white-space: pre-wrap
        }

        .lh-chat-bubble small,
        .lh-chat-message small {
            color: var(--lh-muted);
            font-weight: 800
        }

        .lh-chat-list {
            display: flex;
            flex-direction: column;
            gap: 10px
        }

        .lh-chat-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
            margin-bottom: 4px
        }

        .lh-chat-reply {
            margin-top: 9px;
            margin-left: 10px;
            border-left: 3px solid var(--lh-green-soft);
            padding: 8px 10px;
            background: #f9fbfd;
            border-radius: 12px
        }

        .lh-report-mini,
        .lh-report-card {
            border: 1px solid var(--lh-border);
            border-radius: 14px;
            background: #fff;
            margin-bottom: 8px;
            overflow: hidden
        }

        .lh-report-mini summary,
        .lh-report-card summary {
            list-style: none;
            cursor: pointer;
            padding: 10px 12px;
            font-weight: 900;
            color: #2b6f91
        }

        .lh-report-mini summary::-webkit-details-marker,
        .lh-report-card summary::-webkit-details-marker {
            display: none
        }

        .lh-report-mini p,
        .lh-report-body {
            padding: 0 12px 12px;
            margin: 0;
            color: #374151;
            white-space: pre-wrap
        }

        .lh-report-group {
            margin-bottom: 12px;
            padding: 0;
            overflow: hidden
        }

        .lh-report-group>summary {
            list-style: none;
            cursor: pointer;
            padding: 14px 16px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
            color: #2b6f91;
            font-weight: 950
        }

        .lh-report-group>summary::-webkit-details-marker {
            display: none
        }

        .lh-report-group>summary span {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .lh-report-group>summary em {
            font-style: normal;
            background: rgba(116, 178, 212, .15);
            padding: 4px 10px;
            border-radius: 999px
        }

        .lh-report-list {
            padding: 0 12px 12px
        }

        .lh-report-card summary {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px
        }

        .lh-report-card summary small {
            color: var(--lh-muted)
        }

        .lh-timeline {
            display: flex;
            flex-direction: column;
            gap: 10px
        }

        .lh-timeline-row {
            background: #fff;
            border: 1px solid var(--lh-border);
            border-radius: 18px;
            padding: 13px;
            display: grid;
            grid-template-columns: 42px minmax(0, 1fr);
            gap: 12px
        }

        .lh-timeline-icon {
            width: 42px;
            height: 42px;
            border-radius: 15px;
            background: rgba(116, 178, 212, .14);
            color: #2b6f91;
            display: flex;
            align-items: center;
            justify-content: center
        }

        .lh-timeline-row strong {
            color: #2b6f91
        }

        .lh-timeline-row p {
            margin: 4px 0;
            color: #374151
        }

        .lh-timeline-row small {
            color: var(--lh-muted);
            font-weight: 800
        }

        .lh-neighbor-list {
            display: flex;
            flex-direction: column;
            gap: 10px
        }

        .lh-neighbor-row {
            background: #fff;
            border: 1px solid var(--lh-border);
            border-radius: 18px;
            padding: 13px;
            display: grid;
            grid-template-columns: 1.2fr 1.6fr 2fr auto;
            gap: 12px;
            align-items: center
        }

        .lh-neighbor-row strong,
        .lh-neighbor-row span,
        .lh-neighbor-row small {
            display: block
        }

        .lh-neighbor-row small {
            color: var(--lh-muted);
            font-weight: 800
        }

        .lh-highlight {
            animation: lhHi 1.3s ease
        }

        @keyframes lhHi {

            0%,
            100% {
                box-shadow: var(--lh-shadow)
            }

            40% {
                box-shadow: 0 0 0 5px rgba(248, 172, 0, .25)
            }
        }

        .lh-context-layout {
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            gap: 14px;
            align-items: start
        }

        .lh-context-sidebar {
            position: sticky;
            top: 12px;
            background: #fff;
            border: 1px solid var(--lh-border);
            border-radius: 22px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .05);
            padding: 12px;
            max-height: calc(100vh - 130px);
            overflow: auto
        }

        .lh-context-sidebar h3 {
            margin: 0 0 10px;
            color: #2b6f91;
            font-size: 15px;
            font-weight: 950;
            display: flex;
            align-items: center;
            gap: 8px
        }

        .lh-context-group {
            border: 1px solid var(--lh-border);
            border-radius: 16px;
            margin-bottom: 9px;
            background: #fff;
            overflow: hidden
        }

        .lh-context-group summary {
            list-style: none;
            cursor: pointer;
            padding: 10px 11px;
            font-size: 12px;
            font-weight: 950;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px
        }

        .lh-context-group summary::-webkit-details-marker {
            display: none
        }

        .lh-context-items {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 0 8px 9px
        }

        .lh-context-filter {
            width: 100%;
            border: 0;
            background: #f8fbfd;
            border-radius: 12px;
            min-height: 36px;
            padding: 7px 9px;
            text-align: left;
            color: #334155;
            font-size: 12px;
            font-weight: 850;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 7px
        }

        .lh-context-filter span {
            min-width: 0;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px
        }

        .lh-context-filter em {
            font-style: normal;
            background: #fff;
            border: 1px solid var(--lh-border);
            border-radius: 999px;
            padding: 2px 7px;
            color: var(--lh-muted);
            font-size: 10px;
            flex: 0 0 auto
        }

        .lh-context-filter.active,
        .lh-context-filter:hover {
            background: rgba(116, 178, 212, .16);
            color: #2b6f91
        }

        .lh-context-content {
            min-width: 0
        }

        .lh-context-empty {
            display: none
        }

        .lh-context-content.is-filtered .lh-context-empty {
            display: block
        }

        .lh-context-badges {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: 7px
        }

        .lh-context-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 1px solid var(--lh-border);
            background: #fff;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 11px;
            font-weight: 850;
            color: #475569
        }

        .lh-context-badge.product {
            background: rgba(147, 194, 28, .13);
            border-color: rgba(147, 194, 28, .3);
            color: #5f850e
        }

        .lh-context-badge.object {
            background: rgba(116, 178, 212, .13);
            border-color: rgba(116, 178, 212, .3);
            color: #2b6f91
        }

        .lh-context-section {
            margin-bottom: 13px
        }

        .lh-context-section>summary {
            list-style: none;
            cursor: pointer;
            background: #fff;
            border: 1px solid var(--lh-border);
            border-radius: 18px;
            padding: 12px 14px;
            font-size: 14px;
            font-weight: 950;
            color: #2b6f91;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px
        }

        .lh-context-section>summary::-webkit-details-marker {
            display: none
        }

        .lh-context-section>summary span {
            display: flex;
            align-items: center;
            gap: 8px
        }

        .lh-context-section>summary em {
            font-style: normal;
            background: rgba(116, 178, 212, .14);
            border-radius: 999px;
            padding: 4px 10px;
            color: #2b6f91;
            font-size: 11px
        }

        .lh-context-section-body {
            padding: 10px 0 0;
            display: flex;
            flex-direction: column;
            gap: 10px
        }

        .lh-hidden-by-context {
            display: none !important
        }

        .lh-media-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px
        }

        .lh-media-card {
            border: 1px solid var(--lh-border);
            border-radius: 18px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .04);
            padding: 0;
            text-align: left;
            cursor: pointer
        }

        .lh-media-thumb {
            height: 170px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden
        }

        .lh-media-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform .2s ease
        }

        .lh-media-card:hover .lh-media-thumb img {
            transform: scale(1.04)
        }

        .lh-media-info {
            padding: 10px
        }

        .lh-media-info strong {
            display: block;
            color: #2b6f91;
            font-size: 13px;
            font-weight: 950;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis
        }

        .lh-media-info small {
            display: block;
            color: var(--lh-muted);
            font-size: 11px;
            font-weight: 800;
            margin-top: 3px
        }

        .lh-media-fallback {
            height: 170px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--lh-muted);
            font-weight: 900;
            gap: 8px
        }

        .lh-image-modal {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .86);
            z-index: 2147483600;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 24px
        }

        .lh-image-modal.is-open {
            display: flex
        }

        .lh-image-modal-panel {
            position: relative;
            width: min(1200px, 96vw);
            height: min(860px, 92vh);
            display: grid;
            grid-template-rows: auto minmax(0, 1fr) auto;
            gap: 12px
        }

        .lh-image-modal-top,
        .lh-image-modal-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            color: #fff
        }

        .lh-image-modal-title strong {
            display: block;
            font-size: 16px
        }

        .lh-image-modal-title small {
            display: block;
            color: #cbd5e1;
            font-weight: 700
        }

        .lh-image-modal-actions {
            display: flex;
            gap: 8px;
            align-items: center
        }

        .lh-image-btn {
            border: 1px solid rgba(255, 255, 255, .25);
            background: rgba(255, 255, 255, .1);
            color: #fff;
            border-radius: 999px;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-weight: 900
        }

        .lh-image-btn:hover {
            background: rgba(255, 255, 255, .2)
        }

        .lh-image-stage {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .15);
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative
        }

        .lh-image-stage img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transform: scale(var(--zoom, 1));
            transition: transform .16s ease
        }

        .lh-image-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 60px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, .2);
            background: rgba(15, 23, 42, .35);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center
        }

        .lh-image-nav.prev {
            left: 14px
        }

        .lh-image-nav.next {
            right: 14px
        }

        .lh-image-counter {
            font-size: 13px;
            font-weight: 900;
            color: #e2e8f0
        }

        @media(max-width:1200px) {
            .lh-analytics-row {
                grid-template-columns: repeat(3, minmax(0, 1fr))
            }

            .lh-card-grid.three,
            .lh-data-grid.wide {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }

            .lh-neighbor-row {
                grid-template-columns: 1fr
            }

            .lh-media-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }
        }

        @media(max-width:900px) {
            .lh-app {
                grid-template-columns: 1fr;
                padding: 10px
            }

            .lh-sidebar {
                position: relative;
                height: auto
            }

            .lh-hero {
                flex-direction: column;
                align-items: flex-start
            }

            .lh-card-grid.two,
            .lh-card-grid.three,
            .lh-product-grid,
            .lh-split,
            .lh-data-grid,
            .lh-data-grid.wide,
            .lh-object-summary-row,
            .lh-context-layout {
                grid-template-columns: 1fr
            }

            .lh-context-sidebar {
                position: relative;
                max-height: none
            }

            .lh-object-head {
                flex-direction: column
            }

            .lh-object-progress {
                width: 100%;
                text-align: left
            }

            .lh-analytics-row {
                grid-template-columns: repeat(2, minmax(0, 1fr))
            }

            .lh-media-grid {
                grid-template-columns: 1fr
            }

            .lh-image-modal {
                padding: 10px
            }

            .lh-image-modal-top,
            .lh-image-modal-bottom {
                flex-wrap: wrap
            }

            .lh-image-stage {
                min-height: 55vh
            }
        }
    </style>
@endsection

@section('content')
    @php
        $plainText = function ($value) {
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            $text = html_entity_decode((string) ($value ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $text = preg_replace('/<\s*br\s*\/?>/i', "\n", $text);
            $text = preg_replace('/<\s*\/p\s*>/i', "\n", $text);
            $text = strip_tags($text);
            $text = preg_replace('/[ \t]+/', ' ', $text);
            $text = preg_replace('/\n{3,}/', "\n\n", $text);
            return trim($text);
        };

        $safeCollection = function ($value) {
            return $value instanceof \Illuminate\Support\Collection ? $value : collect($value ?? []);
        };

        $objects = $safeCollection($customer->objects ?? $customer->alternativeAddresses ?? []);
        $leadProductLists = $safeCollection($customer->leadProductLists ?? []);
        $notes = $safeCollection($notes ?? $customer->customerNotes ?? []);
        $reports = $safeCollection($reports ?? $customer->reports ?? []);
        $appointments = $safeCollection($appointments ?? []);
        $tasks = $safeCollection($tasks ?? $customer->personalTasks ?? []);
        $tickets = $safeCollection($tickets ?? $customer->problems ?? []);
        $offerFolders = $safeCollection($offerFolders ?? $customer->offerFolders ?? []);
        $invoices = $safeCollection($invoices ?? $customer->invoices ?? []);
        $images = $safeCollection($images ?? []);
        $neighbors = $safeCollection($neighbors ?? []);
        $timeline = $safeCollection($timeline ?? []);
        $radius = $radius ?? 10;

        $displayName = $customer->display_name ?? trim(($customer->name ?? '') . ' ' . ($customer->lastname ?? ''));
        if (!$displayName) {
            $displayName = $customer->firma ?: 'Kunde';
        }
        $customerAddress = $customer->full_address ?: trim(($customer->street ?? '') . ' ' . ($customer->postcode ?? '') . ' ' . ($customer->city ?? ''));

        $formatDate = function ($date) {
            if (!$date)
                return '—';
            try {
                return \Illuminate\Support\Carbon::parse($date)->format('d.m.Y');
            } catch (\Throwable $e) {
                return (string) $date;
            }
        };
        $formatDateTime = function ($date) {
            if (!$date)
                return '—';
            try {
                return \Illuminate\Support\Carbon::parse($date)->format('d.m.Y H:i');
            } catch (\Throwable $e) {
                return (string) $date;
            }
        };
        $money = function ($value) {
            if ($value === null || $value === '')
                return '—';
            return number_format((float) $value, 2, ',', '.') . ' €';
        };
        $employeeName = function ($employee) {
            if (!$employee)
                return 'Unbekannt';
            $name = trim(($employee->name ?? '') . ' ' . ($employee->lastname ?? ''));
            return $name ?: ($employee->display_name ?? 'Mitarbeiter');
        };
        $objectName = function ($object) {
            if (!$object)
                return 'Allgemeiner Kundenbereich';
            $address = $object->full_address ?: trim(($object->street ?? '') . ' ' . ($object->postcode ?? '') . ' ' . ($object->city ?? ''));
            return $object->object_name ?: ($object->display_name ?? ($address ?: 'Objekt'));
        };
        $objectAddress = function ($object) {
            if (!$object)
                return 'Adresse offen';
            $address = $object->full_address ?: trim(($object->street ?? '') . ' ' . ($object->postcode ?? '') . ' ' . ($object->city ?? ''));
            return $address ?: 'Adresse offen';
        };
        $findObjectName = function ($alternativeId) use ($objects, $objectName) {
            $object = $objects->firstWhere('id', (int) $alternativeId);
            return $object ? $objectName($object) : 'Allgemeiner Kundenbereich';
        };
        $productName = function ($product = null, $fallback = null) {
            if (!$product)
                return $fallback ?: 'Artikelgruppe';
            return $product->name ?? $product->article_group ?? $product->title ?? $product->product_name ?? $fallback ?? 'Artikelgruppe';
        };
        $stageName = function ($lp) {
            if (!$lp)
                return 'Offen';
            return data_get($lp, 'leadStageSubStage.name') ?: data_get($lp, 'productTaskPhase.name') ?: data_get($lp, 'productStage.name') ?: data_get($lp, 'companyStage.name') ?: ($lp->status ?? $lp->work_status ?? 'Offen');
        };
        $findProductName = function ($productId, $alternativeId = null) use ($leadProductLists, $productName) {
            $rows = $leadProductLists;
            if ($alternativeId)
                $rows = $rows->where('alternative_id', (int) $alternativeId);
            $row = $rows->firstWhere('product_id', (int) $productId);
            return $row ? $productName($row->product ?? $row->articleGroup ?? null) : 'Artikelgruppe';
        };
        $contextLabel = function ($item) use ($findObjectName, $findProductName) {
            $object = $findObjectName($item->alternative_id ?? null);
            if (!empty($item->product_id))
                return $object . ' · ' . $findProductName($item->product_id, $item->alternative_id ?? null);
            return $object;
        };
        $valueText = function ($value, $suffix = '') use ($plainText) {
            if ($value === null || $value === '')
                return '—';
            if (is_bool($value))
                return $value ? 'Ja' : 'Nein';
            return trim($plainText($value) . ($suffix ? ' ' . $suffix : ''));
        };
        $renderField = function ($label, $value, $suffix = '', $icon = 'circle') use ($valueText) {
            $isEmpty = ($value === null || $value === '');
            return '<div class="lh-data-cell ' . ($isEmpty ? 'is-empty' : 'is-filled') . '"><span><i data-lucide="' . e($icon) . '"></i>' . e($label) . '</span><strong>' . e($valueText($value, $suffix)) . '</strong></div>';
        };
        $mediaUrl = function ($media) {
            $raw = $media->image ?? $media->path ?? $media->file ?? $media->url ?? null;
            if (!$raw)
                return null;
            $raw = trim((string) $raw);
            if ($raw === '')
                return null;
            if (\Illuminate\Support\Str::startsWith($raw, ['http://', 'https://', '/']))
                return $raw;
            if (\Illuminate\Support\Str::startsWith($raw, ['storage/']))
                return asset($raw);
            return asset('storage/' . ltrim($raw, '/'));
        };
        $contextKeyFor = function ($alternativeId = null, $productId = null) {
            if ($alternativeId && $productId)
                return 'product-' . (int) $alternativeId . '-' . (int) $productId;
            if ($alternativeId)
                return 'object-' . (int) $alternativeId;
            return 'general';
        };

        $objectFields = ['object_type', 'building_type', 'building_condition', 'usage_type', 'living_space', 'unusable_space', 'number_we', 'number_people', 'bathroom_count', 'house_year', 'masonry', 'external_insulation_thickness', 'window_glazing', 'window_frame', 'window_year', 'door_year', 'door_condition', 'roof_type', 'roof_age', 'roof_pitch', 'roof_direction', 'roof_covering', 'heating_system_type', 'heating_system_age', 'old_heating_power', 'installation_location', 'heating_load_calculation', 'heating_type', 'heating_circuits_count', 'pipe_system_material', 'heating_pipe_dimension', 'water_pipe_dimension', 'power_household', 'power_heatpump', 'power_electric_car', 'power_total', 'meter_cabinet', 'meter_count', 'network_wlan', 'electric_car', 'electric_car_count', 'wallbox_count', 'wallbox_location', 'ready_for_offer'];
        $objectCompletionRows = collect();
        foreach ($objects as $object) {
            $filled = 0;
            foreach ($objectFields as $fieldKey) {
                if (filled(data_get($object, $fieldKey)))
                    $filled++;
            }
            $total = count($objectFields);
            $objectCompletionRows->put($object->id, ['filled' => $filled, 'total' => $total, 'remaining' => max(0, $total - $filled), 'percent' => $total > 0 ? (int) round(($filled / $total) * 100) : 0]);
        }
        $objectTotalFilled = (int) $objectCompletionRows->sum('filled');
        $objectTotalFields = max(1, (int) $objectCompletionRows->sum('total'));
        $objectTotalRemaining = max(0, $objectTotalFields - $objectTotalFilled);
        $objectTotalPercent = (int) round(($objectTotalFilled / $objectTotalFields) * 100);

        $stats = $stats ?? [];
        $mainStats = [
            ['label' => 'Objekte', 'value' => $stats['objects'] ?? $objects->count(), 'icon' => 'home'],
            ['label' => 'Produkte', 'value' => $stats['products'] ?? $leadProductLists->count(), 'icon' => 'layers'],
            ['label' => 'Notizen', 'value' => $stats['notes'] ?? $notes->count(), 'icon' => 'messages-square'],
            ['label' => 'Berichte', 'value' => $stats['reports'] ?? $reports->count(), 'icon' => 'file-text'],
            ['label' => 'Termine', 'value' => $stats['appointments'] ?? $appointments->count(), 'icon' => 'calendar-days'],
            ['label' => 'Aufgaben', 'value' => $stats['tasks'] ?? $tasks->count(), 'icon' => 'check-square'],
            ['label' => 'Angebote', 'value' => $stats['offers'] ?? $offerFolders->count(), 'icon' => 'briefcase-business'],
            ['label' => 'Rechnungen', 'value' => $stats['invoices'] ?? $invoices->count(), 'icon' => 'receipt'],
            ['label' => 'Nachbarn', 'value' => $stats['neighbors'] ?? $neighbors->count(), 'icon' => 'map-pinned'],
        ];
        $sideGroups = [
            '01 Kunde' => [['key' => 'overview', 'label' => 'Überblick', 'icon' => 'layout-dashboard'], ['key' => 'object-data', 'label' => 'Objektdaten', 'icon' => 'home'], ['key' => 'timeline', 'label' => 'Timeline', 'icon' => 'history']],
            '02 Kommunikation' => [['key' => 'notes', 'label' => 'Notizen', 'icon' => 'messages-square'], ['key' => 'reports', 'label' => 'Berichte', 'icon' => 'file-text'], ['key' => 'appointments', 'label' => 'Termine', 'icon' => 'calendar-days']],
            '03 Arbeit' => [['key' => 'tasks', 'label' => 'Aufgaben', 'icon' => 'check-square'], ['key' => 'tickets', 'label' => 'Tickets', 'icon' => 'badge-alert'], ['key' => 'offers', 'label' => 'Angebote', 'icon' => 'briefcase-business'], ['key' => 'invoices', 'label' => 'Rechnungen', 'icon' => 'receipt']],
            '04 Referenzen' => [['key' => 'neighbors', 'label' => 'Nachbarn', 'icon' => 'map-pinned'], ['key' => 'media', 'label' => 'Medien', 'icon' => 'image']],
        ];
    @endphp

    <div class="lh-app" id="lhApp">
        <aside class="lh-sidebar" id="lhSidebar">
            <div class="lh-brand">
                <span class="lh-brand-icon"><i data-lucide="history"></i></span>
                <div><strong>Kundenhistorie</strong><small>Call Center Ansicht</small></div>
                <button type="button" class="lh-sidebar-toggle" data-lh-sidebar-toggle title="Menü einklappen"><i
                        data-lucide="panel-left-close"></i></button>
            </div>
            @foreach($sideGroups as $groupTitle => $items)
                <details class="lh-side-group" open>
                    <summary><span>{{ $groupTitle }}</span><i data-lucide="chevron-down"></i></summary>
                    <div class="lh-side-items">
                        @foreach($items as $index => $item)
                            <button type="button" class="lh-side-link {{ $loop->parent->first && $loop->first ? 'active' : '' }}"
                                data-lh-panel-btn="{{ $item['key'] }}"
                                onclick="window.lhOpenPanel && window.lhOpenPanel('{{ $item['key'] }}')"
                                title="{{ $item['label'] }}">
                                <span class="lh-side-number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span><i
                                    data-lucide="{{ $item['icon'] }}"></i><span class="lh-side-label">{{ $item['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </details>
            @endforeach
        </aside>

        <main class="lh-main">
            <header class="lh-hero lh-searchable" data-panel="overview"
                data-title="{{ $displayName }} {{ $customerAddress }} {{ $customer->email }} {{ $customer->phone }}">
                <div class="lh-hero-left">
                    <div class="lh-avatar">{{ mb_substr($displayName, 0, 1) }}</div>
                    <div><span class="lh-kicker">Kundenakte</span>
                        <h1>{{ $displayName }}</h1>
                        <p><i data-lucide="map-pin"></i> {{ $customerAddress ?: 'Adresse offen' }}</p>
                    </div>
                </div>
                <div class="lh-hero-actions">
                    @if(!empty($customer->phone))<a href="tel:{{ $customer->phone }}" class="lh-action-btn"><i
                    data-lucide="phone"></i>{{ $customer->phone }}</a>@endif
                    @if(!empty($customer->email))<a href="mailto:{{ $customer->email }}" class="lh-action-btn"><i
                    data-lucide="mail"></i>{{ $customer->email }}</a>@endif
                    <a href="{{ url('/new_lead_profile/' . $customer->id) }}" class="lh-action-btn primary"><i
                            data-lucide="external-link"></i>Profil</a>
                </div>
            </header>

            <section class="lh-analytics-row">
                @foreach($mainStats as $stat)
                    <div class="lh-stat"><i
                            data-lucide="{{ $stat['icon'] }}"></i><strong>{{ $stat['value'] }}</strong><span>{{ $stat['label'] }}</span>
                    </div>
                @endforeach
            </section>

            <section class="lh-searchbar">
                <div class="lh-search-input"><i data-lucide="search"></i><input type="search" id="lhSearch"
                        placeholder="Live suchen: Name, Adresse, Artikelgruppe, Notiz, Bericht, Termin, Seriennummer ..."
                        autocomplete="off"></div>
                <div class="lh-search-results" id="lhSearchResults"></div>
            </section>

            <section class="lh-panel active" data-lh-panel="overview">
                <div class="lh-section-title"><span>01</span>
                    <h2>Allgemeine Kundeninformationen</h2>
                </div>
                <div class="lh-card-grid three">
                    <article class="lh-card lh-searchable" id="kundendaten" data-panel="overview"
                        data-title="Kundendaten {{ $displayName }} {{ $customerAddress }} {{ $customer->source }} {{ $customer->status }}">
                        <div class="lh-card-head">
                            <h3><i data-lucide="user-round"></i>Kundendaten</h3>
                        </div>
                        <div class="lh-data-grid">
                            {!! $renderField('Firma', $customer->firma, '', 'building-2') !!}{!! $renderField('Name', trim(($customer->name ?? '') . ' ' . ($customer->lastname ?? '')), '', 'user') !!}{!! $renderField('Adresse', $customerAddress, '', 'map-pin') !!}{!! $renderField('Quelle', $customer->source, '', 'radio') !!}{!! $renderField('Status', $customer->status, '', 'activity') !!}{!! $renderField('Kaufstatus', $customer->purchase_status, '', 'badge-euro') !!}{!! $renderField('Anfrage', $formatDate($customer->request_date), '', 'calendar') !!}{!! $renderField('Kontaktperson', $employeeName($customer->contactPerson ?? $customer->contact ?? null), '', 'user-check') !!}
                        </div>
                        @if(!empty($customer->info))
                        <div class="lh-text-box">{{ $plainText($customer->info) }}</div>@endif
                    </article>
                    <article class="lh-card lh-searchable" id="produktstand" data-panel="overview"
                        data-title="Produktstand Artikelgruppen Phasen Status">
                        <div class="lh-card-head">
                            <h3><i data-lucide="git-branch"></i>Produktstand</h3>
                        </div>
                        <div class="lh-mini-list">
                            @foreach($leadProductLists as $lp)
                                <div class="lh-mini-row lh-searchable" data-panel="object-data"
                                    data-title="{{ $productName($lp->product ?? $lp->articleGroup ?? null) }} {{ $stageName($lp) }} {{ $findObjectName($lp->alternative_id ?? null) }}">
                                    <span><i
                                            data-lucide="layers"></i>{{ $productName($lp->product ?? $lp->articleGroup ?? null) }}</span><strong>{{ $stageName($lp) }}</strong>
                                </div>
                            @endforeach
                            @if($leadProductLists->isEmpty())
                            <div class="lh-empty">Noch keine Produkte.</div>@endif
                        </div>
                    </article>
                    <article class="lh-card lh-searchable" id="objekt-status" data-panel="object-data"
                        data-title="Objektdaten Vollständigkeit ausgefüllt offen">
                        <div class="lh-card-head">
                            <h3><i data-lucide="gauge"></i>Objektdaten Status</h3>
                        </div>
                        <div class="lh-progress-hero">
                            <div class="lh-progress-circle" style="--p: {{ $objectTotalPercent }}">
                                <strong>{{ $objectTotalPercent }}%</strong></div>
                            <div><strong>{{ $objectTotalFilled }} ausgefüllt</strong><span>{{ $objectTotalRemaining }}
                                    Felder offen</span></div>
                        </div>
                        <div class="lh-mini-list compact">
                            @foreach($objects as $object)
                                @php
                                    $oc = $objectCompletionRows->get($object->id, ['percent' => 0, 'filled' => 0, 'remaining' => 0]);
                                @endphp
                                <button type="button" class="lh-mini-row lh-jump" data-target="objectdata-{{ $object->id }}"
                                    data-panel="object-data"><span>{{ $objectName($object) }}</span><strong>{{ $oc['percent'] }}%</strong></button>
                            @endforeach
                        </div>
                    </article>
                </div>
            </section>

            <section class="lh-panel" data-lh-panel="object-data">
                <div class="lh-section-title"><span>02</span>
                    <h2>Objektdaten</h2><small>{{ $objectTotalFilled }} Felder ausgefüllt · {{ $objectTotalRemaining }}
                        offen</small>
                </div>
                <div class="lh-object-summary-row">
                    <div class="lh-object-summary-card"><i
                            data-lucide="home"></i><strong>{{ $objects->count() }}</strong><span>Objekte</span></div>
                    <div class="lh-object-summary-card"><i
                            data-lucide="layers"></i><strong>{{ $leadProductLists->count() }}</strong><span>Artikelgruppen</span>
                    </div>
                    <div class="lh-object-summary-card"><i
                            data-lucide="check-circle-2"></i><strong>{{ $objectTotalPercent }}%</strong><span>Füllstand</span>
                    </div>
                    <div class="lh-object-summary-card"><i
                            data-lucide="circle-alert"></i><strong>{{ $objectTotalRemaining }}</strong><span>Offen</span>
                    </div>
                </div>
                @foreach($objects as $object)
                    @php
                        $oc = $objectCompletionRows->get($object->id, ['percent' => 0, 'filled' => 0, 'remaining' => 0, 'total' => 0]);
                        $objectProducts = $safeCollection($object->productLists ?? $object->products ?? []);
                        $objectProductInfos = $safeCollection($object->customerProductInfos ?? []);
                        $objectNotes = $notes->where('alternative_id', $object->id);
                        $objectReports = $reports->where('alternative_id', $object->id);
                        $objectSearch = $objectName($object) . ' ' . $objectAddress($object) . ' ' . ($object->building_type ?? '') . ' ' . ($object->heating_system_type ?? '') . ' ' . ($object->roof_type ?? '');
                    @endphp
                    <article class="lh-object-card lh-searchable" id="objectdata-{{ $object->id }}" data-panel="object-data"
                        data-title="{{ $objectSearch }}">
                        <div class="lh-object-head">
                            <div><span class="lh-object-count">Objekt {{ $loop->iteration }}</span>
                                <h3>{{ $objectName($object) }}</h3>
                                <p><i data-lucide="map-pin"></i>{{ $objectAddress($object) }}</p>
                            </div>
                            <div class="lh-object-progress">
                                <div class="lh-progressbar"><span style="width: {{ $oc['percent'] }}%"></span></div>
                                <strong>{{ $oc['filled'] }} ausgefüllt</strong><small>{{ $oc['remaining'] }} offen</small>
                            </div>
                        </div>
                        <details class="lh-collapse" open>
                            <summary><span><i data-lucide="layers"></i>01 Artikelgruppen &
                                    Kundenprodukte</span><em>{{ $objectProducts->count() + $objectProductInfos->count() }}</em>
                            </summary>
                            <div class="lh-product-grid">
                                @foreach($objectProducts as $lp)
                                    @php
                                        $relatedCpi = $objectProductInfos->firstWhere('product_id', $lp->product_id ?? null);
                                        $productTitle = $productName($lp->product ?? $lp->articleGroup ?? null);
                                    @endphp
                                    <article class="lh-product-card lh-searchable" data-panel="object-data"
                                        data-title="{{ $productTitle }} {{ $stageName($lp) }} {{ $relatedCpi->serial_number ?? '' }} {{ $plainText($relatedCpi->notes ?? '') }}">
                                        <div class="lh-product-head">
                                            <strong>{{ $productTitle }}</strong><span>{{ $stageName($lp) }}</span></div>
                                        <div class="lh-flow"><span
                                                class="done">{{ data_get($lp, 'companyStage.name') ?: ($lp->status ?? 'Lead') }}</span><span
                                                class="done">{{ data_get($lp, 'productStage.name') ?: 'Produktphase offen' }}</span><span>{{ data_get($lp, 'leadStageSubStage.name') ?: 'Substage offen' }}</span><span>{{ data_get($lp, 'productTaskPhase.name') ?: 'Taskphase offen' }}</span>
                                        </div>
                                        <div class="lh-chip-row">
                                            @if(!empty($lp->work_status))<span>{{ $lp->work_status }}</span>@endif
                                            @if(!empty($lp->interest))<span>{{ $lp->interest }}</span>@endif
                                            @if(!empty($lp->price_latest) || !empty($lp->price))<span>{{ $money($lp->price_latest ?: $lp->price) }}</span>@endif
                                            @if($relatedCpi && !empty($relatedCpi->product_count))<span>{{ $relatedCpi->product_count }}
                                            Stück</span>@endif @if($relatedCpi && !empty($relatedCpi->serial_number))<span>SN
                                                {{ $relatedCpi->serial_number }}</span>@endif</div>
                                        @if($relatedCpi && !empty($relatedCpi->notes))
                                        <div class="lh-text-box small">{{ $plainText($relatedCpi->notes) }}</div>@endif
                                    </article>
                                @endforeach
                                @foreach($objectProductInfos as $info)
                                    @php
                                        $existsInLeadProducts = false;
                                        foreach ($objectProducts as $lpCheck) {
                                            if ((int) ($lpCheck->product_id ?? 0) === (int) ($info->product_id ?? 0)) {
                                                $existsInLeadProducts = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if(!$existsInLeadProducts)
                                        <article class="lh-product-card lh-searchable" data-panel="object-data"
                                            data-title="{{ $info->product_name }} {{ $productName($info->product ?? null) }} {{ $info->manufacturer }} {{ $info->serial_number }} {{ $plainText($info->notes) }}">
                                            <div class="lh-product-head">
                                                <strong>{{ $info->product_name ?: $productName($info->product ?? null) }}</strong><span>Installiertes
                                                    Produkt</span></div>
                                            <div class="lh-chip-row">
                                                @if(!empty($info->manufacturer))<span>{{ $info->manufacturer }}</span>@endif
                                                @if(!empty($info->serial_number))<span>SN {{ $info->serial_number }}</span>@endif
                                                @if(!empty($info->product_count))<span>{{ $info->product_count }} Stück</span>@endif
                                            </div>@if(!empty($info->notes))
                                            <div class="lh-text-box small">{{ $plainText($info->notes) }}</div>@endif
                                        </article>
                                    @endif
                                @endforeach
                                @if($objectProducts->isEmpty() && $objectProductInfos->isEmpty())
                                <div class="lh-empty">Keine Artikelgruppen für dieses Objekt.</div>@endif
                            </div>
                        </details>
                        <details class="lh-collapse" open>
                            <summary><span><i data-lucide="database"></i>02 Allgemeine
                                    Objektdaten</span><em>{{ $oc['percent'] }}%</em></summary>
                            <div class="lh-data-grid wide">
                                {!! $renderField('Objektart', $object->object_type, '', 'tag') !!}{!! $renderField('Gebäudeart', $object->building_type, '', 'building') !!}{!! $renderField('Zustand', $object->building_condition, '', 'wrench') !!}{!! $renderField('Nutzungsart', $object->usage_type, '', 'key-round') !!}{!! $renderField('Wohnfläche', $object->living_space, 'm²', 'ruler') !!}{!! $renderField('Nutzfläche', $object->unusable_space, 'm²', 'maximize') !!}{!! $renderField('Wohneinheiten', $object->number_we, '', 'door-open') !!}{!! $renderField('Geschosse', $object->number_stories ?? $object->story_count ?? null, '', 'layers') !!}{!! $renderField('Personen', $object->number_people, '', 'users') !!}{!! $renderField('Bäder', $object->bathroom_count, '', 'bath') !!}{!! $renderField('Baujahr', $object->house_year, '', 'calendar') !!}{!! $renderField('Mauerwerk', $object->masonry, '', 'bricks') !!}{!! $renderField('Dämmung', $object->external_insulation_thickness, '', 'shield') !!}{!! $renderField('Fenster', $object->window_glazing, '', 'layout-panel-top') !!}{!! $renderField('Fensterrahmen', $object->window_frame, '', 'panel-top') !!}{!! $renderField('Tür Zustand', $object->door_condition, '', 'door-open') !!}{!! $renderField('Angebotsbereit', $object->ready_for_offer, '', 'badge-check') !!}
                            </div>@if(!empty($object->note) || !empty($object->object_remark))
                            <div class="lh-text-box">{{ $plainText($object->note ?: $object->object_remark) }}</div>@endif
                        </details>
                        <details class="lh-collapse">
                            <summary><span><i data-lucide="triangle"></i>03
                                    Dachflächen</span><em>{{ $safeCollection($object->roofs ?? [])->count() ?: 'Basis' }}</em>
                            </summary>@php $roofs = $safeCollection($object->roofs ?? []); @endphp @if($roofs->count() > 0)
                                <div class="lh-product-grid">@foreach($roofs as $roof)<div class="lh-mini-card lh-searchable"
                                    data-panel="object-data"
                                    data-title="Dachfläche {{ $roof->designation }} {{ $roof->roof_type }} {{ $roof->orientation }} {{ $object->roof_direction }}">
                                    <strong>{{ $roof->designation ?: 'Dachfläche ' . $loop->iteration }}</strong><span>{{ $roof->roof_type ?? $roof->type ?? $object->roof_type ?? 'Dach' }}</span><small>{{ $roof->orientation ?? $roof->direction ?? $object->roof_direction ?? 'Ausrichtung offen' }}
                                        ·
                                        {{ $roof->pitch ?? $roof->roof_pitch ?? $object->roof_pitch ?? 'Neigung offen' }}</small>
                            </div>@endforeach</div>@else<div class="lh-data-grid wide">
                                    {!! $renderField('Dachtyp', $object->roof_type, '', 'home') !!}{!! $renderField('Dachalter', $object->roof_age, '', 'clock') !!}{!! $renderField('Dachneigung', $object->roof_pitch, '°', 'triangle') !!}{!! $renderField('Ausrichtung', $object->roof_direction, '', 'compass') !!}{!! $renderField('Dacheindeckung', $object->roof_covering, '', 'layers') !!}
                                </div>@endif @if(!empty($object->roof_remark))
                            <div class="lh-text-box">{{ $plainText($object->roof_remark) }}</div>@endif
                        </details>
                        <details class="lh-collapse">
                            <summary><span><i data-lucide="thermometer"></i>04 Heizung & Wärmepumpe</span><em>WP</em></summary>
                            <div class="lh-data-grid wide">
                                {!! $renderField('Heizungsart', $object->heating_system_type, '', 'flame') !!}{!! $renderField('Heizungsalter', $object->heating_system_age, 'Jahre', 'clock') !!}{!! $renderField('Heizleistung alt', $object->old_heating_power, 'kW', 'gauge') !!}{!! $renderField('Aufstellort', $object->installation_location, '', 'map-pin') !!}{!! $renderField('Kamin', $object->fireplace, '', 'flame-kindling') !!}{!! $renderField('Heizlast', $object->heating_load_calculation, 'kW', 'activity') !!}{!! $renderField('Wärmeübergabe', $object->heating_type, '', 'waves') !!}{!! $renderField('Heizkreise', $object->heating_circuits_count, '', 'git-branch') !!}{!! $renderField('Rohrmaterial', $object->pipe_system_material, '', 'package') !!}{!! $renderField('Heizung Dimension', $object->heating_pipe_dimension, '', 'ruler') !!}{!! $renderField('KW / WW Dimension', $object->water_pipe_dimension, '', 'droplets') !!}{!! $renderField('Zirkulation Dimension', $object->circulation_pipe_dimension, '', 'repeat') !!}
                            </div>@if(!empty($object->heating_notes) || !empty($object->heating_remark))
                                <div class="lh-text-box">{{ $plainText($object->heating_notes ?: $object->heating_remark) }}</div>
                            @endif
                        </details>
                        <details class="lh-collapse">
                            <summary><span><i data-lucide="zap"></i>05 Energie, Elektrik & E-Mobilität</span><em>kWh</em>
                            </summary>
                            <div class="lh-data-grid wide">
                                {!! $renderField('Haushaltsstrom', $object->power_household, 'kWh', 'plug') !!}{!! $renderField('WP-Strom', $object->power_heatpump, 'kWh', 'thermometer-sun') !!}{!! $renderField('E-Auto-Strom', $object->power_electric_car, 'kWh', 'car') !!}{!! $renderField('Sonstiges Strom', $object->power_other, 'kWh', 'zap') !!}{!! $renderField('Gesamtverbrauch', $object->power_total, 'kWh', 'activity') !!}{!! $renderField('Zählerschrank', $object->meter_cabinet, '', 'grid-2x2') !!}{!! $renderField('Zähleranzahl', $object->meter_count, '', 'hash') !!}{!! $renderField('SLS Schalter', $object->sls_switch, '', 'toggle-left') !!}{!! $renderField('AC Überspannungsschutz', $object->ac_surge_protection, '', 'shield-check') !!}{!! $renderField('Netzwerk/WLAN', $object->network_wlan, '', 'wifi') !!}{!! $renderField('E-Auto', $object->electric_car, '', 'car') !!}{!! $renderField('Anzahl Autos', $object->electric_car_count, '', 'cars') !!}{!! $renderField('Fahrleistung', $object->car_kilo, 'km', 'route') !!}{!! $renderField('Wallboxen', $object->wallbox_count, '', 'battery-charging') !!}{!! $renderField('Wallbox Ort', $object->wallbox_location, '', 'map-pin') !!}
                            </div>
                        </details>
                        <details class="lh-collapse">
                            <summary><span><i data-lucide="messages-square"></i>06
                                    Objektkommunikation</span><em>{{ $objectNotes->count() + $objectReports->count() }}</em>
                            </summary>
                            <div class="lh-split">
                                <div>
                                    <h4>Notizen</h4>@foreach($objectNotes->take(4) as $note)<div
                                        class="lh-chat-bubble lh-searchable" data-panel="notes"
                                        data-title="{{ $plainText($note->description) }} {{ $objectName($object) }}">
                                        <strong>{{ $employeeName($note->creator ?? null) }}</strong>
                                        <p>{{ $plainText($note->description) }}</p>
                                        <small>{{ $formatDateTime($note->created_at) }}</small>
                                    </div>@endforeach @if($objectNotes->isEmpty())
                                    <div class="lh-empty small">Keine Objektnotizen.</div>@endif
                                </div>
                                <div>
                                    <h4>Berichte</h4>@foreach($objectReports->take(4) as $report)<details
                                        class="lh-report-mini lh-searchable" data-panel="reports"
                                        data-title="{{ $plainText($report->report ?? $report->description ?? $report->note ?? '') }} {{ $objectName($object) }}">
                                        <summary>{{ $formatDate($report->report_date ?? $report->created_at) }} ·
                                            {{ $employeeName($report->reporter ?? null) }}</summary>
                                        <p>{{ $plainText($report->report ?? $report->description ?? $report->note ?? '') }}</p>
                                    </details>@endforeach @if($objectReports->isEmpty())
                                    <div class="lh-empty small">Keine Objektberichte.</div>@endif
                                </div>
                            </div>
                        </details>
                    </article>
                @endforeach
                @if($objects->isEmpty())
                <div class="lh-empty">Keine Objektdaten vorhanden.</div>@endif
            </section>

            <section class="lh-panel" data-lh-panel="timeline">
                <div class="lh-section-title"><span>03</span>
                    <h2>Timeline</h2>
                </div>
                <div class="lh-timeline">@foreach($timeline as $item)<article class="lh-timeline-row lh-searchable"
                    data-panel="timeline"
                    data-title="{{ $item['title'] ?? '' }} {{ $item['text'] ?? '' }} {{ $item['type'] ?? '' }}">
                    <div class="lh-timeline-icon"><i data-lucide="{{ $item['icon'] ?? 'circle' }}"></i></div>
                    <div><strong>{{ $item['title'] ?? 'Historie' }}</strong>
                        <p>{{ $plainText($item['text'] ?? '') }}</p>
                        <small>{{ $formatDateTime($item['date'] ?? null) }}</small>
                    </div>
                </article>@endforeach @if($timeline->isEmpty())
                    <div class="lh-empty">Keine Historie vorhanden.</div>@endif
                </div>
            </section>

            <section class="lh-panel" data-lh-panel="notes">
                <div class="lh-section-title"><span>04</span>
                    <h2>Notizen</h2><small>Klar nach Objekt und Artikelgruppe</small>
                </div>
                <div class="lh-context-layout">
                    <aside class="lh-context-sidebar">
                        <h3><i data-lucide="messages-square"></i> Notizen filtern</h3><button type="button"
                            class="lh-context-filter active" data-lh-context-filter="notes" data-context-key="all"><span><i
                                    data-lucide="list"></i>Alle Notizen</span><em>{{ $notes->count() }}</em></button><button
                            type="button" class="lh-context-filter" data-lh-context-filter="notes"
                            data-context-key="general"><span><i data-lucide="user-round"></i>Allgemeiner
                                Kunde</span><em>{{ $notes->whereNull('alternative_id')->whereNull('product_id')->count() }}</em></button>
                        @foreach($objects as $object)
                            @php
                                $objectProductsForNotes = $leadProductLists->where('alternative_id', $object->id)->values();
                                $objectNoteCount = $notes->where('alternative_id', $object->id)->count();
                            @endphp
                            <details class="lh-context-group" open>
                                <summary><span>{{ $objectName($object) }}</span><i data-lucide="chevron-down"></i></summary>
                                <div class="lh-context-items"><button type="button" class="lh-context-filter"
                                        data-lh-context-filter="notes" data-context-key="object-{{ $object->id }}"><span><i
                                                data-lucide="home"></i>Objekt
                                            allgemein</span><em>{{ $objectNoteCount }}</em></button>@foreach($objectProductsForNotes as $lp)@php $productNoteCount = $notes->filter(function ($n) use ($object, $lp) {
                                                    return (int) ($n->alternative_id ?? 0) === (int) $object->id && (int) ($n->product_id ?? 0) === (int) $lp->product_id; })->count(); @endphp<button
                                                type="button" class="lh-context-filter" data-lh-context-filter="notes"
                                                data-context-key="product-{{ $object->id }}-{{ $lp->product_id }}"><span><i
                                            data-lucide="layers"></i>{{ $productName($lp->product ?? $lp->articleGroup ?? null) }}</span><em>{{ $productNoteCount }}</em></button>@endforeach
                                </div>
                            </details>
                        @endforeach
                    </aside>
                    <div class="lh-context-content" data-lh-context-content="notes">
                        @foreach($notes as $note)
                            @php
                                $noteObject = $objects->firstWhere('id', (int) ($note->alternative_id ?? 0));
                                $noteKey = $contextKeyFor($note->alternative_id ?? null, $note->product_id ?? null);
                                $noteProductName = !empty($note->product_id) ? $findProductName($note->product_id, $note->alternative_id ?? null) : null;
                            @endphp
                            <article class="lh-chat-message lh-searchable" data-context-key="{{ $noteKey }}" data-panel="notes"
                                data-title="{{ $plainText($note->description) }} {{ $contextLabel($note) }} {{ $employeeName($note->creator ?? null) }}">
                                <div class="lh-chat-avatar">{{ mb_substr($employeeName($note->creator ?? null), 0, 1) }}</div>
                                <div class="lh-chat-content">
                                    <div class="lh-chat-meta">
                                        <strong>{{ $employeeName($note->creator ?? null) }}</strong><span>{{ $formatDateTime($note->created_at) }}</span>
                                    </div>
                                    <p>{{ $plainText($note->description) }}</p>
                                    <div class="lh-context-badges"><span class="lh-context-badge object"><i
                                                data-lucide="home"></i>{{ $noteObject ? $objectName($noteObject) : 'Allgemeiner Kunde' }}</span>@if($noteProductName)<span
                                                    class="lh-context-badge product"><i
                                                data-lucide="layers"></i>{{ $noteProductName }}</span>@endif</div>
                                    @foreach($safeCollection($note->replies ?? []) as $reply)<div class="lh-chat-reply">
                                        <strong>{{ $employeeName($reply->creator ?? null) }}</strong>
                                        <p>{{ $plainText($reply->description) }}</p>
                                        <small>{{ $formatDateTime($reply->created_at) }}</small>
                                    </div>@endforeach
                                </div>
                            </article>
                        @endforeach
                        @if($notes->isEmpty())
                        <div class="lh-empty">Keine Notizen vorhanden.</div>@endif
                        <div class="lh-empty lh-context-empty">Keine Notizen für diesen Objekt-/Artikelgruppenfilter.</div>
                    </div>
                </div>
            </section>

            <section class="lh-panel" data-lh-panel="reports">
                <div class="lh-section-title"><span>05</span>
                    <h2>Berichte</h2><small>Klar nach Objekt und Artikelgruppe</small>
                </div>
                <div class="lh-context-layout">
                    <aside class="lh-context-sidebar">
                        <h3><i data-lucide="file-text"></i> Berichte filtern</h3><button type="button"
                            class="lh-context-filter active" data-lh-context-filter="reports"
                            data-context-key="all"><span><i data-lucide="list"></i>Alle
                                Berichte</span><em>{{ $reports->count() }}</em></button><button type="button"
                            class="lh-context-filter" data-lh-context-filter="reports" data-context-key="general"><span><i
                                    data-lucide="user-round"></i>Allgemeiner
                                Kunde</span><em>{{ $reports->whereNull('alternative_id')->whereNull('product_id')->count() }}</em></button>
                        @foreach($objects as $object)
                            @php
                                $objectProductsForReports = $leadProductLists->where('alternative_id', $object->id)->values();
                                $objectReportCount = $reports->where('alternative_id', $object->id)->count();
                            @endphp
                            <details class="lh-context-group" open>
                                <summary><span>{{ $objectName($object) }}</span><i data-lucide="chevron-down"></i></summary>
                                <div class="lh-context-items"><button type="button" class="lh-context-filter"
                                        data-lh-context-filter="reports" data-context-key="object-{{ $object->id }}"><span><i
                                                data-lucide="home"></i>Objekt
                                            allgemein</span><em>{{ $objectReportCount }}</em></button>@foreach($objectProductsForReports as $lp)@php $productReportCount = $reports->filter(function ($r) use ($object, $lp) {
                                                    return (int) ($r->alternative_id ?? 0) === (int) $object->id && (int) ($r->product_id ?? 0) === (int) $lp->product_id; })->count(); @endphp<button
                                                type="button" class="lh-context-filter" data-lh-context-filter="reports"
                                                data-context-key="product-{{ $object->id }}-{{ $lp->product_id }}"><span><i
                                            data-lucide="layers"></i>{{ $productName($lp->product ?? $lp->articleGroup ?? null) }}</span><em>{{ $productReportCount }}</em></button>@endforeach
                                </div>
                            </details>
                        @endforeach
                    </aside>
                    <div class="lh-context-content" data-lh-context-content="reports">
                        @foreach($reports as $report)
                            @php
                                $reportObject = $objects->firstWhere('id', (int) ($report->alternative_id ?? 0));
                                $reportKey = $contextKeyFor($report->alternative_id ?? null, $report->product_id ?? null);
                                $reportProductName = !empty($report->product_id) ? $findProductName($report->product_id, $report->alternative_id ?? null) : null;
                                $reportText = $plainText($report->report ?? $report->description ?? $report->note ?? '');
                            @endphp
                            <details class="lh-report-card lh-searchable" data-context-key="{{ $reportKey }}"
                                data-panel="reports"
                                data-title="{{ $reportText }} {{ $contextLabel($report) }} {{ $employeeName($report->reporter ?? null) }}">
                                <summary><span>{{ $formatDate($report->report_date ?? $report->created_at) }} ·
                                        {{ $employeeName($report->reporter ?? null) }}</span><small>{{ $contextLabel($report) }}</small>
                                </summary>
                                <div class="lh-context-badges"><span class="lh-context-badge object"><i
                                            data-lucide="home"></i>{{ $reportObject ? $objectName($reportObject) : 'Allgemeiner Kunde' }}</span>@if($reportProductName)<span
                                                class="lh-context-badge product"><i
                                            data-lucide="layers"></i>{{ $reportProductName }}</span>@endif</div>
                                <div class="lh-report-body">{{ $reportText }}</div>
                            </details>
                        @endforeach
                        @if($reports->isEmpty())
                        <div class="lh-empty">Keine Berichte vorhanden.</div>@endif
                        <div class="lh-empty lh-context-empty">Keine Berichte für diesen Objekt-/Artikelgruppenfilter.</div>
                    </div>
                </div>
            </section>

            <section class="lh-panel" data-lh-panel="appointments">
                <div class="lh-section-title"><span>06</span>
                    <h2>Termine</h2>
                </div>
                <div class="lh-card-grid two">@foreach($appointments as $appointment)<article class="lh-card lh-searchable"
                    data-panel="appointments"
                    data-title="{{ $appointment->name }} {{ $plainText($appointment->note) }} {{ $appointment->full_address }} {{ $appointment->appointment_type }}">
                    <div class="lh-card-head">
                        <h3><i data-lucide="calendar-days"></i>{{ $appointment->name ?: 'Termin' }}</h3>
                        <small>{{ $formatDate($appointment->start_date) }} {{ $appointment->start_time }}</small>
                    </div>
                    <div class="lh-data-grid">
                        {!! $renderField('Typ', $appointment->appointment_type ?: $appointment->type, '', 'tag') !!}{!! $renderField('Status', $appointment->status, '', 'activity') !!}{!! $renderField('Zeit', trim(($appointment->start_time ?: '') . ' - ' . ($appointment->end_time ?: '')), '', 'clock') !!}{!! $renderField('Ort', $appointment->full_address ?: trim(($appointment->street ?? '') . ' ' . ($appointment->postcode ?? '') . ' ' . ($appointment->city ?? '')), '', 'map-pin') !!}
                    </div>@if(!empty($appointment->note))
                    <div class="lh-text-box">{{ $plainText($appointment->note) }}</div>@endif
                </article>@endforeach @if($appointments->isEmpty())
                    <div class="lh-empty">Keine Termine vorhanden.</div>@endif
                </div>
            </section>
            <section class="lh-panel" data-lh-panel="tasks">
                <div class="lh-section-title"><span>07</span>
                    <h2>Aufgaben</h2>
                </div>
                <div class="lh-card-grid two">@foreach($tasks as $task)<article class="lh-card lh-searchable"
                    data-panel="tasks"
                    data-title="{{ $task->task_title }} {{ $plainText($task->description) }} {{ $task->task_status }}">
                    <div class="lh-card-head">
                        <h3><i data-lucide="check-square"></i>{{ $task->task_title ?: 'Aufgabe' }}</h3>
                        <small>{{ $formatDate($task->due_date) }}</small>
                    </div>
                    <p>{{ $plainText($task->description) }}</p>
                    <div class="lh-chip-row">
                        <span>{{ $task->task_status ?: 'Offen' }}</span><span>{{ $task->priority ?: 'Normal' }}</span><span>{{ $task->progress ?? 0 }}%</span>
                    </div>
                </article>@endforeach @if($tasks->isEmpty())
                    <div class="lh-empty">Keine Aufgaben vorhanden.</div>@endif
                </div>
            </section>
            <section class="lh-panel" data-lh-panel="tickets">
                <div class="lh-section-title"><span>08</span>
                    <h2>Tickets</h2>
                </div>
                <div class="lh-card-grid two">@foreach($tickets as $ticket)<article class="lh-card lh-searchable"
                    data-panel="tickets"
                    data-title="{{ $ticket->ticket_no }} {{ $plainText($ticket->problem) }} {{ $plainText($ticket->solution) }} {{ $ticket->status }}">
                    <div class="lh-card-head">
                        <h3><i data-lucide="badge-alert"></i>{{ $ticket->ticket_no ?: 'Ticket' }}</h3>
                        <small>{{ $formatDate($ticket->date) }}</small>
                    </div>
                    <p>{{ $plainText($ticket->problem) }}</p>@if(!empty($ticket->solution))
                    <div class="lh-text-box small">{{ $plainText($ticket->solution) }}</div>@endif<div
                        class="lh-chip-row">
                        <span>{{ $ticket->status ?: 'Offen' }}</span><span>{{ $findObjectName($ticket->alternative_id ?? null) }}</span><span>{{ $productName($ticket->product ?? null) }}</span>
                    </div>
                </article>@endforeach @if($tickets->isEmpty())
                    <div class="lh-empty">Keine Tickets vorhanden.</div>@endif
                </div>
            </section>
            <section class="lh-panel" data-lh-panel="offers">
                <div class="lh-section-title"><span>09</span>
                    <h2>Angebote</h2>
                </div>
                <div class="lh-card-grid two">@foreach($offerFolders as $folder)<article class="lh-card lh-searchable"
                    data-panel="offers"
                    data-title="{{ $folder->name }} {{ $folder->offer->offer_no ?? '' }} {{ $productName($folder->product ?? null) }} {{ $findObjectName($folder->alternative_id ?? null) }}">
                    <div class="lh-card-head">
                        <h3><i data-lucide="briefcase-business"></i>{{ $folder->name ?: 'Angebot' }}</h3>
                        <small>{{ $formatDateTime($folder->created_at) }}</small>
                    </div>
                    <div class="lh-data-grid">
                        {!! $renderField('Angebotsnummer', $folder->offer->offer_no ?? null, '', 'file-badge') !!}{!! $renderField('Artikelgruppe', $productName($folder->product ?? null), '', 'layers') !!}{!! $renderField('Objekt', $findObjectName($folder->alternative_id ?? null), '', 'home') !!}{!! $renderField('Status', $folder->workflow_status_label ?? $folder->status ?? null, '', 'activity') !!}
                    </div>
                </article>@endforeach @if($offerFolders->isEmpty())
                    <div class="lh-empty">Keine Angebote vorhanden.</div>@endif
                </div>
            </section>
            <section class="lh-panel" data-lh-panel="invoices">
                <div class="lh-section-title"><span>10</span>
                    <h2>Rechnungen</h2>
                </div>
                <div class="lh-card-grid two">@foreach($invoices as $invoice)<article class="lh-card lh-searchable"
                    data-panel="invoices"
                    data-title="{{ $invoice->invoice_no ?? $invoice->invoice_number ?? '' }} {{ $invoice->status ?? '' }}">
                    <div class="lh-card-head">
                        <h3><i
                                data-lucide="receipt"></i>{{ $invoice->invoice_no ?? $invoice->invoice_number ?? 'Rechnung' }}
                        </h3><small>{{ $formatDate($invoice->created_at) }}</small>
                    </div>
                    <div class="lh-chip-row">
                        <span>{{ $invoice->status ?? 'Offen' }}</span><span>{{ $money($invoice->total_gross ?? $invoice->total ?? null) }}</span>
                    </div>
                </article>@endforeach @if($invoices->isEmpty())
                    <div class="lh-empty">Keine Rechnungen vorhanden.</div>@endif
                </div>
            </section>

            <section class="lh-panel" data-lh-panel="neighbors">
                <div class="lh-section-title"><span>11</span>
                    <h2>Nachbarn & Referenzen</h2><small>{{ $radius }} km Radius</small>
                </div>
                <div class="lh-neighbor-list">
                    @foreach($neighbors as $n)@php $neighborName = trim(($n->customer_name ?? '') . ' ' . ($n->customer_lastname ?? ''));
                            if (!$neighborName) {
                                $neighborName = $n->firma ?? $n->lead_firma ?? $n->name ?? 'Kunde';
                            }
                            $leadProducts = collect($n->product_rows ?? [])->where('source', 'lead_product_list')->values();
                        $cpiProducts = collect($n->product_rows ?? [])->where('source', 'customer_product_info')->values(); @endphp
                        <article class="lh-neighbor-row lh-searchable" data-panel="neighbors"
                            data-title="{{ $neighborName }} {{ $n->full_address }} {{ collect($n->product_rows ?? [])->pluck('product_name')->join(' ') }}">
                            <div><strong>{{ $neighborName }}</strong><small>{{ $n->firma ?? $n->lead_firma ?? '' }}</small>
                            </div>
                            <div>
                                <span>{{ $n->full_address ?: trim(($n->street ?? '') . ' ' . ($n->postcode ?? '') . ' ' . ($n->city ?? $n->lead_city ?? '')) }}</span><small>{{ number_format((float) ($n->distance_km ?? 0), 2, ',', '.') }}
                                    km</small></div>
                            <div class="lh-chip-row">
                                @foreach($leadProducts as $product)<span>{{ $product['product_name'] ?? 'Produkt' }} ·
                                {{ $product['stage_label'] ?? 'Offen' }}</span>@endforeach
                                @foreach($cpiProducts as $product)<span>{{ $product['product_name'] ?? 'Produkt' }}</span>@endforeach
                                @if(!$leadProducts->count() && !$cpiProducts->count())<span>Keine Produkte</span>@endif</div><a
                                href="{{ url('/new_lead_profile/' . ($n->customer_id ?? $n->lead_id)) }}" target="_blank"
                                class="lh-action-btn primary">Profil</a>
                    </article>@endforeach @if($neighbors->isEmpty())
                    <div class="lh-empty">Keine Nachbarn im Radius gefunden.</div>@endif
                </div>
            </section>

            <section class="lh-panel" data-lh-panel="media">
                <div class="lh-section-title"><span>12</span>
                    <h2>Medien</h2><small>Bilder mit Galerie, Zoom und Navigation</small>
                </div>
                <div class="lh-media-grid">
                    @foreach($images as $image)@php $url = $mediaUrl($image);
                        $imgContext = $findObjectName($image->alternative_id ?? null); @endphp<button
                            type="button" class="lh-media-card lh-searchable" data-panel="media"
                            data-title="{{ $image->image_name }} {{ $image->status }} {{ $imgContext }}"
                            data-lh-gallery-index="{{ $loop->index }}">@if($url)
                                <div class="lh-media-thumb"><img src="{{ $url }}" alt="{{ $image->image_name ?: 'Bild' }}"
                            loading="lazy"></div>@else<div class="lh-media-fallback"><i data-lucide="file"></i>Datei
                                    </div>@endif<div class="lh-media-info">
                                <strong>{{ $image->image_name ?: 'Bild' }}</strong><small>{{ $imgContext }} ·
                                    {{ $formatDate($image->created_at) }}</small></div>
                    </button>@endforeach @if($images->isEmpty())
                    <div class="lh-empty">Keine Medien vorhanden.</div>@endif
                </div>
            </section>
        </main>
    </div>

    <div class="lh-image-modal" id="lhImageModal" aria-hidden="true">
        <div class="lh-image-modal-panel">
            <div class="lh-image-modal-top">
                <div class="lh-image-modal-title"><strong id="lhImageTitle">Bild</strong><small id="lhImageMeta">—</small>
                </div>
                <div class="lh-image-modal-actions"><button type="button" class="lh-image-btn" data-lh-zoom-out><i
                            data-lucide="zoom-out"></i></button><button type="button" class="lh-image-btn"
                        data-lh-zoom-reset>100%</button><button type="button" class="lh-image-btn" data-lh-zoom-in><i
                            data-lucide="zoom-in"></i></button><button type="button" class="lh-image-btn"
                        data-lh-modal-close><i data-lucide="x"></i></button></div>
            </div>
            <div class="lh-image-stage"><button type="button" class="lh-image-nav prev" data-lh-gallery-prev><i
                        data-lucide="chevron-left"></i></button><img id="lhImagePreview" src="" alt="Bild"><button
                    type="button" class="lh-image-nav next" data-lh-gallery-next><i
                        data-lucide="chevron-right"></i></button></div>
            <div class="lh-image-modal-bottom"><span class="lh-image-counter" id="lhImageCounter">—</span><span>ESC
                    schließen · Pfeiltasten wechseln · +/- Zoom</span></div>
        </div>
    </div>

    <script>
        (function () {
            if (window.__leadHistorySafeInitialized) return;
            window.__leadHistorySafeInitialized = true;
            function ready(fn) { if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', fn); } else { fn(); } }
            function icons() { try { if (window.lucide && typeof window.lucide.createIcons === 'function') { window.lucide.createIcons(); } } catch (e) { } }
            function norm(v) { return String(v || '').toLowerCase().replace(/<[^>]*>/g, ' ').replace(/\s+/g, ' ').trim(); }
            function esc(v) { return String(v || '').replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c]; }); }
            function label(key) { return { overview: 'Überblick', 'object-data': 'Objektdaten', timeline: 'Timeline', notes: 'Notizen', reports: 'Berichte', appointments: 'Termine', tasks: 'Aufgaben', tickets: 'Tickets', offers: 'Angebote', invoices: 'Rechnungen', neighbors: 'Nachbarn', media: 'Medien' }[key] || key; }
            window.lhOpenPanel = function (key, scrollTop) {
                key = key || 'overview';
                document.querySelectorAll('[data-lh-panel]').forEach(function (panel) { var active = panel.getAttribute('data-lh-panel') === key; panel.classList.toggle('active', active); panel.style.display = active ? 'block' : 'none'; });
                document.querySelectorAll('[data-lh-panel-btn]').forEach(function (btn) { btn.classList.toggle('active', btn.getAttribute('data-lh-panel-btn') === key); });
                if (scrollTop !== false) { window.scrollTo({ top: 0, behavior: 'smooth' }); }
                icons();
            };
            window.lhJumpTo = function (id, panel) { window.lhOpenPanel(panel || 'overview', false); setTimeout(function () { var el = document.getElementById(id); if (el) { el.scrollIntoView({ behavior: 'smooth', block: 'start' }); el.classList.add('lh-highlight'); setTimeout(function () { el.classList.remove('lh-highlight'); }, 1300); } }, 120); };
            function applyContext(type, key) {
                key = key || 'all';
                document.querySelectorAll('[data-lh-context-filter="' + type + '"]').forEach(function (btn) { btn.classList.toggle('active', btn.getAttribute('data-context-key') === key); });
                var box = document.querySelector('[data-lh-context-content="' + type + '"]');
                if (!box) return;
                var visible = 0;
                box.querySelectorAll('[data-context-key]').forEach(function (item) { var show = key === 'all' || item.getAttribute('data-context-key') === key; item.classList.toggle('lh-hidden-by-context', !show); if (show) visible++; });
                box.classList.toggle('is-filtered', key !== 'all');
                var empty = box.querySelector('.lh-context-empty');
                if (empty) empty.style.display = (key !== 'all' && visible === 0) ? 'block' : 'none';
                icons();
            }
            ready(function () {
                var app = document.getElementById('lhApp');
                window.lhOpenPanel((document.querySelector('.lh-panel.active') || document.querySelector('.lh-panel') || {}).getAttribute('data-lh-panel') || 'overview', false);
                document.addEventListener('click', function (e) {
                    var tab = e.target.closest('[data-lh-panel-btn]'); if (tab) { e.preventDefault(); window.lhOpenPanel(tab.getAttribute('data-lh-panel-btn')); return; }
                    var jump = e.target.closest('.lh-jump[data-target]'); if (jump) { e.preventDefault(); window.lhJumpTo(jump.getAttribute('data-target'), jump.getAttribute('data-panel') || 'object-data'); return; }
                    var tog = e.target.closest('[data-lh-sidebar-toggle]'); if (tog) { e.preventDefault(); if (app) app.classList.toggle('is-sidebar-collapsed'); icons(); return; }
                    var filter = e.target.closest('[data-lh-context-filter]'); if (filter) { e.preventDefault(); applyContext(filter.getAttribute('data-lh-context-filter'), filter.getAttribute('data-context-key')); return; }
                }, true);
                var input = document.getElementById('lhSearch'), box = document.getElementById('lhSearchResults');
                if (input && box) {
                    var index = Array.from(document.querySelectorAll('.lh-searchable')).map(function (el) { return { el: el, panel: el.getAttribute('data-panel') || (el.closest('[data-lh-panel]') || {}).getAttribute?.('data-lh-panel') || 'overview', title: el.getAttribute('data-title') || el.textContent || '', text: norm((el.getAttribute('data-title') || '') + ' ' + (el.textContent || '')) }; });
                    input.addEventListener('input', function () { var q = norm(input.value); box.innerHTML = ''; box.classList.remove('is-open'); if (q.length < 2) return; var matches = index.filter(function (it) { return it.text.indexOf(q) !== -1; }).slice(0, 16); if (!matches.length) { box.innerHTML = '<div class="lh-search-empty">Keine Treffer gefunden.</div>'; box.classList.add('is-open'); return; } matches.forEach(function (it) { var b = document.createElement('button'); b.type = 'button'; var t = String(it.title || it.el.textContent || '').replace(/\s+/g, ' ').trim(); if (t.length > 95) t = t.slice(0, 95) + '…'; b.innerHTML = '<strong>' + esc(t) + '</strong><small>' + esc(label(it.panel)) + '</small>'; b.addEventListener('click', function () { window.lhOpenPanel(it.panel, false); box.classList.remove('is-open'); setTimeout(function () { it.el.scrollIntoView({ behavior: 'smooth', block: 'center' }); it.el.classList.add('lh-highlight'); setTimeout(function () { it.el.classList.remove('lh-highlight'); }, 1400); }, 120); }); box.appendChild(b); }); box.classList.add('is-open'); });
                    document.addEventListener('click', function (e) { if (!e.target.closest('.lh-searchbar')) box.classList.remove('is-open'); });
                }
                var gallery = [];
                document.querySelectorAll('[data-lh-gallery-index]').forEach(function (card) { var img = card.querySelector('img'); if (!img) return; gallery.push({ src: img.getAttribute('src'), title: (card.querySelector('strong') || {}).textContent || 'Bild', meta: (card.querySelector('small') || {}).textContent || '' }); card.setAttribute('data-lh-gallery-index', gallery.length - 1); });
                var modal = document.getElementById('lhImageModal'), preview = document.getElementById('lhImagePreview'), title = document.getElementById('lhImageTitle'), meta = document.getElementById('lhImageMeta'), counter = document.getElementById('lhImageCounter'); var current = 0, zoom = 1;
                function render() { if (!modal || !gallery.length) return; var item = gallery[current]; preview.src = item.src; preview.style.setProperty('--zoom', zoom); title.textContent = item.title; meta.textContent = item.meta; counter.textContent = (current + 1) + ' / ' + gallery.length + ' · Zoom ' + Math.round(zoom * 100) + '%'; icons(); }
                function open(i) { if (!modal || !gallery.length) return; current = Math.max(0, Math.min(gallery.length - 1, i || 0)); zoom = 1; modal.classList.add('is-open'); modal.setAttribute('aria-hidden', 'false'); document.body.style.overflow = 'hidden'; render(); }
                function close() { if (!modal) return; modal.classList.remove('is-open'); modal.setAttribute('aria-hidden', 'true'); document.body.style.overflow = ''; }
                function next() { current = (current + 1) % gallery.length; zoom = 1; render(); }
                function prev() { current = (current - 1 + gallery.length) % gallery.length; zoom = 1; render(); }
                document.addEventListener('click', function (e) { var card = e.target.closest('[data-lh-gallery-index]'); if (card) { e.preventDefault(); open(parseInt(card.getAttribute('data-lh-gallery-index'), 10) || 0); return; } if (!modal || !modal.classList.contains('is-open')) return; if (e.target.closest('[data-lh-modal-close]') || e.target === modal) { close(); return; } if (e.target.closest('[data-lh-gallery-next]')) { next(); return; } if (e.target.closest('[data-lh-gallery-prev]')) { prev(); return; } if (e.target.closest('[data-lh-zoom-in]')) { zoom = Math.min(4, zoom + .2); render(); return; } if (e.target.closest('[data-lh-zoom-out]')) { zoom = Math.max(.4, zoom - .2); render(); return; } if (e.target.closest('[data-lh-zoom-reset]')) { zoom = 1; render(); return; } });
                document.addEventListener('keydown', function (e) { if (!modal || !modal.classList.contains('is-open')) return; if (e.key === 'Escape') close(); if (e.key === 'ArrowRight') next(); if (e.key === 'ArrowLeft') prev(); if (e.key === '+' || e.key === '=') { zoom = Math.min(4, zoom + .2); render(); } if (e.key === '-' || e.key === '_') { zoom = Math.max(.4, zoom - .2); render(); } });
                applyContext('notes', 'all'); applyContext('reports', 'all'); icons();
            });
        })();
    </script>
@endsection