<?php

namespace App\Http\Controllers\Energie;

use App\Http\Controllers\Controller;
use App\Repositories\CatalogDeviceRepository;
use App\Services\Energie\InverterSizingService;
use App\Services\Energie\KostenService;
use App\Services\Heizlast\FoerderungService;
use App\Services\Heizlast\HeizlastEingabe;
use App\Services\Heizlast\HeizlastKonstanten;
use App\Services\Heizlast\JazService;
use App\Services\Heizlast\VerbrauchsService;
use App\Services\Heizlast\WarmwasserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Erste sichtbare Energie-Fläche: Wechselrichter-String-Auslegung.
 * Reiner server-gerenderter Durchstich über die portierten Kerne:
 * CatalogDeviceRepository (nur SELECT) -> InverterSizingService::bewerteWechselrichter().
 * Keine eigene Physik/Regel-Logik hier. Blade + Bootstrap/Vuexy, KEIN Alpine.
 */
class EnergieAuslegungController extends Controller
{
    public function __construct(
        private CatalogDeviceRepository $repo = new CatalogDeviceRepository,
        private InverterSizingService $service = new InverterSizingService,
        private JazService $jaz = new JazService,
        private WarmwasserService $ww = new WarmwasserService,
        private KostenService $kosten = new KostenService,
        private FoerderungService $foerderung = new FoerderungService,
    ) {}

    /** GET energie.wr-auslegung → Auswahl-Formular (Module/WR), Ergebnis leer. */
    public function index()
    {
        return view('admin.energie.wr_auslegung', [
            'moduleOptions' => $this->moduleOptions(),
            'inverterOptions' => $this->inverterOptions(),
            'eingabe' => ['module_index' => null, 'inverter_index' => null, 'module_gesamt' => 20, 'parallel_strings' => 1],
            'ergebnis' => null,
            'fehler' => null,
        ]);
    }

    /** POST energie.wr-auslegung.berechnen → server-gerendertes Ergebnis (Ampel + Regel-Liste). */
    public function berechnen(Request $request)
    {
        $data = $request->validate([
            'module_index' => ['required', 'integer', 'min:0'],
            'inverter_index' => ['required', 'integer', 'min:0'],
            'module_gesamt' => ['required', 'integer', 'min:1'],
            'parallel_strings' => ['nullable', 'integer', 'min:1'],
        ]);

        $modules = $this->repo->modules()->values();
        $inverters = $this->repo->inverters()->values();

        $modul = $modules->get((int) $data['module_index']);
        $wr = $inverters->get((int) $data['inverter_index']);

        $eingabe = [
            'module_index' => (int) $data['module_index'],
            'inverter_index' => (int) $data['inverter_index'],
            'module_gesamt' => (int) $data['module_gesamt'],
            'parallel_strings' => (int) ($data['parallel_strings'] ?? 1),
        ];

        $moduleOptions = $this->moduleOptions();
        $inverterOptions = $this->inverterOptions();

        if ($modul === null || $wr === null) {
            return view('admin.energie.wr_auslegung', [
                'moduleOptions' => $moduleOptions,
                'inverterOptions' => $inverterOptions,
                'eingabe' => $eingabe,
                'ergebnis' => null,
                'fehler' => 'Gewähltes PV-Modul oder Wechselrichter nicht gefunden. Bitte erneut auswählen.',
            ]);
        }

        $moduleGesamt = $eingabe['module_gesamt'];
        $parallelStrings = $eingabe['parallel_strings'];

        // Der Kern bewertet die Gesamtkonfiguration (String-Zahl leitet er selbst aus module_gesamt
        // ab, intern 1 String/MPPT). parallel_strings wird als Option durchgereicht.
        $opt = ['parallel_strings' => $parallelStrings];

        $bewertung = $this->service->bewerteWechselrichter($modul, $wr, $moduleGesamt, $opt);

        $ergebnis = [
            'ampel' => $bewertung['ampel'],            // 'gruen' | 'gelb' | 'rot' (== gesamtAmpel(regeln))
            'gueltig' => $bewertung['gueltig'],        // bool: gültiges Spannungsfenster
            'ratio' => $bewertung['ratio'],            // DC/AC-Verhältnis
            'p_dc_w' => $moduleGesamt * $modul->pmpp_wp,
            'module_gesamt' => $moduleGesamt,
            'parallel_strings' => $parallelStrings,
            'modul_label' => $moduleOptions[$eingabe['module_index']]['label'] ?? ('Modul #'.($eingabe['module_index'] + 1)),
            'wr_label' => $inverterOptions[$eingabe['inverter_index']]['label'] ?? ('WR #'.($eingabe['inverter_index'] + 1)),
            // formatRegeln() -> UI-Format: id, titel, norm, status(gruen/gelb/rot), wert_text, grenze_text
            'regeln' => $this->service->formatRegeln($bewertung['regeln']),
        ];

        return view('admin.energie.wr_auslegung', [
            'moduleOptions' => $moduleOptions,
            'inverterOptions' => $inverterOptions,
            'eingabe' => $eingabe,
            'ergebnis' => $ergebnis,
            'fehler' => null,
        ]);
    }

