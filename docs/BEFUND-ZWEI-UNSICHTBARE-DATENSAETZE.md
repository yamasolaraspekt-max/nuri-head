# Zwei Datensätze sind für jedes blockbasierte Werkzeug abwesend — und alle drei Prüfungen melden „still"

> **Release-Prüfer, 16.08. ~23:1x.** Auf `6cab51a0`. Gefunden über einen Nebenweg, nicht gesucht.
> **Heute ohne Schaden — aber der Mechanismus trifft A-42 unmittelbar.**

## Der Nebenweg, über den es auffiel

Beim Zählen der Zustände fiel eine Differenz von **eins** auf:

```
zustand:-Zeilen ueber grep            90
zustand:-Werte in YAML-Bloecken       89
```

Eine Zeile Unterschied. Dahinter liegt der Datensatz **A-08** (`docs/STATUS.md` Z.3215–3256):
sein `​```yaml` hat **keinen schließenden Zaun**, der nächste `​```yaml` bei Z.3256 verwirft ihn.
**Für jedes Werkzeug, das über Blöcke geht, existiert dieser Datensatz nicht.**

## Warum keine der drei Prüfungen anschlägt

```
A  Zaunbilanz ueber Zeilenanfaenge    1160 · gerade        -> still
B  Zaun mitten in einer Zeile           10 (Grundlinie 10) -> still
C  Bloecke 442 · parsen 418 · kaputt 24 (Grundlinie 24)    -> still
```

**Jede ist für sich richtig, und genau deshalb greift keine:**

- **A** prüft die *Gesamtzahl* aller Zaunzeilen auf Geradheit. Der fehlende Schließer wird von den
  Schließern der `​```bash`-Blöcke rechnerisch ausgeglichen — 1160 bleibt gerade.
- **B** sucht Zäune *mitten in einer Zeile*. Hier steht der Zaun sauber am Zeilenanfang.
- **C** parst, was das Muster `/​```yaml\n([\s\S]*?)​```/` findet. **Ein Block ohne Schließer wird von
  diesem Muster nicht gefunden** — er kann also nicht als „kaputt" gezählt werden.

```
```yaml-Oeffner in der Datei        444
Bloecke, die C ueberhaupt sieht     442
Differenz                             2
```

**Ein Block ohne schließenden Zaun ist nicht KAPUTT, er ist ABWESEND.** Das ist der Unterschied, den
die Werkzeugkette bisher nicht kannte.

## Die zwei Fälle, einzeln geöffnet

```
Z.3215-3256   A-08    zustand: BETRIEBSBESTAETIGT · ballbesitz: —  (Kette vollstaendig)
Z.7876-7890   (ohne Kennung) — ein Vorschlagsblock, kein Datensatz, kein Ballfeld
```

**Heute kostet es nichts:** A-08 ist durch, der andere trägt keinen Ball. **Aber derselbe
Mechanismus hätte einen offenen Ball verschluckt**, ohne dass eine Prüfung angeschlagen hätte.

## Prüfung D, nachgerüstet und gegengeprobt

`scripts/bloecke.py` prüft jetzt viertens, ob jeder Öffner einen Schließer hat — mit Grundlinie 2
wie die anderen Prüfungen, und mit Nennung der Zeilenbereiche.

**Gegenprobe an einem künstlich zerstörten Bestand** (Schließer eines intakten yaml-Blocks entfernt):

```
A  Zaunbilanz 1159 · UNGERADE — ABBRUCH
C  Bloecke 441 · parsen 416 · kaputt 25 (Grundlinie 24, +1)
D  Oeffner ohne Schliesser 3 (Grundlinie 2, +1)
     Z.1130 bis Z.1170 · Z.3214 bis Z.3255 · Z.7875 bis Z.7889
   ⚠ 1 MEHR als der Altbestand — ein Datensatz koennte unsichtbar geworden sein.
Exit 1
```

**Der Unterschied zum echten Fall ist der Beweis:** beim künstlichen Schaden kippt auch A auf
ungerade. **Bei A-08 nicht** — dort gleichen andere Zäune die Bilanz aus, und deshalb lag der Fall
seit Tagen unbemerkt. **D ist die einzige der vier, die ihn fängt.**

**Eine erste Gegenprobe schlug fehl und das war meine Schuld:** ich hatte irgendeinen `​```` `
entfernt statt den eines yaml-Blocks — D meldete unverändert 2, was richtig war. Erst der gezielte
Eingriff prüfte, was ich prüfen wollte.

## Warum das A-42 betrifft

A-42 verschiebt 168 Blöcke aus `docs/STATUS.md` nach `docs/BEFUNDNOTIZEN.md`. **Ein Umzug schneidet
Blöcke an Zäunen aus und setzt sie woanders wieder ein** — die Operation, bei der ein fehlender
Schließer entsteht oder sich fortpflanzt.

```
A-42-2   Summengleichung: Bloecke vorher == Bloecke nachher
```

**Diese Gleichung zählt Blöcke, die ein Muster findet.** Verliert ein Block beim Umzug seinen
Schließer, verschwindet er aus *beiden* Seiten der Gleichung — sie bleibt erfüllt, und der Datensatz
ist weg. **Genau der Fall, den A-08 heute passiv vorführt.**

Das ist **kein** neues Kriterium, das ich fordere — A-42-2 ist richtig. Es ist ein Hinweis, dass die
Gleichung mit Prüfung D gefahren werden sollte, nicht ohne: `bloecke.py` läuft nach dem Umzug über
**beide** Dateien, und D muss auf beiden 0 melden.

## Rollengrenze

`scripts/bloecke.py` ist mein Messwerkzeug — die Erweiterung ist Messarbeit, kein Bau am Bestand.
**An `docs/STATUS.md` habe ich nichts geändert:** die zwei fehlenden Zäune stehen in einer Datei, die
seit 19:36 nur der Integrator schreiben darf, und ein Zaun im Datensatz ist keine Kleinigkeit — wer
ihn setzt, entscheidet, wo der Block endet.
