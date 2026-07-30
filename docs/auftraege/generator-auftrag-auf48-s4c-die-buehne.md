# AUF-48-S4c — die Bühne aus dem JSX

**Spur A** · **Heimat: ticket** · **Basis: `262de870`** · *Geschnitten 30.07. 20:36*

## Umfang

**Aus `HausplanerApp.tsx` nach `resources/planner/hausplaner/app/rahmen/Buehne.tsx`:**
von `<Stage` **bis ausschliesslich** `{/* Rechtes Eigenschaften-Panel …`.

**Naehte ueber NAMEN** *(PB-007 — die Zeilennummern haben sich durch S3 schon wieder um 7
verschoben)*. Gemessen am Stand `262de870`: **294 Zeilen**.

Enthalten sind die Konva-Ebenen in dieser Reihenfolge: Referenzunterlage (unterstes Kind) ·
Raeume mit Flaechenbeschriftung · Waende mit Bemassung und Gehrung · Oeffnungen · Daecher
(Traufkontur, First) · Treppen · generische Objekte · die zwei Vorschauen beim Zeichnen.

## Die eine Zahl, die diese Scheibe besonders macht

```text
style={{ in diesem Block:  0
```

**Null.** Die Konva-Ebenen arbeiten ueber Props, nicht ueber CSS. *Das ist die einzige der fuenf
Scheiben, bei der AUF-38 nichts zu holen hat — und der Grund, warum sie sich sauber schneiden
laesst: es gibt keine Verflechtung mit der Gestaltung, nur mit den Daten.*

## Kriterien

```yaml
  - id: K-01
    aussage: "Die Ebenenfolge ist unveraendert — sie ist die Zeichenreihenfolge."
    befehl: "die Reihenfolge der Kommentarmarken in Buehne.tsx"
    erwartet: >
      Referenzunterlage · Raeume · Waende · Oeffnungen · Daecher · Treppen · Objekte · Vorschauen
    hinweis: >
      Diese Folge ist keine Kosmetik. Die Unterlage MUSS das erste Kind bleiben (AUF-88-P1/K-03),
      sonst liegt sie ueber der Zeichnung statt darunter.

  - id: K-02
    aussage: "Keine Inline-Stelle ist entstanden."
    befehl: "grep -c 'style={{' HausplanerApp.tsx  +  grep -c 'style={{' Buehne.tsx"
    erwartet: "die SUMME ist 133 — und Buehne.tsx traegt davon 0"

  - id: K-03
    aussage: "Kein Zustand ist mitgewandert."
    befehl: "grep -cE 'useState|useRef|usePlannerUiStore|localStorage' Buehne.tsx"
    erwartet: "0 — die Buehne nimmt Daten entgegen und meldet Ereignisse zurueck"
    ausnahme: >
      `stageRef` ist KEIN Zustand, sondern der Griff auf die Konva-Bühne, den die Kalibrierung
      und der DOM-Zuhoerer aus AUF-88-P1 brauchen. Er wird DURCHGEREICHT, nicht neu angelegt.
      Ein `useRef` in Buehne.tsx waere ein zweiter Griff auf dieselbe Sache — das ist der Befund.

  - id: K-04
    aussage: "Die Bühne ist verriegelt — voraussichtlich erstmals."
    gegenbeweis: >
      Mutiere VOR dem Schreiben der Tests: vertausche zwei Ebenen (Waende vor Raeume) und
      entferne die Vorschau beim Wandzeichnen. Wird keine Zusage rot, ist DAS der Befund.
      *Bei S1 waren 8 von 8 unverriegelt, bei S2 wieder — rechne nicht damit, dass es hier
      anders ist, aber behaupte es auch nicht ungemessen.*

  - id: K-05
    befehl: "npm run test:hausplaner"
    erwartet: "ueber der Zahl aus S3, kein roter Fall"

  - id: L-01
    aussage: "Die Bühne rendert nach dem Umbau noch."
    nachweis: >
      npm run build:hausplaner, dann http://ticket.test/admin/hausplaner/studio
      → Expertenmodus → Taste W → zwei Klicks auf LEERER Flaeche → Wand mit konstanter Dicke
      und Masszahl. Danach 2D → Split → 3D durchschalten.
    gegenbeweis: >
      Konsole nach `hausplaner.js` filtern, nicht nach `error`.

  - id: L-01-anker
    aussage: "Die Messung fand auf der richtigen Seite statt."
    nachweis: >
      VOR jeder anderen Zahl: HTTP-Status 200 · querySelectorAll('canvas') mindestens 1 ·
      document.title enthaelt "Hausplaner". Auch melden, wenn alles gut aussah.
      *Herkunft: der Pruefer hat an zwei eigenen Messungen belegt, dass ohne diesen Anker
      eine Login-Maske und eine HTTP-500-Seite als "sauber" durchgehen.*
```

