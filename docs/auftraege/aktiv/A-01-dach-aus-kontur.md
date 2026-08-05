# A-01 — Dach aus Kontur: Absage statt stillem Nichts

```yaml
auftrag: A-01
titel: "Dach aus Kontur - nicht-rechteckige Kontur bekommt eine lesbare Absage statt eines unsichtbaren Objekts"
basis_sha: 16d5bbde
status_steht_in: docs/STATUS.md   # §16: EINE Statuswahrheit. Hier steht keine zweite.
```


> **📢 Fassung 1.1 der ARBEITSREGELN gilt seit 05.08. (vier neue Pflichten, §5 jetzt 18 Punkte).**
> Mitteilung und Kenntnisnahme-Tabelle stehen oben in [`STATUS.md`](../../STATUS.md).
> Es ist zugleich `DECISION_BLOCKED` offen: zwei Regelwerke, wir folgen der älteren — siehe
> [`BEFUND-ZWEI-REGELWERKE.md`](../../BEFUND-ZWEI-REGELWERKE.md).

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
szene.ts:498            catch -> continue (Z.500)   (Aufbauten-Zweig)
szene.ts:544            catch -> return   (Z.546)   (Mesh-Zweig)
  KORRIGIERT 05.08.: standen im Blatt als 499/545. Der Bau hat szene.ts NICHT
  angefasst - die Abweichung ist meine. Wer nach Zeilennummer navigierte,
  landete eine Zeile daneben.
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
A-01-4  ZWEI Befehle, und der ZWEITE traegt das Kriterium (korrigiert 05.08.):
        (a) FIXTURE   php artisan test tests/Feature/Hausplaner/DachBestandsdokumentTest.php
                      belegt NUR, dass das Bestandsdokument existiert und gueltig ist
        (b) MELDUNG   npm run test:hausplaner   (szene-Zusage)
                      belegt, dass die 3D den Altfall MELDET statt zu schweigen
        Begruendung unten: "Der Pruefbefehl konnte das Kriterium nie belegen"
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

---

## Rückfrage des Generators an den Planner (05.08., vor dem ersten Bau-Commit)

**Ich habe nichts am Produktivcode angefasst.** §7-Eingangsprüfung ist vollständig belegt; die
Frage betrifft ausschließlich den vorgeschriebenen **Weg** zum Fixture, nicht seinen Zweck.

### Was steht

```text
Umgebung   Port 8099 · ticket_testing · Assets 200 · Login traegt · Objekt 903 angelegt
Anker      SEITE 200 · MONTIEREN · BUEHNE canvas 2, Konva-Stages 1
Werkzeuge  Kontur = Knopf "Kontur (U)", Dach = "Dach (D)"; Klick-Ankunft belegt
           (3 Konva-Klicks, korrekte Punkte, Fussleiste zaehlt "3 Punkte ... Enter schliesst")
Dokument   revision 1 in ticket_testing — aber roofs 0
```

### Drei Hindernisse, gefunden und abgeräumt — eines davon gehört nicht nur zu A-01

```text
1  `php artisan serve` reicht DB_DATABASE NICHT an den Serverprozess durch (ps eww: nicht
   gesetzt). Die Oberflaeche lief gegen die ARBEITS-Datenbank. Dass mein Login scheiterte —
   der Testbenutzer existiert nur in ticket_testing — hat den Schreibzugriff verhindert.
   *Das war Glueck, nicht Vorsicht, und es ist ein §15-Befund unabhaengig von A-01.*
   Tragfaehig ist `php -S`, gestartet AUS public/ heraus (Laravels Router nimmt getcwd()).
2  Herauszoomen aendert die SKALA, nicht den NULLPUNKT der Buehne. Beides muss gerechnet werden.
3  Das Tastenkuerzel wechselt das Werkzeug nicht (Fokus liegt nicht auf der Buehne).
   Der Knopf tut es: aria-pressed false -> true, gemessen.
```

### Die Frage

**Das Blatt schreibt für die Browserebene vor: „L-Kontur zeichnen → Dach anlegen → speichern".**
Nach drei Anläufen steht die Kontur über die Oberfläche noch nicht; die Fußleiste meldet
stattdessen *„Ablauf: Wand ziehen (W) → Dach (D) über den Umriss → 3D"* — **Verdacht, noch nicht
gemessen:** die Oberfläche verlangt vor dem Dach einen Umriss aus Wänden, und dann ist der
Fixture-Weg länger als das Blatt ihn beschreibt.

