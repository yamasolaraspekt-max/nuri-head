<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Materialentnahmen — Welle B2 (2026-07-16). Projektübergreifende Material-Übersicht.
 *
 * EHRLICHKEITS-GRENZE (Operanden-Gate): ticket hat KEIN separates Lager-Entnahme-Buch.
 * Die einzige reale Material-Bewegungs-Wahrheit ist der Anforderungs-/Ausgabe-Fluss
 * `planner_item_material_requests` (Status requested→accepted/rejected→ordered/…). Diese
 * Fläche liest genau diesen Fluss projektübergreifend — „Entnahme" meint hier die
 * angeforderte/freigegebene Materialausgabe je Projekt, nicht eine Lagerbestands-Buchung.
 *
 * REINE LESE-FLÄCHE: kein Statuswechsel hier (der bleibt im Planer, wo er heute geschrieben
 * wird — kein zweiter Schreibpfad). Performance: EINE Listen-Query mit LeftJoins (kein N+1),
 * EINE Zähl-Query (GROUP BY status), Pagination 50. Unbekannte Statuswerte bleiben sichtbar.
 */
class MaterialentnahmenController extends Controller
{
    private const PER_PAGE = 50;

    /** Status-Gruppen für den Filter → Menge der Rohwerte, die hineinfallen. */
    private const FILTER = [
        'offen'       => ['requested', 'open', 'new', 'send', 'angefordert'],
        'freigegeben' => ['accepted', 'approved', 'ordered', 'received', 'done', 'completed', 'added'],
        'abgelehnt'   => ['rejected', 'declined', 'blocked'],
    ];

    public function index(Request $request)
    {
        $filter = $request->input('status');
        $filter = array_key_exists($filter, self::FILTER) ? $filter : null;

        $basis = DB::table('planner_item_material_requests as r')
            ->whereNull('r.deleted_at');

        if ($filter !== null) {
            $basis->whereIn(DB::raw('LOWER(TRIM(COALESCE(r.status, "requested")))'), self::FILTER[$filter]);
        }

        // Zähl-Query: je Status-Gruppe (eine GROUP BY über die Rohwerte, in PHP gebündelt).
        $rohZaehlung = DB::table('planner_item_material_requests')
            ->whereNull('deleted_at')
            ->selectRaw('LOWER(TRIM(COALESCE(status, "requested"))) as s, COUNT(*) as n')
            ->groupBy('s')->pluck('n', 's');

        $zaehler = ['offen' => 0, 'freigegeben' => 0, 'abgelehnt' => 0, 'sonstige' => 0, 'gesamt' => 0];
        foreach ($rohZaehlung as $s => $n) {
            $gruppe = 'sonstige';
            foreach (self::FILTER as $key => $werte) {
                if (in_array($s, $werte, true)) { $gruppe = $key; break; }
            }
            $zaehler[$gruppe] += $n;
            $zaehler['gesamt'] += $n;
        }

        $zeilen = $basis
            ->leftJoin('new_leads as c', 'c.id', '=', 'r.customer_id')
            ->leftJoin('lead_alternative_adds as o', 'o.id', '=', 'r.alternative_id')
            ->leftJoin('employees as e', 'e.id', '=', 'r.requested_by_employee_id')
            ->leftJoin('article_groups as ag', 'ag.id', '=', 'r.product_id')
            ->select([
                'r.id', 'r.article_name', 'r.article_no', 'r.name as pos_name', 'r.description',
                'r.quantity', 'r.unit', 'r.priority', 'r.needed_at', 'r.status', 'r.created_at',
                'c.name as c_name', 'c.lastname as c_lastname', 'c.firma as c_firma',
                'o.object_name', 'ag.article_group',
                'e.name as e_name', 'e.lastname as e_lastname',
            ])
            ->orderByDesc('r.created_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.inventory.materialentnahmen', [
            'zeilen' => $zeilen,
            'zaehler' => $zaehler,
            'filter' => $filter,
        ]);
    }
}
