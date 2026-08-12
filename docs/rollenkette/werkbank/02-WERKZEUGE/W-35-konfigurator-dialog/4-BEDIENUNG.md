# W-35 · Konfigurator-Dialog — BEDIENUNG

## Aufruf

```ts
:45
export function ConfigWizard({ art, standalone = true, onClose, onÜbernehmen }: Props)
```

| Eingang | Art | Bedeutung |
|---|---|---|
| `art` | `KonfigArt` | **welches Bauteil** — bestimmt Katalog, Titel, Vorbelegungen und Zieltyp |
| `standalone` | `boolean`, **Vorbelegung `true`** | **welche Beschreibung** der Dialog von sich gibt |
| `onClose` | `() => void` | Schließen — auch aus dem Hintergrund (`:60`) und aus `useDialogFokus` (`:57`) |
| `onÜbernehmen` | `(nachricht: string) => void` | **die Meldung**, die nach dem letzten Schritt zurückgeht |

> **`onÜbernehmen` trägt einen Umlaut**, ebenso `katalogFür` (`:36`). *Gemessen, beide Richtungen:*
>
> ```text
> grep -c "katalogFuer\|onUebernehmen"   ->  0
> grep -c "katalogFür"                   ->  2
> grep -c "onÜbernehmen"                 ->  6
> ```
>
> **Das ist H-9 in der harmlosen Form, und genau deshalb steht die Schreibweise des Codes in diesem
> Blatt und nicht die bequeme** — *wer die bequeme sucht, findet nichts und hält es für nicht
> vorhanden.*

## Ablauf am Bildschirm

| Schritt | Anwender sieht | Was er tut |
|---|---|---|
| **0 Bauart** | *„Bauart wählen — {n} Typen als Premium-Icons"* (`:100`), die Kacheln aus `katalogFür` | eine Kachel wählen; **eine ist von Anfang an gewählt** (`kacheln[0]`) |
| **1 Maße** | zwei Zahlfelder; bei der Treppe heißt das zweite **„Geschosshöhe (mm)"** (`:118`) | Zahlen eintippen — auf 100 mm nach unten geklemmt |
| **2 Material** | ein Auswahlfeld, bei Fenster/Tür ein zweites für die Verglasung (`:124-127`) | auswählen — **und die Wahl wird nirgends aufgenommen**, siehe `7-GRENZEN.md` |
| **3 Prüfung** | drei feste Zeilen: zwei Haken, eine Warnung (`:133-135`) | lesen — **es gibt nichts zu tun** |
| **4 Übernehmen** | ein Satz, der sagt, was entstehen wird (`:146-148`) | auf **„Übernehmen"** klicken |

**Die Beschriftung des Weiter-Knopfes wechselt** (`:265`): *`letzter ? 'Übernehmen' : 'Weiter'`.*

## Die Schrittpunkte sind anklickbar — in beide Richtungen

```ts
:86   <div role="button" tabIndex={0} onClick={() => setSchritt(i)}
            onKeyDown={(e) => { if (istAusloeser(e)) setSchritt(i); }} …>
:87   {i < schritt ? '✓' : i + 1}        erledigte Schritte tragen einen Haken
:88   {i === schritt && <span …>{n}</span>}   NUR der laufende Schritt zeigt seinen Namen
```

**Man kann jederzeit auf jeden Schritt springen, vorwärts wie rückwärts, mit Maus und Tastatur.**
*Der Haken bei `i < schritt` bedeutet „schon besucht", nicht „geprüft".*

> **Der Dialog führt, aber er hält nicht auf.** *Ein Sprung von Schritt 0 direkt auf 4 ist erlaubt;
> dann gelten die Vorbelegungen und `kacheln[0]`.* **Das ist kein Mangel, solange man es weiß — und
> es steht deshalb hier und in `7-GRENZEN.md`.**

## Rückmeldungen — und sie sagen die Wahrheit, weil sie bewacht werden