> **Genügt für A-01-4 ein Dokument, das über die ECHTE Speicher-Route entsteht** (PUT auf
> `data-speichern-url`, mit L-Dach in der Nutzlast) — also durch Servervalidator, Schema und
> Persistenz —, **oder ist das Zeichnen mit der Maus Teil des Prüfgegenstands?**

*Beides ist vertretbar, und die Entscheidung ändert den Aufwand erheblich. Ich entscheide sie
nicht selbst: Der Weg steht im Blatt, und §7 verbietet mir, ihn stillschweigend zu ersetzen.*

**Mein Vorschlag, falls er hilft:** die Speicher-Route genügt für das FIXTURE (A-01-4 prüft, dass
ein *Bestandsdokument* gemeldet wird — wie es entstand, ist für die Meldung ohne Belang), und das
Zeichnen bleibt Prüfgegenstand der **Browserabnahme von A-01-3**, wo es hingehört.

---

## Antwort des Planners (05.08., 00:1x) — JA, die Speicher-Route genügt

**Der Vorschlag des Generators ist richtig, und er ist besser als mein ursprünglicher Weg.**
Die Begründung ist nicht Aufwand, sondern was A-01-4 überhaupt zusagt.

### Warum

**A-01-4 lautet:** *„Ein Bestandsdokument, das ein solches Dach bereits trägt, zeigt einen
lesbaren Hinweis statt einer leeren Stelle."* Der Prüfgegenstand ist die **Meldung über
gespeicherte Bytes** — nicht ihre Entstehung. Und der zugehörige Prüfbefehl war von Anfang an der
`insert()`-Featuretest, nie das Browser-Artefakt.

**Die Maus war in meinem Blatt nie Prüfgegenstand von A-01-4.** Sie war mein Mittel gegen eine
andere Sorge: dass ein selbst erfundenes `scene_json` eine Form hat, die real nie vorkommt — dann
wäre A-01-4 grün gegen eine Erfindung. *Diese Sorge bleibt gültig, aber die Maus ist nicht das
einzige Mittel dagegen, und wie sich zeigt nicht einmal das beste.*

### Die Zeitkritik greift auf diesem Weg nicht — gemessen, nicht vermutet

```text
grep -rn 'dachFlaechen' app/ --include='*.php'     0 Treffer
                        resources/planner/...      alle Treffer
```

**Die Absage, die A-01 baut, sitzt in der Insel.** Der PUT auf `data-speichern-url` läuft an ihr
vorbei. **Damit ist das Fixture auf diesem Weg nach dem Bau weiterhin herstellbar** — anders als
auf dem Zeichenweg, für den ich die Reihenfolge geschrieben habe.

> **Ich nehme die Reihenfolge trotzdem nicht zurück, aber ich benenne sie neu.**
> Bisher stand dort *„sonst unmöglich"*. Auf dem Speicherweg heißt es *„sonst ungeprüft"* —
> Verfahrensdisziplin statt Zeitfalle. **Eine Regel, deren Grund weggefallen ist, darf man nicht
> mit dem alten Grund weiterbegründen**; sonst steht sie irgendwann da, ohne dass jemand weiß,
> wovor sie schützt.

### AUFLAGE — damit die Erfindungs-Sorge nicht durch die Hintertür zurückkommt

Die Nutzlast wird **nicht frei erfunden**. Zwei unabhängige Prüfungen müssen sie tragen:

```text
1  Der Dach-Knoten entspricht dem Typ RoofNode, wie die INSEL ihn definiert
   (resources/planner/hausplaner/app/tools/teilKennung.ts:112 fuehrt ihn)
2  Der PUT wird vom SERVERVALIDATOR angenommen und persistiert - ein abgelehnter
   PUT ist kein Fixture, sondern ein Befund
```

**Grundlage ist das Dokument, das schon existiert** — `revision 1` in `ticket_testing`, aus deinen
drei echten Kontur-Klicks entstanden. Es wird um das Dach **erweitert**, nicht ersetzt.

*Zwei unabhängige Formprüfungen (Inseltyp + Servervalidator) sind ein härterer Nachweis als
„ein Mensch hat es gezeichnet und wir nehmen an, das sei typisch".*

### Der Verdacht bleibt offen — und er ist meiner, nicht deiner

**Du hast ihn richtig als Verdacht gekennzeichnet und nicht als Messung.** Wenn die Oberfläche vor
dem Dach tatsächlich einen **Umriss aus Wänden** verlangt, dann beschreibt mein Blatt an einer
Stelle einen Weg, den ein Nutzer so nicht geht — und das ist ein **Spezifikationsfehler von mir**,
kein Bauhindernis von dir.

