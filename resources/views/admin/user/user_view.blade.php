@extends('admin.layouts.app')

@section('title')
    {{ auth()->user()->email }} Profile
@endsection

@php
    use Carbon\Carbon;

    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | Employee mapping
    |--------------------------------------------------------------------------
    | In your system users.name can store the employee ID.
    | Controller should pass: $employee = Employee::find(auth()->user()->name);
    */
    $profileEmployee = $employee ?? null;

    $displayName = trim(($profileEmployee->name ?? '') . ' ' . ($profileEmployee->lastname ?? ''));

    if (!$displayName) {
        $displayName = $user->name ?? 'User';
    }

    $profileImage = null;

    if ($profileEmployee && !empty($profileEmployee->image)) {
        $profileImage = asset('images/employee/' . $profileEmployee->image);
    } elseif (!empty($user->image)) {
        $profileImage = asset('images/user/' . $user->image);
    } else {
        $profileImage = asset('images/employee/users.png');
    }

    $roleLabel = ((int) $user->is_admin === 1) ? 'Administrator' : 'Limited User';
    $statusLabel = ((int) $user->is_active === 1) ? 'Active' : 'Deactivated';

    $permissions = collect($data ?? [])->filter(function ($pre) use ($user) {
        // user_rolls.user_id = users.id
        return (string) $pre->user_id === (string) $user->id;
    });

    $hasPermission = function ($value) {
        return (int) $value === 1; // Flags sind tinyint(1) (1/0), nicht mehr 'on'
    };
@endphp

