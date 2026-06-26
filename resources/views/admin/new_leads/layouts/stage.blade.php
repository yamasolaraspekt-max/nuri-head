<div class="cst-wrap" data-stage-root data-customer-id="{{ $customer_id }}" data-alternative-id="{{ $alternative_id }}"
    data-product-id="{{ $product_id }}" data-section-id="{{ $section_id }}"
    data-save-url="{{ route('customer-stages.save') }}" data-saved-version="{{ $savedVersion }}"
    data-used-version="{{ $usedVersion }}">

    <style>
        .cst-wrap {
            --cst-green: #93c21c;
            --cst-green-soft: #cfe09b;
            --cst-blue: #74b2d4;
            --cst-blue-soft: #c0d8ea;
            --cst-orange: #f8ac00;
            --cst-pink: #e50656;
            --cst-text: #1f2937;
            --cst-muted: #6b7280;
            --cst-border: #e5e7eb;
            --cst-soft: #f8fafc;
            background: #fff;
            color: var(--cst-text);
            padding: 14px;
        }

        .cst-wrap *,
        .cst-wrap *::before,
        .cst-wrap *::after {
            box-sizing: border-box;
        }

        .cst-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 16px;
        }

        .cst-title {
            margin: 0;
            font-size: 22px;
            font-weight: 950;
            color: var(--cst-blue);
        }

        .cst-subtitle {
            margin-top: 5px;
            color: var(--cst-muted);
            font-size: 13px;
            line-height: 1.5;
        }

        .cst-warning-note {
            margin-top: 10px;
            display: inline-flex;
            align-items: flex-start;
            gap: 8px;
            border-radius: 16px;
            padding: 10px 12px;
            background: rgba(248, 172, 0, .12);
            color: #92400e;
            font-size: 12px;
            font-weight: 850;
            line-height: 1.45;
        }

        .cst-warning-note svg {
            width: 16px;
            height: 16px;
            flex: 0 0 auto;
            margin-top: 1px;
        }

        .cst-card {
            border: 1px solid var(--cst-border);
            border-radius: 24px;
            background: #fff;
            padding: 16px;
            margin-bottom: 14px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
        }

        .cst-card-title {
            font-size: 14px;
            font-weight: 950;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--cst-text);
        }

        .cst-card-title svg,
        .cst-btn svg {
            width: 16px;
            height: 16px;
        }

        .cst-alert {
            display: none;
            border-radius: 16px;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-size: 13px;
            font-weight: 900;
        }

        .cst-alert.success {
            display: block;
            background: rgba(147, 194, 28, .15);
            color: #4d7c0f;
        }

        .cst-alert.error {
            display: block;
            background: rgba(229, 6, 86, .12);
            color: var(--cst-pink);
        }

        .cst-version-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .cst-version-btn {
            border: 1px solid var(--cst-border);
            background: #fff;
            border-radius: 16px;
            padding: 10px 14px;
            min-width: 125px;
            cursor: pointer;
            transition: .16s ease;
            text-align: left;
        }

        .cst-version-btn:hover {
            transform: translateY(-1px);
            border-color: var(--cst-blue);
            box-shadow: 0 8px 20px rgba(15, 23, 42, .06);
        }

        .cst-version-btn.is-active {
            border-color: var(--cst-blue);
            background: rgba(116, 178, 212, .12);
        }

        .cst-version-btn.is-saved {
            border-color: var(--cst-green);
        }

        .cst-version-btn.is-changed {
            border-color: var(--cst-orange);
            background: rgba(248, 172, 0, .10);
        }

        .cst-version-name {
            font-size: 13px;
            font-weight: 950;
            color: var(--cst-text);
        }

        .cst-version-meta {
            margin-top: 4px;
            font-size: 11px;
            color: var(--cst-muted);
            font-weight: 800;
        }

        .cst-badge {
            display: inline-flex;
            border-radius: 999px;
            padding: 4px 8px;
            font-size: 10px;
            font-weight: 950;
            background: #f3f4f6;
            color: var(--cst-text);
            margin-top: 6px;
        }

        .cst-badge-saved {
            background: rgba(147, 194, 28, .15);
            color: #4d7c0f;
        }

        .cst-badge-unsaved {
            background: rgba(248, 172, 0, .16);
            color: #92400e;
        }

        .cst-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
            flex-wrap: wrap;
        }

        .cst-current-info {
            font-size: 13px;
            color: var(--cst-muted);
            font-weight: 800;
        }

        .cst-current-info strong {
            color: var(--cst-text);
        }

        .cst-arrow-flow {
            display: flex;
            align-items: stretch;
            gap: 8px;
            overflow-x: auto;
            padding: 8px 2px 16px;
            scrollbar-width: thin;
        }

        .cst-step {
            position: relative;
            border: 0;
            color: #fff;
            min-width: 130px;
            height: 34px;
            padding: 0 24px 0 18px;
            font-size: 12px;
            font-weight: 950;
            text-align: center;
            clip-path: polygon(0 0, calc(100% - 15px) 0, 100% 50%, calc(100% - 15px) 100%, 0 100%, 12px 50%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            flex: 0 0 auto;
            opacity: .95;
        }

        .cst-step:first-child {
            clip-path: polygon(0 0, calc(100% - 15px) 0, 100% 50%, calc(100% - 15px) 100%, 0 100%);
            border-radius: 8px 0 0 8px;
        }

        .cst-step:last-child {
            border-radius: 0 8px 8px 0;
        }

        .cst-step:nth-child(6n + 1) {
            background: #7fba18;
        }

        .cst-step:nth-child(6n + 2) {
            background: #b8cddd;
        }

        .cst-step:nth-child(6n + 3) {
            background: #cfdea4;
        }

        .cst-step:nth-child(6n + 4) {
            background: #18b39b;
        }

        .cst-step:nth-child(6n + 5) {
            background: #0672d8;
        }

        .cst-step:nth-child(6n + 6) {
            background: #8843b2;
        }

        .cst-step-sort {
            margin-left: 6px;
            font-size: 10px;
            opacity: .8;
        }

        .cst-preview-panel {
            border: 1px dashed var(--cst-border);
            background: var(--cst-soft);
            border-radius: 18px;
            padding: 12px;
            margin-top: 12px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
        }

        .cst-preview-item {
            background: #fff;
            border: 1px solid var(--cst-border);
            border-radius: 16px;
            padding: 10px;
        }

        .cst-preview-item.is-danger {
            border-color: rgba(229, 6, 86, .24);
            background: rgba(229, 6, 86, .04);
        }

        .cst-preview-label {
            font-size: 11px;
            color: var(--cst-muted);
            font-weight: 900;
            margin-bottom: 4px;
        }

        .cst-preview-value {
            font-size: 14px;
            color: var(--cst-text);
            font-weight: 950;
        }

        .cst-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .cst-btn {
            border: 0;
            border-radius: 14px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 950;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            cursor: pointer;
            transition: .16s ease;
        }

        .cst-btn:hover {
            transform: translateY(-1px);
        }

        .cst-btn-primary {
            background: var(--cst-blue);
            color: #fff;
        }

        .cst-btn-danger {
            background: var(--cst-pink);
            color: #fff;
        }

        .cst-btn-soft {
            background: #f3f4f6;
            color: var(--cst-text);
        }

        .cst-btn:disabled {
            opacity: .65;
            cursor: not-allowed;
            transform: none;
        }

        .cst-empty {
            border: 1px dashed var(--cst-border);
            border-radius: 22px;
            padding: 28px;
            text-align: center;
            color: var(--cst-muted);
            font-weight: 900;
        }

        .cst-swal-popup {
            border-radius: 24px !important;
            padding: 22px !important;
        }

        .cst-swal-confirm,
        .cst-swal-cancel {
            border: 0 !important;
            border-radius: 14px !important;
            padding: 10px 16px !important;
            font-size: 13px !important;
            font-weight: 950 !important;
            cursor: pointer !important;
            margin: 0 4px !important;
        }

        .cst-swal-confirm {
            background: #e50656 !important;
            color: #fff !important;
        }

        .cst-swal-cancel {
            background: #f3f4f6 !important;
            color: #1f2937 !important;
        }

        @media (max-width: 900px) {
            .cst-preview-panel {
                grid-template-columns: 1fr;
            }

            .cst-head {
                flex-direction: column;
            }
        }

        @media (max-width: 560px) {
            .cst-wrap {
                padding: 10px;
            }

            .cst-version-btn {
                width: 100%;
            }

            .cst-actions {
                flex-direction: column;
            }

            .cst-btn {
                width: 100%;
            }
        }
    </style>

    <div class="cst-head">
        <div>
            <h3 class="cst-title">Arbeitsprozess</h3>

            <div class="cst-subtitle">
                Wähle eine Version aus. Die Stages werden sofort als Vorschau angezeigt. Erst mit Speichern wird die
                Version übernommen.
            </div>

            <div class="cst-warning-note">
                <i data-feather="alert-triangle"></i>
                <span>
                    Achtung: Wenn du eine andere Version speicherst, werden die bereits gespeicherten
                    Arbeitsprozess-Daten für dieses Produkt und diese Sektion ersetzt.
                </span>
            </div>
        </div>
    </div>

    <div id="customerStageAlert" class="cst-alert"></div>

    @if($groupedStages->isEmpty())
        <div class="cst-empty">
            Keine Stufen für dieses Produkt gefunden.
        </div>
    @else
        <div class="cst-card">
            <div class="cst-card-title">
                <i data-feather="layers"></i>
                Version auswählen
            </div>

            <div class="cst-version-grid">
                @foreach($groupedStages as $version => $items)
                    <button type="button"
                        class="cst-version-btn {{ (string) $version === (string) $usedVersion ? 'is-active' : '' }} {{ (string) $version === (string) $savedVersion ? 'is-saved' : '' }}"
                        data-stage-version-btn data-version="{{ $version }}">
                        <div class="cst-version-name">
                            Version {{ $version }}
                        </div>

                        <div class="cst-version-meta">
                            {{ $items->count() }} Stufen
                        </div>

                        @if((string) $version === (string) $savedVersion)
                            <span class="cst-badge cst-badge-saved">
                                Gespeichert
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <div class="cst-card">
            <div class="cst-toolbar">
                <div class="cst-card-title" style="margin-bottom:0;">
                    <i data-feather="git-branch"></i>
                    Vorschau
                </div>

                <div class="cst-current-info">
                    Version:
                    <strong data-current-version>{{ $usedVersion }}</strong>
                </div>
            </div>

            @foreach($groupedStages as $version => $items)
                <div class="cst-version-panel" data-version-panel="{{ $version }}"
                    style="{{ (string) $version === (string) $usedVersion ? '' : 'display:none;' }}">

                    <div class="cst-arrow-flow">
                        @foreach($items as $stage)
                            <div class="cst-step" title="{{ $stage->stage }}">
                                {{ $stage->stage }}

                                @if(!empty($stage->sort_order))
                                    <span class="cst-step-sort">
                                        {{ $stage->sort_order }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <div class="cst-preview-panel">
                <div class="cst-preview-item">
                    <div class="cst-preview-label">Ausgewählte Version</div>
                    <div class="cst-preview-value" data-preview-version>{{ $usedVersion }}</div>
                </div>

                <div class="cst-preview-item">
                    <div class="cst-preview-label">Gespeicherte Version</div>
                    <div class="cst-preview-value" data-saved-version-text>
                        {{ $savedVersion ?: '-' }}
                    </div>
                </div>

                <div class="cst-preview-item {{ ($savedRowsCount ?? 0) > 0 ? 'is-danger' : '' }}">
                    <div class="cst-preview-label">Vorhandene gespeicherte Zeilen</div>
                    <div class="cst-preview-value" data-saved-rows>
                        {{ $savedRowsCount ?? 0 }}
                    </div>
                </div>
            </div>

            <div class="cst-actions">
                <button type="button" class="cst-btn cst-btn-soft" data-reset-version>
                    <i data-feather="rotate-ccw"></i>
                    Zurücksetzen
                </button>

                <button type="button" class="cst-btn cst-btn-primary" data-save-stage-version>
                    <i data-feather="save"></i>
                    Version speichern
                </button>
            </div>
        </div>
    @endif
</div>

<script>
    (function () {
        const root = document.querySelector('[data-stage-root]');
        if (!root) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const saveUrl = root.dataset.saveUrl;

        let selectedVersion = root.dataset.usedVersion || '';
        let savedVersion = root.dataset.savedVersion || '';

        function refreshIcons() {
            if (window.feather) {
                window.feather.replace();
            }
        }

        function showAlert(type, message) {
            const alertBox = document.getElementById('customerStageAlert');
            if (!alertBox) return;

            alertBox.className = `cst-alert ${type}`;
            alertBox.textContent = message || '';

            clearTimeout(alertBox._timer);
            alertBox._timer = setTimeout(() => {
                alertBox.className = 'cst-alert';
                alertBox.textContent = '';
            }, 3500);
        }

        function getSavedRowsCount() {
            const savedRowsEl = root.querySelector('[data-saved-rows]');
            return parseInt(savedRowsEl?.textContent || '0', 10) || 0;
        }

        function isVersionChanged() {
            return String(savedVersion || '') !== String(selectedVersion || '');
        }

        function updateChangedUi() {
            root.querySelectorAll('[data-stage-version-btn]').forEach(btn => {
                const isSelected = String(btn.dataset.version) === String(selectedVersion);
                const isSaved = String(btn.dataset.version) === String(savedVersion);

                btn.classList.toggle('is-active', isSelected);
                btn.classList.toggle('is-saved', isSaved);
                btn.classList.toggle('is-changed', isSelected && isVersionChanged() && getSavedRowsCount() > 0);
            });

            const saveBtn = root.querySelector('[data-save-stage-version]');

            if (saveBtn) {
                saveBtn.classList.toggle('cst-btn-danger', isVersionChanged() && getSavedRowsCount() > 0);
                saveBtn.classList.toggle('cst-btn-primary', !(isVersionChanged() && getSavedRowsCount() > 0));
            }
        }

        function showVersion(version) {
            selectedVersion = String(version || '');

            root.querySelectorAll('[data-version-panel]').forEach(panel => {
                panel.style.display = String(panel.dataset.versionPanel) === selectedVersion ? '' : 'none';
            });

            const currentVersionEl = root.querySelector('[data-current-version]');
            const previewVersionEl = root.querySelector('[data-preview-version]');

            if (currentVersionEl) currentVersionEl.textContent = selectedVersion || '-';
            if (previewVersionEl) previewVersionEl.textContent = selectedVersion || '-';

            updateChangedUi();
            refreshIcons();
        }

        async function confirmBeforeSave() {
            const savedRows = getSavedRowsCount();

            if (savedRows <= 0 && !savedVersion) {
                return true;
            }

            const versionChanged = isVersionChanged();

            const warningText = versionChanged
                ? `Du wechselst von Version ${savedVersion || '-'} auf Version ${selectedVersion}.`
                : `Du speicherst die aktuell gewählte Version ${selectedVersion} erneut.`;

            const dangerText = `Dabei werden ${savedRows} bereits gespeicherte Arbeitsprozess-Zeilen für dieses Produkt und diese Sektion gelöscht und neu erstellt.`;

            if (window.Swal) {
                const result = await Swal.fire({
                    title: 'Arbeitsprozess wirklich ändern?',
                    html: `
                        <div style="text-align:left;line-height:1.55;">
                            <p style="margin-bottom:10px;font-weight:700;color:#1f2937;">
                                ${warningText}
                            </p>

                            <div style="
                                padding:12px;
                                border-radius:16px;
                                background:rgba(229,6,86,.10);
                                color:#b91c1c;
                                font-weight:850;
                                font-size:13px;
                            ">
                                ${dangerText}
                            </div>

                            <p style="margin-top:10px;margin-bottom:0;color:#6b7280;font-size:13px;">
                                Bitte nur fortfahren, wenn du sicher bist.
                            </p>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ja, ändern und speichern',
                    cancelButtonText: 'Abbrechen',
                    reverseButtons: true,
                    focusCancel: true,
                    buttonsStyling: false,
                    customClass: {
                        popup: 'cst-swal-popup',
                        confirmButton: 'cst-swal-confirm',
                        cancelButton: 'cst-swal-cancel',
                    },
                });

                return result.isConfirmed;
            }

            return confirm(
                `${warningText}\n\n${dangerText}\n\nFortfahren?`
            );
        }

        root.querySelectorAll('[data-stage-version-btn]').forEach(btn => {
            btn.addEventListener('click', function () {
                showVersion(this.dataset.version);
            });
        });

        const resetBtn = root.querySelector('[data-reset-version]');
        if (resetBtn) {
            resetBtn.addEventListener('click', function () {
                showVersion(savedVersion || root.dataset.usedVersion || '');
                showAlert('success', 'Version wurde zurückgesetzt.');
            });
        }

        const saveBtn = root.querySelector('[data-save-stage-version]');
        if (saveBtn) {
            saveBtn.addEventListener('click', async function () {
                if (!selectedVersion) {
                    showAlert('error', 'Bitte eine Version auswählen.');
                    return;
                }

                const confirmed = await confirmBeforeSave();

                if (!confirmed) {
                    return;
                }

                const oldHtml = saveBtn.innerHTML;
                saveBtn.disabled = true;
                saveBtn.innerHTML = 'Speichern...';

                try {
                    const formData = new FormData();
                    formData.append('customer_id', root.dataset.customerId || '');
                    formData.append('alternative_id', root.dataset.alternativeId || '');
                    formData.append('product_id', root.dataset.productId || '');
                    formData.append('section_id', root.dataset.sectionId || '');
                    formData.append('version', selectedVersion);
                    formData.append('status', 'active');

                    const response = await fetch(saveUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok || data.success === false) {
                        throw data;
                    }

                    savedVersion = selectedVersion;
                    root.dataset.savedVersion = selectedVersion;

                    root.querySelectorAll('[data-stage-version-btn]').forEach(btn => {
                        const isSaved = String(btn.dataset.version) === String(selectedVersion);

                        btn.classList.toggle('is-saved', isSaved);

                        let badge = btn.querySelector('.cst-badge-saved');

                        if (isSaved) {
                            if (!badge) {
                                badge = document.createElement('span');
                                badge.className = 'cst-badge cst-badge-saved';
                                badge.textContent = 'Gespeichert';
                                btn.appendChild(badge);
                            }
                        } else if (badge) {
                            badge.remove();
                        }
                    });

                    const savedVersionText = root.querySelector('[data-saved-version-text]');
                    const savedRowsText = root.querySelector('[data-saved-rows]');

                    if (savedVersionText) {
                        savedVersionText.textContent = selectedVersion;
                    }

                    if (savedRowsText) {
                        savedRowsText.textContent = data.saved_rows || 0;
                    }

                    updateChangedUi();
                    showAlert('success', data.message || 'Arbeitsprozess wurde gespeichert.');
                } catch (error) {
                    let message = 'Arbeitsprozess konnte nicht gespeichert werden.';

                    if (error?.message) {
                        message = error.message;
                    }

                    if (error?.errors) {
                        message = Object.values(error.errors).flat().join(' ');
                    }

                    if (error?.debug) {
                        console.warn('Stage save debug:', error.debug);
                    }

                    showAlert('error', message);
                } finally {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = oldHtml;
                    refreshIcons();
                }
            });
        }

        showVersion(selectedVersion);
        refreshIcons();
    })();
</script>