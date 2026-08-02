# Z-05-N1 — Das Konturwerkzeug wird erreichbar: der achte Platz in der Registry

**Spur A** · **Heimat: ticket** · **Basis: HEAD beim Ziehen** · *Geschnitten 01.08. 19:5x*

```yaml
auftrag:
  id: Z-05-N1
  strang: hausplaner-3d
  status: abgenommen   # gebaut 01.08., Votum GRUEN vom Evaluator in a0a6e250 21:43 - eingetragen vom Planner, nicht abgenommen vom Planner
```

## Warum es dieses Blatt gibt — und dass es mein Fehler ist

**Der Generator hat Z-05 gebaut und L-01 blockiert zurueckgemeldet** (`264ab9dc`, 01.08. 19:09).
Sein Befund gegen mein Blatt ist richtig:

> `scope.dateien` nennt drei Dateien, aber K-01 verlangt „existiert in der UI-Auswahl" und L-01
> verlangt, damit im Browser zu zeichnen. Beides ist mit diesen drei Dateien nicht erreichbar.
> Der `population_command` misst nur die Typ-Union — er wird gruen, waehrend das Werkzeug
> unbenutzbar ist.

**Das ist F-07 in Reinform, und ich habe es geschnitten.** Der Zaehler misst die Stelle, an der
gebaut wird, nicht die Stelle, an der es wirkt. Die Union hat heute acht Werte, die Leiste sieben
Knoepfe — gemessen:

```text
node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/werkzeugArten.ts "'kontur'"    -> 1
node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/toolRegistry.ts  "id: 'kontur'" -> 0
```

**Widerspruch vor dem Bau, nicht in der Abnahme — der Weg hat funktioniert.**

## Bestand — gemessen 01.08. 19:3x

```text
node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/toolRegistry.ts   "id: 'kontur'"  -> 0   (Partner "id: 'wand'"    -> 1)
node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/werkzeugThemen.ts "'kontur'"      -> 0   (Partner Union         -> 1)
node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/werkzeugVertrag.ts "'kontur'"     -> 0
node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/toolPresentation.ts "'kontur'"    -> 0
node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/toolRegistry.ts   "shortcut: 'U'" -> 0   (Partner "shortcut: 'W'" -> 1)
grep -ro ', 110[,)]' resources/planner/hausplaner/__tests__/ | wc -l                             -> 9   (Rot-Partner ', 111[,)]' -> 0)
```

**Die 9 Treffer sind 8 Bilanz-Zusagen plus ein Falsch-Positiver** (`__tests__/eigenschaftenPanel.test.ts`
schneidet mit `.slice(0, 110)` eine Fehlermeldung ab — das ist keine Bilanz). Die acht echten:

| Datei | Zeilen |
|---|---|
| `resources/planner/hausplaner/__tests__/arbeitsbereiche.test.ts` | 87, 88 |
| `resources/planner/hausplaner/__tests__/werkzeugVertrag.test.ts` | 42, 59 |
| `resources/planner/hausplaner/__tests__/geplantKnoepfe.test.ts` | 80 |
| `resources/planner/hausplaner/__tests__/werkzeugGruppen.test.ts` | 31 |
| `resources/planner/hausplaner/__tests__/toolPresentation.test.ts` | 30 |
| `resources/planner/hausplaner/__tests__/leisteAusZonen.test.ts` | 101 |

## Die Entscheidung

**`kontur` wird der achte Eintrag mit `art: 'werkzeug'` in `resources/planner/hausplaner/app/tools/toolRegistry.ts`,
Kuerzel `U`, gebunden an `07-architektur`, Fix-Zone waechst von 7 auf 8.**

**Und die Bilanz wird nicht von 110 auf 111 umgeschrieben, sondern getrennt.** Die 110 sind Yamas
importiertes Fachpaket (101 Katalog + 9 Registry). `kontur` ist das **erste eigene Werkzeug, das
nicht aus dem Paket stammt**. Wer die Zahl einfach auf 111 setzt, verliert genau die Eigenschaft,
fuer die die Zusage da war: dass ein **verschwundenes** Paket-Werkzeug auffaellt.

