# W-36 · Fähigkeiten-Navigation — BEDIENUNG

## Aufruf

```ts
FaehigkeitenNavi.tsx:15-22
export function FaehigkeitenNavi(
  { onAktivieren, activeToolId, onEngine }: {
    onAktivieren: (toolId: string) => void;
    activeToolId?: string;
    /** AUF-33: öffnet die Fläche einer verfügbaren Engine. Fehlt der Rückruf, bleibt sie stumm. */
    onEngine?: (engineId: string) => void;
  },
): React.ReactElement
```

| Eingang | Art | Bedeutung |
|---|---|---|
| `onAktivieren` | `(toolId: string) => void` | **setzt das Werkzeug aktiv** — der Weg für `art:'werkzeug'` |
| `activeToolId` | `string?` | **welches Werkzeug gerade läuft** — allein für die Hervorhebung |
| `onEngine` | `(engineId: string) => void`, **optional** | **öffnet die Fläche einer Engine** |

> **Die Optionalität von `onEngine` ist eine Aussage, und der Code sagt sie selbst:** *„Fehlt der
> Rückruf, **bleibt sie stumm**."* **Ohne `onEngine` ist keine Engine klickbar — auch keine
> `verfuegbar`e.** *Das ist gewollt: lieber eine stille Zeile als eine, die nichts tut.*

## Was der Anwender sieht

```text
:28   FAEHIGKEIT_GRUPPEN.map(g => …)     neun Gruppen, in ihrer Reihenfolge
:31   if (items.length === 0) return null;   LEERE GRUPPEN VERSCHWINDEN
:34   <div className="hp-fn-rubrik">{g.label}</div>
:36   items.map(f => <button …>)
:63   <span className="hp-fn-label">{f.label}</span>
:64   <ZustandBadge zustand={f.zustand} />
:71   <div className="hp-fn-fuss">Jeder Eintrag sichtbar · „schläft" = Bedien-Panel folgt (Batch 1–3).</div>
```

**Eine leere Gruppe wird nicht als leer angezeigt — sie fällt weg** (`:31`). *Der Anwender sieht
also nicht neun Rubriken, sondern so viele, wie gefüllt sind.*

> **Der Test dazu verlangt `>= 6` von neun** (`faehigkeiten.test.ts:90-93`) — **drei Gruppen dürfen
> unsichtbar sein.** *Wer „neun Gruppen" liest und den Bildschirm zählt, kommt auf eine andere Zahl;
> beide sind richtig und messen Verschiedenes.*

## Wann ein Eintrag klickbar ist — und wann nicht

```ts
:43   const istEngine = f.art === 'engine'   && f.zustand === 'verfuegbar' && Boolean(onEngine);
:44   const klickbar  = (f.art === 'werkzeug' && f.zustand === 'verfuegbar' && !!f.toolId) || istEngine;
:45   const aktiv     = klickbar && f.toolId === activeToolId;
```

| `art` | klickbar, wenn … | Ziel |
|---|---|---|
| `'werkzeug'` | `zustand === 'verfuegbar'` **und** `toolId` vorhanden | `onAktivieren(f.toolId)` |
| `'engine'` | `zustand === 'verfuegbar'` **und** `onEngine` übergeben | `onEngine(f.id)` |
| **`'aktion'`** | **nie** — *aus der Navi heraus* | — |

**`'aktion'` ist aus dieser Fläche grundsätzlich nicht klickbar**, *und der Kommentar sagt warum
(`:37-38`):* **„Nur modus-schaltende Werkzeuge sind aus der Navi klickbar; Aktionen
(Löschen/Duplizieren) und Engines behalten ihre eigenen Handler (Op-Leiste bzw. Batch 1–3) — hier
nur sichtbar."**

> **Der Kommentar ist an einer Stelle überholt:** *„und Engines" — seit AUF-33 sind sie klickbar,
> sobald sie `verfuegbar` sind.* **Der Kommentar zwei Zeilen darunter (`:40-42`) sagt genau das
> Gegenteil und ist der jüngere:** *„Auch eine ENGINE ist klickbar, sobald sie `verfuegbar` ist …
> Vorher war jede Engine tot, obwohl die Rechenfunktion da war; es fehlte nur die Fläche davor."*
> **Zwei Kommentare übereinander, der obere alt.** *`7-GRENZEN.md`.*

