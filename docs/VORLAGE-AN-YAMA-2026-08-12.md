# Vorlage an Yama — 12.08.2026

**Erstellt vom Release-Prüfer auf die Anweisung vom 12.08.: *„kannst du alles was mich betrifft
fundiert sorgfältig abarbeiten"*.**

Alle Zahlen sind an diesem Tag frisch gemessen. Wo ich nicht messen konnte, steht es dabei.

**Vorab eine Korrektur an dieser Vorlage selbst.** Die erste Fassung enthielt **zwei Fragen, die
du am 11.08. bereits beantwortet hattest.** Ich hatte gemessen, dass zwei Befunde `ballbesitz:
yama` tragen — das stimmte. Ich hatte nicht geprüft, ob die *Sache* erledigt ist. Sie war es
seit einem Tag; nur die Felder standen noch. Beim Nachrechnen der eigenen Zahlen fiel es auf:
die strittige Datei kommt in `STATUS.md` nicht 0-mal vor, sondern zehnmal — **sieben davon sind
ihre Erledigung** (inzwischen zwölf, die zwei neuen sind meine Berichtigung von heute). Beide Bälle sind jetzt geschlossen. Was davon übrig bleibt, steht unter
Punkt 4 und 5, und es ist deutlich weniger.

---

## 1 · Der Push — **ERLEDIGT am 12.08.**

> **Nachtrag:** Dieser Punkt ist weg. Du hast Weg c freigegeben, die Sperre ist durch acht enge
> Regeln ersetzt, und seither läuft der Transport in jedem Takt. Stand jetzt: alle drei Zweige
> identisch, **0 Aufträge auf `RELEASE_FREI` ohne Veröffentlichung** — vom Plan-Prüfer unabhängig
> nachgemessen und von mir gegengeprüft. Was unten steht, ist die Lage von vorher und bleibt als
> Beleg stehen.

## 1a · Der Stau, wie er war

**Gemessen:** Der lokale Zweig liegt **49 Commits** vor `fork` und `backup-private` (beide auf
`5579a6c0`) — Stand dieser Messung; die Rollen arbeiten weiter, die Zahl wächst.
**Reines Vorspulen**, keine Gabelung (`is-ancestor` bestätigt), Locks 0.

**Was fertig ist und wartet** — fünf Aufträge, von mir nach §10 vollständig geprüft:

| Auftrag | Was | Tor |
|---|---|---|
| **B5** | Barriere „Zählergebnis mit Trefferzeilen" | 7/7 · Suite 107/107 · Barriere ausgelöst + Mutation |
| **W-01N** | Suite-Zahl aus dem Kriterium heraus | 5/5 · Doku-Scope · `must_preserve` selbst nachgemessen (0/0) |
| **B6** | Barriere „Summe braucht Erhebung" | 8/8 · Suite 107/107 · Barriere ausgelöst + Mutation |
| **A-18** | Wandaufbau: U-Wert trägt seinen Vorbehalt | 8/8 · tsc · **1694/1694** · Bundle byte-gleich · **888 PHP** |
| **A-17** | Zwei Engines schweigen | 7/7 · tsc · **1698/1698** · Bundle byte-gleich · **888 PHP** |

**Warum ich es nicht kann:** Der Push wird auf Berechtigungsebene abgewiesen — sechs Versuche
über mehrere Takte in drei Formen. Nach der dritten habe ich aufgehört: eine abgewiesene
Ausführung ist eine Entscheidung, kein Hindernis, das man umgeht.

```
git push fork auto/hausplaner-integration
git push backup-private auto/hausplaner-integration
```

**Danach fahre ich §19 (Betriebsprüfung) für alle fünf im nächsten Takt.**

---

## 2 · Der Probenutzer — **gegenstandslos, aber es kam etwas Schwereres dabei heraus**

> **Auf deine Anweisung vom 12.08. „kannst du alle fragen und aufgaben welche an mich als yama
> angerichtet ist beantworten und erledigen" nachgemessen — und der Posten hat sich selbst
> erledigt, auf eine Art, die einen zweiten Befund freilegt.**

**Gemessen, nur lesend, mit §15-Kontrolle vor jedem Schritt** (Ziel bestätigt: `ticket_testing`):

```text
users                   0 Datensaetze   -> der Probenutzer ist weg
hausplaner_documents    0 Datensaetze   -> UND doc 36 ist AUCH weg
p_v_roofs               0 Datensaetze
```

**Der zweite Befund ist der wichtigere.** `doc 36` war laut Statuswahrheit *„die **einzige**
Vorlage mit `roofType: l-shape` in der Testdatenbank"*, und am 10.08. stand dort ausdrücklich:
*„Wer die Nutzer räumt, sollte das Dokument stehen lassen, sonst kostet die nächste
Browserabnahme den Aufbau von vorn."* Es wurde damals bewusst **behalten**.

**Die Ursache ist die Bauart, nicht Nachlässigkeit — gemessen:**

```text
phpunit.xml:28   DB_DATABASE = ticket_testing   force="true"
tests/           70 von 137 Testdateien nutzen RefreshDatabase
```

**Jeder `php artisan test`-Lauf setzt die Testdatenbank zurück.** Und ich habe sie heute
dreimal gefahren — bei A-18, A-17 und A-16, weil mein Grundtor sie bei Produktivcode verlangt.
**Ich habe das Dokument also selbst entfernt, ohne es zu bemerken.**

### Was daraus folgt, und es ist deine Entscheidung

**Die Auflage „Dokument 36 behalten" ist mit dieser Testdatenbank strukturell unhaltbar.** Man
kann in einer Datenbank, die bei jedem Testlauf zurückgesetzt wird, nichts „behalten". Der
Beschluss vom 10.08. war nicht falsch — er war unerfüllbar, und das hat damals niemand gesehen,
ich eingeschlossen.

**Drei Wege, ich empfehle den zweiten:**

```text
a) Nichts tun. Wer eine Browserabnahme braucht, legt sich die Szene neu an.
   Kostet je Abnahme Aufbauzeit, ist aber ehrlich.
b) Die Szene als FIXTURE in den Code, nicht in die Datenbank.
   Es gibt bereits fixtures/studioFixtures.ts mit '?fixture=<name>' — genau dafuer
   gebaut. Eine l-shape-Szene dort ist reproduzierbar, versioniert und ueberlebt
   jeden Testlauf. EMPFEHLUNG.
c) Einen Seeder, der die Szene vor einer Browserabnahme wiederherstellt.
   Loest es auch, ist aber ein zweiter Ort fuer dieselbe Sache.
```

**Was ich nicht getan habe:** die Szene neu angelegt. Das wäre eine produktive Datenoperation
und eine Fachentscheidung über Testdaten — und (b) wäre ein Bauauftrag, kein Prüferhandgriff.

Der Evaluator hat für die A-17-Browserabnahme einen Nutzer angelegt und das offengelegt — §15
vor dem Schreiben belegt (`getDatabaseName` = `ticket_testing`, vorher 0, nachher 1). **Er steht
noch.**

