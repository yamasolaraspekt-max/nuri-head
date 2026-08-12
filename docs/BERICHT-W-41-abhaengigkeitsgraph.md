# Baubericht W-41 — Abhängigkeitsgraph, vorgegeben

```yaml
auftrag: "W-41"
rolle: "generator"
blatt: docs/auftraege/aktiv/W-41-abhaengigkeitsgraph.md
art: "STUFE 6 · VORGABE (Ziel ENTWORFEN) — kein Produktivcode"
in_arbeit_commit: "7e25448d"
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **W-41 ist die dünnste der drei Vorgaben, und das Blatt sagt es selbst.** *Die Quelle führt den
> Abhängigkeitsgraphen ausdrücklich unter „nicht gemessen" — wer das überspielt, verkauft eine
> Vermutung als Erhebung.*

## Was gebaut wurde

```text
docs/rollenkette/werkbank/02-WERKZEUGE/W-41-abhaengigkeitsgraph/
  1-ZWECK.md  2-FUNKTION.md  3-FORMELN.md  4-BEDIENUNG.md
  5-CODE/LIESMICH.md  6-PRUEFUNG.md  7-GRENZEN.md
REGISTER.md   Zeile 128:  LEER -> ENTWORFEN
```

**Kein Produktivcode.** *Keine Datei außerhalb der Werkbank berührt.*

## W-41-1 · Das Verbot ist der Kern, nicht die Propagierung

**Wörtlich aus `REGISTER.md:128`, am Bau-Stand gelesen:**

> *„Änderungen propagieren, **niemals** stille Löschung."*

**`1-ZWECK` trennt beides ausdrücklich:** *ein Graph, der Änderungen weiterträgt, ist gewöhnliche
Technik — ein Graph, der nichts stillschweigend wegwirft, ist eine **Ehrlichkeitskonstruktion**.*
**Wer W-41 als Invalidierungs-Cache beschreibt, verfehlt seinen Zweck.**

*Die Verbindung zum teuersten Fehler des Projekts steht dabei:* **ein Dach, das unsichtbar
verschwand, weil der Renderer die Absage schluckte** — *dieselbe Form, nur für abgeleitete Werte.*

## W-41-2 · Die Grenze zu W-40

```text
W-40 sagt   DASS es outdated gibt und was der Zustand BEDEUTET.
W-41 sagt   WANN er eintritt, WORAUF er sich fortsetzt, und WAS dabei erhalten bleibt.
```

**W-41 definiert `outdated` nicht neu, es verweist.** *Ohne diese Grenze entstünden zwei Orte für
eine Wahrheit — der Befund aus A-20, eine Ebene höher.*

## W-41-3 · Die Quelle führt den Graphen unter NICHT GEMESSEN

**Beide Fundstellen am Bau-Stand gelesen, nicht aus dem Auftragsblatt übernommen:**

```text
BERICHT-PROZESSEBENE-DREI-FRAGEN.md:147
  „Ob es einen Abhaengigkeitsgraphen gibt. Ich habe nach status/revision gesucht,
   nicht nach Kanten zwischen Bauteilen."
:191
  nicht_gemessen: „… · Abhaengigkeitsgraph · …"
