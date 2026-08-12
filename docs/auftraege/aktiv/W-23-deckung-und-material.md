# W-23 — Deckung und Material. Der Operand ist da, und er ist keine Tabelle mit Werten, sondern ein Bereich plus eine Rechnung

```yaml
auftrag: "W-23"
werkzeug: "W-23 Deckung und Material"
art: "STUFE 1 — Blatt schneiden, Ziel BESCHRIEBEN. Der Auftrag, der W-21L entsperrt."
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: e9f370f1
prioritaet: P1
anlass: "Yamas Freigabe 12.08.: 'ich gebe dir frei, du kannst in den Ordner, da sind die
         Ziegeltabellen mit Eindecklatten, bedien dich nach Hersteller Typ Format' —
         und unmittelbar danach seine Fachaussage zur Abhaengigkeit der Lattung."
ballbesitz: "plan-pruefer (DoR), danach generator"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "Yamas Fachaussage 12.08. · braas_dachziegel_datenbank_v14.xlsx, Blatt DB_Produkte
            · W-21L (OPERANDEN-GATE, wartet auf genau diesen Auftrag) · F-050 🟡"
```

## 1 · Yamas Fachaussage — sie ändert den Zuschnitt, und zwar grundlegend

**Wörtlich, 12.08.:**

> *„die eindecklattung ist abhängig von dach neigung und dach maße und zulässig überlappung der
> ziegel"*

**Das ist die Antwort auf die Frage, an der W-21L seit Tagen hängt — und sie lautet nicht „hier ist
die Tabelle".** *Die Lattweite ist **keine Konstante je Ziegelmodell**. Sie ist das Ergebnis einer
Rechnung, in die drei Größen eingehen:*

```text
1  DACHNEIGUNG        entscheidet, ob das Modell ueberhaupt zulaessig ist
                      (Regeldachneigung) und wie viel Ueberdeckung noetig ist
2  DACHMASS           die Sparrenlaenge Traufe->First muss in GANZE Reihen aufgehen
3  ZULAESSIGE          der Ziegel gibt einen BEREICH vor, innerhalb dessen man
   UEBERLAPPUNG       schieben darf — das ist das Verschiebespiel
```

> **Deshalb war die Suche nach „der Lattweite" von Anfang an falsch gestellt.** *Ein Auftrag, der
> eine Tabelle mit einem Wert je Modell erwartet, hätte den Operanden nie gefunden — auch wenn er
> vor ihm lag. **Die Tabelle liefert den Bereich, die Rechnung liefert den Wert.***

## 2 · Die Quelle — gefunden, geöffnet, gezählt

```text
Datei    ~/Desktop/Downloads_Aufgeraeumt_2026-05-22/01_Energie_PV_Waermepumpe/Tabellen/
         braas_dachziegel_datenbank_v14.xlsx        718.574 Byte
Blatt    DB_Produkte (sheet11 von 47 Blaettern)
Umfang   127 Datenzeilen · 48 Spalten
```

**Die Spalten, auf die es ankommt — sie sind genau die drei Größen aus Yamas Satz:**

| Spalte | Feld | Rolle in der Rechnung |
|---|---|---|
| 26 · 27 | `Lattmass_min_mm` · `Lattmass_max_mm` | **der erlaubte Bereich** |
| 32 | `Verschiebespiel_mm` | **die zulässige Überlappung** |
| 33 · 34 | `Regeldachneigung_grad` · `Dachneigung_min_system_grad` | **die Neigungsschranke** |
| 22–25 | `Decklaenge_min/max_mm` · `Deckbreite_min/max_mm` | Deckmaße für die Flächenrechnung |
| 37–39 | `Datenstatus` · `Quelle_1_URL` · `Quelle_2_URL` | **Herkunft je Datensatz** |
| 47 | `Eindeckmass_Text` | Klartextregel, wo vorhanden |

**Datenstatus über alle 127 Zeilen, gezählt:**

```text
78x  verifiziert aus PDF
26x  teilweise verifiziert
17x  verifiziert
 6x  offen
```

## 3 · Die Füllquote — und sie ist der wichtigste Befund dieses Blattes

```text
Datenzeilen                          127
mit Lattmass min UND max              9   (= 7 Modelle, zwei Zeilen doppelt)
mit Verschiebespiel_mm               13
mit Regeldachneigung_grad            17
```

> **Neun von 127.** *Die Datenbank ist gross, aber der Operand, den W-21L braucht, steht bei **sieben
> Modellen** — alle von **einem** Hersteller (Braas). **Das ist kein Mangel der Quelle, sondern die
> ehrliche Lage: sie wird gepflegt, und die Lattmasse sind der am wenigsten gepflegte Teil.***

**Die sieben, im Wortlaut aus der Tabelle:**

