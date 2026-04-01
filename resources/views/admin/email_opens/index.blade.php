 
@extends('admin.layouts.app')

@section('title', 'Email Open Tracking')

@section('style')
<style>
    /* Page layout */
    .email-page-wrapper {
        max-width: 1100px;
        margin: 0 auto;
        padding: 24px 16px 40px;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        color: #1f2933;
    }

    .email-page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 18px;
    }

    .email-page-title {
        font-size: 22px;
        font-weight: 600;
        margin: 0 0 4px;
    }

    .email-page-subtitle {
        margin: 0;
        font-size: 13px;
        color: #6b7280;
    }

    /* Button */
    .btn-primary-custom {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 8px 16px;
        border-radius: 999px;
        border: none;
        font-size: 13px;
        font-weight: 500;
        background: #2563eb;
        color: #ffffff;
        cursor: pointer;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.25);
        transition: background 0.18s ease, transform 0.12s ease, box-shadow 0.18s ease;
    }

    .btn-primary-custom:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(37, 99, 235, 0.3);
    }

    .btn-primary-custom:active {
        transform: translateY(0);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    /* Card */
    .email-card {
        background: #ffffff;
        border-radius: 18px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 20px 30px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .email-card-inner {
        padding: 16px 18px 18px;
        overflow-x: auto;
    }

    /* Table */
    .table-custom {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .table-custom thead {
        background: #f9fafb;
    }

    .table-custom th,
    .table-custom td {
        padding: 8px 8px;
        text-align: left;
        border-bottom: 1px solid #edf0f4;
        vertical-align: top;
    }

    .table-custom th {
        font-weight: 600;
        color: #4b5563;
        font-size: 12px;
        white-space: nowrap;
    }

    .table-custom tbody tr:hover {
        background: #f3f4ff;
    }

    .table-custom td.truncate-ua {
        max-width: 260px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .email-link-button {
        border: none;
        padding: 0;
        background: transparent;
        font-size: 13px;
        color: #2563eb;
        cursor: pointer;
        text-decoration: underline;
    }

    .email-link-button:hover {
        color: #1d4ed8;
    }

    /* Modal overlay */
    .modal-overlay-custom {
        position: fixed;
        inset: 0;
        z-index: 9999;
        display: none; /* hidden by default */
        align-items: center;
        justify-content: center;
    }

    .modal-overlay-custom.is-visible {
        display: flex;
    }

    .modal-backdrop-custom {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(3px);
    }

    /* Modal content */
    .modal-panel-custom {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 420px;
        margin: 0 16px;
        background: #ffffff;
        border-radius: 18px;
        box-shadow: 0 22px 45px rgba(15, 23, 42, 0.35);
        padding: 18px 18px 14px;
        animation: modal-pop 0.18s ease-out;
    }

    @keyframes modal-pop {
        from {
            opacity: 0;
            transform: translateY(6px) scale(0.98);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .modal-title-custom {
        margin: 0 0 10px;
        font-size: 16px;
        font-weight: 600;
        color: #111827;
    }

    .modal-field-group {
        margin-bottom: 10px;
    }

    .modal-label-custom {
        display: block;
        margin-bottom: 4px;
        font-size: 12px;
        font-weight: 500;
        color: #4b5563;
    }

    .modal-input-custom {
        width: 100%;
        border-radius: 10px;
        border: 1px solid #d1d5db;
        padding: 7px 9px;
        font-size: 13px;
        outline: none;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }

    .modal-input-custom:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.25);
    }

    .modal-footer-custom {
        display: flex;
        justify-content: flex-end;
        gap: 6px;
        margin-top: 10px;
    }

    .btn-secondary-custom {
        padding: 7px 12px;
        font-size: 12px;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        background: #ffffff;
        color: #374151;
        cursor: pointer;
        transition: background 0.15s ease;
    }

    .btn-secondary-custom:hover {
        background: #f3f4f6;
    }

    .btn-primary-sm-custom {
        padding: 7px 14px;
        font-size: 12px;
        border-radius: 999px;
        border: none;
        background: #2563eb;
        color: #ffffff;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.12s ease;
    }

    .btn-primary-sm-custom:hover {
        background: #1d4ed8;
        transform: translateY(-0.5px);
    }

    .modal-feedback-custom {
        font-size: 11px;
        margin-top: 4px;
    }

    .modal-feedback-error {
        color: #b91c1c;
    }

    .modal-feedback-info {
        color: #4b5563;
    }

    .modal-feedback-success {
        color: #15803d;
    }
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
                        <h2 class="content-header-title float-left mb-0">Email</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ url('/') }}">Home</a>
                                </li> 
                            </ol>
                        </div>
                    </div>
                </div>
            </div> 
        </div> 

        <div class="content-body">
            <div class="email-page-wrapper">
                <div class="email-page-header">
                    <div>
                        <h1 class="email-page-title">Email Open Tracking</h1>
                        <p class="email-page-subtitle">
                            Shows who opened your marketing emails (IP, approximate location, time).
                        </p>
                    </div>
                    <button id="openSendEmailModal" type="button" class="btn-primary-custom">
                        Send Email
                    </button>
                </div>

                <div class="email-card">
                    <div class="email-card-inner">
                        <table id="email-opens-table" class="table-custom">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Campaign</th>
                                    <th>Email</th>
                                    <th>IP</th>
                                    <th>Country</th>
                                    <th>City</th>
                                    <th>Opened at</th>
                                    <th>User-Agent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filled by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- /.email-page-wrapper -->
        </div>
    </div>
</div>

<!-- Custom Modal -->
<div id="sendEmailModal" class="modal-overlay-custom">
    <div class="modal-backdrop-custom"></div>

    <div class="modal-panel-custom">
        <h2 class="modal-title-custom">Send Marketing Email</h2>

        <div class="modal-field-group">
            <label class="modal-label-custom">Email address</label>
            <input id="sendEmailInput"
                   type="email"
                   class="modal-input-custom"
                   placeholder="user@example.com">
        </div>

        <div class="modal-field-group">
            <label class="modal-label-custom">Campaign (optional)</label>
            <input id="sendCampaignInput"
                   type="text"
                   class="modal-input-custom"
                   placeholder="Winter-2025">
        </div>

        <p id="sendEmailFeedback" class="modal-feedback-custom" style="display:none;"></p>

        <div class="modal-footer-custom">
            <button type="button" id="cancelSendEmail" class="btn-secondary-custom">
                Cancel
            </button>
            <button type="button" id="confirmSendEmail" class="btn-primary-sm-custom">
                Send
            </button>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody       = document.querySelector('#email-opens-table tbody');
    const modal           = document.getElementById('sendEmailModal');
    const openModalBtn    = document.getElementById('openSendEmailModal');
    const cancelModalBtn  = document.getElementById('cancelSendEmail');
    const confirmBtn      = document.getElementById('confirmSendEmail');
    const emailInput      = document.getElementById('sendEmailInput');
    const campaignInput   = document.getElementById('sendCampaignInput');
    const feedbackEl      = document.getElementById('sendEmailFeedback');
    const csrfTokenMeta   = document.querySelector('meta[name="csrf-token"]');
    const csrfToken       = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

    if (!tableBody) return;

    function showModal(prefillEmail) {
        emailInput.value    = prefillEmail || '';
        campaignInput.value = '';
        feedbackEl.textContent = '';
        feedbackEl.style.display = 'none';
        feedbackEl.className = 'modal-feedback-custom modal-feedback-info';

        modal.classList.add('is-visible');
        setTimeout(function () {
            emailInput.focus();
        }, 40);
    }

    function hideModal() {
        modal.classList.remove('is-visible');
    }

    function routeEmailOpensData() {
        return '/admin/email-opens/data';
    }

    function routeSendEmail() {
        return '/admin/email-opens/send';
    }

    function loadTable() {
        fetch(routeEmailOpensData())
            .then(function (response) { return response.json(); })
            .then(function (rows) {
                tableBody.innerHTML = '';

                rows.forEach(function (row) {
                    const tr = document.createElement('tr');

                    const email    = row.email || '';
                    const campaign = row.campaign || '';
                    const openedAt = row.opened_at
                        ? new Date(row.opened_at).toLocaleString()
                        : '';

                    tr.innerHTML = `
                        <td>${row.id}</td>
                        <td>${campaign}</td>
                        <td>
                            ${email
                                ? '<button type="button" class="email-link-button" data-email="' + email + '">' + email + '</button>'
                                : ''
                            }
                        </td>
                        <td>${row.ip || ''}</td>
                        <td>${row.country || ''}</td>
                        <td>${row.city || ''}</td>
                        <td>${openedAt}</td>
                        <td class="truncate-ua" title="${row.user_agent || ''}">
                            ${row.user_agent || ''}
                        </td>
                    `;

                    tableBody.appendChild(tr);
                });
            })
            .catch(function (error) {
                console.error('Failed to load email open data', error);
            });
    }

    function sendEmail() {
        const email    = (emailInput.value || '').trim();
        const campaign = (campaignInput.value || '').trim();

        if (!email) {
            feedbackEl.textContent = 'Please enter an email address.';
            feedbackEl.style.display = 'block';
            feedbackEl.className = 'modal-feedback-custom modal-feedback-error';
            return;
        }

        feedbackEl.textContent = 'Sending...';
        feedbackEl.style.display = 'block';
        feedbackEl.className = 'modal-feedback-custom modal-feedback-info';

        fetch(routeSendEmail(), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ email: email, campaign: campaign })
        })
        .then(function (response) {
            if (!response.ok) {
                return response.json().then(function (data) {
                    throw new Error(data.message || 'Failed to send email.');
                }).catch(function (err) {
                    throw err;
                });
            }
            return response.json();
        })
        .then(function () {
            feedbackEl.textContent = 'Email sent.';
            feedbackEl.style.display = 'block';
            feedbackEl.className = 'modal-feedback-custom modal-feedback-success';

            loadTable();

            setTimeout(function () {
                hideModal();
            }, 700);
        })
        .catch(function (error) {
            console.error(error);
            feedbackEl.textContent = error.message || 'Error sending email.';
            feedbackEl.style.display = 'block';
            feedbackEl.className = 'modal-feedback-custom modal-feedback-error';
        });
    }

    /* Initial load */
    loadTable();

    /* Open modal from top button */
    if (openModalBtn) {
        openModalBtn.addEventListener('click', function () {
            showModal('');
        });
    }

    /* Cancel button */
    if (cancelModalBtn) {
        cancelModalBtn.addEventListener('click', function () {
            hideModal();
        });
    }

    /* Send button */
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function () {
            sendEmail();
        });
    }

    /* Close modal on backdrop click */
    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            hideModal();
        }
    });

    /* Delegated click on email addresses inside table */
    tableBody.addEventListener('click', function (event) {
        const btn = event.target.closest('.email-link-button');
        if (!btn) return;

        const email = btn.getAttribute('data-email') || '';
        showModal(email);
    });
});
</script>
@endsection
