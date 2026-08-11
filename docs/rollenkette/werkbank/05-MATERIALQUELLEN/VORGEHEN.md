# VORGEHEN · Vom Fund zur Rollenzuordnung

> Yamas Einwand vom 07.08.2026: erst aufräumen, sortieren, adressieren und die
> Qualität prüfen — dann zuordnen.
> **Der Einwand ist berechtigt und korrigiert einen Fehler von mir.**

---

## Mein Fehler, den dieses Blatt behebt

Ich habe aus `dachdecker_pro_3d.tsx` sechs Formeln (F-014, F-025, F-026, F-027,
F-050, F-051) in die Formelsammlung eingetragen — **ohne zu prüfen, ob die Zahlen
stimmen**. Das verstößt gegen meinen eigenen Skill `SKILL-formel-pruefen.md`:
*„Niemals schätzen. Immer messen."*

Ein Fund ist noch keine Wahrheit. Dass Code läuft, heißt nicht, dass seine
Konstanten richtig sind.

---

## Die fünf Stufen

```
0 AUFRÄUMEN  →  1 SORTIEREN  →  2 QUALITÄT  →  3 ADRESSIEREN  →  4 ZUORDNEN
   Dubletten     Was ist es      Stimmt es      Wer, wann,        In Werkbank
   weg           überhaupt                      unter welcher     und Rollen
                                                Bedingung
```

**Keine Stufe wird übersprungen.** Wer bei 0 anfängt und bei 4 landet, hat
belastbares Material. Wer bei 4 anfängt, hat meinen Fehler.

---

## Stufe 0 · Aufräumen — gemessen

```
tsx-Dateien im Gemini-Ordner:   162
davon einzigartig (Hash):        64
Kopien:                          98   ← 60 %
```

`profi_holzbau_solar_cad.tsx` liegt **fünfmal**. `dachdecker_pro_3d.tsx` fünfmal.

**Festlegung:** `03-energie-pv-dach-3d/` ist die Arbeitsfassung. Alles unter
`00-komplettimport`, `20-themen-…`, `98-aussortiert` gilt als Kopie.
Vor jeder Zählung nach Inhalts-Hash deduplizieren — nicht nach Dateinamen,
denn `datei (1).tsx` und `datei.tsx` können verschieden sein.

---

## Stufe 1 · Sortieren

Je Fund eine Zeile. **Keine Kopien, nur Verweise** — wie im `~/wissensregister`.

| Feld | Beispiel |
|---|---|
| ID | `M-01` |
| Pfad | absolut, geprüft |
| Art | Code · Daten · Bild · Dokument · Asset |
| Umfang | Zeilen / Bytes |
| Datum | letzte Änderung |
| Hash | zur Dublettenerkennung |

---

## Stufe 2 · Qualität — das Prüfraster

Fünf Fragen, je eine Ampel. **Eine rote Ampel blockiert die Zuordnung.**

| # | Frage | 🟢 grün | 🟡 gelb | 🔴 rot |
|---|---|---|---|---|
| **Q1** | **Läuft es?** | ausgeführt, Ergebnis gesehen | gelesen, sieht lauffähig aus | Fehler beim Ausführen / nicht prüfbar |
| **Q2** | **Stimmen die Zahlen?** | gegen unabhängige Quelle geprüft | plausibel, aber unbelegt | widerlegt |
| **Q3** | **Woher kommt es?** | Herkunft benannt und nachvollziehbar | Herkunft unklar, Inhalt prüfbar | Herkunft unklar UND Inhalt nicht prüfbar |
| **Q4** | **Wie aktuell?** | < 12 Monate oder zeitlos | 12–24 Monate | veraltet gegen heutigen Stand |
| **Q5** | **Darf es benutzt werden?** | eigenes Material, keine fremden Daten | fremde Vorlage, Nutzung zu klären | Kundendaten / Geschäftsgeheimnis / fremde Lizenz |

> **Q2 ist die härteste.** Eine Konstante in einem Prototyp ist eine Behauptung
> des Prototyps, kein Messwert. Sie muss gegen eine unabhängige Quelle
> **oder** gegen ein echtes Aufmaß geprüft werden.

### Was jede Ampel für die Zuordnung bedeutet

| Gesamtergebnis | Folge |
|---|---|
| alle grün | direkt in die Werkbank, mit Belegangabe |
| eine gelb | in die Werkbank **mit Vorbehaltsvermerk** — darf keinen Auftrag begründen |
| eine rot | **nicht zuordnen.** Erst klären oder verwerfen |

---

## Stufe 2 angewandt — die bereits eingetragenen Formeln

**Nachträglich geprüft, was ich zu früh eingetragen habe:**