| Modell | Lattmaß | Verschiebespiel | Regeldachneigung | Datenstatus |
|---|---|---|---|---|
| Braas **Achat 12V** | 330–360 mm | 30 | 16° | verifiziert |
| Braas **Granat 11V** | 338–380 mm | 42 | 25° | verifiziert |
| Braas **Harzer Pfanne 7** | 372–405 mm | — | 22° | verifiziert |
| Braas **Rubin 13V** | 330–360 mm | 30 | — | teilweise verifiziert |
| Braas **Rubin 9V** | 370–400 mm | 30 | 16° | verifiziert |
| Braas **Topas 11V** | 320–380 mm | 60 | 25° | verifiziert |
| Braas **Topas 13V** | 320–360 mm | 40 | 25° | verifiziert |

## 4 · Ein Fund, der die Daten prüfbar macht — das Verschiebespiel ist redundant

```text
Achat 12V         360 - 330 = 30   Verschiebespiel 30   ✓
Granat 11V        380 - 338 = 42   Verschiebespiel 42   ✓
Rubin 13V         360 - 330 = 30   Verschiebespiel 30   ✓
Rubin 9V          400 - 370 = 30   Verschiebespiel 30   ✓
Topas 11V         380 - 320 = 60   Verschiebespiel 60   ✓
Topas 13V         360 - 320 = 40   Verschiebespiel 40   ✓
Harzer Pfanne 7   405 - 372 = 33   Verschiebespiel —    (fehlt, aus dem Bereich ableitbar)
```

> **Sechs von sechs stimmen.** *Das Verschiebespiel ist die Differenz zwischen maximalem und
> minimalem Lattmaß — es sagt dasselbe zweimal. **Und genau deshalb ist es wertvoll: eine
> Doppelangabe, die übereinstimmen muss, ist eine Prüfung.** Wo beide Werte stehen und nicht
> übereinstimmen, ist einer falsch — und wo einer fehlt, ist er ableitbar. Das ist die
> Eingangsprüfung dieses Werkzeugs, und sie kostet eine Subtraktion.*

## 5 · DECISION

```text
W-23 BESCHREIBT den Deckungskatalog als BEREICHSQUELLE, nicht als Wertetabelle:
     je Modell ein Lattmass-BEREICH, eine Neigungsschranke und die Deckmasse.

DIE RECHNUNG gehoert in eine eigene Formel (Vorschlag F-053, Nummer beim Planner):
     Sparrenlaenge L (Traufe -> First, IN DER DACHFLAECHE, nicht horizontal)
     Reihen n     = aufrunden( L / Lattmass_max )
     Lattmass     = L / n
     ZULAESSIG wenn Lattmass_min <= L/n <= Lattmass_max
     UND Dachneigung >= Regeldachneigung
     -> Das ist die Umsetzung von Yamas Satz. Sie steht hier als VORSCHLAG und
        braucht seine Bestaetigung, bevor sie als Formel eingetragen wird.

QUELLE MIT HERKUNFT: jeder uebernommene Wert traegt Datenstatus und Quelle_1_URL mit.
     Ein Wert ohne Datenstatus wird NICHT uebernommen — das ist F-051s Lehre
     (TIME_VARS: vier Fundorte, null Quellen) angewandt, bevor der Fehler entsteht.

NICHT IN DIESEM AUFTRAG:
     - Kein Import in eine Datenbank. Kein Schema, keine Migration, kein Seeder.
       Die 127 Zeilen bleiben, wo sie sind; W-23 BESCHREIBT das Werkzeug.
     - Keine Ausweitung auf die 118 Zeilen ohne Lattmass. Wer fehlende Werte
       ergaenzt, erfindet sie — das ist der Fall, an dem W-21L blockiert ist.
     - Keine anderen Hersteller. Die Quelle traegt Braas; Creaton, Erlus, Jacobi
       stehen im SQL-Schema als Namen OHNE Masse.
```

## 6 · Was das für W-21L bedeutet — der eigentliche Zweck dieses Auftrags

```text
W-21L wartet laut Fahrplan auf "W-23s Ziegeltabelle" — OPERANDEN-GATE.
NACH diesem Auftrag ist die Lage:
  ENTSPERRT fuer SIEBEN Braas-Modelle mit verifiziertem Lattmass-Bereich
  WEITER GESPERRT fuer alles andere — und das ist RICHTIG, nicht bedauerlich:
  eine Lattung ohne belegten Bereich waere genau die erfundene Zahl,
  gegen die das Gate steht.
```

> **Damit ist W-21L nicht „entsperrt", sondern ZUSCHNEIDBAR:** *ein Auftrag über sieben belegte
> Modelle ist möglich; ein Auftrag über „die Lattung" ist es weiterhin nicht. **Der Unterschied
> gehört in W-21Ls Nachschnitt und ist eine Planner-Entscheidung, die nach diesem Auftrag fällt.***

