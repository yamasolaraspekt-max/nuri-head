# W-35 · Konfigurator-Dialog — FUNKTION

## Der tragende Punkt: `TYP_MAP` verbindet die Bedienung mit dem Paket

```ts
ConfigWizard.tsx:43
const TYP_MAP: Record<KonfigArt, ConfiguratorType> =
  { fenster: 'window', tuer: 'door', treppe: 'stair', heizkoerper: 'radiator' };
```

**Links die vier Bedienarten, rechts der Typ aus `geometry/configuratorPackage.ts`.** *Benutzt wird
sie an genau einer Stelle:*

```ts
:232-236
const paket = neuesPaket({ id, type: TYP_MAP[art], jetzt, autor: 'Solar Aspekt', … });
```

> **Wer W-35 beschreibt, ohne diese Abbildung zu nennen, trennt die Bedienung von dem Paket, das sie
> erzeugt.** *Und damit vom Anschluss an W-40: `neuesPaket` liefert ein `ConfiguratorPackage`, und
> dessen `status` ist genau die Gültigkeitsachse, die W-40 beschreibt.* **Der Weg vom Klick bis zur
> Freigabe geht durch diese eine Zeile.**

**`Record<KonfigArt, ConfiguratorType>` ist zugleich ein Wächter** — *dieselbe Bauform wie W-38s
`Record<SchrittStatus, string>`.* **Eine fünfte `KonfigArt` ohne Eintrag in `TYP_MAP` übersetzt der
Typprüfer nicht.**

## Und daneben eine Abbildung OHNE diesen Schutz

```ts
:36-41
function katalogFür(art: KonfigArt): { ordner; titel; kacheln } {
  if (art === 'fenster')  return { … FENSTER_BAUARTEN … };
  if (art === 'tuer')     return { … TUER_BAUARTEN … };
  if (art === 'treppe')   return { … TREPPEN_BAUARTEN … };
  return { ordner: 'heizkoerper', … HEIZKOERPER_TYPEN … };     // <- kein if
}
```

**Der vierte Zweig ist kein `if`, sondern der Rückfall.** *Eine fünfte Art bekäme stillschweigend den
**Heizkörper**-Katalog — kein Fehler, keine Warnung, falsche Kacheln.*

> **Zwei Abbildungen derselben vier Arten, eine typgesichert, eine nicht.** *Das ist eine Aussage
> über die Bauart und keine Feinheit: wer eine Art ergänzt, wird von `TYP_MAP` gestoppt und von
> `katalogFür` durchgelassen.* **Gemessen, nicht vermutet — der `return` ohne `if` steht in `:40`.**

## Die fünf Schritte

```ts
:34
const SCHRITTE = ['Bauart', 'Maße', 'Material', 'Prüfung', 'Übernehmen'] as const;
```

**Fünf, am Code gezählt.** *`as const` macht die Liste zur Quelle der Länge:*

```ts
:53   const letzter = schritt === SCHRITTE.length - 1;
:90   {i < SCHRITTE.length - 1 && <span className="hp-kw-strich" />}
```

**Keine feste `4` und keine feste `5` im Code** — *die Zahl wird zweimal aus der Liste gelesen.* **Wer
einen Schritt ergänzt, ändert eine Zeile.**

### Die Schrittpunkte sind SPRUNGMARKEN, keine Anzeige

```ts
:86
<div role="button" tabIndex={0}
     onClick={() => setSchritt(i)}
     onKeyDown={(e) => { if (istAusloeser(e)) setSchritt(i); }} … >
```

**Jeder der fünf Punkte springt direkt zu seinem Schritt** — *vorwärts wie rückwärts, mit Maus und
mit Tastatur, **ohne jede Prüfung**.*

> **Das ist eine Aussage über das Werkzeug: es führt, aber es hält nicht auf.** *Man kann von
> Schritt 1 sofort auf „Übernehmen" springen.* **Was dabei gilt, steht in `7-GRENZEN.md`** — *die
> Vorbelegungen sind dann die Maße, und die Bauart ist `kacheln[0]`.*

## Vier eigene Zustände, mit ARTABHÄNGIGEN Vorbelegungen