| F-Nr | Was | Q1 | Q2 | Q3 | Q4 | Q5 | Folge |
|---|---|---|---|---|---|---|---|
| **F-014** Eckenerkennung | Winkel + Kreuzprodukt | 🟡 gelesen | 🟢 **mathematisch nachvollzogen**, Standardverfahren | 🟢 Datei:Zeile | 🟢 zeitlos | 🟢 | **grün** — Mathematik ist prüfbar wahr |
| **F-025** Grat/Kehle/Ortgang | Regeltabelle | 🟡 | 🟢 fachlich schlüssig: Kehle nur an einspringender Ecke | 🟢 | 🟢 | 🟢 | **grün** |
| **F-026** Dach über Grundform | L/T-Aufbau | 🟢 **ausgeführt 11.08.** (A-12) | 🟢 **Ergebnis belegt** — L-Dach, 4 benannte Flächen, doppelt abgenommen | 🟢 | 🟢 | 🟢 | **grün für die Parametergeometrie** — NICHT für die Kantentopologie-Beschreibung (Berichtigung in FORMELSAMMLUNG) |
| **F-027** Gaube | Anstieg = d·tan(φ) | 🟡 | 🟢 Trigonometrie | 🟢 | 🟢 | 🟢 | **grün** |
| **F-050** Materialkennwerte | 45 kg/m², 12 Stk/m² | — | 🟡 **teilgeprüft** | 🟢 | 🟡 | 🟢 | **gelb** — siehe unten |
| **F-051** Zeitwerte | 8 min/m² Gerüst usw. | — | 🔴 **völlig unbelegt** | 🟢 | 🔴 unbekannt | 🟢 | **ROT — nicht verwendbar** |

### F-050 im Einzelnen — so sieht eine Q2-Prüfung aus

```
Behauptung (Prototyp):  Dachziegel 45 kg/m², 12 Stück/m²
Gegenquelle:            hausjournal.net — Tonziegel 2,9–3,7 kg/Stück,
                        Betondachstein 3,4–4,35 kg/Stück
Nachrechnung:           12 × 2,9…3,7  = 34,8–44,4 kg/m²  (Ton)
                        12 × 3,4…4,35 = 40,8–52,2 kg/m²  (Beton)
Ergebnis:               45 kg/m² liegt IM Bereich → plausibel
ABER:                   12 Stück/m² ist modellabhängig.
                        Frankfurter Pfanne liegt eher bei 10 Stück/m².
Ampel Q2:               🟡 — plausibel, aber nicht belegt und modellabhängig
Folge:                  Als Näherung nutzbar. Für ein ANGEBOT nicht.
                        Vorher am Herstellerdatenblatt festmachen.
```

### F-051 — warum rot

Die elf Zeitwerte (Gerüst 8 min/m², Sparren 10 min/lfm, Deckung 15 min/m² …)
haben **keine Herkunft**. Sie stammen aus einem Prototyp, der sie selbst nicht belegt.

Kalkulationszeiten hängen ab von: Dachneigung, Zugänglichkeit, Kolonnengröße,
Witterung, Region. **Eine Zahl ohne diese Bedingungen ist keine Zahl.**

> Wer damit ein Angebot rechnet, rechnet mit Erfundenem. Bleibt in der Sammlung
> **als rot markierter Platzhalter**, damit niemand sie versehentlich benutzt —
> und damit sichtbar ist, dass hier echte Werte fehlen.

---

## Stufe 3 · Adressieren — wer braucht was, wann, unter welcher Bedingung

Vier Spalten, alle vier Pflicht:

| Fund | **wer** braucht es | **wann** | **unter welcher Bedingung** |
|---|---|---|---|
| F-014 Eckenerkennung | Planner (Schnitt W-07), Generator (Bau) | vor jedem Dach-Auftrag | keine — grün |
| F-025 Grat/Kehle | Planner, Generator | beim Dachschnitt | keine — grün |
| F-026 L/T-Dach | **Planner zuerst** | **vor der Entscheidung, welcher Dachweg** | 🟢 **ausgeführt 11.08. (A-12)** — die Wegentscheidung ist damit entsperrt und liegt bei Yama |
| F-027 Gaube | Planner (W-22) | wenn W-22 geschnitten wird | keine — grün |
| F-050 Material | Planner (W-23), Generator | bei Mengenwerkzeug | 🟡 **nur als Näherung, nicht für Angebote** |
| F-051 Zeiten | **niemand** | — | 🔴 **gesperrt bis belegt** |
| `profi_holzbau_solar_cad.tsx` | Planner (W-21) | vor dem Sparren-Schnitt | erst Stufe 0–2 durchlaufen |
| Pfettendach-Bilder | Planner (Benennung W-21) | beim Schnitt W-21 | keine — Fachbilder |
| DACHDECKER-PRO-Screenshots | Planner (Bedienung W-07/W-08) | beim Bedienungsblatt | keine — Referenz |
| Dachziegel-DB-Schema | Planner (W-23 Katalog) | wenn Katalog gebaut wird | Datenqualität prüfen (`verified`/`source_url` sind schon im Schema) |

