<?php

namespace App\Http\Controllers\Energie;

use App\Http\Controllers\Controller;
use App\Models\HeizlastBauteil;
use App\Models\HeizlastProjekt;
use App\Models\HeizlastRaum;
use App\Models\SanierungsVariante;
use App\Repositories\CatalogDeviceRepository;
use App\Services\Energie\KostenService;
use App\Services\Energie\PvgisErtragService;
use App\Services\Heizlast\FoerderungService;
use App\Services\Heizlast\HeizlastEingabe;
use App\Services\Heizlast\HeizlastKonstanten;
use App\Services\Heizlast\JazService;
use App\Services\Heizlast\SanierungsWirtschaftlichkeitService;
use App\Services\Heizlast\WarmwasserService;
use App\Services\Klima\KlimaPlzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Energiekonzept-Assembler (Strang Energie).
 *
 * Fasst die BESTEHENDEN Rechenketten WP-Auslegung (EnergieAuslegungController::wpErgebnis)
 * und Sanierungs-Wirtschaftlichkeit (SanierungController::sanierungErgebnis) zu EINEM
 * kundenfertigen Energiekonzept zusammen. Beide Teile sind OPTIONAL (per Checkbox
 * ein-/ausblendbar). Keine eigene Physik/Förderlogik — reiner Durchstich über die
 * portierten Kerne. Server-gerendert (Blade + Bootstrap/Vuexy + jQuery, KEIN Alpine).
 */
class EnergiekonzeptController extends Controller
{
    /** Auswählbare Bauteil-Typen (Sanierungs-Teil, Vokabular wie SanierungController). */
    private const BAUTEIL_TYPEN = ['wand', 'dach', 'decke', 'boden', 'fenster', 'tuer'];

    /** Auswählbare Grenzflächen eines Bauteils. */
    private const GRENZFLAECHEN = ['aussen', 'erdreich', 'unbeheizt'];

    public function __construct(
        private CatalogDeviceRepository $repo = new CatalogDeviceRepository,
        private JazService $jaz = new JazService,
        private WarmwasserService $ww = new WarmwasserService,
        private KostenService $kosten = new KostenService,
        private FoerderungService $foerderung = new FoerderungService,
        private ?SanierungsWirtschaftlichkeitService $sanierung = null,
        private ?KlimaPlzService $klimaPlz = null,
        private ?PvgisErtragService $pvgis = null,
    ) {
        $this->sanierung = $sanierung ?? app(SanierungsWirtschaftlichkeitService::class);
        $this->klimaPlz = $klimaPlz ?? app(KlimaPlzService::class);
        $this->pvgis = $pvgis ?? app(PvgisErtragService::class);
    }

    /** GET energie.energiekonzept → leeres Formular (Kunde + WP-Abschnitt + Sanierungs-Abschnitt). */
    public function index()
    {
        return view('admin.energie.energiekonzept', [
            'wpOptions' => $this->wpOptions(),
            'bauteilTypen' => self::BAUTEIL_TYPEN,
            'grenzflaechen' => self::GRENZFLAECHEN,
            'wpDefaults' => $this->wpDefaults(),
            'konzept' => null,
            'fehler' => null,
        ]);
    }

    /** POST energie.energiekonzept.berechnen → Ergebnis-Vorschau (Gesamt + WP-/Sanierungs-Block). */
    public function berechnen(Request $request)
    {
        $data = $this->validieren($request);

        try {
            $konzept = $this->baueKonzept($data);
        } catch (\Throwable $e) {
            return view('admin.energie.energiekonzept', [
                'wpOptions' => $this->wpOptions(),
                'bauteilTypen' => self::BAUTEIL_TYPEN,
                'grenzflaechen' => self::GRENZFLAECHEN,
                'wpDefaults' => $this->wpDefaults(),
                'konzept' => null,
                'fehler' => 'Berechnung fehlgeschlagen: '.$e->getMessage(),
            ]);
        }

        return view('admin.energie.energiekonzept', [
            'wpOptions' => $this->wpOptions(),
            'bauteilTypen' => self::BAUTEIL_TYPEN,
            'grenzflaechen' => self::GRENZFLAECHEN,
            'wpDefaults' => $this->wpDefaults(),
            'konzept' => $konzept,
            'fehler' => null,
        ]);
    }

