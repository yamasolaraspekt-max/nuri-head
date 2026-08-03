# Z-06-N3 — Bestätigen, und die Bestätigung wieder verlieren

**Spur A** · **Heimat: ticket** · **Basis: Z-06-N2 gebaut** · *Geschnitten 03.08. auf B10*

```yaml
auftrag:
  id: Z-06-N3
  strang: hausplaner-3d
  status: gesperrt
  sperrgrund: "Z-06-N2 muss GRUEN sein. Ein Bestaetigungs-Knopf ohne sichtbare Kennzeichnung ist ein Knopf ohne Frage - der Nutzer bestaetigt etwas, das er nicht sieht. Erst sehen, dann handeln."
  gegengelesen_von:
  gegengelesen_am:
  befund:
  fachentscheidung: "Yama, 02.08. — B10: 'explizite Bestaetigung durch den Nutzer' und 'Auditnachweis, wann und durch wen die Kontur bestaetigt oder geaendert wurde'."
```

## Der Kern ist nicht der Knopf, sondern das ZURÜCKSETZEN

**Ein Bestätigungs-Knopf ist eine halbe Stunde Arbeit. Die Regel dahinter ist die Scheibe:**

```text
Wer eine Kontur BESTAETIGT, bestaetigt DIESE Kontur - nicht die Decke als Gegenstand.
Aendert sich die Kontur, ist die Bestaetigung hinfaellig und der Zustand faellt
auf `zu_pruefen` zurueck.
```

**Ohne diese Regel ist die ganze Kette wertlos:** *ein Nutzer bestätigt einmal, die Geometrie
ändert sich zehnmal, und das Feld sagt weiterhin „bestätigt".* **Dann trägt `freigabe` genau so
wenig wie der Sitzungs-Hinweis, den B10 abgeschafft hat — nur unsichtbarer, weil es nach Sorgfalt
aussieht.**

## Woran erkennt man, dass sich „die Kontur geändert hat"

**Das ist die eigentliche Bauentscheidung und sie braucht eine Messung, kein Gefühl:**

```text
Die Bestaetigung haelt einen FINGERABDRUCK der Geometrie fest, die bestaetigt wurde.
Beim Laden und bei jeder Aenderung wird er neu gebildet und verglichen.
  gleich      -> Bestaetigung gilt
  ungleich    -> freigabe faellt auf `zu_pruefen`, freigabe_am/-von werden gesetzt
```

*Warum ein Fingerabdruck und nicht ein „geändert"-Flag:* **ein Flag muss von jeder Stelle gesetzt
werden, die die Geometrie anfasst — und genau eine vergisst es.** *Der Fingerabdruck wird
BERECHNET; wer ihn vergisst, bekommt keinen falschen Zustand, sondern gar keinen.* **Dieselbe
Entscheidung wie bei der Bilanz in W-05: nicht die Zahl anfassen, sondern sie rechnen.**

## Nahtstellen

