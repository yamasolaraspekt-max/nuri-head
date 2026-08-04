# 15 · AUF-IDS-LI-SV — Shop fragen statt raten

```yaml
auftrag:
  id: AUF-IDS-LI-SV
  strang: produktdaten
  status: HISTORIE   # AUFGEHOBEN nach ARBEITSREGELN §17 (Planner, 04.08. 23:5x). Alte Statuswerte werden NICHT uebernommen; dieses Blatt ist fachlicher Nachweis, kein Prozessstand. Der eine gueltige Arbeitsstand steht in docs/STATUS.md (§16). VORHERIGE MARKE: bereit
  spur: A
  heimat: ticket
  ziel: "IdsCapabilityService fragt LI und SV normkonform ab und legt den Befund in
         supplier_connections.capabilities ab; eingehaengt vor den vorhandenen Suchtest."
  nicht_ziel: "Keine Oberflaeche. Keine Abloesung von normalizeParamsForTest. Keine
               automatische Versionswahl. Keine der uebrigen fuenf IDS-Aktionen."

scope:
  population_command: "grep -c 'searchterm' app/Services/Suppliers/SupplierConnectionTestService.php"
  pfade:
    - app/Services/Suppliers/Ids/IdsCapabilityService.php
    - app/Services/Suppliers/SupplierConnectionTestService.php
    - tests/Feature/Suppliers/IdsCapabilityServiceTest.php
  ausschluesse: []

kriterien:
  - id: K-01
    aussage: "Die LI-Anfrage traegt GENAU einen Parameter: action=LI."
    typ: adversarial
    kritikalitaet: P1
    pruefung:
      typ: manual
      schritte: "Http::fake, Request abfangen, Parameterliste pruefen"
      erwartet: "['action' => 'LI'] und sonst nichts; Test wird rot, wenn man username ergaenzt"
    beleg: testausgabe
    ausgefuehrt_von: generator

  - id: K-02
    aussage: "Die SV-Anfrage traegt genau action=SV."
    typ: adversarial
    kritikalitaet: P1
    pruefung:
      typ: manual
      schritte: "wie K-01 mit SV"
      erwartet: "['action' => 'SV'] und sonst nichts"
    beleg: testausgabe
    ausgefuehrt_von: generator

  - id: K-03
    aussage: "Die Normbeispiele aus IDS 2.5 §5.6 und §5.7 werden korrekt gelesen."
    typ: presence
    kritikalitaet: P1
    pruefung:
      typ: manual
      schritte: "die beiden XML-Bloecke woertlich als Fixture, Werte vergleichen"
      erwartet: "drei Boolesche Werte bzw. sechs Versionen"
    beleg: testausgabe
    ausgefuehrt_von: generator

  - id: K-04
    aussage: "Alle 15 Kantenfaelle haben je einen benannten Test."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      typ: manual
      schritte: "Testnamen gegen die Kantenliste im Fliesstext abgleichen"
      erwartet: "15 von 15, keine Luecke"
    beleg: zaehlausgabe
    ausgefuehrt_von: generator

  - id: K-05
    aussage: "Kein Zugangsdatum erscheint im Log, auch nicht im Fehlerfall."
    typ: adversarial
    kritikalitaet: P1
    pruefung:
      typ: manual
      schritte: "Passwort-Fixture in der gesamten Testausgabe suchen, auch im Timeout-Fall"
      erwartet: "null Treffer"
    beleg: rohausgabe
    ausgefuehrt_von: generator

  - id: K-06
    aussage: "Der Schalter traegt: bei aktiv=false verhaelt sich test() byte-gleich wie vorher."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: manual
      schritte: "Suite mit ids.capabilities.aktiv=false"
      erwartet: "gruen, test() unveraendert"
    beleg: testausgabe
    ausgefuehrt_von: generator

  - id: K-07
    aussage: "Die Migration ist umkehrbar."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: manual
      schritte: "migrate, dann migrate:rollback, SHOW COLUMNS vorher/nachher"
      erwartet: "spaltengleich"
    beleg: rohausgabe-beider-laeufe
    ausgefuehrt_von: generator

selbstnachweis:
  preflight: "./scripts/auftrag-pruefen.sh docs/auftraege/produktdaten/15-ids-li-sv.md"
  gegenprobe: "in K-01 probeweise username mitsenden — der Test muss fallen."
```

