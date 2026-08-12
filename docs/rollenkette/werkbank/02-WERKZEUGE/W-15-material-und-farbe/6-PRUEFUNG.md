# W-15 · Material und Farbe — PRÜFUNG

> **Regel: jedes Kriterium muss VOR dem Bau wirksam rot sein.**
> Ein Kriterium, das schon grün ist, bevor gebaut wurde, prüft nichts.

**Diese Kriterien gelten für STUFE 2 (den Bau).** *Stufe 1 ist der Entwurf; ihre eigenen Kriterien
stehen im Auftragsblatt `W-15-material-und-farbe.md`. **Alle sechs unten sind heute rot, weil es
keinen Code gibt — der Rot-Beleg ist deshalb bei jedem derselbe und trotzdem echt.***

## Abnahmekriterien

| Nr | Kriterium | Rot-Beleg vor dem Bau | Wie gemessen |
|---|---|---|---|
| K-1 | `services.material` existiert und wird von den drei Vertragseinträgen erreicht | `grep -rn 'services\.material' resources/` → **3 Treffer, alle im Vertrag**, 0 Implementierung | derselbe Befehl, danach > 0 außerhalb des Vertrags |
| K-2 | Eine Zuweisung auf **mehrere** markierte Flächen ist **ein** Rückgängig-Schritt | kein Kommando vorhanden — `grep -c 'MaterialCommand' resources/` → **1**, und das ist der Vertrag | Zusage: zwei Flächen belegen, einmal zurücknehmen, beide sind zurück |
| K-3 | Die drei Vorbedingungen führen zu **benannten** Absagen, nicht zu stillem Nichts | keine Absage vorhanden | je eine Zusage pro Fall aus `7-GRENZEN.md` |
| K-4 | Der Anwendertext jeder Absage erreicht die Oberfläche | keine Oberfläche vorhanden | Zusage, die den Text aus `4-BEDIENUNG.md` an der Oberfläche prüft — nicht den Fehlernamen |
| K-5 | `textur` ohne vorherige Materialzuweisung wird abgewiesen | `TextureCommand` → **1 Treffer**, der Vertrag | Zusage mit leerer `materialAssignmentId` |
| K-6 | Die vier Seiteneffekte treten ein — Revision, Autosave, Invalidierung, Auffrischung | nichts davon verdrahtet | je eine Zusage; `dependentResults.invalidate` ist die wichtigste, weil sonst alte Ergebnisse stehen bleiben |

## Fangprobe (Mutationsprobe)

*Ohne sie prüfen die Kriterien womöglich nichts — das ist an A-13 belegt, wo acht Zusagen einen
entfernten Wächter überlebten, weil keine von ihnen speicherte.*

| Mutation | Muss erkannt werden von |
|---|---|
| `vorbedingungen`-Prüfung entfernen (Zuweisung ohne Auswahl erlaubt) | **K-3** |
| Absage werfen, aber im Renderer mit `catch { continue; }` schlucken | **K-4** — genau der teuerste Fehler des Projekts, siehe `7-GRENZEN.md` |
| Zuweisung je Fläche als eigenes Kommando ablegen | **K-2** |
| `dependentResults.invalidate` weglassen | **K-6** |

> **K-4 ist die Fangprobe, auf die es ankommt.** *Ein Werkzeug, dessen Absage unterwegs verschluckt
> wird, verhält sich für den Anwender wie ein Werkzeug, das nichts tut — und genau so ist der
> Dach-Vorfall entstanden.*

## Automatische Tests

| Datei | Prüft |
|---|---|
| `resources/planner/hausplaner/__tests__/materialZuweisung.test.ts` *(neu in Stufe 2)* | K-1, K-2, K-5, K-6 |
| `resources/planner/hausplaner/__tests__/materialAbsagen.test.ts` *(neu in Stufe 2)* | K-3, K-4 |

*Die Dateinamen sind Vorgabe, nicht Ablesung — heute existiert keine von beiden.*

## Sichtprüfung (falls die Oberfläche betroffen ist)

- [ ] 1440 px
- [ ] 1024 px
- [ ] 375 px
- [ ] Meldung bei Absage lesbar und vollständig sichtbar

*Betrifft Stufe 2: das Werkzeug bekommt eine Auswahlliste und eine Vorschau.*

## Bestandsprobe

- [ ] Ein vor der Änderung gespeichertes Dokument lädt danach unverändert

> **Hier besonders wichtig, und der Grund steht im Vertrag:** *`model.revision.increment` und
> `autosave.markDirty` (`werkzeugVertrag.ts:892`) fassen den Bestand an.* **Ein Werkzeug, das die
> Revision erhöht, muss beweisen, dass ältere Dokumente ohne Materialzuweisung weiterhin laden.**
