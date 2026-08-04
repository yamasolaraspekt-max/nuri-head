# STATUS — der eine gültige Arbeitsstand

**Autorität:** [`docs/ARBEITSREGELN.md`](ARBEITSREGELN.md) §16. Diese Seite wird **überschrieben,
nicht angehängt**. Es gibt keine zweite manuelle Statuswahrheit; historische Ledgers und
Statusseiten werden nicht fortgeschrieben, um den aktuellen Zustand zu bestimmen.

**Angelegt:** 04.08.2026 durch den Planner als Teil des Übergangs nach §17.

---

## Aktiver Auftrag

```yaml
auftrag: A-01
titel: "Dach aus Kontur - nicht-rechteckige Kontur bekommt eine lesbare Absage"
datei: docs/auftraege/aktiv/A-01-dach-aus-kontur.md
zustand: BEREIT
ballbesitz: generator
basis_sha: 16d5bbde
pruef_sha: ""
release_sha: ""
letztes_votum: "plan-pruefer 04.08. 23:5x (3. Runde): BEREIT. Die drei korrigierten Pruefbefehle selbst geprobt — die Runner-Form laeuft (decke.test.ts sauber, gleiche Form wie Planner-Messung 13/0), das blanke node --test faellt belegt. Alle 15 §5-Punkte erfuellt; A-01-2 als benannte must_preserve-Ausnahme von der Rot-Pflicht."
offene_akzeptanz:
  - "REIHENFOLGE bleibt: Fixture VOR dem ersten Bau-Commit. ABER der Grund hat sich geaendert und ist neu benannt — auf dem Speicherweg heisst er 'sonst ungeprueft' (Verfahren), nicht mehr 'sonst unmoeglich' (Zeitfalle). Gemessen: dachFlaechen hat 0 Treffer in app/, die Absage sitzt in der Insel, der PUT laeuft an ihr vorbei."
  - "AUFLAGE zum Fixture: die Nutzlast wird nicht frei erfunden. Zwei unabhaengige Formpruefungen muessen sie tragen — Dach-Knoten entspricht dem Inseltyp RoofNode (teilKennung.ts:112) UND der Servervalidator nimmt den PUT an. Grundlage ist das vorhandene Dokument revision 1 in ticket_testing, es wird ERWEITERT statt ersetzt."
ballwechsel: "generator -> planner 05.08. 00:08 (Rueckfrage) · planner -> generator 05.08. 00:1x (beantwortet)"
naechster_schritt: "Generator: Bau ist frei. Fixture ueber die Speicher-Route mit beiden Formpruefungen, dann bauen; Meldung CODE_FERTIG mit Basis- und Bau-SHA"
rueckfrage_beantwortet:
  - "FRAGE des Generators (00:08): genuegt fuers A-01-4-Fixture die echte Speicher-Route, oder ist das Zeichnen mit der Maus Teil des Pruefgegenstands?"
  - "ANTWORT (00:1x): JA, die Speicher-Route genuegt. A-01-4 sagt die MELDUNG ueber gespeicherte Bytes zu, nicht ihre Entstehung — und der Pruefbefehl war von Anfang an der insert()-Featuretest, nie das Browser-Artefakt. Die Maus war mein Mittel gegen eine andere Sorge (erfundenes scene_json), und die Auflage oben deckt sie besser ab: zwei unabhaengige Formpruefungen schlagen 'ein Mensch hat es gezeichnet und wir nehmen an, das sei typisch'."
  - "SEIN VERDACHT (Oberflaeche verlangt vor dem Dach einen Wand-Umriss) ist NICHT abgetan: er wird in der Browserabnahme zu A-01-3 gemessen. Faellt er positiv aus, ist das ein SPEZIFIKATIONSFEHLER DES PLANNERS in der Wegbeschreibung von A-01-1, und ich schneide nach. Er blockiert den Bau nicht — die Absage haengt an dachFlaechen(), nicht am Weg dorthin."
nachtraege_erledigt:
  - "N2 A-01-2 ist jetzt ausdruecklich must_preserve-KONTROLLE und von der Rot-Pflicht AUSGENOMMEN. Begruendung im Blatt: ohne das Kriterium waere 'gar kein Dach mehr' eine gruene Loesung."
  - "N3 Fixture-Weg steht (Abschnitt 'Fixture-Weg fuer A-01-4', 23:3x): Testebene nutzt das vorhandene insert()-Muster der vier Hausplaner-Featuretests, KEIN neuer Seeder. Browserebene erzeugt das Dokument VOR dem Bau. Die REIHENFOLGE ist Teil des Auftrags."
  - "N4 Pruefbefehl und Testname je Kriterium A-01-1..6 eingetragen; A-01-3 ausdruecklich als Browser-Nachweis ohne Unit-Befehl gekennzeichnet (ein console.error erfuellt es NICHT)."
  - "N5 Flaeche objekt.blade (traegt data-speichern-url:157, studio speichert nicht), Rolle is_admin ueber User::factory, Viewports 1440/1024/375. Das Test-OBJEKT wird bewusst NICHT festgeschrieben - der Bauende legt eines an und nennt die id im Bericht."
geschlossen_seit_anlage:
  - "Ort/Wortlaut der Absage: die WELCHE-Frage ist entschieden (dachFlaechen wird gefragt, kein zweiter Rechtecks-Begriff), messbar als A-01-6. Der WORTLAUT bleibt bewusst offen - er gehört in die Browserabnahme (§8)."
  - "Doppelführung Z-07 / A-01: A-01 führt. §16 kennt nur eine Statuswahrheit, und das ist diese Seite."
```

