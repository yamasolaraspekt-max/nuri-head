# W-33 · Start und Projektwahl — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt.
> Keine abgeschriebenen Formeln.**

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **keine** | — | — |

**Ein Startbildschirm rechnet nicht.** *Er zeigt an, was ihm gereicht wird.* **Zwei Stellen bilden
trotzdem etwas ab, und sie stehen hier, weil beide ein Verhalten tragen.**

## Die zwei Stellen, die keine Rechnung sind — und was sie stattdessen tun

### 1 · Die Zeile unter dem Projektnamen

```ts
:106   const zeile = [z.ort, z.datum].filter(Boolean).join(' · ');
:117   {dominant ? [zeile, 'zuletzt bearbeitet'].filter(Boolean).join(' · ') : zeile}
```

**`filter(Boolean)` ist die ganze Regel:** *ein leerer Ort oder ein leeres Datum erzeugt **kein**
hängendes Trennzeichen.*

```text
Ort und Datum      „Musterstadt · 26.07.2026"
nur Datum          „26.07.2026"            und nicht „ · 26.07.2026"
weder noch         „"                      die Zeile bleibt leer statt „ · "
dominant           „Musterstadt · 26.07.2026 · zuletzt bearbeitet"
```

> **Der Controller liefert `ort` als `(string) ($o->city ?? '')`** — *ein leerer Ort ist also der
> Normalfall und kein Fehler.* **Die Zeile ist auf ihn ausgelegt.**

### 2 · Die Aufteilung dominant / übrige

```ts
:221   <ProjektKachel z={projekte[0]} dominant />
:222   {projekte.length > 1 && (
:224     projekte.slice(1).map((z) => <ProjektKachel key={z.id} z={z} dominant={false} />)
```

**Der erste Eintrag ist immer der dominante, die übrigen sind die Reihe.** *Die Reihenfolge kommt
fertig vom Server (`orderByDesc('updated_at')`) — die Fläche sortiert nicht um.*

**Der Grenzfall ist gedeckt:** *bei genau einem Projekt greift `:222` nicht, und es steht nur die
dominante Kachel da — keine leere Reihe darunter.*

> **`key={z.id}`** *(`:224`)* — **die Kennung kommt aus den Daten und wird nicht erfunden.** *Bei
> `projekte[0]` steht kein `key`, und das ist richtig: ein einzelnes Element in keiner Liste braucht
> keinen.*

## Die Größen, die aus `dominant` folgen

**Keine Rechnung, eine Verzweigung — aber sie steht hier, weil es ZEHN Stellen sind und ein
Werkzeugblatt sagen muss, dass es eine einzige Bedingung ist:**

```text
:110   Kachelbild   46 / 38 px, Radius 13 / 11
:111   Ikon         24 / 20 px
:114   Rubrik „Weiterarbeiten"     nur dominant
:115   Schriftgroesse  17 / 13.5,  Gewicht 800 / 700
:116   Metazeile       12.5 / 11.5
:120   Dehnfuge        nur dominant
:121   Pfeil           nur dominant
:126   Abstand      14 / 12
:129   Polster      16px 20px / 12px 16px
:132   Mindestbreite  0 / 230,  dominant zusaetzlich maxWidth 560
```

**Zehn Unterschiede, eine Bedingung.** *Es gibt keine zweite Komponente für die dominante Kachel —
dieselbe Funktion trägt beide Gestalten.*

> **Das ist eine Entwurfsentscheidung mit einer Folge:** *wer die dominante Kachel ändert, ändert
> immer auch die kleine, und umgekehrt.* **Zwei Komponenten wären zwei Wahrheiten über dieselbe
> Kachel gewesen; eine Komponente mit zehn Verzweigungen ist die andere Seite derselben Münze.**

## Genauigkeit

**Gegenstandslos.** *Der Bildschirm zeigt Zeichenketten; die einzige Zahl ist die Kennung, und die
kommt als `(int)` vom Server.* **Das Datum wird ebenfalls dort formatiert
(`format('d.m.Y')`) — die Insel formatiert kein Datum und kann es deshalb auch nicht falsch
formatieren.**
