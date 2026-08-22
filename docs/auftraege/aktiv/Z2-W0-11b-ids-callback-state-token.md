# Z2-W0-11b — Der IDS-Rückweg trägt einen einmaligen Token statt einer CSRF-Ausnahme

**ZIEL:** `POST ids/callback` nimmt keinen Warenkorb mehr entgegen, den nicht dieser Nutzer in
dieser Sitzung ausgelöst hat.

```yaml
auftrag: "Z2-W0-11b"
spur: A
heimat_app: ticket
art: "AUTH — ein einmaliger State-Token ersetzt eine CSRF-Ausnahme.
      KEINE Aenderung der XML-Verarbeitung, KEINE Aenderung der Auto-Anlage,
      KEINE neue Rechtelogik."
teil_a_erledigt: "Z2-W0-11 Teil A (21.08.): uid kommt aus der Sitzung (IdsController.php:69),
                  fuenf tote Ausnahmen entfernt. GEMESSEN: 0 uid-Lesestellen."
operand_y12: "GELIEFERT (gen 19 Posten 9): IDS-Connect-Standard — eigener einmaliger state/nonce
              in der hookUrl beim Absprung, TTL, an Sitzung/Nutzer gebunden, Pruefung im Callback,
              Ablehnung ohne/mit verbrauchtem Token. Partnerfrage NUR falls der Bau misst, dass die
              hookUrl-Query nicht zurueckkommt — siehe Kriterium (f)."
mess_sha: 1146cbe6
kennung_geprueft: "Z2-W0-11b ist im Teil-A-Blatt als Folgeauftrag benannt
                   (generator-auftrag-z2-w0-11-ids-callback-csrf.md, Abgrenzungstabelle).
                   Vom Auftrag zugewiesen, nicht selbst vergeben."
dor_beleg: "ERTEILT — plan-pruefer 2026-08-22T16:52:38, Beleg 343bd48f (plan-pruefer-DOR-Z2-W0-11b-ERTEILT.yaml)"
basis_sha: 1146cbe6
prioritaet: P0
ballbesitz: "generator (DoR erteilt — baubar)"
regelgrundlage: "Planner gen 19 Posten 9. Einordnung Kategorie 2 der Regel RECHTE_ALLE_FUER_ALLE
                 (Yama 21.08.): Integritaets-/Auth-Luecke bleibt Befund, auch bei offenem Schalter."
```

## Die Lage, gemessen am Stand `1146cbe6`

```
routes/web.php:511      Route::post('/ids/callback', [IdsController::class, 'callback'])
VerifyCsrfToken:20      'ids/callback'   <- die Ausnahme, die dieses Blatt ersetzt
IdsController.php:33    callback(Request $request)
              :35       Log::info("IDS CALLBACK HIT", ['query' => $request->query()])
              :69       $userId = auth()->id()          <- Teil A, gemessen: 0 uid-Lesestellen
              :70       $auto = $request->query('auto') == '1'   <- steuert die AUTO-ANLAGE
              :74-90    foreach items -> ImportedIdsItem::create(...) [+ autoPromoteItem]

der Absprung
IdsSearchController:157 $callbackUrl = route('ids.callback')     <- OHNE Query, ohne Token
```

**Der Konstruktor setzt `auth`, die Route liegt hinter `web`.** *Der Rückweg läuft also durch den
Browser des angemeldeten Nutzers* — Teil A hat das belegt und daraus `auth()->id()` begründet.
**Was fehlt, ist die Bindung an genau diesen Absprung:** heute genügt ein POST auf die Adresse,
und mit `?auto=1` legt er ohne weiteres Zutun Produkte an.

## Die Grundmenge — und ein vierter Rückweg, der ohne Ausnahme läuft

**Sechs Pfade stehen in `VerifyCsrfToken::$except`. Vier davon sind Rückwege von Fremdsystemen:**

| URI-Muster | Controller | in `$except` |
|---|---|---|
| `ids/callback` | `IdsController@callback` | **ja** — dieses Blatt |
| `admin/supplier-connectors/*/return` | `SupplierConnectionController@handleReturn` | ja |
| `admin/offers/folders/*/supplier/*/return` | `OfferSupplierSearchController@handleReturn` | ja |
| **`admin/offer-template-supplier/{sc}/return`** | `OfferTemplateSupplierController@handleReturn` | **NEIN — 0 Treffer** |

