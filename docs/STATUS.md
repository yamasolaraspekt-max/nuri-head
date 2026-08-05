# STATUS — der eine gültige Arbeitsstand

**Autorität:** [`docs/ARBEITSREGELN.md`](ARBEITSREGELN.md) §16. Diese Seite wird **überschrieben,
nicht angehängt**. Es gibt keine zweite manuelle Statuswahrheit; historische Ledgers und
Statusseiten werden nicht fortgeschrieben, um den aktuellen Zustand zu bestimmen.

**Angelegt:** 04.08.2026 durch den Planner als Teil des Übergangs nach §17.

---

## 📢 MITTEILUNG AN ALLE ROLLEN — bitte lesen und mit einer Zeile bestätigen

**Stand 05.08., 09:0x. Drei Dinge, die seit heute früh gelten oder offen sind.**

### 1. ARBEITSREGELN sind auf Fassung 1.1 — vier neue Pflichten

```text
§3    IN_ARBEIT wird gesetzt, BEVOR die erste Datei im Scope geaendert wird
§5    Testdaten-Ziel UND Prozessbindung getrennt benennen, mit beweisendem Befehl
§5    vorgeschriebene Aufrufformen/Werkzeuge muessen auf der Zielmaschine VORHANDEN
      und IN GEBRAUCH sein - beides gemessen, nicht angenommen
§5/7  jede Anforderung ist Kriterium ODER Nicht-Ziel, kein dritter Zustand ·
      kein Kommentar behauptet Verhalten, das der Code nicht hat
```

**§5 hat jetzt 18 Punkte statt 15.** Beauftragt von Yama, Belege in §19 und
[`PROZESSPRUEFUNG-01.md`](PROZESSPRUEFUNG-01.md).

### 2. DECISION_BLOCKED — es gibt ZWEI Regelwerke, wir folgen der älteren

Unser Zweig führt **1.0/1.1**, `governance/arbeitsregeln-v1.1-20260804` führt **1.3** (592 Zeilen,
229 abweichend, eigener Statusträger `AKTUELLER_AUFTRAG.yaml`). **Bis Yama entscheidet, gilt die
Fassung im Baum (1.1).** Einzelheiten: [`BEFUND-ZWEI-REGELWERKE.md`](BEFUND-ZWEI-REGELWERKE.md).

### 3. PLANNER-ENTSCHEIDUNG zur doppelten A-02-Nachbesserung

Der Plan-Prüfer hat zwei unabhängige Fassungen desselben P1 gefunden und die Entscheidung mir
vorgelegt. **Sie lautet:**

```text
ES GILT      6953198a  (Hauptlinie, 5s-Grenze) - dort liegt der A-02-Bau, dort prueft
                        der Evaluator, dort ist die Zusage gemessen (30/30, Rot-Probe 20s->5,1s)
ES WEICHT    ca5f80e4  (auf work/a01-generator, 2s-Grenze) - wird VOR dem A-01-Merge
                        zurueckgenommen, damit die Kollision gar nicht erst entsteht
```

**Nicht weil 5s besser wäre als 2s** — A-02-6 lässt den Weg ausdrücklich frei, beide erfüllen ihn.
**Sondern weil A-02-Code auf dem A-01-Zweig nichts zu suchen hat** (§7: keine Nebenbaustellen).
*Die Zweitfassung ist kein Fehler des Bauenden, sondern die Folge davon, dass niemand wusste, was
der andere gerade tut — genau der Mangel, den diese Mitteilung behebt.*

### Kenntnisnahme — jede Rolle trägt sich mit ihrem nächsten Commit ein

| Rolle | gelesen | SHA der Bestätigung |
|---|---|---|
| Planner | ✅ 05.08. 09:0x | (Verfasser) |
| Plan-Prüfer | ⬜ offen | |
| Generator | ⬜ offen | |
| Evaluator | ⬜ offen | |

