<!DOCTYPE html>
<html lang="en" class="antialiased light-layout">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Solar Aspekt">
    <meta name="keywords" content="Photovoltaik, WP, PV, Solar, Warmpumpe, Heizung, Heiztechnique,">
    <meta name="author" content="Solar Aspekt">
    <title>SA-DESK - @yield('title')</title>
    <link rel="apple-touch-icon" href="{{ asset('logo/logo_half.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('logo/logo_half.png')}}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300,500,600" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if(auth()->check())
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="chat-user-id" content="{{ auth()->id() }}">
        <meta name="chat-user-name" content="{{ optional(auth()->user()->employee)->display_name }}">
        <meta name="employee-id" content="{{ auth()->user()->name }}">

    @endif

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/katex.min.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/vendors/css/editors/quill/monokai-sublime.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/editors/quill/quill.snow.css') }}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/dark-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/themes/semi-dark-layout.css') }}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/core/colors/palette-gradient.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css/pages/app-email.css') }}">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">


    <!-- Theme Initialization Script -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme');

            /*
            |--------------------------------------------------------------------------
            | Default theme
            |--------------------------------------------------------------------------
            | White/light mode is default.
            | Dark mode is only used if the user manually selected it before.
            |--------------------------------------------------------------------------
            */
            const shouldUseDark = savedTheme === 'dark';

            document.documentElement.classList.toggle('dark', shouldUseDark);

            document.addEventListener('DOMContentLoaded', function () {
                document.body.classList.toggle('dark-layout', shouldUseDark);
                document.body.classList.toggle('semi-dark-layout', shouldUseDark);
                document.body.classList.toggle('light-layout', !shouldUseDark);

                if (!savedTheme) {
                    localStorage.setItem('theme', 'light');
                }
            });
        })();
    </script>

    <style>
        /* =========================================================
        SA-DESK APP LAYOUT DESIGN SYSTEM
        Light/Dark theme + sidebar + quick sider + chat + toasts
        ========================================================= */

        :root {
            /* Brand */
            --brand-blue: #74b2d4;
            --brand-darkblue: #569ad8;
            --brand-green: #93c21c;
            --brand-lightgreen: #cfe09a;
            --brand-slate: #2c3e50;
            --brand-dark: #1f2937;

            /* Light theme */
            --bg-body: #f8fafc;
            --bg-surface: #ffffff;
            --bg-hover: #f1f5f9;
            --bg-subtle: #f8fafc;
            --bg-active: rgba(147, 194, 28, 0.15);

            --border-color: #e2e8f0;
            --border-light: #f1f5f9;

            --text-main: #1f2937;
            --text-muted: #64748b;
            --text-light: #94a3b8;
            --text-inverse: #ffffff;

            /* Status */
            --color-danger: #ef4444;
            --color-warning: #fbbf24;
            --color-success: #10b981;
            --color-info: #3b82f6;

            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, .05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, .10), 0 2px 4px -2px rgba(0, 0, 0, .10);
            --shadow-soft: 0 4px 20px -2px rgba(0, 0, 0, .05);
            --shadow-float: 0 10px 40px -10px rgba(15, 23, 42, .12);

            /* Header */
            --header-bg: rgba(255, 255, 255, .86);

            /* Sizes */
            --header-height: 64px;
            --left-sidebar-width: 229px;
            --right-sidebar-width: 280px;
            --right-sidebar-collapsed: 80px;
            --mobile-bottom-nav-height: 64px;

            /* Radius */
            --radius-sm: 8px;
            --radius: 12px;
            --radius-lg: 16px;

            /* Animation */
            --transition: .2s ease;
            --transition-layout: .3s ease;
        }

        .dark {
            --bg-body: #0f172a;
            --bg-surface: #1e293b;
            --bg-hover: #334155;
            --bg-subtle: #0f172a;
            --bg-active: rgba(147, 194, 28, .20);

            --border-color: #334155;
            --border-light: #1e293b;

            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-light: #64748b;

            --header-bg: rgba(15, 23, 42, .86);
            --shadow-float: 0 10px 40px -10px rgba(0, 0, 0, .50);
        }

        /* =========================================================
        RESET / BASE
        ========================================================= */

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        * {
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            font-size: 14px;
            display: flex;
            height: 100vh;
            overflow: hidden;
            transition: background-color var(--transition), color var(--transition);
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .font-heading {
            font-family: 'Montserrat', system-ui, sans-serif;
        }

        button,
        input,
        textarea,
        select {
            font-family: inherit;
        }

        button {
            background: none;
            border: 0;
            color: inherit;
            cursor: pointer;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        img {
            max-width: 100%;
            display: block;
        }

        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 999px;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #475569;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* =========================================================
        UTILITIES
        ========================================================= */

        .truncate {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .hidden {
            display: none !important;
        }

        .w-full {
            width: 100%;
        }

        .h-full {
            height: 100%;
        }

        .flex-1 {
            flex: 1;
            min-width: 0;
        }

        .bg-gradient-brand {
            background: linear-gradient(135deg, var(--brand-blue) 0%, var(--brand-green) 100%);
        }

        .text-brand-blue {
            color: var(--brand-blue) !important;
        }

        .text-brand-green {
            color: var(--brand-green) !important;
        }

        .text-muted {
            color: var(--text-muted) !important;
        }

        .badge {
            min-width: 18px;
            height: 18px;
            padding: 0 6px;
            border-radius: 999px;
            background: var(--color-danger);
            color: #ffffff;
            font-size: 10px;
            font-weight: 900;
            line-height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* =========================================================
        OVERLAYS
        ========================================================= */

        .overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .50);
            z-index: 40;
            opacity: 0;
            visibility: hidden;
            transition: opacity var(--transition-layout), visibility var(--transition-layout);
        }

        .overlay.is-active {
            opacity: 1;
            visibility: visible;
        }

        /* =========================================================
        LEFT SIDEBAR
        ========================================================= */

        .sidebar-left {
            position: fixed;
            inset-y: 0;
            left: 0;
            width: var(--left-sidebar-width);
            background: var(--bg-surface);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            z-index: 50;
            transform: translateX(-100%);
            transition: transform var(--transition-layout), width var(--transition-layout), border var(--transition-layout), opacity var(--transition-layout);
        }

        .sidebar-left.is-open {
            transform: translateX(0);
        }

        @media (min-width: 768px) {
            .sidebar-left {
                position: static;
                transform: translateX(0);
                z-index: 20;
                flex-shrink: 0;
            }

            .sidebar-left.collapsed {
                width: 0;
                opacity: 0;
                overflow: hidden;
                border-right-width: 0;
                pointer-events: none;
            }
        }

        .sidebar-header {
            height: var(--header-height);
            display: flex;
            align-items: center;
            padding: 0 24px;
            border-bottom: 1px solid var(--border-color);
            background: var(--bg-surface);
            cursor: pointer;
            flex-shrink: 0;
            transition: background-color var(--transition);
        }

        .sidebar-header:hover {
            background: var(--bg-hover);
        }

        .brand-logo {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 18px;
            font-weight: 900;
            box-shadow: var(--shadow-sm);
            flex-shrink: 0;
        }

        .sidebar-nav {
            padding: 12px;
            overflow-y: auto;
            overflow-x: hidden;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .nav-label {
            margin: 16px 12px 8px;
            color: var(--text-light);
            font-size: 12px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .nav-item {
            width: 100%;
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
            transition: background-color var(--transition), color var(--transition);
        }

        .nav-item:hover {
            background: var(--bg-hover);
        }

        .nav-item.active {
            background: var(--bg-active);
            color: var(--brand-green);
        }

        .nav-item-content {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .submenu {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows var(--transition-layout);
        }

        .submenu.open {
            grid-template-rows: 1fr;
        }

        .submenu-inner {
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 4px;
            padding: 4px 8px 4px 36px;
        }

        .submenu-link {
            padding: 8px 12px;
            border-radius: 6px;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color var(--transition), color var(--transition);
        }

        .submenu-link:hover {
            background: var(--bg-hover);
            color: var(--brand-green);
        }

        .submenu-link.highlight {
            background: rgba(147, 194, 28, .10);
            color: var(--brand-green);
            font-weight: 700;
        }

        .sidebar-footer {
            padding: 2px;
            border-top: 1px solid var(--border-color);
            background: var(--bg-surface);
            flex-shrink: 0;
            position: relative;
        }

        .profile-btn {
            width: 100%;
            padding: 8px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background-color var(--transition);
        }

        .profile-btn:hover {
            background: var(--bg-hover);
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid var(--border-color);
            object-fit: cover;
            flex-shrink: 0;
        }

        /* =========================================================
        MAIN CONTENT
        ========================================================= */

        .main-wrapper {
            flex: 1;
            min-width: 0;
            min-height: 0;
            height: 100vh;
            background: var(--bg-body);
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            padding-bottom: var(--mobile-bottom-nav-height);
        }

        @media (min-width: 768px) {
            .main-wrapper {
                padding-bottom: 0;
            }
        }

        .main-content-scroll {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 24px;
            background: var(--bg-body);
            scroll-behavior: smooth;
        }

        @media (max-width: 767px) {
            .main-content-scroll {
                padding: 16px;
                padding-bottom: 96px;
            }
        }

        /* =========================================================
        TOP HEADER
        ========================================================= */

        .top-header {
            height: var(--header-height);
            padding: 0 16px;
            background: var(--header-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 10;
            flex-shrink: 0;
        }

        @media (min-width: 768px) {
            .top-header {
                padding: 0 24px;
            }
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }

        .icon-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            transition: background-color var(--transition), color var(--transition), transform var(--transition);
        }

        .icon-btn:hover {
            background: var(--bg-hover);
            color: var(--brand-blue);
        }

        .icon-btn.primary {
            width: 36px;
            height: 36px;
            background: var(--brand-green);
            color: #ffffff;
            box-shadow: var(--shadow-sm);
        }

        .icon-btn.primary:hover {
            background: var(--brand-darkblue);
            color: #ffffff;
            transform: scale(1.05);
        }

        .search-wrapper {
            position: relative;
            width: 100%;
            max-width: 400px;
            margin-left: 16px;
            display: none;
        }

        @media (min-width: 640px) {
            .search-wrapper {
                display: block;
            }
        }

        .search-input {
            width: 100%;
            padding: 8px 16px 8px 36px;
            background: var(--bg-hover);
            border: 1px solid transparent;
            border-radius: var(--radius);
            color: var(--text-main);
            font-size: 14px;
            outline: none;
            transition: background-color var(--transition), border-color var(--transition), box-shadow var(--transition);
        }

        .search-input:focus {
            background: var(--bg-surface);
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 2px rgba(116, 178, 212, .20);
        }

        .search-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            pointer-events: none;
        }

        .quick-menu-btn {
            padding: 8px 12px;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 800;
            box-shadow: var(--shadow-sm);
            transition: border-color var(--transition), color var(--transition), background-color var(--transition);
        }

        .quick-menu-btn:hover {
            border-color: var(--brand-blue);
            color: var(--brand-blue);
        }

        /* =========================================================
        RIGHT CHAT SIDEBAR
        ========================================================= */

        .sidebar-right {
            position: fixed;
            inset-y: 0;
            right: 0;
            width: var(--right-sidebar-width);
            background: var(--bg-surface);
            border-left: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            z-index: 50;
            transform: translateX(100%);
            transition: transform var(--transition-layout), width var(--transition-layout);
            flex-shrink: 0;
        }

        .sidebar-right.is-open {
            transform: translateX(0);
        }

        @media (min-width: 768px) {
            .sidebar-right {
                position: static;
                transform: translateX(0);
                z-index: 30;
                width: var(--right-sidebar-collapsed);
            }

            .sidebar-right:hover {
                width: var(--right-sidebar-width);
            }

            .sidebar-right .hide-on-collapse {
                opacity: 0;
                white-space: nowrap;
                transition: opacity var(--transition);
            }

            .sidebar-right:hover .hide-on-collapse {
                opacity: 1;
            }

            .sidebar-right .center-on-collapse {
                margin: 0 auto;
            }

            .sidebar-right:hover .center-on-collapse {
                margin: 0;
            }

            .sidebar-right .fab-expand {
                width: 40px;
                border-radius: 50%;
            }

            .sidebar-right:hover .fab-expand {
                width: 100%;
                border-radius: var(--radius);
            }
        }

        .user-chat-item {
            width: 100%;
            padding: 8px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            border: 1px solid transparent;
            transition: background-color var(--transition), border-color var(--transition), transform var(--transition);
        }

        .user-chat-item:hover {
            background: var(--bg-hover);
        }

        .user-chat-item.has-unread {
            background: rgba(239, 68, 68, .08);
            border-color: rgba(239, 68, 68, .18);
        }

        .user-chat-item.has-unread p:first-child {
            color: var(--color-danger) !important;
        }

        .status-dot {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 12px;
            height: 12px;
            border: 2px solid var(--bg-surface);
            border-radius: 50%;
        }

        .status-dot.online {
            background: var(--color-success);
        }

        .status-dot.offline {
            background: var(--text-light);
        }

        .chat-contact-unread-badge {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            min-width: 20px;
            height: 20px;
            padding: 0 7px;
            border-radius: 999px;
            background: var(--color-danger);
            color: #ffffff;
            font-size: 11px;
            font-weight: 900;
            line-height: 20px;
            text-align: center;
            box-shadow: 0 8px 18px rgba(239, 68, 68, .24);
        }

        .chat-global-badge {
            min-width: 18px;
            height: 18px;
            padding: 0 6px;
            border-radius: 999px;
            background: var(--color-danger);
            color: #ffffff;
            font-size: 10px;
            font-weight: 900;
            line-height: 18px;
            text-align: center;
            box-shadow: 0 8px 18px rgba(239, 68, 68, .30);
            border: 2px solid var(--bg-surface);
            z-index: 5;
        }

        /* =========================================================
        DROPDOWNS
        ========================================================= */

        .dropdown-menu {
            position: absolute;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            box-shadow: var(--shadow-float);
            padding: 8px;
            z-index: 100;
            opacity: 0;
            transform: translateY(10px) scale(.95);
            pointer-events: none;
            transition: opacity var(--transition), transform var(--transition), visibility var(--transition);
        }

        .dropdown-menu.show {
            opacity: 1;
            transform: translateY(0) scale(1);
            pointer-events: auto;
        }

        .dropdown-item {
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            transition: background-color var(--transition), color var(--transition);
        }

        .dropdown-item:hover {
            background: var(--bg-hover);
        }

        .hover-group {
            position: relative;
        }

        .hover-dropdown {
            position: absolute;
            right: 0;
            top: 100%;
            margin-top: 8px;
            width: 200px;
            background: var(--brand-slate);
            color: #ffffff;
            border: 1px solid #374151;
            border-radius: var(--radius);
            box-shadow: var(--shadow-float);
            padding: 8px;
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transform: translateY(8px);
            transition: opacity var(--transition), transform var(--transition), visibility var(--transition);
        }

        .hover-group:hover .hover-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .hover-dropdown-item {
            padding: 10px 12px;
            border-radius: var(--radius-sm);
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            transition: background-color var(--transition), color var(--transition);
        }

        .hover-dropdown-item:hover {
            background: rgba(255, 255, 255, .10);
        }

        /* =========================================================
        QUICK SIDER
        ========================================================= */

        .quick-sider {
            position: fixed;
            top: 0;
            right: 0;
            height: 100%;
            width: 100%;
            max-width: 384px;
            background: var(--brand-slate);
            color: #ffffff;
            z-index: 60;
            transform: translateX(100%);
            display: flex;
            flex-direction: column;
            box-shadow: -10px 0 40px rgba(0, 0, 0, .30);
            transition: transform .3s cubic-bezier(.16, 1, .3, 1);
        }

        .quick-sider.is-open {
            transform: translateX(0);
        }

        .qs-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, .10);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .qs-grid {
            padding: 20px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            overflow-y: auto;
        }

        .qs-tile {
            min-height: 92px;
            padding: 16px;
            background: #314b62;
            border: 1px solid rgba(255, 255, 255, .10);
            border-radius: var(--radius);
            color: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            position: relative;
            transition: transform var(--transition), background-color var(--transition), box-shadow var(--transition), color var(--transition);
        }

        .qs-tile:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, .20);
            color: var(--brand-green);
        }

        .qs-tile i,
        .qs-tile svg {
            width: 24px;
            height: 24px;
        }

        /* =========================================================
        CHAT POPUPS
        ========================================================= */

        .chat-container {
            position: fixed;
            right: 0;
            bottom: var(--mobile-bottom-nav-height);
            width: 100%;
            display: flex;
            gap: 16px;
            align-items: flex-end;
            z-index: 40;
            pointer-events: none;
        }

        @media (min-width: 768px) {
            .chat-container {
                right: 90px;
                bottom: 0;
                width: auto;
                padding: 0 16px;
            }
        }

        .chat-popup {
            width: 100%;
            height: 80vh;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 16px 16px 0 0;
            display: flex;
            flex-direction: column;
            box-shadow: var(--shadow-float);
            transform: translateY(100%);
            transition: transform .3s cubic-bezier(.16, 1, .3, 1);
            pointer-events: auto;
            flex-shrink: 0;
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .chat-popup {
                width: 320px;
                height: 400px;
            }
        }

        .chat-popup.open {
            transform: translateY(0);
        }

        .chat-popup.minimized {
            transform: translateY(calc(100% - 48px));
        }

        .chat-header {
            height: 48px;
            background: var(--bg-surface);
            border-bottom: 1px solid var(--border-color);
            border-radius: 16px 16px 0 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 12px;
            cursor: pointer;
            flex-shrink: 0;
        }

        .chat-header.flash {
            animation: chatHeaderFlash 1s ease-in-out 4;
        }

        @keyframes chatHeaderFlash {

            0%,
            100% {
                background: var(--bg-surface);
            }

            50% {
                background: rgba(239, 68, 68, .14);
            }
        }

        .chat-body {
            flex: 1;
            padding: 16px;
            overflow-y: auto;
            background: var(--bg-subtle);
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .chat-msg {
            max-width: 85%;
            padding: 10px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            line-height: 1.45;
            word-break: break-word;
        }

        .chat-msg.them {
            align-self: flex-start;
            background: var(--bg-hover);
            color: var(--text-main);
            border-bottom-left-radius: 2px;
        }

        .chat-msg.me {
            align-self: flex-end;
            background: var(--bg-active);
            color: var(--text-main);
            border-bottom-right-radius: 2px;
        }

        .chat-footer {
            padding: 12px;
            padding-bottom: env(safe-area-inset-bottom, 12px);
            background: var(--bg-surface);
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .chat-footer input[type="text"] {
            flex: 1;
            min-width: 0;
            background: var(--bg-hover);
            border: 1px solid transparent;
            border-radius: 999px;
            padding: 8px 12px;
            color: var(--text-main);
            outline: none;
            font-size: 14px;
            transition: border-color var(--transition), box-shadow var(--transition), background-color var(--transition);
        }

        .chat-footer input[type="text"]:focus {
            background: var(--bg-surface);
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 2px rgba(116, 178, 212, .20);
        }

        /* =========================================================
        CHAT TOAST NOTIFICATIONS - TOP RIGHT
        ========================================================= */

        .chat-toast-wrap {
            position: fixed;
            top: 84px;
            right: 24px;
            bottom: auto;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 10px;
            pointer-events: none;
        }

        @media (max-width: 767px) {
            .chat-toast-wrap {
                top: 76px;
                left: 12px;
                right: 12px;
                bottom: auto;
                align-items: stretch;
            }
        }

        .chat-toast {
            width: min(360px, 100%);
            padding: 12px;
            background: var(--bg-surface);
            color: var(--text-main);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-float);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            cursor: pointer;
            pointer-events: auto;
            animation: chatToastInTop .22s ease-out;
        }

        @keyframes chatToastInTop {
            from {
                opacity: 0;
                transform: translateY(-12px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .chat-toast-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--border-color);
            flex: 0 0 auto;
        }

        .chat-toast-title {
            margin: 0 0 4px;
            color: var(--text-main);
            font-size: 13px;
            font-weight: 900;
        }

        .chat-toast-message {
            margin: 0;
            color: var(--text-muted);
            font-size: 12px;
            line-height: 1.45;
        }

        .chat-toast-close {
            margin-left: auto;
            color: var(--text-light);
            width: 24px;
            height: 24px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: background-color var(--transition), color var(--transition);
        }

        .chat-toast-close:hover {
            background: var(--bg-hover);
            color: var(--color-danger);
        }

        /* =========================================================
        MOBILE BOTTOM NAV
        ========================================================= */

        .mobile-nav {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            background: var(--bg-surface);
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 24px calc(8px + env(safe-area-inset-bottom, 0px));
            z-index: 40;
        }

        @media (min-width: 768px) {
            .mobile-nav {
                display: none;
            }
        }

        .mobile-nav-btn {
            color: var(--text-muted);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            transition: color var(--transition);
        }

        .mobile-nav-btn:hover,
        .mobile-nav-btn.active {
            color: var(--brand-blue);
        }

        .mobile-nav-btn span {
            font-size: 10px;
            font-weight: 900;
        }

        .fab-container {
            position: relative;
            top: -16px;
        }

        .fab-main {
            width: 48px;
            height: 48px;
            background: var(--brand-green);
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(147, 194, 28, .40);
            transition: transform var(--transition), background-color var(--transition);
        }

        .fab-main:hover {
            transform: scale(1.05);
            background: var(--brand-darkblue);
        }

        /* =========================================================
        ICON SIZES
        ========================================================= */

        .icon-sm {
            width: 14px;
            height: 14px;
        }

        .icon-md {
            width: 16px;
            height: 16px;
        }

        .icon-lg {
            width: 20px;
            height: 20px;
        }

        .icon-xl {
            width: 24px;
            height: 24px;
        }

        /* =========================================================
            RIGHT SIDEBAR CURRENT USER MENU
            ========================================================= */

        .right-user-footer {
            position: relative;
            padding: 12px 8px;
            border-top: 1px solid var(--border-color);
            background: var(--bg-surface);
            flex-shrink: 0;
        }

        .right-user-btn {
            width: 100%;
            min-height: 48px;
            padding: 8px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-main);
            transition: background-color var(--transition), border-color var(--transition);
        }

        .right-user-btn:hover {
            background: var(--bg-hover);
        }

        .right-user-avatar-wrap {
            position: relative;
            flex-shrink: 0;
        }



        .right-user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--border-color);
            background: var(--bg-hover);
        }

        .right-user-online-dot {
            position: absolute;
            right: 1px;
            bottom: 1px;
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: var(--color-success);
            border: 2px solid var(--bg-surface);
        }

        .right-user-name {
            margin: 0;
            font-size: 13px;
            font-weight: 900;
            color: var(--text-main);
        }

        .right-user-role {
            margin: 2px 0 0;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
        }

        .right-user-dropdown {
            right: 12px;
            bottom: 72px;
            width: 240px;
            background-color: var(--brand-slate);
            color: #ffffff;
        }

        .right-user-dropdown-head {
            padding: 10px 12px;
            border-bottom: 1px solid #374151;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .right-user-dropdown-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, .18);
        }

        .right-user-dropdown-name {
            margin: 0;
            font-size: 14px;
            font-weight: 900;
            color: #ffffff;
        }

        .right-user-dropdown-role {
            margin: 2px 0 0;
            font-size: 12px;
            color: #9ca3af;
        }

        /* =========================================================
            LEFT SIDEBAR FOOTER MENU - FIXED
            ========================================================= */


        .sidebar-nav {
            min-height: 0;
        }

        .sidebar-footer {
            position: relative;
            z-index: 99999 !important;
            overflow: visible !important;
        }

        .sidebar-profile-menu {
            position: absolute;
            left: 16px;
            bottom: calc(100% + 12px);
            width: 240px;
            background: var(--brand-slate);
            color: #ffffff;
            border: 1px solid #374151;
            border-radius: 16px;
            box-shadow: 0 18px 45px rgba(0, 0, 0, .32);
            padding: 8px;
            z-index: 999999 !important;

            display: none;
            opacity: 0;
            transform: translateY(10px) scale(.96);
            transition: opacity .18s ease, transform .18s ease;
        }

        .sidebar-profile-menu.is-open {
            display: block;
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .sidebar-profile-head {
            padding: 10px 12px;
            border-bottom: 1px solid #374151;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-profile-head img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, .18);
        }

        .sidebar-profile-name {
            margin: 0;
            color: #ffffff;
            font-size: 14px;
            font-weight: 900;
        }

        .sidebar-profile-role {
            margin: 2px 0 0;
            color: #9ca3af;
            font-size: 12px;
        }

        .sidebar-profile-link {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            color: #ffffff !important;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            text-align: left;
            background: transparent;
            border: 0;
            transition: background-color .18s ease, color .18s ease;
        }

        .sidebar-profile-link:hover {
            background: rgba(255, 255, 255, .10);
            color: #ffffff !important;
        }

        .sidebar-profile-link.danger {
            color: #f87171 !important;
        }

        .sidebar-profile-link.danger:hover {
            background: rgba(239, 68, 68, .14);
            color: #fca5a5 !important;
        }

        @media (max-width: 767px) {
            .sidebar-profile-menu {
                left: 12px;
                right: 12px;
                width: auto;
            }
        }

        @media (min-width: 768px) {
            .sidebar-right:not(:hover) .right-user-btn {
                justify-content: center;
                padding-left: 0;
                padding-right: 0;
            }

            .sidebar-right:not(:hover) .right-user-avatar {
                width: 40px;
                height: 40px;
            }
        }

        @media (max-width: 767px) {
            .right-user-dropdown {
                right: 12px;
                left: 12px;
                width: auto;
            }
        }

        /* =========================================================
        MOBILE FIXES
        ========================================================= */

        @media (max-width: 639px) {
            .hide-mobile {
                display: none !important;
            }
        }

        @media (max-width: 767px) {

            .sidebar-left,
            .sidebar-right {
                box-shadow: var(--shadow-float);
            }

            .sidebar-right {
                width: min(320px, 88vw);
            }

            .chat-popup {
                height: 82vh;
                height: 82dvh;
            }
        }
    </style>

    <style>
        /* Banner / Toast Container */
        .oc-toast-wrap {
            position: fixed;
            top: 80px;
            /* Below header */
            right: 20px;
            z-index: 1099999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .oc-toast {
            pointer-events: auto;
            width: 350px;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            box-shadow: var(--shadow-float);
            padding: 14px;
            display: flex;
            gap: 12px;
            align-items: flex-start;
            animation: ocToastIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes ocToastIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .oc-toast-ic {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }

        .oc-toast-ic.ok {
            background: #ecfdf5;
            color: #10b981;
        }

        .oc-toast-ic.bad {
            background: #fef2f2;
            color: #ef4444;
        }

        .oc-toast-ic.info {
            background: #eff6ff;
            color: #3b82f6;
        }

        .oc-toast-ttl {
            font-weight: 800;
            font-size: 13px;
            margin: 0;
            color: var(--text-main);
        }

        .oc-toast-msg {
            font-size: 12px;
            color: var(--text-muted);
            margin: 4px 0 0 0;
            line-height: 1.4;
        }

        .user-chat-item.is-group {
            background: rgba(116, 178, 212, .055);
        }

        .user-chat-item.is-group:hover {
            background: rgba(116, 178, 212, .11);
        }

        .chat-group-mini-icon {
            position: absolute;
            right: -3px;
            bottom: -3px;
            width: 17px;
            height: 17px;
            border-radius: 999px;
            background: var(--brand-blue);
            color: #ffffff;
            border: 2px solid var(--bg-surface);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .chat-group-mini-icon svg {
            width: 10px;
            height: 10px;
        }

        .chat-group-label {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: var(--brand-blue);
            font-size: 10px;
            font-weight: 850;
            max-width: 155px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>

    <style>
        /* =========================================================
        DARK MODE LEFT SIDEBAR VISIBILITY FIX
        Works with custom sidebar + included admin.layouts.sidebar
        ========================================================= */

        html.dark .sidebar-left,
        html.dark #leftSidebar {
            background: #111827 !important;
            border-right-color: #334155 !important;
            color: #f8fafc !important;
        }

        html.dark .sidebar-left .sidebar-header,
        html.dark .sidebar-left .sidebar-footer {
            background: #111827 !important;
            border-color: #334155 !important;
        }

        html.dark .sidebar-left *,
        html.dark #leftSidebar * {
            border-color: rgba(148, 163, 184, .22);
        }

        /* Main sidebar links */
        html.dark .sidebar-left a,
        html.dark .sidebar-left button,
        html.dark .sidebar-left .nav-item,
        html.dark .sidebar-left .nav-link,
        html.dark .sidebar-left .menu-item,
        html.dark .sidebar-left .menu-title,
        html.dark .sidebar-left span,
        html.dark .sidebar-left p,
        html.dark .sidebar-left small,
        html.dark .sidebar-left .font-heading {
            color: #e5e7eb !important;
        }

        /* Muted / helper texts */
        html.dark .sidebar-left .text-muted,
        html.dark .sidebar-left .nav-label,
        html.dark .sidebar-left .submenu-link,
        html.dark .sidebar-left .menu-sub a,
        html.dark .sidebar-left .menu-sub .menu-title {
            color: #cbd5e1 !important;
        }

        /* Icons */
        html.dark .sidebar-left i,
        html.dark .sidebar-left svg,
        html.dark .sidebar-left .feather,
        html.dark .sidebar-left [data-lucide] {
            color: #dbeafe !important;
            stroke: currentColor !important;
        }

        .sa-report-mini-details {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 7px;
            font-size: 11px;
            color: #64748b;
            font-weight: 800;
        }

        .sa-report-mini-details span {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            max-width: 100%;
            padding: 3px 7px;
            border-radius: 999px;
            background: #f1f5f9;
            color: #475569;
        }

        .sa-report-mini-details svg {
            width: 12px;
            height: 12px;
            stroke-width: 2.4;
        }

        html.dark .sa-report-mini-details span {
            background: rgba(148, 163, 184, .14);
            color: #cbd5e1;
        }

        /* Hover states */
        html.dark .sidebar-left a:hover,
        html.dark .sidebar-left button:hover,
        html.dark .sidebar-left .nav-item:hover,
        html.dark .sidebar-left .nav-link:hover,
        html.dark .sidebar-left .submenu-link:hover,
        html.dark .sidebar-left .menu-item:hover,
        html.dark .sidebar-left li:hover>a {
            background: rgba(116, 178, 212, .14) !important;
            color: #ffffff !important;
        }

        /* Active menu item */
        html.dark .sidebar-left .active,
        html.dark .sidebar-left .nav-item.active,
        html.dark .sidebar-left .nav-link.active,
        html.dark .sidebar-left .submenu-link.highlight,
        html.dark .sidebar-left li.active>a {
            background: rgba(147, 194, 28, .18) !important;
            color: #cfe09a !important;
        }

        /* Active icons */
        html.dark .sidebar-left .active i,
        html.dark .sidebar-left .active svg,
        html.dark .sidebar-left .nav-item.active i,
        html.dark .sidebar-left .nav-item.active svg,
        html.dark .sidebar-left .nav-link.active i,
        html.dark .sidebar-left .nav-link.active svg {
            color: #cfe09a !important;
        }

        /* Submenu background */
        html.dark .sidebar-left .submenu-inner,
        html.dark .sidebar-left .menu-sub {
            background: transparent !important;
        }

        /* Profile footer */
        html.dark .sidebar-left .profile-btn {
            background: transparent !important;
            color: #f8fafc !important;
        }

        html.dark .sidebar-left .profile-btn:hover {
            background: rgba(255, 255, 255, .08) !important;
        }

        /* User name in sidebar footer */
        html.dark .sidebar-left .profile-btn p,
        html.dark .sidebar-left .profile-btn span {
            color: #f8fafc !important;
        }

        /* Profile dropdown already dark, but force readable text */
        html.dark .sidebar-profile-menu,
        html.dark #sidebarProfileMenu {
            background: #1f2937 !important;
            border-color: #374151 !important;
            color: #ffffff !important;
        }

        html.dark .sidebar-profile-menu *,
        html.dark #sidebarProfileMenu * {
            color: inherit;
        }

        html.dark .sidebar-profile-link {
            color: #ffffff !important;
        }

        html.dark .sidebar-profile-link:hover {
            background: rgba(255, 255, 255, .10) !important;
            color: #ffffff !important;
        }

        .chat-msg-sender {
            font-weight: bold;
            color: #74b2d3;
        }

        /* Keep danger logout red */
        html.dark .sidebar-profile-link.danger,
        html.dark .sidebar-profile-link.danger i,
        html.dark .sidebar-profile-link.danger svg {
            color: #f87171 !important;
        }

        /* Brand text */
        html.dark .sidebar-header .font-heading {
            color: #ffffff !important;
        }

        html.dark .sidebar-header span {
            color: #cbd5e1 !important;
        }

        /* If old Vuexy vertical menu classes exist inside included sidebar */
        html.dark .sidebar-left .main-menu-content,
        html.dark .sidebar-left .navigation,
        html.dark .sidebar-left .navigation-main {
            background: transparent !important;
        }

        html.dark .sidebar-left .navigation li a,
        html.dark .sidebar-left .navigation-main li a {
            color: #e5e7eb !important;
        }

        html.dark .sidebar-left .navigation li a span,
        html.dark .sidebar-left .navigation-main li a span {
            color: #e5e7eb !important;
        }

        html.dark .sidebar-left .navigation li a:hover,
        html.dark .sidebar-left .navigation-main li a:hover {
            background: rgba(116, 178, 212, .14) !important;
            color: #ffffff !important;
        }

        html.dark .sidebar-left .navigation li.active>a,
        html.dark .sidebar-left .navigation-main li.active>a {
            background: rgba(147, 194, 28, .18) !important;
            color: #cfe09a !important;
        }
    </style>

    <style>
        /* =========================================================
        COLLAPSIBLE SIDEBAR GROUPS
        ========================================================= */

        .sa-sidebar-section {
            position: relative;
            margin: 1px 0;
        }

        .sa-section-toggle {
            width: 100%;
            min-height: 34px;
            padding: 8px 10px;
            border-radius: 10px;
            color: var(--text-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            font-size: 11px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .055em;
            transition: background-color var(--transition), color var(--transition);
        }

        .sa-section-toggle:hover {
            background: var(--bg-hover);
            color: var(--brand-blue);
        }

        .sa-section-title-wrap {
            min-width: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .sa-section-title {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sa-section-right {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            flex-shrink: 0;
        }

        .sa-section-count {
            min-width: 20px;
            height: 18px;
            padding: 0 6px;
            border-radius: 999px;
            background: rgba(116, 178, 212, .12);
            color: var(--brand-blue);
            border: 1px solid rgba(116, 178, 212, .18);
            font-size: 10px;
            font-weight: 950;
            line-height: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .sa-section-chevron {
            transition: transform var(--transition-layout);
        }

        .sa-sidebar-section.is-open .sa-section-chevron {
            transform: rotate(180deg);
        }

        .sa-section-body {
            display: grid;
            grid-template-rows: 0fr;
            transition: grid-template-rows var(--transition-layout);
        }

        .sa-sidebar-section.is-open .sa-section-body {
            grid-template-rows: 1fr;
        }

        .sa-section-body-inner {
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sa-sidebar-section.is-open .sa-section-body-inner {
            overflow: visible;
        }

        .sa-sidebar-section.is-collapsed .sa-section-body-inner {
            overflow: hidden;
        }

        /* Tooltip shown when the whole group is collapsed */
        .sa-section-tooltip {
            position: absolute;
            left: calc(100% + 12px);
            top: 0;
            width: 260px;
            max-height: 360px;
            overflow-y: auto;
            padding: 12px;
            border-radius: 14px;
            background: var(--brand-slate);
            color: #ffffff;
            box-shadow: var(--shadow-float);
            border: 1px solid rgba(255, 255, 255, .10);
            z-index: 999999;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateX(-6px);
            transition: opacity .18s ease, transform .18s ease, visibility .18s ease;
            text-transform: none;
            letter-spacing: 0;
        }

        .sa-section-tooltip strong {
            display: block;
            margin-bottom: 8px;
            color: #ffffff;
            font-size: 13px;
            font-weight: 950;
        }

        .sa-section-tooltip span {
            display: block;
            padding: 5px 0;
            color: #e5e7eb;
            font-size: 12px;
            font-weight: 750;
            line-height: 1.35;
            border-top: 1px solid rgba(255, 255, 255, .08);
        }

        .sa-sidebar-section.is-collapsed>.sa-section-toggle:hover .sa-section-tooltip {
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }

        /* Hide old nav label spacing if still used somewhere */
        .sa-sidebar-section+.nav-label {
            margin-top: 10px;
        }

        /* Active/open visual */
        .sa-sidebar-section.is-open>.sa-section-toggle {
            color: var(--brand-blue);
        }

        html.dark .sa-section-toggle {
            color: #cbd5e1;
        }

        html.dark .sa-section-toggle:hover,
        html.dark .sa-sidebar-section.is-open>.sa-section-toggle {
            background: rgba(116, 178, 212, .14);
            color: #bfdbfe;
        }

        html.dark .sa-section-count {
            background: rgba(116, 178, 212, .18);
            color: #bfdbfe;
            border-color: rgba(191, 219, 254, .20);
        }

        html.dark .sa-section-tooltip {
            background: #1f2937;
            border-color: #374151;
        }

        /* Mobile: tooltip is not needed because sidebar has enough width */
        @media (max-width: 767px) {
            .sa-section-tooltip {
                display: none;
            }
        }
    </style>

    <style>
        /* =========================================================
        COMPACT RIGHT CHAT SIDEBAR + REALTIME ONLINE STATUS
        ========================================================= */

        .sidebar-right {
            --chat-avatar-size: 34px;
            --chat-item-height: 44px;
        }

        .sidebar-right .sidebar-nav,
        .sidebar-right #realChatContactList,
        .sidebar-right .chat-contact-list,
        .sidebar-right .real-chat-contact-list {
            padding: 6px !important;
            gap: 2px !important;
            overflow-y: auto;
            overflow-x: hidden;
            flex: 1;
            min-height: 0;
        }

        .user-chat-item {
            width: 100%;
            min-height: var(--chat-item-height);
            padding: 5px 7px !important;
            border-radius: 10px !important;
            display: flex;
            align-items: center;
            gap: 8px !important;
            position: relative;
            border: 1px solid transparent;
            background: transparent;
            transition:
                background-color .16s ease,
                border-color .16s ease,
                transform .16s ease;
        }

        .user-chat-item:hover {
            background: var(--bg-hover);
        }

        .user-chat-item.is-online {
            background: rgba(16, 185, 129, .045);
        }

        .user-chat-item.is-online:hover {
            background: rgba(16, 185, 129, .09);
        }

        .user-chat-item.is-offline {
            opacity: .72;
        }

        .user-chat-item.has-unread {
            background: rgba(239, 68, 68, .08);
            border-color: rgba(239, 68, 68, .18);
            opacity: 1;
        }

        .user-chat-item.has-unread .chat-contact-name {
            color: var(--color-danger) !important;
        }

        .chat-avatar-wrap {
            width: var(--chat-avatar-size);
            height: var(--chat-avatar-size);
            flex: 0 0 var(--chat-avatar-size);
            position: relative;
        }

        .chat-avatar-img {
            width: var(--chat-avatar-size);
            height: var(--chat-avatar-size);
            border-radius: 999px;
            object-fit: cover;
            border: 1px solid var(--border-color);
            background: var(--bg-hover);
        }

        .status-dot {
            position: absolute;
            right: -1px;
            bottom: -1px;
            width: 11px;
            height: 11px;
            border: 2px solid var(--bg-surface);
            border-radius: 999px;
            box-shadow: 0 0 0 1px rgba(15, 23, 42, .08);
        }

        .status-dot.online {
            background: #10b981 !important;
            box-shadow:
                0 0 0 2px var(--bg-surface),
                0 0 0 4px rgba(16, 185, 129, .20);
        }

        .status-dot.offline {
            background: #94a3b8 !important;
        }

        .chat-contact-body {
            flex: 1;
            min-width: 0;
            text-align: left;
        }

        .chat-contact-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            min-width: 0;
        }

        .chat-contact-name {
            margin: 0;
            color: var(--text-main);
            font-size: 12px;
            font-weight: 850;
            line-height: 1.1;
            max-width: 145px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-contact-meta {
            margin: 2px 0 0;
            color: var(--text-muted);
            font-size: 10px;
            font-weight: 700;
            line-height: 1.1;
            max-width: 145px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-online-label {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: #10b981;
            font-size: 10px;
            font-weight: 900;
        }

        .chat-offline-label {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            color: var(--text-light);
            font-size: 10px;
            font-weight: 800;
        }

        .chat-contact-unread-badge {
            position: static !important;
            transform: none !important;
            min-width: 18px;
            height: 18px;
            padding: 0 6px;
            border-radius: 999px;
            background: var(--color-danger);
            color: #ffffff;
            font-size: 10px;
            font-weight: 950;
            line-height: 18px;
            text-align: center;
            flex-shrink: 0;
        }

        /* collapsed right sidebar: only avatar visible */
        @media (min-width: 768px) {
            .sidebar-right:not(:hover) .user-chat-item {
                justify-content: center;
                padding: 5px 0 !important;
                gap: 0 !important;
            }

            .sidebar-right:not(:hover) .chat-contact-body,
            .sidebar-right:not(:hover) .chat-contact-unread-badge {
                display: none !important;
            }

            .sidebar-right:not(:hover) .chat-avatar-wrap {
                margin: 0 auto;
            }
        }

        html.dark .user-chat-item.is-online {
            background: rgba(16, 185, 129, .08);
        }

        html.dark .user-chat-item.is-online:hover {
            background: rgba(16, 185, 129, .13);
        }


        .chat-date-divider {
            align-self: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 8px auto 2px;
            padding: 5px 11px;
            border-radius: 999px;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 900;
            box-shadow: var(--shadow-sm);
        }

        .chat-msg-time {
            font-size: 10px;
            color: var(--text-light);
            margin-top: 4px;
            text-align: right;
        }
    </style>

    <style>
        /* =========================================================
        QUICK MENU ONLY FIX
        Keeps the rest of the layout untouched.
        ========================================================= */

        .quick-menu-group {
            display: inline-flex;
            align-items: center;
            flex-shrink: 0;
        }

        .quick-menu-btn {
            min-width: 134px;
            height: 40px;
            padding: 0 13px 0 10px !important;
            border-radius: 999px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 9px !important;
            white-space: nowrap;
            line-height: 1;
        }

        .quick-menu-btn-icon {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            background: rgba(116, 178, 212, .12);
            color: var(--brand-blue);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .quick-menu-btn-text {
            display: inline-block;
            font-size: 12px;
            font-weight: 950;
            text-transform: uppercase;
            letter-spacing: .055em;
            white-space: nowrap;
        }

        .quick-menu-dropdown {
            width: 230px !important;
            padding: 8px !important;
            border-radius: 16px !important;
        }

        .quick-menu-dropdown .hover-dropdown-item {
            min-height: 40px;
            justify-content: flex-start;
            white-space: nowrap;
        }

        .quick-sider {
            width: min(460px, 100vw) !important;
            max-width: 460px !important;
            z-index: 120000 !important;
        }

        #quickSiderOverlay.is-active {
            z-index: 119999 !important;
        }

        .qs-content-modern .qs-grid {
            align-items: stretch;
        }

        .qs-content-modern .qs-tile {
            width: 100%;
            min-width: 0;
            overflow: visible;
        }

        .qs-content-modern .qs-tile span:not(.badge):not(.chat-global-badge) {
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 767px) {
            .quick-menu-btn {
                min-width: 40px;
                width: 40px;
                padding: 0 !important;
            }

            .quick-menu-btn-text {
                display: none;
            }
        }
    </style>

<style>
    /* =========================================================
       LEFT SIDEBAR EDGE COLLAPSE BUTTON
    ========================================================= */

    @media (min-width: 768px) {
        #leftSidebar {
            position: relative;
        }

        .sa-left-sidebar-edge-toggle {
            position: fixed;
            top: 92px;
            left: calc(var(--left-sidebar-width) - 15px);
            width: 34px;
            height: 34px;
            border-radius: 999px;
            z-index: 100000;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-surface);
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            box-shadow: 0 14px 34px rgba(15, 23, 42, .16);
            opacity: 0;
            transform: scale(.88);
            transition:
                opacity .18s ease,
                transform .18s ease,
                left var(--transition-layout),
                background-color .18s ease,
                color .18s ease,
                border-color .18s ease;
        }

        .sa-left-sidebar-edge-toggle::before {
            content: "";
            position: absolute;
            inset: -22px -12px;
            border-radius: 999px;
        }

        #leftSidebar:hover+.sa-left-sidebar-edge-toggle,
        .sa-left-sidebar-edge-toggle:hover,
        .sa-left-sidebar-edge-toggle:focus-visible {
            opacity: 1;
            transform: scale(1);
        }

        .sa-left-sidebar-edge-toggle:hover {
            background: var(--brand-green);
            color: #ffffff;
            border-color: var(--brand-green);
            box-shadow: 0 18px 42px rgba(147, 194, 28, .28);
        }

        .sa-left-sidebar-edge-toggle svg {
            width: 16px;
            height: 16px;
            stroke-width: 3;
            position: relative;
            z-index: 2;
        }

        body.sa-left-sidebar-collapsed .sa-left-sidebar-edge-toggle {
            left: 12px;
            opacity: .9;
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-green));
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 18px 42px rgba(116, 178, 212, .30);
        }

        body.sa-left-sidebar-collapsed .sa-left-sidebar-edge-toggle:hover {
            opacity: 1;
            transform: scale(1.06);
        }

        .sa-left-sidebar-edge-toggle .sa-icon-open {
            display: block;
        }

        .sa-left-sidebar-edge-toggle .sa-icon-closed {
            display: none;
        }

        body.sa-left-sidebar-collapsed .sa-left-sidebar-edge-toggle .sa-icon-open {
            display: none;
        }

        body.sa-left-sidebar-collapsed .sa-left-sidebar-edge-toggle .sa-icon-closed {
            display: block;
        }

        html.dark .sa-left-sidebar-edge-toggle {
            background: #111827;
            border-color: #334155;
            color: #e5e7eb;
        }

        html.dark .sa-left-sidebar-edge-toggle:hover,
        html.dark body.sa-left-sidebar-collapsed .sa-left-sidebar-edge-toggle {
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-green));
            color: #ffffff;
            border-color: transparent;
        }
    }

    @media (max-width: 767px) {
        .sa-left-sidebar-edge-toggle {
            display: none !important;
        }
    }
</style>
    @yield('style')
    @stack('style')

    @vite(['resources/js/bootstrap.js', 'resources/js/notification.js', 'resources/js/chat.js'])


</head>

@php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

$authUser = auth()->user();
$employeeId = $authUser?->name;

$employee = $employeeId
    ? DB::table('employees')->where('id', $employeeId)->first()
    : null;

$currentUserName = trim(
    ($employee->name ?? '') . ' ' . ($employee->lastname ?? '')
);

if (!$currentUserName) {
    $currentUserName = $employee->display_name
        ?? $authUser?->name
        ?? $authUser?->email
        ?? 'Benutzer';
}

$currentUserRole = $authUser?->role
    ? ucfirst($authUser->role)
    : 'Benutzer';

$currentUserImage = null;

if (!empty($authUser?->image)) {
    $currentUserImage = asset('images/user/' . $authUser->image);
} elseif (!empty($employee?->image)) {
    $currentUserImage = asset('images/employee/' . $employee->image);
} else {
    $currentUserImage = 'https://ui-avatars.com/api/?name='
        . urlencode($currentUserName)
        . '&background=74b2d4&color=fff';
}

$profileUrl = Route::has('profile.edit')
    ? route('profile.edit')
    : url('/user');

$chatUrl = Route::has('chats.view')
    ? route('chats.view', $employeeId)
    : '#';

$logoutUrl = Route::has('logout')
    ? route('logout')
    : url('/logout');
$createLinks = [
    'inquiry' => Route::has('inquiry.create')
        ? route('inquiry.create')
        : url('/inquiry_create'),

    'customer' => url('/new_lead_create'),

    'brand' => Route::has('brand.index')
        ? route('brand.index')
        : url('/brand'),

    'distributor' => Route::has('distributors.index')
        ? route('distributors.index')
        : url('/distributors'),

    'product' => Route::has('product.create')
        ? route('product.create')
        : url('/product_create'),

    'employee' => Route::has('emp.create')
        ? route('emp.create')
        : url('/emp_create'),
];
@endphp

<style>
    /* Detailed Search Dropdown Styles */
    .search-results-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        left: 0;
        right: 0;
        background: var(--bg-surface);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-float);
        max-height: 600px;
        overflow-y: auto;
        z-index: 9999;
        display: none;
        padding: 12px;
    }

    .search-results-dropdown.is-active {
        display: block;
    }

    .search-item {
        display: flex;
        padding: 12px;
        border-radius: var(--radius);
        gap: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        margin-bottom: 4px;
    }

    .search-item:hover,
    .search-item.is-selected {
        background: var(--bg-hover);
        border-color: var(--brand-blue);
    }

    .search-item-main-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        object-fit: cover;
        background: var(--bg-body);
        flex-shrink: 0;
    }

    .search-item-body {
        flex: 1;
        min-width: 0;
    }

    .search-item-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 4px;
    }

    .search-item-title {
        font-weight: 800;
        font-size: 15px;
        color: var(--text-main);
    }

    .search-item-type-badge {
        font-size: 10px;
        padding: 2px 8px;
        border-radius: 6px;
        background: var(--bg-body);
        color: var(--text-muted);
        font-weight: 900;
        text-transform: uppercase;
    }

    .search-item-info-row {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 4px;
    }

    .search-item-info-row span {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Layout for the Search Items */
    .search-item {
        position: relative;
        display: flex;
        padding: 14px;
        border-radius: var(--radius);
        gap: 16px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        margin-bottom: 6px;
        background: var(--bg-surface);
    }

    .search-item:hover {
        background: var(--bg-hover);
        border-color: var(--brand-blue);
        transform: translateY(-1px);
    }

    /* Avatar and Type Icon Wrapper */
    .search-avatar-wrapper {
        position: relative;
        flex-shrink: 0;
    }

    .search-item-main-avatar {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        object-fit: cover;
        background: var(--bg-body);
        border: 1px solid var(--border-color);
    }

    .search-type-icon-badge {
        position: absolute;
        bottom: -4px;
        right: -4px;
        width: 22px;
        height: 22px;
        background: var(--brand-blue);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid var(--bg-surface);
        box-shadow: var(--shadow-sm);
    }

    /* Detail Styling */
    .search-item-title {
        font-weight: 800;
        font-size: 15px;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .search-item-type-text {
        font-size: 11px;
        color: var(--text-light);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .search-item-info-row {
        font-size: 12px;
        color: var(--text-muted);
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 4px;
    }

    .search-item-info-row span {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    /* Participant Avatars */
    .participant-stack {
        display: flex;
        align-items: center;
        margin-top: 8px;
    }

    .participant-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 2px solid var(--bg-surface);
        margin-left: -8px;
    }

    .sa-create-dropdown {
        width: 220px;
    }

    .quick-sider {
        max-width: 460px;
        background: var(--brand-slate);
        color: #ffffff;
        z-index: 1200;
    }

    .qs-content-modern {
        flex: 1;
        min-height: 0;
        overflow-y: auto;
        padding: 18px;
    }

    .qs-content-modern .qs-grid {
        padding: 0;
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        overflow: visible;
    }

    .qs-grid-secondary {
        margin-top: 14px;
    }

    .qs-content-modern .qs-tile {
        min-height: 92px;
        padding: 14px 10px;
        background: #314b62;
        border: 1px solid rgba(255, 255, 255, .10);
        border-radius: 16px;
        color: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        position: relative;
        text-align: center;
        text-decoration: none !important;
        transition: transform .18s ease, background-color .18s ease, border-color .18s ease, box-shadow .18s ease;
    }

    .qs-content-modern .qs-tile:hover {
        transform: translateY(-3px);
        background: #3a5870;
        border-color: rgba(116, 178, 212, .45);
        color: var(--brand-green);
        box-shadow: 0 10px 24px rgba(0, 0, 0, .22);
    }

    .qs-content-modern .qs-tile svg {
        width: 24px;
        height: 24px;
    }

    .qs-content-modern .qs-tile span {
        font-size: 12px;
        font-weight: 800;
        line-height: 1.2;
    }

    .qs-has-sub {
        position: relative;
        min-width: 0;
    }

    .qs-has-sub .qs-toggle {
        width: 100%;
    }

    .qs-caret {
        position: absolute;
        right: 9px;
        bottom: 9px;
        width: 14px !important;
        height: 14px !important;
        color: #9ca3af;
        transition: transform .18s ease;
    }

    .qs-toggle[aria-expanded="true"] .qs-caret {
        transform: rotate(180deg);
    }

    .qs-sub {
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        min-width: 240px;
        max-width: min(360px, 88vw);
        padding: 8px;
        background: #26394c;
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 16px;
        box-shadow: 0 18px 45px rgba(0, 0, 0, .35);
        z-index: 3000;
    }

    .qs-sub-item {
        width: 100%;
        min-height: 40px;
        padding: 10px 11px;
        border-radius: 12px;
        color: #ffffff !important;
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 12px;
        font-weight: 800;
        text-decoration: none !important;
        transition: background .18s ease, color .18s ease;
    }

    .qs-sub-item:hover {
        background: rgba(255, 255, 255, .10);
        color: var(--brand-green) !important;
    }

    .qs-sub-department {
        min-width: 320px;
        max-height: 390px;
        overflow-y: auto;
    }

    .qs-sub-department .js-dept-list {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .qs-sub-department .dept-link {
        justify-content: space-between;
        gap: 12px;
    }

    .avatar-stack {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    .avatar-stack img {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #26394c;
        margin-left: -8px;
        background: #ffffff;
    }

    .avatar-stack img:first-child {
        margin-left: 0;
    }

    .avatar-stack-more {
        font-size: 9px;
        background: #cbd5e1;
        color: #1f2937;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #26394c;
        margin-left: -8px;
        font-weight: 900;
    }

    .qs-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        min-width: 18px;
        height: 18px;
        padding: 0 6px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 900;
    }

    @media (max-width: 575px) {
        .quick-sider {
            max-width: 100vw;
            width: 100vw;
        }

        .qs-content-modern .qs-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .qs-sub {
            position: fixed;
            left: 14px;
            right: 14px;
            top: 88px;
            min-width: 0;
            max-width: none;
        }

        .qs-sub-department {
            max-height: 70vh;
        }
    }

    @media (max-width: 639px) {
        .header-actions .hover-group:has(.icon-btn.primary) {
            display: block !important;
        }

        .header-actions .icon-btn.primary {
            display: flex !important;
        }

        .sa-create-dropdown {
            right: -8px;
            width: 220px;
        }
    }

    .participant-avatar:first-child {
        margin-left: 0;
    }

    .verified-badge {
        color: var(--color-success);
        font-weight: bold;
    }

    .deleted-item {
        opacity: 0.6;
        filter: grayscale(1);
    }
</style>

<style>
    .sa-report-hub {
        position: relative;
    }

    .sa-report-hub-btn {
        position: relative;
        width: 42px;
        height: 42px;
        border: 1px solid rgba(116, 178, 212, .22);
        border-radius: 14px;
        background:
            radial-gradient(circle at top left, rgba(116, 178, 212, .18), transparent 34%),
            linear-gradient(135deg, #ffffff, #f8fafc);
        color: var(--text-main);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
        transition: all .2s ease;
    }

    .sa-report-hub-btn:hover {
        transform: translateY(-1px);
        border-color: rgba(116, 178, 212, .45);
        box-shadow: 0 16px 34px rgba(15, 23, 42, .10);
        color: var(--brand-blue);
    }

    .sa-report-hub-icon {
        width: 22px;
        height: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .sa-report-hub-icon svg {
        width: 20px;
        height: 20px;
        stroke-width: 2.3;
    }

    .sa-report-hub-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 999px;
        background: #e50656;
        color: #fff;
        border: 2px solid #fff;
        font-size: 10px;
        line-height: 16px;
        font-weight: 900;
        text-align: center;
        box-shadow: 0 8px 18px rgba(229, 6, 86, .35);
    }

    .sa-report-panel {
        top: 42px;
        left: -189px !important;
        width: 448px;
        padding: 0;
        overflow: hidden;
        border-radius: 22px;
        z-index: 1090999;
        border: 1px solid rgba(226, 232, 240, .95);
        background: rgba(255, 255, 255, .96);
        backdrop-filter: blur(16px);
        box-shadow: 0 28px 70px rgba(15, 23, 42, .18), 0 8px 20px rgba(15, 23, 42, .08);
    }

    .sa-report-panel-glow {
        position: absolute;
        top: -80px;
        right: -80px;
        width: 180px;
        height: 180px;
        border-radius: 999px;
        background: rgba(116, 178, 212, .20);
        filter: blur(18px);
        pointer-events: none;
    }

    .sa-report-panel-head {
        position: relative;
        padding: 16px;
        border-bottom: 1px solid rgba(226, 232, 240, .9);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        background:
            linear-gradient(135deg, rgba(116, 178, 212, .14), rgba(147, 194, 28, .10)),
            #ffffff;
    }

    .sa-report-panel-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .sa-report-panel-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        background: #ffffff;
        color: var(--brand-blue);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 18px rgba(116, 178, 212, .18);
        flex: 0 0 auto;
    }

    .sa-report-panel-icon svg {
        width: 21px;
        height: 21px;
        stroke-width: 2.4;
    }

    .sa-report-panel-head h6 {
        font-weight: 950;
        font-size: 14px;
        letter-spacing: .03em;
        color: #111827;
        margin: 0;
        text-transform: uppercase;
    }

    .sa-report-panel-head p {
        margin: 3px 0 0;
        font-size: 11px;
        color: #6b7280;
        font-weight: 700;
        white-space: nowrap;
    }

    .sa-report-read-all {
        border: 1px solid rgba(116, 178, 212, .22);
        border-radius: 999px;
        padding: 8px 10px;
        background: #ffffff;
        color: var(--brand-blue);
        font-size: 11px;
        font-weight: 900;
        cursor: pointer;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all .2s ease;
    }

    .sa-report-read-all svg {
        width: 14px;
        height: 14px;
        stroke-width: 2.5;
    }

    .sa-report-read-all:hover {
        background: rgba(116, 178, 212, .10);
        border-color: rgba(116, 178, 212, .38);
    }

    .sa-report-panel-tabs {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px;
        border-bottom: 1px solid rgba(226, 232, 240, .85);
        background: #fff;
    }

    .sa-report-panel-tabs button {
        border: none;
        border-radius: 999px;
        padding: 7px 11px;
        background: #f3f4f6;
        color: #6b7280;
        font-size: 11px;
        font-weight: 900;
        cursor: pointer;
        transition: all .18s ease;
    }

    .sa-report-panel-tabs button:hover {
        background: #e5e7eb;
        color: #111827;
    }

    .sa-report-panel-tabs button.active {
        background: rgba(147, 194, 28, .14);
        color: #5f8512;
    }

    .sa-report-drop-list {
        max-height: 390px;
        overflow-y: auto;
        background: #fff;
        padding: 10px;
    }

    .sa-report-drop-list::-webkit-scrollbar {
        width: 8px;
    }

    .sa-report-drop-list::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 999px;
    }

    .sa-report-item {
        display: grid;
        grid-template-columns: 42px 1fr;
        gap: 11px;
        padding: 12px;
        border-radius: 16px;
        cursor: pointer;
        border: 1px solid transparent;
        transition: all .2s ease;
        position: relative;
        margin-bottom: 8px;
        background: #fff;
    }

    .sa-report-item:last-child {
        margin-bottom: 0;
    }

    .sa-report-item:hover {
        background: #f8fafc;
        border-color: rgba(116, 178, 212, .20);
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(15, 23, 42, .06);
    }

    .sa-report-item.unread {
        background:
            linear-gradient(135deg, rgba(147, 194, 28, .10), rgba(116, 178, 212, .07)),
            #ffffff;
        border-color: rgba(147, 194, 28, .22);
    }

    .sa-report-item.unread::before {
        content: "";
        position: absolute;
        left: 0;
        top: 14px;
        bottom: 14px;
        width: 4px;
        border-radius: 0 999px 999px 0;
        background: #93c21c;
    }

    .sa-report-item.unread::after {
        content: "";
        position: absolute;
        top: 14px;
        right: 13px;
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: #e50656;
        box-shadow: 0 0 0 4px rgba(229, 6, 86, .11);
    }

    .sa-report-avatar {
        width: 42px;
        height: 42px;
        border-radius: 15px;
        object-fit: cover;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px rgba(226, 232, 240, 1), 0 8px 18px rgba(15, 23, 42, .08);
        background: #f3f4f6;
    }

    .sa-report-title {
        font-size: 13px;
        font-weight: 950;
        color: #111827;
        padding-right: 18px;
        line-height: 1.25;
    }

    .sa-report-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px;
        font-size: 11px;
        color: #6b7280;
        margin-top: 4px;
        line-height: 1.3;
        font-weight: 700;
    }

    .sa-report-meta span:first-child {
        color: var(--brand-blue);
        background: rgba(116, 178, 212, .11);
        border-radius: 999px;
        padding: 2px 7px;
        font-weight: 900;
    }

    .sa-report-text {
        font-size: 12px;
        color: #4b5563;
        margin-top: 7px;
        line-height: 1.45;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .sa-report-empty {
        min-height: 220px;
        padding: 34px 18px;
        text-align: center;
        color: #6b7280;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .sa-report-empty-icon {
        width: 56px;
        height: 56px;
        border-radius: 20px;
        background:
            radial-gradient(circle at top left, rgba(116, 178, 212, .18), transparent 55%),
            #f8fafc;
        color: var(--brand-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        border: 1px solid rgba(226, 232, 240, .95);
    }

    .sa-report-empty-icon svg {
        width: 25px;
        height: 25px;
        stroke-width: 2.3;
    }

    .sa-report-empty strong {
        color: #111827;
        font-size: 14px;
        font-weight: 950;
        margin-bottom: 4px;
    }

    .sa-report-empty span {
        font-size: 12px;
        color: #6b7280;
    }

    .sa-report-drop-foot {
        border-top: 1px solid rgba(226, 232, 240, .9);
        background: #fff;
        padding: 10px;
    }

    .sa-report-drop-foot a {
        display: flex;
        width: 100%;
        padding: 12px 14px;
        border-radius: 14px;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #fff;
        font-size: 12px;
        font-weight: 950;
        text-decoration: none;
        background: linear-gradient(135deg, var(--brand-blue), #5f9fc5);
        box-shadow: 0 10px 20px rgba(116, 178, 212, .22);
    }

    .sa-report-drop-foot a:hover {
        color: #fff;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .sa-report-drop-foot svg {
        width: 15px;
        height: 15px;
        stroke-width: 2.5;
    }

    .sa-report-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 1099999;
        background: rgba(15, 23, 42, .62);
        backdrop-filter: blur(5px);
        align-items: center;
        justify-content: center;
        padding: 18px;
    }

    .sa-report-modal {
        width: 100%;
        max-width: 780px;
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 34px 90px rgba(15, 23, 42, .36);
        border: 1px solid rgba(229, 231, 235, .95);
        overflow: hidden;
        animation: saReportModalIn .18s ease-out;
    }

    .sa-report-modal-topline {
        height: 5px;
        background: linear-gradient(90deg, #93c21c, #74b2d4, #f8ac00, #e50656);
    }

    @keyframes saReportModalIn {
        from {
            opacity: 0;
            transform: translateY(12px) scale(.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .sa-report-modal-head {
        padding: 20px;
        background:
            radial-gradient(circle at top right, rgba(116, 178, 212, .18), transparent 34%),
            linear-gradient(135deg, rgba(116, 178, 212, .12), rgba(147, 194, 28, .10)),
            #ffffff;
        border-bottom: 1px solid rgba(226, 232, 240, .95);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 14px;
    }

    .sa-report-modal-titlebox {
        display: flex;
        align-items: center;
        gap: 12px;
        min-width: 0;
    }

    .sa-report-modal-icon {
        width: 48px;
        height: 48px;
        border-radius: 17px;
        background: #fff;
        color: var(--brand-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 22px rgba(116, 178, 212, .18);
        flex: 0 0 auto;
    }

    .sa-report-modal-icon svg {
        width: 24px;
        height: 24px;
        stroke-width: 2.4;
    }

    .sa-report-modal-head h3 {
        margin: 0;
        font-size: 19px;
        font-weight: 950;
        color: #111827;
    }

    .sa-report-modal-head p {
        margin: 5px 0 0;
        color: #6b7280;
        font-size: 12px;
        font-weight: 800;
    }

    .sa-report-modal-close {
        width: 38px;
        height: 38px;
        border-radius: 13px;
        border: 1px solid rgba(226, 232, 240, .95);
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #6b7280;
        transition: all .18s ease;
    }

    .sa-report-modal-close:hover {
        color: #111827;
        background: #f8fafc;
        transform: rotate(90deg);
    }

    .sa-report-modal-close svg {
        width: 18px;
        height: 18px;
        stroke-width: 2.5;
    }

    .sa-report-modal-body {
        padding: 20px;
        max-height: 70vh;
        overflow-y: auto;
    }

    .sa-report-info-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 16px;
    }

    @media (max-width: 820px) {
        .sa-report-info-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .sa-report-panel {
            width: min(410px, calc(100vw - 24px));
            right: -10px;
        }
    }

    @media (max-width: 520px) {
        .sa-report-info-grid {
            grid-template-columns: 1fr;
        }
    }

    .sa-report-info-box {
        border: 1px solid rgba(226, 232, 240, .95);
        background:
            radial-gradient(circle at top left, rgba(116, 178, 212, .08), transparent 55%),
            #f8fafc;
        border-radius: 16px;
        padding: 13px;
        min-width: 0;
    }

    .sa-report-info-box span {
        display: block;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .07em;
        color: #6b7280;
        font-weight: 950;
        margin-bottom: 5px;
    }

    .sa-report-info-box strong {
        display: block;
        font-size: 13px;
        color: #111827;
        font-weight: 950;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sa-report-full-text {
        border: 1px solid rgba(226, 232, 240, .95);
        border-radius: 18px;
        padding: 16px;
        background:
            linear-gradient(180deg, #ffffff, #f8fafc);
    }

    .sa-report-full-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .sa-report-full-head span {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 11px;
        font-weight: 950;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .06em;
    }

    .sa-report-full-head svg {
        width: 15px;
        height: 15px;
        stroke-width: 2.4;
        color: var(--brand-blue);
    }

    .sa-report-full-text p {
        margin: 0;
        font-size: 14px;
        line-height: 1.75;
        color: #374151;
        white-space: pre-wrap;
    }

    .sa-report-modal-foot {
        padding: 15px 20px;
        border-top: 1px solid rgba(226, 232, 240, .95);
        background: #fafafa;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .sa-report-modal-secondary,
    .sa-report-modal-primary {
        border: none;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 12px;
        font-weight: 950;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: all .18s ease;
    }

    .sa-report-modal-secondary {
        background: #fff;
        color: #111827;
        border: 1px solid rgba(226, 232, 240, .95);
    }

    .sa-report-modal-secondary:hover {
        background: #f8fafc;
    }

    .sa-report-modal-primary {
        background: linear-gradient(135deg, var(--brand-blue), #5f9fc5);
        color: #fff;
        box-shadow: 0 10px 22px rgba(116, 178, 212, .24);
    }

    .sa-report-modal-primary:hover {
        color: #fff;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .sa-report-modal-primary svg {
        width: 15px;
        height: 15px;
        stroke-width: 2.5;
    }
</style>

<style>
    /* =========================================================
    ROUTE ACTIVE STATE FOR SIDEBAR + QUICK MENU
    ========================================================= */

    .sidebar-left a.is-route-active,
    .sidebar-left button.is-route-active,
    .sidebar-left .nav-item.is-route-active,
    .sidebar-left .nav-link.is-route-active,
    .sidebar-left .submenu-link.is-route-active,
    .sidebar-left li.is-route-active>a {
        background: rgba(147, 194, 28, .16) !important;
        color: var(--brand-green) !important;
        font-weight: 900 !important;
        box-shadow: inset 3px 0 0 var(--brand-green);
    }

    .sidebar-left a.is-route-active i,
    .sidebar-left a.is-route-active svg,
    .sidebar-left button.is-route-active i,
    .sidebar-left button.is-route-active svg,
    .sidebar-left .nav-item.is-route-active i,
    .sidebar-left .nav-item.is-route-active svg,
    .sidebar-left .nav-link.is-route-active i,
    .sidebar-left .nav-link.is-route-active svg,
    .sidebar-left .submenu-link.is-route-active i,
    .sidebar-left .submenu-link.is-route-active svg {
        color: var(--brand-green) !important;
        stroke: currentColor !important;
    }

    .sidebar-left .submenu.open-by-route {
        grid-template-rows: 1fr !important;
    }

    .sidebar-left .submenu.open-by-route .submenu-inner {
        overflow: visible;
    }

    /* Parent button of active submenu */
    .sidebar-left .has-active-child,
    .sidebar-left .nav-item.has-active-child,
    .sidebar-left .nav-link.has-active-child {
        background: rgba(116, 178, 212, .10) !important;
        color: var(--brand-blue) !important;
        font-weight: 900 !important;
    }

    /* Quick menu active */
    .quick-sider .qs-tile.is-route-active,
    .quick-sider .qs-sub-item.is-route-active {
        background: rgba(147, 194, 28, .22) !important;
        border-color: rgba(147, 194, 28, .55) !important;
        color: #ffffff !important;
        box-shadow: inset 0 0 0 2px rgba(147, 194, 28, .35), 0 10px 24px rgba(0, 0, 0, .22);
    }

    .quick-sider .qs-tile.is-route-active i,
    .quick-sider .qs-tile.is-route-active svg,
    .quick-sider .qs-sub-item.is-route-active i,
    .quick-sider .qs-sub-item.is-route-active svg {
        color: #cfe09a !important;
        stroke: currentColor !important;
    }

    /* Dark mode active fix */
    html.dark .sidebar-left a.is-route-active,
    html.dark .sidebar-left button.is-route-active,
    html.dark .sidebar-left .nav-item.is-route-active,
    html.dark .sidebar-left .nav-link.is-route-active,
    html.dark .sidebar-left .submenu-link.is-route-active,
    html.dark .sidebar-left li.is-route-active>a {
        background: rgba(147, 194, 28, .22) !important;
        color: #d9f99d !important;
        box-shadow: inset 3px 0 0 #93c21c;
    }

    html.dark .sidebar-left .has-active-child,
    html.dark .sidebar-left .nav-item.has-active-child,
    html.dark .sidebar-left .nav-link.has-active-child {
        background: rgba(116, 178, 212, .16) !important;
        color: #bfdbfe !important;
    }
</style>
<style>
    .sa-sidebar-right {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex-shrink: 0;
    }

    .sa-sidebar-count {
        min-width: 20px;
        height: 20px;
        padding: 0 7px;
        border-radius: 999px;
        background: rgba(229, 6, 86, .12);
        color: #e50656;
        border: 1px solid rgba(229, 6, 86, .18);
        font-size: 10px;
        font-weight: 950;
        line-height: 18px;
        text-align: center;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 14px rgba(229, 6, 86, .08);
    }

    .nav-item .sa-sidebar-count,
    .submenu-link .sa-sidebar-count {
        margin-left: auto;
        flex-shrink: 0;
    }

    .submenu-link {
        justify-content: space-between;
    }

    .submenu-link .nav-item-content {
        min-width: 0;
    }

    html.dark .sa-sidebar-count {
        background: rgba(229, 6, 86, .18);
        color: #fda4af;
        border-color: rgba(253, 164, 175, .25);
    }
</style>
<style>
    /* =========================================================
       MOBILE SIDEBAR SCROLL FIX
    ========================================================= */

    @media (max-width: 767px) {

        html,
        body {
            height: 100%;
            overflow: hidden;
        }

        #leftSidebar.sidebar-left {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            width: min(320px, 88vw) !important;
            height: 100vh !important;
            height: 100dvh !important;
            max-height: 100vh !important;
            max-height: 100dvh !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
            transform: translateX(-105%);
            z-index: 99999 !important;
            box-shadow: 0 20px 60px rgba(15, 23, 42, .28);
        }

        #leftSidebar.sidebar-left.is-open {
            transform: translateX(0) !important;
        }

        #leftSidebar .sidebar-header {
            flex: 0 0 var(--header-height);
        }

        #leftSidebar .sidebar-nav {
            flex: 1 1 auto !important;
            min-height: 0 !important;
            height: auto !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            -webkit-overflow-scrolling: touch;
            overscroll-behavior: contain;
            padding-bottom: 18px;
        }

        #leftSidebar .sidebar-footer {
            flex: 0 0 auto !important;
            position: relative !important;
            z-index: 5 !important;
        }

        #mobileSidebarOverlay.is-active {
            z-index: 99990 !important;
        }

        /* Profile dropdown should not destroy sidebar scroll */
        #sidebarProfileMenu.sidebar-profile-menu {
            position: fixed !important;
            left: 14px !important;
            right: 14px !important;
            bottom: calc(var(--mobile-bottom-nav-height) + 16px) !important;
            width: auto !important;
            max-height: 70vh;
            overflow-y: auto;
            z-index: 100000 !important;
        }
    }

    /* =========================================================
       MOBILE GLOBAL SEARCH FIX
    ========================================================= */

    @media (max-width: 639px) {
        .search-wrapper {
            display: none !important;
        }

        body.mobile-search-open .search-wrapper {
            display: block !important;
            position: fixed !important;
            top: 12px !important;
            left: 12px !important;
            right: 12px !important;
            width: auto !important;
            max-width: none !important;
            margin: 0 !important;
            z-index: 110000 !important;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            padding: 10px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .30);
        }

        body.mobile-search-open .search-input {
            height: 44px;
            font-size: 15px;
            padding-right: 44px;
        }

        body.mobile-search-open .search-results-dropdown {
            position: fixed !important;
            top: 76px !important;
            left: 12px !important;
            right: 12px !important;
            max-height: calc(100dvh - 150px) !important;
            width: auto !important;
            z-index: 110001 !important;
            display: none;
        }

        body.mobile-search-open .search-results-dropdown.is-active {
            display: block !important;
        }

        .mobile-search-close-btn {
            display: none;
        }

        body.mobile-search-open .mobile-search-close-btn {
            display: flex !important;
            position: fixed;
            top: 22px;
            right: 22px;
            width: 30px;
            height: 30px;
            border-radius: 999px;
            align-items: center;
            justify-content: center;
            background: var(--bg-hover);
            color: var(--text-muted);
            z-index: 110002;
        }

        .search-item {
            padding: 12px;
            gap: 12px;
        }

        .search-item-main-avatar {
            width: 44px;
            height: 44px;
        }

        .search-item-info-row {
            gap: 8px;
        }
    }

    /* =========================================================
   GLOBAL SEARCH FIX
========================================================= */

    .sa-global-search {
        position: relative;
        width: 100%;
        max-width: 500px;
    }

    .sa-global-search .search-icon {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        z-index: 2;
        color: var(--text-light);
        pointer-events: none;
    }

    .sa-global-search .search-input {
        width: 100%;
        height: 40px;
        padding: 8px 44px 8px 38px;
        background: var(--bg-hover);
        border: 1px solid transparent;
        border-radius: var(--radius);
        color: var(--text-main);
        font-size: 14px;
        outline: none;
    }

    .sa-global-search .search-input:focus {
        background: var(--bg-surface);
        border-color: var(--brand-blue);
        box-shadow: 0 0 0 2px rgba(116, 178, 212, .20);
    }

    .mobile-search-close-btn {
        display: none;
    }

    /* Desktop dropdown */
    .sa-global-search .search-results-dropdown {
        position: absolute;
        top: calc(100% + 10px);
        left: 0;
        right: 0;
        max-height: 600px;
        overflow-y: auto;
        z-index: 9999;
    }

    /* Mobile search overlay */
    @media (max-width: 639px) {
        .sa-global-search {
            display: none !important;
        }

        body.mobile-search-open .sa-global-search {
            display: block !important;
            position: fixed !important;
            top: 12px !important;
            left: 12px !important;
            right: 12px !important;
            width: auto !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 10px !important;
            z-index: 110000 !important;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            box-shadow: 0 24px 70px rgba(15, 23, 42, .30);
        }

        body.mobile-search-open .sa-global-search .search-input {
            height: 44px;
            padding-left: 40px;
            padding-right: 44px;
            font-size: 15px;
            border-radius: 14px;
        }

        body.mobile-search-open .sa-global-search .search-icon {
            left: 24px;
        }

        body.mobile-search-open .mobile-search-close-btn {
            display: flex !important;
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            border-radius: 999px;
            align-items: center;
            justify-content: center;
            background: var(--bg-hover);
            color: var(--text-muted);
            z-index: 3;
        }

        body.mobile-search-open .sa-global-search .search-results-dropdown {
            position: fixed !important;
            top: 76px !important;
            left: 12px !important;
            right: 12px !important;
            width: auto !important;
            max-height: calc(100dvh - 150px) !important;
            z-index: 110001 !important;
        }
    }

    /* =========================================================
   RIGHT MESSAGE SIDEBAR - MANUAL OPEN ONLY
   Removes annoying hover expansion
   ========================================================= */

    @media (min-width: 768px) {
        #rightSidebar.sidebar-right {
            position: static;
            transform: translateX(0);
            z-index: 30;
            width: var(--right-sidebar-collapsed) !important;
            min-width: var(--right-sidebar-collapsed);
            max-width: var(--right-sidebar-collapsed);
            transition: width var(--transition-layout), min-width var(--transition-layout), max-width var(--transition-layout);
        }

        #rightSidebar.sidebar-right:hover {
            width: var(--right-sidebar-collapsed) !important;
            min-width: var(--right-sidebar-collapsed);
            max-width: var(--right-sidebar-collapsed);
        }

        #rightSidebar.sidebar-right.is-expanded,
        #rightSidebar.sidebar-right.is-expanded:hover {
            width: var(--right-sidebar-width) !important;
            min-width: var(--right-sidebar-width);
            max-width: var(--right-sidebar-width);
        }

        #rightSidebar .hide-on-collapse {
            opacity: 0 !important;
            visibility: hidden;
            width: 0;
            max-width: 0;
            overflow: hidden;
            pointer-events: none;
            white-space: nowrap;
            transition: opacity var(--transition), visibility var(--transition), width var(--transition-layout), max-width var(--transition-layout);
        }

        #rightSidebar.is-expanded .hide-on-collapse {
            opacity: 1 !important;
            visibility: visible;
            width: auto;
            max-width: 220px;
            pointer-events: auto;
        }

        #rightSidebar .center-on-collapse {
            margin: 0 auto !important;
        }

        #rightSidebar.is-expanded .center-on-collapse {
            margin: 0 !important;
        }

        #rightSidebar .user-chat-item {
            justify-content: center;
            padding: 5px 0 !important;
            gap: 0 !important;
        }

        #rightSidebar.is-expanded .user-chat-item {
            justify-content: flex-start;
            padding: 5px 7px !important;
            gap: 8px !important;
        }

        #rightSidebar .chat-contact-body,
        #rightSidebar .chat-contact-unread-badge {
            display: none !important;
        }

        #rightSidebar.is-expanded .chat-contact-body {
            display: block !important;
        }

        #rightSidebar.is-expanded .chat-contact-unread-badge {
            display: inline-flex !important;
        }

        #rightSidebar .right-user-btn {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        #rightSidebar.is-expanded .right-user-btn {
            justify-content: flex-start;
            padding-left: 8px;
            padding-right: 8px;
        }

        #rightSidebar .right-user-avatar {
            width: 40px;
            height: 40px;
        }

        #rightSidebar.is-expanded .right-user-avatar {
            width: 38px;
            height: 38px;
        }

        #rightSidebar .right-sidebar-toggle-icon {
            transition: transform var(--transition-layout);
        }

        #rightSidebar.is-expanded .right-sidebar-toggle-icon {
            transform: rotate(180deg);
        }
    }

    /* Recent message preview when sidebar is closed */
    .chat-hover-preview {
        position: absolute;
        right: calc(100% + 10px);
        top: 50%;
        width: 260px;
        max-width: 260px;
        padding: 10px 12px;
        border-radius: 14px;
        background: var(--brand-slate);
        color: #ffffff;
        box-shadow: var(--shadow-float);
        border: 1px solid rgba(255, 255, 255, .12);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(-50%) translateX(8px);
        transition: opacity .18s ease, visibility .18s ease, transform .18s ease;
        z-index: 999999;
    }

    .chat-hover-preview::after {
        content: "";
        position: absolute;
        right: -6px;
        top: 50%;
        width: 12px;
        height: 12px;
        background: var(--brand-slate);
        border-right: 1px solid rgba(255, 255, 255, .12);
        border-top: 1px solid rgba(255, 255, 255, .12);
        transform: translateY(-50%) rotate(45deg);
    }

    .chat-hover-preview-title {
        margin: 0 0 4px;
        font-size: 12px;
        font-weight: 950;
        color: #ffffff;
    }

    .chat-hover-preview-message {
        margin: 0;
        font-size: 12px;
        font-weight: 700;
        line-height: 1.4;
        color: #e5e7eb;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .chat-hover-preview-time {
        margin: 6px 0 0;
        font-size: 10px;
        font-weight: 800;
        color: #cbd5e1;
    }

    /* Only show preview while sidebar is collapsed */
    @media (min-width: 768px) {
        #rightSidebar:not(.is-expanded) .user-chat-item:hover .chat-hover-preview {
            opacity: 1;
            visibility: visible;
            transform: translateY(-50%) translateX(0);
        }

        #rightSidebar.is-expanded .chat-hover-preview {
            display: none !important;
        }
    }

    @media (max-width: 767px) {
        .chat-hover-preview {
            display: none !important;
        }
    }