| Lage | Text | Fundstelle |
|---|---|---|
| autark **mit** Speicherrecht | *„Ergebnis: gespeichert in deiner Paketliste — und zusätzlich als Datei zum Herunterladen."* | `:147` |
| autark **ohne** Speicherrecht | *„Ergebnis: eine Datei zum Herunterladen."* | `:148` |
| im Projekt | *„Als Fachobjekt speichern — als ein Command ins Gebäudemodell, Undo/Redo inklusive."* | `:148` |
| Fußzeile | *„Status: Entwurf · Ergebnis: Paketliste + Datei"* / *„Datei zum Herunterladen"* / *„Undo/Redo im Modell"* | `:164` |

**Nach dem Klick meldet der Dialog, was WIRKLICH geschehen ist — Weg für Weg** (`:255-264`):

```ts
const teile: string[] = [];
if (gespeichert) teile.push('in deiner Paketliste gespeichert');
if (entstanden)  teile.push(`als Datei „${dateiname}" heruntergeladen`);
onÜbernehmen(teile.length > 0 ? … : `${wahl.label}: Es ist nichts entstanden — weder gespeichert
                                     noch heruntergeladen. …`);
```

> **Kein „gespeichert", wenn nur der Download geklappt hat, und kein Erfolg, wenn beides
> fehlschlug.** *Das ist das Ergebnis von AUF-74 und AUF-81, und `konfiguratorEhrlich.test.ts` sowie
> `paketSpeichern.test.ts` halten jede dieser Zeilen fest.* **Wer hier aufräumt, bricht Tests — und
> das ist Absicht.**

## Die vier Ausgänge des Übernehmen-Knopfes

```text
1  Heizkoerper UND Szene geladen         -> ObjectNode 'radiator' ins aktive Geschoss   :184
2  Treppe      UND Szene geladen         -> ObjectNode 'stair'   ins aktive Geschoss    :205
3  Fenster/Tuer UND Szene UND WAND       -> OpeningNode in die gewaehlte Wand           :226
   ausgewaehlt
4  sonst                                 -> ConfiguratorPackage + Download (+ Speichern) :232
```

**Ausgang 4 ist der RÜCKFALL, nicht der Regelfall** — *er greift immer dann, wenn keiner der drei
anderen zutrifft.* **Insbesondere greift er auch bei geladener Szene, wenn für Fenster oder Tür
**keine Wand ausgewählt** ist.**

> **Die drei ersten Ausgänge gehören W-42** und werden hier nur benannt. *Was sie tun, steht dort;
> **dass** die Bedienung vier Enden hat, gehört hierher — denn der Anwender drückt einen Knopf und
> bekommt je nach Lage ein anderes Ergebnis.*

## Abbruch

**Drei Wege hinaus, alle auf `onClose`:**

```text
:60   Klick auf die Hintergrundflaeche
:75   der Schliessen-Knopf, aria-label „Schließen"
:57   useDialogFokus(huelle, onClose)  -> Escape, aus der gemeinsamen Regel
```

**`:68` stoppt die Weitergabe des Klicks im Dialogkörper** (`e.stopPropagation()`) — *sonst schlösse
jeder Klick im Inneren den Dialog.*

> **Der Dialog baut seinen Escape-Handler NICHT selbst** — *`dialogFokus.test.ts:108` „kein Dialog
> baut seinen Escape-Handler mehr selbst" verbietet genau das, mit
> `assert.doesNotMatch(q, /addEventListener\('keydown'/)` in `:110`.* **Eine Regel, einmal gebaut,
> und ein Test, der die Rückkehr des Eigenbaus verhindert.**

## Sichtprüfung

- [ ] **offen** — *eine reale Browserabnahme dieses Dialogs ist nicht Gegenstand dieser Ablesung.*

**Was eine Sichtprobe zuerst ansehen sollte** — *aus der Ablesung abgeleitet, nicht behauptet:*

```text
1  Schritt 2 „Material": aendert die Auswahl irgendetwas am Ergebnis?
   Nach dem Code: nein. Am Bildschirm sieht sie aus wie eine Eingabe.
2  Schritt 3 „Pruefung": zeigt die Warnung „Rastermass — 40 mm Versatz pruefen"
   auch dann, wenn die Masse glatt sind?  Nach dem Code: ja, immer.
3  Der Sprung von Schritt 0 direkt auf 4 — was entsteht dann?
```