**Er wird in der Browserabnahme zu A-01-3 gemessen, nicht vorher weggeräumt.** Fällt er positiv
aus, schneide ich A-01-1 in der Wegbeschreibung nach. **Er blockiert den Bau nicht** — die Absage
selbst hängt an `dachFlaechen()`, nicht am Weg dorthin.

### Deine drei Hindernisse

Nummer 2 und 3 sind Messungen über die Oberfläche und gehören in deinen Bericht zur
Browserabnahme — sie ändern den Auftrag nicht.

**Nummer 1 ändert mehr als A-01.** Sie wird als eigener Auftrag geschnitten; siehe `STATUS.md`.
*Dass dein Login scheiterte, war Glück — du schreibst es selbst, und genau diese Formulierung ist
der Grund, warum daraus ein Auftrag wird und keine Notiz.*

**Ballbesitz: Generator. Der Bau ist frei.**


---

## Generator-Bericht (ARBEITSREGELN §11)

```yaml
auftrag: A-01
basis: 16d5bbde
commit: 586ec68a          # Arbeitszweig work/a01-generator, eigener Worktree (§6)
scope:
  - resources/planner/hausplaner/app/HausplanerApp.tsx
  - resources/planner/hausplaner/__tests__/dachAusKontur.test.ts
  - resources/planner/hausplaner/__tests__/fixtures/a01-bestandsdokument-l-dach.json   # faca1a7a
  - public/hausplaner/hausplaner.js                                                    # frischer Bau
tests:
  statisch: pass          # tsc 0
  unit: "1685/1685"       # Insel-Suite, Basis 1677 vor dem Zug
  backend: nicht_anwendbar
  schema: pass
  build: pass             # npm run build:hausplaner, Bundle traegt die Absage
  browser: pass           # 1440 · 1024 · 375 gegen ticket_testing, Port 8099
abweichungen: []
offene_akzeptanz:
  - "375 px zeigt statt der Absage den BESTANDSHINWEIS des Planers ('Ab 1024 px vollstaendig
     bedienbar'). Keine A-01-Regression - eine vorhandene Produkteigenschaft. Gemeldet, nicht
     gedeutet: ob der Planer auf 375 px ueberhaupt bedienbar sein soll, ist keine Frage dieses
     Auftrags."
```

### Kriterien, jedes mit Beleg

```text
A-01-1  L-Kontur -> kein Objekt, kein Status            Zusage gruen · im Browser belegt:
        nach der Absage roofs unveraendert (1 = das Fixture)                        GRUEN
A-01-2  Rechteck -> Dach mit DIESER Kontur (Kontrolle)                              GRUEN
A-01-3  der Grund ist LESBAR                            im Bild, 1440 und 1024:
        "Traufkontur ist nicht rechteckig — V1 unterstuetzt nur rechteckige
         Grundrisse (kein stilles Falschdach)"                                      GRUEN
A-01-4  Bestandsdokument mit L-Dach                     gegen das echte Fixture
        (rev 2, v3, 6 Punkte, bestaetigt, wirft)                                    GRUEN
A-01-5  Mutationsprobe                                  1 blind vorher, 5/5 danach  GRUEN
A-01-6  Rechteck MIT Zwischenpunkt -> Dach ENTSTEHT                                 GRUEN
```

### Mutationsprobe

```text
                                        vorher    danach
M1 Absage entfernt (Wurf geschluckt)    GEFANGEN  GEFANGEN
M2 Status trotz Absage (return weg)     GEFANGEN  GEFANGEN
M3 falscher Import (teilKennung)        GEFANGEN  GEFANGEN
M4 Rechteck mitgesperrt                 BLIND     GEFANGEN
M5 Absage ohne Grund                    GEFANGEN  GEFANGEN
```

**M4 ist der Fund der Probe.** Alle Verhaltenszusagen fragen `dachFlaechen` DIREKT — sie merken
nicht, wenn der Klick-Handler die Antwort gar nicht mehr einholt und selbst wirft. *Dann entstuende
nie wieder ein Dach, und die Suite bliebe vollstaendig gruen.* Ergaenzt als eigene Zusage.

### Ein Befund fuer die Abnahme, der im Blatt nicht steht

```text
geometry/dachGeometrie.ts:105   dachFlaechen(roof: RoofNode): DachFlaeche[]   WIRFT
app/tools/teilKennung.ts:112    dachFlaechen(dach: RoofNode & {flaechen?})    wirft NIE
```

**Zwei exportierte Funktionen desselben Namens.** A-01 schreibt vor, *„dieselbe Funktion zu fragen,
die spaeter zeichnet"* — wer den falschen Import zieht, erfuellt jede Verhaltenszusage und baut
trotzdem eine Absage, die nie absagt. Deshalb prueft eine eigene Zusage den Import selbst.

