{{-- resources/views/admin/product/product/stages/brand_modal.blade.php --}}

<style>
    /* =========================================================
       CUSTOM BRAND MODAL
    ========================================================= */
    .brand-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(15, 23, 42, .58);
    }

    .brand-modal-backdrop.is-open {
        display: flex;
    }

    .brand-modal {
        width: 100%;
        max-width: 1180px;
        max-height: calc(100vh - 36px);
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 22px;
        box-shadow: 0 24px 70px rgba(15, 23, 42, .28);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        transform: translateY(12px) scale(.98);
        opacity: 0;
        transition: all .18s ease;
    }

    .brand-modal-backdrop.is-open .brand-modal {
        transform: translateY(0) scale(1);
        opacity: 1;
    }

    .brand-modal-header {
        padding: 18px 20px;
        border-bottom: 1px solid #e5e7eb;
        background: #fafafa;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
    }

    .brand-modal-title {
        margin: 0;
        font-size: 18px;
        font-weight: 900;
        color: #111827;
        text-transform: uppercase;
    }

    .brand-modal-subtitle {
        margin-top: 4px;
        font-size: 12px;
        font-weight: 700;
        color: #6b7280;
    }

    .brand-modal-close {
        width: 40px;
        height: 40px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        color: #6b7280;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all .18s ease;
    }

    .brand-modal-close:hover {
        background: #f3f4f6;
        color: #111827;
    }

    .brand-modal-body {
        padding: 20px;
        overflow: auto;
    }

    .brand-modal-footer {
        padding: 16px 20px;
        border-top: 1px solid #e5e7eb;
        background: #fafafa;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }

    .brand-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 14px;
    }

    .brand-grid-full {
        grid-column: 1 / -1;
    }

    .brand-form-group label {
        display: block;
        font-size: 12px;
        font-weight: 900;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin-bottom: 7px;
    }

    .brand-input,
    .brand-select {
        width: 100%;
        min-height: 42px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        background: #fff;
        padding: 10px 12px;
        color: #111827;
        font-size: 14px;
        outline: none;
        transition: all .18s ease;
    }

    .brand-input:focus,
    .brand-select:focus {
        border-color: #8fc73e;
        box-shadow: 0 0 0 3px rgba(143, 199, 62, .14);
    }

    .brand-error {
        display: block;
        min-height: 17px;
        margin-top: 5px;
        font-size: 12px;
        font-weight: 800;
        color: #ef4444;
    }

    .brand-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 10px;
    }

    .brand-section-title {
        margin: 0;
        font-size: 14px;
        font-weight: 900;
        color: #111827;
        text-transform: uppercase;
    }

    .brand-table-wrap {
        overflow-x: auto;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
    }

    .brand-table {
        width: 100%;
        min-width: 1080px;
        margin: 0;
        border-collapse: collapse;
    }

    .brand-table th {
        background: #f9fafb;
        color: #6b7280;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: .05em;
        padding: 10px;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .brand-table td {
        padding: 10px;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: top;
    }

    .brand-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .brand-table .brand-input {
        min-height: 38px;
        font-size: 13px;
        border-radius: 9px;
    }

    .brand-logo-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .brand-logo-preview {
        width: 76px;
        height: 76px;
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        background: #f9fafb;
        object-fit: cover;
        flex: 0 0 auto;
    }

    .brand-note {
        margin-top: 5px;
        font-size: 12px;
        font-weight: 700;
        color: #6b7280;
    }

    .brand-btn,
    .brand-btn-soft,
    .brand-btn-danger,
    .brand-btn-success {
        border: 0;
        border-radius: 10px;
        padding: 10px 15px;
        font-weight: 900;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        text-decoration: none;
        transition: all .18s ease;
        white-space: nowrap;
    }

    .brand-btn {
        background: #74b2d4;
        color: #fff;
    }

    .brand-btn:hover {
        background: #559fc7;
        color: #fff;
    }

    .brand-btn-success {
        background: #8fc73e;
        color: #fff;
    }

    .brand-btn-success:hover {
        background: #7baa18;
        color: #fff;
    }

    .brand-btn-soft {
        background: #fff;
        color: #374151;
        border: 1px solid #e5e7eb;
    }

    .brand-btn-soft:hover {
        background: #f9fafb;
        color: #111827;
    }

    .brand-btn-danger {
        background: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecaca;
        padding: 9px 11px;
    }

    .brand-btn-danger:hover {
        background: #fee2e2;
        color: #dc2626;
    }

    .brand-btn[disabled],
    .brand-btn-success[disabled] {
        opacity: .7;
        cursor: not-allowed;
    }

    @media (max-width: 768px) {
        .brand-grid {
            grid-template-columns: 1fr;
        }

        .brand-modal {
            max-height: calc(100vh - 20px);
        }

        .brand-modal-body {
            padding: 14px;
        }
    }
</style>

<div class="brand-modal-backdrop" id="new_brand" aria-hidden="true">
    <div class="brand-modal" role="dialog" aria-modal="true" aria-labelledby="brandModalLabel">
        <form id="brandForm" novalidate enctype="multipart/form-data">
            @csrf

            <div class="brand-modal-header">
                <div>
                    <h4 class="brand-modal-title" id="brandModalLabel">Neue Marke hinzufügen</h4>
                    <div class="brand-modal-subtitle">
                        Der Hersteller wird nach dem Speichern automatisch im Dropdown ausgewählt.
                    </div>
                </div>

                <button type="button" class="brand-modal-close js-brand-modal-close" aria-label="Schließen">
                    <i class="feather icon-x"></i>
                </button>
            </div>

            <div class="brand-modal-body">
                <div class="brand-grid">
                    {{-- Hersteller --}}
                    <div class="brand-form-group">
                        <label for="brand_name">Hersteller *</label>
                        <input type="text" class="brand-input" id="brand_name" name="name" placeholder="z. B. Viessmann"
                            required>
                        <span class="brand-error" id="name-error"></span>
                    </div>

                    {{-- Initial --}}
                    <div class="brand-form-group">
                        <label for="brand_initial">Initial *</label>
                        <input type="text" class="brand-input" id="brand_initial" name="initial" maxlength="10"
                            placeholder="z. B. V" required>
                        <span class="brand-error" id="initial-error"></span>
                    </div>

                    {{-- Zweckkategorie --}}
                    <div class="brand-form-group brand-grid-full">
                        <label for="brand_purpose">Zweckkategorie *</label>
                        <select name="purpose" id="brand_purpose" class="brand-select" required>
                            <option value="">Bitte wählen</option>
                            <option value="PHOTOVOLTAIK">PHOTOVOLTAIK</option>
                            <option value="BATTERIESPEICHER">BATTERIESPEICHER</option>
                            <option value="WÄRMEPUMPE">WÄRMEPUMPE</option>
                            <option value="WALLBOX">WALLBOX</option>
                            <option value="ELEKTRO">ELEKTRO</option>
                            <option value="SANITÄR">SANITÄR</option>
                            <option value="BAD">BAD</option>
                            <option value="BAUELEMENTE">BAUELEMENTE</option>
                            <option value="KÜCHE">KÜCHE</option>
                            <option value="SOLAR CARPORT">SOLAR CARPORT</option>
                            <option value="SOFTWARE">SOFTWARE</option>
                            <option value="HARDWARE">HARDWARE</option>
                        </select>
                        <span class="brand-error" id="purpose-error"></span>
                    </div>

                    {{-- Abteilungen --}}
                    <div class="brand-grid-full">
                        <div class="brand-section-head">
                            <h6 class="brand-section-title">Abteilungen & Ansprechpartner</h6>

                            <button type="button" class="brand-btn" id="add_brand">
                                <i class="feather icon-plus"></i>
                                Zeile hinzufügen
                            </button>
                        </div>

                        <div class="brand-table-wrap">
                            <table class="brand-table" id="add_department">
                                <thead>
                                    <tr>
                                        <th>Abteilung</th>
                                        <th>Ansprechpartner</th>
                                        <th>Position</th>
                                        <th>E-Mail</th>
                                        <th>Mobil</th>
                                        <th>Festnetz</th>
                                        <th>Büro</th>
                                        <th class="text-right">Aktion</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td>
                                            <input type="text" class="brand-input" placeholder="Abteilung"
                                                name="brand[0][brand_department]">
                                        </td>
                                        <td>
                                            <input type="text" class="brand-input" placeholder="Ansprechpartner"
                                                name="brand[0][name]">
                                        </td>
                                        <td>
                                            <input type="text" class="brand-input" placeholder="Position"
                                                name="brand[0][position]">
                                        </td>
                                        <td>
                                            <input type="email" class="brand-input" placeholder="E-Mail"
                                                name="brand[0][email]">
                                        </td>
                                        <td>
                                            <input type="text" class="brand-input" placeholder="Mobilnummer"
                                                name="brand[0][phone]">
                                        </td>
                                        <td>
                                            <input type="text" class="brand-input" placeholder="Festnetznummer"
                                                name="brand[0][home]">
                                        </td>
                                        <td>
                                            <input type="text" class="brand-input" placeholder="Büro-Telefonnummer"
                                                name="brand[0][office]">
                                        </td>
                                        <td class="text-right">
                                            <button type="button" class="brand-btn-danger brand-remove-row">
                                                <i class="feather icon-trash-2"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Logo --}}
                    <div class="brand-form-group brand-grid-full">
                        <label for="brand_image">Logo</label>

                        <div class="brand-logo-row">
                            <img src="{{ asset('logo/logo.png') }}" alt="Logo Vorschau" class="brand-logo-preview"
                                id="brandLogoPreview">

                            <div style="flex:1;">
                                <input type="file" class="brand-input" id="brand_image" name="image"
                                    accept="image/png,image/jpeg,image/jpg">
                                <div class="brand-note">
                                    Optional. Erlaubt: PNG, JPG, JPEG. Max. 2 MB.
                                </div>
                                <span class="brand-error" id="image-error"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="brand-modal-footer">
                <button type="button" class="brand-btn-soft js-brand-modal-close">
                    Schließen
                </button>

                <button type="submit" class="brand-btn-success" id="saveBrandBtn">
                    <i class="feather icon-save"></i>
                    Speichern
                </button>
            </div>
        </form>
    </div>
</div>
