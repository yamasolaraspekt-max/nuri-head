# Heizlast, Waermepumpe, Heizkoerper, FBH — die Kette ist fertig, das Tor fehlt

**26.07.2026, Planner.** Yama: *"Dasselbe gilt fuer Heizlastberechnung, Auslegung von Waermepumpe,
Heizkoerper, Fussbodenheizung — die liegen alle bei ticket, wberechnung und playground … genug
Material, damit die Arbeit schnell geht."*

**Ergebnis vorweg, und es ist besser als „genug Material": die Kette ist auf beiden Seiten
gebaut. Was fehlt, ist ein Tor — und es traegt Yamas Namen im Quelltext.**

---

## Erst eine Grenze, die ich nicht ueberspiele

**`wberechnung` ist an diese Sitzung nicht angebunden.** Angebunden sind `Documents/ticket`,
`Playground` und einige `ticket-*`-Baeume. Was in wberechnung liegt, **habe ich nicht gesehen und
behaupte darueber nichts.** Dasselbe gilt fuer die Ordner auf dem Schreibtisch.

## Wo das Material wirklich liegt — gezaehlt

Dateien je Baum, in denen der Begriff vorkommt:

| Begriff | Insel | ticket `app/` | Playground |
|---|---|---|---|
| Heizlast | 22 | **58** | 4 |
| Waerme-/Wärmepumpe | 11 | **36** | 0 |
| Heizkoerper/-körper | 28 | **37** | 0 |
| Fussbodenheizung | 7 | **9** | 0 |
| DIN 12831 | 1 | **12** | 0 |
| JAZ | 0 | **12** | 0 |
| Vorlauf | 7 | **28** | 0 |

**Der Schwerpunkt liegt eindeutig im CRM, nicht im Planer** — und Playground traegt dazu **nichts**
bei. Das ist dasselbe Bild wie bei der Wechselrichter-Auslegung: **die Rechnung wohnt dort, wo die
Produkte wohnen.**

## Und die Naht zwischen beiden ist gebaut — auf beiden Seiten

**Im CRM, fertig und geroutet:**

```
routes/web.php:5000  POST /objekt/{objekt}/uebernehmen
                     -> HausplanerController::uebernehmen
                     -> middleware permission:Hausplaner,update
app/Domain/Hausplaner/Actions/UebernehmeSzeneInAuslegung.php
app/Domain/Hausplaner/Actions/ErmittleUebernahmeStatus.php
app/Services/Geometrie/SzeneProjektionService.php
app/Services/Heizlast/GeometrieAbleitungService.php
app/Models/RaumGeometrie.php · HeizlastRaum.php
```

**Im Planer, gebaut und ungerufen:**

```
projection/raumProjektion.ts   98 Z   — einziger Aufrufer: seine Testdatei
projection/dachProjektion.ts   43 Z   — einziger Aufrufer: seine Testdatei
```

## Der Satz, auf den es ankommt — er steht im Quelltext, nicht von mir

Kopfkommentar von `SzeneProjektionService.php`, woertlich:

> *"P2-1b: planare Raumerkennung (Wandachsen-Graph, T-Punkt-Teilung, Halbkanten-Umlaeufe,
> Innenflaechen = positive Shoelace). MEHRRAUM. innen/aussen: eine Kante, deren BEIDE Halbkanten in
> Innenraeumen liegen, ist 'innen' (kein Azimut); sonst 'aussen' (Azimut aus rechter Normale,
> Nord=+y). decke/boden ehrlich null. **Verdrahtung/Schreiben nach gebaeude_geometrie = P2-2
> (Yama-Go). Diese Klasse schreibt NICHTS und wird von KEINEM Produktivpfad aufgerufen.**"*

**Das ist keine Baustelle. Das ist ein fertiges Bauteil mit einem Schild davor.** Jemand hat die
Projektion vollstaendig gebaut — Mehrraum, Innen/Aussen-Unterscheidung, Azimut aus der rechten
Normale, `decke`/`boden` ehrlich `null` statt still gefuellt — und die letzte Verdrahtung
**ausdruecklich** einer Entscheidung von Yama vorbehalten.

**Und das Recht dafuer existiert bereits:** `permission:Hausplaner,update`. Anders als beim Import
(`permission.import`, das es nicht gibt) ist hier nichts offen.

## Was daraus folgt

**Es ist nicht „genug Material, damit die Arbeit schnell geht".** Es ist **fertige Arbeit, die auf
ein Wort wartet.** Die Frage an Yama ist nicht *"wollen wir das bauen"*, sondern:

> **P2-2 Go: darf `SzeneProjektionService` verdrahtet werden und nach `gebaeude_geometrie`
> schreiben?**

Was danach noch fehlt, ist die Gegenseite im Planer: `raumProjektion.ts` liefert den Vertrag
`RaumGeometrieProjektion`, und **niemand ruft sie.** Das ist derselbe Befund wie ueberall heute —
gebaut, getestet, nicht angeschlossen — aber hier haengt eine ganze Auslegungskette dahinter.

**Reihenfolge, die ich daraus ableite:**

1. **Yama-Go fuer P2-2** einholen. Kostet nichts, entscheidet alles.
2. **`raumProjektion` anschliessen** — der Planer liefert die Geometrie, das CRM rechnet.
   Nimmt zwei Waisen von der Liste (`raumProjektion`, `dachProjektion`).
3. **`dach_flaechen[]`** aus der Andock-Spec dazu — damit bekommt auch die PV-/WR-Seite ihre
   Eingangsdaten. Derselbe Weg, dieselbe Naht.
4. Erst danach ueberhaupt die Frage, ob in wberechnung oder auf dem Schreibtisch etwas liegt, das
   **fehlt**. **Bis dahin ist die Antwort: es fehlt nichts, es ist nur nicht verbunden.**

## Was ich ausdruecklich nicht vorschlage

**Nichts aus wberechnung oder von Papier abtippen, bevor Punkt 1 bis 3 stehen.** Solange die
vorhandene Kette nicht laeuft, wuerde jedes zusaetzliche Material dieselbe Halde vergroessern, die
ich heute gemessen habe: **28 Module, die richtig rechnen und die niemand ruft.**
