# A-01 — Dach aus Kontur: Absage statt stillem Nichts

```yaml
auftrag: A-01
titel: "Dach aus Kontur - nicht-rechteckige Kontur bekommt eine lesbare Absage statt eines unsichtbaren Objekts"
zustand: ENTWURF
ballbesitz: plan-pruefer
basis_sha: 16d5bbde
pruef_sha: ""
release_sha: ""
letztes_votum: ""
naechster_schritt: "Plan-Pruefer prueft die Definition of Ready nach ARBEITSREGELN §5"
```

## Herkunft und Einordnung

Neu eingeordnet nach `docs/ARBEITSREGELN.md` §17. **Der Vorlaeufer `generator-auftrag-z07-dach-aus-kontur.md` ist fachlicher Nachweis, kein Prozessstand** — seine alten Statuswerte (`bereit`, `gebaut`) sind aufgehoben und werden nicht uebernommen.

**Fehlerklasse des Vorlaeufers: SPEC.** Das alte P1-Kriterium verlangte, eine L-Kontur bekomme ein L-Dach mit 68 m² statt 80 m². Das ist unerfuellbar: `geometry/dachGeometrie.ts:87` wirft `DachGeometrieUngueltig` fuer jede Kontur, die nicht ihrer Bounding-Box entspricht — eine Schranke, die es vor dem Auftrag schon gab. Der Planner hatte die Machbarkeit behauptet statt sie zu messen. Nach §12 gehoert dieser Befund dem Planner, nicht dem Generator.

## Ziel und Nutzen

Wer eine nicht-rechteckige Kontur zeichnet und ein Dach anlegt, bekommt heute **nichts Sichtbares** — und im Datenstand trotzdem ein Dach-Objekt mit dem Status `bestaetigt`. Ein bestaetigter Status auf einem Bauteil, das in keiner Ansicht existiert, ist die schaerfste Form des Herkunftsverlusts.

Nach A-01 gilt: entweder es entsteht ein sichtbares Dach, oder es entsteht **gar nichts** und der Nutzer liest den Grund.

## Nicht-Ziele

- **Keine L-, T- oder U-Daecher.** Walm, Kehle und Verschneidung sind ein eigener Auftrag mit eigener Machbarkeitsmessung.
- Keine Aenderung an `dachGeometrie.ts` selbst — die Schranke dort ist richtig und bleibt.
- Kein Umbau der Decken-Kette (Z-06 ist abgenommen und beweist, dass Konturen dort tragen).

## Ist-Zustand, an Basis 16d5bbde gemessen

```text
HausplanerApp.tsx:961   polygon: ausKontur ? letzteKontur : gebaeudeUmriss()
HausplanerApp.tsx:965   ...herkunftFuerNeuesDach(ausKontur)
  -> die Kontur wird UEBERNOMMEN, ohne Pruefung, ob die Domaene sie tragen kann
dachGeometrie.ts:87     wirft DachGeometrieUngueltig bei |kontur - bbox| / bbox > 0.01
szene.ts:499            catch -> continue      (Aufbauten-Zweig)
szene.ts:545            catch -> return        (Mesh-Zweig)
  -> die Schranke sagt woertlich "sonst kein stilles Falschdach"; beide Faenger
     machen daraus ein stilles FEHLENDES Dach
```

Die Ironie gehoert in den Auftrag: **die Sicherung funktioniert, sie wird nur nicht gehoert.**

## Scope

```text
resources/planner/hausplaner/app/…            Absage beim Anlegen, Ort und Wortlaut
resources/planner/hausplaner/renderers/three-d/szene.ts   die zwei Faenger melden statt zu schlucken
resources/planner/hausplaner/__tests__/…      die Zusagen unten
```

## Festlegung des Planners — EINE Frage, nicht zwei (SPEC, 04.08. 23:2x)

**Offener Punkt 1 ist damit geschlossen.** Der Plan-Pruefer hat gemeldet, dass die Absage nicht
festgenagelt ist; am Code nachgemessen ist das schaerfer als gedacht — **es gibt bereits ZWEI
Rechtecks-Begriffe, und sie widersprechen sich:**