---

> **Rolle:** Planner · **Stand:** 02.08.2026 · **Heimat-App:** `ticket` · **Spur:** A
> **Beauftragt von:** Yama, 02.08.2026 (kein selbst erfundener Posten)
> **Norm:** `docs/quellen/ids/2.5/IDS-Schnittstelle-2.5.pdf` §5.6 und §5.7, Seite 13 ·
> Aufrufparameter §5.8, Seite 14
> **Legende:** BELEGT · BEWERTUNG · ANNAHME · OFFEN

---

## 1 · Ziel und Entscheidung

Die IDS-Norm kennt zwei Abfragen, mit denen ein Shop selbst beantwortet, was er braucht und was
er kann:

| Aktion | Frage | Antwort |
|---|---|---|
| `LI` | Welche Zugangsdaten sind erforderlich? | drei Boolesche Werte |
| `SV` | Welche Schnittstellenversionen werden unterstützt? | Liste von Versionen |

**Entscheidung: Beide werden gebaut und in die Verbindungsprüfung eingehängt — vor dem
vorhandenen Suchtest, nicht statt seiner.**

### Warum gerade diese zwei zuerst

Sie sind die einzigen IDS-Funktionen, die **keine** offene Frage berühren. Kein Warenkorb, kein
Preis, keine Artikelidentität, kein Schema mit strittigen Feldern. Ihre Antwortstruktur steht in
der Norm vollständig ausformuliert. Sie hängen an **keiner** der Messzahlen, auf die Paket 1
wartet.

### Warum das die Verbindungsprüfung verbessert — BELEGT

`SupplierConnectionTestService::test()` (`:12-90`) prüft eine Verbindung heute, indem er eine
**Artikelsuche** mit `searchterm = 'test'` gegen den Shop schickt. Das hat drei Nachteile, die
`LI` und `SV` nicht haben:

1. Es ist ein fachlicher Vorgang mit Nebenwirkungen beim Lieferanten — eine Suche taucht in dessen
   Protokollen auf.
2. Es beantwortet nicht, **warum** es scheitert. Fehlende Kundennummer und falsches Passwort sehen
   gleich aus.
3. Es erzwingt shop-spezifische Sonderbehandlung. `normalizeParamsForTest()` existiert ausweislich
   des Kommentars in `:34-43` nur, um für **Sonepar WKE** „non-standard IDS params" zu entfernen —
   ausdrücklich ohne GC Online und FEGA zu berühren.

**BEWERTUNG.** Punkt 3 ist der eigentliche Gewinn. `LI` und `SV` brauchen laut §5.6/§5.7 **nur**
den Parameter `action`. Kein Suchbegriff, keine Hook-URL, keine Zugangsdaten. Damit entfällt jeder
Grund für eine shop-spezifische Ausnahme — die Abfrage ist für alle Shops identisch. Ein Test, der
keine Sonderfälle braucht, ist ein besserer Test.

---

## 2 · Spur

**A.** Additive Schemaänderung an einer Bestandstabelle, ausgehende Verbindung zu einem externen
Partner, Umgang mit verschlüsselten Zugangsdaten. Kein Geld, keine Bestandsdaten — aber Schema
und Autorisierung genügen jeweils für sich.

---

## 3 · Die Norm — wörtlich, damit nichts erfunden wird

### §5.6 Logininformationen

> Bei der Anfrage wird nur der Parameter Aktionscode = „LI" für Logininformationen übertragen.
> Als Antwort wird folgende XML Struktur erwartet:

```xml
<Logininformationen>
    <Kundennummer_erforderlich>false</Kundennummer_erforderlich>
    <Benutzername_erforderlich>true</Benutzername_erforderlich>
    <Passwort_erforderlich>true</Passwort_erforderlich>
</Logininformationen>
```