```text
PAKET_WERKZEUGE = 110        (Konstante, benannt, unveraendert)
EIGENE_WERKZEUGE = ['kontur'] (Liste, waechst)
erwartet = PAKET_WERKZEUGE + EIGENE_WERKZEUGE.length
```

*Faellt ein Paket-Werkzeug raus und kommt ein eigenes dazu, bleibt die Summe gleich — die
getrennte Zaehlung faengt es trotzdem.* **Eine nackte 111 faengt es nicht.**

**Nicht `polygon` umbenennen.** `polygon` steht in `03-zeichnen-cad` bei den sechs stillgelegten
Primitiven (`bogen`, `kreis`, `linie`, `polygon`, `polylinie`, `rechteck`). Sie bleiben stillgelegt —
`kontur` ist ein Bauteil-Umriss, kein Zeichen-Primitiv. Yamas Entscheidung vom 01.08. gilt.

## Nahtstellen

**Hier wird geschrieben:**

```text
resources/planner/hausplaner/app/tools/toolRegistry.ts       der achte art:'werkzeug'-Eintrag
resources/planner/hausplaner/app/tools/werkzeugThemen.ts     'kontur' in 07-architektur
resources/planner/hausplaner/app/tools/werkzeugVertrag.ts    ein Vertrag mit eigener commandId
resources/planner/hausplaner/app/tools/toolPresentation.ts   eine Praesentationsregel, Zone = fix
die sechs Zusage-Dateien oben                                Bilanz getrennt statt hochgezaehlt
```

**Hier bewusst NICHT:**

```text
resources/planner/hausplaner/domain/validation.ts   kein persistiertes Schema. Die Werkzeugauswahl
                                                    ist UI-Zustand (gemessen: 0 gegen main).
resources/planner/hausplaner/geometry/kontur.ts     steht und ist gruen. Diese Scheibe macht sie
                                                    erreichbar, sie fasst die Geometrie nicht an.
ADD_CEILING und jede Speicher-Wirkung               das ist Z-06.
```

**Erweiterungspunkt, jetzt nicht gebaut:** `EIGENE_WERKZEUGE` als Liste anlegen, nicht als
zweite Konstante — das naechste eigene Werkzeug (Z-08 Dachkontur) haengt sich dort an, ohne dass
noch einmal acht Zusagen angefasst werden.

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/app/tools/toolRegistry.ts
    - resources/planner/hausplaner/app/tools/werkzeugThemen.ts
    - resources/planner/hausplaner/app/tools/werkzeugVertrag.ts
    - resources/planner/hausplaner/app/tools/toolPresentation.ts
  population_command: "node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/toolRegistry.ts \"id: 'kontur'\""
  ausschluesse:
    - stelle: "Die sechs stillgelegten Primitive (linie, polylinie, rechteck, kreis, bogen, polygon)"
      grund: "Sie bleiben stillgelegt. `kontur` ist ein Bauteil-Umriss, kein Zeichen-Primitiv."
      entschieden_von: planner
    - stelle: "geometry/kontur.ts und die Selbstschnitt-Pruefung"
      grund: "Gebaut und gruen in 264ab9dc. Diese Scheibe macht sie erreichbar, nicht anders."
      entschieden_von: planner
    - stelle: "ADD_CEILING und jede Speicher-Wirkung"
      grund: "Eigene Scheibe Z-06."
      entschieden_von: planner

