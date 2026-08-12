# Baubericht W-36 — Fähigkeiten-Navigation, abgelesen

```yaml
auftrag: "W-36"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-36-faehigkeiten-navigation.md
art: "STUFE 6 · ABLESUNG — 129 + 76 Zeilen, DoR in zweiter Fassung erteilt"
basis_sha: 08b264cc
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Dieser Bau hat mich FÜNF eigene Fehlgriffe gekostet, alle vor der Fertigmeldung gefunden — und
> der lehrreichste ist der zweite, weil er den ersten aufhebt.** *Abschnitt „Fünf eigene
> Fehlgriffe" unten. Sie stehen alle auch in den Blättern.*

## Was gebaut wurde

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-36-faehigkeiten-navigation/
  1-ZWECK.md · 2-FUNKTION.md · 3-FORMELN.md · 4-BEDIENUNG.md
  5-CODE/LIESMICH.md · 6-PRUEFUNG.md · 7-GRENZEN.md
REGISTER.md   Zeile 123:  LEER -> BESCHRIEBEN
```

**Kein Produktivcode.** *Sieben `schläft`-Stellen, ein toter Import und zwei überholte Dateiköpfe
sind **gemeldet**, keine davon berührt.*

## W-36-1 (TRAGEND) · Vier Achsen, vier Träger — und die Regel dahinter

| Achse | Träger | Fundstelle |
|---|---|---|
| `SchrittStatus` | Fahrschritt / Prüfpunkt | `studioDaten.ts:163` |
| `ConfiguratorStatus` | `ConfiguratorPackage` | `configuratorPackage.ts:26`, Feld `:72` |
| **`FaehigkeitZustand`** | **`Faehigkeit`** | **`tools/faehigkeiten.ts:25`** |
| `WerkzeugAnzeige` | Werkzeug | `tools/werkzeugZustand.ts:30` — **SECHS Werte** |

**Alle vier einzeln geöffnet.** *Die vierte trägt sechs Werte; das Auftragsblatt kürzt mit
`'system' | 'aktiv' | …` ab — im Blatt stehen sie vollständig, weil eine abgekürzte Aufzählung beim
nächsten Zitieren zur vollständigen wird.*

### Und es gibt eine FÜNFTE Statusgröße — ohne Typ

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

> **Ein Test benutzt bereits einen Wert, den der Kommentar nicht kennt, und nichts hält ihn auf.**
> *Bei den vier gebauten Achsen wäre `'planung'` ein Übersetzungsfehler; hier ist es eine gültige
> Zeichenkette.* **Dieselbe Lage wie `TYP_MAP` gegen `katalogFür` in W-35.**

### Die dritte Achse ist ZWEIMAL deklariert

```text
tools/faehigkeiten.ts:25   FaehigkeitZustand = 'verfuegbar'|'voraussetzung'|'nur_ergebnis'|'in_entwicklung'
app/studioUi.tsx:28        StudioZustand     = 'verfuegbar'|'voraussetzung'|'nur_ergebnis'|'in_entwicklung'
```

**Zeichengleich, zwei Namen, zwei Dateien.** *Sie treffen sich in `FaehigkeitenNavi.tsx:64`, und es
übersetzt, weil TypeScript Werte prüft und nicht Namen.*

> **Die Sicherung wirkt nur in EINER Richtung:** *ein fünfter Wert in `FaehigkeitZustand` bricht die
> Badge-Zeile — gut. Einer in `StudioZustand` bricht nichts, und die Navi kennt ihn nie.* **Und die
> Beschriftung hängt an der zweiten** (`ZUSTAND` ist `Record<StudioZustand, …>`) — **ein neuer
> Fähigkeitszustand wäre damit ein Zustand ohne Wort.**

## W-36-2 · Der Befund ist GRÖSSER als der Auftrag ihn schneidet

**Der Auftrag nennt drei `schlaeft`-Treffer in einer Datei. Gemessen:**

```text
grep -rn "schläft\|schlaeft" resources/planner/hausplaner     ->  7 in DREI Dateien
grep -rn "schlafen\|schläft\|schlaeft"                        ->  9  (mit dem Verb)
```

```text
faehigkeiten.ts:7 · :24 · :73        die drei aus dem Auftrag
FaehigkeitenNavi.tsx:5               Dateikopf
FaehigkeitenNavi.tsx:72              die FUSSZEILE — auf dem BILDSCHIRM
studioUi.tsx:24                      „Zustands-Pille (aktiv/schläft)"
studioUi.tsx:30                      „kein ‚schläft ohne Grund'"  — REDEWENDUNG, unschuldig
faehigkeiten.test.ts:31              „sollte schlafen" — in einer Fehlermeldung
```

