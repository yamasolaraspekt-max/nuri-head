<?php

namespace App\Http\Controllers\Energie;

use App\Http\Controllers\Controller;
use App\Repositories\CatalogDeviceRepository;
use App\Services\Energie\InverterSizingService;
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
