# W-36 · Fähigkeiten-Navigation — GRENZEN

> **Dieses Blatt ist Pflicht.** *Und die Lehre aus W-40/1 gilt: „die Quelle sagt es nicht" ist erst
> dann eine Grenze, wenn auch der BESTAND nichts hergibt. Alles unten ist am Code gemessen.*

## W-36-2 · Der Kommentar mischt zwei Achsen — und er steht nicht allein

**Der Befund des Auftrags, wörtlich am Bau-Stand nachgelesen:**

```text
faehigkeiten.ts:24
  /** 'aktiv' = bedienbar · 'schlaeft' = registriert/sichtbar, Handler/Panel folgt (Batch 1–3). */
faehigkeiten.ts:25, der TYP direkt darunter
  export type FaehigkeitZustand =
    'verfuegbar' | 'voraussetzung' | 'nur_ergebnis' | 'in_entwicklung';
```

**Ein Kommentar erklärt zwei Wörter für einen Typ, der keines von beiden trägt.**

### Was die zwei Wörter WIRKLICH sind — gemessen

| Wort | Befund |
|---|---|
| **`'aktiv'`** | **existiert** — aber als Wert von `WerkzeugAnzeige` in `tools/werkzeugZustand.ts:30`. **Andere Achse, anderer Träger, andere Datei.** |
| **`'schlaeft'`** | **existiert NICHT als Wert.** *Kein Vorkommen im Code außerhalb von Kommentaren und einer Bildschirmzeile.* |

> **Das ist gefährlicher als ein veralteter Kommentar.** *Ein veralteter beschreibt einen alten
> Stand.* **Dieser mischt eine Nachbarachse mit einem Begriff, den es nie als Wert gab** — *wer ihn
> liest, sucht `'schlaeft'` im Code und findet Treffer, die wie Belege aussehen.*

### Der Befund ist GRÖSSER als der Auftrag ihn schneidet: SIEBEN Stellen in DREI Dateien

**Der Auftrag nennt drei Treffer in `faehigkeiten.ts` (`:7`, `:24`, `:73`). Gemessen:**

```text
grep -rn "schläft\|schlaeft" resources/planner/hausplaner --include='*.ts' --include='*.tsx'

app/tools/faehigkeiten.ts:7     „die 13 reinen Rechen-Engines … als art:'engine', zustand:'schlaeft'"
app/tools/faehigkeiten.ts:24    der Kommentar aus W-36-2
app/tools/faehigkeiten.ts:73    „// --- 2) Reine Rechen-Engines (… zustand 'schlaeft')"
app/FaehigkeitenNavi.tsx:5      „Engines/„schläft" zeigen ihren Zustand"
app/FaehigkeitenNavi.tsx:72     die FUSSZEILE — auf dem BILDSCHIRM
app/studioUi.tsx:24             „Zustands-Pille (aktiv/schläft) — GETEILTER Baustein"
app/studioUi.tsx:30             „sonst kein „schläft ohne Grund""
```

**Sieben Stellen, drei Dateien** — *und `studioUi.tsx` steht in keinem Scope-Block dieses Auftrags.*

> **Die schwerste ist `FaehigkeitenNavi.tsx:72`, denn sie steht auf dem BILDSCHIRM:**
>
> ```text
> „Jeder Eintrag sichtbar · „schläft" = Bedien-Panel folgt (Batch 1–3)."
> ```
>
> **Der Anwender bekommt die Erklärung einer Marke, die er nirgends sieht.** *Die Marke, die er
> wirklich sieht, heißt „in Entwicklung" (`studioUi.tsx:36`, Feld `kurz`).* **Ein Kommentar
> verwirrt die nächste Rolle; eine Bildschirmzeile verwirrt den Nutzer.**
>
> *`studioUi.tsx:30` ist demgegenüber unschuldig* — **„sonst kein ‚schläft ohne Grund'" benutzt das
> Wort als Redewendung, nicht als Wert.** *Ich führe es mit, weil eine Zählung sonst nicht
> nachvollziehbar wäre, und kennzeichne es: **6 problematische, 1 Redewendung**.*

**ZWEI Zahlen, und beide stimmen — je nachdem, wonach man sucht:**

```text
grep -rn "schläft\|schlaeft"                    ->  7    die Wortform
grep -rn "schlafen\|schläft\|schlaeft"          ->  9    mit dem Verb
```

