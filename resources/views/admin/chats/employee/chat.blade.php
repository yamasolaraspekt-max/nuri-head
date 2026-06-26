<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8" />
    <title>SA-DESK - CHAT</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- jQuery + vendors used in chat.js -->
    <script src="{{asset('js/jquery-3.7.1.min.js')}}"></script>
    <script src="{{ asset('js/dropzone.min.js') }}"></script>
    <link  href="{{ asset('css/dropzone.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('js/sweetalert2@11.js') }}"></script>
    <link  href="{{ asset('css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('js/select2.min.js') }}"></script> 
    <!-- Feather Icons -->
    <script src="{{ asset('js/feather-icons.js') }}"></script>

    <style>
        :root {
            --c-green: #93c21c;
            --c-pink: #cfe09b;
            --c-blue: #74b2d4;
            --c-blue-light: #c0d8ea;
        }

        /* Mobile slide-in sidebars */
        .sidebar-left,
        .sidebar-right {
            transition: transform 0.25s ease;
        }
        .sidebar-left.hidden-mobile {
            transform: translateX(-100%);
        }
        .sidebar-right.hidden-mobile {
            transform: translateX(100%);
        }
        @media (min-width: 1024px) {
            .sidebar-left,
            .sidebar-right {
                transform: translateX(0) !important;
                position: static !important;
            }
        }

        /* Chat-specific helpers */
        .sentinel{ height:1px; }

            .bg-mine,
            .bg-theirs {
                max-width: 100%;
                word-wrap: break-word;
                word-break: break-word;
                overflow-wrap: anywhere;
                box-sizing: border-box;
            }

        /* voice bubble */
        .voice-wrap{
            display:flex;
            align-items:center;
            gap:.5rem;
            background: var(--c-blue-light);
            border:1px solid rgba(148,163,184,.7);
            border-radius:0.75rem;
            padding:.5rem .6rem;
            max-width:100%;
        }
        .voice-audio{
            height:32px;
            max-width:260px;
            width:100%;
            flex:1 1 auto;
        }
        .voice-meta{
            font-size:12px;
            color:#6b7280;
            white-space:nowrap;
            min-width: 42px;
            text-align: right;
        }

        /* Emoji popover */
        .emoji-list{
            position:absolute;
            bottom:3.0rem;
            left:0;
            background:#fff;
            border:1px solid #e5e7eb;
            border-radius:0.75rem;
            padding:0.5rem;
            display:grid;
            grid-template-columns:repeat(8,1.6rem);
            gap:.35rem;
            box-shadow:0 12px 28px rgba(0,0,0,.12), 0 2px 6px rgba(0,0,0,.08);
            max-height:220px;
            overflow:auto;
            z-index:40;
        }
        .emoji-list span{
            font-size:20px;
            line-height:1;
            padding:6px;
            border-radius:0.5rem;
            cursor:pointer;
            transition:background .12s, transform .08s;
        }
        .emoji-list span:hover{
            background:#f3f4f6;
            transform:translateY(-1px);
        }

        /* Mic animation in your palette */
        @keyframes mic-pulse{
            0%{box-shadow:0 0 0 0 rgba(148, 187, 233, .65)}
            70%{box-shadow:0 0 0 12px rgba(148, 187, 233, 0)}
            100%{box-shadow:0 0 0 0 rgba(148, 187, 233, 0)}
        }
        .recording-pulse{ animation:mic-pulse 1.2s infinite }

        /* mine / theirs colors */
            .bg-mine{
                background: #fefefe;
                border: 1px solid rgba(148, 163, 184, 0.5);
                color:#0f172a;
                padding:10px;

            }

            .bg-theirs{
                background: #c0d8ea;       /* requested color */
                border: 1px solid rgba(148, 163, 184, 0.5);
                color:#0f172a;
                padding:10px;
            }

            /* generic bubble + room for icons on the right */
            .message-bubble{
                position: relative;
                padding-right: 2.75rem;    /* space so icons never sit on top of text/time */
                box-sizing: border-box;
                max-width: 100%;
                word-wrap: break-word;
                word-break: break-word;
                overflow-wrap: anywhere;
            }

            /* hover buttons default: top-right inside bubble */
            .hover-buttons{
                position: absolute;
                top: -0.71rem;
                right: 0.35rem;
                display: flex;
                gap: 0.25rem;
                align-items: center;
                opacity: 0;
                transition: opacity .15s ease;
            }

            /* show actions on hover (desktop) */
            .group:hover .hover-buttons{
                opacity: 1;
            }

            /* mobile: make actions flow under the text instead of absolute overlay */
            @media (max-width: 640px){
                .message-bubble{
                    padding-right: 0.75rem; /* less room needed when buttons move below */
                }
                .hover-buttons{
                    position: static;
                    margin-top: 0.25rem;
                    justify-content: flex-end;
                    opacity: 1;             /* always visible on touch devices */
                }
            }

            @keyframes newMsgBorder {
                0% {
                    box-shadow: 0 0 0 0 rgba(116, 178, 212, 0.9); /* #74b2d4 */
                    border-color: #74b2d4;
                }
                70% {
                    box-shadow: 0 0 0 8px rgba(116, 178, 212, 0);
                    border-color: #74b2d4;
                }
                100% {
                    box-shadow: 0 0 0 0 rgba(116, 178, 212, 0);
                    border-color: rgba(148, 163, 184, 0.5); /* back to default */
                }
            }

            .message-new {
                animation: newMsgBorder 1.4s ease-out 2;
            }

            /* Clamp long messages and expand on "Mehr lesen" */
            .sa-clamped {
                display: -webkit-box;
                -webkit-line-clamp: 6;          /* ~6 lines */
                -webkit-box-orient: vertical;
                overflow: hidden;
            }


        </style>

        <style>
            /* Tutorials entry in left sidebar */
            .tutorial-entry {
                position: relative;
            }
            .tutorial-entry::before {
                content: "Neu";
                position: absolute;
                top: -0.45rem;
                right: 0.75rem;
                font-size: 9px;
                padding: 1px 6px;
                border-radius: 999px;
                background: #f97316;
                color: #fff;
            }

            /* Tutorial article body inside chat */
            .tutorial-body {
                background: #f9fafb;
                border-radius: 0.75rem;
                padding: 0.75rem 0.9rem;
                border: 1px solid rgba(148, 163, 184, 0.4);
            }
            .tutorial-body h1,
            .tutorial-body h2,
            .tutorial-body h3 {
                font-weight: 600;
                margin-top: 0.75rem;
                margin-bottom: 0.4rem;
            }
            .tutorial-body p {
                margin-bottom: 0.4rem;
            }
            .tutorial-body ul,
            .tutorial-body ol {
                padding-left: 1.1rem;
                margin-bottom: 0.5rem;
            }
            .tutorial-body code {
                font-size: 0.85em;
                background: #e5e7eb;
                padding: 2px 4px;
                border-radius: 4px;
            }

        </style>

        <style>
            /* Solar news message bubble */
            .message-bubble.news-solar {
                border-left: 3px solid #22c55e;              /* emerald accent */
                background: linear-gradient(
                    135deg,
                    rgba(16, 185, 129, 0.10),
                    rgba(59, 130, 246, 0.05)
                );
                position: relative;
            }

            /* Small pill badge for the news label */
            .news-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .08em;
                padding: 2px 8px;
                border-radius: 999px;
                background: rgba(16, 185, 129, 0.12);
                color: #047857; /* emerald-700 */
                margin-bottom: 4px;
            }

            /* Tiny dot inside the badge (optional) */
            .news-badge-dot {
                width: 6px;
                height: 6px;
                border-radius: 999px;
                backg
                round: #22c55e;
            }

            .message-bubble.news-solar {
                border-left: 3px solid #22c55e;
                background: linear-gradient(
                    135deg,
                    rgba(16, 185, 129, 0.10),
                    rgba(59, 130, 246, 0.05)
                );
                position: relative;
            }

            .news-badge {
                display: inline-flex;
                align-items: center;
                gap: 4px;
                font-size: 10px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: .08em;
                padding: 2px 8px;
                border-radius: 999px;
                background: rgba(16, 185, 129, 0.12);
                color: #047857;
                margin-bottom: 4px;
            }

           .news-badge-dot {
                width: 6px;
                height: 6px;
                border-radius: 999px;
                background: #22c55e;
            }

           .chat-drop-overlay{
                position: fixed;
                inset: 0;
                display: none;
                align-items: center;
                justify-content: center;
                background: rgba(15, 23, 42, 0.40);
                backdrop-filter: blur(2px);
                z-index: 99999;
                pointer-events: none; /* overlay should not block drag/drop */
            }

            .chat-drop-overlay.is-active{
                display: flex;
            }

            .chat-drop-overlay > *{
                pointer-events: none;
            }

                .chat-drop-card{
                width: min(520px, calc(100% - 32px));
                border-radius: 18px;
                background: #ffffff;
                border: 1px dashed rgba(148,163,184,.9);
                box-shadow: 0 18px 60px rgba(15,23,42,.25);
                padding: 18px 20px;
                text-align: center;
                }

                .chat-drop-title{
                font-weight: 800;
                font-size: 16px;
                color: #0f172a;
                }

                .chat-drop-sub{
                margin-top: 6px;
                font-size: 12px;
                color: #64748b;
                }



                /* =========================================================
                Chat Media / PDF Lightbox
                ========================================================= */
                .chat-media-modal {
                    position: fixed;
                    inset: 0;
                    z-index: 100000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .chat-media-modal.hidden {
                    display: none;
                }

                .chat-media-backdrop {
                    position: absolute;
                    inset: 0;
                    background:
                        radial-gradient(circle at top right, rgba(116, 178, 212, .28), transparent 35%),
                        rgba(15, 23, 42, .82);
                    backdrop-filter: blur(8px);
                }

                .chat-media-shell {
                    position: relative;
                    width: min(1120px, calc(100vw - 24px));
                    height: min(820px, calc(100vh - 24px));
                    border-radius: 26px;
                    background: rgba(255, 255, 255, .96);
                    border: 1px solid rgba(255, 255, 255, .55);
                    box-shadow: 0 30px 90px rgba(15, 23, 42, .45);
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                }

                .chat-media-header {
                    height: 62px;
                    padding: 0 16px 0 20px;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 12px;
                    border-bottom: 1px solid rgba(148, 163, 184, .28);
                    background: linear-gradient(90deg, rgba(227, 239, 251, .95), rgba(255,255,255,.95));
                }

                .chat-media-title {
                    font-weight: 800;
                    font-size: 14px;
                    color: #0f172a;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                    max-width: min(680px, calc(100vw - 180px));
                }

                .chat-media-counter {
                    margin-top: 2px;
                    font-size: 11px;
                    color: #64748b;
                }

                .chat-media-actions {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }

                .chat-media-icon-btn {
                    width: 38px;
                    height: 38px;
                    border-radius: 999px;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    background: #ffffff;
                    border: 1px solid rgba(148, 163, 184, .45);
                    color: #334155;
                    transition: transform .15s ease, background .15s ease, border-color .15s ease;
                }

                .chat-media-icon-btn:hover {
                    transform: translateY(-1px);
                    background: #f8fafc;
                    border-color: #74b2d4;
                }

                .chat-media-body {
                    flex: 1;
                    min-height: 0;
                    background:
                        linear-gradient(135deg, rgba(192, 216, 234, .22), rgba(207, 224, 155, .18)),
                        #f8fafc;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 18px;
                }

                .chat-media-body img {
                    max-width: 100%;
                    max-height: 100%;
                    border-radius: 18px;
                    object-fit: contain;
                    box-shadow: 0 18px 60px rgba(15, 23, 42, .20);
                    background: white;
                }

                .chat-media-body iframe {
                    width: 100%;
                    height: 100%;
                    border: 0;
                    border-radius: 18px;
                    background: white;
                    box-shadow: 0 18px 60px rgba(15, 23, 42, .18);
                }

                .chat-media-nav {
                    position: absolute;
                    top: 50%;
                    z-index: 4;
                    width: 48px;
                    height: 48px;
                    transform: translateY(-50%);
                    border-radius: 999px;
                    background: rgba(255, 255, 255, .92);
                    border: 1px solid rgba(148, 163, 184, .5);
                    color: #0f172a;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    box-shadow: 0 12px 35px rgba(15, 23, 42, .22);
                    transition: transform .15s ease, background .15s ease, opacity .15s ease;
                }

                .chat-media-nav:hover {
                    transform: translateY(-50%) scale(1.04);
                    background: #ffffff;
                }

                .chat-media-nav:disabled {
                    opacity: .35;
                    cursor: not-allowed;
                }

                .chat-media-prev {
                    left: 18px;
                }

                .chat-media-next {
                    right: 18px;
                }

                .chat-attachment-album {
                    display: flex;
                    flex-direction: column;
                    gap: 8px;
                    max-width: min(100%, 330px);
                }

                .chat-attachment-summary {
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    width: fit-content;
                    max-width: 100%;
                    padding: 4px 9px;
                    border-radius: 999px;
                    background: rgba(255,255,255,.72);
                    border: 1px solid rgba(148, 163, 184, .35);
                    color: #475569;
                    font-size: 11px;
                    font-weight: 600;
                }

                .chat-image-grid {
                    display: grid;
                    gap: 4px;
                    overflow: hidden;
                    border-radius: 16px;
                    border: 1px solid rgba(148, 163, 184, .45);
                    background: rgba(255,255,255,.65);
                    box-shadow: 0 8px 24px rgba(15, 23, 42, .12);
                }

                .chat-image-grid-one {
                    grid-template-columns: minmax(180px, 260px);
                }

                .chat-image-grid-two {
                    grid-template-columns: repeat(2, minmax(112px, 1fr));
                }

                .chat-image-grid-many {
                    grid-template-columns: repeat(2, minmax(112px, 1fr));
                }

                .chat-image-tile {
                    position: relative;
                    display: block;
                    min-width: 0;
                    aspect-ratio: 1 / 1;
                    overflow: hidden;
                    background: #e2e8f0;
                    cursor: zoom-in;
                    border: 0;
                    padding: 0;
                }

                .chat-image-grid-one .chat-image-tile {
                    aspect-ratio: 4 / 3;
                }

                .chat-attachment-image {
                    width: 100%;
                    height: 100%;
                    max-width: none;
                    max-height: none;
                    object-fit: cover;
                    border-radius: 0;
                    cursor: zoom-in;
                    border: 0;
                    box-shadow: none;
                    transition: transform .15s ease, filter .15s ease;
                }

                .chat-image-tile:hover .chat-attachment-image {
                    transform: scale(1.025);
                    filter: brightness(.96);
                }

                .chat-image-more {
                    position: absolute;
                    inset: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: rgba(15, 23, 42, .54);
                    color: #fff;
                    font-size: 28px;
                    font-weight: 800;
                    backdrop-filter: blur(1px);
                }

                .chat-file-stack {
                    display: flex;
                    flex-direction: column;
                    gap: 6px;
                }

                .chat-attachment-file {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    max-width: 100%;
                    padding: 8px 10px;
                    border-radius: 14px;
                    background: rgba(255,255,255,.75);
                    border: 1px solid rgba(148, 163, 184, .45);
                    color: #0f172a;
                    font-size: 12px;
                    text-align: left;
                    cursor: pointer;
                    transition: background .15s ease, border-color .15s ease;
                }

                .chat-attachment-file:hover {
                    background: #ffffff;
                    border-color: #74b2d4;
                }

                @media (max-width: 640px) {
                    .chat-media-shell {
                        width: 100vw;
                        height: 100vh;
                        border-radius: 0;
                    }

                    .chat-media-body {
                        padding: 10px;
                    }

                    .chat-media-prev {
                        left: 8px;
                    }

                    .chat-media-next {
                        right: 8px;
                    }

                    .chat-media-nav {
                        width: 42px;
                        height: 42px;
                    }

                    .chat-media-title {
                        max-width: calc(100vw - 160px);
                    }
                }

                .direction-ltr,
                    .message-content,
                    .message-text {
                        direction: ltr;
                        text-align: left;
                        unicode-bidi: plaintext;
                    }
        </style>

</head>
<body class="text-slate-900" style="background-color: #f1f1f1ff;">


@php
$emp = DB::table('employees')
    ->select('name', 'lastname', 'image')
    ->where('id', auth()->user()->name)
    ->first();

$empImage = $emp->image ?? 'users.png';
$fullname = trim(($emp->name ?? '') . ' ' . ($emp->lastname ?? '')) ?: 'Ich';
@endphp

<div class="min-h-screen flex flex-col px-2 sm:px-4 py-4">

   <!-- Top bar / navigation -->
<header class="mb-4">
    <!-- Mobile top bar -->
    <div class="lg:hidden">
        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white px-3 py-3 shadow-sm">
            <a href="{{ url('/') }}"
               class="inline-flex items-center gap-2 rounded-full bg-[#93c21c] px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-600">
                <i data-feather="arrow-left" class="h-4 w-4"></i>
                Dashboard
            </a>

            <div class="text-right">
                <div class="text-sm font-bold text-slate-800">SA-DESK</div>
                <div class="text-[11px] text-slate-500">Chat</div>
            </div>
        </div>
    </div>

    <!-- Desktop header: fixed one-line layout -->
    <div class="hidden lg:block">
        <div class="flex h-[70px] items-center gap-3 rounded-3xl border border-slate-200 bg-white px-4 shadow-sm">

            <!-- Brand -->
            <div class="flex shrink-0 items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-[#93c21c] text-white shadow-sm">
                    <i data-feather="message-circle" class="h-5 w-5"></i>
                </div>

                <div class="leading-tight">
                    <h1 class="text-xl font-black tracking-tight text-slate-900">
                        SA-DESK
                    </h1>
                    <p class="text-xs text-slate-500">
                        Team Chat
                    </p>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="ml-auto flex h-full min-w-0 items-center justify-end gap-1.5 overflow-x-auto whitespace-nowrap">
                <a href="{{ url('/') }}"
                   class="inline-flex h-10 shrink-0 items-center justify-center gap-1.5 rounded-full bg-[#93c21c] px-3 text-xs font-semibold text-white shadow-sm transition hover:bg-emerald-600">
                    <i data-feather="home" class="h-4 w-4 shrink-0"></i>
                    <span>Dashboard</span>
                </a>

                <a href="{{ url('new_lead_view') }}"
                   class="inline-flex h-10 shrink-0 items-center justify-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-[#74b2d4] hover:bg-[#e3effb]">
                    <i data-feather="list" class="h-4 w-4 shrink-0 text-[#74b2d4]"></i>
                    <span>Kunden-Liste</span>
                </a>

                <a href="{{ url('lead/kanban') }}"
                   class="inline-flex h-10 shrink-0 items-center justify-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-[#74b2d4] hover:bg-[#e3effb]">
                    <i data-feather="folder" class="h-4 w-4 shrink-0 text-[#74b2d4]"></i>
                    <span>Kunden Übersicht</span>
                </a>

                @php 
                    $row = $notification ?? null;
                @endphp

                @if(
    $row
    && $row->item_id === 'Employee'
    && $row->is_read === 'on'
    && $row->user_id === auth()->user()->name
)
                    <a href="{{ url('emp') }}"
                       class="inline-flex h-10 shrink-0 items-center justify-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-[#93c21c] hover:bg-[#f4f9e8]">
                        <i data-feather="users" class="h-4 w-4 shrink-0 text-[#93c21c]"></i>
                        <span>Mitarbeiter</span>
                    </a>
                @endif

                <a href="{{ url('admin/todo/personal?tab=my') }}"
                   class="inline-flex h-10 shrink-0 items-center justify-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-[#93c21c] hover:bg-[#f4f9e8]">
                    <i data-feather="check-square" class="h-4 w-4 shrink-0 text-[#93c21c]"></i>
                    <span>Aufgaben</span>
                </a>

                <a href="{{ url('tasks/calendar/personal') }}"
                   class="inline-flex h-10 shrink-0 items-center justify-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-[#74b2d4] hover:bg-[#e3effb]">
                    <i data-feather="calendar" class="h-4 w-4 shrink-0 text-[#74b2d4]"></i>
                    <span>Kalender</span>
                </a>

                <a href="#"
                   class="inline-flex h-10 shrink-0 items-center justify-center gap-1.5 rounded-full border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100">
                    <i data-feather="settings" class="h-4 w-4 shrink-0 text-slate-500"></i>
                    <span>Einstellungen</span>
                </a>

                <div class="inline-flex h-10 shrink-0 items-center justify-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 text-xs font-semibold text-slate-600">
                    <span class="h-2 w-2 shrink-0 rounded-full bg-[#93c21c]"></span>
                    <span>Online</span>
                </div>
            </nav>
        </div>
    </div>
</header>

    <!-- Offline banner -->
    <div id="offlineBanner" class="hidden bg-amber-100 text-amber-900 px-4 py-2 text-sm rounded-md mb-3">
        ⚠️ Keine Internetverbindung. Nachrichten werden gesendet, sobald du wieder online bist.
    </div>

    <!-- MAIN CHAT SHELL -->
    <main class="flex-1 flex items-stretch justify-center">
        <div class="flex w-full h-[calc(100vh-5.5rem)] lg:h-[calc(100vh-7rem)] bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-200">

            <!-- LEFT: Chats/Groups -->
            <aside
                id="chatSidebar"
                class="sidebar-left hidden-mobile fixed inset-y-0 left-0 z-30 w-64 bg-white border-r flex flex-col lg:static lg:flex lg:w-72"
                aria-label="Chatliste"
            >
                <!-- Header / current user -->
                <div class="p-3 flex items-center justify-between border-b" style="background:#e3effb">
                    <div class="flex items-center gap-2 min-w-0">
                        <div class="relative">
                            <img
                                src="{{ asset('images/employee/' . $empImage) }}"
                                class="w-9 h-9 rounded-full object-cover ring-2 ring-emerald-100"
                                alt="Avatar {{ $fullname }}"
                            />
                            <span class="absolute -bottom-0 -right-0 w-3 h-3 rounded-full bg-[#93c21c] border-2 border-white"></span>
                        </div>
                        <div class="min-w-0">
                            <div class="font-semibold truncate max-w-[10rem]" title="{{ $fullname }}">{{ $fullname }}</div>
                            <div class="text-[11px] text-emerald-600 flex items-center gap-1">
                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-[#93c21c] animate-pulse"></span>
                                Aktiv
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                                id="createGroupBtn"
                                class="inline-flex items-center justify-center text-white px-2.5 py-1 rounded-full text-xs shadow-sm active:scale-[0.98] transition"
                                style="background: #93c21c"
                                title="Gruppe erstellen"
                            >

                            <i data-feather="plus" class="w-3 h-3 mr-1"></i>
                            Gruppe
                        </button>
                    </div>
                </div>

                <!-- Search -->
                <div class="p-2 border-b bg-white">
                    <div class="relative">
                        <i data-feather="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input
                            id="searchInput"
                            placeholder="Benutzer & Gruppen suchen…"
                            class="w-full pl-9 pr-3 py-2 text-sm border rounded-full focus:outline-none focus:ring-2 focus:ring-emerald-400/70 focus:border-emerald-400 bg-slate-50"
                        />
                    </div>
                </div>

                <!-- Skeleton while loading -->
                <div id="listSkeleton" class="px-3 py-2 space-y-2 hidden">
                    <div class="animate-pulse h-10 bg-slate-100/80 rounded-xl"></div>
                    <div class="animate-pulse h-10 bg-slate-100/80 rounded-xl"></div>
                    <div class="animate-pulse h-10 bg-slate-100/80 rounded-xl"></div>
                    <div class="animate-pulse h-10 bg-slate-100/80 rounded-xl"></div>
                    <div class="animate-pulse h-10 bg-slate-100/80 rounded-xl"></div>
                </div>

                <!-- Chat list (filled by chat.js) -->
                <ul
                    id="userList"
                    class="divide-y divide-slate-100 px-2 pb-3 overflow-y-auto flex-1 bg-white"
                    aria-live="polite"
                ></ul>
            </aside>

            <!-- MIDDLE: Conversation -->
            <section class="flex-1 flex flex-col" aria-label="Nachrichtenbereich" style="background-color:#f6fafc;">

                <!-- Header -->
                <div class="sticky top-0 z-10 px-4 py-2 border-b bg-white/90 backdrop-blur flex items-center gap-3">
                    <button
                        id="toggleLeft"
                        class="lg:hidden text-xs text-emerald-700 bg-white px-2 py-1 rounded-full shadow-sm border border-emerald-100 flex items-center gap-1"
                        aria-label="Kontakte öffnen"
                    >
                        <i data-feather="users" class="w-3 h-3"></i>
                        Kontakte
                    </button>
                    <h2 id="chatTitle" class="text-base lg:text-lg font-semibold truncate text-slate-800">
                        Wähle einen Kontakt
                    </h2>
                    <div class="ml-auto flex items-center gap-2">
                        <span id="typingIndicator" class="text-xs lg:text-sm text-emerald-500 hidden">schreibt…</span>
                        <button
                            id="toggleRight"
                            class="lg:hidden text-xs text-slate-600 bg-white px-2 py-1 rounded-full shadow-sm border flex items-center gap-1"
                            aria-label="Einstellungen öffnen"
                        >
                            <i data-feather="settings" class="w-3 h-3"></i>
                            Details
                        </button>
                    </div>
                </div>

                <!-- Search in messages -->
                <div class="px-4 py-2 bg-white border-b">
                    <div class="relative h-11">
                        <i data-feather="search" class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input
                            id="messageSearchInput"
                            placeholder="Nachrichten durchsuchen…"
                            class="w-full h-full pl-9 pr-3 text-sm border rounded-full focus:outline-none focus:ring-2 focus:ring-emerald-400/70 focus:border-emerald-400 bg-slate-50"
                        />
                    </div>
                </div>

                <!-- Messages (chat.js fills #chatBox) -->
                <div id="chatScroll" class="flex-1 overflow-y-auto px-4 py-3">
                    <div id="topSentinel" class="sentinel"></div>
                    <div id="chatBox" class="space-y-4"></div>
                    <div id="bottomSentinel" class="sentinel"></div>
                </div>

                <!-- Reply preview -->
                <div id="replyBox" class="hidden px-4 py-2 bg-emerald-50/70 text-xs lg:text-sm text-emerald-800"></div>

                <!-- Recording HUD (voice) -->
                <div
                    id="recHUD"
                    class="hidden px-4 pb-2 bg-slate-50 text-xs lg:text-sm"
                >
                    <div class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white/80 px-3 py-1">
                        <canvas id="waveCanvas" width="200" height="28"></canvas>
                        <span id="recTimer" class="text-xs tabular-nums text-gray-700">00:00</span>
                    </div>
                </div>
 
                <!-- Composer -->
                <div class="sticky bottom-0 z-20 border-t bg-white/95 backdrop-blur px-2 sm:px-4 py-2 sm:py-3">
                    <div class="mx-auto max-w-3xl flex items-end gap-2 sm:gap-3">
                        <!-- Eingabebereich -->
                        <div class="flex-1 relative">
                            <!-- Tools links im Feld -->
                            <div class="absolute left-2 top-1.5 flex items-center gap-1 sm:gap-2">
                                <button
                                    id="emojiBtn"
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-100"
                                    title="Emoji"
                                    type="button"
                                >
                                    😊
                                </button>
                                <button
                                    id="attachBtn"
                                    class="w-8 h-8 rounded-full flex items-center justify-center text-slate-500 hover:bg-slate-100"
                                    title="Datei anhängen"
                                    type="button"
                                >
                                    <i data-feather="paperclip" class="w-4 h-4"></i>
                                </button>
                                <button
                                    id="voiceBtn"
                                    class="hidden xs:flex sm:flex w-8 h-8 rounded-full items-center justify-center text-slate-500 hover:bg-slate-100"
                                    title="Sprachnachricht"
                                    type="button"
                                >
                                    <i data-feather="mic" class="w-4 h-4"></i>
                                </button>
                            </div>

                            <input id="fileInput" type="file" class="hidden" multiple>

                            <!-- Textarea wie bei ChatGPT: kompakt, auto-grow, mobil kleiner -->
                            <textarea
                                id="messageInput"
                                rows="1"
                                placeholder="Nachricht eingeben…"
                                class="w-full text-sm border rounded-2xl bg-slate-50 focus:outline-none focus:ring-2 focus:ring-[#93c21c] focus:border-[#93c21c]
                                    resize-none
                                    pl-2 sm:pl-3
                                    pr-2 sm:pr-3
                                    pt-10 pb-2
                                    min-h-[44px] sm:min-h-[60px]
                                    max-h-[180px] sm:max-h-[220px]"
                            ></textarea>

                            <!-- Emoji-Popover relativ zur Eingabe -->
                            <div
                                id="emojiList"
                                class="emoji-list hidden left-0 bottom-full mb-2"
                                role="dialog"
                                aria-label="Emoji-Auswahl"
                            >
                                <span>😀</span><span>😁</span><span>😂</span><span>🤣</span>
                                <span>😎</span><span>😍</span><span>😢</span><span>😭</span>
                                <span>😡</span><span>👍</span><span>👎</span><span>🙏</span>
                            </div>
                        </div>

                        <!-- Senden-Button getrennt, wie bei ChatGPT -->
                        <button
                            id="sendButton"
                            type="button"
                            class="shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-full bg-[#93c21c] hover:bg-emerald-600 text-white flex items-center justify-center shadow-md"
                            title="Senden"
                        >
                            <i data-feather="send" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

            </section>

            <!-- RIGHT: Details / Settings -->
            <aside
                id="rightSidebar"
                class="sidebar-right hidden-mobile fixed inset-y-0 right-0 z-30 w-72 bg-white border-l flex flex-col lg:static lg:flex"
                aria-label="Chat Einstellungen"
            >
                <div class="p-3 border-b bg-gradient-to-l from-slate-50 to-slate-100">
                    <h3 class="text-base lg:text-lg font-semibold flex items-center gap-2 text-slate-800">
                        <i data-feather="info" class="w-4 h-4 text-emerald-500"></i>
                        Chat Einstellungen
                    </h3>
                </div>

                <div class="p-3 border-b" style="background: linear-gradient(90deg,#74b2d4,#c0d8ea);">
                    <div class="flex items-center gap-3">
                        <div id="activeChatAvatar"
                            class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-sm font-semibold text-slate-700 overflow-hidden">
                            <!-- filled by chat.js -->
                        </div>
                        <div class="min-w-0">
                            <h2 id="activeChatName" class="text-lg font-semibold truncate text-slate-900">Chat Name</h2>
                            <p id="activeChatMeta" class="text-sm text-gray-800 truncate">Chat Info</p>
                        </div>

                         <div id="activeChatContext"
                            class="px-4 py-3 border-b text-xs text-slate-700 space-y-1 hidden">
                        </div>
                    </div>
                </div>


                <!-- Toggles -->
                <div class="px-4 py-3 border-b space-y-3 text-sm">
                    <!-- 📌 Pin button -->
                    <button
                        id="pinChatBtn"
                        class="w-full px-3 py-2 rounded-md border bg-white flex items-center justify-between text-sm font-medium text-slate-700 hover:bg-amber-50 hover:border-amber-300 hidden"
                        type="button"
                    >
                        <span>Anpinnen</span>
                        <i data-feather="bookmark" class="w-4 h-4"></i>
                    </button>

                    <label class="flex justify-between items-center gap-2">
                        <span class="font-medium text-slate-700">Sound</span>
                        <input id="soundToggle" type="checkbox" class="h-4 w-4" style="accent-color:#93c21c;" checked>
                    </label>
                    <label class="flex justify-between items-center gap-2">
                        <span class="font-medium text-slate-700">Auto-Löschen</span>
                        <input id="autoDeleteToggle" type="checkbox" class="h-4 w-4" style="accent-color:#cf309b;">
                    </label>
                    <label class="flex justify-between items-center gap-2">
                        <span class="font-medium text-slate-700">Dark Mode</span>
                        <input id="darkModeToggle" type="checkbox" class="h-4 w-4" style="accent-color:#74b2d4;">
                    </label>
                </div>


                <!-- Members, files, group actions -->
                <div class="p-4 flex-1 overflow-y-auto space-y-4 text-sm">
                    <div>
                        <h4 class="font-semibold mb-2 text-slate-700">Mitglieder</h4>
                        <ul id="groupMembers" class="space-y-2 text-gray-700"></ul>
                        <button
                            id="addMemberBtn"
                            class="mt-3 px-3 py-1 text-xs bg-[#93c21c] text-white rounded-full hover:bg-emerald-600"
                        >
                            ➕ Mitglied hinzufügen
                        </button>
                    </div>

                    <div id="groupActions" class="space-y-2 hidden">
                        <h4 class="text-sm font-semibold mb-2 text-slate-700">Gruppenaktionen</h4>

                        <button
                            id="renameGroupBtn"
                            class="w-full px-3 py-2 text-sm border rounded-md flex items-center justify-between hover:bg-gray-50"
                            type="button"
                        >
                            <span>Gruppe bearbeiten</span>
                            <i data-feather="edit-3" class="w-4 h-4"></i>
                        </button>

                        <button
                            id="leaveGroupBtn"
                            class="w-full px-3 py-2 text-sm border rounded-md flex items-center justify-between hover:bg-amber-50 text-amber-700 border-amber-300"
                            type="button"
                        >
                            <span>Gruppe verlassen</span>
                            <i data-feather="log-out" class="w-4 h-4"></i>
                        </button>

                        <button
                            id="deleteGroupBtn"
                            class="w-full px-3 py-2 text-sm border rounded-md flex items-center justify-between hover:bg-red-50 text-red-600 border-red-300"
                            type="button"
                        >
                            <span>Gruppe löschen</span>
                            <i data-feather="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div> 
                     
                </div>
            </aside>
        </div>
    </main>

<div id="chatDropOverlay" class="chat-drop-overlay">
        <div class="bg-white/95 border-2 border-dashed border-emerald-400/80 rounded-2xl px-6 py-8 max-w-md w-[90%] text-center shadow-2xl" style="pointer-events: none;">
        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50">
            <i data-feather="upload-cloud" class="w-6 h-6 text-emerald-500"></i>
        </div>
        <p class="font-medium text-slate-800 text-sm sm:text-base">
            Datei hier ablegen, um sie zu senden
        </p>
        <p class="mt-1 text-xs sm:text-sm text-slate-500">
            Bilder, PDFs und andere Dateien werden als Anhang in den aktuellen Chat hochgeladen.
        </p>
    </div>
</div>


</div>

<!-- Toast -->
<div id="toast" class="fixed bottom-4 right-4 bg-black text-white px-4 py-2 rounded shadow-lg opacity-0 transition-opacity duration-300 z-50"></div>

<!-- Group Modal -->
<!-- Group Modal -->
<div id="groupModal" class="fixed inset-0 bg-black/40 z-50 hidden items-center justify-center" role="dialog" aria-modal="true" aria-label="Gruppe erstellen oder bearbeiten">
    <div class="bg-white p-6 rounded w-full max-w-lg shadow space-y-4">
        <h2 class="text-lg font-bold text-[#374151]">Gruppe erstellen / bearbeiten</h2>

        <!-- Group name -->
        <input id="groupName" type="text" placeholder="Gruppenname"
               class="w-full border rounded px-3 py-2 focus:ring-2 focus:ring-[#74b2d4] focus:border-[#74b2d4]" />

        <!-- Customer selector -->
        <div class="space-y-1">
           <label for="groupCustomerSearch" class="text-sm text-gray-700">
                Kunde / Adresse / Produkt
            </label>

            <select id="groupCustomerSearch"
                    class="w-full border rounded px-3 py-2 text-sm"
                    style="width: 100%;"></select>

            <input type="hidden" id="groupCustomerId">
            <input type="hidden" id="groupAlternativeId">
            <input type="hidden" id="groupProductId">
            <input type="hidden" id="leadProductListId">

            <p id="groupCustomerLabel" class="text-xs text-gray-500 mt-1"></p>

        </div>

        <!-- Avatar dropzone (unchanged) -->
        <div id="dropzone" class="border-dashed border-2 rounded px-3 py-6 text-center cursor-pointer bg-[#c0d8ea]/40">
            <p class="text-sm text-gray-600">Avatar hier ablegen oder klicken</p>
            <input type="hidden" id="groupAvatarPath" />
        </div>

        <div id="userCheckboxList" class="max-h-64 overflow-y-auto border rounded p-3 text-sm space-y-2 bg-[#f9fafb]"></div>

        <div class="flex justify-end gap-2 pt-4">
            <button id="cancelGroupBtn" class="px-4 py-2 border rounded text-sm text-gray-700 hover:bg-[#c0d8ea]/50">
                Abbrechen
            </button>
            <button id="submitGroupBtn"
                    class="px-4 py-2 rounded text-sm font-medium text-white"
                    style="background: linear-gradient(90deg,#93c21c,#cf309b);">
                Speichern
            </button>
        </div>
    </div>
</div>

<!-- Add members modal -->
<div id="addMemberModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 hidden" role="dialog" aria-modal="true" aria-label="Mitglieder hinzufügen">
    <div class="bg-white rounded-lg shadow p-4 w-full max-w-md">
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-lg font-bold">Mitglieder hinzufügen</h3>
            <button onclick="document.getElementById('addMemberModal').classList.add('hidden')" aria-label="Schließen">✖</button>
        </div>

        <!-- Benutzer-Auswahl -->
        <div class="mb-3">
            <label for="addUserSelect" class="text-sm mb-1 block">Benutzer auswählen:</label>
            <select id="addUserSelect" class="w-full border rounded p-1 select2" multiple="multiple" style="width: 100%"></select>
        </div>

        <!-- Globale Einstellungen für ALLE neu hinzugefügten Mitglieder -->
        <div class="mb-3 space-y-2 text-sm">
            <div>
                <label for="addMemberRole" class="block mb-1">Rolle für neue Mitglieder</label>
                <select id="addMemberRole" class="w-full border rounded px-2 py-1">
                    <option value="member">👤 Mitglied</option>
                    <option value="moderator">🛡 Moderator</option>
                    <option value="admin">⭐ Admin</option>
                </select>
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" id="addMemberHistoryFromJoin" class="h-4 w-4" checked>
                <span>Nur neue Nachrichten ab jetzt sehen (keine Historie)</span>
            </label>

            <label class="flex items-center gap-2">
                <input type="checkbox" id="addMemberCanWrite" class="h-4 w-4" checked>
                <span>Darf schreiben</span>
            </label>
        </div>

        <div class="text-right mt-2">
            <button class="bg-blue-500 text-white px-4 py-1 text-sm rounded" onclick="submitAddMembers()">Hinzufügen</button>
        </div>
    </div>

</div>

<!-- Share message modal -->
<div id="shareMessageModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50"
     role="dialog"
     aria-modal="true"
     aria-label="Nachricht teilen">
    <div class="bg-white rounded-lg shadow p-4 w-full max-w-md">
        <div class="flex justify-between items-center mb-3">
            <h3 class="text-lg font-bold text-slate-800">Nachricht teilen</h3>
            <button id="shareCancelBtn" type="button"
                    class="text-slate-500 hover:text-slate-700">
                ✖
            </button>
        </div>

        <label class="text-xs font-medium text-slate-600 mb-1 block">
            Nachricht
        </label>
        <textarea id="shareMessagePreview"
                  class="w-full border rounded-md px-2 py-1 text-sm mb-3 bg-slate-50"
                  rows="4"
                  readonly></textarea>

        <label class="text-xs font-medium text-slate-600 mb-1 block">
            Empfänger auswählen
        </label>
        <select id="shareTargetSelect"
                class="w-full border rounded px-2 py-1 text-sm select2"
                multiple="multiple" style="width:100%"></select>

        <p class="mt-2 text-[11px] text-slate-500">
            Die Nachricht wird als neue Chat-Nachricht an die ausgewählten Mitarbeiter gesendet.
        </p>

        <div class="text-right mt-4 flex justify-end gap-2">
            <button type="button"
                    id="shareCancelBtnFooter"
                    class="px-3 py-1.5 text-xs border rounded-md text-slate-600 hover:bg-slate-100">
                Abbrechen
            </button>
            <button type="button"
                    id="shareSendBtn"
                    class="px-3 py-1.5 text-xs rounded-md text-white"
                    style="background: linear-gradient(90deg,#93c21c,#74b2d4);">
                Senden
            </button>
        </div>
    </div>
</div>


<!-- Drag & Drop overlay for file upload -->

 

<!-- Chat Media / PDF Preview Modal -->
<div id="chatMediaModal"
     class="chat-media-modal hidden"
     role="dialog"
     aria-modal="true"
     aria-label="Dateivorschau">
    <div class="chat-media-backdrop" data-chat-media-close></div>

    <div class="chat-media-shell">
        <div class="chat-media-header">
            <div class="min-w-0">
                <div id="chatMediaTitle" class="chat-media-title">Vorschau</div>
                <div id="chatMediaCounter" class="chat-media-counter"></div>
            </div>

            <div class="chat-media-actions">
                <a id="chatMediaDownload"
                   href="#"
                   target="_blank"
                   rel="noopener"
                   class="chat-media-icon-btn"
                   title="In neuem Tab öffnen">
                    <i data-feather="external-link" class="w-5 h-5"></i>
                </a>

                <button type="button"
                        class="chat-media-icon-btn"
                        data-chat-media-close
                        title="Schließen">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <button type="button"
                id="chatMediaPrev"
                class="chat-media-nav chat-media-prev"
                title="Zurück">
            <i data-feather="chevron-left" class="w-7 h-7"></i>
        </button>

        <div id="chatMediaBody" class="chat-media-body"></div>

        <button type="button"
                id="chatMediaNext"
                class="chat-media-nav chat-media-next"
                title="Weiter">
            <i data-feather="chevron-right" class="w-7 h-7"></i>
        </button>
    </div>
</div>

<!-- Notification Sound -->
<audio id="notificationSound" src="{{ asset('notification/notification.mp3') }}" preload="auto"></audio>
@vite(['resources/js/app.js'])
<script>
    (function () {
        // Feather icons
        document.addEventListener('DOMContentLoaded', function () {
            if (window.feather) {
                window.feather.replace();
            }
        });

        // Mobile sidebars (independent from chat.js)
        const leftSidebar  = document.getElementById('chatSidebar');
        const rightSidebar = document.getElementById('rightSidebar');
        const toggleLeft   = document.getElementById('toggleLeft');
        const toggleRight  = document.getElementById('toggleRight');

        function toggleSidebar(sidebar) {
            if (!sidebar) return;
            sidebar.classList.toggle('hidden-mobile');
        }

        if (toggleLeft) {
            toggleLeft.addEventListener('click', function () {
                toggleSidebar(leftSidebar);
            });
        }

        if (toggleRight) {
            toggleRight.addEventListener('click', function () {
                toggleSidebar(rightSidebar);
            });
        }

        // Close sidebars when clicking outside on mobile
        document.addEventListener('click', function (e) {
            if (window.innerWidth >= 1024) return;

            if (
                leftSidebar &&
                !leftSidebar.contains(e.target) &&
                (!toggleLeft || !toggleLeft.contains(e.target))
            ) {
                leftSidebar.classList.add('hidden-mobile');
            }

            if (
                rightSidebar &&
                !rightSidebar.contains(e.target) &&
                (!toggleRight || !toggleRight.contains(e.target))
            ) {
                rightSidebar.classList.add('hidden-mobile');
            }
        });

        // Global vars chat.js expects
        window.userId            = {{ auth()->user()->id }};
        window.userName          = @json($fullname);
        window.userImage         = @json(asset('images/employee/' . $empImage));
        window.csrfToken         = @json(csrf_token());
        window.employeeLocation  = @json(asset('images/employee'));
        window.defaultPic        = @json(asset('images/gender/users.png'));
        window.assetImages       = @json(asset('images'));
        window.notificationSound = @json(asset('notification/notification.mp3'));

         
    })();
</script>


 

</body>
</html>
