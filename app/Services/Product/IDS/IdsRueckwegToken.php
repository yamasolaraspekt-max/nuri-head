<?php

namespace App\Services\Product\IDS;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Z2-W0-11b — der einmalige State-Token des IDS-Rückwegs.
 *
 * ---
 *
 * **Wozu:** `POST ids/callback` stand in `VerifyCsrfToken::$except`. Jeder, der die Route kennt,
 * konnte einen Warenkorb schicken; `ImportedIdsItem::create` lief, und mit `?auto=1` legte
 * `autoPromoteItem` Produkt, Lieferant und Preis an. Teil A hat die *Fremdzuschreibung*
 * geschlossen — die `uid` kommt seither aus der Sitzung. Offen blieb, **ob dieser Nutzer den
 * Absprung überhaupt ausgelöst hat.** Diese Klasse beantwortet genau das.
 *
 * ## Warum nicht `URL::temporarySignedRoute`
 *
 * Das Bordmittel gibt es im Haus (`JitsiService.php:65`), und es wäre weniger Code. **Es erfüllt
 * die Anforderung aber nicht:** eine Signatur ist *ohne Serverzustand* nachprüfbar und deshalb
 * **nicht einmalig** — derselbe Link trägt bis zum Ablauf beliebig oft. Das Blatt sagt es
 * ausdrücklich: *„einmalig" ist ohne Speicher nicht prüfbar.* Ein zweiter Warenkorb auf denselben
 * Token ist genau der Fall, den wir abweisen wollen.
 *
 * ## Was gespeichert wird — und was nicht
 *
 * Der Token selbst ist **Zufall** (`Str::random(40)`), aus nichts ableitbar. Serverseitig liegt
 * dazu, **wem** er gehört: Nutzer-Id und Sitzung. Beim Rückweg wird beides verglichen und der
 * Eintrag **gelöscht** — dasselbe Token ein zweites Mal ist ungültig.
 *
 * *Die Ablaufzeit steckt in der Cache-TTL; ein abgelaufener Eintrag ist schlicht weg, und
 * „nicht gefunden" ist derselbe Ausgang wie „falsch". Ein Angreifer erfährt nicht, ob er
 * geraten oder nur zu spät geraten hat.*
 */
final class IdsRueckwegToken
{
    /** Wie lange ein Absprung offen bleibt. Eine Warenkorb-Auswahl im Shop dauert Minuten, nicht Stunden. */
    public const LAUFZEIT_SEKUNDEN = 1800;

    /** Der Name des Query-Parameters am Rückweg. */
    public const PARAMETER = 'state';

    private static function schluessel(string $token): string
    {
        return 'ids:rueckweg:'.$token;
    }

    /**
     * Erzeugt einen Token für DIESEN Nutzer in DIESER Sitzung und merkt ihn serverseitig.
     *
     * @return string der Token, wie er an die `hookUrl` gehängt wird
     */
    public static function erzeuge(?int $userId, ?string $sitzung): string
    {
        $token = Str::random(40);
        Cache::put(self::schluessel($token), [
            'user_id'   => $userId,
            'sitzung'   => $sitzung,
            'erteilt'   => now()->toIso8601String(),
            'laeuft_ab' => now()->addSeconds(self::LAUFZEIT_SEKUNDEN)->toIso8601String(),
        ], self::LAUFZEIT_SEKUNDEN);

        return $token;
    }

    /**
     * Prüft den Token und **verbraucht** ihn. Zurück kommt der gespeicherte Eintrag (mit
     * `user_id`), oder `null`, wenn der Token fehlt, abgelaufen ist, schon verbraucht wurde
     * oder einem anderen Nutzer gehört.
     *
     * ## Warum der Eintrag zurückkommt und nicht nur `true`
     *
     * **Der Rückweg kommt server-zu-server.** Der Shop schickt den Warenkorb an
     * `ids/callback`; ob dabei ein Sitzungs-Cookie mitreist, bestimmt der Partner, nicht wir.
     * Ist keiner dabei, ist `auth()->id()` **null** — und eine Wache, die dann auf Nutzergleichheit
     * besteht, weist **den regulären Weg** ab. *Das wäre kein Schutz, sondern ein Ausfall
     * (Kriterium d).*
     *
     * Deshalb: **der Token IST die Bindung.** Wer ihn hat, hat den Absprung ausgelöst — er ist
     * Zufall, serverseitig hinterlegt und einmalig. Wessen Absprung es war, steht im Eintrag.
     * **Ist zusätzlich jemand angemeldet, muss er passen** — dann gilt beides.
     *
     * *Das schließt zugleich den Rest aus Teil A: der Importeur kommt jetzt aus dem
     * serverseitigen Eintrag, nicht mehr aus `auth()` allein und nie aus der Query.*
     *
     * @return array{user_id: int|null, sitzung: string|null, erteilt: string, laeuft_ab: string}|null
     */
    public static function verbrauche(?string $token, ?int $angemeldeterNutzer): ?array
    {
        if ($token === null || $token === '') {
            return null;
        }

        $eintrag = Cache::get(self::schluessel($token));
        if (! is_array($eintrag)) {
            return null;   // unbekannt ODER abgelaufen ODER bereits verbraucht
        }

        // Ist jemand angemeldet, muss er der sein, der abgesprungen ist. Ist niemand angemeldet
        // (Server-zu-Server), traegt der Token allein — er ist nicht ableitbar und nicht wiederholbar.
        if ($angemeldeterNutzer !== null && ($eintrag['user_id'] ?? null) !== $angemeldeterNutzer) {
            return null;   // fremder Token — NICHT loeschen, sonst waere das eine Waffe gegen den Berechtigten
        }

        Cache::forget(self::schluessel($token));

        return $eintrag;
    }
}