> **Ein Rückweg derselben Bauart** (`Route::match(['GET','POST'])`, `handleReturn`, Präfix
> `admin/offer-template-supplier`) **steht nicht in der Ausnahmeliste.** *Das ist der billigste
> Messpunkt dieses Blattes: läuft er, dann ist „der Rückweg braucht eine Ausnahme" nicht allgemein
> wahr — läuft er nicht, ist eine zweite Lücke gefunden.* **Beides ist ein Ergebnis** (Kriterium e).

**Und ein Beleg im Haus, der die Partnerfrage erübrigen könnte:**

```
OfferSupplierSearchController.php:103   $hookUrl .= '?' . http_build_query([... 'uid' => ...])
IdsSearchController.php:157             $callbackUrl = route('ids.callback')   <- ohne Query
```

**Ein Rückweg hängt bereits eine Query an die `hookUrl`.** *Funktioniert er, ist belegt, dass die
Query zurückkommt — dann ist die Partnerfrage nicht nötig.* **Nebenbefund, gemessen und hier
benannt:** die `uid` in dieser Query wird von **keinem** der drei `handleReturn` gelesen (0
Lesestellen) — *ein toter Parameter, kein Loch. Er gehört nicht in dieses Blatt, aber er gehört
notiert, bevor jemand ihn für eine Zuschreibung hält.*

---

## Abnahmekriterien

- **Z2-W0-11b-a** · **DER ABSPRUNG SETZT EINEN EINMALIGEN TOKEN.**

  **Verlangt:** `IdsSearchController` hängt an die `hookUrl` einen **einmaligen** Token, der
  **an Sitzung und Nutzer gebunden** ist und eine **TTL** hat. Serverseitig gespeichert (Cache oder
  Tabelle) — **nicht** aus Nutzerdaten ableitbar.

  **Messbefehl:**
  ```
  grep -n 'route(.ids.callback.)' app/Http/Controllers/Product/IDS/gconline/IdsSearchController.php
      -> die Zeile traegt jetzt einen Query-Parameter
  zwei Abspruenge nacheinander -> ZWEI VERSCHIEDENE Token (Rohausgabe beider)
  der Speicherort nennt: Nutzer-Id, Ablaufzeitpunkt
  ```

  **Heutiges (rotes) Ergebnis:** `IdsSearchController.php:157` baut `route('ids.callback')`
  **ohne Query**; `grep -riE '\bnonce\b|\bstate\b'` über `app/Http/Controllers/Product/IDS/` → **0**.

  **Absage-Regel:** Ein aus `user_id` + Zeitstempel **berechneter** Wert erfüllt (a) **nicht**, wenn
  er ohne Serverzustand nachprüfbar ist. *Ein Token, den der Empfänger nachrechnen kann, kann auch
  der Angreifer nachrechnen — und „einmalig" ist ohne Speicher nicht prüfbar.*

- **Z2-W0-11b-b** · **DER CALLBACK PRÜFT IHN — VOR DEM ERSTEN SCHREIBZUGRIFF.**

  **Verlangt:** `callback()` prüft den Token **bevor** `ImportedIdsItem::create` oder
  `autoPromoteItem` läuft. Ungültig/fehlend/verbraucht → **4xx**, **keine** Zeile in der Datenbank.

  **Messbefehl:**
  ```
  im Diff: die Pruefung steht VOR der foreach-Schleife (IdsController.php:74)
  vier Laeufe, je HTTP-Code UND SELECT COUNT(*) FROM imported_ids_items vorher/nachher:
    gueltiger Token      -> 200, Zeilen +n
    KEIN Token           -> 4xx, Zeilen +0
    fremder Token        -> 4xx, Zeilen +0
    VERBRAUCHTER Token   -> 4xx, Zeilen +0     <- die Einmaligkeit, ausgeloest
  ```

  **Heutiges (rotes) Ergebnis:** ein POST auf `ids/callback` mit gültigem XML legt Zeilen an,
  **ohne dass irgendein Token geprüft wird** — die Ausnahme in `VerifyCsrfToken:20` lässt ihn durch.

  **Absage-Regel:** Eine Prüfung **nach** dem Parsen, aber **vor** dem Speichern erfüllt (b) — eine
  **nach** dem ersten `create` **nicht**. *Ein zurückgerollter Import ist kein verhinderter Import;
  `autoPromoteItem` fasst Produkt, Lieferant und Preis an.*

