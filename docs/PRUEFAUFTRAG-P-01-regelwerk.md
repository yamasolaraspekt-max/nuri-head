# P-01 — Prüfauftrag: die Regelwerksfassung prüfen und freigeben

```yaml
pruefauftrag: P-01
gegenstand: "ARBEITSREGELN Fassung 1.1 und 1.2.1 — Inhalt pruefen, freigeben, Gabelung zu 1.3 beurteilen. Kriterien: Widerspruchsfreiheit · Pruefbarkeit · Herkunft · Machtpruefung · Gabelung · KAUSALITAET · PLAUSIBILITAET · KONSISTENZ (die letzten drei auf Yamas Weisung 05.08.)"
vorgelegt_von: planner
ballbesitz: plan-pruefer
grundlage: "Yamas Weisung 05.08.: 'lass doch von plan pruefer die fassung pruefen und freigeben, dann wird das verbindlich'"
stand_sha: 7c7d38f6
status_steht_in: docs/STATUS.md
```

## Warum es diesen Prüfauftrag gibt

**Ich habe Regeln geschrieben, die niemand geprüft hat.** Auf Yamas Auftrag, aber ungeprüft — und
damit gilt für sie genau das, was §5 für jeden Auftrag verlangt und was sie selbst von anderen
fordern. *Ein Regelwerk, das ohne Prüfung verbindlich wird, ist die Ausnahme, die es verbietet.*

**Yamas Weisung macht deine Freigabe zum Akt, der sie verbindlich macht.** Nicht meine Niederschrift.

## Prüfgegenstand — und was ausdrücklich NICHT dazugehört

```text
ZU PRUEFEN     1.1    vier Regeln     §3 (IN_ARBEIT-Ausloeser) · §5 (drei Punkte, 15->18)
                                      §7 (zwei Punkte)                      450b5bee
               1.2.1  fuenf Abschnitte §12.1 Ball · 12.2 Nachbesserung · 12.3 Rueckweg
                                      12.4 Kriterien · 12.5 Abnahme trotz Befund   7c7d38f6

NICHT ZU       1.2    Yamas eigene Weisung (Vertretung der Veroeffentlichung),
PRUEFEN               von ihm selbst committet - c811836c. Nicht mein Text,
                      nicht mein Vorschlag, steht nicht zur Disposition.
```

## Ein Hindernis, das du sonst selbst suchen müsstest

**Es gibt keinen Diff von 1.0 nach 1.1.** `ARBEITSREGELN.md` war auf diesem Zweig **ungetrackt**;
mein Commit `450b5bee` erscheint deshalb als `+495/-0` — er hat die ganze Datei angelegt.

```text
Umweg 1   §19 Aenderungsverzeichnis listet jede 1.1-Regel mit ihrem Vorfall
Umweg 2   git show governance/arbeitsregeln-v1.1-20260804:docs/ARBEITSREGELN.md
          -> eine unabhaengige Fassung derselben Wurzel, gegen die sich vergleichen laesst
Umweg 3   PROZESSPRUEFUNG-01.md traegt die Messungen, aus denen 1.1 entstand
```

*Ich nenne es, weil du es sonst als Erstes suchst und nicht findest.*

## Was ich dich zu prüfen bitte

**P-01-1 — Widerspruchsfreiheit.** Widerspricht eine neue Regel einem bestehenden Paragraphen?
*Besonders: §12.1 sagt `SPEC` erzeugt `SPEC_BLOCKED` statt `NACHBESSERN` — §3 führt beide, aber die
Zuordnung stand nirgends. Ist das eine Ergänzung oder ein stiller Widerspruch?*

**P-01-2 — Prüfbarkeit.** Hat jede neue Regel einen **beobachtbaren Auslöser**, oder ist sie Prosa?
*Eine Regel ohne Auslöser ist ein Vorsatz. Das ist genau der Vorwurf, den 1.1 gegen die alten
Regeln erhebt — sie muss ihn selbst aushalten.*

**P-01-3 — Herkunft.** §19 behauptet, jede Regel stamme aus einem gemessenen Vorfall.
**Stichprobe genügt nicht: bitte alle neun.** *Wenn eine erfunden ist, ist die Tabelle wertlos.*

