<?php

namespace App\Services\Suppliers\Ids;

use App\Models\SupplierConnection;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * AUF-IDS-LI-SV: fragt einen IDS-Shop normkonform, welche Zugangsdaten er braucht (LI)
 * und welche Schnittstellenversionen er spricht (SV). IDS 2.5 §5.6/§5.7:
 * "Bei der Anfrage wird NUR der Parameter Aktionscode uebertragen." — keine Zugangsdaten,
 * keine hookurl, kein searchterm.
 */
final class IdsCapabilityService
{
    private const ERLAUBTE_VERSIONEN = ['1.3', '2.0', '2.1', '2.2', '2.3', '2.5'];

    /** Fragt LI. Gibt null zurueck, wenn der Shop nicht antwortet oder nicht LI spricht. */
    public function loginInfo(SupplierConnection $connection): ?array
    {
        $antwort = $this->anfrage($connection, 'LI');

        if ($antwort['xml'] === null) {
            return null;
        }

        return $this->parseLi($antwort['xml']);
    }

    /** Fragt SV. Gibt eine leere Liste zurueck, wenn der Shop nicht antwortet. */
    public function versionen(SupplierConnection $connection): array
    {
        $antwort = $this->anfrage($connection, 'SV');

        if ($antwort['xml'] === null) {
            return ['unterstuetzt' => false, 'versionen' => [], 'unbekannt' => [], 'hinweis' => $antwort['hinweis']];
        }

        return $this->parseSv($antwort['xml']);
    }

    /** Beide zusammen, fuer die Verbindungspruefung. Wirft nie. */
    public function ermitteln(SupplierConnection $connection): IdsCapabilities
    {
        try {
            // Kante 12: inaktive Verbindungen gar nicht erst anfragen.
            if (!$connection->is_active) {
                return IdsCapabilities::leer('Verbindung ist inaktiv — nicht angefragt.');
            }

            // Kante 13: LI/SV sind IDS-Funktionen — OMD, DATANORM & Co. nicht anfragen.
            if (strtolower((string) $connection->connector_type) !== 'ids') {
                return IdsCapabilities::leer(
                    'Verbindungstyp ' . $connection->connector_type . ' spricht kein IDS — nicht angefragt.'
                );
            }

            if (!$connection->test_url && !$connection->endpoint_url) {
                return IdsCapabilities::leer('Keine Shop-Adresse hinterlegt — nicht angefragt.');
            }

            // Kante 14: die Norm warnt vor parallelen Funktionsaufrufen im selben Shop —
            // Aufrufe je Verbindung serialisieren.
            $lock = Cache::lock('ids-capabilities-' . $connection->id, 60);

            try {
                $lock->block(5);
            } catch (LockTimeoutException) {
                return IdsCapabilities::leer(
                    'Eine Faehigkeitsabfrage fuer diese Verbindung laeuft bereits — nicht parallel angefragt.'
                );
            }

            try {
                $befund = $this->abfragen($connection);
            } finally {
                $lock->release();
            }

            $this->ablegen($connection, $befund);

            return $befund;
        } catch (\Throwable $exception) {
            // Wirft nie. Fehlermeldung ohne Zugangsdaten loggen (Kante 15).
            Log::warning('IDS-Faehigkeitsabfrage fehlgeschlagen', [
                'connection_id' => $connection->id,
                'error' => $exception->getMessage(),
            ]);

            return IdsCapabilities::leer('Unerwarteter Fehler bei der Faehigkeitsabfrage.');
        }
    }