    // ── Wärmepumpen-Auslegung (Heizlast → JAZ/Verbrauch → Wirtschaftlichkeit + KfW/BEG) ──
    // Reiner Durchstich über die portierten Heizlast-/Energie-Kerne (nur SELECT im Repository):
    // JazService, WarmwasserService, VerbrauchsService, KostenService, FoerderungService.
    // Keine eigene Physik-/Förderlogik hier. Blade + Bootstrap/Vuexy, KEIN Alpine.

    /** GET energie.wp-auslegung → Auswahl-Formular (WP-Liste), Ergebnis leer. */
    public function wpIndex()
    {
        return view('admin.energie.wp_auslegung', [
            'wpOptions' => $this->wpOptions(),
            'eingabe' => $this->wpDefaults(),
            'ergebnis' => null,
            'fehler' => null,
        ]);
    }

    /** POST energie.wp-auslegung.berechnen → server-gerendertes Ergebnis (Kenndaten + Wirtschaftlichkeit). */
    public function wpBerechnen(Request $request)
    {
        $data = $request->validate([
            'wp_index' => ['required', 'integer', 'min:0'],
            'heizlast_kw' => ['required', 'numeric', 'min:0.1', 'max:100'],
            'heizsystem' => ['required', 'in:fussbodenheizung,heizkoerper,beides'],
            'wp_typ' => ['required', 'in:luft_wasser,sole_sonde,sole_kollektor,wasser_wasser'],
            'personen_im_haushalt' => ['required', 'integer', 'min:1', 'max:20'],
            'ww_mit_wp' => ['nullable', 'boolean'],
            'badewanne_vorhanden' => ['nullable', 'boolean'],
            'investition' => ['required', 'numeric', 'min:0'],
            'heizungsart' => ['nullable', 'in:oel,gas,fluessiggas,kohle,nacht,holz,fernwaerme,keine'],
            'heizung_alter' => ['nullable', 'integer', 'min:0', 'max:120'],
            'anzahl_we' => ['nullable', 'integer', 'min:1'],
            'selbst_bewohnte_we' => ['nullable', 'integer', 'min:0'],
            'effizienzbonus' => ['nullable', 'boolean'],
            'einkommensbonus' => ['nullable', 'boolean'],
            'strompreis' => ['nullable', 'numeric', 'min:0'],
            // optionale Plausibilisierung über die Verbrauchsmethode (Methode V)
            'verbrauch_menge' => ['nullable', 'numeric', 'min:0'],
            'verbrauch_einheit' => ['nullable', 'in:kWh,m3,Liter,kg,Ster'],
            'aktuelles_heizmedium' => ['nullable', 'in:erdgas,heizoel,fluessiggas,pellets,scheitholz,strom,fernwaerme'],
            'verbrauch_zeitraum_jahre' => ['nullable', 'integer', 'min:1', 'max:10'],
            'enthaelt_warmwasser' => ['nullable', 'boolean'],
        ]);

        $wwMitWp = $request->boolean('ww_mit_wp');
        $badewanne = $request->boolean('badewanne_vorhanden');
        $effizienzbonus = $request->boolean('effizienzbonus');
        $einkommensbonus = $request->boolean('einkommensbonus');
        $enthaeltWw = $request->boolean('enthaelt_warmwasser');
        $strompreis = isset($data['strompreis']) ? (float) $data['strompreis'] : 0.30;
        $heizungsart = $data['heizungsart'] ?? 'keine';
        $heizungAlter = (int) ($data['heizung_alter'] ?? 0);
        $anzahlWe = (int) ($data['anzahl_we'] ?? 1);
        $selbstWe = (int) ($data['selbst_bewohnte_we'] ?? 1);

        // Eingabe (auch für Repopulation des Formulars).
        $eingabe = array_merge($this->wpDefaults(), [
            'wp_index' => (int) $data['wp_index'],
            'heizlast_kw' => (float) $data['heizlast_kw'],
            'heizsystem' => $data['heizsystem'],
            'wp_typ' => $data['wp_typ'],
            'personen_im_haushalt' => (int) $data['personen_im_haushalt'],
            'ww_mit_wp' => $wwMitWp,
            'badewanne_vorhanden' => $badewanne,
            'investition' => (float) $data['investition'],
            'heizungsart' => $heizungsart,
            'heizung_alter' => $heizungAlter,
            'anzahl_we' => $anzahlWe,
            'selbst_bewohnte_we' => $selbstWe,
            'effizienzbonus' => $effizienzbonus,
            'einkommensbonus' => $einkommensbonus,
            'strompreis' => $strompreis,
            'verbrauch_menge' => isset($data['verbrauch_menge']) ? (float) $data['verbrauch_menge'] : null,
            'verbrauch_einheit' => $data['verbrauch_einheit'] ?? 'kWh',
            'aktuelles_heizmedium' => $data['aktuelles_heizmedium'] ?? 'erdgas',
            'verbrauch_zeitraum_jahre' => (int) ($data['verbrauch_zeitraum_jahre'] ?? 1),
            'enthaelt_warmwasser' => $enthaeltWw,
        ]);

        $wpOptions = $this->wpOptions();
        $hp = $this->repo->heatPumps()->values()->get((int) $data['wp_index']);

        if ($hp === null) {
            return view('admin.energie.wp_auslegung', [
                'wpOptions' => $wpOptions,
                'eingabe' => $eingabe,
                'ergebnis' => null,
                'fehler' => 'Gewählte Wärmepumpe nicht gefunden. Bitte erneut auswählen.',
            ]);
        }

        // HeizlastEingabe-DTO aus den Formulardaten (direkte Heizlast-Übergabe = Methode „direkt").
        $e = HeizlastEingabe::fromArray([
            'methode' => 'direkt',
            'phi_hl_kw' => (float) $data['heizlast_kw'],
            'heizsystem' => $data['heizsystem'],
            'wp_typ' => $data['wp_typ'],
            'personen_im_haushalt' => (int) $data['personen_im_haushalt'],
            'ww_mit_wp' => $wwMitWp,
            'badewanne_vorhanden' => $badewanne,
            'alter_heizung_jahre' => $heizungAlter,
            'strompreis' => $strompreis,
            // optionale Plausibilisierung
            'verbrauch_menge' => $eingabe['verbrauch_menge'],
            'verbrauch_einheit' => $eingabe['verbrauch_einheit'],
            'aktuelles_heizmedium' => $eingabe['aktuelles_heizmedium'],
            'verbrauch_zeitraum_jahre' => $eingabe['verbrauch_zeitraum_jahre'],
            'enthaelt_warmwasser' => $enthaeltWw,
        ]);

        // JAZ + Auslegungs-Vorlauf (JazService).
        $jaz = $this->jaz->jaz($e);
        $vorlaufTemp = $this->jaz->vorlaufTemp($e);

        // Warmwasser (WarmwasserService).
        $wwErgebnis = $this->ww->ergebnis($e);
        $qWwKwh = $this->ww->qWwKwh($e);

        // Jahres-Heizarbeit aus Heizlast × Vollbenutzungsstunden (Kern-Default) → WP-Stromverbrauch.
        $bvh = HeizlastKonstanten::B_VH_DEFAULT;
        $qHeizKwh = (float) $data['heizlast_kw'] * $bvh;
        $stromKwh = $this->jaz->stromverbrauch($e, $qHeizKwh, $qWwKwh);
        $stromkostenJahr = $stromKwh * $strompreis;

        // Optionale Verbrauchsmethode zur Plausibilisierung (null, wenn kein Verbrauch angegeben).
        $verbrauchPlausi = (new VerbrauchsService($this->ww))->berechne($e);

        // Investitionssumme (KostenService – eine Wahrheit für die förderfähigen Kosten).
        $kosten = $this->kosten->berechne([], null, null, (float) $data['investition']);
        $investitionNetto = $kosten['summe_netto'];

        // KfW/BEG-Förderung (FoerderungService::berechne).
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

        $ergebnis = [
            'wp_label' => $wpOptions[$eingabe['wp_index']]['label'] ?? ('WP #'.($eingabe['wp_index'] + 1)),
            'wp' => [
                'hersteller' => $hp->hersteller,
                'modell' => $hp->modell,
                'geraetetyp' => $hp->geraetetyp,
                'serie' => $hp->serie,
                'kaeltemittel' => $hp->kaeltemittel,
                'scop_35' => $hp->scop_35,
                'scop_55' => $hp->scop_55,
                'heizleistung_a7_w35_kw' => $hp->heizleistung_a7_w35_kw,
                'heizleistung_am7_w35_kw' => $hp->heizleistung_am7_w35_kw,
                'max_vorlauf_c' => $hp->max_vorlauf_c,
                'modulation_min_kw' => $hp->modulation_min_kw,
                'modulation_max_kw' => $hp->modulation_max_kw,
            ],
            'heizlast_kw' => (float) $data['heizlast_kw'],
            'heizsystem_label' => $this->heizsystemLabels()[$data['heizsystem']] ?? $data['heizsystem'],
            'wp_typ_label' => $this->wpTypLabels()[$data['wp_typ']] ?? $data['wp_typ'],
            'jaz' => round($jaz, 2),
            'vorlauf_temp' => $vorlaufTemp,
            'b_vh' => $bvh,
            'q_heiz_kwh' => round($qHeizKwh),
            'q_ww_kwh' => round($qWwKwh),
            'strom_kwh' => round($stromKwh),
            'strompreis' => $strompreis,
            'stromkosten_jahr' => round($stromkostenJahr),
            'ww' => $wwErgebnis,
            'ww_mit_wp' => $wwMitWp,
            'verbrauch_plausi' => $verbrauchPlausi,
            'investition_netto' => $investitionNetto,
            'foerderung' => $foerderung,
        ];

        return view('admin.energie.wp_auslegung', [
            'wpOptions' => $wpOptions,
            'eingabe' => $eingabe,
            'ergebnis' => $ergebnis,
            'fehler' => null,
        ]);
    }