**Die zwei zusätzlichen sind Verbformen in Testmeldungen**, *darunter
`faehigkeiten.test.ts:31`:* **„`${e.id}` sollte schlafen (Fläche folgt in einer spaeteren
Scheibe)"** — *eine Fehlermeldung, die ein Wort benutzt, das der Typ nicht kennt.*

> **Wer die Wortform zählt, bekommt sieben; wer den Begriff meint, neun.** *Beide Zahlen stehen hier
> mit dem Befehl, der sie erzeugt.* **Ich hatte zuerst nur die Wortform gemessen** — *derselbe Griff
> wie bei H-9: das Muster misst die Schreibweise und nicht die Sache.*

**Der Code wird NICHT geändert** — *ein Blatt beschreibt, es berichtigt keinen Code.*

## Die dritte Achse ist ZWEIMAL deklariert

```text
tools/faehigkeiten.ts:25   FaehigkeitZustand = 'verfuegbar'|'voraussetzung'|'nur_ergebnis'|'in_entwicklung'
app/studioUi.tsx:28        StudioZustand     = 'verfuegbar'|'voraussetzung'|'nur_ergebnis'|'in_entwicklung'
```

**Zeichengleich, zwei Namen.** *Sie treffen sich in `FaehigkeitenNavi.tsx:64`, und es übersetzt, weil
TypeScript Werte prüft und nicht Namen.*

> **Die Sicherung wirkt nur in EINER Richtung.** *Ein fünfter Wert in `FaehigkeitZustand` bricht die
> Badge-Zeile — gut.* **Ein fünfter in `StudioZustand` bricht nichts, und die Navi kennt ihn nie.**
>
> **Und die Beschriftung hängt an der ZWEITEN:** `ZUSTAND` (`studioUi.tsx:32`) ist ein
> `Record<StudioZustand, …>`. **Ein neuer Fähigkeitszustand wäre damit ein Zustand ohne Wort** —
> *genau das, was W-38s `Record<SchrittStatus, string>` für die Fortschrittsachse verhindert.*

**Ob die zwei zusammengehören, entscheidet dieses Blatt nicht:** *`StudioZustand` wird auch von
`dashboard/panelTabs.ts:30`, `dashboard/fachFlaechen.ts:68` und `FachFlaeche.tsx:111` benutzt.*

## Ein FÜNFTER Zustand — ohne Typ

```ts
tools/toolTypes.ts:108-109
/** Projektzustand: 'editable' | 'readonly' | 'conflict' | 'offline' … */
projectState: string;
```

```text
toolContext.ts:37             projectState: e.projectState ?? 'editable'
activation.ts:30              vergleich(ctx.projectState, rule.operator, wert)
arbeitsbereiche.test.ts:120   projectState: 'planung'    <- in KEINER der vier
```

**Ein Test benutzt bereits einen Wert, den der Kommentar nicht kennt, und nichts hält ihn auf.**

> **Bei den vier gebauten Achsen wäre `'planung'` ein Übersetzungsfehler; hier ist es eine gültige
> Zeichenkette.** *Dieselbe Lage wie `TYP_MAP` gegen `katalogFür` in W-35: eine Abbildung mit
> Typschutz, eine ohne.* **Ob `projectState` eine fünfte Achse ist oder ein freies Feld, ist eine
> Entwurfsfrage und gehört dem Planner.**

## W-36-6 · `WerkzeugAnzeige` — eine Achse ohne Registereintrag

```ts
tools/werkzeugZustand.ts:30
export type WerkzeugAnzeige = 'system' | 'aktiv' | 'gesperrt' | 'angeheftet' | 'empfohlen' | 'weitere';
```

**Sechs Werte, eigener Träger (das Werkzeug), eigene Zeichen-Tabelle (`ANZEIGE_ZEICHEN`, `:33`)** —
*und **kein** Eintrag im Werkzeugregister der Werkbank.*

> **Das ist eine Anschlusslücke der STUFE, nicht dieses Blattes.** *Vier Statusachsen sind
> beschrieben oder beschreibbar; die vierte hat niemanden, der sie beschreibt.* **Gemeldet, nicht
> geschnitten — ein Blatt zu schneiden ist Planner-Arbeit.**

**Und die Datei trägt eine eigene Namensfalle:** *sie heißt `werkzeugZustand.ts`, definiert
`WerkzeugAnzeige` und **importiert** einen anderen Typ namens `WerkzeugZustand` aus `toolTypes`
(`:26`).* **Der importierte ist keine Achse, sondern ein Ergebnis:**