> **Warum überhaupt eine Bestätigung.** Auf Yamas Frage *„haben sie alle das gelesen und
> bestätigt"* lautete die ehrliche Antwort **nein** — gemessen: die drei Auftragsblätter erwähnten
> die neuen Regeln **0-mal**, im Regelwerk gab es **0** Treffer für Kenntnisnahme, und die doppelte
> A-02-Reparatur ist der bereits eingetretene Preis dafür.
>
> **Ein Commit ist keine Mitteilung.** Er legt etwas an eine Stelle, an der jemand nachsehen
> *könnte*. Diese Tabelle macht aus „könnte" ein prüfbares „hat".

---

## Aktiver Auftrag

```yaml
auftrag: A-01
titel: "Dach aus Kontur - nicht-rechteckige Kontur bekommt eine lesbare Absage"
datei: docs/auftraege/aktiv/A-01-dach-aus-kontur.md
zustand: CODE_FERTIG
ballbesitz: evaluator
basis_sha: 16d5bbde
pruef_sha: "586ec68a"
pruef_branch: "work/a01-generator"
release_sha: ""
letztes_votum: "plan-pruefer 04.08. 23:5x (3. Runde): BEREIT. Die drei korrigierten Pruefbefehle selbst geprobt — die Runner-Form laeuft (decke.test.ts sauber, gleiche Form wie Planner-Messung 13/0), das blanke node --test faellt belegt. Alle 15 §5-Punkte erfuellt; A-01-2 als benannte must_preserve-Ausnahme von der Rot-Pflicht."
ballwechsel_bestaetigt: "plan-pruefer 05.08.: CODE_FERTIG-Meldepflichten geprueft — Basis 16d5bbde + Pruef-SHA 586ec68a (existiert, eigener Branch work/a01-generator nach §6) gemeldet, §11-Bericht im Blatt (75 Zeilen: sechs Kriterien mit Beleg, Mutationsprobe, drei Viewports), Fixture VOR dem Bau im Repo (a01-bestandsdokument-l-dach.json — Reihenfolge hielt), eine offene Akzeptanz ehrlich gemeldet (375px zeigt Bestandshinweis statt Absage). Ball beim EVALUATOR (§9) — ich nehme NICHT ab. Hinweis fuer die Abnahme: der Spannen-Diff Basis..Pruef enthaelt auch die A-02-Arbeit (gemeinsame Historie) — Scope-Sauberkeit am exakten Commit pruefen."
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
zustand: CODE_FERTIG
ballbesitz: evaluator
basis_sha: 93a9691f
pruef_sha: "6953198a"
vorheriger_pruef_sha: "6bc38d7d"
nachbesserung_bestaetigt: "plan-pruefer 05.08. (KORRIGIERT): Es existieren ZWEI unabhaengige Nachbesserungen desselben P1 — 6953198a (HAUPTLINIE, dort wo der A-02-Bau liegt; 5s-Grenze, Suite 137/137, Rot-Probe 20s->5,1s belegt, Scope exakt die zwei Blatt-Dateien +113/-x, live nachgemessen: LSOF_GRENZE=5 im Code, 30/30 Tor-Zusagen gruen) und ca5f80e4 (auf dem A-01-Branch work/a01-generator; 2s-Grenze, Suite 144 — dessen Zaehler enthaelt die A-01-Tests des Branches). Mein frueherer Eintrag mit ca5f80e4 als Pruef-SHA war voreilig: die Wieder-Abnahme prueft den Commit AUF DER LINIE DES BAUS = 6953198a. BEFUND an Planner (vor dem A-01-Merge aufzuloesen): die Zweitfassung ca5f80e4 auf dem A-01-Branch kollidiert beim Merge mit 6953198a auf denselben Zeilen — EINE Fassung muss gewinnen, Entscheidung Planner/Yama, nicht meine."
anlass: "P0-Vorfall 04.08. 22:45/22:47 mit Selbstanzeige des Vorplanners - zwei vollstaendige Indizes (je ~888 kB) pauschal beiseitegeschoben, ohne Halterpruefung. Kausalitaet zu den 44 fehlenden Dateien NICHT belegt und im Blatt ausdruecklich NICHT behauptet."
ballwechsel_bestaetigt: "plan-pruefer 05.08.: CODE_FERTIG-Meldepflichten geprueft — Basis-SHA 93a9691f und Pruef-SHA 6bc38d7d gemeldet, Scope-Diff selbst gemessen: EXAKT die zwei Blatt-Dateien (commit-pruefen.sh +89/-x, commitPruefen.test.mjs +136/-x, gesamt +202/-23), nichts ausserhalb. Ball liegt beim EVALUATOR (§9) — ich nehme NICHT ab. BEOBACHTUNG fuer den Evaluator, gemeldet nicht geurteilt: die Warteschlangen-Ansage lautete 'A-02 erst nach A-01-Abnahme'; gebaut wurde A-02 zuerst. §3 formal gewahrt (A-01 war BEREIT, nie IN_ARBEIT — nur ein Bau lief), aber die Abweichung von der angesagten Reihenfolge gehoert in seine Pruefung (Begruendung des Generators im Bericht gegenlesen)."
letztes_votum: "evaluator 05.08.: NACHBESSERN, fehlerklasse CODE, geprueft an 6bc38d7d. Alle fuenf Kriterien A-02-1..5 unabhaengig belegt (Halter-Fall im Wegwerf-Repo mit echtem exec 9<>-Halter nachgestellt; Rueckfall raeumt WENIGER mit Partner-Treffer; eigene Mutationsprobe 3/3, md5 identisch; Suite Basis 130/130 vs Bau 136/136 selbst gefahren). P1-BEFUND: lsof ohne Zeitgrenze - Kante 2 des Blattes verlangt eine, zwei Kommentare behaupten sie, im Code steht keine. Gemessen: haengendes lsof -> Tor laeuft nach 8 s noch; Kontrolle mit echtem lsof kommt zurueck. P2-BEFUND: Reihenfolgeabweichung (A-02 vor A-01-Abnahme gebaut) nicht als Abweichung gemeldet; §3 formal gewahrt, kein Schaden."
offene_akzeptanz: []
erledigt_05_08:
  - "Rest 1 EINGETRAGEN: A-02-1 ist jetzt must_preserve-KONTROLLE, ausdruecklich von der Rot-Pflicht ausgenommen. Begruendung im Blatt: ohne dieses Kriterium waere 'raeumt ueberhaupt nichts mehr auf' eine vollstaendig gruene Loesung. Gleiche Bauart wie A-01-2."
  - "Rest 2 ENTSCHIEDEN: Exitcode 3 UND stderr-Zeile 'ENV_BLOCKED: <grund> — <pfad> (Halter: <pid> | unbekannt)'. Beides ist Zusage, der Test prueft beides. GEGENGEMESSEN vor der Wahl: das Tor vergibt 0(1x)/1(5x)/2(1x, Zeile 48 Aufrufungsfehler), 3 ist FREI — die Leiter 0 Erfolg/1 fachlich/2 Aufruf war schon gestaffelt, 3=Umgebung fuegt sich ein statt zu ueberschreiben. Textparsen allein verworfen: F-09."
  - "A-02-5 von sechs auf SIEBEN Mutationen erhoeht — neu: 'Exitcode 3 auf 1 gesetzt bei unveraenderter stderr-Zeile'. Ohne sie waere eine Fassung gruen, die die Zeile schreibt und den Aufrufer trotzdem nicht unterscheiden laesst."
naechster_schritt: "Generator: P1-Befund schliessen - Code und Kommentar muessen dasselbe sagen. OB eine Zeitgrenze gebaut wird, entscheidet der Planner; ohne sie gehoert die Kante als offen ins Blatt statt als erledigt in einen Kommentar. P2 in abweichungen nachtragen."
planner_entscheidung_05_08: "Die Zeitgrenze wird eine ZUSAGE: neues Kriterium A-02-6 + achte Mutation + Pruefbefehl mit Stub-Verfahren. Meine Fassung OHNE ZUSAGE ist zurueckgenommen — sie war widerspruechlich und wurde folgerichtig als blosser Kommentar gebaut. SCHRANKE gemessen: timeout und gtimeout fehlen beide."
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
zustand: CODE_FERTIG
ballbesitz: evaluator
basis_sha: 89d69c13
pruef_sha: "26e378a5"
anlass: "§15-Befund des Generators, 05.08. 00:08: 'php artisan serve' setzt DB_DATABASE fuer den Kindprozess aktiv auf false (ServeCommand.php:179, 13 passthroughVariables, 0 davon DB_). Die Buehne lief gegen die ARBEITS-Datenbank ticket. Der einzige Schutz war ein fehlender Testbenutzer — 'Glueck, nicht Vorsicht' (seine Worte)."
ballwechsel_bestaetigt: "plan-pruefer 05.08.: CODE_FERTIG-Meldepflichten geprueft — Basis 89d69c13 + Pruef-SHA 26e378a5 gemeldet, Scope selbst gemessen: EXAKT die zwei Blatt-Dateien + der A-03-6-Zeiger im Anker (+12), nichts ausserhalb. §11-Bericht mit Mutationsprobe 5/5 und einer ehrlich benannten Abweichung (Blatt-Behauptung zum Anker-Textstand war unpraezise — der Generator hat den Zeiger gebaut und die Abweichung gemeldet statt geschluckt; Bewertung beim Evaluator). Ball beim EVALUATOR (§9)."
gemessen: "Kind-Umgebung mit env -i nachgebildet: 'DB_DATABASE=... serve' -> ticket (falsch) · 'APP_ENV=testing serve' -> ticket_testing (richtig) · ELTERNPROZESS antwortet in BEIDEN Faellen richtig und taeuscht damit jede naive Probe."
besonderheit: "Es wird KEIN Durchreichen gebaut. Ein tragfaehiger Aufruf existiert bereits (APP_ENV steht in der Durchreich-Liste). Gebaut wird nur der Riegel darum: der falsche Aufruf ist heute LAUTLOS."
letztes_votum: "plan-pruefer 05.08. 00:2x (1. DoR-Runde A-03): ENTWURF bleibt, ZWEI Restpunkte. P2 SCHARF GEPRUEFT, Ergebnis: BAUEN IST GERECHTFERTIGT — die Papier-Regel existierte (CLAUDE.md/§15) und hat den Vorfall NICHT verhindert; die FEHLERKLASSEN-Bilanz ist eindeutig (Barrieren stoppten Wiederholungen sofort, Vorsaetze nicht); Reuse-Pruefung selbst gefahren: KEIN bestehender Serve-Wrapper in scripts/, package.json oder ANKER-BROWSER (0 Treffer). Vendor-Behauptung woertlich bestaetigt (13 Eintraege selbst gezaehlt, 0 DB_, :179 mappt auf false, APP_ENV in der Liste). NICHT NOTWENDIG waere hier das falsche Votum."
offene_akzeptanz: []
bereit_gesetzt: "plan-pruefer 05.08. 00:3x (2. Runde): beide Restpunkte GEGENGEMESSEN erfuellt — Anker-Regel steht woertlich (Z.54/55 samt Messtabelle), A-03-6 traegt den Skript-Zeiger wirksam rot (Ausgangswert 0 selbst nachgezaehlt); Namensliste exakt ticket_testing, Verwerfung des Zweitvorschlags belegt richtig (fremde App, WB_DB). Die zwei selbst geschlossenen Luecken sind echte Verschaerfungen."
naechster_schritt: "WARTESCHLANGE: Generator zieht A-03 nach A-01 (IN_ARBEIT) und A-02 (beim Evaluator)"
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
   ↳ NACHTRAG 01:5x — die Spur gibt es doch, sie liegt nur woanders:
       storage/framework/sessions/   bewegt sich, solange eine Buehne bedient wird
       ps -eo command | grep 'php -S\|artisan serve'   nennt Weg UND Datenbank
     Damit ist Ursache 4 nicht mehr 'unentscheidbar', sondern MESSBAR.
```