> Erlaubte Werte sind „true" / „false".

### §5.7 Schnittstellenversion

> Bei der Anfrage wird nur der Parameter Aktionscode = „SV" für Schnittstellenversion übertragen.
> Als Antwort wird folgende XML Struktur erwartet:

```xml
<Schnittstellenversionen>
    <Version>1.3</Version>
    <Version>2.0</Version>
    <Version>2.1</Version>
    <Version>2.2</Version>
    <Version>2.3</Version>
    <Version>2.5</Version>
</Schnittstellenversionen>
```

> Erlaubte Werte sind „1.3", „2.0", „2.1", „2.2", „2.3" und „2.5".

**Beachte: „nur der Parameter Aktionscode".** Wer zusätzlich Zugangsdaten oder `hookurl`
mitschickt, weicht von der Norm ab.

---

## 4 · Nahtstellen

### Angefasst

| # | Was | Wo |
|---|---|---|
| 1 | Neuer Dienst `IdsCapabilityService` | `app/Services/Suppliers/Ids/IdsCapabilityService.php` |
| 2 | Additive Migration an `supplier_connections` | neue Migration |
| 3 | Einhängen in die Verbindungsprüfung | `SupplierConnectionTestService::test()`, **vor** dem Suchtest |
| 4 | Tests | `tests/Feature/Suppliers/IdsCapabilityServiceTest.php` |

### Die Schnittstelle des Dienstes

```php
namespace App\Services\Suppliers\Ids;

final class IdsCapabilities
{
    public function __construct(
        public readonly ?bool  $kundennummerErforderlich,   // null = Shop hat nicht geantwortet
        public readonly ?bool  $benutzernameErforderlich,
        public readonly ?bool  $passwortErforderlich,
        public readonly array  $versionen,                  // z.B. ['1.3','2.0','2.5']; leer = unbekannt
        public readonly bool   $liUnterstuetzt,
        public readonly bool   $svUnterstuetzt,
        public readonly ?string $hinweis,                   // warum etwas fehlt, fuer die Anzeige
    ) {}
}

final class IdsCapabilityService
{
    /** Fragt LI. Gibt null zurueck, wenn der Shop nicht antwortet oder nicht LI spricht. */
    public function loginInfo(SupplierConnection $c): ?array;

    /** Fragt SV. Gibt eine leere Liste zurueck, wenn der Shop nicht antwortet. */
    public function versionen(SupplierConnection $c): array;

    /** Beide zusammen, fuer die Verbindungspruefung. Wirft nie. */
    public function ermitteln(SupplierConnection $c): IdsCapabilities;
}
```

### Schema — additiv, `nullable`

```php
Schema::table('supplier_connections', function (Blueprint $t) {
    $t->json('capabilities')->nullable();              // Rohbefund LI + SV
    $t->timestamp('capabilities_checked_at')->nullable();
});
```

**Warum eine eigene Spalte und nicht `request_config`:** `request_config` ist **Konfiguration**,
also das, was wir dem Shop sagen. `capabilities` ist ein **Befund**, also das, was der Shop uns
sagt. Beides in ein Feld zu legen wäre die zweite Wahrheit, vor der der Wächter warnt — beim
nächsten Import wüsste niemand mehr, welcher Wert gesetzt und welcher gemessen war.

### Bewusst NICHT angefasst

| Ort | Warum nicht |
|---|---|
| der vorhandene Suchtest in `test()` | bleibt vollständig erhalten. `LI`/`SV` treten **davor**, nicht an seine Stelle |
| `normalizeParamsForTest()` mit der Sonepar-Sonderbehandlung | betrifft nur den Suchtest. Ihre Ablösung ist ein eigener Posten, **nachdem** `LI`/`SV` sich bewährt haben |
| `buildOpenParams()` | `LI`/`SV` bauen ihre zwei Parameter selbst — sie brauchen den Parametersalat nicht, und ihn zu benutzen würde die Norm verletzen |
| `auth_type`, `username`, `password` | werden von `LI`/`SV` **nicht** gesendet |
| die Oberfläche | Anzeige des Befundes ist ein eigener, kleiner Posten (Spur B) |