    /**
     * POST energie.energiekonzept.dokument → kundenfertiges Beratungs-/PDF-Dokument.
     * Gleiche Rechnung wie berechnen(); die Dokument-View baut ein anderer Agent —
     * verlässt sich nur auf die $konzept-Struktur.
     */
    public function dokument(Request $request)
    {
        $data = $this->validieren($request);

        try {
            $konzept = $this->baueKonzept($data);
        } catch (\Throwable $e) {
            return view('admin.energie.energiekonzept', [
                'wpOptions' => $this->wpOptions(),
                'bauteilTypen' => self::BAUTEIL_TYPEN,
                'grenzflaechen' => self::GRENZFLAECHEN,
                'wpDefaults' => $this->wpDefaults(),
                'konzept' => null,
                'fehler' => 'Berechnung fehlgeschlagen: '.$e->getMessage(),
            ]);
        }

        return view('admin.energie.energiekonzept_dokument', ['konzept' => $konzept]);
    }

    /**
     * Validierung für Berechnung und Dokument. WP- und Sanierungs-Felder sind nur
     * verpflichtend, wenn der jeweilige Teil aktiviert ist (required_if).
     *
     * @return array<string, mixed>
     */
    private function validieren(Request $request): array
    {
        $typen = implode(',', self::BAUTEIL_TYPEN);
        $grenz = implode(',', self::GRENZFLAECHEN);

        return $request->validate([
            'kunde.name' => ['nullable', 'string', 'max:190'],
            'kunde.plz' => ['nullable', 'string', 'max:10'],
            'kunde.ort' => ['nullable', 'string', 'max:190'],
            'kunde.berater' => ['nullable', 'string', 'max:190'],

            'wp_aktiv' => ['nullable', 'boolean'],
            'wp.wp_index' => ['nullable', 'required_if:wp_aktiv,1', 'integer', 'min:0'],
            'wp.heizlast_kw' => ['nullable', 'required_if:wp_aktiv,1', 'numeric', 'min:0.1', 'max:100'],
            'wp.heizsystem' => ['nullable', 'required_if:wp_aktiv,1', 'in:fussbodenheizung,heizkoerper,beides'],
            'wp.wp_typ' => ['nullable', 'required_if:wp_aktiv,1', 'in:luft_wasser,sole_sonde,sole_kollektor,wasser_wasser'],
            'wp.personen_im_haushalt' => ['nullable', 'required_if:wp_aktiv,1', 'integer', 'min:1', 'max:20'],
            'wp.ww_mit_wp' => ['nullable', 'boolean'],
            'wp.badewanne_vorhanden' => ['nullable', 'boolean'],
            'wp.investition' => ['nullable', 'required_if:wp_aktiv,1', 'numeric', 'min:0'],
            'wp.heizungsart' => ['nullable', 'in:oel,gas,fluessiggas,kohle,nacht,holz,fernwaerme,keine'],
            'wp.heizung_alter' => ['nullable', 'integer', 'min:0', 'max:120'],
            'wp.anzahl_we' => ['nullable', 'integer', 'min:1'],
            'wp.selbst_bewohnte_we' => ['nullable', 'integer', 'min:0'],
            'wp.effizienzbonus' => ['nullable', 'boolean'],
            'wp.einkommensbonus' => ['nullable', 'boolean'],
            'wp.strompreis' => ['nullable', 'numeric', 'min:0'],

            'sanierung_aktiv' => ['nullable', 'boolean'],
            'projekt.standort_plz' => ['nullable', 'string', 'max:10'],
            'projekt.baujahr' => ['nullable', 'integer', 'min:1800', 'max:2100'],
            'projekt.ziel_vorlauf_c' => ['nullable', 'numeric', 'min:20', 'max:90'],
            'projekt.spreizung_k' => ['nullable', 'numeric', 'min:1', 'max:30'],

            'raum.grundflaeche_m2' => ['nullable', 'required_if:sanierung_aktiv,1', 'numeric', 'min:1'],
            'raum.hoehe_m' => ['nullable', 'required_if:sanierung_aktiv,1', 'numeric', 'min:1', 'max:10'],
            'raum.theta_int_c' => ['nullable', 'numeric', 'min:10', 'max:30'],
            'raum.luftwechsel_1h' => ['nullable', 'numeric', 'min:0', 'max:5'],

            'bauteile' => ['nullable', 'required_if:sanierung_aktiv,1', 'array', 'min:1'],
            'bauteile.*.typ' => ['required_with:bauteile', "in:$typen"],
            'bauteile.*.grenzflaeche' => ['required_with:bauteile', "in:$grenz"],
            'bauteile.*.flaeche_m2' => ['required_with:bauteile', 'numeric', 'min:0.1'],
            'bauteile.*.u_wert' => ['required_with:bauteile', 'numeric', 'min:0.01', 'max:10'],

            'massnahmen' => ['nullable', 'array'],
            'massnahmen.*.ziel_typ' => ['required_with:massnahmen', "in:$typen"],
            'massnahmen.*.u_neu' => ['required_with:massnahmen', 'numeric', 'min:0.01', 'max:10'],
            'massnahmen.*.kosten_brutto' => ['required_with:massnahmen', 'numeric', 'min:0'],

            'annahmen.energiepreis_ct_kwh' => ['nullable', 'numeric', 'min:0'],
            'annahmen.isfp' => ['nullable'],
            'annahmen.anzahl_we' => ['nullable', 'integer', 'min:1'],

            'pv_aktiv' => ['nullable', 'boolean'],
            'pv.kwp' => ['nullable', 'required_if:pv_aktiv,1', 'numeric', 'min:0', 'max:1000'],
            'pv.angle' => ['nullable', 'numeric', 'min:0', 'max:90'],
            'pv.aspect' => ['nullable', 'numeric', 'min:-180', 'max:180'],
            'pv.investition' => ['nullable', 'numeric', 'min:0'],
            'pv.strompreis' => ['nullable', 'numeric', 'min:0'],
            'pv.einspeiseverguetung' => ['nullable', 'numeric', 'min:0'],
            'pv.eigenverbrauch_pct' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pv.lat' => ['nullable', 'numeric', 'min:-90', 'max:90'],
            'pv.lon' => ['nullable', 'numeric', 'min:-180', 'max:180'],
        ]);
    }

