# Z-06-N2 — Man sieht der Decke an, dass sie geraten ist. Auch morgen.

**Spur A** · **Heimat: ticket** · **Basis: Z-06-N1 gebaut** · *Geschnitten 03.08. auf B10*

```yaml
auftrag:
  id: Z-06-N2
  strang: hausplaner-3d
  status: gesperrt
  sperrgrund: "Z-06-N1 muss GRUEN sein. Ohne die persistierten Felder `herkunft` und `freigabe` gibt es nichts zu kennzeichnen - die Kennzeichnung LIEST sie, sie erzeugt sie nicht. Wer N2 vor N1 baut, kennzeichnet einen Zustand aus dem Arbeitsspeicher und baut damit genau den Fehler nach, den B10 abstellt."
  gegengelesen_von:
  gegengelesen_am:
  befund:
  fachentscheidung: "Yama, 02.08. — B10: 'sichtbare Kennzeichnung nach erneutem Laden'. Wortlaut: 'Morgen darf dieselbe Decke nicht wie ein bestaetigtes, exakt geplantes Bauteil erscheinen.'"
```

## Warum das eine eigene Scheibe ist und nicht Teil von N1

**N1 schreibt zwei Felder ins Schema. N2 macht sie sichtbar. Das sind verschiedene Fehlerklassen:**
*ein falsches Schema kostet eine Migration, eine falsche Anzeige kostet einen Commit.* **Ein
Blatt, das beides anfasst, hat zwei Rückwege und keinen davon ganz.**

## Was heute da ist — gemessen, bevor entschieden wurde

```text
FussUndUeberlagerungen.tsx:101   {konturHinweis && <span className="hp-kontur-hinweis">…}
hausplaner.css:532               .hp-kontur-hinweis { color: var(--hp-ink); }
```

**Der heutige Hinweis ist eine Fußzeile in der Standardfarbe, und er lebt nur in der Sitzung.**
*Er sagt „Näherung", während er da ist — und schweigt, sobald man neu lädt.* **Beides ändert sich
hier: er liest ab jetzt das persistierte Feld, und er ist am OBJEKT, nicht nur am Fuß.**

**Die Farbe ist vorhanden und wird nicht neu erfunden:** `FARBEN.warnung` steht bereits in
derselben Datei (Zeile 104, `letzteAblehnung`). *Und die Statusfarben liegen im CSS — 6 Treffer.
Kein neuer Inline-Stil: AUF-38 ist bei NULL offenen Stellen zu Ende gegangen, und das bleibt so.*

## Die Entscheidung — drei Orte, und der dritte ist der eigentliche

```text
1  FUSS       der bestehende Hinweis liest `freigabe` statt eines Sitzungs-Zustands
2  LISTE      im Geschoss-/Objektbaum traegt eine unbestaetigte Decke ein Zeichen
3  3D-KOERPER die Decke selbst ist erkennbar anders dargestellt
```

**Der dritte ist der eigentliche, und er ist der Grund für diese Scheibe:** *wer die Datei morgen
öffnet, schaut auf das Modell, nicht in eine Fußzeile.* **Eine Kennzeichnung, die man erst findet,
wenn man sie sucht, erfüllt B10 nicht.**

**Wie im 3D-Körper — bewusst NICHT über die Farbe allein:**

```text
Farbe allein waere die falsche Wahl: der Planer wird gedruckt, geteilt und von Leuten
angesehen, die Rot und Gruen nicht unterscheiden. Die Kennzeichnung traegt deshalb ZWEI
Kanaele - eine sichtbare Andersartigkeit der Flaeche (Muster/Transparenz) UND das
Textzeichen in Liste und Fuss. Welcher Kanal im 3D genau: Entscheidung des Bauenden,
mit Beleg im Browsertest.
```

## Nahtstellen

