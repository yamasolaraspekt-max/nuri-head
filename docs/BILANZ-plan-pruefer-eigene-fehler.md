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

---

## Nachtrag: Fehler 17, gefangen vor der Meldung

*(erhoben 20:44, Messstand 4ad2d638)*

**Der Planner hat um 20:39 alle sieben offenen A-40-Befunde behoben** (`70994393`) und für den
siebten — die nicht reproduzierbaren Reichweiten — die **Ursache** gefunden: das Register
kennzeichnet widerlegte Formelzuordnungen als durchgestrichenes `F-001`, sein grep zählte sie
mit. *„Es waren drei verschiedene Fragen."*

**Beim Nachmessen habe ich zunächst widersprochen** — 1 Durchstreichung statt 2, 11 echte statt
10. **Das war mein Fehler.** Mein Muster `~~\s*F-001\s*~~` verlangte ein schließendes `~~`
unmittelbar nach der Kennung. In `W-04` steht aber:

```
~~F-001 ✓, F-002 ✓~~
```

— **eine Durchstreichung über zwei Formeln.** Mit korrekter Auswertung (F-001 *innerhalb* eines
`~~…~~`-Bereichs) ergibt sich **12 gesamt · 2 durchgestrichen · 10 echte** — zeichengenau seine
Zahlen.

**Das ist derselbe Fehlertyp wie die zwölf anderen:** ein Muster ohne Fangprobe, das die
naheliegende Form trifft und die zweite übersieht. Er ist gefangen worden, bevor er in eine
Meldung ging — durch das Öffnen aller zwölf Vorkommen im Kontext statt des Vertrauens auf die
Zahl.

**Damit steht die Bilanz bei 17 eigenen Fehlern**, sechzehn behoben, drei offen (die drei aus
dem Abschnitt oben; Fehler 17 ist mit dieser Feststellung erledigt).

**Und die drei Auflagen an A-40-5 sind eingetragen** — nachgemessen in Z.217 und 223:
je Kennung zählen, ein belegter Eintrag ohne Ampel wird nicht gemeldet, beide Sammlungen
getrennt ausweisen. **Keine Abschwächung, sondern drei zusätzliche Bedingungen.**

---

## Nachtrag: Fehler 18, wieder vor der Meldung gefangen

*(erhoben 20:56, Messstand 956f6cfc)*

`14cf28ca` (20:54) schreibt mir eine Zahl zu: *„A-38s Anlass trägt 59 von 497 und 58 von 70 —
der Plan-Prüfer misst in JEDEM Baum genau 5."*

**Ich habe nachgemessen und kam auf 348** — Merges ohne Rollenmarke, über alle fünf Rollenzweige,
je 348 bzw. 345. **Das sah nach einer Abweichung aus, ist aber keine.**

**Mein eigener Commit `3ff972b3` von 14:15 sagt wörtlich:** *„ohne Marke sind es überall genau
FÜNF, nie 58, also 8 bis 25 Prozent statt 83."* Er zitiert mich **richtig**.

**Der Unterschied liegt im Befehl, nicht in der Sache.** Damals bin ich A-38s eigenen Messbefehl
gefahren, der `git log` ohne `--all` benutzt und damit **der ersten Elternlinie** folgt — je
Worktree eine andere. Heute habe ich `--merges` über den ganzen Graphen gezählt. **Zwei
Befehle, zwei Fragen, zwei richtige Antworten.**

**Das ist derselbe Fehler, den ich damals selbst beschrieben habe:** *„der Befehl verlagert das
Problem vom fehlenden Befehl zum unbenannten ORT."* Ich wäre ihm heute beinahe selbst
aufgesessen — mit meiner eigenen Diagnose in der Hand.

**Gefangen durch:** die Frage „habe ich diese Zahl je genannt?" **vor** der Meldung, statt der
Annahme, eine fremde Zahl sei falsch, nur weil meine andere ist. Das ist die Umkehrung des
Musters, das mich heute sechzehnmal erwischt hat.

**Bilanz jetzt: 18 eigene Fehler.** Sechzehn behoben, Nummer 17 und 18 vor der Meldung gefangen,
drei aus dem Abschnitt oben weiterhin offen (BEFUND-Felder, P-04-Dublette, `PROBE-TOR.md`).

---

## Nachtrag: Fehler 19 — ich habe den Fehlalarm des Generators exakt reproduziert

*(erhoben 21:05, Messstand be7c3134)*

**Die Inventur des Generators nachgemessen** — neun Fehler in drei Klassen. Zwei seiner Zahlen
sind am Code prüfbar, beide geprüft:

| Zahl | seine Angabe | meine Messung |
|---|---|---|
| `analysiereAuswechslung` Aufrufer außerhalb Tests | **0** bei 11 Zusagen | **0** — die einzige Nennung ist die Definition selbst |
| `ZimmererFlags` Felder | **13** | **13** |

**Bei der zweiten habe ich zuerst 4 gemessen — genau seinen Fehlalarm.** Mein Regex
`^\s+([a-zA-Z]\w*)\??:` findet **eine** Flagge je Zeile. Sein Commit sagt den Grund:
*„mein Zähler meldete ZimmererFlags mit 4 statt 13 Flaggen — er zählte ZEILEN, und dort stehen
mehrere Flaggen je Zeile."* Mit allen Vorkommen gezählt sind es 13:
`sparren firstpfette mittelpfette fusspfette kehlbalken stuhlsaeule strebeKopfband zange
aufschiebling gratsparren kehlsparren schifter wechsel`.

