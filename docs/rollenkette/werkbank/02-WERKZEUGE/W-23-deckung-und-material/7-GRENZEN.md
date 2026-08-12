# W-23 · Deckung und Material — GRENZEN

> **Dieses Blatt ist Pflicht.**
> Der teuerste Fehler des Projekts bisher: ein Dach, das bei nicht-rechteckiger
> Kontur unsichtbar verschwand statt eine Absage zu geben. Die Domäne verweigerte
> korrekt — der Renderer schluckte die Absage mit `catch { continue; }`.
> **Ein Werkzeug ohne benannte Grenze baut genau diesen Fehler wieder ein.**

## Die Füllquote — ungeschönt, weil sie die eigentliche Grenze ist

```text
Datenzeilen in DB_Produkte                       127
davon mit Lattmass min UND max                     9   ->  SIEBEN Modelle
davon mit Verschiebespiel_mm                      13
davon mit Regeldachneigung_grad                   17

Datenstatus ueber alle 127:   78x verifiziert aus PDF · 26x teilweise verifiziert
                              17x verifiziert         ·  6x offen
```

> **Neun von 127.** *Die Datenbank ist groß und gepflegt — aber der Operand, den dieses Werkzeug
> braucht, steht bei **sieben Modellen**, und alle sieben sind von **einem** Hersteller (Braas).*
> **Das ist kein Mangel der Quelle, sondern die ehrliche Lage: die Lattmaße sind ihr am wenigsten
> gepflegter Teil.**

**Die Folge, klar ausgesprochen: für jedes andere Modell kann dieses Werkzeug NICHTS sagen.**
*Nicht „ungefähr", nicht „per Vorgabe" — nichts.* **Wer fehlende Werte ergänzt, erfindet sie; genau
daran hängt das Operanden-Gate von W-21L.**

## Was dieses Werkzeug NICHT kann

| Fall | Warum nicht | Was der Anwender stattdessen sieht |
|---|---|---|
| **Modell ohne Lattmaß** (118 von 127 Zeilen) | die Quelle führt den Bereich nicht | Absage „keine Lattmaße hinterlegt", mit dem Hinweis auf die sieben belegten |
| **Neigung unter der Regeldachneigung** | Fachschranke aus der Quelle (Spalte 33) | **Absage 1** — mit beiden Zahlen |
| **Regeldachneigung fehlt** — heute `Rubin 13V`, **beide Zeilen** | die Schranke ist nicht prüfbar | **Absage 2** — die Rechnung bleibt aus |
| **Keine gleichmäßige Teilung** (`n_min > n_max`) | Teilbarkeit, nicht Fachwissen | **Absage 3** — mit beiden Nachbarwerten und dem Bereich |
| **Traufreihe · Firstanschluss · Ortgang · Restausgleich** | die Formel rechnet die **Regelfläche** (Ampel 🟡) | keine Zahl dafür — ausdrücklich außerhalb |
| **Andere Hersteller** (Creaton, Erlus, Jacobi) | stehen im Schema als Namen **ohne Maße** | Absage „keine Lattmaße hinterlegt" |

> **Die dritte Zeile ist der unangenehmste Befund dieses Blattes**, und er stammt aus der Messung,
> nicht aus dem Auftrag: *`Rubin 13V` hat in **beiden** Zeilen ein vollständiges Lattmaß **und** ein
> stimmiges Verschiebespiel — aber **keine Regeldachneigung**.* **Ausgerechnet das Modell mit der
> doppelten Datenlage kann die Schranke nicht passieren.** *Das Auftragsblatt führt für alle sieben
> eine Regeldachneigung; gemessen fehlt sie bei einem.*

## Die Absagekette

```
Schicht 1/2 wirft benannten Fehler
        ↓
Schicht 3 fängt und übersetzt
        ↓
Schicht 4 reicht DURCH — kein catch/continue
        ↓
Schicht 5 zeigt dem Anwender einen verständlichen Satz
```

| Fall | Fehlername *(Vorgabe)* | Wer fängt | Anwendertext steht in |
|---|---|---|---|
| Neigung zu gering | `NeigungUnterRegeldachneigung` | Schicht 3 | `4-BEDIENUNG.md` |
| Schranke unbekannt | `RegeldachneigungFehlt` | Schicht 3 | `4-BEDIENUNG.md` |
| Keine Teilung | `KeineGleichmaessigeTeilung` | Schicht 3 | `4-BEDIENUNG.md` |
| Kein Lattmaß | `LattmassNichtHinterlegt` | Schicht 3 | `4-BEDIENUNG.md` |

**Und die schärfere Zusage für dieses Werkzeug:** *die Absage ist hier **kein Fehler, sondern ein
Ergebnis**. Sie darf nicht als `null`, `0` oder `NaN` durch die Schichten wandern — die Rückgabe ist
eine **Union** aus Reihen **oder** benanntem Fall (`5-CODE/LIESMICH.md`).* **Eine `0` als Lattmaß
wäre der Dach-Vorfall mit anderen Zahlen.**

## Fänger-Prüfung

- [ ] Jeder Fehlerpfad ist durch einen Test belegt, der prüft: **die Meldung erreicht die Oberfläche**
- [ ] Kein `catch { }` ohne Weiterreichen im Pfad dieses Werkzeugs
- [ ] Kein stilles `return` bei ungültiger Eingabe

## Bekannte Ungenauigkeiten

| Größe | Abweichung | Ab wann stört es |
|---|---|---|
| Lattmaß `L/n` | exakt gerechnet, **gerundet erst zur Anzeige** | wenn der gerundete Wert den Bereich verlässt — deshalb `K-5` |
| Regelfläche | Anschlüsse **nicht** erfasst | sobald jemand die Zahl für das ganze Dach nimmt |

## Was später kommen könnte

**Absichtlich weggelassen, damit es nicht als Fehler gemeldet wird:**

- **Die 118 Zeilen ohne Lattmaß füllen.** *Nur aus belegter Quelle — sonst ist es genau die erfundene
  Zahl, gegen die das Gate steht.*
- **Traufreihe, First, Ortgang, Restausgleich.** *Sie machen aus der Regelfläche ein Dach; die Ampel
  wird dann grün diskutierbar.*
- **Andere Hersteller.** *Creaton, Erlus, Jacobi — Namen ohne Maße.*
- **Import in eine Datenbank.** *Ausdrückliches Nicht-Ziel dieses Auftrags: kein Schema, keine
  Migration, kein Seeder.*
