<?php

namespace App\Http\Controllers\Customer\Deal;

use App\Http\Controllers\Controller;
use App\Models\Deal;
use Illuminate\Http\Request;

/**
 * Pipeline (Abwicklung) — Welle B2 (2026-07-16). Board über den Durchführungsstand.
 *
 * EINE Wahrheit (Planner-Vorlage docs/planner-vorlage-pipeline-abwicklung.md):
 * die Fläche liest AUSSCHLIESSLICH deals.project_status. deal_status (kaufmännisch),
 * LeadStage (Vertriebs-Funnel) und measurement_status (Teilschritt) bleiben unberührt;
 * deals.status (Alt-Doppelspur) wird bewusst ignoriert.
 *
 * V1 = LESEND: kein Drag&Drop, kein Statuswechsel hier — der bleibt in den Workflows,
 * die project_status heute schreiben (kein zweiter Schreibpfad). Operanden-Gate:
 * unbekannte/leere Statuswerte verschwinden NICHT still, sondern landen sichtbar in
 * „Sonstige"/„Ohne Status". Performance: EINE Query mit select()+minimalem Eager-Load
 * (kein N+1), Gruppierung in PHP, je Spalte gedeckelt mit sichtbarem „+N weitere".
 */
class PipelineController extends Controller
{
    /** Bekannte Stufen in Prozessreihenfolge: key => [Label, Ton]. */
    private const STUFEN = [
        'new'         => ['Neu', 'info'],
        'planned'     => ['Geplant', 'info'],
        'start'       => ['Gestartet', 'info'],
        'measurement' => ['Aufmaß', 'warning'],
        'on_going'    => ['In Arbeit', 'accent'],
        'on_review'   => ['In Prüfung', 'warning'],
        'pause'       => ['Pausiert', 'danger'],
        'completed'   => ['Abgeschlossen', 'success'],
    ];

    /** cancel ist keine Board-Spalte, sondern nur ein Zähler (beendete Vorgänge verstopfen nicht). */
    private const ABGEBROCHEN = 'cancel';

    /** Karten je Spalte, damit das Board bei vielen Aufträgen tragfähig bleibt (kein stiller Cap). */
    private const CAP_JE_SPALTE = 60;

    public function pipeline(Request $request)
    {
        $deals = Deal::query()
            ->select(['id', 'customer_id', 'alternative_id', 'product_id', 'price', 'project_status', 'order_number', 'updated_at'])
            ->with([
                'customer:id,name,lastname,firma',
                'alternative:id,object_name',
                'product:id,article_group', // B2-1-Fix: ArticleGroup-Namensspalte ist article_group, nicht name
            ])
            ->orderByDesc('updated_at')
            ->get();

        // Spalten in fester Reihenfolge vorbereiten (auch leere Stufen zeigen den Prozess).
        $spalten = [];
        foreach (self::STUFEN as $key => [$label, $ton]) {
            $spalten[$key] = ['key' => $key, 'label' => $label, 'ton' => $ton, 'karten' => [], 'anzahl' => 0];
        }
        $sonstige = ['key' => '_sonstige', 'label' => 'Sonstige', 'ton' => 'info', 'karten' => [], 'anzahl' => 0];
        $ohneStatus = ['key' => '_ohne', 'label' => 'Ohne Status', 'ton' => 'info', 'karten' => [], 'anzahl' => 0];
        $abgebrochen = 0;

        foreach ($deals as $deal) {
            $status = strtolower(trim((string) $deal->project_status));

            if ($status === self::ABGEBROCHEN) {
                $abgebrochen++;
                continue;
            }

            if ($status === '') {
                $ziel = &$ohneStatus;
            } elseif (isset($spalten[$status])) {
                $ziel = &$spalten[$status];
            } else {
                $ziel = &$sonstige; // Operanden-Gate: fremder Wert bleibt sichtbar, nie stiller Verlust
            }

            $ziel['anzahl']++;
            if (count($ziel['karten']) < self::CAP_JE_SPALTE) {
                $kunde = $deal->customer;
                $ziel['karten'][] = [
                    'id' => $deal->id,
                    'order_number' => $deal->order_number,
                    'kunde' => $kunde?->firma ?: trim(($kunde->name ?? '') . ' ' . ($kunde->lastname ?? '')),
                    'objekt' => $deal->alternative?->object_name,
                    'produkt' => $deal->product?->article_group,
                    'price' => $deal->price !== null ? (float) $deal->price : null,
                    'aktualisiert' => $deal->updated_at,
                ];
            }
            unset($ziel);
        }

        // Sonstige/Ohne-Status nur zeigen, wenn belegt (kein leeres Rauschen).
        $anzeige = array_values($spalten);
        if ($ohneStatus['anzahl'] > 0) {
            $anzeige[] = $ohneStatus;
        }
        if ($sonstige['anzahl'] > 0) {
            $anzeige[] = $sonstige;
        }

        return view('admin.deal.pipeline', [
            'spalten' => $anzeige,
            'abgebrochen' => $abgebrochen,
            'gesamt' => $deals->count() - $abgebrochen,
            'capJeSpalte' => self::CAP_JE_SPALTE,
        ]);
    }
}
