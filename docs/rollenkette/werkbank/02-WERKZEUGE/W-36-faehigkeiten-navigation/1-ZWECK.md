# W-36 · Fähigkeiten-Navigation — ZWECK

> **ABLESUNG.** *Der Code existiert:* [`app/tools/faehigkeiten.ts`](../../../../../resources/planner/hausplaner/app/tools/faehigkeiten.ts) **129 Zeilen** und
> [`app/FaehigkeitenNavi.tsx`](../../../../../resources/planner/hausplaner/app/FaehigkeitenNavi.tsx) **76 Zeilen**, am Bau-Stand gezählt.

## Welches Problem des Anwenders löst dieses Werkzeug?

**Der Anwender soll sehen, was das Programm kann — und zwar an EINER Stelle.**

**Der Dateikopf sagt, woraus das entstanden ist** (`:1-13`, wörtlich):

> *„Batch 0 — **EINE Fähigkeiten-Registry (Navigation).** Konsolidiert die bisher **zwei Register**
> + die bislang **unsichtbaren Rechen-Engines** zu **EINER Wahrheit**, aus der die Navi
> datengetrieben rendert."*

**Drei Quellen, eine Liste** — *und der Kopf nennt sie einzeln:*

```text
1  die echten Werkzeuge aus toolRegistry.TOOL_DEFINITIONS
2  die 13 reinen Rechen-Engines aus geometry/* als art:'engine'
     „Panels folgen in Batch 1–3 — hier nur SICHTBAR machen"
3  eine CAD-sinnvolle Teilmenge aus toolCatalog.TOOL_KATALOG,
     remappt in die Gruppe 'werkzeuge'
```

> **Und was ausdrücklich NICHT übernommen wird, steht dabei:** *„literale DTP-Tools
> (Text/Bézier/Rahmen/Farbfelder/Preflight) werden **BEWUSST NICHT** übernommen (Produkt-Scope:
> manueller Bauplaner, kein DTP)."* **Eine Abgrenzung, die ihren Grund mitträgt — nicht eine
> Auslassung.**

**QUELLE 3 IST HEUTE LEER** — *gemessen, `:96`:*

```ts
const werkzeugKatalogFaehigkeiten: Faehigkeit[] = [];
```

**Der Kommentar darüber (`:91-95`) sagt, warum, und es ist wieder eine Ehrlichkeitsfrage:**

> *„Bis I2 spiegelte die Fähigkeiten-Navi eine Teilmenge des Werkzeug-Katalogs als `cad-*`-Einträge.
> **Das waren anklickbare Zeilen ohne Handler — falsche Versprechen (AUF-28).** Seit I4 stehen die
> 110 Werkzeuge dort, wo sie hingehören: in den Kategorie-Gruppen der oberen Werkzeugleiste. Die
> Navi führt wieder nur das, was sie meint — Fachbereiche und Rechen-Engines. **EINE Wahrheit je
> Sachverhalt, und keine Zeile, die etwas verspricht.**"*

**Damit ist der Dateikopf an dieser Stelle überholt:** *er nennt drei Quellen, die dritte liefert
seit I4 nichts.* **Der Bau ist nicht falsch — die Beschreibung darüber ist alt.** *`7-GRENZEN.md`.*

**Die tragende Regel des Werkzeugs steht als letzter Satz des Kopfes:**

> ***„Regel: `geometry/*`-Engines werden NUR referenziert/aufgerufen, nie geändert (Byte-Treue)."***

**Damit ist W-36 ein Verzeichnis und kein Besitzer.** *Es macht dreizehn Rechenmodule sichtbar, ohne
eines davon anzufassen.*

## Der tragende Punkt: die DRITTE von VIER Statusachsen

**Nach Yamas Auflösung zu W-40 gilt: der Schlüssel ist der TRÄGER und nicht das Wort.** *Vier Achsen
sind keine vier Wahrheiten, solange jede an ihrem eigenen Träger hängt.*