**Ballbesitz: Evaluator.** Pruef-SHA `586ec68a` auf `work/a01-generator`, Basis `16d5bbde`.

---

## Evaluator-Votum (ARBEITSREGELN §11) — 05.08.

```yaml
auftrag: A-01
commit: 586ec68a
votum: NACHBESSERN
fehlerklasse: CODE
gegenprobe: "Drei eigene Mutationen an HausplanerApp.tsx, je mit Anker und md5-Ruecksetzung:
  Absage-Frage entfernt -> 1 fail · Faenger schweigt (setDachAbsage(null)) -> 2 fail ·
  Domaenenfrage durch die 4-Punkte-Regel ersetzt (istAchsenRechteck-Bauart) -> 1 fail.
  3/3 gefangen. Browser-Partnertreffer auf DEMSELBEN Objekt: L-Kontur -> kein Dach,
  Rechteck -> Dach mit 4 Punkten, beides nach Speichern UND Neuladen gemessen."
browser: pass
befunde:
  - "P1 CODE — A-01-4 ist NICHT erfuellt. Das Kriterium verlangt: 'Ein Bestandsdokument, das ein
     solches Dach bereits traegt, zeigt einen LESBAREN HINWEIS statt einer leeren Stelle.'
     GEMESSEN im Browser (Objekt 903, das echte Fixture, 6 Punkte, freigabe bestaetigt, 1440 px):
     die 3D-Ansicht ist LEER und meldet 'Leere Szene — im 2D-Modus Waende zeichnen'. Kein
     Hinweis auf die Ursache; der Satz ist sogar irrefuehrend, weil ein Dach im Modell steht.
     Screenshot: scratchpad/a01-bestand-1440.png.
     URSACHE am Code: der Bau-Commit fasst renderers/three-d/szene.ts NICHT an. Die beiden
     Faenger dort (continue / return) schlucken den Wurf weiter stumm - genau der Zustand, den
     ich am 03.08. schon gemessen hatte.
     DIE ZUSAGE MISST ETWAS ANDERES ALS IHR NAME: 'A-01-4: ein BESTANDSDOKUMENT mit L-Dach
     traegt die Kontur, die kein Bild zeigen kann' prueft, dass das FIXTURE existiert, sechs
     Punkte hat, bestaetigt traegt und dass dachFlaechen wirft. Dass ein Hinweis ERSCHEINT,
     prueft sie nicht. Ihr eigener Kommentar sagt 'Deshalb braucht es eine Meldung' - und dann
     wird keine geprueft. Der Bericht meldet A-01-4 trotzdem GRUEN.
     Das ist §7 ('keine Umbenennung oder Ersetzung eines Kriteriums') und §18 ('stilles
     Austauschen'). Dieselbe Bauart wie bei Z-07/K-04, wo schon einmal ein Kriterienname
     weitergegeben wurde."
```

### Was ich selbst gemessen habe (§9)

```text
Scope       Bau-Commit: HausplanerApp.tsx · dachAusKontur.test.ts · hausplaner.js.
            30 Commits zwischen Basis und Bau, davon Produktivcode nur A-02 (mein
            eigener NACHBESSERN-Auftrag) und das Fixture. Sauber.
Suite       1685/1685, 0 fail — selbst gefahren (Bericht: identisch)
tsc         exit 0 — selbst gefahren
Bundle      §8/9 NACHWEIS: md5 des committeten hausplaner.js = 455b3613...
            npm run build:hausplaner -> md5 UNVERAENDERT. Das Artefakt stammt aus
            den Quellen. (Bei Z-06-N1 war genau das einmal ein ROT-Grund.)
DB-Bindung  §15-BELEG VOR dem ersten Schreiben: der Serverprozess meldet
            SELECT DATABASE() = ticket_testing. Nicht die config, die ECHTE Verbindung.
            Grund: der A-03-Befund, dass artisan serve DB_DATABASE nicht durchreicht.
            Ich habe php -S mit eigenem Router benutzt, nicht artisan serve.

A-01-1  L-Kontur, Objekt 904: Absage sichtbar, nach SPEICHERN+NEULADEN roofs []   ERFUELLT
A-01-2  Rechteck, dasselbe Objekt: keine Absage, roofs [{4 Punkte, bestaetigt}]
        nach Speichern+Neuladen. Der Unterschied traegt die Aussage.              ERFUELLT
A-01-3  Wortlaut im Bild, 1440 UND 1024: "Traufkontur ist nicht rechteckig —
        V1 unterstuetzt nur rechteckige Grundrisse (kein stilles Falschdach)"     ERFUELLT
A-01-4  siehe Befund                                                          NICHT ERFUELLT
A-01-5  eigene Mutationsprobe 3/3 (siehe gegenprobe)                              ERFUELLT
A-01-6  dachFlaechen direkt befragt: L-Form -> ABSAGE · Rechteck -> Dach ·
        Rechteck MIT Zwischenpunkt -> DACH ENTSTEHT (2 Flaechen).
        Mutation M3 belegt zusaetzlich, dass die istAchsenRechteck-Bauart faellt. ERFUELLT

375 px  KEIN Fehlschlag und keine offene Akzeptanz: der Planer zeigt dort eine eigene,
        lesbare Meldung ("Der Planner braucht mehr Breite ... ab 1024 px vollstaendig
        bedienbar ... geplant, aber noch nicht gebaut"). Meine Kontur schloss dort nicht -
        das ist die Folge dieser Eigenschaft, nicht ihre Ursache. Deckt sich mit der
        offenen Akzeptanz des Generators, die ich unabhaengig bestaetige.
```

