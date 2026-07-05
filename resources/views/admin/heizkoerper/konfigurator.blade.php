@extends('admin.layouts.app')

@section('title', 'Heizkörper-Konfigurator')

{{--
    Heizkörper-Konfigurator (M4-a v-b) — ERSTE Alpine-Fläche im CRM (CLAUDE.md-Präzedenzfall).
    Alpine NUR hier. fetch gegen die abgenommenen Endpunkte heizkoerper.berechnen / .kompatibilitaet,
    CSRF aus <meta name="csrf-token">. Physik/Regeln liegen serverseitig (M3-Kerne) — hier nur UI.
    Datenlage-Stufe wird EHRLICH und nicht wegklickbar gelabelt; Sperren/Hinweise prominent.
--}}

@section('content')
<section class="heizkoerper-konfigurator"
    x-data="heizkoerperKonfigurator({
        urls: {
            berechnen: '{{ route('heizkoerper.berechnen') }}',
            kompatibilitaet: '{{ route('heizkoerper.kompatibilitaet') }}'
        }
    })">

    <div class="content-header row">
        <div class="content-header-left col-12 mb-1">
            <h2 class="content-header-title">Heizkörper-Konfigurator</h2>
            <p class="text-muted mb-0">EN-442-Auslegung &amp; Ventil-/Zubehör-Kompatibilität. Aufnahme läuft weiter über die Heizkörper-Konfiguration; hier Rechnung &amp; Kompatibilität.</p>
        </div>
    </div>

    <div class="alert alert-danger" x-show="generalError" x-text="generalError" x-cloak></div>

    <div class="row">
        {{-- Eingabe --}}
        <div class="col-lg-5 col-12">
            <div class="card">
                <div class="card-header"><h4 class="card-title">Eingabe</h4></div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Bestehende Aufnahme (optional)</label>
                        <select class="form-control" x-model="form.installation_id">
                            <option value="">— Direkteingabe —</option>
                            @foreach ($installations as $inst)
                                <option value="{{ $inst->id }}">#{{ $inst->id }} · {{ $inst->room ?: 'Raum ?' }}@if($inst->radiator_spec_id) · Spec {{ $inst->radiator_spec_id }}@endif</option>
                            @endforeach
                        </select>
                        <small class="text-muted">Bei Auswahl werden Spec, Baulänge, Anschluss serverseitig aus der Aufnahme gezogen.</small>
                    </div>

                    <div class="row">
                        <div class="form-group col-6">
                            <label>Katalog-Spec-ID</label>
                            <input type="number" class="form-control" x-model="form.radiator_spec_id" placeholder="z. B. 1">
                            <small class="text-danger" x-text="fieldError('radiator_spec_id')"></small>
                        </div>
                        <div class="form-group col-6">
                            <label>Baulänge (mm)</label>
                            <input type="number" class="form-control" x-model="form.baulaenge_mm">
                            <small class="text-danger" x-text="fieldError('baulaenge_mm')"></small>
                        </div>
                        <div class="form-group col-6">
                            <label>Anzahl</label>
                            <input type="number" class="form-control" x-model="form.anzahl" min="1">
                        </div>
                        <div class="form-group col-6">
                            <label>Vorlauf (°C)</label>
                            <input type="number" class="form-control" x-model="form.vorlauf">
                            <small class="text-danger" x-text="fieldError('vorlauf')"></small>
                        </div>
                        <div class="form-group col-6">
                            <label>Raumtemperatur (°C)</label>
                            <input type="number" class="form-control" x-model="form.raumtemp">
                        </div>
                        <div class="form-group col-6">
                            <label>Spreizung (K)</label>
                            <input type="number" class="form-control" x-model="form.spreizung">
                            <small class="text-danger" x-text="fieldError('spreizung')"></small>
                        </div>
                        <div class="form-group col-6">
                            <label>Heizlast (W, optional)</label>
                            <input type="number" class="form-control" x-model="form.heizlast_w" placeholder="für Ampel &amp; Min-Vorlauf">
                        </div>
                    </div>

                    <hr>
                    <div class="row">
                        <div class="form-group col-12">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="istVentilHk" x-model="form.ist_ventil_heizkoerper">
                                <label class="custom-control-label" for="istVentilHk">Ventil-Heizkörper (integriertes Ventilunterteil)</label>
                            </div>
                        </div>
                        <div class="form-group col-6">
                            <label>Anschluss</label>
                            <select class="form-control" x-model="form.anschluss_fuehrung">
                                <option value="zweirohr">Zweirohr</option>
                                <option value="einrohr">Einrohr</option>
                            </select>
                        </div>
                        <div class="form-group col-6">
                            <label>Kopf-Norm (Bestand)</label>
                            <select class="form-control" x-model="form.kopf_norm_bestand">
                                <option value="">—</option>
                                <option value="M30x1_5">M30x1,5</option>
                                <option value="RA">RA</option>
                                <option value="RAV">RAV (Altnorm)</option>
                                <option value="RAVL">RAVL (Altnorm)</option>
                                <option value="sonstige">sonstige</option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex">
                        <button type="button" class="btn btn-primary mr-1" @click="berechnen()" :disabled="loadingBerechnen">
                            <span x-show="!loadingBerechnen">EN-442 berechnen</span>
                            <span x-show="loadingBerechnen" x-cloak>Berechne…</span>
                        </button>
                        <button type="button" class="btn btn-outline-primary" @click="pruefeKompatibilitaet()" :disabled="loadingKompat">
                            <span x-show="!loadingKompat">Kompatibilität prüfen</span>
                            <span x-show="loadingKompat" x-cloak>Prüfe…</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ergebnis --}}
        <div class="col-lg-7 col-12">
            {{-- EN-442 --}}
            <div class="card" x-show="result" x-cloak>
                <div class="card-header"><h4 class="card-title">EN-442-Ergebnis</h4></div>
                <div class="card-body">
                    <p class="mb-1">
                        Q<sub>norm</sub>: <strong x-text="result?.q_norm_w"></strong> W ·
                        Q<sub>real</sub> (Szenario): <strong x-text="result?.q_real"></strong> W
                        <span class="badge ml-1" :class="ampelClass(result?.ampel)" x-text="ampelText(result?.ampel)"></span>
                        <template x-if="result && result.min_vorlauf !== null">
                            <span class="ml-1">· Min-Vorlauf: <strong x-text="result.min_vorlauf"></strong> °C</span>
                        </template>
                    </p>
                    <table class="table table-striped mb-0">
                        <thead><tr><th>Vorlauf (°C)</th><th>Rücklauf (°C)</th><th>Leistung (W)</th></tr></thead>
                        <tbody>
                            <template x-for="row in (result?.en442_tabelle || [])" :key="row.vorlauf">
                                <tr><td x-text="row.vorlauf"></td><td x-text="row.ruecklauf"></td><td x-text="row.q_w"></td></tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Kompatibilität --}}
            <div class="card" x-show="kompat" x-cloak>
                <div class="card-header"><h4 class="card-title">Ventil-/Zubehör-Kompatibilität</h4></div>
                <div class="card-body">
                    {{-- Ehrliches Datenlage-Label (nicht wegklickbar) --}}
                    <div class="alert"
                        :class="kompat?.datenqualitaet === 'serien-praezise' ? 'alert-success' : 'alert-warning'">
                        <template x-if="kompat?.datenqualitaet === 'regel-kandidaten'">
                            <span><strong>Kandidaten nach Regelwerk</strong> — keine Serien-Präzision (SKU/Preis folgen aus Lieferanten-Import).</span>
                        </template>
                        <template x-if="kompat?.datenqualitaet === 'serien-praezise'">
                            <span><strong>Serien-präzise</strong> — Zubehör aus der Kompatibilitätsdatenbank (mit Hersteller-Artikelnummer).</span>
                        </template>
                    </div>

                    {{-- Sperren prominent (z. B. §5.3 Einrohr) --}}
                    <template x-for="s in (kompat?.sperren || [])" :key="s">
                        <div class="alert alert-danger mb-1" x-text="s"></div>
                    </template>

                    <table class="table table-sm" x-show="(kompat?.positionen || []).length">
                        <thead><tr><th>Kategorie</th><th>Artikel</th><th>Hersteller-Nr.</th><th>Begründung</th></tr></thead>
                        <tbody>
                            <template x-for="p in (kompat?.positionen || [])" :key="p.kategorie + '-' + (p.accessory_id || '')">
                                <tr>
                                    <td x-text="p.kategorie"></td>
                                    <td x-text="(p.hersteller ? p.hersteller + ' ' : '') + (p.name || '')"></td>
                                    <td x-text="p.herst_artikelnr || '—'"></td>
                                    <td class="text-muted"><small x-text="p.begruendung"></small></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>

                    <template x-if="kompat && kompat.voreinstellstufe !== null">
                        <p class="mb-1">Voreinstellstufe: <strong x-text="kompat.voreinstellstufe"></strong></p>
                    </template>

                    <template x-for="h in (kompat?.hinweise || [])" :key="h">
                        <div class="alert alert-info mb-1" x-text="h"></div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('style')