@once
    @push('style')
        <style>
            :root {
                --profile-bg: #f3f4f6;
                --profile-card: #ffffff;
                --profile-text: #0f172a;
                --profile-muted: #64748b;
                --profile-border: #e5e7eb;

                --profile-blue: #74b2d4;
                --profile-green: #93c21c;
                --profile-orange: #f59e0b;
                --profile-red: #ef4444;
                --profile-purple: #c08eea;

                --profile-shadow-sm: 0 1px 2px rgba(15, 23, 42, .06);
                --profile-shadow: 0 20px 60px -30px rgba(15, 23, 42, .35);
                --profile-radius: 18px;
                --profile-transition: all .2s ease;
            }

            body {
                background: var(--profile-bg);
            }

            .profile-wrap {
                font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; 
                color: var(--profile-text);
            }

            .profile-hero {
                position: relative;
                overflow: hidden;
                border: 1px solid rgba(116, 178, 212, .35);
                border-radius: 24px;
                background:
                    radial-gradient(circle at top left, rgba(116, 178, 212, .35), transparent 32%),
                    radial-gradient(circle at bottom right, rgba(147, 194, 28, .22), transparent 34%),
                    linear-gradient(135deg, #ffffff, #f8fafc);
                box-shadow: var(--profile-shadow-sm);
                padding: 22px; 
            }

            .profile-hero-inner {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 18px;
                position: relative;
                z-index: 2;
            }

            .profile-user {
                display: flex;
                align-items: center;
                gap: 16px;
                min-width: 0;
            }

            .profile-avatar {
                width: 86px;
                height: 86px;
                border-radius: 24px;
                padding: 4px;
                background: linear-gradient(135deg, var(--profile-blue), var(--profile-green));
                box-shadow: 0 20px 35px -24px rgba(15, 23, 42, .4);
                flex: 0 0 auto;
            }

            .profile-avatar img {
                width: 100%;
                height: 100%;
                border-radius: 20px;
                object-fit: cover;
                background: #fff;
                border: 3px solid #fff;
            }

            .profile-title {
                font-size: 28px;
                font-weight: 950;
                letter-spacing: -.04em;
                margin: 0;
                color: #0f172a;
                line-height: 1.1;
            }

            .profile-sub {
                font-size: 14px;
                color: var(--profile-muted);
                margin-top: 7px;
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                align-items: center;
            }

            .profile-badge {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                padding: 7px 11px;
                border-radius: 999px;
                font-size: 12px;
                font-weight: 900;
                border: 1px solid var(--profile-border);
                background: #fff;
                color: #334155;
            }

            .profile-badge.ok {
                background: rgba(147, 194, 28, .14);
                color: #4d6a08;
                border-color: rgba(147, 194, 28, .28);
            }

            .profile-badge.warn {
                background: rgba(245, 158, 11, .14);
                color: #9a5b00;
                border-color: rgba(245, 158, 11, .25);
            }

            .profile-badge.info {
                background: rgba(116, 178, 212, .16);
                color: #0b5b7a;
                border-color: rgba(116, 178, 212, .30);
            }

            .profile-dot {
                width: 8px;
                height: 8px;
                border-radius: 999px;
                background: currentColor;
            }

            .profile-actions {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                justify-content: flex-end;
            }

            .profile-btn {
                border: none;
                background: var(--profile-green);
                color: #fff;
                padding: 11px 15px;
                border-radius: 14px;
                font-weight: 900;
                cursor: pointer;
                transition: var(--profile-transition);
                display: inline-flex;
                align-items: center;
                gap: 8px;
                box-shadow: 0 12px 25px -16px rgba(147, 194, 28, .85);
                text-decoration: none;
                white-space: nowrap;
            }

            .profile-btn:hover {
                color: #fff;
                text-decoration: none;
                filter: brightness(.96);
                transform: translateY(-1px);
            }

            .profile-btn.blue {
                background: var(--profile-blue);
                box-shadow: 0 12px 25px -16px rgba(116, 178, 212, .85);
            }

            .profile-btn.light {
                background: rgba(116, 178, 212, .13);
                color: #0b5b7a;
                border: 1px solid rgba(116, 178, 212, .35);
                box-shadow: none;
            }

            .profile-btn.light:hover {
                color: #0b5b7a;
                background: rgba(116, 178, 212, .2);
            }

            .profile-grid {
                display: grid;
                grid-template-columns: minmax(0, 1fr) minmax(390px, .78fr);
                gap: 18px;
                margin-bottom: 18px;
            }

            .profile-card {
                background: var(--profile-card);
                border: 1px solid var(--profile-border);
                border-radius: var(--profile-radius);
                box-shadow: var(--profile-shadow-sm);
                overflow: hidden;
                transition: var(--profile-transition);
            }

            .profile-card:hover {
                box-shadow: var(--profile-shadow);
            }

            .profile-card-head {
                padding: 16px 18px;
                border-bottom: 1px solid rgba(116, 178, 212, .22);
                background: linear-gradient(135deg, rgba(116, 178, 212, .15), rgba(147, 194, 28, .08));
                display: flex;
                justify-content: space-between;
                gap: 12px;
                align-items: center;
            }

            .profile-card-title {
                font-size: 16px;
                font-weight: 950;
                color: #0f172a;
                margin: 0;
                display: flex;
                align-items: center;
                gap: 9px;
            }

            .profile-card-body {
                padding: 18px;
            }

            .profile-info-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 12px;
            }

            .profile-info {
                border: 1px solid var(--profile-border);
                border-radius: 16px;
                padding: 13px;
                background: #f8fafc;
            }

            .profile-info-label {
                font-size: 11px;
                color: var(--profile-muted);
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: .06em;
                margin-bottom: 6px;
            }

            .profile-info-value {
                font-size: 14px;
                color: #0f172a;
                font-weight: 850;
                word-break: break-word;
            }

            .profile-form {
                display: grid;
                gap: 13px;
            }

            .profile-field {
                display: flex;
                flex-direction: column;
                gap: 7px;
            }

            .profile-label {
                font-size: 12px;
                font-weight: 950;
                color: #0f172a;
            }

            .profile-input {
                width: 100%;
                border: 1px solid var(--profile-border);
                background: #fff;
                border-radius: 14px;
                padding: 11px 13px;
                outline: none;
                transition: var(--profile-transition);
                font-size: 14px;
            }

            .profile-input:focus {
                border-color: rgba(147, 194, 28, .85);
                box-shadow: 0 0 0 3px rgba(147, 194, 28, .18);
            }

            .profile-error {
                padding: 9px 11px;
                border-radius: 12px;
                background: rgba(239, 68, 68, .10);
                border: 1px solid rgba(239, 68, 68, .24);
                color: #b91c1c;
                font-size: 12px;
                font-weight: 700;
            }

            .profile-alert {
                border-radius: 16px;
                padding: 13px 16px;
                margin-bottom: 14px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                font-size: 14px;
                font-weight: 850;
                border: 1px solid transparent;
            }

            .profile-alert.success {
                background: rgba(147, 194, 28, .13);
                color: #4d6a08;
                border-color: rgba(147, 194, 28, .30);
            }

            .profile-alert.danger {
                background: rgba(239, 68, 68, .10);
                color: #b91c1c;
                border-color: rgba(239, 68, 68, .25);
            }

            .profile-alert button {
                border: none;
                background: transparent;
                color: inherit;
                font-weight: 950;
                cursor: pointer;
                font-size: 18px;
                line-height: 1;
            }

            .profile-permission-card {
                margin-top: 18px;
            }

            .profile-permission-top {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                flex-wrap: wrap;
            }

            .profile-search {
                min-width: 260px;
                border: 1px solid var(--profile-border);
                border-radius: 14px;
                padding: 10px 13px 10px 38px;
                outline: none;
                font-size: 13px;
                transition: var(--profile-transition);
                background-color: #fff;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z' /%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: 12px center;
                background-size: 16px;
            }

            .profile-search:focus {
                border-color: rgba(116, 178, 212, .85);
                box-shadow: 0 0 0 3px rgba(116, 178, 212, .18);
            }

            .profile-permission-table-wrap {
                overflow-x: auto;
            }

            .profile-permission-table {
                width: 100%;
                border-collapse: separate;
                border-spacing: 0 10px;
            }

            .profile-permission-table thead th {
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: .07em;
                color: var(--profile-muted);
                font-weight: 950;
                padding: 0 12px;
                white-space: nowrap;
            }

            .profile-permission-table tbody tr {
                background: #fff;
                box-shadow: var(--profile-shadow-sm);
            }

            .profile-permission-table tbody td {
                padding: 12px;
                border-top: 1px solid var(--profile-border);
                border-bottom: 1px solid var(--profile-border);
                font-size: 13px;
                font-weight: 850;
                white-space: nowrap;
            }

            .profile-permission-table tbody td:first-child {
                border-left: 1px solid var(--profile-border);
                border-top-left-radius: 14px;
                border-bottom-left-radius: 14px;
                color: #0f172a;
            }

            .profile-permission-table tbody td:last-child {
                border-right: 1px solid var(--profile-border);
                border-top-right-radius: 14px;
                border-bottom-right-radius: 14px;
            }

            .profile-check {
                width: 26px;
                height: 26px;
                border-radius: 10px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 1px solid var(--profile-border);
                background: #f8fafc;
                color: #94a3b8;
                font-weight: 950;
            }

            .profile-check.yes {
                background: rgba(147, 194, 28, .16);
                color: #4d6a08;
                border-color: rgba(147, 194, 28, .30);
            }

            .profile-check.no {
                background: rgba(239, 68, 68, .08);
                color: #b91c1c;
                border-color: rgba(239, 68, 68, .18);
            }

            .profile-empty {
                text-align: center;
                padding: 50px 20px;
                color: var(--profile-muted);
                font-weight: 800;
            }

            .profile-icon {
                width: 18px;
                height: 18px;
                display: block;
            }

            @media (max-width: 1100px) {
                .profile-wrap {
                    max-width: 100%;
                    padding: 94px 14px 22px;
                }

                .profile-grid {
                    grid-template-columns: 1fr;
                }

                .profile-hero-inner {
                    align-items: flex-start;
                    flex-direction: column;
                }

                .profile-actions {
                    justify-content: flex-start;
                    width: 100%;
                }

                .profile-info-grid {
                    grid-template-columns: 1fr;
                }

                .profile-search {
                    min-width: 100%;
                    width: 100%;
                }
            }
        </style>
    @endpush
