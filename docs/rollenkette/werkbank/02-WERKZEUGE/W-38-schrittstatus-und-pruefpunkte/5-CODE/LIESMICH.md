# W-38 · CODE

## Wo der Code wirklich lebt

| Schicht | Datei im Repo | Zweck |
|---|---|---|
| 1 Domäne | — | **keine** |
| 2 Geometrie | — | **keine** |
| 3 Anwendung | `resources/planner/hausplaner/app/studioDaten.ts` **(257 Z.)** | **der ganze Bestand von W-38** |
| 4 Darstellung | — | **keine** |
| 5 Oberfläche | — | **keine** (die vier Wörter zeichnet `app/GuidedView.tsx`, W-34) |

> **Ein Werkzeug in genau einer Datei, und diese Datei gehört ihm nicht allein.** *`studioDaten.ts`
> trägt außerdem die Design-Tokens `T`, `TREPPE_FARBEN`, `FARBEN`, `OP_TOKEN`, `FACH`, `PROJ` — die
> gehören **nicht** zu W-38.* **W-38 ist Zeile 143-174 und 245-257, plus die stillgelegte
> Konstante ab 157.**

## Schnittstelle

```ts
// studioDaten.ts:163 — die vier Stufen
export type SchrittStatus = 'ok' | 'prog' | 'warn' | 'open';

// :164-166 — die drei einfachen Datenformen
export interface Pruefpunkt { status: SchrittStatus; text: string; }
export interface Aufgabe    { warn?: boolean; titel: string; detail?: string; }
export interface Empfehlung { titel: string; aktion: string; cfg?: boolean; }

// :167-174 — die zusammengesetzte Form
export interface Fahrschritt {
  titel: string; status: SchrittStatus; hinweis: string;
  checks: Pruefpunkt[]; aufgaben: Aufgabe[]; empfehlung: Empfehlung | null;
}

// :255-257 — die Beschriftungen
export const STATUS_LABEL: Record<SchrittStatus, string>;

// :143 / :157 — die stillgelegte Startbildschirm-Attrappe
export interface ZuletztEintrag { name: string; meta: string; icon: string; goto?: number; win?: boolean; }
export const ZULETZT_STILLGELEGT: readonly ZuletztEintrag[];

// :186 — die stillgelegte Schritt-Attrappe
export const STEPS_STILLGELEGT: readonly Fahrschritt[];
```

## Kernstelle

```ts
// studioDaten.ts:255-257 — die einzige Stelle, an der ein Zustand zu einem Wort wird
export const STATUS_LABEL: Record<SchrittStatus, string> = {
  ok: 'Vollständig', prog: 'In Bearbeitung', warn: 'Prüfung erforderlich', open: 'Offen',
};
```

**`Record<SchrittStatus, string>` ist die Kernstelle und nicht das Objekt darunter:** *sie zwingt
jede neue Stufe dazu, sofort ein deutsches Wort mitzubringen.* **Ohne sie könnte eine fünfte Stufe
entstehen, die im Bildschirm als `undefined` erscheint.**

## Die Nutzer je Typ — mit Trefferzeile

> **Gemessen am 12.08. mit `grep -rn '\b<Typ>\b' --include='*.ts' --include='*.tsx'` über
> `resources/planner/hausplaner`, dann jede Trefferzeile GELESEN.** *Das Lesen war nötig — vier der
> Treffer waren Fließtext.*

**`SchrittStatus` — 3 Dateien**

```text
app/GuidedView.tsx:4     import { T, STATUS_LABEL, type SchrittStatus, type Fahrschritt }
app/GuidedView.tsx:18    const badgeFarbe: Record<SchrittStatus, { bg: string; fg: string }>
app/GuidedView.tsx:22    const checkFarbe: Record<SchrittStatus, { bg; fg; sym }>
app/dashboard/fahrschritte.ts:27   import type { Fahrschritt, Pruefpunkt, SchrittStatus }
app/dashboard/fahrschritte.ts:43   export function statusAus(checks: readonly Pruefpunkt[]): SchrittStatus
__tests__/gefuehrteEhrlich.test.ts:23  import … type SchrittStatus
__tests__/gefuehrteEhrlich.test.ts:47  const s: SchrittStatus = statusAus([…])
```

**`Pruefpunkt` — 1 Datei**

