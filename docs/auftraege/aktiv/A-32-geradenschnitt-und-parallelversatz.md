# A-32 — F-004 ist spezifiziert und nicht gebaut. Und der Parallelversatz braucht keine neue Nummer

```yaml
auftrag: "A-32"
werkzeug: "—  (Geometrie-Fundament der Hausplaner-Insel)"
art: "BAU — zwei Funktionen reiner Geradenmathematik plus Tests. Kein Modellbefehl,
      kein Schema, keine Oberflaeche."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: 8233cf6e
prioritaet: P2
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 13.08. — Claim VOR dem Schnitt."
kennung_geprueft: "A-32 hat NULL Treffer in docs/STATUS.md und NULL Blaetter in
                   docs/auftraege/aktiv/. A-01 bis A-31 sind vergeben. Frei."
keine_dublette: "Gemessen, BEVOR geschnitten: grep auf exportierte Funktionen mit schnitt, intersect
                 oder kreuz im ganzen Hausplaner liefert nur berechneAusschnitt und flaechenBilanz
                 (geometry/dachAusschnitt.ts) — Dachausschnitt, nicht Geradenschnitt. F-020 (Straight
                 Skeleton) ist NICHT gebaut: grep auf skeleton und F-020 ist leer. Und es gibt keine
                 Datei fuer reine Geradenmathematik — wallGeometry.ts ist wand-spezifisch
                 (WandEingabe, WandBand, Tuer), editierGeometrie.ts sind Bearbeitungs-Operationen."
anlass: "Cluster 3 des Fahrplans (trimmen, verlaengern, versatz) haengt daran, und die Landkarte hat
         recht: 'der Befehl, der es rechnet, fehlt'. Ich hatte es zuerst als Anschlussarbeit
         eingeordnet und musste das zurueckziehen (485004c4). Nach A-31 (Klammer im Store) ist DIES
         der zweite und letzte Baustein — und er ist von A-31 UNABHAENGIG: trimmen aendert EINEN
         Knoten und braucht keine Klammer."
grundlage: "FORMELSAMMLUNG.md:75-87 (F-004 — Zaehlerzeile am 13.08. berichtigt, siehe Abschnitt 1)
            und :132-144 (F-020, die
            Kantenversatz-Formel) und :4 ('Eine Formel steht genau einmal') ·
            geometry/wallGeometry.ts:84 (EPS = 1e-6) und :110-152 (gehrungsEcken, loest den
            Sonderfall) · commands/applyCommand.ts:129 (die Normale, eingebettet) ·
            app/tools/werkzeugLandkarte.ts:77/:79/:80"
```

## 1 — F-004 ist bis zum Grenzfall spezifiziert, und es gibt sie nicht

