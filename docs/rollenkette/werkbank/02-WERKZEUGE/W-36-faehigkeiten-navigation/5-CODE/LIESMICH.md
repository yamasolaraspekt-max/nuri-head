# W-36 · CODE

## Wo der Code wirklich lebt

| Schicht | Datei im Repo | Zweck |
|---|---|---|
| 3 Anwendung | **`app/tools/faehigkeiten.ts`** · **129 Zeilen** | **die eine Registry** — drei Typachsen, die Liste, vier Funktionen |
| 4/5 Oberfläche | **`app/FaehigkeitenNavi.tsx`** · **76 Zeilen** | **die Fläche** — ein Export, rein datengetrieben |

**Am Bau-Stand gemessen:** `wc -l` → **129** und **76**.

## Die Landkarte von `faehigkeiten.ts`

```text
:1-13    Dateikopf — nennt DREI Quellen und die Byte-Treue-Regel.
         Zwei Aussagen darin sind ueberholt (7-GRENZEN).
:15      import { TOOL_DEFINITIONS } from './toolRegistry'   die EINZIGE Einfuhr
:17-19   FaehigkeitGruppe    NEUN Gruppen
:22      FaehigkeitArt       'werkzeug' | 'aktion' | 'engine'
:24      der Kommentar mit 'aktiv' und 'schlaeft'  — W-36-2
:25      FaehigkeitZustand   VIER Zustaende
:27-45   Faehigkeit          mit den ARTABHAENGIGEN Feldern
:46      FAEHIGKEIT_GRUPPEN  export const, neun Gruppen mit Label
:59      WERKZEUG_GRUPPE     const, MODULINTERN — Werkzeug-id -> Gruppe
:63-71   Quelle 1  werkzeugFaehigkeiten  = TOOL_DEFINITIONS.map(…)
:73-88   Quelle 2  engineFaehigkeiten    = 13 Engines, von Hand
:91-96   Quelle 3  werkzeugKatalogFaehigkeiten = []   LEER seit I4
:99-103  FAEHIGKEITEN  readonly, die drei Quellen gespreizt
:106-129 die vier exportierten Funktionen
```

**EINE Einfuhr, und das ist eine Aussage:** *`faehigkeiten.ts` kennt nur `toolRegistry`.* **Es kennt
weder die Navi noch `studioUi` noch ein `geometry/`-Modul** — *die dreizehn Engines werden als
**Zeichenketten** referenziert (`engineModul: 'geometry/…'`), nicht importiert.*

> **Genau deshalb braucht es den Guard-Test.** *Eine Zeichenkette lässt sich nicht übersetzen, nur
> nachschlagen* — **und `faehigkeiten.test.ts:38` schlägt sie zur Laufzeit nach, mit `await
> import()`.** *Was der Compiler hier nicht kann, holt ein Test nach.*

## Die Kernstelle: drei Quellen, eine Liste

```ts
:99-103
export const FAEHIGKEITEN: readonly Faehigkeit[] = [
  ...werkzeugFaehigkeiten,        // aus TOOL_DEFINITIONS gerechnet
  ...engineFaehigkeiten,          // 13, von Hand gepflegt
  ...werkzeugKatalogFaehigkeiten, // [] — leer seit I4
];
```

**Die erste Quelle ist gerechnet, die zweite gepflegt, die dritte leer.** *Das ist der ganze Bau.*

```ts
:63-71
const werkzeugFaehigkeiten: Faehigkeit[] = TOOL_DEFINITIONS.map((t) => ({
  id: t.id, label: t.label,
  gruppe: WERKZEUG_GRUPPE[t.id] ?? 'werkzeuge',   // Rückfall
  art: t.art,
  zustand: 'verfuegbar',                          // FEST
  funktion: t.helpText,
  toolId: t.id,
}));
```

> **Zwei Stellen ohne Schutz, beide am Code ablesbar:**
>
> 1. **`WERKZEUG_GRUPPE[t.id] ?? 'werkzeuge'`** — *ein neues Werkzeug ohne Eintrag landet
>    stillschweigend in „werkzeuge".* **Kein Fehler, keine Warnung, falsche Rubrik.** *Dieselbe
>    Bauform wie `katalogFür` in W-35: ein Rückfall statt eines Typschutzes.*
> 2. **`zustand: 'verfuegbar'` ist FEST verdrahtet.** *Jedes Werkzeug aus `TOOL_DEFINITIONS` gilt als
>    verfügbar — es gibt keinen Weg, ein Werkzeug als `in_entwicklung` zu führen.*

## `FaehigkeitenNavi.tsx` — die Fläche

```text
:1-9     Dateikopf, mit dem Token-Scope: „AUSSCHLIESSLICH T.* aus studioDaten —
         kein Hex/rgba in dieser Datei (Hex lebt nur in studioDaten.ts)"
:11-13   drei Einfuhren: React · T · ZustandBadge
:14      FAEHIGKEIT_GRUPPEN, faehigkeitenNach, type Faehigkeit
:16-22   der EINE Export, drei Eingaenge
:28-31   Gruppen durchlaufen, LEERE weglassen
:43-45   istEngine · klickbar · aktiv
:50-58   der Knopf: title, aria-disabled, onClick, Stile
:63-64   Beschriftung + ZustandBadge
:71-73   die Fusszeile mit dem Phantomwort
```

**Kein Zustand, kein Effekt, kein Speicher** — *die Fläche ist eine reine Abbildung von
`FAEHIGKEITEN` auf Knöpfe.* **`activeToolId` kommt von außen, `hover` gibt es hier nicht** *(anders
als in W-33s `StartView`, wo drei Komponenten je einen halten).*

## Abhängigkeiten

| Braucht | Warum | Richtung geprüft? |
|---|---|---|
| `tools/toolRegistry` | `TOOL_DEFINITIONS` — Quelle 1 | **einseitig** |
| `studioDaten` (`T`) | die Farbmarken | einseitig |
| `studioUi` (`ZustandBadge`) | **die Beschriftung der Zustände** | einseitig — *aber der TYP kommt von dort zurück, siehe `7-GRENZEN`* |
| die 13 `geometry/*`-Module | **nur als Zeichenkette** in `engineModul` | **keine Einfuhr** — Byte-Treue |
| **wer W-36 braucht** | `rahmen/GruppenzeileUndSchiene.tsx:35` führt es ein, `:346` rendert es | — |

**Der Renderer ist `GruppenzeileUndSchiene`, nicht `HausplanerApp`** — *gemessen:*
`grep -rn "FaehigkeitenNavi" --include='*.tsx'` **außerhalb der Tests findet genau eine Einfuhr und
eine Verwendung.** *`HausplanerApp.tsx:145` nennt die Navi nur in einem Kommentar.*

> **`schienenReiter.test.ts:100` hält genau das fest:** *`assert.equal((appQuelle.match(/<FaehigkeitenNavi/g) ?? []).length, 1)`* —
> **die Navi hängt an genau EINER Stelle im Baum.**

## Ein toter Import

```text
HausplanerApp.tsx:39   import { faehigkeitNach } from './tools/faehigkeiten';
grep -n "faehigkeitNach" HausplanerApp.tsx   ->  NUR die Importzeile.
```

**Der einzige echte Aufrufer ist `rahmen/FussUndUeberlagerungen.tsx`** *(`:212` für `gruppe`, `:213`
für `zustand`).* **Gemeldet, nicht entfernt** — *eine Ablesung ändert ihre Quelle nicht.*
