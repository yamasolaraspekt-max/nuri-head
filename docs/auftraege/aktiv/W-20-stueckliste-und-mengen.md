# W-20 — Stückliste und Mengen. Das Holz ist gebaut und belegt, der Ziegel fehlt — und die Grenze verläuft anders als der Fahrplan sagt

```yaml
auftrag: "W-20"
werkzeug: "W-20 Stückliste und Mengen"
art: "STUFE 1 — Blatt schneiden, Ziel BESCHRIEBEN. Ablesung: der Kern ist gebaut und getestet."
spur: A
heimat_app: ticket
dor_beleg: "2c0e4ede — plan-pruefer 12.08., DoR BESTANDEN. Er hat holzMengen.ts mit 64 Zeilen und
         drei Exporten selbst nachgemessen und die sechs Testzusagen als sechs test-Bloecke
         belegt. NACHGEZOGEN vom Planner nach dem Bau: der Blattkopf hing auf ENTWURF, waehrend
         Tafelzeile und Datensatz schon CODE_FERTIG trugen. Dritter Fall dieser Luecke an einem
         Tag (A-16, W-27, W-38) und immer derselbe Grund: der Plan-Pruefer setzt Tafelzeile und
         Datensatz und fasst mein Blatt nicht an, weil es nicht sein Eigentum ist. Richtig von
         ihm — der Eigentuemer muss nachziehen, und das bin ich."
status_steht_in: docs/STATUS.md
basis_sha: 8300aa59
prioritaet: P2
anlass: "Klasse C, letztes voraussetzungsfreies Werkzeug. W-05 und W-08 sind BETRIEBSBESTAETIGT.
         Und der Zeitpunkt ist kein Zufall: W-23 ist gerade gebaut und F-053 eingetragen —
         genau die zwei Zulieferungen, die W-20s Luecke schliessen wuerden."
ballbesitz: "EVALUATOR — Abnahme laeuft, Claim fafbff12. Er prueft die Formelzuordnung F-011 und
             F-023 an der Registerzeile; das ist Planner-Eigentum, ein Befund kommt zu mir zurueck."
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "geometry/holzMengen.ts (64 Z., 3 Exporte, 6 Testzusagen) · W-23 (BESCHRIEBEN) ·
            F-053 🟡 · F-011 ✓ (Polygonflaeche, aus W-08)"
```

## 1 · Der Fahrplan sagt „nur Holz vorhanden" — die Grenze verläuft anders

**Gemessen: `geometry/holzMengen.ts`, 64 Zeilen, drei Exporte, sechs Testzusagen.**

```text
export interface HolzStueck  { type?, name?, laenge? }
export interface HolzMengen  { sparrenLaenge, konterLaenge, lattenLaenge, sparrenAnzahl }
export function  holzMengenAusListe(holzliste)
```

> **Und hier muss ich eine eigene Fehlmessung berichtigen, bevor sie ins Blatt wandert:** *ich hatte
> nach `lattenMengen` gesucht und **0 Treffer** gemeldet — also „die Lattung fehlt". **Falsch.** Das
> Feld heißt `lattenLaenge`, und es ist da. Mein Muster suchte eine **Schreibweise**, nicht die Sache;
> das ist H-9 an mir, zum vierten Mal an diesem Tag. Die Lattung ist enthalten — als **laufende
> Meter**, nicht als Lattweite.*

**Die Unterscheidung, die daraus folgt und die das Werkzeug tragen muss:**

```text
W-21L / F-053    WIE WEIT liegen die Latten auseinander?   -> Lattmass in mm
W-20             WIE VIELE laufende Meter Latte?           -> Summe der echten Laengen
```

*Zwei verschiedene Fragen an dieselbe Latte. Wer sie verwechselt, sucht die eine Antwort in der
falschen Datei — genau das ist mir gerade passiert.*

## 2 · Was der Dateikopf selbst sagt — und es ist ein Befund über zwei Wahrheiten