```text
                          istAchsenRechteck        dachFlaechen (Kante-1, der Renderer)
                          dachAusschnitt.ts:72     dachGeometrie.ts:87
Rechteck                  true                     DURCH, 2 Flaechen
Rechteck + Zwischenpunkt  FALSE                    DURCH, 2 Flaechen      <- WIDERSPRUCH
L-Form                    false                    WIRFT
gemessen an HEAD, beide Funktionen direkt befragt
```

**Ein Rechteck mit kollinearem Zwischenpunkt** — vier Ecken, aber fuenf Punkte, weil jemand
zwischendurch geklickt hat — **wuerde von `istAchsenRechteck` abgewiesen und vom Renderer klaglos
gezeichnet.** Wer die falsche Funktion fragt, sagt dem Nutzer ab, obwohl sein Dach entstehen koennte.

### DECISION

> **Die Absage fragt `dachFlaechen()` selbst — sie prueft nicht nach.**
> Kein zweiter Rechtecks-Begriff, keine nachgebaute Regel, keine kopierte Toleranz:
> der Anlege-Pfad ruft dieselbe Funktion, die spaeter zeichnet, und behandelt ihren Wurf.

*Begruendung: eine Regel zweimal zu schreiben ist genau die zweite Wahrheit, an der dieser Auftrag
entstanden ist. Zwei Begriffe koennen auseinanderlaufen; eine Funktion kann es nicht.*

**Folge fuer den Scope:** `dachGeometrie.ts` bleibt unveraendert (Nicht-Ziel), es wird nur
**aufgerufen**. `istAchsenRechteck` wird fuer diesen Zweck **nicht** verwendet — es bleibt, wo es
heute steht (Ausschnitt-Logik), und wird nicht angefasst.

## Akzeptanzkriterien

**Jedes P1 ist an Basis 16d5bbde wirksam rot — MIT EINER benannten Ausnahme.**

**A-01-2 ist an der Basis bereits GRUEN.** Der Z-07-Vorlauf hat die Kontur-Uebernahme schon gebaut
(`HausplanerApp.tsx:961`), also entsteht das Rechteck-Dach heute korrekt. *Der Plan-Pruefer hat das
gemessen und den Selbstwiderspruch gemeldet: §5 verlangt gleichzeitig „jedes P1 wirksam rot" und
„kein Kriterium ist bereits erfuellt" — beides zusammen wuerde A-01-2 verbieten.*

> **A-01-2 ist kein Bau-Kriterium, sondern eine `must_preserve`-KONTROLLE.**
> Es wird nicht rot erwartet. Es haelt fest, dass die Absage aus A-01-1 den **funktionierenden**
> Fall nicht mitreisst — ohne es waere „gar kein Dach mehr" eine gruene Loesung.

**Von der Rot-Pflicht ausgenommen: A-01-2.** Alle uebrigen P1 sind an der Basis wirksam rot, und
der Plan-Pruefer bestaetigt das vor dem Bau.

**A-01-1 (P1, negativ):** Nicht-rechteckige Kontur -> **kein Dach-Objekt**. Die Anzahl der Dächer in der Szene bleibt unverändert, es wird kein Status geschrieben, insbesondere kein `bestaetigt`.

**A-01-2 (P1, positiv, Kontrolle):** Rechteck-Kontur -> Dach entsteht und folgt der **gezeichneten Kontur**, nicht der Bounding-Box aller Wände. *Erst der Unterschied zwischen A-01-1 und A-01-2 macht die Aussage.*

**A-01-3 (P1, sichtbar):** Der Nutzer liest den Grund der Absage. Kein stiller Fehlschlag, keine reine Konsolenmeldung.

**A-01-4 (P1, Bestand):** Ein Bestandsdokument, das ein solches Dach bereits trägt, zeigt einen lesbaren Hinweis statt einer leeren Stelle. *Die Absage aus A-01-1 wirkt erst beim Anlegen — 3D-Ansichten müssen den Altfall trotzdem melden.*

