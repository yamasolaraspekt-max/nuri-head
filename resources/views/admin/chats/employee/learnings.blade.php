@extends('admin.layouts.app')

@section('title') Learning Chat Topics @endsection

@section('style')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Quill --}}
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">

{{-- Select2 --}}
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">

<style>
    :root {
        --learning-green: #93c21c;
        --learning-blue: #74b2d4;
        --learning-bg: #f6f7fb;
        --learning-border: rgba(15, 23, 42, .08);
        --learning-muted: #6b7280;
    }

    body {
        background: var(--learning-bg) !important;
    }

    .learning-page {
        padding-top: .5rem;
    }

    .learning-shell {
        background: #fff;
        border-radius: 18px;
        border: 1px solid var(--learning-border);
        box-shadow: 0 12px 30px rgba(15,23,42,.06);
        padding: 18px;
    }

    .learning-shell-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
        gap: 12px;
    }

    .learning-shell-title {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .learning-shell-title-icon {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(147,194,28,.12), rgba(116,178,212,.14));
    }

    .learning-shell-title h4 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .learning-shell-subtitle {
        color: var(--learning-muted);
        font-size: 12px;
    }

    .learning-shell-actions .btn-icon-soft {
        border-radius: 999px;
        border: 1px solid rgba(148,163,184,.4);
        padding: 5px 10px;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f9fafb;
    }

    .learning-shell-actions .btn-icon-soft i {
        font-size: 13px;
    }

    .learning-list-panel {
        border-right: 1px solid rgba(148,163,184,.25);
    }

    .learning-list-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 8px;
    }

    .learning-list-toolbar h5 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .learning-list-toolbar h5 i {
        font-size: 16px;
        color: var(--learning-blue);
    }

    .btn-learning-new {
        background: var(--learning-green);
        color: #fff;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: none;
    }

    .btn-learning-new i {
        font-size: 13px;
    }

    .learning-list-item {
        border-radius: 12px;
        padding: 8px 10px;
        cursor: pointer;
        border: 1px solid transparent;
        transition: background .15s, border-color .15s, transform .1s, box-shadow .1s;
    }

    .learning-list-item:hover {
        background: #f1f5f9;
        border-color: rgba(148,163,184,.6);
        transform: translateY(-1px);
        box-shadow: 0 6px 12px rgba(15,23,42,.06);
    }

    .learning-list-item.active {
        background: linear-gradient(135deg, #e3f7c5, #cfe09b);
        border-color: var(--learning-green);
        box-shadow: 0 8px 18px rgba(22,163,74,.18);
    }

    .learning-label {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 999px;
        background: rgba(148,163,184,.12);
        color: #4b5563;
    }

    .learning-label-active {
        background: rgba(22,163,74,.12);
        color: #15803d;
    }

    #learningBodyEditor {
        min-height: 240px;
        border-radius: 14px;
        background: #fff;
    }

    .ql-toolbar.ql-snow {
        border-radius: 10px 10px 0 0;
    }

    .ql-container.ql-snow {
        border-radius: 0 0 10px 10px;
    }

    .learning-media-card {
        border-radius: 12px;
        border: 1px solid rgba(148,163,184,.4);
        padding: 8px 10px;
        background: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 6px;
    }

    .learning-media-icon {
        width: 32px;
        height: 32px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(148,163,184,.15);
    }

    .learning-header-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        background: #e3effb;
        border: 1px solid rgba(148,163,184,.4);
        font-size: 11px;
        color: #1f2937;
    }

    .learning-header-pill i {
        font-size: 14px;
        color: var(--learning-blue);
    }

    .learning-meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-top: 4px;
    }

    .learning-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 2px 8px;
        border-radius: 999px;
        border: 1px dashed rgba(148,163,184,.4);
        font-size: 10px;
        color: #4b5563;
        background: #f9fafb;
    }

    .learning-meta-pill i {
        font-size: 11px;
    }

    .learning-form-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }

    .learning-form-title {
        font-size: 15px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .learning-form-title i {
        font-size: 16px;
        color: var(--learning-green);
    }

    .learning-active-toggle {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid rgba(148,163,184,.4);
        background: #f9fafb;
    }

    .learning-active-toggle input {
        margin: 0;
    }

    .learning-actions-footer button {
        border-radius: 999px !important;
    }

    .learning-actions-footer .btn-outline-danger {
        border-width: 1px;
    }

    .learning-actions-footer .btn-save {
        background: linear-gradient(90deg, var(--learning-green), var(--learning-blue));
        color: #fff;
        border: none;
    }

    .select2-container--default .select2-selection--multiple {
        border-radius: 999px;
        min-height: 34px;
        border-color: rgba(148,163,184,.7);
    }

    .select2-container--default .select2-selection--multiple .select2-selection__rendered {
        padding: 2px 8px;
    }

    @media (max-width: 991.98px) {
        .learning-list-panel {
            border-right: none;
            border-bottom: 1px solid rgba(148,163,184,.25);
            margin-bottom: 10px;
            padding-bottom: 10px;
        }
    }

    /* Quill content media */
    #learningBodyEditor .ql-editor img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 8px 0;
    }

    #learningBodyEditor .ql-editor .ql-video {
        max-width: 100%;
        display: block;
        margin: 8px 0;
    }

