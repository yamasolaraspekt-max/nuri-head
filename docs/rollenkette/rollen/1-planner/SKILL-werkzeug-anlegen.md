# SKILL · Ein neues Werkzeug anlegen

**Wann:** Ein Handgriff des Anwenders ist noch nirgends abgebildet.

---

## Schritt 1 — Prüfen, ob es wirklich neu ist

`02-WERKZEUGE/REGISTER.md` lesen. Zwei Fragen:

- Deckt ein vorhandenes Werkzeug den Zweck schon ab? → **Erweiterung**, kein Neubau
- Ist es ein Teilschritt eines vorhandenen? → gehört in dessen `2-FUNKTION.md`

> Ein Werkzeug ist neu, wenn der Anwender es **eigenständig aufruft** und es
> **eigenständig abbricht**. Alles andere ist ein Zustand innerhalb eines Werkzeugs.

## Schritt 2 — Nummer und Ordner

```bash
cp -r 02-WERKZEUGE/_VORLAGE 02-WERKZEUGE/W-<nn>-<kurzname>
```

Nummer ist fortlaufend und wird **nie wiederverwendet**. Kurzname in Kleinbuchstaben
mit Bindestrichen, deutsch, sprechend: `dach-aus-kontur`, nicht `roofgen`.

## Schritt 3 — Von hinten nach vorne füllen

Nicht von 1 nach 7. **Von 7 nach 1.**

| Reihenfolge | Blatt | Warum zuerst |
|---|---|---|
| 1. | `7-GRENZEN.md` | Was es **nicht** kann, entscheidet über den Schnitt |
| 2. | `1-ZWECK.md` | Jetzt lässt sich der Zweck ehrlich formulieren |
| 3. | `3-FORMELN.md` | Trägt die Mathematik? Wenn nicht: hier abbrechen |
| 4. | `2-FUNKTION.md` | Erst jetzt steht fest, was verarbeitet wird |
| 5. | `4-BEDIENUNG.md` | Für jede Grenze aus 7 ein Anwendersatz |
| 6. | `6-PRUEFUNG.md` | Kriterien — jedes vor dem Bau rot |
| 7. | `5-CODE/` | Zuletzt, vom Generator |

> **Warum von hinten:** Wer mit dem Zweck anfängt, schreibt auf, was das Werkzeug
> können *soll*. Wer mit den Grenzen anfängt, findet heraus, was es können *kann*.
> Der Unterschied zwischen beiden ist genau die Menge an Aufträgen, die rot werden.

## Schritt 4 — Formeln eintragen, nicht abschreiben

Fehlt eine Formel in `01-MATHEMATIK/FORMELSAMMLUNG.md`?

1. Dort eintragen, mit neuer F-Nummer
2. **Grenzfall ausfüllen** — das Feld darf nicht leer bleiben
3. Im Werkzeug nur die Nummer nennen

Eine Formel, die an zwei Orten steht, wird an einem korrigiert und am anderen
vergessen.

## Schritt 5 — Ins Register eintragen

Zeile in `REGISTER.md`: Nummer, Name, Reifegrad `LEER` → `BESCHRIEBEN`,
Abhängigkeiten, F-Nummern. **Und in die Abhängigkeitskette einzeichnen.**

## Schritt 6 — Vorlegen

Werkzeugordner ist beschrieben, aber noch nicht gebaut. Was jetzt kommt, regelt
`SKILL-auftrag-schneiden.md`.

---

## Fertig-Probe

- [ ] `7-GRENZEN.md` nennt mindestens einen Fall, den das Werkzeug nicht kann
- [ ] Für jeden dieser Fälle steht in `4-BEDIENUNG.md` ein Satz in Anwendersprache
- [ ] Jede benutzte Formel hat eine F-Nummer und einen ausgefüllten Grenzfall
- [ ] Das Werkzeug steht im Register **und** in der Abhängigkeitskette
- [ ] Kein Blatt enthält noch `<spitze Klammern>` aus der Vorlage