**Erweiterungspunkt, jetzt nicht bauen:** Sobald `capabilities.versionen` gefüllt ist, kann der
Warenkorb-Aufruf die höchste gemeinsame Version wählen statt `2.5` anzunehmen. Der Dienst wird so
geschnitten, dass das später ohne Umbau andockt — gebaut wird es hier nicht.

---

## 5 · Kantenliste — jede Zeile ist ein Testfall

| # | Fall | Erwartung |
|---|---|---|
| 1 | Shop antwortet mit HTML statt XML | `liUnterstuetzt = false`, **kein** Throw, `hinweis` gesetzt |
| 2 | Shop kennt `LI` nicht und liefert 404 | wie 1 — Abwesenheit ist **kein** Fehler |
| 3 | HTTP 200, aber Fehlerseite im Text | wie 1 |
| 4 | XML mit BOM oder Latin-1 statt UTF-8 | wird gelesen, nicht abgelehnt |
| 5 | `<Kundennummer_erforderlich>` fehlt ganz | dieser eine Wert `null`, die anderen gelten |
| 6 | Wert ist `1`/`0`/`ja` statt `true`/`false` | als unbekannt (`null`) werten, `hinweis` setzen — **nicht** raten |
| 7 | `<Schnittstellenversionen>` leer | leere Liste, `svUnterstuetzt = true` |
| 8 | Version außerhalb der erlaubten sechs (z. B. `3.0`) | aufnehmen **und** als unbekannt kennzeichnen |
| 9 | Timeout | leerer Befund, `last_test_status` unverändert lassen |
| 10 | Redirect auf eine Login-Seite | wie 1 |
| 11 | Antwort größer als 1 MB | abbrechen, nicht in den Speicher laden |
| 12 | Verbindung ist `is_active = false` | gar nicht erst anfragen |
| 13 | `connector_type` ist `omd` oder `datanorm` | gar nicht erst anfragen — `LI`/`SV` sind IDS-Funktionen |
| 14 | Zwei Aufrufe gleichzeitig gegen denselben Shop | serialisieren. Die Norm warnt bei **jeder** Funktion vor parallelen Aufrufen |
| 15 | Zugangsdaten im Log | dürfen **nie** erscheinen, auch nicht im Fehlerfall |

Fall 6 ist der wichtigste. Ein Shop, der `1` statt `true` schickt, ist nicht normkonform — aber
zu raten, dass `1` wohl `true` heißt, ist genau die Sorte stillschweigender Annahme, die später
niemand mehr findet. Lieber unbekannt und sichtbar.

Fall 14 ist die Umsetzung einer wörtlichen Normwarnung, die auf jeder Funktionsseite steht:
*„es kann zu Problemen kommen, wenn in einem Shop-System mehrere Funktionen parallel gestartet
werden."*

---

## 6 · Rückweg und Entdeckung

**Rückweg, dreifach:**

1. `config('ids.capabilities.aktiv') = false` — die Verbindungsprüfung verhält sich exakt wie
   heute, ohne Deploy.
2. Commit zurückdrehen — der Dienst ist neu, der Eingriff in `test()` ist ein vorangestellter
   Block.
3. `migrate:rollback` — zwei `nullable` Spalten fallen weg, keine Bestandsspalte betroffen.

**Voraussetzung vor Beginn:** gepusht. Stand 02.08.2026 09:15 erledigt — `ff5f8dc9` steht auf
`fork/auto/hausplaner-integration`, `rev-list --count '@{u}..HEAD'` = 0.

**Entdeckung — zwei benannte Signale:**

```sql
-- 1) Greift die Abfrage ueberhaupt? Muss steigen, sobald eine Verbindung existiert.
SELECT COUNT(*) FROM supplier_connections WHERE capabilities_checked_at IS NOT NULL;

-- 2) Antworten die Shops normkonform? Wenn hier alles NULL bleibt, sprechen sie LI/SV nicht.
SELECT id, name, JSON_EXTRACT(capabilities, '$.li_unterstuetzt')
  FROM supplier_connections WHERE capabilities IS NOT NULL;
```