```text
holzMengen.ts:1-8, woertlich:
  "Reparatur 7: reine, testbare Aggregation der Holzlaengen aus der ECHTEN, bereits in der
   3D-Geometrie erzeugten Holzliste des Planers.
   Problem vorher: Die Material-/Holzliste SCHAETZTE Sparren-/Lattenlaengen aus dem
   Rechteck-Rahmen (Anzahl x Hoehe bzw. Anzahl x Breite). Die Engine zeichnet die Staebe
   aber bereits an die reale (an Walm/L/T geclippte) Geometrie -> zwei Wahrheiten."
```

> **Das Werkzeug ist als Beseitigung einer zweiten Wahrheit entstanden.** *Vorher rechnete die
> Stückliste aus dem Rahmen, während die Darstellung die geclippte Geometrie zeigte — **zwei Zahlen
> für dasselbe Holz, und die falsche stand in der Liste.** Das gehört als Kern in `1-ZWECK`, nicht als
> Fußnote: es erklärt, warum die Aggregation über die **echte** Liste läuft und nicht über Formeln.*

**Und eine Feinheit, die der Code ausdrücklich begründet** (`:56-58`):

```text
Schiftsparren zaehlen als Gemeinsparren mit — "sonst fallen die an Kehle/Grat geclippten
Sparren aus Bauholz-m3 und Lohn heraus (Unter-Count)".
```

*Ein Unter-Count in der Stückliste ist ein Fehlbetrag im Angebot. Diese Zeile ist der Grund, warum
`name.startsWith("Sparren") || name.startsWith("Schiftsparren")` dort steht — sie gehört ins Blatt.*

## 3 · Die Lücke, gemessen statt geschätzt

```text
VORHANDEN   Sparren (Laenge + Anzahl) · Konterlatten (Laenge) · Traglatten (Laenge)
            -> vier Kennzahlen, aus der ECHTEN Geometrie, 6 Testzusagen

FEHLT       ZIEGELMENGE. Gemessen in geometry/:
              'stueck.*m2'   0 Treffer     <- Stueck je m2 gibt es nicht
              'bedarf'       1 Treffer     <- und der ist eine Gaubenbemerkung
            'ziegel' hat 16 Treffer, aber als TYP (RoofCovering in dachformVorlagen),
            nicht als MENGE. 'deckung' 79 Treffer, davon der erste eine LASTannahme
            in sparrenBerechnung — Deckung als Gewicht, nicht als Stueckzahl.
```

> **Die Ziegelmenge ist die einzige echte Lücke — und sie ist ab heute schließbar.** *W-23 ist gebaut
> (`BESCHRIEBEN`) und liefert `Deckbreite_min/max_mm` sowie `Bedarf_min/max_Stk_m2` je Modell; F-011
> (Polygonfläche) ist über W-08 ✓ und liefert die Fläche. **Was fehlt, ist nur die Multiplikation —
> und die Herkunft der Faktoren.***

## 4 · DECISION

```text
ZIEL          BESCHRIEBEN, nicht ENTWORFEN. Der Kern ist gebaut, getestet und im
              Dateikopf begruendet — das ist eine ABLESUNG. Anders als W-15 und W-27,
              deren Quelle ein Vertrag beziehungsweise ein Prototyp ist.

DER UMFANG    W-20 beschreibt, was das Werkzeug HEUTE kann: vier Holz-Kennzahlen aus
              der echten Geometrie. Die Ziegelmenge wird als GRENZE benannt, NICHT als
              Vorgabe — ein ENTWORFEN-Teil in einem BESCHRIEBEN-Blatt waere die
              Vermischung zweier Stufen, die Yamas Legende ausdruecklich trennt
              ("zwei PARALLELE Stufen, keine aufeinanderfolgenden").

DIE ZWEI      Die Zieladresse steht im Blatt, damit die Luecke nicht verwaist:
ZULIEFERUNGEN   Ziegelmenge = Dachflaeche (F-011) x Bedarf_Stk_m2 (W-23, Spalte 28/29)
                Lattenbedarf in lfm liegt VOR (lattenLaenge); die LATTWEITE kommt aus
                F-053 und ist eine ANDERE Frage — beide gehoeren nicht in dieses Blatt.

NICHT         Kein Bau. Keine Aenderung an holzMengen.ts. Keine Ziegelrechnung.
              Und ausdruecklich KEINE Schaetzung: der Dateikopf sagt, dass genau das
              der Fehler war, den dieses Werkzeug beseitigt hat.
```

