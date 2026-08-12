# W-27/1 — BAU: Dachkantentypen in die Insel. Der erste Auftrag, der Produktivcode erzeugt

```yaml
auftrag: "W-27/1"
werkzeug: "W-27 Dachkantentypen — First · Grat · Kehle · Traufe · Ortgang"
art: "BAU — Produktivcode in der Hausplaner-Insel. Ziel: W-27 von ENTWORFEN auf GEBAUT.
      ERSTER Bauauftrag der Werkbank: 0 von 43 Werkzeugen tragen heute GEBAUT."
spur: A
heimat_app: ticket
dor_beleg: "steht aus — plan-pruefer."
status_steht_in: docs/STATUS.md
basis_sha: ff7e23ec
prioritaet: P1
anlass: "Yamas Freigabe 12.08.: 'dann fang mit W-27 an', auf meine Frage nach dem ersten
         Bauauftrag. Vorher hatte er gefragt, ob überhaupt mit der Produktion von Werkzeugen
         begonnen wurde — gemessene Antwort: nein, 0 von 43 tragen GEBAUT."
ballbesitz: "plan-pruefer (DoR)"
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "W-27s sieben Blätter (ENTWORFEN, BETRIEBSBESTAETIGT) als VORGABE ·
            docs/planner/pv-belegung-referenz/DachplanerProPage.tsx (3.786 Z.) als Quelle ·
            F-025 🟢 · F-026 🟢 · W-07 BETRIEBSBESTAETIGT"
```

## 1 — Was gebaut wird (aus W-27s Vorgabe, nicht neu entworfen)

```ts
// Die drei Typlisten — woertlich aus 2-FUNKTION, Fundstellen im Prototyp :85-87
type EdgeTopologyType   = 'TRAUFE' | 'GIEBEL' | 'PULT_WAND' | 'WALM' | 'TEILWALM';
type TopologyCornerType = 'innen' | 'aussen';
type TopologyJoinType   = 'grat' | 'kehle' | 'ortgang' | 'neutral';

// Die zwei Strukturen — :128, :135
interface EdgeTopologyConfig { id; type; pitch; label }
interface TopologyCornerInfo { index; point; angleDeg; cornerType; joinType }

// Die Funktion — :193-230
analyzeTopology(points: TopologyPoint[], edgeConfigs: EdgeTopologyConfig[]): TopologyCornerInfo[]
```

**Die vier Verarbeitungsschritte, vollständig aus der Vorgabe:**

```text
0  UMLAUFSINN      signedArea > 0 -> isCCW                              (:194-195)
1  ECKENWINKEL     baseAngle = acos(n1·n2) in Grad                      (:203-204)
                   isInnerReflex = isCCW ? cross > 0 : cross < 0         (:205-206)
                   angleDeg = isInnerReflex ? 360 - baseAngle : baseAngle (:207)
2  ECKENART        cornerType = angleDeg > 180 ? 'innen' : 'aussen'      (:208)
3  VERBINDUNGSART  joinType = 'neutral'                            (Default, :215)
                   prevIsTraufe UND nextIsTraufe -> innen ? 'kehle' : 'grat'  (:216)
                   Traufe an GIEBEL, beliebige Folge -> 'ortgang'             (:217)
```

> **Der tragende Satz der Vorgabe:** *`EdgeTopologyType` beschreibt eine **KANTE**,
> `TopologyJoinType` beschreibt, was an einer **ECKE** entsteht. **First und Grat sind keine
> Kantentypen — sie entstehen zwischen zwei Kanten.** Wer sie als Kantentyp baut, baut die
> Registerzeile nachträglich richtig und die Sache falsch.*

## 2 — Wohin: eine neue Datei, und der Grund

```text
NEU:  resources/planner/hausplaner/geometry/dachTopologie.ts

Die Nachbarschaft traegt 53 Dateien, acht davon mit dach-Praefix:
  dachAusschnitt · dachGeometrie · dachOeffnung · dachUForm
  dachVerschneidung · dachVorlage · dachWerte · dachformVorlagen
Eine Datei fuer KANTEN- oder ECKEN-Topologie gibt es NICHT — gemessen.
```

