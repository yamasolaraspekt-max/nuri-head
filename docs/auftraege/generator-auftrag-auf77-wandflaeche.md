# ⇒ GENERATOR-AUFTRAG AUF-77 — Wandfläche brutto und netto (M1)

**Vom:** Planner · **26.07.2026, 10:00** · **Spur A** · **Heimat-App:** `ticket`
**Gesperrt bis AUF-76 abgenommen ist.** **Grundlage:**
`docs/planner/bestandsaufnahme-mengenermittlung-2026-07-26.md`, Stufe **M1**.

**Vorher gelesen:** HEAD `6cfea37` · `domain/scene.types.ts` (WallNode, OpeningNode) ·
`geometry/polygonFlaeche.ts` · `geometry/roomDetection.ts` · Bestandsaufnahme §3.1 und §8.

---

## 1. Der Befund, der diesen Posten auslöst

```
$ grep -rn "wandflaeche|nettoflaeche|bruttoflaeche" --include=*.ts .
(kein Treffer ausserhalb von Tests)
```

**Die Öffnungen liegen im Modell — aber niemand zieht sie von einer Wandfläche ab, weil es keine
Wandfläche gibt.** Auf dieser einen fehlenden Rechnung setzen Putz, Dämmung, Anstrich, Fassade und
Heizlast **alle** auf.

## 2. Was gebaut wird

**Eine neue, reine Datei `geometry/wandFlaeche.ts`** — ohne DOM, ohne Store, ohne Befehl.
**Vorhandene Dateien in `geometry/` tragen null Zeilen.**

**Eingang:** eine `WallNode`, ihre `OpeningNode`s, das Bezugsmaß.
**Ausgang je Wand:**

| Größe | Rechnung |
|---|---|
| Länge, Höhe, Dicke | aus dem Knoten |
| **Bruttofläche je Seite** | Länge × Höhe |
| **Öffnungsfläche** | Σ (`width` × `height`) der Öffnungen dieser Wand |
| **Nettofläche je Seite** | brutto − Öffnungsfläche, **voller Abzug** |
| **Volumen brutto** | Länge × Höhe × Dicke |
| **Öffnungsvolumen** | Σ (`width` × `height` × Dicke) |
| **Volumen netto** | brutto − Öffnungsvolumen |

**Jedes einzelne Ergebnis trägt sein Bezugsmaß als Pflichtangabe** — `roh` oder `fertig`. **Es gibt
keinen Rückgabewert ohne diese Angabe.** Das ist Yamas Entscheidung vom 26.07. in ausführbarer Form
und der Kern dieses Postens.

**Keine Übermessung.** Der volle Abzug ist eindeutig und von keiner Gewerkeregel abhängig. **Die
Übermessung ist eine Regel auf diese Zahlen und gehört nach M2** — sie ändert hier nichts, sie
leitet später ein drittes Ergebnis ab.

## 3. Die zwei Bezugsmaße — und wo ihre Grenze liegt

- **Roh:** `thickness` und `height` wie im Knoten. **Heute vollständig rechenbar.**
- **Fertig:** Dicke und Höhe abzüglich der Schichten aus **AUF-76**. **Fehlen die Schichten, gilt
  `fertig = roh` — und das Ergebnis sagt es ausdrücklich.** *Ein fehlender Wert darf nie
  stillschweigend zu einer geschätzten Zahl werden.*

**Ausdrücklich zurückgestellt: die Länge im Fertigmaß.** Ob eine Wand fertig kürzer ist, hängt von
den **angrenzenden** Wänden und deren Schichten ab. **Das ist eine eigene Rechnung über den
Wandverbund und gehört nicht in diesen Posten.** Bis dahin ist die Länge in beiden Bezugsmaßen
dieselbe — **und das Ergebnis benennt diese Grenze**, statt sie zu verschweigen.

## 4. Die Kanten

1. **Wand ohne Öffnungen** ⇒ netto = brutto. Kein Sonderfall, aber ein Test.
2. **Öffnung ragt über die Wand hinaus** (`offsetFromWallStart + width > Länge`) ⇒ **melden, nicht
   klemmen.** Eine stillschweigend gekürzte Öffnung erzeugt eine plausible falsche Zahl.
