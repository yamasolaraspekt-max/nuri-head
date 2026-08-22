# VOTUM Z1-E4-1 — Die Bodenplatte als eigenes Bauteil

**Votum: ABGENOMMEN (BROWSER)** — zehn Kriterien, acht voll belegt, zwei mit benanntem Restpunkt.

| | | |
|---|---|---|
| Blattstand (geltend) | `a552c423` | 22.08. 23:22:13 |
| Lieferung | `3b4e8f6b` + `4ada01dd` | `d5e88f15` liegt dazwischen und ist Z1-E0-1b (eigener Posten, von mir 23:28 abgenommen) |
| Ausgangsstand | `7500bb7d` | |
| Endstand meiner Messung | `97c610ca` | nach `git merge --ff-only fork/auto/hausplaner-integration` |
| gelesen_bis | 22.08. 23:37:19 | Pull-Zeit, 6g' |
| Reifegrad | Browserabnahme mit Bildbeleg; DB-Lease 6j vor jedem Lauf | |

---

## Vorab: mein eigener Befund von 23:33 ist erledigt — und ich sage, warum

Um 23:33:40 habe ich gemeldet, (e) sei nicht erfüllt. **Das war für `3b4e8f6b` richtig und ist
durch `4ada01dd` erledigt — 28 Sekunden vor meiner Meldung.** Der Plan-Prüfer hat mich darauf
gestoßen; ich habe es selbst nachgemessen und bestätige es unten unter (e).

Ich lasse den Befund stehen, statt ihn zu tilgen: Wer nur das Votum liest, soll sehen, dass die
Meldung existiert hat und woran sie lag. **Die Ursache war nicht Nachlässigkeit, sondern dass eine
Prüfung länger dauert als der Abstand zwischen zwei Ständen.** Die Lehre, die ich für mich ziehe
und ab jetzt anwende: **vor dem Absenden eines Mangels den Stand noch einmal ziehen.**

---

## Die zehn Kriterien

### (a) Die Leiste beginnt mit „Bodenplatte", und ein Klick erzeugt sie — **ERFÜLLT**

**Registry**, `toolPresentation.ts:76-86`, selbst gelesen im Stand `4ada01dd`:

```
{ toolId: 'bodenplatte', zone: 'fix', ordnung: 1, ... }   <- Platz 1
{ toolId: 'auswahl',     zone: 'fix', ordnung: 2, ... }   <- rueckt nach
wand 3 · fenster 4 · tuer 5 · treppe 6 · decke 7 · kontur 8 · dach 9
```

**Im Browser gemessen** (Bühne Port 8143, `ticket_testing` am Kindprozess geprüft,
Fixture `?fixture=bodenplatte`, Expertenmodus), Werkzeugspalte von oben nach unten:

```
 1. "Bodenplatte (B) — Bodenplatte auf der unte…"   y=432
 2. "Markieren (V)"    y=469        6. "Treppe (R)"   y=618
 3. "Wand (W)"         y=507        7. "Decke (K)"    y=655
 4. "Fenster (F)"      y=544        8. "Kontur (U)"   y=693
 5. "Tür (T)"          y=581        9. "Dach (D)"     y=730
```

**Bildbeleg 2D und 3D:** `belege/Z1-E4-1-a-2d.png`, `belege/Z1-E4-1-a-3d.png`. Im 3D-Bild liegt
die Platte als eigene helle Fläche zwischen den Wänden — **ein eigener Knoten, keine Decke.**

*Absage-Regel geprüft:* der Eintrag trägt **nicht** `bauteilKind: 'ceiling'` — der Test
`Z1-E4-1-a Absage-Regel` misst genau das, und die Zwischendecke desselben Geschosses bleibt
möglich (eigener Test).

**Zu meinen ersten drei Bildern:** sie waren **byte-identisch** (`7aa0ad78…`, dreimal), weil der
Viewport die CRM-Schale zeigte statt der Zeichenfläche. Selbst gefunden über `shasum`, neu
erhoben mit `scrollIntoView`. **Ein Bildbeleg, den niemand auf Verschiedenheit prüft, belegt nur,
dass ein Browser lief.**

### (b) Zweite Platte im selben Geschoss → Ablehnung mit Grund — **ERFÜLLT**

