# AUF-48-S4b — Bedien-Werkzeugleiste und Planer-Schiene aus dem JSX

**Spur A** (Umbau an der tragenden Oberfläche) · **Heimat: ticket** · **Basis: `ee1b3c59`**
*Geschnitten 30.07. 20:28. **S4 war zu gross — dies ist die zweite von fuenf Scheiben**,
nicht die zweite von zwei.*

## Warum neu geteilt wurde

Beim Schneiden von S4a stand: „S4b = alles ab der Buehne bis Dateiende". **Gemessen sind das
1108 Zeilen.** Eine Scheibe dieser Groesse ist kein Umbau mehr, sondern ein Umzug — und der
Prueferaufwand waechst schneller als der Nutzen. **Gemessen am Stand `59e91b50`:**

```text
S4b  Bedien-Werkzeugleiste + Schiene   1267–1541   275 Zeilen   22 Inline-Stellen
S4c  die Buehne (<Stage>)              1542–1835   294 Zeilen    0 Inline-Stellen
S4d  Eigenschaften-Panel               1836–2248   413 Zeilen   67 Inline-Stellen
S4e  Statusleiste + Command-Palette    2249–2375   127 Zeilen   20 Inline-Stellen
                                                   ----------   ----
                                                   1109         109
Datei gesamt 2375 · Inline-Stellen gesamt 133
```

**Zwei Zahlen aus dieser Messung sind fuer AUF-38-S7 wichtiger als fuer diesen Auftrag:**
`<Stage>` hat **null** `style={{`-Stellen — die Konva-Ebenen arbeiten ueber Props. Und das
**Eigenschaften-Panel traegt allein 67 der 133** Inline-Stellen, also die Haelfte der ganzen
Datei. *Wer S7 gegen „120 im JSX-Block" plant, plant in Wahrheit gegen S4d.*

## Umfang dieser Scheibe

**Aus `HausplanerApp.tsx` nach `resources/planner/hausplaner/app/rahmen/WerkzeugleisteUndSchiene.tsx`:**
vom Kommentar `{/* Bedien-Werkzeugleiste — Icons, jedes mit Tooltip … */}` **bis
ausschliesslich** `<Stage`.

**Die Naehte stehen ueber NAMEN, nicht ueber Zeilennummern** *(PB-007-Lehre — Zeilennummern
verschieben sich, sobald S4a landet)*:

| Naht | Anker im Quelltext |
|---|---|
| Anfang | `{/* Bedien-Werkzeugleiste — Icons, jedes mit Tooltip + Funktionsbeschreibung */}` |
| Ende | die Zeile **vor** `<Stage` |

Enthalten sind: die Bedien-Werkzeugleiste (Modus-Schalter 2D/Split/3D, Zoom, Raster), die
Themen-Gruppen des gewaehlten Arbeitsbereichs (AUF-34), die Kontext-Options-Leiste (§19/UI-4)
und die Planer-Schiene mit ihren drei Reitern samt Fuss (AUF-27).

**Nicht enthalten und nicht anfassen:** `<Stage>` und alles danach. Der Kopfrahmen aus S4a.

## Kriterien

```yaml
  - id: K-01
    aussage: "Die Sache bleibt gleich, nicht nur die Gestalt."
    befehl: "grep -c 'style={{' HausplanerApp.tsx  +  grep -c 'style={{' WerkzeugleisteUndSchiene.tsx"
    erwartet: "die SUMME ueber beide Dateien ist 133 — unveraendert"
    hinweis: >
      Diese Zusage misst ausdruecklich die Summe, nicht die Einzelzahl. Ein Rueckgang in der
      einen Datei ohne Zuwachs in der anderen ist eine Loeschung und gehoert gemeldet.

  - id: K-02
    aussage: "Kein Zustand ist mitgewandert."
    befehl: "grep -cE 'useState|useRef|usePlannerUiStore|localStorage' WerkzeugleisteUndSchiene.tsx"
    erwartet: "0 — die Scheibe nimmt Werte entgegen und gibt Absichten zurueck"

  - id: K-03
    aussage: "Die drei Reiter der Schiene und der Modus-Schalter sind verriegelt."
    gegenbeweis: >
      Mutiere VOR dem Schreiben der Tests: setze den Modus-Schalter fest auf '2D' und
      vertausche zwei Reiter-Beschriftungen. Wird keine Zusage rot, ist DAS der Befund —
      dann melden statt Tests nachreichen, die nur das Vorhandene bestaetigen.

  - id: K-04
    aussage: "Die Testzahl steigt."
    befehl: "npm run test:hausplaner"
    erwartet: "ueber 1476, kein roter Fall"

  - id: L-01
    aussage: "Die Buehne rendert nach dem Umbau noch — im Browser, nicht nur im tsc."
    nachweis: >
      npm run build:hausplaner, dann http://ticket.test/admin/hausplaner/studio
      → Expertenmodus → Taste W → zwei Klicks auf LEERER Flaeche.
      Zusaetzlich fuer DIESE Scheibe: die vier Arbeitsbereiche durchschalten und den
      Modus-Schalter 2D → Split → 3D fahren. Erwartet: die Schiene wechselt ihre Menues
      mit dem Bereich, alle drei Modi zeichnen.
    gegenbeweis: >
      Browserkonsole nach `hausplaner.js` filtern — NICHT nach `error`. Zwei Fehler aus
      `chat-*.js` sind Dauergaeste der Vue-Hauptapp und zaehlen weder als Treffer noch als
      Freibrief. Ein Laufzeitbefund gilt erst nach einem Zug auf LEERER Flaeche.
    bekannt: >
      BT-01 ist VORBESTEHEND und nicht Gegenstand dieser Scheibe: die Palette zeigt in
      Bauphysik/Heizung/Elektro·PV dieselben sieben Architektur-Werkzeuge, sechs ausgegraut.
      Das darf sich durch den Umbau nicht AENDERN — weder verschwinden noch schlimmer werden.
```

