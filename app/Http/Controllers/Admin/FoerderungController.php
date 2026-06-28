<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Foerderung;
use Illuminate\Http\Request;

/**
 * Förderprogramm-Verwaltung — Ersatz für das kaputte BEG-Förderungen-Modul.
 *
 * Logik übernommen aus playgrounds Api\FoerderungController (Suche/Filter/Sort,
 * Auto-Nummer FOE-JJJJ-NNNN, Soft-Archiv). Angepasst an ticket:
 * - Auth über tickets `middleware('auth')` (KEINE playground-RBAC).
 * - Server-gerendertes Blade (admin.layouts.app) statt JSON-SPA.
 * - Schreibaktionen als AJAX-JSON für das Bootstrap-Modal.
 */
class FoerderungController extends Controller
{
    /** @var string[] */
    private array $typen = ['KfW', 'BAFA', 'Land', 'Kommune', 'EU', 'sonstiger'];

    /** @var string[] */
    private array $statusList = ['geplant', 'beantragt', 'bewilligt', 'ausgezahlt', 'abgelehnt'];

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $q = Foerderung::query();

        if (! $request->boolean('mit_archivierten')) {
            $q->whereNull('archived_at');
        }

        if ($s = trim((string) $request->input('search'))) {
            $term = '%' . $s . '%';
            $q->where(function ($w) use ($term) {
                $w->where('bezeichnung', 'like', $term)
                    ->orWhere('projekt_ref', 'like', $term)
                    ->orWhere('antragsnummer', 'like', $term);
            });
        }

        if ($request->filled('foerdertyp') && in_array($request->input('foerdertyp'), $this->typen, true)) {
            $q->where('foerdertyp', $request->input('foerdertyp'));
        }

        if ($request->filled('antragsstatus') && in_array($request->input('antragsstatus'), $this->statusList, true)) {
            $q->where('antragsstatus', $request->input('antragsstatus'));
        }

        $sort = in_array($request->input('sort'), ['bezeichnung', 'foerdertyp', 'betrag', 'antragsstatus', 'deadline'], true)
            ? $request->input('sort') : 'deadline';
        $dir = $request->input('dir') === 'desc' ? 'desc' : 'asc';

        $fundings = $q->orderBy($sort, $dir)->paginate(10)->withQueryString();

        // KPI-Kennzahlen über die nicht-archivierten Förderungen
        $aktive = fn () => Foerderung::whereNull('archived_at');
        $stats = [
            'total'      => $aktive()->count(),
            'beantragt'  => $aktive()->whereIn('antragsstatus', ['beantragt', 'bewilligt'])->count(),
            'ausgezahlt' => $aktive()->where('antragsstatus', 'ausgezahlt')->count(),
            'volumen'    => (float) $aktive()->where('antragsstatus', '!=', 'abgelehnt')->sum('betrag'),
        ];

        return view('admin.foerderungen.index', [
            'fundings'   => $fundings,
            'stats'      => $stats,
            'typen'      => $this->typen,
            'statusList' => $this->statusList,
            'filter'     => [
                'search'           => (string) $request->input('search', ''),
                'foerdertyp'       => (string) $request->input('foerdertyp', ''),
                'antragsstatus'    => (string) $request->input('antragsstatus', ''),
                'mit_archivierten' => $request->boolean('mit_archivierten'),
                'sort'             => $sort,
                'dir'              => $dir,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePayload($request);
        $data['antragsnummer'] = $request->filled('antragsnummer')
            ? trim((string) $request->input('antragsnummer'))
            : $this->naechsteNummer();
        $data['projekt_ref'] = $request->input('projekt_ref');
        $data['notiz'] = $request->input('notiz');

        Foerderung::create($data);

        return response()->json(['success' => true, 'message' => 'Förderung angelegt.']);
    }

    public function update(Request $request, Foerderung $foerderung)
    {
        $data = $this->validatePayload($request);
        $data['projekt_ref'] = $request->input('projekt_ref');
        $data['notiz'] = $request->input('notiz');

        $foerderung->update($data);

        return response()->json(['success' => true, 'message' => 'Förderung gespeichert.']);
    }

    /** Soft-Archiv (kein Hard-Delete — Förderhistorie bleibt erhalten). */
    public function destroy(Foerderung $foerderung)
    {
        $foerderung->update(['archived_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Förderung archiviert.']);
    }

    public function restore(Foerderung $foerderung)
    {
        $foerderung->update(['archived_at' => null]);

        return response()->json(['success' => true, 'message' => 'Förderung wiederhergestellt.']);
    }

    // ── intern ──────────────────────────────────────────────────────────────

    private function validatePayload(Request $request): array
    {
        return $request->validate([
            'bezeichnung'   => 'required|string|max:255',
            'foerdertyp'    => 'nullable|string|in:' . implode(',', $this->typen),
            'betrag'        => 'nullable|numeric|min:0',
            'antragsstatus' => 'nullable|string|in:' . implode(',', $this->statusList),
            'deadline'      => 'nullable|date',
        ], [
            'bezeichnung.required' => 'Bitte geben Sie ein Förderprogramm an.',
            'foerdertyp.in'        => 'Bitte wählen Sie einen gültigen Typ.',
            'betrag.numeric'       => 'Der Förderbetrag muss eine Zahl sein.',
            'antragsstatus.in'     => 'Bitte wählen Sie einen gültigen Status.',
            'deadline.date'        => 'Bitte geben Sie ein gültiges Datum an.',
        ]);
    }

    private function naechsteNummer(): string
    {
        $jahr = now()->year;
        $prefix = "FOE-{$jahr}-";
        $letzte = Foerderung::where('antragsnummer', 'like', $prefix . '%')
            ->orderBy('antragsnummer', 'desc')->value('antragsnummer');
        $n = $letzte ? ((int) substr($letzte, strlen($prefix))) + 1 : 1;

        return $prefix . str_pad((string) $n, 4, '0', STR_PAD_LEFT);
    }
}