```text
Hier wird geschrieben:
  app/rahmen/FussUndUeberlagerungen.tsx    der Hinweis liest `freigabe`
  app/dashboard/…                          das Zeichen in der Liste
  renderers/three-d/szene.ts               die Darstellung der Flaeche
  hausplaner.css                           die Klassen dazu - KEIN Inline-Stil

Hier bewusst NICHT:
  domain/validation.ts       Das ist N1. N2 LIEST nur.
  Der Bestaetigungs-Knopf    Das ist N3. Hier gibt es noch keinen Weg, etwas zu bestaetigen -
                             die Kennzeichnung zeigt also einen Zustand, den man noch nicht
                             aufloesen kann. Das ist Absicht: erst sehen, dann handeln.
  Das DACH                   traegt dieselben Felder aus N1 und bekommt dieselbe
                             Kennzeichnung - aber Z-07/Z-08 sind noch nicht gebaut.
                             Wer hier eine Dach-Sonderbehandlung einbaut, baut fuer etwas,
                             das es nicht gibt.
```

## Kriterien

```yaml
scope:
  dateien:
    - resources/planner/hausplaner/app/rahmen/FussUndUeberlagerungen.tsx
    - resources/planner/hausplaner/renderers/three-d/szene.ts
    - resources/planner/hausplaner/hausplaner.css
  population_command: "grep -ro 'freigabe' resources/planner/hausplaner/app/ | wc -l"
  ausschluesse:
    - stelle: "domain/validation.ts"
      grund: "Das ist Z-06-N1. N2 liest die Felder, es erzeugt sie nicht."
      entschieden_von: planner
    - stelle: "Der Bestaetigungs-Knopf"
      grund: "Z-06-N3. Erst sehen, dann handeln - die Reihenfolge ist Absicht."
      entschieden_von: planner
    - stelle: "Eine Dach-Sonderbehandlung"
      grund: "Z-07/Z-08 sind nicht gebaut. Das Dach bekommt dieselben Felder und dieselbe Kennzeichnung, sobald es sie gibt."
      entschieden_von: planner

kriterien:
  - id: K-01
    typ: presence
    kritikalitaet: P1
    aussage: "Die Anzeige liest das persistierte Feld."
    pruefung:
      befehl: "grep -ro 'freigabe' resources/planner/hausplaner/app/ | wc -l"
      erwartet: "mindestens 1"
    ausgangswert: "0 (gemessen 03.08.; Partner 'konturHinweis' -> mehrfach, die Messung ist nicht leer)"

  - id: K-02
    typ: absence
    kritikalitaet: P1
    aussage: "Kein neuer Inline-Stil - AUF-38 bleibt bei NULL offenen."
    pruefung:
      befehl: "node scripts/statische-inline-stile.mjs"
      erwartet: "0 offen, Gesamtzahl hoechstens 118"
    ausgangswert: "118 Stellen / 0 offen (gemessen 02.08.)"

  - id: K-03
    typ: behavioural
    kritikalitaet: P1
    aussage: "DIE KERNZUSAGE VON B10: die Kennzeichnung ueberlebt das NEULADEN."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        B3 - gegen die Entscheidungsfunktion, nicht ueber den Schirm:
          freigabe 'vorschlag'   -> Kennzeichnung AN
          freigabe 'zu_pruefen'  -> Kennzeichnung AN
          freigabe 'abgelehnt'   -> Kennzeichnung AN
          freigabe 'bestaetigt'  -> Kennzeichnung AUS
        Und die Herkunft steht im Text, nicht nur ein Ausrufezeichen:
          herkunft 'abgeleitet' -> der Text NENNT sie ("aus dem Grundriss abgeleitet")
        Die letzte Zeile ist die scharfe: ein blosses Warnzeichen sagt "irgendwas stimmt
        nicht" und laesst den Leser raten. B10 verlangt den HERKUNFTSSTATUS, nicht ein Gefuehl.
      erwartet: "fuenf Zusagen, davon eine ROTE (bestaetigt -> AUS)"

  - id: K-04
    typ: behavioural
    kritikalitaet: P1
    aussage: "ZWEI Kanaele - die Kennzeichnung haengt nicht an der Farbe allein."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: |
        Der Planer wird gedruckt, geteilt und von Leuten angesehen, die Rot und Gruen nicht
        unterscheiden.
          die Flaeche ist auch OHNE Farbunterschied als anders erkennbar (Muster/Transparenz)
          das Textzeichen steht in Liste UND Fuss
        Eine Zusage, die nur die Farbe prueft, ist gruen und die Kennzeichnung trotzdem
        unsichtbar fuer einen Teil der Nutzer.
      erwartet: "zwei Kanaele belegt, jeder einzeln"

  - id: K-05
    typ: behavioural
    aussage: "Die ganze Insel bleibt gruen."
    ausgefuehrt_von: generator
    pruefung:
      typ: gate
      schritte: "npm run test:hausplaner"
      erwartet: "0 fail. Ausgangswert 1649 pass / 0 fail (02.08. 13:4x) plus die Zusagen aus N1."

  - id: K-06
    typ: behavioural
    aussage: "Die Mutationsprobe kommt VOR den Zusagen."
    pruefung:
      typ: verfahren
      schritte: |
        Mindestens 6 Mutationen: Kennzeichnung nur im Fuss · nur in der Liste · nur im 3D ·
        Kennzeichnung an den Sitzungs-Zustand statt an `freigabe` gehaengt · 'bestaetigt'
        zeigt sie auch · die Herkunft wird nicht genannt, nur ein Warnzeichen.
        Wie viele kommen durch?

  - id: L-01
    typ: presence
    kritikalitaet: P1
    aussage: "Browsertest an http://ticket.test - NEU LADEN und hinsehen."
    pruefung:
      typ: browser
      schritte: |
        npm run build:hausplaner, dann /admin/hausplaner/studio, angemeldet, Expertenmodus.
        (a) Waende zeichnen, Decke anlegen OHNE Kontur, speichern
        (b) SEITE NEU LADEN
        (c) die Decke ist im 3D-Koerper erkennbar anders als eine bestaetigte
        (d) die Liste zeigt das Zeichen, der Fuss nennt die HERKUNFT im Klartext
        KONTROLLE davor (B4): dieselbe Folge mit MANUELL gezeichneter Kontur
            -> keine Kennzeichnung, weder im Koerper noch in der Liste
        Erst weil die Kontrolle anders ausfaellt, bedeutet das Ergebnis etwas.
        Drei Pflicht-Viewports: 1440, 1024, 375.

  - id: L-01-anker
    typ: verweis
    quelle: docs/auftraege/ANKER-BROWSER.md
```