```ts
:47-50
const [schritt, setSchritt] = React.useState(0);
const [wahl,    setWahl]    = React.useState<Kachel>(kacheln[0]);
const [breite,  setBreite]  = React.useState(art === 'treppe' ? 1000 : art === 'heizkoerper' ? 1000 : 1010);
const [hoehe,   setHoehe]   = React.useState(art === 'fenster' ? 1360 : art === 'heizkoerper' ?  600 : 2010);
```

| Art | Breite | Höhe |
|---|---|---|
| Fenster | **1010** *(Rückfall)* | **1360** |
| Tür | **1010** *(Rückfall)* | **2010** *(Rückfall)* |
| Treppe | **1000** | **2010** *(Rückfall)* |
| Heizkörper | **1000** | **600** |

**Die Tabelle steht hier vollständig, weil die Rückfallwerte mitzählen.** *Das Auftragsblatt nennt
„Treppe 1000, Heizkörper 1000/600, Fenster 1360" — richtig, aber die `1010` und die `2010` fehlen
darin, und genau sie gelten für die **Tür**, die dort nicht vorkommt.*

> **Dass die Vorbelegung von der Art abhängt, ist eine Aussage und keine Feinheit** — *sie ist der
> Unterschied zwischen einem Dialog, der vier Bauteile kann, und vier Dialogen.*

**`wahl` beginnt bei `kacheln[0]`** *(`:48`)* — **also bei der ersten Bauart des jeweiligen Katalogs,
nicht bei „nichts gewählt".** *Es gibt keinen leeren Anfangszustand.*

## Die Grenze zu W-42 — gespiegelt, nicht neu gezogen

```text
W-35 IST      der DIALOG: die vier Arten, die fuenf Schritte, katalogFür, TYP_MAP,
              die Vorbelegungen, die Schrittnavigation, die Vorschau, der Fuss.
              Die Bezeichner tragen die Schreibweise des CODES, mit Umlaut —
              wer katalogFuer sucht, findet 0 Treffer.

W-35 IST NICHT
              der SCHREIBPFAD ins Gebaeudemodell. Die drei executeCommand-Stellen
              (:184 radiator, :205 stair, :226 window/door) gehoeren zu W-42 und
              werden hier NUR mit Verweis genannt.
              configuratorPackage.ts  -> eigener Gegenstand, Freigabegrade in W-40
              state/paketSpeichern.ts -> eigener Gegenstand
              W-04 (Oeffnung) und W-09 (Treppe), beide BESCHRIEBEN — sie liefern die
              FACHLOGIK, W-35 die BEDIENUNG.
```

> **Beide Werkzeuge leben in derselben Datei, und das ist ungewöhnlich genug, um es hier zu sagen** —
> *wer `ConfigWizard.tsx` öffnet, sieht W-35 und W-42 nebeneinander.* **W-42s Blatt zieht die Grenze
> gleich: „W-35 ist alles bis zur Auswahl; W-42 ist, was danach damit geschieht."** *Hier steht
> dieselbe Grenze von der anderen Seite — und keine zweite Beschreibung des Schreibpfads.*

## Was der Dialog importiert

```ts
:8-19   react · dialogFokus · paketSpeichern · studioDaten(T) · studioUi(Ikon)
        oeffnungsBauarten · treppenBauarten · heizkoerperTypen
        configuratorPackage(neuesPaket) · hausplanerStore · scene.types · treppeObjekt
```

**Zwölf `import`-Zeilen, gezählt statt abgeschätzt** (`grep -c '^import '` → **12**) — *ich hatte
zuerst elf geschrieben, weil ich die Zeilenspanne `:9-19` gezählt habe statt die Zeilen; `react` in
`:8` fiel dabei heraus.* **Drei der Einfuhren sind die Kataloge der vier Arten:** *`FENSTER_BAUARTEN`
und `TUER_BAUARTEN` (beide aus `oeffnungsBauarten`), `TREPPEN_BAUARTEN`, `HEIZKOERPER_TYPEN`.* **Der
Dialog hält keine eigene Bauartenliste; er zeigt fremde.**