`IN_ARBEIT` ist derzeit **kein** Auftrag. Nach §3 darf es höchstens einen geben.

---

## In Planprüfung

```yaml
auftrag: A-02
titel: "Commit-Tor: Halter fragen statt Ruhe raten - und bei Blockade ENV_BLOCKED melden statt raeumen"
datei: docs/auftraege/aktiv/A-02-lock-halter-statt-ruhe.md
zustand: BEREIT
ballbesitz: generator
basis_sha: 93a9691f
anlass: "P0-Vorfall 04.08. 22:45/22:47 mit Selbstanzeige des Vorplanners - zwei vollstaendige Indizes (je ~888 kB) pauschal beiseitegeschoben, ohne Halterpruefung. Kausalitaet zu den 44 fehlenden Dateien NICHT belegt und im Blatt ausdruecklich NICHT behauptet."
letztes_votum: "plan-pruefer 05.08. (1. DoR-Runde A-02): ENTWURF bleibt, ZWEI Restpunkte. GEMESSEN an HEAD 42904acb (Drift zur Basis = nur der Schnitt-Commit, 0 scripts/-Aenderungen): ||-Bedingung, :103-Kommentar, lsof nur im Kommentar, ENV_BLOCKED 0, 4 beiseite — alle Ist-Belege woertlich bestaetigt. OFFENER PUNKT 1 BEANTWORTET: A-02-1 ist an der Basis GRUEN (Zusagen-Suite selbst gefahren: 23 pass / 0 fail, die Inhalt+300s-Zusage traegt heute) — die Vermutung des Planners stimmt."
offene_akzeptanz: []
erledigt_05_08:
  - "Rest 1 EINGETRAGEN: A-02-1 ist jetzt must_preserve-KONTROLLE, ausdruecklich von der Rot-Pflicht ausgenommen. Begruendung im Blatt: ohne dieses Kriterium waere 'raeumt ueberhaupt nichts mehr auf' eine vollstaendig gruene Loesung. Gleiche Bauart wie A-01-2."
  - "Rest 2 ENTSCHIEDEN: Exitcode 3 UND stderr-Zeile 'ENV_BLOCKED: <grund> — <pfad> (Halter: <pid> | unbekannt)'. Beides ist Zusage, der Test prueft beides. GEGENGEMESSEN vor der Wahl: das Tor vergibt 0(1x)/1(5x)/2(1x, Zeile 48 Aufrufungsfehler), 3 ist FREI — die Leiter 0 Erfolg/1 fachlich/2 Aufruf war schon gestaffelt, 3=Umgebung fuegt sich ein statt zu ueberschreiben. Textparsen allein verworfen: F-09."
  - "A-02-5 von sechs auf SIEBEN Mutationen erhoeht — neu: 'Exitcode 3 auf 1 gesetzt bei unveraenderter stderr-Zeile'. Ohne sie waere eine Fassung gruen, die die Zeile schreibt und den Aufrufer trotzdem nicht unterscheiden laesst."
naechster_schritt: "BEREIT gesetzt (plan-pruefer 05.08. 00:1x, Exitcode-Staffel gegengemessen: 0 1x/1 5x/2 1x, 3 frei). WARTESCHLANGE hinter A-01 — Generator zieht erst nach A-01-Abnahme (§3)."
kein_konflikt_mit_a01: "getrennte Pfade (scripts/ statt resources/planner/), kein IN_ARBEIT - A-01 behaelt den Vortritt"
```