kriterien:
  - id: N1-01
    typ: presence
    kritikalitaet: P1
    aussage: "`kontur` ist ein Eintrag der Registry, nicht nur ein Wert der Typ-Union."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/toolRegistry.ts \"id: 'kontur'\""
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 01.08. 19:3x; Partner \"id: 'wand'\" -> 1, die Messung ist nicht leer)"

  - id: N1-02
    typ: presence
    kritikalitaet: P1
    aussage: "`kontur` steht in genau einem Thema - die Zerlegung bleibt vollstaendig."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/werkzeugThemen.ts \"'kontur'\""
      erwartet: "1"
    ausgangswert: "0 (gemessen 01.08. 19:3x)"

  - id: N1-03
    typ: presence
    kritikalitaet: P1
    aussage: "`kontur` hat einen Vertrag im Katalog."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/werkzeugVertrag.ts \"'kontur'\""
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 01.08. 19:3x)"

  - id: N1-04
    typ: presence
    kritikalitaet: P1
    aussage: "`kontur` hat genau eine Praesentationsregel."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/toolPresentation.ts \"'kontur'\""
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 01.08. 19:3x)"

  - id: N1-05
    typ: presence
    kritikalitaet: P1
    aussage: "Keine nackte 110 mehr in den Bilanz-Zusagen - getrennt statt hochgezaehlt."
    pruefung:
      befehl: "grep -ro ', 110[,)]' resources/planner/hausplaner/__tests__/ | wc -l"
      erwartet: "1"
    ausgangswert: "9 (8 Bilanz-Zusagen + 1 Falsch-Positiver in eigenschaftenPanel.test.ts)"
    gegenbeweis: |
      Der Rot-Partner ', 111[,)]' liefert heute 0. Steht er nach dem Bau bei 8, ist die Zahl
      hochgezaehlt statt getrennt worden - dann ist dieses Kriterium ROT, auch wenn die Suite
      gruen ist. Erwartet wird EIN Rest: der `.slice(0, 110)` in eigenschaftenPanel.test.ts.

  - id: N1-06
    typ: absence
    aussage: "Kein persistiertes Schema wurde angefasst."
    pruefung:
      befehl: "git diff main -- resources/planner/hausplaner/domain/validation.ts | wc -l"
      erwartet: "0"
    ausgangswert: "0 (Partner: dieselbe Form gegen geometry/kontur.ts -> 181, der Befehl misst)"
    gegenbeweis: |
      `type`, `objectType`, `zoneType`, `routeType` bleiben, wie sie sind. Waechst diese Zahl,
      ist ein persistierter Wert umbenannt worden.

  - id: N1-07
    typ: absence
    aussage: "Die sechs stillgelegten Primitive bleiben stillgelegt."
    pruefung:
      befehl: "node scripts/zaehle.mjs resources/planner/hausplaner/app/tools/toolRegistry.ts \"'polygon'\""
      erwartet: "0"
    ausgangswert: "0"
    gegenbeweis: |
      WARUM N1-06 und N1-07 KEIN `kritikalitaet: P1` tragen - offen benannt, nicht umgangen:
      S-07 sperrt ein Kriterium, dessen Messung schon vor dem Bau der Zielwert ist, aber NUR bei
      P1. Beide sind echte Invarianten: sie sind heute 0 und sollen 0 bleiben. Mit P1 wuerde S-07
      jedes Mal sperren, obwohl nichts falsch ist - S-07 kann ein P1-absence-Kriterium mit
      konstantem Zielwert strukturell nicht zulassen. Das ist ein Befund am Werkzeug, nicht am
      Blatt. Der Evaluator misst beide trotzdem; ein Wachsen auf 1 ist rot.

  - id: N1-08
    typ: behavioural
    kritikalitaet: P1
    aussage: "Die Fix-Zone zaehlt 8 - UND ihre Rot-Gegenprobe ist mitgewandert."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        In resources/planner/hausplaner/__tests__/toolPresentation.test.ts stehen ZWEI Zahlen:
          Zeile 67  "Fix-Zone = genau die 7 art:werkzeug-Registry-ids"   7 -> 8
          Zeile 85  assert.equal(fix.length, 6, 'haette ein Werkzeug verloren')   6 -> 7
        Wer nur die erste anfasst, laesst eine Gegenprobe zurueck, die nichts mehr faengt.
      erwartet: "gruen, beide Zahlen gewandert"

  - id: N1-09
    typ: behavioural
    aussage: "Die ganze Insel bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: "0 fail. Ausgangswert 1603/1603 - Generator-Meldung aus 264ab9dc, vom Planner NICHT nachgemessen (der Planner faehrt keine npm-Gates)."

  - id: N1-10
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 6 Mutationen: kontur aus der Registry nehmen · art von 'werkzeug' auf 'aktion'
        aendern · das Thema 07-architektur entfernen · den Vertrag entfernen · die
        Praesentationsregel entfernen · das Kuerzel U mit einem bestehenden kollidieren lassen.
        Wie viele kommen durch?

  - id: L-01
    typ: presence
    kritikalitaet: P1
    aussage: "Browsertest an http://ticket.test - das Werkzeug ist waehlbar und zeichnet."
    pruefung:
      typ: browser
      schritte: |
        npm run build:hausplaner, dann /admin/hausplaner/studio, angemeldet, Expertenmodus.
        (a) die Leiste nennt "Kontur" - der Knopf ist da und waehlbar
        (b) Taste U schaltet auf das Werkzeug
        (c) L-Form mit sechs Punkten zeichnen, auf den ersten Punkt klicken -> schliesst
        (d) eine Acht zeichnen und schliessen wollen -> bleibt offen, Statusleiste nennt den Grund
        (e) Escape mitten im Zeichnen -> nichts bleibt liegen, kein halber Strich
        Drei Pflicht-Viewports: 1440, 1024, 375.
        PARTNERMESSUNG in jedem Viewport: "Wand" ist waehlbar. Ist sie es nicht, ist die
        Messung kaputt und nicht das Werkzeug.

  - id: L-01-anker
    typ: verweis
    quelle: docs/auftraege/ANKER-BROWSER.md
