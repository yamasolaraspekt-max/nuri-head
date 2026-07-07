<?php

namespace App\Http\Controllers\Energie;

use App\Http\Controllers\Controller;
use App\Models\Konstruktion;
use App\Models\Material;

/**
 * Materialliste — Read-only Referenz-Browse (Strang Energie).
 *
 * Zeigt den transplantierten Referenzkatalog aus wberechnung: Baustoffe/Dämmstoffe
 * (materials, mit Bemessungs-λ) und wiederverwendbare Bauteilaufbauten (konstruktionen,
 * mit gecachtem U-Wert). Reine Anzeige, keine Bearbeitung — die Pflege der Referenzdaten
 * läuft über Seeder/Import, nicht über diese Seite.
 */
class MateriallisteController extends Controller
{
    /** GET energie.materialliste → Referenzkatalog (Materialien + Konstruktionen). */
    public function index()
    {
        $materialien = Material::orderBy('kategorie')->orderBy('name')->get();
        $konstruktionen = Konstruktion::orderBy('typ')->orderBy('name')->get();

        return view('admin.energie.materialliste', compact('materialien', 'konstruktionen'));
    }
}