```ts
toolTypes.ts:112-115
/** Ergebnis der Aktivierung: aktiv? und (bei inaktiv) der Grund. */
export interface WerkzeugZustand { enabled: boolean; reason: string | null; }
```

> **Datei heißt wie der Typ, den sie NICHT definiert.** *Wer `werkzeugZustand.ts` öffnet, um
> `WerkzeugZustand` zu lesen, findet `WerkzeugAnzeige`.* **Benannt, nicht behoben.**

## Zwei überholte Stellen im eigenen Dateikopf

```text
faehigkeiten.ts:9-11   „eine CAD-sinnvolle Teilmenge aus toolCatalog.TOOL_KATALOG …"
                       -> QUELLE 3 IST LEER: :96 const werkzeugKatalogFaehigkeiten = [];
                          Der Grund steht in :91-95 und ist gut: es waren
                          „anklickbare Zeilen ohne Handler — falsche Versprechen (AUF-28)".
                          Der BAU ist richtig, die BESCHREIBUNG darueber ist alt.

faehigkeiten.ts:7-8    „die 13 reinen Rechen-Engines … zustand:'schlaeft'
                        (Panels folgen in Batch 1–3 — hier nur SICHTBAR machen)"
                       -> gemessen: 8 der 13 Engines tragen 'verfuegbar', sind also
                          KLICKBAR (FaehigkeitenNavi:43). Fuer die Mehrzahl sind die
                          Panels nicht „Batch 1–3", sie sind da.
```

**Und ein dritter Widerspruch, zwei Zeilen auseinander:**

```text
FaehigkeitenNavi.tsx:37-38   „Aktionen … und Engines behalten ihre eigenen Handler
                              (Op-Leiste bzw. Batch 1–3) — hier nur sichtbar."
FaehigkeitenNavi.tsx:40-42   „AUF-33/L2: Auch eine ENGINE ist klickbar, sobald sie
                              `verfuegbar` ist — dann öffnet sie ihre Fläche."
```

> **Zwei Kommentare übereinander, der obere alt.** *Der untere trägt eine Auftragsnummer und ist der
> jüngere.* **Der Code folgt dem unteren (`:43`).**

## Was das Werkzeug nicht kann

```text
KEINE Hervorhebung fuer Engines   `aktiv` (:45) vergleicht f.toolId mit activeToolId.
                                  Eine Engine hat keine toolId — eine laufende Engine
                                  wird also nie hervorgehoben.

KEINE Anzeige leerer Gruppen      :31 return null. Der Anwender sieht nicht neun
                                  Rubriken, sondern so viele wie gefuellt sind.
                                  Der Test verlangt nur „mindestens sechs".

KEIN Weg fuer art:'aktion'        aus dieser Flaeche heraus gar nicht klickbar.
                                  Sie tragen auch KEINE Marke, die das sagt —
                                  nur grauer Text und aria-disabled.

KEIN Schutz gegen fehlende        FAEHIGKEIT_GRUPPEN (:46) ist ein Array; eine
Gruppe im Array                   fehlende Gruppe faellt nicht auf, eine falsch
                                  benannte waere ein Typfehler.

KEINE Pflicht fuer engine-Felder  eingang/ausgang/engineModul/engineExport sind
im TYP                            alle optional. Was sie erzwingt, ist der
                                  Guard-Test — nicht der Compiler.
```

## Was dieses Blatt ausdrücklich NICHT entscheidet

| Frage | Gehört |
|---|---|
| Sollen `FaehigkeitZustand` und `StudioZustand` zusammengelegt werden? | **Planner** — *drei fremde Dateien betroffen* |
| Ist `projectState` eine fünfte Achse oder ein freies Feld? | **Planner** |
| Bekommt `WerkzeugAnzeige` ein eigenes Werkzeugblatt? | **Planner** — *Anschlusslücke der Stufe* |
| Sollen die sieben `schläft`-Stellen berichtigt werden — und die Bildschirmzeile zuerst? | **Yama** *(sichtbare Änderung)* / **Planner** *(die sechs Kommentare)* |
| Sollen `voraussetzung` und `nur_ergebnis` bleiben, obwohl sie 0 Vorkommen haben? | **Planner** |

## Was später kommen könnte

```text
- die Fusszeile „schläft" -> das erste, was sich lohnt: eine Zeile, die der
  Nutzer liest und die auf nichts zeigt
- ein Blatt fuer WerkzeugAnzeige
- ein Record<FaehigkeitZustand, …> statt der Kopplung an StudioZustand
```