- **Z2-W0-11b-c** · **`auto=1` KOMMT NICHT AN EINEM UNGÜLTIGEN TOKEN VORBEI.**

  **Verlangt:** Der Auto-Modus (`IdsController.php:70`) wird **erst nach** bestandener Tokenprüfung
  ausgewertet. Ein Aufruf mit `?auto=1` und ungültigem Token legt **kein** Produkt, **keinen**
  Lieferanten und **keinen** Preis an.

  **Messbefehl:**
  ```
  POST ids/callback?auto=1 ohne gueltigen Token
    -> 4xx
    -> COUNT(*) products / distributors / distributor_prices  UNVERAENDERT (drei Zaehlungen)
  ```

  **Heutiges (rotes) Ergebnis:** `auto` wird aus der Query gelesen und wirkt sofort — **die
  Auto-Anlage ist der teuerste Teil dieses Pfades und heute völlig ungeschützt.**

  **Absage-Regel:** Nur `imported_ids_items` zu zählen erfüllt (c) **nicht.** *`autoPromoteItem`
  schreibt in den Produktstamm; wer dort nicht nachzählt, hat den eigentlichen Schaden nicht
  gemessen.*

- **Z2-W0-11b-d** · **DIE CSRF-AUSNAHME FÄLLT WEG — UND DER REGULÄRE WEG LÄUFT WEITER.**

  **Verlangt:** `'ids/callback'` steht **nicht mehr** in `VerifyCsrfToken::$except`. Der reguläre
  Rückweg funktioniert unverändert: Warenkorb kommt an, Items werden angelegt.

  **Messbefehl:**
  ```
  grep -c "'ids/callback'" app/Http/Middleware/VerifyCsrfToken.php    vorher 1  ->  nachher 0
  Vollständiger Durchlauf: Absprung -> Shop-Antwort -> Items sichtbar, mit Rohausgabe
  ```

  **Heutiges (rotes) Ergebnis:** `grep -c` → **1**.

  **Absage-Regel:** Die Ausnahme stehen zu lassen *„zur Sicherheit"* erfüllt (d) **nicht.** *Dann
  gibt es zwei Wege hinein, und der ungeschützte bleibt offen — die Ausnahme ist der Befund, nicht
  ihr Umfeld.* **Fällt der Token-Weg nicht sauber ohne sie, ist das ein Messergebnis und gehört in
  den Bericht — nicht stillschweigend beides behalten.**

- **Z2-W0-11b-e** · **DIE GRUNDMENGE IST GEMESSEN, NICHT DIE EINE ROUTE.**

  **Verlangt:** Der Bericht führt **alle** Rückweg-Ausnahmen mit Stand-SHA und sagt je Eintrag:
  trifft er eine reale Route (`route:list`), und schreibt sein Controller?
  **Und er beantwortet die Gegenprobe:** `admin/offer-template-supplier/{sc}/return` steht **nicht**
  in `$except` — **läuft dieser Rückweg?**

  **Messbefehl:**
  ```
  STAND=$(git rev-parse --short HEAD)
  php artisan route:list | grep -E 'return|callback'      -> Abgleich mit $except
  je Ausnahme: Controller-Methode + Anzahl ::create/->save
  Gegenprobe: POST auf admin/offer-template-supplier/<id>/return
      -> 419 (CSRF greift)  ODER  2xx (er laeuft ohne Ausnahme)   — beides im Bericht
  ```

  **Heutiges Ergebnis:** **4 Rückwege, 3 in `$except`, 1 nicht** (`grep -c 'offer-template-supplier'`
  → **0**). `OfferSupplierSearchController` hat **4** Schreibstellen, `OfferTemplateSupplierController`
  **0**.

  **Absage-Regel:** Ein Nachweis nur für `ids/callback` erfüllt (e) **nicht.** *Dieselbe Regel wie
  bei `Z2-W0-5b`: der Name der einen Route ist nicht die Sache. **Und Teil A hat genau hier fünf
  tote Ausnahmen gefunden** — die Liste altert, also wird sie mitgemessen.*