```text
FORMELSAMMLUNG.md:75-87, vollstaendig:
  ### F-004 · Schnittpunkt zweier Geraden
  - Zweck:   Wandachsen verschneiden, Ecke bilden
  - Eingabe: Gerade 1 durch A,B · Gerade 2 durch C,D
  - Formel:  n = (Cx−Ax)(Dy−Cy) − (Cy−Ay)(Dx−Cx)      <- BERICHTIGT 13.08.
             m = (Bx−Ax)(Dy−Cy) − (By−Ay)(Dx−Cx)
             t = n / m              (m ≠ 0)
             S = A + t·(B−A)
  - Ausgabe: Schnittpunkt S
  - Grenzfall: |m| < ε → parallel oder deckungsgleich, kein Schnittpunkt.
               „Das ist der haeufigste Absturzgrund in Wandverschneidungen.
                Immer pruefen."

DIE ZAEHLERZEILE WAR FALSCH — gefunden VOM GENERATOR beim Ziehen dieses
Auftrags (5bf61e54), nicht von mir beim Schneiden:
  alte Fassung   n = (Ax−Cx)(Dy−Cy) − (Ay−Cy)(Dx−Cx)   also (A−C) kreuz s
  richtig        n = (Cx−Ax)(Dy−Cy) − (Cy−Ay)(Dx−Cx)   also (C−A) kreuz s
  Sie liefert −t und damit einen Punkt auf der FALSCHEN SEITE von A;
  S = A + t·(B−A) legt die Bedeutung von t eindeutig fest.
NACHGERECHNET, vier Faelle, zwei unabhaengige Muster:
  waagrecht x senkrecht  alt (−5,0)   richtig (5,0)    Soll (5,0)
  kurz, versetzt         alt (−1,0)   richtig (1,0)    Soll (1,0)
  zwei Diagonalen        alt (−2,−2)  richtig (2,2)    Soll (2,2)
  schief ohne Symmetrie  alt (−2.625, 0.1875)  richtig (4.625, 3.8125)
  -> t-Summe alt+richtig ist in JEDEM Fall exakt 0.000000000000
  -> und die Probe OHNE Soll-Wert von Hand, beim schiefen Fall: liegt S auf
     BEIDEN Geraden? alter Punkt auf A−B JA, auf C−D NEIN (Kreuzprodukt −58).
     Der alte Punkt ist also KEIN Schnittpunkt.
FORMELSAMMLUNG.md:80 ist berichtigt, mit der alten Fassung daneben und dem
Beleg. Reichweite gemessen: die Formelzeile stand an ZWEI Stellen, hier und
dort; in zehn weiteren Dokumenten wird F-004 nur GENANNT.

GEBAUT IST SIE NICHT. Was existiert, loest einen ANDEREN Fall:
  geometry/wallGeometry.ts:110  gehrungsEcken(V, p, q, h)  — NICHT exportiert
    bekommt einen GEMEINSAMEN Scheitel V uebergeben und rechnet ueber die
    Winkelhalbierende (t = einheit(p+q), len = h/sinHalb) bei GLEICHER
    Halbdicke h. Fuer trimmen braucht man den Schnittpunkt zweier Waende,
    die sich gerade NICHT beruehren.
```

> **Das ist ein Bau nach Spezifikation — aber die Spezifikation musste erst berichtigt werden.** *Formel,
> Ausgabe und Grenzfall standen da, und die Sammlung sagt sogar, wo später der Fehler sitzt. **Nur die
> Zählerzeile selbst war falsch** (oben). **Was fehlt, ist die Umsetzung** — und ohne sie bleiben
> `trimmen` und `verlaengern` zu.*

> ***Und das ist die Lehre, die über diesen Auftrag hinausgeht:*** *die Formel stand seit ihrer Aufnahme
> unangefochten da und wurde mehrfach geprüft — aber auf **Vorhandensein und Wortlaut**, nie auf
> **Richtigkeit**. Auch ich habe sie beim Schneiden zitiert und nicht gerechnet. **Aufgefallen ist sie
> erst, als jemand sie bauen sollte.** Eine Formel, die niemand rechnet, ist nicht geprüft, sondern nur
> abgeschrieben.*

## 2 — Der Parallelversatz braucht KEINE neue Nummer: die Formel steht in F-020

```text
FORMELSAMMLUNG.md:139-144, innerhalb von F-020 (Straight Skeleton):
  - Formel (Kantenversatz zur Zeit t):
      Kante als Gerade:  a·x + b·y + c = 0    mit a²+b² = 1
      Versetzte Kante:   a·x + b·y + c − t = 0
      Gewichtet:         w·(a·x + b·y + c) − t = 0

UND DER KOPF DER SAMMLUNG SAGT, :4:  „Eine Formel steht genau einmal."
```

> **Damit ist die Zuordnung entschieden und begründet: der Parallelversatz einer Wand ist die
> Normalform-Verschiebung aus F-020.** *Dieselbe Mathematik, anderer Zweck — F-020 versetzt **alle**
> Kanten eines Polygons nach innen (Vorzeichen aus der Orientierung), hier wird **eine** Gerade auf
> eine benannte Seite versetzt. **Eine neue F-Nummer dafür wäre ein Verstoß gegen „genau einmal"** und
> die zweite Wahrheit, die die Bauordnung verbietet.*

> ***Der Widerspruchsweg steht offen:*** *hält der Bauende es beim Rechnen für eine **andere** Formel,
> meldet er das als Befund — mit der Stelle, an der die zwei auseinanderlaufen. **Er erfindet keine
> Nummer und er schweigt nicht.** Das ist die Lehre aus W-21: eine gemeldete Lücke ist billiger als
> eine geratene Nummer.*

