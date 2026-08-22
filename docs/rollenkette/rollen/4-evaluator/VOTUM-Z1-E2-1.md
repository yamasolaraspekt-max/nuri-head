# VOTUM Z1-E2-1 — Etagen-Integrität

**ABGENOMMEN (BROWSER) — sechs von sechs Kriterien.**

| Feld | Wert |
|---|---|
| Blattstand | `d2890e85` |
| Bau | `51b0ddfb` · Ausgang `ad2ac724` |
| Mein Stand | `7ad29c6f` |
| gelesen_bis | 2026-08-22T22:38:28+02:00 |
| Bühne | Port 8108, Chrome **headful**, DB am Kindprozess `ticket_testing`, DB-Lease Token 37 |

## Z1-E2-1-a · Etage mit Decke löschen → Ablehnung, sichtbar — ERFÜLLT

Im Browser ausgelöst, Fixture `?fixture=etagen-hoehenkette`, EG dupliziert, dann Löschen versucht:

> **enthält noch Bauteile und eine Decke — erst leeren, dann löschen.**

**Und die Etage bleibt:** „· 2 von 2" vor *und* nach dem Versuch. Bildbeleg
`belege/Z1-E2-1-a-loeschen-abgelehnt.png`.

Die Absage-Regel („eine Ablehnung ohne sichtbaren Grund erfüllt (a) nicht") ist eingehalten: die
Meldung nennt **was** im Weg steht, nicht nur *dass* etwas im Weg steht. Der Code trägt es:

```
hatDecke = Array.isArray(draft.ceilings) && draft.ceilings.some(c => c.levelId === command.levelId)
if (hatNodes || hatDach || hatDecke) { … }
was = [hatNodes?'Bauteile':null, hatDach?'ein Dach':null, hatDecke?'eine Decke':null]
throw new CommandAbgelehnt(`Geschoss ${level.name} enthält noch ${was} — erst leeren, dann löschen.`,
                           'level_nicht_leer')
```

Anzeigeweg: Store (`hausplanerStore.ts:172`) → `HausplanerApp.tsx:1521` →
`FussUndUeberlagerungen.tsx:104`, in Warnfarbe.

## Z1-E2-1-b · EG duplizieren → OG hat die Decke — ERFÜLLT

**Am Dokument gemessen**, wie der Messbefehl es verlangt (`dupliziereGeschoss` direkt aufgerufen):

```
Level  alt → neu :  sortOrder 0 → 1        elevation 0 → 2740
Decke  alt → neu :  id ceiling-hk → neu-3  levelId lvl-eg → neu-1   dickeMm 240 mitgenommen
neue levelId zeigt auf das neue Level:  true
```

Beide Ids verschieden, beide `levelId` verschieden, beide Level mit eigener `sortOrder` **und**
eigener `elevation` — genau die vier verlangten Unterschiede.

Im Browser sichtbar bestätigt: „EG (Kopie) · **+2740 mm** · 2 von 2".

**Der Doppelbeleg aus (a) trägt zusätzlich:** die Ablehnungsmeldung nennt „**eine Decke**" am
*duplizierten* Geschoss. Hätte die Kopie keine Decke bekommen, stünde dort nur „Bauteile". Zwei
unabhängige Wege, dieselbe Aussage.

## Z1-E2-1-c · Verwaiste Decke wird beim Laden benannt — ERFÜLLT

Direkt an `validateSceneIntegrity` ausgelöst — der Messbefehl spricht vom **Laden**, nicht von der
Oberfläche:

```
CeilingNode auf 'lvl-gibtsnicht'  ->  ["Decke ceil-verwaist: unbekanntes Level lvl-gibtsnicht."]
RoofNode    auf 'lvl-weg'         ->  ["Dach roof-verwaist: unbekanntes Level lvl-weg."]
Gegenprobe: dieselbe Decke auf 'lvl-eg'  ->  []          kein Falsch-Positiv
```

**Benannt, nicht still ausgeschlossen** — die Meldung nennt **Bauteil und Level**. Die Absage-Regel
ist eingehalten: nichts wird leise repariert. Dass auch `roofs` mitgeprüft werden, verlangt das
Kriterium ausdrücklich, und es ist erfüllt.

## Z1-E2-1-d · Bestandsdokumente laden unverändert — ERFÜLLT

```
Dokument ohne 'ceilings' (Altaufrufer, Feld fehlt ganz)  ->  []
```

Keine neue Meldung. Die Prüfung ist additiv (`ceilings?` / `roofs?` optional) — die Absage-Regel
(„eine schärfere Prüfung, die Altbestand ablehnt, ist keine Härtung, sondern ein Ausfall") ist
gewahrt.

## Z1-E2-1-e · Lieferung grün und vollständig — ERFÜLLT

`test:hausplaner` **1785 / 1785**, 0 fail · `tsc:hausplaner` **0** · Bündel im Bau-Diff (1).

## Z1-E2-1-f · Kein Modell-, kein Schema-Diff — ERFÜLLT

```
domain/scene.types.ts                 diff ad2ac724..51b0ddfb  ->  LEER
*scene-document-v2.schema.json        diff ad2ac724..51b0ddfb  ->  LEER
Gegenprobe domain/validation.ts       1 file changed, 17 insertions(+)
Existenzpruefung beider Pfade         vorhanden
```

**Beide SHA genannt**, wie die Absage-Regel verlangt. Dass `validation.ts` sich ändert, ist vom
Blatt ausdrücklich erlaubt („*`domain/validation.ts` **darf** sich ändern (Kriterium c) —
`scene.types.ts` nicht*"). Ich habe zuerst gefragt, ob es so gewollt ist, bevor ich einen Mangel
gemeldet hätte.

## Zum Bildbeleg des Generators — eine Feststellung, kein Mangel

Seine Zusage zu (a) nennt die Fußzeile wörtlich und verweist auf `/tmp/z1e21-gruen-ablehnung.png`.
**Ich habe das Bild geöffnet: es zeigt das geöffnete Geschoss-Menü, aber die Meldung selbst ist
darauf nicht zu sehen.** Sein Textbeleg stimmt — ich habe denselben Wortlaut unabhängig ausgelöst —,
aber sein *Bild* stützt ihn nicht. Für dieses Votum trägt mein eigener Bildbeleg; ich nenne es,
damit niemand später das falsche Bild als Nachweis liest.

## Meine eigenen Messausfälle in diesem Lauf — sechs, und alle vom Werkzeug

1. Erster (a)-Versuch gegen ein **Ein-Geschoss**-Dokument — dort greift `level_letztes`, nicht
   `level_nicht_leer`. Mein Aufbau, nicht der Bau.
2. Nach dem Duplizieren schließt sich das Geschoss-Menü; mein erneuter Klick auf dasselbe Feld
   öffnete es **nicht**. Erst ein neutraler Zwischenklick löste es — dann stand der Löschen-Knopf
   wieder da (`x 319, y 440`).
3. (b) über das Projekt-Panel gesucht: **es listet Decken gar nicht** — Gegenprobe, das Original-EG
   zeigt ebenfalls nur „Wände 4", obwohl die Fixture eine Decke trägt. Kein Befund gegen den Bau;
   der Messbefehl meint das Dokument, nicht die Anzeige.
4. 3D-Knopf geklickt, Ansicht blieb auf 2D (im Bild geprüft).
5. Import ohne `.ts`-Endung → `ERR_MODULE_NOT_FOUND`; die Insel nutzt `test-register.mjs` als
   Auflöser.
6. `dupliziereGeschoss` mit falscher Parameterreihenfolge gerufen (`neueId` ist der **vierte**
   Parameter) → „neueId is not a function". Signatur gelesen, wiederholt.

*Fünf davon kosteten Zeit, keiner erzeugte einen Falschbefund — weil jede leere Messung zuerst als
Werkzeugverdacht behandelt wurde.*

**Ball:** Integrator (Transport).
