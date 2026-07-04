<?php

namespace App\Services\Suppliers\Mappers;

use App\Models\Distributor;
use App\Models\Product;
use App\Models\SupplierArticleMap;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * IDS/Sonepar-Mapper (W2, erster produktiver Kanal). Reine Abbildung einer bereits
 * normalisierten Import-Zeile auf supplier_article_map — KEIN Zugriff auf den produktiven
 * Import-Pfad, keine Seiteneffekte am Bestand. Idempotent über den neutralen Schlüssel.
 *
 * Regel (Yama): fehlender hersteller (Marke) ODER fehlendes Product => keine Zeile schreiben,
 * Skip strukturiert loggen + Tages-Zähler im Cache (Abdeckungslücke sichtbar).
 */
class IdsMapper implements SupplierArticleMapper
{
    public function channel(): string
    {
        return 'ids';
    }

    public function map(array $row, Distributor $d, ?Product $p): ?SupplierArticleMap
    {
        if ($p === null) {
            $this->recordSkip($d, null, $this->str($row['manufacturer_article_no'] ?? ''), 'product_fehlt');

            return null;
        }

        $hersteller = $p->brand?->name;
        if ($hersteller === null || trim((string) $hersteller) === '') {
            $this->recordSkip($d, $p, $this->str($row['manufacturer_article_no'] ?? ''), 'hersteller_fehlt');

            return null;
        }

        $imageUrl = $this->str($row['image_url'] ?? '');

        return SupplierArticleMap::updateOrCreate(
            [
                'hersteller' => $hersteller,
                'herst_artikelnr' => $this->str($row['manufacturer_article_no'] ?? ''),
                'supplier_channel' => 'ids',
            ],
            [
                'distributor_id' => $d->id,
                'lieferanten_artikelnr' => $this->str($row['distributor_article_no'] ?? '') ?: null,
                'ek_preis' => $this->toDecimal($row['purchase_price'] ?? null),
                'vk_preis' => $this->toDecimal($row['price'] ?? null),
                'bild_url' => $imageUrl !== '' ? $imageUrl : null,
                'product_id' => $p->id,
                'last_synced_at' => now(),
            ],
        );
    }

    private function recordSkip(Distributor $d, ?Product $p, string $herstArtNr, string $grund): void
    {
        Log::info('supplier_article_map skip', [
            'channel' => 'ids',
            'distributor_id' => $d->id,
            'product_id' => $p?->id,
            'herst_artikelnr' => $herstArtNr !== '' ? $herstArtNr : null,
            'grund' => $grund,
        ]);

        $key = 'supplier_map_skips:ids:' . now()->format('Y-m-d');
        Cache::add($key, 0);
        Cache::increment($key);
    }

    private function str(mixed $value): string
    {
        return trim((string) ($value ?? ''));
    }

    /** Normalisiert Preis-Strings (dt./engl. Dezimal) auf float; leer/ungültig -> null. */
    private function toDecimal(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        $s = str_replace([' ', "\u{00a0}"], '', (string) $value);
        if (str_contains($s, ',') && str_contains($s, '.')) {
            $s = str_replace('.', '', $s);   // Tausenderpunkt weg
            $s = str_replace(',', '.', $s);  // Dezimalkomma -> Punkt
        } else {
            $s = str_replace(',', '.', $s);
        }

        return is_numeric($s) ? (float) $s : null;
    }
}
