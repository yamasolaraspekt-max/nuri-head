# Auftragsschema — maschinenlesbarer Kopf jedes Auftragsblatts

*Planner, 27.07.2026. Entstanden aus dem ersten `NACHBESSERN` des Zyklus und Yamas Frage, wie wir
konzeptionell besser werden. **Es ist kein neues Regelwerk, sondern die Form, in der die
bestehenden Regeln maschinell durchsetzbar werden.***

## 0. Warum ueberhaupt ein Schema

Am 27.07. sind an einem Tag sieben Befunde entstanden, vier davon meine. Die Ursachen waren nicht
Unaufmerksamkeit, sondern vier Konstruktionsfehler:

| Ursache | Beleg vom 27.07. |
|---|---|
| Unscharfes **Wort** als Bedingung (`statisch`) | Generator und Evaluator massen beide ehrlich und kamen gegensaetzlich heraus |
| **Vollstaendigkeit** nicht von Korrektheit getrennt | 11 Stellen richtig umgestellt, 15 uebersehen, Test gruen |
| Zusage prueft **Gestalt statt Wirkung** | *„diese elf Klassen existieren“* geht nie rot, wenn etwas fehlt |
| Anweisung am **falschen Ort** | Sperre im Fliesstext, zweimal in drei Stunden nicht gelesen |

**Und der Befund ueber allen:** Ich habe die Lehre zu Punkt 4 aufgeschrieben und **drei Stunden
spaeter denselben Fehler gemacht.** Das ist ein Beleg gegen das Medium, nicht gegen den Willen.
Prosa in einem langen Dokument aendert Verhalten nicht. **Deshalb dieses Schema: es wird gepruft,
nicht gelesen.**

## 1. Der Kopf

Jedes Auftragsblatt in `docs/auftraege/` beginnt mit **einem** YAML-Block. Alles davor ist Titel,
alles danach ist Erlaeuterung. **Was nicht im Block steht, ist kein Auftrag, sondern Hintergrund.**

```yaml
auftrag:
  id: AUF-38-S4                     # eindeutig, referenzierbar im Votum
  status: aktiv                     # aktiv | gesperrt | erledigt
  spur: A                           # A | B (bestehende Risiko-Spur, kein zweites System)
  heimat: ticket
  ziel: "HausplanerStudio.tsx traegt keine statischen Inline-Stile mehr."
  nicht_ziel: "Dynamische Stellen bleiben inline. Keine Palette-Entscheidungen."

scope:
  # Die Grundgesamtheit ist ein BEFEHL, keine Zahl.
  # Eine Zahl im Auftrag ist eine Messung zum Zeitpunkt des Schreibens und veraltet;
  # ein Befehl misst zum Pruefzeitpunkt neu. (Lehre vom 26.07.: "20 genannt, 34 gemessen".)
  population_command: "./scripts/zaehle-statische-stile.sh app/HausplanerStudio.tsx"
  population_at_writing: 19         # Messung des Planners, ausdruecklich KEINE Bedingung
  pfade:
    - resources/planner/hausplaner/app/HausplanerStudio.tsx
  ausschluesse: []                  # jeder Ausschluss braucht 'grund' und 'entschieden_von'

kriterien:
  - id: K-01
    aussage: "Keine statische Stelle verbleibt."
    typ: absence
    kritikalitaet: P1
    pruefung:
      befehl: "npm run test:hausplaner -- --filter=stilschicht"
      erwartet: exit 0
    beleg: testausgabe

  - id: K-02
    aussage: "Alle bei Auftragserteilung vorhandenen Stellen sind erfasst."
    typ: coverage
    kritikalitaet: P1
    pruefung:
      befehl: "./scripts/zaehle-statische-stile.sh app/HausplanerStudio.tsx"
      erwartet: "0 verbleibend, Abweichung zur Population begruendet"
    beleg: zaehlausgabe

  - id: K-03
    aussage: "Die Flaeche sieht unveraendert aus."
    typ: behavioural
    kritikalitaet: P1
    pruefung:
      typ: visuell
      schritte: "headful, drei Viewports, Toast ausgeloest (Konfigurator oeffnen, uebernehmen)"
      erwartet: "sha256 der Bildschirmfotos identisch"
    beleg: sha256-paare
    ausgefuehrt_von: evaluator      # der Generator kann diesen Beleg nicht selbst fuehren

ausnahmen:
  - stelle: "Namenskuerzel oben rechts"
    grund: "zwei Farben ohne Token in T; ein Token zu erfinden waere ein Palette-Entscheid"
    verriegelt: "Test prueft BEIDE Richtungen: Stelle inline UND Farben nicht in T"
    entschieden_von: planner

selbstnachweis:                     # der Generator fuellt das vor der Uebergabe
  preflight: "./scripts/auftrag-pruefen.sh docs/auftraege/<blatt>.md"
  gegenprobe: "eine Stelle zurueckdrehen, Test muss rot werden"
```