- **Z2-W0-11b-f** · **DIE PARTNERFRAGE WIRD ERST GESTELLT, WENN SIE NÖTIG IST.**

  **Verlangt:** Der Bau **misst zuerst**, ob die `hookUrl`-Query zurückkommt. **Erst wenn sie es
  nicht tut**, geht eine Frage an den Partner — mit dem gemessenen Beleg.

  **Messbefehl:**
  ```
  Beleg im Haus zuerst: OfferSupplierSearchController.php:103 haengt bereits ?uid=... an die
      hookUrl. Kommt dieser Weg zurueck? (Log/Lauf) -> dann kommt die Query zurueck
  gconline: ein Absprung mit Testtoken, dann Log::info bei IdsController.php:35
      ('query' => $request->query()) -> steht der Token in der Rohausgabe?
  ```

  **Heutiges (rotes) Ergebnis:** nicht erhoben. *Der Log an `:35` gibt die Query bereits aus —
  **die Messung ist mit dem vorhandenen Werkzeug machbar**, sie wurde nur nie gefahren.*

  **Absage-Regel:** Die Partnerfrage **vor** der Messung zu stellen erfüllt (f) **nicht.** *Eine
  Frage an einen externen Partner kostet Tage; der Log, der sie beantworten könnte, läuft seit
  jeher mit.* **Kommt die Query nicht zurück, ist die Frage berechtigt und geht mit der Rohausgabe
  raus — nicht mit einer Vermutung.**

---

## Nicht-Ziele

- **Keine Änderung an der XML-Verarbeitung** (`parseIdsXml`) und **keine** an `autoPromoteItem`.
  *Dieses Blatt entscheidet, **ob** verarbeitet wird, nicht **wie**.*
- **Keine Änderung an den anderen drei Rückwegen.** Sie werden **gemessen** (e), nicht umgebaut.
  *Ergibt (e) einen Befund, ist er ein eigenes Blatt.*
- **Kein Entfernen der `uid` aus `OfferSupplierSearchController:104`.** Der Parameter ist tot
  (0 Lesestellen) — **aber ihn zu entfernen ist eine eigene Änderung mit eigener Rot-Probe.**
- **Keine Änderung am Schalter `RECHTE_ALLE_FUER_ALLE`.** *Diese Lücke folgt ihm ohnehin nicht — sie
  umgeht ihn, wie bei `Z2-W0-5b`.*
- **Kein Umbau auf einen anderen Standard.** Y-12 ist entschieden; das Blatt setzt ihn um.

## Nachvollzugs-Matrix (ARBEITSREGELN §5 / N3)

| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| Z2-W0-11b-a Token beim Absprung | AP-1 Token erzeugen/speichern | n.U. | n.U. |
| Z2-W0-11b-b Prüfung vor dem Schreiben | AP-2 Wache im Callback | n.U. | n.U. |
| Z2-W0-11b-c `auto=1` kommt nicht vorbei | AP-2 (drei Zählungen) | n.U. | n.U. |
| Z2-W0-11b-d Ausnahme entfernt, Weg läuft | AP-3 Ausnahme + Durchlauf | n.U. | n.U. |
| Z2-W0-11b-e Grundmenge gemessen | AP-4 Routen-/Ausnahme-Abgleich | n.U. | n.U. |
| Z2-W0-11b-f Messung vor Partnerfrage | AP-1 (Log-Beleg) | n.U. | n.U. |

**Arbeitspakete:** AP-1 Token und Absprung · AP-2 Wache im Callback · AP-3 Ausnahme und
Regeldurchlauf · AP-4 Grundmenge.

## N4 — Bedienweg

**Bedienweg: keiner im Sinne eines Bedienelements.** Der Weg ist der **vorhandene** Absprung in den
IDS-Shop und der Rückweg daraus; er ändert sich für den Benutzer **nicht**.
**Zielreifegrad:** entfällt — die Zusage ist der HTTP-Code und die Zahl der neuen Zeilen.
*Sichtbar wird das Blatt nur im Fehlerfall: ein abgelehnter Rückweg muss eine verständliche Meldung
liefern, keinen Stacktrace.*

## Rückweg

**Revert dieses einen Commits** — und in derselben Bewegung die Ausnahme wieder eintragen, sonst
steht der reguläre Rückweg still. **Das ist ausdrücklich zu prüfen, bevor der Bau als fertig gilt:**
*ein Rückweg, der den Hauptweg unterbricht, ist keiner.* Ein Schema entsteht nur, falls der Token in
einer Tabelle statt im Cache liegt — **dann ist die Migration additiv und im Bericht zu nennen.**
