# ZUSTELLUNG — plan-pruefer an integrator · 39 Bälle abgeben

**Auf Yamas Vorhalt:** *„behebe deinen Fehler — der Plan-Prüfer hat 39. Das ist der größte
Stapel nach meinem."*

**Der Vorhalt trifft.** Gemessen in `docs/STATUS.md`: **39 Blöcke tragen
`ballbesitz: plan-pruefer`.** Keiner davon wartet auf Arbeit von mir.

*(zugestellt 16.08. 21:33 · Messstand 6fa1b897 · gemessen gegen auto/hausplaner-integration)*
**Ballbesitz für alle folgenden Punkte: integrator** — er ist seit der A-37-Sperre der einzige,
der `docs/STATUS.md` schreiben darf.

---

## Die Ursache, und sie ist ein Muster über den ganzen Tag

Ich habe `ballbesitz: plan-pruefer` in jeden Befundblock geschrieben, den ich verfasst habe —
als Angabe **„ich habe das gemessen"**. Das Feld bedeutet aber **„hier liegt Arbeit"**.

**Das ist H-9: ein Wort, zwei Bedeutungen** — dieselbe Klasse, die ich heute mehrfach bei
anderen gemeldet habe. Die Folge ist ein Stapel von 39 Bällen, von denen **keiner** auf eine
Handlung von mir wartet. Wer die Ballortung fährt, findet 39 offene Posten bei einer Rolle, die
nichts liegen hat.

---

## Gruppe 1 · Acht Blöcke, deren Auftrag abgeschlossen ist — Ball ersatzlos weg

**A-41 ist BETRIEBSBESTAETIGT.** Acht Blöcke tragen meine DoR- und Kantenprüfungen dazu:
FUND 1 bestätigt · DoR Teil 3 · DoR Teil 4 · beide roten Punkte behoben · A-41-11 hält ·
K1 belegt · A-41-5 erfüllt · K2-Befund behoben.

**Alle acht sind erledigte Prüfarbeit an einem abgenommenen Auftrag.**
**Soll:** `ballbesitz` auf `—` setzen, wie es der Integrator bei A-18 bereits getan hat
(`ballbesitz: —  # Kette vollstaendig`).

---

## Gruppe 2 · Zwanzig Blöcke zu laufenden Aufträgen — Ball an den jeweiligen Halter

| Kennung | Blöcke | Auftrag steht auf | Ball gehört an |
|---|---|---|---|
| A-37 | 2 | CODE_FERTIG | **evaluator** (Abnahme läuft, Schritt I bestanden) |
| A-38 | 4 | ENTWURF | **planner** (DoR gefahren, Befunde zugestellt) |
| A-39 | 4 | ENTWURF | **planner** (alle Kriterien geprüft, sieben Befunde abgearbeitet) |
| A-40 | 6 | ENTWURF | **planner** (elf Befunde, davon zehn behoben) |
| A-42 | 3 | ENTWURF | **planner** (DoR gefahren, drei Befunde aufgenommen) |
| BERICHTIGUNG-W-17-1 | 1 | — | **—** (Berichtigung eines eigenen Messfehlers, erledigt) |

**In allen sechs Fällen ist meine Prüfung gefahren und das Ergebnis zugestellt.** Nichts davon
wartet auf mich.

---

## Gruppe 3 · Elf Blöcke, die mir tatsächlich gehören — aber nichts von mir verlangen

| Kennung | Blöcke | Art | Soll |
|---|---|---|---|
| P-02 | 4 | VORLAGE bei Yama | Ball auf **yama**, nicht auf mich |
| P-03 · P-04 · P-05 · P-08 | 5 | eigene Selbstbefunde, alle behoben oder zurückgenommen | Ball auf **—** |
| SELBSTBERICHTIGUNG-ZEITSTEMPEL 1+2 | 2 | eigene Berichtigungen, erledigt | Ball auf **—** |

**P-03 bis P-08 tragen zusätzlich das erfundene Wort `BEFUND` im `zustand`-Feld** — das ist
meine bereits am 20:41 zugestellte Sache, hier nur zur Vollständigkeit wiederholt: drei Blöcke
(P-03, P-04 zweimal), und P-04 ist dadurch eine Kennungs-Dublette.

---

## Zusammenfassung für den Integrator

```
8   A-41-Bloecke              -> ballbesitz: —
2   A-37-Bloecke              -> ballbesitz: evaluator
17  A-38/39/40/42-Bloecke     -> ballbesitz: planner
4   P-02-Bloecke              -> ballbesitz: yama
8   eigene Befunde/Berichtigungen -> ballbesitz: —
--
39
```

**Was ich ausdrücklich nicht verlange:** eine Zustandsänderung an irgendeinem Auftrag. Es geht
allein um das Feld `ballbesitz`, das ich neununddreißigmal falsch gesetzt habe.

**Und für meine eigene Wache:** Ab sofort trägt ein Befundblock von mir den Ball des
**Empfängers**, nicht meinen. Meine Urheberschaft steht im Feld `rolle:` — dafür brauche ich
den Ballbesitz nicht.

---

## Nachweis — jede Zahl oben gegengeprüft (16.08. 21:5x · Stand 95980a2b)

**Die 39.** Zwei Muster, weil eines davon eine Zeile verfehlt:

```
grep -c '^ballbesitz: plan-pruefer$'  ->  38     (strikt)
grep -c '^ballbesitz: plan-pruefer'   ->  39     (mit Nachtext)
```

Die Differenz ist **Z.18694**, eine Ballzeile mit angehängter Begründung
(`# 16.08. vom Planner zurueckgegeben … VORHER: ballbesitz: planner`). Sie zählt mit —
**39 ist richtig.** Zur Warnung für den nächsten, der das nachzählt: `ballbesitz.*plan-pruefer`
liefert **80** und ist falsch, weil es Blöcke mitfängt, in deren *Kommentar* die Rolle erwähnt
wird, ohne dass der Ball bei ihr liegt.

**Die Aufteilung.** Über das Feld `auftrag:` — nicht `kennung:`, das es in dieser Datei nicht gibt:

```
8 A-41 · 6 A-40 · 4 P-02 · 4 A-39 · 4 A-38 · 3 A-42 · 2 P-04 · 2 A-37
1 je: P-03 · P-05 · P-08 · BERICHTIGUNG-W-17-1-TAFELZEILE
     · SELBSTBERICHTIGUNG-ZEITSTEMPEL · SELBSTBERICHTIGUNG-ZEITSTEMPEL-2
SUMME 39      davon 8 mit zustand-Feld, 31 ohne
```

**Die sieben Auftragszustände**, auf die sich die Ball-Zuordnung stützt — alle frisch gelesen:

| Auftrag | Zustand | Beleg |
|---|---|---|
| A-37 | CODE_FERTIG | Z.18514 |
| A-38 · A-39 · A-40 · A-42 | ENTWURF | je eigener Block |
| A-41 | BETRIEBSBESTAETIGT | Z.23389 (Release-Prüfer, §10+19) |
| P-02 | VORLAGE | eigener Block |

**Ein Messfehler im Prüfen selbst, gefangen und hier offengelegt:** Meine erste Gegenprobe für
A-37 war `grep -A1 '^auftrag: "A-37"'` und kam **leer** zurück. Das ist keine Bestätigung und
kein Widerspruch, sondern eine ausgefallene Messung — die Zustandszeile steht 21 Zeilen nach der
Auftragszeile, nicht direkt darunter. Nachgeholt über einen Blockscanner. **Keine Zahl der
Zustellung hat sich dadurch geändert.**
