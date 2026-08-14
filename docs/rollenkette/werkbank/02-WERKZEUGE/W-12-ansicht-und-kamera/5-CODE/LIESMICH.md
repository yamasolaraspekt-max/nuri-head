# W-12 · Ansicht und Kamera — CODE

**W-12 hat KEIN eigenes Modul.** *Seine vier Gegenstände liegen verteilt in Dateien, die anderen
Werkzeugen gehören — und genau das ist die Aussage: die Ansicht ist eine **Eigenschaft** des
Programms und kein Werkzeug (siehe `7-GRENZEN`).*

## Wo W-12 wirklich sitzt — am Bau-Stand erhoben

| Gegenstand | Datei | Stellen |
|---|---|---|
| **Ansichtszustand** | `store/hausplanerStore.ts` | `:20` Typ · `:28` Feld · `:45` Schnittstelle · `:126` Rumpf |
| **Kamera + Steuerung** | `renderers/three-d/szene.ts` (696 Z) | `:23` Einfuhr · `:100`/`:101` Felder · `:170` Kamera · `:178` `OrbitControls` |
| **Raster 3D** | `renderers/three-d/szene.ts` | `:212-215` `GridHelper(80, 80, …)` |
| **Raster 2D** | `app/HausplanerApp.tsx` | `:349` Zustand · `:1261-1269` Erzeugung · `:1337`/`:1409` Durchreichung |
| | `app/dashboard/Kopfrahmen.tsx` | `:290-292` Ansichtsknöpfe · `:304` Rasterknopf |
| | `app/rahmen/Buehne.tsx` | **`:146`** Zeichenstelle `{rasterAn && rasterLinien}` |
| **F-032** | `renderers/three-d/szene.ts` | `:621` `makeBasis` · `:627` `applyMatrix4` |

> ***`Buehne.tsx:62` ist KEIN Beleg*** — *dort steht `rasterAn: boolean;`, die Props-Typzeile.*
> **Ein Typeintrag beweist nicht, dass gezeichnet wird.** *Der Beleg ist `:146`.*

## Die zwei `modus` — der Grund, warum dieses Blatt zuerst die Träger nennt

```text
HausplanerModus  store/hausplanerStore.ts:20    '2d' | 'split' | '3d'
                 setModus  :45 (Schnittstelle) · :126 (Rumpf, eine Zeile: set({ modus }))

StudioModus      app/studioDaten.ts:97          'start' | 'guided' | 'expert'
                 setModus  app/HausplanerStudio.tsx:23  — ein React-useState, KEIN Store
```

**Gemessen: `setModus` kommt an zwei Stellen als Setzer vor, und sie gehören verschiedenen
Zuständen.** *Wer den falschen greift, schaltet nichts oder das Falsche.*

## Der Hygiene-Posten, benannt und nicht angefasst

**`app/state/uiState.ts:11`** *(99 Z.)* führt wörtlich:

> *„(Rename `modus→viewMode` ist ein eigener Hygiene-Slice.)"*

**Und `:10` sagt, warum der Zustand dort NICHT liegt:** *„Ansicht (2d/split/3d) bleibt im
Modell-Store (`modus`) — die Activation-Engine liest sie von dort."*

> **Dieses Blatt nennt den Posten und ändert nichts.** *Eine Umbenennung an dieser Stelle wäre ein
> eigener Vorgang mit eigener Reichweite.*

## Kein eigener Befehl

**`setModus` schreibt mit `set({ modus })` direkt in den Store** — *kein `executeCommand`, kein
Patch, keine Historie.* **Die Ansicht ist kein Dokumentzustand.**
