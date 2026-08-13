# W-31 · PV-Schnellbelegung — GRENZEN

## Das Wichtigste zuerst: die Grenze läuft zwischen ZWEI APPS

**Wörtlich aus `geometry/pvBelegung.ts:6-7`:**

> *„**GRENZE:** Ertrag/Verschattung/Strings bleiben der Fach-Engine (**wberechnung**) vorbehalten —
> hier nur Geometrie/Anzahl/Leistung."*

> ***Das ist eine Aussage über die Arbeitsteilung zwischen zwei Anwendungen und keine Feinheit.***
> *`ticket` sagt, **wie viele Module aufs Dach passen**. Was sie **liefern**, sagt `wberechnung`.*

**Was daraus folgt, und es ist der praktische Teil:** *`kWp` ist eine **Nennleistung** — Modulzahl
mal Nennleistung des Moduls.* **Es ist kein Ertrag, keine Jahresarbeit, keine Prognose.** *Wer die
Zahl als Ertrag weiterreicht, hat die Systemgrenze überschritten, ohne es zu merken — die Einheit
sieht in beiden Fällen technisch aus.*

| außerhalb | wo es hingehört |
|---|---|
| Ertrag (kWh/a) | `wberechnung` |
| Verschattung | `wberechnung` · W-19 |
| String-Verschaltung, Wechselrichter | `wberechnung` |
| Ausrichtung und Neigung als Rechengröße | **F-028, bei Yama** — s. u. |
| Dachgeometrie selbst | W-07 · W-08 |
| Deckungsmaterial | W-23 |

## Die Spannung, die dieses Blatt BENENNT und nicht entscheidet

**Der tragende Satz von W-31 lautet: die Schnellstufe trägt keinen Azimut.** *Gemessen: `PvEingabe`
hat **sieben** Felder und keine Richtung.*

**Und es gibt genau eine Stelle im Bedienweg, an der doch eine Richtung steht:**

```text
app/dashboard/fachFlaechen.ts:252
  { label: 'Ausrichtung und Neigung', einheit: '°' }
```

*Sie steht unter den **Eingängen** der Fachfläche `fach-pv-module` — also im Bild, das der Anwender
sieht.*

> **HEUTE STEHT SIE NICHT IN `PvEingabe`.** *Sie ist eine **Ankündigung**, kein Feld.* **Und ihre
> Verwirklichung hängt an F-028** (Azimut-Konvention an der Systemgrenze, Ampel **🔴**, aufgenommen
> auf Yamas ausdrückliche Auflage): *gesperrt ist genau das **Durchreichen eines Azimut zwischen
> zwei Konventionen*** — und das wäre der Fall, sobald diese Angabe wirklich in die Rechnung ginge.

**Das ist ausdrücklich KEIN Widerspruch und kein Mangel der Fachfläche.** *`app/FachFlaeche.tsx:4-6`
beschreibt sich selbst als **„Feldstruktur-Vorschau (deaktivierte Ein- und Ausgangsfelder mit
sichtbarem Grund)"** — **eine Vorschau darf künftige Felder zeigen, das ist ihr Zweck.***

> ***Dieses Blatt benennt die Spannung und entscheidet sie nicht.*** *Ob die Vorschau das Feld
> zeigen darf, ist keine Frage dieser Ablesung. **Was das Blatt schuldet, ist der Hinweis** — sonst
> behauptet es „kein Azimut" und lässt die einzige Stelle weg, an der eine Richtung steht, und die
> nächste Rolle findet sie beim ersten `grep` auf „Ausrichtung".*

## Wo das Modul rechnerisch aufhört

| Grenze | Beleg |
|---|---|
| **Nur RECHTECKE.** Kein Walm, keine Gaube, kein Ausschnitt, keine Dachfenster. | `PvEingabe` kennt zwei Maße |
| **Ein Randabstand für alles** — First, Traufe und Ortgang bekommen denselben Wert | `:47`, `rand` einmal, `:49-50` zweimal abgezogen |
| **Keine Teilbelegung, keine Aussparung** | die Belegung ist ein volles Raster |
| **Kein Mindestabstand zur Firstlinie aus Norm oder Landesrecht** | die 300 mm sind eine Voreinstellung, keine Vorschrift |
| **Keine Verschattung durch eigene Aufbauten** | Kamin und Gaube gehen nicht ein |

> **Die Flächennutzung wird gegen die BRUTTO-Dachfläche gerechnet** (`:62`, ohne Randabzug).
> ***100 % sind damit unerreichbar, und das ist richtig:*** *der Randabstand ist echte, nicht
> belegbare Fläche. Wer den Wert gegen die Nutzfläche erwartet, liest ihn zu niedrig.*

## Der Grenzfall, der leicht übersehen wird

**Bei Gleichstand gewinnt `hochkant`** (`:57`, `hochN >= querN`). *Das ist eine willkürliche, aber
**festgelegte** Entscheidung — und sie ist die einzige, die Determinismus sichert.* **Der Wächter
hält sie fest** (`pvBelegung.test.ts`, Zusage „Determinismus"). *Wer sie ändert, ändert bei
quadratischen Modulmaßen jedes Ergebnis.*