**Und F-020 selbst ist nicht gebaut** — *`grep` auf `skeleton` und `F-020` über die ganze Insel ist
leer. **Dieser Auftrag baut F-020 nicht**, er benutzt nur die eine Zeile Normalform daraus. Das ist
ausdrücklich im Scope-Ausschluss, weil „Straight Skeleton" ein eigenes Vorhaben von anderer Größe ist.*

## 3 — Die Falle, die der Auftrag lösen muss: WELCHES ε?

```text
F-004s Grenzfall sagt „|m| < ε" und nennt KEINEN Wert. Und m ist ein
Kreuzprodukt zweier Differenzvektoren — die Einheit ist mm², nicht mm.

IM HAUS GIBT ES ZWEI verschiedene Epsilons, beide gemessen:
  geometry/wallGeometry.ts:84   const EPS = 1e-6   dimensionslos, benutzt fuer
                               sinHalb in der Gehrung (ein Sinus, kein Mass)
  FORMELSAMMLUNG F-001:53      ε = 0,5 mm         fuer Punktabstaende

KEINES VON BEIDEN PASST AUF m:
  0,5 mm ist eine Laenge, m ist eine Flaeche.
  1e-6 dimensionslos auf einen mm²-Wert angewandt heisst: zwei Waende von je
  10 m Laenge (10 000 mm) haben bei EINEM Grad Winkel ein m von etwa
  1,7 Millionen — und zwei Waende von 100 mm bei DEMSELBEN Winkel etwa 175.
  Eine absolute Schwelle waere also LAENGENABHAENGIG: dieselbe Winkellage
  waere bei kurzen Waenden 'parallel' und bei langen nicht.
```