    /**
     * Auswahl-Optionen für Wärmepumpen, index-aligned zu repo->heatPumps().
     * Label direkt aus dem DTO (hersteller/modell/heizleistung sind bereits gemappt).
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

    /** @return array<string, mixed> Formular-Defaults (auch Smoke-Vorbelegung). */
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
            'verbrauch_menge' => null,
            'verbrauch_einheit' => 'kWh',
            'aktuelles_heizmedium' => 'erdgas',
            'verbrauch_zeitraum_jahre' => 1,
            'enthaelt_warmwasser' => true,
        ];
    }

    /** @return array<string, string> */
    private function heizsystemLabels(): array
    {
        return [
            'fussbodenheizung' => 'Fußbodenheizung (Vorlauf ~35 °C)',
            'heizkoerper' => 'Heizkörper (Vorlauf ~55 °C)',
            'beides' => 'Gemischt (Vorlauf ~45 °C)',
        ];
    }

    /** @return array<string, string> */
    private function wpTypLabels(): array
    {
        return [
            'luft_wasser' => 'Luft/Wasser',
            'sole_sonde' => 'Sole/Wasser — Erdsonde',
            'sole_kollektor' => 'Sole/Wasser — Flächenkollektor',
            'wasser_wasser' => 'Wasser/Wasser',
        ];
    }

    /**
     * Auswahl-Optionen für PV-Module, index-aligned zu repo->modules().
     * Label aus products.model (parallele identische Query -> gleiche Reihenfolge) + pmpp_wp aus DTO.
     *
     * @return array<int, array{index:int, label:string}>
     */
    private function moduleOptions(): array
    {
        $modules = $this->repo->modules()->values();
        $rows = DB::table('product_pv_module_specs')->get()->values();

        $modelById = [];
        $ids = $rows->pluck('product_id')->filter()->all();
        if ($ids) {
            $modelById = DB::table('products')->whereIn('id', $ids)->pluck('model', 'id')->all();
        }

        $out = [];
        foreach ($modules as $i => $m) {
            $row = $rows[$i] ?? null;
            $model = ($row && isset($modelById[$row->product_id])) ? $modelById[$row->product_id] : null;
            $label = 'Modul #'.($i + 1).' — '.($model ? $model.' · ' : '').$m->pmpp_wp.' Wp';
            $out[$i] = ['index' => $i, 'label' => $label];
        }

        return $out;
    }

    /**
     * Auswahl-Optionen für Wechselrichter, index-aligned zu repo->inverters().
     * Label aus inverters.company + inverters.name (parallele identische Query) + p_ac_nenn_w aus DTO.
     *
     * @return array<int, array{index:int, label:string}>
     */
    private function inverterOptions(): array
    {
        $inverters = $this->repo->inverters()->values();
        $rows = DB::table('inverters')->get()->values();

        $out = [];
        foreach ($inverters as $i => $wr) {
            $row = $rows[$i] ?? null;
            $name = $row ? trim(($row->company ?? '').' '.($row->name ?? '')) : '';
            $label = 'WR #'.($i + 1).' — '.($name !== '' ? $name.' · ' : '').$wr->p_ac_nenn_w.' W';
            $out[$i] = ['index' => $i, 'label' => $label];
        }

        return $out;
    }
}