## Kantenliste — jede Zeile mit Zusage oder Grund (B9)

```text
1  Die Kennzeichnung haengt am Sitzungs-Zustand statt an `freigabe` -
   dann ist nach dem Neuladen alles wieder weg.                        -> K-03, K-06
2  Sie steht nur im Fuss und niemand sieht sie im Modell.              -> K-03, K-04
3  Sie haengt an der Farbe allein.                                     -> K-04
4  'bestaetigt' zeigt sie auch - dann ist sie Zierrat.                 -> K-03 rote Zeile
5  Ein blosses Warnzeichen ohne die Herkunft im Klartext.              -> K-03 letzte Zeile
6  Ein neuer Inline-Stil.                                              -> K-02
7  Die Kennzeichnung stoert beim Arbeiten, weil sie immer da ist.
   OHNE ZUSAGE, mit Grund: das ist der PUNKT. Eine Kennzeichnung, die man wegklicken kann,
   ist nach dem naechsten Laden entweder weg (dann bricht B10) oder gespeichert (dann ist
   sie ein zweiter Freigabe-Zustand neben `freigabe`). Wenn sie stoert, ist die Antwort
   BESTAETIGEN - und dafuer gibt es N3.
8  Die Darstellung im 3D kollidiert mit einer spaeteren Material-Darstellung.
   OHNE ZUSAGE, mit Grund: es gibt heute keine Material-Darstellung im 3D, gegen die man
   pruefen koennte. Der Bauende waehlt den Kanal und BELEGT ihn im Browsertest; kollidiert
   er spaeter, ist das eine Zeile im dann vorhandenen Blatt.
```

## Rückweg und Entdeckung

**Rückweg:** drei Dateien, kein Schema, keine Migration. **Der Commit lässt sich zurückdrehen** —
und der Zustand davor ist der, in dem die Felder da sind und niemand sie sieht.

**Entdeckung:** K-03 letzte Zeile. **Wenn die Kennzeichnung nur ein Warnzeichen ist und die
Herkunft nicht nennt, sieht alles grün aus** — und der Nutzer weiß, dass etwas nicht stimmt, aber
nicht was. *B10 verlangt den Herkunftsstatus, nicht ein Gefühl.*