## Reihenfolge

**S3 → S4a → S4b → S4c → S4d → S4e.** Der Generator hat um 20:2x angesagt, dass S3 sein
naechstes Blatt ist. *S4b liegt damit als drittes bereit — die Front bleibt gefuellt.*

---

## NACHTRAG 20:34 — L-01 gehärtet: der Anker gegen die falsche Seite

**Der Prüfer hat an sich selbst gefunden, was meine L-01 nicht abfängt:**

```text
1. Anmeldung fehlgeschlagen  -> dreimal die Login-Maske vermessen  -> "0 Überlauf, 0 unerreichbar"
2. Vite-Manifest fehlte      -> HTTP 500                           -> dreimal die Fehlerseite -> "sauber"
```

**Beide Male hätte er ein falsches Grün geliefert.** Gefangen hat es nicht seine Sorgfalt, sondern
die Zahl `canvas: 0` — *eine Planer-Seite ohne Zeichenfläche gibt es nicht.*

**Meine L-01 hat genau diesen Anker nicht.** Sie sagt „Konsole nach `hausplaner.js` filtern" — aber
auf einer Login-Maske meldet `hausplaner.js` selbstverständlich nichts, und die Zusage stünde grün.
**Das ist derselbe Fehlertyp, den ich heute neunmal gemacht habe: ein Befehl, der die Gestalt misst
statt die Sache.**

**Ergänzung, verbindlich für jede Laufzeitprobe:**

```yaml
  - id: L-01-anker
    aussage: "Die Messung fand auf der richtigen Seite statt."
    nachweis: >
      VOR jeder anderen Zahl drei Werte nennen, die auf der falschen Seite unmöglich sind:
        HTTP-Status                          -> 200
        document.querySelectorAll('canvas')  -> mindestens 1
        document.title                       -> enthält "Hausplaner"
      Erst wenn alle drei stehen, zählt irgendeine weitere Zahl.
    gegenbeweis: >
      Melde die drei Werte auch dann, wenn alles gut aussah. Ein Bericht ohne diesen Anker
      ist nicht "wahrscheinlich richtig", sondern ununterscheidbar von einer Messung an der
      Login-Maske. *Herkunft: Prüfer, 30.07. 20:3x, an zwei eigenen Fehlmessungen belegt.*
```


---

## KORREKTUR 21:20 — dieses Blatt war ueberholt, bevor es gezogen wurde

**Der Generator hat beim Bau von S4a einen Fehler in MEINEM Anker gefunden** (P-04 seines
Berichts): *„Bedien-Werkzeugleiste = letzter Block vor der Buehne" stimmt nicht — zwei Bloecke
folgen.* **Er hat recht, und die Folge ist groesser als der Befund:**

Mit `cdc320c0` ist die **Bedien-Werkzeugleiste selbst nach `dashboard/Kopfrahmen.tsx` umgezogen.**
Der Anfangs-Anker dieses Blattes — `{/* Bedien-Werkzeugleiste … */}` — **existiert in
`HausplanerApp.tsx` nicht mehr.** Wer das Blatt so zieht, sucht einen Anker, der weg ist.

### Neu gemessen gegen `cdc320c0`

```text
Datei gesamt        2175   (von 2382)
Inline-Stellen      118    (von 133 — 15 sind mit dem Kopfrahmen gegangen)

S4b  Themen-Gruppen + Kontext-Optionen + Schiene   1121–1341   221 Z.   20 Inline
S4c  die Buehne                                    1342–1635   294 Z.    0 Inline
S4d  Eigenschaften-Panel                           1636–2048   413 Z.   67 Inline
S4e  Statusleiste + Command-Palette                2049–2175   127 Z.   20 Inline
```

### Die NEUEN Naehte dieses Blattes — wieder ueber Namen

| Naht | Anker |
|---|---|
| Anfang | `{/* AUF-34: die Themen-Gruppen des GEWÄHLTEN Arbeitsbereichs …` |
| Ende | die Zeile **vor** `<Stage` |

Enthalten: die Themen-Gruppen-Zeile · die Kontext-Options-Leiste (§19/UI-4) · die Planer-Schiene
mit ihren drei Reitern und dem Fuss. **Nicht mehr enthalten:** die Bedien-Werkzeugleiste — die
ist in `Kopfrahmen.tsx` und dort bereits verriegelt.

**K-01 (Inline-Summe) aendert sich entsprechend:** die Summe ueber `HausplanerApp.tsx` +
`Kopfrahmen.tsx` + das neue Modul muss **133** bleiben, nicht 118. *Die 15 Stellen im Kopfrahmen
zaehlen mit — sonst misst die Zusage nur noch einen Ausschnitt und wird gruen, weil Inhalt eine
Datei weiter gezogen ist. Genau diese Falle hat der Generator in S4a fuer 24 geerbte Zusagen
selbst geschlossen.*

### Und die Lehre, die groesser ist als dieses Blatt

**Ein Blatt, das drei Scheiben vorausgeschnitten wird, veraltet an seinen Ankern.** „Immer ein
Blatt voraus" bleibt richtig — aber ab jetzt gilt: **vor dem Ziehen wird der Anfangs-Anker
gegen HEAD geprueft.** Ein Anker, der nicht mehr trifft, ist kein Schnitt, sondern eine Suche.