`pruefeBodenplatteProLevel` (`applyCommand.ts:134`) prüft `b.levelId === slab.levelId` — **je
Geschoss, nicht je Gebäude**, aufgerufen in ADD (:401) *und* UPDATE (:422, „falls levelId geändert
wurde"). Damit ist auch die Umgehung in zwei Schritten zu.

**Im Browser ausgelöst** — Werkzeug gewählt, in die Grundfläche geklickt:

```
✋ Level lvl-eg hat bereits eine Bodenplatte (max. 1 je Geschoss).
```

Bildbeleg `belege/Z1-E4-1-b-ablehnung.png`. *Absage-Regel:* keine gebäudeweite Sperre — der Test
`Absage-Regel: eine Platte auf einem ANDEREN Level laeuft durch (Keller bleibt moeglich)` deckt
den Kellerfall ab.

### (c) Geschoss darunter → Hinweis, kein Zwang — **CODE UND TEST BELEGT, BROWSERPROBE OFFEN**

Gebaut: `HausplanerApp.tsx:337` `bodenplatteHinweis` — *„Text oder null, keine Ablehnung"*.
Drei Tests, alle in meinem eigenen Lauf grün:

```
Z1-E4-1-c: liegt ein Geschoss darunter, kommt ein HINWEIS — und die Platte entsteht
Z1-E4-1-c Gegenprobe: auf der untersten Etage schweigt der Hinweis
Z1-E4-1-c: unterste Etage wird ueber elevation bestimmt, nicht ueber sortOrder
```

**Was ich NICHT gemessen habe:** den Hinweis im Browser. Die Fixture `etagen-hoehenkette` zeigte
`EG · ±0 mm · 1 von 1` — ein Geschoss, also kein Fall für (c); ein zweites anzulegen hätte den
Rahmen der DB-Lease gesprengt. **Das Blatt verlangt für (c) ausdrücklich den Browser.**
*Ich melde das als offen, statt aus drei grünen Tests „im Browser gesehen" zu machen.*
Nachzuholen im nächsten Takt, ~10 Minuten.

### (d1) Laden — und der Bestand lädt unverändert — **ERFÜLLT**

`SCHEMA_VERSION` **3 → 4** (`scene.types.ts:23`). `migriereSzene` erweitert um
`foundationSlabs: Array.isArray(q.foundationSlabs) ? q.foundationSlabs : []`; `aufV3` ist jetzt
**Zwischenstufe**, v1/v2 laufen durch `aufV3` und danach `aufV4`.

Der tragende Test misst, was sein Name sagt — **ich habe ihn gelesen, nicht nur laufen lassen**:
er prüft `schemaVersion === 4`, `foundationSlabs === []` und danach **jedes andere Feld einzeln**
(id, projectId, revision, levels, nodes, materials, roofs, ceilings, settings, metadata), dass das
**Original unangetastet** bleibt und dass das Migrat **ladbar** ist.

*Absage-Regel:* das JSON-Schema ist **erzeugt, nicht handgepflegt** — `schema:hausplaner:check`
läuft laut `package.json:7` und `:10` **vor** Build und Suite. Beide liefen bei mir mit rc=0, also
ist das Schema nachgezogen; ein handgepflegtes wäre an diesem Tor gefallen.

### (d2) Speichern — der PHP-Validator spiegelt die Sammlungen — **ERFÜLLT, mit einem Restpunkt**

`SceneDocumentValidator.php`: eine Sammlungstabelle für `ceilings`/`roofs`/`foundationSlabs`
(Bezeichnungen Decke/Dach/Bodenplatte), `nodes` weiter in eigener Schleife (:99).

**Selbst gelaufen** (Wegwerf-Checkout, `TEST_ROLLE=evaluator`, DB-Lease vom Lauf selbst gezogen):

```
TESTLAUF db=ticket_testing halter=evaluator quelle=SELECT_DATABASE()
Tests: 21 passed (56 assertions)
  ✓ szene mit bodenplatte wird gespeichert und vollstaendig persistiert
  ✓ szene ohne bodenplatte bleibt speicherbar
  ✓ decke auf unbekanntem level wird abgelehnt
  ✓ dach auf unbekanntem level wird abgelehnt
  ✓ bodenplatte auf unbekanntem level wird abgelehnt
  ✓ alle drei sammlungen auf bekanntem level gehen durch
```

Der letzte ist die **Gegenprobe, die ich sonst selbst hätte bauen müssen** — sie schließt aus,
dass „422" auch von einer Sammlung käme, die immer ablehnt. Die Ablehnungstests prüfen zusätzlich
den **Fehlertext** („unbekanntes Level" **und** den Sammlungsnamen) und dass die Zeile
**unverändert** bleibt.

**Der Kommentar wurde ehrlich gemacht:** aus *„spiegeln EXAKT"* wurde *„spiegeln"* plus
*„Was den Satz trägt, sind die Feature-Tests je Sammlung, nicht dieser Satz."*

> **RESTPUNKT (d2-1): die vierte Sammlung `nodes` hat keinen eigenen Feature-Test.**
> Gemessen: `grep 'gibt-es-nicht'` in `tests/` → **drei** Treffer (ceilings, roofs,
> foundationSlabs), keiner für `nodes`. Das Blatt fordert im **Verlangt-Text** „für `nodes`,
> `ceilings`, `roofs` **und** `foundationSlabs` … je Sammlung EIN PHP-Feature-Test" und im
> **Messbefehl** „vier Feature-Tests" — die **Absage-Regel** dagegen sagt „Drei Schleifen ohne die
> drei Tests".
> **Vier gegen drei, und der Widerspruch steht im Blatt, nicht im Bau.** Die *Wirkung* ist da:
> `nodes` wird seit dem Bestand geprüft (:99-104), es war nie Teil der Lücke.
> **Ich werte das nicht als Verletzung**, benenne es aber: die Hilfsmethode
> `assert422WegenUnbekanntemLevel($id, $sammlung, $eintrag, $bezeichnung)` ist schon parametrisiert
> — der fehlende Test ist ein Vierzeiler. *Planner: bitte entscheiden, ob vier oder drei gilt.*

### (e) Die Höhenkette kennt die Platte als unteres Ende — **ERFÜLLT** (in der verschärften Fassung)

Gebaut in `4ada01dd`, `pruefeBodenplatteAufbau` (`applyCommand.ts:159`), aufgerufen in **ADD (:402)
und UPDATE (:423)** — *„auch beim Ändern: der Aufbau darf nicht wegfallen"*.

**Eigener Gegenbeweis** — nicht die Tests des Generators, sondern eine selbst gebaute Probe gegen
die Fixture:

```
erdberuehrt, KEIN Aufbau (schichten [])          ABGELEHNT  bodenplatte_ohne_aufbau
erdberuehrt, schichten undefined                 ABGELEHNT  bodenplatte_ohne_aufbau
erdberuehrt, oberkanteMm 0                       ABGELEHNT  bodenplatte_oberkante_nicht_negativ
erdberuehrt, oberkanteMm +50                     ABGELEHNT  bodenplatte_oberkante_nicht_negativ
erdberuehrt, Aufbau da, oberkante -100 (SOLL)    DURCH
NICHT erdberuehrt, kein Aufbau, oberkante 0      DURCH
```

**Die Null ist eingeschlossen** — genau die Absage-Regel. **Und die letzte Zeile ist die wichtige:**
beide Regeln hängen an `erdberuehrt`, nicht am Bauteil; die Platte über einer Tiefgarage bleibt
möglich. *Wer den Zweig entfernte, verböte einen baulich normalen Fall.*

**Fixture selbst nachgerechnet:** Aufbau 120+60 = **180** · `oberkanteMm` **−180** · `dickeMm` 250
· UK **−430** · `bodenplatteOberkanteMm(schichten)` = **−180**. Und
**`floorThickness` des EG = 200 ≠ Aufbau 180** — die Berichtigung des Plan-Prüfers steht als
Zusage im Code, nicht nur als Satz.

**Bedienweg im Browser gesehen:** Fußzeile zeigt „**Fußbodenaufbau** · mm · — ohne Aufbau keine
Platte", das Zahlenfeld ist **leer**. **Kein Vorgabewert, kein stiller Default.**

**Additiv:** `hoehenkette.ts` über den E4-Bau **+62 / −0**; die Nachbesserung berührt die Datei gar
nicht. Die sechs entfernten Zeilen im Gesamtdiff gehören zu `d5e88f15` (Umbenennung, Z1-E0-1b) und
**nicht zu dieser Lieferung** — getrennt gemessen, damit sie niemandem falsch zugerechnet werden.

### (f) Heizlast-Projektion liefert die Grenzfläche Erdreich — **ERFÜLLT**

`raumProjektion.ts:143`:
```ts
boden: bodenplatte?.erdberuehrt === true
  ? { grenzflaeche: 'erdreich', bauteil_typ: 'boden' }
  : null,
```
**Aus der Platte, nicht aus der Decke** — die Absage-Regel ist eingehalten und im Code begründet
(`UWertService.php` `'boden' => [0.17, 0.00]` gegen `'decke' => [0.10, 0.04]`). Test + Gegenprobe
(`ohne Platte bleibt boden null — und ohne Erdberuehrung auch`) grün.

### (g) Das Panel behauptet nichts, was nicht geprüft ist — **ERFÜLLT**

Wortprobe: `grep -ci 'geprüft'` auf `BodenplattenPanel.tsx` → **4**. **Alle vier stehen im
Kommentar**, der das Kriterium zitiert. Nach Entfernen der Kommentare: **0 im gerenderten Code**,
Gegenprobe „Dicke" **6** (die Messung funktioniert). *Ort ≠ Wirkung — hätte ich die Datei roh
gezählt, hätte ich einen Mangel gemeldet, den es nicht gibt.*

### (h) Fach-Linsen vor der DoR — **ERFÜLLT**

Vorlage der lesenden Sitzung 18:52 (Maurer/Statiker/Software-Architekt) liegt in
`STEUERUNG-dirigent/yama-lesesitzung-VORLAGE-bodenplatte-und-zwischendecke.yaml`; der Dirigent hat
ausdrücklich keine zweite Runde verlangt.

### (i) Die Lieferung ist grün und vollständig — **ERFÜLLT**

Alles selbst gelaufen, nichts aus der Fertigmeldung übernommen:

```
npm run tsc:hausplaner        rc=0
npm run test:hausplaner       ℹ tests 1814 · ℹ pass 1814 · ℹ fail 0
npm run test:hausplaner:dom   ℹ tests 41   · ℹ pass 41   · ℹ fail 0
php artisan test (Nutzlast)   21 passed (56 assertions), db=ticket_testing
npm run build:hausplaner      rc=0
```

**Das Bündel ist byte-genau das committete:** vorher 1 549 825 Bytes, nach meinem eigenen Bau
**1 549 825 Bytes**. Ein mitcommittetes Bündel, das sich aus dem Quellstand identisch
reproduzieren lässt, ist der stärkere Beleg als „ist vorhanden".

---

## Der E2-Bezug (Auflage des Dirigenten)

**Z1-E2-1 bleibt ABGENOMMEN.** Die PHP-Lücke war **kein E2-Kriterium** — E2 verlangte die
TS-Integritätsprüfung für `ceilings`/`roofs`, und die ist dort gebaut und von mir gemessen worden.
Die Lücke entstand **dadurch**, dass die TS-Seite Regeln bekam, die PHP nicht hatte; der Kommentar
`SceneDocumentValidator.php` behauptete die Spiegelung trotzdem. **Der Dirigent hat sie E4
zugewiesen (22:52), nicht E2 aufgemacht — und in E4 ist sie jetzt geschlossen** (d2 oben, drei
Sammlungen mit Tests und Gegenprobe).

## Offen gemeldet vom Generator, von mir bestätigt

*„Der PHP-Validator prüft die Aufbau-Pflicht nicht."* — **Stimmt, selbst nachgesehen.** Der
Halbsatz d2 verfügt nur die Level-Spiegelung, der Dirigent hat „kein weiterer Umfang" gesagt.
Damit lehnt der Client ab, was der Server annimmt. **Das ist kein Mangel dieser Lieferung**, weil
es außerhalb des Kriteriums liegt — aber es ist dieselbe Klasse Lücke wie die, die E4 gerade
geschlossen hat. *Seine Frage an den Planner („Bedienregel oder Speicherweg?") ist die richtige.*

## Was mir in diesem Lauf selbst passiert ist

| | |
|---|---|
| zsh `:r`-Modifier bei `"$SHA:pfad"` | dritte Mal heute; `${SHA}:` schreiben |
| Glob ohne Treffer (`scripts/*.js`) | brach die ganze Zeile ab, erster Teil wurde nie ausgewertet |
| `BROADCAST_CONNECTION` statt `BROADCAST_DRIVER` | Variable sah richtig aus, war die falsche — 500er |
| `.env.example` nennt DB `laravel` | **der Bühnen-Riegel hat es gefangen**, am Kindprozess gemessen |
| drei byte-identische Bildbelege | über `shasum` selbst entdeckt, neu erhoben |
| „Text nennt Bodenplatte: true" auf der Fehlerseite | der Treffer kam aus dem Ignition-`curl`-Befehl, nicht aus der Szene |

**Der vierte und der sechste Punkt gehören zusammen:** beide hätten als Befund gegen den Bau
gelesen werden können, und beide waren mein Aufbau.

## Ball

**generator:** nichts aus diesem Votum — die Lieferung trägt.
**planner:** die eine Entscheidung zu (d2): vier Feature-Tests (Verlangt-Text) oder drei
(Absage-Regel)? Das Blatt sagt beides.
**evaluator (ich):** (c) im Browser nachholen, nächster Takt.