    /**
     * Baut das $konzept: WP-Teil (falls aktiviert) über die WP-Kette, Sanierungs-Teil
     * (falls aktiviert) über die Sanierungs-Kette, plus die Gesamt-Summen. Gemeinsame
     * Wahrheit für Vorschau (berechnen) und Dokument (dokument).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function baueKonzept(array $data): array
    {
        $kunde = $data['kunde'] ?? [];
        $wpAktiv = (bool) ($data['wp_aktiv'] ?? false);
        $sanAktiv = (bool) ($data['sanierung_aktiv'] ?? false);

        $pvAktiv = (bool) ($data['pv_aktiv'] ?? false);

        $wp = $wpAktiv ? $this->baueWp($data['wp'] ?? []) : null;
        $sanierung = $sanAktiv ? $this->baueSanierung($data) : null;
        $pv = $pvAktiv ? $this->bauePv($data['pv'] ?? [], $data['projekt'] ?? [], $kunde) : null;

        // Gesamt-Summen: WP-Netto-Investition + Sanierungs-(Brutto-)Investition; Förderungen addiert.
        $wpInvest = (float) ($wp['investition_netto'] ?? 0);
        $sanInvest = (float) ($sanierung['wirtschaftlichkeit']['investition_eur'] ?? 0);
        $wpFoerd = (float) ($wp['foerderung']['zuschuss'] ?? 0);
        $sanFoerd = (float) ($sanierung['wirtschaftlichkeit']['foerderung_eur'] ?? 0);

        // PV: additiv in die Gesamt-Summe — Investition erhöht, PV-Ertrag zählt als
        // jährliche Ersparnis. Für PV gibt es hier keine Förderung (PV-Förderung = 0).
        $pvInvest = (float) ($pv['investition_eur'] ?? 0);
        $pvErtrag = (float) ($pv['ertrag_gesamt_eur_a'] ?? 0);

        $investitionGesamt = $wpInvest + $sanInvest + $pvInvest;
        $foerderungGesamt = $wpFoerd + $sanFoerd;
        $einsparungGesamt = (float) ($sanierung['wirtschaftlichkeit']['einsparung_eur_a'] ?? 0) + $pvErtrag;

        return [
            'kunde' => [
                'name' => $kunde['name'] ?? null,
                'plz' => $kunde['plz'] ?? null,
                'ort' => $kunde['ort'] ?? null,
                'berater' => $kunde['berater'] ?? null,
            ],
            'datum' => now()->format('d.m.Y'),
            'wp' => $wp,
            'sanierung' => $sanierung,
            'pv' => $pv,
            'gesamt' => [
                'investition_eur' => round($investitionGesamt),
                'foerderung_eur' => round($foerderungGesamt),
                'eigenanteil_eur' => round($investitionGesamt - $foerderungGesamt),
                'einsparung_eur_a' => round($einsparungGesamt),
            ],
        ];
    }

    /**
     * WP-Teil über GENAU die WP-Rechenkette aus EnergieAuslegungController::wpErgebnis.
     * Liefert die Contract-Teilstruktur (oder null, wenn die gewählte WP nicht existiert).
     *
     * @param  array<string, mixed>  $in
     * @return array<string, mixed>|null
     */
    private function baueWp(array $in): ?array
    {
        $defaults = $this->wpDefaults();
        $wpIndex = (int) ($in['wp_index'] ?? 0);

        $hp = $this->repo->heatPumps()->values()->get($wpIndex);
        if ($hp === null) {
            return null;
        }

        $wwMitWp = (bool) ($in['ww_mit_wp'] ?? $defaults['ww_mit_wp']);
        $badewanne = (bool) ($in['badewanne_vorhanden'] ?? false);
        $effizienzbonus = (bool) ($in['effizienzbonus'] ?? false);
        $einkommensbonus = (bool) ($in['einkommensbonus'] ?? false);
        $strompreis = isset($in['strompreis']) && $in['strompreis'] !== '' ? (float) $in['strompreis'] : 0.30;
        $heizungsart = $in['heizungsart'] ?? $defaults['heizungsart'];
        $heizungAlter = (int) ($in['heizung_alter'] ?? $defaults['heizung_alter']);
        $anzahlWe = (int) ($in['anzahl_we'] ?? 1);
        $selbstWe = (int) ($in['selbst_bewohnte_we'] ?? 1);
        $heizlastKw = (float) ($in['heizlast_kw'] ?? $defaults['heizlast_kw']);

        // HeizlastEingabe-DTO (direkte Heizlast-Übergabe = Methode „direkt") — wie wpErgebnis.
        $e = HeizlastEingabe::fromArray([
            'methode' => 'direkt',
            'phi_hl_kw' => $heizlastKw,
            'heizsystem' => $in['heizsystem'] ?? $defaults['heizsystem'],
            'wp_typ' => $in['wp_typ'] ?? $defaults['wp_typ'],
            'personen_im_haushalt' => (int) ($in['personen_im_haushalt'] ?? $defaults['personen_im_haushalt']),
            'ww_mit_wp' => $wwMitWp,
            'badewanne_vorhanden' => $badewanne,
            'alter_heizung_jahre' => $heizungAlter,
            'strompreis' => $strompreis,
        ]);

        $jaz = $this->jaz->jaz($e);
        $qWwKwh = $this->ww->qWwKwh($e);
        $qHeizKwh = $heizlastKw * HeizlastKonstanten::B_VH_DEFAULT;
        $stromKwh = $this->jaz->stromverbrauch($e, $qHeizKwh, $qWwKwh);
        $stromkostenJahr = $stromKwh * $strompreis;

        // Investitionssumme (KostenService – eine Wahrheit für die förderfähigen Kosten).
        $kosten = $this->kosten->berechne([], null, null, (float) ($in['investition'] ?? 0));
        $investitionNetto = $kosten['summe_netto'];

        // KfW/BEG-Förderung (FoerderungService::berechne) — Aufruf wie in wpErgebnis.
        $foerderung = $this->foerderung->berechne([
            'foerderfaehige_kosten' => $investitionNetto,
            'anzahl_we' => $anzahlWe,
            'selbst_bewohnte_we' => $selbstWe,
            'we_unter_40k' => $einkommensbonus ? min($selbstWe, 1) : 0,
            'heizungsart' => $heizungsart === 'keine' ? null : $heizungsart,
            'heizung_alter' => $heizungAlter,
            'effizienzbonus' => $effizienzbonus,
            'zusatz' => 0,
            'rabatt' => 0,
        ]);

        return [
            'hersteller' => $hp->hersteller,
            'modell' => $hp->modell,
            'jaz' => round($jaz, 2),
            'strom_kwh' => round($stromKwh),
            'stromkosten_jahr' => round($stromkostenJahr),
            'investition_netto' => $investitionNetto,
            'foerderung' => [
                'zuschuss' => $foerderung['zuschuss'],
                'effektiver_satz_pct' => $foerderung['effektiver_satz_pct'],
                'netto_investition' => $foerderung['netto_investition'],
            ],
        ];
    }