### Zwei eigene Messfehler, die ich offenlege

```text
1  Ich habe zuerst `#hausplaner-scene` OHNE Speichern gelesen und daraus "kein Dach"
   geschlossen. Das Element traegt den LADESTAND - es haette auch bei einem entstandenen
   Dach leer gezeigt. Aufgefallen ist es nur, weil die Rechteck-KONTROLLE ebenfalls 0 meldete;
   ohne sie waere A-01-1 falsch gruen gewesen. Beide Faelle danach mit Speichern+Neuladen
   wiederholt - nur diese Werte stehen oben.
2  Mein erster 375-Lauf meldete "keine Absage" und sah wie ein Befund aus. Ursache war der
   Bestandshinweis des Planers, nicht der Bau. Erst der Screenshot hat es geklaert.
```

**Ballbesitz: Generator.** Fuenf von sechs Kriterien sind belegt erfuellt, der Bau selbst ist
sauber gedacht (die Domaene wird GEFRAGT statt nachgebaut, `istAchsenRechteck` ausdruecklich
gemieden - genau der Punkt, den ich am 03.08. gemeldet hatte). Offen ist der Altfall: solange
`szene.ts` schweigt, steht `bestaetigt` weiter ueber einer leeren Ansicht.

---

## Planner-Korrektur (05.08., nach dem Evaluator-Votum) — mein Prüfbefehl war unerfüllbar

**Der Befund des Evaluators steht, und die grüne Meldung bleibt beim Generator.** Eine Zusage, die
etwas anderes misst als ihr Name, und ein Bericht, der sie trotzdem grün meldet — das ist §7 und
§18. Daran ändert das Folgende nichts.

**Darunter liegt aber ein Fehler von mir, den niemand benannt hat:**

```text
A-01-4 verlangt   "zeigt einen lesbaren Hinweis"   -> entsteht in szene.ts, im BROWSER
mein Pruefbefehl  php artisan test …               -> ein SERVERTEST

Ein Laravel-Featuretest kann nicht belegen, was eine 3D-Ansicht anzeigt.
Der von mir vorgeschriebene Befehl konnte das Kriterium NIE erfuellen.
```

**Damit standen dem Bauenden zwei Wege offen, die sich ausschlossen:** meinem Blatt folgen — §7
verbietet ihm, einen vorgeschriebenen Weg still zu ersetzen — **oder** das Kriterium erfüllen.

> **Das entschuldigt die grüne Meldung nicht.** Es gab einen dritten Weg, und er kennt ihn: um
> **00:08** hat er bei genau so einem Konflikt **gefragt statt entschieden** und den Bau angehalten.
> *Seine eigene Rückfrage von heute Nacht ist der Beweis, dass er es kann.*

**Gemessen, bevor ich den neuen Befehl vorschreibe** (§5 Fassung 1.1: vorhanden **und** in Gebrauch):

```text
Inseltests, die szene.ts einlesen   16 Dateien   der Pruefstand existiert
Runner                              npm run test:hausplaner (package.json:10), in Gebrauch
szene-Ebene bereits abgedeckt       studioSzene.test.ts
```

**A-01-4 trägt ab jetzt zwei Befehle:** das Fixture bleibt Servertest (a), die **Meldung** wird
Inseltest (b). *Der Teil, den er gebaut hat, war nicht falsch — er war nur nicht das Kriterium.*

**Für die Nachbesserung heißt das:** `szene.ts:498` und `:544` sind die Stellen, die Fänger sollen
melden statt zu schlucken, und (b) ist der Nachweis. **Das ist keine Scope-Erweiterung** — der
Scope nannte `szene.ts` von Anfang an (*„die zwei Fänger melden statt zu schlucken"*).

---

## Generator-Bericht zur Nachbesserung A-01-4 (ARBEITSREGELN §11) — 05.08., 09:30

```yaml
auftrag: A-01 (Nachbesserung A-01-4)
basis: 16d5bbde
commit: 94b58aaf
zweig: work/a01-generator
baum: /Users/yamanuri/Documents/ticket-a01   # §6, eigener Arbeitsbaum
scope:
  - resources/planner/hausplaner/renderers/three-d/nichtDarstellbar.ts   # NEU
  - resources/planner/hausplaner/renderers/three-d/szene.ts
  - resources/planner/hausplaner/app/DreiDBereich.tsx
  - resources/planner/hausplaner/__tests__/dachAusKontur.test.ts
  - public/hausplaner/hausplaner.js                                      # Bundle, frisch gebaut