3. **Zwei Öffnungen überlappen** ⇒ **melden.** Das ist „Öffnung doppelt abgezogen" aus §28 der
   Vorlage und der häufigste Fehler solcher Systeme.
4. **Öffnung höher als die Wand** ⇒ melden.
5. **Höhe oder Länge 0** ⇒ Ergebnis 0, kein `NaN`, kein `Infinity`.
6. **Schichtsumme größer als `thickness`** ⇒ **melden, nicht rechnen.** Ein negatives Fertigmaß ist
   kein Ergebnis.
7. **Millimeter bleiben ganzzahlig, m² wird gerundet — und die Rundung steht an genau einer Stelle.**
   *Zwei Rundungsorte ergeben zwei Summen, die sich um Cents unterscheiden, und genau daran zerbricht
   später ein Angebot.*

## 5. Was **nicht** gebaut wird

- **Keine Oberfläche, keine Anzeige, kein Werkzeug.** Das ist eine Rechnung. Sichtbar wird sie in M3.
- **Keine Persistenz, kein Command, kein Speicherzustand.**
- **Keine Aggregation** über Raum, Geschoss oder Gebäude — das ist **M3**.
- **Keine Materialmenge** — das ist M5 und braucht die Dichte, die es noch nicht gibt.
- **Keine zweite Flächenengine.** `polygonFlaecheM2` und die Raumerkennung bleiben, was sie sind;
  wo dieselbe Rechnung gebraucht wird, wird **gelesen, nicht nachgebaut.**
- **Kein Anfassen von `store/`, `domain/`, `renderers/`, `scene.types`.**

## 6. Abnahmekriterien

1. `tsc:hausplaner` · `schema:hausplaner:check` · `test:hausplaner` — Exit 0, Zahlen vorher/nachher.
2. **Nur eine neue Datei in `geometry/`;** alle vorhandenen Dateien dort **null Zeilen**.
   `store/`, `domain/`, `renderers/`, `app/` — **null Zeilen**.
3. **Rein und ohne DOM:** `grep` belegt kein `window`, kein `document`, kein `getState`, kein
   `executeCommand`. Zweimal dieselbe Eingabe ⇒ tiefengleiches Ergebnis.
4. **Kein Ergebnis ohne Bezugsmaß:** ein Test belegt, dass **jedes** zurückgegebene Feldbündel die
   Angabe trägt. **Ein Ergebnis ohne sie muss ein Typfehler sein, keine Nachlässigkeit.**
5. **Handrechnung als Gegenprobe:** eine Wand 5 000 × 2 500 mm mit einem Fenster 1 200 × 1 400 mm ⇒
   brutto **12,5 m²**, Öffnung **1,68 m²**, netto **10,82 m²**. Im Test als Zahl, nicht als Formel.
6. **Fehlende Schichten:** Test ⇒ `fertig = roh`, **und das Ergebnis sagt es**.
7. **Die fünf Meldefälle aus §4** je ein Test — **und keiner davon liefert eine Zahl**, sondern eine
   Meldung. *Eine Wand mit einer überlappenden Öffnung darf kein Ergebnis haben.*
8. **Eine Rundungsstelle:** `grep` belegt genau einen Ort, an dem gerundet wird.
9. **Mutations-Gegenbeweis:** den Öffnungsabzug entfernen ⇒ mindestens ein Test rot; die
   Bezugsmaß-Angabe entfernen ⇒ Typfehler. Zahlen nennen.
10. **Klassifikation: `Vorarbeit`.** Für den Nutzer ändert sich nichts — noch nicht.

## 7. Was zurückgegeben wird

- **Zeigt sich, dass die Öffnungsfläche nicht ohne Laibungsbetrachtung sinnvoll ist:** benennen.
  Laibungen sind in der Vorlage eine eigene Größe und gehören nicht heimlich in den Abzug.
- **Braucht das Ergebnis eine Kennung, um später auf die Wand zurückzuzeigen** (Rückverfolgbarkeit):
  **die `nodeId` gehört hinein.** Wenn dabei mehr nötig ist als eine Kennung, ist das der Beginn von
  M2 — **melden, nicht mitbauen.**
