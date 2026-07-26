# Dach, Zimmerei und PV — was wir schon haben, was liegen blieb, und ein Fund

**26.07.2026, Planner.** Yama: *"Wir haben eine vollstaendige, sehr ausgereifte Zimmermann- und
Dach-Konfiguration in 3D — schau dir alles genau an und setze es hier effizient ein, ohne etwas
Neues zu machen."* Und die Rueckfrage: *"Haben wir das aus Playground rueberholt, oder liegt es
dort als React-Datei?"*

**Kurze Antwort: rueberholt — bewusst und ausgewaehlt, nach einer Spezifikation vom 16.07. Und
unsere Insel ist inzwischen weiter als die Quelle.** Aber es liegen drei Dinge herum, die wir
nicht nutzen, und eines davon loest ein Problem, das ich heute gemessen habe.

---

## 1 · Was uebernommen wurde — byte-identisch

`Playground/src/utils/*` gegen `hausplaner/geometry/*`, mit Pruefsumme:

| Datei | Playground | Insel | |
|---|---|---|---|
| `dachformVorlagen.ts` | 2399 Z | 2399 Z | **identisch** |
| `dachAusschnitt.ts` | 510 Z | 510 Z | **identisch** |
| `dachUForm.ts` | 126 Z | 126 Z | **identisch** |
| `dachWerte.ts` | 103 Z | 103 Z | **identisch** |
| `dachOeffnung.ts` | 96 Z | 96 Z | **identisch** |
| `dachVerschneidung.ts` | 135 Z | **205 Z** | Insel **weiter** |

**Die Vorlagen sind da.** `dachformVorlagen.ts` mit **2399 Zeilen** ist der Vorlagenschatz, den du
meinst — er liegt seit der Uebernahme in der Insel und ist von dort testgedeckt.

## 2 · Und die Insel ist der neuere Stand, nicht der aeltere

Playground fuehrt eine **eigene Kopie derselben Hausplaner-Insel** (`src/hausplaner/`). Der
Vergleich der 3D-Renderer:

| | Playground | Insel |
|---|---|---|
| `renderers/three-d/szene.ts` | 285 Z | **663 Z** |
| `renderers/three-d/dachMesh.ts` | 108 Z | **355 Z** |
| `renderers/three-d/platzierung.ts` | 85 Z | **172 Z** |
| `renderers/three-d/segmentierung.ts` | 124 Z | 124 Z (identisch) |

**Aus Playground zurueckzuholen gibt es beim Dach-3D nichts** — wir wuerden aelteren Code
einhandeln. Das ist die wichtigste Einzelaussage dieser Untersuchung, weil sie einen ganzen
Arbeitsstrang erspart.

## 3 · Was bewusst **nicht** uebernommen wurde — und der Grund gilt weiter

`Playground/docs/hausplaner/dach-andock-spec.md`, **16.07.2026**, Planner-Entwurf. Zwei
Festlegungen daraus im Wortlaut:

> **▲D2 Keine Klassen-Transplantation.** *Die `RoofEngine`-Klasse des Prototyps
> (3786-Zeilen-@ts-nocheck-Monolith) wird NICHT uebernommen. Uebernommen wird ihre BEWIESENE
> Mathematik.*

> **▲D3 Eine Wahrheit.** *`roofConfigStore`/`roof_templates`/DachplanerPro bleiben Prototyp und
> werden vom Hausplaner NICHT gelesen.*

Gemessen: `src/pages/energie/DachplanerProPage.tsx` = **3786 Zeilen**. Die Zahl stimmt bis auf die
Zeile. **Die Entscheidung ist also nicht offen, sie ist getroffen** — und ich halte sie fuer
richtig: ein Monolith mit abgeschalteter Typpruefung (`@ts-nocheck`) ist genau die Bauart, die uns
heute den groessten Befund eingebracht hat, nur in gross.

Nicht uebernommen und weiterhin Prototyp: `roofConfigStore.ts` (344 Z), `roofVocab.ts` (101),
`roofTypes.ts` (111), `roofConfiguratorService.ts` (111), `dachproduktService.ts` (194).

## 4 · Das dritte Ding — und es steht in **ticket**, nicht in Playground

`resources/views/admin/roof_config/` und `resources/views/admin/layouts/`. Gemessen:

| Datei | Zeilen | `THREE.*` |
|---|---|---|
| `roof_config/roof.blade.php` | 1192 | **158** |
| `roof_config/roofs.blade.php` | 1231 | **121** |
| `layouts/roof.blade.php` | 2688 | **116** |
| `roof_config/config.blade.php` | 2493 (210 KB) | **0** — reine Oberflaeche, kein 3D |

Erreichbar ueber `/roof`, `/roofs`, `/testnav` — **Testrouten, in kein Menue eingebunden.**
Daneben liegen sieben `copy`-Fassungen derselben Datei.

**Und jetzt das Entscheidende: das ist etwas anderes als unser Dach-3D.**

| | zeichnet |
|---|---|
| Insel `dachMesh.ts` | Dach**flaechen** — Verschneidung, Triangulierung, Gaube |
| Blade `roof.blade.php` | Trag**werk** — 65× `rafter`, 33× `sparren`, 12× `pfette`, 7× `latte`, `Pfanne`, `Ziegel` |

Funktionsnamen aus der Blade-Datei: `makeRafterGeometry`, `basisRafter`, `basisLatten`,
`makePfanneTileGeometry`, `makeTileQuaternion`, `buildStructure`, `dimChain`, `buildDims`.
**Das ist die Zimmerei, die du meinst** — Sparren, Pfetten, Latten, Pfannen als einzelne Koerper,
mit Masskette.

## 5 · Die Konvergenz — und sie ist der Grund, warum das jetzt zaehlt

**Die Insel rechnet das Tragwerk bereits. Sie zeigt es nur nicht.**

- `geometry/sparrenBerechnung.ts` — **heute angeschlossen** (AUF-52 Scheibe A, Votum `497215c6`
  FREIGABE): neun Eingabefelder, elf Ergebniszahlen, Eurocode 5 mit Schneelast nach DIN EN 1991-1-3.
- `geometry/schifterListe.ts`, `holzBauteile.ts`, `holzMengen.ts`, `sparrenTrennung.ts` — gebaut,
  getestet, **von niemandem gerufen** (sie stehen auf meiner Liste der 28).

**Die Blade-Datei ist die fehlende Anzeige fuer Zahlen, die wir bereits erzeugen.** Kein neues
Vorhaben — eine vorhandene Anzeige fuer eine vorhandene Rechnung.

**Was ich ausdruecklich nicht vorschlage:** die 1192 Zeilen zu kopieren. Die Datei bringt ihre
**eigene Szene, eigene Kamera, eigenes Dachmodell** mit — sie hineinzuziehen waere eine zweite
Wahrheit ueber das Dach, genau das, was ▲D3 fuer Playground verboten hat.

**Was ich vorschlage:** die **Geometrie-Funktionen** nehmen, nicht die Datei.
`makeRafterGeometry`, `basisLatten`, `makePfanneTileGeometry`, `makeTileQuaternion` sind reine
Formgeber — sie bekommen Masse und liefern Koerper. Szene, Kamera, Auswahl und Klemmung hat die
Insel bereits (`szene.ts`, 663 Zeilen). **Die Blade-Datei ist damit eine Vorlage, kein Bauteil.**

## 6 · Der Fund — `belegungStatus.ts`

In Playground, 42 Zeilen, in der Insel **nicht vorhanden**. Sein eigener Kopfkommentar:

> *"Eine vorhandene PV-Belegung darf bei Aenderungen an Gebaeude- oder Dachgeometrie nicht mehr
> STILL verschwinden. Sie wird stattdessen als „pruefpflichtig" markiert, der Nutzer wird gewarnt,
> und abhaengige Kennzahlen gelten nicht mehr als gesichert gueltig."*
> *"Bewusst rein (keine React-/THREE-Abhaengigkeit) und damit vollstaendig per Unit-Test
> pruefbar."*

**Das ist woertlich der Befund, den ich heute gemessen habe** — nur fuer PV statt fuer Raeume:
eine gekuerzte Wand laesst einen Raum still verschwinden und hinterlaesst eine plausible
20,00-m²-Zahl. Ich habe heute Nachmittag zwei Bauarten einer Absicherung gemessen und die
differenzielle als die richtige benannt. **Jemand hat dieselbe Krankheit schon einmal behandelt,
das Mittel ist rein, testbar und liegt seit Wochen ungenutzt.**

