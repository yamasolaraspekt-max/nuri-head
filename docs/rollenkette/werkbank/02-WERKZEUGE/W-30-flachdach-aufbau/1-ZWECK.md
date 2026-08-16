# W-30 · Flachdach-Aufbau — ZWECK

> ***EINORDNUNG: W-30 ist eine ABLESUNG*** — *gemessen, nicht angenommen.* **Und das Ergebnis ist
> ein anderes als bei W-17, W-25 und W-29: hier fehlt nicht der Anschluss, sondern die RECHNUNG.**

```text
VORLAGE       GEBAUT   dachformVorlagen.ts:1579-1600  category 'flat',
                       engineShape 'rect', Zimmerer-Angaben, Lastabtragsweg
SINNBILD      GEBAUT   :644  svgFlach(palette, attika)  — Zeichnung mit/ohne Attika
FLAGGEN       GEBAUT   FLAGS_FLACH  (:1593)
WARNUNG       GEBAUT   :488  PULT_GEFAELLE — Gefaelle ueber die Laenge nicht abgebildet
ATTIKA        DATENFELD :163 attika?: number (m, optional)  ·  :223 attika: number
GEFAELLE      TEXT     :1599 „Gefaelle >= 2 % (Richtwert)"
ABLAEUFE      FEHLT    kein Feld, keine Rechnung, kein Waechter
```

## Welches Problem des Anwenders löst dieses Werkzeug?

**Ein Flachdach ist kein Dach mit Neigung null.** *Es hat ein Gefälle, damit das Wasser abläuft, eine
Attika als Randabschluss und Abläufe, durch die das Wasser weggeht.* **Fehlt eines davon, steht das
Wasser** — *und der Aufbau, den der Code selbst nennt* (`:1600`), *ist* „Abdichtung → Dämmung →
Tragdecke → Wände".

## Der tragende Punkt: die drei Titel-Bestandteile sind DREI VERSCHIEDENE Reifegrade

| aus dem Titel | Stand, gemessen |
|---|---|
| **Gefälle** | **gerechnet** — `clampPitchGrad(pitch, 1.5, 8)` (`:497`), ein **eigenes Band nur für `category 'flat'`**, mit Warnung `PITCH_GEKLEMMT` statt stillem Abschneiden. Dazu `PULT_GEFAELLE` (`:488`): die Engine bildet Gefälle **nur über die Breite** ab |
| **Attika** | **als Datenfeld** (`:163`, `:223`) und **als Sinnbild** (`:644` `svgFlach(p, attika)`) — **kein Rechner**, kein Verbraucher außerhalb der Vorlagendatei |
| **Abläufe** | **gar nicht** — kein Feld, keine Rechnung, kein Wort außerhalb der Vorlagen-Aufzählung |

> ***Drei Bestandteile in einem Titel, drei verschiedene Reifegrade — und ich habe den ersten
> zuerst falsch eingeordnet.*** *Meine erste Fassung schrieb „Gefälle nur als Text".* **Gemessen
> gibt es eine eigene Klemmung `[1,5°; 8°]` allein für Flachdächer** (`:497`), *mit einer
> ausdrücklichen Warnung statt eines stillen Abschneidens* (`:400` *„kein stilles Abschneiden"*)
> *und acht Zusagen darauf* (`dachformVorlagen.test.ts:226-235`).
>
> **Der Befund ist deshalb nicht „hier fehlt die Rechnung", sondern feiner:** *das Gefälle ist
> gerechnet, die Attika ist ein Datenfeld ohne Rechner, und die Abläufe kommen nicht vor.* **Drei
> Stufen in einem Werkzeugnamen.**

## Was stattdessen gebaut ist, und es ist nicht wenig

**Die Vorlage trägt die fachlichen Angaben, die ein Zimmerer braucht** (`:1591-1600`):

```text
dachstuhltyp        „Tragdecke mit Gefaelledaemmung (Flachdach, Richtwert)"
materialFestigkeit  „NH C24 / Stahlbeton (je Konstruktion)"
holzfeuchteProzent  „<= 18 % (Holzdecke)"
abbundhinweis       „Tragdecke mit Gefaelledaemmung; kein Sparrendach."
spannweiteHinweis   „Durchbiegung/Pfuetzenbildung beachten; Gefaelle >= 2 % (Richtwert)."
lastabtragsweg      „Abdichtung -> Daemmung -> Tragdecke -> Waende."
```

> ***„kein Sparrendach" ist die wichtigste Zeile davon.*** *Sie sagt, warum W-30 nicht einfach eine
> Dachform neben Sattel und Walm ist:* **die ganze Sparren- und Lattungsrechnung aus W-21 trägt hier
> nicht.** *Eine Tragdecke hat keine Sparren.*
>
> **Und „Pfützenbildung beachten" ist die Stelle, an der der Code selbst auf die fehlende Rechnung
> zeigt** — *er nennt das Problem, das ein Gefällemodell lösen würde, und überlässt es dem Menschen.*