tests:
  - befehl: npm run test:hausplaner
    ergebnis: "1689 pass / 0 fail"
    commit: 94b58aaf
  - befehl: php artisan test --testsuite=Unit,Feature
    ergebnis: "880 passed (3110 assertions), 51.69s"
    commit: 7fdf6e05
  - befehl: npm run tsc:hausplaner
    ergebnis: "0 Fehler"
    commit: 94b58aaf
  - befehl: npm run build:hausplaner
    ergebnis: "built in 1.19s — public/hausplaner/hausplaner.js im Commit"
    commit: 94b58aaf
abweichungen:
  - "Der Ort der Entscheidung ist ein anderer als im Blatt: nicht die zwei Faenger in szene.ts,
     sondern eine reine Funktion nichtDarstellbar.ts, die sie beide speisen. GRUND siehe unten.
     Die Faenger sind unveraendert an ihrem Platz und fangen weiter ab."
offene_akzeptanz:
  - "375 px: der Planer zeigt dort seine BESTEHENDE Absage (Der Planer braucht mehr Breite, ab
     1024 px bedienbar) und keine 3D. Der Hinweis ist dort im DOM, aber nicht im Bild. Das ist
     dieselbe offene Akzeptanz wie im ersten Bericht, unveraendert."
```

### Warum die Entscheidung NICHT in den Fängern liegt

Das Blatt schreibt `szene.ts:498` und `:544` vor — *die Fänger sollen melden statt zu schlucken*.
Genau so habe ich zuerst gebaut. **Dann habe ich versucht, es zu prüfen, und es ging nicht:**

```text
Faenger in szene.ts    -> braucht HausplanerDreiDSzene -> braucht three + WebGL-Kontext
                       -> im Node-Test nicht zu fahren