    private function abfragen(SupplierConnection $connection): IdsCapabilities
    {
        $hinweise = [];

        $li = $this->anfrage($connection, 'LI');
        $liWerte = null;

        if ($li['xml'] !== null) {
            $liWerte = $this->parseLi($li['xml']);
        }

        if ($liWerte === null && $li['hinweis'] !== null) {
            $hinweise[] = 'LI: ' . $li['hinweis'];
        }

        if ($liWerte !== null && ($liWerte['hinweis'] ?? null) !== null) {
            $hinweise[] = 'LI: ' . $liWerte['hinweis'];
        }

        $sv = $this->anfrage($connection, 'SV');
        $svWerte = ['unterstuetzt' => false, 'versionen' => [], 'unbekannt' => []];

        if ($sv['xml'] !== null) {
            $svWerte = $this->parseSv($sv['xml']);

            if (($svWerte['hinweis'] ?? null) !== null) {
                $hinweise[] = 'SV: ' . $svWerte['hinweis'];
            }
        } elseif ($sv['hinweis'] !== null) {
            $hinweise[] = 'SV: ' . $sv['hinweis'];
        }

        return new IdsCapabilities(
            kundennummerErforderlich: $liWerte['kundennummer'] ?? null,
            benutzernameErforderlich: $liWerte['benutzername'] ?? null,
            passwortErforderlich: $liWerte['passwort'] ?? null,
            versionen: $svWerte['versionen'],
            versionenUnbekannt: $svWerte['unbekannt'],
            liUnterstuetzt: $liWerte !== null,
            svUnterstuetzt: (bool) $svWerte['unterstuetzt'],
            hinweis: $hinweise === [] ? null : implode(' · ', $hinweise),
        );
    }

    /**
     * Stellt genau EINE normkonforme Anfrage: nur der Parameter action.
     * Gibt ['xml' => ?SimpleXMLElement, 'hinweis' => ?string] zurueck. Wirft nie.
     */
    private function anfrage(SupplierConnection $connection, string $action): array
    {
        $url = $connection->test_url ?: $connection->endpoint_url;

        try {
            $response = Http::timeout((int) config('ids.capabilities.timeout', 10))
                ->accept('text/xml, application/xml;q=0.9, */*;q=0.1')
                ->withOptions(['allow_redirects' => false])
                ->get($url, ['action' => $action]);
        } catch (\Throwable $exception) {
            // Kante 9: Timeout/Netzfehler => leerer Befund. Fehlertext OHNE Zugangsdaten.
            return ['xml' => null, 'hinweis' => 'Shop nicht erreichbar (' . class_basename($exception) . ').'];
        }

        // Kante 10: Redirect (z. B. auf eine Login-Seite) ist keine Antwort.
        if ($response->redirect()) {
            return ['xml' => null, 'hinweis' => 'Shop leitet um (HTTP ' . $response->status() . ') statt zu antworten.'];
        }

        // Kante 2: 404 & Co. — Abwesenheit ist kein Fehler, nur ein Befund.
        if ($response->status() !== 200) {
            return ['xml' => null, 'hinweis' => 'Shop antwortet mit HTTP ' . $response->status() . '.'];
        }

        // Kante 11: uebergrosse Antworten nicht in den Speicher laden.
        $maxBytes = (int) config('ids.capabilities.max_bytes', 1048576);
        $contentLength = (int) $response->header('Content-Length');

        if ($contentLength > $maxBytes) {
            return ['xml' => null, 'hinweis' => 'Antwort groesser als ' . $maxBytes . ' Bytes — abgebrochen.'];
        }

        $body = (string) $response->body();

        if (strlen($body) > $maxBytes) {
            return ['xml' => null, 'hinweis' => 'Antwort groesser als ' . $maxBytes . ' Bytes — abgebrochen.'];
        }

        $xml = $this->lesen($body);

        if ($xml === null) {
            // Kanten 1 + 3: HTML oder Fehlerseite statt XML.
            return ['xml' => null, 'hinweis' => 'Antwort ist kein auswertbares XML.'];
        }

        return ['xml' => $xml, 'hinweis' => null];
    }

