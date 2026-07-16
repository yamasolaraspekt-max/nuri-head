<?php

namespace App\Http\Controllers\Customer\Offer;

use App\Http\Controllers\Controller;
use App\Models\OfferFolder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Nachfassen fällig — Welle A2 (2026-07-16).
 *
 * REINE LESE-FLÄCHE: zeigt Angebote in aktiven Status ohne Bewegung seit X Tagen
 * (Regeln: config/vertrieb.php). Kein Schreibzugriff, keine Automatik — nachgefasst
 * wird vom Menschen; die Fläche macht nur sichtbar, wo Umsatz liegen bleibt.
 */
class NachfassenController extends Controller
{
    public function index(Request $request)
    {
        $tage = (int) config('vertrieb.nachfassen_tage', 7);
        $eskalation = (int) config('vertrieb.nachfassen_eskalation_tage', 21);
        $statuses = array_map('strtolower', (array) config('vertrieb.nachfassen_status', []));
        $grenze = Carbon::now()->subDays($tage);

        $faellig = OfferFolder::query()
            ->with(['customer:id,firma,name,lastname', 'product:id,name'])
            ->whereIn(DB::raw("LOWER(COALESCE(offer_status, 'draft'))"), $statuses)
            ->where('updated_at', '<=', $grenze)
            ->orderBy('updated_at')
            ->get();

        // Aktive Angebote, die noch in der Frist sind (nur Zähler, Kontext für die Kopf-Karten).
        $inFrist = OfferFolder::query()
            ->whereIn(DB::raw("LOWER(COALESCE(offer_status, 'draft'))"), $statuses)
            ->where('updated_at', '>', $grenze)
            ->count();

        $rows = $faellig->map(function (OfferFolder $folder) use ($eskalation) {
            $cust = $folder->customer;
            $kunde = trim((string) ($cust->firma ?? '')) !== ''
                ? $cust->firma
                : trim(($cust->name ?? '') . ' ' . ($cust->lastname ?? ''));

            $tageOhneBewegung = (int) Carbon::parse($folder->updated_at)->diffInDays(Carbon::now());

            return [
                'folder'    => $folder,
                'kunde'     => $kunde !== '' ? $kunde : '— kein Kunde verknüpft —',
                'produkt'   => $folder->product->name ?? null,
                'status'    => $folder->offer_status_label,
                'tage'      => $tageOhneBewegung,
                'eskaliert' => $tageOhneBewegung >= $eskalation,
            ];
        });

        return view('admin.offer.nachfassen', [
            'rows'       => $rows,
            'inFrist'    => $inFrist,
            'tage'       => $tage,
            'eskalation' => $eskalation,
            'eskaliertCount' => $rows->where('eskaliert', true)->count(),
        ]);
    }
}