| Achse | Werte | Träger | Fundstelle | Werkzeug |
|---|---|---|---|---|
| **`SchrittStatus`** | `ok` · `prog` · `warn` · `open` | **Fahrschritt / Prüfpunkt** | `app/studioDaten.ts:163` | W-38, W-34 |
| **`ConfiguratorStatus`** | `draft` · `incomplete` · `generated` · `checked` · `approved` · `integrated` · `outdated` | **`ConfiguratorPackage`** | `geometry/configuratorPackage.ts:26`, Feld `:72` | W-40, W-42 |
| **`FaehigkeitZustand`** | `verfuegbar` · `voraussetzung` · `nur_ergebnis` · `in_entwicklung` | **`Faehigkeit`** | **`tools/faehigkeiten.ts:25`** | **W-36 — HIER** |
| **`WerkzeugAnzeige`** | `system` · `aktiv` · `gesperrt` · `angeheftet` · `empfohlen` · `weitere` | **Werkzeug** | `tools/werkzeugZustand.ts:30` | **kein Werkzeug im Register** |

**Alle vier Fundstellen einzeln geöffnet.** *Die vierte trägt SECHS Werte — das Auftragsblatt kürzt
sie mit `'system' | 'aktiv' | …` ab und behauptet keine Zahl; hier stehen sie vollständig.*

> **Warum die Träger im Blatt stehen müssen:** *W-40 hat zwei Nachbesserungsrunden gekostet, weil ich
> die Gültigkeitsachse an den Schritt gehängt habe statt ans Paket.* **Wer die Achsen ohne Träger
> aufzählt, baut denselben Fehler ein drittes Mal ein.**

## Wann greift der Anwender darauf zu?

**`FaehigkeitenNavi` ist die Fläche, die diese Liste zeigt** — *76 Zeilen, **ein** Export.* **Der
Anwender sucht dort eine Fähigkeit, findet sie in einer von neun Gruppen und sieht an ihrem
Zustand, ob er sie schon benutzen kann.**

**Der Typ nennt VIER Zustände. In den Daten kommen ZWEI vor.**

```text
grep -o "zustand: '[a-z_]*'" faehigkeiten.ts | sort | uniq -c
  9  zustand: 'verfuegbar'          <- TEXTVORKOMMEN, nicht Faehigkeiten
  5  zustand: 'in_entwicklung'
  0  zustand: 'voraussetzung'
  0  zustand: 'nur_ergebnis'
```

> **BERICHTIGT: die neun sind KEINE neun Fähigkeiten.** *Eines der neun Vorkommen steht in einer
> `.map()` (`:68`) und erzeugt so viele Einträge, wie `TOOL_DEFINITIONS` lang ist.* **Wer die
> Textstellen zählt und Fähigkeiten meint, zählt falsch — mein erster Griff war genau das.**
>
> **Die echte Zahl steht in einem Test, und er zählt sie in drei Summanden:**
>
> ```ts
> schienenReiter.test.ts:95-98
> assert.equal(alleFaehigkeiten().length, 22 + EIGENE_WERKZEUGE.length + AUS_PAKET_GEHOBEN.length);
>                                         // toolRegistry.ts:335 ['kontur']              = 1
>                                         // toolRegistry.ts:332 ['bemassen','flaeche-messen'] = 2
> ```
>
> **`22 + 1 + 2 = 25` Fähigkeiten.** *Und der Kommentar sagt, warum die Summe zerlegt ist:* **„Eine
> Gesamtzahl hätte gedeckt, dass ein Paket-Werkzeug verschwindet, während ein gehobenes
> dazukommt."** *Dieselbe Regel, die diese Werkbank für Zählungen aufgestellt hat, hier von einem
> Test durchgesetzt.*

**Was die vier Zustandswerte angeht, bleibt die Aussage:** *nur `verfuegbar` und `in_entwicklung`
werden vergeben; `voraussetzung` und `nur_ergebnis` haben **null** Vorkommen.*

**Die Bedeutung der vier steht im Code — aber in einer ANDEREN Datei:**

