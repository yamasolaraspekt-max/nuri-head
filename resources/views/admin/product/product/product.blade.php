@extends('admin.layouts.app')

@section('title') Produkte @endsection

@section('style')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}">

    <style>
        :root {
            --products-bg: #ffffff;
            --products-soft: #f8fafc;
            --products-line: rgba(15, 23, 42, .08);
            --products-line-2: #e5e7eb;
            --products-text: #111827;
            --products-muted: #6b7280;
            --products-brand: #74b2d4;
            --products-green: #93c21c;
            --products-green-dark: #7baa18;
            --products-green-soft: #f4fae7;
            --products-blue: #2563eb;
            --products-blue-soft: #eff6ff;
            --products-success: #16a34a;
            --products-success-soft: #ecfdf5;
            --products-danger: #dc2626;
            --products-danger-soft: #fef2f2;
            --products-warning: #f59e0b;
            --products-warning-soft: #fffbeb;
            --products-shadow: 0 18px 45px rgba(15, 23, 42, .08);
            --products-shadow-lg: 0 30px 80px rgba(15, 23, 42, .16);
            --cart-width: 430px;
        }

        .products-shell {
            border-radius: 18px;
            background: var(--products-bg);
            box-shadow: var(--products-shadow);
            border: 1px solid rgba(15, 23, 42, .06);
            padding: 1.25rem 1.5rem;
        }

        @media (max-width: 991.98px) {
            .products-shell {
                padding: 1rem;
            }
        }

        .products-header {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1rem;
        }

        .products-header-title h2 {
            margin: 0;
            font-size: 1.3rem;
            color: var(--products-text);
            font-weight: 800;
        }

        .products-header-title small {
            color: var(--products-muted);
            font-size: .8rem;
        }

        .products-header-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .5rem;
            align-items: center;
        }

        .products-meta-pill {
            font-size: .75rem;
            padding: .15rem .6rem;
            border-radius: 999px;
            background: #eff6ff;
            color: var(--products-brand);
            display: inline-flex;
            align-items: center;
            gap: .35rem;
        }

        .products-filters-shell {
            margin-bottom: 1rem;
        }

        #product-list-loading {
            display: none;
            text-align: center;
            padding: 2rem 0;
            color: var(--products-muted);
        }

        #product-pagination {
            margin-top: .75rem;
        }

        .view-toggle-group {
            border-radius: 999px;
            background: #f3f4f6;
            padding: 2px;
            display: inline-flex;
            align-items: center;
        }

        .view-toggle-btn {
            border-radius: 999px;
            border: 0;
            background: transparent;
            padding: .25rem .65rem;
            font-size: .75rem;
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            color: #4b5563;
            cursor: pointer;
        }

        .view-toggle-btn.active {
            background: #ffffff;
            box-shadow: 0 0 0 1px rgba(148, 163, 184, .6);
            color: #111827;
        }

        .bulk-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .5rem;
            font-size: .78rem;
            padding: .35rem .75rem;
            border-radius: 999px;
            background: #f9fafb;
            border: 1px dashed rgba(148, 163, 184, .7);
        }

        .bulk-bar .badge-count {
            font-weight: 700;
            padding: 0 .2rem;
        }

        .bulk-cart-btn {
            border: 1px solid rgba(147, 194, 28, .28);
            background: var(--products-green-soft);
            color: #66860f;
            font-weight: 800;
        }

        .bulk-cart-btn:hover {
            border-color: var(--products-green);
            background: #edf7d3;
        }

        .bulk-check {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }

        .bulk-check input[type="checkbox"] {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            width: 100%;
            height: 100%;
            margin: 0;
        }

        .bulk-check span {
            width: 18px;
            height: 18px;
            border-radius: 6px;
            border: 1px solid rgba(148, 163, 184, .9);
            background: #f9fafb;
            display: inline-block;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .18);
            transition: all .12s ease-out;
            position: relative;
        }


        .bulk-check span::after {
            content: "";
            position: absolute;
            width: 9px;
            height: 5px;
            border-left: 2px solid #ffffff;
            border-bottom: 2px solid #ffffff;
            transform: rotate(-45deg) scale(0.4);
            top: 4px;
            left: 4px;
            opacity: 0;
            transition: all .12s ease-out;
        }

        .bulk-check input:checked+span {
            background: linear-gradient(135deg, #2563eb, #22c55e);
            border-color: #74b2d4;
            box-shadow: 0 0 0 1px rgba(37, 99, 235, .45);
        }

        .bulk-check input:checked+span::after {
            opacity: 1;
            transform: rotate(-45deg) scale(1);
        }

        .product-thumb-table {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            object-fit: cover;
            border: 1px solid rgba(148, 163, 184, .22);
            background: #f8fafc;
            box-shadow: 0 4px 12px rgba(15, 23, 42, .08);
        }

        .product-thumb-table-placeholder {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border: 1px dashed rgba(148, 163, 184, .45);
            color: #94a3b8;
            font-size: .9rem;
        }

        .product-card-image-wrap {
            margin: -.25rem -.25rem .9rem;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, .18);
            background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            aspect-ratio: 16/10;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-card-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .product-card-image-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            gap: .35rem;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            font-size: .8rem;
        }

        .product-duplicated-flash {
            position: relative;
            border-color: #f97316 !important;
            box-shadow: 0 0 0 2px rgba(249, 115, 22, .45), 0 18px 45px rgba(15, 23, 42, .25);
            background-image: linear-gradient(90deg, rgba(249, 115, 22, .04), rgba(59, 130, 246, .03));
            animation: productClonePulse .9s ease-in-out 2;
        }

        .product-duplicated-badge {
            display: inline-flex;
            align-items: center;
            font-size: .7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .03em;
            padding: .08rem .45rem;
            margin-left: .35rem;
            border-radius: 999px;
            color: #9a3412;
            background: rgba(254, 215, 170, .95);
            border: 1px solid rgba(248, 153, 73, .7);
            box-shadow: 0 1px 2px rgba(15, 23, 42, .25);
        }

        .product-updated-flash {
            position: relative;
            border-color: #22c55e !important;
            box-shadow: 0 0 0 2px rgba(34, 197, 94, .35), 0 18px 45px rgba(15, 23, 42, .22);
            background-image: linear-gradient(90deg, rgba(34, 197, 94, .06), rgba(59, 130, 246, .04));
            animation: productUpdatedPulse .9s ease-in-out 2;
        }

        .product-updated-badge {
            display: inline-flex;
            align-items: center;
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .03em;
            padding: .08rem .45rem;
            margin-left: .35rem;
            border-radius: 999px;
            color: #14532d;
            background: rgba(187, 247, 208, .95);
            border: 1px solid rgba(34, 197, 94, .55);
            box-shadow: 0 1px 2px rgba(15, 23, 42, .18);
        }

        @keyframes productClonePulse {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-1px);
            }

            100% {
                transform: translateY(0);
            }
        }

        @keyframes productUpdatedPulse {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-1px);
            }

            100% {
                transform: translateY(0);
            }
        }

        .custom-menu {
            display: none !important;
        }

        .list-menu-container {
            position: relative;
        }

        .product-menu-float {
            position: fixed;
            min-width: 220px;
            max-width: 260px;
            background-color: #ffffff;
            color: #111827;
            padding: .5rem 0;
            border-radius: 12px;
            border: 1px solid rgba(15, 23, 42, .12);
            box-shadow: 0 18px 45px rgba(15, 23, 42, .25);
            z-index: 9999;
            opacity: 0;
            transform: scale(.95);
            transform-origin: top right;
            pointer-events: none;
        }

        .product-menu-float.show {
            opacity: 1;
            transform: scale(1);
            pointer-events: auto;
            animation: productMenuFadeIn .16s ease-out;
        }

        .product-menu-float.drop-up {
            transform-origin: bottom right;
        }

        .product-menu-float .dropdown-item {
            padding: .45rem 1rem;
            font-size: .85rem;
            cursor: pointer;
            white-space: nowrap;
        }

        .product-menu-float .dropdown-item:hover {
            background: rgba(15, 23, 42, .04);
        }

        .product-menu-float .dropdown-divider {
            height: 1px;
            margin: .35rem 0;
            background: rgba(148, 163, 184, .5);
        }

        @keyframes productMenuFadeIn {
            from {
                opacity: 0;
                transform: scale(.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* cart icon */


        .product-cart-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .42);
            backdrop-filter: blur(2px);
            opacity: 0;
            pointer-events: none;
            transition: opacity .18s ease;
            z-index: 1090;
        }

        .product-cart-backdrop.show {
            opacity: 1;
            pointer-events: auto;
        }

        .product-cart-drawer {
            position: fixed;
            top: 0;
            right: 0;
            width: var(--cart-width);
            max-width: 100vw;
            height: 100vh;
            background: #fff;
            box-shadow: -20px 0 60px rgba(15, 23, 42, .18);
            z-index: 1095;
            transform: translateX(100%);
            transition: transform .22s ease;
            display: flex;
            flex-direction: column;
        }

        .product-cart-backdrop {
            z-index: 1090;
        }

        .product-cart-drawer {
            z-index: 1095;
        }

        .swal2-container {
            z-index: 20000 !important;
        }

        .swal2-backdrop-show,
        .swal2-shown>.swal2-container {
            z-index: 20000 !important;
        }

        .product-cart-drawer.show {
            transform: translateX(0);
        }

        .product-cart-head {
            padding: 1rem 1rem .9rem;
            border-bottom: 1px solid #e5e7eb;
            background: linear-gradient(180deg, #fff 0%, #fafafa 100%);
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .75rem;
        }

        .product-cart-head h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 800;
            color: #111827;
        }

        .product-cart-head small {
            display: block;
            margin-top: .25rem;
            color: #6b7280;
            font-size: .76rem;
            line-height: 1.45;
        }

        .product-cart-close {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #6b7280;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .product-cart-close:hover {
            background: #f8fafc;
            color: #111827;
        }

        .product-cart-body {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: .9rem;
            overflow: auto;
            flex: 1;
            min-height: 0;
        }

        .product-cart-group {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: .9rem;
            background: #fff;
        }

        .product-cart-label {
            display: block;
            font-size: .72rem;
            font-weight: 800;
            color: #6b7280;
            margin-bottom: .38rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .product-cart-input,
        .product-cart-select,
        .product-cart-textarea {
            width: 100%;
            min-height: 40px;
            border: 1px solid #dbe2ea;
            border-radius: 12px;
            background: #fff;
            color: #111827;
            padding: .7rem .8rem;
            outline: none;
            transition: all .15s ease;
        }

        .product-cart-textarea {
            min-height: 84px;
            resize: vertical;
        }

        .product-cart-input:focus,
        .product-cart-select:focus,
        .product-cart-textarea:focus {
            border-color: #93c21c;
            box-shadow: 0 0 0 4px rgba(147, 194, 28, .15);
        }

        .product-cart-row {
            display: grid;
            grid-template-columns: 1fr 94px;
            gap: .6rem;
        }

        .product-cart-btn {
            border: none;
            border-radius: 12px;
            min-height: 40px;
            padding: .7rem .95rem;
            font-weight: 800;
            cursor: pointer;
            transition: all .15s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .45rem;
        }

        .product-cart-btn-primary {
            background: #93c21c;
            color: #fff;
        }

        .product-cart-btn-primary:hover {
            background: #7baa18;
        }

        .product-cart-btn-soft {
            background: #fff;
            color: #111827;
            border: 1px solid #e5e7eb;
        }

        .product-cart-btn-soft:hover {
            background: #f9fafb;
        }

        .product-cart-meta-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .55rem;
        }

        .product-cart-stat {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #f8fafc;
            padding: .75rem;
        }

        .product-cart-stat-label {
            font-size: .68rem;
            font-weight: 800;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: .2rem;
        }

        .product-cart-stat-value {
            font-size: .92rem;
            font-weight: 900;
            color: #111827;
        }

        .product-cart-sections {
            display: flex;
            flex-direction: column;
            gap: .7rem;
        }

        .product-cart-section {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
        }

        .product-cart-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .7rem;
            padding: .8rem .9rem;
            border-bottom: 1px solid #eef2f7;
            background: #fbfdff;
        }

        .product-cart-section-title {
            display: flex;
            align-items: center;
            gap: .55rem;
            min-width: 0;
        }

        .product-cart-section-dot {
            width: 12px;
            height: 12px;
            border-radius: 999px;
            flex: 0 0 auto;
        }

        .product-cart-section-name {
            font-size: .86rem;
            font-weight: 800;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-cart-items {
            padding: .8rem;
            display: flex;
            flex-direction: column;
            gap: .6rem;
        }

        .product-cart-item {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #fff;
            overflow: hidden;
        }

        .product-cart-item.sub {
            margin-left: 18px;
            border-left: 3px solid #cbd5e1;
        }

        .product-cart-item-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: .7rem;
            padding: .75rem;
        }

        .product-cart-item-left {
            min-width: 0;
            flex: 1;
        }

        .product-cart-item-title {
            font-size: .82rem;
            font-weight: 800;
            color: #111827;
            line-height: 1.35;
            margin-bottom: .2rem;
        }

        .product-cart-item-meta {
            font-size: .72rem;
            color: #6b7280;
            line-height: 1.5;
        }

        .product-cart-item-controls {
            display: flex;
            align-items: center;
            gap: .35rem;
            flex-wrap: wrap;
            margin-top: .45rem;
        }

        .product-cart-mini-input {
            width: 84px;
            min-height: 32px;
            border: 1px solid #dbe2ea;
            border-radius: 10px;
            padding: .35rem .5rem;
            font-size: .76rem;
        }

        .product-cart-empty {
            text-align: center;
            padding: 1rem;
            border: 1px dashed #dbe2ea;
            border-radius: 14px;
            color: #94a3b8;
            font-size: .8rem;
            background: #fcfcfd;
        }

        .product-cart-pill {
            display: inline-flex;
            align-items: center;
            padding: .18rem .45rem;
            border-radius: 999px;
            font-size: .65rem;
            font-weight: 800;
            background: #eff6ff;
            color: #2563eb;
            margin-left: .35rem;
        }

        .product-cart-helper {
            font-size: .72rem;
            color: #6b7280;
            line-height: 1.55;
        }

        .product-add-cart-btn {
            border: 1px solid rgba(147, 194, 28, .25);
            background: #f4fae7;
            color: #6d8c12;
        }

        .product-add-cart-btn:hover {
            border-color: #93c21c;
            background: #ebf6cf;
            color: #5d7710;
        }

        .badge-light {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
        }

        .badge-success {
            background: #ecfdf5;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .masterset-swal-on-top {
            z-index: 20000 !important;
        }

        #ms-cart-step-products-wrap,
        #ms-cart-step-config-wrap {
            animation: fadeInCartStep .18s ease;
        }

        @keyframes fadeInCartStep {
            from {
                opacity: 0;
                transform: translateY(4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-cart-header-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            min-width: 22px;
            height: 22px;
            border-radius: 999px;
            background: #ef4444;
            color: #fff;
            font-size: .72rem;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px;
            border: 2px solid #fff;
            line-height: 1;
        }

        .product-cart-product {
            display: flex;
            gap: .75rem;
            align-items: flex-start;
        }

        .product-cart-product-media {
            width: 72px;
            height: 72px;
            border-radius: 14px;
            overflow: hidden;
            flex: 0 0 72px;
            border: 1px solid rgba(148, 163, 184, .22);
            background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-cart-product-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .product-cart-product-placeholder {
            color: #94a3b8;
            font-size: 1rem;
        }

        .product-cart-product-content {
            min-width: 0;
            flex: 1;
        }

        .product-cart-item-desc {
            font-size: .72rem;
            color: #6b7280;
            line-height: 1.45;
            margin-top: .25rem;
        }

        .product-cart-item-price-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            margin-top: .45rem;
            font-size: .76rem;
            font-weight: 700;
            color: #111827;
        }

        .btn-block {
            width: 100%;
        }

        .product-cart-item {
            position: relative;
        }

        .product-cart-item.dragging {
            opacity: .45;
            transform: scale(.98);
        }

        .product-cart-drop-target {
            outline: 2px dashed #93c21c;
            outline-offset: 2px;
            background: #f4fae7;
        }

        .product-cart-item-head-actions {
            display: flex;
            align-items: center;
            gap: .35rem;
            margin-top: .45rem;
            flex-wrap: wrap;
        }

        .product-cart-drag-handle {
            cursor: grab;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #64748b;
        }

        .product-cart-drag-handle:active {
            cursor: grabbing;
        }

        .product-cart-root-dropzone {
            border: 2px dashed #dbe2ea;
            border-radius: 14px;
            padding: .85rem;
            text-align: center;
            color: #64748b;
            background: #fafafa;
            font-size: .78rem;
            margin-bottom: .65rem;
        }

        .product-cart-root-dropzone.active {
            border-color: #93c21c;
            background: #f4fae7;
            color: #5d7710;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container .select2-selection--single {
            min-height: 40px !important;
            border: 1px solid #dbe2ea !important;
            border-radius: 12px !important;
            display: flex !important;
            align-items: center !important;
            padding: .1rem .2rem !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px !important;
            padding-left: .65rem !important;
            color: #111827 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 38px !important;
        }
    </style>

    <style>
        .product-image-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .58);
            backdrop-filter: blur(4px);
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
            z-index: 30000;
        }

        .product-image-modal-backdrop.show {
            opacity: 1;
            pointer-events: auto;
        }

        .product-image-modal {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
            z-index: 30001;
        }

        .product-image-modal.show {
            opacity: 1;
            pointer-events: auto;
        }

        .product-image-modal-dialog {
            width: min(1100px, 100%);
            max-height: calc(100vh - 48px);
            transform: translateY(14px) scale(.98);
            transition: transform .22s ease;
        }

        .product-image-modal.show .product-image-modal-dialog {
            transform: translateY(0) scale(1);
        }

        .product-image-modal-card {
            background: #fff;
            border-radius: 28px;
            overflow: hidden;
            box-shadow: 0 40px 110px rgba(15, 23, 42, .28);
            border: 1px solid rgba(15, 23, 42, .06);
            display: flex;
            flex-direction: column;
            max-height: calc(100vh - 48px);
        }

        .product-image-modal-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 22px 14px;
            border-bottom: 1px solid #eef2f7;
            background: linear-gradient(180deg, #ffffff 0%, #fafcff 100%);
        }

        .product-image-modal-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 900;
            color: #111827;
        }

        .product-image-modal-subtitle {
            margin-top: 4px;
            color: #6b7280;
            font-size: .82rem;
        }

        .product-image-modal-close {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .16s ease;
            flex: 0 0 auto;
        }

        .product-image-modal-close:hover {
            background: #f8fafc;
            color: #111827;
            transform: translateY(-1px);
        }

        .product-image-modal-body {
            padding: 20px;
            overflow: auto;
        }

        .product-image-layout {
            display: grid;
            grid-template-columns: minmax(0, 1.25fr) minmax(320px, .85fr);
            gap: 18px;
            align-items: start;
        }

        .product-image-preview-shell,
        .product-image-form-card {
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .05);
        }

        .product-image-preview-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border-bottom: 1px solid #eef2f7;
            background: #f8fafc;
            font-size: .84rem;
            font-weight: 800;
            color: #111827;
        }

        .product-image-preview-badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid rgba(37, 99, 235, .12);
            font-size: .7rem;
            font-weight: 800;
        }

        .product-image-preview-box {
            min-height: 430px;
            background: linear-gradient(180deg, #f8fafc 0%, #eef2f7 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
        }

        .product-image-preview-box img {
            max-width: 100%;
            max-height: 390px;
            object-fit: contain;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 18px 42px rgba(15, 23, 42, .14);
        }

        .product-image-preview-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .75rem;
            color: #94a3b8;
            font-weight: 800;
            text-align: center;
        }

        .product-image-preview-placeholder i {
            width: 40px;
            height: 40px;
        }

        .product-image-form-card {
            padding: 18px;
        }

        .product-image-product-name {
            font-size: .96rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 14px;
            line-height: 1.45;
        }

        .product-image-upload-drop {
            width: 100%;
            border: 2px dashed #cbd5e1;
            border-radius: 18px;
            background: #f8fafc;
            padding: 18px 16px;
            cursor: pointer;
            transition: all .18s ease;
            display: block;
            margin: 0;
        }

        .product-image-upload-drop:hover {
            border-color: #74b2d4;
            background: #eff6ff;
        }

        .product-image-upload-inner {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .product-image-upload-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            background: #ffffff;
            border: 1px solid #dbe2ea;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .product-image-upload-title {
            font-size: .88rem;
            font-weight: 800;
            color: #111827;
        }

        .product-image-upload-text {
            font-size: .76rem;
            color: #6b7280;
            margin-top: 2px;
        }

        .product-image-file-name {
            min-height: 18px;
            margin-top: 8px;
            font-size: .76rem;
            color: #64748b;
            word-break: break-word;
        }

        .product-image-form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .product-image-gallery-wrap {
            margin-top: 18px;
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            background: #fff;
            padding: 18px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, .05);
        }

        .product-image-gallery-head {
            margin-bottom: 14px;
        }

        .product-image-gallery-head h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 900;
            color: #111827;
        }

        .product-image-gallery-head small {
            color: #6b7280;
            font-size: .78rem;
        }

        .product-image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(138px, 1fr));
            gap: 12px;
        }

        .product-image-gallery-item {
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 8px 20px rgba(15, 23, 42, .05);
        }

        .product-image-gallery-thumb {
            aspect-ratio: 1/1;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-image-gallery-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .product-image-gallery-body {
            padding: 10px;
        }

        .product-image-gallery-title {
            font-size: .74rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.35;
            min-height: 32px;
            word-break: break-word;
        }

        .product-image-gallery-actions {
            display: flex;
            gap: 6px;
            margin-top: 8px;
        }

        .product-image-gallery-btn {
            flex: 1;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #475569;
            font-size: .72rem;
            font-weight: 800;
            padding: 6px 8px;
            cursor: pointer;
            transition: all .15s ease;
        }

        .product-image-gallery-btn:hover {
            background: #f8fafc;
        }

        .product-image-gallery-btn.delete:hover {
            background: #fef2f2;
            border-color: #fecaca;
            color: #dc2626;
        }

        .product-modern-distributor-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .product-modern-distributor-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 8px 10px;
            border: 1px solid rgba(148, 163, 184, .20);
            border-radius: 14px;
            background: #f8fafc;
        }

        .product-modern-distributor-top {
            display: flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
        }

        .product-modern-distributor-name {
            font-size: 12px;
            font-weight: 800;
            color: #334155;
            line-height: 1.35;
            word-break: break-word;
        }

        .product-modern-distributor-price {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
            font-size: 12px;
            color: #0f172a;
            font-weight: 700;
            padding-left: 20px;
        }

        .product-modern-distributor-price small {
            color: #64748b;
            font-weight: 700;
            margin-right: 2px;
        }

        .product-modern-distributor-badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 900;
            line-height: 1;
            white-space: nowrap;
        }

        .product-modern-distributor-badge.cheapest {
            background: #ecfdf5;
            color: #15803d;
            border: 1px solid rgba(34, 197, 94, .20);
        }

        .product-modern-distributor-badge.expensive {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid rgba(239, 68, 68, .18);
        }

        .product-modern-distributor-meta {
            font-size: 11px;
            color: #6b7280;
            padding-left: 20px;
            line-height: 1.4;
        }

        .product-modern-distributor-more {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 10px;
            border-radius: 12px;
            background: #eff6ff;
            border: 1px solid rgba(37, 99, 235, .14);
            color: #2563eb;
            font-size: 11px;
            font-weight: 800;
            cursor: pointer;
            width: max-content;
        }

        .product-dist-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .58);
            backdrop-filter: blur(4px);
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
            z-index: 30020;
        }

        .product-dist-modal-backdrop.show {
            opacity: 1;
            pointer-events: auto;
        }

        .product-dist-modal {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            opacity: 0;
            pointer-events: none;
            transition: opacity .2s ease;
            z-index: 30021;
        }

        .product-dist-modal.show {
            opacity: 1;
            pointer-events: auto;
        }

        .product-dist-modal-dialog {
            width: min(760px, 100%);
            max-height: calc(100vh - 48px);
            transform: translateY(14px) scale(.98);
            transition: transform .22s ease;
        }

        .product-dist-modal.show .product-dist-modal-dialog {
            transform: translateY(0) scale(1);
        }

        .product-dist-modal-card {
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 40px 110px rgba(15, 23, 42, .28);
            border: 1px solid rgba(15, 23, 42, .06);
            display: flex;
            flex-direction: column;
            max-height: calc(100vh - 48px);
        }

        .product-dist-modal-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 20px 14px;
            border-bottom: 1px solid #eef2f7;
            background: linear-gradient(180deg, #ffffff 0%, #fafcff 100%);
        }

        .product-dist-modal-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 900;
            color: #111827;
        }

        .product-dist-modal-subtitle {
            margin-top: 4px;
            color: #6b7280;
            font-size: .82rem;
        }

        .product-dist-modal-close {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .product-dist-modal-body {
            padding: 18px;
            overflow: auto;
        }

        .product-dist-modal-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        body.product-dist-modal-open {
            overflow: hidden !important;
        }

        body.product-image-modal-open {
            overflow: hidden !important;
        }

        @media (max-width: 991.98px) {
            .product-image-layout {
                grid-template-columns: 1fr;
            }

            .product-image-preview-box {
                min-height: 300px;
            }

            .product-image-preview-box img {
                max-height: 260px;
            }
        }

        @media (max-width: 575.98px) {
            .product-image-modal {
                padding: 12px;
            }

            .product-image-modal-card {
                border-radius: 22px;
            }

            .product-image-modal-head,
            .product-image-modal-body {
                padding: 14px;
            }

            .product-image-gallery {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        .product-supplier-select-wrap {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .product-supplier-select {
            width: 100%;
            min-height: 36px;
            border: 1px solid #dbe2ea;
            border-radius: 12px;
            background: #fff;
            color: #111827;
            padding: 7px 10px;
            font-size: 12px;
            font-weight: 800;
            outline: none;
            transition: all .15s ease;
        }

        .product-supplier-select:focus {
            border-color: #74b2d4;
            box-shadow: 0 0 0 4px rgba(116, 178, 212, .16);
        }

        .product-supplier-detail {
            border: 1px solid rgba(148, 163, 184, .22);
            border-radius: 14px;
            background: #f8fafc;
            padding: 9px 10px;
        }

        .product-supplier-detail-name {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            font-weight: 900;
            color: #334155;
            margin-bottom: 6px;
        }

        .product-supplier-detail-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 5px;
        }

        .product-supplier-detail-line {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 8px;
            font-size: 11px;
            line-height: 1.45;
        }

        .product-supplier-detail-label {
            color: #64748b;
            font-weight: 800;
            white-space: nowrap;
        }

        .product-supplier-detail-value {
            color: #0f172a;
            font-weight: 800;
            text-align: right;
            word-break: break-word;
        }

        .product-supplier-empty {
            font-size: 12px;
            color: #94a3b8;
            font-weight: 700;
        }

        .product-history-change-head {
            display: grid;
            grid-template-columns: 170px 1fr 1fr;
            gap: 8px;
            margin-bottom: 7px;
            font-size: 10px;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .product-history-change-caption {
            display: block;
            font-size: 9px;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .05em;
            margin-bottom: 4px;
        }

        .product-history-change-row {
            display: grid;
            grid-template-columns: 170px 1fr 1fr;
            gap: 8px;
            align-items: stretch;
            font-size: 11px;
        }

        .product-history-change-field,
        .product-history-change-old,
        .product-history-change-new {
            border-radius: 12px;
            padding: 9px 10px;
            border: 1px solid #e5e7eb;
            line-height: 1.45;
            word-break: break-word;
        }

        .product-history-change-field {
            background: #f8fafc;
            color: #111827;
            font-weight: 900;
        }

        .product-history-change-old {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #9a3412;
        }

        .product-history-change-new {
            background: #ecfdf5;
            border-color: #bbf7d0;
            color: #14532d;
        }

        @media (max-width: 991.98px) {
            .product-history-change-head {
                display: none;
            }

            .product-history-change-row {
                grid-template-columns: 1fr;
            }
        }

        /* =========================================================
           ✅ Notion-like filter bar + fixed horizontal product rows
        ========================================================= */

        .products-shell {
            background: #fbfbfa;
            border-color: #e7e5e4;
            box-shadow: 0 18px 55px rgba(15, 23, 42, .07);
        }

        .products-header {
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(15, 23, 42, .06);
        }

        .products-header-actions {
            justify-content: flex-end;
        }

        .product-filter-notion {
            border: 1px solid rgba(15, 23, 42, .08);
            background: #ffffff;
            border-radius: 18px;
            box-shadow: 0 8px 28px rgba(15, 23, 42, .05);
            overflow: hidden;
        }

        .product-filter-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px;
        }

        .product-filter-search {
            flex: 1;
            min-width: 260px;
            position: relative;
        }

        .product-filter-search i {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            color: #94a3b8;
            width: 16px;
            height: 16px;
        }

        .product-filter-search input {
            width: 100%;
            height: 42px;
            border: 1px solid transparent;
            background: #f8fafc;
            border-radius: 13px;
            padding: 0 14px 0 40px;
            color: #111827;
            outline: none;
            font-size: 13px;
            font-weight: 700;
            transition: all .16s ease;
        }

        .product-filter-search input:focus {
            background: #ffffff;
            border-color: #93c21c;
            box-shadow: 0 0 0 4px rgba(147, 194, 28, .14);
        }

        .product-filter-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .product-filter-btn {
            height: 38px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            background: #ffffff;
            color: #374151;
            font-size: 12px;
            font-weight: 900;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            cursor: pointer;
            transition: all .15s ease;
        }

        .product-filter-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .product-filter-btn.primary {
            background: #111827;
            color: #ffffff;
            border-color: #111827;
        }

        .product-filter-btn.primary:hover {
            background: #020617;
        }

        .product-filter-btn.green {
            background: #f4fae7;
            color: #66860f;
            border-color: rgba(147, 194, 28, .25);
        }

        .product-filter-btn.green:hover {
            border-color: #93c21c;
            background: #edf7d3;
        }

        .product-filter-count {
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            background: #eff6ff;
            color: #2563eb;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 900;
        }

        .product-filter-panel {
            display: none;
            border-top: 1px solid rgba(15, 23, 42, .06);
            padding: 14px;
            background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%);
        }

        .product-filter-panel.show {
            display: block;
            animation: productFilterOpen .16s ease-out;
        }

        @keyframes productFilterOpen {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-filter-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .product-filter-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
        }

        .product-filter-field label {
            margin: 0;
            font-size: 11px;
            font-weight: 900;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .product-filter-field .form-control,
        .product-filter-field select {
            min-height: 40px;
            border-radius: 12px;
            border: 1px solid #dbe2ea;
            background: #ffffff;
            color: #111827;
            font-size: 13px;
            font-weight: 700;
        }

        .product-filter-field .select2-container .select2-selection--single {
            min-height: 40px !important;
            border-radius: 12px !important;
        }

        .product-filter-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 14px;
            padding-top: 12px;
            border-top: 1px solid rgba(15, 23, 42, .06);
        }

        .product-filter-footer-left,
        .product-filter-footer-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .product-filter-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            height: 28px;
            border-radius: 999px;
            padding: 0 10px;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            color: #64748b;
            font-size: 11px;
            font-weight: 900;
        }

        .product-filter-chip.active {
            background: #eff6ff;
            color: #2563eb;
            border-color: rgba(37, 99, 235, .18);
        }

        /* =========================================================
           ✅ Force injected product details into row layout
           Works for product-modern/product-card/list rows from AJAX
        ========================================================= */

        #product-list {
            min-width: 0;
        }

        #product-list .product-modern-list,
        #product-list .product-list,
        #product-list .products-list {
            display: flex !important;
            flex-direction: column !important;
            gap: 10px !important;
        }

        #product-list .product-modern-item,
        #product-list .product-list-item,
        #product-list .product-row,
        #product-list .product-card[data-product-id] {
            width: 100% !important;
            display: grid !important;
            grid-template-columns: 42px 72px minmax(260px, 1.7fr) minmax(130px, .8fr) minmax(150px, .9fr) minmax(150px, .9fr) minmax(130px, .75fr) minmax(170px, .8fr) 48px !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 12px 14px !important;
            border: 1px solid rgba(15, 23, 42, .08) !important;
            border-radius: 16px !important;
            background: #ffffff !important;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .04) !important;
            margin: 0 0 10px 0 !important;
        }

        #product-list .product-modern-item:hover,
        #product-list .product-list-item:hover,
        #product-list .product-row:hover,
        #product-list .product-card[data-product-id]:hover {
            border-color: rgba(147, 194, 28, .45) !important;
            box-shadow: 0 16px 34px rgba(15, 23, 42, .08) !important;
        }

        #product-list .product-card-image-wrap {
            margin: 0 !important;
            width: 58px !important;
            height: 58px !important;
            aspect-ratio: 1/1 !important;
            border-radius: 14px !important;
        }

        #product-list .product-card-image,
        #product-list .product-thumb-table {
            width: 58px !important;
            height: 58px !important;
            object-fit: cover !important;
            border-radius: 14px !important;
        }

        #product-list .product-card-image-placeholder,
        #product-list .product-thumb-table-placeholder {
            width: 58px !important;
            height: 58px !important;
            border-radius: 14px !important;
        }

        #product-list .product-modern-title,
        #product-list .product-list-name,
        #product-list .product-card-title {
            font-size: 13px !important;
            font-weight: 900 !important;
            color: #111827 !important;
            line-height: 1.35 !important;
            margin: 0 !important;
            white-space: normal !important;
        }

        #product-list .product-modern-meta,
        #product-list .product-card-meta,
        #product-list .product-list-meta {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            flex-wrap: wrap !important;
            color: #64748b !important;
            font-size: 11px !important;
            line-height: 1.45 !important;
        }

        #product-list .product-modern-distributor-list {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 6px !important;
            flex-wrap: wrap !important;
        }

        #product-list .product-modern-distributor-item {
            padding: 6px 8px !important;
            border-radius: 12px !important;
            min-width: 0 !important;
        }

        #product-list .product-modern-distributor-price,
        #product-list .product-modern-distributor-meta {
            padding-left: 0 !important;
        }

        #product-list .product-modern-actions,
        #product-list .product-card-actions,
        #product-list .list-menu-container {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 6px !important;
            flex-wrap: nowrap !important;
        }

        #product-list .dropdown,
        #product-list .btn-group {
            position: relative;
        }

        #product-list table {
            width: 100%;
            table-layout: auto;
        }

        #product-list table td,
        #product-list table th {
            vertical-align: middle !important;
            white-space: normal;
        }

        @media(max-width:1399.98px) {

            #product-list .product-modern-item,
            #product-list .product-list-item,
            #product-list .product-row,
            #product-list .product-card[data-product-id] {
                grid-template-columns: 42px 66px minmax(220px, 1.6fr) minmax(120px, .8fr) minmax(140px, .9fr) minmax(130px, .8fr) 48px !important;
            }
        }

        @media(max-width:991.98px) {
            .product-filter-top {
                align-items: stretch;
                flex-direction: column;
            }

            .product-filter-actions {
                width: 100%;
            }

            .product-filter-actions .product-filter-btn {
                flex: 1;
                justify-content: center;
            }

            .product-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            #product-list .product-modern-item,
            #product-list .product-list-item,
            #product-list .product-row,
            #product-list .product-card[data-product-id] {
                grid-template-columns: 36px 58px minmax(0, 1fr) !important;
                align-items: start !important;
            }
        }

        @media(max-width:575.98px) {
            .product-filter-grid {
                grid-template-columns: 1fr;
            }

            #product-list .product-modern-item,
            #product-list .product-list-item,
            #product-list .product-row,
            #product-list .product-card[data-product-id] {
                grid-template-columns: 34px minmax(0, 1fr) !important;
            }

            #product-list .product-card-image-wrap,
            #product-list .product-card-image,
            #product-list .product-thumb-table,
            #product-list .product-thumb-table-placeholder {
                display: none !important;
            }
        }
    </style>


    <style>
        /* =========================================================
                   FINAL PRODUCTS UI OVERRIDE
                   - Notion-like collapsed filters
                   - Header stays clean
                   - List view stays horizontal
                   - Card view keeps real cards
                ========================================================= */

        .products-shell {
            background: #fbfbfa !important;
            border-color: #e7e5e4 !important;
            box-shadow: 0 18px 55px rgba(15, 23, 42, .07) !important;
        }

        .products-header {
            padding-bottom: 14px !important;
            border-bottom: 1px solid rgba(15, 23, 42, .06) !important;
        }

        .products-header-actions-clean {
            justify-content: flex-end !important;
        }

        .products-cart-btn {
            border-radius: 999px !important;
            font-weight: 800 !important;
            min-height: 34px !important;
        }

        .products-selection-bar {
            margin-top: 10px;
            border: 1px dashed rgba(148, 163, 184, .55);
            background: #ffffff;
            border-radius: 16px;
            padding: 10px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .products-selection-info {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            font-size: 13px;
            font-weight: 800;
        }

        .products-selection-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .products-selection-actions #bulk-action {
            width: 160px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 800;
        }

        .product-filter-notion {
            border: 1px solid rgba(15, 23, 42, .08) !important;
            background: #ffffff !important;
            border-radius: 18px !important;
            box-shadow: 0 8px 28px rgba(15, 23, 42, .05) !important;
            overflow: hidden !important;
        }

        .product-filter-top {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 12px !important;
            padding: 12px !important;
        }

        .product-filter-search {
            flex: 1 !important;
            min-width: 280px !important;
            position: relative !important;
        }

        .product-filter-search i {
            position: absolute !important;
            top: 50% !important;
            left: 14px !important;
            transform: translateY(-50%) !important;
            color: #94a3b8 !important;
            width: 16px !important;
            height: 16px !important;
        }

        .product-filter-search input {
            width: 100% !important;
            height: 44px !important;
            border: 1px solid transparent !important;
            background: #f8fafc !important;
            border-radius: 14px !important;
            padding: 0 14px 0 42px !important;
            color: #111827 !important;
            outline: none !important;
            font-size: 13px !important;
            font-weight: 800 !important;
            transition: all .16s ease !important;
        }

        .product-filter-search input:focus {
            background: #ffffff !important;
            border-color: #93c21c !important;
            box-shadow: 0 0 0 4px rgba(147, 194, 28, .14) !important;
        }

        .product-filter-actions {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex-wrap: wrap !important;
        }

        .product-filter-btn {
            height: 38px !important;
            border-radius: 12px !important;
            border: 1px solid #e5e7eb !important;
            background: #ffffff !important;
            color: #374151 !important;
            font-size: 12px !important;
            font-weight: 900 !important;
            padding: 0 12px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 7px !important;
            cursor: pointer !important;
            transition: all .15s ease !important;
            text-decoration: none !important;
            white-space: nowrap !important;
        }

        .product-filter-btn:hover {
            background: #f8fafc !important;
            border-color: #cbd5e1 !important;
            color: #111827 !important;
        }

        .product-filter-btn.primary {
            background: #111827 !important;
            color: #ffffff !important;
            border-color: #111827 !important;
        }

        .product-filter-btn.primary:hover {
            background: #020617 !important;
            color: #ffffff !important;
        }

        .product-filter-btn.green {
            background: #f4fae7 !important;
            color: #66860f !important;
            border-color: rgba(147, 194, 28, .25) !important;
        }

        .product-filter-btn.green:hover {
            border-color: #93c21c !important;
            background: #edf7d3 !important;
            color: #5f7d0f !important;
        }

        #product-filter-toggle.active {
            background: #eff6ff !important;
            border-color: rgba(37, 99, 235, .22) !important;
            color: #2563eb !important;
        }

        .product-filter-count {
            min-width: 20px !important;
            height: 20px !important;
            padding: 0 6px !important;
            border-radius: 999px !important;
            background: #eff6ff !important;
            color: #2563eb !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 11px !important;
            font-weight: 900 !important;
        }

        .product-filter-panel {
            display: none !important;
            border-top: 1px solid rgba(15, 23, 42, .06) !important;
            padding: 14px !important;
            background: linear-gradient(180deg, #ffffff 0%, #fafafa 100%) !important;
        }

        .product-filter-panel.show {
            display: block !important;
            animation: productFilterOpen .16s ease-out !important;
        }

        @keyframes productFilterOpen {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-filter-grid {
            display: grid !important;
            grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
            gap: 12px !important;
        }

        .product-filter-field {
            display: flex !important;
            flex-direction: column !important;
            gap: 6px !important;
            min-width: 0 !important;
        }

        .product-filter-field label {
            margin: 0 !important;
            font-size: 11px !important;
            font-weight: 900 !important;
            color: #64748b !important;
            text-transform: uppercase !important;
            letter-spacing: .05em !important;
        }

        .product-filter-field .form-control,
        .product-filter-field select {
            min-height: 40px !important;
            border-radius: 12px !important;
            border: 1px solid #dbe2ea !important;
            background: #ffffff !important;
            color: #111827 !important;
            font-size: 13px !important;
            font-weight: 800 !important;
        }

        .product-filter-footer {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 10px !important;
            flex-wrap: wrap !important;
            margin-top: 14px !important;
            padding-top: 12px !important;
            border-top: 1px solid rgba(15, 23, 42, .06) !important;
        }

        .product-filter-footer-left,
        .product-filter-footer-right {
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
            flex-wrap: wrap !important;
        }

        .product-filter-chip {
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            min-height: 28px !important;
            border-radius: 999px !important;
            padding: 5px 10px !important;
            background: #f8fafc !important;
            border: 1px solid #e5e7eb !important;
            color: #64748b !important;
            font-size: 11px !important;
            font-weight: 900 !important;
        }

        .product-filter-chip.active {
            background: #eff6ff !important;
            color: #2563eb !important;
            border-color: rgba(37, 99, 235, .18) !important;
        }

        #product-list {
            min-width: 0;
        }

        .products-view-list #product-list .table-responsive {
            border: 1px solid rgba(15, 23, 42, .08);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .04);
        }

        .products-view-list #product-list table {
            width: 100% !important;
            min-width: 1250px !important;
            table-layout: auto !important;
            margin-bottom: 0 !important;
        }

        .products-view-list #product-list table th,
        .products-view-list #product-list table td {
            vertical-align: middle !important;
            white-space: normal !important;
        }

        .products-view-list #product-list .product-modern-list,
        .products-view-list #product-list .product-list,
        .products-view-list #product-list .products-list {
            display: flex !important;
            flex-direction: column !important;
            gap: 10px !important;
        }

        .products-view-list #product-list .product-modern-item,
        .products-view-list #product-list .product-list-item,
        .products-view-list #product-list .product-row,
        .products-view-list #product-list .product-card[data-product-id] {
            width: 100% !important;
            display: grid !important;
            grid-template-columns: 42px 72px minmax(270px, 1.7fr) minmax(140px, .8fr) minmax(160px, .9fr) minmax(160px, .9fr) minmax(130px, .75fr) minmax(170px, .8fr) 48px !important;
            align-items: center !important;
            gap: 12px !important;
            padding: 12px 14px !important;
            border: 1px solid rgba(15, 23, 42, .08) !important;
            border-radius: 16px !important;
            background: #ffffff !important;
            box-shadow: 0 8px 22px rgba(15, 23, 42, .04) !important;
            margin: 0 0 10px 0 !important;
        }

        .products-view-list #product-list .product-card-image-wrap {
            margin: 0 !important;
            width: 58px !important;
            height: 58px !important;
            aspect-ratio: 1/1 !important;
            border-radius: 14px !important;
        }

        .products-view-list #product-list .product-card-image,
        .products-view-list #product-list .product-thumb-table {
            width: 58px !important;
            height: 58px !important;
            object-fit: cover !important;
            border-radius: 14px !important;
        }

        .products-view-list #product-list .product-card-image-placeholder,
        .products-view-list #product-list .product-thumb-table-placeholder {
            width: 58px !important;
            height: 58px !important;
            border-radius: 14px !important;
        }

        .products-view-list #product-list .product-modern-title,
        .products-view-list #product-list .product-list-name,
        .products-view-list #product-list .product-card-title {
            font-size: 13px !important;
            font-weight: 900 !important;
            color: #111827 !important;
            line-height: 1.35 !important;
            margin: 0 !important;
            white-space: normal !important;
        }

        .products-view-list #product-list .product-modern-meta,
        .products-view-list #product-list .product-card-meta,
        .products-view-list #product-list .product-list-meta {
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
            flex-wrap: wrap !important;
            color: #64748b !important;
            font-size: 11px !important;
            line-height: 1.45 !important;
        }

        .products-view-list #product-list .product-modern-distributor-list {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            gap: 6px !important;
            flex-wrap: wrap !important;
        }

        .products-view-list #product-list .product-modern-actions,
        .products-view-list #product-list .product-card-actions,
        .products-view-list #product-list .list-menu-container {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 6px !important;
            flex-wrap: nowrap !important;
        }

        .products-view-card #product-list .product-card[data-product-id] {
            display: flex !important;
            flex-direction: column !important;
            width: 100% !important;
            height: 100% !important;
            padding: 1rem !important;
            border-radius: 18px !important;
            gap: .75rem !important;
        }

        .products-view-card #product-list .product-card-image-wrap {
            width: auto !important;
            height: auto !important;
            aspect-ratio: 16/10 !important;
            margin: -.25rem -.25rem .75rem !important;
        }

        @media(max-width:1199.98px) {
            .product-filter-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            }
        }

        @media(max-width:991.98px) {
            .product-filter-top {
                align-items: stretch !important;
                flex-direction: column !important;
            }

            .product-filter-actions {
                width: 100% !important;
            }

            .product-filter-actions .product-filter-btn {
                flex: 1 !important;
            }

            .product-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            }

            .products-selection-bar {
                align-items: stretch;
                flex-direction: column;
            }

            .products-selection-actions {
                width: 100%;
            }

            .products-selection-actions #bulk-action,
            .products-selection-actions button {
                flex: 1;
            }

            .products-view-list #product-list .product-modern-item,
            .products-view-list #product-list .product-list-item,
            .products-view-list #product-list .product-row,
            .products-view-list #product-list .product-card[data-product-id] {
                grid-template-columns: 36px 58px minmax(0, 1fr) !important;
                align-items: start !important;
            }
        }

        @media(max-width:575.98px) {
            .product-filter-grid {
                grid-template-columns: 1fr !important;
            }

            .products-header-actions-clean,
            .product-filter-actions,
            .products-selection-actions {
                flex-direction: column;
                align-items: stretch !important;
            }

            .products-header-actions-clean>*,
            .product-filter-actions>*,
            .products-selection-actions>* {
                width: 100%;
            }
        }
    </style>


    <style>
        /* =========================================================
                   WIDE SCREEN STABILITY FIX
                   Keeps the page readable on 1440px, 1920px and ultrawide.
                   Main goal: no vertical broken product rows.
                ========================================================= */

        .products-page {
            overflow-x: hidden;
        }

        .products-page .content-wrapper {
            width: 100%;
            max-width: 1760px;
            margin-left: auto;
            margin-right: auto;
            padding-left: clamp(12px, 1.1vw, 24px);
            padding-right: clamp(12px, 1.1vw, 24px);
        }

        .products-page .content-body,
        .products-page .products-shell,
        .products-page .products-filters-shell,
        .products-page #product-list,
        .products-page #product-pagination {
            min-width: 0;
            max-width: 100%;
        }

        @media (min-width:1800px) {
            .products-page .content-wrapper {
                max-width: 1680px;
            }
        }

        .products-header {
            align-items: flex-start !important;
        }

        .products-header-title {
            min-width: 280px;
            max-width: 560px;
        }

        .products-header-actions-clean {
            max-width: 100%;
            min-width: 0;
        }

        .product-filter-top {
            min-width: 0;
        }

        .product-filter-actions {
            flex: 0 0 auto;
        }

        @media (max-width:1500px) {
            .product-filter-top {
                align-items: stretch !important;
                flex-direction: column !important;
            }

            .product-filter-actions {
                width: 100% !important;
                justify-content: flex-start !important;
            }
        }

        @media (min-width:1501px) {
            .product-filter-search {
                min-width: 420px !important;
            }
        }

        @media (min-width:1400px) {
            .product-filter-grid {
                grid-template-columns: repeat(4, minmax(210px, 1fr)) !important;
            }
        }

        @media (min-width:1750px) {
            .product-filter-grid {
                grid-template-columns: repeat(4, minmax(240px, 1fr)) !important;
            }
        }

        /* The AJAX list can be a table OR card-like rows. On wide/tablet desktop
                   widths with a sidebar, force a horizontal row with safe overflow. */
        .products-view-list #product-list {
            overflow-x: auto;
            overflow-y: visible;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px;
        }

        .products-view-list #product-list::-webkit-scrollbar {
            height: 8px;
        }

        .products-view-list #product-list::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 999px;
        }

        .products-view-list #product-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        .products-view-list #product-list .product-modern-list,
        .products-view-list #product-list .product-list,
        .products-view-list #product-list .products-list {
            width: 100%;
            min-width: 1240px;
        }

        .products-view-list #product-list .product-modern-item,
        .products-view-list #product-list .product-list-item,
        .products-view-list #product-list .product-row,
        .products-view-list #product-list .product-card[data-product-id] {
            min-width: 1240px !important;
            grid-template-columns: 38px 64px minmax(280px, 1.15fr) 120px 150px 190px 125px 150px 44px !important;
            overflow: visible !important;
        }

        @media (min-width:1500px) {

            .products-view-list #product-list .product-modern-list,
            .products-view-list #product-list .product-list,
            .products-view-list #product-list .products-list {
                min-width: 0;
            }

            .products-view-list #product-list .product-modern-item,
            .products-view-list #product-list .product-list-item,
            .products-view-list #product-list .product-row,
            .products-view-list #product-list .product-card[data-product-id] {
                min-width: 0 !important;
                grid-template-columns: 38px 64px minmax(300px, 1.35fr) minmax(120px, .55fr) minmax(140px, .62fr) minmax(170px, .72fr) minmax(120px, .5fr) minmax(150px, .55fr) 44px !important;
            }
        }

        @media (min-width:1900px) {

            .products-view-list #product-list .product-modern-item,
            .products-view-list #product-list .product-list-item,
            .products-view-list #product-list .product-row,
            .products-view-list #product-list .product-card[data-product-id] {
                grid-template-columns: 40px 66px minmax(340px, 1.25fr) minmax(130px, .5fr) minmax(150px, .55fr) minmax(180px, .65fr) minmax(130px, .45fr) minmax(160px, .5fr) 46px !important;
            }
        }

        /* Do not let long text stretch the whole row on wide monitors. */
        .products-view-list #product-list .product-modern-title,
        .products-view-list #product-list .product-list-name,
        .products-view-list #product-list .product-card-title,
        .products-view-list #product-list .product-modern-meta,
        .products-view-list #product-list .product-card-meta,
        .products-view-list #product-list .product-list-meta,
        .products-view-list #product-list .product-modern-distributor-name,
        .products-view-list #product-list .product-supplier-detail-value {
            min-width: 0 !important;
            overflow-wrap: anywhere !important;
            word-break: normal !important;
        }

        .products-view-list #product-list .product-modern-distributor-list {
            max-width: 100%;
        }

        .products-view-list #product-list .product-modern-distributor-item {
            max-width: 220px;
        }

        .products-view-list #product-list .product-supplier-select-wrap,
        .products-view-list #product-list .product-modern-distributor-list {
            min-width: 0 !important;
        }

        .products-view-card #product-list {
            overflow-x: visible;
        }

        .products-view-card #product-list .product-modern-list,
        .products-view-card #product-list .product-list,
        .products-view-card #product-list .products-list {
            min-width: 0 !important;
        }
    </style>