</style>
<style>
    /* =========================================================
       LEFT SIDEBAR MOUSE-FOLLOW COLLAPSE BUTTON
    ========================================================= */

    @media (min-width: 768px) {
        .sa-left-sidebar-edge-toggle {
            position: fixed;
            top: var(--sa-left-sidebar-edge-y, 110px);
            left: calc(var(--left-sidebar-width) - 16px);
            width: 34px;
            height: 34px;
            border-radius: 999px;
            z-index: 100000;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--bg-surface);
            color: var(--text-muted);
            border: 1px solid var(--border-color);
            box-shadow: 0 14px 34px rgba(15, 23, 42, .18);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(-50%) scale(.88);
            transition:
                opacity .14s ease,
                visibility .14s ease,
                transform .14s ease,
                left var(--transition-layout),
                background-color .18s ease,
                color .18s ease,
                border-color .18s ease,
                box-shadow .18s ease;
        }

        .sa-left-sidebar-edge-toggle.is-visible {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(-50%) scale(1);
        }

        .sa-left-sidebar-edge-toggle:hover {
            background: var(--brand-green);
            color: #ffffff;
            border-color: var(--brand-green);
            box-shadow: 0 18px 42px rgba(147, 194, 28, .30);
            transform: translateY(-50%) scale(1.06);
        }

        .sa-left-sidebar-edge-toggle svg {
            width: 16px;
            height: 16px;
            stroke-width: 3;
        }

        body.sa-left-sidebar-collapsed .sa-left-sidebar-edge-toggle {
            left: 14px;
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-green));
            color: #ffffff;
            border-color: transparent;
            box-shadow: 0 18px 42px rgba(116, 178, 212, .30);
        }

        .sa-left-sidebar-edge-toggle .sa-icon-open {
            display: block;
        }

        .sa-left-sidebar-edge-toggle .sa-icon-closed {
            display: none;
        }

        body.sa-left-sidebar-collapsed .sa-left-sidebar-edge-toggle .sa-icon-open {
            display: none;
        }

        body.sa-left-sidebar-collapsed .sa-left-sidebar-edge-toggle .sa-icon-closed {
            display: block;
        }

        html.dark .sa-left-sidebar-edge-toggle {
            background: #111827;
            border-color: #334155;
            color: #e5e7eb;
        }

        html.dark .sa-left-sidebar-edge-toggle:hover,
        html.dark body.sa-left-sidebar-collapsed .sa-left-sidebar-edge-toggle {
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-green));
            color: #ffffff;
            border-color: transparent;
        }
    }

    @media (max-width: 767px) {
        .sa-left-sidebar-edge-toggle {
            display: none !important;
        }
    }