**P-01-4 — Machtprüfung, die mir selbst gilt.** Habe ich mir mit einer Regel Befugnis zugeschoben?
*Ich habe sie geschrieben und kann das schlecht selbst beurteilen. Konkret verdächtig: §12.5
(„abgenommen trotz `SPEC`-Befund") entlastet die Rolle, deren Fehler `SPEC` ist — meine.*

**P-01-5 — die Gabelung.** `governance/arbeitsregeln-v1.1-20260804` führt eine **eigene 1.3**:

```text
hier 1.2.1     590 Zeilen   §5: 18 Punkte   Statustraeger: STATUS.md (in §16 NICHT benannt)
dort 1.3       592 Zeilen   §5: 15 Punkte   Statustraeger: docs/AKTUELLER_AUFTRAG.yaml (benannt)
               229 abweichende Zeilen · dort zusaetzlich: Statuscommits mit genau EINEM
               Zustandswechsel · fortsetzung_zustand · Push = Transport, nicht VEROEFFENTLICHT
```

**Deine Aufgabe hier ist ein Votum mit Messung, keine Ausführung.** *Fällt es auf 1.3, ist der
Statusträger ein anderer — das ändert die tägliche Arbeit aller Rollen mitten in vier laufenden
Aufträgen und braucht einen Übergangsplan. Den schneide ich, du entscheidest ihn nicht mit.*

## Drei zusätzliche Kriterien (Yama, 05.08.) — und wo ich selbst Zweifel habe

**Yama verlangt: Kausalität, Plausibilität, Konsistenz.** Sie sind schärfer als meine fünf Punkte,
und ich nenne zu jedem die Stelle, an der ich meine **eigene** Regel für gefährdet halte. *Nicht um
sie zu retten — um dir die Suche zu ersparen und dir zu zeigen, dass ich sie kenne.*

### P-01-6 — KAUSALITÄT

**Nicht: „gab es den Vorfall?" (das ist P-01-3), sondern: HÄTTE DIE REGEL IHN VERHINDERT?**
Je Regel eine Antwort, und die Antwort darf `NEIN` sein.

> **Mein Verdacht gegen mich: §12.5.** Sie hält fest, was der Evaluator bei A-03 **ohnehin schon
> getan hat**, bevor es die Regel gab. Sie hat also nichts verhindert — sie beschreibt.
> *Beschreibende Regeln sind nicht wertlos, aber sie gehören nicht in eine Tabelle, die
> „erzwungen durch" überschrieben ist. Prüfe, ob ich Beschreibung als Ursache verkauft habe.*

### P-01-7 — PLAUSIBILITÄT

**Ist die Regel im Alltag lebbar, oder wird sie umgangen?** *A-02 hat gezeigt, dass eine unlebbare
Regel nicht befolgt, sondern umgangen wird — und der Umweg gefährlicher ist als der Fall, gegen den
die Regel gebaut wurde.*

> **Mein Verdacht gegen mich: der §5-Punkt „vorhanden UND in Gebrauch".**
> Für ein **neues** Werkzeug ist er unerfüllbar — was neu ist, ist per Definition noch nicht in
> Gebrauch. Nach dem Wortlaut könnte kein Auftrag je ein neues Werkzeug vorschreiben.
> **Ich halte das für einen echten Mangel, nicht für eine Spitzfindigkeit.** *Wenn du ihn
> bestätigst, gehört ein Halbsatz hinein: „in Gebrauch" gilt für **vorhandene** Formen; ein neu
> gebautes Werkzeug muss stattdessen einen benannten Erstnutzer haben.*
>
> Zweiter Verdacht: **§12.4 „alle Kriterien erneut"** — plausibel, solange Kriterien Befehle sind.
> Sobald eines eine Browserabnahme ist, kostet jede Nachbesserungsrunde eine volle Browserrunde.
> Der Absatz nimmt das aus, aber prüfe, ob die Ausnahme scharf genug formuliert ist.

### P-01-8 — KONSISTENZ

**Widerspruchsfreiheit (P-01-1) plus Begriffstreue:** heißt dieselbe Sache überall gleich, und wird
derselbe Begriff nie für zwei Dinge benutzt?

> **Mein Verdacht gegen mich: `SPEC_BLOCKED` trägt jetzt zwei Bedeutungen.**
> §3 definiert ihn als *„Auftrag ist widersprüchlich, unvollständig oder nicht machbar"* — das ist
> ein Auftrag **vor** dem Bau. §12.1 schickt jetzt auch einen **gebauten** Auftrag dorthin, dessen
> Spezifikation sich im Nachhinein als falsch erwies. **Das sind zwei verschiedene Lagen im selben
> Zustand.**
>
> *Ein Zustand, der „noch nicht baubar" und „gebaut, aber falsch verlangt" zugleich bedeutet, sagt
> beim Lesen nichts mehr. Möglicherweise braucht es einen eigenen Zustand — oder §3 muss die zweite
> Bedeutung ausdrücklich aufnehmen. Das ist deine Entscheidung, nicht meine Vorwegnahme.*

**Zusätzlich zur Konsistenz nach außen:** stimmt das Regelwerk mit dem überein, was tatsächlich
geschieht? *`docs/STATUS.md` ist in §16 **nicht benannt**, wird aber von allen Rollen als
Statuswahrheit benutzt. Entweder benennt §16 sie, oder wir benutzen etwas, das die Regeln nicht
kennen. Diese Frage hängt an der Gabelung (P-01-5).*

## Dein Votum

```text
FREIGEGEBEN            1.1 und 1.2.1 sind ab sofort verbindlich (Yamas Weisung)
FREIGEGEBEN MIT AUFLAGE  verbindlich, benannte Stellen nachzubessern - ich bessere nach
NICHT FREIGEGEBEN      mit Liste; die Fassungen bleiben Entwurf, ich schneide neu
```

**Zur Gabelung getrennt:** `1.2.1 FUEHRT` · `1.3 FUEHRT` · `YAMA MUSS ENTSCHEIDEN`.

## Was ich nicht von dir will

- **Keine Umformulierung.** Melde, was fehlt — ich schneide neu (deine eigene Rollengrenze).
- **Keine Freigabe aus Höflichkeit.** `NICHT FREIGEGEBEN` ist hier das wertvollere Votum: diese
  Regeln binden ab Freigabe **jede** Rolle, auch dich, und sie sind schwerer zurückzunehmen als ein
  Auftrag. *Ein durchgewinkter Auftrag kostet einen Tag. Eine durchgewinkte Regel kostet, bis sie
  jemandem auffällt.*

---

# VOTUM des Plan-Prüfers — 05.08.

```yaml
pruefauftrag: P-01
votum: FREIGEGEBEN MIT AUFLAGE      # 1.1 + 1.2.1 sind ab sofort verbindlich; vier benannte Stellen bessert der Planner nach
gabelung: "1.2.1 FUEHRT (Inhalt) — die ZWEIG-Zusammenfuehrung (fork enthaelt den governance-Merge, wir nicht; 42 vs 10 Commits) bleibt ausdruecklich bei Yama: das ist Topologie, nicht Fassungsinhalt"
gemessen_an: 90ebba40
```

**P-01-1 Widerspruchsfreiheit — ERGÄNZUNG mit einer Konsistenz-Lücke** (fällt mit P-01-8 zusammen):
§12.1 schickt einen GEBAUTEN Auftrag nach `SPEC_BLOCKED`, §3 definiert den Zustand als „vor dem
Bau nicht machbar". Im Verhalten kein Widerspruch (beide heißen „Spezifikation muss neu"), im
Definitionstext schon. → **Auflage A1.**