</style>
@endsection

@section('content')
<div class="app-content content">
    <div class="content-wrapper">
        {{-- HEADER --}}
        <div class="content-header row">
            <div class="content-header-left col-md-8 col-12 mb-1">
                <h2 class="content-header-title float-left mb-0 d-flex align-items-center">
                    <i class="feather icon-book-open mr-50"></i>
                    Learning Chat Topics
                </h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/employee_dashboard') }}">
                                <i class="feather icon-home"></i> Dashboard
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ url('/chat_dashboard') }}">
                                <i class="feather icon-message-circle"></i> Chat
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            Learning topics
                        </li>
                    </ol>
                </div>
            </div>

            <div class="content-header-right col-md-4 col-12 d-flex align-items-center justify-content-md-end justify-content-start mb-1">
                <span class="learning-header-pill">
                    <i class="feather icon-layers"></i>
                    Curated guides to train employees inside chat.
                </span>
            </div>
        </div>

        {{-- BODY --}}
        <div class="content-body mt-1">
            <div class="learning-page">
                <div class="learning-shell">
                    {{-- Shell header --}}
                    <div class="learning-shell-header">
                        <div class="learning-shell-title">
                            <div class="learning-shell-title-icon">
                                <i class="feather icon-compass"></i>
                            </div>
                            <div>
                                <h4>Learning library for chat prompts</h4>
                                <div class="learning-shell-subtitle">
                                    Create reusable “how-to” topics and assign them to departments, positions, or specific employees.
                                </div>
                            </div>
                        </div>
                        <div class="learning-shell-actions">
                            <button type="button" class="btn-icon-soft" id="btnNewTopicHeader">
                                <i class="feather icon-file-plus"></i>
                                New topic
                            </button>
                        </div>
                    </div>

                    <hr class="mb-2 mt-2">

                    <div class="row">
                        {{-- LEFT PANEL: Topics list --}}
                        <div class="col-lg-4 learning-list-panel">
                            <div class="learning-list-toolbar">
                                <h5>
                                    <i class="feather icon-list"></i>
                                    Topics
                                </h5>
                                <button id="btnNewTopic"
                                        class="btn-learning-new">
                                    <i class="feather icon-plus"></i>
                                    New
                                </button>
                            </div>

                            <div class="mb-2">
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="feather icon-search"></i>
                                        </span>
                                    </div>
                                    <input type="text"
                                           id="searchTopics"
                                           class="form-control"
                                           placeholder="Search topics, labels, intro…">
                                </div>
                            </div>

                            <div id="learningTopicList" style="max-height: 600px; overflow-y: auto;">
                                <div class="text-muted small pt-2 d-flex align-items-center">
                                    <i class="feather icon-info mr-50"></i>
                                    No topics yet. Create your first guide on the right side.
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT PANEL: Editor --}}
                        <div class="col-lg-8">
                            <form id="learningForm" onsubmit="return false;">
                                <input type="hidden" id="learningId">

                                <div class="learning-form-header">
                                    <div class="learning-form-title">
                                        <i class="feather icon-edit-3"></i>
                                        <span id="learningFormTitle">Create new topic</span>
                                    </div>

                                    <div class="learning-active-toggle">
                                        <i class="feather icon-power"></i>
                                        <span>Active</span>
                                        <input type="checkbox" id="learningActive" checked>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-group mb-1">
                                            <label class="small mb-1">
                                                <i class="feather icon-type mr-25"></i> Title
                                            </label>
                                            <input type="text"
                                                   id="learningTitle"
                                                   class="form-control form-control-sm"
                                                   placeholder="How to create a new customer">
                                        </div>

                                        <div class="form-group mb-1">
                                            <label class="small mb-1">
                                                <i class="feather icon-tag mr-25"></i> Prompt label (chip text in chat)
                                            </label>
                                            <input type="text"
                                                   id="learningPromptLabel"
                                                   class="form-control form-control-sm"
                                                   placeholder="How to create customer">
                                        </div>

                                        <div class="form-group mb-1">
                                            <label class="small mb-1">
                                                <i class="feather icon-align-left mr-25"></i> Short intro
                                            </label>
                                            <input type="text"
                                                   id="learningShortIntro"
                                                   class="form-control form-control-sm"
                                                   placeholder="Step-by-step guide to create a customer inside SA-DESK.">
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="form-group mb-1">
                                            <label class="small mb-1">
                                                <i class="feather icon-activity mr-25"></i> Difficulty
                                            </label>
                                            <select id="learningDifficulty" class="form-control form-control-sm">
                                                <option value="beginner">Beginner</option>
                                                <option value="intermediate">Intermediate</option>
                                                <option value="advanced">Advanced</option>
                                            </select>
                                        </div>

                                        <div class="form-group mb-1">
                                            <label class="small mb-1">
                                                <i class="feather icon-clock mr-25"></i> Estimated duration (minutes)
                                            </label>
                                            <input type="number"
                                                   id="learningMinutes"
                                                   class="form-control form-control-sm"
                                                   min="1" max="480"
                                                   placeholder="e.g. 5">
                                        </div>

                                        <div class="form-group mb-1">
                                            <label class="small mb-1">
                                                <i class="feather icon-users mr-25"></i> Audience scope
                                            </label>
                                            <select id="learningAudienceScope" class="form-control form-control-sm">
                                                <option value="all">All employees</option>
                                                <option value="department">Departments</option>
                                                <option value="position">Positions</option>
                                                <option value="employee">Specific employees</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                {{-- Assignments --}}
                                <div class="row mt-1">
                                    <div class="col-md-4">
                                        <label class="small mb-1">
                                            <i class="feather icon-briefcase mr-25"></i> Departments
                                        </label>
                                        <select id="learningDepartments"
                                                class="form-control form-control-sm select2-learning"
                                                multiple="multiple" style="width: 100%;">
                                            @foreach($departments as $dep)
                                                <option value="{{ $dep->id }}">{{ $dep->department_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small mb-1">
                                            <i class="feather icon-award mr-25"></i> Positions
                                        </label>
                                        <select id="learningPositions"
                                                class="form-control form-control-sm select2-learning"
                                                multiple="multiple" style="width: 100%;">
                                            @foreach($positions as $pos)
                                                <option value="{{ $pos->id }}">{{ $pos->position }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="small mb-1">
                                            <i class="feather icon-user-check mr-25"></i> Employees
                                        </label>
                                        <select id="learningEmployees"
                                                class="form-control form-control-sm select2-learning"
                                                multiple="multiple" style="width: 100%;">
                                            @foreach($employees as $emp)
                                                <option value="{{ $emp->id }}">
                                                    {{ $emp->name }} {{ $emp->lastname }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="learning-meta-row mt-1 mb-1">
                                    <div class="learning-meta-pill">
                                        <i class="feather icon-sliders"></i>
                                        Used for smart chat suggestions
                                    </div>
                                    <div class="learning-meta-pill">
                                        <i class="feather icon-shield"></i>
                                        Scope controlled via audience settings
                                    </div>
                                </div>

                                <hr>

                                {{-- Quill editor --}}
                                <div class="form-group">
                                    <label class="small mb-1">
                                        <i class="feather icon-file-text mr-25"></i> Content (rich training article)
                                    </label>
                                    <div id="learningBodyEditor"></div>
                                    <input type="hidden" id="learningBody">
                                </div>

                                {{-- Media --}}
                                <div class="form-group">
                                    <label class="small mb-1">
                                        <i class="feather icon-image mr-25"></i> Media (images, video, audio, files)
                                    </label>

                                    <div class="d-flex align-items-center mb-2">
                                        <div class="custom-file" style="max-width: 260px;">
                                            <input type="file" class="custom-file-input" id="mediaFile">
                                            <label class="custom-file-label" for="mediaFile">Choose file</label>
                                        </div>
                                        <button type="button"
                                                id="btnUploadMedia"
                                                class="btn btn-sm btn-outline-secondary ml-2">
                                            <i class="feather icon-upload"></i> Upload
                                        </button>
                                    </div>

                                    <div id="learningMediaList" class="mb-2">
                                        <small class="text-muted d-flex align-items-center">
                                            <i class="feather icon-info mr-50"></i>
                                            No media attached yet.
                                        </small>
                                    </div>
                                </div>

                                <div class="learning-actions-footer text-right mt-3">
                                    <button type="button" id="btnDeleteTopic"
                                            class="btn btn-outline-danger btn-sm mr-2" style="display:none;">
                                        <i class="feather icon-trash-2"></i> Delete
                                    </button>
                                    <button type="button" id="btnSaveTopic"
                                            class="btn btn-sm btn-save">
                                        <i class="feather icon-save"></i> Save topic
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div> {{-- .learning-shell --}}
            </div> {{-- .learning-page --}}
        </div> {{-- .content-body --}}
    </div> {{-- .content-wrapper --}}
</div> {{-- .app-content --}}
@endsection

@section('script')
<script src="{{ asset('js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('js/select2.min.js') }}"></script>
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/quill-image-resize-module@3.0.0/image-resize.min.js"></script>

<script>
    (function () {
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const routes = {
            list: "{{ route('admin.chat.learnings.list') }}",
            store: "{{ route('admin.chat.learnings.store') }}",
            show: "{{ route('admin.chat.learnings.show', ['topic' => '__ID__']) }}",
            delete: "{{ route('admin.chat.learnings.destroy', ['topic' => '__ID__']) }}",
            uploadMedia: "{{ route('admin.chat.learnings.media.upload', ['topic' => '__ID__']) }}",
            deleteMedia: "{{ route('admin.chat.learnings.media.destroy', ['media' => '__ID__']) }}"
        };

        let quill;
        let topics = [];
        let currentTopicId = null;

        function initSelect2() {
            $('.select2-learning').select2({
                placeholder: 'Select…',
                width: '100%',
                allowClear: true
            });
        }

       function initQuill() {
            // Try to detect the correct ImageResize constructor
            let ImageResizeModule = null;

            if (window.ImageResize || window.quillImageResizeModule) {
                const raw = window.ImageResize || window.quillImageResizeModule;
                ImageResizeModule = raw.default || raw; // handle UMD / ESM builds

                if (typeof ImageResizeModule === 'function') {
                    Quill.register('modules/imageResize', ImageResizeModule);
                } else {
                    // If it's not a constructor, do NOT register it, to avoid "e is not a constructor"
                    ImageResizeModule = null;
                }
            }

            const toolbarOptions = [
                [{ header: [1, 2, 3, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ color: [] }, { background: [] }],
                [{ list: 'ordered' }, { list: 'bullet' }],
                [{ align: [] }],
                ['link', 'image', 'video', 'blockquote', 'code-block'],
                ['clean']
            ];

            const modulesConfig = {
                toolbar: {
                    container: toolbarOptions,
                    handlers: {
                        // Custom video handler for YouTube and other URLs
                        video: function () {
                            const range = this.quill.getSelection(true);
                            const url = prompt('Paste YouTube or video URL:');

                            if (!url) return;

                            let embedUrl = url.trim();

                            try {
                                if (embedUrl.includes('youtube.com/watch')) {
                                    const u = new URL(embedUrl);
                                    const v = u.searchParams.get('v');
                                    if (v) {
                                        embedUrl = 'https://www.youtube.com/embed/' + v;
                                    }
                                } else if (embedUrl.includes('youtu.be/')) {
                                    const parts = embedUrl.split('youtu.be/')[1].split(/[?&]/);
                                    const v = parts[0];
                                    if (v) {
                                        embedUrl = 'https://www.youtube.com/embed/' + v;
                                    }
                                }
                            } catch (e) {
                                // If URL parsing fails, keep original url
                            }

                            this.quill.insertEmbed(range.index, 'video', embedUrl, Quill.sources.USER);
                            this.quill.setSelection(range.index + 1, Quill.sources.SILENT);
                        }
                    }
                }
            };

            // Only enable imageResize module if we have a valid constructor
            if (ImageResizeModule) {
                modulesConfig.imageResize = {
                    modules: ['Resize', 'DisplaySize', 'Toolbar']
                };
            }

            quill = new Quill('#learningBodyEditor', {
                theme: 'snow',
                placeholder: 'Write the training article here…',
                modules: modulesConfig
            });
        }


        function syncQuillToHidden() {
            const html = quill.root.innerHTML;
            $('#learningBody').val(html === '<p><br></p>' ? '' : html);
        }

        function difficultyLabel(d) {
            if (d === 'advanced') {
                return '<span class="learning-label learning-label-active">Advanced</span>';
            }
            if (d === 'intermediate') {
                return '<span class="learning-label">Intermediate</span>';
            }
            return '<span class="learning-label">Beginner</span>';
        }

        function minutesLabel(m) {
            if (!m) return '';
            return '<span class="learning-label ml-1"><i class="feather icon-clock" style="font-size: 10px;"></i> ' + m + ' min</span>';
        }

        function renderTopicList(filtered) {
            const list = $('#learningTopicList');
            list.empty();

            if (!filtered || !filtered.length) {
                list.html(
                    '<div class="text-muted small pt-2 d-flex align-items-center">' +
                        '<i class="feather icon-info mr-50"></i>' +
                        'No topics yet. Create your first guide on the right side.' +
                    '</div>'
                );
                return;
            }

            filtered.forEach(function (t) {
                const item = $('<div class="learning-list-item"></div>');
                item.attr('data-id', t.id);

                if (t.id === currentTopicId) {
                    item.addClass('active');
                }

                const activeBadge = t.is_active
                    ? '<span class="learning-label learning-label-active mr-1">Active</span>'
                    : '<span class="learning-label mr-1">Inactive</span>';

                const updatedAt = t.updated_at
                    ? new Date(t.updated_at).toLocaleString()
                    : '';

                item.html(
                    '<div class="d-flex justify-content-between align-items-start">' +
                        '<div>' +
                            '<div class="font-weight-semibold" style="font-size: 13px;">' + (t.title || '') + '</div>' +
                            '<div class="text-muted small">' +
                                activeBadge +
                                difficultyLabel(t.difficulty) +
                                minutesLabel(t.estimated_minutes) +
                            '</div>' +
                        '</div>' +
                        '<div class="text-right">' +
                            (updatedAt
                                ? '<div class="text-muted" style="font-size: 10px;">' +
                                    '<i class="feather icon-clock mr-25"></i>' + updatedAt +
                                  '</div>'
                                : ''
                            ) +
                        '</div>' +
                    '</div>' +
                    (t.prompt_label
                        ? '<div class="text-muted small mt-1">' +
                            '<i class="feather icon-tag" style="font-size: 11px;"></i> ' + t.prompt_label +
                          '</div>'
                        : ''
                    )
                );

                item.on('click', function () {
                    loadTopic(t.id);
                });

                list.append(item);
            });

            if (window.feather) {
                feather.replace();
            }
        }

        function fetchTopics() {
            $.get(routes.list, function (res) {
                topics = res || [];
                applyTopicFilter();
            }).fail(function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Could not load topics.'
                });
            });
        }

        function applyTopicFilter() {
            const q = ($('#searchTopics').val() || '').toLowerCase();
            if (!q) {
                renderTopicList(topics);
                return;
            }

            const filtered = topics.filter(function (t) {
                const hay = [
                    t.title || '',
                    t.prompt_label || '',
                    t.short_intro || ''
                ].join(' ').toLowerCase();
                return hay.indexOf(q) !== -1;
            });

            renderTopicList(filtered);
        }

        function resetForm() {
            currentTopicId = null;
            $('#learningId').val('');
            $('#learningFormTitle').text('Create new topic');
            $('#learningTitle').val('');
            $('#learningPromptLabel').val('');
            $('#learningShortIntro').val('');
            $('#learningDifficulty').val('beginner');
            $('#learningMinutes').val('');
            $('#learningAudienceScope').val('all');
            $('#learningActive').prop('checked', true);

            $('#learningDepartments').val(null).trigger('change');
            $('#learningPositions').val(null).trigger('change');
            $('#learningEmployees').val(null).trigger('change');

            quill.setContents([]);
            $('#learningBody').val('');

            $('#learningMediaList').html(
                '<small class="text-muted d-flex align-items-center">' +
                    '<i class="feather icon-info mr-50"></i>' +
                    'No media attached yet.' +
                '</small>'
            );

            $('#btnDeleteTopic').hide();
            $('.learning-list-item').removeClass('active');
        }

        function highlightActiveTopic(id) {
            $('.learning-list-item').removeClass('active');
            if (!id) return;
            $('.learning-list-item[data-id="' + id + '"]').addClass('active');
        }

        function renderMediaList(mediaList) {
            const container = $('#learningMediaList');
            container.empty();

            if (!mediaList || !mediaList.length) {
                container.html(
                    '<small class="text-muted d-flex align-items-center">' +
                        '<i class="feather icon-info mr-50"></i>' +
                        'No media attached yet.' +
                    '</small>'
                );
                return;
            }

            $('#learningMediaList').data('mediaList', mediaList);

            mediaList.forEach(function (m) {
                const iconName = (function () {
                    if (m.media_type === 'image') return 'image';
                    if (m.media_type === 'video') return 'video';
                    if (m.media_type === 'audio') return 'volume-2';
                    return 'file';
                })();

                const url = m.file_path ? '{{ asset('storage') }}/' + m.file_path : '#';

                const card = $('<div class="learning-media-card"></div>');
                card.attr('data-id', m.id);

                card.html(
                    '<div class="d-flex align-items-center">' +
                        '<div class="learning-media-icon">' +
                            '<i class="feather icon-' + iconName + '"></i>' +
                        '</div>' +
                        '<div>' +
                            '<div style="font-size: 13px; font-weight: 500;">' + (m.title || ('Media #' + m.id)) + '</div>' +
                            (m.description ? '<div class="text-muted small">' + m.description + '</div>' : '') +
                            '<div>' +
                                '<a href="' + url + '" target="_blank" class="small">' +
                                    '<i class="feather icon-external-link mr-25"></i>Open' +
                                '</a>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<button type="button" class="btn btn-xs btn-outline-danger btn-delete-media">' +
                        '<i class="feather icon-trash-2"></i>' +
                    '</button>'
                );

                card.find('.btn-delete-media').on('click', function () {
                    deleteMedia(m.id, card);
                });

                container.append(card);
            });

            if (window.feather) {
                feather.replace();
            }
        }

        function loadTopic(id) {
            if (!id) return;

            $.get(routes.show.replace('__ID__', id), function (res) {
                const topic = res.topic || {};
                currentTopicId = topic.id || null;

                $('#learningId').val(currentTopicId);
                $('#learningFormTitle').text('Edit topic');

                $('#learningTitle').val(topic.title || '');
                $('#learningPromptLabel').val(topic.prompt_label || '');
                $('#learningShortIntro').val(topic.short_intro || '');
                $('#learningDifficulty').val(topic.difficulty || 'beginner');
                $('#learningMinutes').val(topic.estimated_minutes || '');
                $('#learningAudienceScope').val(topic.audience_scope || 'all');
                $('#learningActive').prop('checked', !!topic.is_active);

                const bodyHtml = topic.body || '';
                quill.setContents([]);
                if (bodyHtml) {
                    const delta = quill.clipboard.convert(bodyHtml);
                    quill.setContents(delta);
                }
                $('#learningBody').val(bodyHtml);

                const depIds = res.department_ids || [];
                const posIds = res.position_ids || [];
                const empIds = res.employee_ids || [];

                $('#learningDepartments').val(depIds).trigger('change');
                $('#learningPositions').val(posIds).trigger('change');
                $('#learningEmployees').val(empIds).trigger('change');

                renderMediaList(res.media || []);

                $('#btnDeleteTopic').show();
                highlightActiveTopic(currentTopicId);
            }).fail(function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Could not load topic.'
                });
            });
        }

        function collectPayload() {
            syncQuillToHidden();

            return {
                id: $('#learningId').val() || null,
                title: $('#learningTitle').val().trim(),
                prompt_label: $('#learningPromptLabel').val().trim(),
                short_intro: $('#learningShortIntro').val().trim(),
                body: $('#learningBody').val(),
                difficulty: $('#learningDifficulty').val(),
                estimated_minutes: $('#learningMinutes').val() || null,
                audience_scope: $('#learningAudienceScope').val(),
                is_active: $('#learningActive').is(':checked') ? 1 : 0,
                department_ids: $('#learningDepartments').val() || [],
                position_ids: $('#learningPositions').val() || [],
                employee_ids: $('#learningEmployees').val() || []
            };
        }

        function saveTopic() {
            const payload = collectPayload();

            if (!payload.title) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Title is required',
                    text: 'Please enter a title.'
                });
                return;
            }

            $.ajax({
                url: routes.store,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                data: payload,
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        text: 'Topic has been saved.'
                    });

                    const topic = res.topic || null;
                    if (topic && topic.id) {
                        currentTopicId = topic.id;
                        $('#learningId').val(topic.id);
                        $('#learningFormTitle').text('Edit topic');
                        $('#btnDeleteTopic').show();
                    }

                    fetchTopics();
                },
                error: function (xhr) {
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        const msgs = [];
                        Object.keys(errors).forEach(function (k) {
                            msgs.push(errors[k].join(' '));
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation error',
                            html: msgs.join('<br>')
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not save topic.'
                        });
                    }
                }
            });
        }

        function deleteTopic() {
            const id = $('#learningId').val();
            if (!id) return;

            Swal.fire({
                icon: 'warning',
                title: 'Delete topic?',
                text: 'This will remove the topic and all media.',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: routes.delete.replace('__ID__', id),
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf },
                    data: { _method: 'DELETE' },
                    success: function () {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: 'Topic has been deleted.'
                        });
                        resetForm();
                        fetchTopics();
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not delete topic.'
                        });
                    }
                });
            });
        }

        function uploadMedia() {
            if (!currentTopicId) {
                Swal.fire({
                    icon: 'info',
                    title: 'Save topic first',
                    text: 'Please save the topic before uploading media.'
                });
                return;
            }

            const input = document.getElementById('mediaFile');
            if (!input.files || !input.files[0]) {
                Swal.fire({
                    icon: 'info',
                    title: 'No file selected',
                    text: 'Please choose a file first.'
                });
                return;
            }

            const file = input.files[0];
            const fd = new FormData();
            fd.append('file', file);

            $.ajax({
                url: routes.uploadMedia.replace('__ID__', currentTopicId),
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf },
                data: fd,
                processData: false,
                contentType: false,
                success: function (res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Uploaded',
                        text: 'Media has been uploaded.'
                    });

                    // Reset label
                    input.value = '';
                    $('.custom-file-label[for="mediaFile"]').text('Choose file');

                    const mediaList = $('#learningMediaList').data('mediaList') || [];
                    const newMedia = res.media || null;
                    if (newMedia) {
                        mediaList.push(newMedia);
                        $('#learningMediaList').data('mediaList', mediaList);
                        renderMediaList(mediaList);
                    } else {
                        loadTopic(currentTopicId);
                    }
                },
                error: function (xhr) {
                    let msg = 'Could not upload media.';
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        const msgs = [];
                        Object.keys(errors).forEach(function (k) {
                            msgs.push(errors[k].join(' '));
                        });
                        msg = msgs.join(' ');
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg
                    });
                }
            });
        }

        function deleteMedia(mediaId, cardEl) {
            if (!mediaId) return;

            Swal.fire({
                icon: 'warning',
                title: 'Delete media?',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel'
            }).then(function (result) {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: routes.deleteMedia.replace('__ID__', mediaId),
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf },
                    data: { _method: 'DELETE' },
                    success: function () {
                        if (cardEl && cardEl.remove) {
                            cardEl.remove();
                        } else {
                            loadTopic(currentTopicId);
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Could not delete media.'
                        });
                    }
                });
            });
        }

        function bindEvents() {
            $('#btnNewTopic').on('click', function () {
                resetForm();
            });

            $('#btnNewTopicHeader').on('click', function () {
                resetForm();
            });

            $('#btnSaveTopic').on('click', function () {
                saveTopic();
            });

            $('#btnDeleteTopic').on('click', function () {
                deleteTopic();
            });

            $('#btnUploadMedia').on('click', function () {
                uploadMedia();
            });

            $('#searchTopics').on('input', function () {
                applyTopicFilter();
            });

            $('#mediaFile').on('change', function () {
                const fileName = this.files && this.files[0] ? this.files[0].name : 'Choose file';
                $(this).next('.custom-file-label').text(fileName);
            });
        }

        $(document).ready(function () {
            initSelect2();
            initQuill();
            bindEvents();
            fetchTopics();
        });
    })();
</script>
@endsection