```text
Hier wird geschrieben:
  domain/validation.ts        das Feld geometrieFingerabdruck (Schema v3 -> v4, ADDITIV; Name analog geometrieHerkunft, insel-weit kollisionsfrei)
  geometry/freigabe.ts        bildeFingerabdruck() + pruefeFingerabdruck()
  app/…                       der Bestaetigungs-Knopf und sein Ort
  commands/applyCommand.ts    jede Geometrie-Aenderung laeuft hier durch

Hier bewusst NICHT:
  Eine Freigabe-Historie      Zwei Felder (freigabe_am, freigabe_von) decken Yamas
                              "wann und durch wen" ab - das steht seit N1 so und
                              gilt weiter. Eine Historie ist eine eigene Entscheidung.
  Rollen und Rechte           WER bestaetigen DARF, ist eine Frage an die App, nicht
                              an den Planer. Heute traegt `freigabe_von` den
                              angemeldeten Benutzer; eine Berechtigungspruefung
                              waere eine eigene Scheibe mit eigenem Rueckweg.
  Ein Sammel-Bestaetigen      "alle Decken bestaetigen" ist genau der Knopf, der die
                              Pruefung wieder abschafft. Wenn er je gebraucht wird,
                              braucht er eine eigene Entscheidung von Yama.
```

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/geometry/freigabe.ts
    - resources/planner/hausplaner/domain/validation.ts
    - resources/planner/hausplaner/commands/applyCommand.ts
  population_command: "grep -ro 'geometrieFingerabdruck' resources/planner/hausplaner/ | wc -l"
  ausschluesse:
    - stelle: "Eine Freigabe-Historie"
      grund: "freigabe_am und freigabe_von decken 'wann und durch wen' ab. Historie ist eine eigene Entscheidung (Dateigroesse, Aufraeumfrage)."
      entschieden_von: planner
    - stelle: "Rollen und Rechte"
      grund: "WER bestaetigen darf, ist eine Frage an die App. Eigene Scheibe, eigener Rueckweg."
      entschieden_von: planner
    - stelle: "Ein Sammel-Bestaetigen"
      grund: "'Alle bestaetigen' ist der Knopf, der die Pruefung wieder abschafft. Braucht eine eigene Entscheidung von Yama."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Das Feld geometrieFingerabdruck existiert."
    pruefung:
      befehl: "grep -ro 'geometrieFingerabdruck' resources/planner/hausplaner/ | wc -l"
      erwartet: "mindestens 2"
    ausgangswert: "0 (gemessen 03.08.; bewusst ueber das VERZEICHNIS, weil geometry/freigabe.ts vor N1 nicht existiert; Partner 'schneidetSichSelbst' -> 2, die Messung ist nicht leer)"

  - id: K-02
    typ: behavioural
    kritikalitaet: P1
    aussage: "DIE KERNZUSAGE: eine geaenderte Kontur verliert ihre Bestaetigung."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B3 - gegen die Entscheidungsfunktion:
          bestaetigen -> freigabe 'bestaetigt', Fingerabdruck festgehalten
          EINEN Punkt der Kontur verschieben -> freigabe faellt auf 'zu_pruefen'
          denselben Punkt zurueckschieben (identische Geometrie) -> BLEIBT 'zu_pruefen'
          erneut bestaetigen -> 'bestaetigt', neuer Fingerabdruck
        Die DRITTE Zeile ist die scharfe und sie ist Absicht: eine Aenderung, die
        rueckgaengig gemacht wurde, hat trotzdem stattgefunden. Wer sie automatisch
        wieder bestaetigt, baut ein Werkzeug, das dem Nutzer das Hinsehen abnimmt -
        und genau das soll es nicht.
      erwartet: "vier Zusagen, die dritte ist die tragende"

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "Der Fingerabdruck wird GERECHNET, nicht gesetzt."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Eine Geometrie-Aenderung, die den Fingerabdruck NICHT ausdruecklich neu bildet,
        darf die Bestaetigung trotzdem nicht ueberleben:
          Kontur ueber einen Weg aendern, der `bildeFingerabdruck` gar nicht ruft
            -> beim naechsten Laden faellt die Freigabe auf 'zu_pruefen'
        Das ist der Unterschied zwischen Rechnen und Setzen: ein "geaendert"-Flag muesste
        an JEDER Stelle gesetzt werden, die die Geometrie anfasst - und genau eine vergisst
        es. Wer diese Zusage nicht hinbekommt, hat ein Flag gebaut und es nur so genannt.
      erwartet: "die Freigabe faellt auch auf dem ungeplanten Weg"

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "Zeitstempel und Urheber bei JEDEM Wechsel - auch beim automatischen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        bestaetigt durch den Nutzer  -> freigabe_von = der angemeldete Benutzer
        automatisch zurueckgesetzt   -> freigabe_von = 'system', freigabe_am neu
        kein Wechsel                 -> BEIDE bleiben unveraendert
        Die zweite Zeile ist die, die man vergisst: ein Ruecksetzen ohne Stempel sieht
        im Nachhinein aus wie "war nie bestaetigt".
      erwartet: "drei Zusagen, die zweite ist die vergessene"

  - id: K-05
    typ: presence
    kritikalitaet: P1
    aussage: "Die Schema-Version ist erneut angehoben."
    pruefung:
      befehl: "grep -o 'schemaVersion: z.literal(4)' resources/planner/hausplaner/domain/validation.ts | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (nach N1 steht dort z.literal(3))"
    gegenbeweis: |
      Ein v3-Dokument hat `freigabe`, aber keinen Fingerabdruck. Laedt es als v4, gilt
      jede Bestaetigung als gueltig, die nie gegen eine Geometrie gehalten wurde -
      und die Migration muesste sie auf 'zu_pruefen' setzen, statt sie zu erben.

  - id: K-06
    typ: behavioural
    aussage: "Die ganze Insel bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: "0 fail. Ausgangswert: der Stand nach N2."

  - id: K-07
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 7 Mutationen: Fingerabdruck wird beim Bestaetigen nicht festgehalten ·
        Vergleich immer gleich · Rueckgaengig-Machen stellt die Bestaetigung wieder her ·
        Ruecksetzen ohne Stempel · Fingerabdruck als Flag statt als Rechnung · Migration
        erbt die Bestaetigung · Bestaetigen ohne Benutzer (leeres freigabe_von).
        Wie viele kommen durch?

  - id: L-01
    typ: presence
    kritikalitaet: P1
    aussage: "Browsertest - bestaetigen, aendern, und die Kennzeichnung kommt ZURUECK."
    pruefung:
      typ: browser
      schritte: |
        npm run build:hausplaner, dann /admin/hausplaner/studio, angemeldet, Expertenmodus.
        (a) Decke ohne Kontur anlegen -> gekennzeichnet (aus N2)
        (b) bestaetigen -> Kennzeichnung verschwindet
        (c) SPEICHERN und NEU LADEN -> sie bleibt verschwunden
        (d) einen Konturpunkt verschieben -> die Kennzeichnung ist WIEDER DA
        (e) speichern, neu laden -> sie ist immer noch da
        Schritt (d) ist der eigentliche Test. Ohne ihn prueft man einen Knopf,
        nicht eine Regel.

  - id: L-01-anker
    typ: verweis
    quelle: docs/auftraege/ANKER-BROWSER.md
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  Der Fingerabdruck wird beim Bestaetigen nicht festgehalten.         -> K-02
2  Der Vergleich sagt immer "gleich".                                  -> K-02, K-07
3  Rueckgaengig-Machen stellt die Bestaetigung wieder her.             -> K-02 dritte Zeile
4  Der Fingerabdruck ist in Wahrheit ein Flag, das gesetzt wird.       -> K-03
5  Ein Ruecksetzen ohne Stempel - sieht spaeter aus wie "nie bestaetigt". -> K-04
6  Die Migration erbt die Bestaetigung aus v3.                         -> K-05
7  Ein Sammel-Bestaetigen wird "der Bequemlichkeit halber" eingebaut.
   OHNE ZUSAGE, mit Grund: er steht als Ausschluss im Blatt. Eine Zusage dagegen waere
   eine Zusage ueber Absicht, und die gibt es nicht. Wenn er gebraucht wird, entscheidet
   Yama - und dann mit einem eigenen Blatt, das den Preis benennt.
8  Zwei Nutzer bestaetigen dieselbe Decke gleichzeitig.
   OHNE ZUSAGE, mit Grund: der Hausplaner hat heute keine gleichzeitige Bearbeitung -
   eine Szene gehoert einer Sitzung. Kaeme sie, waere das eine Frage an die App
   (Sperren, Revisionen), nicht an diese Scheibe. Benannt, damit die naechste Sitzung
   nicht raet, ob es uebersehen wurde.
```

## Rückweg und Entdeckung

**Rückweg:** eine Schema-Version zurück — **und wie bei N1 ehrlich benannt: sobald eine Datei als
v4 abgelegt ist, ist der Rückweg eine Rückmigration.**

**Entdeckung:** K-02 dritte Zeile. **Wenn eine rückgängig gemachte Änderung die Bestätigung
wiederherstellt, sieht das aus wie Komfort und ist ein Loch:** *dann genügt Ändern-und-Zurück, um
jede Prüfung zu umgehen — und niemand hat je hingesehen.*
