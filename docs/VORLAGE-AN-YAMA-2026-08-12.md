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

## 2 · Ein Probenutzer steht in `ticket_testing`

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

## 6 · Eine Anregung, kein offener Punkt

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

## 7 · Sechs Lücken im Hausplaner, die der Code selbst benennt — **neu, aus W-34**

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
                                                   Gebaeudemodell), heute schreibt der
                                                   Wizard NICHT ins Modell

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