## Reihenfolge dahinter

**S4a → S4b → S4c → S4d → S4e.** Gemessen am Stand `262de870`:

```text
S4a  Kopfrahmen               1133–1273  (verschoben durch S3)
S4b  Werkzeugleiste+Schiene   1274–1548   275 Z.   22 Inline
S4c  DIESES BLATT             1549–1842   294 Z.    0 Inline
S4d  Eigenschaften-Panel      1843–2255   413 Z.   67 Inline   <- die Haelfte der Datei
S4e  Statusleiste+Palette     2256–2382   127 Z.   20 Inline
```


---

## KORREKTUR 21:42 — die Naht neu gesetzt, nachdem der Generator meinen Endanker in S4b widerlegt hat

**Sein Befund zu S4b (P-04, zweite Stelle):** *„Ende: die Zeile vor `<Stage>` ist strukturell
nicht schneidbar. Dort stehen ZWEI OEFFNENDE `div`, beide schliessen erst NACH der Buehne. Wer
bis dorthin schneidet, nimmt unausgeglichene Tags mit."* **Er hat recht, und die Ursache ist
meine Schnittmethode:** ich schneide nach **Kommentar-Ankern** und habe die **Tag-Bilanz** nie
geprueft. *Ein Anker, der einen Block halbiert, ist kein Schnitt.*

### Fuer S4c neu gemessen — und diesmal richtig

**Mein erster Versuch war unbrauchbar:** eine Regex-Bilanz ueber oeffnende und schliessende Tags
meldete **-21** fuer eine Datei, die fehlerfrei baut. *Ein Messwert, der einem funktionierenden
Stand widerspricht, ist ein Messfehler und keine Entdeckung — ich habe ihn deshalb nicht
gemeldet, sondern anders gemessen.*

**Ueber die Einrueckung, gegen `2eb16643`:**

```text
Zeile 1088   <div style={{ display: modus === '3d' ? 'none' : 'block', width: stageBreite, … }}>   Einrueckung 8
Zeile 1089   <Stage                                                                                Einrueckung 8
Zeile 1381   </div>   auf derselben Einrueckung 8   ->  schliesst VOR dem Panel-Anker (1383)
```

**Die Buehnen-Huelle schliesst bei 1381, der Panel-Anker steht bei 1383.** *Der Schnitt ist also
moeglich* — aber **nicht so, wie das Blatt ihn beschrieb.**

### Die neuen Naehte

| Naht | Anker | war vorher |
|---|---|---|
| Anfang | **`<div style={{ display: modus === '3d' ? …`** — die Huelle, **nicht** `<Stage` | `<Stage` |
| Ende | die Zeile **nach** dem `</div>` auf derselben Einrueckung | „vor dem Panel-Anker" |

**Warum die Huelle mit muss:** sie traegt `display` nach Ansichtsmodus und `width: stageBreite`.
*Bleibt sie in der Hauptfunktion, muss die neue Komponente `modus` und `stageBreite` nur
durchreichen, um sie nirgends zu benutzen — und die Hauptfunktion behaelt ein `div`, dessen
einziger Inhalt eine fremde Komponente ist. Das ist kein Schnitt, sondern eine Naht mitten im
Bauteil.*

```yaml
  - id: K-06
    aussage: "Der Schnitt ist tag-ausgeglichen."
    nachweis: >
      Vor dem Schneiden: pruefe, dass Anfangs- und Endanker auf DERSELBEN Einrueckung liegen und
      dass zwischen ihnen kein Block beginnt, der spaeter schliesst.
    gegenbeweis: >
      Faellt `npm run tsc:hausplaner` mit einem Tag-Fehler, war die Naht falsch gesetzt —
      und dann ist das ein Befund gegen DIESES Blatt, nicht gegen deinen Bau. Melde ihn so.

  - id: K-07
    aussage: "`_zerlegteApp.ts` kennt das neue Modul."
    befehl: "grep -c \"app/rahmen/Buehne.tsx\" resources/planner/hausplaner/__tests__/_zerlegteApp.ts"
    erwartet: "1"
    gegenbeweis: >
      Setze eine verbotene Zeichenkette in das NEUE Modul, OHNE es in TEILE einzutragen,
      und lass die Absenz-Zusagen laufen. Bleiben sie gruen, ist die Liste die Luecke.
      *Der Kopf der Datei begruendet zu Recht, warum kein `readdir` — aber eine von Hand
      gepflegte Liste braucht eine Zusage gegen das Vergessen. Das ist sie.*