**Kein Eingriff in bestehende Dateien.** *Der Bau ist additiv: eine neue Datei, ein Export, und die
Anbindung erst dann, wenn W-27/1 abgenommen ist. **Wer beim Bauen eine vorhandene Dachdatei
umschreibt, hat den Auftrag verlassen** — das wäre ein eigener Vorgang und geht zurück an mich (§7).*

## 3 — Die Namenskollision, und sie ist der gefährlichste Punkt

**`'kehle'` und `'grat'` existieren bereits im Bestand** — gemessen: `'kehle'` in 6 Dateien,
`'grat'` in 5. Ich habe die Stelle geöffnet (Pflichtprüfung 7):

```text
geometry/schifterListe.ts:58   klassifiziereSchifter(vStart, vEnd, vMax, tol)
  :62  untenAngeschnitten = a > tol     // erreicht die Traufe (v=0) nicht -> Kehle
  :63  obenAngeschnitten  = b < m - tol // erreicht den First (v=vMax) nicht -> Grat
  :64  beides -> 'beidseitig'   :65 -> 'kehle'   :66 -> 'grat'   :67 -> 'voll'
```

> **Das ist eine SPARREN-Klassifikation, keine Ecken-Klassifikation.** *Sie fragt, ob ein **Stab**
> angeschnitten ist; W-27 fragt, was an einer **Ecke des Grundrisses** entsteht. **Gleiche Wörter,
> andere Sache** — und genau deshalb ist die Verwechslung wahrscheinlich. Der Generator hat diesen
> Unterschied bei W-27s Ablesung selbst gefunden und benannt: „es fehlt die ECKEN-Klassifikation",
> nicht „die Klassifikation".*

**Der Bau muss die Grenze im Code sichtbar machen** — *durch Namen, Dateikommentar oder beides. Ein
Leser, der `'kehle'` an zwei Orten findet, muss in beiden erkennen können, welche Frage dort
beantwortet wird. **Sonst entsteht die zweite Wahrheit, die der Wächter verbietet.***

## 4 — Was Fangproben fangen müssen (und eine davon ist die wichtigste)

```text
UMLAUFSINN WEGLASSEN — die tragende Probe.
  Ohne Schritt 0 klassifiziert die Funktion bei umgekehrt gezeichnetem Polygon ALLE
  Ecken falsch herum, und zwar LEISE: es gibt kein Ergebnis das ungueltig aussieht.
  Das steht als K-4 in W-27s Blatt, und der Generator hat es bei der Ablesung als
  eine von zwei Praezisierungen aus der Quelle herausgehoben.

prevIsTraufe ZU ENG FASSEN.
  prevIsTraufe ist WAHR fuer TRAUFE, WALM UND TEILWALM (Prototyp :212-213).
  Wer es als „beide Kanten sind TRAUFE" liest, baut Walm- und Teilwalmdaecher
  falsch — also genau die Daecher, wegen derer es Grate gibt.

DEN VIERTEN AUSGANG VERGESSEN.
  'neutral' ist der Default und nicht der Restfall. Wer nur drei Ausgaenge baut,
  liefert undefined statt 'neutral'.

Jede Fangprobe muss WIRKSAM sein: sie wird gefahren und muss FALLEN.
Eine Probe, die gruen bleibt, prueft nichts — das ist der Befund aus W-34-1 und
W-39-5 an einem einzigen Tag.
```

## 5 — Scope und Schutzgrenzen

```text
BAUT       geometry/dachTopologie.ts (neu) + Tests dazu.

BAUT NICHT die Anbindung an W-07 oder die Dachflaechen — erst nach der Abnahme.
           KEINE Aenderung an schifterListe.ts, dachGeometrie.ts oder einer anderen
           vorhandenen Datei.
           KEINE Aenderung am Prototyp docs/planner/pv-belegung-referenz/ — er ist
           Quelle und bleibt unberuehrt.
           KEINE Datenbank, KEINE Produktdaten, KEIN Backend. React/TypeScript bleibt
           auf die Insel begrenzt, und geometry/ IST die Insel.
```

## 6 — Abnahmekriterien