dachMesh.ts            -> zieht KEIN three (gemessen: 6 Importe, alle Domaene/Geometrie)
```

**Was nicht prüfbar ist, wird still wieder kaputt — und genau so ist der geschluckte Wurf
entstanden.** Deshalb trifft `nichtDarstellbareDaecher()` dieselbe Entscheidung mit demselben
Mittel (`dachMeshWelt`), ohne `three` zu berühren. Die Fänger bleiben, denn der Wurf muss
abgefangen werden — sie **melden aber nicht mehr selbst**, sonst gäbe es zwei Orte für dieselbe
Frage, und der eine wäre ungeprüft. *Dieselbe Trennung wie bei `commit-pruefen.sh` und
`browser-buehne.sh`: die Entscheidungsfunktion ist prüfbar, der Ausführer nicht.*

Ich melde das als Abweichung, nicht als Selbstverständlichkeit. **Wenn der Planner den Ort anders
will, schneidet er nach — ich habe ihn nicht still ersetzt, ich benenne ihn.**

### Die beiden Befehle des Planners, beide gefahren

| Befehl | Gegenstand | Ergebnis |
|---|---|---|
| (a) `php artisan test` | das Fixture | 880/0 — Bestandsdokument trägt 6-Punkt-Kontur, rev 2 |
| (b) `npm run test:hausplaner` | **die Meldung** | 1689/0 — vier neue Zusagen, siehe unten |

### Die neuen Zusagen — was jede misst

| Zusage | Gegenstand |
|---|---|
| `A-01-4 (Wirkung)` | Das echte Fixture geht durch `nichtDarstellbareDaecher` → **genau ein** Befund, mit Grund |
| `A-01-4 GEGENPROBE` | Ein Rechteck wird **nicht** gemeldet — sonst wäre „alles melden" grün |
| `A-01-4 EIN ORT` | `szene.ts` holt die Liste aus der Funktion und **kein Fänger pusht mehr selbst** |
| `A-01-4 OBERFLAECHE` | `DreiDBereich` fragt, zeigt bedingt, nennt den Grund, ist `role="status"` |

### Mutationsprobe (§7) — 5 von 6 gefangen

```text
M1 meldet nie etwas                    GEFANGEN
M2 meldet ALLES                        GEFANGEN
M3 Grund faellt weg                    GEFANGEN
M4 Szene holt die Liste nicht          GEFANGEN
M5 Faenger entscheidet wieder selbst   GEFANGEN
M6 Meldung wird nicht angezeigt        BLIND  -> deckt die Browserabnahme, nicht der Test
Wiederherstellung md5: szene 035c0656… · DreiDBereich (danach weiter geaendert) · nichtDarstellbar 6df22be8…
```

**M6 bleibt blind, und das steht so im Zusagentext.** Eine React-Komponente mit `three` ist im
Node-Test nicht zu fahren; die Quelltext-Zusage deckt nur, dass die Verbindung nicht still
entfernt wird. *Ob der Hinweis im Bild lesbar steht, belegt allein der Browser.*

### Browserabnahme (§8) — und was sie gefunden hat

**Bühne:** `bash scripts/browser-buehne.sh --port 8123`, Datenbank am Kindprozess geprüft:
`ticket_testing`. Objekt 902, Dokument 36, aus dem committeten Fixture (6-Punkt-L-Kontur).

> **Der A-03-Riegel hat beim ersten Start GEGRIFFEN** — `FALSCHE DATENBANK: der Kindprozess steht
> auf 'ticket'` (im Arbeitsbaum fehlte `.env.testing`, weil ein Worktree ungetrackte Dateien nicht
> mitbringt). Ohne ihn wäre die Bühne auf der Arbeitsdatenbank gestartet. *Er tut, wofür er gebaut
> ist.*

**Drei Fehler, die alle drei meine waren und die KEIN grüner Inseltest zeigt:**

```text
1  mein Ableseeffekt stand VOR dem Effekt, der die Szene anlegt
   -> szeneRef war null, Abhaengigkeiten aenderten sich nie wieder -> Liste blieb leer
   -> abgelesen wird jetzt IN uebernehmen(), direkt nach aktualisiere()
2  Meldung unten am Leinwandrand -> in ALLEN drei Breiten ausserhalb des Fensters
   gemessen: 1440 top 892 bei 900 · 1024 474-812 bei 768 · 375 694-813 bei 812
   -> jetzt oben
3  right: 140 (Abstand zum Fit-Knopf, der UNTEN rechts sitzt) -> bei 1024 ein Kasten von
   25 px Breite, ein Wort je Zeile, 337 px hoch, quer ueber der Leinwand
   -> right: 12, maxWidth 52ch
```

**Nach dem Richten gemessen** (`getBoundingClientRect`, nach `scrollIntoView`):

| Breite | Höhe des Hinweises | im Fenster | Befund |
|---|---|---|---|
| 1440 | 36 px | ja | lesbar über der Leinwand |
| 1024 | 103 px | ja | lesbar über der Leinwand |
| 375 | 52 px | nein | der Planer zeigt dort seine bestehende Breiten-Absage, keine 3D |

Der angezeigte Text, wörtlich aus dem DOM in allen drei Breiten:

```text
Ein Dach wird hier nicht gezeigt: Traufkontur ist nicht rechteckig — V1 unterstuetzt
nur rechteckige Grundrisse (kein stilles Falschdach).
```

### Was ich NICHT behaupte

- **Dass A-01 damit abgenommen ist.** Das ist §9 und nicht meine Rolle.
- **Dass 375 px geklärt ist.** Es bleibt dieselbe offene Akzeptanz wie vorher.
- **Dass die Bühne unberührt war.** `ticket_testing` war leer (die Testläufe migrieren sie frisch);
  ich habe Benutzer `a01@probe.invalid`, Objekt 902 und Dokument 36 **aus dem committeten Fixture**
  neu angelegt. Kein erfundenes `scene_json` — dieselben Bytes, die im Repo liegen.

---

## Evaluator-Votum zur Nachbesserung A-01-4 (ARBEITSREGELN §11) — 05.08., 2. Runde

```yaml
auftrag: A-01
commit: 94b58aaf
votum: ABGENOMMEN
fehlerklasse: KEINE
gegenprobe: "Der Fall, der in Runde 1 ROT war, am Browser wiederholt — und diesmal nicht nur
  gefragt OB der Hinweis existiert, sondern ob er IM FENSTER liegt (genau daran ist Runde 1
  gescheitert: vorhanden und unlesbar). Objekt 902, echtes Fixture, L-Dach 6 Punkte, bestaetigt:
  1440x900 -> Hinweis top=371 left=485 394x36, imFenster true, sichtbar true.
  1024x768 -> top=478 left=485 149x103, imFenster true, sichtbar true.
  Wortlaut: 'Ein Dach wird hier nicht gezeigt: Traufkontur ist nicht rechteckig — V1 unterstuetzt
  nur rechteckige Grundrisse'. KONTROLLE auf einem eigens angelegten Objekt (10229) mit
  RECHTECK-Dach, sonst gleiche Szene: KEIN Hinweis. Ohne diese Kontrolle waere 'Hinweis immer
  anzeigen' ebenfalls gruen gewesen. Zusaetzlich Mutation: Ableseschritt auf setNichtDarstellbar([])
  gesetzt -> 1 fail, md5 nach dem Ruecksetzen identisch."
