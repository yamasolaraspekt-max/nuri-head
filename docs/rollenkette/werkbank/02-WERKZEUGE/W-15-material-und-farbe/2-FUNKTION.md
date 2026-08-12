# W-15 · Material und Farbe — FUNKTION

> **Jede Angabe auf diesem Blatt ist ZITIERT, nicht geschätzt.** *Fundstelle ist
> `resources/planner/hausplaner/app/tools/werkzeugVertrag.ts` mit Zeilennummer. Es ist das erste
> `2-FUNKTION` der Werkbank, das nicht schätzen muss — wer hier schätzt, verschenkt die Quelle.*

## Der Vertrag führt DREI Einträge, nicht vier

| Zeile | `werkzeugId` | `commandId` | Familie |
|---|---|---|---|
| `:874` | `material-aufnehmen` | `PaintCommand` | `assign-or-calculate` |
| `:886` | `material-zuweisen` | `MaterialCommand` | `assign-or-calculate` |
| `:898` | `textur` | `TextureCommand` | `assign-or-calculate` |

> **Richtigstellung gegenüber dem Auftragsblatt, gemessen:** *das Blatt W-15/1 nennt **vier**
> Werkzeuge und führt `PaintCommand` als viertes.* **`PaintCommand` ist kein Werkzeug, sondern die
> `commandId` von `material-aufnehmen`** — es steht in `:875`, direkt unter dessen `werkzeugId`.
> *Gegenprobe: `grep -c "werkzeugId: 'paint'"` → **0**; `PaintCommand` hat im ganzen Vertrag genau
> **eine** Fundstelle, und die ist `:875`.* **Drei Einträge, drei Kommandos.**

## Eingabe

| Was | Typ | Einheit | Pflicht | Prüfung | Quelle |
|---|---|---|---|---|---|
| `objectIds` | Kennungen | — | ja | `selection.count >= 1` | `:877`, `:889` |
| `parameters` | Parametersatz (Farbe) | — | ja | Vertrag nennt keine Feinprüfung | `:877` |
| `surfaceSlot` | Flächenplatz | — | ja | offen — der Vertrag nennt keine Werteliste | `:889` |
| `surfaceMaterialId` | Kennung | — | ja | offen — kein Katalog im Repo gemessen | `:889` |
| `variantId` | Kennung | — | ja | offen — desgleichen | `:889` |
| `materialAssignmentId` | Kennung | — | ja (nur `textur`) | muss aus einer vorherigen Zuweisung stammen | `:901` |
| `textureSetId` · `mapping` | Kennung · Abbildung | — | ja (nur `textur`) | offen | `:901` |

**Was hier bewusst „offen" heißt:** *der Vertrag nennt die Feldnamen, aber keine Wertebereiche und
keinen Katalog. **Ein Wertebereich, den ich hier erfände, wäre kein Entwurf mehr, sondern eine
Erfindung mit Quellenanschein.***

## Verarbeitung — der Zustandsautomat

```
Auswahl steht  ──Werkzeug waehlen──►  Material/Farbe waehlen  ──bestaetigen──►  zugewiesen
     │                                        │
     │                                        └──Esc──► Ausgangszustand, nichts geaendert
     └──Auswahl leer──► Absage (siehe 7-GRENZEN, Fall 2), Werkzeug startet nicht
```

**Zustand 1 „Auswahl steht":** *angezeigt wird die getroffene Auswahl; erwartet wird die Wahl von
Material, Farbe oder Textur. Abbruch führt zurück, ohne etwas zu ändern.*
**Zustand 2 „Wahl getroffen":** *angezeigt wird die Vorschau der Zuweisung; erwartet wird die
Bestätigung. Abbruch verwirft die Wahl, die Auswahl bleibt bestehen.*
**Fertig:** *die Zuweisung ist am Objekt, das Modell hat eine neue Revision.*

## Ausgabe

| Was | Typ | Wohin | Quelle |
|---|---|---|---|
| `assignmentOrResultIds` | Kennungsliste | Modell (`material-aufnehmen`) | `:878` |
| `materialAssignmentIds` | Kennungsliste | Modell (`material-zuweisen`) | `:890` |
| `updatedAssignmentId` | Kennung | Modell (`textur`) | `:902` |

## Kommando (für Rückgängig)

- **Name:** `PaintCommand` (`:875`) · `MaterialCommand` (`:887`) · `TextureCommand` (`:899`)
- **Ausführen:** die Zuweisung entsteht am Objekt; laut Vertrag folgen vier Seiteneffekte —
  `model.revision.increment`, `autosave.markDirty`, `dependentResults.invalidate`,
  `renderer.refreshAffectedObjects` (`:880`, `:892`, `:904`, wortgleich bei allen dreien)
- **Zurücknehmen:** `umkehrbar: true` bei allen drei Einträgen (`:881`, `:893`, `:905`).
  **Der Vertrag sagt DASS, nicht WIE** — *die Wiederherstellungsform ist in Stufe 2 zu entwerfen und
  gehört nicht in dieses Blatt.*
- **Bündelung:** *der Vertrag sagt dazu nichts.* **Offen.** *Eine Zuweisung auf zwanzig markierte
  Flächen sollte ein Rückgängig-Schritt sein und nicht zwanzig — das ist eine Entwurfsfrage für
  Stufe 2, hier benannt statt entschieden.*
- **Protokollpflichtig:** `true` bei allen dreien (`:882`, `:894`, `:906`).

## Schichtzuordnung

- **Schicht 1 (Domäne):** *ja* — die Zuweisung hängt am Objekt und erhöht die Modellrevision.
- **Schicht 2 (Geometrie):** **keine.** *Material und Farbe rechnen nicht — Begründung in
  `3-FORMELN.md`.*
- **Schicht 3 (Anwendung):** `services.material.execute(...)` — *dreimal im Vertrag genannt
  (`:883`, `:895`, `:907`), **im Repo nicht vorhanden**. Siehe `5-CODE/LIESMICH.md`.*
- **Schicht 4/5:** die belegte Fläche zeigt ihr Material; `renderer.refreshAffectedObjects` nennt
  den Auslöser, die Darstellung selbst ist noch nicht entworfen.