    /**
     * Sanierungs-Teil über GENAU die Kette aus SanierungController: transientes
     * Heizlast-Projekt (Gebäude = 1 Raum) + Bauteile + Variante bauen,
     * SanierungsWirtschaftlichkeitService::berechneVergleich(), Projekt wieder verwerfen.
     * Liefert die Contract-Teilstruktur.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>|null
     */
    private function baueSanierung(array $data): ?array
    {
        $projektIn = $data['projekt'] ?? [];
        $raumIn = $data['raum'] ?? [];
        $bauteileIn = $data['bauteile'] ?? [];
        $massnahmenIn = $data['massnahmen'] ?? [];
        $annahmenIn = $data['annahmen'] ?? [];

        if (empty($bauteileIn) || empty($raumIn['grundflaeche_m2'])) {
            return null;
        }

        $plz = (string) ($projektIn['standort_plz'] ?? '');
        $normAussentemp = ($plz !== '' ? $this->klimaPlz->getNormAussentempForPlz($plz) : null) ?? -8.5;

        $ergebnis = DB::transaction(function () use (
            $projektIn, $raumIn, $bauteileIn, $massnahmenIn, $annahmenIn, $plz, $normAussentemp
        ) {
            $projekt = HeizlastProjekt::create([
                'name' => 'Energiekonzept-Sanierung',
                'standort_plz' => $plz !== '' ? $plz : null,
                'norm_aussentemp_c' => $normAussentemp,
                'baujahr' => $projektIn['baujahr'] ?? null,
                'sanierungsstufe' => 'unsaniert',
                // Schema-Defaults (Migration: ziel_vorlauf_c=45, spreizung_k=7) explizit setzen,
                // damit der Assembler nicht auf eine ggf. nicht angewandte DB-Default-Klausel angewiesen ist.
                'ziel_vorlauf_c' => $projektIn['ziel_vorlauf_c'] ?? 45,
                'spreizung_k' => $projektIn['spreizung_k'] ?? 7,
            ]);

            $raum = HeizlastRaum::create([
                'heizlast_projekt_id' => $projekt->getKey(),
                'name' => 'Gebäude',
                'nutzung' => 'wohnen',
                'theta_int_c' => $raumIn['theta_int_c'] ?? null,
                'grundflaeche_m2' => $raumIn['grundflaeche_m2'],
                'hoehe_m' => $raumIn['hoehe_m'],
                'luftwechsel_1h' => $raumIn['luftwechsel_1h'] ?? null,
            ]);

            foreach ($bauteileIn as $b) {
                HeizlastBauteil::create([
                    'heizlast_raum_id' => $raum->getKey(),
                    'typ' => $b['typ'],
                    'grenzflaeche' => $b['grenzflaeche'],
                    'flaeche_m2' => $b['flaeche_m2'],
                    'u_strategie' => 'A',
                    'u_wert' => $b['u_wert'],
                ]);
            }

            $massnahmen = [];
            foreach ($massnahmenIn as $m) {
                $massnahmen[] = [
                    'ziel' => ['typ' => $m['ziel_typ']],
                    'u_neu' => (float) $m['u_neu'],
                    'kosten_brutto' => (float) $m['kosten_brutto'],
                ];
            }

            $variante = SanierungsVariante::create([
                'heizlast_projekt_id' => $projekt->getKey(),
                'label' => 'Sanierung',
                'massnahmen' => $massnahmen,
            ]);

            $r = $this->sanierung->berechneVergleich($projekt->fresh(), $variante, [
                'energiepreis_ct_kwh' => (float) ($annahmenIn['energiepreis_ct_kwh'] ?? 12.0),
                'isfp' => (bool) ($annahmenIn['isfp'] ?? false),
                'anzahl_we' => (int) ($annahmenIn['anzahl_we'] ?? 1),
            ]);

            // TRANSIENT: Rechner-Tool — Projekt (Cascade: Raum/Bauteile/Variante) wieder verwerfen.
            $projekt->delete();

            return $r;
        });

        return [
            'delta' => [
                'phi_hl_kw' => $ergebnis['delta']['phi_hl_kw'],
                'phi_hl_pct' => $ergebnis['delta']['phi_hl_pct'],
                'q_a_kwh' => $ergebnis['delta']['q_a_kwh'],
            ],
            'wirtschaftlichkeit' => [
                'investition_eur' => $ergebnis['wirtschaftlichkeit']['investition_eur'],
                'foerderung_eur' => $ergebnis['wirtschaftlichkeit']['foerderung_eur'],
                'netto_investition_eur' => $ergebnis['wirtschaftlichkeit']['netto_investition_eur'],
                'einsparung_eur_a' => $ergebnis['wirtschaftlichkeit']['einsparung_eur_a'],
                'amortisation_jahre' => $ergebnis['wirtschaftlichkeit']['amortisation_jahre'],
                'gewinn_30j_eur' => $ergebnis['wirtschaftlichkeit']['gewinn_30j_eur'],
            ],
            'foerderung' => [
                'quote_pct' => $ergebnis['foerderung']['quote_pct'],
            ],
        ];
    }