## 7 · Abnahmekriterien

```text
W-23-1  (P1) Die sieben Blaetter von W-23 werden aus der QUELLE abgelesen, nicht
        entworfen — Datei, Blatt, Spaltennummern und Zeilenzahl stehen in 5-CODE.
        Gegenprobe: jede genannte Zahl ist in der Tabelle nachlesbar.

W-23-2  (P1, DER TRAGENDE PUNKT) 3-FORMELN traegt die Rechnung aus Abschnitt 5 als
        VORSCHLAG mit dem Vermerk "vorgeschlagen, nicht entschieden" — und Yamas
        Fachaussage WOERTLICH als ihre Grundlage. Ohne diesen Vermerk waere es eine
        erfundene Fachregel; mit ihm ist es eine Ableitung aus seinem Satz.

W-23-3  (P1) 7-GRENZEN nennt die Fuellquote UNGESCHOENT: 9 von 127 Zeilen tragen ein
        vollstaendiges Lattmass, alle sieben Modelle sind von EINEM Hersteller.
        Und die Folge: das Werkzeug kann fuer andere Modelle NICHTS sagen.

W-23-4  (P1) Die Eingangspruefung aus Abschnitt 4 steht in 6-PRUEFUNG als Kriterium:
        Verschiebespiel == Lattmass_max - Lattmass_min. Rot-Beleg: die sechs Modelle,
        bei denen es aufgeht, und die Harzer Pfanne, bei der der Wert fehlt.

W-23-5  (P1) KEIN Wert ohne Herkunft: jede uebernommene Zahl traegt Datenstatus und,
        wo vorhanden, Quelle_1_URL. Gegenprobe: die Zahl der uebernommenen Werte ist
        gleich der Zahl der mitgefuehrten Statusangaben.

W-23-6  (must_preserve) resources/** und app/** byte-identisch — reine Doku-Stufe.
        Die Quelldatei auf dem Desktop wird NICHT verandert, nur gelesen.
        Kein Import, kein Schema, keine Migration, kein Seeder.

W-23-7  (P1, §3 wird BELEGT) Beide Orte nach ARBEITSREGELN §3, beide Zahlen genannt,
        Messung unmittelbar vor der ersten Aenderung.
```

## 8 · Rückweg & Entdeckung

```text
RUECKWEG      reiner Revert. Sieben Doku-Blaetter plus eine Registerzeile; kein Code,
              kein Datenpfad, keine Migration.
KOPIE AUSSERHALB DER MASCHINE  ZUM BAUZEITPUNKT ZU PRUEFEN. Heute steht der Transport
              (die Berechtigungssperre ist am 12.08. aufgeloest worden, c89e9096/6bbd337a).
ENTDECKUNG    das Signal ist die Fuellquote: nennt ein Blatt mehr als SIEBEN Modelle mit
              Lattmass, sind Werte hinzugekommen, die nicht in der Quelle stehen — dann
              ist W-23-5 gebrochen und jemand hat gerundet, geschaetzt oder erfunden.
```

## 9 · Konfliktprüfung §3 — unmittelbar vor dem Schnitt gemessen (H-4)

```text
Tafelzeile (Spalte 2)   ->  1   (A-19, Generator)
Zustandsfeld            ->  1   deckungsgleich
Index leer · STATUS.md im Arbeitsbaum unveraendert
SCOPE-UEBERSCHNEIDUNG mit A-19: keine. A-19 fasst ARBEITSREGELN.md an, W-23 die
        Werkbank-Blaetter und REGISTER.md.
ACHTUNG: REGISTER.md liegt im Scope MEHRERER W-Blaetter. Wer W-23 baut, prueft §3
        unmittelbar vor der Registerzeile erneut — die Lehre aus ce30174f.
W-23 wird auf ENTWURF geschnitten und nimmt keinen §3-Platz.
```

```yaml
zustand: ENTWURF
ballbesitz: "plan-pruefer (DoR)"
was_yama_bestaetigen_muss: "die Rechnung aus Abschnitt 5. Sie ist die Umsetzung SEINES Satzes,
       aber die Umsetzung ist meine — Reihenanzahl aufrunden und dann gleichmaessig teilen ist
       Handwerksregel, und ich habe sie abgeleitet, nicht abgelesen."
zweiter_posten: "118 der 127 Zeilen haben KEIN Lattmass. Das ist Pflegearbeit an der Quelle und
       gehoert Yama, nicht diesem Auftrag."
was_dieser_auftrag_entsperrt: "W-21L fuer SIEBEN Braas-Modelle. Nicht mehr, und das ist richtig."
```