**A-01-5 (P1, Mutationsprobe):** Mindestens fünf Mutationen werden blind eingespielt und müssen fallen, darunter: Absage entfernt · Status trotz Absage geschrieben · Fänger schweigt wieder · Rechteck-Fall mitgesperrt · Toleranz so geweitet, dass ein L durchkommt.

**A-01-6 (P1, die FALSCHE Absage):** Ein Rechteck **mit kollinearem Zwischenpunkt** (fuenf Punkte,
vier Ecken) -> **Dach ENTSTEHT.** *Ohne dieses Kriterium besteht ein Bau, der `istAchsenRechteck`
fragt, alle uebrigen Zusagen — L-Form rot, glattes Rechteck gruen — und weist trotzdem Daecher ab,
die der Renderer zeichnen wuerde.* **Gemeldet vom Plan-Pruefer, am Code bestaetigt.**
Pruefbefehl: die Anlege-Entscheidung mit dieser Kontur gegen `dachFlaechen` — beide muessen
dasselbe sagen.

## Festlegung des Planners — der Fixture-Weg fuer A-01-4 (04.08. 23:3x)

**Offener Punkt 2 ist damit geschlossen.** Gemessen, was es schon gibt (§2 Wiederverwendung):

```text
grep -rln 'hausplaner_documents' database/ tests/ app/Console
  tests/Feature/Hausplaner/HausplanerSpeichernNutzlastTest.php
  tests/Feature/Hausplaner/UebernahmeKnopfTest.php
  tests/Feature/Hausplaner/UebernehmeSzeneInAuslegungTest.php
  tests/Feature/Hausplaner/SnapshotRueckwegVersionTest.php
  -> vier Testdateien legen Dokumente per DB::table(...)->insert() an
Seeder mit Hausplaner-Bezug: KEINER
```

### DECISION — zwei Ebenen, ein Weg je Ebene

```text
TESTEBENE     Das vorhandene Muster wird wiederverwendet, nicht neu erfunden:
              DB::table('hausplaner_documents')->insert() mit L-Kontur im scene_json,
              wie in SnapshotRueckwegVersionTest. Laeuft gegen ticket_testing (§15),
              reproduzierbar, kein Handgriff. KEIN neuer Seeder.
BROWSEREBENE  Das Dokument wird VOR dem Bau ueber die Oberflaeche erzeugt
              (L-Kontur zeichnen -> Dach anlegen -> speichern) und sein scene_json
              als Datei im Repo abgelegt.
```

### ⚠ Das Zeitkritische, das bisher in keinem Blatt stand

> **Nach dem Bau von A-01 laesst sich dieses Fixture nicht mehr herstellen.**
> Die Absage ist genau die Funktion, die das Anlegen verhindert. Wer erst baut und dann das
> Bestandsdokument sucht, hat A-01-4 dauerhaft unpruefbar gemacht — **der Bau zerstoert seinen
> eigenen Pruefstand.**

**Deshalb ist die Reihenfolge Teil des Auftrags, nicht Geschmackssache:**

```text
1  Fixture erzeugen und als Datei ablegen   VOR dem ersten Bau-Commit
2  Ablage als Beleg im Bericht nennen       Pfad + Kontur-Punkte
3  erst danach die Absage bauen
```

*Das ist dieselbe Klasse wie ein Vorher-Wert, den niemand festgehalten hat — nur teurer, weil
hier nicht eine Zahl fehlt, sondern ein Zustand, den es danach nirgends mehr gibt.*

## Pruefbefehle je Kriterium (Nachtrag 4) und Testdaten (Nachtrag 5)

**§5 verlangt beides ausdruecklich; A-01-6 hatte einen Befehl, die uebrigen nicht.**

**KORRIGIERT 04.08. 23:4x auf den Befund des Plan-Pruefers.** Meine erste Fassung nannte fuer
A-01-1/-2/-6 ein blankes `node --test <datei>.ts`. **Er hat es geprobt, ich danach auch:**