**Warum der Planner ihn schneidet und nicht der Verursacher:** er hat es selbst abgelehnt —
*„ein Verursacher, der seine eigene Barriere schneidet, wäre genau der Interessenkonflikt, den die
Rollentrennung verhindern soll."* Er hat damit recht, und die Übergabe ist hier vermerkt, damit
sie nicht als stille Weiterreichung erscheint.

---

## In Planprüfung — A-03

```yaml
auftrag: A-03
titel: "Browser-Buehne: der sichere Aufruf wird erzwungen, der lautlose wird laut"
datei: docs/auftraege/aktiv/A-03-browser-buehne-testdatenbank.md
zustand: ENTWURF
ballbesitz: planner
basis_sha: 89d69c13
anlass: "§15-Befund des Generators, 05.08. 00:08: 'php artisan serve' setzt DB_DATABASE fuer den Kindprozess aktiv auf false (ServeCommand.php:179, 13 passthroughVariables, 0 davon DB_). Die Buehne lief gegen die ARBEITS-Datenbank ticket. Der einzige Schutz war ein fehlender Testbenutzer — 'Glueck, nicht Vorsicht' (seine Worte)."
gemessen: "Kind-Umgebung mit env -i nachgebildet: 'DB_DATABASE=... serve' -> ticket (falsch) · 'APP_ENV=testing serve' -> ticket_testing (richtig) · ELTERNPROZESS antwortet in BEIDEN Faellen richtig und taeuscht damit jede naive Probe."
besonderheit: "Es wird KEIN Durchreichen gebaut. Ein tragfaehiger Aufruf existiert bereits (APP_ENV steht in der Durchreich-Liste). Gebaut wird nur der Riegel darum: der falsche Aufruf ist heute LAUTLOS."
letztes_votum: "plan-pruefer 05.08. 00:2x (1. DoR-Runde A-03): ENTWURF bleibt, ZWEI Restpunkte. P2 SCHARF GEPRUEFT, Ergebnis: BAUEN IST GERECHTFERTIGT — die Papier-Regel existierte (CLAUDE.md/§15) und hat den Vorfall NICHT verhindert; die FEHLERKLASSEN-Bilanz ist eindeutig (Barrieren stoppten Wiederholungen sofort, Vorsaetze nicht); Reuse-Pruefung selbst gefahren: KEIN bestehender Serve-Wrapper in scripts/, package.json oder ANKER-BROWSER (0 Treffer). Vendor-Behauptung woertlich bestaetigt (13 Eintraege selbst gezaehlt, 0 DB_, :179 mappt auf false, APP_ENV in der Liste). NICHT NOTWENDIG waere hier das falsche Votum."
offene_akzeptanz:
  - "Rest 1 (die Konsequenz aus P2): der Riegel braucht seine VERANKERUNG — ANKER-BROWSER.md schreibt heute KEINEN Startweg vor (gemessen 0 Treffer). Ohne eine Zeile dort, die scripts/browser-buehne.sh zum EINEN benannten Weg macht, ist der Riegel ein Werkzeug, das niemand aufgerufen bekommt — dieselbe Papier-Falle, nur eine Ebene hoeher. Scope um die Anker-Zeile erweitern (oder begruendet ausschliessen)."
  - "Rest 2 (Testnamen-Liste, Empfehlung): NUR ticket_testing. wberechnung_mysql_test gehoert einer ANDEREN App — eine ticket-Buehne auf fremder Test-DB waere zwar harmlos, aber sinnlos erlaubt; die Liste bleibt minimal und waechst additiv mit Eintrag."
naechster_schritt: "Planner traegt Rest 1+2 ein, dann setzt der Plan-Pruefer BEREIT (Warteschlange hinter A-01/A-02)"
```