## 5 · Abnahmekriterien

```text
W-20-1  (P1) Die sieben Blaetter lesen holzMengen.ts AB, mit Zeilenangabe: die drei
        Exporte (:23, :29, :44), die Typunterscheidung (:52-58) und die
        Gueltigkeitspruefung gueltigeLaenge (:41). Gegenprobe: jede Angabe nachlesbar.

W-20-2  (P1, DER TRAGENDE PUNKT) 1-ZWECK nennt den Grund aus dem Dateikopf WOERTLICH:
        die Stueckliste schaetzte vorher aus dem Rechteck-Rahmen, waehrend die Engine
        an die geclippte Geometrie zeichnete — ZWEI WAHRHEITEN. Ohne diesen Satz liest
        die naechste Rolle eine gewoehnliche Aggregation und weiss nicht, warum sie
        nicht rechnen darf.

W-20-3  (P1) Die Schiftsparren-Begruendung steht in 2-FUNKTION: sie zaehlen als
        Gemeinsparren mit, "sonst fallen die an Kehle/Grat geclippten Sparren aus
        Bauholz-m3 und Lohn heraus". Ein Unter-Count in der Stueckliste ist ein
        Fehlbetrag im Angebot — das ist die Begruendung, nicht die Bequemlichkeit.

W-20-4  (P1) 7-GRENZEN nennt die Ziegelmenge als FEHLEND, mit den Messzahlen:
        'stueck.*m2' 0 Treffer in geometry/, 'bedarf' 1 und der ist eine
        Gaubenbemerkung. Und die Zieladresse: F-011 x Bedarf_Stk_m2 aus W-23.
        AUSDRUECKLICH auch: 'ziegel' hat 16 und 'deckung' 79 Treffer — beide als TYP
        beziehungsweise LAST, nicht als Menge. Wer die 95 Treffer fuer eine
        Mengenrechnung haelt, sucht falsch.

W-20-5  (P1) Die Unterscheidung Lattmass gegen Lattenlaenge steht im Blatt. Grund: ich
        habe sie beim Messen selbst verwechselt und 'lattenMengen 0 Treffer' gemeldet,
        obwohl das Feld lattenLaenge heisst und gefuellt ist. Wer die zwei Fragen
        vermischt, sucht die Lattweite in der Mengendatei.

W-20-6  (must_preserve) resources/** und app/** byte-identisch — reine Doku-Stufe.
        holzMengen.ts und seine 6 Testzusagen unberuehrt.

W-20-7  (P1, §3 wird BELEGT) Beide Orte nach ARBEITSREGELN §3, beide Zahlen genannt,
        unmittelbar vor der ersten Aenderung. REGISTER.md liegt im Scope mehrerer
        W-Blaetter — vor der Registerzeile erneut messen.
```

## 6 · Rückweg & Entdeckung

```text
RUECKWEG      reiner Revert. Sieben Doku-Blaetter; kein Code, kein Datenpfad.
KOPIE AUSSERHALB DER MASCHINE  ZUM BAUZEITPUNKT ZU PRUEFEN, hier nicht behauptet.
ENTDECKUNG    das Signal ist eine Zahl, die aus einer FORMEL kommt statt aus der Liste:
              nennt ein Blatt eine Sparrenlaenge als "Anzahl x Hoehe", ist genau die
              zweite Wahrheit zurueck, die dieses Werkzeug beseitigt hat.
```

## 7 · Konfliktprüfung §3 — unmittelbar vor dem Schnitt gemessen (H-4)

```text
Tafelzeile   ->  0        Zustandsfeld  ->  0        §3 ist FREI
docs/STATUS.md im Arbeitsbaum: 0
Dieser SCHNITT fasst das neue Blatt und STATUS.md an. Der BAU wuerde REGISTER.md
        beruehren und muss §3 dann erneut messen (W-20-7).
ZUR REIHENFOLGE: W-23 steht auf CODE_FERTIG beim Evaluator. W-20 verweist auf W-23s
        Spalten (Bedarf_Stk_m2), baut aber NICHT darauf — der Verweis ist eine
        Zieladresse, keine Abhaengigkeit. W-20 kann vor W-23s Abnahme gebaut werden.
W-20 wird auf ENTWURF geschnitten und nimmt keinen §3-Platz.
```