```text
node --test .../decke.test.ts                          -> 'test failed', 0 Zusagen gelaufen
<runner> --import test-register.mjs --test <dieselbe>  -> 13 pass / 0 fail
```

*Die Insel ist TypeScript und braucht den Loader — ohne ihn stirbt der Lauf vor der ersten Zusage.
Ein Bau, der A-01 korrekt umsetzt, waere an meinem Befehl gescheitert (F-20: Befehl auf einem Boden
gemessen, den es nicht gibt). Ich bin heute als Generator selbst darueber gestolpert und habe es
trotzdem ins Blatt geschrieben.*

```text
RUNNER  ./scripts/node-runtime.sh --experimental-strip-types \
          --import ./resources/planner/hausplaner/test-register.mjs --test <datei>
        Das Qualitaetstor faehrt ohnehin `npm run test:hausplaner` (ganze Suite + Schema-Check);
        der Einzelaufruf oben ist fuer die schnelle Rueckmeldung waehrend des Baus.

A-01-1  <RUNNER> resources/planner/hausplaner/__tests__/dachAusKontur.test.ts
        Testname: "A-01-1: L-Kontur erzeugt KEIN Dach-Objekt und keinen Status"
        misst: roofs.length vorher == nachher · kein 'bestaetigt' geschrieben
A-01-2  <RUNNER> dieselbe Datei
        Testname: "A-01-2 KONTROLLE: Rechteck-Kontur erzeugt ein Dach mit DIESER Kontur"
        misst: polygon === gezeichnete Kontur, NICHT gebaeudeUmriss()   (must_preserve)
A-01-3  Browserabnahme, kein Unit-Befehl. Sichtbarkeitsnachweis: Screenshot + Wortlaut
        im Bericht. Ein console.error allein erfuellt A-01-3 NICHT.
A-01-4  php artisan test tests/Feature/Hausplaner/DachBestandsdokumentTest.php
        Testname: "A-01-4: Bestandsdokument mit L-Dach laedt und meldet"
        Fixture nach dem Weg oben (insert()-Muster, ticket_testing)
A-01-5  Mutationsprobe, kein fester Befehl - Verfahren: je Mutation die Suite fahren,
        Datei danach md5-identisch wiederherstellen, Ergebnis im Bericht als Tabelle
A-01-6  <RUNNER> dieselbe Datei
        Testname: "A-01-6: Rechteck MIT Zwischenpunkt erzeugt ein Dach"
        misst: Anlege-Entscheidung == dachFlaechen()-Verhalten fuer dieselbe Kontur
```

**Die Testdatei `__tests__/dachAusKontur.test.ts` existiert noch nicht** — sie entsteht mit dem
Bau. *Der Name steht hier, damit Bericht und Abnahme denselben Ort meinen.*

### Testdaten, Rolle und Browserpfad (Nachtrag 5)

```text
Flaeche      /admin/hausplaner/objekt/{objekt}   -> objekt.blade.php
             Grund: sie traegt data-speichern-url (:157). studio.blade speichert NICHT (:3),
             eine Sichtprobe dort kann Persistenz nicht zeigen.
Rolle        ein Benutzer mit is_admin=1; im Test ueber User::factory()->create(['is_admin'=>1]),
             wie in den vier vorhandenen Hausplaner-Featuretests.
Objekt       NICHT ein bestehendes aus der Arbeits-DB benennen. Der Bauende legt ein eigenes an
             und nennt dessen alternative_id im Bericht.
             *Ein Blatt, das eine gewachsene Objekt-Id festschreibt, ist beim naechsten Lauf
             falsch - und niemand merkt es, weil die Id ja existiert.*
Viewports    1440 · 1024 · 375   (§8)
```

## Qualitätstor (ARBEITSREGELN §8)

Sichtbare Änderung **und** Datenwirkung — beide Zusatzblöcke gelten:

```text
Grundtor        Scope-Diff · statische Analyse · tsc · Unit · DOM · Schema · frischer Build ·
                getrackte Artefakte aus aktuellen Quellen · nichts ausserhalb des Scopes
Browser         objekt.blade (die Fläche MIT data-speichern-url:157; studio speichert NICHT,
                studio.blade:3) · Viewports 1440/1024/375 · Konsole ohne neue Fehler ·
                Screenshots der Absage
Persistenz      Speichern und Neuladen · abgewiesener Fall schreibt NICHTS · Bestandsdokument
                mit nicht-rechteckigem Dach lädt und meldet
```

## Rückweg

Die Änderung ist additiv (eine Prüfung vor dem Anlegen, zwei Meldungen statt zweier stiller Zweige). Rückweg ist das Zurücknehmen des Commits; es entstehen keine neuen persistierten Felder und keine Migration.

## Offene Punkte für den Plan-Prüfer

1. **GESCHLOSSEN 04.08. 23:2x durch den Planner.** Die WELCHE-Frage ist entschieden (Abschnitt Festlegung: `dachFlaechen` wird gefragt) und als A-01-6 messbar gemacht. **Der WORTLAUT bleibt offen und ist Absicht** — er gehoert zur sichtbaren Wirkung und wird in der Browserabnahme (§8) beurteilt, nicht vorab diktiert.
2. **GESCHLOSSEN 04.08. 23:3x durch den Planner.** Zwei Ebenen, ein Weg je Ebene (Abschnitt Fixture-Weg): Testebene nutzt das vorhandene insert()-Muster, Browserebene erzeugt das Dokument VOR dem Bau und legt es als Datei ab. **Die Reihenfolge ist Teil des Auftrags** — nach dem Bau ist das Fixture nicht mehr herstellbar.
3. Der Z-07-Code aus dem Vorlauf liegt bereits im Zweig (`herkunftFuerNeuesDach`, 2 Stellen in der App). Der Plan-Prüfer entscheidet, ob er als Ist-Zustand stehen bleibt oder ob A-01 auf einer Basis ohne ihn geschnitten wird.

---

## DoR-Prüfbericht des Plan-Prüfers (ARBEITSREGELN §5) — 04.08.

```yaml
auftrag: A-01
basis_geprueft: 16d5bbde   # existiert; Insel-Drift seit Basis: 0 Dateien (git diff --name-only)
votum: ENTWURF_BLEIBT      # naeher an BEREIT als an SPEC_BLOCKED - fuenf benannte Nachtraege
ballbesitz_danach: planner
```

**Bestätigt (selbst gemessen an Basis = HEAD-Inselstand):**
- Ist-Beleg wortgleich am Code: `HausplanerApp.tsx:961/:965` (Kontur ungeprüft übernommen,
  Status aus der Domäne), `dachGeometrie.ts:87` (Kante-1-Wurf), `szene.ts:499 continue /
  :545 return` (beide Fänger schlucken).
- **A-01-1 ist an der Basis wirksam rot:** L-Kontur → Dach-Objekt entsteht MIT `bestaetigt`,
  keine Ansicht zeigt es. A-01-3/-4/-5 ebenfalls rot (kein Absage-Ort, kein Melde-Weg,
  keine Mutationen). Rückweg additiv, Nicht-Ziele klar, Scope klein, Konflikt: einzige
  Datei-Überschneidung ist `szene.ts` mit dem späteren N2 — bei „ein Auftrag zugleich" (§3)
  unkritisch.
- **Entscheidung zu Punkt 3:** Der Z-07-Code **bleibt als Ist-Zustand** (0 Drift seit Basis,
  handwerklich abgenommen; eine Basis ohne ihn würde abgenommene Arbeit verwerfen).
  Basis-SHA 16d5bbde bestätigt.

**Fünf Nachträge, bevor `BEREIT` gesetzt werden kann (je einer pro §5-Lücke):**