**Gefangen durch: seinen Grund lesen, bevor ich meine Zahl für richtig halte.** Hätte ich die 4
gemeldet, wäre es ein Fehlbefund gegen ein korrektes Blatt gewesen — mit derselben Ursache, die
er zwei Zeilen darüber selbst benennt.

**Das ist heute das vierte Mal, dass ich denselben Griff mache wie der Gemessene:**
bei den A-40-Kanten, bei A-39s Nenner, bei `ballrueckgabe.py` und hier. **Jedes Mal stand die
Auflösung im Text, den ich schon vor mir hatte.** Seine eigene Lehre trifft es genau:
*„eine Zahl, die dem widerspricht, was man weiß, ist zuerst ein Verdacht gegen die Messung."*

**Bilanz jetzt: 19 eigene Fehler.** Sechzehn behoben, drei (17, 18, 19) vor der Meldung
gefangen, drei aus dem Abschnitt oben weiterhin offen.

---

## Nachtrag 19.08. — diese Bilanz stand 64 Stunden still, und neun Fehler stehen außerhalb von ihr

*(erhoben 19.08. 13:2x · Messstand `cba422dd` · Anlass: §112, Posten (d) Alterung)*

**Diese Datei wurde zuletzt von `32b8bcee` (16.08. 21:05) geschrieben und endet bei Fehler 19.**
Seither sind neun weitere eigene Fehler vergeben, benannt und committet — **keiner davon steht
hier:**

| Fehler | Commit | Zeit | verzeichnet in |
|---|---|---|---|
| 21 | `d771e71d` | 16.08. 22:48 | Befund-Blatt |
| 22 | `94c98ad0` | 16.08. 21:46 | Befund-Blatt |
| 23 | `761b7e96` | 16.08. 22:21 | Befund-Blatt |
| 24 | `761b7e96` | 16.08. 22:21 | Befund-Blatt |
| 25 | `1bfdbd0f` | 16.08. 23:53 | Befund-Blatt |
| 26 | `1d386676` | 17.08. 00:32 | Befund-Blatt |
| 27 | `cdd80e81` | 17.08. 02:02 | Befund-Blatt |
| 28 | `4f6b65b1` | 17.08. 02:23 | Befund-Blatt |
| 29 | `23cd7fdc` | 17.08. 01:16 | Befund-Blatt |

**Nummer 20 findet sich in keinem Commit und in keinem Blatt** — eine Lücke in meiner eigenen
Nummernvergabe. Sie wird hier gemeldet, nicht aufgefüllt; eine Nummer nachträglich zu vergeben
hieße, einen Fehler zu erfinden oder einen echten zu verstecken.

**Zahl heute: 19 verbucht, mindestens 28 tatsächlich.** Die neun Einträge werden **nicht**
hierher kopiert — sie stehen in `docs/BEFUND-plan-pruefer-rueckweg-und-tor.md` an der Stelle, an
der sie entstanden, mit dem Stand, gegen den sie gemessen wurden. Rückdatieren würde genau den
Anker zerstören, dessen Fehlen unten als Fehler 30 steht.

## Fehler 30 — der erklärte Messstand trug keine einzige der veröffentlichten Zahlen

*(gefunden in §112, gegen `cba422dd`)*

Die Alterungsrunde vom 16.08. erklärt: *„geschrieben 21:08, Messstand `32b8bcee`"* und
veröffentlicht sechs Commit-Zahlen. **Keine davon ist an `32b8bcee` gemessen.** Alle sechs treffen
`bea33236` (16.08. 21:29), 36 Commits und 24 Minuten weiter:

```
A-37 741/777·777   A-38 703/739·739   A-39 601/637·637
A-40 601/637·637   A-42 529/565·565   W-21L 1837/1873·1873
                   (@32b8bcee / @bea33236 · veroeffentlicht)
```

**Klasse:** richtige Zahl, falscher Anker — das Spiegelbild der Befunde §109–§111, wo der Anker
stimmte und die Zahl alterte. **Wirkung:** wer die Runde nachrechnet, findet 741 statt 777 und
hält meine Messung für falsch. Die Angabe, die den Beweis führbar machen soll, macht ihn
unführbar. **Genau das werfe ich seit §77 den Blättern vor.**

**Gefangen durch:** die gleichmäßige Differenz. Alle sechs Zahlen wuchsen um exakt 244, während
`32b8bcee..HEAD` 280 ergibt — zwei Gleichmaße, die sich widersprechen, und der Widerspruch war
der Zeiger auf den wahren Stand.

**Behoben:** nein, und nicht behebbar — die Runde ist geschrieben und zitiert. **Berichtigt:** hier
und in §112. **Vorbeugung ab sofort:** der Messstand wird aus derselben Ausführung genommen wie
die Zahlen (`H=$(git rev-parse --short HEAD)` einmal, dann alle Zählungen gegen `$H`), nicht
vorher notiert und nachher gemessen.

**Bilanz jetzt: 30 vergeben, 19 hier verbucht, eine Nummer (20) unbelegt.**
