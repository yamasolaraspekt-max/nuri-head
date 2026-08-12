# W-34 · CODE

## Wo der Code wirklich lebt

| Schicht | Datei im Repo | Zweck |
|---|---|---|
| 1 Domäne | `domain/scene.types` | **nur gelesen** — `SceneDocument`, `SceneNode` (`fahrschritte.ts:26`) |
| 2 Geometrie | — | **keine** |
| 3 Anwendung | `app/dashboard/fahrschritte.ts` **(202 Z.)** | die Ableitung: `statusAus`, `SCHRITTE_OHNE_GRUNDLAGE`, `ableitenSchritte`, `schrittTitel` |
| 4/5 Oberfläche | `app/GuidedView.tsx` **(165 Z.)** | Stepper, Fokus-Schrittkarte, Aufgaben-Panel, Navigation |

**Zwei Dateien, und beide gehören W-34 ganz** — *anders als bei W-38, das sich `studioDaten.ts` mit
den Design-Tokens teilt.*

## Schnittstelle

```ts
// fahrschritte.ts:43 — die Regel
export function statusAus(checks: readonly Pruefpunkt[]): SchrittStatus;

// :56 — die sechs Luecken, als Record Titel -> was fehlt
export const SCHRITTE_OHNE_GRUNDLAGE: Readonly<Record<string, string>>;

// :74 — die elf Schritte aus dem Dokument
export function ableitenSchritte(scene: SceneDocument | null): Fahrschritt[];

// :200 — nur die Titel, feste Reihenfolge
export function schrittTitel(): string[];

// GuidedView.tsx:27
export function GuidedView(props: Props): React.ReactElement;
//   Props.schritte: readonly Fahrschritt[]   <- die Ableitung kommt als DATEN herein
```

## Kernstelle

```ts
// fahrschritte.ts:118-122 — ein Schritt ohne Modellgrundlage
const ohneGrundlage = (titel: string, zusatz = ''): Fahrschritt => schritt(
  titel,
  `${SCHRITTE_OHNE_GRUNDLAGE[titel]}${zusatz ? ` ${zusatz}` : ''}`,
  [],
);
```

**Die leere Prüfpunktliste ist die Kernstelle, nicht der Titel:** *sie geht in `statusAus`, und
`checks.length === 0` ergibt `open` (`:44`).* **Der Zustand „offen" entsteht also nicht durch eine
Zuweisung, sondern dadurch, dass es nichts zu prüfen gibt** — *eine Behauptung ist gar nicht erst
formulierbar.*

## Der Anschluss an W-38 — mit Fundstelle

```ts
GuidedView.tsx:4
import { T, STATUS_LABEL, type SchrittStatus, type Fahrschritt } from './studioDaten';
```

```text
STATUS_LABEL    GuidedView.tsx:71    …>{STATUS_LABEL[s.status]}</span>
SchrittStatus   GuidedView.tsx:18    const badgeFarbe: Record<SchrittStatus, { bg; fg }>
                GuidedView.tsx:22    const checkFarbe: Record<SchrittStatus, { bg; fg; sym }>
Fahrschritt     GuidedView.tsx:15    schritte: readonly Fahrschritt[];

und auf der Ableitungsseite:
fahrschritte.ts:27
import type { Fahrschritt, Pruefpunkt, SchrittStatus } from '../studioDaten';
```

> **W-34 benutzt vier Namen aus W-38 und definiert keinen davon neu.** *`badgeFarbe` und
> `checkFarbe` sind `Record<SchrittStatus, …>` — sie **erzwingen**, dass alle vier Stufen aus W-38
> eine Darstellung haben.* **Benutzen ist nicht besitzen: `studioDaten.ts` gehört W-38 und wurde
> für W-34 nicht angefasst.**

**Die Richtung ist einseitig und geprüft:** *W-34 → W-38. Es gibt keinen Import von `fahrschritte`
oder `GuidedView` in `studioDaten.ts` — jene Datei importiert nichts (0 `import`-Zeilen, in W-38
gemessen).* **Kein Kreis möglich.**

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| **W-38** `app/studioDaten.ts` | die vier Stufen, `Pruefpunkt`, `Fahrschritt`, `STATUS_LABEL` | **ja** — W-38 importiert nichts, kein Kreis |
| `domain/scene.types` | `SceneDocument`, `SceneNode` — nur als Typ gelesen | **ja** — reine Typ-Importe |
| `app/dashboard/dialogFokus` | `istAusloeser` für Tastaturbedienung (`GuidedView.tsx:3`) | **ja** |
| `app/ConfigWizard` | `KonfigArt` als Typ (`GuidedView.tsx:5`) — *nur die Art, nicht der Dialog* | **ja** |
| `app/studioUi` | `Ikon` (`GuidedView.tsx:6`) | **ja** |

> **`ConfigWizard` steht hier als Typ-Import und ist trotzdem erwähnenswert:** *W-34 kennt die
> **Art** eines Konfigurators, um ihn anzustoßen — es kennt seinen Inhalt nicht.* **Der
> Konfigurator selbst ist W-35.**