**Wichtig für die Bewertung:** Wenn Signal 2 zeigt, dass kein Shop `LI` beantwortet, ist der
Auftrag **trotzdem erfüllt**. Die Norm erlaubt Shops, Funktionen nicht anzubieten; die Präambel
sagt ausdrücklich, der Umfang differiere zwischen den Systemen. Ein sauberes „dieser Shop kann es
nicht" ist das Ergebnis, nicht das Scheitern.

---

## 7 · Abnahmekriterien

1. **`LI` wird normkonform gestellt.** Ein Test mit `Http::fake()` belegt, dass die Anfrage
   **genau einen** Parameter trägt: `action=LI`. Keine Zugangsdaten, keine `hookurl`, kein
   `searchterm`. *Gegen-Beweis: der Test muss rot werden, wenn man `username` ergänzt.*
2. **`SV` ebenso** mit `action=SV`.
3. **Die Beispielantworten aus §5.6 und §5.7 werden korrekt gelesen** — die drei Booleschen Werte
   bzw. die sechs Versionen, wörtlich aus der Norm als Fixture.
4. **Alle 15 Kantenfälle** aus §5 haben je einen benannten Test, alle grün.
5. **Kein Zugangsdatum im Log.** `grep` über die Testausgabe nach dem Passwort-Fixture liefert
   **null Treffer**. Dieser Test läuft auch für den Fehlerfall.
6. **Der Schalter trägt.** Mit `ids.capabilities.aktiv = false` ist die Suite grün und
   `SupplierConnectionTestService::test()` verhält sich byte-gleich wie vorher.
7. **Migration ist umkehrbar.** `migrate` gefolgt von `migrate:rollback` lässt
   `supplier_connections` spaltengleich zurück — `SHOW COLUMNS` vorher/nachher als Rohausgabe.
8. Suite gegen die Baseline aus Paket 1 Schritt 1 nicht schlechter.

**Ohne Live-Verbindung prüfbar:** alles davon. `Http::fake()` mit den Normbeispielen als Fixture
ersetzt den Lieferanten vollständig. Das ist der Grund, warum dieser Auftrag jetzt läuft und
nicht auf einen Zugang wartet.

---

## 8 · Was ausdrücklich nicht dazugehört

- Keine Oberfläche. Der Befund wird gespeichert, nicht angezeigt — Anzeige ist ein eigener
  Spur-B-Posten.
- Keine Ablösung von `normalizeParamsForTest()`.
- Keine automatische Versionswahl beim Warenkorb-Aufruf.
- Keine der übrigen fünf IDS-Aktionen.
- Keine Änderung an `OmdClient` oder am DATANORM-Weg.

---

## 9 · Heimat-App und Rollen

**Heimat:** `ticket`.
**Generator:** setzt um — **nicht** die Instanz, die diesen Auftrag geschrieben hat.
**Evaluator:** frische Instanz, misst selbst nach, Rot-Probe zu Kriterium 1 ist Pflichtbeleg.

## 10 · Ledger

```
PLANNER 2026-08-02 · AUF-IDS-LI-SV · Spur A · Heimat ticket
  Beauftragt von Yama am 02.08. Zwei Normabfragen (IDS 2.5 §5.6/§5.7, S.13) bauen:
  LI = welche Zugangsdaten noetig, SV = welche Versionen der Shop spricht.
  Nahtstelle: vor den vorhandenen Suchtest in SupplierConnectionTestService::test().
  Gewinn: der heutige Test feuert eine Artikelsuche mit searchterm=test und braucht dafuer
  eine Sonepar-Sonderbehandlung (normalizeParamsForTest). LI/SV brauchen nur action - keine
  Sonderfaelle.
  Schema additiv: supplier_connections.capabilities (json) + capabilities_checked_at.
  15 Kantenfaelle, 8 Abnahmekriterien, vollstaendig ohne Live-Verbindung pruefbar (Http::fake).
  Ballbesitz: Generator.
```
