# W-15 · Material und Farbe — GRENZEN

> **Dieses Blatt ist Pflicht.**
> Der teuerste Fehler des Projekts bisher: ein Dach, das bei nicht-rechteckiger
> Kontur unsichtbar verschwand statt eine Absage zu geben. Die Domäne verweigerte
> korrekt — der Renderer schluckte die Absage mit `catch { continue; }`.
> **Ein Werkzeug ohne benannte Grenze baut genau diesen Fehler wieder ein.**

## Was dieses Werkzeug NICHT kann

| Fall | Warum nicht | Was der Anwender stattdessen sieht |
|---|---|---|
| **Kein Projekt offen** | Vorbedingung `project.open` (`werkzeugVertrag.ts:891`) | Absage 1 aus `4-BEDIENUNG.md` — mit dem Weg „Projekt öffnen oder anlegen" |
| **Nichts ausgewählt** | Vorbedingung `selection.count >= 1` (`:891`) — es gibt keine Fläche, der etwas zugewiesen werden könnte | Absage 2 — „wähle zuerst mindestens eine Fläche" |
| **Keine Bearbeitungsrechte** | Vorbedingung `permission.edit` (`:891`); die Zuweisung ändert das Modell | Absage 3 — „das Projekt darf von dir nicht bearbeitet werden" |
| **Textur ohne Material** | `textur` verlangt eine `materialAssignmentId` (`:901`) — eine Textur liegt auf einem Material, nicht auf einer nackten Fläche | Absage 4 — „weise zuerst ein Material zu" |
| **Mengen ermitteln** | nicht dieses Werkzeug — W-20 und W-23 | kein Fehler: das Werkzeug wird dafür gar nicht erst angeboten |
| **Dachdeckung wählen** | W-23 „Deckung und Material" (`REGISTER.md:60`) | dito |

## Die Absagekette

Für jeden Fall oben muss die Kette vollständig sein:

```
Schicht 1/2 wirft benannten Fehler
        ↓
Schicht 3 fängt und übersetzt
        ↓
Schicht 4 reicht DURCH — kein catch/continue
        ↓
Schicht 5 zeigt dem Anwender einen verständlichen Satz
```

| Fall | Fehlername *(Vorgabe)* | Wer fängt | Anwendertext steht in |
|---|---|---|---|
| Kein Projekt offen | `KeinProjektOffen` | Schicht 3 (`services.material`) | `4-BEDIENUNG.md` |
| Nichts ausgewählt | `AuswahlLeer` | Schicht 3 | `4-BEDIENUNG.md` |
| Keine Rechte | `KeineBearbeitungsrechte` | Schicht 3 | `4-BEDIENUNG.md` |
| Textur ohne Material | `MaterialzuweisungFehlt` | Schicht 3 | `4-BEDIENUNG.md` |

*Die vier Fehlernamen sind **Vorgabe für Stufe 2**, keine Ablesung — im Repo existiert keiner von
ihnen. Sie stehen hier, damit Stufe 2 sie nicht erfindet und damit `6-PRUEFUNG.md` K-3 und K-4
darauf zeigen können.*

## Fänger-Prüfung

- [ ] Jeder Fehlerpfad ist durch einen Test belegt, der prüft:
      **die Meldung erreicht die Oberfläche**
- [ ] Kein `catch { }` ohne Weiterreichen im Pfad dieses Werkzeugs
- [ ] Kein stilles `return` bei ungültiger Eingabe

> **Für Stufe 2 verbindlich, und aus einem gemessenen Fall heraus:** *die Absage darf nicht am
> Renderer enden. Beim Dach-Vorfall war die Domäne korrekt — die Kette war es nicht.* **Ein Test,
> der nur prüft „es wurde geworfen", hätte auch damals grün gezeigt.**

## Bekannte Ungenauigkeiten

| Größe | Abweichung | Ab wann stört es |
|---|---|---|
| — | — | *keine — das Werkzeug rechnet nicht (`3-FORMELN.md`)* |

## Was später kommen könnte

**Absichtlich weggelassen, damit es nicht als Fehler gemeldet wird:**

- **Materialkatalog mit Herstellerdaten.** *Woher `surfaceMaterialId` und `variantId` stammen, ist
  ungemessen — im Repo ist kein Katalog gefunden worden. Ohne ihn ist die Zuweisung eine Kennung
  ohne Nachschlagewerk.*
- **Mengenermittlung aus der Zuweisung.** *Gehört zu W-20/W-23; heute verbraucht **niemand** die
  Zuweisung — `grep -rn 'surfaceMaterialId|materialAssignment' resources/` ohne den Vertrag →
  **0 Treffer**.*
- **Bündelung mehrerer Zuweisungen zu einem Rückgängig-Schritt.** *Der Vertrag sagt dazu nichts;
  `6-PRUEFUNG.md` K-2 macht daraus eine Vorgabe statt einer Annahme.*
- **Darstellung des Materials in der Ansicht.** *Der Vertrag nennt nur den Auslöser
  (`renderer.refreshAffectedObjects`), nicht die Darstellung.*