**P-01-2 Prüfbarkeit — JA für acht von neun.** §3-Auslöser messbar (Zustand vs. erste Scope-Änderung),
die drei §5-Punkte checklistenfähig mit Befehl, 12.1–12.4 mechanisch bzw. am Commit ablesbar.
Die Kommentar-Regel (§7) hat keinen automatischen Trigger, aber eine prüfbare Aussage je Fundstelle
— der A-02-Fall wurde genau so gemessen. Akzeptiert mit Anmerkung, keine Auflage.

**P-01-3 Herkunft — ALLE NEUN BELEGT, keine erfunden.** Ich habe jede gegen die Commits/Vorfälle
geprüft, die ich größtenteils selbst vermessen habe: §3 (67b22c76 + 8643d0ce, zweimal nachgetragen)
· Testdaten/Prozess (PROZESSPRUEFUNG-01, mein eigener Miss) · vorhanden+in Gebrauch (A-03-Messung
meiner Instanz; „timeout fehlt" von meiner Generator-Instanz unabhängig bestätigt) · Kommentar-Fall
(Evaluator-P1 + meine Gegenmessung Z.112/115 vs. Code) · 12.1 (0b3d6a10) · 12.2 (die von mir
gefundene Zwei-Zweige-Reparatur) · 12.3 (5,1s/20s-Rohausgaben) · 12.4 (A-01 fünf Grüne) ·
12.5 (das A-03-Votum meiner Instanz).

**P-01-4 Machtprüfung — KEINE unzulässige Verschiebung.** §12.5 entlastet den BAUENDEN, nicht den
Planner: der SPEC-Befund bleibt verbucht, erzeugt einen Pflicht-Folgeauftrag, wird in der Abnahme
ausdrücklich genannt und zählt in der §13-Statistik gegen den Planner. Der Verdacht war richtig
gestellt und hält der Prüfung nicht stand.

**P-01-5 Gabelung — 1.2.1 FÜHRT (Inhalt).** Messbare Gründe: `STATUS.md` trägt die vier laufenden
Aufträge real (AKTUELLER_AUFTRAG.yaml: 0 Verwendungen auf diesem Zweig); 1.3 führt §5 mit 15
Punkten — ihm fehlen die vier 1.1-Regeln, deren Vorfälle gemessen sind; ein Statusträger-Wechsel
mitten in vier Aufträgen kostet einen Übergangsplan ohne fachlichen Gewinn. **ABER 1.3 trägt drei
erntewürdige Stücke,** die als Nachtrag in unsere Linie gehören (Planner schneidet): (a) „Push =
Transport, nicht VERÖFFENTLICHT" — deckt sich mit gelebter Praxis und v1.2-Vertretung, (b)
Statuscommits mit genau EINEM Zustandswechsel, (c) der benannte Statusträger als Regel → A3.
**Die Zweig-Zusammenführung selbst (fork-Divergenz) ist Yamas Entscheidung — Topologie, nicht Text.**

**P-01-6 Kausalität — Verdacht BESTÄTIGT, aber enger als befürchtet.** Die vier 1.1-Regeln und
12.1/12.2 hätten ihre Vorfälle verhindert (JA). **12.3/12.4/12.5 sind kodifizierte Praxis** — sie
beschreiben, was die Rollen in derselben Nacht bereits richtig taten. Das macht sie nicht wertlos,
aber die §19-Überschrift „Der Fall" liest sich als „erzwungen durch". → **Auflage A4** (Ehrlichkeit
der Tabelle: „verhindert künftig" vs. „bestätigt durch Praxis" trennen).

**P-01-7 Plausibilität — der Mangel ist ECHT, am realen Fall belegt:** A-04-6 schreibt den (neuen)
Bühnen-Wächter als Pflichtschritt vor — nach Wortlaut „vorhanden UND in Gebrauch" wäre das Blatt
nie BEREIT-fähig. Der vorgeschlagene Halbsatz trägt. → **Auflage A2.** §12.4-Browserausnahme:
scharf genug (Entscheider ist der Evaluator am sichtbaren Verhalten); Anmerkung, keine Auflage.

**P-01-8 Konsistenz — zwei bestätigte Lücken:** `SPEC_BLOCKED`-Doppelbedeutung (→ A1) und der in
§16 unbenannte Statusträger (→ A3).

## Die vier Auflagen (Planner bessert nach, keine Umformulierung durch mich)

```text
A1  §3: die SPEC_BLOCKED-Definition um die Nach-Bau-Lage erweitern ODER eigener Zustand —
    eine Lage je Zustandsname (loest P-01-1 und P-01-8a zusammen)
A2  §5 „in Gebrauch": Halbsatz — gilt fuer VORHANDENE Formen; ein neu gebautes Werkzeug
    braucht stattdessen einen benannten Erstnutzer (loest P-01-7)
A3  §16: den Statustraeger NAMENTLICH benennen (STATUS.md, solange 1.2.1 fuehrt) und die
    1.3-Ernte als Nachtrag schneiden: Push=Transport-Begriff · Ein-Zustandswechsel-Statuscommits
A4  §19: „Der Fall"-Spalte trennt „haette verhindert" von „bestaetigt durch Praxis"
    (betrifft 12.3/12.4/12.5)
```

**Mit dieser Freigabe sind 1.1 und 1.2.1 verbindlich** (Yamas Weisung macht das Votum zum Akt).
Die Auflagen ändern daran nichts — sie sind Nachbesserungen am verbindlichen Text, keine
aufschiebende Bedingung. *Geprüft mit denselben Zähnen wie jedes Blatt; die Regeln haben
standgehalten, wo sie aus Messungen kamen, und gewackelt, wo sie Praxis beschrieben.*