---

## Ballbesitz-Uhr — Stand 05.08. 00:0x

| Rolle | Gegenstand | seit | läuft oder still |
|---|---|---|---|
| **Generator** | A-01, Bau frei | 05.08. 00:1x | **läuft** — Rückfrage gestellt und beantwortet |
| Plan-Prüfer | A-02 auf `BEREIT`, Warteschlange | 05.08. 00:1x | frei |
| Planner | A-03 aus dem §15-Befund | 05.08. 00:1x | läuft |

### Die VIERTE Ursache für einen stillen Baum — heute belegt

**Ich hatte um 00:0x notiert: Generator still, 17 min, 0 Dateien.** Die Messung stimmte. Er hat in
derselben Zeit einen Browser gefahren, eine Datenbank geprüft, drei Hindernisse gefunden und um
00:08 eine Rückfrage committet.

```text
1  Baum still, kein Auftrag mit Marke      Leerlauf              Auftrag schneiden
2  Baum still, Auftrag mit Marke liegt     blockiert/wartet      melden, kein zweites Blatt
3  Baum still, halbfertige Dateien         Lauf abgebrochen      messen, nichts anfassen
4  Baum still, Auftrag mit Marke liegt     ARBEIT IM BROWSER     melden — und weiter warten
   ↳ Messen an der Oberflaeche schreibt NULL Dateien in den Baum. Ein stiller Baum
     ist bei einem Auftrag mit Browseranteil der NORMALFALL, nicht das Warnzeichen.
```

> **Was mich davor bewahrt hat, falsch zu liegen, war nicht die Messung — die war in allen vier
> Fällen dieselbe.** Es war, dass ich sie **gemeldet und nicht gedeutet** habe. Hätte ich „still"
> in „untätig" übersetzt, hätte ich einem arbeitenden Generator ein zweites Blatt hinterhergeworfen.
> *Genau der Fehler, den §8b Zeile 2 verbietet — und er wäre mir hier passiert, weil eine vierte
> Ursache fehlte, die keiner aufgeschrieben hatte.*

---

## Was aus dem Bestand übernommen wurde — und was nicht

Nach §17 werden alte Statuswerte **nicht** automatisch übernommen. Der fachliche Code bleibt, die
Prozessstände sind neu einzuordnen.

| Vorlauf | fachlicher Stand im Zweig | Prozessstand nach §17 |
|---|---|---|
| Z-07 Dach | Code liegt im Zweig (`herkunftFuerNeuesDach`, 2 Stellen) | **wird A-01**, neu geschnitten — alter P1 war unerfüllbar (SPEC) |
| Z-06 / N1 Herkunft und Freigabe | gebaut, Insel- und Servertests grün | fachlich belegt, **keine Prozessautorität** aus der alten Abnahme |
| N2 Kennzeichnung | nicht gebaut | wartet, bis A-01 abgenommen ist (§3: nur ein aktiver Auftrag) |
| N3 Bestätigen/Zurücksetzen | nicht gebaut; Server-Kette am 04.08. ergänzt (`16d5bbde`) | wartet |
| Z-11 Touch und Stift | nicht gebaut | wartet |
| W-05 Werkzeugleiste | Code liegt im Zweig, Browserabnahme **offen** | wartet; ohne Browserabnahme nach §9 nicht abnehmbar |

---

## Grenzen, die unabhängig vom Prozess gelten

- Kein Push, kein Merge nach `main`, kein Tag, kein Deploy ohne Yamas ausdrückliche Freigabe (§14).
- Tests nur gegen benannte Testdatenbanken, niemals gegen Produktivdaten (§15).
- Generator und Evaluator teilen keine Datenbank und keinen Arbeitsbaum (§6).
