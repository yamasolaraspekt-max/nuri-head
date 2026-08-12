# W-36 · Fähigkeiten-Navigation — FUNKTION

## Die VIER Statusachsen, JE MIT TRÄGER

**Yamas Auflösung zu W-40 gilt hier als Regel und nicht als Einzelfall: der Schlüssel ist der
TRÄGER, nicht das Wort.**

| Achse | Werte | **Träger** | Fundstelle |
|---|---|---|---|
| `SchrittStatus` | `ok` · `prog` · `warn` · `open` | **Fahrschritt / Prüfpunkt** | `app/studioDaten.ts:163` |
| `ConfiguratorStatus` | sieben, von `draft` bis `outdated` | **`ConfiguratorPackage`** | `geometry/configuratorPackage.ts:26`, Feld `:72` |
| **`FaehigkeitZustand`** | `verfuegbar` · `voraussetzung` · `nur_ergebnis` · `in_entwicklung` | **`Faehigkeit`** | **`tools/faehigkeiten.ts:25`**, Feld `:32` |
| `WerkzeugAnzeige` | `system` · `aktiv` · `gesperrt` · `angeheftet` · `empfohlen` · `weitere` | **Werkzeug** | `tools/werkzeugZustand.ts:30` |

**Vier Achsen sind keine vier Wahrheiten, solange jede an ihrem eigenen Träger hängt.** *Alle vier
Fundstellen einzeln geöffnet.*

> **Die vierte trägt SECHS Werte.** *Das Auftragsblatt kürzt mit `'system' | 'aktiv' | …` ab — hier
> stehen sie vollständig, weil eine abgekürzte Aufzählung beim nächsten Zitieren zur vollständigen
> wird.*

### Die dritte Achse ist ZWEIMAL deklariert

```text
app/tools/faehigkeiten.ts:25
  export type FaehigkeitZustand = 'verfuegbar' | 'voraussetzung' | 'nur_ergebnis' | 'in_entwicklung';

app/studioUi.tsx:28
  export type StudioZustand    = 'verfuegbar' | 'voraussetzung' | 'nur_ergebnis' | 'in_entwicklung';
```

**Zeichengleich, zwei Namen, zwei Dateien.** *Und sie treffen sich in der Navi:*

```ts
FaehigkeitenNavi.tsx:64   <ZustandBadge zustand={f.zustand} />
                          f.zustand ist FaehigkeitZustand
studioUi.tsx:39           ZustandBadge({ zustand }: { zustand: StudioZustand })
```

**Es übersetzt, weil die Unions strukturell identisch sind** — *TypeScript prüft die Werte, nicht die
Namen.*

> **Das ist keine zweite Wahrheit, solange sie gleich bleiben — und genau darin liegt die Gefahr.**
> *Wer einen fünften Wert in `FaehigkeitZustand` einträgt, bekommt einen Übersetzungsfehler an der
> Badge-Zeile und wird gestoppt.* **Wer ihn in `StudioZustand` einträgt, wird NICHT gestoppt: die
> Navi reicht weiterhin nur die vier alten durch, und der neue Zustand existiert für sie nicht.**
> *Die Sicherung wirkt in einer Richtung.*
>
> **Und die BESCHRIFTUNG hängt an der zweiten:** `ZUSTAND` in `studioUi.tsx:32` ist ein
> `Record<StudioZustand, …>`. **Ein neuer `FaehigkeitZustand` wäre also zugleich ein Zustand ohne
> Wort — dieselbe Lage, die W-38s `Record<SchrittStatus, string>` für die Fortschrittsachse
> verhindert.**
>
> **Ob die zwei Namen zusammengelegt gehören, entscheidet dieses Blatt nicht** — *`StudioZustand`
> wird auch von `panelTabs.ts:30`, `fachFlaechen.ts:68` und `FachFlaeche.tsx:111` benutzt, also weit
> außerhalb von W-36.* **Benannt, nicht behoben; `7-GRENZEN.md`.**

### Und es gibt einen FÜNFTEN Zustand — ohne Typ

```ts
tools/toolTypes.ts:108-109
/** Projektzustand: 'editable' | 'readonly' | 'conflict' | 'offline' … */
projectState: string;
```

**Vier Werte in einem Kommentar, `string` im Typ.** *Der Unterschied ist messbar:*

```text
toolContext.ts:37       projectState: e.projectState ?? 'editable'
activation.ts:30        vergleich(ctx.projectState, rule.operator, wert)
arbeitsbereiche.test.ts:120   projectState: 'planung'      <- steht in KEINER der vier
```

> **Ein Test benutzt bereits einen Wert, den der Kommentar nicht kennt — und nichts hält ihn auf.**
> *Bei den anderen vier Achsen wäre `'planung'` ein Übersetzungsfehler; hier ist es eine gültige
> Zeichenkette.* **Das ist dieselbe Lage wie `TYP_MAP` gegen `katalogFür` in W-35: eine Abbildung mit
> Typschutz, eine ohne.**
>
> **Ob `projectState` eine fünfte ACHSE ist oder ein freies Feld, entscheidet dieses Blatt nicht** —
> *siehe `7-GRENZEN.md`.* **Gemessen ist: er verhält sich wie eine Achse und ist nicht als eine
> gebaut.**

