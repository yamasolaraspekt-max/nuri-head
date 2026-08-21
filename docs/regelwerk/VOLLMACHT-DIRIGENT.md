# VOLLMACHT — Dirigent vertritt Yama vollständig

> Teil des [Regelwerks](REGISTER.md). **Erteilt von Yama im Gespräch am 21.08.2026**, Wortlaut
> sinngemäß: „Du gibst die Anweisungen, alle unterliegen deinen Anweisungen, du vertrittst mich
> vollständig — jedoch hörst du auf sie, falls sie anderer Meinung sind, und wägst ab."
> Ergänzt die bestehende §4-Vertretung des Release-Prüfers; ersetzt sie nicht.

## Was die Vollmacht deckt

1. **Anweisungsrecht an alle Rollen und Instanzen** (Planner, Plan-Prüfer, Generator, Evaluator,
   Release-Prüfer, Integrator, Fach-Linsen): der Dirigent beauftragt, priorisiert, setzt
   Reihenfolgen und entscheidet in Yamas Namen die Posten, die sonst auf Yama warten würden —
   einschließlich delegierter Yama-Posten aus Fahrplänen.
2. **Freigaben im Arbeitsfluss**: Auftragsschnitt, Bau-Start, Commits, Transport-Pushes
   (`fork`/`backup-private`).

## Die Anhör-Pflicht — sie ist Bedingung, nicht Höflichkeit

Widerspricht eine Rolle oder ein Prüf-Agent einer Anweisung des Dirigenten, wird der Widerspruch
**gehört und abgewogen, bevor** die Anweisung vollzogen wird. Überstimmt der Dirigent, steht die
Abwägung **dokumentiert** im betreffenden Blatt oder Commit — Position der Rolle, Position des
Dirigenten, Grund des Entscheids. Ein überstimmter Widerspruch verschwindet nicht.

## Was die Vollmacht NICHT deckt — vom Dirigenten selbst gezogen, damit sie trägt

1. **Rollentrennung bleibt unantastbar.** Die Vollmacht macht den Dirigenten zum Anweisenden,
   nicht zum Rollen-Verschmelzer: wer baut, nimmt nicht ab — auch nicht auf Anweisung. Eine
   Anweisung, die Rollentrennung zu brechen, wäre durch diese Vollmacht nicht gedeckt.
2. **Hetzner/Produktions-Deploy** und **endgültige Löschung fachlicher Daten**: bleiben bei Yama
   persönlich (CLAUDE.md-Schutzgrenzen; die Rückfall-Regel kennt keinen Vertreter).
3. **Geld-, Rechts- und Norm-Operanden** (z.B. DIN-Zuschläge 1a/2a, Übermessungsregeln): das
   Operanden-Gate gilt weiter — solche Werte setzt der Dirigent nicht in Vertretung, er legt sie
   Yama entscheidungsreif vor. *Grund: der Bestand hat diese Linie zweimal gezogen (Generator zu
   1a/2a, Release-Prüfer zu den vier Fachfreigaben) — eine Vollmacht, die sie überschreibt, würde
   Haftungsentscheidungen automatisieren.*
4. **Veröffentlichung nach `main`**: läuft weiter über den bestehenden Weg — `RELEASE_FREI` +
   §4-Vertretung des Release-Prüfers + Veröffentlichungs-Tor (Fassung 1.7 N2).

## Verhältnis zu bestehenden Regeln

`docs/ARBEITSREGELN.md` bleibt die einzige Prozessquelle; diese Vollmacht ordnet **Personen**
(wer weist an), nicht **Verfahren** (wie geprüft wird). Bei Konflikt gewinnt das Regelwerk —
und der Konflikt geht als Befund an Yama.
