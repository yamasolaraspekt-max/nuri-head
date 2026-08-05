# A-05 — Messauftrag: was fehlt wirklich zwischen L-Kontur und L-Dach?

```yaml
auftrag: A-05
titel: "MESSAUFTRAG (kein Produktivbau): welche Luecke bleibt zwischen einer L-Kontur und einem l-shape-Dach"
basis_sha: 42c0320f
status_steht_in: docs/STATUS.md   # §16: EINE Statuswahrheit. Hier steht keine zweite.
```

> **📢 Fassung 1.1/1.2.1 der ARBEITSREGELN liegt beim Plan-Prüfer (P-01).** Mitteilung und
> Kenntnisnahme in [`STATUS.md`](../../STATUS.md).

## Art des Auftrags — hier wird nichts gebaut

**Die Lieferung ist ein Bericht mit Messungen, kein Produktivcode.** `CODE_FERTIG` bedeutet hier:
Bericht liegt. Der Evaluator prüft, ob die Messungen echt und nachvollziehbar sind — nicht, ob
etwas funktioniert.

**Erlaubt:** lesen · vorhandene Tests fahren · Wegwerf-Proben außerhalb des Produktivbaums.
**Verboten:** jede Änderung in `resources/`, `app/`, `tests/`.

## Anlass

Yamas Frage (*„warum greift ihr auf playground und PV-Dachplaner nicht zurück"*) hat einen
SPEC-Befund ausgelöst: [`BEFUND-ZWEI-DACHPFADE.md`](../../BEFUND-ZWEI-DACHPFADE.md).
**A-01 baut eine Absage für L-Konturen — während die Insel `l-shape`-Dächer rendert.**

## Wiederverwendungsprüfung (§2) — die ich bei A-01 versäumt habe

```text
resources/planner/hausplaner/domain/roofShape.ts        RoofShape · istVerschneidungsForm
resources/planner/hausplaner/geometry/dachVerschneidung.ts  lTBauGueltig · verschneidungslinien
                                                        verschneidungsFlaechen   (10 Exporte)
resources/planner/hausplaner/geometry/dachUForm.ts      uBauGueltig · uFormFlaechen (10 Exporte)
resources/planner/hausplaner/renderers/three-d/dachMesh.ts:149/153/215
                                                        behandelt u-shape, l-shape, t-shape
Zusagen                                                 dachVerschneidung.test.ts ·
                                                        dachUForm.test.ts · roofShape.test.ts
docs/planner/pv-belegung-referenz/konzepte/             Fachvorgabe (219 Z.) + Umsetzungsplan
                                                        -> zielt auf die playground-App, deren
                                                        Kerndateien es hier NICHT gibt.
                                                        Fachwissen ja, Bauanleitung nein.
docs/_playground-archiv/                                65 Dach-/3D-Dateien, als Vergleich
```

## Ist-Beleg, an `42c0320f` gemessen — der Kern in zwei Zeilen

```text
app/HausplanerApp.tsx:962   roofType: 'sattel',        FEST VERDRAHTET beim Anlegen
dachMesh.ts:149/215         if (roof.roofType === 'u-shape') … istVerschneidungsForm(roof.roofType)

-> Der Anlege-Pfad setzt IMMER 'sattel', egal welche Kontur gezeichnet wurde.
   Der Renderer koennte 'l-shape' - er bekommt es nie.
```

## Die vier Fragen — jede mit einer belegbaren Antwort

> **⚠ VORMESSUNG DES GENERATORS (05.08., unaufgefordert):** `roofType` auf `l-shape` gesetzt ->
> `dachMeshWelt` liefert `{ dreiecke: [], firstHoeheMm: 2500 }`, `dachflaechen` 0 Flächen.
> **Es wirft nicht mehr, es liefert leer — ein STILLES LEERES DACH, genau der Zustand, den A-01-4
> beseitigt hat.** Damit ist A-05-1 nicht mehr eine von vier Fragen, sondern **die** Frage.
> Und für jeden Folgeauftrag gilt: **„beim Anlegen einfach die Form setzen" bringt den A-01-Fehler
> zurück**, schlimmer sogar, weil auf diesem Pfad nicht einmal die Absage greift.
>
> **Zur Wegwerf-Probe:** Er hat sie unter `__tests__` angelegt und wieder entfernt, ohne Commit,
> und das offengelegt. Meine Formulierung „außerhalb des Produktivbaums" war für eine Insel-Zusage
> unerfüllbar. **Nachgezogen:** üblicher Ort erlaubt, solange sie vor dem Bericht entfernt ist und
> kein Commit sie trägt.

**A-05-1 (jetzt die Kernfrage):** Welche Eingaben braucht `verschneidungsFlaechen` **über
`roofType` hinaus**?
`dachMesh.ts:153` baut ein `VerschneidungEingabe` aus `{...e, form}`. **Was steckt in `e`, woher
kommt es, und kann der Anlege-Pfad es aus einer gezeichneten Kontur liefern?**
*Antwortform: die Feldliste mit Herkunft je Feld.*

**A-05-2:** Sind `lTBauGueltig` / `uBauGueltig` **Erkenner oder Validierer**? Also: sagen sie
*„diese Kontur IST ein L"* oder nur *„dieses L ist baubar"*? **Gibt es im Bestand überhaupt etwas,
das die Form einer Kontur erkennt?**
*Antwortform: Signatur, ein Aufruf mit L-Kontur, das Ergebnis.*

**A-05-3:** Was passiert **heute**, wenn ein Dokument mit `roofType: 'l-shape'` geladen wird —
rendert es, oder fehlt mehr? *Antwortform: vorhandene Zusagen fahren; falls keine passt, eine
Wegwerf-Probe außerhalb des Produktivbaums.*

**A-05-4:** **Die Lückenliste.** Was genau fehlt zwischen „Nutzer zeichnet L-Kontur" und
„`l-shape`-Dach steht"? *Antwortform: nummerierte Liste, je Punkt eine Fundstelle. Auch die
Antwort „nur die Formzuweisung" ist zulässig — dann bitte mit Beleg.*

## Was NICHT Gegenstand ist

- **Kein Urteil über A-01.** A-01 läuft; ob sein Nicht-Ziel bleibt, entscheide ich mit dem Bericht.
- **Keine Empfehlung, was gebaut werden soll.** Messen, nicht planen. *Sonst schneidet der Bericht
  den nächsten Auftrag, und das ist meine Arbeit.*
- **Keine Aussage über Walm/Krüppelwalm.** Die Fachvorgabe nennt sie; sie sind hier nicht gefragt.

## Warum das ein eigener Auftrag ist und keine Nebenmessung

**Weil die Antwort A-01s Nicht-Ziel umwerfen kann**, und eine Entscheidung dieser Größe keine
Fußnote in einer Nachbesserung sein darf. *Und weil ich sonst wieder derjenige wäre, der misst und
gleich selbst entscheidet, was die Messung bedeutet.*

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle · Produktivcode   KEINE
Testdaten-Ziel                                                              KEINES
Prozessbindung                                                              entfaellt, kein Serverstart
Werkzeuge auf der Zielmaschine    npm run test:hausplaner - vorhanden und in Gebrauch
```

## Rückweg

Es entsteht nur ein Bericht. **Nichts zurückzunehmen.**