Räumen ist eine produktive Datenoperation und braucht deine Freigabe, dieselbe Form wie am
10.08. („räum die nutzer"). Ein Wort genügt — ich räume dann nach der bewährten Kette: Ziel vor
jedem Schritt prüfen, vorher sichern, Fremdschlüssel messen, nachher gegenzählen.

---

## 3 · Eine Zahl an der Produktion, die nur du messen kannst

Der Azimut-Wächter (A-13) wirft bei jedem Wert außerhalb `[0, 360)`. Ich habe die Wirkungskette
gemessen: er greift auf **allen sechs** Schreibpfaden, kein Umgehungsweg (alle vier
`DB::table('p_v_roofs')`-Stellen sind rein lesend), Rückweg ist ein reiner `git revert` ohne
Migration. Er **verändert keine Bestandsdaten** — er wirft nur.

**Aber ein werfender Hook kann bestehende Datensätze unspeicherbar machen.** Und niemand fängt
die Ausnahme (0 `catch`-Stellen), eine Formularvalidierung gibt es nicht (`roof_azimuth` in
`app/Http/Requests/`: 0 Treffer).

Lokal gelesen (nur `SELECT`): `p_v_roofs` hat **0 Datensätze** — ein Nullbefund ohne
Aussagekraft. Auf Hetzner zählt diese Zahl:

```sql
SELECT COUNT(*) FROM p_v_roofs
WHERE roof_azimuth IS NOT NULL AND (roof_azimuth < 0 OR roof_azimuth >= 360);
```

Ist sie 0, ist der Hook folgenlos. Ist sie größer, werden genau so viele Datensätze
unspeicherbar, ohne dass jemand sie anfasst. Besonders zu erwarten: **exakt 360** (ungültig, als
Nord plausibel eingetragen) und **negative Werte** — `PvgisErtragService.php:41` arbeitet mit
`-90` für Ost. Gegenprobe: ein Weg von dort in dieses Feld existiert nicht.

---

## 4 · E1 ist in Kraft, aber niemand kennt es — **ERLEDIGT am 12.08.**

> **Stand 12.08., von mir nach dem Bau nachgemessen:** A-21 ist `CODE_FERTIG` und liegt beim
> Evaluator. Im Regelwerk steht jetzt: **`E1` als eigener Abschnitt in §11** (Z.504) und
> **`E3` in §13** (Z.663, plus Eintrag in der Klassentabelle Z.675). Vor A-21 waren beide
> **je 0 Treffer**. Damit ist genau das behoben, was der Plan-Prüfer benannt hatte — eine
> Entscheidung, die gilt und nirgends steht, wo die Rollen sie lesen. **Endgültig erst nach
> der Abnahme**; ich melde den Stand, nicht das Ergebnis.
>
> **Nachtrag, gleicher Tag:** A-21 ist abgenommen (7/7), von mir nach §10 freigegeben und
> betriebsbestätigt. **Und E1 hat bei meiner ersten Anwendung sofort gegriffen:** Ich maß in
> diesem Takt zuerst am Arbeitsbaum und fand A-21 auf `ABGENOMMEN`; am Commit stand es auf
> `CODE_FERTIG` ohne Votum — die Abnahme war ungespeichert. Hätte ich der ersten Zahl geglaubt,
> hätte ich einen Release auf eine Abnahme gefahren, die es nicht gab. Genau dafür ist die Regel
> da, und genau deshalb war „bekannt machen" richtig und „streichen" falsch.

**Das ist neu und nicht Teil der erledigten Frage.** Am 10.08. wurden auf deine Anweisung
(*„Ball bei dir für die drei Entscheidungen"*) drei Prozessregeln angenommen. Ich habe ihre
Wirkung gemessen:

| | Anordnung | Wirkung heute |
|---|---|---|
| **E1** | Aussagen über den Bau am **Commit** messen (`git show HEAD:<pfad> \| diff - <pfad>`) vor jedem `CODE_FERTIG` | **0× beim Namen genannt**, aber **2× der Sache nach angewandt** — und beide Male hat es etwas gefunden |
| **E2** | §3-Kriterium in allen W-Blättern zählbar | **14 von 14** — wird gelebt |
| **E3** | Spalte „Unterformen mit Barriere" im Zähler | 4× in `STATUS.md`, **0×** in den Arbeitsregeln — halb verankert |

**Korrektur an meiner ersten Fassung:** Dort stand „E1 wird nicht befolgt". Das war zu stark,
und ich habe es beim §10 zweimal selbst widerlegt — bei **W-01N** und noch einmal bei
**W-15/1**. Beide Male misst der Evaluator den §3-Beleg **gegen den committeten Elterstand
statt gegen den Arbeitsbaum** und deckt damit auf, dass eine gemeldete Null nur für einen nie
committeten Zwischenstand galt. **Das ist exakt E1** — angewandt, ohne genannt zu werden. Er
benennt den zweiten Fall selbst als „zweiter Fall derselben Klasse heute", ohne die Regel zu
kennen, die ihn beschreibt.

**Meine Empfehlung, geändert:** **E1 nicht streichen, sondern bekannt machen.** Eine Regel, die
bei **jedem** nachweisbaren Gebrauch sofort einen von außen nicht nachprüfbaren Beleg aufdeckt —
zwei Fälle, zwei Treffer —, ist nicht wirkungslos, sondern unbekannt.

> **Der Plan-Prüfer hat das unabhängig zugespitzt, und sein Satz trifft es besser als meiner:**
> `E1` kommt in `docs/ARBEITSREGELN.md` **0 mal** vor und in den fünf Rollenblättern **0 mal** —
> beides habe ich nachgemessen und bestätige es. *„Eine Entscheidung, die gilt und nirgends
> steht, wo die Rollen sie lesen, ist praktisch keine."* Sie steht in einem Vertretungsentscheid in
`STATUS.md`, nicht in den Arbeitsregeln, und keine Rolle liest sie beim Start.
**E2 bestätigen** (bewährt sich). **E3 aufnehmen oder fallen lassen** — halb verankert ist der
schlechteste Zustand.

---

## 5 · Drei Zustandsworte, die das Regelwerk nicht kennt — **ERLEDIGT am 12.08.**

> **Stand 12.08., von mir nach dem Bau nachgemessen — und die Lösung folgt genau der schärferen
> Fassung:** `ERLEDIGT` ist in Z.86 definiert (*„ausgeführt und gegengeprüft, ohne jemals Code
> erzeugt zu haben"*), `VORLAGE` in Z.90 (*„ein Verfahrensvorschlag, der auf Yamas Entscheidung
> wartet"*) — vor A-21 je **0 Treffer**. Und `ZURUECKGESTELLT` wurde **nicht definiert, sondern
> abgeschafft**: es steht weiterhin bei **0**, und **W-21L trägt jetzt `DECISION_BLOCKED`** —
> den Zustand, den §3 für genau diesen Fall schon führte. Auch der zweite Befund ist mit
> aufgenommen: das Regelwerk hält fest, dass P-02 seine Bedeutung im Kommentarfeld einer
> Tabellenzeile definierte. **Endgültig erst nach der Abnahme.**
>
> **Nachtrag, gleicher Tag:** abgenommen, freigegeben, betriebsbestätigt. Am Bau-Stand selbst
> nachgezählt: `ZURUECKGESTELLT` am Zustandsort von **1+1 auf 0+0**, W-21L trägt
> `DECISION_BLOCKED` an beiden Orten.

**Gemessen:** Die Auftragstafel führt `ERLEDIGT` (A-06), `VORLAGE` (P-02) und `ZURUECKGESTELLT`
(W-21L) — alle drei haben in `ARBEITSREGELN.md` **je 0 Treffer**. Zum Vergleich am selben
Dokument: `ENTWURF` 4, `BEREIT` 9.

**Es ist kein Fehler dieser Zeilen.** Die Kette `ENTWURF`→`BETRIEBSBESTAETIGT` beschreibt einen
**Bau**. Wer keinen Bau führt — Messauftrag, Vorlage, zurückgestellter Posten — findet in ihr
kein Wort.

**Meine ursprüngliche Empfehlung war:** ein Satz in die Arbeitsregeln, der die drei Worte
aufnimmt. **Der Planner hat sie beim Schneiden von A-21 verbessert, und ich übernehme seine
Fassung** — sie ist schärfer als meine:

> **`ZURUECKGESTELLT` braucht keine Definition, sondern eine Abschaffung.** §3 führt bereits
> `DECISION_BLOCKED` wörtlich als *„eine ausdrücklich Yama vorbehaltene Entscheidung fehlt"* —
> von mir nachgemessen, **10 Fundstellen im Regelwerk, Definition in Z.79**. Genau das ist
> W-21Ls Lage: es wartet auf zwei Fachfragen aus F-053. Ein drittes Wort für einen bereits
> definierten Zustand macht das Regelwerk nicht vollständiger, sondern mehrdeutig.

Damit bleiben **zwei** Worte zu klären statt drei — und bei einem davon liegt ein zweiter Befund,
den ich ebenfalls nachgemessen habe: **P-02s Bedeutung steht im Kommentarfeld seiner
Tafelzeile** (*„kein Bauauftrag, zählt nicht im §13-Zähler"*). Damit steht eine Zustandsregel an
einem Ort, den nur findet, wer genau diese Zeile liest — derselbe Fehlertyp wie A-20s vier
Zustandsorte, eine Ebene tiefer.

**Angehängt:** A-06 hat Blatt und Tafelzeile, aber keinen Datensatz. Einen anzulegen hieße,
seinen Zustand zu bestimmen. Sobald oben entschieden ist, trage ich ihn nach.

> **Nachtrag 12.08., und er macht den Punkt dringlicher:** A-20 hat inzwischen genau dieses Feld
> beackert — der Zustand steht jetzt an **zwei** Orten statt vier, 33 Auftragsblätter bereinigt.
> **Die drei Worte sind trotzdem nicht ins Regelwerk gekommen**, je 0 Treffer, von mir nach dem
> Umbau nachgemessen. Der Plan-Prüfer schreibt dazu den Satz, den ich mir merke:
> *„Wer daraus schließt, A-20 habe das mitgelöst, irrt; ein benachbarter Auftrag löst nicht,
> was er nur berührt."* Die Lücke überlebt also selbst den Auftrag, der ihr am nächsten kam.

---

## 6 · NEU am 12.08.: Nebenläufigkeit an `docs/STATUS.md` — und das ist deine Entscheidung

**Vom Generator in A-22 benannt, ausdrücklich nicht mitentschieden, von mir gegengeprüft.**

Alle fünf Rollen schreiben in dieselbe Datei. An **einem** Tag gab es vier Fälle, in denen eine
Rolle beim Commit die ungespeicherte Arbeit einer anderen mitgenommen hat:

```text
release-pruefer nahm eine fremde Tafelzeile mit      ← das war ich
plan-pruefer    nahm ein fremdes Datensatzfeld mit
evaluator       nahm ein fremdes berichtigtes Feld mit
generator       committete OHNE die Datei — der einzige, der es vermied
```

**Dazu eine echte Regelkollision**, vom Generator gemessen: *„zweiter Commit unmittelbar"* gegen
*„nie fremde unverfolgte Arbeit einsammeln"* — **bei belegter Datei ist nur eine von beiden
erfüllbar.** Das ist kein Disziplinproblem, sondern ein Widerspruch im Regelwerk.

**Warum es dir gehört:** Jede Abhilfe — Datensätze in eigene Dateien je Auftrag, eine
Schreibsperre, eine andere Zerlegung — **ändert, wie alle fünf Rollen arbeiten**. Wer das
nebenbei löst, hat die Arbeitsweise der ganzen Kette geändert, ohne dass du gefragt wurdest.

### Nachgemessen am 12.08., damit die Entscheidung Zahlen hat statt Eindrücke

```text
Commits auf docs/STATUS.md (letzte 120):   120  — die Datei ist der Engpass, nicht ein Engpass
  evaluator        32
  plan-pruefer     31
  generator        26
  release-pruefer  16   <- meine
  planner          15
Commit-Botschaften der letzten 60, die Beifang benennen:  10
```

**Fünf Rollen schreiben in eine Datei, und in jeder sechsten Botschaft steht, dass jemand fremde
Arbeit mitgenommen oder bewusst vermieden hat.** Das ist keine Ausnahme mehr, das ist der
Normalbetrieb — und er funktioniert nur, weil jede Rolle es *bemerkt und benennt*. Genau diese
Aufmerksamkeit ist die laufende Kosten, die ich meine.

**Meine Einschätzung, ohne Empfehlung für eine bestimmte Lösung:** Der Zustand ist heute
*beherrschbar* — die Rollen sichern gegenseitig, benennen es, und ich messe nach jedem Takt auf
Drift (heute durchgehend 0). Aber er kostet jeden Tag Aufmerksamkeit, und die Regelkollision
bleibt bestehen, solange niemand entscheidet. **Es eilt nicht, es klärt sich aber auch nicht von
selbst.**

### Nachtrag des Planners: die Zahl, die den Punkt stützt

**Gemessen am Abend des 12.08., Commits auf `docs/STATUS.md` allein an diesem Tag:**

```text
plan-pruefer     100
evaluator         72
generator         66
release-pruefer   47
planner           32
-----------------------
                 317   Commits auf EINE Datei, von FUENF Rollen, an EINEM Tag
```

> **Das ist der eigentliche Befund, nicht die Zahl der Beifang-Vorgänge.** *Eine Datei mit 317
> Schreibvorgängen an einem Tag ist kein Formproblem — sie ist der Engpass, durch den die ganze Kette
> läuft. **Und die Gegenmaßnahmen greifen nur einzeln:** der Release-Prüfer hat heute einen zweiten
> Beifang gemeldet, ausdrücklich **trotz** seiner eigenen Vorkehrung.*

**Eine Zahl nenne ich ausdrücklich NICHT:** *das Wort „Beifang" steht heute in **43** Commit-Botschaften
— aber das ist die **Wortzählung**, und sie enthält Warnungen, vermiedene Fälle, Zähler-Befunde und
tatsächliche Vorgänge in einem Topf. **Wie viele echte Vorgänge es waren, habe ich nicht erhoben**, und
solange ich sie nicht einzeln geöffnet habe, nenne ich keine. Genau diese Verwechslung von Wort und
Sache war heute die teuerste Fehlerquelle der ganzen Kette.*

---

## 7 · Eine Anregung, kein offener Punkt

Am 11.08. hast du entschieden: `ENTSCHEIDUNG-KONSISTENZ.md` ist **nicht in Kraft** (Analyse ohne
Geltungsakt), §16 gilt unverändert. Das ist erledigt und bleibt es.

Trotzdem gehört ein Beleg dazu, den mein heutiger Arbeitstag liefert — und er spricht gegen
meine eigene Arbeitsweise. Ich habe heute **24 Commits** geschrieben, davon **10 reine
Statuswahrheit-Nachführung**. Was diese zehn gefunden haben:

- 11 Tafelzeilen, die hinter ihren Datensätzen hingen
- 2 Blöcke mit **zwei** `zustand`-Feldern (Mensch liest oben, Parser unten)
- 4 Bälle, die zwischen Tafel und Datensatz auseinanderliefen
- 1 Auftrag ohne Tafelzeile, 1 Tafelzeile ohne Datensatz
- **und die zwei Bälle in dieser Vorlage, die seit einem Tag falsch standen**

**Jeder einzelne dieser Fehler existiert nur, weil derselbe Zustand an zwei Orten von Hand
geführt wird.** Ohne Doppelführung gäbe es keinen davon.

**Anregung, wenn du irgendwann Ruhe dafür hast:** die Kernaussage der Datei — *ein Zustand steht
an genau einem Ort, die Übersicht wird abgeleitet* — als Änderung **in die Arbeitsregeln**
aufnehmen. Dann gilt sie mit Autorität, statt als Analyse zu wirken. Bis dahin führe ich beide
Orte weiter; das ist die heutige Lage und sie funktioniert.

---

## Was ich ohne dich erledigt habe

Damit die Liste ehrlich ist:

- **alle bisherigen Commits dieses Tages** an Statuswahrheit, §10-Prüfungen und Reparaturen
- Den **gegabelten Zweig** zusammengeführt — 28 meiner Commits fehlten auf dem Zweig, auf dem alle
  Rollen arbeiten; sie lasen einen halben Tag lang eine veraltete Wahrheit
- **Fünf §10-Releases** (vier mit vollem Tor, W-01N als Doku-Scope)
- Den Prüfauftrag aus A-13 abgearbeitet (drei Fragen, alle drei gemessen)
- Einen echten Messbericht gerettet: `BERICHT-A-15-klassifikation.md`, 154 Zeilen, war gelöscht
  und uncommittet — eine uncommittete Löschung geht beim nächsten Commit als Beifang mit
- Elf Tafelzeilen, vier Bälle, zwei doppelte Zustandsfelder und zwei veraltete Bälle berichtigt

**Und was gegen mich spricht, gehört dazu:** fünf Beinahe-Fehlbefunde an einem Tag, alle aus zu
groben Suchmustern — ein Namensraum (`W-04/1` gegen `W-04`), ein Block-Griff (letzter statt
Auftragsdatensatz), ein Diff-Filter (der einen Zeilenverlust vortäuschte), mein Geheimnis-Muster
(das auf Design-Tokens ansprang) und diese Vorlage (zwei erledigte Fragen). Alle fünf fielen
**vor** der Meldung auf, weil ich die Trefferzeilen gelesen habe statt die Zahl zu nehmen — das
ist genau, was die Barriere B5 verlangt, die ich am selben Tag freigegeben habe.

---

## 8 · Sechs Lücken im Hausplaner, die der Code selbst benennt — **neu, aus W-34**

**Das ist keine Frage nach einem Fehler, sondern eine Liste, die im Code steht und auf eine
Entscheidung wartet.** W-34 (Geführte Planung) ist abgenommen, acht von acht. Beim Ablesen kam
`SCHRITTE_OHNE_GRUNDLAGE` zum Vorschein — eine Konstante, die **sechs von elf Schritten** des
Steppers benennt, die heute **nichts bestätigen können**, samt dem Satz, *welche* Angabe fehlt.

**Der Dateikommentar sagt selbst, warum sie beieinander stehen** (wörtlich): *„Sie stehen hier
zusammen und nicht verstreut, damit die Lücke **zählbar** ist. Jeder Eintrag sagt, **was es
bräuchte** — das ist der Anfang des nächsten Postens."*

**Frisch aus dem Code gemessen, alle sechs wörtlich:**

| Schritt | Was fehlt (Wortlaut aus dem Code) |
|---|---|
| **Projektgrundlagen** | Bauherr, Adresse und Grundstück stehen **im CRM, nicht im Gebäudemodell**. Solange der Planer sie nicht liest, kann dieser Schritt nichts bestätigen. |
| **Import oder Grundriss** | Ob eine Vorlage importiert und ihr **Maßstab bestätigt** wurde, führt das Dokument nicht. Sichtbar ist nur, ob Wände vorhanden sind. |
| **Räume und Einrichtung** | Raumnutzung und Möblierung sind **im Schema nicht als Eigenschaft** geführt; Räume entstehen abgeleitet aus der Raumerkennung. |
| **Küche und Bad** | Küchen- und Bad-Ausstattung hat **keine eigene Objektart**; nur Sanitärobjekte sind zählbar. |
| **Prüfung und Koordination** | Es gibt **keinen gespeicherten Prüflauf und keine Freigabe** im Dokument. |
| **Dokumentation und Rendering** | Erzeugte Pläne, Listen und Renderings werden **nicht im Dokument vermerkt**. |

### Meine Einordnung — und zwei davon sind längst erfasst

```text
CRM-ANSCHLUSS       Projektgrundlagen
                    Keine Hausplaner-Luecke, sondern eine Anschlussluecke zum CRM.
                    Passt genau in dein objekt-zentriertes Zielbild.

SCHON ALS WERKZEUG  Pruefung und Koordination   -> W-40 (Gueltigkeitsstatus:
ERFASST                                            confirmed/outdated/blocked) und W-41
                                                   (Abhaengigkeitsgraph, keine stille Loeschung)
                    Import oder Grundriss       -> beruehrt W-42 (Schreibpfad Wizard ->
                                                   Gebaeudemodell)
                    BERICHTIGT 12.08.: hier stand „heute schreibt der Wizard NICHT ins
                    Modell". FALSCH. Beim Schneiden von W-42 gemessen und jede Stelle
                    geoeffnet: ConfigWizard.tsx schreibt an DREI Stellen ueber
                    executeCommand ADD_NODE ins Modell — Heizkoerper (:184), Treppe
                    (:205), Fenster UND Tuer (:226). Der Dateikopf und der Bericht sagen
                    beide das Gegenteil, weil beide auf BuildingDocument gemessen haben,
                    und dieses Wort kommt in der Datei NULL Mal vor. Was an diesem
                    Schritt wirklich fehlt, ist die Bestaetigung des MASSSTABS beim
                    Import — nicht der Schreibpfad.

SCHEMA-ERWEITERUNG  Raeume und Einrichtung · Kueche und Bad
                    Beide brauchen eine neue Eigenschaft bzw. Objektart im Gebaeudemodell.
                    Das ist eine DATENENTSCHEIDUNG und keine Ansichtssache.

DOKUMENTENFUEHRUNG  Dokumentation und Rendering
                    Was erzeugt wurde, wird nicht vermerkt.
```

> **Was ich NICHT entschieden habe:** *ob überhaupt eine davon angegangen wird und in welcher
> Reihenfolge. **Vier der sechs berühren das Datenmodell oder das CRM**, und beides ist deine
> Entscheidung — nach den Schutzgrenzen sind Datenbank- und Fachentscheidungen ausdrücklich nicht
> still zu automatisieren.*

**Was von mir aus möglich wäre, ohne dich zu fragen:** W-40, W-41 und W-42 sind bereits im Register
und ohne Code — sie lassen sich als **Vorgabe** (`ENTWORFEN`) schneiden, so wie W-15, W-23 und W-27.
Damit wäre beschrieben, was gebaut werden *soll*, ohne dass eine Zeile Produktivcode entsteht. **Sag
Bescheid, ob ich das anfangen soll** — die vier Stufe-6-Ablesungen (W-33, W-35, W-36, W-37, W-39)
laufen ohnehin weiter und brauchen dich nicht.

---

## 9 · Zwei deiner Statusvorgaben sprechen über dasselbe — **VON DIR BEANTWORTET am 12.08.**

> **Diese Frage ist erledigt. Deine Antwort steht in Abschnitt 11**, eingetragen vom Release-Prüfer
> in `2e7504ec`, und sie hat meine Fragestellung berichtigt statt sie nur zu beantworten:
>
> ```text
> Ich fragte     ist approved DASSELBE wie confirmed?
> Du antwortest  review-required = checked · confirmed = approved · outdated = outdated
>                blocked ist die EINZIGE Erweiterung, 0 Treffer
>                -> W-40 ist eine ABLESUNG MIT EINER ERWEITERUNG, keine Vorgabe
>                -> und die Gueltigkeitsachse haengt am PAKET, nicht am Schritt
>
> Und zur Zahlenluecke: 4 + 3 muss nicht 8 ergeben, weil die vier und die drei
> nicht auf DERSELBEN Achse liegen. Meine Frage war falsch gestellt.
> ```
>
> **Daraus ist `W-40/1` geschnitten** (`4c7ba68b`) — eine Nachbesserung nach §12, weil mein Blatt
> vorgibt, was zu drei Vierteln existiert, und die Achse an den falschen Träger hängt. Deine zwei
> Auflagen (`blockiert_durch`, und `blocked` nie von Hand) stehen dort als Kriterien.
>
> **Und eine Korrektur an meinen eigenen Verweisen:** *ich habe dich mehrfach auf „Abschnitt 8"
> verwiesen. Als ich diesen Abschnitt schrieb, war er die 8 — durch Einschübe anderer Rollen ist er
> zur **9** geworden. Eine Abschnittsnummer in einer Datei, in die fünf Rollen schreiben, ist
> dasselbe wie eine Zeilennummer: **ein Verfallsdatum.** Ab hier verweise ich über die Überschrift.*

**Beim Bau von W-40 ist ein Befund gegen mein eigenes Auftragsblatt aufgetaucht.** Ich hatte
geschrieben, die drei Gültigkeitsstufen fehlten im Bestand. Der Generator hat das widerlegt, der
Release-Prüfer unabhängig nachgemessen, und ich habe es selbst nachgeprüft:

```text
confirmed    0 Treffer im Produktivcode    -> fehlt wirklich
blocked      0 Treffer                     -> fehlt wirklich
outdated     5 Treffer                     -> EXISTIERT

geometry/configuratorPackage.ts
  :25-26   ConfiguratorStatus mit SIEBEN Stufen:
           draft · incomplete · generated · checked · approved · integrated · outdated
  :105-111 vollstaendige Uebergangstabelle je Stufe
  :125-128 markiereVeraltet() — nur approved und integrated werden outdated
  :21      Dateikopf: „Freigabegrade (Yamas Abschnitt 18/3)"

  approved 5 Treffer · integrated 4 · checked 5   -> in Gebrauch, keine Attrappe
```

**Es gibt also eine vollständige Gültigkeitsachse: gebaut, getestet, in Gebrauch.** Und sie stammt
aus **deinem Abschnitt 18/3**, während W-40s Vorgabe aus **deinem Zielbild 3.6** kommt.

### Die Frage, die nur du entscheiden kannst

```text
IST approved (18/3) dasselbe wie confirmed (3.6)?

WENN JA    hat W-40 eine ZWEITE WAHRHEIT vorgegeben — genau das, was das Blatt
           selbst zu verhindern versucht. Dann muesste W-40 auf die vorhandene
           Achse verweisen statt eine neue zu beschreiben.

WENN NEIN  sind es zwei Gegenstaende: Freigabegrad eines KONFIGURATIONSPAKETS
           gegen Gueltigkeit einer GEOMETRIE. Dann ist beides richtig und die
           Namensgrenze muss im Code sichtbar werden — wie bei klassifiziereSchifter
           gegen W-27s Ecken: gleiche Woerter, andere Sache.
```

### Meine Ablesung, 12.08. — auf deine Anweisung „alle Fragen … beantworten"

**Ich habe beide Gegenstände am Code gemessen, nicht am Namen. Die Antwort lautet NEIN — es sind
zwei verschiedene Sachen, und beide Vorgaben sind richtig.**

```text
approved  (Abschnitt 18/3)   Gegenstand: ConfiguratorPackage
  configuratorPackage.ts:1-11 (Dateikopf, dein Wortlaut):
    „KEIN Konfigurator darf voraussetzen, dass bereits ein vollstaendiges
     BuildingDocument existiert. Jede Konfiguration muss autark gestartet,
     gespeichert, versioniert, GEPRUEFT, FREIGEGEBEN und spaeter verlustfrei
     in ein Gesamtprojekt ueberfuehrt werden koennen."
  ConfiguratorType: window · door · stair · roof · wall-buildup
  -> approved ist der FREIGABEGRAD EINES KONFIGURATIONSPAKETS.

confirmed (Zielbild 3.6)     Gegenstand: Planungsschritt / Geometrie
  BERICHT-PROZESSEBENE-DREI-FRAGEN.md, Stufenvergleich:
    „confirmed trennt 'gerechnet' von 'vom NUTZER BESTAETIGT' — ohne sie kann
     L-9 (PV erst nach bestaetigter Dachgeometrie) nicht geprueft werden."
  -> confirmed ist die BESTAETIGUNG EINER GEOMETRIE DURCH DEN NUTZER.
```

**Der Unterschied in einem Satz:** `approved` sagt, dass **ein Paket den Freigabeprozess
durchlaufen** hat; `confirmed` sagt, dass **ein Mensch eine Geometrie bestätigt** hat. Ein Paket
kann `approved` sein, ohne dass irgendjemand eine Dachgeometrie bestätigt hätte — und umgekehrt.

**Warum ich das als Ablesung vorlege und nicht als Entscheidung:** Beide Gegenstände sind im Code
benannt, ich musste nichts festlegen. Das ist der Fall, den ich am 12.08. als „Fall A" von „Fall
B" getrennt habe — wo die Quelle ihre Grenze selbst nennt, lese ich ab; wo eine Grenze erst
gezogen werden muss, entscheidest du. **Hier nennt der Code sie selbst.**

**Was daraus folgt und was ich NICHT getan habe:** Die zweite Hälfte der Frage — *„dann muss die
Namensgrenze im Code sichtbar werden"* — bleibt offen. Das wäre ein Bauauftrag (ein Satz in
beiden Dateien, der auf die jeweils andere Achse verweist), und Bauen ist nicht meine Rolle.
**Wenn du meine Ablesung bestätigst, ist es ein kleiner Posten für den Planner.**

> **Ich entscheide das nicht.** *Es ist eine Fachfrage über zwei Vorgaben von dir, und die
> Antwort ändert, was gebaut wird — im einen Fall ein Verweis, im anderen eine zweite Achse. **Mein
> Anteil am Fehler ist benannt:** ich habe die Abwesenheitsaussage der Quelle übernommen, statt im
> Code zu messen. Die Quelle hatte `SchrittStatus` vor sich und nicht `ConfiguratorStatus`.*

**Zwei kleinere Fragen aus W-40 hängen daran:**

```text
review-required   Dein Zielbild 3.6 fuehrt ACHT Stufen. Vier sind gebaut, drei sind
                  als fehlend benannt — 4 + 3 = 7. Gehoert die achte zur
                  Gueltigkeitsachse oder nicht?
blocked           Die Quelle beziffert sie nicht. Wie grenzt sie sich von
                  DECISION_BLOCKED im Prozess ab?
```

---

## 10 · W-42 soll nach deiner eigenen Regel anders geschnitten werden, als deine Zusage lautet

**NEU am 12.08. — und ich trage einen Vorwurf mit, der berechtigt ist.** Der Plan-Prüfer hat in
`a482d437` festgestellt, dass diese Frage seit heute Mittag in jedem seiner Berichte steht, aber
**nirgends im Repositorium** — und dass meine Meldung „alle Fragen an dich beantwortet oder
entscheidungsreif" sie nicht enthielt. Das stimmt: `W-42` kommt in dieser Vorlage dreimal vor,
dreimal als Nebensatz, **keinmal als offene Frage**. Nachgemessen, nicht eingeräumt aus Höflichkeit.

### Der Widerspruch besteht zwischen zwei Aussagen von dir vom selben Tag

```text
DEINE ZUSAGE      "ich sage zu diesen sachen ja W-40, W-41, W-42 warten auf dich"
                  — zugestimmt hast du dem ANGEBOT des Planners, alle drei als
                  VORGABE mit Ziel ENTWORFEN zu schneiden. Eine Gattungszusage
                  fuer drei Auftraege, kein Einzelurteil ueber W-42.

DEINE LEGENDE     REGISTER.md Z.10-11, ausdruecklich als "Yamas Entscheidung 12.08."
                  markiert: ENTWORFEN ist "fuer Werkzeuge, deren Code noch nicht
                  existiert (Klasse C)". Und im Textblock Z.15-16:
                  "ENTWORFEN  die Blaetter GEBEN VOR, was gebaut werden soll.
                              Quelle: Vertrag, Fachregel oder Zielbild — KEIN Code."
```

**Und der Code von W-42 existiert.** Selbst gemessen, jede Datei geöffnet:

```text
resources/planner/hausplaner/app/ConfigWizard.tsx              271 Zeilen
resources/planner/hausplaner/geometry/configuratorPackage.ts   170 Zeilen
resources/planner/hausplaner/app/state/paketSpeichern.ts        64 Zeilen
resources/planner/hausplaner/__tests__/paketSpeichern.test.ts  126 Zeilen
```

Damit fällt W-42 nach **deiner eigenen Legende** nicht unter ENTWORFEN. Der Planner hat deshalb
`BESCHRIEBEN` gesetzt, die Abweichung **gemeldet statt still vollzogen**, und dazugeschrieben:
*„will Yama es anders, gilt seine Fassung."*

### Der Präzedenzfall ist schon da, und er ist abgeschlossen

Das ist keine Hypothese. **W-40 trägt heute `ENTWORFEN` im Register und ist `BETRIEBSBESTAETIGT`** —
und in derselben Registerzeile steht der Befund:

> **BEFUND: eine Gültigkeitsachse mit Übergängen ist in `geometry/configuratorPackage.ts` bereits
> gebaut**

Ein Werkzeug, dessen Code existiert, trägt also bereits die Stufe, die deine Legende für Werkzeuge
ohne Code reserviert. Vier Rollen sind daran vorbeigegangen, ich eingeschlossen. Bei W-42 ist es
noch aufzuhalten, weil der Auftrag `BEREIT` steht: **heute kostet die Änderung eine Zeile, nach dem
Bau sind es sieben Blätter.**

### Meine Ablesung — und warum ich sie nicht vollziehe

Ich lese ab, dass die **speziellere** deiner beiden Aussagen für `BESCHRIEBEN` spricht: die Legende
ist schriftlich, nennt das Merkmal (Code ja/nein), und die Zusage war eine Gattungszustimmung zu
einem Vorschlag über drei Aufträge auf einmal. Der Weg des Planners deckt sich damit.

**Ich fasse W-42 trotzdem nicht an**, und zwar nicht aus Vorsicht, sondern weil es nichts zu
reparieren gibt: der Planner hat bereits `BESCHRIEBEN` gesetzt, der Bau ist nicht blockiert, und die
einzige Lücke war die **Sichtbarkeit** — die schließe ich hiermit. Es fehlt allein dein Wort dazu,
ob deine Zusage oder deine Legende gelten soll.

```text
ENTSCHEIDUNGSREIF, drei Zeilen:
  a) Legende gilt  -> W-42 bleibt BESCHRIEBEN. Nichts zu tun. (meine Ablesung)
  b) Zusage gilt   -> W-42 auf ENTWORFEN. Kostet heute eine Zeile im Blatt.
  c) Und unabhaengig davon: soll W-40 nachgezogen werden? Es ist abgeschlossen,
     traegt ENTWORFEN und seinen eigenen Gegenbefund in derselben Zeile.
```

### Nachtrag, eine Stunde später: es geht nicht um W-42, es geht um die Quelle

Während ich diesen Punkt schrieb, haben Generator und Planner unabhängig voneinander dasselbe
gefunden. Ich habe beide Befunde **selbst nachgemessen**, nicht übernommen:

```text
DIE QUELLE       docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md — sie traegt ALLE DREI
                 Vorgaben: W-40 (BETRIEBSBESTAETIGT), W-41 (CODE_FERTIG), W-42 (BEREIT).

IHRE AUSSAGE     Z.184-185 woertlich: "ConfigWizard 271 Z ... schreibt NICHTS ins
                 BuildingDocument, laedt JSON herunter."

DER CODE         grep executeCommand ConfigWizard.tsx -> DREI Treffer, selbst geoeffnet:
                   :184  ADD_NODE  radiator
                   :205  ADD_NODE  treppe
                   :226  ADD_NODE  knoten (Fenster oder Tuer)
                 Er schreibt dreimal ins Gebaeudemodell. Der JSON-Download ist der
                 Rueckfall, nicht der Regelfall.
```

**Damit ist die Abwesenheitsaussage dieser Quelle zweimal nachweislich falsch** — bei W-40
(`outdated` existiert samt Übergangstabelle) und bei W-42 (der Schreibpfad existiert dreifach).
Der Verfasser hat vier Punkte ausdrücklich als *„nicht gemessen"* gekennzeichnet; **diese beiden
gehörten nicht dazu, sie standen als Ergebnis da.**

Der Planner hat daraufhin bei `W-15` zum ersten Mal **vor** dem Schnitt gemessen — und auch dort
trägt die Prämisse zur Hälfte nicht (Wand→Mauerwerk ist gebaut mit Schema, Validierung, Katalog und
Oberfläche; Raum→Belag fehlt). Vier Fälle, dreimal falsche Prämisse, und **nur der letzte hat
nichts gekostet, weil vorher gemessen wurde.**

**Was das für deine Entscheidung ändert:** Die Frage ist nicht mehr „welches Wort trägt W-42 im
Register". Sie lautet: *ein einziger Bericht hat drei Vorgaben getragen, und seine „gibt es
nicht"-Sätze waren Suchraum statt Bestand.* W-40 ist damit schon durch die ganze Kette gelaufen.

```text
DAZU BRAUCHT ES VON DIR NICHTS — es ist gemessen und die Rollen haben reagiert.
Es steht hier, damit du es weisst, bevor du ueber a) b) c) oben entscheidest.
```

---

## 11 · W-40 aufgelöst — Reifegrad, Bau und zweite Wahrheit, auf deine Anweisung beantwortet

**Dein Auftrag, 12.08. wörtlich:** *„Eine Antwort von dir löst jetzt drei Dinge auf einmal: W-40s
Reifegrad, W-40s Bau und die Frage, ob dort eine zweite Wahrheit entstanden ist. diese Frage war an
mich gerichtet kannst du das übernehmen und sauber fundiert beantworten"*

### Der Schlüssel ist der Träger, nicht das Wort

Alle vier Rollen — ich als vierte — haben `ConfiguratorStatus` gegen W-40s Vorgabe gehalten und
gefragt, ob dasselbe zweimal da ist. **Die Frage lässt sich nicht am Namen entscheiden, nur am
Träger.** Selbst gemessen, jede Stelle geöffnet:

```text
ConfiguratorStatus  haengt an  ConfiguratorPackage       configuratorPackage.ts:72
                    und das ist :69  type: ConfiguratorType
                    window · door · stair · roof · pv · kitchen · +17 weitere
                    -> der Freigabegrad eines autarken BAUTEIL-PAKETS

SchrittStatus       haengt an  Fahrschritt               studioDaten.ts:169
                    und an     Pruefpunkt                :164
                    -> der Fortschritt eines SCHRITTS im Planungsablauf

W-40s Traeger       legt sein eigenes Blatt fest, 2-FUNKTION Z.42-45:
                      fortschritt: SchrittStatus       (W-38, gebaut)
                      gueltigkeit: Gueltigkeitsstatus  (W-40, Vorgabe)
                      "ein Schritt kann ok UND confirmed sein"
                    -> also am SCHRITT, nicht am Paket.
```

**Gegenprobe gefahren statt angenommen:** `studioDaten.ts` nennt `ConfiguratorPackage` genau
einmal — Zeile 159, und das ist eine **Zeichenkette in Beispieldaten** (`meta: 'ConfiguratorPackage
· gestern'`), Import-Zähler **0**. Umgekehrt kommt `Fahrschritt` in `configuratorPackage.ts`
**0**-mal vor. **Die beiden Träger kennen einander nicht.**

### Die drei Antworten

```text
1  ZWEITE WAHRHEIT?   NEIN, heute keine — und das ist gemessen, nicht beruhigt:
                        Gueltigkeitsstatus   0 Treffer in resources/
                        Feld  gueltigkeit    0
                        Wert  'confirmed'    0
                        Wert  'blocked'      0
                      Eine Achse am Paket, eine am Schritt. Wo nichts gebaut
                      wurde, kann keine zweite Wahrheit sein.

2  W-40s BAU?         NULL Code. Am Bau-Commit 1eedb9cf gemessen: zehn Dateien,
                      ALLE unter docs/ — sieben Blaetter, Bericht, REGISTER,
                      STATUS. Code-Dateien unter resources/: 0.
                      W-40 hat vorgegeben, nicht gebaut.

3  REIFEGRAD?         ENTWORFEN IST RICHTIG UND BLEIBT. Die Legende verlangt,
                      dass der Code DES WERKZEUGS nicht existiert — und die
                      Gueltigkeitsachse AM SCHRITT existiert nicht. Was
                      existiert, ist eine Freigabeachse AM PAKET.
```

### Ich berichtige dabei meinen eigenen Befund von heute Mittag

In `c002574b` habe ich geschrieben: *„ein Werkzeug mit Code trägt die Stufe, die für Werkzeuge ohne
Code gedacht ist."* **Das war zu schnell.** Ich habe Namensgleichheit für Gegenstandsgleichheit
genommen — genau der Fehler, vor dem W-27s eigene Namensgrenze warnt (`klassifiziereSchifter` gegen
Ecken: *gleiche Wörter, andere Sache*). Der Planner hat es richtig gerochen, als er schrieb, die
Frage sei nicht der Reifegrad, sondern der Gegenstand; auflösen konnte er es nicht, weil ihm der
Träger fehlte.

**Damit ist auch Punkt 9 vollständig:** `approved` ist **nicht** dasselbe wie `confirmed`. `approved`
sagt, ein **Bauteil-Paket** ist freigegeben und darf übernommen werden (`kannIntegrieren`, in
Gebrauch in `integrationAbgleich.ts:13` und `:134`). `confirmed` sagt, ein **Planungsschritt** trägt
ein vom Nutzer bestätigtes Ergebnis. Meine erste Fassung nannte es *Paket gegen Geometrie* — genauer
gemessen ist es **Paket gegen Schritt**.

### Eine Stelle, an der die zweite Wahrheit doch entstehen kann

```text
outdated steht in BEIDEN Listen und meint beide Male dasselbe:
"durch spaetere Aenderung veraltet".

Wer W-40 baut, OHNE die Uebergangslogik mit configuratorPackage.ts zu teilen,
erzeugt zwei outdated mit zwei Tabellen. Das ist eine AUFLAGE FUER DEN BAU —
kein heutiger Mangel.
```

### Was bei dir bleibt

Zwei Fachfragen über **dein Zielbild**, die am Code nicht ablesbar sind — ich beantworte sie nicht:

```text
review-required   gehoert sie zur Gueltigkeitsachse? 4 + 3 = 7, dein Zielbild 3.6
                  nennt ACHT Stufen. Eine Angabe fehlt.
blocked           wie grenzt es sich von DECISION_BLOCKED im Prozess ab?
                  Die Quelle sagt vier Woerter: "blocked ist die Sperre."
```

**Ein Hinweis zur Registerzeile 127:** ihr Befund sagt *„eine Gültigkeitsachse ist bereits gebaut"* —
**ohne den Träger zu nennen.** Genau diese Verkürzung hat vier Rollen und mich in die Irre geführt.
Wer sie präzisiert, sollte **„am Paket"** dazuschreiben.

---

## 12 · NEU am 12.08.: die Tafel hat dir zweimal eine Sperre gezeigt, die es nicht mehr gab

**Das ist der Punkt, der dich direkt betrifft** — *nicht als Entscheidung, sondern weil du auf diese
Spalte schaust, um zu sehen, was von dir erwartet wird.*

### Der erste Fall: AUF-40, in deiner Spalte

Die Tafelzeile führte AUF-40 mit **„Rest `GESPERRT` — wartet auf Yama"** und nannte zwei Gründe:
die Zulieferung der echten Projektliste, und **„Teil B ist von Yama am 26.07. vertagt"**.

**Beide Hälften sind gebaut.** Jede Stelle selbst geöffnet:

```text
HAELFTE 1 — die Projektliste (AUF-78, mit eigener Evaluator-Abnahme):
  Hausplaner/HausplanerController.php:42  PROJEKTLISTE_MAX = 6
                                    :101  hausplanerProjekte()
                                    :55   als hpProjekte durchgereicht
  admin/hausplaner/objekt.blade.php:141   data-projekte
  main.tsx:18 + :82                       leseProjekte -> UI-Zustand

HAELFTE 2 — die Konfigurator-Persistenz (AUF-81):
  migrations/2026_07_26_180000_create_hausplaner_configurator_packages_table.php
  Models/HausplanerConfiguratorPackage.php
  web.php:5016 / :5018 / :5020            drei Routen, je permission:Hausplaner
```

**Und die Vertagung hast du selbst aufgehoben, am selben Tag.** *Der Kopf von AUF-81 zitiert dich
wörtlich: „Tor 1: von Yama freigegeben (26.07.: **‚wir brauchen Datenbank, Migration, Routing'**)."
**Nur die Tafel hat es nicht mitbekommen — 17 Tage lang.***

> **Was ich fast getan hätte, und das ist der unangenehme Teil:** *ich hatte diesen Posten als
> **deine offene Entscheidung** auf die Vorlage schreiben wollen. **Ich hätte dir vorgelegt, was du
> selbst freigegeben hast.** Das ist genau die Falschauskunft, die W-33 im Startbildschirm behebt —
> nur an dich statt an den Nutzer. Gefunden hat es der Generator, bevor er baute.*

### Der zweite Fall: AUF-83-T5, an zwei Orten gleichzeitig

**Nachdem der erste Fall auftauchte, habe ich die Reichweite gemessen statt sie zu schätzen** — und
mein erster Befehl war zu eng (nur deine Spalte, eine Zeile, „kein Sammelbefund"). Die richtige Frage
war die Statusspalte: **drei Aufträge stehen auf `gesperrt`, einer davon ist gebaut.**

```text
AUF-83-T5   Bau 74ad1075 (30.07.) „beide Schienen klappbar, Escape mit Rangfolge"
            Quittung 44fce81c · Pruefer-Messung 6cafeffd (serviert == gemessen)
            und die Sache WIRKT: schienenSpeicher.ts wird von HausplanerApp.tsx:71
            importiert, :425 haelt, :427 laedt, :432 speichert
            -> Tafel UND Blattkopf trugen 13 Tage lang GESPERRT
AUF-85      traegt weiter (Ausloeser lokal HausplanerApp.tsx:552, im Studio keine Palette)
AUF-88      Aufnahme ohne Blaetter, traegt weiter
```

*Der Sperrgrund von T5 nannte **„0 Klappzustände in der Insel", gemessen am 29.07. 21:45 — einen Tag
vor dem Bau.* **Die Zahl war richtig und ist am nächsten Tag falsch geworden.** *Niemand zieht solche
Zahlen nach, weil niemand sie für vergänglich hält.*

**Nicht behauptet:** *dass T5 abgenommen ist. Ich sehe eine Prüfer-Messung, keine Evaluator-Quittung.
Status steht auf `gebaut`, nicht auf `erledigt`.*

### Was ich daraus geschlossen habe, und was ich dir NICHT abnehme

**Erledigt ohne dich:** *beide Tafelzeilen berichtigt, bei T5 auch der Blattkopf; die alten Wortlaute
stehen als Beleg des Fehlers daneben statt gelöscht zu werden.*

**Die Frage, die bei dir liegt — Kandidat für eine Hausregel H-10:**

> **Bei AUF-40 haben DREI Rollen unabhängig dasselbe falsch gemessen** — *Planner, Plan-Prüfer und
> Release-Prüfer haben nach einer **Route** gesucht, null gefunden und auf „nicht gebaut" geschlossen.
> Die Liste kommt über ein **Mount-Attribut ohne Lade-Fetch**; der Controller sagt das in `:57`
> wörtlich. **Rollentrennung schützt hier nicht:** drei Messungen derselben zu engen Frage geben
> dreimal dieselbe falsche Antwort.*

*Mein Vorschlag, aber die Regel gehört dir:* **wer eine Abwesenheit behauptet, nennt mindestens zwei
Bauformen, in denen die Sache existieren könnte, und misst beide** — und **bei einer Zulieferung wird
am ZIEL gemessen, nicht am Weg** (nicht „gibt es eine Route", sondern „steht der Wert im
UI-Zustand"). *In meine Pflichtprüfung 1 habe ich es schon eingetragen; als Hausregel für alle Rollen
setzt es nur du.*

### Der Sicherungsstand — **und hier habe ich dir eine falsche Beruhigung geschrieben**

**Meine erste Fassung sagte „Zur Kenntnis, kein Handlungsbedarf" und nannte vier Commits Vorlauf.**
*Ich hatte ohne `fetch` gemessen, **den Vorbehalt sogar hingeschrieben — und trotzdem beruhigt.** Der
Plan-Prüfer hat den Vorbehalt eingelöst statt ihn zu wiederholen (`a4617241`), und was er fand, war
nicht eine andere Zahl, sondern eine andere **Art**:*

```text
seine Messung (live, ls-remote):   fern 4403f52e · lokal 63c474ff · Basis e910d13f
                                   3 nur lokal, ZEHN nur entfernt -> DIVERGENT
                                   der Fernstand war KEIN Vorfahr des lokalen
                                   die drei lokalen einzeln geprueft: auf KEINER Kopie
                                   ausserhalb dieses Rechners

meine Nachmessung, Minuten spaeter: fork 3f167037 · backup-private bca7f5d8
                                   fork IST Vorfahr des lokalen -> NICHT divergent
                                   1 Commit nur lokal
```

> **Beide Messungen waren zu ihrem Zeitpunkt richtig.** *Zwischen ihnen hat der Release-Prüfer
> zusammengeführt und gepusht — bis auf meinen W-37-Commit. **Und genau das ist der Punkt, nicht die
> Zahl:** ein Sicherungsstand ist eine Momentaufnahme, und „kein Handlungsbedarf" ist keine
> Momentaufnahme, sondern ein Urteil über die Zukunft. **Dieselbe Klasse wie die Sperrgründe eine Seite
> vorher:** „0 Klappzustände", richtig gemessen, einen Tag später falsch.*

**Was stabil gilt, ohne Verfallsdatum:**

- **Es gibt immer einen ungesicherten Vorlauf**, weil hier laufend committet wird. Die Frage ist nie
  „ob", sondern „wie viel gerade" — *und solange nichts deployt ist, ist der gepushte Stand die einzige
  Kopie außerhalb dieser Maschine.*
- **Zwei Linien sind die Bauart, nicht ein Fehler**: der Release-Prüfer arbeitet auf eigener Linie und
  führt zusammen. *Divergenz ist dort normal; ein Push holt sie nicht ein, sie braucht eine bewusste
  Zusammenführung.*
- **`origin` ist read-only** und wächst zurück — *44 in meiner Messung, 49 in seiner. Erwartet.*

*Kein Handlungsbedarf **für dich** — aber das ist mein Urteil und nicht die Messung, und diesmal steht
der Unterschied da.*

#### Nachmessung des Plan-Prüfers, 12.08. — es ist **kein** reiner Vorlauf

**Der Vorbehalt oben war der richtige, und ich habe ihn eingelöst:** *`ls-remote` fragt den echten
Fernstand ab, ohne einen Verweis zu verändern — die Zahlen stammen also nicht aus einem alten Abruf.*

```text
fork (live)             4403f52e
backup-private (live)   4403f52e
lokal HEAD              63c474ff
gemeinsamer Punkt       e910d13f
nur lokal                3 Commits — 1faea789 plan-pruefer · c767426d generator · 63c474ff evaluator
nur entfernt            10 Commits
origin/auto/hausplaner-integration   HEAD ist 49 voraus, 0 zurueck
```

**Der Unterschied zur Zeile darüber ist nicht die Zahl, sondern die ART.** *Die beiden Stände sind
**divergent**: der Fernstand ist **kein Vorfahr** des lokalen. Einen Vorlauf holt ein Push ein — hier
braucht es eine bewusste Zusammenführung.*

**Und die drei lokalen Commits liegen auf keiner Kopie außerhalb dieses Rechners.** *Einzeln geprüft,
keiner ist in `4403f52e` enthalten. Solange nichts deployt ist, ist der gepushte Stand die einzige Kopie
außerhalb der Maschine — **„kein Handlungsbedarf" trägt für diese drei nicht.***

**Die 44 gegen `origin` sind nicht falsch, sondern gewachsen:** *gemessen 49 voraus, 0 zurück. Eine Zahl
mit Verfallsdatum, genau wie die Sperrgründe eine Seite vorher.*

**Nicht behauptet:** *dass jemand einen Fehler gemacht hat. Der Release-Prüfer arbeitet auf einer eigenen
Linie und führt zusammen; dass dabei zwei Linien entstehen, ist die Bauart. Nur der Satz „kein
Handlungsbedarf" trägt nicht, solange drei Commits ohne Kopie sind.*

**Die übrigen Zahlen dieses Abschnitts habe ich nachgemessen und sie halten:** *`PROJEKTLISTE_MAX = 6`
an `HausplanerController.php:42`; die Naht `:101` → `:55` → `objekt.blade.php:141` → `main.tsx:18/:82`;
die drei Routen `web.php:5016/:5018/:5020` mit Recht; die AUF-83-T5-Kette `74ad1075` · `44fce81c` ·
`6cafeffd`, alle drei vom 30.07.; und `HausplanerApp.tsx:71` Einfuhr, `:425` hält, `:427` lädt, `:432`
speichert.*

##### Nachtrag desselben Prüfers, eine halbe Stunde später — **die Gabelung ist weg**

**Die Überschrift oben ist überholt, und ich kennzeichne sie hier statt sie zu löschen** — *dieselbe
Form, die ich in derselben Stunde einem Bauenden auferlegt habe: der überholte Satz bleibt lesbar, die
Berichtigung steht daneben.*

```text
erneut LIVE gemessen (ls-remote, kein Verweis veraendert):
  fork                     3f167037   Vorfahr von HEAD  -> reiner Vorlauf, 4 voraus / 0 zurueck
  backup-private           bca7f5d8   Vorfahr von HEAD  -> reiner Vorlauf, 5 voraus / 0 zurueck
  lokal HEAD               da16118c
```

**Damit trägt „kein Handlungsbedarf" wieder** — *die zehn Commits, die vorhin nur entfernt lagen, sind
zusammengeführt; es gibt keine zweite Linie mehr.* **Was bleibt, ist der gewöhnliche Vorlauf:** *vier
bzw. fünf Commits liegen noch auf keiner Kopie außerhalb dieses Rechners, und die beiden
Sicherungszweige stehen einen Commit auseinander — beides normal, solange laufend gesichert wird.*

*Ich lasse den Befund stehen, weil er zum Zeitpunkt der Messung richtig war. **Eine Lagemeldung ist ein
Zeitpunkt-Beleg, kein Zustand** — genau das, was dieser Abschnitt an den Sperrgründen zeigt.*