    /** Kante 4: BOM und Latin-1 werden gelesen, nicht abgelehnt. Wirft nie. */
    private function lesen(string $body): ?\SimpleXMLElement
    {
        // UTF-8-BOM entfernen.
        $body = preg_replace('/^\xEF\xBB\xBF/', '', $body) ?? $body;

        $vorher = libxml_use_internal_errors(true);

        try {
            $xml = simplexml_load_string($body, \SimpleXMLElement::class, LIBXML_NONET);

            if ($xml !== false) {
                return $xml;
            }

            libxml_clear_errors();

            // Zweiter Versuch: Latin-1 nach UTF-8 wandeln (Deklaration ggf. anpassen).
            $konvertiert = mb_convert_encoding($body, 'UTF-8', 'ISO-8859-1');
            $konvertiert = preg_replace('/encoding="[^"]*"/i', 'encoding="UTF-8"', $konvertiert) ?? $konvertiert;

            $xml = simplexml_load_string($konvertiert, \SimpleXMLElement::class, LIBXML_NONET);

            return $xml === false ? null : $xml;
        } catch (\Throwable) {
            return null;
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($vorher);
        }
    }

    /**
     * §5.6: <Logininformationen> mit drei Booleschen Kindern.
     * Gibt null zurueck, wenn die Struktur nicht die der Norm ist.
     */
    private function parseLi(\SimpleXMLElement $xml): ?array
    {
        if ($xml->getName() !== 'Logininformationen') {
            return null;
        }

        $hinweise = [];

        $lese = function (string $element) use ($xml, &$hinweise): ?bool {
            // Kante 5: fehlendes Element => dieser eine Wert bleibt unbekannt.
            if (!isset($xml->{$element})) {
                $hinweise[] = $element . ' fehlt in der Antwort.';

                return null;
            }

            $wert = strtolower(trim((string) $xml->{$element}));

            // Kante 6: nur die Normwerte "true"/"false" gelten — nicht raten.
            return match ($wert) {
                'true' => true,
                'false' => false,
                default => (static function () use ($element, $wert, &$hinweise) {
                    $hinweise[] = $element . " traegt nicht normkonformen Wert '" . $wert . "' — als unbekannt gewertet.";

                    return null;
                })(),
            };
        };

        return [
            'kundennummer' => $lese('Kundennummer_erforderlich'),
            'benutzername' => $lese('Benutzername_erforderlich'),
            'passwort' => $lese('Passwort_erforderlich'),
            'hinweis' => $hinweise === [] ? null : implode(' ', $hinweise),
        ];
    }

    /** §5.7: <Schnittstellenversionen> mit 0..n <Version>-Kindern. */
    private function parseSv(\SimpleXMLElement $xml): array
    {
        if ($xml->getName() !== 'Schnittstellenversionen') {
            return ['unterstuetzt' => false, 'versionen' => [], 'unbekannt' => [], 'hinweis' => 'Antwort ist kein SV-XML.'];
        }

        $versionen = [];
        $unbekannt = [];

        foreach ($xml->Version as $version) {
            $wert = trim((string) $version);

            if ($wert === '') {
                continue;
            }

            $versionen[] = $wert;

            // Kante 8: unbekannte Versionen aufnehmen UND kennzeichnen.
            if (!in_array($wert, self::ERLAUBTE_VERSIONEN, true)) {
                $unbekannt[] = $wert;
            }
        }

        return [
            // Kante 7: eine leere Liste ist eine gueltige SV-Antwort.
            'unterstuetzt' => true,
            'versionen' => $versionen,
            'unbekannt' => $unbekannt,
            'hinweis' => $unbekannt === []
                ? null
                : 'Versionen ausserhalb der Norm: ' . implode(', ', $unbekannt) . '.',
        ];
    }

    private function ablegen(SupplierConnection $connection, IdsCapabilities $befund): void
    {
        $connection->forceFill([
            'capabilities' => $befund->toArray(),
            'capabilities_checked_at' => now(),
        ])->save();
    }
}
