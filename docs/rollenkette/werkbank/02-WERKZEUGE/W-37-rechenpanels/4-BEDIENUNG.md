# W-37 · Rechenpanels — BEDIENUNG

## Der Ablauf

```text
1  Panel waehlen        enginePanel(engineId)          enginePanels.ts:522
2  Formular fuellen     startwerte(panel)              :527  — die Vorgaben stehen drin
3  Pflicht pruefen      fehlendePflichtfelder(...)     :538  — nennt die FELDER, nicht nur die Zahl
4  Rechnen              panel.berechne(werte)          -> die echte Engine
5  Lesen                Ergebnisfelder + Pruefhinweise app/EngineFlaeche.tsx
```

**`fehlendePflichtfelder` gibt `EngineFeld[]` zurück und nicht `number`** — *der Anwender bekommt
gesagt, **welches** Feld fehlt, nicht **wie viele**.*

## Die Schwere-Anzeige: ZEICHEN und WORT, und das ist eine Aussage

**`SCHWERE_ANZEIGE` (`EngineFlaeche.tsx:31-35`), am Code gezählt — DREI Grade:**

| Grad | Zeichen | Wort | Farbmarke |
|---|---|---|---|
| `fehler` | `✕` | „Fehler" | `errInk` |
| `warnung` | `⚠` | „Warnung" | `warnInk` |
| `info` | `ℹ` | „Hinweis" | `muted` |

> ***Jeder Grad trägt ZEICHEN und WORT — beides, nicht eines.*** *Das ist doppelt gesichert: gegen
> **Überlesen** (ein Zeichen allein wird übersehen) und gegen **fehlende Farbe** (Ausdruck,
> Farbenblindheit, Kontrastmodus).* **Wer den Grad nur an der Farbmarke trüge, hätte eine Anzeige,
> die auf Papier nichts sagt.**

**Und ein unbekannter Grad fällt auf `info` zurück** (`:178`, `?? SCHWERE_ANZEIGE.info`) — *keine
leere Zeile, kein Absturz.*

## Der VIERTE Zustand, der nicht in der Tabelle steht

**Eine bestandene Prüfung bekommt `✓ erfüllt` in `okInk`** (`:177`) — *nicht ihren Schweregrad.*

**Der Kommentar darüber sagt, warum, und er ist ein Eingeständnis:**

> *„Der Schweregrad sagt, wie schwer eine **VERLETZUNG** wöge — nicht, ob sie vorliegt. **Eine
> bestandene Prüfung als ‚✕ Fehler' zu zeigen, war der Fehler meiner ersten Fassung; die Sichtprobe
> hat ihn gefunden.** Bestanden ⇒ ✓ erfüllt."*

> ***Das gehört ins Blatt, weil es die Bedeutung des Feldes festlegt:*** *`schwere` ist die
> **Gewichtung des Falls**, nicht sein **Ergebnis**. Wer die zwei verwechselt, zeigt jede erfüllte
> Prüfung als Fehler — und es fällt erst im Bild auf, nicht im Test.*

## Was der Anwender am Panel sonst liest

**Jedes Panel trägt `titel`, `zweck` und `grundlage`** (`EnginePanel`, `:57`). *Die `grundlage`
nennt Verfahren oder Norm der dahinterliegenden Engine* — bei `engine-fensterprodukt` etwa
*„DIN EN ISO 10077-1 — Uw = (Ag·Ug + Af·Uf + lg·Psi) / (Ag + Af)"*.

> **Damit steht die Herkunft der Zahl am selben Ort wie die Zahl.** *Der Anwender muss nicht wissen,
> welches Modul rechnet — aber er sieht, **wonach** gerechnet wird.*

## Der Vorbehalt bei `engine-sparren`

**Er ist ein ERGEBNISFELD wie jedes andere** (`enginePanels.ts:225`, `{ schluessel: 'vorbehalt',
label: 'Vorbehalt' }`) — *er steht in derselben Liste wie Querschnitt und Ausnutzung und wird im
selben Block angezeigt.*

> **Er ist damit nicht wegklappbar und keine Fußnote.** *Dieselbe Bauform bei `engine-fbh` (`:264`)
> und `engine-abwasser` (`:354`), dort aus A-17 — der Kommentar sagt es wörtlich: „im selben Blick
> wie das verwendete Gefälle, nicht in einer Fußnote."*

## Abbruch

**Keiner nötig.** *Die Panels halten keinen Zwischenzustand im Modell — Felder ändern, neu rechnen.*