    /**
     * PV-Teil (optional). Holt den PVGIS-Jahresertrag über den kanonischen
     * PvgisErtragService (EINE Wahrheit; kein zweites PVGIS-Tool) mit graceful
     * Fallback und rechnet die einfache PV-Wirtschaftlichkeit (Eigenverbrauch-
     * Ersparnis + Einspeise-Vergütung). Koordinaten: direkte lat/lon-Eingabe
     * (Vorrang) oder Auflösung der Standort-PLZ über `klima_plz`; ohne beides
     * greift der Fallback-Spezifischertrag (keine neue Geocoding-API).
     *
     * @param  array<string, mixed>  $in  PV-Eingaben
     * @param  array<string, mixed>  $projekt  Projekt-Eingaben (für standort_plz)
     * @param  array<string, mixed>  $kunde  Kunde-Eingaben (PLZ-Fallback)
     * @return array<string, mixed>|null
     */
    private function bauePv(array $in, array $projekt, array $kunde): ?array
    {
        $kwp = (float) ($in['kwp'] ?? 0);
        if ($kwp <= 0) {
            return null;
        }

        $angle = isset($in['angle']) && $in['angle'] !== '' ? (float) $in['angle'] : 30.0;
        $aspect = isset($in['aspect']) && $in['aspect'] !== '' ? (float) $in['aspect'] : 0.0;
        $investition = (float) ($in['investition'] ?? 0);
        $strompreis = isset($in['strompreis']) && $in['strompreis'] !== '' ? (float) $in['strompreis'] : 0.30;
        $einspeise = isset($in['einspeiseverguetung']) && $in['einspeiseverguetung'] !== '' ? (float) $in['einspeiseverguetung'] : 0.08;
        $eigenverbrauchPct = isset($in['eigenverbrauch_pct']) && $in['eigenverbrauch_pct'] !== '' ? (float) $in['eigenverbrauch_pct'] : 30.0;

        $lat = isset($in['lat']) && $in['lat'] !== '' ? (float) $in['lat'] : null;
        $lon = isset($in['lon']) && $in['lon'] !== '' ? (float) $in['lon'] : null;
        $plz = (string) ($projekt['standort_plz'] ?? $kunde['plz'] ?? '');

        $ertrag = $this->pvgis->jahresertragFuerStandort($plz !== '' ? $plz : null, $lat, $lon, $kwp, $angle, $aspect);
        $jahresertragKwh = (float) $ertrag['jahresertrag_kwh'];

        $eigenverbrauchKwh = $jahresertragKwh * $eigenverbrauchPct / 100;
        $einspeisungKwh = $jahresertragKwh - $eigenverbrauchKwh;
        $ersparnisEigenverbrauch = $eigenverbrauchKwh * $strompreis;
        $verguetung = $einspeisungKwh * $einspeise;
        $ertragGesamt = $ersparnisEigenverbrauch + $verguetung;

        return [
            'kwp' => $kwp,
            'spezifischer_ertrag' => round((float) $ertrag['spezifischer_ertrag'], 1),
            'jahresertrag_kwh' => round($jahresertragKwh),
            'quelle' => $ertrag['quelle'],
            'investition_eur' => round($investition),
            'eigenverbrauch_pct' => $eigenverbrauchPct,
            'strompreis' => $strompreis,
            'einspeiseverguetung' => $einspeise,
            'ersparnis_eigenverbrauch_eur_a' => round($ersparnisEigenverbrauch),
            'verguetung_einspeisung_eur_a' => round($verguetung),
            'ertrag_gesamt_eur_a' => round($ertragGesamt),
        ];
    }