```

## Kantenliste — wo das erfahrungsgemaess bricht

```text
1  Die Rot-Gegenprobe der Fix-Zone (Zeile 85, `fix.length, 6`) bleibt stehen, waehrend Zeile 67
   auf 8 wandert. Dann ist die Gegenprobe tot und niemand merkt es. -> N1-08
2  Die Bilanz wird auf 111 gesetzt statt getrennt. Suite gruen, Verlusterkennung weg. -> N1-05
3  Kuerzel-Kollision: `U` ist heute frei (gemessen 0), aber `shortcutKollisionen()` in
   toolRegistry.ts meldet Kollisionen nur, wenn jemand sie abfragt.
4  `kontur` landet in ZWEI Themen (einmal 07-architektur, einmal versehentlich 03-zeichnen-cad).
   Dann ist die Zerlegung nicht mehr vollstaendig. -> N1-02 erwartet genau 1, nicht "mindestens".
5  Der Katalog-Zaehler 101 (toolKatalog.test.ts) wird mitgezogen, obwohl `kontur` in die
   Registry gehoert, nicht in den Katalog. 101 bleibt 101.
6  Konva loest keine CSS-Variable auf. Ein neues Werkzeug-Icon mit `var(--sa-...)` malt still
   die vorherige Farbe. `studioDaten.ts` behaelt echte Farbwerte.
7  Der Browsertest misst gegen einen alten Build. `npm run build:hausplaner` gehoert VOR (a).
```

## Rueckweg und Entdeckung

**Rueckweg:** Registry-Eintrag und drei Datenzeilen, kein Datenpfad, kein Schema, keine Migration —
der Commit laesst sich zurueckdrehen. Der Generator hat den Eintrag am 01.08. schon einmal gebaut
und wieder zurueckgedreht; die Suite stand danach wieder bei 1603/1603.

**Aber:** solange nichts gepusht ist, liegt der Rueckweg auf derselben Platte wie die Arbeit. Am
01.08. um 19:25 lagen **45 Commits** ungepusht (`git rev-list --count fork/auto/hausplaner-integration..auto/hausplaner-integration`).
*Das ist kein Ordnungsposten dieses Blattes, aber es ist der Grund, warum „zurueckdrehbar" heute
weniger wert ist, als es klingt.*

**Entdeckung:** der Knopf ist in der Leiste sichtbar oder er ist es nicht. Der stille Fall ist
Kante 1 und 2 — eine gruene Suite, deren Gegenproben nichts mehr fangen. Dagegen steht N1-05 mit
dem Rot-Partner `', 111[,)]'`.

## Danach

**Z-06** — die Decke nimmt die gezeichnete Kontur statt der Bounding-Box. *Erst mit diesem Blatt
gibt es ueberhaupt eine gezeichnete Kontur; ohne es waere der Kontur-Zweig in Z-06 toter Code.*
