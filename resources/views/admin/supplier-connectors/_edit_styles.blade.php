<style>
    .sc-page {
        padding: 24px;
        background: #f8fafc;
        min-height: calc(100vh - 80px);
    }

    .sc-topbar {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .sc-title {
        font-size: 26px;
        font-weight: 900;
        color: #111827;
        margin: 0;
        letter-spacing: -0.03em;
    }

    .sc-subtitle {
        color: #6b7280;
        font-size: 14px;
        margin-top: 6px;
        line-height: 1.5;
    }

    .sc-top-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .sc-btn,
    .map-btn {
        border: none;
        border-radius: 14px;
        padding: 10px 14px;
        font-weight: 900;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
        font-size: 14px;
        transition: .18s ease;
    }

    .sc-btn:hover,
    .map-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .sc-btn-primary,
    .map-btn-primary {
        background: #74b2d4;
        color: white;
        box-shadow: 0 10px 22px rgba(116, 178, 212, .22);
    }

    .sc-btn-green,
    .map-btn-green {
        background: #93c21c;
        color: white;
        box-shadow: 0 10px 22px rgba(147, 194, 28, .20);
    }

    .sc-btn-soft,
    .map-btn-soft {
        background: white;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .map-btn-danger {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .sc-status-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .sc-status-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 14px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .045);
    }

    .sc-status-label {
        color: #6b7280;
        font-size: 12px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 6px;
    }

    .sc-status-value {
        color: #111827;
        font-size: 15px;
        font-weight: 900;
        word-break: break-word;
    }

    .sc-status-help {
        color: #6b7280;
        font-size: 12px;
        margin-top: 5px;
        line-height: 1.4;
    }

    .sc-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 900;
        white-space: nowrap;
    }

    .sc-badge-green {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #bbf7d0;
    }

    .sc-badge-red {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    .sc-badge-gray {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .map-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        padding: 18px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
        margin-top: 18px;
    }

    .map-card-header {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .map-card-title {
        font-size: 18px;
        font-weight: 900;
        color: #111827;
        margin: 0;
    }

    .map-card-desc {
        color: #6b7280;
        font-size: 13px;
        line-height: 1.5;
        margin-top: 4px;
    }

    .map-info {
        background: rgba(116, 178, 212, .10);
        border: 1px solid rgba(116, 178, 212, .25);
        color: #075985;
        border-radius: 16px;
        padding: 12px 14px;
        font-size: 13px;
        font-weight: 800;
        line-height: 1.5;
        margin-bottom: 16px;
    }

    .map-grid {
        display: grid;
        grid-template-columns: 1.3fr 1fr 1fr 1fr;
        gap: 12px;
    }

    .map-grid-bottom {
        display: grid;
        grid-template-columns: 1fr 160px 150px 150px;
        gap: 12px;
        margin-top: 12px;
        align-items: center;
    }

    .map-input,
    .map-select {
        width: 100%;
        border: 1px solid #d1d5db;
        border-radius: 12px;
        padding: 9px 10px;
        font-size: 13px;
        background: white;
        outline: none;
        transition: .18s ease;
    }

    .map-label {
        display: block;
        font-size: 12px;
        color: #374151;
        font-weight: 900;
        margin-bottom: 6px;
    }

    .map-check {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 900;
        color: #374151;
    }

    .map-table-wrap {
        overflow-x: auto;
        border: 1px solid #f1f5f9;
        border-radius: 16px;
    }

    .map-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 1200px;
    }

    .map-table th {
        text-align: left;
        background: #f9fafb;
        color: #6b7280;
        padding: 10px;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: .05em;
        white-space: nowrap;
    }

    .map-table td {
        border-top: 1px solid #f1f5f9;
        padding: 10px;
        vertical-align: middle;
    }

    .map-row-actions {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .sc-empty {
        padding: 32px 20px;
        text-align: center;
        color: #6b7280;
        border: 1px dashed #d1d5db;
        border-radius: 16px;
        background: #fafafa;
    }

    .sc-empty strong {
        display: block;
        color: #111827;
        font-size: 15px;
        margin-bottom: 4px;
    }

    .sc-toast {
        position: fixed;
        right: 24px;
        top: 24px;
        z-index: 99999;
        min-width: 320px;
        max-width: 460px;
        padding: 14px 16px;
        border-radius: 16px;
        box-shadow: 0 18px 45px rgba(15, 23, 42, .16);
        font-weight: 900;
        line-height: 1.45;
        animation: scToastIn .25s ease;
    }

    .sc-toast-success {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #bbf7d0;
    }

    .sc-toast-error {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid #fecaca;
    }

    @keyframes scToastIn {
        from {
            opacity: 0;
            transform: translateY(-8px) scale(.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    @media(max-width: 1100px) {
        .sc-status-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .map-grid,
        .map-grid-bottom {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media(max-width: 700px) {
        .sc-page {
            padding: 16px;
        }

        .sc-topbar,
        .map-card-header {
            flex-direction: column;
            align-items: stretch;
        }

        .sc-top-actions {
            justify-content: stretch;
        }

        .sc-btn,
        .map-btn {
            width: 100%;
        }

        .sc-status-grid,
        .map-grid,
        .map-grid-bottom {
            grid-template-columns: 1fr;
        }

        .sc-toast {
            left: 16px;
            right: 16px;
            top: 16px;
            min-width: unset;
        }
    }
</style>