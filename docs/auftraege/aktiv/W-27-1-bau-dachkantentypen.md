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


## §11 — Votum W-27/1 (Evaluator, Erstinstanz, 12.08.2026)

```yaml
auftrag: "W-27/1"
votum: ABGENOMMEN
geprueft_an: "a2b63a1f"
elter: "044f78a3"
scope_diff: "7 Dateien, +551/-14. ZWEI Code-Dateien — die erste Abnahme dieser Reihe mit
  Produktivcode. Ausdruecklich geprueft: 0 W-41-Dateien im Scope, obwohl der Generator parallel
  daran baut."
pruefstand: "git worktree add -q --detach auf a2b63a1f, node_modules UND vendor per cp -al —
  und ein ZWEITER Pruefstand auf dem Elter 044f78a3, weil eine Suitezahl ohne Vergleichsstand
  keine Aussage ueber Regression ist."
paragraf_15: "GEGENSTANDSLOS — kein DB-Zugriff, keine Migration, kein Seed im Scope."
browserabnahme: "ENTFAELLT — reine Geometrie ohne Oberflaechenanbindung; der Auftrag schliesst
  die Anbindung an W-07 ausdruecklich aus."

messtisch:

  W-27_1_1_typen_und_strukturen_wie_die_vorgabe:
    urteil: ERFUELLT
    zeichengenau_verglichen: "Die drei Typlisten gegen W-27s 2-FUNKTION gelegt:
      EdgeTopologyType :40 'TRAUFE'|'GIEBEL'|'PULT_WAND'|'WALM'|'TEILWALM' · TopologyCornerType
      :43 'innen'|'aussen' · TopologyJoinType :51 'grat'|'kehle'|'ortgang'|'neutral'. Alle drei
      stimmen mit der Vorgabe Wort fuer Wort. Die zwei Strukturen EdgeTopologyConfig :54 und
      TopologyCornerInfo :62 ebenso."
    beide_abweichungen_stehen_mit_grund: "Mir ist beim Lesen der Ausfuhren aufgefallen, dass der
      Bau VIER Strukturen traegt, wo die Vorgabe zwei nennt. Der Bericht fuehrt genau diese
      beiden als Abweichung 1 und 2 mit Grund: TopologyAnalysis als Rueckgabetyp — und zwar weil
      die QUELLE (Prototyp :193) ihn so fuehrt und das Auftragsblatt TopologyCornerInfo[] nur
      verkuerzt hatte; TopologyPoint exportiert, weil sonst die Eingabe der oeffentlichen
      Funktion nicht benennbar waere. Die Rangfolge ist richtig herum: er folgt der Vorgabe, nicht
      der Verkuerzung im Auftragsblatt. Eine dritte Aenderung (zwei benannte Hilfsfunktionen,
      beide nicht exportiert) ist ebenfalls benannt."

  W-27_1_2_analyzeTopology_TRAGEND:
    urteil: ERFUELLT
    vier_schritte_selbst_gelesen: ":100 'Schritt 0 — der Umlaufsinn' (Funktion :106), :133
      'Schritt 1 — Eckenwinkel', :151 'Schritt 2 — Eckenart', :154 'Schritt 3 — Verbindungsart'.
      Vier, einschliesslich des Umlaufsinns, wie das Kriterium verlangt."
    UND_ALLE_VIER_AUSGAENGE_SELBST_ERZEUGT: "Hier reicht 'die Zeilen zeigen' nicht — ein Ausgang,
      der im Code steht, muss auch erreichbar sein. Ich habe analyzeTopology AUFGERUFEN und alle
      vier erzeugt:
        4x TRAUFE, Rechteck        -> grat    (aussen)
        Traufe/Giebel wechselnd    -> ortgang
        4x GIEBEL                  -> neutral
        L-Form, 6x TRAUFE          -> kehle an der einspringenden Ecke, cornerType 'innen'
      Erreichte Ausgaenge: grat, kehle, neutral, ortgang — deckungsgleich mit dem Typ. Die
      Innenecke der L-Form wird korrekt als einzige mit cornerType 'innen' erkannt."

  W-27_1_3_namensgrenze:
    urteil: ERFUELLT
    die_stelle_zitiert: "dachTopologie.ts:4-17. Der Dateikopf nennt BEIDE Seiten:
      'geometry/schifterListe.ts -> klassifiziereSchifter(...) fragt, ob ein STAB angeschnitten
      ist ... Das ist eine Sparren-Klassifikation.' gegen 'Diese Datei fragt, was an einer ECKE
      des Grundrisses entsteht ... Das ist eine Ecken-Klassifikation.' Mit dem Satz 'Gleiche
      Woerter, andere Sache.' Die Gegenseite habe ich geoeffnet: schifterListe.ts:58 traegt
      klassifiziereSchifter tatsaechlich mit der Signatur (vStart, vEnd, vMax, tol)."

  W-27_1_4_drei_fangproben_gefahren_und_gefallen:
    urteil: ERFUELLT
    ALLE_DREI_SELBST_GEFAHREN: "Je mit Anker (Treffer genau 1x) und md5-Ruecksetzung auf
      bfc684226f02161448ef02d20b7629f3:
        1) Umlaufsinn weglassen (isCCW aus isInnerReflex entfernt)
           -> 1707 pass, 2 FAIL: 'K-4 TRAGEND: der Umlaufsinn — dasselbe Polygon in beiden
              Richtungen ergibt dieselben Ecken' und 'K-4: die L-Form hat GENAU EINE
              einspringende Ecke, in beiden Umlaufrichtungen'
        2) prevIsTraufe auf 'TRAUFE' verengt
           -> 1708 pass, 1 FAIL: 'WALM und TEILWALM zaehlen als Traufe im weiteren Sinn'
        3) joinType-Vorbelegung 'neutral' entfernt
           -> 1707 pass, 2 FAIL: 'der VIERTE Ausgang ... ist neutral — nicht undefined' und
              'jede Ecke traegt IMMER einen der vier Ausgaenge'
      Alle drei FALLEN. md5 nach jeder Ruecksetzung identisch. Seine Angaben 2/1/2 decken sich
      zeichengenau mit meinen Laeufen.
      Das ist der Punkt, an dem W-34-1 und W-39-5 an einem Tag gescheitert sind — hier haelt er."

  W-27_1_5_tests_und_suite:
    urteil: ERFUELLT
    an_BEIDEN_staenden_gemessen: "BAU a2b63a1f: 1709 tests, 1709 pass, 0 fail.
      ELTER 044f78a3: 1698 tests, 1698 pass, 0 fail. Differenz +11, und dachTopologie.test.ts
      traegt genau 11 test()-Bloecke — die neuen Tests sind vollstaendig die eigenen, und es gibt
      keine Regression. Zaehler am Bau-Stand genannt, nicht aus dem Blatt uebernommen."

  W-27_1_6_uebergangspruefung:
    urteil: ERFUELLT
    beleg: "Der Bericht traegt die Tabelle 'Vorgabe (W-27) gegen Gebaut' mit sechs Zeilen und
      zwei markierten Abweichungen, eingeleitet mit dem woertlichen Satz aus der Registerlegende.
      Die Ablesung ist am gebauten Code gemacht und nicht aus dem Entwurf fortgeschrieben."

  W-27_1_7_blaetter_nachgezogen:
    urteil: ERFUELLT
    gemessen: "REGISTER.md: W-27 von ENTWORFEN auf GEBAUT (Diff geoeffnet). 5-CODE/LIESMICH.md
      nennt dachTopologie.ts mit 183 Zeilen — ich habe die Datei gezaehlt, es sind 183 — und den
      Test mit elf Tests. 6-PRUEFUNG traegt die drei Fangproben mit der Spalte 'Gefahren?' und
      den Zaehlern 2/1/2 sowie der Grundlinie 1709."

  W-27_1_8_nichts_fremdes_beruehrt:
    urteil: ERFUELLT
    am_commit_gemessen: "git show a2b63a1f --name-only: genau sieben Dateien — die neue
      Geometrie, ihr Test, W-27s zwei Blaetter, REGISTER.md, der Bericht und docs/STATUS.md.
      Der Prototyp ist unberuehrt (0 Treffer). In STATUS.md habe ich jede geaenderte Zeile auf
      ihren umgebenden Auftragsblock zurueckgefuehrt: FREMDE Zustandsaenderungen 0 — die einzige
      ist W-27/1s eigene Fertigmeldung, und die ist ausdruecklich erlaubt."

meine_eigenen_messfehler_in_dieser_runde: "Keine, die das Urteil beruehrt haetten. Zwei Punkte mit
  erhoehter Gefahr habe ich ausdruecklich geprueft statt angenommen: die parallel gebauten
  W-41-Dateien im Scope (0 Treffer) und der Vergleichsstand fuer die Suitezahl (zweiter Pruefstand
  auf dem Elter, statt 1709 allein zu melden)."

was_diesen_bau_traegt: "Er ist der erste mit Produktivcode, und er haelt genau an der Stelle, an
  der heute zwei Blaetter gescheitert sind: die Fangproben sind GEFAHREN und FALLEN, nicht
  abgelesen. Dazu zwei Dinge, die ein Bauender nicht tun muss — er folgt bei der Rueckgabe der
  QUELLE statt der Verkuerzung im eigenen Auftragsblatt und sagt es, und er benennt eine dritte
  Aenderung, die gar keine Schnittstellen-Abweichung ist."
```