> **Die schwerste ist `FaehigkeitenNavi.tsx:72`, denn sie steht auf dem BILDSCHIRM:** *„Jeder
> Eintrag sichtbar · **„schläft"** = Bedien-Panel folgt (Batch 1–3)."* **Der Anwender bekommt die
> Erklärung einer Marke, die er nirgends sieht** — *die Marke heißt „in Entwicklung"
> (`studioUi.tsx:36`).* **Ein Kommentar verwirrt die nächste Rolle; eine Bildschirmzeile verwirrt den
> Nutzer.**

**Und `'aktiv'` existiert sehr wohl** — *als Wert von `WerkzeugAnzeige` in `werkzeugZustand.ts:30`.*
**Andere Achse, anderer Träger, andere Datei.** *Der Kommentar mischt also eine Nachbarachse mit
einem Begriff, den es nie als Wert gab.*

## W-36-5 · Die zwölf Wächter in drei Klassen, am Bau-Stand erhoben

```text
IMPORT  7      NUR QUELLE  3      WORTZUFALL  2      =  12
```

**IMPORT (7):** *`faehigkeiten`, `toolPresentation`, `schienenReiter` und die **vier**
`enginePanel`-Tests (Rest, Sparren, TgaHeizung, Treppe) — sie tragen wortgleich dieselbe
Importzeile.*

**NUR QUELLE (3):** *`keineKappung:22` (`readFileSync`), `gruppenzeileUndSchiene:102` (Marke
`'<FaehigkeitenNavi'`), `stilschicht:679` (Dateiliste).*

**WORTZUFALL (2):** *`werkzeugRegistry:14` (ein FELD namens `faehigkeiten`), `ansichtBereit:96`
(ein PARAMETERNAME, der auf `capabilities` abgebildet wird).*

> **`gruppenzeileUndSchiene` ist der interessanteste:** *er prüft in BEIDE Richtungen —
> `assert.ok(schiene.includes(marke))` **und** `assert.ok(!app.includes(marke))`.* **Der Kommentar
> sagt warum: „Ohne diesen Partner bliebe die Zusage oben grün, wenn beides nebeneinander stünde."**
> *Das ist B4 in Testform.*

## Der Guard-Test ist der stärkste Wächter dieser Werkbank

```ts
faehigkeiten.test.ts:38
const modul = (await import('../' + e.engineModul)) as Record<string, unknown>;
assert.equal(typeof modul[e.engineExport as string], 'function', …);
```

**Er beweist statt zu lesen.** *`faehigkeiten.ts` referenziert die dreizehn Engines als
**Zeichenketten** (`engineModul: 'geometry/…'`) und importiert keine davon — was der Compiler dort
nicht prüfen kann, holt dieser Test zur Laufzeit nach.*

**Und der Test daneben (`:21-34`) ist schärfer, als ich erwartet hatte:** *er nagelt **13 Engines**
fest und listet die **8 angeschlossenen** namentlich — „genau die angeschlossenen Engines", sortiert
verglichen, „damit die Zusage nicht an der Zeilennummer im Register hängt".*

## FÜNF eigene Fehlgriffe, alle vor der Meldung gefunden

**1 · Ich habe die vier Zustände ERKLÄRT.** *„sie braucht erst etwas anderes", „sie rechnet, hat aber
noch keine Bedienfläche".* **Erfunden — sie klangen plausibel und standen nirgends.**

**2 · Daraufhin schrieb ich, die Bedeutung stehe NIRGENDS.** *Auch falsch: sie steht in
`studioUi.tsx:32-37` mit `kurz`, `lang`, Farbe und Punkt.*

> **Der Griff ist bei beiden derselbe: ich habe den Suchbereich des AUFTRAGS für den Suchbereich der
> SACHE gehalten.** *Der Auftrag nennt zwei Dateien; die Beschriftung liegt in einer dritten.*
> **Erst erfinden, dann eine Abwesenheit behaupten — beide Male hätte ein Blick über die zwei
> Dateien hinaus es verhindert.**

**3 · „9 verfügbar / 5 in Entwicklung" waren TEXTVORKOMMEN, keine Fähigkeiten.** *Eines der neun
steht in einer `.map()` (`:68`) und erzeugt so viele Einträge, wie `TOOL_DEFINITIONS` lang ist.*
**Die echte Zahl steht in `schienenReiter.test.ts:95`: `22 + 1 + 2 = 25`.**

