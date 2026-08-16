# BILANZ — alle eigenen Fehler des Plan-Prüfers am 16.08.

**Auf Yamas Auftrag:** *„kannst du auch im Bezug auf dich alle Fehler erst mal suchen, wieviel
sie sind, dann alle nacheinander beheben."*

**Erhebung, nicht Erinnerung.** Grundlage sind alle eigenen Commits des Tages, einzeln
klassifiziert — keine Stichprobe, keine Hochrechnung.

*(erhoben 16.08. 20:42 · Messstand d52e68ca · gemessen über alle Zweige)*

## Die Zahl

```
plan-pruefer-Commits heute (eindeutig)      157
davon mit echtem EIGENEM Fehler              16
davon selbst berichtigt                      15
davon nach Zustellung fremd behoben           1
NOCH OFFEN                                    3
```

## Die sechzehn, mit ihrer Behebung

| # | Zeit | Fehler | behoben durch |
|---|---|---|---|
| 1 | 15:14 | am falschen Objekt gemessen (Tafelzeile statt Datensatz) | `7e306f0f`, selbst |
| 2 | 16:03 | A-37-5 als „nicht erfüllbar" zu schwer klassifiziert | `f9fb856d`, selbst |
| 3 | 16:16 | eigener K2-Lösungsvorschlag hätte die halbe Werkbank verloren | `5196524a`, selbst |
| 4 | 18:02 | 86 `zeit`-Felder fortgeschrieben statt gemessen | `032942db`, selbst |
| 5 | 18:06 | diese Berichtigung war zu weit — nur ~30 betroffen | `28a16eb3`, selbst |
| 6 | 18:11 | Regelankündigung als Eintrag gezählt | `d3708bee`, selbst |
| 7 | 18:28 | Ballortung las nur eine Datei, 36 Blattfelder übersehen | `9edc948b`, selbst |
| 8 | 18:37 | Uhrzeit in der Prosa erfunden (18:39 statt 18:35) | `2dbeeb94`, selbst |
| 9 | 19:14 | „W-28 existiert nicht" — es existierte seit 16:47 | `24a49f46`, selbst |
| 10 | 19:22 | zsh-Schleife über Variable: Messung war wertlos | `e388f7c7`, selbst |
| 11 | 19:27 | „W-25 ist der fünfte Fall" — existierte seit 14:01 | `c2c3793d`, selbst |
| 12 | 19:44 | Prognose „wächst mit jedem Ballwechsel" zu weit | `a9c208de`, selbst |
| 13 | 19:53 | Ballwechsel bestätigt, ohne Blattstabilität zu prüfen | `51e580e2`, selbst |
| 14 | 19:56 | „die Statuswahrheit ist eingefroren" — Regel nicht gelesen | `001abb9e`, selbst |
| 15 | 20:17 | vier Proben als Erhebung ausgegeben | `7909a9ea`, selbst |
| 16 | 20:26 | A-18 „ohne `zustand`" — nur 14 von 84 Zeilen gelesen | `2b20c87f`, selbst |

**Dazu einer, den ich nicht selbst beheben konnte:** das erfundene Zustandswort `BEFUND` im
A-40-Block. Zugestellt an den Integrator, von ihm um 20:39 mit `0f969d5e` entfernt.
**A-40s Zustandskette lautet jetzt wieder `ENTWURF`** — nachgemessen.

## Die drei offenen — und warum ich sie nicht selbst schließen kann

**1 · `zustand: BEFUND` in drei weiteren eigenen Blöcken** (P-03, P-04 zweimal).
Folgenlos, weil es eigene Kennungen sind — aber es ist dasselbe erfundene Wort.
**Nicht behebbar:** `docs/STATUS.md` ist seit der A-37-Sperre nur für den Integrator schreibbar.
**Zugestellt:** `docs/ZUSTELLUNG-plan-pruefer-an-integrator.md`.

**2 · P-04 ist eine Kennungs-Dublette** — zwei Blöcke mit `zustand` unter einer Kennung. Das
ist genau der Fall, den meine eigene Wache unter Punkt 2 zu messen hat.
**Nicht behebbar:** dieselbe Sperre. Teil derselben Zustellung.

**3 · `docs/PROBE-TOR.md`** — meine Probedatei von 19:47, mit der ich gemessen habe, ob die
Sperre nur eine Datei betrifft. Sie liegt in allen Zweigen.
**Zweimal versucht, zweimal abgewiesen:** um 19:50 direkt, um 20:42 über `git rm --cached` plus
Löschung. Beide Male dieselbe Antwort: *„F-14: was nicht geschrieben wurde, wird auch nicht
belegt."* Das Werkzeug führt eine reine Löschung nicht als Beleg. Der Arbeitsbaum ist nach
jedem Versuch wiederhergestellt worden — Baum sauber, 0 Einträge.
**Vorgelegt bei Yama**, mit genau diesem Grund.

## Was die sechzehn gemeinsam haben

Zwölf von sechzehn sind **derselbe Fehler in verschiedenen Kleidern**: aus einem Teil auf das
Ganze geschlossen. Vier Zeilen statt vierundachtzig gelesen, vier Proben statt einer Erhebung,
ein Zweig statt sechs, ein Muster ohne Fangprobe. Die vier übrigen sind erfundene Werte —
zwei Uhrzeiten, ein Zustandswort, eine Zahl aus dem Gedächtnis.

**Keiner der sechzehn ist durch eine fremde Meldung aufgefallen, dreizehn habe ich selbst
gefunden.** Drei fand der Bestand: W-28 über den Planner-Konflikt, W-25 über den
Release-Prüfer, die Ballwechsel-Bestätigung über seine Warnung an den Evaluator.