```text
app/dashboard/fahrschritte.ts:27    import type { … Pruefpunkt … }
app/dashboard/fahrschritte.ts:35    const erfuellt = (text: string): Pruefpunkt => ({ status: 'ok', text })
app/dashboard/fahrschritte.ts:37    const offen    = (text: string): Pruefpunkt => ({ status: 'open', text })
app/dashboard/fahrschritte.ts:43    statusAus(checks: readonly Pruefpunkt[])
app/dashboard/fahrschritte.ts:113   titel: string, hinweis: string, checks: Pruefpunkt[],
```

**`Fahrschritt` — 2 Dateien**

```text
app/GuidedView.tsx:4      import … type Fahrschritt
app/GuidedView.tsx:15     schritte: readonly Fahrschritt[];
app/dashboard/fahrschritte.ts:27   import type { Fahrschritt … }
app/dashboard/fahrschritte.ts:74   export function ableitenSchritte(scene: SceneDocument | null): Fahrschritt[]
app/dashboard/fahrschritte.ts:114  aufgaben: Fahrschritt['aufgaben'] = [], empfehlung: Fahrschritt['empfehlung']
app/dashboard/fahrschritte.ts:115  ): Fahrschritt => ({ titel, status: statusAus(checks), … })
app/dashboard/fahrschritte.ts:118  const ohneGrundlage = (titel: string, zusatz = ''): Fahrschritt =>
```

**`STATUS_LABEL` — 2 Dateien**

```text
app/GuidedView.tsx:4      import { T, STATUS_LABEL, … }
app/GuidedView.tsx:71     …>{STATUS_LABEL[s.status]}</span>
__tests__/gefuehrteEhrlich.test.ts:23, :38, :42, :43, :44, :45
```

**`Aufgabe` und `Empfehlung` — KEIN Nutzer außerhalb `studioDaten.ts`**

```text
Aufgabe      studioDaten.ts:165  Definition
             studioDaten.ts:172  aufgaben: Aufgabe[]      <- der einzige Verwender
Empfehlung   studioDaten.ts:166  Definition
             studioDaten.ts:173  empfehlung: Empfehlung | null   <- der einzige Verwender
```

> **Beide werden ausschließlich ÜBER `Fahrschritt` benutzt.** *`fahrschritte.ts:114` greift sie mit
> `Fahrschritt['aufgaben']` und `Fahrschritt['empfehlung']` ab — indizierter Zugriff, der die Typen
> benutzt, ohne ihre Namen zu nennen.* **Das ist kein Mangel, sondern die Bauform: `Aufgabe` und
> `Empfehlung` sind Bestandteile, keine eigenständigen Schnittstellen.**

### Vier Fehltreffer, die ich beim Lesen verworfen habe

```text
app/GuidedView.tsx:119            {/* Seitenpanel: Aufgabe + Empfehlung + … */}   Kommentar
geometry/sparrenBerechnung.ts:30  // L/300 (Empfehlung Endzustand, …)             Kommentar
__tests__/enginePanelTgaHeizung.test.ts:65  'FBH: ein Fall mit verletztem
                                             Pruefpunkt bleibt …'                 Testname
__tests__/enginePanelRest.test.ts:73        'ein verletzter Pruefpunkt bleibt …'  Testname
```

> **Und einer davon steht im Auftragsblatt:** *dessen Erhebung nennt
> `app/dashboard/enginePanels.ts` als `Pruefpunkt`-Nutzer.* **Gemessen: diese Datei nennt
> `studioDaten` an keiner Stelle und importiert nichts daraus.** *Ihr einziger Treffer ist
> `enginePanels.ts:235`, ein deutscher Anzeigetext:*
> `grundlage: 'Auslegung nach Verlegeabstand … Pruefpunkte zu Leistung …'`
>
> **H-6, wörtlich: ein Wort ist kein Beleg; erst die Stelle ist einer.** *Die beiden dort genannten
> Testdateien sind derselbe Fall — der Wort im Testnamen, nicht der Typ.*

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| **nichts** | die Datei importiert kein Modul der Insel | **ja** — kein Kreis möglich, weil keine Kante hinausgeht |

> **W-38 ist eine Senke im Abhängigkeitsgraphen.** *Genau deshalb kann `braucht: alle` in seiner
> Registerzeile keine Vorbedingung sein: es hängt von nichts ab, es wird von vielem benutzt.*