Dazu: `geometry/pvBelegung.ts` (75 Z) **ist** in der Insel — aber sie steht auf meiner Liste der 28
ohne Aufrufer. **Wir haben die Belegung uebernommen und die Statuslogik dazu liegen lassen.**

---

## Was ich vorschlage — und was nicht

**Kein neuer Posten (§14).** Alles hier geht auf die Befundliste. Was ich empfehle, sobald die
Reihe frei wird, in dieser Rangfolge:

1. **`belegungStatus.ts` als Muster fuer die Wandecke.** Nicht kopieren — lesen. Es beantwortet
   die Frage, die ich Yama heute offen vorgelegt habe (melden statt verhindern), mit einem
   erprobten Zuschnitt. **Bester Nutzen je Aufwand von allem hier.**
2. **Die vier Formgeber aus `roof.blade.php`** als Vorlage fuer die Tragwerks-Anzeige, gespeist aus
   `sparrenBerechnung` (heute abgenommen) und `holzBauteile`/`holzMengen`. Damit fallen drei der 28
   ungerufenen Module weg — und zwar durch Anschluss, nicht durch Neubau.
3. **Nichts aus Playground zurueckholen.** Die Mathematik ist byte-identisch da, das 3D ist bei uns
   neuer, und der Monolith ist bewusst draussen.

**Und eine Aufraeum-Beobachtung, kein Vorschlag:** in `roof_config/` liegen **sieben `copy`-Fassungen**
derselben Datei (`config.blade copy 2..7.php`). Solange niemand weiss, welche davon der Stand ist,
ist jede Uebernahme daraus ein Ratespiel. **Bevor jemand daraus etwas holt, gehoert geklaert,
welche Datei gilt** — das ist eine Frage an Yama, keine Planner-Entscheidung.


---

# NACHTRAG — Yama widerspricht, und er hat recht. Die Messung dazu

Yama: *"Ich habe das so sauber detailliert ausgearbeitet, sehr viele Vorlagen gebaut, all das
brauchen wir hier dringend, und die Qualitaet ist viel besser als hier."*

**Meine Formulierung oben war schief.** Ich habe Zeilenzahlen verglichen und daraus "die Insel ist
weiter" gemacht. **Mehr Zeilen ist kein Qualitaetsmass.** Die richtige Frage lautet nicht, wo mehr
Code steht, sondern **ob die Arbeit ankommt.** Die habe ich jetzt gestellt.

## Die Vorlagen sind hier — und niemand ruft sie

`geometry/dachformVorlagen.ts`, **2399 Zeilen**, byte-identisch aus Playground uebernommen. Inhalt:
Sattel, Pult, Walm, Flach in mehreren Deckungen und Neigungen, dazu **Zeltdach, Krueppelwalm,
Mansard, Mansardwalm, Schleppdach, versetztes Pult, Schmetterling, Grabendach, Sheddach,
Tonnendach** — und eine durchdachte Mechanik dahinter: `standardAufbauten()`, `hauptflaecheId()`,
`ENGINE_FLAECHEN`, mit Kommentaren, die erklaeren, **warum** ein L-/T-/U-Grundriss keine
eindeutige Hauptflaeche hat und deshalb nichts automatisch gesetzt wird. Das ist sorgfaeltige
Arbeit, und sie ist nicht meine.

**Gemessen, wer sie zur Laufzeit ruft: niemand.**

```
renderers/three-d/dachMesh.ts:13   import type { EngineRoofShape } from '.../dachformVorlagen';
domain/roofShape.ts:3              (nur im Kommentar erwaehnt)
```

**Beide Verweise sind `import type` bzw. Prosa.** Ein `import type` verschwindet beim Uebersetzen
restlos — er ruft nichts, er erzeugt nichts, er haelt nichts am Leben. **Zur Laufzeit ist die
Datei tot.**

## Und das korrigiert meine eigene Zaehlung von heute Nachmittag

Mein Waisen-Zaehler hat jeden `from '...'`-Treffer als Aufrufer gewertet — **auch reine
Typ-Importe.** Neu gemessen, mit Unterscheidung:

```
Module der Insel (ohne Tests)                       128
alte Zaehlung (jeder Import zaehlt)                  25
KORRIGIERT (nur echte Laufzeit-Aufrufer)             28
```

Drei Module kommen dazu, weil sie **nur** als Typ erreicht werden:
**`dachformVorlagen`** · `treppeSvg` · `toolTypes`.

*(`toolTypes` ist zu Recht ein reines Typmodul — kein Mangel. Die anderen beiden nicht.)*

**Das ist derselbe Fehler wie meine Tafelzaehlung heute Morgen:** ein Zaehler, der die **Form**
prueft statt die **Wirkung**. Ein Import sieht aus wie ein Aufruf und ist keiner. Vierter Fall
desselben Musters an einem Tag.

## Was der Nutzer davon sieht

| | |
|---|---|
| Vorlagen in der Datei | rund **150** Eintraege, davon **11** mit `status: 'verfuegbar'` |
| Dachformen, die das Schema zulaesst | **8** (`sattel walm pult flach rect l-shape t-shape u-shape`) |
| Auswahl in der Oberflaeche | ein `<select>` mit genau diesen 8 (`HausplanerApp.tsx:1885`) |
| Zeltdach, Krueppelwalm, Mansard, Schmetterling, Shed, Tonnendach | **nicht waehlbar** |

**Deine Einschaetzung stimmt also, und der Grund ist nicht, dass es fehlt.** Es liegt hier, es ist
sorgfaeltig gebaut, und die Oberflaeche reicht acht Knoepfe davon durch.

## Wo deine Aussage uneingeschraenkt zutrifft

**Beim Tragwerk.** Die Insel zeichnet Dach**flaechen**. Sie zeichnet **kein einziges Bauteil**:
kein Sparren, keine Pfette, keine Latte, keine Pfanne. Die Blade-Datei tut es
(65× `rafter`, 33× `sparren`, 12× `pfette`, 7× `latte`, `Pfanne`, `Ziegel`, plus Massketten).
**Da fehlt hier wirklich etwas, und dort ist es fertig.**

## Und der Fund bleibt der Fund

`belegungStatus.ts` (Playground, 42 Zeilen, hier nicht vorhanden) behandelt genau die Krankheit,
die ich heute an der Raumerkennung gemessen habe: *nicht still verschwinden, sondern als
pruefpflichtig markieren.* Rein, testbar, ohne React und ohne THREE. **Auch das ist deine Arbeit,
und auch die liegt ungenutzt.**

---

## Korrigierte Rangfolge

1. **`dachformVorlagen` anschliessen** statt neue Dachformen zu bauen. Der Engpass ist die
   8-Werte-Liste `RoofShape` und der `<select>` daneben — **nicht die Vorlagen.** Ob `RoofShape`
   erweitert wird, ist eine Schema-Entscheidung und gehoert Yama (dieselbe Frage wie bei den 20
   Werkzeugen ohne Schema-Platz).
2. **`belegungStatus.ts` als Muster** fuer die Wandecke.
3. **Die vier Formgeber aus `roof.blade.php`** fuer die Tragwerks-Anzeige, gespeist aus
   `sparrenBerechnung` (heute abgenommen) und `holzBauteile`/`holzMengen`.
4. **Waechter gegen die Wiederholung:** ein Zaehler, der Module meldet, deren einzige Verweise
   `import type` sind. Er haette heute drei gefunden — und er kostet weniger als eine der
   Vorlagen.


---

# NACHTRAG 2 — Die ganze Kette, Glied fuer Glied gemessen

Yama nennt sie namentlich: *"Dachstuhl-Aufbau, Gauben, Dachfenster, Kamine, Luefter, Solarmodule,
Unterkonstruktion, Dachhaken, Montagesystem, dann Stueckliste, dann Auslegungstool fuer
Wechselrichter."*

Gezaehlt wurde, in **wie vielen Dateien** je Baum der Begriff vorkommt — Insel
(`hausplaner/`), Blade (`views/admin/`), Playground (`src/`):