> **Die vierte Spalte ist die wichtigste.** „F-050 nur als Näherung, nicht für
> Angebote" ist der Unterschied zwischen einem nützlichen Wert und einem
> Kundenschaden.

---

## Stufe 4 · Zuordnen

Erst jetzt. Und mit drei Pflichtangaben je Eintrag:

1. **Ampel** aus Stufe 2 — sichtbar im Text, nicht im Anhang
2. **Bedingung** aus Stufe 3 — was gilt, bevor es benutzt wird
3. **Herkunft** — Datei:Zeile, damit man zurückgehen kann

---

## Was als nächstes zu tun ist

| Nr | Schritt | Ergebnis |
|---|---|---|
| 1 | Alle 64 einzigartigen tsx nach Thema sortieren | Inventar mit Hash |
| 2 | Die 5 Dach-/Holzbau-Dateien durch Q1–Q5 schicken | Ampeln |
| 3 | ~~**F-026 tatsächlich ausführen** — ein L-Grundriss, Ergebnis ansehen~~ **ERLEDIGT 11.08.** | **🟡 → 🟢** *mit Berichtigung* |
| 4 | F-050 an einem Herstellerdatenblatt festmachen | 🟡 → 🟢 |
| 5 | F-051 verwerfen oder an echten Aufmaßen neu erheben | 🔴 → 🟢 oder gelöscht |
| 6 | Ergebnis ins `~/wissensregister` zurückschreiben | ein Katalog, nicht zwei |

> **Schritt 3 ist der wichtigste.** Die Behauptung „der Code kann L-Grundrisse"
> stammt bisher aus dem *Lesen* des Codes. Das ist genau die Art unbelegter
> Machbarkeitsaussage, an der Z-07 gescheitert ist — nur diesmal in die andere
> Richtung. **Ausführen, ansehen, dann behaupten.**

### Schritt 3 GESCHLOSSEN — 11.08., und er hat sich selbst gerechtfertigt

**Ausgeführt als Auftrag A-12** (`docs/BERICHT-A-12-f026.md`, 424 Zeilen), **doppelt und unabhängig
abgenommen** (`171baafe` und `6a06c386` — zwei Evaluator-Instanzen, beide `ABGENOMMEN`, kein
inhaltlicher Widerspruch). *Ampel eingetragen vom Planner nach A-12-4.*

```text
DAS ERGEBNIS   ein L-Dach mit VIER benannten Flaechen (main_N/main_S/ext_W/ext_E),
               zwei Firstlinien, 1 Kehle, 1 Grat, 7 Pfetten, 10 Dreiecke,
               First 5482 mm, Flaechensumme 167.246 m2
               -> "der Code kann L-Grundrisse" ist damit BELEGT, nicht gelesen
DER HAKEN      und er ist genau die Art Befund, fuer die dieser Schritt da war:
               das VERFAHREN, das die Formelsammlung fuer F-026 beschreibt, laeuft
               NICHT. Die Kantentopologie-Kette (:95/:123/:134) wird aus dem
               Geometrie-Aufbau (:774-971) null Mal gerufen — sie speist eine
               ANZEIGE in der React-Schicht (:1496/:1706). Und fuer l-shape/t-shape
               liefert sie grate=5, wo der Bau 1 Grat setzt.
               Beides selbst am Fremdcode nachgemessen, Befehle in FORMELSAMMLUNG.
```

> **Die Lehre dieses Schrittes ist stärker als erwartet, und sie geht über F-026 hinaus:**
> *Das Lesen hatte recht — der Code kann L-Grundrisse. **Aber die Begründung, die ich aus dem Lesen
> mitgeschrieben hatte, war falsch: ich hatte das Verfahren aus den Funktionen abgeleitet, die
> danach aussahen, statt aus denen, die aufgerufen werden.*** **„Ausführen, ansehen, dann behaupten"
> reicht nicht — es muss heißen: ausführen, ansehen, und prüfen, ob der Weg dorthin der ist, den man
> beschrieben hat.** *Ein richtiges Ergebnis ist kein Beweis für die richtige Erklärung; hier waren
> das Ergebnis richtig und die Erklärung falsch, und ohne A-12 hätte die falsche Erklärung den
> nächsten Dach-Auftrag begründet.*