```yaml
ballbesitz: "plan-pruefer (DoR)"
warum_BESCHRIEBEN_und_nicht_ENTWORFEN: "der Kern ist gebaut, getestet und im Dateikopf begruendet —
        das ist eine Ablesung. Die fehlende Ziegelmenge wird als GRENZE benannt, nicht als Vorgabe;
        ein ENTWORFEN-Teil in einem BESCHRIEBEN-Blatt wuerde zwei Stufen vermischen, die Yamas
        Legende ausdruecklich als parallel und nicht aufeinanderfolgend fuehrt."
meine_eigene_fehlmessung: "ich meldete 'lattenMengen 0 Treffer' und schloss auf 'Lattung fehlt'. Das
        Feld heisst lattenLaenge und ist gefuellt. H-9 an mir, vierter Fall heute: mein Muster suchte
        eine Schreibweise statt die Sache. Berichtigt, und die Unterscheidung Lattmass gegen
        Lattenlaenge ist jetzt Kriterium W-20-5."
was_es_nicht_ist: "kein Angebot, keine Preisrechnung, keine Zeitwerte. F-051 ist 🔴 GESPERRT und
        A-16 hat gezeigt, was ein Lohnkostenbetrag ohne Herkunft anrichtet."
```

## §11 — Votum W-20 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-20"
votum: ABGENOMMEN
fehlerklasse: KEINE
abnahme_commit: "65358372"
elter: "a146e0b3"
pruefstand: "worktree --detach auf 65358372, node_modules UND vendor"

messtisch_alle_sieben:
  W-20-1: GRUEN
    beleg: "Die fuenf genannten Fundstellen selbst geoeffnet, alle exakt:
              :23 export interface HolzStueck   ·  :29 export interface HolzMengen
              :44 export function holzMengenAusListe(...)
              :41 return typeof l === 'number' && Number.isFinite(l) && l > 0 ? l : 0
                  — die Funktion heisst gueltigeLaenge und beginnt in :40
              :52-58 die Typunterscheidung (latte / Konterlatte / Schiftsparren)
            Und die Zahl der Exporte selbst gezaehlt: 3, wie das Blatt sagt."
  W-20-2: GRUEN — der tragende Punkt, WOERTLICH
    beleg: "1-ZWECK:11-12 zitiert den Dateikopf: '... schaetzte Sparren-/Lattenlaengen aus dem
            Rechteck-Rahmen ... Die Engine zeichnet die Staebe aber bereits an die reale (an
            Walm/L/T geclippte) Geometrie -> ZWEI WAHRHEITEN.'
            Ich habe den Dateikopf (holzMengen.ts:5-9) danebengelegt: zeichengleich zitiert,
            nicht paraphrasiert."
  W-20-3: GRUEN
    beleg: "2-FUNKTION:37-43 traegt die Begruendung als Zitat aus dem Code:
            'EA28: Schiftsparren sind Gemeinsparren (nur verkuerzt/angeschnitten) — sie MUESSEN
            hier mitzaehlen, sonst fallen die an Kehle/Grat geclippten Sparren aus Bauholz-m3
            und Lohn heraus (Unter-Count).'
            Im Code selbst geoeffnet: :57-58, wortgleich. Die Ueberschrift trifft die Sache:
            'die Begruendung steht im Code, nicht in der Gewohnheit'."
  W-20-4: GRUEN — alle vier Messzahlen selbst nachgemessen
    beleg: |
      'stueck.*m2' in geometry/   0    Blatt sagt 0    stimmt
      'bedarf'                    1    Blatt sagt 1    stimmt — und ich habe die Zeile GELESEN:
                                                       gaubeGeometrie.ts:127, eine Gaubenbemerkung
      'ziegel'                   16    Blatt sagt 16   stimmt
      'deckung'                  79    Blatt sagt 79   stimmt
    bewertung: "Und der Satz dazu ist der eigentliche Wert: 'Wer die 95 Treffer fuer eine
            Mengenrechnung haelt, sucht falsch.' Das ist H-6 vorweggenommen, nicht nachgetragen."
  W-20-5: GRUEN
    beleg: "1-ZWECK:49-54 trennt die zwei Fragen: 'WIE WEIT liegen die Latten auseinander ->
            Lattmass in mm' gegen 'lattenLaenge (holzMengen.ts:35) und ist gefuellt'.
            Zeile 35 selbst geoeffnet: 'lattenLaenge: number;'. Und 2-FUNKTION:58 sagt es
            noch einmal in der Ergebnistabelle: 'Traglatten, nicht Lattmass'."
    bemerkenswert: "Das Kriterium ist aus SEINEM eigenen Messfehler entstanden ('ich habe sie
            beim Messen selbst verwechselt') — und die Unterscheidung steht jetzt an zwei
            Stellen im Blatt, damit sie der naechsten Rolle nicht auch passiert."
  W-20-6: GRUEN
    beleg: "resources/ und app/ 0 Dateien im Bau. holzMengen.test.ts traegt 6 test()-Bloecke,
            wie das Blatt sagt. Insel-Suite 1698/1698/0."
  W-20-7: GRUEN
    beleg: "Am Elter des Baus steht W-20 an beiden Orten auf IN_ARBEIT (Tafel 1 / Feld 1).
            Und die Registerzeile: genau EINE geaendert (LEER -> BESCHRIEBEN), gelesen statt
            gezaehlt — Minus- und Plus-Fassung derselben W-20-Zeile."

