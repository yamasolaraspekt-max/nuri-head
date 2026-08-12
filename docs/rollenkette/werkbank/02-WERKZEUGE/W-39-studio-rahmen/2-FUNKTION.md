# W-39 · Studio-Rahmen — FUNKTION

## Eingabe

| Was | Typ | Pflicht | Prüfung |
|---|---|---|---|
| — | — | — | **keine** |

**`HausplanerStudio()` nimmt keine Eigenschaften entgegen** (`:22`). *Alles, was es braucht, holt es
aus zwei Stores und aus dem eigenen Zustand.*

## Die drei Modi — mit ihren Zeilen, nicht mit einem Suchmuster

```ts
:23   const [modus, setModus] = React.useState<StudioModus>('start');
      StudioModus = 'start' | 'guided' | 'expert'        <- aus W-38s studioDaten
```

| Modus | Render-Zweig | Zeile | Wohin |
|---|---|---|---|
| `start` | `{modus === 'start' && <StartView …>}` | **`:131`** | W-33 |
| `guided` | `{modus === 'guided' && <GuidedView …>}` | **`:132`** | W-34 |
| `expert` | `{imExperte && ( … )}` | **`:133`** | die eingebettete `HausplanerApp` |

> **Der dritte Zweig heißt NICHT `modus === 'expert'`.** *Er heißt `imExperte` und steht als eigene
> Variable in `:85`:*
>
> ```ts
> :85   const imExperte = modus === 'expert';
> ```
>
> **Wer nach dem Vergleich sucht, findet den dritten Zweig nicht — und schlösse daraus, der
> Expertenmodus rendere nichts.** *Das Auftragsblatt verbietet ein Suchmuster auf `'expert'`
> ausdrücklich als Nachweis; hier stehen deshalb die Zeilen.* **H-9: ein Muster, das eine
> Schreibweise voraussetzt, misst die Schreibweise und nicht die Sache.**

**`imExperte` hat einen zweiten Nutzer, und der erklärt, warum es die Variable gibt:**

```ts
:130   overflow: imExperte ? 'hidden' : 'auto'
```

*Die Bühne scrollt in den beiden anderen Modi und im Expertenmodus nicht — dort scrollt die
eingebettete App selbst.* **Ein zweiter Nutzer ist der Grund, aus dem der Vergleich einen Namen
bekommen hat.**

## Verarbeitung — der Zustandsautomat

```text
start ──Modusschalter──► guided ──Modusschalter──► expert
  ▲                        │                         │
  └────────────────────────┴─── Modusschalter ◄──────┘

und ein zweiter Weg:  gehGeführt(s?)  (:73)  setzt Schritt UND Modus zugleich
```

**Der Wechsel hat genau zwei Auslöser** — *den Modusschalter (`:87-95`, über `setModus`) und
`gehGeführt` (`:73`), das aus der Startseite heraus in die geführte Planung springt und dabei
optional den Schritt mitsetzt.* **Es gibt keinen dritten Pfad; `setModus` erscheint an drei Stellen:
`:73`, `:90` und `:132` (der Sprung von `guided` nach `expert`).**

## Was der Rahmen selbst hält — am Code gezählt

```text
React.useState        5     :23 modus · :24 schritt · :25 toast · :26 konfig · :29 fachOffen
React.useRef          1     :60 toastTimer
React.useMemo         2     :36 schritte · :40 modell
React.useCallback     1     :67 zeigeToast
```

> **Das Auftragsblatt nennt „SECHS eigene Zustände (:23-29) … `+1`".** *Gemessen sind es im genannten
> Bereich **fünf** `useState`.* **Das `+1` ist `toastTimer` — ein `useRef` in `:60`, also außerhalb
> des Bereichs und kein Zustand:** *ein Ref löst kein Neuzeichnen aus, das ist sein Zweck.*
> **W-39-3 verlangt „gezählt am Code, nicht geschätzt" — also fünf Zustände und ein Ref, getrennt
> genannt.**

**Zwei Stores, mit den Feldern, die wirklich gelesen werden:**

| Store | gelesene Felder | Zeilen |
|---|---|---|
| `useHausplanerStore` | `scene` · `speicherStatus` · `speichernUrl` (als `kannSpeichern`) · `konfliktRevision` | `:30` `:31` `:55` `:56` |
| `usePlannerUiStore` | `projekte` | `:39` |