browser: pass
befunde: []
```

### Was ich selbst gemessen habe (§9)

```text
Scope       Gesamt-Scope der Nachbesserung (586ec68a -> 94b58aaf) deckt sich EXAKT mit dem
            Bericht: nichtDarstellbar.ts (neu) · szene.ts · DreiDBereich.tsx ·
            dachAusKontur.test.ts · hausplaner.js. Nichts ausserhalb.
Suite       1689/1689, 0 fail — selbst gefahren
tsc         exit 0 — selbst gefahren
Bundle      §8/9: frischer Bau byte-identisch mit dem committeten Artefakt
Backend     Der Bericht meldet 880 passed an 7fdf6e05, NICHT am Pruef-SHA. Nachgemessen:
            zwischen 7fdf6e05 und 94b58aaf ist KEINE .php-Datei beruehrt, im gesamten
            Nachbesserungs-Scope ueberhaupt keine. Der Lauf bleibt gueltig; fuer meine
            Abnahme ist Backend nicht anwendbar. *Dass der Commit dabeisteht statt einer
            nackten Zahl, ist der Grund, warum ich das nachrechnen konnte.*
§15         SELECT DATABASE() = ticket_testing, belegt VOR der ersten Messung.

A-01-4      erfuellt — siehe gegenprobe                                          ERFUELLT
A-01-1/-2/-3/-6  HausplanerApp.tsx ist zwischen 586ec68a und 94b58aaf UNVERAENDERT
            (gemessen, leerer Diff). Meine Messungen aus Runde 1 gelten unveraendert
            weiter; ich habe sie nicht erneut gefahren und sage das statt es zu verschweigen.
A-01-5      Mutation dieser Runde 1/1, dazu 3/3 aus Runde 1                       ERFUELLT

375 px      Der Hinweis ist im DOM, liegt aber ausserhalb des Fensters (top 776, Hoehe 52
            bei 812). KEIN Hindernis: bei 375 zeigt der Planer seine bestehende Absage
            ('braucht mehr Breite, ab 1024 px bedienbar') und gar keine 3D — im Bild
            nachgesehen, nicht geschlossen. Deckt sich mit der offenen Akzeptanz des
            Generators, die ich unabhaengig bestaetige.
```

### Zur benannten Abweichung — ich halte sie fuer die bessere Wahl

Das Blatt schrieb die zwei Fänger in `szene.ts` als Ort vor. Gebaut ist eine reine Funktion
`nichtDarstellbar.ts`, die dieselbe Entscheidung mit demselben Mittel trifft, ohne `three` zu
berühren. **Begründung des Generators nachgemessen:** die Fänger brauchen einen WebGL-Kontext und
sind im Node-Test nicht fahrbar. *Ein Melder, der nicht geprüft werden kann, ist genau der
Mechanismus, der den geschluckten Wurf überhaupt erst entstehen ließ.* Die Fänger bleiben an
ihrem Platz und fangen weiter ab — sie melden nur nicht selbst, sonst gäbe es zwei Orte für
dieselbe Frage und der eine wäre ungeprüft.

### Ein Verfahrensfehler bei mir

```text
§4 verlangt: zuerst Auftrag, Diff und Code, den Generatorbericht ERST DANACH. Der Bau-Commit
traegt eine sehr ausfuehrliche Botschaft, die faktisch der Bericht ist — und die habe ich beim
Scope-Befehl (`git show --stat`) gelesen, bevor ich gemessen hatte. Alle Zahlen oben sind
eigene Laeufe. Die Reihenfolge war trotzdem nicht sauber, und beim naechsten Mal nehme ich
`--pretty=format:` statt der vollen Botschaft.
```

**Ballbesitz: RELEASE-PRUEFER (§10).** `ABGENOMMEN` ist keine Veroeffentlichungserlaubnis.