@endsection

@section('content')
    <div class="app-content products-page">

        <div class="content-wrapper">

            <div class="content-body">
                <div class="products-shell">

                    <div class="products-header">
                        <div class="products-header-title">
                            <h2>Artikel & Produkte</h2>
                            <small>Verwalten Sie alle Artikel, Wärmepumpen, PV-Komponenten und Zubehör zentral.</small>
                        </div>

                        <div class="products-header-actions products-header-actions-clean">
                            <span class="products-meta-pill">
                                <i class="feather icon-layers"></i>
                                <span><span id="total-products-label">0</span> Einträge</span>
                            </span>

                            <div class="view-toggle-group">
                                <button type="button" class="view-toggle-btn" data-view="card" id="view-card-btn">
                                    <i class="feather icon-grid"></i> Karten
                                </button>
                                <button type="button" class="view-toggle-btn active" data-view="list" id="view-list-btn">
                                    <i class="feather icon-list"></i> Liste
                                </button>
                            </div>

                            <button type="button" id="product-cart-fab"
                                class="btn btn-outline-success position-relative products-cart-btn">
                                <i class="feather icon-shopping-cart mr-25"></i> Cart
                                <span class="product-cart-header-badge" id="product-cart-fab-count">0</span>
                            </button>
                        </div>
                    </div>

                    <div class="products-filters-shell">
                        <form id="product-filter-form" class="product-filter-notion">
                            <div class="product-filter-top">
                                <div class="product-filter-search">
                                    <i class="feather icon-search"></i>
                                    <input type="text" id="search" name="search" autocomplete="off"
                                        placeholder="Artikelname, Art.Nr., Hersteller, Gruppe suchen ...">
                                </div>

                                <div class="product-filter-actions">
                                    <button type="submit" class="product-filter-btn primary">
                                        <i class="feather icon-search"></i>
                                        Suchen
                                    </button>

                                    <button type="button" class="product-filter-btn" id="product-filter-toggle"
                                        aria-expanded="false" aria-controls="product-filter-panel">
                                        <i class="feather icon-sliders"></i>
                                        Filter
                                        <span class="product-filter-count" id="product-filter-active-count">0</span>
                                    </button>

                                    <button type="button" id="filter-reset-btn" class="product-filter-btn">
                                        <i class="feather icon-rotate-ccw"></i>
                                        Zurücksetzen
                                    </button>

                                    <a href="{{ route('product.create') }}" class="product-filter-btn green">
                                        <i class="feather icon-plus"></i>
                                        Neues Produkt
                                    </a>
                                </div>
                            </div>

                            <div class="product-filter-panel" id="product-filter-panel">
                                <div class="product-filter-grid">
                                    <div class="product-filter-field">
                                        <label for="filter_brand">Hersteller</label>
                                        <select id="filter_brand" name="brand_id" class="form-control select2">
                                            <option value="">Alle Hersteller</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="product-filter-field">
                                        <label for="filter_distributor">Lieferant</label>
                                        <select id="filter_distributor" name="distributor_id" class="form-control select2">
                                            <option value="">Alle Lieferanten</option>
                                            @foreach($distributors as $dist)
                                                <option value="{{ $dist->id }}">{{ $dist->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="product-filter-field">
                                        <label for="filter_article_group">Artikel-Gruppe</label>
                                        <select id="filter_article_group" name="article_group_id"
                                            class="form-control select2">
                                            <option value="">Alle Gruppen</option>
                                            @foreach($articleGroups as $group)
                                                <option value="{{ $group->id }}">{{ $group->article_group }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="product-filter-field">
                                        <label for="filter_status">Status</label>
                                        <select id="filter_status" name="status" class="form-control">
                                            <option value="">Alle Status</option>
                                            <option value="Published">Aktiv</option>
                                            <option value="Unpublished">Inaktiv</option>
                                        </select>
                                    </div>

                                    <div class="product-filter-field">
                                        <label for="filter_category">Kategorie</label>
                                        <select id="filter_category" name="category" class="form-control">
                                            <option value="">Alle Kategorien</option>
                                            <option value="Produkt">Produkt</option>
                                            <option value="Dachziegel">Dachziegel</option>
                                            <option value="Ziegel">Ziegel</option>
                                            <option value="Fenster">Fenster</option>
                                            <option value="Tür">Tür</option>
                                        </select>
                                    </div>

                                    <div class="product-filter-field">
                                        <label for="filter_no_image">Bildstatus</label>
                                        <select id="filter_no_image" name="no_image" class="form-control">
                                            <option value="">Alle Produkte</option>
                                            <option value="1">Ohne Bild</option>
                                        </select>
                                    </div>

                                    <div class="product-filter-field">
                                        <label for="filter_sort">Sortierung</label>
                                        <select id="filter_sort" class="form-control">
                                            <option value="created_at|desc">Neueste zuerst</option>
                                            <option value="created_at|asc">Älteste zuerst</option>
                                            <option value="product|asc">Name A–Z</option>
                                            <option value="product|desc">Name Z–A</option>
                                            <option value="brand|asc">Hersteller A–Z</option>
                                            <option value="brand|desc">Hersteller Z–A</option>
                                            <option value="article_no|asc">Art.Nr. aufsteigend</option>
                                            <option value="article_no|desc">Art.Nr. absteigend</option>
                                        </select>
                                    </div>

                                    <div class="product-filter-field">
                                        <label for="filter_per_page">Pro Seite</label>
                                        <select id="filter_per_page" class="form-control">
                                            <option value="12">12</option>
                                            <option value="24">24</option>
                                            <option value="48">48</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="product-filter-footer">
                                    <div class="product-filter-footer-left">
                                        <span class="product-filter-chip" id="product-filter-chip-brand">Hersteller:
                                            Alle</span>
                                        <span class="product-filter-chip" id="product-filter-chip-dist">Lieferant:
                                            Alle</span>
                                        <span class="product-filter-chip" id="product-filter-chip-group">Gruppe: Alle</span>
                                        <span class="product-filter-chip" id="product-filter-chip-status">Status:
                                            Alle</span>
                                        <span class="product-filter-chip" id="product-filter-chip-category">Kategorie:
                                            Alle</span>
                                        <span class="product-filter-chip" id="product-filter-chip-image">Bild: Alle</span>
                                    </div>

                                    <div class="product-filter-footer-right">
                                        <a href="{{ route('products.export.no-images') }}" id="export-no-image-products-btn"
                                            class="product-filter-btn">
                                            <i class="feather icon-download"></i>
                                            CSV ohne Bilder
                                        </a>

                                        <a href="{{ route('admin.products.images.csv-import.index') }}"
                                            class="product-filter-btn">
                                            <i class="feather icon-image"></i>
                                            Produktbilder CSV Import
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="products-selection-bar">
                            <div class="products-selection-info">
                                <i class="feather icon-check-square"></i>
                                Auswahl: <strong id="selected-count-label">0</strong> Produkte
                            </div>

                            <div class="products-selection-actions">
                                <select id="bulk-action" class="form-control form-control-sm">
                                    <option value="">Aktion wählen</option>
                                    <option value="publish">Veröffentlichen</option>
                                    <option value="unpublish">Deaktivieren</option>
                                    <option value="delete">Löschen</option>
                                </select>

                                <button type="button" id="bulk-apply-btn" class="btn btn-sm btn-outline-primary" disabled>
                                    Anwenden
                                </button>

                                <button type="button" id="bulk-add-cart-btn" class="btn btn-sm bulk-cart-btn" disabled>
                                    <i class="feather icon-shopping-cart mr-25"></i> Auswahl in Cart
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="product-list-loading">
                        <i class="feather icon-loader"></i> Produkte werden geladen ...
                    </div>

                    <div id="product-list" class="mt-1"></div>
                    <div id="product-pagination" class="mt-1"></div>
                </div>
            </div>
        </div>
    </div>



    <div class="product-cart-backdrop" id="product-cart-backdrop"></div>

    <aside class="product-cart-drawer" id="product-cart-drawer">
        <div class="product-cart-head">
            <div>
                <h4>Master-Set Cart</h4>
                <small>Produkte sammeln, Sektionen bilden und in einen neuen oder bestehenden Master Set umwandeln.</small>
            </div>

            <button type="button" class="product-cart-close" id="product-cart-close">
                <i class="feather icon-x"></i>
            </button>
        </div>

        <div class="product-cart-body">
            <input type="hidden" id="ms-cart-id" value="">
            <input type="hidden" id="ms-cart-step" value="products">

            <div class="product-cart-group">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div>
                        <div class="product-cart-label mb-0">Schritt</div>
                        <div class="d-flex align-items-center" style="gap:.5rem; margin-top:.35rem;">
                            <span id="cart-step-badge-products" class="badge badge-success">1. Produkte</span>
                            <span id="cart-step-badge-config" class="badge badge-light">2. Master Set</span>
                        </div>
                    </div>

                    <div class="d-flex" style="gap:.5rem;">
                        <button type="button" id="ms-cart-back-btn" class="btn btn-sm btn-outline-secondary"
                            style="display:none;">
                            <i class="feather icon-arrow-left mr-25"></i> Zurück
                        </button>

                        <button type="button" id="ms-cart-next-btn" class="product-cart-btn product-cart-btn-primary">
                            <i class="feather icon-save mr-25"></i> Als Master Set speichern
                        </button>
                    </div>
                </div>
            </div>

            <div class="product-cart-group">
                <div class="product-cart-meta-grid">
                    <div class="product-cart-stat">
                        <div class="product-cart-stat-label">Produkte</div>
                        <div class="product-cart-stat-value" id="ms-cart-count-total">0</div>
                    </div>
                    <div class="product-cart-stat">
                        <div class="product-cart-stat-label">Sub</div>
                        <div class="product-cart-stat-value" id="ms-cart-sub-total">0,00 €</div>
                    </div>
                    <div class="product-cart-stat">
                        <div class="product-cart-stat-label">Gesamt</div>
                        <div class="product-cart-stat-value" id="ms-cart-total">0,00 €</div>
                    </div>
                </div>
            </div>

            {{-- STEP 1: PRODUCTS --}}
            <div id="ms-cart-step-products-wrap">
                <div class="product-cart-sections" id="ms-cart-sections">
                    <div class="product-cart-empty">Noch keine Cart-Daten geladen.</div>
                </div>
            </div>

            {{-- STEP 2: CONFIG --}}
            <div id="ms-cart-step-config-wrap" style="display:none;">
                <div class="product-cart-group">
                    <label class="product-cart-label">Artikelgruppe</label>
                    <select id="ms-cart-article-group" class="product-cart-select select2-cart">
                        <option value="">Bitte wählen</option>
                        @foreach($articleGroups as $group)
                            <option value="{{ $group->id }}">{{ $group->article_group }}</option>
                        @endforeach
                    </select>

                    <div class="mt-1"></div>

                    <label class="product-cart-label">Modus</label>
                    <select id="ms-cart-mode" class="product-cart-select select2-cart">
                        <option value="new">Neuer Master Set</option>
                        <option value="existing">In bestehenden Master Set einfügen</option>
                    </select>

                    <div id="ms-cart-new-wrap">
                        <div class="mt-1"></div>
                        <label class="product-cart-label">Name</label>
                        <input type="text" id="ms-cart-name" class="product-cart-input" placeholder="z. B. PV Set Premium">
                    </div>

                    <div id="ms-cart-existing-wrap" style="display:none;">
                        <div class="mt-1"></div>
                        <label class="product-cart-label">Bestehender Master Set</label>
                        <select id="ms-cart-master-set" class="product-cart-select select2-cart">
                            <option value="">Bitte zuerst Artikelgruppe wählen</option>
                        </select>
                    </div>

                    <div class="mt-1"></div>

                    <label class="product-cart-label">Beschreibung</label>
                    <textarea id="ms-cart-description" class="product-cart-textarea"
                        placeholder="Kurze Beschreibung ..."></textarea>

                    <div class="mt-1"></div>

                    <button type="button" class="product-cart-btn product-cart-btn-primary btn-block" id="ms-cart-save-btn">
                        Cart speichern / starten
                    </button>
                </div>

                <div class="product-cart-group">
                    <div class="product-cart-meta-grid">
                        <div class="product-cart-stat">
                            <div class="product-cart-stat-label">Main</div>
                            <div class="product-cart-stat-value" id="ms-cart-main-total">0,00 €</div>
                        </div>
                        <div class="product-cart-stat">
                            <div class="product-cart-stat-label">Sub</div>
                            <div class="product-cart-stat-value" id="ms-cart-sub-total-config">0,00 €</div>
                        </div>
                        <div class="product-cart-stat">
                            <div class="product-cart-stat-label">Gesamt</div>
                            <div class="product-cart-stat-value" id="ms-cart-total-config">0,00 €</div>
                        </div>
                    </div>
                </div>

                <div class="product-cart-group">
                    <div class="product-cart-row">
                        <div>
                            <label class="product-cart-label">Neue Sektion</label>
                            <input type="text" id="ms-cart-section-name" class="product-cart-input"
                                placeholder="z. B. Wechselrichter">
                        </div>
                        <div>
                            <label class="product-cart-label">Farbe</label>
                            <input type="color" id="ms-cart-section-color" class="product-cart-input" value="#93c21c"
                                style="padding:.3rem;">
                        </div>
                    </div>

                    <div class="mt-1"></div>

                    <button type="button" class="product-cart-btn product-cart-btn-soft btn-block"
                        id="ms-cart-add-section-btn">
                        Sektion hinzufügen
                    </button>
                </div>

                <div class="product-cart-group">
                    <label class="product-cart-label">Aktive Ziel-Sektion</label>
                    <select id="ms-cart-target-section" class="product-cart-select select2-cart">
                        <option value="">Bitte Sektion wählen</option>
                    </select>
                    <div class="product-cart-helper" style="margin-top:.45rem;">
                        Einzelne Produkte oder die aktuelle Mehrfachauswahl werden in diese Sektion eingefügt.
                    </div>
                </div>

                <div class="product-cart-group">
                    <button type="button" class="product-cart-btn product-cart-btn-primary btn-block"
                        id="ms-cart-convert-btn">
                        In Master Set umwandeln
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <div class="modal fade" id="productListSelectModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productListSelectModalLabel">Zu Liste hinzufügen</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Schließen">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    @csrf
                    <input type="hidden" id="list-modal-product-id">
                    <input type="hidden" id="list-modal-type">

                    <div class="form-group">
                        <label for="list-modal-select">Liste wählen</label>
                        <select id="list-modal-select" class="form-control select2-list-modal">
                            <option value="">Listen werden geladen...</option>
                        </select>
                    </div>

                    <small id="list-modal-footer-message" class="text-muted" style="font-size:.75rem;"></small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Abbrechen</button>
                    <button type="button" id="list-modal-save-btn" class="btn btn-primary">
                        <i class="feather icon-save mr-25"></i> Hinzufügen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="product-image-modal-backdrop" id="productImageModalBackdrop"></div>

    <div class="product-image-modal" id="productImageModal" aria-hidden="true">
        <div class="product-image-modal-dialog">
            <div class="product-image-modal-card">

                <div class="product-image-modal-head">
                    <div>
                        <h4 class="product-image-modal-title">Produktbild verwalten</h4>
                        <div class="product-image-modal-subtitle" id="product-image-modal-subtitle">
                            Bild hochladen oder ersetzen
                        </div>
                    </div>

                    <button type="button" class="product-image-modal-close" id="productImageModalClose">
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="product-image-modal-body">
                    <form id="product-image-form" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="product-image-product-id" name="product_id">

                        <div class="product-image-layout">
                            <div class="product-image-left">
                                <div class="product-image-preview-shell">
                                    <div class="product-image-preview-head">
                                        <span>Vorschau</span>
                                        <span class="product-image-preview-badge">Aktuelles Bild</span>
                                    </div>

                                    <div class="product-image-preview-box">
                                        <img id="product-image-preview" src="" alt="Produktbild" style="display:none;">
                                        <div class="product-image-preview-placeholder" id="product-image-placeholder">
                                            <i class="feather icon-image"></i>
                                            <span>Kein Bild vorhanden</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="product-image-right">
                                <div class="product-image-form-card">
                                    <label class="product-cart-label">Produkt</label>
                                    <div class="product-image-product-name" id="product-image-product-name">–</div>

                                    <label class="product-cart-label">Bildname</label>
                                    <input type="text" class="product-cart-input" id="product-image-name" name="name"
                                        placeholder="z. B. Hauptbild">

                                    <div style="height:12px;"></div>

                                    <label class="product-cart-label">Datei auswählen</label>

                                    <label class="product-image-upload-drop" for="product-image-input">
                                        <input type="file" id="product-image-input" name="image" accept="image/*" hidden>
                                        <div class="product-image-upload-inner">
                                            <div class="product-image-upload-icon">
                                                <i class="feather icon-upload-cloud"></i>
                                            </div>
                                            <div>
                                                <div class="product-image-upload-title">Bild hochladen</div>
                                                <div class="product-image-upload-text">JPG, PNG, WEBP, AVIF</div>
                                            </div>
                                        </div>
                                    </label>

                                    <div class="product-image-file-name" id="product-image-file-name"></div>

                                    <div class="product-image-form-actions">
                                        <button type="submit" class="btn btn-primary" id="product-image-save-btn">
                                            <i class="feather icon-save mr-25"></i> Bild speichern
                                        </button>

                                        <button type="button" class="btn btn-outline-secondary"
                                            id="productImageModalCancel">
                                            Abbrechen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="product-image-gallery-wrap">
                            <div class="product-image-gallery-head">
                                <div>
                                    <h5>Bisherige Bilder</h5>
                                    <small>Vorhandene Bilder dieses Produkts</small>
                                </div>
                            </div>

                            <div class="product-image-gallery" id="product-image-gallery">
                                <div class="text-muted small">Keine Bilder geladen.</div>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <div class="product-dist-modal-backdrop" id="productDistModalBackdrop"></div>

    <div class="product-dist-modal" id="productDistModal" aria-hidden="true">
        <div class="product-dist-modal-dialog">
            <div class="product-dist-modal-card">
                <div class="product-dist-modal-head">
                    <div>
                        <h4 class="product-dist-modal-title">Weitere Lieferanten</h4>
                        <div class="product-dist-modal-subtitle" id="productDistModalSubtitle">Produkt</div>
                    </div>

                    <button type="button" class="product-dist-modal-close" id="productDistModalClose">
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="product-dist-modal-body">
                    <div class="product-dist-modal-list" id="productDistModalList"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="product-history-backdrop" id="productHistoryBackdrop"></div>

    <div class="product-history-modal" id="productHistoryModal" aria-hidden="true">
        <div class="product-history-dialog">
            <div class="product-history-card">

                <div class="product-history-head">
                    <div>
                        <h4 class="product-history-title" id="productHistoryTitle">
                            Produkt-Historie
                        </h4>
                        <div class="product-history-subtitle" id="productHistorySubtitle">
                            Ersteller, letzte Änderung und Änderungsverlauf
                        </div>
                    </div>

                    <button type="button" class="product-history-close" id="productHistoryClose">
                        <i class="feather icon-x"></i>
                    </button>
                </div>

                <div class="product-history-body">
                    <div class="product-history-summary" id="productHistorySummary">
                        <div class="product-history-summary-card">
                            <div class="product-history-summary-label">Erstellt von</div>
                            <div class="product-history-summary-value">–</div>
                        </div>
                        <div class="product-history-summary-card">
                            <div class="product-history-summary-label">Erstellt am</div>
                            <div class="product-history-summary-value">–</div>
                        </div>
                        <div class="product-history-summary-card">
                            <div class="product-history-summary-label">Geändert von</div>
                            <div class="product-history-summary-value">–</div>
                        </div>
                        <div class="product-history-summary-card">
                            <div class="product-history-summary-label">Geändert am</div>
                            <div class="product-history-summary-value">–</div>
                        </div>
                    </div>

                    <div id="productHistoryContent">
                        <div class="product-history-loading">
                            Historie wird geladen ...
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        (function ($) {
            "use strict";

            const ROUTES = {
                list: @json(route('products.list')),
                bulk: @json(route('products.bulk')),
                dupBase: @json(url('products')),
                historyBase: @json(url('/product')),
                distributorsByBrand: @json(route('ajax.distributors.by-brand')),

                favoriteLists: @json(route('ajax.products.favorite-lists')),
                stampLists: @json(route('ajax.stamp.lists')),
                favoriteAttach: @json(url('admin/ajax/products/favorite-lists')),
                favoriteDetach: @json(url('admin/ajax/products/favorite-lists')),
                stampAttach: @json(url('admin/ajax/stamp-articles/lists')),
                stampDetach: @json(url('admin/ajax/stamp-articles/lists')),

                cartCreate: @json(route('admin.master-set-carts.store')),
                cartShowBase: @json(url('/admin/master-set-carts')),
                cartArticleGroupMasterSets: @json(route('admin.master-set-carts.article-group-master-sets')),
                cartSectionStoreBase: @json(url('/admin/master-set-carts')),
                cartItemStoreBase: @json(url('/admin/master-set-carts')),
                cartItemUpdateBase: @json(url('/admin/master-set-carts/items')),
                cartConvertBase: @json(url('/admin/master-set-carts')),

                productImageBase: @json(url('/productsList')),
                productImagesBase: @json(url('/productsList')),
                productImageDeleteBase: @json(url('/productsList/images')),
            };

            const CSRF = () => $('meta[name="csrf-token"]').attr('content') || '';

            let currentView = 'list';
            let currentListAction = 'add';
            let DIST_ALL_CACHE = null;
            let lastLoadedProductsUrl = null;
            let draggedCartItemId = null;

            const cartState = {
                cart: null,
                sections: [],
                items: []
            };

            const $el = {
                form: () => $('#product-filter-form'),
                list: () => $('#product-list'),
                pagination: () => $('#product-pagination'),
                loader: () => $('#product-list-loading'),
                total: () => $('#total-products-label'),
                noImage: () => $('#filter_no_image'),
                exportNoImage: () => $('#export-no-image-products-btn'),

                bulkAction: () => $('#bulk-action'),
                bulkApply: () => $('#bulk-apply-btn'),
                bulkCart: () => $('#bulk-add-cart-btn'),
                selectedCt: () => $('#selected-count-label'),

                modal: () => $('#productListSelectModal'),
                modalTitle: () => $('#productListSelectModalLabel'),
                modalSelect: () => $('#list-modal-select'),
                modalPid: () => $('#list-modal-product-id'),
                modalType: () => $('#list-modal-type'),
                modalBtn: () => $('#list-modal-save-btn'),
                modalMsg: () => $('#list-modal-footer-message'),

                cartDrawer: () => $('#product-cart-drawer'),
                cartBackdrop: () => $('#product-cart-backdrop'),
                cartFab: () => $('#product-cart-fab'),
                cartFabCount: () => $('#product-cart-fab-count'),
                cartClose: () => $('#product-cart-close'),

                cartId: () => $('#ms-cart-id'),
                cartArticleGroup: () => $('#ms-cart-article-group'),
                cartMode: () => $('#ms-cart-mode'),
                cartName: () => $('#ms-cart-name'),
                cartDescription: () => $('#ms-cart-description'),
                cartMasterSet: () => $('#ms-cart-master-set'),
                cartNewWrap: () => $('#ms-cart-new-wrap'),
                cartExistingWrap: () => $('#ms-cart-existing-wrap'),
                cartSectionName: () => $('#ms-cart-section-name'),
                cartSectionColor: () => $('#ms-cart-section-color'),
                cartTargetSection: () => $('#ms-cart-target-section'),
                cartSectionsWrap: () => $('#ms-cart-sections'),
                cartMainTotal: () => $('#ms-cart-main-total'),
                cartSubTotal: () => $('#ms-cart-sub-total'),
                cartTotal: () => $('#ms-cart-total'),
                cartSaveBtn: () => $('#ms-cart-save-btn'),
                cartAddSectionBtn: () => $('#ms-cart-add-section-btn'),
                cartConvertBtn: () => $('#ms-cart-convert-btn'),

                cartStepInput: () => $('#ms-cart-step'),
                cartStepProductsWrap: () => $('#ms-cart-step-products-wrap'),
                cartStepConfigWrap: () => $('#ms-cart-step-config-wrap'),
                cartStepProductsBadge: () => $('#cart-step-badge-products'),
                cartStepConfigBadge: () => $('#cart-step-badge-config'),
                cartBackBtn: () => $('#ms-cart-back-btn'),
                cartNextBtn: () => $('#ms-cart-next-btn'),
                cartCountTotal: () => $('#ms-cart-count-total'),
                cartSubTotalConfig: () => $('#ms-cart-sub-total-config'),
                cartTotalConfig: () => $('#ms-cart-total-config'),

                search: () => $('#search'),
                brand: () => $('#filter_brand'),
                group: () => $('#filter_article_group'),
                dist: () => $('#filter_distributor'),
                status: () => $('#filter_status'),
                category: () => $('#filter_category'),
                sort: () => $('#filter_sort'),
                perPage: () => $('#filter_per_page'),
                resetBtn: () => $('#filter-reset-btn')
            };

            function escapeHtml(str) {
                return String(str ?? '')
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#039;');
            }

            function toastError(msg) {
                if (window.toastr) toastr.error(msg);
                else alert(msg);
            }

            function toastSuccess(msg) {
                if (window.toastr) toastr.success(msg);
                else alert(msg);
            }

            function toastInfo(msg) {
                if (window.toastr) toastr.info(msg);
                else alert(msg);
            }

            function refreshFeather() {
                if (window.feather) feather.replace();
            }


            function getSelectedText($select, fallback = 'Alle') {
                const text = $select.find('option:selected').text();
                return text && text.trim() ? text.trim() : fallback;
            }

            function applyCurrentViewClass() {
                const $shell = $('.products-shell');

                $shell
                    .toggleClass('products-view-list', currentView === 'list')
                    .toggleClass('products-view-card', currentView === 'card');
            }

            function countActiveFilters() {
                return [
                    $el.search().val(),
                    $el.brand().val(),
                    $el.group().val(),
                    $el.dist().val(),
                    $el.status().val(),
                    $el.category().val(),
                    $el.noImage().val()
                ].filter(Boolean).length;
            }

            function updateProductFilterUI() {
                const active = countActiveFilters();

                $('#product-filter-active-count').text(active);

                const filters = [
                    { id: '#product-filter-chip-brand', label: 'Hersteller', value: $el.brand().val(), text: getSelectedText($el.brand()) },
                    { id: '#product-filter-chip-dist', label: 'Lieferant', value: $el.dist().val(), text: getSelectedText($el.dist()) },
                    { id: '#product-filter-chip-group', label: 'Gruppe', value: $el.group().val(), text: getSelectedText($el.group()) },
                    { id: '#product-filter-chip-status', label: 'Status', value: $el.status().val(), text: getSelectedText($el.status()) },
                    { id: '#product-filter-chip-category', label: 'Kategorie', value: $el.category().val(), text: getSelectedText($el.category()) },
                    { id: '#product-filter-chip-image', label: 'Bild', value: $el.noImage().val(), text: getSelectedText($el.noImage()) }
                ];

                filters.forEach(item => {
                    $(item.id)
                        .text(item.label + ': ' + item.text)
                        .toggleClass('active', !!item.value);
                });
            }

            function toggleProductFilterPanel(forceState = null) {
                const $panel = $('#product-filter-panel');
                const $btn = $('#product-filter-toggle');
                const shouldOpen = forceState === null ? !$panel.hasClass('show') : !!forceState;

                $panel.toggleClass('show', shouldOpen);
                $btn.toggleClass('active', shouldOpen).attr('aria-expanded', shouldOpen ? 'true' : 'false');

                if (shouldOpen) {
                    setTimeout(() => $('.select2').trigger('change.select2'), 50);
                }
            }

            function moneyFormat(value) {
                const n = Number(value || 0);

                return n.toLocaleString('de-DE', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' €';
            }

            function maybeImageUrl(path) {
                if (!path) return '';

                const raw = String(path).trim();

                if (!raw) return '';
                if (/^https?:\/\//i.test(raw)) return raw;
                if (raw.startsWith('/')) return raw;

                return '/images/products/' + raw;
            }

            function normalizeJsonArray(value) {
                if (Array.isArray(value)) return value;

                try {
                    const parsed = JSON.parse(value || '[]');
                    return Array.isArray(parsed) ? parsed : [];
                } catch (e) {
                    return [];
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Distributor modal
            |--------------------------------------------------------------------------
            */

            function openDistributorModal(productName, distributors) {
                const list = Array.isArray(distributors) ? distributors : [];
                const $list = $('#productDistModalList');
                const $subtitle = $('#productDistModalSubtitle');

                $subtitle.text(productName || 'Produkt');

                if (!list.length) {
                    $list.html('<div class="text-muted small">Keine Lieferanten vorhanden.</div>');
                } else {
                    let html = '';

                    list.forEach(function (dist) {
                        const badgeHtml =
                            dist.price_badge === 'Günstigster'
                                ? '<span class="product-modern-distributor-badge cheapest">Günstigster</span>'
                                : (
                                    dist.price_badge === 'Teuerster'
                                        ? '<span class="product-modern-distributor-badge expensive">Teuerster</span>'
                                        : ''
                                );

                        const priceHtml = dist.display_price !== null && dist.display_price !== undefined
                            ? Number(dist.display_price).toLocaleString('de-DE', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) + ' €'
                            : '–';

                        const metaParts = [];

                        if (dist.article_no) metaParts.push('Art.-Nr.: ' + escapeHtml(dist.article_no));
                        if (dist.availability) metaParts.push(escapeHtml(dist.availability));

                        html += `
                            <div class="product-modern-distributor-item">
                                <div class="product-modern-distributor-top">
                                    <i class="fa fa-truck"></i>
                                    <div class="product-modern-distributor-name">${escapeHtml(dist.distributor_name || dist.name || '')}</div>
                                </div>

                                <div class="product-modern-distributor-price">
                                    <small>${escapeHtml(dist.display_price_label || 'Preis')}:</small>
                                    <span>${priceHtml}</span>
                                    ${badgeHtml}
                                </div>

                                ${metaParts.length ? `<div class="product-modern-distributor-meta">${metaParts.join(' &nbsp;·&nbsp; ')}</div>` : ''}
                            </div>
                        `;
                    });

                    $list.html(html);
                }

                $('#productDistModalBackdrop').addClass('show');
                $('#productDistModal').addClass('show').attr('aria-hidden', 'false');
                $('body').addClass('product-dist-modal-open');

                refreshFeather();
            }

            function closeDistributorModal() {
                $('#productDistModalBackdrop').removeClass('show');
                $('#productDistModal').removeClass('show').attr('aria-hidden', 'true');
                $('body').removeClass('product-dist-modal-open');
            }

            /*
            |--------------------------------------------------------------------------
            | Lieferant dropdown details
            |--------------------------------------------------------------------------
            */

            function renderSupplierDetail(supplier) {
                const row = supplier || {};

                const name = row.name || row.distributor_name || 'Lieferant';
                const ekPrice = row.ek_price_formatted || row.display_price_formatted || '–';
                const articleNo = row.article_no || '–';
                const availability = row.availability || '–';

                return `
                    <div class="product-supplier-detail-name">
                        <i class="fa fa-truck"></i>
                        <span>${escapeHtml(name)}</span>
                    </div>

                    <div class="product-supplier-detail-grid">
                        <div class="product-supplier-detail-line">
                            <span class="product-supplier-detail-label">EK Preis</span>
                            <span class="product-supplier-detail-value">${escapeHtml(ekPrice)}</span>
                        </div>

                        <div class="product-supplier-detail-line">
                            <span class="product-supplier-detail-label">Art.-Nr.</span>
                            <span class="product-supplier-detail-value">${escapeHtml(articleNo)}</span>
                        </div>

                        <div class="product-supplier-detail-line">
                            <span class="product-supplier-detail-label">Verfügbarkeit</span>
                            <span class="product-supplier-detail-value">${escapeHtml(availability)}</span>
                        </div>
                    </div>
                `;
            }

            /*
            |--------------------------------------------------------------------------
            | Product history modal
            |--------------------------------------------------------------------------
            */

            function openProductHistoryModal() {
                $('#productHistoryBackdrop').addClass('show');
                $('#productHistoryModal').addClass('show').attr('aria-hidden', 'false');
                $('body').addClass('product-history-modal-open');

                refreshFeather();
            }

            function closeProductHistoryModal() {
                $('#productHistoryBackdrop').removeClass('show');
                $('#productHistoryModal').removeClass('show').attr('aria-hidden', 'true');
                $('body').removeClass('product-history-modal-open');
            }

            function productHistoryActionLabel(action) {
                const labels = {
                    created: 'Erstellt',
                    updated: 'Geändert',
                    deleted: 'Gelöscht',
                    restored: 'Wiederhergestellt'
                };

                return labels[action] || 'Geändert';
            }

            function productHistoryIcon(action) {
                if (action === 'created') return 'icon-plus-circle';
                if (action === 'deleted') return 'icon-trash-2';
                if (action === 'restored') return 'icon-rotate-ccw';

                return 'icon-edit-3';
            }

            function productHistoryValue(value) {
                if (value === null || value === undefined || value === '') return '–';

                if (typeof value === 'object') {
                    try {
                        return escapeHtml(JSON.stringify(value, null, 2));
                    } catch (e) {
                        return '–';
                    }
                }

                return escapeHtml(String(value));
            }

            function renderProductHistorySummary(product) {
                const html = `
                    <div class="product-history-summary-card">
                        <div class="product-history-summary-label">Erstellt von</div>
                        <div class="product-history-summary-value">${escapeHtml(product.created_by || '–')}</div>
                    </div>

                    <div class="product-history-summary-card">
                        <div class="product-history-summary-label">Erstellt am</div>
                        <div class="product-history-summary-value">${escapeHtml(product.created_at || '–')}</div>
                    </div>

                    <div class="product-history-summary-card">
                        <div class="product-history-summary-label">Geändert von</div>
                        <div class="product-history-summary-value">${escapeHtml(product.updated_by || '–')}</div>
                    </div>

                    <div class="product-history-summary-card">
                        <div class="product-history-summary-label">Geändert am</div>
                        <div class="product-history-summary-value">${escapeHtml(product.updated_at || '–')}</div>
                    </div>
                `;

                $('#productHistorySummary').html(html);
            }

            function renderProductHistoryItems(histories) {
                if (!Array.isArray(histories) || !histories.length) {
                    return `
                    <div class="product-history-empty">
                        Noch keine Historie für dieses Produkt vorhanden.
                    </div>
                `;
                }

                return `
                <div class="product-history-timeline">
                    ${histories.map(function (row) {
                    const action = row.action || 'updated';
                    const actionLabel = row.action_label || productHistoryActionLabel(action);
                    const changes = Array.isArray(row.changes) ? row.changes : [];

                    const fieldChips = changes.length
                        ? `
                                <div class="product-history-fields">
                                    ${changes.map(change => `
                                        <span class="product-history-field-chip">
                                            ${escapeHtml(change.label || 'Feld')}
                                        </span>
                                    `).join('')}
                                </div>
                            `
                        : '';

                    const changesHtml = changes.slice(0, 30).map(function (change) {
                        return `
                                <div class="product-history-change-row">
                                    <div class="product-history-change-field">
                                        ${escapeHtml(change.label || 'Feld')}
                                    </div>

                                    <div class="product-history-change-old">
                                        <span class="product-history-change-caption">Vorher</span>
                                        <div>${escapeHtml(change.old_value || '–')}</div>
                                    </div>

                                    <div class="product-history-change-new">
                                        <span class="product-history-change-caption">Nachher</span>
                                        <div>${escapeHtml(change.new_value || '–')}</div>
                                    </div>
                                </div>
                            `;
                    }).join('');

                    return `
                            <div class="product-history-item">
                                <div class="product-history-icon ${escapeHtml(action)}">
                                    <i class="feather ${productHistoryIcon(action)}"></i>
                                </div>

                                <div>
                                    <div class="product-history-item-top">
                                        <div>
                                            <div class="product-history-action">
                                                ${escapeHtml(actionLabel)}
                                            </div>

                                            <div class="product-history-meta">
                                                Geändert von: ${escapeHtml(row.changed_by_name || 'System')}
                                            </div>
                                        </div>

                                        <div class="product-history-date">
                                            <i class="feather icon-calendar"></i>
                                            ${escapeHtml(row.created_at || '–')}
                                        </div>
                                    </div>

                                    ${fieldChips}

                                    ${changesHtml ? `
                                        <div class="product-history-changes">
                                            <div class="product-history-change-head">
                                                <div>Feld</div>
                                                <div>Vorher</div>
                                                <div>Nachher</div>
                                            </div>

                                            ${changesHtml}
                                        </div>
                                    ` : `
                                        <div class="product-history-empty">
                                            Keine einzelnen Feldänderungen gespeichert.
                                        </div>
                                    `}
                                </div>
                            </div>
                        `;
                }).join('')}
                </div>
            `;
            }

            function loadProductHistory(productId, productName) {
                if (!productId) {
                    toastError('Produkt-ID fehlt.');
                    return;
                }

                $('#productHistoryTitle').text('Produkt-Historie');
                $('#productHistorySubtitle').text(productName || 'Historie wird geladen ...');

                $('#productHistorySummary').html(`
                    <div class="product-history-summary-card">
                        <div class="product-history-summary-label">Erstellt von</div>
                        <div class="product-history-summary-value">–</div>
                    </div>
                    <div class="product-history-summary-card">
                        <div class="product-history-summary-label">Erstellt am</div>
                        <div class="product-history-summary-value">–</div>
                    </div>
                    <div class="product-history-summary-card">
                        <div class="product-history-summary-label">Geändert von</div>
                        <div class="product-history-summary-value">–</div>
                    </div>
                    <div class="product-history-summary-card">
                        <div class="product-history-summary-label">Geändert am</div>
                        <div class="product-history-summary-value">–</div>
                    </div>
                `);

                $('#productHistoryContent').html(`
                    <div class="product-history-loading">
                        Historie wird geladen ...
                    </div>
                `);

                openProductHistoryModal();

                $.ajax({
                    url: ROUTES.historyBase + '/' + productId + '/history',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function (res) {
                        if (!res.success) {
                            $('#productHistoryContent').html(`
                                <div class="product-history-empty">
                                    Historie konnte nicht geladen werden.
                                </div>
                            `);
                            return;
                        }

                        const product = res.product || {};

                        $('#productHistoryTitle').text('Historie: ' + (product.name || productName || 'Produkt'));
                        $('#productHistorySubtitle').text(
                            'Art.Nr.: ' + (product.article_no || '–') + ' · Produkt-ID: #' + (product.id || productId)
                        );

                        renderProductHistorySummary(product);
                        $('#productHistoryContent').html(renderProductHistoryItems(res.histories || []));

                        refreshFeather();
                    },
                    error: function (xhr) {
                        $('#productHistoryContent').html(`
                            <div class="product-history-empty">
                                Historie konnte nicht geladen werden.<br>
                                ${escapeHtml(xhr.responseJSON?.message || 'Bitte Route /product/{id}/history und Tabelle product_histories prüfen.')}
                            </div>
                        `);
                    }
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Product image modal
            |--------------------------------------------------------------------------
            */

            function openProductImageModal() {
                $('#productImageModalBackdrop').addClass('show');
                $('#productImageModal').addClass('show').attr('aria-hidden', 'false');
                $('body').addClass('product-image-modal-open');

                refreshFeather();
            }

            function closeProductImageModal() {
                $('#productImageModalBackdrop').removeClass('show');
                $('#productImageModal').removeClass('show').attr('aria-hidden', 'true');
                $('body').removeClass('product-image-modal-open');
            }

            function setProductImagePreview(src) {
                const $img = $('#product-image-preview');
                const $placeholder = $('#product-image-placeholder');

                if (src) {
                    $img.attr('src', src).show();
                    $placeholder.hide();
                } else {
                    $img.attr('src', '').hide();
                    $placeholder.show();
                }
            }

            function renderProductImageGallery(images) {
                const $wrap = $('#product-image-gallery');

                if (!Array.isArray(images) || !images.length) {
                    $wrap.html('<div class="text-muted small">Noch keine weiteren Bilder vorhanden.</div>');
                    return;
                }

                let html = '';

                images.forEach(function (img) {
                    html += `
                        <div class="product-image-gallery-item">
                            <div class="product-image-gallery-thumb">
                                <img src="${escapeHtml(img.url)}" alt="${escapeHtml(img.name || 'Bild')}">
                            </div>

                            <div class="product-image-gallery-body">
                                <div class="product-image-gallery-title">${escapeHtml(img.name || 'Produktbild')}</div>

                                <div class="product-image-gallery-actions">
                                    <button type="button"
                                            class="product-image-gallery-btn js-use-gallery-image"
                                            data-url="${escapeHtml(img.url)}">
                                        Vorschau
                                    </button>

                                    <button type="button"
                                            class="product-image-gallery-btn delete js-delete-gallery-image"
                                            data-image-id="${escapeHtml(img.id)}">
                                        Löschen
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                $wrap.html(html);
            }

            function loadProductImages(productId, currentImageUrl, productName) {
                $('#product-image-product-id').val(productId);
                $('#product-image-product-name').text(productName || '–');
                $('#product-image-name').val(productName || '');
                $('#product-image-file-name').text('');
                $('#product-image-input').val('');
                $('#product-image-modal-subtitle').text('Bild von: ' + (productName || 'Produkt'));

                setProductImagePreview(currentImageUrl || '');
                renderProductImageGallery([]);

                $.ajax({
                    url: ROUTES.productImagesBase + '/' + productId + '/images',
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if (res.product) {
                            $('#product-image-modal-subtitle').text('Bild von: ' + (res.product.name || productName || 'Produkt'));
                            setProductImagePreview(res.product.image_url || currentImageUrl || '');
                        }

                        renderProductImageGallery(res.images || []);
                        openProductImageModal();
                    },
                    error: function () {
                        openProductImageModal();
                        toastError('Produktbilder konnten nicht geladen werden.');
                    }
                });
            }

            function saveProductImage() {
                const productId = $('#product-image-product-id').val();
                const input = document.getElementById('product-image-input');
                const file = input?.files?.[0] || null;

                if (!productId) {
                    toastError('Produkt-ID fehlt.');
                    return;
                }

                if (!file) {
                    toastInfo('Bitte zuerst ein Bild auswählen.');
                    return;
                }

                if (file.size > (2 * 1024 * 1024)) {
                    const msg = 'Das Bild ist zu groß. Bitte wählen Sie ein Bild unter 2 MB.';

                    if (window.Swal) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Datei zu groß',
                            text: msg
                        });
                    } else {
                        toastError(msg);
                    }

                    return;
                }

                const formData = new FormData();

                formData.append('_token', CSRF());
                formData.append('image', file);
                formData.append('name', $('#product-image-name').val() || '');

                const $btn = $('#product-image-save-btn');
                const oldHtml = $btn.html();

                $btn.prop('disabled', true).html('<i class="feather icon-loader mr-25"></i> Speichert ...');
                refreshFeather();

                $.ajax({
                    url: ROUTES.productImageBase + '/' + productId + '/image',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function (res) {
                        if (!res.success) {
                            toastError(res.message || 'Bild konnte nicht gespeichert werden.');
                            return;
                        }

                        toastSuccess(res.message || 'Bild erfolgreich gespeichert.');
                        closeProductImageModal();
                        loadProducts(lastLoadedProductsUrl || null, productId);
                    },
                    error: function (xhr) {
                        let msg = 'Bild konnte nicht gespeichert werden.';

                        if (xhr.responseJSON?.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON?.message) {
                            msg = xhr.responseJSON.message;
                        }

                        if (window.Swal) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Upload fehlgeschlagen',
                                html: msg
                            });
                        } else {
                            toastError(msg);
                        }
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html(oldHtml);
                        refreshFeather();
                    }
                });
            }

            function deleteGalleryImage(imageId) {
                if (!imageId) return;

                Swal.fire({
                    title: 'Bild löschen?',
                    text: 'Dieses Bild wird aus der Galerie entfernt.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ja, löschen',
                    cancelButtonText: 'Abbrechen'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: ROUTES.productImageDeleteBase + '/' + imageId,
                        type: 'DELETE',
                        dataType: 'json',
                        data: { _token: CSRF() },
                        success: function (res) {
                            toastSuccess(res.message || 'Bild gelöscht.');

                            const productId = $('#product-image-product-id').val();
                            const currentPreview = $('#product-image-preview').attr('src') || '';
                            const productName = $('#product-image-product-name').text() || '';

                            loadProductImages(productId, currentPreview, productName);
                        },
                        error: function (xhr) {
                            toastError(xhr.responseJSON?.message || 'Bild konnte nicht gelöscht werden.');
                        }
                    });
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Filters / loading products
            |--------------------------------------------------------------------------
            */

            function cacheAllDistributorsOnce() {
                if (DIST_ALL_CACHE) return;

                const $dist = $el.dist();

                DIST_ALL_CACHE = $dist.find('option').toArray().slice(1).map(opt => ({
                    id: String(opt.value || ''),
                    name: (opt.textContent || '').trim()
                })).filter(x => x.id !== '');
            }

            function setDistributorOptions(items, selectedId = '') {
                const $dist = $el.dist();
                const keep = selectedId ? String(selectedId) : String($dist.val() || '');

                $dist.empty().append(new Option('Alle', '', false, false));

                (items || []).forEach(row => {
                    $dist.append(new Option(row.name, String(row.id), false, false));
                });

                if (keep && $dist.find(`option[value="${CSS.escape(keep)}"]`).length) {
                    $dist.val(keep);
                } else {
                    $dist.val('');
                }

                $dist.trigger('change');
            }

            function reloadDistributorsForBrand(brandId, done) {
                const $dist = $el.dist();

                cacheAllDistributorsOnce();

                if (!brandId) {
                    setDistributorOptions(DIST_ALL_CACHE, '');

                    if (typeof done === 'function') done();

                    return;
                }

                $dist.prop('disabled', true);
                $dist.empty().append(new Option('Lade Lieferanten...', '', false, false)).trigger('change');

                $.ajax({
                    url: ROUTES.distributorsByBrand,
                    type: 'GET',
                    dataType: 'json',
                    data: { brand_id: brandId },
                    success: function (res) {
                        setDistributorOptions(res.items || [], '');
                    },
                    error: function (xhr) {
                        setDistributorOptions(DIST_ALL_CACHE, '');
                        toastError('Lieferanten konnten nicht geladen werden.');
                        console.log('distributorsByBrand failed', xhr.status, xhr.responseText);
                    },
                    complete: function () {
                        $dist.prop('disabled', false);

                        if (typeof done === 'function') done();
                    }
                });
            }

            function buildQueryData() {
                const sortVal = $el.sort().val() || 'created_at|desc';
                const [sort_by = 'created_at', sort_dir = 'desc'] = String(sortVal).split('|');

                return {
                    search: $el.search().val() || '',
                    brand_id: $el.brand().val() || '',
                    article_group_id: $el.group().val() || '',
                    distributor_id: $el.dist().val() || '',
                    status: $el.status().val() || '',
                    category: $el.category().val() || '',
                    no_image: $el.noImage().val() || '',
                    per_page: $el.perPage().val() || 12,
                    sort_by: sort_by,
                    sort_dir: sort_dir,
                    view_type: currentView
                };
            }

            function updateNoImageExportUrl() {
                const data = buildQueryData();

                data.no_image = 1;

                const baseUrl = @json(route('products.export.no-images'));

                $el.exportNoImage().attr('href', baseUrl + '?' + $.param(data));
            }

            function resetSelection() {
                $el.selectedCt().text('0');
                $el.bulkAction().val('');
                $el.bulkApply().prop('disabled', true);
                $el.bulkCart().prop('disabled', true);

                $('.product-select').prop('checked', false);
                $('.product-modern-item, tr, .product-card').removeClass('is-selected');

                const $selectAll = $('#select-all-page');

                if ($selectAll.length) {
                    $selectAll.prop('checked', false).prop('indeterminate', false);
                }
            }

            function loadProducts(pageUrl = null, highlightId = null) {
                resetSelection();

                const requestUrl = pageUrl || ROUTES.list;
                const requestData = pageUrl ? undefined : buildQueryData();

                updateNoImageExportUrl();

                if (pageUrl) {
                    lastLoadedProductsUrl = pageUrl;
                } else {
                    lastLoadedProductsUrl = ROUTES.list + '?' + $.param(requestData || {});
                }

                $.ajax({
                    url: requestUrl,
                    type: 'GET',
                    dataType: 'json',
                    data: requestData,
                    beforeSend: () => $el.loader().show(),
                    complete: () => $el.loader().hide(),
                    success: (res) => {
                        $el.list().html(res.html || '');
                        $el.pagination().html(res.pagination || '');
                        $el.total().text(res.total || 0);

                        applyCurrentViewClass();
                        updateProductFilterUI();

                        if (highlightId) {
                            setTimeout(() => highlightUpdatedProduct(highlightId), 80);
                        }

                        refreshFeather();
                    },
                    error: () => toastError('Fehler beim Laden der Produkte.')
                });
            }

            function highlightDuplicatedProduct(productId) {
                if (!productId) return;

                const $target = $('[data-product-id="' + productId + '"]').closest('tr, .product-card, .product-modern-item');

                if (!$target.length) return;

                if ($target[0]?.scrollIntoView) {
                    $target[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }

                $target.addClass('product-duplicated-flash');
                $target.find('.product-duplicated-badge').remove();

                const $slot = $target.find('.product-card-title, .product-list-name, .product-modern-title').first();

                if ($slot.length) {
                    const $badge = $('<span class="product-duplicated-badge">Clone</span>');

                    $slot.append($badge);

                    setTimeout(() => $badge.fadeOut(200, function () {
                        $(this).remove();
                    }), 2200);
                }

                setTimeout(() => $target.removeClass('product-duplicated-flash'), 2200);
            }

            function highlightUpdatedProduct(productId) {
                if (!productId) return;

                const $target = $('[data-product-id="' + productId + '"]').closest('tr, .product-card, .product-modern-item');

                if (!$target.length) return;

                if ($target[0]?.scrollIntoView) {
                    $target[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                }

                $target.addClass('product-updated-flash');
                $target.find('.product-updated-badge').remove();

                const $slot = $target.find('.product-card-title, .product-list-name, .product-modern-title').first();

                if ($slot.length) {
                    const $badge = $('<span class="product-updated-badge">Updated</span>');

                    $slot.append($badge);

                    setTimeout(() => $badge.fadeOut(200, function () {
                        $(this).remove();
                    }), 2200);
                }

                setTimeout(() => $target.removeClass('product-updated-flash'), 2200);
            }

            /*
            |--------------------------------------------------------------------------
            | Bulk actions
            |--------------------------------------------------------------------------
            */

            function getSelectedIds() {
                return $('.product-select:checked').map(function () {
                    return $(this).val();
                }).get();
            }

            function updateBulkState() {
                const ids = getSelectedIds();
                const count = ids.length;

                $el.selectedCt().text(count);

                const hasAction = ($el.bulkAction().val() || '') !== '';

                $el.bulkApply().prop('disabled', !(count > 0 && hasAction));
                $el.bulkCart().prop('disabled', !(count > 0));

                $('.product-select').each(function () {
                    const $checkbox = $(this);
                    const $row = $checkbox.closest('.product-modern-item, tr, .product-card');

                    $row.toggleClass('is-selected', $checkbox.is(':checked'));
                });

                const $selectAll = $('#select-all-page');

                if ($selectAll.length) {
                    const $cbs = $('.product-select');
                    const total = $cbs.length;
                    const checked = $cbs.filter(':checked').length;

                    $selectAll.prop('checked', total > 0 && total === checked);
                    $selectAll.prop('indeterminate', checked > 0 && checked < total);
                }
            }

            function applyBulkAction() {
                const action = $el.bulkAction().val();
                const ids = getSelectedIds();

                if (!action) return toastInfo('Bitte zuerst eine Aktion wählen.');
                if (!ids.length) return toastInfo('Keine Produkte ausgewählt.');

                const map = {
                    publish: {
                        title: 'Produkte veröffentlichen?',
                        text: 'Ausgewählte Produkte werden veröffentlicht.',
                        icon: 'question'
                    },
                    unpublish: {
                        title: 'Produkte deaktivieren?',
                        text: 'Ausgewählte Produkte werden deaktiviert.',
                        icon: 'question'
                    },
                    delete: {
                        title: 'Produkte löschen?',
                        text: 'Ausgewählte Produkte werden dauerhaft gelöscht.',
                        icon: 'warning'
                    }
                };

                const cfg = map[action];

                if (!cfg) return toastError('Ungültige Aktion.');

                Swal.fire({
                    title: cfg.title,
                    text: cfg.text,
                    icon: cfg.icon,
                    showCancelButton: true,
                    confirmButtonText: 'Ja, ausführen',
                    cancelButtonText: 'Abbrechen'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: ROUTES.bulk,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            _token: CSRF(),
                            action,
                            ids
                        },
                        success: (res) => {
                            if (res.status === 'success') {
                                toastSuccess(res.message || 'Aktion ausgeführt.');
                                loadProducts(lastLoadedProductsUrl || null);
                            } else {
                                toastError(res.message || 'Aktion fehlgeschlagen.');
                            }
                        },
                        error: (xhr) => {
                            toastError(xhr.responseJSON?.message || 'Aktion fehlgeschlagen.');
                        }
                    });
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Favorite / stamp lists
            |--------------------------------------------------------------------------
            */

            function configureModalHeader(listType, mode) {
                const isFav = listType === 'favorite';

                const cfg = mode === 'remove'
                    ? {
                        title: isFav ? 'Aus Favoriten-Liste entfernen' : 'Aus Stempel-Liste entfernen',
                        btnHtml: '<i class="feather icon-trash-2 mr-25"></i> Entfernen',
                        btnClass: 'btn btn-danger'
                    }
                    : {
                        title: isFav ? 'Zu Favoriten-Liste hinzufügen' : 'Zu Stempel-Liste hinzufügen',
                        btnHtml: '<i class="feather icon-save mr-25"></i> Hinzufügen',
                        btnClass: 'btn btn-primary'
                    };

                $el.modalTitle().text(cfg.title);
                $el.modalBtn().html(cfg.btnHtml).attr('class', cfg.btnClass);

                refreshFeather();
            }

            function loadListsForType(listType, cb) {
                const url = listType === 'favorite' ? ROUTES.favoriteLists : ROUTES.stampLists;
                const $select = $el.modalSelect();

                $select.empty().append('<option value="">Listen werden geladen...</option>');
                $el.modalMsg().text('');

                $.ajax({
                    url,
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        as: 'select',
                        product_id: $el.modalPid().val()
                    },
                    success: (res) => {
                        const lists = res.lists || [];

                        $select.empty();

                        if (!lists.length) {
                            $select.append('<option value="">Keine Listen vorhanden</option>');
                            $el.modalMsg().text('Es sind noch keine Listen vorhanden. Bitte legen Sie zuerst eine Liste im entsprechenden Modul an.');
                        } else {
                            $select.append('<option value="">Bitte Liste wählen...</option>');

                            lists.forEach((row) => {
                                const label = row.is_attached ? (row.name + ' (enthält Produkt)') : row.name;

                                $select.append(
                                    $('<option>', {
                                        value: row.id,
                                        text: label
                                    }).attr('data-attached', row.is_attached ? 1 : 0)
                                );
                            });
                        }

                        $select.trigger('change');

                        if (typeof cb === 'function') cb();
                    },
                    error: () => {
                        toastError('Listen konnten nicht geladen werden.');
                        $select.empty().append('<option value="">Fehler beim Laden</option>').trigger('change');
                    }
                });
            }

            function openListModal(productId, listType, mode) {
                currentListAction = mode === 'remove' ? 'remove' : 'add';

                $el.modalPid().val(productId);
                $el.modalType().val(listType);

                configureModalHeader(listType, currentListAction);

                loadListsForType(listType, function () {
                    $el.modal().modal('show');
                });
            }

            function addProductToList() {
                const productId = $el.modalPid().val();
                const listType = $el.modalType().val();
                const listId = $el.modalSelect().val();

                if (!listId) return toastInfo('Bitte zuerst eine Liste wählen.');

                let url;
                let payload;

                if (listType === 'favorite') {
                    url = ROUTES.favoriteAttach + '/' + listId + '/products';
                    payload = {
                        _token: CSRF(),
                        product_id: productId
                    };
                } else {
                    url = ROUTES.stampAttach + '/' + listId + '/attach';
                    payload = {
                        _token: CSRF(),
                        stamp_article_id: productId
                    };
                }

                $.ajax({
                    url,
                    type: 'POST',
                    dataType: 'json',
                    data: payload,
                    success: (res) => {
                        $el.modal().modal('hide');
                        toastSuccess(res.message || 'Produkt zur Liste hinzugefügt.');
                        loadProducts(lastLoadedProductsUrl || null, productId);
                    },
                    error: (xhr) => {
                        if (xhr.status === 409) toastInfo('Dieses Produkt ist bereits in dieser Liste.');
                        else toastError('Produkt konnte nicht hinzugefügt werden.');
                    }
                });
            }

            function removeProductFromList() {
                const productId = $el.modalPid().val();
                const listType = $el.modalType().val();
                const listId = $el.modalSelect().val();

                if (!listId) return toastInfo('Bitte zuerst eine Liste wählen.');

                const url = listType === 'favorite'
                    ? ROUTES.favoriteDetach + '/' + listId + '/products/' + productId
                    : ROUTES.stampDetach + '/' + listId + '/detach-by-product/' + productId;

                $.ajax({
                    url,
                    type: 'DELETE',
                    dataType: 'json',
                    data: { _token: CSRF() },
                    success: (res) => {
                        $el.modal().modal('hide');
                        toastSuccess(res.message || 'Produkt aus der Liste entfernt.');
                        loadProducts(lastLoadedProductsUrl || null, productId);
                    },
                    error: (xhr) => {
                        if (xhr.status === 404) toastInfo('Dieses Produkt ist nicht in dieser Liste.');
                        else toastError('Produkt konnte nicht entfernt werden.');
                    }
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Duplicate
            |--------------------------------------------------------------------------
            */

            function duplicateProduct(productId) {
                if (!productId) return;

                Swal.fire({
                    title: 'Produkt duplizieren?',
                    text: 'Es wird ein neues Produkt mit denselben Daten angelegt.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ja, duplizieren',
                    cancelButtonText: 'Abbrechen'
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    $.ajax({
                        url: ROUTES.dupBase + '/' + productId + '/duplicate',
                        type: 'POST',
                        dataType: 'json',
                        data: { _token: CSRF() },
                        success: (res) => {
                            if (res?.success) {
                                toastSuccess(res.message || 'Produkt dupliziert.');

                                const newId = res.new_id || res.product_id || res.product?.id || null;

                                loadProducts(lastLoadedProductsUrl || null, newId || productId);
                            } else {
                                toastError(res?.message || 'Duplizieren fehlgeschlagen.');
                            }
                        },
                        error: (xhr) => {
                            toastError(xhr.responseJSON?.message || 'Duplizieren fehlgeschlagen.');
                        }
                    });
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Cart
            |--------------------------------------------------------------------------
            */

            function openCartDrawer() {
                $el.cartBackdrop().addClass('show');
                $el.cartDrawer().addClass('show');
                $('body').css('overflow', 'hidden');
            }

            function closeCartDrawer() {
                $el.cartBackdrop().removeClass('show');
                $el.cartDrawer().removeClass('show');
                $('body').css('overflow', '');
            }

            function updateCartFabCount() {
                const total = Array.isArray(cartState.items) ? cartState.items.length : 0;

                $el.cartFabCount().text(total);
            }

            function getCartStep() {
                return $el.cartStepInput().val() || 'products';
            }

            function setCartStep(step) {
                if ($el.cartStepInput().length) {
                    $el.cartStepInput().val(step);
                }

                const isProducts = step === 'products';

                $el.cartStepProductsWrap().toggle(isProducts);
                $el.cartStepConfigWrap().toggle(!isProducts);
                $el.cartNextBtn().toggle(isProducts);
                $el.cartBackBtn().toggle(!isProducts);

                $el.cartStepProductsBadge()
                    .toggleClass('badge-success', isProducts)
                    .toggleClass('badge-light', !isProducts);

                $el.cartStepConfigBadge()
                    .toggleClass('badge-success', !isProducts)
                    .toggleClass('badge-light', isProducts);

                refreshFeather();
            }

            function goToConfigStep() {
                if (!Array.isArray(cartState.items) || !cartState.items.length) {
                    toastInfo('Bitte zuerst Produkte in den Cart einfügen.');
                    return;
                }

                setCartStep('config');
            }

            function goToProductsStep() {
                setCartStep('products');
            }

            function cartModeToggle() {
                const mode = $el.cartMode().val() || 'new';

                $el.cartNewWrap().toggle(mode === 'new');
                $el.cartExistingWrap().toggle(mode === 'existing');
            }

            function cartChildrenOf(parentId) {
                return (cartState.items || [])
                    .filter(item => Number(item.parent_id || 0) === Number(parentId))
                    .sort((a, b) => {
                        return (Number(a.sort_order || 0) - Number(b.sort_order || 0)) || (Number(a.id) - Number(b.id));
                    });
            }

            function cartRootsOf(sectionId) {
                return (cartState.items || [])
                    .filter(item => Number(item.section_id || 0) === Number(sectionId) && !item.parent_id)
                    .sort((a, b) => {
                        return (Number(a.sort_order || 0) - Number(b.sort_order || 0)) || (Number(a.id) - Number(b.id));
                    });
            }

            function renderCartSummary() {
                const count = Array.isArray(cartState.items) ? cartState.items.length : 0;

                $el.cartCountTotal().text(count);
                $el.cartMainTotal().text(moneyFormat(cartState.cart?.main_total || 0));
                $el.cartSubTotal().text(moneyFormat(cartState.cart?.sub_total || 0));
                $el.cartTotal().text(moneyFormat(cartState.cart?.total || 0));
                $el.cartSubTotalConfig().text(moneyFormat(cartState.cart?.sub_total || 0));
                $el.cartTotalConfig().text(moneyFormat(cartState.cart?.total || 0));

                updateCartFabCount();
            }

            function renderCartTargetSections() {
                const $target = $el.cartTargetSection();

                if (!$target.length) return;

                const currentVal = $target.val();

                $target.empty().append('<option value="">Bitte Sektion wählen</option>');

                (cartState.sections || []).forEach(section => {
                    $target.append(new Option(section.name, section.id, false, String(currentVal) === String(section.id)));
                });

                $target.trigger('change.select2');
            }

            function normalizeCartItem(raw) {
                const item = raw || {};
                const product = item.product || {};

                const imageUrl =
                    item.product_image_url ||
                    item.image_url ||
                    item.image ||
                    maybeImageUrl(product.product_image) ||
                    maybeImageUrl(product.image) ||
                    '';

                return {
                    ...item,
                    title: item.title || product.product || product.name || 'Ohne Titel',
                    article_no: item.article_no || product.article_no || '',
                    description: item.description || item.short_description || product.short_description || product.description || '',
                    unit_price: Number(item.unit_price ?? product.unit_price ?? product.price ?? product.purchase_price ?? 0),
                    qty: Number(item.qty ?? 1),
                    product_image_url: imageUrl
                };
            }

            function renderCartItemNode(item, isSub = false) {
                const lineTotal = Number(item.qty || 0) * Number(item.unit_price || 0);
                const children = cartChildrenOf(item.id);

                return `
                    <div class="product-cart-item ${isSub ? 'sub' : ''}"
                         data-cart-item-id="${escapeHtml(item.id)}"
                         data-parent-id="${escapeHtml(item.parent_id || '')}"
                         data-section-id="${escapeHtml(item.section_id || '')}"
                         draggable="true">

                        <div class="product-cart-item-row">
                            <div class="product-cart-item-left w-100">
                                <div class="product-cart-product">
                                    <div class="product-cart-product-media">
                                        ${item.product_image_url
                        ? `<img src="${escapeHtml(item.product_image_url)}" alt="${escapeHtml(item.title)}">`
                        : `<div class="product-cart-product-placeholder"><i class="feather icon-image"></i></div>`
                    }
                                    </div>

                                    <div class="product-cart-product-content">
                                        <div class="product-cart-item-title">
                                            ${escapeHtml(item.title)}
                                            <span class="product-cart-pill">${item.parent_id ? 'Sub' : 'Main'}</span>
                                        </div>

                                        <div class="product-cart-item-meta">
                                            Art.-Nr.: ${escapeHtml(item.article_no || '–')}
                                            ${item.distributor?.name ? `&nbsp;·&nbsp; Lieferant: ${escapeHtml(item.distributor.name)}` : ''}
                                            ${item.availability ? `&nbsp;·&nbsp; ${escapeHtml(item.availability)}` : ''}
                                        </div>

                                        ${item.description ? `<div class="product-cart-item-desc">${escapeHtml(item.description)}</div>` : ''}

                                        <div class="product-cart-item-price-line">
                                            <span>Einzelpreis: ${moneyFormat(item.unit_price || 0)}</span>
                                            <span>Gesamt: ${moneyFormat(lineTotal)}</span>
                                        </div>

                                        <div class="product-cart-item-head-actions">
                                            <button type="button"
                                                    class="btn btn-sm product-cart-drag-handle"
                                                    title="Ziehen um zu verschieben">
                                                <i class="feather icon-move"></i>
                                            </button>

                                            <button type="button"
                                                    class="btn btn-sm product-add-cart-btn js-make-root-item"
                                                    data-item-id="${escapeHtml(item.id)}"
                                                    data-section-id="${escapeHtml(item.section_id || '')}">
                                                <i class="feather icon-corner-down-left mr-25"></i> Als Main
                                            </button>
                                        </div>

                                        <div class="product-cart-item-controls">
                                            <input type="number"
                                                   min="0"
                                                   step="0.01"
                                                   value="${Number(item.qty || 0)}"
                                                   class="product-cart-mini-input js-cart-item-qty"
                                                   data-item-id="${escapeHtml(item.id)}"
                                                   title="Menge">

                                            <input type="number"
                                                   min="0"
                                                   step="0.01"
                                                   value="${Number(item.unit_price || 0)}"
                                                   class="product-cart-mini-input js-cart-item-price"
                                                   data-item-id="${escapeHtml(item.id)}"
                                                   title="Preis">

                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger js-cart-remove-item"
                                                    data-item-id="${escapeHtml(item.id)}">
                                                <i class="feather icon-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        ${children.length ? `
                            <div class="product-cart-items" style="padding-top:0;">
                                ${children.map(child => renderCartItemNode(child, true)).join('')}
                            </div>
                        ` : ''}
                    </div>
                `;
            }

            function renderProductOnlyList() {
                const items = (cartState.items || []).filter(item => !item.parent_id);

                if (!items.length) {
                    return '<div class="product-cart-empty">Noch keine Produkte im Cart.</div>';
                }

                return `
                    <div class="product-cart-root-dropzone" data-root-dropzone="1">
                        Ziehen Sie importierte Produkte auf ein anderes Produkt, um daraus eine Sub-Komponente zu machen.
                    </div>

                    ${items.map(item => renderCartItemNode(item, false)).join('')}
                `;
            }

            function renderCartSections() {
                const $wrap = $el.cartSectionsWrap();

                if (!$wrap.length) return;

                if (getCartStep() === 'products') {
                    $wrap.html(renderProductOnlyList());
                    renderCartSummary();
                    renderCartTargetSections();
                    bindCartDragAndDrop();
                    refreshFeather();
                    return;
                }

                if (!cartState.sections.length) {
                    $wrap.html('<div class="product-cart-empty">Noch keine Sektionen vorhanden.</div>');
                    renderCartSummary();
                    renderCartTargetSections();
                    refreshFeather();
                    return;
                }

                const html = cartState.sections
                    .sort((a, b) => {
                        return (Number(a.sort_order || 0) - Number(b.sort_order || 0)) || (Number(a.id) - Number(b.id));
                    })
                    .map(section => {
                        const roots = cartRootsOf(section.id);

                        return `
                            <div class="product-cart-section" data-section-id="${escapeHtml(section.id)}">
                                <div class="product-cart-section-head">
                                    <div class="product-cart-section-title">
                                        <span class="product-cart-section-dot" style="background:${escapeHtml(section.color || '#93c21c')}"></span>
                                        <span class="product-cart-section-name">${escapeHtml(section.name || 'Sektion')}</span>
                                    </div>
                                </div>

                                <div class="product-cart-items">
                                    <div class="product-cart-root-dropzone" data-root-dropzone="1" data-section-id="${escapeHtml(section.id)}">
                                        Produkt hier ablegen = Main-Komponente in dieser Sektion
                                    </div>

                                    ${roots.length
                                ? roots.map(item => renderCartItemNode(item, false)).join('')
                                : '<div class="product-cart-empty">Noch keine Produkte in dieser Sektion.</div>'
                            }
                                </div>
                            </div>
                        `;
                    }).join('');

                $wrap.html(html);
                renderCartSummary();
                renderCartTargetSections();
                bindCartDragAndDrop();
                refreshFeather();
            }

            function moveCartItem(itemId, payload, done) {
                $.ajax({
                    url: ROUTES.cartItemUpdateBase.replace(/\/items$/, '') + '/items/' + itemId + '/move',
                    type: 'POST',
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    headers: {
                        'X-CSRF-TOKEN': CSRF(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function (res) {
                        if (res.success === false) {
                            toastError(res.message || 'Item konnte nicht verschoben werden.');
                            return;
                        }

                        const cartId = $el.cartId().val() || '';

                        loadCart(cartId, function () {
                            if (typeof done === 'function') done(res);
                        });
                    },
                    error: function (xhr) {
                        toastError(xhr.responseJSON?.message || 'Item konnte nicht verschoben werden.');
                    }
                });
            }

            function bindCartDragAndDrop() {
                $(document)
                    .off('dragstart.cartDnD', '.product-cart-item')
                    .on('dragstart.cartDnD', '.product-cart-item', function (e) {
                        const itemId = $(this).data('cart-item-id');

                        draggedCartItemId = itemId;

                        $(this).addClass('dragging');

                        if (e.originalEvent?.dataTransfer) {
                            e.originalEvent.dataTransfer.setData('text/plain', String(itemId));
                            e.originalEvent.dataTransfer.effectAllowed = 'move';
                        }
                    });

                $(document)
                    .off('dragend.cartDnD', '.product-cart-item')
                    .on('dragend.cartDnD', '.product-cart-item', function () {
                        draggedCartItemId = null;

                        $('.product-cart-item').removeClass('dragging product-cart-drop-target');
                        $('.product-cart-root-dropzone').removeClass('active');
                    });

                $(document)
                    .off('dragover.cartDnD', '.product-cart-item')
                    .on('dragover.cartDnD', '.product-cart-item', function (e) {
                        e.preventDefault();

                        const targetId = Number($(this).data('cart-item-id'));

                        if (!draggedCartItemId || Number(draggedCartItemId) === targetId) return;

                        $(this).addClass('product-cart-drop-target');
                    });

                $(document)
                    .off('dragleave.cartDnD', '.product-cart-item')
                    .on('dragleave.cartDnD', '.product-cart-item', function () {
                        $(this).removeClass('product-cart-drop-target');
                    });

                $(document)
                    .off('drop.cartDnD', '.product-cart-item')
                    .on('drop.cartDnD', '.product-cart-item', function (e) {
                        e.preventDefault();

                        const targetId = Number($(this).data('cart-item-id'));
                        const targetSectionId = Number($(this).data('section-id')) || null;

                        $(this).removeClass('product-cart-drop-target');

                        if (!draggedCartItemId || Number(draggedCartItemId) === targetId) return;

                        moveCartItem(draggedCartItemId, {
                            parent_id: targetId,
                            section_id: targetSectionId
                        }, function () {
                            toastSuccess('Produkt wurde als Sub-Komponente verschoben.');
                        });
                    });

                $(document)
                    .off('dragover.cartDnD', '.product-cart-root-dropzone')
                    .on('dragover.cartDnD', '.product-cart-root-dropzone', function (e) {
                        e.preventDefault();
                        $(this).addClass('active');
                    });

                $(document)
                    .off('dragleave.cartDnD', '.product-cart-root-dropzone')
                    .on('dragleave.cartDnD', '.product-cart-root-dropzone', function () {
                        $(this).removeClass('active');
                    });

                $(document)
                    .off('drop.cartDnD', '.product-cart-root-dropzone')
                    .on('drop.cartDnD', '.product-cart-root-dropzone', function (e) {
                        e.preventDefault();

                        $(this).removeClass('active');

                        const sectionId = $(this).data('section-id') || null;

                        if (!draggedCartItemId) return;

                        moveCartItem(draggedCartItemId, {
                            parent_id: null,
                            section_id: sectionId
                        }, function () {
                            toastSuccess('Produkt wurde als Main-Komponente verschoben.');
                        });
                    });

                $(document)
                    .off('click.makeRoot', '.js-make-root-item')
                    .on('click.makeRoot', '.js-make-root-item', function () {
                        const itemId = $(this).data('item-id');
                        const sectionId = $(this).data('section-id') || null;

                        moveCartItem(itemId, {
                            parent_id: null,
                            section_id: sectionId
                        }, function () {
                            toastSuccess('Produkt wurde als Main-Komponente gesetzt.');
                        });
                    });
            }

            function fillCartConfigForm() {
                if (!cartState.cart) return;

                $el.cartId().val(cartState.cart.id || '');
                $el.cartArticleGroup().val(cartState.cart.article_group_id || '').trigger('change.select2');
                $el.cartMode().val(cartState.cart.mode || 'new').trigger('change.select2');
                $el.cartName().val(cartState.cart.name || '');
                $el.cartDescription().val(cartState.cart.description || '');

                cartModeToggle();
            }

            function loadCartMasterSets(articleGroupId, selectedId = '') {
                const $select = $el.cartMasterSet();

                if (!$select.length) return;

                $select.empty().append('<option value="">Lade Master Sets ...</option>').trigger('change.select2');

                if (!articleGroupId) {
                    $select.empty().append('<option value="">Bitte zuerst Artikelgruppe wählen</option>').trigger('change.select2');
                    return;
                }

                $.ajax({
                    url: ROUTES.cartArticleGroupMasterSets,
                    type: 'GET',
                    dataType: 'json',
                    data: { article_group_id: articleGroupId },
                    success: function (res) {
                        const items = res.items || [];

                        $select.empty().append('<option value="">Master Set wählen</option>');

                        items.forEach(row => {
                            const text = row.text || row.name || ('Set #' + row.id);

                            $select.append(new Option(text, row.id, false, String(selectedId) === String(row.id)));
                        });

                        $select.trigger('change.select2');
                    },
                    error: function () {
                        $select.empty().append('<option value="">Fehler beim Laden</option>').trigger('change.select2');
                        toastError('Master Sets konnten nicht geladen werden.');
                    }
                });
            }

            function loadCart(cartId, cb) {
                if (!cartId) {
                    cartState.cart = null;
                    cartState.sections = [];
                    cartState.items = [];

                    $el.cartId().val('');

                    renderCartSections();

                    if (typeof cb === 'function') cb();

                    return;
                }

                $.ajax({
                    url: ROUTES.cartShowBase + '/' + cartId,
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        cartState.cart = res.cart || null;
                        cartState.sections = res.sections || [];
                        cartState.items = (res.items || []).map(normalizeCartItem);

                        fillCartConfigForm();

                        if (cartState.cart?.article_group_id && $el.cartMode().val() === 'existing') {
                            loadCartMasterSets(cartState.cart.article_group_id, cartState.cart.target_master_set_id || '');
                        }

                        renderCartSections();

                        if (typeof cb === 'function') cb();
                    },
                    error: function () {
                        toastError('Cart konnte nicht geladen werden.');
                    }
                });
            }

            function saveCartConfig() {
                const cartId = $el.cartId().val() || '';
                const articleGroupId = $el.cartArticleGroup().val() || '';
                const mode = $el.cartMode().val() || 'new';
                const name = ($el.cartName().val() || '').trim();
                const description = ($el.cartDescription().val() || '').trim();
                const targetMasterSetId = $el.cartMasterSet().val() || '';

                if (!articleGroupId) return toastInfo('Bitte zuerst eine Artikelgruppe wählen.');
                if (mode === 'new' && !name) return toastInfo('Bitte einen Namen für den neuen Master Set eingeben.');
                if (mode === 'existing' && !targetMasterSetId) return toastInfo('Bitte einen bestehenden Master Set wählen.');

                const payload = {
                    article_group_id: articleGroupId,
                    mode: mode,
                    name: mode === 'new' ? name : null,
                    description: description,
                    target_master_set_id: mode === 'existing' ? targetMasterSetId : null
                };

                $.ajax({
                    url: cartId ? (ROUTES.cartShowBase + '/' + cartId) : ROUTES.cartCreate,
                    type: cartId ? 'PUT' : 'POST',
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    headers: {
                        'X-CSRF-TOKEN': CSRF(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function (res) {
                        if (res.success === false) {
                            toastError(res.message || 'Cart konnte nicht gespeichert werden.');
                            return;
                        }

                        const newCartId = cartId || res.cart_id || res.id;

                        if (newCartId) {
                            $el.cartId().val(newCartId);

                            loadCart(newCartId, function () {
                                toastSuccess(res.message || 'Cart gespeichert.');
                                setCartStep('config');
                                openCartDrawer();
                            });
                        } else {
                            toastSuccess(res.message || 'Cart gespeichert.');
                        }
                    },
                    error: function (xhr) {
                        toastError(xhr.responseJSON?.message || 'Cart konnte nicht gespeichert werden.');
                    }
                });
            }

            function addCartSection() {
                const cartId = $el.cartId().val() || '';
                const name = ($el.cartSectionName().val() || '').trim();
                const color = $el.cartSectionColor().val() || '#93c21c';

                if (!cartId) return toastInfo('Bitte zuerst den Cart speichern / starten.');
                if (!name) return toastInfo('Bitte einen Sektionsnamen eingeben.');

                $.ajax({
                    url: ROUTES.cartSectionStoreBase + '/' + cartId + '/sections',
                    type: 'POST',
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify({ name, color }),
                    headers: {
                        'X-CSRF-TOKEN': CSRF(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function (res) {
                        if (res.success === false) {
                            toastError(res.message || 'Sektion konnte nicht erstellt werden.');
                            return;
                        }

                        $el.cartSectionName().val('');

                        loadCart(cartId, function () {
                            toastSuccess(res.message || 'Sektion hinzugefügt.');
                            setCartStep('config');
                        });
                    },
                    error: function (xhr) {
                        toastError(xhr.responseJSON?.message || 'Sektion konnte nicht erstellt werden.');
                    }
                });
            }

            function ensureCartExistsForImport(callback) {
                const cartId = $el.cartId().val() || '';

                if (cartId) {
                    if (typeof callback === 'function') callback(cartId);
                    return;
                }

                $.ajax({
                    url: ROUTES.cartCreate,
                    type: 'POST',
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        article_group_id: null,
                        mode: 'new',
                        name: null,
                        description: null,
                        target_master_set_id: null
                    }),
                    headers: {
                        'X-CSRF-TOKEN': CSRF(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function (res) {
                        const newCartId = res.cart_id || res.id || res.cart?.id || '';

                        if (!newCartId) {
                            toastError(res.message || 'Cart konnte nicht vorbereitet werden.');
                            return;
                        }

                        $el.cartId().val(newCartId);
                        cartState.cart = res.cart || { id: newCartId };

                        if (typeof callback === 'function') callback(newCartId);
                    },
                    error: function (xhr) {
                        toastError(xhr.responseJSON?.message || 'Cart konnte nicht vorbereitet werden.');
                    }
                });
            }

            function addProductToCart(productId, parentId = null) {
                ensureCartExistsForImport(function (cartId) {
                    const sectionId = $el.cartTargetSection().val() || '';

                    $.ajax({
                        url: ROUTES.cartItemStoreBase + '/' + cartId + '/items',
                        type: 'POST',
                        dataType: 'json',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            product_id: productId,
                            section_id: parentId ? null : (sectionId || null),
                            parent_id: parentId || null,
                            source_type: 'product',
                            node_type: parentId ? 'sub' : 'main',
                            qty: 1
                        }),
                        headers: {
                            'X-CSRF-TOKEN': CSRF(),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        success: function (res) {
                            if (res.success === false) {
                                toastError(res.message || 'Produkt konnte nicht in den Cart eingefügt werden.');
                                return;
                            }

                            loadCart(cartId, function () {
                                toastSuccess(res.message || 'Produkt zum Cart hinzugefügt.');
                                setCartStep('products');
                                openCartDrawer();
                            });
                        },
                        error: function (xhr) {
                            toastError(xhr.responseJSON?.message || 'Produkt konnte nicht in den Cart eingefügt werden.');
                        }
                    });
                });
            }

            function addBulkProductsToCart(productIds) {
                if (!Array.isArray(productIds) || !productIds.length) {
                    toastInfo('Keine Produkte ausgewählt.');
                    return;
                }

                ensureCartExistsForImport(function (cartId) {
                    const sectionId = $el.cartTargetSection().val() || null;

                    let chain = $.Deferred().resolve();

                    productIds.forEach(function (productId) {
                        chain = chain.then(function () {
                            return $.ajax({
                                url: ROUTES.cartItemStoreBase + '/' + cartId + '/items',
                                type: 'POST',
                                dataType: 'json',
                                contentType: 'application/json',
                                data: JSON.stringify({
                                    product_id: productId,
                                    section_id: sectionId,
                                    parent_id: null,
                                    source_type: 'product',
                                    node_type: 'main',
                                    qty: 1
                                }),
                                headers: {
                                    'X-CSRF-TOKEN': CSRF(),
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });
                        });
                    });

                    chain.done(function () {
                        loadCart(cartId, function () {
                            toastSuccess(productIds.length + ' Produkte wurden zum Cart hinzugefügt.');
                            setCartStep('products');
                            openCartDrawer();
                        });
                    }).fail(function (xhr) {
                        toastError(xhr.responseJSON?.message || 'Produkte konnten nicht vollständig eingefügt werden.');
                    });
                });
            }

            function updateCartItem(itemId, payload) {
                $.ajax({
                    url: ROUTES.cartItemUpdateBase + '/' + itemId,
                    type: 'PUT',
                    dataType: 'json',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    headers: {
                        'X-CSRF-TOKEN': CSRF(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function (res) {
                        if (res.success === false) {
                            toastError(res.message || 'Cart-Item konnte nicht aktualisiert werden.');
                            return;
                        }

                        const cartId = $el.cartId().val() || '';

                        loadCart(cartId);
                    },
                    error: function (xhr) {
                        toastError(xhr.responseJSON?.message || 'Cart-Item konnte nicht aktualisiert werden.');
                    }
                });
            }

            function removeCartItem(itemId) {
                $.ajax({
                    url: ROUTES.cartItemUpdateBase + '/' + itemId,
                    type: 'DELETE',
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': CSRF(),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    success: function (res) {
                        if (res.success === false) {
                            toastError(res.message || 'Cart-Item konnte nicht gelöscht werden.');
                            return;
                        }

                        const cartId = $el.cartId().val() || '';

                        loadCart(cartId, function () {
                            toastSuccess(res.message || 'Item entfernt.');
                        });
                    },
                    error: function (xhr) {
                        toastError(xhr.responseJSON?.message || 'Cart-Item konnte nicht gelöscht werden.');
                    }
                });
            }

            function convertCartToMasterSet() {
                const cartId = $el.cartId().val() || '';

                if (!cartId) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Cart fehlt',
                        text: 'Bitte zuerst einen Cart erstellen.',
                        customClass: { container: 'masterset-swal-on-top' }
                    });
                }

                const articleGroupId = $el.cartArticleGroup().val() || '';
                const mode = $el.cartMode().val() || 'new';
                const name = ($el.cartName().val() || '').trim();
                const targetMasterSetId = $el.cartMasterSet().val() || '';
                const description = ($el.cartDescription().val() || '').trim();

                if (!articleGroupId) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Artikelgruppe fehlt',
                        text: 'Bitte zuerst eine Artikelgruppe wählen und den Cart speichern.',
                        customClass: { container: 'masterset-swal-on-top' }
                    });
                }

                if (mode === 'new' && !name) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Name fehlt',
                        text: 'Bitte zuerst einen Namen für den neuen Master Set eingeben.',
                        customClass: { container: 'masterset-swal-on-top' }
                    });
                }

                if (mode === 'existing' && !targetMasterSetId) {
                    return Swal.fire({
                        icon: 'warning',
                        title: 'Master Set fehlt',
                        text: 'Bitte zuerst einen bestehenden Master Set wählen.',
                        customClass: { container: 'masterset-swal-on-top' }
                    });
                }

                Swal.fire({
                    title: 'Cart umwandeln?',
                    text: 'Der aktuelle Cart wird zuerst gespeichert und dann in einen Master Set umgewandelt.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ja, umwandeln',
                    cancelButtonText: 'Abbrechen',
                    customClass: { container: 'masterset-swal-on-top' }
                }).then((result) => {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Bitte warten',
                        text: 'Konfiguration wird gespeichert ...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading(),
                        customClass: { container: 'masterset-swal-on-top' }
                    });

                    const configPayload = {
                        article_group_id: articleGroupId,
                        mode: mode,
                        name: mode === 'new' ? name : null,
                        description: description,
                        target_master_set_id: mode === 'existing' ? targetMasterSetId : null
                    };

                    $.ajax({
                        url: ROUTES.cartShowBase + '/' + cartId,
                        type: 'PUT',
                        dataType: 'json',
                        contentType: 'application/json',
                        data: JSON.stringify(configPayload),
                        headers: {
                            'X-CSRF-TOKEN': CSRF(),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        success: function (saveRes) {
                            if (!saveRes.success) {
                                Swal.fire({
                                    icon: 'warning',
                                    title: 'Speichern fehlgeschlagen',
                                    text: saveRes.message || 'Cart-Konfiguration konnte nicht gespeichert werden.',
                                    customClass: { container: 'masterset-swal-on-top' }
                                });
                                return;
                            }

                            Swal.fire({
                                title: 'Bitte warten',
                                text: 'Master Set wird erstellt ...',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => Swal.showLoading(),
                                customClass: { container: 'masterset-swal-on-top' }
                            });

                            $.ajax({
                                url: ROUTES.cartConvertBase + '/' + cartId + '/convert',
                                type: 'POST',
                                dataType: 'json',
                                headers: {
                                    'X-CSRF-TOKEN': CSRF(),
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                success: function (res) {
                                    if (!res.success) {
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'Umwandlung nicht möglich',
                                            text: res.message || 'Cart konnte nicht umgewandelt werden.',
                                            customClass: { container: 'masterset-swal-on-top' }
                                        });
                                        return;
                                    }

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Master Set erstellt',
                                        html: `
                                            <div style="font-size:14px; line-height:1.6;">
                                                <div><strong>${escapeHtml(res.master_set_name || 'Master Set')}</strong> wurde erfolgreich erstellt.</div>
                                                <div style="margin-top:6px; color:#64748b;">ID: ${escapeHtml(res.master_set_id || '-')}</div>
                                            </div>
                                        `,
                                        confirmButtonText: 'OK',
                                        customClass: { container: 'masterset-swal-on-top' }
                                    }).then(() => {
                                        closeCartDrawer();

                                        $el.cartId().val('');
                                        $el.cartName().val('');
                                        $el.cartDescription().val('');
                                        $el.cartArticleGroup().val('').trigger('change');
                                        $el.cartMasterSet().val('').trigger('change');
                                        $el.cartSectionName().val('');
                                        $el.cartTargetSection()
                                            .empty()
                                            .append('<option value="">Bitte Sektion wählen</option>')
                                            .trigger('change');

                                        cartState.cart = null;
                                        cartState.sections = [];
                                        cartState.items = [];

                                        setCartStep('products');
                                        renderCartSections();
                                        renderCartSummary();
                                        updateCartFabCount();

                                        loadProducts(lastLoadedProductsUrl || null);
                                    });
                                },
                                error: function (xhr) {
                                    let title = 'Fehler';
                                    let html = 'Cart konnte nicht umgewandelt werden.';

                                    if (xhr.status === 422) {
                                        title = 'Validierung fehlgeschlagen';

                                        if (xhr.responseJSON?.errors) {
                                            html = Object.values(xhr.responseJSON.errors)
                                                .flat()
                                                .map(msg => `<div>${escapeHtml(msg)}</div>`)
                                                .join('');
                                        } else if (xhr.responseJSON?.message) {
                                            html = escapeHtml(xhr.responseJSON.message);
                                        }
                                    } else if (xhr.responseJSON?.message) {
                                        html = escapeHtml(xhr.responseJSON.message);
                                    }

                                    Swal.fire({
                                        icon: 'error',
                                        title: title,
                                        html: html,
                                        customClass: { container: 'masterset-swal-on-top' }
                                    });
                                }
                            });
                        },
                        error: function (xhr) {
                            let html = xhr.responseJSON?.message || 'Cart-Konfiguration konnte nicht gespeichert werden.';

                            if (xhr.responseJSON?.errors) {
                                html = Object.values(xhr.responseJSON.errors)
                                    .flat()
                                    .map(msg => `<div>${escapeHtml(msg)}</div>`)
                                    .join('');
                            }

                            Swal.fire({
                                icon: 'error',
                                title: 'Speichern fehlgeschlagen',
                                html: html,
                                customClass: { container: 'masterset-swal-on-top' }
                            });
                        }
                    });
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Init / events
            |--------------------------------------------------------------------------
            */

            function initSelect2() {
                $('.select2').select2({ width: '100%' });

                $('.select2-cart').select2({
                    width: '100%',
                    dropdownParent: $el.cartDrawer()
                });

                $el.modalSelect().select2({
                    width: '100%',
                    dropdownParent: $el.modal()
                });
            }

            function bindEvents() {
                $el.form().on('submit', function (e) {
                    e.preventDefault();
                    updateProductFilterUI();
                    loadProducts();
                });

                $('#product-filter-toggle').on('click', function () {
                    toggleProductFilterPanel();
                });

                $el.search().on('input', function () {
                    updateProductFilterUI();
                });

                $('#filter_article_group, #filter_distributor, #filter_status, #filter_category, #filter_sort, #filter_per_page, #filter_no_image')
                    .on('change', function () {
                        updateProductFilterUI();
                        loadProducts();
                    });

                $el.brand().on('change', function () {
                    const brandId = $(this).val() || '';

                    updateProductFilterUI();

                    reloadDistributorsForBrand(brandId, function () {
                        updateProductFilterUI();
                        loadProducts();
                    });
                });

                $el.resetBtn().on('click', function () {
                    $el.search().val('');
                    $el.group().val('').trigger('change.select2');
                    $el.dist().val('').trigger('change.select2');
                    $el.status().val('');
                    $el.category().val('');
                    $el.noImage().val('');
                    $el.sort().val('created_at|desc');
                    $el.perPage().val('12');
                    $el.brand().val('').trigger('change.select2');

                    toggleProductFilterPanel(false);
                    updateProductFilterUI();

                    setDistributorOptions(DIST_ALL_CACHE, '');

                    setTimeout(function () {
                        loadProducts();
                    }, 100);
                });

                $(document).on('click', '.js-open-image-modal', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    loadProductImages(
                        $(this).data('product-id'),
                        $(this).data('image') || '',
                        $(this).data('product-name') || ''
                    );
                });

                $('#productImageModalClose, #productImageModalCancel, #productImageModalBackdrop').on('click', closeProductImageModal);

                $('#productImageModal').on('click', function (e) {
                    if (e.target === this) closeProductImageModal();
                });

                $('#product-image-input').on('change', function () {
                    const file = this.files && this.files[0] ? this.files[0] : null;

                    if (!file) {
                        $('#product-image-file-name').text('');
                        return;
                    }

                    $('#product-image-file-name').text(file.name);

                    const reader = new FileReader();

                    reader.onload = function (ev) {
                        setProductImagePreview(ev.target.result);
                    };

                    reader.readAsDataURL(file);
                });

                $('#product-image-form').on('submit', function (e) {
                    e.preventDefault();
                    saveProductImage();
                });

                $(document).on('click', '.js-use-gallery-image', function () {
                    setProductImagePreview($(this).data('url') || '');
                });

                $(document).on('click', '.js-delete-gallery-image', function () {
                    deleteGalleryImage($(this).data('image-id'));
                });

                $(document).on('click', '.js-product-history', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    loadProductHistory(
                        $(this).data('product-id'),
                        $(this).data('product-name') || ''
                    );
                });

                $('#productHistoryClose, #productHistoryBackdrop').on('click', closeProductHistoryModal);

                $('#productHistoryModal').on('click', function (e) {
                    if (e.target === this) closeProductHistoryModal();
                });

                $(document).on('click', '.view-toggle-btn', function () {
                    const view = $(this).data('view') || 'card';

                    if (view === currentView) return;

                    currentView = view;

                    $('.view-toggle-btn').removeClass('active');
                    $(this).addClass('active');

                    applyCurrentViewClass();
                    loadProducts();
                });

                $(document).on('click', '#product-pagination a', function (e) {
                    e.preventDefault();

                    const url = $(this).attr('href');

                    if (!url || url === '#') return;

                    loadProducts(url);
                });

                $(document).on('click', '.product-card-main', function () {
                    const url = $(this).data('details-url');

                    if (url) window.location.href = url;
                });

                $(document).on('click', '.js-duplicate-product', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    duplicateProduct($(this).data('product-id'));
                });

                $(document).on('change', '.product-select', updateBulkState);

                $(document).on('change', '#select-all-page', function () {
                    const checked = $(this).is(':checked');

                    $('.product-select').prop('checked', checked);

                    updateBulkState();
                });

                $el.bulkAction().on('change', updateBulkState);
                $el.bulkApply().on('click', applyBulkAction);

                $el.bulkCart().on('click', function () {
                    addBulkProductsToCart(getSelectedIds());
                });

                $(document).on('click', '.js-add-to-list', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const pid = $(this).data('product-id');
                    const type = $(this).data('list-type');

                    if (pid && type) openListModal(pid, type, 'add');
                });

                $(document).on('click', '.js-remove-from-list', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const pid = $(this).data('product-id');
                    const type = $(this).data('list-type');

                    if (pid && type) openListModal(pid, type, 'remove');
                });

                $el.modalBtn().on('click', function () {
                    if (currentListAction === 'remove') removeProductFromList();
                    else addProductToList();
                });

                $el.cartMode().on('change', function () {
                    cartModeToggle();

                    if ($(this).val() === 'existing') {
                        loadCartMasterSets($el.cartArticleGroup().val() || '');
                    }
                });

                $el.cartArticleGroup().on('change', function () {
                    if ($el.cartMode().val() === 'existing') {
                        loadCartMasterSets($(this).val() || '');
                    }
                });

                $el.cartSaveBtn().on('click', saveCartConfig);
                $el.cartAddSectionBtn().on('click', addCartSection);
                $el.cartConvertBtn().on('click', convertCartToMasterSet);

                $el.cartNextBtn().on('click', goToConfigStep);
                $el.cartBackBtn().on('click', goToProductsStep);

                $(document).on('click', '.js-add-product-to-cart', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    addProductToCart($(this).data('product-id'));
                });

                $(document).on('change', '.js-cart-item-qty', function () {
                    updateCartItem($(this).data('item-id'), {
                        qty: $(this).val()
                    });
                });

                $(document).on('change', '.js-cart-item-price', function () {
                    updateCartItem($(this).data('item-id'), {
                        unit_price: $(this).val()
                    });
                });

                $(document).on('click', '.js-cart-remove-item', function () {
                    removeCartItem($(this).data('item-id'));
                });

                $(document).on('change', '.js-product-supplier-select', function () {
                    const $select = $(this);
                    const index = Number($select.val() || 0);
                    const $wrap = $select.closest('[data-supplier-widget="1"]');
                    const $detail = $wrap.find('.js-product-supplier-detail');
                    const suppliers = normalizeJsonArray($wrap.attr('data-suppliers'));

                    const supplier = suppliers[index] || suppliers[0] || null;

                    if (!supplier) {
                        $detail.html('<div class="product-supplier-empty">Keine Lieferantendaten vorhanden.</div>');
                        return;
                    }

                    $detail.html(renderSupplierDetail(supplier));
                    refreshFeather();
                });

                $(document).on('click', '.js-open-distributor-modal', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const productName = $(this).data('product-name') || '';
                    const distributors = normalizeJsonArray($(this).attr('data-distributors'));

                    openDistributorModal(productName, distributors);
                });

                $('#productDistModalClose, #productDistModalBackdrop').on('click', closeDistributorModal);

                $('#productDistModal').on('click', function (e) {
                    if (e.target === this) closeDistributorModal();
                });

                $el.cartFab().on('click', function () {
                    setCartStep(getCartStep() || 'products');
                    openCartDrawer();
                });

                $el.cartClose().add($el.cartBackdrop()).on('click', closeCartDrawer);

                $(document).on('keydown', function (e) {
                    if (e.key === 'Escape') {
                        closeCartDrawer();
                        closeProductImageModal();
                        closeDistributorModal();
                        closeProductHistoryModal();
                    }
                });
            }

            function init() {
                initSelect2();
                bindEvents();
                cartModeToggle();
                setCartStep('products');
                applyCurrentViewClass();
                updateProductFilterUI();
                loadProducts();
                renderCartSections();
                bindCartDragAndDrop();
                updateNoImageExportUrl();
                refreshFeather();
            }

            $(init);

            window.addProductToCart = addProductToCart;
            window.addBulkProductsToCart = addBulkProductsToCart;
            window.openCartDrawer = openCartDrawer;
            window.closeCartDrawer = closeCartDrawer;

        })(jQuery);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            "use strict";

            let activeMenu = null;

            function getOrCreateFloatMenu() {
                let el = document.getElementById('product-menu-float');

                if (!el) {
                    el = document.createElement('div');
                    el.id = 'product-menu-float';
                    el.className = 'product-menu-float';
                    document.body.appendChild(el);
                }

                return el;
            }

            function closeMenu() {
                const el = document.getElementById('product-menu-float');

                if (el) {
                    el.classList.remove('show');
                    el.classList.remove('drop-up');
                }

                activeMenu = null;
            }

            function openMenu(toggle) {
                const container = toggle.closest('.list-menu-container');

                if (!container) return;

                const template = container.querySelector('.custom-menu');

                if (!template) return;

                const floatMenu = getOrCreateFloatMenu();

                floatMenu.innerHTML = template.innerHTML;

                const rect = toggle.getBoundingClientRect();
                const vw = window.innerWidth;
                const vh = window.innerHeight;

                floatMenu.style.visibility = 'hidden';
                floatMenu.style.display = 'block';
                floatMenu.classList.add('show');

                const menuWidth = floatMenu.offsetWidth || 240;
                const menuHeight = floatMenu.offsetHeight || 180;

                floatMenu.classList.remove('show');
                floatMenu.style.display = '';
                floatMenu.style.visibility = '';

                let top = rect.bottom + 8;
                let left = rect.right - menuWidth;

                if (left < 8) left = 8;
                if (left + menuWidth > vw - 8) left = vw - menuWidth - 8;

                let dropUp = false;

                if (top + menuHeight > vh - 8) {
                    top = rect.top - menuHeight - 8;
                    dropUp = true;
                }

                floatMenu.style.top = top + 'px';
                floatMenu.style.left = left + 'px';

                if (dropUp) floatMenu.classList.add('drop-up');
                else floatMenu.classList.remove('drop-up');

                floatMenu.classList.add('show');

                activeMenu = floatMenu;
            }

            document.addEventListener('click', function (e) {
                const toggle = e.target.closest('.js-menu-toggle');

                if (toggle) {
                    e.preventDefault();
                    e.stopPropagation();

                    if (activeMenu && activeMenu.classList.contains('show')) {
                        closeMenu();
                        return;
                    }

                    closeMenu();
                    openMenu(toggle);
                    return;
                }

                if (e.target.closest('#product-menu-float')) return;

                closeMenu();
            });

            window.addEventListener('resize', closeMenu);
            window.addEventListener('scroll', closeMenu, true);
        });
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
                url: "{{ url('product')}}",
            },
        ];

        if (window.setGlobalBreadcrumbs) {
            window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
        }
    </script>
@endpush