**Und `aktiv` (`:45`) hat eine Eigenheit:** *es vergleicht `f.toolId` mit `activeToolId` — eine
Engine hat aber gar keine `toolId`.* **Eine laufende Engine wird also nie hervorgehoben**, *auch
wenn ihre Fläche offen ist.* **Gemessen, nicht vermutet: `istEngine` setzt `klickbar`, aber
`f.toolId` bleibt `undefined`.**

## Rückmeldungen

| Zeichen | Woher |
|---|---|
| **die Zustandsmarke** | `ZustandBadge` (`:64`) → `studioUi.tsx:32` mit `kurz`, `lang`, Farbe **und Punkt** |
| **die Hervorhebung** | `background: aktiv ? T.okSoft : 'transparent'` (`:56`) |
| **der Mauszeiger** | `cursor: klickbar ? 'pointer' : 'default'` (`:57`) |
| **die Schriftfarbe** | `color: klickbar ? T.ink : T.muted` (`:58`) |
| **`aria-disabled`** | `!klickbar` (`:51`) |
| **der Titel** | `` `${f.label} — ${f.funktion}${f.eingang ? ` · ${f.eingang} → ${f.ausgang ?? ''}` : ''}` `` (`:50`) |

> **Der Titel zeigt bei Engines Eingang und Ausgang der Rechnung** — *`eingang → ausgang`.* **Damit
> ist die Zusage aus `2-FUNKTION` bedienbar: die artabhängigen Felder sind nicht Zierde, sie
> erscheinen auf dem Bildschirm.**

**Der Zustand wird NIE nur über Farbe gemeldet.** *Der Dateikopf sagt es (`:6`): „A11y: Zustand als
Farbe UND Text (kein Nur-Farbe-Signal)", und `studioUi.tsx:30-31` wiederholt es: „Jeder Zustand
trägt Farbe UND Text UND Punkt."*

## Die Fußzeile spricht ein Wort, das es nicht gibt

```text
:71-72   „Jeder Eintrag sichtbar · „schläft" = Bedien-Panel folgt (Batch 1–3)."
```

**`'schlaeft'` ist kein Wert von `FaehigkeitZustand`** *(gemessen: 0 Vorkommen als Wert)* — **und
hier steht es nicht in einem Kommentar, sondern auf dem BILDSCHIRM.**

> **Der Anwender liest eine Erklärung für eine Marke, die keine Fähigkeit trägt.** *Die Marke, die er
> wirklich sieht, heißt „in Entwicklung" (`studioUi.tsx:36`, `kurz`).* **Das ist die
> benutzersichtbare Ausprägung desselben Befundes, den W-36-2 für den Kommentar in `:24` verlangt —
> und sie ist die teurere von beiden.** *`7-GRENZEN.md`.*

## Abbruch

**Es gibt keinen.** *Die Navi hält keinen Zustand, öffnet nichts und schließt nichts — sie meldet
nach oben und rendert neu, wenn sich `activeToolId` ändert.*

## Sichtprüfung

- [ ] **offen**

**Was eine Sichtprobe zuerst ansehen sollte** — *aus der Ablesung abgeleitet:*

```text
1  Steht in der Fusszeile wirklich „schläft"? Der Code sagt ja; auf dem
   Bildschirm traegt keine Marke dieses Wort.
2  Wie viele Rubriken erscheinen? Der Typ nennt neun Gruppen, leere fallen
   weg (:31), der Test verlangt mindestens sechs.
3  Wird eine laufende ENGINE hervorgehoben? Nach dem Code nicht — `aktiv`
   haengt an `toolId`, und die hat keine Engine.
4  Sind die 'aktion'-Eintraege wirklich stumm, und sieht man ihnen das an?
   `aria-disabled`, grauer Text, Standardzeiger — aber KEINE Zustandsmarke
   sagt „hier nicht klickbar".
```