## 2. Die Prueftypen

| Typ | Bedeutung | Beispiel |
|---|---|---|
| `presence` | die gewuenschte Wirkung ist da | die Klasse wird benutzt |
| `absence` | der verbotene Zustand ist weg | keine statische Stelle mehr |
| `coverage` | **alle** betroffenen Stellen sind erfasst | 19 gefunden, 19 umgestellt, 0 offen |
| `behavioural` | das Verhalten stimmt in allen Zustaenden | Toast erscheint, Dialog geht auf |
| `adversarial` | die Sperre laesst sich nicht umgehen | Ownership-Gate gegen fremde ID |
| `manual` | benannte Schritte mit erwartetem Ergebnis | headful-Sichtprobe |

**Drei Regeln zu den Typen — jede aus einem konkreten Fehler:**

**T1 — `absence` allein genuegt nie.** *„Keine statische Stelle mehr“* ist auch gruen, wenn die
Datei geloescht wird. **Jedes `absence`-Kriterium braucht einen `presence`- oder
`behavioural`-Partner.** Bei AUF-38 ist das die K9-Sichtprobe — die haben wir zufaellig, weil die
Klassifikation *sichtbar* sie mitbringt. Zufall ist keine Konstruktion.

**T2 — `manual` braucht eine Begruendung, warum kein automatischer Typ geht.** Sonst wandert alles
Laestige dorthin. Wir kennen den Mechanismus von Spur B: *was lastig ist, wird umgangen, und das
Umgehen wird zur Gewohnheit.* Bei P0/P1 stimmt der **Evaluator** dem `manual` zu, nicht der
Planner — sonst genehmigt der Auftraggeber sich die bequeme Pruefung selbst.

**T3 — `adversarial` ist Pflicht genau bei den Spur-A-Ausloesern**, nicht pauschal bei P1: Geld,
Datum/Frist, Recht, Autorisierung/Sicherheit, Migration/Schema, Bestandsdaten, abgeleitete Werte.
**Bei einer Stilschicht gibt es keinen Angreifer, der eine CSS-Klasse umgeht** — ein Pflichtfeld
dort erzeugt Alibi-Tests und entwertet den Typ da, wo er zaehlt.

## 3. Wer prueft was — die Rollen bleiben getrennt

| Wer | Tut | Tut ausdruecklich nicht |
|---|---|---|
| **Planner** | Kriterien definieren, Grundgesamtheit als Befehl benennen, Schema-Pflichtfelder fuellen | Umsetzung fachlich freigeben |
| **Generator** | bauen, **Preflight selbst fahren**, Belege und Gegenprobe liefern | eigene Arbeit freigeben |
| **Preflight-Skript** | mechanische Mindestbedingungen | interpretieren, Ausnahmen erfinden |
| **Evaluator** | jedes Kriterium einzeln pruefen, eigene Pruefpfade entwickeln, Gegen-Beweis | fehlende Umsetzung selbst ergaenzen |

**Der Preflight ist keine vierte Instanz.** Er ist ein Skript, das **der Generator vor der
Uebergabe selbst ausfuehrt**; seine Ausgabe gehoert in den Bericht. Damit bleibt der Planner
vollstaendig aus dem Pruefpfad — sonst pruefte ich Arbeit gegen Kriterien, die ich selbst
formuliert habe, und waere fuer deren Luecken genauso betriebsblind wie am 27.07.

**Ein roter Preflight heisst nicht „fachlich abgelehnt“, sondern „nicht evaluierbar“.**

## 4. Das Votum

Das Votum nennt **jede Kriterien-ID** mit drei moeglichen Staenden:

| Stand | Bedeutung |
|---|---|
| `erfuellt` | mit Rohbeleg |
| `nicht erfuellt` | mit reproduzierbarem Fall |
| **`nicht geprueft`** | **zaehlt nie als erfuellt** |

Je Kriterium: ID · Pruefmethode · Erwartung · Ist-Ergebnis · Beleg · Stand · Gegen-Beweisversuch.

**Warum das noetig ist:** Scheibe 2 wurde am 26.07. freigegeben, obwohl dieselbe Luecke drin war
(29 statisch von 36). Der Evaluator hat den kritischen Punkt sauber von Hand geprueft — die
**Vollstaendigkeit** hat er nicht geprueft, weil das Format sie nicht verlangte. Ein Kriterium, das
niemand abhakt, faellt still durch.