> ***Die Entscheidung dieses Auftrags: m wird NORMALISIERT, nicht absolut geprüft.*** *`m` geteilt
> durch das Produkt der beiden Segmentlängen ist der **Sinus des Zwischenwinkels** — dimensionslos,
> längenunabhängig, und damit vergleichbar mit dem `EPS = 1e-6`, das das Haus schon für einen Sinus
> benutzt (`wallGeometry.ts:84`). **Der Grenzfall wird dann eine Winkelaussage** („die Achsen sind
> parallel"), und das ist, was der Aufrufer wissen will.*

> **Warum das im Auftrag steht und nicht im Bau:** *ohne diese Festlegung rät der Bauende eine
> Schwelle, und der Evaluator prüft gegen die Erwartung, die er selbst hineingelesen hat. **Und der
> Fehler wäre nicht sichtbar** — er zeigt sich erst bei einer ungewöhnlichen Wandlänge, also lange
> nach der Abnahme.*

## 4 — Wohin die zwei Funktionen gehören

```text
GEPRUEFT, ob eine vorhandene Datei passt (Bauordnung: vor Neuentwicklung
vorhandene Module pruefen):
  wallGeometry.ts     317 Z. — WAND-spezifisch: WandEingabe, WandBand,
                      wandBaender, tuerBlattGeometrie. Eine allgemeine
                      Geradenfunktion hier vergroessert eine schon grosse
                      Datei um einen fremden Zweck.
  editierGeometrie.ts  75 Z. — BEARBEITUNGS-Operationen auf Waenden
                      (versetzen, spiegeln, bbox). Der Parallelversatz waere
                      hier denkbar, der Geradenschnitt nicht.
  dachAusschnitt.ts   510 Z. · linienBauteile.ts 167 Z. — Dach, nicht Geraden.
  -> Es gibt KEINE Datei fuer reine Geradenmathematik.

VORGABE: eine neue Datei geometry/geradenGeometrie.ts, mit BEIDEN Funktionen.
BEGRUENDUNG, und sie ist gemessen: dieselbe Rechnung sitzt heute ZWEIMAL im
Verbraucher eingebaut statt in geometry/ zu liegen —
  commands/applyCommand.ts:129   die Normale, in treppenDurchbrueche
  geometry/wallGeometry.ts:110   der Gehrungs-Schnittpunkt, nicht exportiert
Beide Male fehlt sie dem naechsten Verbraucher. Eine eigene Datei ist die
Antwort auf diese Beobachtung und nicht eine Vorliebe.
```

## 5 — Scope

```text
A-32 IST  (1) geometry/geradenGeometrie.ts mit ZWEI Funktionen:
              - Schnittpunkt zweier Geraden nach F-004, mit dem Grenzfall als
                NORMALISIERTER Pruefung (Abschnitt 3). Rueckgabe: Punkt ODER
                null bei parallel/deckungsgleich — kein NaN, kein Wurf.
              - Parallelversatz einer Gerade/Wandachse auf eine benannte Seite
                mit Abstand d, Mathematik nach F-020s Normalform (:141-143).
                Rueckgabe: die versetzte Achse.
          (2) Tests, die ROT werden koennen, mit den Grenzfaellen:
              parallel, deckungsgleich, Laenge 0, sehr kurze gegen sehr lange
              Segmente bei GLEICHEM Winkel (der Laengenunabhaengigkeits-Fall).
          (3) die F-Nummern-Zuordnung steht als Kommentar an den Funktionen:
              F-004 beim Schnitt, F-020 (Normalform) beim Versatz.

A-32 IST NICHT
          der BAU von F-020 Straight Skeleton. Nur die eine Normalform-Zeile
          wird benutzt. Skeleton ist ein eigenes Vorhaben anderer Groesse und
          heute NICHT gebaut (grep auf skeleton und F-020 ist leer).
          das ANSCHLIESSEN von trimmen, verlaengern oder versatz. Kein
          Registry-Eintrag, kein Modellbefehl, keine Oberflaeche. Die drei
          Werkzeuge sind DANACH je ein eigener kleiner Vorgang.
          eine Aenderung an gehrungsEcken oder wallGeometry.ts. Die Gehrung
          loest ihren Sonderfall richtig und bleibt, wie sie ist. Ob sie spaeter
          auf die neue Funktion umgestellt wird, ist eine eigene Frage — HIER
          wird sie nicht angefasst, damit kein Bestand riskiert wird.
          eine Aenderung an treppenDurchbrueche (applyCommand.ts:129). Dieselbe
          Begruendung. Als Befund benannt, nicht umgebaut.
          eine neue F-NUMMER in der Formelsammlung. Der Versatz ist F-020s
          Normalform; eine zweite Nummer waere ein Verstoss gegen
          FORMELSAMMLUNG:4.
```

## 6 — Abnahmekriterien

```text
A-32-1 (P1, TRAGEND) Der Schnittpunkt rechnet nach F-004 IN DER BERICHTIGTEN
       FASSUNG — n = (Cx−Ax)(Dy−Cy) − (Cy−Ay)(Dx−Cx). Die alte Zaehlerzeile
       liefert −t; wer sie abschreibt, baut den gespiegelten Punkt. Und der
       Grenzfall ist
       NORMALISIERT geprueft, nicht absolut. Der Nachweis ist ein Test, der ROT
       werden kann und den Unterschied ZEIGT: zwei Segmente von 100 mm und zwei
       von 10 000 mm mit DEMSELBEN Zwischenwinkel ergeben dasselbe Urteil
       (Schnitt oder parallel). Eine absolute mm²-Schwelle faellt an diesem Test
       durch — das ist der Sinn.
       Ohne diese Pruefung ist der Fehler unsichtbar bis zu einer
       ungewoehnlichen Wandlaenge, also lange nach der Abnahme.
A-32-2 (P1) Bei parallel oder deckungsgleich kommt `null` zurueck — kein NaN,
       kein Infinity, kein Wurf. Test je Fall. Das ist dieselbe Zusage, die
       kalibrierung.ts:33 (berechneMassstab) schon macht und deren Wortlaut
       W-16/1 als Vertrag an die Aufrufer festhaelt: „ein Aufrufer muss nicht
       selbst darauf pruefen".
A-32-3 (P1) Der Parallelversatz ist als F-020s NORMALFORM umgesetzt und die
       Zuordnung steht als Kommentar an der Funktion, mit Fundstelle
       (FORMELSAMMLUNG.md:141-143). KEINE neue F-Nummer.
       WIDERSPRUCHSWEG: haelt der Bauende es beim Rechnen fuer eine ANDERE
       Formel, meldet er es als Befund mit der Stelle, an der die zwei
       auseinanderlaufen — er erfindet keine Nummer und er schweigt nicht.
A-32-4 Die Seite des Versatzes ist BENANNT und nicht implizit: der Aufrufer sagt,
       auf welche Seite versetzt wird, und die Bedeutung steht im Doc-Kommentar
       (welche Seite bei welcher Achsrichtung). Ein Vorzeichen ohne Erklaerung
       ist die Stelle, an der der naechste Verbraucher spiegelverkehrt baut.
A-32-5 KEIN Anschluss. Gegenprobe: app/tools/**, commands/** und domain/**
       kommen im Bau-Commit NULL Mal vor. Steht dort eine Aenderung, ist der
       Auftrag gesprengt und geht zurueck an den Planner.
A-32-6 gehrungsEcken (wallGeometry.ts:110) und treppenDurchbrueche
       (applyCommand.ts:129) sind UNBERUEHRT. Gegenprobe am Diff: beide Dateien
       kommen im Bau-Commit nicht vor. Ihre Umstellung ist ein eigener Vorgang.
A-32-7 Die Insel-Suite bleibt gruen, selbst gefahren, Rohausgabe mit Zaehler
       vorher/nachher.
A-32-8 Das EPS ist NICHT abgeschrieben, sondern begruendet: der Wert steht mit
       einem Satz da, warum er fuer eine dimensionslose Groesse gilt. Wenn der
       Bau 1e-6 aus wallGeometry.ts:84 uebernimmt, steht die Fundstelle dabei —
       und der Satz, dass es dort ebenfalls auf einen Sinus angewandt wird.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am COMMIT** (E1),
**Nachweis muss rot werden können** (Pflichtprüfung 4), **jede Zahl mit Träger** (Prüfung 7).

```yaml
was_die_pflichtpruefungen_hier_verhindert_haben: "Pruefung 1 zweimal. ERSTENS die Frage, ob es die
        Funktion schon gibt: der Geradenschnitt SCHEINT gebaut zu sein, weil gehrungsEcken einen
        Schnittpunkt liefert — aber sie loest den Fall mit GEMEINSAMEM Scheitel, und trimmen braucht
        genau den anderen. Haette ich nur gegreppt, waere der Auftrag als Dublette abgelehnt worden
        oder haette den falschen Bestand angeschlossen. ZWEITENS die Frage nach der F-NUMMER: mein
        erster Gedanke war, der Parallelversatz brauche eine neue. Die Sammlung hat sie schon — in
        F-020, und ihr Kopf sagt in :4 'Eine Formel steht genau einmal'. Eine neue Nummer waere eine
        zweite Wahrheit gewesen, und zwar eine, die kein Test faengt."
die_eps_falle_ist_der_eigentliche_wert_dieses_blattes: "F-004 sagt '|m| < ε' und nennt keinen Wert. m
        ist ein Kreuzprodukt in mm². Das Haus fuehrt zwei Epsilons — 1e-6 dimensionslos in
        wallGeometry.ts:84 und 0,5 mm in F-001 — und KEINES passt: eine Laenge ist keine Flaeche, und
        eine absolute Flaechenschwelle waere laengenabhaengig. Zwei Waende von 10 m bei einem Grad
        Winkel haben ein m von etwa 1,7 Millionen, zwei von 100 mm bei demselben Winkel etwa 175.
        Ohne Festlegung haette der Bauende geraten und der Fehler waere erst bei einer
        ungewoehnlichen Wandlaenge aufgefallen. A-32-1 macht die Laengenunabhaengigkeit zum TEST."
was_ich_gemessen_habe_und_was_nicht: "SELBST GEMESSEN: F-004 und F-020 im Wortlaut der Sammlung, dass
        keine exportierte Schnittfunktion existiert, dass F-020 nicht gebaut ist, gehrungsEcken im
        Rumpf samt ihrer Voraussetzung (gemeinsamer Scheitel, gleiche Halbdicke), die zwei Epsilons
        mit Fundstelle, die Zeilenzahlen aller geometry-Dateien, und dass keine Datei fuer reine
        Geradenmathematik existiert. NICHT GEMESSEN: ob die Umstellung von gehrungsEcken auf die neue
        Funktion moeglich waere — sie ist ausdruecklich ausgeschlossen, weil sie Bestand riskiert, und
        ich habe die Aequivalenz nicht geprueft. Das ist ein eigener Vorgang und ich behaupte nicht,
        dass er lohnt."
A_32_nimmt_keinen_paragraf3_platz: "ENTWURF, nicht IN_ARBEIT."
```
