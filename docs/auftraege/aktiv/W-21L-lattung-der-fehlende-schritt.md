# W-21L — der fehlende Schritt Deckungsart → Lattenabstand. GESCHNITTEN und am Operanden BLOCKIERT

```yaml
auftrag: "W-21L"
werkzeug: "W-21 Sparren und Lattung (Folgeauftrag)"
art: "Bau-Auftrag, Spur A — aber BLOCKIERT: die Fachdaten existieren nicht"
titel: "Niemand leitet den Lattenabstand aus der Deckungsart ab. Und die Daten dafuer fehlen."
spur: A
heimat_app: ticket
status: ENTWURF
status_steht_in: docs/STATUS.md
basis_sha: 4f0d4584
prioritaet: P2
blockiert_durch: "OPERANDEN-GATE — keine Deckungsart/Lattweiten-Daten im Repo, gemessen"
anlass: "Generator-Befund aus W-21/1 (992d5d76). Er hat MEINE Auftragsvermutung widerlegt
         und praeziser gemessen — dieser Auftrag zieht die Folge."
ballbesitz: "YAMA — der Auftrag kann ohne Fachdaten nicht in DoR gehen"
claim: "planner 12.08. — Claim VOR dem Schnitt."
```

## Der Generator hat meine Vermutung widerlegt — und er hatte recht

**Ich hatte in `W-21/1` geschrieben:** Lattung sei als *Lastanteil* gebaut, als *Gegenstand* nicht.
**Der Generator hat genauer gemessen** (`992d5d76`), und ich habe es gegengeprüft:

```text
LATTUNG ALS LAST     sparrenBerechnung.ts:63 — aber NICHT als Lattung:
                     "eigenlastKnM2?: number
                      /** staendige Last (Dachdeckung + LATTUNG + Sparren-Eigengewicht)
                          auf Dachflaeche, kN/m². Default 0,9. */"
                     -> sie steckt in einer SAMMELPAUSCHALE. Wer den Lattenabstand
                        aendert, aendert die Last NICHT. Das ist praeziser als
                        "als Last gebaut" — und schwaecher.
LATTUNG ALS MENGE    holzMengen.ts:32 konterLaenge · :34 lattenLaenge, je lfm
                     "Summe der echten Konterlattenlaengen" / "Traglattenlaengen"
                     -> GEBAUT, aus der echten 3D-Liste. Meine Vermutung war hier falsch.
WAS FEHLT            der Schritt DAZWISCHEN: niemand leitet den LATTENABSTAND aus der
                     DECKUNGSART ab.
```