die_frage_aus_meinem_claim_beantwortet:
  was_ich_pruefen_wollte: "Die Registerzeile fuehrt F-011 und F-023. Nach 603eddc2 (sieben von
        zehn Registerformeln gefallen) wollte ich die Zuordnung am CODE pruefen, bevor ich sie
        im Blatt lese."
  was_der_bau_sagt: "3-FORMELN fuehrt beide auf und schreibt daneben: 'Was das Werkzeug HEUTE
        rechnet — und es ist keine Formel. Eine Summe ueber eine Liste. Es gibt keine
        Umformung, keine Trigonometrie, keinen Grenzwert.' Die Registerzeile traegt jetzt
        'F-011, F-023 — heute keine benutzt'."
  was_ich_messe: "holzMengen.ts enthaelt KEINEN Math-Aufruf, keine Winkelfunktion, keine
        Wurzel. Die einzigen Rechenzeilen sind drei Summierungen (:53, :55, :59) und ein
        Zaehler (:60). Die Aussage des Blattes haelt: heute rechnet W-20 keine Formel."
  meine_dritte_falle_dabei: "Mein Suchmuster meldete zuerst EINEN Math-Treffer. Gelesen war es
        Zeile 57 — und getroffen hatte 'sin' das Wort 'sind' in 'Schiftsparren SIND
        Gemeinsparren'. Ein Fehltreffer im Kommentar, kein Rechenaufruf. Genau H-6, und genau
        die Falle, vor der W-20-4 im selben Blatt warnt."

was_diesen_bau_heraushebt:
  - "Er zitiert den Code, statt ihn zu paraphrasieren — Dateikopf und EA28-Kommentar stehen
     woertlich im Blatt, und ich konnte beide danebenlegen."
  - "Er macht aus einem EIGENEN Messfehler ein Kriterium (W-20-5, Lattmass gegen Lattenlaenge)
     und schreibt die Unterscheidung an zwei Stellen ins Blatt."
  - "Er nennt die Registerformeln, ohne sie zu benutzen — mit dem Satz, wofuer sie SPAETER
     gebraucht werden (Ziegelmenge aus W-23). Keine geratene Zuordnung, keine leere Spalte."

zusammenfassung: "Sieben von sieben. Jede Fundstelle geoeffnet, jede der vier Messzahlen
     nachgemessen, der Dateikopf danebengelegt — alles trifft. Die Formelfrage aus meinem
     Claim ist beantwortet und zwar in die richtige Richtung: die Registerzeile fuehrt zwei
     Formeln, das Werkzeug benutzt heute keine, und genau das steht jetzt in beiden."

ballbesitz: release-pruefer
```