**4 · Die Engines trügen `in_entwicklung` — falsch.** *Gemessen: **8 von 13** tragen `verfuegbar`,
sind also klickbar.* **Der Dateikopf sagt, die Panels folgten „in Batch 1–3"; für die Mehrzahl sind
sie da.**

**5 · Ich habe nur die Wortform `schläft` gezählt** — *sieben.* **Mit dem Verb sind es neun.**
*Derselbe Griff wie H-9: das Muster misst die Schreibweise und nicht die Sache.* **Beide Zahlen
stehen jetzt im Blatt, je mit ihrem Befehl.**

**Dazu zwei falsche Zeilenangaben, beim Nachmessen der eigenen Angaben gefunden:** *`:97` statt
`:96` für die leere Quelle 3; `title` und `aria-disabled` um eine Zeile verschoben (`:50`/`:51`
statt `:51`/`:52`).*

## Zwei überholte Dateiköpfe und ein Widerspruch in zwei Zeilen

```text
faehigkeiten.ts:9-11   nennt Quelle 3 „eine CAD-sinnvolle Teilmenge aus TOOL_KATALOG"
                       -> :96 const werkzeugKatalogFaehigkeiten = [];   LEER seit I4
                          Der Grund in :91-95 ist gut: es waren „anklickbare Zeilen ohne
                          Handler — falsche Versprechen (AUF-28)".
                          Der BAU ist richtig, die BESCHREIBUNG darueber ist alt.

FaehigkeitenNavi.tsx:37-38   „Aktionen … und Engines behalten ihre eigenen Handler"
                     :40-42   „AUF-33/L2: Auch eine ENGINE ist klickbar, sobald sie
                               `verfuegbar` ist"
                       -> zwei Kommentare uebereinander, der obere alt.
                          Der Code folgt dem unteren (:43).
```

## Ein toter Import

```text
HausplanerApp.tsx:39   import { faehigkeitNach } from './tools/faehigkeiten';
grep -n "faehigkeitNach" HausplanerApp.tsx   ->  NUR die Importzeile.
```

**Der einzige echte Aufrufer ist `FussUndUeberlagerungen.tsx:212`/`:213`.** *Das Auftragsblatt führt
`HausplanerApp:39` als „in Gebrauch" — als **Fundstelle** richtig, als **Benutzung** nicht.*

## Zwei Stellen ohne Typschutz, am Code ablesbar

```ts
:66   gruppe: WERKZEUG_GRUPPE[t.id] ?? 'werkzeuge'
```
**Ein neues Werkzeug ohne Eintrag landet stillschweigend in „werkzeuge".** *Kein Fehler, keine
Warnung, falsche Rubrik — dieselbe Bauform wie `katalogFür` in W-35.*

```ts
:68   zustand: 'verfuegbar',
```
**Fest verdrahtet: jedes Werkzeug aus `TOOL_DEFINITIONS` gilt als verfügbar.** *Es gibt keinen Weg,
ein Werkzeug als `in_entwicklung` zu führen.*

## W-36-8 · Sieben Blätter, Gegenprobe grün

```text
1-ZWECK 49a27668 · 2-FUNKTION 9c6bcb42 · 3-FORMELN a623a4d1 · 4-BEDIENUNG 3dcce436
5-CODE/LIESMICH a88924f5 · 6-PRUEFUNG 53b5a342 · 7-GRENZEN 6a030f71
gegen ALLE uebrigen Werkzeugblaetter: 0 Kollisionen
```

## Was nicht gefahren wurde

| | |
|---|---|
| **Mutationsproben** | **keine gesetzt** — die Fangtabelle sagt es je Zeile |
| **Insel-Suite** | **nicht gefahren** — kein Produktivcode berührt |
| **Browserabnahme** | **offen**; der erste Punkt ist der einzige, der einen Nutzer betrifft: **steht in der Fußzeile wirklich „schläft"?** |

**Zwei Zeilen der Fangtabelle tragen OHNE Mutation:** *ein fünfter Wert in `StudioZustand` und ein
unbekannter `projectState` — beide folgen aus dem Typ, und ein `string` hat keine verbotenen Werte.*

## must_preserve und Rückweg

| Richtung | Ergebnis |
|---|---|
| geändert (`resources/`, `app/`) | **0** |
| hinzugefügt | **0** |
| entfernt | **0** |
| Rückweg | reine Neuanlage plus **eine** Registerzeile; `git revert` genügt |
