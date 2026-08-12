# Deine Antwort war inhaltlich stark — ihr fehlte die Reichweite

> **Auf deine Frage vom 12.08.:** *„kann sein dass meine vorherige antwort in gänzlich nicht optimal
> war, meinst du könnte man das verbessern wenn tue es damit der workflow ergebnis hier besser wird"*

**Kurz: Der Inhalt war nicht das Problem — er war das Beste an ihr.** Verbesserbar ist genau eine
Sache, und sie hat heute messbar Arbeit gekostet.

## Was stark war, damit die Kritik nicht das Falsche trifft

```text
AM CODE BELEGT      fuenf Fundstellen statt einer Meinung. Vier davon habe ich
                    nachgemessen und sie stimmen zeichengenau.
DIE FRAGE BERICHTIGT statt sie nur zu beantworten: "4 + 3 muss nicht 8 ergeben,
                    weil die vier und die drei nicht auf DERSELBEN Achse liegen."
                    Die Frage war falsch gestellt, und das zu sagen ist mehr wert
                    als eine Antwort auf die falsche Frage.
ENTSCHIEDEN         kein "man koennte" — review-required = checked, confirmed =
                    approved, blocked ist die einzige Erweiterung.
ZWEI AUFLAGEN       blockiert_durch verpflichtend, blocked nie von Hand. Beide
                    verhindern einen konkreten Folgefehler.
```

## Was fehlte — und was es gekostet hat

**Die Antwort sagt, was gilt. Sie sagt nicht, was dadurch ungültig wird.**

Gemessen, fünf Commits über drei Rollen:

```text
ea418041  generator      "Yamas Antwort ueberholt DREIZEHN Stellen in W-40s
                          Blaettern — die Liste, damit sie niemand sucht"
4c7ba68b  planner        W-40/1 geschnitten
d39f14de  planner        "mein Kriterium nannte EINE ueberholte Stelle,
                          es sind DREIZEHN"           <- der teure Punkt
2bd433c0  plan-pruefer   DoR
65e21b01  plan-pruefer   Nachtrag zur eigenen Freigabe
```

**Der Planner hat den Nachbesserungsauftrag falsch geschnitten**, weil er die Reichweite nicht
kannte — er nannte eine überholte Stelle, es waren dreizehn. Der Generator musste sie suchen. Das
ist keine Nachlässigkeit von beiden: **niemand konnte wissen, wie weit die Entscheidung reicht,
weil die Entscheidung es nicht sagte.**

## Die Verbesserung — drei Zeilen, nicht mehr

```text
1  WAS GILT          hattest du. Unveraendert.

2  WAS DADURCH       fehlte. Zwei zulaessige Formen:
   UEBERHOLT IST       a) die Liste, wenn du sie kennst
                       b) "Reichweite NICHT gemessen — wer nachzieht, misst sie
                          zuerst und meldet die Zahl"
                     Form b) ist vollwertig. Sie kostet dich nichts und sagt der
                     naechsten Rolle, dass Suchen zum Auftrag gehoert.

3  WER ZIEHT NACH    fehlte. Ohne das schneidet der Planner nach Gefuehl.
```

**Warum Form b) genügt:** Du musst die dreizehn Stellen nicht selbst finden. Der Satz *„Reichweite
nicht gemessen"* hätte gereicht — dann hätte der Planner sein Kriterium als *„alle überholten
Stellen, Zahl beim Bau zu messen"* geschnitten statt als *„eine Stelle"*, und die Berichtigung
`d39f14de` wäre nicht nötig gewesen.

## Deine Antwort in der verbesserten Form — so hätte sie ausgesehen

*Unverändert im Inhalt, ergänzt um die drei Zeilen:*

```text
ENTSCHEIDUNG   review-required = checked · confirmed = approved · outdated = outdated
               blocked ist die EINZIGE Erweiterung (0 Treffer)
               -> W-40 ist eine ABLESUNG MIT EINER ERWEITERUNG
               -> die Gueltigkeitsachse haengt am PAKET, nicht am Schritt

REICHWEITE     Nicht gemessen. W-40s sieben Blaetter sind an jeder Stelle betroffen,
               die "die drei Stufen fehlen" oder die Achse an den SCHRITT haengt.
               Wer nachzieht, misst die Zahl zuerst und meldet sie.

NACHZUG        Planner: Nachbesserungsauftrag nach §12 schneiden.
               Das Kriterium lautet "ALLE ueberholten Stellen", nicht "die eine".
```

## Ein Nebenbefund zu deinen Belegen

```text
fahrschritte.ts:43-49        Zeile 43 ist exakt richtig.
                             Der Pfad ist app/dashboard/fahrschritte.ts —
                             das dashboard-Glied fehlt.

STATUS_UEBERGAENGE :101-110  beginnt bei :103. Inhalt stimmt.
```

**Das ist heute das zweite fehlende Pfadglied** — der Generator hatte dasselbe bei
`app/state/paketSpeichern.ts`, und seine erste Messung lief ins Leere, bis er suchte statt zu
glauben. Kein Fehler der Sache, aber jedes Mal ein verlorener Griff bei dem, der nachschlägt.

## Und was ich an MEINER Seite geändert habe

Deine Frage gilt dem Ablauf, nicht nur der Antwort — deshalb der Teil, der mich betrifft:

**Zwei meiner Release-Vermerke sind heute als stiller Beifang in fremden Commits gelandet** (W-41 in
`0474f53b`, W-42 in `65e21b01`). Beim zweiten Mal trotz der Gegenmaßnahme, die ich nach dem ersten
gezogen hatte: mein Commit scheiterte an einer Ref-Sperre, weil eine andere Rolle im selben
Augenblick schrieb.

```text
URSACHE   fuenf Rollen, EIN Arbeitsbaum, EIN git-Index, EINE Datei.
          Ein Commit kann dort scheitern, ohne dass jemand etwas falsch macht,
          und jede gescheiterte Sperre oeffnet ein Fenster.

BEHOBEN   Ich schreibe Release-Vermerke ab jetzt im EIGENEN Worktree
          (~/Documents/ticket-release-pruefung), pushe von dort und ziehe den
          Hauptbaum per fast-forward nach. Diese Datei ist so entstanden.
          Meine eigene Taktregel sagt das seit jeher unter Punkt 2 — ich hatte
          sie auf Pruefstaende bezogen und nicht auf die Statuswahrheit.
          Die Unterscheidung war falsch: sie ist die Datei mit den MEISTEN
          Schreibern.
```

**Der Vorschlag daraus, für alle Rollen — aber das ist deine Entscheidung, nicht meine:** wer in
`docs/STATUS.md` schreibt, tut es aus seinem eigenen Worktree. Es kostet einen `git fetch` und
schließt das Fenster ganz, statt es zu verkleinern.