> **Meine Fehlerklasse hier: ich habe aus zwei Fundstellen eine Zweiteilung gemacht („Last ja,
> Gegenstand nein"), die es nicht gibt.** *Die Lattung ist an **beiden** Enden vorhanden — als Zahl in
> einer Pauschale und als Länge in einer Mengenliste. **Was fehlt, ist die Mitte**, und die hätte ich
> nur gefunden, wenn ich nach dem Schritt gesucht hätte statt nach dem Gegenstand.*

## Zwei Dinge sind schärfer, als der Generator gemeldet hat — selbst nachgemessen

```text
1  konterlattungMm liest NIEMAND — auch kein Test.
   Der Generator schrieb: "wird ausserhalb seiner eigenen Datei von KEINEM
   Produktivcode gelesen, nur von einem Test."
   Gemessen (B5, Zeilen gelesen): DREI Treffer, ALLE in dachformVorlagen.ts selbst
     :122   konterlattungMm: [number, number]     (Vertrag)
     :1381  konterlattungMm: [24, 48]             (Wert)
     :1407  konterlattungMm: [0, 0]               (Wert)
   In __tests__/: NULL Treffer.
   -> es ist kein "nur von einem Test gelesenes" Feld, es ist ein TOTER VERTRAG:
      definiert, zweimal befuellt, von nichts gelesen.
      Kein Vorwurf an ihn — er war zu GROSSZUEGIG, und das verschaerft den Befund.

2  dachWerte.ts liegt ZWEIMAL, byte-identisch, 103 Zeilen je:
     resources/planner/hausplaner/geometry/dachWerte.ts   <- wird importiert
     resources/planner/utils/dachWerte.ts                 <- kein Importeur gefunden
   Importeure der geometry-Fassung: dachGeometrie.ts, dachformVorlagen.ts,
   __tests__/dachWerte.test.ts
   -> eine TOTE KOPIE. Heute harmlos (byte-gleich), morgen divergent.
      Genau die "verwaiste zweite Wahrheit", die die Bauordnung verbietet.
   NICHT IM SCOPE dieses Auftrags — eigener Befund, eigener Auftrag. Hier benannt,
   damit er nicht zwischen den Blaettern verschwindet.
```

## Das OPERANDEN-GATE — warum dieser Auftrag nicht in den DoR gehen kann

**Gemessen über `resources/`, `app/`, `database/`:**

```text
grep -rniE 'lattweite|lattabstand|deckbreite|decklaenge'   -> 0 Treffer
grep -rlniE 'deckungsart|ziegelart|dachziegel|falzziegel|
             biberschwanz|frankfurter'                     -> nur zwei ALTE Controller
                                                              (Old/PVChecklistController,
                                                               NewLeadsController)
```

**Und der einzige Lattenabstand-Wert im Repo ist kein Fachwert:**

```text
dachWerte.ts:20   battenDist: 0.05,  // Lattenabstand   min 5 cm
dachWerte.ts:19   rafterDist: 0.05,  // Sparrenabstand min 5 cm
                  (Schutz gegen Division durch ~0 / Endlosschleife)
-> 5 cm ist eine SCHUTZSCHRANKE gegen Division durch Null, keine Deckungsangabe.
   Ein Dachziegel hat je nach Modell 32 bis 38 cm Lattweite. Wer die 5 cm als
   Fachwert liest, baut Unsinn.
```

> **Deshalb ist dieser Auftrag geschnitten und blockiert, nicht geschnitten und wartend.** *Der
> fehlende Schritt fehlt **nicht aus Nachlässigkeit**, sondern weil die Fachdaten nicht existieren:
> es gibt im ganzen Repo keine Tabelle „Deckungsart → Lattweite".* **Wer diesen Schritt ohne die
> Tabelle baut, muss die Werte erfinden — und eine erfundene Lattweite ist ein Dachschaden, kein
> Rechenfehler.**

**CLAUDE.md, wörtlich:** *„Fach-, Rechts-, Geld-, Datenschutz-, Authentifizierungs- und
Datenbankentscheidungen werden nicht still automatisiert. **Fehlende Operanden führen zu Rückfrage**
oder einem ausdrücklich bestätigten Vorschlag."*

## Was Yama liefern muss, damit dieser Auftrag laufen kann

```text
EINE TABELLE, je Deckungsart:
  Deckungsart (z.B. Falzziegel, Biberschwanz-Doppeldeckung, Frankfurter Pfanne,
              Betondachstein, Trapezblech, Schiefer)
  Lattweite   von … bis (mm) — sie ist neigungs- UND modellabhaengig
  Mindestneigung (Grad) — Regeldachneigung je Deckungsart
  Quelle      Herstellerdatenblatt oder Fachregel (ZVDH-Regelwerk)

WOHER SIE KOMMEN KANN, gemessen NICHT geraten:
  M-04 traegt laut BESTAND-YAMA.md ein Dachziegel-DB-Schema mit verified/source_url
  -> DAS ist der richtige Ort. Der Auftrag W-23 (Mengen/Katalog) ist dafuer
     vorgesehen und steht auf LEER.
  -> Ich schlage vor: dieser Auftrag wartet auf W-23, statt eine eigene Tabelle
     zu bauen. Zwei Ziegeltabellen waeren eine zweite Wahrheit.

ALTERNATIVE, falls es schneller gehen soll:
  DREI Deckungsarten mit belegter Quelle genuegen fuer den ersten Schritt —
  aber die Ampel bleibt dann 🟡 "nur die drei benannten", und das MUSS im
  Ergebnis stehen (A-10-Klasse: sag, was du nicht kannst).
```

## Nicht-Ziele

- **Keine erfundenen Lattweiten.** *Nicht als Vorgabe, nicht als Default, nicht als „üblicher Wert".*
- **Keine zweite Ziegeltabelle** neben der, die W-23 aufbauen soll.
- **Kein Anfassen der Sammelpauschale** `eigenlastKnM2` (Default 0,9). *Sie zu zerlegen ist eine
  Statik-Änderung und braucht N-003s Fach-Gate.*
- **Keine Behebung der `dachWerte.ts`-Doppelung.** Eigener Befund, eigener Auftrag.
- **Kein Löschen von `konterlattungMm`.** *Ein toter Vertrag wird gemeldet, nicht stillschweigend
  entfernt — er kann eine geplante Schnittstelle sein.*

## Akzeptanzkriterien (gelten erst nach dem Operanden)

**W-21L-1 (P1, die Quelle steht am Wert):** Jede Lattweite trägt Deckungsart, Bereich und **Quelle**
(Herstellerdatenblatt oder Fachregel). *Ein Wert ohne Quelle ist F-051 in neuer Kleidung.*

**W-21L-2 (P1, die Grenze wird gemeldet, nicht geraten):** Bei einer **unbekannten** Deckungsart
liefert die Ableitung **keinen Default**, sondern eine Absage mit Wortlaut. *A-10-Klasse.*

**W-21L-3 (P1, die Neigungsabhängigkeit steht drin):** Die Lattweite hängt von der Dachneigung ab
(flacher → engere Lattung wegen Überdeckung). **Wer sie als reine Modelleigenschaft baut, baut sie
falsch** — das ist im Blatt zu benennen, auch wenn zunächst nur ein Bereich geliefert wird.

**W-21L-4 (P1, `konterlattungMm` wird angeschlossen ODER als tot gemeldet):** Entweder liest die
neue Ableitung das vorhandene Feld — oder das Blatt sagt, dass es weiterhin von nichts gelesen wird.
*Nachweis: `grep -rn 'konterlattungMm'` mit Trefferzeilen (B5).*

**W-21L-5 (`must_preserve`):** `eigenlastKnM2` und die Sparren-Vorbemessung unverändert; Insel-Suite
grün (ohne Zahl); die beiden `dachWerte.ts` unangetastet.

**W-21L-6 (P1, §3 wird BELEGT):** Befehl mit Ausgabe, an beiden Orten, **Messung unmittelbar vor der
ersten Änderung** *(Lehre aus `ce30174f`)*.

## Kantenliste

```text
unbekannte Deckungsart          -> Absage, kein Default (W-21L-2)
Deckungsart ohne Neigungsangabe -> Bereich liefern UND die Unsicherheit nennen
Lattweite 5 cm aus dachWerte    -> VERBOTEN als Fachwert. Das ist eine
                                   Division-durch-Null-Schranke.
zwei Ziegeltabellen entstehen   -> Nicht-Ziel. Auf W-23 warten.
eigenlastKnM2 wird zerlegt      -> Statik-Aenderung, N-003-Gate, NICHT hier.
```

## Rückweg und Entdeckung

**Rückweg:** Der Auftrag ist heute reine Vorbereitung; es gibt nichts zurückzurollen. Nach dem Bau:
neue Datei plus Anschluss, `git revert` genügt, solange die Sammelpauschale unberührt bleibt.

**Entdeckung:** Taucht eine Lattweite ohne Quelle im Code auf, hat `W-21L-1` nicht gewirkt.
*Prüfbar mit einem `grep` über die neue Datei — jede Zahl braucht eine Quellenangabe in derselben
Zeile oder direkt darüber.*

## Konfliktprüfung (§5)

```text
§3 UNMITTELBAR gemessen  0 IN_ARBEIT
W-21/1                   BESCHRIEBEN (992d5d76) — dieser Auftrag ist sein FOLGEauftrag,
                         keine Nachbesserung. W-21/1 bleibt unberuehrt.
W-23 (Mengen/Katalog)    LEER — traegt laut Vorschlag die Ziegeltabelle. Dieser Auftrag
                         WARTET darauf, statt eine eigene zu bauen.
N-003 (Fach-Gate)        beruehrt eigenlastKnM2 — ausdrueckliches Nicht-Ziel hier.
Scope frei               keine gemeinsame Datei mit einem offenen Auftrag.
```

```yaml
fehlerklasse: "SPEC (meine Vermutung war zu grob) + fehlende Fachdaten (kein Verschulden)"
prioritaet: P2
blockiert: "ja — Operanden-Gate. Geht NICHT in DoR, bevor die Tabelle da ist."
offen_an_yama: "die Deckungsart/Lattweiten-Tabelle mit Quelle — oder die Freigabe,
                auf W-23 zu warten"
befund_1: "konterlattungMm ist ein TOTER VERTRAG: definiert, zweimal befuellt, von nichts
           gelesen — auch von keinem Test. Schaerfer als gemeldet."
befund_2: "dachWerte.ts liegt zweimal byte-identisch (geometry/ und utils/), nur die
           geometry-Fassung wird importiert. Tote Kopie, eigener Auftrag noetig."
gewuerdigt: "der Generator hat meine Auftragsvermutung widerlegt statt sie zu bauen.
             Genau das soll §7 leisten — und es hat funktioniert."
kern: "die Lattung ist an beiden Enden gebaut und in der Mitte leer. Ich habe nach dem
       Gegenstand gesucht und haette nach dem Schritt suchen muessen."
```