**Vier gegen eins** — *und `:37-38` sagt, warum das eine leer sein darf:* **„Auf der Studio-Fläche
ist sie leer — dorthin reicht der Controller sie bewusst nicht durch."**

## `modeBtn` — die Schalter-Fabrik

```ts
:87   const modeBtn = (m: StudioModus, label: string, ico: string, titel?: string)
:88     const on = m === modus;
:90     onClick={() => setModus(m)}  title={titel}
```

**Drei Aufrufe, einer je Modus** (`:110`, `:111`, `:112`). *Die Fabrik trägt die Hervorhebung des
aktiven Modus (`on`) und den optionalen Titel — **nur der Expertenschalter bekommt einen**.*

### Die Entwurfsentscheidung, die der Code begründet

**`:135-139`, wörtlich:**

> *„**Der Erklärtext ist erhalten, aber er kostet keine Zeile mehr.** Er stand als dauerhafte Leiste
> über der Bühne und beantwortete eine Frage, die man **genau einmal** hat. Jetzt trägt ihn der
> Modusschalter als Titel. **Der Weg zurück in die geführte Planung ist nicht verschwunden** — er
> steht als eigener Schalter im Kopf, sichtbar in jedem Modus (K-05)."*

**Beides ist nachgemessen:** *der Titel steht in `:112`, und der `guided`-Schalter in `:111` steht
im selben `<div className="hp-modusschalter">` wie die anderen zwei — also in **jedem** Modus
sichtbar, weil die Kopfzeile nicht vom Modus abhängt.*

## Ausgabe

| Was | Wohin |
|---|---|
| genau **ein** `return` | `:97` — das ganze Studio |
| Toast | `:146-148`, fest positioniert, 2600 ms (`:70`) |
| Konfigurator | `:149-151` — `ConfigWizard`, W-35 |
| Fachplaner-Fläche | `:154-156` — `FachFlaeche`, mit Herkunft für den Zurück-Weg |

> **Eine zweite Fehlspur, die dieselbe Sorte ist wie `imExperte`:** *`:89` sieht wie ein zweiter
> Rückgabepfad aus, gehört aber zu `modeBtn`.* **Das Studio hat genau EINEN `return` — `:97`.**

## Schichtzuordnung

| Schicht | W-39 | Beleg |
|---|---|---|
| 1 Domäne | **liest** | über `useHausplanerStore` → `scene` |
| 2 Geometrie | **nein** | keine F-Nummer, siehe `3-FORMELN.md` |
| 3 Anwendung | **ja** | `app/HausplanerStudio.tsx` — Modus, Zustände, Einhängen |
| 4/5 Oberfläche | **ja** | Kopfzeile, Modusschalter, Bühne, Toast |

## Scope — der Rahmen, nicht die dreizehn Module

```text
W-39 IST      app/HausplanerStudio.tsx — die Modus-Verwaltung, die fuenf Zustaende,
              die zwei Store-Anbindungen, der Modusschalter, und die ADDITIVE
              Einbettung der HausplanerApp per Flag imStudio.

W-39 IST NICHT die dreizehn importierten Module. Sie werden BENUTZT, nicht besessen:
              StartView              -> W-33
              GuidedView · fahrschritte -> W-34 (BETRIEBSBESTAETIGT)
              ConfigWizard           -> W-35
              studioDaten            -> W-38 (BETRIEBSBESTAETIGT)
              HausplanerApp · FachFlaeche · studioUi · uiState · hausplanerStore ·
              speicherAnzeige · dialogFokus · fachFlaechen
                                     -> je eigenes Werkzeug ODER noch nicht erfasst
```

**Das Blatt NENNT sie mit Grenze, beschreibt sie aber nicht.** *Welche davon heute noch gar kein
Werkzeug haben, steht in `7-GRENZEN.md` — das ist die Anschlussliste der Stufe.*

**Keine Datei außer `HausplanerStudio.tsx` wurde für W-39 angefasst**, *und die `HausplanerApp`
schon gar nicht: dass sie unverändert bleibt, ist der Kern aus `1-ZWECK.md`.*