1. **Antwort auf Punkt 1 — die Festlegung gehört VOR den Bau:** Die Absage stellt
   **dieselbe Frage wie der Renderer** — die Kante-1-Flächenprüfung aus `dachGeometrie.ts`
   wird als Funktion **wiederverwendet** (kein zweiter Rechtecks-Begriff; `istAchsenRechteck`
   aus `dachAusschnitt.ts:72` widerspricht ihr beim Rechteck mit kollinearem Zwischenpunkt —
   gemessen, Evaluator 3545321a). **Pflicht-Testfall dazu: Rechteck MIT Zwischenpunkt →
   Dach ENTSTEHT.** Ohne diese Festlegung ist die Grenze zwischen A-01-1 und A-01-2 nicht
   messbar — der Auftrag bleibt sonst SPEC-anfällig genau an der Stelle, an der der
   Vorläufer fiel.
2. **A-01-2 als KONTROLLE/must_preserve kennzeichnen:** Es ist an der Basis **bereits grün**
   (`polygon: ausKontur ? letzteKontur : …` steht seit Z-07). Die Blatt-Zeile „Jedes P1 ist
   an Basis wirksam rot" widerspricht sich sonst selbst; §5 verlangt „kein Kriterium bereits
   erfüllt" — als ausdrücklich benannte Kontrolle ist es zulässig, als Arbeits-Kriterium nicht.
3. **A-01-4 Fixture-Weg benennen** (Antwort auf Punkt 2): Es existiert **kein**
   `__tests__/fixtures/`-Bestand (gemessen). Vorschlag: reproduzierbares v3-Szenen-Fixture
   mit L-Dach als JSON unter `__tests__/fixtures/` + für die Browser-Probe ein Seed-Weg in
   `ticket_testing` (objekt.blade). Der Weg muss im Blatt stehen, nicht im Kopf des Bauenden.
4. **Prüfbefehle je Kriterium ergänzen** (§5: „jeder Prüfbefehl auf Syntax und Aussagekraft
   geprüft"): heute tragen A-01-1…5 keine Befehle/Testnamen. Mindestens: Testdatei/Filter je
   Kriterium, A-01-1 als Szene-Zähl-Assertion, A-01-5 mit den fünf benannten Mutationen.
5. **Browser-Testdaten konkretisieren:** Rolle/Route sind benannt (objekt.blade,
   data-speichern-url), es fehlt das benannte Test-Objekt (welches Objekt in ticket_testing,
   welcher Login-Weg) — §5 verlangt Testdaten ausdrücklich.

*Rollenansage: geprüft als Plan-Prüfer (nicht Planner dieses Blatts, nicht Bauender).
Messbefehle im Bericht; nichts am Produktivcode verändert.*

### Vorabmessungen des Plan-Prüfers zu Nachtrag 3 und 5 (04.08., Zusatz)

**Nachtrag 3 — der Fixture-Weg ist MACHBAR, gemessen:** Das Server-Schema
(`domain/scene-document-v2.schema.json`) beschränkt `roofs[].polygon` nur auf
`minItems: 3` Punkte — **keine Rechtecks-Bedingung**; auch
`SpeichereHausplanerDokumentRequest` prüft kein Dach-Polygon (grep roof/polygon: 0 Treffer).
Ein Dokument mit L-Dach passiert also Server und Persistenz und erzeugt exakt den Altfall,
den A-01-4 melden soll. *Der Fixture-Weg scheitert an nichts Vorhandenem.*

**Nachtrag 5 — ein reproduzierbares Muster existiert bereits:**
`tests/Feature/Hausplaner/UebernahmeKnopfTest::objekt(seed)` legt Customer+Objekt
deterministisch an (Seed-700-Muster) — für die PHP-Seite direkt wiederverwendbar.
Für die Browser-Probe fehlt nur die Benennung: dasselbe Seed-Muster einmal gegen
`ticket_testing` ausführen + Admin-Login auf `objekt.blade`. Beides gehört als je ein
Satz ins Blatt, dann ist §5 „Testdaten benannt" erfüllt.