## Die drei Typachsen dieses Werkzeugs

```ts
:17-19   FaehigkeitGruppe   NEUN Gruppen
         'dach-zimmerei' | 'tga-heizung' | 'energie-pv' | 'sanitaer' | 'kueche'
         | 'bau' | 'fenster-tuer' | 'treppe' | 'werkzeuge'
:22      FaehigkeitArt      'werkzeug' | 'aktion' | 'engine'
:25      FaehigkeitZustand  'verfuegbar' | 'voraussetzung' | 'nur_ergebnis' | 'in_entwicklung'
```

**Neun Gruppen, am Code gezählt.** *Und `FAEHIGKEIT_GRUPPEN` (`:46`) trägt sie ein zweites Mal — mit
Beschriftung:*

```ts
:46   export const FAEHIGKEIT_GRUPPEN: ReadonlyArray<{ id: FaehigkeitGruppe; label: string }>
```

> **Zwei Listen derselben neun Gruppen: die Union und das Array.** *Die Union ist typgesichert, das
> Array nicht — eine fehlende Gruppe im Array fällt nicht auf, eine zusätzliche wäre ein Typfehler.*
> **`ReadonlyArray<{id: FaehigkeitGruppe}>` schützt gegen falsche Namen, nicht gegen fehlende.**
>
> **Und kein Test schließt die Lücke — gemessen:**
>
> ```ts
> faehigkeiten.test.ts:90-93
> test('mehrere Gruppen sind nicht leer (Navi zeigt sie)', () => {
>   const nichtLeer = FAEHIGKEIT_GRUPPEN.filter((g) => faehigkeitenNach(g.id).length > 0);
>   assert.ok(nichtLeer.length >= 6, …);
> });
> ```
>
> **`>= 6` von neun.** *Der Test verlangt ausdrücklich nicht, dass alle neun Gruppen gefüllt sind —
> drei dürfen leer bleiben.* **Das ist eine bewusst weiche Schranke und keine Lücke; sie steht hier,
> weil „neun Gruppen" ohne diesen Satz wie „neun gefüllte Gruppen" klingt.**

## Die ARTABHÄNGIGEN Felder — der Kern der Struktur

```ts
:27-45   export interface Faehigkeit {
           id · label · gruppe · art · zustand
           funktion         „Einzeiler: was die Fähigkeit fachlich tut"

           // Nur art:'engine':
           eingang?  ausgang?      „Eingang/Ausgang der echten Rechnung (fürs spätere Panel)"
           engineModul?            „Doku-Referenz auf das echte Modul
                                    (nur aufgerufen, nie geändert)"
           engineExport?           „der ECHTE Export-Name im Modul (≠ Modulname).
                                    Vom Guard-Test verriegelt."

           // Nur art:'werkzeug'|'aktion':
           toolId?                 „die TOOL_DEFINITIONS-id, die aktiviert wird
                                    (falls schon verdrahtet)"
         }
```

**Alle artabhängigen Felder sind `optional`** — *der Typ erzwingt die Zuordnung NICHT.* **Ein
`engine` ohne `engineModul` übersetzt sauber; was ihn aufhält, ist ein Test und nicht der
Compiler.**

### `engineExport` ≠ Modulname — und der Guard-Test ist ein BEWEIS

```ts
__tests__/faehigkeiten.test.ts:38
test('Guard (AP-E): jede Engine-Fähigkeit importiert REAL + der deklarierte Export existiert
      (Export ≠ Modulname)', async () => {
  const engines = FAEHIGKEITEN.filter((f) => f.art === 'engine');
  for (const e of engines) {
    assert.ok(e.engineModul && e.engineExport, …);
    const modul = (await import('../' + e.engineModul)) as Record<string, unknown>;
    assert.equal(typeof modul[e.engineExport as string], 'function', …);
  }
});
```

**Was er verriegelt, in drei Punkten:**

```text
1  jede Engine-Faehigkeit MUSS Modul UND Export deklarieren  (die optionalen
   Felder werden fuer art:'engine' zur Pflicht — im Test, nicht im Typ)
2  das Modul wird WIRKLICH IMPORTIERT, dynamisch zur Laufzeit —
   kein Textmuster, kein Pfadvergleich
3  der deklarierte Export MUSS im Modul existieren UND eine Funktion sein
```

**Der Kommentar darüber nennt den Zweck:** *„Verriegelt die ‚echte Engines'-Zusage **per Beweis**:
dynamischer Import + Prüfung des deklarierten Export-Namens. Rot, sobald Modul ODER Export
fehlt/verfälscht ist (Gegenbeweis)."*

