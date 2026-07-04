<?php

namespace App\Services\Suppliers\Mappers;

use Illuminate\Support\Facades\Log;

/**
 * Auflösung connector_type -> SupplierArticleMapper (W2). Container-gebunden (Singleton),
 * damit OMD/DATANORM später ohne erneuten Eingriff andocken.
 *
 * 'ids' UND 'oci' -> IdsMapper: der OCI-Punchout (Sonepar) liefert IDS-normalisierte Zeilen,
 * der Kanal ist fachlich 'ids'. Ohne diese Doppelauflösung bliebe die Map auf der produktiven
 * Strecke leer.
 */
class MapperRegistry
{
    /** @var array<string, bool> bereits geloggte unbekannte connector_types (Log einmalig pro Lauf) */
    private array $loggedUnknown = [];

    public function resolve(?string $connectorType): ?SupplierArticleMapper
    {
        $key = strtolower(trim((string) $connectorType));

        return match ($key) {
            'ids', 'oci' => new IdsMapper(),
            'omd' => new OmdMapper(),
            'datanorm' => new DatanormMapper(),
            default => $this->none($key),
        };
    }

    private function none(string $key): ?SupplierArticleMapper
    {
        if (! isset($this->loggedUnknown[$key])) {
            $this->loggedUnknown[$key] = true;
            Log::debug('supplier_article_map: kein Mapper fuer connector_type', [
                'connector_type' => $key !== '' ? $key : null,
            ]);
        }

        return null;
    }
}