    /**
     * Auswahl-Optionen für Wärmepumpen, index-aligned zu repo->heatPumps().
     *
     * @return array<int, array{index:int, label:string}>
     */
    private function wpOptions(): array
    {
        $out = [];
        foreach ($this->repo->heatPumps()->values() as $i => $h) {
            $name = trim(($h->hersteller ?? '').' '.($h->modell ?? ''));
            $heiz = $h->heizleistung_a7_w35_kw !== null ? ' · '.$h->heizleistung_a7_w35_kw.' kW (A7/W35)' : '';
            $label = 'WP #'.($i + 1).' — '.($name !== '' ? $name : 'Gerät').$heiz;
            $out[$i] = ['index' => $i, 'label' => $label];
        }

        return $out;
    }

    /** @return array<string, mixed> Formular-Defaults für den WP-Abschnitt. */
    private function wpDefaults(): array
    {
        return [
            'wp_index' => null,
            'heizlast_kw' => 8.0,
            'heizsystem' => 'heizkoerper',
            'wp_typ' => 'luft_wasser',
            'personen_im_haushalt' => 4,
            'ww_mit_wp' => true,
            'badewanne_vorhanden' => false,
            'investition' => 25000,
            'heizungsart' => 'gas',
            'heizung_alter' => 25,
            'anzahl_we' => 1,
            'selbst_bewohnte_we' => 1,
            'effizienzbonus' => false,
            'einkommensbonus' => false,
            'strompreis' => 0.30,
        ];
    }
}
