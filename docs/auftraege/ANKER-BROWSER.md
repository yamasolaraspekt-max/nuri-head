# ANKER-BROWSER — der Browser-Anker, EINMAL

**Die eine Quelle für `L-01-anker`. Kein Blatt schreibt ihn mehr aus.**

*Angelegt vom Generator, 03.08.2026, als Teil von W-08. Bis dahin stand er ausgeschrieben in
**16** Blättern — und war an einem Tag **zweimal** falsch. Beide Korrekturen kosteten je sechs
Ersetzungen mit Zeilennummern, und beide erreichten nur die Hälfte der Träger: zwölf Blätter
behielten die alte Fassung, weil sie `ruht`, `abgenommen` oder ohne Kopf waren. **Zwölf
Splice-Gelegenheiten für zwei Erkenntnisse** (F-19).*

---

## So steht er künftig im Blatt — drei Zeilen

```yaml
  - id: L-01-anker
    typ: verweis
    quelle: docs/auftraege/ANKER-BROWSER.md
```

**Eine Korrektur ist damit EINE Datei statt achtzehn.**

---

## Der Anker

```text
Dreistufig. Stufe 2 ist ein SCHRITT, kein Zweig - es wird KEIN Projekt geoeffnet.

1  SEITE       HTTP 200 - document.title enthaelt "Hausplaner"
               - #hausplaner-root existiert und ist groesser als 0x0
               - #hausplaner-scene existiert (das JSON-Element aus der Blade-Seite;
                 ohne es meldet main.tsx "Mount oder Szene fehlt" und montiert nie)

2  MONTIEREN   Knopf "Expertenmodus" innerhalb #hausplaner-root klicken, bis 5 s warten.
               Kein Projekt, kein Schreiben in die Datenbank.

3  BUEHNE      ERST DANN: querySelectorAll('canvas') mindestens 1 (gemessen: 2)

Bleibt canvas NACH Stufe 2 bei 0, ist DAS der rote Befund - der Startzustand davor ist keiner.
```

**Die Aussage dazu:** *„Die Bühne ist MONTIERT, bevor irgendeine Zahl abgelesen wird — und der
Weg dahin steht im Blatt."*

---

## Wie er zu dieser Fassung kam — die Herkunft gehört dazu

**Erste Fassung (bis 02.08. 11:36):** *„VOR jeder anderen Zahl: HTTP 200, `querySelectorAll('canvas')`
mindestens 1, `document.title` enthält Hausplaner."*

**Erste Korrektur, dreistufig** — Planner-Befund `docs/planner/befund-anker-startzustand-2026-08-02.md`:
im Startzustand ist `canvas` **0**, und das ist richtig so. Die Bühne existiert erst mit offenem
Projekt; der alte Anker gab dort falsch rot. *„Ich habe die Stelle gemessen, nicht die Wirkung."*

**Zweite Korrektur, Stufe 2 wird ein SCHRITT** — gemessen am laufenden Browser: der Expertenmodus
montiert die Bühne **ohne** Projekt, `canvas` geht 0 → 2. Der Zweig *„Projekt öffnen ODER mit
STARTZUSTAND enden"* braucht keinen seiner beiden Ausgänge.

**NICHT übernommen:** *„`#hausplaner-scene` mit 0 Kindern"* als Startzeichen. Das Element ist ein
`<script type="application/json">` — es hat **nie** Element-Kinder. Als Zeichen taugt seine
EXISTENZ, nicht seine Kinderzahl. *(An der Quelle gemessen: `studio.blade.php:93`, `main.tsx:28`.)*

---

## Die Sperre dazu: S-11

```text
Ein Blatt mit `typ: browser` MUSS genau einen `L-01-anker` tragen.
  Ist er `typ: verweis`, muss die Quelle existieren.
  Ist er AUSGESCHRIEBEN, ist das der Fehler - die Kopie ist es, die driftet.
```

**S-11 greift nur bei `status` aus `aktiv · bereit · gebaut · entwurf · gesperrt`.** Das ist keine
Nachsicht gegenüber dem Archiv, sondern der Riegel für den einzigen Weg, auf dem ein alter Anker
zurückkommt: **wer ein `ruht`-Blatt auf `bereit` setzt, fällt in derselben Sekunde in S-11.**
*Ein Archivblatt kann nicht schaden, solange es Archiv bleibt; die Sperre sitzt am Übergang, nicht
am Ruhezustand.*

Gezählt wird mit `node scripts/anker-inventur.mjs` — **strukturell über die ```yaml-Blöcke, nie
über ein Textmuster.** *Die erste Fassung von W-08 zählte mit `grep`, traf sich selbst und
brauchte eine Namensausnahme; die hielt keine zwei Stunden, weil `FEHLERKLASSEN.md` das Muster in
der Beschreibung von F-19 zitiert. **Wer eine Ausnahme braucht, hat die falsche Naht gewählt.***

---

## Die Persistenz-Fläche — wo „speichern / neu laden" gemessen wird (Nachtrag 03.08., Planner)

```text
Wer SPEICHERN prueft, prueft auf der OBJEKT-Flaeche:
  objekt.blade.php:157   data-speichern-url="…route('hausplaner.objekt.speichern')…"
NIE auf studio:
  studio.blade.php:3     "KEINE Persistenz (kein data-speichern-url => Speichern ist
                          im Store ein No-Op)" — im Quelltext selbst dokumentiert.

Herkunft: Z-06-N1 ROT (Evaluator, 03.08.) — eine P1-Zusage prueft auf einer Flaeche
OHNE Speichern, ob etwas das Speichern ueberlebt. Sie konnte weder gruen noch rot
werden, und die fehlende Server-Naht rutschte durch. Traeger mit Speicher-Schritt:
N1 (umgeschrieben 03.08.) · N2, N3, Z-07 ziehen beim Gegenlesen nach (je 1 Treffer).
```