```text
W-27/1-1  (P1) Die drei Typen und zwei Strukturen stehen wie in der VORGABE, mit den
          Fundstellen des Prototyps. Abweichungen sind zulaessig, aber JEDE muss im
          Bericht mit Grund stehen — stillschweigende Umbenennung ist keine Umsetzung.
W-27/1-2  (P1, TRAGEND) analyzeTopology traegt ALLE VIER Schritte einschliesslich des
          Umlaufsinns und ALLE VIER Ausgaenge einschliesslich 'neutral'. Nachweis: die
          Zeilen zeigen, nicht behaupten.
W-27/1-3  (P1) Die NAMENSGRENZE zu klassifiziereSchifter ist im Code sichtbar — im
          Dateikopf oder durch Namen. Ein Leser muss an beiden Orten erkennen, welche
          Frage dort beantwortet wird. Nachweis: die Stelle zitieren.
W-27/1-4  (P1) Die drei Fangproben aus Abschnitt 4 sind gefahren und FALLEN, je mit
          Zaehlerstand vorher und nachher. Eine Probe, die gruen bleibt, ist gemeldet
          und nicht stillschweigend ersetzt.
W-27/1-5  Die Tests laufen, und die Insel-Suite bleibt vollstaendig gruen. Zaehler am
          Bau-Stand nennen, nicht aus diesem Blatt uebernehmen.
W-27/1-6  UEBERGANGSPRUEFUNG nach der Legende: die VORGABE wird gegen die ABLESUNG des
          gebauten Codes geprueft, und Abweichungen stehen im Bericht. Woertlich aus der
          Registerlegende: 'Ein Entwurf, der gebaut wurde und danach nicht nachgemessen
          wird, ist eine unbelegte Behauptung ueber den eigenen Code.'
W-27/1-7  W-27s Blaetter sind nachgezogen: 5-CODE/LIESMICH.md nennt die neue Datei mit
          Zeilen, 6-PRUEFUNG traegt die gefahrenen Fangproben. Die Registerzeile geht
          von ENTWORFEN auf GEBAUT.
W-27/1-8  Prototyp und alle bestehenden Dateien unberuehrt. Nachweis AM COMMIT:
          git show <bau-sha> --name-only zeigt nur die neue Datei, ihre Tests und die
          genannten Blaetter. KEINE FREMDE Aenderung — die eigene Fertigmeldung ist
          ausdruecklich erlaubt und braucht keinen zweiten Commit.
```

**Nachweisform: Befehl und Trefferzeilen** (B5), **Messung am Commit** (E1), **jede Fundstelle am
Bau-Stand statt aus diesem Blatt** (Pflichtprüfung 8), **jede Zählung mit geöffneter Stelle**
(Pflichtprüfung 7).

```yaml
warum_das_der_erste_Bauauftrag_ist: "Yama hat gefragt, ob ueberhaupt mit der Produktion begonnen
        wurde. Gemessene Antwort: 0 von 43 Werkzeugen tragen GEBAUT, die Werkbank hat bisher
        beschrieben und vorgegeben. W-27 ist der beste Anfang, weil die Vorgabe fertig und
        betriebsbestaetigt ist, F-025 und F-026 gruen sind, W-07 betriebsbestaetigt ist, und weil es
        W-07s groesste Grenze schliesst — die Ecken-Erkennung, deren Fehlen Yama bei A-01 selbst als
        Grund fuer ein falsches Nicht-Ziel benannt hat."
warum_eine_NEUE_datei: "Gemessen: 53 Dateien in geometry/, acht mit dach-Praefix, keine fuer
        Kanten- oder Ecken-Topologie. Ein additiver Bau ist ausserdem die Bauart, die dieser Insel
        entspricht — W-39 hat es vorgemacht: die HausplanerApp bleibt unveraendert."
was_dieser_auftrag_NICHT_entscheidet: "Die Anbindung an W-07 und die Dachflaechen. Sie folgt nach der
        Abnahme als eigener Vorgang, damit der erste Bau eine pruefbare Groesse behaelt."
W_27_1_nimmt_den_paragraf3_platz: "Sobald er gezogen wird, ist er IN_ARBEIT und §3 ist belegt. Der
        Platz ist frei: §3 steht bei 0."
```