<style>[x-cloak]{display:none !important;}</style>
@endpush

@push('scripts')
<script>
    function heizkoerperKonfigurator(config) {
        return {
            urls: config.urls,
            csrf: document.querySelector('meta[name="csrf-token"]')?.content || '',
            form: {
                installation_id: '', radiator_spec_id: '', baulaenge_mm: 1000, anzahl: 1,
                vorlauf: 55, raumtemp: 20, spreizung: 10, heizlast_w: '',
                ist_ventil_heizkoerper: false, anschluss_fuehrung: 'zweirohr', kopf_norm_bestand: '',
            },
            result: null,
            kompat: null,
            fieldErrors: {},
            generalError: '',
            loadingBerechnen: false,
            loadingKompat: false,

            async berechnen() {
                this.loadingBerechnen = true; this.generalError = ''; this.fieldErrors = {};
                try {
                    const res = await this.post(this.urls.berechnen, this.berechnenPayload());
                    if (res.status === 422) { this.fieldErrors = (await res.json()).errors || {}; this.result = null; return; }
                    if (!res.ok) { this.generalError = 'Berechnung fehlgeschlagen (Status ' + res.status + ').'; this.result = null; return; }
                    this.result = await res.json();
                } catch (e) {
                    this.generalError = 'Netzwerkfehler – bitte erneut versuchen.'; this.result = null;
                } finally { this.loadingBerechnen = false; }
            },

            async pruefeKompatibilitaet() {
                this.loadingKompat = true; this.generalError = ''; this.fieldErrors = {};
                try {
                    const res = await this.post(this.urls.kompatibilitaet, this.kompatPayload());
                    if (res.status === 422) { this.fieldErrors = (await res.json()).errors || {}; this.kompat = null; return; }
                    if (!res.ok) { this.generalError = 'Kompatibilitätsprüfung fehlgeschlagen (Status ' + res.status + ').'; this.kompat = null; return; }
                    this.kompat = await res.json();
                } catch (e) {
                    this.generalError = 'Netzwerkfehler – bitte erneut versuchen.'; this.kompat = null;
                } finally { this.loadingKompat = false; }
            },

            post(url, body) {
                return fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
                    body: JSON.stringify(body),
                });
            },

            berechnenPayload() {
                const f = this.form, p = { vorlauf: f.vorlauf, raumtemp: f.raumtemp, spreizung: f.spreizung };
                if (f.installation_id) p.installation_id = f.installation_id;
                if (f.radiator_spec_id) p.radiator_spec_id = f.radiator_spec_id;
                if (f.baulaenge_mm) p.baulaenge_mm = f.baulaenge_mm;
                if (f.anzahl) p.anzahl = f.anzahl;
                if (f.heizlast_w !== '') p.heizlast_w = f.heizlast_w;
                return p;
            },

            kompatPayload() {
                const f = this.form, p = {
                    ist_ventil_heizkoerper: f.ist_ventil_heizkoerper,
                    anschluss_fuehrung: f.anschluss_fuehrung,
                    spreizung: f.spreizung,
                };
                if (f.installation_id) p.installation_id = f.installation_id;
                if (f.kopf_norm_bestand) p.kopf_norm_bestand = f.kopf_norm_bestand;
                if (f.heizlast_w !== '') p.heizlast_w = f.heizlast_w;
                return p;
            },

            ampelClass(a) { return { gruen: 'badge-success', gelb: 'badge-warning', rot: 'badge-danger', na: 'badge-secondary' }[a] || 'badge-secondary'; },
            ampelText(a) { return { gruen: 'Deckt (grün)', gelb: 'Knapp (gelb)', rot: 'Unterdeckung (rot)', na: '—' }[a] || a; },
            fieldError(name) { return this.fieldErrors[name] ? this.fieldErrors[name][0] : ''; },
        };
    }
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>
@endpush
