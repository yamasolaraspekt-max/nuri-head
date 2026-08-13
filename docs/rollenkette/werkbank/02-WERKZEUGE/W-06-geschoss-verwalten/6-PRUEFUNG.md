# W-06 · Geschoss verwalten — PRÜFUNG

## Drei Wächter — und die ZUGRIFFSART entscheidet, ob man sie findet

**Über Importe gemessen, nicht über Dateinamen.** *Wer nach `geschoss*.test.ts` sucht, findet zwei
Dateien und übersieht die dritte.*

| Wächter | Z | Zusagen | Zugriff auf W-06 |
|---|---|---|---|
| `__tests__/geschossVorlage.test.ts` | 82 | 6 | **IMPORT** — `dupliziereGeschoss`, `LevelVorlage` (`:6`) |
| `__tests__/geschossFlaeche.test.ts` | 165 | 16 | **IMPORT + QUELLE** — s. u. |
| `__tests__/paletteNavigation.test.ts` | 178 | 15 | **IMPORT** — `stapel` (`:18`), gefüttert bei `:45` |

## Die Stelle, an der ein Import-Muster in die Irre führt

**`geschossFlaeche.test.ts` heißt nach der Komponente, importiert aber das DATENMODUL und prüft die
Komponente über ihre QUELLE:**

```text
:16   import { stapel, kurzfassung, hoehenLabel, nachbar } from '../app/dashboard/geschossStapel'
:27   const flaeche = ohneKommentare(readFileSync(… '../app/dashboard/GeschossFlaeche.tsx' …))
```

> **Wer mit einem Import-Muster nach `GeschossFlaeche` sucht, findet NULL — und schließt auf
> ungetestet.** *Gemessen ist sie **strenger** verriegelt, als ein Import es könnte:*

```text
:111  assert.equal((app.match(/<GeschossFlaeche/g) ?? []).length, 1,
                   'genau eine Geschoss-Flaeche')          -> erzwingt GENAU EINE Verwendung
:125  assert.doesNotMatch(app, /function GeschossFlaeche/,
                   'keine zweite Definition im App-Rumpf') -> schliesst eine ZWEITE Definition aus
```

**Ein Import belegt, dass etwas benutzt wird. Diese zwei Zeilen belegen, dass es genau EINMAL benutzt
und NIRGENDS ein zweites Mal definiert wird.** *Das ist die stärkere Aussage — und sie ist nur über
die Quelle zu haben.*

**Und die Quelle ist die ZERLEGTE Hauptansicht, nicht eine einzelne Datei** (`:21`, `zerlegteApp()`):
*eine Absenz-Zusage darf nicht dadurch grün werden, dass Inhalt in eine andere Scheibe umzieht.*

## Was die Wächter im Einzelnen halten

```text
geschossVorlage.test.ts   der id-REMAP: Oeffnungen haengen nach dem Kopieren an den NEUEN
                          Waenden; haengende Wirtswand -> hostWallId undefined
                          die Hoehenlage: elevation + defaultWallHeight + floorThickness
                          sortOrder + 1

geschossFlaeche.test.ts   K3  kein zweiter Geschoss-Waehler in der App
                              ('Geschoss waehlen' und das alte Textfeld sind WEG)
                          K6  Rueckgaengig/Wiederholen und 2D/Split/3D sind NICHT Teil
                              der Gruppe; die Flaeche kennt auch save() nicht
                          K6  die Flaeche steht auf MODULEBENE, nicht im App-Rumpf
                          K5  Umbenennen ueber UPDATE_LEVEL, undo-faehig
                              (mit produceWithPatches und enablePatches, :17)

paletteNavigation.test.ts der zweite Bedienweg: der Stapel als Palettenrubrik
```

> **K6 ist eine Absenz-Zusage über FREMDE Begriffe** — *sie prüft, dass `undo()`, `redo()`,
> `setModus`, `kannUndo`, `kannRedo` und `save()` in der Fläche **nicht** vorkommen.* **Das ist die
> Verriegelung des AUF-43-Befundes:** vier unabhängige Aufgaben lagen in einer Gruppe, und diese
> Zusage sorgt dafür, dass sie nicht zurückwandern.

## Was NICHT geprüft wird

- **Kein Browser-Nachweis in diesen Wächtern.** *Sie messen Modell und Quelltext; wie die Fläche
  aussieht, entscheidet eine Browserabnahme.*
- **Die Zahl `34 der 110 entsperrten Werkzeuge`** aus dem Dateikopf ist **hier nicht nachgemessen** —
  sie steht als zitierter Befund, nicht als geprüfte Zusage. *Der Nenner ist inzwischen 111 (A-29).*
