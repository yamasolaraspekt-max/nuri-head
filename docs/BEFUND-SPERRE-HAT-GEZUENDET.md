# Die Sperre hat gezündet — und der einzige Schreiber ist nicht freigegeben

> **Release-Prüfer, 16.08. ~19:5x. Dringend, und ich habe es ausgelöst.**
> Der Plan-Prüfer meldet es zeitgleich: *„die Statuswahrheit ist eingefroren, und der einzige
> berechtigte Schreiber hat sie nie geschrieben."*

## Der Zustand, gemessen

```
Tor im Baum vorhanden:
  ticket · ticket-rolle-planner · ticket-rolle-plan-pruefer
  ticket-rolle-generator · ticket-rolle-evaluator · ticket-release-pruefung
                                                          -> 6 von 6

docs/STATUS.md schreiben darf:
  release-pruefer  GESPERRT      generator   GESPERRT
  planner          GESPERRT      evaluator   GESPERRT
  plan-pruefer     GESPERRT      integrator  frei

Integrator auf SCHREIBEND (Schritt J):  NICHT erteilt
```

**Fünf Rollen dürfen nicht mehr schreiben, und die sechste darf noch nicht.** Die Statuswahrheit hat
formal genau einen Schreiber und faktisch keinen.

## Wie es dazu kam — meine Handlung, nicht mein Fehler

Die Sperre war seit 16:52 **selbst-konditioniert**: der Generator hatte sie so gebaut, dass sie erst
zündet, *„sobald der Transport das Tor überall hingebracht hat (A-37-18)"*. Das war die richtige
Konstruktion — sie verhinderte die Ungleichbehandlung, die ich um 16:46 gemeldet hatte.

**Der Rückweg von 19:3x hat genau diese Bedingung erfüllt.** Ich habe das Tor in die letzten beiden
Bäume gebracht, um P-07 zu beheben (*„94 Commits erreichen den Planner nicht"*). Das war richtig und
nötig — der Plan-Prüfer bestätigt im selben Atemzug: *„der Rückweg ist offen."*

**Nur hing an derselben Handlung ein zweiter Schalter.** A-37-18 zu erfüllen heißt, die Sperre scharf
zu machen. Beides war dieselbe Bewegung, und ich habe sie als eine gesehen.

**Das ist keine Reue, sondern eine Warnung für die nächste Runde:** eine selbst-konditionierte
Barriere zündet dann, wenn jemand *anderes* ihre Bedingung erfüllt — und der weiß es womöglich
nicht. Der Generator hat die Zündbedingung sauber dokumentiert; ich habe sie beim Rückweg nicht
mitgelesen.

## Was jetzt blockiert ist — und was nicht

**Nicht blockiert:** Transport, Merges, Blätter, Werkzeuge, jede Arbeit außerhalb von
`docs/STATUS.md`. Merge-Commits laufen nicht über `commit-pruefen.sh` und damit nicht durch das Tor.

**Blockiert:** jeder Zustandswechsel im Datensatz. Konkret liegen an:

```
A-37   Log CODE_FERTIG (19:38, Generator, korrekter Wortlaut)
       Datensatz BEREIT — der Nachzug ist gesperrt
```

**Mich trifft es beim nächsten `ABGENOMMEN`:** ein Release-Vermerk nach §10 geht in
`docs/STATUS.md`. Heute liegt keiner an — A-41 und W-17/1 sind durch. Beim nächsten steht mein Takt.

## Was das für Schritt J bedeutet

Um 17:1x habe ich Schritt J **nicht erteilt**, weil V6 unerfüllt war: *„Eigener Rollen- und
Checkoutschutz aktiv — positive **und** negative Sperrfälle bestanden."* Der Stand hat sich zur
Hälfte gedreht:

```
V6 Teil 1  Schutz AKTIV                     jetzt ERFUELLT  (6 von 6, zuendet nachweislich)
V6 Teil 2  Sperrfaelle unabhaengig bestanden   weiter OFFEN  (Schritt I, Evaluator)
```

**Der Grund, aus dem ich damals abgelehnt habe, gilt zur Hälfte weiter** — und zur anderen Hälfte
hat sich die Lage genau in die Richtung bewegt, die ich als Weg A empfohlen hatte.

## Was ich Yama vorlege — drei Wege, und ich empfehle einen

**Weg 1 — Schritt I jetzt, dann J.** Der Evaluator prüft die positiven und negativen Sperrfälle; er
hat das Tor im Baum, A-37 steht auf `CODE_FERTIG` und liegt bei ihm. Danach ist V6 vollständig und
Schritt J trägt. *Kosten: eine Prüfrunde.*

> **NACHTRAG 20:4x (F6) — „eingefroren" gilt so nicht mehr.** Hier stand, die Statuswahrheit
> bleibe bis Schritt J eingefroren. Gemessen: der **Integrator hat um 20:16 geschrieben**
> (`15e11078`, A-37 von `BEREIT` auf `CODE_FERTIG` nachgezogen) — die Sperre laesst ihn durch,
> weil sie die Rolle prueft und nicht die Betriebsart. Eingefroren sind die **fuenf anderen**
> Rollen, nicht die Datei. Siehe `BEFUND-BARRIERE-KENNT-DIE-BETRIEBSART-NICHT.md`.

**Weg 2 — Schritt J sofort, Schritt I nachziehen.** Löst die Blockade in einer Minute, kehrt aber
die Reihenfolge um, die deine Neufassung vom 14.08. ausdrücklich begründet: *„er schreibt erst,
nachdem seine Barriere fremd geprüft ist."*

**Weg 3 — die Zündung zurücknehmen.** Technisch möglich, praktisch falsch: das Tor aus einem Baum
zu entfernen, um eine Sperre zu entschärfen, ist genau die Umgehung, gegen die sie gebaut ist. **Ich
schlage ihn nur vor, um ihn ausdrücklich abzuraten.**

**Meine Empfehlung: Weg 1**, und er ist heute kurz. Der Evaluator ist auf Stand, hat das Tor und
keinen anderen Ball. Die Blockade kostet bis dahin nichts, weil kein `ABGENOMMEN` wartet — das ist
messbar, nicht gehofft.

**Was ich nicht entscheide:** Schritt J. Er steht in der Tabelle bei dir, und er ist eine
Entscheidung über Vollmacht — die vertrete ich nicht in deinem Namen, auch nicht unter Zeitdruck und
auch nicht, wenn meine eigene Handlung den Druck erzeugt hat.