```

**Und der Satz des Verfassers dazu steht mit im Blatt:** *„Nach fünf Messfehlern an zwei Tagen
schreibe ich lieber vier Lücken hin als eine Vermutung."*

## W-41-4 · Die Anschlussliste — als Frage, mit EINER belegten Kante

| Kante | Stand | Beleg |
|---|---|---|
| **Dachfläche → PV-Belegung** | **BELEGT** | `geometry/pvBelegung.ts:10-14` — `pvSchnellBelegung(e: PvEingabe)` nimmt `dachLaenge` und `dachBreite` |
| Dachkontur → Dachflächen | Kandidat | *nicht gemessen* |
| Geschossgeometrie → Dachkontur | Kandidat | *nicht gemessen* |
| Dachflächen → Stückliste (W-20) | Kandidat | *nicht gemessen* |
| Öffnungen → Wandflächen | Kandidat | *nicht gemessen* |
| Konfigurationspaket → Gebäudemodell | Kandidat | *der Schreibpfad ist selbst nicht gebaut — W-42* |

> **Genau eine Kante ist belegt, und es ist ausgerechnet die, an der Yamas L-9 hängt** — *„PV erst
> nach bestätigter Dachgeometrie".* **Die Bedingung liefert W-40 (`confirmed`), die Kante dieser
> Beleg — der Mechanismus dazwischen fehlt, und das ist W-41.**

**Die fünf übrigen sind als Kandidaten gekennzeichnet und nicht als Ergebnis.** *W-41-4 sagt es
wörtlich: „Eine erfundene Struktur ist der schwerere Fehler als eine kurze Liste."*

## W-41-5 · Was erhalten bleiben muss

```text
der alte Wert    sonst ist es eine Loeschung mit anderem Namen
der Zeitpunkt    sonst laesst sich nicht sagen, ob die Invalidierung vor oder nach
                 einer Bestaetigung lag
der GRUND        welche Aenderung sie ausgeloest hat
```

> **Der Grund ist der Punkt, an dem sich „propagieren" von „löschen" unterscheidet** — *und die
> Quelle gibt ihn **nicht** her.* **Als Vorgabe aufgenommen, weil W-41-5 sonst nicht prüfbar wäre;
> als Beleglücke in `7-GRENZEN` benannt.**

**Ein Vorbild für die Form steht im Bestand:** *`markiereVeraltet` führt `updatedAt` und
`updatedBy`* — **der Grund fehlt auch dort.**

## W-41-6 · Sieben Blätter, Gegenprobe grün

```text
Blatt                W-41      Vorlage   gleich?   Dublette unter 28 Werkzeugen?
1-ZWECK.md           de59fdf3  e921aa08  nein      keine
2-FUNKTION.md        869b25cc  20e1ac73  nein      keine
3-FORMELN.md         f3fcd01f  a7d05b09  nein      keine
4-BEDIENUNG.md       d9668b41  9845bcf1  nein      keine
5-CODE/LIESMICH.md   c9d9aab0  619cf07e  nein      keine
6-PRUEFUNG.md        822883b6  719012f0  nein      keine
7-GRENZEN.md         7ea98362  a5b225f8  nein      keine
```

## Meine eigene erste Aussage war zu stark, und sie steht berichtigt im Blatt

**Ich hatte in `1eedb9cf` und `d0d7ec44` gemeldet, W-41s Prämisse „es gibt keinen Code" sei zu weit
gefasst** — *und das zweimal als Grund benutzt, W-41 nicht zu ziehen.*

```text
markiereVeraltet, Aufrufer ausserhalb der Tests   0
Kanten · Graph · Propagierung                     0
Treffer auf „abhaengig"                            Produktmerkmale in dachformVorlagen.ts
                                                   + ein useMemo-Kommentar
```

> **Markieren und propagieren sind zwei Dinge.** *Der Zustand existiert, die Markierfunktion
> existiert **ohne Aufrufer**, der Graph und die Propagierung nicht.* **Und genau darum geht es in
> W-41 — die Prämisse trägt.** *Mein Einwand traf W-40, wo er auch angekommen ist (der Planner hat
> ihn in `c4973de2` übernommen und die Fachfrage an Yama eskaliert); für W-41 war er überzeichnet.*

## must_preserve und Rückweg

| | Ergebnis |
|---|---|
| `resources/**` · `app/**` | **0 Dateien geändert** — nur gelesen |
| `docs/STATUS.md` | nur W-41s eigener Zustand |
| Rückweg | reine Neuanlage plus **eine** geänderte Registerzeile; `git revert` genügt |