@endonce

@section('content')
    <div class="profile-wrap">

        @if(Session('save_msg'))
            <div class="profile-alert success">
                <span>{{ Session('save_msg') }}</span>
                <button type="button" onclick="this.closest('.profile-alert').remove()">×</button>
            </div>
        @endif

        @if(Session('not_msg'))
            <div class="profile-alert danger">
                <span>{{ Session('not_msg') }}</span>
                <button type="button" onclick="this.closest('.profile-alert').remove()">×</button>
            </div>
        @endif

        <div class="profile-hero">
            <div class="profile-hero-inner">
                <div class="profile-user">
                    <div class="profile-avatar">
                        <img src="{{ $profileImage }}" alt="Profile image">
                    </div>

                    <div>
                        <h1 class="profile-title">{{ $displayName }}</h1>

                        <div class="profile-sub">
                            <span>{{ $user->email }}</span>

                            <span class="profile-badge {{ (int) $user->is_active === 1 ? 'ok' : 'warn' }}">
                                <span class="profile-dot"></span>
                                {{ $statusLabel }}
                            </span>

                            <span class="profile-badge info">
                                <span class="profile-dot"></span>
                                {{ $roleLabel }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="profile-actions">
                    <a href="{{ route('user.photo') }}" class="profile-btn blue">
                        <svg class="profile-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h11v12H4z" />
                        </svg>
                        Change Photo
                    </a>
                </div>
            </div>
        </div>

        <div class="profile-grid">
            <div class="profile-card">
                <div class="profile-card-head">
                    <h3 class="profile-card-title">
                        <svg class="profile-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 21a8 8 0 10-16 0" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11a4 4 0 100-8 4 4 0 000 8z" />
                        </svg>
                        Account Information
                    </h3>
                </div>

                <div class="profile-card-body">
                    <div class="profile-info-grid">
                        <div class="profile-info">
                            <div class="profile-info-label">Email</div>
                            <div class="profile-info-value">{{ $user->email }}</div>
                        </div>

                        <div class="profile-info">
                            <div class="profile-info-label">User Name / Employee ID</div>
                            <div class="profile-info-value">{{ $user->name }}</div>
                        </div>

                        <div class="profile-info">
                            <div class="profile-info-label">Display Name</div>
                            <div class="profile-info-value">{{ $displayName }}</div>
                        </div>

                        <div class="profile-info">
                            <div class="profile-info-label">Created At</div>
                            <div class="profile-info-value">
                                {{ Carbon::parse($user->created_at)->diffForHumans() }}
                            </div>
                        </div>

                        <div class="profile-info">
                            <div class="profile-info-label">Status</div>
                            <div class="profile-info-value">{{ $statusLabel }}</div>
                        </div>

                        <div class="profile-info">
                            <div class="profile-info-label">Role</div>
                            <div class="profile-info-value">{{ $roleLabel }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-card">
                <div class="profile-card-head">
                    <h3 class="profile-card-title">
                        <svg class="profile-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 10V8a5 5 0 0110 0v2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 10h12v11H6z" />
                        </svg>
                        Change Password
                    </h3>
                </div>

                <div class="profile-card-body">
                    <form class="profile-form" action="{{ route('user.change.pass') }}" method="post">
                        @csrf

                        <div class="profile-field">
                            <label class="profile-label">Current Password</label>
                            <input type="password" name="password" class="profile-input" autocomplete="current-password">

                            @if($errors->has('password'))
                                <div class="profile-error">{{ $errors->first('password') }}</div>
                            @endif
                        </div>

                        <div class="profile-field">
                            <label class="profile-label">New Password</label>
                            <input type="password" name="new_password" class="profile-input" autocomplete="new-password">

                            @if($errors->has('new_password'))
                                <div class="profile-error">{{ $errors->first('new_password') }}</div>
                            @endif
                        </div>

                        <div class="profile-field">
                            <label class="profile-label">Confirm Password</label>
                            <input type="password" name="confirm_password" class="profile-input"
                                autocomplete="new-password">

                            @if($errors->has('confirm_password'))
                                <div class="profile-error">{{ $errors->first('confirm_password') }}</div>
                            @endif
                        </div>

                        <div style="display:flex;justify-content:flex-end;margin-top:4px">
                            <button type="submit" class="profile-btn">
                                <svg class="profile-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="2.2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19 21H5a2 2 0 01-2-2V7a2 2 0 012-2h11l5 5v9a2 2 0 01-2 2z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-8H7v8" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 3v4h8" />
                                </svg>
                                Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="profile-card profile-permission-card">
            <div class="profile-card-head">
                <div class="profile-permission-top">
                    <h3 class="profile-card-title">
                        <svg class="profile-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3l8 4v5c0 5-3.5 8.5-8 9-4.5-.5-8-4-8-9V7l8-4z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-5" />
                        </svg>
                        Permissions
                    </h3>

                    <span class="profile-badge info">
                        {{ $permissions->count() }} Modules
                    </span>
                </div>

                <input type="text" id="permissionSearch" class="profile-search" placeholder="Search module...">
            </div>

            <div class="profile-card-body">
                @if($permissions->count())
                    <div class="profile-permission-table-wrap">
                        <table class="profile-permission-table" id="permissionTable">
                            <thead>
                                <tr>
                                    <th>Module</th>
                                    <th>Read</th>
                                    <th>Update</th>
                                    <th>Create</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($permissions as $pre)
                                    <tr>
                                        <td>{{ $pre->item_id }}</td>

                                        <td>
                                            <span class="profile-check {{ $hasPermission($pre->is_read) ? 'yes' : 'no' }}">
                                                {{ $hasPermission($pre->is_read) ? '✓' : '×' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="profile-check {{ $hasPermission($pre->is_update) ? 'yes' : 'no' }}">
                                                {{ $hasPermission($pre->is_update) ? '✓' : '×' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="profile-check {{ $hasPermission($pre->is_add) ? 'yes' : 'no' }}">
                                                {{ $hasPermission($pre->is_add) ? '✓' : '×' }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="profile-check {{ $hasPermission($pre->is_delete) ? 'yes' : 'no' }}">
                                                {{ $hasPermission($pre->is_delete) ? '✓' : '×' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="profile-empty">
                        No permission records found for this user.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@once
    @push('scripts')
        <script>
            (() => {
                "use strict";

                const search = document.getElementById("permissionSearch");
                const table = document.getElementById("permissionTable");

                if (!search || !table) return;

                search.addEventListener("input", function () {
                    const q = String(this.value || "").toLowerCase().trim();
                    const rows = table.querySelectorAll("tbody tr");

                    rows.forEach(row => {
                        const text = String(row.textContent || "").toLowerCase();
                        row.style.display = text.includes(q) ? "" : "none";
                    });
                });
            })();
        </script>
    @endpush
@endonce