## 5. Wiederholungsfehler brauchen eine Barriere

**Bei der zweiten Wiederholung derselben Fehlerklasse wird nicht noch ein Absatz geschrieben,
sondern eine der folgenden Barrieren gebaut:**

| Barriere | Beispiel |
|---|---|
| Pflichtfeld im Schema | `population_command` fehlt ⇒ Auftrag ungueltig |
| Test | Negativtest gegen den verbotenen Zustand |
| Gate | fehlender Beleg blockiert die Uebergabe |
| Vorlage | Auftrag laesst sich ohne Pruefmethode nicht anlegen |

**Damit das nicht wieder am Erinnern haengt, braucht die Regel selbst einen Traeger:** ein Register
`docs/auftraege/FEHLERKLASSEN.md` mit je einer Zeile pro Fehlerklasse und einem **Zaehler**. Beim
Eintragen des zweiten Vorkommens ist die Barriere faellig. *Ohne Zaehler waere auch diese Regel nur
wieder ein Satz, den ich in drei Stunden vergesse — genau der Fehler, gegen den sie gebaut ist.*

## 6. Aufwand: das Schema darf den Engpass nicht verschieben

Am 27.07. hat der Generator eine Scheibe in **vier Minuten** gebaut, die Abnahme dauerte **fuenf
Stunden**. Ein Schema, dessen Ausfuellen vierzig Minuten kostet, macht den Planner zum neuen
Engpass. Deshalb:

- **Spur A** (Geld, Frist, Recht, Autorisierung, Schema, Bestandsdaten, abgeleitete Werte): voller
  Kopf, `coverage` und `adversarial` Pflicht.
- **Spur B** (Markup, Text, Abstaende, Farben): `auftrag`, `scope`, **ein** Kriterium mit Befehl.
  Mehr nicht.

**Das Schema dockt an die bestehende Spur an und fuehrt keine zweite Einstufung ein.** Zwei
Einstufungssysteme nebeneinander waeren eine zweite Wahrheit — dieselbe Krankheit, die wir im Code
bekaempfen.

## 7. Was als Naechstes zu bauen ist

**Ich kann den Validator nicht bauen** — Cowork schreibt nur `docs/`. Das ist hier sogar richtig:
sonst baute ich das Werkzeug, das meine eigene Arbeit prueft.

Reihenfolge:

1. **Dieses Schema** (liegt hiermit).
2. **Auftrag an den Generator:** `scripts/auftrag-pruefen.sh` mit **fuenf** harten Pruefungen —
   mehr nicht, sonst bauen wir ein Meta-System statt eines Gates:
   1. genau **ein** Auftrag mit `status: aktiv`
   2. jedes Kriterium hat `typ` **und** `pruefung.befehl` (oder `manual` **mit** Begruendung)
   3. jedes P0/P1-Kriterium vom Typ `absence` hat einen `presence`/`behavioural`-Partner
   4. jedes `coverage`-Kriterium hat ein `population_command`, und der Befehl ist ausfuehrbar
   5. jeder Eintrag unter `ausschluesse` hat `grund` **und** `entschieden_von`
3. **Abnahme durch den Evaluator** — nach genau den Kriterien, die das Skript durchsetzen soll.
   Das ist der erste ehrliche Test des Schemas.
4. **Rueckwaerts-Probe ohne Folgen:** das Blatt von AUF-38 Scheibe 3 im alten Zustand durch den
   Validator schicken. **Findet er die Luecke nicht, die uns das NACHBESSERN eingebracht hat, taugt
   er nichts** — und das merken wir, bevor er im Weg steht.
5. **Erst danach** wandert die Regel nach `docs/agents/06-laufzeiten-und-takt.md`. Das Dokument
   beschreibt dann einen Prozess, der laeuft, statt einen, der gemeint ist.

## 8. Was dieses Schema NICHT loest

- **Es macht Kriterien nicht richtig, nur pruefbar.** Ein mechanisch sauberes, fachlich falsches
  Kriterium geht glatt durch.
- **Es ersetzt keine Abnahme.** Ein gruener Preflight sagt nur: evaluierbar.
- **Es faengt keine fehlende Grundgesamtheit**, die niemand bemerkt hat. Wenn der
  `population_command` die falschen Stellen zaehlt, ist auch die `coverage` falsch — nur eben
  ueberpruefbar falsch statt unsichtbar falsch. **Das ist der ganze Gewinn: aus einem stillen
  Fehler einen lauten machen.**