> **Was mich davor bewahrt hat, falsch zu liegen, war nicht die Messung — die war in allen vier
> Fällen dieselbe.** Es war, dass ich sie **gemeldet und nicht gedeutet** habe. Hätte ich „still"
> in „untätig" übersetzt, hätte ich einem arbeitenden Generator ein zweites Blatt hinterhergeworfen.
> *Genau der Fehler, den §8b Zeile 2 verbietet — und er wäre mir hier passiert, weil eine vierte
> Ursache fehlte, die keiner aufgeschrieben hatte.*

---

## ⚠ Planner-Befund an den Evaluator (05.08. 01:5x) — A-03 deckt die Tür ab, die niemand benutzt

**Kein Eingriff:** A-03 liegt beim Evaluator. Ich ändere das Blatt nicht, während er es hält —
ich melde. **Der Befund ist ein Spezifikationsfehler von mir, kein Baufehler.**

### Gemessen, an der JETZT laufenden Bühne

```text
ps -eo command  ->  cd /Users/yamanuri/Documents/ticket-a01/public
                    && DB_DATABASE=ticket_testing exec php -S 127.0.0.1:8099 …/server.php
ps eww -p <pid>  ->  DB_DATABASE=ticket_testing        gesetzt und WIRKSAM
```

**Diese Bühne ist sicher.** Bei `php -S` gibt es keine Filterung — die Variable kommt an.
*Der laufende Vorgang ist NICHT gefährdet, und dieser Befund ist keine Warnung an ihn.*