| Glied | Insel | Blade | Playg. | Befund |
|---|---|---|---|---|
| Sparren | **29** | 18 | 22 | rechnet: `sparrenBerechnung` **heute angeschlossen** (AUF-52 A) |
| Pfette | 4 | 2 | 5 | duenn ueberall |
| Latte | 15 | **21** | 9 | Blade zeichnet sie, Insel nicht |
| Kehlbalken | 1 | 0 | 1 | kaum vorhanden |
| Gaube | **25** | 4 | 16 | Insel am staerksten (`gaubeGeometrie.ts`) |
| Dachfenster | **20** | 4 | 14 | Schema `RoofAufbau` nennt es ausdruecklich |
| Kamin | 19 | **22** | 19 | in allen dreien |
| Luefter | 9 | 7 | 9 | duenn |
| Modul (PV) | 57 | **83** | 11 | Belegung: `pvBelegung.ts` (75 Z) — **ungerufen** |
| **Dachhaken** | **1** | 4 | 3 | **echte Luecke, ueberall duenn** |
| **Unterkonstruktion** | **1** | 1 | 4 | **echte Luecke** |
| Montagesystem / Schiene | 13 | 13 | 5 | verstreut, kein Modell |
| Stueckliste | 6 | 2 | **9** | Playground am weitesten |
| **Wechselrichter / MPPT** | **0** | **18** | **0** | **nur im CRM, nicht im Planer** |

## Drei Aussagen, die daraus folgen

**1. Der Dachstuhl ist gerechnet, aber nicht gezeigt.** `sparrenBerechnung` ist seit heute
angeschlossen und abgenommen; `schifterListe`, `holzBauteile`, `holzMengen`, `sparrenTrennung`
stehen auf der Waisenliste. Die Blade-Datei zeichnet Sparren, Pfetten, Latten und Pfannen als
Koerper. **Die Anzeige und die Rechnung existieren beide — sie kennen einander nicht.**

**2. Dachhaken, Unterkonstruktion und Montagesystem sind die einzige echte Luecke der Kette.**
Ueberall duenn, in keinem der drei Baeume ein Modell. Alles andere ist irgendwo gebaut.
*Das ist die Stelle, an der wirklich etwas Neues entstehen muesste* — und sie haengt an einer
Frage, die schon offen ist: Herstellerdaten, Artikelnummern, Bibliotheksobjekte.

**3. Die Wechselrichter-Auslegung existiert — im CRM, nicht im Planer.**
`resources/views/admin/energie/wr_auslegung.blade.php` (193 Z) und
`wr_auslegung_dokument.blade.php` (184 Z, offenbar der Ausdruck dazu). Im Planer: **null Treffer**
fuer `wechselrichter` und `mppt`.

**Und genau hier gilt, was ich heute schon zum Produktstamm geschrieben habe:** die Auslegung
gehoert dorthin, wo `ProductPV`, `ProductWP` und `DistributorPrice` liegen — ins CRM. **Der Planer
soll sie nicht nachbauen, er soll sie fuettern**: Dachflaeche, Azimut, Neigung, Modulzahl,
Strangbildung. Die Andock-Spec vom 16.07. nennt genau diesen Vertrag bereits: *"neuer, EIGENER
Vertrag `dach_flaechen[]` (flaeche_m2, azimut_grad, neigung_grad, first_laenge_mm) … Das ist die
belastbare Quelle fuer PV/Heizlast."*

**Die Verbindung ist also entworfen und nicht gebaut.** Sie ist kein neues Vorhaben, sondern eine
benannte Naht zwischen zwei Dingen, die es beide gibt.

## Reihenfolge, die sich daraus ergibt

1. **`dachformVorlagen` erreichbar machen** (Engpass: 8-Werte-`RoofShape`, nicht die Vorlagen).
2. **Tragwerk zeigen** — Formgeber aus `roof.blade.php`, gespeist aus `sparrenBerechnung`.
   Nimmt drei Waisen von der Liste.
3. **`pvBelegung` anschliessen** + `belegungStatus` als Statuslogik daneben.
4. **`dach_flaechen[]` liefern** — der Vertrag steht schon im Papier vom 16.07.; damit bekommt die
   vorhandene WR-Auslegung im CRM ihre Eingangsdaten.
5. **Dachhaken / Unterkonstruktion / Montagesystem** — als Letztes, weil es das Einzige ist, das
   wirklich neu gebaut werden muss, und weil es die Bibliotheksfrage voraussetzt.

**Nichts davon ist heute ein Posten (§14).** Alles steht auf der Befundliste.