```ts
app/studioUi.tsx:32-37
const ZUSTAND: Record<StudioZustand, { kurz; lang; fg; bg; rand; punkt }> = {
  verfuegbar:     { kurz: 'verfügbar',      lang: 'verfügbar – bedienbar' },
  voraussetzung:  { kurz: 'Vorauss. fehlt', lang: 'Voraussetzung fehlt (z. B. Räume/Auswahl)' },
  nur_ergebnis:   { kurz: 'nur Ergebnis',   lang: 'nur Ergebnis – kein Zeichen-Modus' },
  in_entwicklung: { kurz: 'in Entwicklung', lang: 'in Entwicklung – Panel folgt' },
};
```

> **ZWEI EIGENE FEHLGRIFFE, beide beim Nachmessen gefangen — und der zweite hebt den ersten auf.**
>
> **Erstens** *hatte ich hier vier Erklärungen hingeschrieben* — *„sie braucht erst etwas anderes",
> „sie rechnet, hat aber noch keine Bedienfläche".* **Erfunden: sie klangen plausibel und standen
> nirgends.**
>
> **Zweitens** *schrieb ich daraufhin, die Bedeutung stehe **NIRGENDS**.* **Auch falsch.** *Sie steht
> in `studioUi.tsx:32-37`, mit Kurz- und Langtext, Farbe und Punkt.* **Ich hatte in
> `faehigkeiten.ts` und `FaehigkeitenNavi.tsx` gesucht — den beiden Dateien des Auftrags — und die
> Beschriftung liegt in einer dritten.**
>
> **Der Griff dahinter ist bei beiden derselbe:** *ich habe den Suchbereich des Auftrags für den
> Suchbereich der Sache gehalten.* **Erst erfinden, dann eine Abwesenheit behaupten — beide Male
> hätte ein Blick über die zwei Dateien hinaus es verhindert.**

**Und das Feld `lang` sagt genau das, was ich erraten wollte** — *„nur Ergebnis – kein Zeichen-Modus"
statt meines „sie rechnet, hat aber noch keine Bedienfläche".* **Ähnlich, und nicht dasselbe: der
Code sagt, was FEHLT (der Zeichen-Modus), meine Fassung sagte, was NOCH KOMMT.**

**Was der Code über die zwei benutzten Zustände hergibt, und nur das:**

```ts
FaehigkeitenNavi.tsx:43-44
const istEngine  = f.art === 'engine'   && f.zustand === 'verfuegbar' && Boolean(o…);
const klickbar   = (f.art === 'werkzeug' && f.zustand === 'verfuegbar' && !!f.too…)
```

**`verfuegbar` ist die Bedingung für KLICKBAR.** *Das ist die einzige Wirkung, die ein Zustandswert
in diesem Werkzeug hat — alles andere ist Anzeige.*

> **Der Dateikopf nennt für die Engines den Zustand `'schlaeft'`** *(`:7`)* — **ein Wort, das der Typ
> nicht kennt.** *Und er sagt, sie seien „hier nur SICHTBAR" gemacht, die Panels folgten in Batch
> 1–3.* **Beides ist überholt, und die Messung zeigt es doppelt:**
>
> ```text
> grep -o "art: 'engine', zustand: '[a-z_]*'" faehigkeiten.ts | sort | uniq -c
>   8  art: 'engine', zustand: 'verfuegbar'
>   5  art: 'engine', zustand: 'in_entwicklung'
> ```
>
> **Dreizehn Engines, und ACHT davon sind `verfuegbar`** — *also klickbar (`FaehigkeitenNavi:43`).*
> **Die Panels sind für die Mehrzahl nicht mehr „Batch 1–3", sie sind da.**
>
> *Ich hatte hier zuerst geschrieben, die Engines trügen `'in_entwicklung'`* — **auch das war
> abgeleitet statt gemessen, und es stimmt nur für fünf von dreizehn.** *Der vollständige Befund
> steht in `7-GRENZEN.md`; er ist der Grund, warum dieser Auftrag geschnitten wurde.*
