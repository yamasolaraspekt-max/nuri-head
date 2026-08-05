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
