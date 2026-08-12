# W-35 · CODE

## Wo der Code wirklich lebt

| Schicht | Datei im Repo | Zweck |
|---|---|---|
| 4/5 Oberfläche | **`app/ConfigWizard.tsx`** · **271 Zeilen** | **der ganze Dialog** — Typen, Katalogwahl, Zustände, fünf Schritte, Fuß, die vier Ausgänge |
| — | *keine weitere* | **W-35 lebt in EINER Datei** |

**Am Bau-Stand gemessen:** `wc -l` → **271**.

## Die Landkarte der Datei

```text
:1-7     Dateikopf      — traegt ZWEI ueberholte Aussagen, siehe unten
:8-19    zwoelf import-Zeilen
:21      ICON_BASE      — Wurzel der Bauart-Bilder, aus import.meta.url
:23      KonfigArt      — die VIER Arten
:25-30   Props          — art · standalone? · onClose · onÜbernehmen
:32      Kachel         — id · datei · label
:34      SCHRITTE       — die fuenf, as const
:36-41   katalogFür     — Art -> { ordner, titel, kacheln }   OHNE Typschutz
:43      TYP_MAP        — Art -> ConfiguratorType             MIT Typschutz
:45-58   der Kopf der Komponente: vier Zustaende, iconUrl, letzter, Dialogfokus
:59-93   Huelle, Kopfzeile, Schrittpunkte
:96-160  Koerper: die fuenf Schritte, daneben die Vorschau
:163-267 Fuss: Statuszeile, Zurueck, und der Uebernehmen-Knopf mit VIER Ausgaengen
```

## Die Kernstelle: ein Knopf, vier Ausgänge

```ts
:167-265   onClick des Weiter/Übernehmen-Knopfes
  :168     if (!letzter) { setSchritt(schritt + 1); return; }     ← solange nicht am Ende
  :171-172 const store = useHausplanerStore.getState();  const scene = store.scene;
  :174     if (art === 'heizkoerper' && scene) { … :184 ADD_NODE … return; }
  :190     if (art === 'treppe'      && scene) { … :205 ADD_NODE … return; }
  :211     if ((art === 'fenster' || art === 'tuer') && scene) { … :226 ADD_NODE … return; }
  :232     const paket = neuesPaket({ … type: TYP_MAP[art] … });   ← der Rückfall
```

**Jeder der ersten drei Zweige endet mit `return`** — *sie schließen sich gegenseitig aus, und der
vierte ist alles Übrige.* **Der Zustand `scene` entscheidet, nicht der Anwender.**

> **Die drei `ADD_NODE`-Stellen gehören W-42** und stehen hier nur als Wegweiser. *Was jeder Zweig
> baut, welche Felder er setzt und was bei `ok === false` geschieht, steht in W-42s Blättern.*

## Der Dateikopf ist ÜBERHOLT — an zwei Stellen

```text
:2   „geführter Konfigurator-Dialog für Fenster/Tür/Treppe."
     -> DREI Arten. :23 traegt VIER. heizkoerper fehlt.

:5-6 „Der Schreibpfad ins Gebäudemodell (Command) bleibt die nächste Scheibe."
     -> gemessen: grep -n ADD_NODE ConfigWizard.tsx
        :184 radiator · :205 treppe · :226 knoten   (dazu :210 als Kommentar)
        Der Schreibpfad IST gebaut. W-42 hat ihn abgelesen und ist ABGENOMMEN.
```

> **Beide Aussagen bleiben unangetastet, und zwar mit Absicht.** *W-42 hat `:6` ausdrücklich als
> überholt benannt und stehen gelassen, mit dem Satz „eine Ablesung ändert ihre Quelle nicht".*
> **Dasselbe gilt hier für `:2` — dieses Blatt ändert keine Zeile Produktivcode.**
>
> **Der Grund, warum der Kopf trotzdem in diesem Blatt steht:** *die Registerzeile 122 trug **beide**
> Fehler — „Fenster·Tür·Treppe" und „schreibt NICHT ins Gebäudemodell".* **Sie hat sie vom Dateikopf
> geerbt, denn sie stammt aus einer Erhebung, die den Kopf gelesen hat.** *Ein überholter Dateikopf
> ist keine Kleinigkeit: er ist das Erste, was jeder liest, und er pflanzt sich in jede Ablesung
> fort.*

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| `geometry/oeffnungsBauarten` | `FENSTER_BAUARTEN`, `TUER_BAUARTEN`, `fensterBauartNach` | **einseitig** — W-35 einführt, nicht umgekehrt |
| `geometry/treppenBauarten` · `geometry/heizkoerperTypen` | die beiden anderen Kataloge | einseitig |
| `geometry/configuratorPackage` | `neuesPaket`, `ConfiguratorType` — **der Anschluss an W-40** | einseitig |
| `geometry/treppeObjekt` | `treppeZuParametern` für den Treppen-Zweig | einseitig, **gehört zu W-42s Zweig** |
| `store/hausplanerStore` | `getState`, `executeCommand`, `selectedNodeIds`, `activeLevelId` | einseitig, **W-42s Gegenstand** |
| `app/dashboard/dialogFokus` | `useDialogFokus`, `istAusloeser` — die gemeinsame Dialogregel | einseitig |
| `app/state/paketSpeichern` | `speicherePaket`, `kannPaketSpeichern` | einseitig |
| `app/studioDaten` · `app/studioUi` | `T` (Farbmarken) und `Ikon` | einseitig |

**W-04 (Öffnung) und W-09 (Treppe) liefern die FACHLOGIK, W-35 die BEDIENUNG** — *beide sind
`BESCHRIEBEN`, und der Dialog kennt sie nur über die Bauartenkataloge.*