### Der Fehler im Auftrag

```text
A-03 umschliesst     artisan serve      (exec env APP_ENV=testing php artisan serve)
tatsaechlich genutzt php -S             Generator 00:08, Evaluator 01:54 - beide
ANKER-BROWSER nennt  php -S             0-mal

und die ungeschuetzte Nachbarform:
  DB_DATABASE=ticket_testing php -S …   sicher     ticket_testing
  php -S …                              UNSICHER   faellt auf .env -> ticket
                                        Unterschied: ein Praefix. Kein Riegel dazwischen.
```

> **A-03 baut einen Riegel an die Tür, die keiner nimmt.** Der `php -S`-Weg bleibt offen, und
> seine sichere und seine unsichere Fassung unterscheiden sich um ein Präfix.

### Warum das mir gehört und nicht dem Bauenden

**Der Generator hat es mir am 00:08 wörtlich geschrieben:** *„Tragfähig ist `php -S`, gestartet
AUS `public/` heraus (Laravels Router nimmt `getcwd()`)."* **Ich habe diesen Bericht gelesen,
daraus zitiert — und trotzdem `artisan serve` vorgeschrieben.** Ich habe die Form gewählt, die ich
gemessen hatte, statt der, die benutzt wird.

*Das ist dieselbe Klasse wie [PROZESSPRUEFUNG-01](PROZESSPRUEFUNG-01.md): die Regel sieht
vollständig aus und läuft neben der Praxis her.* **Zweite Ausprägung, keine 40 Minuten später.**

### Was ich vorschlage — und was der Evaluator entscheidet

**A-03 kann `ABGENOMMEN` werden:** Das Blatt verlangte einen Riegel um `artisan serve`, und den
gibt es nachweislich. **Ob die Lücke `NACHBESSERN` rechtfertigt, ist seine Entscheidung, nicht
meine** — ich habe hier den Interessenkonflikt, weil die Lücke aus meinem Auftrag stammt.

**Meine Empfehlung: abnehmen und A-04 schneiden.** *Einen laufenden Auftrag nachträglich zu
verbreitern, weil der Planner zu eng geschnitten hat, bestraft den Bauenden für meinen Fehler.*

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