> **Das ist ein Wächter gegen genau die Verwechslung, die diese Kette mehrfach eingeholt hat: ein
> Name, der wie die Sache aussieht und eine andere ist.** *`engineModul` heißt etwa
> `geometry/…`, und der Export darin heißt anders.* **Wer nach dem Modulnamen sucht, findet die
> Funktion nicht — und der Test hält genau das fest, indem er sie AUFRUFBAR nachweist statt sie zu
> lesen.**
>
> **Er ist damit derselben Klasse wie `projektKlick` in W-33** *(rendert statt zu lesen)* **und
> stärker als jede Textprobe in dieser Werkbank.**

## Die vier exportierten Funktionen

| Zeile | Funktion | Rückgabe | Aufrufer im Produktivcode | in Tests |
|---|---|---|---|---|
| `:106` | `faehigkeitenNach(gruppe)` | `Faehigkeit[]` | **`FaehigkeitenNavi.tsx:30`** | `faehigkeiten`, `toolPresentation` |
| `:111` | `alleFaehigkeiten()` | `Faehigkeit[]` | **0** | `schienenReiter.test.ts:96` |
| `:116` | **`doppelteIds()`** | `string[]` | **0** | `faehigkeiten:10`, `toolPresentation:180` |
| `:127` | `faehigkeitNach(id)` | `Faehigkeit \| undefined` | **`FussUndUeberlagerungen.tsx:212`, `:213`** | — |

**Jede Zeile gemessen, nicht abgeleitet** — *und dabei ein Fund:*

```text
HausplanerApp.tsx:39   import { faehigkeitNach } from './tools/faehigkeiten';

grep -n "faehigkeitNach" HausplanerApp.tsx   ->  NUR die Importzeile.
```

> **Ein toter Import.** *Das Auftragsblatt führt `HausplanerApp:39` als „in Gebrauch" — die
> **Import**zeile trifft, die **Benutzung** nicht.* **Der einzige echte Aufrufer ist
> `FussUndUeberlagerungen`, und der ruft zweimal (`:212` für `gruppe`, `:213` für `zustand`).**
>
> *Das ist keine Beanstandung des Auftrags: „in Gebrauch: HausplanerApp:39" ist als Fundstelle
> richtig.* **Es ist die Sorte Feinheit, die beim Aufräumen zählt — wer die Funktion streicht, bricht
> eine Datei und nicht zwei.**

**`doppelteIds` und `alleFaehigkeiten` haben NULL Aufrufer im Produktivcode** — *sie leben
ausschließlich in Tests.* **Das mindert sie nicht: `doppelteIds` ist als Prüffunktion gebaut, und ein
Wächter, der nur im Test läuft, ist ein Wächter.** *Aber „in Gebrauch" heißt hier „im Test benutzt"
und nicht „im Programm".*

**`doppelteIds` ist selbst eine Ehrlichkeitsfunktion** — *sie liefert die Kennungen, die mehr als
einmal vorkommen.* **Eine Registry, die „EINE Wahrheit" sein will, braucht genau das.**

> **Drei dieser vier standen im ersten Auftragsentwurf in KEINEM Scope-Block.** *Der Plan-Prüfer hat
> es über die Vollständigkeitsfrage gefunden — „eine von vier genannt, drei in Gebrauch".*
> **Nachgetragen nach `e5285913`.**

**Und eine Konstante, die NICHT exportiert ist:**

```ts
:59   const WERKZEUG_GRUPPE: Record<string, FaehigkeitGruppe> = { … }
```

**`const`, nicht `export const`** — *modulintern.* **Zwei exportierte Konstanten
(`FAEHIGKEIT_GRUPPEN`, `FAEHIGKEITEN`), vier exportierte Funktionen, eine private Zuordnung.**

## Die Grenze zu W-37 — er importiert, er besitzt nicht

```text
W-36 IST      faehigkeiten.ts und FaehigkeitenNavi.tsx: die drei Typachsen, die
              neun Gruppen, die artabhaengigen Felder, FAEHIGKEITEN und alle VIER
              exportierten Funktionen.

W-36 IST NICHT
              werkzeugZustand.ts mit WerkzeugAnzeige -> eigene Achse, KEIN Werkzeug
                im Register (Anschlussluecke, 7-GRENZEN)
              die ENGINE-PANELS -> W-37.
                enginePanelRest.test.ts IMPORTIERT W-36, besitzt es aber nicht —
                dieselbe Richtung wie W-39 zu W-33.
              toolRegistry / TOOL_DEFINITIONS -> eigener Gegenstand, nur Verweis
              SchrittStatus -> W-38 · ConfiguratorStatus -> W-40/W-42
              die geometry/*-Engines SELBST -> „nur referenziert/aufgerufen, nie
                geaendert (Byte-Treue)", Dateikopf :13
```

> **Die letzte Zeile ist die schärfste Grenze dieses Werkzeugs, und sie steht im Code selbst.**
> *W-36 macht dreizehn Rechenmodule sichtbar und fasst keines an.* **Der Guard-Test beweist beides
> zugleich: dass sie existieren, und dass W-36 sie nur benennt.**