</style>

<body>

    <!-- Shared Mobile Overlay -->
    <div id="mobileSidebarOverlay" class="overlay" onclick="closeAllMobileSidebars()"></div>

    <!-- LEFT SIDEBAR -->
    <aside id="leftSidebar" class="sidebar-left">

        <!-- Sidebar Header -->
        <div class="sidebar-header">
            <div style="display: flex; align-items: center; gap: 12px; width: 100%; min-width: 0;">
                <div class="brand-logo bg-gradient-brand">S</div>
                <div class="flex-1 truncate" style="display: flex; flex-direction: column;">
                    <span class="font-heading" style="font-weight: 700; color: var(--text-main);">Solar Aspekt</span>
                    <span
                        style="font-size: 10px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600;">Workspace</span>
                </div>
                <button class="icon-btn" onclick="toggleSidebarCollapse()"
                    style="width: 28px; height: 28px; display: block;">
                    <i data-lucide="x" class="icon-md" style="display: var(--display-mobile-x, none);"></i>
                    <i data-lucide="chevrons-up-down" class="icon-md"
                        style="display: var(--display-desktop-chevron, block);"></i>
                </button>
            </div>
        </div>

        <style>
            @media (max-width: 767px) {
                #leftSidebar {
                    --display-mobile-x: block;
                    --display-desktop-chevron: none;
                }
            }
        </style>

        <!-- Sidebar Navigation -->
        @include('admin.layouts.sidebar')

        <!-- User Profile Bottom -->
        <div class="sidebar-footer">
            <button class="profile-btn" type="button" onclick="toggleSidebarProfileMenu(event)">
                <img src="{{ $currentUserImage }}" alt="{{ $currentUserName }}" class="avatar"
                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($currentUserName) }}&background=74b2d4&color=fff'">

                <div class="flex-1 truncate" style="text-align: left;">
                    <p style="font-weight: 700; font-size: 12px; color: var(--text-main);" class="truncate">
                        {{ $currentUserName }}
                    </p>

                    <p
                        style="font-size: 10px; font-weight: 800; color: var(--brand-green); display: flex; align-items: center; gap: 4px;">
                        <span
                            style="width: 6px; height: 6px; border-radius: 50%; background-color: var(--brand-green);"></span>
                        Online
                    </p>
                </div>

                <i data-lucide="settings-2" class="icon-md text-muted"></i>
            </button>

            <div id="sidebarProfileMenu" class="sidebar-profile-menu">
                <div class="sidebar-profile-head">
                    <img src="{{ $currentUserImage }}" alt="{{ $currentUserName }}"
                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($currentUserName) }}&background=74b2d4&color=fff'">

                    <div style="min-width: 0;">
                        <p class="sidebar-profile-name truncate">
                            {{ $currentUserName }}
                        </p>
                        <p class="sidebar-profile-role truncate">
                            {{ $currentUserRole }} · Online
                        </p>
                    </div>
                </div>

                <!-- STATUS -->
                <button type="button" class="sidebar-profile-link" data-sa-status="aktiv">
                    <i data-lucide="user-check" class="icon-md text-brand-green"></i>
                    Aktiv
                </button>

                <button type="button" class="sidebar-profile-link" data-sa-status="abwesend">
                    <i data-lucide="user-x" class="icon-md" style="color:#f87171;"></i>
                    Abwesend
                </button>

                <button type="button" class="sidebar-profile-link" data-sa-status="mittagspause">
                    <i data-lucide="pause-circle" class="icon-md" style="color:var(--color-warning);"></i>
                    Mittagspause
                </button>

                <div style="height: 1px; background-color: #374151; margin: 6px 0;"></div>

                <!-- LINKS -->
                <a href="{{ url('employee_profile/' . $employeeId) }}" class="sidebar-profile-link">
                    <i data-lucide="user" class="icon-md text-brand-blue"></i>
                    Mein Profil
                </a>

                <a href="{{ url('employee_notifications/' . $employeeId) }}" class="sidebar-profile-link">
                    <i data-lucide="bell" class="icon-md text-brand-blue"></i>
                    Meine Anträge
                </a>

                <a href="{{ url('admin/employees/' . $employeeId . '/time-management') }}" class="sidebar-profile-link">
                    <i data-lucide="clock" class="icon-md text-brand-green"></i>
                    Arbeitszeit
                </a>

                <a href="{{ $profileUrl }}" class="sidebar-profile-link">
                    <i data-lucide="settings" class="icon-md text-brand-blue"></i>
                    Profil bearbeiten
                </a>

                <button type="button" onclick="toggleDarkMode(event)" class="sidebar-profile-link">
                    <i data-lucide="moon" class="icon-md text-brand-blue dark-mode-icon-moon"></i>
                    <i data-lucide="sun" class="icon-md text-brand-blue dark-mode-icon-sun" style="display:none;"></i>
                    <span id="sidebarThemeLabel">Dark Mode</span>
                </button>

                <div style="height: 1px; background-color: #374151; margin: 6px 0;"></div>

                <form method="POST" action="{{ $logoutUrl }}" style="margin: 0;">
                    @csrf

                    <button type="submit" class="sidebar-profile-link danger">
                        <i data-lucide="log-out" class="icon-md"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>
    <button
        id="leftSidebarEdgeToggle"
        type="button"
        class="sa-left-sidebar-edge-toggle"
        onclick="toggleSidebarCollapse(event)"
        aria-label="Sidebar einklappen"
        title="Sidebar einklappen"
    >
        <i data-lucide="minus" class="sa-icon-open"></i>
        <i data-lucide="panel-left-open" class="sa-icon-closed"></i>
    </button

    <!-- MAIN CONTENT AREA -->
    <main class="main-wrapper">

        <!-- TOP NAVIGATION -->
        <header class="top-header">
            <!-- Breadcrumbs & Search -->
            <div style="display: flex; align-items: center; gap: 16px; flex: 1;">
                <button onclick="toggleSidebarCollapse()" class="icon-btn" title="Menü" style="margin-left: -8px;">
                    <i data-lucide="menu" class="icon-lg"></i>
                </button>

                <div id="globalBreadcrumbs" class="breadcrumbs"
                    style="display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 500; flex-wrap: wrap;">
                    <a href="{{ url('/employee_dashboard') }}" class="text-muted"
                        style="transition: color 0.2s; text-decoration: none;">
                        Workspace
                    </a>

                    <i data-lucide="chevron-right" class="icon-sm text-muted"></i>

                    <span style="font-weight: 700; color: var(--text-main);">
                        Dashboard
                    </span>
                </div>
                <style>
                    @media (max-width: 767px) {
                        .breadcrumbs {
                            display: none !important;
                        }
                    }
                </style>

                <div class="search-wrapper sa-global-search">
                    <i data-lucide="search" class="search-icon"></i>

                    <input type="text" id="searchInput" placeholder="Suchen... (Kunden, Termine, Aufgaben...) (⌘K)"
                        class="search-input" autocomplete="off">

                    <button type="button" class="mobile-search-close-btn" onclick="closeMobileSearch(event)"
                        aria-label="Suche schließen">
                        <i data-lucide="x" class="icon-md"></i>
                    </button>

                    <div id="searchResultsDropdown" class="search-results-dropdown">
                        <div id="searchLoader" class="search-loading hidden">
                            <div class="spinner-sm"></div>
                            <span>Suche läuft...</span>
                        </div>

                        <div id="searchContent"></div>
                    </div>
                </div>

            </div>

            <!-- Right Actions -->
            <div class="header-actions">

                <div style="position: relative;" class="hide-mobile">
                    <button class="icon-btn" onclick="toggleDropdown('activityDropdown'); event.stopPropagation();"
                        title="Live-Aktivitäten">
                        <i data-lucide="activity" class="icon-lg"></i>
                        <span id="activityBadge" class="badge badge-pill badge-danger shadow-sm"
                            style="display:none; position:absolute; top:4px; right:4px; min-width:18px; height:18px; font-size:10px; align-items:center; justify-content:center; padding:0;">
                            0
                        </span>
                    </button>
                    <div id="activityDropdown" class="dropdown-menu"
                        style="right: 0; top: 50px; width: 340px; padding: 0; overflow: hidden; border-radius: 16px;">
                        <div
                            style="padding: 14px 16px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: var(--bg-subtle);">
                            <h6 class="mb-0" style="font-weight: 800; font-size: 13px;">LIVE-AKTIVITÄTEN</h6>
                            <p class="mb-0" style="font-size: 11px; opacity: 0.7;">Kürzliche Änderungen</p>
                        </div>

                        <div id="quickActivityList" style="max-height: 350px; overflow-y: auto; background: #fff;">
                            <div class="text-center p-3 text-muted"><small>Lade Aktivitäten...</small></div>
                        </div>

                        <button onclick="toggleActivitySidebar();"
                            style="width: 100%; padding: 12px; border: none; border-top: 1px solid var(--border-color); font-size: 12px; font-weight: 800; color: var(--brand-blue); background: #fff; cursor: pointer;">
                            VERLAUF & DETAILS ÖFFNEN
                        </button>
                    </div>
                </div>

                <div style="position: relative;">
                    <button class="icon-btn" onclick="toggleDropdown('notifDropdown'); event.stopPropagation();"
                        title="Benachrichtigungen">
                        <i data-lucide="bell" class="icon-lg"></i>
                        <span id="navbarNotificationBadge" class="badge badge-pill badge-danger shadow-sm"
                            style="display:none; position:absolute; top: 4px; right: 4px; min-width: 18px; height: 18px; font-size: 10px; display: flex; align-items: center; justify-content: center; padding: 0;">
                            0
                        </span>
                    </button>

                    <div id="notifDropdown" class="dropdown-menu"
                        style="left: -99px; top: 50px; width: 350px; padding: 0; overflow: hidden; border-radius: 16px; z-index: 1090999;">
                        <div
                            style="padding: 14px 16px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; background: var(--bg-subtle);">
                            <h6 class="mb-0" style="font-weight: 800; font-size: 13px;">BENACHRICHTIGUNGEN</h6>
                            <span id="dropNotifCount" class="badge badge-primary" style="font-size: 10px;">0 Neu</span>
                        </div>

                        <div id="quickNotifList" style="max-height: 350px; overflow-y: auto; background: #fff;">
                            <div class="text-center p-3 text-muted"><small>Lade Nachrichten...</small></div>
                        </div>

                        <button onclick="openNotificationSlider();"
                            style="width: 100%; padding: 12px; border: none; border-top: 1px solid var(--border-color); font-size: 12px; font-weight: 800; color: var(--brand-blue); background: #fff; cursor: pointer;">
                            POSTEINGANG & SUCHE
                        </button>
                    </div>
                </div>

                <div class="sa-report-hub hide-mobile" id="reportNotifyMenu">
                    <button type="button" class="sa-report-hub-btn" id="reportNotifyTrigger"
                        onclick="toggleDropdown('reportNotifyDropdown'); event.stopPropagation();" title="Reports">
                        <span class="sa-report-hub-icon">
                            <i data-lucide="clipboard-list"></i>
                        </span>

                        <span id="reportNotifyBadge" class="sa-report-hub-badge" style="display:none;">0</span>
                    </button>

                    <div id="reportNotifyDropdown" class="dropdown-menu sa-report-panel">
                        <div class="sa-report-panel-glow"></div>

                        <div class="sa-report-panel-head">
                            <div class="sa-report-panel-title-wrap">
                                <span class="sa-report-panel-icon">
                                    <i data-lucide="clipboard-check"></i>
                                </span>

                                <div>
                                    <h6>Reports</h6>
                                    <p>Neue Berichte & aktuelle Meldungen</p>
                                </div>
                            </div>

                            <button type="button" id="reportMarkAllReadBtn" class="sa-report-read-all">
                                <i data-lucide="check-check"></i>
                                <span>Alle gelesen</span>
                            </button>
                        </div>

                        <div class="sa-report-panel-tabs">
                            <button type="button" class="active" data-report-filter="all">Alle</button>
                            <button type="button" data-report-filter="unread">Ungelesen</button>
                            <button type="button" data-report-filter="important">Wichtig</button>
                        </div>

                        <div id="reportNotifyList" class="sa-report-drop-list">
                            <div class="sa-report-empty">
                                <div class="sa-report-empty-icon">
                                    <i data-lucide="clipboard-check"></i>
                                </div>
                                <strong>Keine neuen Berichte</strong>
                                <span>Alles ist aktuell gelesen.</span>
                            </div>
                        </div>

                        <div class="sa-report-drop-foot">
                            <a href="{{ route('admin.report.index') }}">
                                <span>Alle Reports öffnen</span>
                                <i data-lucide="arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div style="width: 1px; height: 24px; background-color: var(--border-color); margin: 0 4px;"></div>

                <div class="hover-group">
                    <button type="button" class="icon-btn" title="Neu anlegen"
                        style="background:#73b1d4; color:#ffffff;">
                        <i data-lucide="plus" class="icon-lg"></i>
                    </button>

                    <div class="hover-dropdown sa-create-dropdown">
                        <div style="padding: 8px 12px; border-bottom: 1px solid #374151; margin-bottom: 4px;">
                            <p
                                style="font-weight: bold; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af;">
                                Neu Anlegen
                            </p>
                        </div>

                        <a href="{{ $createLinks['inquiry'] }}" class="hover-dropdown-item">
                            <i data-lucide="help-circle" class="icon-md text-brand-blue"></i>
                            Anfrage
                        </a>

                        <a href="{{ $createLinks['customer'] }}" class="hover-dropdown-item">
                            <i data-lucide="user-plus" class="icon-md text-brand-green"></i>
                            Kunde
                        </a>

                        <a href="{{ $createLinks['brand'] }}" class="hover-dropdown-item">
                            <i data-lucide="briefcase" class="icon-md" style="color: var(--color-warning);"></i>
                            Hersteller
                        </a>

                        <a href="{{ $createLinks['distributor'] }}" class="hover-dropdown-item">
                            <i data-lucide="truck" class="icon-md" style="color: #fb923c;"></i>
                            Lieferant
                        </a>

                        <a href="{{ $createLinks['product'] }}" class="hover-dropdown-item">
                            <i data-lucide="box" class="icon-md" style="color: #c084fc;"></i>
                            Produkt
                        </a>

                        <a href="{{ $createLinks['employee'] }}" class="hover-dropdown-item">
                            <i data-lucide="contact" class="icon-md" style="color: #f472b6;"></i>
                            Personal
                        </a>
                    </div>
                </div>

                <div class="hover-group quick-menu-group">
                    <button type="button" id="quickMenuButton" class="quick-menu-btn" onclick="toggleQuickSider(event)"
                        aria-controls="quickSider" aria-expanded="false" title="Quick Menu öffnen">
                        <span class="quick-menu-btn-icon">
                            <i data-lucide="grid-3x3" class="icon-md"></i>
                        </span>
                        <span class="quick-menu-btn-text">Quick Menu</span>
                    </button>

                    <div class="hover-dropdown quick-menu-dropdown" aria-label="Quick Menu Kurzlinks">
                        <a href="javascript:void(0);" class="hover-dropdown-item"
                            onclick="toggleQuickSider(event); return false;">
                            <i data-lucide="grid-2x2" class="icon-md text-brand-blue"></i>
                            Alle Apps
                        </a>

                        <a href="{{ url('/') }}" class="hover-dropdown-item">
                            <i data-lucide="layout-dashboard" class="icon-md text-brand-green"></i>
                            Dashboard
                        </a>

                        <a href="{{ url('admin/chat') }}" class="hover-dropdown-item" style="position: relative;"
                            onclick="openQuickMenuChat(event); return false;">
                            <i data-lucide="message-square" class="icon-md text-brand-blue"></i>
                            Nachrichten
                            <span id="hoverChatUnreadBadge" class="chat-global-badge"
                                style="display:none; position:absolute; right:12px;">0</span>
                        </a>

                        <a href="{{ url('tasks/calendar/personal') }}" class="hover-dropdown-item">
                            <i data-lucide="calendar" class="icon-md" style="color: var(--color-warning);"></i>
                            Kalender
                        </a>

                        <a href="{{ url('admin/todo/personal?tab=my') }}" class="hover-dropdown-item">
                            <i data-lucide="check-square" class="icon-md text-brand-green"></i>
                            Aufgaben
                        </a>

                        <a href="{{ Route::has('ai.chats') ? route('ai.chats') : url('ai/chats') }}"
                            class="hover-dropdown-item">
                            <i data-lucide="bot" class="icon-md text-brand-blue"></i>
                            KI Chat
                        </a>
                    </div>
                </div>

                <style>
                    /* Hide entire header actions on very small screens if necessary */
                    @media (max-width: 639px) {
                        .hide-mobile {
                            display: none !important;
                        }
                    }

                    /* Professional transition for all dropdowns */
                    .dropdown-menu {
                        transition: all 0.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                    }
                </style>
            </div>
        </header>

        <!-- Main Dashboard Content would go here -->
        <div class="main-content-scroll" id="mainContentScroll">
            @yield('content')
        </div>

    </main>

    <!-- RIGHT SIDEBAR (Chat/Users) -->
    <aside id="rightSidebar" class="sidebar-right group">

        <div
            style="height: 64px; display: flex; align-items: center; padding: 0 12px; border-bottom: 1px solid var(--border-color); justify-content: space-between; gap: 8px;">
            <button type="button" class="icon-btn" onclick="toggleRightSidebarDesktop()"
                title="Nachrichtenleiste öffnen/schließen" aria-label="Nachrichtenleiste öffnen/schließen"
                style="flex-shrink: 0;">
                <i data-lucide="panel-right-open" class="icon-lg right-sidebar-toggle-icon"></i>
            </button>

            <div class="hide-on-collapse" style="display: flex; align-items: center; min-width: 0; flex: 1;">
                <i data-lucide="message-square" class="icon-lg text-muted" style="transition: color 0.2s;"></i>

                <span style="margin-left: 12px; font-weight: bold; color: var(--text-main);">
                    Messaging
                </span>

                <span id="rightSidebarChatUnreadBadge" class="chat-global-badge" style="display:none; margin-left:8px;">
                    0
                </span>
            </div>

            <button class="icon-btn mobile-only hide-on-collapse" onclick="toggleRightSidebarMobile()">
                <i data-lucide="x" class="icon-md"></i>
            </button>
        </div>

        <style>
            @media (min-width: 768px) {
                #rightSidebar .mobile-only {
                    display: none !important;
                }
            }
        </style>
        <style>
            @media (min-width: 768px) {
                #rightSidebar .mobile-only {
                    display: none;
                }
            }
        </style>

        <div id="realChatContactList"
            style="flex: 1; overflow-y: auto; overflow-x: hidden; padding: 16px 8px; display: flex; flex-direction: column; gap: 8px;">
            <div class="hide-on-collapse" style="padding: 12px; color: var(--text-muted); font-size: 12px;">
                Kontakte werden geladen...
            </div>
        </div>

        <div class="right-user-footer">
            <button class="right-user-btn" type="button" onclick="toggleDropdown('rightUserDropdown')">
                <div class="right-user-avatar-wrap center-on-collapse">
                    <img src="{{ $currentUserImage }}" alt="{{ $currentUserName }}" class="right-user-avatar"
                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($currentUserName) }}&background=74b2d4&color=fff'">

                    <span class="right-user-online-dot"></span>
                </div>

                <div class="hide-on-collapse flex-1" style="text-align: left; min-width: 0;">
                    <p class="right-user-name truncate">
                        {{ $currentUserName }}
                    </p>

                    <p class="right-user-role truncate">
                        {{ $currentUserRole }} · Online
                    </p>
                </div>

                <i data-lucide="more-vertical" class="icon-md text-muted hide-on-collapse"></i>
            </button>

            <div id="rightUserDropdown" class="dropdown-menu right-user-dropdown">
                <div class="right-user-dropdown-head">
                    <img src="{{ $currentUserImage }}" alt="{{ $currentUserName }}" class="right-user-dropdown-avatar"
                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($currentUserName) }}&background=74b2d4&color=fff'">

                    <div style="min-width: 0;">
                        <p class="right-user-dropdown-name truncate">
                            {{ $currentUserName }}
                        </p>

                        <p class="right-user-dropdown-role truncate">
                            {{ $currentUserRole }}
                        </p>
                    </div>
                </div>

                <a href="{{ $profileUrl }}" class="dropdown-item" style="color: white;">
                    <i data-lucide="user" class="icon-md text-brand-blue"></i>
                    Mein Profil
                </a>

                <a href="{{ $chatUrl }}" class="dropdown-item" style="color: white;">
                    <i data-lucide="message-square" class="icon-md text-brand-green"></i>
                    Chat öffnen
                </a>

                <a href="#" onclick="toggleDarkMode(event)" class="dropdown-item" style="color: white;">
                    <i data-lucide="moon" class="icon-md text-brand-blue"></i>
                    Dark Mode
                </a>

                <div style="height: 1px; background-color: #374151; margin: 4px 0;"></div>

                <form method="POST" action="{{ $logoutUrl }}" style="margin: 0;">
                    @csrf

                    <button type="submit" class="dropdown-item" style="color: #f87171; width: 100%; text-align: left;">
                        <i data-lucide="log-out" class="icon-md"></i>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- QUICK SIDER OVERLAY -->
    <div id="quickSiderOverlay" class="overlay" onclick="toggleQuickSider()"></div>

    <!-- QUICK SIDER SIDEBAR -->
    <aside id="quickSider" class="quick-sider">
        <!-- Header -->
        <div class="qs-header">
            <div style="display:flex; align-items:center; gap:12px;">
                <i data-lucide="command" class="icon-lg text-brand-blue"></i>
                <h3 style="font-weight:bold; font-size:18px; letter-spacing:0.05em;">Schnellzugriff</h3>
            </div>

            <button class="icon-btn" style="background-color:#1f2937; color:#9ca3af; width:32px; height:32px;"
                onclick="toggleQuickSider()">
                <i data-lucide="x" class="icon-md"></i>
            </button>
        </div>

        <div class="qs-content-modern">
            <!-- Main quick grid -->
            <div class="qs-grid">
                <a href="#" onclick="toggleQuickSider(); toggleSidebarCollapse(); return false;"
                    class="qs-tile menu_view_icon">
                    <i data-lucide="command"></i>
                    <span>Menü</span>
                </a>

                <a href="{{ url('/') }}" class="qs-tile dashboard_view_icon">
                    <i data-lucide="home"></i>
                    <span>Dashboard</span>
                </a>

                <a href="#" onclick="toggleQuickSider(); openMobileSearch(event); return false;"
                    class="qs-tile nav-link-search">
                    <i data-lucide="search"></i>
                    <span>Suche</span>
                </a>

                <a href="#" class="qs-tile nav-link-time">
                    <i data-lucide="play-circle"></i>
                    <span>Zeiterfassung</span>
                </a>

                <a href="{{ url('admin/chat') }}" class="qs-tile message_view_icon position-relative">
                    <i data-lucide="message-square"></i>
                    <span>Nachrichten</span>
                    <span id="quickChatUnreadBadge" class="chat-global-badge"
                        style="display:none; position:absolute; top:8px; right:8px;">
                        0
                    </span>
                </a>

                <a href="{{ url('tasks/calendar/personal') }}" class="qs-tile calendar_view_icon">
                    <i data-lucide="calendar" style="color:var(--color-warning);"></i>
                    <span>Kalender</span>
                </a>

                <a href="{{ Route::has('deal.measurements.kanban') ? route('deal.measurements.kanban') : url('deal-measurements-kanban') }}"
                    class="qs-tile {{ request()->is('deal-measurements-kanban*') || request()->is('deal-measurements*') ? 'is-route-active' : '' }}">
                    <i data-lucide="ruler" style="color:var(--brand-green);"></i>
                    <span>Feinaufmaß</span>
                </a>

                <a href="{{ Route::has('employee.capacity.view') ? route('employee.capacity.view') : '#' }}"
                    class="qs-tile watch_view_icon">
                    <i data-lucide="clock"></i>
                    <span>Kapazität</span>
                </a>

                <a href="{{ Route::has('lead.reference') ? route('lead.reference') : '#' }}"
                    class="qs-tile map_view_icon">
                    <i data-lucide="map-pin"></i>
                    <span>Karte</span>
                </a>

                <a href="{{ Route::has('ai.chats') ? route('ai.chats') : url('ai/chats') }}" class="qs-tile">
                    <i data-lucide="bot" class="text-brand-blue"></i>
                    <span>KI Chat</span>
                </a>

                <a href="{{ Route::has('breaking-news.index') ? route('breaking-news.index') : url('breaking-news') }}"
                    class="qs-tile">
                    <i data-lucide="alert-triangle" style="color:#f87171;"></i>
                    <span>Breaking News</span>
                </a>

                <a href="#" onclick="toggleQuickSider(); openNotificationSlider?.(); return false;" class="qs-tile">
                    <i data-lucide="bell"></i>
                    <span>Benachr.</span>
                    <span id="notificationBellBadge" class="badge badge-danger qs-badge"
                        style="display:none; position:absolute; top:8px; right:8px;">
                        0
                    </span>
                </a>

                <a href="#" onclick="submitSaLogout(event); return false;" class="qs-tile nav-log-off">
                    <i data-lucide="power" style="color:#f87171;"></i>
                    <span>Logout</span>
                </a>
            </div>

            <!-- Second grid -->
            <div class="qs-grid qs-grid-secondary">
                <!-- Ticket dropdown -->
                <div class="qs-has-sub">
                    <button type="button" class="qs-tile qs-toggle" aria-expanded="false" aria-controls="qs-sub-ticket">
                        <i data-lucide="alert-triangle"></i>
                        <span>Ticket</span>
                        <i data-lucide="chevron-down" class="qs-caret"></i>
                    </button>

                    <div id="qs-sub-ticket" class="qs-sub" hidden style="right:-110px !important">
                        <a class="qs-sub-item" href="{{ url('/error') }}">
                            <i data-lucide="alert-triangle" class="icon-sm"></i>
                            Fehler & Fehlerheft
                        </a>

                        <a class="qs-sub-item" href="{{ url('problem_create') }}">
                            <i data-lucide="ticket" class="icon-sm"></i>
                            Anlegen
                        </a>

                        <a class="qs-sub-item" href="{{ url('problem_view') }}">
                            <i data-lucide="wrench" class="icon-sm"></i>
                            Liste
                        </a>
                    </div>
                </div>

                <a class="qs-tile" href="{{ url('admin/todo/personal?tab=my') }}">
                    <i data-lucide="check-square"></i>
                    <span>Aufgaben</span>
                </a>

                <a href="{{ route('general-tasks.index') }}" class="qs-tile">
                    <i data-lucide="users-round"></i>
                    <span>Team-Aufgaben</span>
                    <span class="badge" data-count-key="general_tasks_open"
                        style="position:absolute;top:8px;right:8px;">0</span>
                </a>

                <a class="qs-tile calendar_view_icon" href="{{ url('customer/appointments') }}">
                    <i data-lucide="calendar-days"></i>
                    <span>Termine</span>
                </a>

                <a class="qs-tile" href="{{ url('lead/kanban') }}">
                    <i data-lucide="chevron-down" class="qs-caret"></i>
                    <span>Prozess</span>
                </a>


                <a class="qs-tile" href="{{ url('/all-contacts') }}">
                    <i data-lucide="users"></i>
                    <span>Kontakte</span>
                </a>

                <!-- Department dropdown -->
                <div class="qs-has-sub" id="qs-department-wrapper"
                    data-url="{{ Route::has('quick.departments') ? route('quick.departments') : url('/quick/departments') }}">

                    <button type="button" class="qs-tile qs-toggle" aria-expanded="false"
                        aria-controls="qs-sub-department">
                        <i data-lucide="layers"></i>
                        <span>Abteilung</span>
                        <i data-lucide="chevron-down" class="qs-caret"></i>
                    </button>

                    <div id="qs-sub-department" class="qs-sub qs-sub-department" hidden>
                        <div class="p-3 text-center text-muted small js-dept-loading">
                            Abteilungen werden geladen ...
                        </div>

                        <div class="js-dept-error p-2 text-center text-danger small" style="display:none;"></div>

                        <div class="js-dept-list"></div>
                    </div>
                </div>
            </div>
        </div>
    </aside>
    <form id="logout-form" method="POST" action="{{ $logoutUrl }}" style="display:none;">
        @csrf
    </form>

    <!-- CHAT POPUP CONTAINER -->
    <div id="chatContainer" class="chat-container">
        <!-- Chat windows will be injected here via JS -->
    </div>

    <!-- MOBILE BOTTOM NAVIGATION APP BAR -->
    <nav class="mobile-nav">
        <button class="mobile-nav-btn active">
            <i data-lucide="home" class="icon-xl"></i>
            <span>Home</span>
        </button>

        <button class="mobile-nav-btn" type="button" onclick="openMobileSearch(event)">
            <i data-lucide="search" class="icon-xl"></i>
            <span>Suche</span>
        </button>

        <!-- FAB (Floating Action Button) -->
        <div class="fab-container hover-group">
            <button class="fab-main" onclick="toggleDropdown('mobileFabDropdown')">
                <i data-lucide="plus" class="icon-xl"></i>
            </button>

            <div id="mobileFabDropdown" class="dropdown-menu"
                style="bottom: 100%; left: 50%; transform: translateX(-50%) translateY(10px); margin-bottom: 8px; width: 200px; background-color: var(--brand-slate); color: white;">
                <div
                    style="padding: 8px 12px; border-bottom: 1px solid #374151; margin-bottom: 4px; text-align: center;">
                    <p
                        style="font-weight: bold; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af;">
                        Neu Anlegen</p>
                </div>
                <a href="{{ $createLinks['inquiry'] }}" class="dropdown-item" style="color: white;">
                    <i data-lucide="help-circle" class="icon-md text-brand-blue"></i>
                    Anfrage
                </a>

                <a href="{{ $createLinks['customer'] }}" class="dropdown-item" style="color: white;">
                    <i data-lucide="user-plus" class="icon-md text-brand-green"></i>
                    Kunde
                </a>

                <a href="{{ $createLinks['brand'] }}" class="dropdown-item" style="color: white;">
                    <i data-lucide="briefcase" class="icon-md" style="color: var(--color-warning);"></i>
                    Hersteller
                </a>

                <a href="{{ $createLinks['distributor'] }}" class="dropdown-item" style="color: white;">
                    <i data-lucide="truck" class="icon-md" style="color: #fb923c;"></i>
                    Lieferant
                </a>

                <a href="{{ $createLinks['product'] }}" class="dropdown-item" style="color: white;">
                    <i data-lucide="box" class="icon-md" style="color: #c084fc;"></i>
                    Produkt
                </a>

                <a href="{{ $createLinks['employee'] }}" class="dropdown-item" style="color: white;">
                    <i data-lucide="contact" class="icon-md" style="color: #f472b6;"></i>
                    Personal
                </a>
            </div>
        </div>

        <button class="mobile-nav-btn" onclick="toggleRightSidebarMobile()">
            <div style="position: relative;">
                <i data-lucide="message-square" class="icon-xl"></i>
                <span id="mobileChatUnreadBadge" class="chat-global-badge"
                    style="display:none; position:absolute; top:-9px; right:-10px;">
                    0
                </span>
            </div>
            <span>Chat</span>
        </button>

        <button class="mobile-nav-btn" onclick="toggleSidebarCollapse()">
            <i data-lucide="menu" class="icon-xl"></i>
            <span>Menü</span>
        </button>
    </nav>

    <div id="saReportModalBackdrop" class="sa-report-modal-backdrop" style="display:none;">
        <div class="sa-report-modal" role="dialog" aria-modal="true">
            <div class="sa-report-modal-topline"></div>

            <div class="sa-report-modal-head">
                <div class="sa-report-modal-titlebox">
                    <span class="sa-report-modal-icon">
                        <i data-lucide="clipboard-check"></i>
                    </span>

                    <div>
                        <h3 id="saReportModalTitle">Bericht</h3>
                        <p id="saReportModalSub">Details</p>
                    </div>
                </div>

                <button type="button" class="sa-report-modal-close" onclick="closeSaReportModal()">
                    <i data-lucide="x"></i>
                </button>
            </div>

            <div class="sa-report-modal-body">
                <div class="sa-report-info-grid">
                    <div class="sa-report-info-box">
                        <span>Mitarbeiter</span>
                        <strong id="saReportModalEmployee">—</strong>
                    </div>

                    <div class="sa-report-info-box">
                        <span>Typ</span>
                        <strong id="saReportModalType">—</strong>
                    </div>

                    <div class="sa-report-info-box">
                        <span>Ziel</span>
                        <strong id="saReportModalTarget">—</strong>
                    </div>

                    <div class="sa-report-info-box">
                        <span>Zeit</span>
                        <strong id="saReportModalTime">—</strong>
                    </div>
                </div>

                <div class="sa-report-info-grid">
                    <div class="sa-report-info-box">
                        <span>Kunde / Bezug</span>
                        <strong id="saReportModalCustomer">—</strong>
                    </div>

                    <div class="sa-report-info-box">
                        <span>Status</span>
                        <strong id="saReportModalStatus">—</strong>
                    </div>

                    <div class="sa-report-info-box">
                        <span>Priorität</span>
                        <strong id="saReportModalPriority">—</strong>
                    </div>

                    <div class="sa-report-info-box">
                        <span>Datum</span>
                        <strong id="saReportModalTargetDate">—</strong>
                    </div>
                </div>

                <div class="sa-report-full-text">
                    <div class="sa-report-full-head">
                        <span>
                            <i data-lucide="file-text"></i>
                            Berichtstext
                        </span>
                    </div>

                    <p id="saReportModalReport">—</p>
                </div>
            </div>

            <div class="sa-report-modal-foot">
                <button type="button" class="sa-report-modal-secondary" onclick="closeSaReportModal()">
                    Schließen
                </button>

                <a id="saReportModalOpenLink" href="#" class="sa-report-modal-primary" style="display:none;">
                    <i data-lucide="external-link"></i>
                    Eintrag öffnen
                </a>
            </div>
        </div>
    </div>


    @include('admin.layouts.notification')
    @include('admin.layouts.activity')

    @if(auth()->check())
        <style>
            .sa-mention-toast-wrap {
                position: fixed;
                top: 88px;
                right: 24px;
                z-index: 1200000;
                display: flex;
                flex-direction: column;
                gap: 12px;
                pointer-events: none;
            }

            .sa-mention-toast {
                width: min(390px, calc(100vw - 24px));
                pointer-events: auto;
                display: flex;
                gap: 12px;
                align-items: flex-start;
                padding: 14px;
                border-radius: 18px;
                background: linear-gradient(135deg, rgba(255, 255, 255, .98), rgba(248, 250, 252, .96));
                border: 1px solid rgba(116, 178, 212, .35);
                box-shadow: 0 22px 55px rgba(15, 23, 42, .18);
                cursor: pointer;
                animation: saMentionToastIn .25s ease-out;
                position: relative;
                overflow: hidden;
            }

            .sa-mention-toast::before {
                content: "";
                position: absolute;
                inset: 0 auto 0 0;
                width: 5px;
                background: linear-gradient(180deg, #74b2d4, #93c21c);
            }

            .sa-mention-avatar {
                width: 46px;
                height: 46px;
                border-radius: 999px;
                object-fit: cover;
                border: 2px solid rgba(147, 194, 28, .35);
                flex-shrink: 0;
            }

            .sa-mention-body {
                min-width: 0;
                flex: 1;
            }

            .sa-mention-kicker {
                font-size: 11px;
                font-weight: 950;
                color: #93c21c;
                text-transform: uppercase;
                letter-spacing: .04em;
                margin-bottom: 3px;
            }

            .sa-mention-title {
                margin: 0;
                color: #1f2937;
                font-size: 14px;
                font-weight: 950;
            }

            .sa-mention-msg {
                margin: 5px 0 0;
                color: #64748b;
                font-size: 12px;
                line-height: 1.45;
            }

            .sa-mention-action {
                margin-top: 9px;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 11px;
                font-weight: 900;
                color: #569ad8;
            }

            .sa-mention-close {
                width: 26px;
                height: 26px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #94a3b8;
                flex-shrink: 0;
            }

            .sa-mention-close:hover {
                background: #f1f5f9;
                color: #ef4444;
            }

            @keyframes saMentionToastIn {
                from {
                    opacity: 0;
                    transform: translateY(-12px) scale(.98);
                }

                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            html.dark .sa-mention-toast {
                background: linear-gradient(135deg, rgba(30, 41, 59, .98), rgba(15, 23, 42, .96));
                border-color: rgba(116, 178, 212, .28);
            }

            html.dark .sa-mention-title {
                color: #f8fafc;
            }

            html.dark .sa-mention-msg {
                color: #cbd5e1;
            }

            @media (max-width: 767px) {
                .sa-mention-toast-wrap {
                    top: 76px;
                    left: 12px;
                    right: 12px;
                }
            }
        </style>

        <script>
            (function () {
                const currentUserId =
                    document.querySelector('meta[name="chat-user-id"]')?.content ||
                    document.querySelector('meta[name="user-id"]')?.content ||
                    window.userId ||
                    null;

                const csrf =
                    document.querySelector('meta[name="csrf-token"]')?.content ||
                    window.csrfToken ||
                    '';

                if (!currentUserId) return;

                const renderedMentionIds = new Set();

                function escapeHtml(value) {
                    return String(value ?? '')
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');
                }

                function mentionWrap() {
                    let wrap = document.getElementById('saMentionToastWrap');

                    if (!wrap) {
                        wrap = document.createElement('div');
                        wrap.id = 'saMentionToastWrap';
                        wrap.className = 'sa-mention-toast-wrap';
                        document.body.appendChild(wrap);
                    }

                    return wrap;
                }

                async function markMentionRead(id) {
                    if (!id) return;

                    await fetch(`/chat/mentions/${id}/read`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });
                }

                function showMentionToast(mention) {
                    if (!mention || !mention.id) return;

                    const key = String(mention.id);

                    if (renderedMentionIds.has(key)) return;
                    renderedMentionIds.add(key);

                    const wrap = mentionWrap();

                    const toast = document.createElement('div');
                    toast.className = 'sa-mention-toast';
                    toast.dataset.mentionId = key;

                    const sender = mention.sender_name || 'Mitarbeiter';
                    const groupName = mention.group_name || 'Chat';
                    const msg = mention.message || 'Du wurdest in einer Nachricht markiert.';
                    const avatar = mention.sender_avatar || '/images/gender/users.png';

                    toast.innerHTML = `
                    <img class="sa-mention-avatar" src="${escapeHtml(avatar)}" alt="">
                    <div class="sa-mention-body">
                        <div class="sa-mention-kicker">@Erwähnung</div>
                        <p class="sa-mention-title">${escapeHtml(sender)} hat dich markiert</p>
                        <p class="sa-mention-msg">
                            <strong>${escapeHtml(groupName)}</strong><br>
                            ${escapeHtml(msg)}
                        </p>
                        <div class="sa-mention-action">
                            Öffnen und als gelesen markieren
                            <span>→</span>
                        </div>
                    </div>
                    <button type="button" class="sa-mention-close" title="Schließen">×</button>
                `;

                    toast.querySelector('.sa-mention-close')?.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();

                        toast.remove();
                        renderedMentionIds.delete(key);
                    });

                    toast.addEventListener('click', async function () {
                        try {
                            await markMentionRead(mention.id);
                        } catch (error) {
                            console.error('Mention read failed:', error);
                        }

                        toast.remove();

                        if (mention.open_url) {
                            window.location.href = mention.open_url;
                        }
                    });

                    wrap.prepend(toast);
                }

                async function loadUnreadMentions() {
                    try {
                        const response = await fetch('/chat/mentions/unread', {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                        });

                        if (!response.ok) return;

                        const data = await response.json();
                        (data.mentions || []).forEach(showMentionToast);
                    } catch (error) {
                        console.warn('Unread chat mentions could not be loaded:', error);
                    }
                }

                function bootMentionEcho() {
                    if (!window.Echo || typeof window.Echo.private !== 'function') {
                        setTimeout(bootMentionEcho, 700);
                        return;
                    }

                    window.Echo.private(`chat.user.${currentUserId}`)
                        .listen('.chat-mention-created', function (event) {
                            showMentionToast(event.mention || event);
                        });
                }

                document.addEventListener('DOMContentLoaded', function () {
                    loadUnreadMentions();
                    bootMentionEcho();
                });
            })();
        </script>
    @endif


    <!-- BEGIN: Vendor JS-->
    <script src="{{ asset('app-assets/vendors/js/vendors.min.js')}}"></script>
    <!-- BEGIN: Page Vendor JS-->
    <script src="{{ asset('app-assets/vendors/js/editors/quill/katex.min.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/editors/quill/highlight.min.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/editors/quill/quill.min.js')}}"></script>
    <!-- END: Page Vendor JS-->

    <script src="{{ asset('app-assets/vendors/js/extensions/tether.min.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/shepherd.min.js')}}"></script>
    <!-- BEGIN: Theme JS-->


    <script src="{{asset('app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{asset('app-assets/js/core/app.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/components.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/extensions/toastr.min.js') }}"></script>

    <!-- END: Theme JS-->


    <!-- BEGIN: Toastr-->
    <script src="{{ asset('app-assets/js/scripts/extensions/toastr.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/modal/components-modal.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('js/checkHoliday.js')}}"></script>
    <script src="{{ asset('js/checkEndHoliday.js')}}"></script>
    <!-- END: Theme JS-->
    <script src="{{ asset('js/select2.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>window.userId = {{ auth()->id() }};</script>
    <audio id="notificationSound" src="{{ asset('notification/notification.mp3') }}" preload="auto"></audio>
    <audio id="chatNotificationSound" src="{{ asset('notification/notification.mp3') }}" preload="auto"></audio>
    <audio id="activityNotificationSound" src="{{ asset('notification/short.mp3') }}" preload="auto"></audio>
    <audio id="appointmentReminderSound" src="{{ asset('notification/short.mp3') }}" preload="auto"></audio>
    <script>
        const empImage = "{{ asset('images/employee/') }}"
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        });
    </script>


    <!-- JAVASCRIPT LOGIC -->
    <script>

        'use strict';

        /**
         * ============================================================
         * BASIC HELPERS
         * ============================================================
         */
        function saGet(id) {
            return document.getElementById(id);
        }

        function saMeta(name, fallback = '') {
            return document.querySelector(`meta[name="${name}"]`)?.getAttribute('content') || fallback;
        }

        function saEscapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function saRefreshIcons() {
            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }

            if (window.feather && typeof window.feather.replace === 'function') {
                window.feather.replace();
            }
        }

        async function saSafeJson(response) {
            const contentType = response.headers.get('content-type') || '';
            const text = await response.text();

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${text.slice(0, 250)}`);
            }

            if (!contentType.includes('application/json')) {
                throw new Error(`Expected JSON, got "${contentType}". Body: ${text.slice(0, 250)}`);
            }

            return JSON.parse(text || 'null');
        }

        /**
         * ============================================================
         * INITIALIZE ICONS
         * ============================================================
         */
        document.addEventListener('DOMContentLoaded', function () {
            saRefreshIcons();
        });

        /**
         * ============================================================
         * DARK MODE
         * ============================================================
         */
        function applyThemeMode(mode) {
            const isDark = mode === 'dark';

            document.documentElement.classList.toggle('dark', isDark);
            document.documentElement.classList.toggle('light-layout', !isDark);

            document.body.classList.toggle('dark-layout', isDark);
            document.body.classList.toggle('semi-dark-layout', isDark);
            document.body.classList.toggle('light-layout', !isDark);

            localStorage.setItem('theme', isDark ? 'dark' : 'light');

            if (typeof updateSidebarThemeButton === 'function') {
                updateSidebarThemeButton();
            }

            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }

            if (window.feather && typeof window.feather.replace === 'function') {
                window.feather.replace();
            }
        }

        function toggleDarkMode(e) {
            if (e) e.preventDefault();

            const isDark = document.documentElement.classList.contains('dark');
            applyThemeMode(isDark ? 'light' : 'dark');
        }

        /**
         * ============================================================
         * MOBILE SIDEBARS
         * ============================================================
         */
        function closeAllMobileSidebars() {
            const leftSidebar = saGet('leftSidebar');
            const rightSidebar = saGet('rightSidebar');
            const overlay = saGet('mobileSidebarOverlay');

            leftSidebar?.classList.remove('is-open');
            rightSidebar?.classList.remove('is-open');
            overlay?.classList.remove('is-active');
        }

             function updateLeftSidebarEdgeButton() {
                    const sidebar = saGet('leftSidebar');
                    const button = saGet('leftSidebarEdgeToggle');

                    if (!sidebar || !button) return;

                    const isCollapsed = sidebar.classList.contains('collapsed');

                    document.body.classList.toggle('sa-left-sidebar-collapsed', isCollapsed);

                    button.setAttribute(
                        'aria-label',
                        isCollapsed ? 'Sidebar ausklappen' : 'Sidebar einklappen'
                    );

                    button.setAttribute(
                        'title',
                        isCollapsed ? 'Sidebar ausklappen' : 'Sidebar einklappen'
                    );

                    if (typeof saRefreshIcons === 'function') {
                        saRefreshIcons();
                    }
                }

                function restoreLeftSidebarState() {
                    const sidebar = saGet('leftSidebar');

                    if (!sidebar) return;

                    if (window.innerWidth >= 768 && localStorage.getItem('leftSidebarCollapsed') === '1') {
                        sidebar.classList.add('collapsed');
                    } else {
                        sidebar.classList.remove('collapsed');
                    }

                    updateLeftSidebarEdgeButton();
                }

                function toggleSidebarCollapse(event) {
                    if (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    const sidebar = saGet('leftSidebar');
                    const overlay = saGet('mobileSidebarOverlay');
                    const rightSidebar = saGet('rightSidebar');

                    if (!sidebar) return;

                    if (window.innerWidth < 768) {
                        rightSidebar?.classList.remove('is-open');

                        const isClosed = !sidebar.classList.contains('is-open');

                        if (isClosed) {
                            sidebar.classList.add('is-open');
                            overlay?.classList.add('is-active');
                        } else {
                            sidebar.classList.remove('is-open');
                            overlay?.classList.remove('is-active');
                        }

                        return;
                    }

                    const shouldCollapse = !sidebar.classList.contains('collapsed');

                    sidebar.classList.toggle('collapsed', shouldCollapse);
                    document.body.classList.toggle('sa-left-sidebar-collapsed', shouldCollapse);

                    localStorage.setItem('leftSidebarCollapsed', shouldCollapse ? '1' : '0');

                    updateLeftSidebarEdgeButton();
                }

                function bindLeftSidebarMouseFollowButton() {
                    const sidebar = saGet('leftSidebar');
                    const button = saGet('leftSidebarEdgeToggle');

                    if (!sidebar || !button) return;

                    let hideTimer = null;

                    const showButtonAtMouse = function (clientY) {
                        const minY = 82;
                        const maxY = window.innerHeight - 46;
                        const y = Math.max(minY, Math.min(clientY, maxY));

                        button.style.setProperty('--sa-left-sidebar-edge-y', y + 'px');
                        button.classList.add('is-visible');

                        clearTimeout(hideTimer);
                    };

                    const hideButtonLater = function () {
                        clearTimeout(hideTimer);

                        hideTimer = setTimeout(function () {
                            if (!button.matches(':hover')) {
                                button.classList.remove('is-visible');
                            }
                        }, 180);
                    };

                    document.addEventListener('mousemove', function (event) {
                        if (window.innerWidth < 768) return;

                        const isCollapsed = sidebar.classList.contains('collapsed');

                        if (isCollapsed) {
                            const nearCollapsedEdge = event.clientX >= 0 && event.clientX <= 44;

                            if (nearCollapsedEdge) {
                                showButtonAtMouse(event.clientY);
                            } else if (!button.matches(':hover')) {
                                hideButtonLater();
                            }

                            return;
                        }

                        const rect = sidebar.getBoundingClientRect();

                        const nearRightBorder =
                            event.clientX >= rect.right - 18 &&
                            event.clientX <= rect.right + 22 &&
                            event.clientY >= rect.top + 12 &&
                            event.clientY <= rect.bottom - 12;

                        if (nearRightBorder) {
                            showButtonAtMouse(event.clientY);
                        } else if (!button.matches(':hover')) {
                            hideButtonLater();
                        }
                    });

                    button.addEventListener('mouseenter', function () {
                        clearTimeout(hideTimer);
                        button.classList.add('is-visible');
                    });

                    button.addEventListener('mouseleave', hideButtonLater);
                }

                document.addEventListener('DOMContentLoaded', function () {
                    restoreLeftSidebarState();
                    bindLeftSidebarMouseFollowButton();
                });

                window.addEventListener('resize', function () {
                    const sidebar = saGet('leftSidebar');
                    const button = saGet('leftSidebarEdgeToggle');

                    if (!sidebar) return;

                    if (window.innerWidth < 768) {
                        sidebar.classList.remove('collapsed');
                        document.body.classList.remove('sa-left-sidebar-collapsed');
                        button?.classList.remove('is-visible');
                    } else {
                        restoreLeftSidebarState();
                    }
                });

            function restoreLeftSidebarState() {
                const sidebar = saGet('leftSidebar');

                if (!sidebar) return;

                if (window.innerWidth >= 768 && localStorage.getItem('leftSidebarCollapsed') === '1') {
                    sidebar.classList.add('collapsed');
                } else {
                    sidebar.classList.remove('collapsed');
                }

                updateLeftSidebarEdgeButton();
            }

            function toggleSidebarCollapse(event) {
                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                const sidebar = saGet('leftSidebar');
                const overlay = saGet('mobileSidebarOverlay');
                const rightSidebar = saGet('rightSidebar');

                if (!sidebar) return;

                if (window.innerWidth < 768) {
                    rightSidebar?.classList.remove('is-open');

                    const isClosed = !sidebar.classList.contains('is-open');

                    if (isClosed) {
                        sidebar.classList.add('is-open');
                        overlay?.classList.add('is-active');
                    } else {
                        sidebar.classList.remove('is-open');
                        overlay?.classList.remove('is-active');
                    }

                    return;
                }

                const shouldCollapse = !sidebar.classList.contains('collapsed');

                sidebar.classList.toggle('collapsed', shouldCollapse);
                document.body.classList.toggle('sa-left-sidebar-collapsed', shouldCollapse);

                localStorage.setItem('leftSidebarCollapsed', shouldCollapse ? '1' : '0');

                updateLeftSidebarEdgeButton();
            }

            document.addEventListener('DOMContentLoaded', function () {
                restoreLeftSidebarState();
            });

            window.addEventListener('resize', function () {
                const sidebar = saGet('leftSidebar');

                if (!sidebar) return;

                if (window.innerWidth < 768) {
                    sidebar.classList.remove('collapsed');
                    document.body.classList.remove('sa-left-sidebar-collapsed');
                } else {
                    restoreLeftSidebarState();
                }
            });

        function toggleRightSidebarDesktop() {
            const sidebar = saGet('rightSidebar');
            if (!sidebar) return;

            if (window.innerWidth < 768) {
                toggleRightSidebarMobile();
                return;
            }

            sidebar.classList.toggle('is-expanded');
            localStorage.setItem(
                'rightMessageSidebarExpanded',
                sidebar.classList.contains('is-expanded') ? '1' : '0'
            );

            saRefreshIcons();
        }

        function restoreRightSidebarState() {
            const sidebar = saGet('rightSidebar');
            if (!sidebar) return;

            if (window.innerWidth >= 768 && localStorage.getItem('rightMessageSidebarExpanded') === '1') {
                sidebar.classList.add('is-expanded');
            }
        }

        function toggleRightSidebarMobile() {
            const sidebar = saGet('rightSidebar');
            const overlay = saGet('mobileSidebarOverlay');
            const leftSidebar = saGet('leftSidebar');

            if (!sidebar) return;

            if (window.innerWidth < 768) {
                leftSidebar?.classList.remove('is-open');

                const isClosed = !sidebar.classList.contains('is-open');

                if (isClosed) {
                    sidebar.classList.add('is-open');
                    overlay?.classList.add('is-active');
                } else {
                    sidebar.classList.remove('is-open');
                    overlay?.classList.remove('is-active');
                }
            }
        }

        /**
         * ============================================================
         * DROPDOWNS
         * ============================================================
         */
        function toggleDropdown(id) {
            const dropdown = saGet(id);
            if (!dropdown) return;

            const isShowing = dropdown.classList.contains('show');

            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');

                if (menu.id === 'mobileFabDropdown') {
                    menu.style.transform = 'translateX(-50%) translateY(10px)';
                }
            });

            if (!isShowing) {
                dropdown.classList.add('show');

                if (id === 'mobileFabDropdown') {
                    dropdown.style.transform = 'translateX(-50%) translateY(0)';
                }
            } else if (id === 'mobileFabDropdown') {
                dropdown.style.transform = 'translateX(-50%) translateY(10px)';
            }
        }

        document.addEventListener('click', function (e) {
            const clickedDropdown = e.target.closest('.dropdown-menu');
            const clickedDropdownButton = e.target.closest('button[onclick^="toggleDropdown"]');
            const clickedFab = e.target.closest('.fab-main');

            if (clickedDropdown || clickedDropdownButton || clickedFab) return;

            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');

                if (menu.id === 'mobileFabDropdown') {
                    menu.style.transform = 'translateX(-50%) translateY(10px)';
                }
            });
        });

        /**
         * ============================================================
         * LEFT SIDEBAR SUBMENUS
         * ============================================================
         */
        function toggleSubmenu(id) {
            const submenu = saGet(id);
            const icon = saGet('icon-' + id);

            if (!submenu) return;

            if (submenu.classList.contains('open')) {
                submenu.classList.remove('open');

                if (icon) {
                    icon.style.transform = 'rotate(0deg)';
                }
            } else {
                submenu.classList.add('open');

                if (icon) {
                    icon.style.transform = 'rotate(180deg)';
                }
            }
        }

        /**
         * ============================================================
         * QUICK SIDER
         * ============================================================
         */
        function toggleQuickSider(event, forceState = null) {
            if (event) {
                event.preventDefault?.();
                event.stopPropagation?.();
            }

            const overlay = saGet('quickSiderOverlay');
            const sider = saGet('quickSider');
            const button = saGet('quickMenuButton');

            if (!sider) return false;

            const isOpen = sider.classList.contains('is-open');
            const shouldOpen = forceState === null ? !isOpen : Boolean(forceState);

            sider.classList.toggle('is-open', shouldOpen);
            overlay?.classList.toggle('is-active', shouldOpen);
            button?.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');

            if (shouldOpen) {
                document.querySelectorAll('.dropdown-menu.show').forEach(menu => menu.classList.remove('show'));
            }

            saRefreshIcons?.();
            return false;
        }

        function openQuickMenuChat(event) {
            if (event) {
                event.preventDefault?.();
                event.stopPropagation?.();
            }

            window.location.href = @json(url('admin/chat'));
            return false;
        }

        function submitSaLogout(event) {
            if (event) {
                event.preventDefault?.();
                event.stopPropagation?.();
            }

            const form = saGet('logout-form');

            if (form) {
                form.submit();
                return false;
            }

            window.location.href = @json($logoutUrl);
            return false;
        }

        /**
         * ============================================================
         * REAL RIGHT SIDEBAR CHAT ONLY
         * ============================================================
         * This block controls only:
         * - right sidebar chat contacts
         * - chat popup windows
         * - private + group message sending
         * - file/image sending
         * - group sender labels
         * - realtime Echo updates
         *
         * It does not touch sidebar, search, theme, dropdowns or other layout scripts.
         */
        const chatContainer = saGet('chatContainer');
        const realChatContactList = saGet('realChatContactList');

        const openChats = new Map(); 

        const currentChatUser = {
            id: Number(saMeta('chat-user-id', '0')),
            name: saMeta('chat-user-name', 'Ich') || 'Ich',
            employeeId: saMeta('employee-id', ''),
        };

        const chatCsrf = saMeta('csrf-token', '');

        const chatRoutes = {
            employees: '/chat/employees',
            fetchUser: id => `/chat/fetch/${id}`,
            fetchGroup: id => `/chat/group/fetch/${id}`,
            markReadUser: id => `/chat/mark-read/${id}`,
            markReadGroup: id => `/chat/group/mark-read/${id}`,
            send: '/chat/send',
        };

        const chatNotificationState = {
            contacts: [],
            unreadByKey: new Map(),
        };

        function chatContactKey(contact) {
            return `${contact?.type === 'group' ? 'group' : 'user'}-${contact?.id}`;
        }

        function chatDomId(contactOrKey) {
            const key = typeof contactOrKey === 'string' ? contactOrKey : chatContactKey(contactOrKey);
            return `chat-${String(key).replace(/[^a-zA-Z0-9_-]/g, '')}`;
        }

        function getOpenChatContact(target) {
            if (target && typeof target === 'object' && target.id) {
                return target;
            }

            const key = String(target || '');

            if (openChats.has(key)) {
                return openChats.get(key);
            }

            return chatNotificationState.contacts.find(item => {
                return chatContactKey(item) === key ||
                    String(item.id) === key ||
                    String(item.user_id || '') === key ||
                    String(item.chat_user_id || '') === key;
            }) || null;
        }

        function chatDisplayName(employee) {
            return `${employee?.name ?? ''} ${employee?.lastname ?? ''}`.trim()
                || employee?.display_name
                || employee?.full_name
                || `User #${employee?.id ?? ''}`;
        }

        function chatDisplayTitle(contact) {
            if (contact?.type === 'group') {
                return contact.name || contact.context_label || `Gruppe #${contact.id}`;
            }

            return chatDisplayName(contact);
        }

        function chatAvatarUrl(contact) {
            const image = contact?.image || contact?.avatar || contact?.avatar_url;

            if (image) {
                const imageString = String(image);

                if (imageString.startsWith('http') || imageString.startsWith('/')) {
                    return imageString;
                }

                if (contact?.type === 'group') {
                    return `/storage/${imageString.replace(/^storage\//, '')}`;
                }

                return `/images/employee/${imageString}`;
            }

            return `https://ui-avatars.com/api/?name=${encodeURIComponent(chatDisplayTitle(contact) || 'User')}&background=74b2d4&color=fff`;
        }

        function collectOnlineIdsFromPresenceUser(user) {
            return [
                user?.id,
                user?.user_id,
                user?.chat_user_id,
                user?.employee_id,
                user?.employee?.id,
            ].map(value => String(value || '').trim()).filter(Boolean);
        }

       const chatOnlineIds = new Set();
        const chatOnlineLeftTimers = new Map();

        function collectOnlineIdsFromPresenceUser(user) {
            return [
                user?.id,
                user?.user_id,
                user?.chat_user_id,
                user?.employee_id,
                user?.employee?.id,
                user?.name, // important if Laravel user.name stores employee_id
            ]
                .map(value => String(value || '').trim())
                .filter(Boolean);
        }

        function rememberOnlineUser(user) {
            collectOnlineIdsFromPresenceUser(user).forEach(id => {
                if (chatOnlineLeftTimers.has(id)) {
                    clearTimeout(chatOnlineLeftTimers.get(id));
                    chatOnlineLeftTimers.delete(id);
                }

                chatOnlineIds.add(id);
            });
        }

        function forgetOnlineUser(user) {
            collectOnlineIdsFromPresenceUser(user).forEach(id => {
                if (chatOnlineLeftTimers.has(id)) {
                    clearTimeout(chatOnlineLeftTimers.get(id));
                }

                const timer = setTimeout(() => {
                    chatOnlineIds.delete(id);
                    chatOnlineLeftTimers.delete(id);

                    chatNotificationState.contacts = chatNotificationState.contacts.map(contact => ({
                        ...contact,
                        online: isChatContactOnline(contact),
                    }));

                    renderRightSidebarContacts(chatNotificationState.contacts);
                }, 8000);

                chatOnlineLeftTimers.set(id, timer);
            });
        }

        function isChatContactOnline(contact) {
            if (!contact || contact.type === 'group') return false;

            const possibleIds = [
                contact.id,
                contact.user_id,
                contact.chat_user_id,
                contact.employee_id,
                contact.employee?.id,
                contact.name, // your users.name can be employee_id
            ]
                .map(value => String(value || '').trim())
                .filter(Boolean);

            return possibleIds.some(id => chatOnlineIds.has(id));
        }

        function normalizeChatMessage(message) {
            const sender = message?.sender || message?.from_user || message?.user || message?.employee || null;

            const senderName = (
                message?.sender_label ||
                message?.senderName ||
                message?.sender_name ||
                message?.from_user_name ||
                message?.user_name ||
                message?.employee_name ||
                message?.from_user?.display_name ||
                message?.from_user?.full_name ||
                message?.sender?.display_name ||
                message?.sender?.full_name ||
                `${message?.from_user?.name ?? ''} ${message?.from_user?.lastname ?? ''}`.trim() ||
                `${sender?.name ?? ''} ${sender?.lastname ?? ''}`.trim()
            );

            const attachments = Array.isArray(message?.attachments) ? message.attachments : [];
            const firstAttachment = attachments[0] || null;

            return {
                id: message?.id || message?.message_id || null,
                fromUserId: Number(message?.from_user_id || message?.fromUserId || message?.user_id || message?.sender_id || message?.from_user?.id || 0),
                toUserId: Number(message?.to_user_id || message?.toUserId || message?.receiver_id || message?.to_user?.id || 0),
                groupId: Number(message?.group_id || message?.groupId || 0),
                senderName: senderName || 'Unbekannter Benutzer',
                text: message?.message || message?.body || message?.text || '',
                createdAt: message?.created_at || message?.createdAt || new Date().toISOString(),
                type: message?.type || message?.message_type || 'text',
                fileUrl: message?.file_url || message?.audio_url || message?.image_url || message?.attachment_url || message?.url || firstAttachment?.url || '',
                fileName: message?.file_name || message?.filename || message?.original_name || firstAttachment?.name || 'Datei',
                attachments,
                readers: Array.isArray(message?.readers) ? message.readers : [],
            };
        }

        function renderSingleChatAttachment(attachment, fallbackType = '') {
            const fileUrl = attachment?.url || attachment?.fileUrl || attachment?.path || '';
            if (!fileUrl) return '';

            const url = String(fileUrl).startsWith('http') || String(fileUrl).startsWith('/')
                ? String(fileUrl)
                : `/storage/${String(fileUrl).replace(/^storage\//, '')}`;

            const name = attachment?.name || attachment?.fileName || attachment?.original_name || attachment?.filename || 'Datei';
            const type = String(attachment?.type || attachment?.mime || fallbackType || '').toLowerCase();

            const isImage = attachment?.is_image || type.startsWith('image/') || /\.(png|jpe?g|gif|webp|bmp|svg)$/i.test(url);
            const isAudio = type.startsWith('audio/') || /\.(webm|mp3|wav|m4a|ogg)$/i.test(url) || fallbackType === 'voice' || fallbackType === 'audio';

            if (isImage) {
                return `
                <a href="${saEscapeHtml(url)}" target="_blank" rel="noopener noreferrer">
                    <img src="${saEscapeHtml(url)}" alt="${saEscapeHtml(name)}" style="max-width:180px;border-radius:10px;display:block;margin-bottom:6px;">
                </a>
            `;
            }

            if (isAudio) {
                return `<audio controls preload="none" src="${saEscapeHtml(url)}" style="max-width:220px;display:block;margin-bottom:6px;"></audio>`;
            }

            return `
            <a href="${saEscapeHtml(url)}" target="_blank" rel="noopener noreferrer" style="display:inline-flex;align-items:center;gap:6px;color:inherit;font-weight:700;">
                <i data-lucide="file" class="icon-sm"></i>
                ${saEscapeHtml(name)}
            </a>
        `;
        }

        function renderChatAttachment(message) {
            const attachments = Array.isArray(message.attachments) ? message.attachments : [];

            if (attachments.length) {
                return attachments.map(attachment => renderSingleChatAttachment(attachment, message.type)).join('');
            }

            if (!message.fileUrl) return '';

            return renderSingleChatAttachment({
                url: message.fileUrl,
                name: message.fileName,
                type: message.type,
            }, message.type);
        }

        function chatDateKey(date) {
            const d = date instanceof Date ? date : new Date(date);
            if (Number.isNaN(d.getTime())) return '';

            return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
        }

        function chatDateLabel(date) {
            const d = date instanceof Date ? date : new Date(date);
            if (Number.isNaN(d.getTime())) return '';

            const today = new Date();
            const yesterday = new Date();
            yesterday.setDate(today.getDate() - 1);

            if (chatDateKey(d) === chatDateKey(today)) return 'Heute';
            if (chatDateKey(d) === chatDateKey(yesterday)) return 'Gestern';

            return d.toLocaleDateString('de-DE', {
                weekday: 'short',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
            });
        }

        function appendChatDateDividerIfNeeded(body, createdAt) {
            if (!body || !createdAt) return;

            const dateKey = chatDateKey(createdAt);
            if (!dateKey) return;

            const lastChild = body.lastElementChild;
            const lastMessageDate = lastChild?.dataset?.date || '';

            if (lastMessageDate === dateKey) return;

            const divider = document.createElement('div');
            divider.className = 'chat-date-divider';
            divider.dataset.date = dateKey;
            divider.textContent = chatDateLabel(createdAt);

            body.appendChild(divider);
        }

        function calculateTotalUnread() {
            let total = 0;

            chatNotificationState.unreadByKey.forEach(value => {
                total += Number(value || 0);
            });

            return total;
        }

        function updateGlobalUnreadBadges(totalUnread) {
            const count = Number(totalUnread || 0);
            const label = count > 99 ? '99+' : String(count);

            ['mobileChatUnreadBadge', 'rightSidebarChatUnreadBadge', 'quickChatUnreadBadge', 'hoverChatUnreadBadge'].forEach(id => {
                const badge = saGet(id);
                if (!badge) return;

                if (count > 0) {
                    badge.textContent = label;
                    badge.style.display = 'inline-flex';
                } else {
                    badge.textContent = '0';
                    badge.style.display = 'none';
                }
            });
        }

        function setContactUnread(contactOrKey, count) {
            const key = typeof contactOrKey === 'string' ? contactOrKey : chatContactKey(contactOrKey);
            const value = Number(count || 0);

            if (value > 0) {
                chatNotificationState.unreadByKey.set(key, value);
            } else {
                chatNotificationState.unreadByKey.delete(key);
            }

            updateGlobalUnreadBadges(calculateTotalUnread());
        }

        function increaseContactUnread(contactOrKey, amount = 1) {
            const key = typeof contactOrKey === 'string' ? contactOrKey : chatContactKey(contactOrKey);
            const current = Number(chatNotificationState.unreadByKey.get(key) || 0);

            setContactUnread(key, current + amount);
        }

        function updateUnreadBadgesFromContacts(contacts) {
            chatNotificationState.unreadByKey.clear();

            contacts.forEach(contact => {
                if (Number(contact.unread || 0) > 0) {
                    chatNotificationState.unreadByKey.set(chatContactKey(contact), Number(contact.unread || 0));
                }
            });

            updateGlobalUnreadBadges(calculateTotalUnread());
        }

        function getChatSound() {
            return saGet('chatNotificationSound') || saGet('notificationSound');
        }

        function playChatNotificationSound() {
            const sound = getChatSound();
            if (!sound) return;

            try {
                sound.currentTime = 0;
                sound.play().catch(() => { });
            } catch (error) {
                console.warn('Chat sound failed:', error);
            }
        }

        function createChatToastWrap() {
            let wrap = saGet('chatToastWrap');

            if (!wrap) {
                wrap = document.createElement('div');
                wrap.id = 'chatToastWrap';
                wrap.className = 'chat-toast-wrap';
                document.body.appendChild(wrap);
            }

            return wrap;
        }

        function getMessagePreviewFromEvent(event) {
            const text = event?.message || event?.body || event?.text || '';
            if (text) return text;

            const type = String(event?.type || event?.message_type || '').toLowerCase();

            if (type === 'image') return '📷 Bild';
            if (type === 'voice' || type === 'audio') return '🎤 Sprachnachricht';
            if (type === 'file') return '📎 Datei';

            if (event?.file_url || event?.attachment_url || event?.image_url || event?.audio_url) {
                return '📎 Anhang';
            }

            return 'Neue Nachricht';
        }

        function showInAppChatToast(contact, messageText) {
            const wrap = createChatToastWrap();
            const toast = document.createElement('div');
            const title = chatDisplayTitle(contact) || 'Neue Nachricht';
            const avatar = chatAvatarUrl(contact || { displayName: title });

            toast.className = 'chat-toast';
            toast.innerHTML = `
            <img src="${saEscapeHtml(avatar)}" class="chat-toast-avatar" alt="${saEscapeHtml(title)}">
            <div style="flex:1;min-width:0;">
                <p class="chat-toast-title truncate">${saEscapeHtml(title)}</p>
                <p class="chat-toast-message">${saEscapeHtml(messageText || 'Du hast eine neue Nachricht erhalten.')}</p>
            </div>
            <button type="button" class="chat-toast-close" title="Schließen" aria-label="Schließen">
                <i data-lucide="x" class="icon-sm"></i>
            </button>
        `;

            toast.querySelector('.chat-toast-close')?.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                toast.remove();
            });

            toast.addEventListener('click', function () {
                if (contact) openChat(contact);
                toast.remove();
            });

            wrap.appendChild(toast);
            saRefreshIcons();

            setTimeout(() => toast.remove(), 7000);
        }

        function showBrowserChatNotification(contact, messageText) {
            if (!('Notification' in window)) return;
            if (Notification.permission !== 'granted') return;
            if (document.hasFocus()) return;

            const title = chatDisplayTitle(contact) || 'Neue Nachricht';
            const notification = new Notification(title, {
                body: messageText || 'Du hast eine neue Nachricht erhalten.',
                icon: chatAvatarUrl(contact || { displayName: title }),
                tag: `chat-${chatContactKey(contact || { id: Date.now() })}`,
            });

            notification.onclick = function () {
                window.focus();
                if (contact) openChat(contact);
                notification.close();
            };
        }

        function notifyIncomingChatMessage(contact, event) {
            const messagePreview = getMessagePreviewFromEvent(event);

            playChatNotificationSound();
            showInAppChatToast(contact, messagePreview);
            showBrowserChatNotification(contact, messagePreview);
        }

        async function loadRightSidebarContacts() {
            if (!realChatContactList || !currentChatUser.id) return;

            try {
                const response = await fetch(chatRoutes.employees, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                const data = await saSafeJson(response);

                const employees = (data.employees || []).map(employee => {
                    const contact = {
                        ...employee,
                        type: 'user',
                        displayName: chatDisplayName(employee),
                        unread: Number(employee.unread || 0),
                    };

                    return {
                        ...contact,
                        online: isChatContactOnline(contact),
                    };
                });

                const groups = (data.groups || []).map(group => ({
                    ...group,
                    type: 'group',
                    displayName: group.name || group.context_label || `Gruppe #${group.id}`,
                    unread: Number(group.unread || 0),
                    online: false,
                }));

                const contacts = [...groups, ...employees].sort((a, b) => {
                    if ((b.unread || 0) !== (a.unread || 0)) return (b.unread || 0) - (a.unread || 0);
                    if (Number(b.is_pinned) !== Number(a.is_pinned)) return Number(b.is_pinned) - Number(a.is_pinned);
                    return Number(b.last_msg_at || 0) - Number(a.last_msg_at || 0);
                });

                chatNotificationState.contacts = contacts;
                updateUnreadBadgesFromContacts(contacts);
                renderRightSidebarContacts(contacts);
            } catch (error) {
                console.error('Chat contacts could not be loaded:', error);

                realChatContactList.innerHTML = `
                <div class="hide-on-collapse" style="padding:12px;color:var(--color-danger);font-size:12px;">
                    Kontakte konnten nicht geladen werden.
                </div>
            `;
            }
        }

        function getContactRecentMessage(contact) {
            if (!contact) return '';

            const possibleValues = [
                contact.last_msg,
                contact.last_message_text,
                contact.message_preview,
                contact.latest_message_text,
                contact.latestMessageText,
                contact.lastMessageText,
                contact.preview,
                contact.last_preview,
                contact.recent_message,
                contact.recentMessage,
                contact.lastMessage,
                contact.latestMessage,
                contact.last_message,
                contact.latest_message,
            ];

            for (const value of possibleValues) {
                if (!value) continue;

                if (typeof value === 'string' || typeof value === 'number') {
                    const text = String(value).trim();
                    if (text) return text;
                }

                if (typeof value === 'object') {
                    const nestedText =
                        value.message ||
                        value.body ||
                        value.text ||
                        value.content ||
                        value.caption ||
                        value.file_name ||
                        value.filename ||
                        value.original_name ||
                        '';

                    if (nestedText && String(nestedText).trim()) {
                        return String(nestedText).trim();
                    }

                    const nestedType = String(value.type || value.message_type || '').toLowerCase();

                    if (nestedType === 'image') return '📷 Bild';
                    if (nestedType === 'voice' || nestedType === 'audio') return '🎤 Sprachnachricht';
                    if (nestedType === 'file') return '📎 Datei';

                    if (value.file_url || value.attachment_url || value.image_url || value.audio_url) {
                        return '📎 Anhang';
                    }
                }
            }

            if (contact.file_url || contact.attachment_url || contact.image_url || contact.audio_url) {
                return '📎 Anhang';
            }

            return '';
        }

        function getContactRecentMessageTime(contact) {
            const raw =
                contact?.last_msg_at ||
                contact?.last_message_at ||
                contact?.latest_message_at ||
                contact?.lastMessageAt ||
                contact?.latestMessageAt ||
                contact?.last_message?.created_at ||
                contact?.latest_message?.created_at ||
                contact?.lastMessage?.created_at ||
                contact?.latestMessage?.created_at ||
                '';

            if (!raw) return '';

            const date = new Date(raw);

            if (Number.isNaN(date.getTime())) return '';

            return date.toLocaleString('de-DE', {
                day: '2-digit',
                month: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
            });
        }

        function renderRightSidebarContacts(contacts) {
            if (!realChatContactList) return;

            if (!Array.isArray(contacts) || !contacts.length) {
                realChatContactList.innerHTML = `
            <div class="hide-on-collapse" style="padding:8px;color:var(--text-muted);font-size:12px;">
                Keine Kontakte oder Gruppen gefunden.
            </div>
        `;
                return;
            }

            realChatContactList.innerHTML = contacts.map(contact => {
                const avatar = chatAvatarUrl(contact);
                const isGroup = contact.type === 'group';
                const isOnline = !isGroup && Boolean(contact.online);
                const unread = Number(contact.unread || 0);
                const title = chatDisplayTitle(contact);
                const domKey = chatContactKey(contact);

                const recentMessage = getContactRecentMessage(contact);
                const recentTime = getContactRecentMessageTime(contact);

                const customerName =
                    contact.customer_name ||
                    contact.customer?.name ||
                    contact.customer?.display_name ||
                    contact.lead_name ||
                    contact.context_label ||
                    '';

                const meta = isGroup
                    ? (customerName || contact.meta_line || 'Kundengruppe')
                    : (isOnline ? '● Online' : '● Offline');

                const previewTitle = isGroup && customerName
                    ? `${title} · ${customerName}`
                    : title;

                const previewMessage = recentMessage
                    ? recentMessage
                    : (isGroup ? 'Noch keine neue Kundennachricht.' : 'Noch keine neue Nachricht.');

                const previewTimeHtml = recentTime
                    ? `<p class="chat-hover-preview-time">${saEscapeHtml(recentTime)}</p>`
                    : '';

                return `
            <button
                type="button"
                class="user-chat-item ${isGroup ? 'is-group' : (isOnline ? 'is-online' : 'is-offline')} ${unread > 0 ? 'has-unread' : ''}"
                data-chat-key="${saEscapeHtml(domKey)}"
                data-chat-type="${saEscapeHtml(contact.type)}"
                data-chat-id="${Number(contact.id)}"
                title="${saEscapeHtml(title)}"
            >
                <span class="chat-avatar-wrap center-on-collapse">
                    <img src="${saEscapeHtml(avatar)}" alt="${saEscapeHtml(title)}" class="chat-avatar-img" loading="lazy">

                    ${isGroup ? `
                        <span class="chat-group-mini-icon">
                            <i data-lucide="users" class="icon-sm"></i>
                        </span>
                    ` : `
                        <span class="status-dot ${isOnline ? 'online' : 'offline'}" title="${isOnline ? 'Online' : 'Offline'}"></span>
                    `}
                </span>

                <span class="chat-contact-body hide-on-collapse">
                    <span class="chat-contact-top">
                        <span class="chat-contact-name">${saEscapeHtml(title)}</span>
                        ${unread > 0 ? `<span class="chat-contact-unread-badge">${unread}</span>` : ''}
                    </span>

                    <span class="${isGroup ? 'chat-group-label' : (isOnline ? 'chat-online-label' : 'chat-offline-label')}">
                        ${isGroup ? '👥 ' + saEscapeHtml(meta) : saEscapeHtml(meta)}
                    </span>
                </span>

                <span class="chat-hover-preview">
                    <p class="chat-hover-preview-title">${saEscapeHtml(previewTitle)}</p>
                    <p class="chat-hover-preview-message">${saEscapeHtml(previewMessage)}</p>
                    ${previewTimeHtml}
                </span>
            </button>
        `;
            }).join('');

            realChatContactList.querySelectorAll('[data-chat-key]').forEach(button => {
                button.addEventListener('click', function () {
                    openRealChatByKey(this.dataset.chatKey);
                });
            });

            saRefreshIcons();
        }

        function openRealChatByKey(key) {
            const contact = chatNotificationState.contacts.find(item => chatContactKey(item) === key);

            if (!contact) {
                console.warn('Chat contact not found for key:', key);
                return;
            }

            openChat(contact);
        }

        function openRealChat(id) {
            const idString = String(id);
            const contact = chatNotificationState.contacts.find(item => {
                return String(item.id) === idString ||
                    String(item.user_id || '') === idString ||
                    String(item.chat_user_id || '') === idString ||
                    chatContactKey(item) === idString;
            });

            if (!contact) {
                console.warn('Chat contact not found for id:', id);
                return;
            }

            openChat(contact);
        }

        function openChat(contact) {
            if (!chatContainer || !contact?.id) return;

            if (typeof closeAllMobileSidebars === 'function' && window.innerWidth < 768) {
                closeAllMobileSidebars();
            }

            const key = chatContactKey(contact);
            const chatId = chatDomId(key);

            if (openChats.has(key)) {
                const existingChat = saGet(chatId);

                if (existingChat) {
                    existingChat.classList.remove('minimized');
                    existingChat.querySelector('.chat-footer input[type="text"]')?.focus();
                }

                return;
            }

            const maxChats = window.innerWidth < 768 ? 1 : 2;

            if (openChats.size >= maxChats) {
                closeChat(Array.from(openChats.keys())[0]);
            }

            openChats.set(key, contact);

            const avatar = chatAvatarUrl(contact);
            const title = chatDisplayTitle(contact);
            const isGroup = contact.type === 'group';

            chatContainer.insertAdjacentHTML('beforeend', `
            <div id="${saEscapeHtml(chatId)}" class="chat-popup open" data-chat-key="${saEscapeHtml(key)}" data-chat-type="${saEscapeHtml(contact.type)}" data-chat-id="${saEscapeHtml(contact.id)}">
                <div class="chat-header">
                    <div class="chat-header-main" style="display:flex;align-items:center;gap:8px;min-width:0;flex:1;cursor:pointer;">
                        <div style="position:relative;flex-shrink:0;">
                            <img src="${saEscapeHtml(avatar)}" style="width:32px;height:32px;border-radius:50%;border:1px solid var(--border-color);object-fit:cover;" alt="${saEscapeHtml(title)}">
                            ${isGroup ? `
                                <span class="chat-group-mini-icon"><i data-lucide="users" class="icon-sm"></i></span>
                            ` : (contact.online ? `
                                <span class="status-dot online" style="width:10px;height:10px;"></span>
                            ` : '')}
                        </div>
                        <span style="font-weight:bold;font-size:14px;" class="truncate">${saEscapeHtml(title)}</span>
                    </div>

                    <div style="display:flex;align-items:center;gap:4px;">
                        <button type="button" class="icon-btn chat-minimize-btn" style="width:28px;height:28px;" title="Minimieren">
                            <i data-lucide="minus" class="icon-sm"></i>
                        </button>
                        <button type="button" class="icon-btn chat-close-btn" style="width:28px;height:28px;color:var(--color-danger);" title="Schließen">
                            <i data-lucide="x" class="icon-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="chat-body" id="${saEscapeHtml(chatId)}-body">
                    <div style="text-align:center;color:var(--text-muted);font-size:12px;">Nachrichten werden geladen...</div>
                </div>

                <div class="chat-footer">
                    <input type="file" id="${saEscapeHtml(chatId)}-file" style="display:none;">
                    <button type="button" class="chat-file-btn" style="color:var(--text-muted);" title="Datei senden">
                        <i data-lucide="paperclip" class="icon-lg"></i>
                    </button>
                    <input type="text" class="chat-message-input" placeholder="Nachricht..." style="flex:1;background-color:var(--bg-hover);border:none;border-radius:99px;padding:8px 12px;color:var(--text-main);outline:none;font-size:14px;">
                    <button type="button" class="chat-send-btn" style="color:var(--brand-blue);" title="Senden">
                        <i data-lucide="send" class="icon-lg"></i>
                    </button>
                </div>
            </div>
        `);

            const chatEl = saGet(chatId);
            const fileInput = saGet(`${chatId}-file`);
            const textInput = chatEl?.querySelector('.chat-message-input');

            chatEl?.querySelector('.chat-header-main')?.addEventListener('click', () => toggleChatMinimize(chatId));
            chatEl?.querySelector('.chat-minimize-btn')?.addEventListener('click', event => {
                event.preventDefault();
                event.stopPropagation();
                toggleChatMinimize(chatId);
            });
            chatEl?.querySelector('.chat-close-btn')?.addEventListener('click', event => {
                event.preventDefault();
                event.stopPropagation();
                closeChat(key);
            });
            chatEl?.querySelector('.chat-file-btn')?.addEventListener('click', () => fileInput?.click());
            chatEl?.querySelector('.chat-send-btn')?.addEventListener('click', () => sendRealChatMessage(key, textInput));

            textInput?.addEventListener('keydown', event => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    sendRealChatMessage(key, textInput);
                }
            });

            fileInput?.addEventListener('change', function () {
                const file = this.files?.[0];
                this.value = '';
                if (file) sendRealChatFile(key, file);
            });

            textInput?.addEventListener('paste', event => {
                const items = event.clipboardData?.items || [];

                for (const item of items) {
                    if (item.type && item.type.startsWith('image/')) {
                        const file = item.getAsFile();
                        if (!file) continue;

                        event.preventDefault();
                        sendRealChatFile(key, new File([file], `pasted-image-${Date.now()}.png`, { type: file.type }));
                    }
                }
            });

            saRefreshIcons();
            fetchChatMessages(contact);

            if (isGroup) {
                markGroupChatRead(contact.id);
            } else {
                markChatRead(contact.id);
            }

            setContactUnread(key, 0);

            chatNotificationState.contacts = chatNotificationState.contacts.map(item => {
                if (chatContactKey(item) === key) {
                    return { ...item, unread: 0 };
                }
                return item;
            });

            renderRightSidebarContacts(chatNotificationState.contacts);

            setTimeout(() => textInput?.focus(), 50);
        }

        function closeChat(key) {
            const normalizedKey = String(key || '');
            const contact = getOpenChatContact(normalizedKey);
            const chatId = contact ? chatDomId(chatContactKey(contact)) : chatDomId(normalizedKey);

            saGet(chatId)?.remove();
            openChats.delete(normalizedKey);

            if (contact) {
                openChats.delete(chatContactKey(contact));
            }
        }

        function toggleChatMinimize(chatId) {
            if (window.innerWidth < 768) return;
            saGet(chatId)?.classList.toggle('minimized');
        }

        async function fetchChatMessages(contact) {
            const chatId = chatDomId(contact);
            const body = saGet(`${chatId}-body`);

            if (!body) return;

            const isGroup = contact.type === 'group';
            const url = isGroup ? chatRoutes.fetchGroup(contact.id) : chatRoutes.fetchUser(contact.id);

            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                const data = await saSafeJson(response);
                const messages = Array.isArray(data) ? data : (data.messages || []);

                body.innerHTML = '';

                if (!messages.length) {
                    body.innerHTML = '<div style="text-align:center;color:var(--text-muted);font-size:12px;">Noch keine Nachrichten.</div>';
                    return;
                }

                messages
                    .map(normalizeChatMessage)
                    .sort((a, b) => new Date(a.createdAt) - new Date(b.createdAt))
                    .forEach(message => appendChatMessage(contact, message));

                body.scrollTop = body.scrollHeight;

                if (isGroup) {
                    markGroupChatRead(contact.id);
                } else {
                    markChatRead(contact.id);
                }
            } catch (error) {
                console.error('Messages could not be loaded:', error);
                body.innerHTML = '<div style="text-align:center;color:var(--color-danger);font-size:12px;">Nachrichten konnten nicht geladen werden.</div>';
            }
        }

        function appendChatMessage(contact, rawMessage) {
            if (!contact || !contact.id) return;

            const body = saGet(`${chatDomId(contact)}-body`);
            if (!body) return;

            const message = rawMessage?.fromUserId !== undefined ? rawMessage : normalizeChatMessage(rawMessage);
            const isMe = Number(message.fromUserId) === Number(currentChatUser.id);
            const isGroup = contact.type === 'group';
            const createdAt = message.createdAt ? new Date(message.createdAt) : new Date();
            const dateKey = chatDateKey(createdAt);

            appendChatDateDividerIfNeeded(body, createdAt);

            const time = createdAt.toLocaleTimeString('de-DE', {
                hour: '2-digit',
                minute: '2-digit',
            });

            const bubble = document.createElement('div');
            bubble.className = `chat-msg ${isMe ? 'me' : 'them'}`;
            bubble.dataset.date = dateKey;

            if (message.id) {
                bubble.dataset.messageId = String(message.id);
            }

            const attachmentHtml = renderChatAttachment(message);
            const textHtml = message.text ? `<div class="chat-msg-text" style="white-space:pre-wrap;">${saEscapeHtml(message.text)}</div>` : '';
            const senderName = isMe
                ? 'Ich'
                : (message.senderName || message.sender_label || message.sender_name || message.from_user_name || 'Unbekannter Benutzer');

            bubble.innerHTML = `
            ${isGroup ? `<div class="chat-msg-sender">${saEscapeHtml(senderName)}</div>` : ''}
            ${attachmentHtml}
            ${textHtml}
            <div class="chat-msg-time">${saEscapeHtml(time)}</div>
        `;

            body.appendChild(bubble);
            body.scrollTop = body.scrollHeight;
            saRefreshIcons();
        }

        async function sendRealChatMessage(target, input) {
            if (!input) return;

            const contact = getOpenChatContact(target);

            if (!contact) {
                console.error('Chat contact not found for sending:', target);
                return;
            }

            const text = input.value.trim();
            if (!text) return;

            input.value = '';

            const isGroup = contact.type === 'group';
            const key = chatContactKey(contact);

            appendChatMessage(contact, {
                fromUserId: currentChatUser.id,
                toUserId: isGroup ? 0 : contact.id,
                groupId: isGroup ? contact.id : 0,
                senderName: currentChatUser.name || 'Ich',
                text,
                createdAt: new Date().toISOString(),
                type: 'text',
            });

            const payload = {
                message: text,
                type: 'text',
            };

            if (isGroup) {
                payload.group_id = contact.id;
            } else {
                payload.to_user_id = contact.id;
            }

            try {
                const response = await fetch(chatRoutes.send, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': chatCsrf,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload),
                });

                await saSafeJson(response).catch(() => null);
                setContactUnread(key, 0);
            } catch (error) {
                console.error('Message could not be sent:', error);
            }
        }

        async function sendRealChatFile(target, file) {
            if (!file) return;

            const contact = getOpenChatContact(target);

            if (!contact) {
                console.error('Chat contact not found for file sending:', target);
                return;
            }

            const isGroup = contact.type === 'group';
            const isImage = file.type && file.type.startsWith('image/');
            const localUrl = URL.createObjectURL(file);

            appendChatMessage(contact, {
                fromUserId: currentChatUser.id,
                toUserId: isGroup ? 0 : contact.id,
                groupId: isGroup ? contact.id : 0,
                senderName: currentChatUser.name || 'Ich',
                text: '',
                createdAt: new Date().toISOString(),
                type: isImage ? 'image' : 'file',
                fileUrl: localUrl,
                fileName: file.name,
            });

            const formData = new FormData();
            formData.append('file', file);
            formData.append('message', '');
            formData.append('type', isImage ? 'image' : 'file');

            if (isGroup) {
                formData.append('group_id', contact.id);
            } else {
                formData.append('to_user_id', contact.id);
            }

            try {
                const response = await fetch(chatRoutes.send, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': chatCsrf,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                    body: formData,
                });

                await saSafeJson(response).catch(() => null);
            } catch (error) {
                console.error('File could not be sent:', error);
            }
        }

        function markChatRead(userId) {
            fetch(chatRoutes.markReadUser(userId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': chatCsrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            }).catch(() => { });
        }

        function markGroupChatRead(groupId) {
            fetch(chatRoutes.markReadGroup(groupId), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': chatCsrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            }).catch(() => { });
        }

        function findContactForIncomingEvent(event) {
            const groupId = Number(event?.group_id || event?.groupId || 0);

            if (groupId) {
                return chatNotificationState.contacts.find(contact => contact.type === 'group' && Number(contact.id) === groupId) || {
                    id: groupId,
                    type: 'group',
                    name: event?.group_name || `Gruppe #${groupId}`,
                    displayName: event?.group_name || `Gruppe #${groupId}`,
                    unread: 0,
                    online: false,
                };
            }

            const fromId = Number(event?.from_user_id || event?.fromUserId || event?.from_user?.id || event?.user_id || event?.sender_id || 0);
            const toId = Number(event?.to_user_id || event?.toUserId || event?.to_user?.id || event?.receiver_id || 0);
            const isOwnMessage = fromId === Number(currentChatUser.id);
            const partnerId = isOwnMessage ? toId : fromId;

            return chatNotificationState.contacts.find(contact => contact.type !== 'group' && String(contact.id) === String(partnerId)) || {
                id: partnerId,
                type: 'user',
                displayName: event?.from_user?.name || event?.sender_name || 'Neue Nachricht',
                name: event?.from_user?.name || event?.sender_name || '',
                lastname: event?.from_user?.lastname || '',
                image: event?.from_user?.image || event?.sender_avatar || null,
                unread: 0,
                online: true,
            };
        }

        function shouldNotifyForMessage(contact, event) {
            const message = normalizeChatMessage(event);

            if (Number(message.fromUserId) === Number(currentChatUser.id)) return false;

            const chatWindow = saGet(chatDomId(chatContactKey(contact)));

            if (!chatWindow) return true;
            if (chatWindow.classList.contains('minimized')) return true;
            if (!document.hasFocus()) return true;

            return false;
        }

        function handleIncomingChatEvent(event) {
            const message = normalizeChatMessage(event);
            const contact = findContactForIncomingEvent(event);

            if (!contact || !contact.id) return;

            const key = chatContactKey(contact);
            const isOwnMessage = Number(message.fromUserId) === Number(currentChatUser.id);
            const chatIsOpen = openChats.has(key);
            const chatWindow = saGet(chatDomId(key));

            if (chatIsOpen && !isOwnMessage) {
                appendChatMessage(contact, message);

                if (chatWindow?.classList.contains('minimized')) {
                    const header = chatWindow.querySelector('.chat-header');
                    header?.classList.add('flash');
                    setTimeout(() => header?.classList.remove('flash'), 2500);
                }

                if (shouldNotifyForMessage(contact, event)) {
                    increaseContactUnread(key, 1);
                    notifyIncomingChatMessage(contact, event);
                } else {
                    if (contact.type === 'group') markGroupChatRead(contact.id);
                    else markChatRead(contact.id);
                    setContactUnread(key, 0);
                }

                return;
            }

            if (!chatIsOpen && !isOwnMessage) {
                increaseContactUnread(key, 1);
                notifyIncomingChatMessage(contact, event);
            }

            loadRightSidebarContacts();
        }

        let chatPresenceInitialized = false;

            function refreshRightSidebarOnlineState() {
                chatNotificationState.contacts = chatNotificationState.contacts.map(contact => ({
                    ...contact,
                    online: isChatContactOnline(contact),
                }));

                renderRightSidebarContacts(chatNotificationState.contacts);
            }

            function initChatEchoListeners() {
                if (!currentChatUser.id) return;

                if (!window.Echo || typeof window.Echo.private !== 'function') {
                    setTimeout(initChatEchoListeners, 700);
                    return;
                }

                if (chatPresenceInitialized) return;
                chatPresenceInitialized = true;

                try {
                    window.Echo.private(`chat.user.${currentChatUser.id}`)
                        .listen('.message-sent', handleIncomingChatEvent)
                        .listen('MessageSent', handleIncomingChatEvent);

                    if (typeof window.Echo.join === 'function') {
                        window.Echo.join('online')
                            .here(users => {
                                chatOnlineIds.clear();

                                (users || []).forEach(user => {
                                    rememberOnlineUser(user);
                                });

                                refreshRightSidebarOnlineState();
                            })
                            .joining(user => {
                                rememberOnlineUser(user);
                                refreshRightSidebarOnlineState();
                            })
                            .leaving(user => {
                                forgetOnlineUser(user);
                                // Do not refresh immediately here.
                                // The 8-second timer prevents temporary offline flicker.
                            })
                            .error(error => {
                                console.warn('Chat presence channel error:', error);
                            });
                    }
                } catch (error) {
                    chatPresenceInitialized = false;
                    console.warn('Echo chat listener could not be initialized:', error);
                }
            }

        window.openRealChat = openRealChat;
        window.openRealChatByKey = openRealChatByKey;
        window.openChat = openChat;
        window.closeChat = closeChat;
        window.toggleChatMinimize = toggleChatMinimize;
        window.sendRealChatMessage = sendRealChatMessage;
        window.sendRealChatFile = sendRealChatFile;
        window.loadRightSidebarContacts = loadRightSidebarContacts;


        /**
         * ============================================================
         * KEYBOARD SHORTCUTS
         * ============================================================
         */
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                saGet('searchInput')?.focus();
            }

            if (e.key === 'Escape') {
                const quickSider = saGet('quickSider');

                if (quickSider?.classList.contains('is-open')) {
                    toggleQuickSider();
                }

                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });

        /**
         * ============================================================
         * STARTUP
         * ============================================================
         */
        document.addEventListener('DOMContentLoaded', function () {
            loadRightSidebarContacts();
            initChatEchoListeners();

            setInterval(loadRightSidebarContacts, 30000);
        });
    </script>

    <script>
        function toggleSidebarProfileMenu(event) {
            if (event) {
                event.preventDefault();
                event.stopPropagation();
            }

            const menu = document.getElementById('sidebarProfileMenu');
            if (!menu) return;

            document.querySelectorAll('.dropdown-menu.show').forEach(item => {
                item.classList.remove('show');
            });

            menu.classList.toggle('is-open');

            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        }

        document.addEventListener('click', function (event) {
            const menu = document.getElementById('sidebarProfileMenu');
            const clickedInsideFooter = event.target.closest('.sidebar-footer');

            if (!menu) return;

            if (!clickedInsideFooter) {
                menu.classList.remove('is-open');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                document.getElementById('sidebarProfileMenu')?.classList.remove('is-open');
            }
        });
    </script>
    <script>
        function updateSidebarThemeButton() {
            const isDark = document.documentElement.classList.contains('dark');

            const label = document.getElementById('sidebarThemeLabel');
            const moon = document.querySelector('.dark-mode-icon-moon');
            const sun = document.querySelector('.dark-mode-icon-sun');

            if (label) {
                label.textContent = isDark ? 'Light Mode' : 'Dark Mode';
            }

            if (moon) {
                moon.style.display = isDark ? 'none' : 'inline-block';
            }

            if (sun) {
                sun.style.display = isDark ? 'inline-block' : 'none';
            }

            if (window.lucide && typeof window.lucide.createIcons === 'function') {
                window.lucide.createIcons();
            }
        }

        const originalToggleDarkMode = window.toggleDarkMode;

        window.toggleDarkMode = function (event) {
            if (typeof originalToggleDarkMode === 'function') {
                originalToggleDarkMode(event);
            } else {
                if (event) event.preventDefault();

                document.documentElement.classList.toggle('dark');

                localStorage.setItem(
                    'theme',
                    document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                );
            }

            updateSidebarThemeButton();
        };

        document.addEventListener('DOMContentLoaded', function () {
            updateSidebarThemeButton();
        });

        document.addEventListener('click', function (event) {
            const statusButton = event.target.closest('[data-sa-status]');
            if (!statusButton) return;

            const status = statusButton.dataset.saStatus;

            document.querySelectorAll('[data-sa-status]').forEach(button => {
                button.classList.remove('is-active-status');
            });

            statusButton.classList.add('is-active-status');

            console.log('Status changed:', status);

            /*
            Optional AJAX save:
    
            fetch('/employee/status/update', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({
                    status: status,
                }),
            });
            */
        });


    </script>
    <!-- Globale search  -->
    <script>
        (function () {
            'use strict';

            if (window.__SA_GLOBAL_SEARCH_BOOTED__) return;
            window.__SA_GLOBAL_SEARCH_BOOTED__ = true;

            function get(id) {
                return document.getElementById(id);
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function cleanSearchAddress(value) {
                return String(value || '')
                    .replace(/\b(lat|latitude)\s*[:=]?\s*-?\d+(\.\d+)?\s*[,|;]?\s*/gi, '')
                    .replace(/\b(lng|lon|long|longitude)\s*[:=]?\s*-?\d+(\.\d+)?\s*[,|;]?\s*/gi, '')
                    .replace(/\b-?\d{1,3}\.\d{4,}\s*,\s*-?\d{1,3}\.\d{4,}\b/g, '')
                    .replace(/\s{2,}/g, ' ')
                    .replace(/^[,\s|;-]+|[,\s|;-]+$/g, '')
                    .trim();
            }

            function refreshIcons() {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }

                if (window.feather && typeof window.feather.replace === 'function') {
                    window.feather.replace();
                }
            }

            window.openMobileSearch = function (event) {
                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                if (typeof window.closeAllMobileSidebars === 'function') {
                    window.closeAllMobileSidebars();
                }

                document.body.classList.add('mobile-search-open');

                const input = get('searchInput');
                const dropdown = get('searchResultsDropdown');

                setTimeout(function () {
                    input?.focus();
                    input?.select();

                    if (input && input.value.trim().length >= 2) {
                        dropdown?.classList.add('is-active');
                    }
                }, 50);

                refreshIcons();
            };

            window.closeMobileSearch = function (event) {
                if (event) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                document.body.classList.remove('mobile-search-open');
                get('searchResultsDropdown')?.classList.remove('is-active');
            };

            document.addEventListener('DOMContentLoaded', function () {
                const input = get('searchInput');
                const dropdown = get('searchResultsDropdown');
                const content = get('searchContent');
                const loader = get('searchLoader');

                if (!input || !dropdown || !content) {
                    console.warn('Global search elements missing.');
                    return;
                }

                let searchTimeout = null;
                let abortController = null;
                let selectedIndex = -1;

                function getLucideIcon(type) {
                    const map = {
                        'Kunde': 'user',
                        'Hersteller': 'factory',
                        'Lieferant': 'truck',
                        'Anfrage': 'inbox',
                        'Mitarbeiter': 'users',
                        'Termin': 'calendar',
                        'Aufgabe': 'check-square',
                        'Ticket': 'alert-triangle',
                        'Angebot': 'file-text',
                        'Auftrag': 'briefcase',
                        'Produkt': 'box',
                    };

                    return map[type] || 'circle';
                }

                function getAvatar(item) {
                    if (item.avatar) return item.avatar;

                    if (item.type === 'Mitarbeiter' && item.image) {
                        return `/images/employee/${item.image}`;
                    }

                    if (item.image) return item.image;

                    return '/images/gender/male.png';
                }

                async function performSearch(query) {
                    query = String(query || '').trim();
                    selectedIndex = -1;

                    if (query.length < 2) {
                        dropdown.classList.remove('is-active');
                        content.innerHTML = '';
                        return;
                    }

                    if (abortController) {
                        abortController.abort();
                    }

                    abortController = new AbortController();

                    loader?.classList.remove('hidden');
                    dropdown.classList.add('is-active');

                    try {
                        const response = await fetch(`/global-search?q=${encodeURIComponent(query)}`, {
                            method: 'GET',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                            },
                            credentials: 'same-origin',
                            signal: abortController.signal,
                        });

                        if (!response.ok) {
                            throw new Error('HTTP ' + response.status);
                        }

                        const data = await response.json();
                        renderDetailedResults(Array.isArray(data) ? data : (data.results || data.data || []));
                    } catch (error) {
                        if (error.name === 'AbortError') return;

                        console.error('Search failed:', error);

                        content.innerHTML = `
                    <div style="padding:20px; text-align:center; color:var(--color-danger); font-weight:800;">
                        Suche konnte nicht geladen werden.
                    </div>
                `;
                    } finally {
                        loader?.classList.add('hidden');
                    }
                }

                function renderDetailedResults(results) {
                    if (!results || !results.length) {
                        content.innerHTML = `
                    <div style="padding:20px; text-align:center; color:var(--text-muted); font-weight:800;">
                        Keine Ergebnisse gefunden.
                    </div>
                `;
                        return;
                    }

                    content.innerHTML = results.map(function (item, index) {
                        const isDeleted = !!item.deleted_at;
                        const lucideName = getLucideIcon(item.type);
                        const avatarPath = getAvatar(item);
                        const title = `${item.name || ''} ${item.lastname || ''}`.trim() || item.title || item.label || 'Ohne Name';
                        const url = item.url || '#';
                        const address = cleanSearchAddress(item.address);

                        return `
                    <div class="search-item ${isDeleted ? 'deleted-item' : ''}"
                         data-search-index="${index}"
                         data-url="${escapeHtml(url)}"
                         role="button"
                         tabindex="0">

                        <div class="search-avatar-wrapper">
                            <img src="${escapeHtml(avatarPath)}"
                                 class="search-item-main-avatar"
                                 alt="${escapeHtml(title)}"
                                 onerror="this.src='/images/gender/male.png'">

                            <div class="search-type-icon-badge">
                                <i data-lucide="${escapeHtml(lucideName)}" style="width:12px; height:12px;"></i>
                            </div>
                        </div>

                        <div class="search-item-body">
                            <div class="search-item-header">
                                <div>
                                    <div class="search-item-title">
                                        ${escapeHtml(title)}
                                        ${item.verified_label ? `<i data-lucide="check-circle" class="icon-sm text-brand-green"></i>` : ''}
                                    </div>

                                    <div class="search-item-type-text">
                                        ${escapeHtml(item.type || 'Eintrag')}
                                    </div>
                                </div>
                            </div>

                            <div class="search-item-info-row">
                                ${item.email ? `<span><i data-lucide="mail" class="icon-sm"></i> ${escapeHtml(item.email)}</span>` : ''}
                                ${item.phone ? `<span><i data-lucide="phone" class="icon-sm"></i> ${escapeHtml(item.phone)}</span>` : ''}
                                ${item.start_date_label ? `<span><i data-lucide="calendar" class="icon-sm"></i> ${escapeHtml(item.start_date_label)}</span>` : ''}
                            </div>

                            ${address ? `
                                <div class="search-item-info-row" style="margin-top:2px;">
                                    <span><i data-lucide="map-pin" class="icon-sm"></i> ${escapeHtml(address)}</span>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
                    }).join('');

                    content.querySelectorAll('.search-item').forEach(function (item) {
                        item.addEventListener('click', function () {
                            const url = this.dataset.url;

                            if (url && url !== '#') {
                                window.location.href = url;
                            }
                        });

                        item.addEventListener('keydown', function (event) {
                            if (event.key === 'Enter') {
                                this.click();
                            }
                        });
                    });

                    refreshIcons();
                }

                function updateSelectedResult() {
                    const items = Array.from(content.querySelectorAll('.search-item'));

                    items.forEach(function (item, index) {
                        item.classList.toggle('is-selected', index === selectedIndex);
                    });

                    if (selectedIndex >= 0 && items[selectedIndex]) {
                        items[selectedIndex].scrollIntoView({
                            block: 'nearest',
                            behavior: 'smooth',
                        });
                    }
                }

                input.addEventListener('input', function () {
                    clearTimeout(searchTimeout);

                    searchTimeout = setTimeout(function () {
                        performSearch(input.value);
                    }, 280);
                });

                input.addEventListener('focus', function () {
                    if (input.value.trim().length >= 2) {
                        dropdown.classList.add('is-active');
                    }
                });

                input.addEventListener('keydown', function (event) {
                    const items = Array.from(content.querySelectorAll('.search-item'));

                    if (event.key === 'ArrowDown') {
                        event.preventDefault();
                        if (!items.length) return;

                        selectedIndex = selectedIndex + 1 >= items.length ? 0 : selectedIndex + 1;
                        updateSelectedResult();
                    }

                    if (event.key === 'ArrowUp') {
                        event.preventDefault();
                        if (!items.length) return;

                        selectedIndex = selectedIndex - 1 < 0 ? items.length - 1 : selectedIndex - 1;
                        updateSelectedResult();
                    }

                    if (event.key === 'Enter') {
                        if (selectedIndex >= 0 && items[selectedIndex]) {
                            event.preventDefault();
                            items[selectedIndex].click();
                        }
                    }

                    if (event.key === 'Escape') {
                        dropdown.classList.remove('is-active');
                        document.body.classList.remove('mobile-search-open');
                        input.blur();
                    }
                });

                document.addEventListener('click', function (event) {
                    const clickedSearch = event.target.closest('.search-wrapper');
                    const clickedClose = event.target.closest('.mobile-search-close-btn');
                    const clickedSearchButton = event.target.closest('.mobile-nav-btn, .nav-link-search');

                    if (clickedSearch || clickedClose || clickedSearchButton) return;

                    dropdown.classList.remove('is-active');

                    if (window.innerWidth < 640) {
                        document.body.classList.remove('mobile-search-open');
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if ((event.metaKey || event.ctrlKey) && event.key.toLowerCase() === 'k') {
                        event.preventDefault();

                        if (window.innerWidth < 640) {
                            window.openMobileSearch(event);
                        } else {
                            input.focus();
                            input.select();
                        }
                    }
                });
            });
        })();
    </script>
    <script>


        (function () {
            'use strict';

            const config = {
                userId: {{ auth()->id() }},
                listUrl: "{{ route('get.notification.list') }}",
                markReadUrl: id => `{{ url('/notification/mark-as-read') }}/${id}`,
                markAllReadUrl: "{{ url('/notification/mark-all-read') }}",
                csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',
            };

            const state = {
                notifications: [],
                filter: 'all',
                search: '',
                isLoading: false,
            };

            function $(selector) {
                return document.querySelector(selector);
            }

            function byId(id) {
                return document.getElementById(id);
            }

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function refreshIcons() {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }

                if (window.feather && typeof window.feather.replace === 'function') {
                    window.feather.replace();
                }
            }

            function safeText(value, fallback = '-') {
                const text = String(value ?? '').trim();
                return text || fallback;
            }

            function notificationType(notification) {
                return String(notification.type || notification.data?.type || '').toLowerCase();
            }

            function getIcon(type) {
                const key = String(type || '').toLowerCase();

                const map = {
                    lead: 'user-plus',
                    customer: 'user',
                    inquiry: 'help-circle',
                    appointment: 'calendar',
                    ticket: 'alert-triangle',
                    problem: 'alert-triangle',
                    task: 'check-square',
                    offer: 'file-text',
                    invoice: 'credit-card',
                    chat: 'message-square',
                    system: 'bell',
                };

                for (const name in map) {
                    if (key.includes(name)) return map[name];
                }

                return 'bell';
            }

            function getTypeLabel(type) {
                const key = String(type || '').toLowerCase();

                if (key.includes('lead')) return 'Lead';
                if (key.includes('customer')) return 'Kunde';
                if (key.includes('inquiry')) return 'Anfrage';
                if (key.includes('appointment')) return 'Termin';
                if (key.includes('ticket') || key.includes('problem')) return 'Ticket';
                if (key.includes('task')) return 'Aufgabe';
                if (key.includes('offer')) return 'Angebot';
                if (key.includes('invoice')) return 'Rechnung';

                return key ? key : 'Info';
            }

            function flattenGroups(groups) {
                const items = [];

                Object.keys(groups || {}).forEach(type => {
                    const list = Array.isArray(groups[type]) ? groups[type] : [];

                    list.forEach(item => {
                        items.push({
                            ...item,
                            type: item.type || type,
                        });
                    });
                });

                return items.sort((a, b) => {
                    const dateA = new Date(a.performed_at || a.created_at || 0);
                    const dateB = new Date(b.performed_at || b.created_at || 0);

                    return dateB - dateA;
                });
            }

            function getNotificationUrl(notification) {
                return notification.data?.url ||
                    notification.url ||
                    notification.link ||
                    '#';
            }

            function isUnread(notification) {
                return !notification.read_at;
            }

            function getSearchHaystack(notification) {
                return [
                    notification.title,
                    notification.message,
                    notification.type,
                    notification.data?.title,
                    notification.data?.message,
                ].map(value => String(value ?? '').toLowerCase()).join(' ');
            }

            function filteredNotifications() {
                const search = state.search.toLowerCase().trim();

                return state.notifications.filter(notification => {
                    const type = notificationType(notification);

                    const matchesFilter =
                        state.filter === 'all' ||
                        (state.filter === 'unread' && isUnread(notification)) ||
                        type.includes(state.filter);

                    const matchesSearch =
                        !search ||
                        getSearchHaystack(notification).includes(search);

                    return matchesFilter && matchesSearch;
                });
            }

            function updateBadges() {
                const unread = state.notifications.filter(isUnread).length;
                const label = unread > 99 ? '99+' : String(unread);

                const navBadge = byId('navbarNotificationBadge');
                if (navBadge) {
                    navBadge.textContent = label;
                    navBadge.style.display = unread > 0 ? 'flex' : 'none';
                }

                const dropdownCount = byId('dropNotifCount');
                if (dropdownCount) {
                    dropdownCount.textContent = `${label} Neu`;
                    dropdownCount.style.display = unread > 0 ? 'inline-flex' : 'none';
                }

                const sideBadge = byId('notifUnreadBadge');
                if (sideBadge) {
                    sideBadge.textContent = label;
                    sideBadge.style.display = unread > 0 ? 'inline-flex' : 'none';
                }
            }

            function renderState(target, icon, message) {
                target.innerHTML = `
            <div class="sa-notif-state">
                <div class="sa-notif-state-icon">
                    <i data-lucide="${escapeHtml(icon)}" class="icon-lg"></i>
                </div>
                ${escapeHtml(message)}
            </div>
        `;

                refreshIcons();
            }

            function renderQuickList() {
                const list = byId('quickNotifList');
                if (!list) return;

                const latest = state.notifications.slice(0, 6);

                if (!latest.length) {
                    list.innerHTML = `
                <div style="padding:18px; text-align:center; color:var(--text-muted); font-size:12px; font-weight:700;">
                    Keine Benachrichtigungen
                </div>
            `;
                    return;
                }

                list.innerHTML = latest.map(notification => {
                    const type = notificationType(notification);
                    const url = getNotificationUrl(notification);

                    return `
                <button type="button"
                        class="sa-notif-quick-item ${isUnread(notification) ? 'unread' : ''}"
                        onclick="window.NotificationCenter.openNotification('${escapeHtml(notification.id)}', '${escapeHtml(url)}')">

                    <div class="sa-notif-quick-icon">
                        <i data-lucide="${getIcon(type)}" class="icon-sm"></i>
                    </div>

                    <div style="flex:1; min-width:0;">
                        <p class="sa-notif-quick-title">
                            ${escapeHtml(safeText(notification.title, 'Benachrichtigung'))}
                        </p>

                        <p class="sa-notif-quick-text">
                            ${escapeHtml(safeText(notification.message, 'Neue Nachricht'))}
                        </p>
                    </div>

                    ${isUnread(notification) ? '<span class="sa-notif-unread-dot" style="right:10px;top:10px;"></span>' : ''}
                </button>
            `;
                }).join('');

                refreshIcons();
            }

            function renderSidebarList() {
                const list = byId('notificationSidebarList');
                if (!list) return;

                if (state.isLoading) {
                    renderState(list, 'loader-circle', 'Benachrichtigungen werden geladen...');
                    return;
                }

                const filtered = filteredNotifications();

                if (!filtered.length) {
                    renderState(list, 'inbox', 'Keine passenden Benachrichtigungen gefunden.');
                    return;
                }

                list.innerHTML = filtered.map(notification => {
                    const type = notificationType(notification);
                    const unread = isUnread(notification);
                    const url = getNotificationUrl(notification);

                    return `
                <article class="sa-notif-card ${unread ? 'unread' : ''}">
                    ${unread ? '<span class="sa-notif-unread-dot"></span>' : ''}

                    <div class="sa-notif-icon">
                        <i data-lucide="${getIcon(type)}" class="icon-lg"></i>
                    </div>

                    <div class="sa-notif-content">
                        <div class="sa-notif-card-top">
                            <span class="sa-notif-type">
                                ${escapeHtml(getTypeLabel(type))}
                            </span>

                            <span class="sa-notif-time">
                                ${escapeHtml(safeText(notification.created_human || notification.time || notification.performed_human, ''))}
                            </span>
                        </div>

                        <h6 class="sa-notif-card-title">
                            ${escapeHtml(safeText(notification.title, 'Benachrichtigung'))}
                        </h6>

                        <p class="sa-notif-card-text">
                            ${escapeHtml(safeText(notification.message, 'Neue Nachricht'))}
                        </p>

                        <div class="sa-notif-actions">
                            <button type="button"
                                    class="sa-notif-action-btn primary"
                                    onclick="window.NotificationCenter.openNotification('${escapeHtml(notification.id)}', '${escapeHtml(url)}')">
                                <i data-lucide="external-link" class="icon-sm"></i>
                                Öffnen
                            </button>

                            ${unread ? `
                                <button type="button"
                                        class="sa-notif-action-btn"
                                        onclick="markAsRead('${escapeHtml(notification.id)}')">
                                    <i data-lucide="check" class="icon-sm"></i>
                                    Gelesen
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </article>
            `;
                }).join('');

                refreshIcons();
            }

            async function fetchNotifications() {
                state.isLoading = true;
                renderSidebarList();

                try {
                    const response = await fetch(config.listUrl, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const groups = await response.json();
                    state.notifications = Array.isArray(groups) ? groups : flattenGroups(groups);

                    updateBadges();
                    renderQuickList();
                    renderSidebarList();
                } catch (error) {
                    console.error('Notification fetch error:', error);

                    state.notifications = [];

                    const list = byId('notificationSidebarList');
                    if (list) {
                        renderState(list, 'alert-triangle', 'Benachrichtigungen konnten nicht geladen werden.');
                    }

                    updateBadges();
                    renderQuickList();
                } finally {
                    state.isLoading = false;
                }
            }

            async function markRead(id) {
                if (!id) return;

                try {
                    await fetch(config.markReadUrl(id), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': config.csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });
                } catch (error) {
                    console.error('Mark read error:', error);
                }

                await fetchNotifications();
            }

            async function markAllRead() {
                try {
                    await fetch(config.markAllReadUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': config.csrf,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });
                } catch (error) {
                    console.error('Mark all read error:', error);
                }

                await fetchNotifications();
            }

            window.NotificationCenter = {
                fetch: fetchNotifications,

                openNotification(id, url) {
                    const targetUrl = url && url !== '#' ? url : null;

                    if (id) {
                        markRead(id).finally(() => {
                            if (targetUrl) {
                                window.location.href = targetUrl;
                            }
                        });

                        return;
                    }

                    if (targetUrl) {
                        window.location.href = targetUrl;
                    }
                },
            };

            window.toggleNotificationSidebar = function () {
                const sider = byId('notificationSidebar');
                const overlay = byId('notificationSidebarOverlay');

                if (!sider) return;

                const willOpen = !sider.classList.contains('is-open');

                sider.classList.toggle('is-open', willOpen);
                overlay?.classList.toggle('is-active', willOpen);
                sider.setAttribute('aria-hidden', willOpen ? 'false' : 'true');

                byId('notifDropdown')?.classList.remove('show');

                if (willOpen) {
                    renderSidebarList();
                }
            };

            window.openNotificationSlider = function () {
                window.toggleNotificationSidebar();
            };

            window.closeNotification = function () {
                const sider = byId('notificationSidebar');
                const overlay = byId('notificationSidebarOverlay');

                sider?.classList.remove('is-open');
                overlay?.classList.remove('is-active');
                sider?.setAttribute('aria-hidden', 'true');
            };

            window.markAsRead = markRead;
            window.markAllNotificationsRead = markAllRead;

            document.addEventListener('DOMContentLoaded', function () {
                byId('notifSidebarSearch')?.addEventListener('input', function () {
                    state.search = this.value || '';
                    renderSidebarList();
                });

                byId('notifFilters')?.addEventListener('click', function (event) {
                    const chip = event.target.closest('.sa-notif-chip');
                    if (!chip) return;

                    this.querySelectorAll('.sa-notif-chip').forEach(item => {
                        item.classList.remove('active');
                    });

                    chip.classList.add('active');

                    state.filter = chip.dataset.filter || 'all';

                    renderSidebarList();
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        window.closeNotification();
                    }
                });

                fetchNotifications();
                setInterval(fetchNotifications, 60000);
            });

            if (window.Echo) {
                window.Echo.private(`App.Models.User.${config.userId}`)
                    .notification(function () {
                        const sound = byId('notificationSound');

                        if (sound) {
                            try {
                                sound.currentTime = 0;
                                sound.play().catch(() => { });
                            } catch (e) { }
                        }

                        fetchNotifications();
                    });
            }
        })();
    </script>

    <!-- Activity Script -->
    <script>
        (function () {
            'use strict';

            /*
            |--------------------------------------------------------------------------
            | Activity Routes
            |--------------------------------------------------------------------------
            */
            const activityRoutes = {
                recent: @json(url('/api/live-activities/recent')),
                saveFilters: @json(url('/api/live-activities/save-filters')),
                readBase: @json(url('/api/live-activities')),
            };

            const endpoint = activityRoutes.recent;

            /*
            |--------------------------------------------------------------------------
            | Default Filter State
            |--------------------------------------------------------------------------
            */
            window.userActivityFilters = window.userActivityFilters || {
                customers: [],
                employees: [],
                departments: [],
                products: [],
                types: [],
                is_muted: false
            };

            const state = {
                activities: [],
                filter: 'all',
                search: '',
                unread: 0,
                isLoading: false,
            };

            const iconMap = {
                customer: 'user',
                lead: 'user-plus',
                ticket: 'alert-triangle',
                problem: 'alert-triangle',
                appointment: 'calendar',
                task: 'check-square',
                offer: 'file-text',
                deal: 'briefcase',
                invoice: 'credit-card',
                product: 'box',
                employee: 'users',
                user: 'user',
                system: 'activity',
                notes: 'sticky-note',
                process: 'git-branch',
                address: 'map-pin',
                general: 'activity',
            };

            /*
            |--------------------------------------------------------------------------
            | Helpers
            |--------------------------------------------------------------------------
            */
            function dom(id) {
                return document.getElementById(id);
            }

            function csrfToken() {
                return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            }

            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>'"]/g, function (tag) {
                    return {
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        "'": '&#039;',
                        '"': '&quot;'
                    }[tag] || tag;
                });
            }

            function refreshIcons() {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }

                if (window.feather && typeof window.feather.replace === 'function') {
                    window.feather.replace();
                }
            }

            function normalizeType(item) {
                return String(item.type || item.model || item.model_key || item.model_de || '').toLowerCase();
            }

            function getIcon(item) {
                const type = normalizeType(item);

                for (const key in iconMap) {
                    if (type.includes(key)) {
                        return iconMap[key];
                    }
                }

                return 'activity';
            }

            function getAvatar(item) {
                if (item.creator_image) {
                    const img = String(item.creator_image);

                    if (img.startsWith('http') || img.startsWith('/')) {
                        return img;
                    }

                    return `/images/employee/${img}`;
                }

                return '/images/gender/male.png';
            }

            function safeText(value, fallback = '-') {
                return String(value ?? '').trim() || fallback;
            }

            function getSearchHaystack(item) {
                return [
                    item.model_de,
                    item.customer_name,
                    item.detail_text,
                    item.creator_name,
                    item.time,
                    item.type,
                    item.model,
                ].map(function (val) {
                    return String(val ?? '').toLowerCase();
                }).join(' ');
            }

            function getIdArray(values) {
                return (values || []).map(function (item) {
                    return typeof item === 'object' ? String(item.id) : String(item);
                }).filter(Boolean);
            }

            function getSelect2Ids(selector) {
                if (typeof jQuery === 'undefined') return [];

                const $select = jQuery(selector);

                if (!$select.length) return [];

                const value = $select.val();

                if (Array.isArray(value)) {
                    return value.map(String).filter(Boolean);
                }

                return value ? [String(value)] : [];
            }

            function getSelect2Data(selector) {
                if (typeof jQuery === 'undefined') return [];

                const $select = jQuery(selector);

                if (!$select.length) return [];

                if ($select.hasClass('select2-hidden-accessible')) {
                    return ($select.select2('data') || []).map(function (item) {
                        return {
                            id: String(item.id),
                            text: item.text
                        };
                    });
                }

                return Array.from($select[0].selectedOptions || []).map(function (option) {
                    return {
                        id: String(option.value),
                        text: option.textContent.trim()
                    };
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Select2 Settings
            |--------------------------------------------------------------------------
            */
            function initActivitySettingSelect2() {
                if (typeof jQuery === 'undefined' || !jQuery.fn.select2) {
                    return;
                }

                jQuery('.select2-activity-static').each(function () {
                    const $select = jQuery(this);

                    if ($select.hasClass('select2-hidden-accessible')) {
                        return;
                    }

                    $select.select2({
                        width: '100%',
                        dropdownAutoWidth: true,
                        placeholder: $select.data('placeholder') || 'Auswählen...',
                        closeOnSelect: false,
                        allowClear: true,
                        language: {
                            noResults: function () {
                                return 'Keine passenden Optionen gefunden';
                            },
                            searching: function () {
                                return 'Sucht...';
                            }
                        }
                    });

                    $select.on('change', function () {
                        renderSidebar();
                    });
                });
            }

            function setSelect2Values(selector, values) {
                if (typeof jQuery === 'undefined') return;

                const $select = jQuery(selector);

                if (!$select.length) return;

                const ids = getIdArray(values);

                $select.val(ids).trigger('change');
            }

            function applySavedFiltersToSelect2() {
                const prefs = window.userActivityFilters || {};

                initActivitySettingSelect2();

                setSelect2Values('#filter-customers', prefs.customers);
                setSelect2Values('#filter-employees', prefs.employees);
                setSelect2Values('#filter-departments', prefs.departments);
                setSelect2Values('#filter-products', prefs.products);
                setSelect2Values('#filter-types', prefs.types);

                if (dom('activityMuteToggle')) {
                    dom('activityMuteToggle').checked = !!prefs.is_muted;
                }
            }

            function pullSelect2FilterData() {
                return {
                    customers: getSelect2Data('#filter-customers'),
                    employees: getSelect2Data('#filter-employees'),
                    departments: getSelect2Data('#filter-departments'),
                    products: getSelect2Data('#filter-products'),
                    types: getSelect2Data('#filter-types'),
                };
            }

            function clearSelect2Filters() {
                if (typeof jQuery !== 'undefined') {
                    jQuery('#filter-customers').val(null).trigger('change');
                    jQuery('#filter-employees').val(null).trigger('change');
                    jQuery('#filter-departments').val(null).trigger('change');
                    jQuery('#filter-products').val(null).trigger('change');
                    jQuery('#filter-types').val(null).trigger('change');
                }

                if (dom('activityMuteToggle')) {
                    dom('activityMuteToggle').checked = false;
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Filter Panel
            |--------------------------------------------------------------------------
            */
            window.toggleActivitySettingsPanel = function () {
                const panel = dom('activityFilterSection');
                const triggerBtn = dom('activityFilterTriggerBtn');

                if (!panel) return;

                const isHidden = window.getComputedStyle(panel).display === 'none';

                panel.style.display = isHidden ? 'block' : 'none';
                triggerBtn?.classList.toggle('active', isHidden);

                if (isHidden) {
                    setTimeout(function () {
                        initActivitySettingSelect2();
                        applySavedFiltersToSelect2();
                    }, 50);
                }
            };

            window.saveActivityFilters = function (event) {
                const btn = event?.currentTarget;
                const originalText = btn ? btn.innerText : '';

                if (btn) {
                    btn.innerText = 'Speichert...';
                    btn.disabled = true;
                }

                const filters = pullSelect2FilterData();

                const payload = {
                    customers: filters.customers,
                    employees: filters.employees,
                    departments: filters.departments,
                    products: filters.products,
                    types: filters.types,
                    is_muted: dom('activityMuteToggle')?.checked ? 1 : 0,
                    _token: csrfToken()
                };

                if (typeof jQuery !== 'undefined') {
                    jQuery.post(activityRoutes.saveFilters, payload, function () {
                        window.userActivityFilters = {
                            customers: payload.customers,
                            employees: payload.employees,
                            departments: payload.departments,
                            products: payload.products,
                            types: payload.types,
                            is_muted: !!payload.is_muted
                        };

                        fetchActivities();

                        if (btn) {
                            btn.innerText = 'Angewendet!';
                            setTimeout(function () {
                                btn.innerText = originalText;
                                btn.disabled = false;
                            }, 1200);
                        }
                    }).fail(function (xhr) {
                        console.error(xhr.responseText || xhr);

                        if (btn) {
                            btn.innerText = 'Fehler!';
                            setTimeout(function () {
                                btn.innerText = originalText;
                                btn.disabled = false;
                            }, 1200);
                        }
                    });

                    return;
                }

                fetch(activityRoutes.saveFilters, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload)
                }).then(function (response) {
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    window.userActivityFilters = {
                        customers: payload.customers,
                        employees: payload.employees,
                        departments: payload.departments,
                        products: payload.products,
                        types: payload.types,
                        is_muted: !!payload.is_muted
                    };

                    fetchActivities();

                    if (btn) {
                        btn.innerText = 'Angewendet!';
                        setTimeout(function () {
                            btn.innerText = originalText;
                            btn.disabled = false;
                        }, 1200);
                    }
                }).catch(function (error) {
                    console.error(error);

                    if (btn) {
                        btn.innerText = 'Fehler!';
                        setTimeout(function () {
                            btn.innerText = originalText;
                            btn.disabled = false;
                        }, 1200);
                    }
                });
            };

            window.clearActivityFilters = function () {
                clearSelect2Filters();

                window.userActivityFilters = {
                    customers: [],
                    employees: [],
                    departments: [],
                    products: [],
                    types: [],
                    is_muted: false
                };

                fetchActivities();
            };

            /*
            |--------------------------------------------------------------------------
            | Filter Logic
            |--------------------------------------------------------------------------
            */
            function getActiveRuntimeFilters() {
                const live = {
                    customers: getSelect2Ids('#filter-customers'),
                    employees: getSelect2Ids('#filter-employees'),
                    departments: getSelect2Ids('#filter-departments'),
                    products: getSelect2Ids('#filter-products'),
                    types: getSelect2Ids('#filter-types'),
                };

                return {
                    customers: live.customers.length ? live.customers : getIdArray(window.userActivityFilters.customers || []),
                    employees: live.employees.length ? live.employees : getIdArray(window.userActivityFilters.employees || []),
                    departments: live.departments.length ? live.departments : getIdArray(window.userActivityFilters.departments || []),
                    products: live.products.length ? live.products : getIdArray(window.userActivityFilters.products || []),
                    types: live.types.length ? live.types : getIdArray(window.userActivityFilters.types || []),
                };
            }

            function passesActivitySettings(item) {
                const prefs = getActiveRuntimeFilters();

                const itemType = normalizeType(item);

                const mCust =
                    !prefs.customers.length ||
                    prefs.customers.includes(String(item.customer_id));

                const mEmp =
                    !prefs.employees.length ||
                    prefs.employees.includes(String(item.employee_id)) ||
                    prefs.employees.includes(String(item.creator_id)) ||
                    prefs.employees.includes(String(item.created_by));

                const mDept =
                    !prefs.departments.length ||
                    prefs.departments.includes(String(item.department_key)) ||
                    prefs.departments.includes(String(item.department));

                const mProd =
                    !prefs.products.length ||
                    prefs.products.includes(String(item.product_id)) ||
                    prefs.products.includes(String(item.article_group_id));

                const mType =
                    !prefs.types.length ||
                    prefs.types.some(function (type) {
                        return itemType.includes(String(type).toLowerCase());
                    });

                return mCust && mEmp && mDept && mProd && mType;
            }

            function filteredActivities() {
                const search = state.search.toLowerCase().trim();

                return state.activities.filter(function (item) {
                    const type = normalizeType(item);

                    const matchesChip =
                        state.filter === 'all' ||
                        type.includes(state.filter) ||
                        String(item.model_de ?? '').toLowerCase().includes(state.filter);

                    const matchesSearch =
                        !search ||
                        getSearchHaystack(item).includes(search);

                    return matchesChip && matchesSearch && passesActivitySettings(item);
                });
            }

            /*
            |--------------------------------------------------------------------------
            | Badge / Empty / Loading
            |--------------------------------------------------------------------------
            */
            function setActivityBadge(count) {
                const badge = dom('activityBadge');

                if (!badge) return;

                const value = Number(count || 0);

                if (value > 0) {
                    badge.textContent = value > 99 ? '99+' : String(value);
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            }

            function renderEmpty(target, message = 'Keine Aktivitäten gefunden.') {
                target.innerHTML = `
            <div class="sa-activity-state">
                <div class="sa-activity-state-icon">
                    <i data-lucide="inbox" class="icon-lg"></i>
                </div>
                ${escapeHtml(message)}
            </div>
        `;

                refreshIcons();
            }

            function renderLoading(target) {
                target.innerHTML = `
            <div class="sa-activity-state">
                <div class="sa-activity-state-icon">
                    <i data-lucide="loader-circle" class="icon-lg"></i>
                </div>
                Aktivitäten werden geladen...
            </div>
        `;

                refreshIcons();
            }

            function renderQuickLoading() {
                const quickList = dom('quickActivityList');

                if (!quickList) return;

                quickList.innerHTML = `
            <div class="text-center p-3 text-muted">
                <small>Lade Aktivitäten...</small>
            </div>
        `;
            }

            function renderQuickEmpty(message = 'Keine Aktivitäten gefunden.') {
                const quickList = dom('quickActivityList');

                if (!quickList) return;

                quickList.innerHTML = `
            <div class="text-center p-3 text-muted">
                <div style="width:42px;height:42px;border-radius:14px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;color:var(--brand-blue);">
                    <i data-lucide="inbox" style="width:18px;height:18px;"></i>
                </div>
                <small>${escapeHtml(message)}</small>
            </div>
        `;

                refreshIcons();
            }

            /*
            |--------------------------------------------------------------------------
            | Render Sidebar List
            |--------------------------------------------------------------------------
            */
            function renderSidebar() {
                const list = dom('activityList');

                if (!list) return;

                if (state.isLoading) {
                    return renderLoading(list);
                }

                const items = filteredActivities();

                if (!items.length) {
                    return renderEmpty(list, 'Keine passenden Aktivitäten gefunden.');
                }

                list.innerHTML = items.map(function (item) {
                    const originalIndex = state.activities.indexOf(item);
                    const avatar = getAvatar(item);

                    const changeCount = item.changes && typeof item.changes === 'object'
                        ? Object.keys(item.changes).length
                        : 0;

                    return `
                <button type="button"
                        class="sa-activity-card"
                        onclick="window.ActivitySidebar.showDetails(${originalIndex})">

                    <div class="sa-activity-avatar-wrap">
                        <img src="${escapeHtml(avatar)}"
                             class="sa-activity-avatar"
                             alt="${escapeHtml(safeText(item.creator_name, 'User'))}"
                             onerror="this.src='/images/gender/male.png'">

                        <div class="sa-activity-type-dot">
                            <i data-lucide="${escapeHtml(getIcon(item))}" style="width:12px;height:12px;"></i>
                        </div>
                    </div>

                    <div class="sa-activity-card-main">
                        <div class="sa-activity-card-top">
                            <span class="sa-activity-model">
                                ${escapeHtml(safeText(item.model_de || item.type, 'Aktivität'))}
                            </span>

                            <span class="sa-activity-time">
                                ${escapeHtml(safeText(item.time, ''))}
                            </span>
                        </div>

                        <p class="sa-activity-customer">
                            ${escapeHtml(safeText(item.customer_name, 'Allgemein'))}
                        </p>

                        <p class="sa-activity-text">
                            ${escapeHtml(safeText(item.detail_text, 'Keine Beschreibung'))}
                        </p>

                        <div class="sa-activity-meta">
                            <span>
                                <i data-lucide="user" class="icon-sm"></i>
                                <span class="truncate">
                                    ${escapeHtml(safeText(item.creator_name, 'System'))}
                                </span>
                            </span>

                            ${changeCount > 0 ? `
                                <span>
                                    <i data-lucide="repeat-2" class="icon-sm"></i>
                                    ${changeCount} Änderung(en)
                                </span>
                            ` : ''}
                        </div>
                    </div>
                </button>
            `;
                }).join('');

                refreshIcons();
            }

            /*
            |--------------------------------------------------------------------------
            | Render Quick Dropdown Recent List
            |--------------------------------------------------------------------------
            */
            function renderQuickActivities() {
                const quickList = dom('quickActivityList');

                if (!quickList) return;

                if (state.isLoading) {
                    return renderQuickLoading();
                }

                const items = state.activities.slice(0, 6);

                if (!items.length) {
                    return renderQuickEmpty('Keine neuen Aktivitäten.');
                }

                quickList.innerHTML = items.map(function (item) {
                    const originalIndex = state.activities.indexOf(item);
                    const icon = getIcon(item);

                    return `
                <button type="button"
                        class="sa-activity-quick-item"
                        onclick="window.ActivitySidebar.showDetails(${originalIndex})">

                    <span class="sa-activity-quick-icon">
                        <i data-lucide="${escapeHtml(icon)}" style="width:16px;height:16px;"></i>
                    </span>

                    <span style="flex:1; min-width:0;">
                        <p class="sa-activity-quick-title">
                            ${escapeHtml(safeText(item.model_de || item.type, 'Aktivität'))}
                            ${item.time ? `<span style="float:right; color:var(--text-muted); font-size:10px; font-weight:800;">${escapeHtml(item.time)}</span>` : ''}
                        </p>

                        <p class="sa-activity-quick-text">
                            ${escapeHtml(safeText(item.customer_name, 'Allgemein'))}
                        </p>

                        <p class="sa-activity-quick-text">
                            ${escapeHtml(safeText(item.detail_text, 'Keine Beschreibung'))}
                        </p>
                    </span>
                </button>
            `;
                }).join('');

                refreshIcons();
            }

            /*
            |--------------------------------------------------------------------------
            | Render Details Modal
            |--------------------------------------------------------------------------
            */
            function renderDetails(item) {
                const body = dom('activityDetailModalBody');
                const title = dom('activityDetailModalTitle');
                const subtitle = dom('activityDetailModalSubtitle');

                if (!body || !title) return;

                title.textContent = `${safeText(item.model_de || item.type, 'Aktivität')} - Details`;

                if (subtitle) {
                    subtitle.textContent = safeText(item.time, 'Aktivitätsdetails');
                }

                const detailBoxes = [
                    ['Bereich', item.model_de || item.type || 'Aktivität'],
                    ['Kunde / Objekt', item.customer_name || 'Allgemein'],
                    ['Aktion von', item.creator_name || 'System'],
                    ['Zeitpunkt', item.time || item.created_at || '-'],
                ];

                let changesHtml = '';

                if (item.changes && typeof item.changes === 'object' && Object.keys(item.changes).length) {
                    changesHtml = `
                <h6 class="sa-activity-section-title">Änderungen</h6>

                <div class="sa-activity-change-list">
                    ${Object.entries(item.changes).map(function ([field, val]) {
                        const oldValue = val && typeof val === 'object' ? val.from : '';
                        const newValue = val && typeof val === 'object' ? val.to : val;

                        return `
                            <div class="sa-activity-change-row">
                                <p class="sa-activity-change-field">
                                    ${escapeHtml(field)}
                                </p>

                                <div class="sa-activity-change-values">
                                    <div class="sa-activity-change-pill old">
                                        ${escapeHtml(safeText(oldValue, 'Kein Wert'))}
                                    </div>

                                    <div class="sa-activity-arrow">
                                        <i data-lucide="arrow-right" class="icon-md"></i>
                                    </div>

                                    <div class="sa-activity-change-pill new">
                                        ${escapeHtml(safeText(newValue, 'Kein Wert'))}
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('')}
                </div>
            `;
                }

                body.innerHTML = `
            <div class="sa-activity-detail-grid">
                ${detailBoxes.map(function ([label, value]) {
                    return `
                        <div class="sa-activity-detail-box">
                            <p class="sa-activity-detail-label">
                                ${escapeHtml(label)}
                            </p>

                            <p class="sa-activity-detail-value">
                                ${escapeHtml(safeText(value))}
                            </p>
                        </div>
                    `;
                }).join('')}
            </div>

            <div class="sa-activity-message-box">
                ${escapeHtml(safeText(item.detail_text, 'Keine Beschreibung vorhanden.'))}
            </div>

            ${changesHtml}
        `;

                dom('activityDetailModalBackdrop')?.classList.add('is-active');
                refreshIcons();
            }

            /*
            |--------------------------------------------------------------------------
            | Fetch Activities
            |--------------------------------------------------------------------------
            */
            async function fetchActivities() {
                state.isLoading = true;

                renderSidebar();
                renderQuickActivities();

                try {
                    const response = await fetch(endpoint, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }

                    const json = await response.json();

                    state.activities = Array.isArray(json)
                        ? json
                        : (json.data || []);

                } catch (error) {
                    console.error(error);

                    state.activities = [];

                    const list = dom('activityList');

                    if (list) {
                        renderEmpty(list, 'Aktivitäten konnten nicht geladen werden.');
                    }

                    renderQuickEmpty('Aktivitäten konnten nicht geladen werden.');
                } finally {
                    state.isLoading = false;

                    renderSidebar();
                    renderQuickActivities();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Public API
            |--------------------------------------------------------------------------
            */
            window.ActivitySidebar = {
                fetch: fetchActivities,

                showDetails(index) {
                    const item = state.activities[index];

                    if (!item) return;

                    renderDetails(item);
                },

                resetUnread() {
                    state.unread = 0;
                    setActivityBadge(0);
                },

                renderQuick: renderQuickActivities,
            };

            window.toggleActivitySidebar = function () {
                const sider = dom('activitySidebar');
                const backdrop = dom('activityBackdrop');

                if (!sider) return;

                const willOpen = !sider.classList.contains('is-open');

                sider.classList.toggle('is-open', willOpen);
                backdrop?.classList.toggle('is-active', willOpen);
                sider.setAttribute('aria-hidden', willOpen ? 'false' : 'true');

                if (willOpen) {
                    window.ActivitySidebar.resetUnread();
                    fetchActivities();
                }
            };

            window.closeActivityDetailModal = function (event) {
                if (event && event.target !== dom('activityDetailModalBackdrop')) {
                    return;
                }

                dom('activityDetailModalBackdrop')?.classList.remove('is-active');
            };

            /*
            |--------------------------------------------------------------------------
            | DOM Events
            |--------------------------------------------------------------------------
            */
            document.addEventListener('DOMContentLoaded', function () {
                initActivitySettingSelect2();
                applySavedFiltersToSelect2();

                const searchInput = dom('activitySearchInput');

                searchInput?.addEventListener('input', function () {
                    state.search = this.value || '';
                    renderSidebar();
                });

                dom('activityFilters')?.addEventListener('click', function (event) {
                    const chip = event.target.closest('.sa-activity-chip');

                    if (!chip) return;

                    this.querySelectorAll('.sa-activity-chip').forEach(function (item) {
                        item.classList.remove('active');
                    });

                    chip.classList.add('active');

                    state.filter = chip.dataset.type || 'all';

                    renderSidebar();
                });

                dom('clearActivityFiltersBtn')?.addEventListener('click', function () {
                    window.clearActivityFilters();
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key !== 'Escape') return;

                    dom('activityDetailModalBackdrop')?.classList.remove('is-active');

                    if (dom('activitySidebar')?.classList.contains('is-open')) {
                        window.toggleActivitySidebar();
                    }
                });

                fetchActivities();

                setInterval(fetchActivities, 60000);
            });

            /*
            |--------------------------------------------------------------------------
            | Realtime Echo Listener
            |--------------------------------------------------------------------------
            */
            if (window.Echo) {
                window.Echo.private('company-activities')
                    .listen('.activity.created', function () {
                        const sound = dom('activityNotificationSound');

                        if (sound && !window.userActivityFilters?.is_muted) {
                            try {
                                sound.currentTime = 0;
                                sound.play().catch(function () { });
                            } catch (e) { }
                        }

                        if (!dom('activitySidebar')?.classList.contains('is-open')) {
                            state.unread += 1;
                            setActivityBadge(state.unread);
                        }

                        fetchActivities();
                    });
            }

        })();
    </script>
    <!-- quickmenu -->
    <script>
        (function () {
            'use strict';

            function refreshIcons() {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }
            }

            function closeOtherQuickSubs(exceptPanel) {
                document.querySelectorAll('#quickSider .qs-sub').forEach(function (panel) {
                    if (panel !== exceptPanel) {
                        panel.hidden = true;
                    }
                });

                document.querySelectorAll('#quickSider .qs-toggle').forEach(function (button) {
                    const targetId = button.getAttribute('aria-controls');
                    const panel = targetId ? document.getElementById(targetId) : null;

                    if (panel !== exceptPanel) {
                        button.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            document.addEventListener('click', function (event) {
                const toggle = event.target.closest('#quickSider .qs-toggle');

                if (toggle) {
                    event.preventDefault();
                    event.stopPropagation();

                    const targetId = toggle.getAttribute('aria-controls');
                    const panel = targetId ? document.getElementById(targetId) : null;

                    if (!panel) return;

                    const willOpen = panel.hidden;

                    closeOtherQuickSubs(panel);

                    panel.hidden = !willOpen;
                    toggle.setAttribute('aria-expanded', String(willOpen));

                    if (willOpen && targetId === 'qs-sub-department') {
                        window.loadQuickDepartments?.();
                    }

                    refreshIcons();
                    return;
                }

                if (!event.target.closest('#quickSider .qs-has-sub')) {
                    closeOtherQuickSubs(null);
                }
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeOtherQuickSubs(null);
                }
            });

            refreshIcons();
        })();
    </script>

    <script>
        (function () {
            'use strict';

            const wrapper = document.getElementById('qs-department-wrapper');
            if (!wrapper) return;

            const panel = document.getElementById('qs-sub-department');
            if (!panel) return;

            const loadingEl = panel.querySelector('.js-dept-loading');
            const listEl = panel.querySelector('.js-dept-list');
            const errorEl = panel.querySelector('.js-dept-error');
            const url = wrapper.dataset.url;

            let isLoaded = false;

            function escapeHtml(str) {
                if (str === null || str === undefined) return '';

                return String(str)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            window.loadQuickDepartments = function () {
                if (isLoaded) return;
                if (!url) return;

                loadingEl.style.display = 'block';
                errorEl.style.display = 'none';
                listEl.innerHTML = '';

                fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                })
                    .then(function (res) {
                        if (!res.ok) {
                            throw new Error('HTTP ' + res.status);
                        }

                        return res.json();
                    })
                    .then(function (items) {
                        isLoaded = true;
                        loadingEl.style.display = 'none';
                        renderDepartments(items);
                    })
                    .catch(function (err) {
                        console.error('Department load failed:', err);

                        loadingEl.style.display = 'none';
                        errorEl.textContent = 'Abteilungen konnten nicht geladen werden.';
                        errorEl.style.display = 'block';
                    });
            };

            function renderDepartments(items) {
                listEl.innerHTML = '';

                if (!items || !items.length) {
                    listEl.innerHTML = `
                <div style="padding:14px; text-align:center; color:#9ca3af; font-size:12px; font-weight:700;">
                    Keine Abteilungen gefunden
                </div>
            `;
                    return;
                }

                items.forEach(function (dept) {
                    const a = document.createElement('a');

                    a.className = 'qs-sub-item dept-link';
                    a.href = dept.url || '#';

                    const staffHtml = (dept.staff || []).slice(0, 5).map(function (emp) {
                        return `
                    <img src="${escapeHtml(emp.avatar)}"
                         alt="${escapeHtml(emp.name)}"
                         onerror="this.src='https://ui-avatars.com/api/?name=${encodeURIComponent(emp.name || 'User')}&background=74b2d4&color=fff'">
                `;
                    }).join('');

                    const moreHtml = dept.more_count && Number(dept.more_count) > 0
                        ? `<span class="avatar-stack-more">+${escapeHtml(dept.more_count)}</span>`
                        : '';

                    a.innerHTML = `
                <span class="text-truncate"
                      style="max-width:130px; font-weight:800; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                    ${escapeHtml(dept.name)}
                </span>

                <div class="avatar-stack">
                    ${staffHtml}
                    ${moreHtml}
                </div>
            `;

                    listEl.appendChild(a);
                });
            }
        })();
    </script>
    <script>
        (function () {
            'use strict';

            window.setGlobalBreadcrumbs = function (items) {
                const breadcrumbBox = document.getElementById('globalBreadcrumbs');

                if (!breadcrumbBox) {
                    return;
                }

                const breadcrumbs = Array.isArray(items) && items.length
                    ? items
                    : [
                        {
                            label: 'Workspace',
                            url: "{{ url('/employee_dashboard') }}"
                        },
                        {
                            label: 'Dashboard',
                            url: null
                        }
                    ];

                breadcrumbBox.innerHTML = '';

                breadcrumbs.forEach(function (item, index) {
                    const isLast = index === breadcrumbs.length - 1;
                    const label = item.label || '';
                    const url = item.url || item.href || null;

                    let element;

                    if (url && !isLast) {
                        element = document.createElement('a');
                        element.href = url;
                        element.textContent = label;
                        element.className = 'text-muted';
                        element.style.transition = 'color 0.2s';
                        element.style.textDecoration = 'none';
                        element.style.cursor = 'pointer';

                        element.addEventListener('mouseenter', function () {
                            element.style.color = 'var(--text-main)';
                        });

                        element.addEventListener('mouseleave', function () {
                            element.style.color = '';
                        });
                    } else if (url && isLast && item.clickable === true) {
                        element = document.createElement('a');
                        element.href = url;
                        element.textContent = label;
                        element.style.fontWeight = '700';
                        element.style.color = 'var(--text-main)';
                        element.style.textDecoration = 'none';
                        element.style.cursor = 'pointer';
                    } else {
                        element = document.createElement('span');
                        element.textContent = label;
                        element.style.fontWeight = isLast ? '700' : '500';
                        element.style.color = isLast ? 'var(--text-main)' : 'var(--text-muted)';
                    }

                    breadcrumbBox.appendChild(element);

                    if (!isLast) {
                        const icon = document.createElement('i');
                        icon.setAttribute('data-lucide', 'chevron-right');
                        icon.className = 'icon-sm text-muted';
                        breadcrumbBox.appendChild(icon);
                    }
                });

                if (window.lucide && window.lucide.createIcons) {
                    window.lucide.createIcons();
                }

                if (window.feather && window.feather.replace) {
                    window.feather.replace();
                }
            };

            document.addEventListener('DOMContentLoaded', function () {
                if (window.GlobalBreadcrumbs && Array.isArray(window.GlobalBreadcrumbs)) {
                    window.setGlobalBreadcrumbs(window.GlobalBreadcrumbs);
                }
            });
        })();
    </script>

    <!-- User Report  -->
    <script type="module">
        (function () {
            'use strict';

            if (window.__SA_REPORT_NOTIFICATIONS_BOOTED__) return;
            window.__SA_REPORT_NOTIFICATIONS_BOOTED__ = true;

            const routes = {
                list: @json(route('admin.overdue.reports.notifications')),
                read: (id) => @json(url('/admin/overdue-report-notifications')) + '/' + encodeURIComponent(id) + '/read',
                readAll: @json(route('admin.overdue.reports.notifications.readAll')),
            };

            const fallbackAvatar = @json(asset('images/gender/male.png'));
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

            const state = {
                reports: [],
                filter: 'all',
                isLoading: false,
                lastUnreadCount: 0,
                refreshTimer: null,
            };

            const dom = {
                menu: document.getElementById('reportNotifyMenu'),
                trigger: document.getElementById('reportNotifyTrigger'),
                dropdown: document.getElementById('reportNotifyDropdown'),
                badge: document.getElementById('reportNotifyBadge'),
                list: document.getElementById('reportNotifyList'),
                markAll: document.getElementById('reportMarkAllReadBtn'),
                tabs: document.querySelectorAll('.sa-report-panel-tabs button'),
                sound: document.getElementById('notificationSound') || document.getElementById('activityNotificationSound'),

                modalBackdrop: document.getElementById('saReportModalBackdrop'),
                modalTitle: document.getElementById('saReportModalTitle'),
                modalSub: document.getElementById('saReportModalSub'),
                modalEmployee: document.getElementById('saReportModalEmployee'),
                modalType: document.getElementById('saReportModalType'),
                modalTarget: document.getElementById('saReportModalTarget'),
                modalTime: document.getElementById('saReportModalTime'),
                modalReport: document.getElementById('saReportModalReport'),
            };

            if (!dom.menu || !dom.list) return;

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function stripHtml(value) {
                const div = document.createElement('div');
                div.innerHTML = String(value ?? '');
                return div.textContent || div.innerText || '';
            }

            function refreshIcons() {
                if (window.lucide) {
                    window.lucide.createIcons();
                }

                if (window.feather) {
                    window.feather.replace();
                }
            }

            function typeLabel(type) {
                const map = {
                    inquiry: 'Anfrage',
                    task: 'Aufgabe',
                    appointment: 'Termin',
                    ticket: 'Ticket',
                    lead: 'Lead',
                    customer: 'Kunde',
                    offer: 'Angebot',
                    deal: 'Auftrag',
                    problem: 'Problem',
                    report: 'Bericht',
                };

                return map[type] || type || 'Bericht';
            }

            function typeIcon(type) {
                const map = {
                    inquiry: 'help-circle',
                    task: 'check-square',
                    appointment: 'calendar-clock',
                    ticket: 'ticket',
                    lead: 'user-round-plus',
                    customer: 'user-round',
                    offer: 'file-text',
                    deal: 'briefcase-business',
                    problem: 'alert-triangle',
                    report: 'clipboard-check',
                };

                return map[type] || 'clipboard-check';
            }

            function isImportant(item) {
                const type = String(item?.type || '').toLowerCase();
                const report = String(item?.report || '').toLowerCase();
                const title = String(item?.title || '').toLowerCase();

                return ['ticket', 'problem', 'appointment'].includes(type)
                    || report.includes('dringend')
                    || report.includes('wichtig')
                    || report.includes('notfall')
                    || title.includes('dringend')
                    || title.includes('wichtig')
                    || title.includes('notfall');
            }

            function updateBadge(count) {
                if (!dom.badge) return;

                count = Number(count || 0);

                if (count > 0) {
                    dom.badge.textContent = count > 99 ? '99+' : String(count);
                    dom.badge.style.display = 'flex';
                } else {
                    dom.badge.textContent = '0';
                    dom.badge.style.display = 'none';
                }
            }

            function setLoading(isLoading) {
                state.isLoading = isLoading;

                if (!dom.list) return;

                if (isLoading && !state.reports.length) {
                    dom.list.innerHTML = `
                <div class="sa-report-empty">
                    <div class="sa-report-empty-icon">
                        <i data-lucide="loader-circle"></i>
                    </div>
                    <strong>Reports werden geladen</strong>
                    <span>Aktuelle Berichte werden abgerufen...</span>
                </div>
            `;

                    refreshIcons();
                }
            }

            function filteredReports() {
                if (state.filter === 'unread') {
                    return state.reports.filter(item => item.is_unread);
                }

                if (state.filter === 'important') {
                    return state.reports.filter(item => isImportant(item));
                }

                return state.reports;
            }

            function renderEmpty() {
                let title = 'Keine neuen Berichte';
                let text = 'Alles ist aktuell gelesen.';

                if (state.filter === 'unread') {
                    title = 'Keine ungelesenen Berichte';
                    text = 'Es sind keine neuen Reports offen.';
                }

                if (state.filter === 'important') {
                    title = 'Keine wichtigen Berichte';
                    text = 'Für diesen Filter gibt es aktuell keine Einträge.';
                }

                dom.list.innerHTML = `
            <div class="sa-report-empty">
                <div class="sa-report-empty-icon">
                    <i data-lucide="clipboard-check"></i>
                </div>
                <strong>${escapeHtml(title)}</strong>
                <span>${escapeHtml(text)}</span>
            </div>
        `;

                refreshIcons();
            }

            function renderList() {
                if (!dom.list) return;

                const unreadCount = state.reports.filter(item => item.is_unread).length;
                updateBadge(unreadCount);

                const items = filteredReports();

                if (!items.length) {
                    renderEmpty();
                    return;
                }

                dom.list.innerHTML = items.map(item => {
                    const employee = item.employee || 'Unbekannt';
                    const title = item.title || item.target_label || ('#' + (item.target_id || item.id || ''));
                    const subtitle = item.subtitle || item.target_customer || '';
                    const reportText = stripHtml(item.report || '');
                    const important = isImportant(item);
                    const targetNo = item.target_no || ('#' + (item.target_id || ''));
                    const status = item.target_status || '';
                    const customer = item.target_customer || '';

                    return `
                <article
                    class="sa-report-item ${item.is_unread ? 'unread' : ''} ${important ? 'important' : ''}"
                    data-report-id="${escapeHtml(item.id)}"
                    title="Bericht öffnen"
                >
                    <div class="sa-report-avatar-wrap">
                        <img
                            src="${escapeHtml(item.employee_image || fallbackAvatar)}"
                            class="sa-report-avatar"
                            alt="${escapeHtml(employee)}"
                            onerror="this.src='${escapeHtml(fallbackAvatar)}'"
                        >

                        <span class="sa-report-type-dot">
                            <i data-lucide="${escapeHtml(typeIcon(item.type))}"></i>
                        </span>
                    </div>

                    <div class="sa-report-content">
                        <div class="sa-report-title">
                            ${escapeHtml(employee)} hat einen Bericht erstellt
                        </div>

                        <div class="sa-report-meta">
                            <span class="sa-report-type-pill">${escapeHtml(typeLabel(item.type))}</span>
                            <span class="sa-report-target">${escapeHtml(title)}</span>
                            ${targetNo ? `<span>${escapeHtml(targetNo)}</span>` : ''}
                            <span class="sa-report-time">${escapeHtml(item.created_human || 'gerade eben')}</span>
                        </div>

                        ${customer || status || subtitle
                            ? `
                                    <div class="sa-report-mini-details">
                                        ${customer ? `<span><i data-lucide="user" class="icon-sm"></i>${escapeHtml(customer)}</span>` : ''}
                                        ${status ? `<span><i data-lucide="circle-dot" class="icon-sm"></i>${escapeHtml(status)}</span>` : ''}
                                        ${subtitle ? `<span><i data-lucide="info" class="icon-sm"></i>${escapeHtml(subtitle)}</span>` : ''}
                                    </div>
                                `
                            : ''
                        }

                        <div class="sa-report-text">
                            ${escapeHtml(reportText || 'Kein Berichtstext vorhanden.')}
                        </div>
                    </div>
                </article>
            `;
                }).join('');

                refreshIcons();
            }

            async function requestJson(url, options = {}) {
                const response = await fetch(url, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        ...(options.headers || {}),
                    },
                    ...options,
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(data.message || 'HTTP ' + response.status);
                }

                return data;
            }

            async function fetchReports() {
                if (state.isLoading) return;

                setLoading(true);

                try {
                    const data = await requestJson(routes.list);

                    state.reports = Array.isArray(data.items) ? data.items : [];
                    state.lastUnreadCount = state.reports.filter(item => item.is_unread).length;

                    renderList();
                } catch (error) {
                    console.error('Report notifications failed:', error);

                    if (!state.reports.length) {
                        dom.list.innerHTML = `
                    <div class="sa-report-empty">
                        <div class="sa-report-empty-icon">
                            <i data-lucide="circle-alert"></i>
                        </div>
                        <strong>Reports konnten nicht geladen werden</strong>
                        <span>Bitte später erneut versuchen.</span>
                    </div>
                `;

                        refreshIcons();
                    }
                } finally {
                    setLoading(false);
                }
            }

            async function markRead(id) {
                if (!id) return;

                const oldReports = [...state.reports];

                state.reports = state.reports.map(item => {
                    if (String(item.id) === String(id)) {
                        return {
                            ...item,
                            is_unread: false,
                            read_at: new Date().toISOString(),
                        };
                    }

                    return item;
                });

                renderList();

                try {
                    await requestJson(routes.read(id), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                        },
                    });
                } catch (error) {
                    console.error('Mark report read failed:', error);
                    state.reports = oldReports;
                    renderList();
                }
            }

            async function markAllRead() {
                if (!state.reports.length) return;

                const oldReports = [...state.reports];

                state.reports = state.reports.map(item => ({
                    ...item,
                    is_unread: false,
                    read_at: new Date().toISOString(),
                }));

                renderList();

                try {
                    await requestJson(routes.readAll, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                        },
                    });
                } catch (error) {
                    console.error('Mark all reports read failed:', error);
                    state.reports = oldReports;
                    renderList();
                }
            }

            function findReport(id) {
                return state.reports.find(item => String(item.id) === String(id));
            }

            window.openSaReportModal = function (item) {
                if (!item || !dom.modalBackdrop) return;

                const employee = item.employee || 'Unbekannt';
                const title = item.title || item.target_label || ('#' + (item.target_id || item.id || ''));
                const reportText = stripHtml(item.report || '');

                if (dom.modalTitle) {
                    dom.modalTitle.textContent = `${typeLabel(item.type)} Bericht`;
                }

                if (dom.modalSub) {
                    dom.modalSub.textContent = `${employee} · ${item.created_human || 'gerade eben'}`;
                }

                if (dom.modalEmployee) {
                    dom.modalEmployee.textContent = employee;
                }

                if (dom.modalType) {
                    dom.modalType.textContent = typeLabel(item.type);
                }

                if (dom.modalTarget) {
                    dom.modalTarget.textContent = title;
                }

                if (dom.modalTime) {
                    dom.modalTime.textContent = item.created_at || item.created_human || '—';
                }

                if (dom.modalReport) {
                    dom.modalReport.textContent = reportText || '—';
                }

                if (dom.modalCustomer) {
                    dom.modalCustomer.textContent = item.target_customer || item.subtitle || '—';
                }

                if (dom.modalStatus) {
                    dom.modalStatus.textContent = item.target_status || '—';
                }

                if (dom.modalPriority) {
                    dom.modalPriority.textContent = item.target_priority || '—';
                }

                if (dom.modalTargetDate) {
                    dom.modalTargetDate.textContent = item.target_date || '—';
                }

                if (dom.modalOpenLink) {
                    if (item.target_link) {
                        dom.modalOpenLink.href = item.target_link;
                        dom.modalOpenLink.style.display = 'inline-flex';
                    } else {
                        dom.modalOpenLink.href = '#';
                        dom.modalOpenLink.style.display = 'none';
                    }
                }

                dom.modalBackdrop.style.display = 'flex';
                document.body.style.overflow = 'hidden';

                refreshIcons();
            };

            window.closeSaReportModal = function () {
                if (dom.modalBackdrop) {
                    dom.modalBackdrop.style.display = 'none';
                }

                document.body.style.overflow = '';
            };

            function playNotificationSound() {
                if (!dom.sound) return;

                dom.sound.currentTime = 0;
                dom.sound.play().catch(() => { });
            }

            function pushLiveReport(payload) {
                const item = {
                    id: payload.id,
                    type: payload.type || 'report',
                    target_id: payload.target_id || null,
                    title: payload.title || 'Neuer Bericht',
                    report: payload.report || '',
                    employee_id: payload.employee_id || null,
                    employee: payload.employee || 'Unbekannt',
                    employee_image: payload.employee_image || fallbackAvatar,
                    created_at: payload.created_at || new Date().toISOString(),
                    created_human: payload.created_human || 'gerade eben',
                    read_at: null,
                    is_unread: true,
                };

                const alreadyExists = state.reports.some(report => String(report.id) === String(item.id));

                if (alreadyExists) {
                    state.reports = state.reports.map(report => {
                        return String(report.id) === String(item.id) ? { ...report, ...item } : report;
                    });
                } else {
                    state.reports.unshift(item);
                }

                state.reports = state.reports.slice(0, 15);

                renderList();
                playNotificationSound();
            }

            function bindEvents() {
                dom.list?.addEventListener('click', function (event) {
                    const card = event.target.closest('.sa-report-item');
                    if (!card) return;

                    const id = card.dataset.reportId;
                    const item = findReport(id);

                    if (!item) return;

                    window.openSaReportModal(item);
                    markRead(id);
                });

                dom.markAll?.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    markAllRead();
                });

                dom.dropdown?.addEventListener('click', function (event) {
                    event.stopPropagation();
                });

                dom.modalBackdrop?.addEventListener('click', function (event) {
                    if (event.target === dom.modalBackdrop) {
                        window.closeSaReportModal();
                    }
                });

                dom.tabs?.forEach(button => {
                    button.addEventListener('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();

                        dom.tabs.forEach(tab => tab.classList.remove('active'));
                        this.classList.add('active');

                        state.filter = this.dataset.reportFilter || 'all';
                        renderList();
                    });
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        window.closeSaReportModal();
                    }
                });
            }

            function bindEcho() {
                if (!window.Echo) return;

                window.Echo.channel('overdue-reports')
                    .listen('.overdue.report.created', function (payload) {
                        pushLiveReport(payload);
                    });
            }

            bindEvents();
            bindEcho();

            fetchReports();

            state.refreshTimer = setInterval(fetchReports, 60000);
        })();
    </script>

    <script>
        (function () {
            'use strict';

            if (window.__SA_ROUTE_ACTIVE_BOOTED__) return;
            window.__SA_ROUTE_ACTIVE_BOOTED__ = true;

            function cleanUrl(url) {
                if (!url || url === '#') return '';

                try {
                    const parsed = new URL(url, window.location.origin);

                    let path = parsed.pathname || '/';

                    path = path.replace(/\/+$/, '');

                    return path || '/';
                } catch (e) {
                    return '';
                }
            }

            function currentPath() {
                let path = window.location.pathname || '/';

                path = path.replace(/\/+$/, '');

                return path || '/';
            }

            function isRealLink(element) {
                const href = element.getAttribute('href');

                if (!href) return false;
                if (href === '#') return false;
                if (href.startsWith('javascript:')) return false;
                if (href.startsWith('mailto:')) return false;
                if (href.startsWith('tel:')) return false;

                return true;
            }

            function scoreLink(linkPath, activePath) {
                if (!linkPath) return 0;

                if (linkPath === activePath) {
                    return 100000 + linkPath.length;
                }

                if (linkPath !== '/' && activePath.startsWith(linkPath + '/')) {
                    return 50000 + linkPath.length;
                }

                return 0;
            }

            function clearActiveStates(scope) {
                scope.querySelectorAll('.is-route-active').forEach(function (item) {
                    item.classList.remove('is-route-active');
                });

                scope.querySelectorAll('.has-active-child').forEach(function (item) {
                    item.classList.remove('has-active-child');
                });

                scope.querySelectorAll('.open-by-route').forEach(function (item) {
                    item.classList.remove('open-by-route');
                });
            }

            function findBestRouteLink(scope) {
                const activePath = currentPath();
                const links = Array.from(scope.querySelectorAll('a[href]')).filter(isRealLink);

                let best = null;
                let bestScore = 0;

                links.forEach(function (link) {
                    const linkPath = cleanUrl(link.getAttribute('href'));
                    const score = scoreLink(linkPath, activePath);

                    if (score > bestScore) {
                        best = link;
                        bestScore = score;
                    }
                });

                return best;
            }

            function openParentMenus(activeLink) {
                if (!activeLink) return;

                const submenu = activeLink.closest('.submenu');

                if (submenu) {
                    submenu.classList.add('open', 'open-by-route');

                    const submenuId = submenu.getAttribute('id');

                    if (submenuId) {
                        const icon = document.getElementById('icon-' + submenuId);

                        if (icon) {
                            icon.style.transform = 'rotate(180deg)';
                        }
                    }
                }

                const submenuParent = submenu
                    ? submenu.previousElementSibling
                    : null;

                if (submenuParent) {
                    submenuParent.classList.add('has-active-child');
                }

                const liParent = activeLink.closest('li');

                if (liParent) {
                    liParent.classList.add('is-route-active');

                    const parentUl = liParent.closest('ul');

                    if (parentUl) {
                        const parentLi = parentUl.closest('li');

                        if (parentLi) {
                            parentLi.classList.add('has-active-child');

                            const parentAnchor = parentLi.querySelector(':scope > a');

                            if (parentAnchor) {
                                parentAnchor.classList.add('has-active-child');
                            }
                        }
                    }
                }

                const dropdownPanel = activeLink.closest('.qs-sub');

                if (dropdownPanel) {
                    const toggle = document.querySelector('[aria-controls="' + dropdownPanel.id + '"]');

                    if (toggle) {
                        toggle.classList.add('has-active-child');
                    }
                }
            }

            function applyRouteActiveState() {
                const scopes = [
                    document.getElementById('leftSidebar'),
                    document.getElementById('quickSider'),
                    document.querySelector('.mobile-nav'),
                ].filter(Boolean);

                scopes.forEach(function (scope) {
                    clearActiveStates(scope);

                    const bestLink = findBestRouteLink(scope);

                    if (!bestLink) return;

                    bestLink.classList.add('is-route-active');
                    openParentMenus(bestLink);
                });

                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }

                if (window.feather && typeof window.feather.replace === 'function') {
                    window.feather.replace();
                }
            }

            document.addEventListener('DOMContentLoaded', applyRouteActiveState);

            window.SaRouteActive = {
                refresh: applyRouteActiveState
            };
        })();
    </script>
    <!-- Sidebar Counter -->
    <!-- Sidebar Counter -->
    <script>
        (function () {
            'use strict';

            if (window.__SA_SIDEBAR_COUNTS_BOOTED__) return;
            window.__SA_SIDEBAR_COUNTS_BOOTED__ = true;

            const config = {
                url: @json(route('api.sidebar.counts')),
                refreshMs: 30000,
            };

            const state = {
                isLoading: false,
                lastCounts: {},
                timer: null,
            };

            function formatCount(value) {
                const number = Number(value || 0);

                if (number > 9999) {
                    return Math.floor(number / 1000) + 'k+';
                }

                if (number > 99) {
                    return '99+';
                }

                return String(number);
            }

            function getBadgeTone(key, count) {
                const hotKeys = [
                    'chat_unread',
                    'tickets_open',
                    'reports_remaining_total',
                    'reports_all_remaining',
                    'reports_my_remaining',
                    'inquiries_unpublished',
                    'my_inquiries_unpublished',
                    'customer_inquiries_unpublished',
                    'customers_waiting',
                    'system_warnings',
                ];

                const successKeys = [
                    'inquiries_published',
                    'attendance_today',
                    'employees',
                    'customers',
                    'offers',
                    'deals',
                    'invoices',
                ];

                if (count <= 0) return '';

                if (hotKeys.includes(key)) {
                    return 'is-hot';
                }

                if (successKeys.includes(key)) {
                    return 'is-success';
                }

                return 'is-info';
            }

            function updateBadgeElement(element, key, value) {
                const oldValue = Number(element.dataset.countValue || 0);
                const count = Number(value || 0);

                element.dataset.countValue = String(count);

                element.classList.remove('is-empty', 'is-hot', 'is-info', 'is-success');

                if (count > 0) {
                    element.textContent = formatCount(count);
                    element.title = count + ' Einträge';
                    element.classList.add(getBadgeTone(key, count));

                    if (oldValue !== count) {
                        element.classList.remove('is-pulse');
                        void element.offsetWidth;
                        element.classList.add('is-pulse');
                    }
                } else {
                    element.textContent = '0';
                    element.title = '';
                    element.classList.add('is-empty');
                }
            }

            function updateCounts(counts) {
                state.lastCounts = counts || {};

                document.querySelectorAll('[data-sidebar-count]').forEach(function (badge) {
                    const key = badge.getAttribute('data-sidebar-count');
                    const value = state.lastCounts[key] || 0;

                    updateBadgeElement(badge, key, value);
                });
            }

            async function fetchSidebarCounts() {
                if (state.isLoading) return;

                state.isLoading = true;

                try {
                    const response = await fetch(config.url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    });

                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }

                    const data = await response.json();

                    updateCounts(data.counts || {});
                } catch (error) {
                    console.error('Sidebar counts could not be loaded:', error);
                } finally {
                    state.isLoading = false;
                }
            }

            function bootEchoRefresh() {
                if (!window.Echo) {
                    setTimeout(bootEchoRefresh, 1000);
                    return;
                }

                try {
                    window.Echo.private('sidebar.counts')
                        .listen('.sidebar.counts.changed', function () {
                            fetchSidebarCounts();
                        })
                        .listen('.overdue.report.created', function () {
                            fetchSidebarCounts();
                        })
                        .listen('.chat.message.created', function () {
                            fetchSidebarCounts();
                        });
                } catch (error) {
                    console.warn('Sidebar count Echo listener failed:', error);
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                fetchSidebarCounts();

                state.timer = setInterval(fetchSidebarCounts, config.refreshMs);

                bootEchoRefresh();
            });

            window.SaSidebarCounts = {
                refresh: fetchSidebarCounts,
                update: updateCounts,
            };
        })();
    </script>
    <script>
        (function () {
            'use strict';

            if (window.__SA_SIDEBAR_GROUPS_BOOTED__) return;
            window.__SA_SIDEBAR_GROUPS_BOOTED__ = true;

            function refreshSidebarIcons() {
                if (window.lucide && typeof window.lucide.createIcons === 'function') {
                    window.lucide.createIcons();
                }

                if (window.feather && typeof window.feather.replace === 'function') {
                    window.feather.replace();
                }
            }

            window.toggleSidebarSection = function (id) {
                const section = document.getElementById(id);
                if (!section) return;

                const isOpen = section.classList.contains('is-open');
                const toggle = section.querySelector('.sa-section-toggle');

                section.classList.toggle('is-open', !isOpen);
                section.classList.toggle('is-collapsed', isOpen);

                if (toggle) {
                    toggle.setAttribute('aria-expanded', String(!isOpen));
                }

                localStorage.setItem('sa_sidebar_section_' + id, !isOpen ? 'open' : 'closed');

                refreshSidebarIcons();
            };

            function sectionHasActiveLink(section) {
                if (!section) return false;

                return Boolean(
                    section.querySelector(
                        '.is-route-active, .has-active-child, .submenu.open-by-route'
                    )
                );
            }

            function applySidebarSectionInitialState() {
                document.querySelectorAll('[data-sidebar-section]').forEach(function (section) {
                    const id = section.id;
                    const stored = localStorage.getItem('sa_sidebar_section_' + id);
                    const defaultOpen = section.getAttribute('data-section-default-open') === '1';
                    const hasActive = sectionHasActiveLink(section);

                    /*
                    |--------------------------------------------------------------------------
                    | Open priority:
                    | 1. Active route section
                    | 2. User saved state
                    | 3. Default state: Hauptmenü + CRM open
                    |--------------------------------------------------------------------------
                    */
                    const shouldOpen = hasActive || stored === 'open' || (!stored && defaultOpen);

                    section.classList.toggle('is-open', shouldOpen);
                    section.classList.toggle('is-collapsed', !shouldOpen);

                    const toggle = section.querySelector('.sa-section-toggle');
                    if (toggle) {
                        toggle.setAttribute('aria-expanded', String(shouldOpen));
                    }
                });

                refreshSidebarIcons();
            }

            document.addEventListener('DOMContentLoaded', function () {
                applySidebarSectionInitialState();

                /*
                Route active script may run after DOMContentLoaded.
                Run once more shortly after, so active section opens correctly.
                */
                setTimeout(applySidebarSectionInitialState, 150);
            });

            window.SaSidebarSections = {
                refresh: applySidebarSectionInitialState
            };
        })();
    </script>

    <script>
        (function () {
            'use strict';

            const summaryUrl = @json(Route::has('admin.recent-reports.employee-summary') ? route('admin.recent-reports.employee-summary') : null);
            const currentEmployeeId = @json((int) ($employeeId ?? 0));

            if (!summaryUrl || window.__SA_REPORT_SIDEBAR_COUNTS__) return;
            window.__SA_REPORT_SIDEBAR_COUNTS__ = true;

            function formatReportCount(value) {
                const number = Number(value || 0);
                if (number > 9999) return Math.floor(number / 1000) + 'k+';
                if (number > 99) return '99+';
                return String(number);
            }

            function setSidebarCount(key, value) {
                document.querySelectorAll('[data-sidebar-count="' + key + '"]').forEach(function (badge) {
                    const count = Number(value || 0);
                    if (count > 0) {
                        badge.textContent = formatReportCount(count);
                        badge.style.display = 'inline-flex';
                        badge.setAttribute('title', count + ' offene Berichte');
                    } else {
                        badge.textContent = '0';
                        badge.style.display = 'none';
                        badge.removeAttribute('title');
                    }
                });
            }

            async function refreshReportSidebarCounts() {
                try {
                    const response = await fetch(summaryUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });

                    if (!response.ok) return;

                    const data = await response.json();
                    const items = Array.isArray(data.items) ? data.items : [];
                    const totals = data.totals || {};
                    const mine = items.find(function (item) {
                        return Number(item.id) === Number(currentEmployeeId);
                    });

                    const allRemaining = Number(totals.total || 0);
                    const myRemaining = Number(mine?.total || 0);

                    setSidebarCount('reports_remaining_total', allRemaining);
                    setSidebarCount('reports_all_remaining', allRemaining);
                    setSidebarCount('reports_my_remaining', myRemaining);
                } catch (error) {
                    console.warn('Report sidebar counts could not be loaded:', error);
                }
            }

            document.addEventListener('DOMContentLoaded', refreshReportSidebarCounts);
            window.addEventListener('sa:reports-updated', refreshReportSidebarCounts);

            setInterval(refreshReportSidebarCounts, 45000);
        })();
    </script>

    <!-- Task Managment  -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const employeeId = @json($employeeId ?? null);

            if (!employeeId || !window.Echo) {
                console.warn('Echo or employeeId missing for personal task reminders.');
                return;
            }

            window.Echo.private(`employee.${employeeId}.tasks`)
                .listen('.personal-task.reminder', function (e) {
                    console.log('Personal task realtime reminder:', e);

                    if (window.Swal) {
                        Swal.fire({
                            icon: e.type === 'repeat' ? 'success' : 'info',
                            title: e.type === 'repeat' ? 'Wiederholte Aufgabe' : 'Aufgaben-Erinnerung',
                            html: `
                        <div style="text-align:left">
                            <strong>${e.task_title || 'Aufgabe'}</strong><br>
                            <span>${e.message || ''}</span>
                        </div>
                    `,
                            showCancelButton: true,
                            confirmButtonText: 'Öffnen',
                            cancelButtonText: 'Schließen',
                        }).then((result) => {
                            if (result.isConfirmed && e.url) {
                                window.location.href = e.url;
                            }
                        });
                    } else {
                        alert(e.message || 'Neue Aufgaben-Erinnerung');
                    }

                    const taskCard = document.querySelector(`[data-task-id="${e.task_id}"]`);
                    if (taskCard) {
                        taskCard.classList.add('pt-highlight');
                        setTimeout(() => taskCard.classList.remove('pt-highlight'), 4000);
                    }
                });
        });
    </script>
    <!-- Task Managment -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const employeeId = @json($employeeId ?? null);

            if (!employeeId || !window.Echo) {
                console.warn('Echo or employeeId missing for personal task reminders.');
                return;
            }

            window.Echo.private(`employee.${employeeId}.tasks`)
                .listen('.personal-task.reminder', function (e) {
                    console.log('Personal task realtime reminder:', e);
                });
        });
    </script>

    @includeIf('admin.layouts.partials.general-task-realtime-toast')

    {{-- Put this before
</body> in resources/views/admin/layouts/app.blade.php --}}
@if(\Illuminate\Support\Facades\Route::has('admin.maintenance.contracts.incoming'))
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            if (window.__maintenanceIncomingBooted) return;
            window.__maintenanceIncomingBooted = true;

            try {
                const response = await fetch("{{ route('admin.maintenance.contracts.incoming') }}?days=30", {
                    headers: { 'Accept': 'application/json' }
                });

                const data = await response.json();
                const items = data.items || [];

                if (!data.ok || !items.length) return;

                const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g, m => ({
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
                }[m]));

                const box = document.createElement('div');
                box.style.cssText = `
                    position:fixed;right:22px;bottom:22px;z-index:99999;
                    width:390px;max-width:calc(100vw - 40px);
                    background:#fff;border:1px solid #f59e0b;border-radius:18px;
                    box-shadow:0 22px 55px rgba(15,23,42,.22);overflow:hidden;
                    font-family:Inter,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
                `;

                box.innerHTML = `
                    <div style="padding:14px 16px;background:#fffbeb;color:#92400e;font-weight:950;display:flex;justify-content:space-between;gap:10px;align-items:center;">
                        <span><i class="fa fa-bell"></i> Incoming Wartungsverträge (${items.length})</span>
                        <button type="button" style="border:0;background:transparent;cursor:pointer;font-size:18px;color:#92400e;">×</button>
                    </div>
                    <div style="padding:12px;max-height:340px;overflow:auto;">
                        ${items.slice(0, 8).map(item => `
                            <a href="${escapeHtml(item.show_url)}" style="display:block;text-decoration:none;color:#111827;padding:12px;border:1px solid #fde68a;border-radius:14px;margin-bottom:10px;background:#fffdf5;">
                                <div style="font-weight:950;">${escapeHtml(item.contract_no || '')} ${escapeHtml(item.title || 'Wartung')}</div>
                                <div style="font-size:12px;color:#6b7280;margin-top:4px;">${escapeHtml(item.customer || '–')}</div>
                                <div style="font-size:12px;color:#b45309;margin-top:4px;">Nächste Wartung: ${escapeHtml(item.next_service_de || '–')}</div>
                            </a>
                        `).join('')}
                    </div>
                `;

                box.querySelector('button').addEventListener('click', () => box.remove());
                document.body.appendChild(box);
            } catch (error) {
                console.warn('Maintenance incoming notification failed', error);
            }
        });
    </script>
@endif


@stack('scripts')
@yield('script')
</body>

</html>