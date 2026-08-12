# W-27 — Dachkantentypen. Die Regel ist ausformuliert, sie steht nur im falschen Baum

```yaml
auftrag: "W-27"
werkzeug: "W-27 Dachkantentypen — First · Grat · Kehle · Traufe · Ortgang"
art: "STUFE 1 — Blatt schneiden, Ziel ENTWORFEN (Klasse C, wie W-15). Quelle ist ein Prototyp,
      nicht der Bestand — deshalb VORGABE und nicht Ablesung."
spur: A
heimat_app: ticket
status: BEREIT
dor_beleg: "2c0e4ede — plan-pruefer 12.08.: 'W-20 UND W-27 DoR BESTANDEN, beide BEREIT'. Die drei
         Zahlen selbst nachgemessen ('ortgang' als String-Literal 0, TopologyJoinType 0,
         cornerType 0). MIT AUFLAGE, die in Abschnitt 1 eingearbeitet ist: die FORMULIERUNG ging
         weiter als die Zahl.
         NACHGEZOGEN 12.08. vom Planner — und der Mangel ist MEINER: der Pruefer hat den Zustand
         gesetzt und mein Blatt nicht angefasst, weil es nicht sein Eigentum ist. Genau die Luecke
         aus A-16, zum zweiten Mal an einem Tag. Bei W-20 hat der Generator sie selbst
         ueberbrueckt ('die DoR dort gefunden, wo sie steht, statt am Blattkopf zu scheitern') —
         das war Findigkeit, kein Verfahren."
status_steht_in: docs/STATUS.md
basis_sha: c2c6bf4e
prioritaet: P2
anlass: "Klasse C, zweites Werkzeug. Voraussetzungsfrei: W-07 ist BETRIEBSBESTAETIGT,
         F-025 und F-026 sind 🟢 nach A-12."
ballbesitz: "GENERATOR — DoR ist durch (2c0e4ede)."
claim: "planner 12.08. — Claim VOR dem Schnitt."
grundlage: "F-025 🟢 · F-026 🟢 · F-014 ⚠ (die Warnung aus meiner Registermessung 12.08.)
            · docs/planner/pv-belegung-referenz/DachplanerProPage.tsx (3.786 Z.) als Quelle"
```

## 1 · Der Befund — die Regel existiert ausformuliert, und der Insel fehlt die ERKENNUNG

**Gemessen: die vollständige Kantentopologie steht im Prototyp; in der Insel fehlen die Typen und die Ableitung.**

```text
QUELLE   docs/planner/pv-belegung-referenz/DachplanerProPage.tsx   3.786 Zeilen

  :85   type EdgeTopologyType   = 'TRAUFE' | 'GIEBEL' | 'PULT_WAND' | 'WALM' | 'TEILWALM'
  :86   type TopologyCornerType = 'innen' | 'aussen'
  :87   type TopologyJoinType   = 'grat' | 'kehle' | 'ortgang' | 'neutral'
  :128  interface EdgeTopologyConfig { id, type, pitch, label }
  :135  interface TopologyCornerInfo { index, point, angleDeg, cornerType, joinType }
  :155  buildTopologyPolygon(build)
  :182  getDefaultEdgeTopologyConfigs(build, pointCount)
  :193  analyzeTopology(points, edgeConfigs)

GEGENPROBE in resources/planner/ — was die Insel hat und was nicht:
  'grat'              17 Treffer     (als Wort, in Geometriecode)
  'kehle'             33 Treffer     (als Wort)
  'ortgang'            0 Treffer     <- als STRING-LITERAL, siehe Berichtigung unten
  TopologyJoinType     0 Treffer     <- die ERKENNUNG fehlt
  cornerType           0 Treffer     <- die Innen/Aussen-Unterscheidung fehlt
```

> ## ⚠ BERICHTIGT 12.08. — meine Zahl war richtig, meine FORMULIERUNG war falsch
>
> **Auflage des Plan-Prüfers (`2c0e4ede`), selbst nachgemessen und bestätigt:**
>
> ```text
> 'ortgang' als String-Literal        0 Treffer      <- meine Messung, richtig
> 'ortgang' klein, ueberall           9 Treffer
> 'Ortgang' gross                    20 Treffer
> ortgangFlaechenlaengeM              EXPORTIERTE Funktion, dachformVorlagen.ts,
>                                     mit eigener Testzusage (:151-152)
> ortgangausbildung                   FELD mit Werten (:127, :1386, :1410)
> ```
>
> **Hier stand: „gibt es in der Insel NICHT".** *Das ist falsch. Die Insel hat den Ortgang — als
> **Längenrechnung** (`ortgangFlaechenlaengeM(10, 0.3) = 10.6`, getestet) und als **Ausbildungstext**
> je Dachform. **Was fehlt, ist der KANTENTYP, nicht die Sache.***
>
> **Warum das kein Formfehler ist:** *wer den weiteren Satz in `7-GRENZEN` übernimmt, setzt eine
> falsche Grenzaussage ins Werkzeug — und der Nächste baut eine Ortgang-Länge neu, **die es schon
> gibt**. Aus einer zu weiten Formulierung wird doppelter Code.*
>
> **Und die Regel dahinter ist die Umkehrung von H-8, in seinen Worten:** *„**null Vorkommen eines
> MUSTERS ist kein Beleg für die Abwesenheit der SACHE**." H-8 sagt, dass viele Vorkommen keine
> Herkunft belegen; hier belegt keines keine Abwesenheit. **Beides ist derselbe Denkfehler in zwei
> Richtungen** — und er hat ihn nach eigener Aussage zwanzig Runden lang mit A-06 selbst gemacht.*

> **Der Unterschied, auf den es ankommt: die Insel hat die BEGRIFFE und einzelne RECHNUNGEN, nicht
> die ERKENNUNG.** *`grat` und `kehle` kommen 50 Mal vor, `Ortgang` 20 Mal — als Wörter, als Feld
> (`ortgangausbildung`) und als getestete Längenrechnung (`ortgangFlaechenlaengeM`). **Was fehlt, ist
> die Funktion, die aus einer Ecke ableitet, welcher Typ dort entsteht** — der Kantentyp, nicht die
> Sache.*

## 2 · Die Regel im Wortlaut, aus `analyzeTopology` gelesen

```text
1  ECKENWINKEL          angleDeg = isInnerReflex ? 360 - baseAngle : baseAngle
2  ECKENART             cornerType = angleDeg > 180 ? 'innen' : 'aussen'
3  VERBINDUNGSART       joinType = 'neutral'
     prevIsTraufe UND nextIsTraufe          ->  innen ? 'kehle' : 'grat'
     (Traufe und GIEBEL, beliebige Folge)   ->  'ortgang'
     sonst                                  ->  'neutral'
4  ZAEHLUNG             innenEcken · aussenEcken · grate · kehlen · ortgaenge
```

**Das ist F-025 („Was entsteht an der Ecke — Grat, Kehle oder Ortgang?") in ausführbarer Form.** *Die
Formelsammlung führt F-025 als 🟢 mit dem Vermerk „Mathematik nachvollzogen" — hier steht, wie sie
angewandt wird. **Der Prototyp ist damit nicht nur Beleg, sondern Vorlage.***

## 3 · Und es löst die F-014-Warnung aus meiner eigenen Registermessung

*Bei der F-Spalten-Prüfung am 12.08. habe ich W-07s `F-014` auf ⚠ gesetzt, mit dieser Begründung:*

> *„Konvexität wird geprüft, die **Erkennung** einspringender Ecken fehlt — die V1-Grenze **lehnt ab
> statt zu erkennen**."*

**Der Prototyp erkennt sie:** `isInnerReflex` und `angleDeg > 180` sind genau diese Unterscheidung.

```text
W-07 heute      pruefeRechteckigeKontur wirft bei allem, was nicht rechteckig ist
                (1 % Toleranz gegen die Bounding-Box) — L, T, U werden ABGELEHNT
W-27 waere      die Ecke wird KLASSIFIZIERT statt abgelehnt: innen -> Kehle,
                aussen -> Grat, Traufe-an-Giebel -> Ortgang
```

> **Damit ist W-27 nicht nur ein weiteres C-Werkzeug, sondern der Anschluss an W-07s größte
> Grenze.** *Und es ist derselbe Zusammenhang, den Yama am 12.08. bei A-01 aufgehoben hat: „das
> Nicht-Ziel »keine L/T/U-Dächer« stammt aus Unwissen über die Fähigkeit." **Die Fähigkeit liegt im
> Prototyp.** W-27 beschreibt sie, damit sie gebaut werden kann.*

## 4 · DECISION

```text
ZIEL          ENTWORFEN, nicht BESCHRIEBEN. Die sieben Blaetter GEBEN VOR, was gebaut
              werden soll — Quelle ist der Prototyp, nicht der Bestand. Zweites C-Werkzeug
              nach W-15, dieselbe Stufe, dieselbe Begruendung (Yamas Entscheidung 12.08.).

QUELLE        DachplanerProPage.tsx mit Zeilenangabe je uebernommener Struktur. KEIN
              Abschreiben ohne Fundstelle — das ist F-051s Lehre (vier Fundorte, null Quellen).

DIE FUENF     Das Register nennt "First·Grat·Kehle·Traufe·Ortgang". Der Prototyp trennt
KANTEN- UND   das anders, und die Trennung ist die bessere:
ECKENTYPEN      KANTEN (EdgeTopologyType):  TRAUFE · GIEBEL · PULT_WAND · WALM · TEILWALM
                ECKEN (TopologyJoinType):   grat · kehle · ortgang · neutral
              First und Grat sind KEINE Kantentypen im Prototyp — sie entstehen an ECKEN.
              Das Blatt uebernimmt die Trennung und benennt die Abweichung zur Registerzeile.

NICHT         Kein Bau. Kein Modul in resources/. W-27 BESCHREIBT.
              Keine Aenderung an W-07: dessen V1-Grenze bleibt, bis W-27 gebaut ist.
              Kein Straight Skeleton — F-020 und F-021 sind nach meiner Messung 12.08.
              NICHT ZUTREFFEND (0 Treffer auf 'skelett' in allen acht Dachmodulen).
              W-27 arbeitet auf dem Kanten-/Eckenweg, nicht auf dem Skelettweg.
```

## 5 · Abnahmekriterien

```text
W-27-1  (P1) Die sieben Blaetter geben die Struktur aus der Quelle VOR, jede mit
        Zeilenangabe: die drei Typlisten (:85-87), die zwei Schnittstellen (:128, :135),
        die drei Funktionen (:155, :182, :193). Gegenprobe: jede Angabe ist im Prototyp
        nachlesbar.

W-27-2  (P1, DER TRAGENDE PUNKT) 2-FUNKTION traegt die Entscheidungsregel aus Abschnitt 2
        vollstaendig — einschliesslich des Falls 'neutral'. Eine Regel, die nur drei von
        vier Ausgaengen nennt, laesst den vierten dem Bauenden; und 'neutral' ist der
        haeufigste, weil er fuer jede Kante ohne Traufe-Beteiligung gilt.

W-27-3  (P1, BERICHTIGT 12.08.) 7-GRENZEN nennt die Luecke als KANTENTYP, nicht als
        Sache. Die Zahlen: TopologyJoinType 0, cornerType 0 — das ist die fehlende
        ERKENNUNG. Aber der Ortgang SELBST ist da: ortgangFlaechenlaengeM ist eine
        exportierte, getestete Laengenrechnung und ortgangausbildung ein Feld mit
        Werten; 'Ortgang' hat 20 Treffer. VERBOTEN ist die Formulierung 'gibt es nicht'
        — sie fuehrt dazu, dass jemand eine Ortgang-Laenge neu baut, die es gibt.
        Ebenso bei 'grat' (17) und 'kehle' (33): Woerter und Rechnungen sind da, die
        KLASSIFIKATION fehlt. Gegenprobe im Bericht: je Begriff die Trefferzeile, die
        zeigt WAS existiert — nicht nur die Zahl, die zeigt was das Muster nicht fand.

W-27-4  (P1) Die Abweichung zur Registerzeile steht im Blatt: das Register nennt fuenf
        Namen in EINER Liste, der Prototyp trennt Kanten von Ecken. Die Trennung wird
        uebernommen; die Registerzeile wird beim Bau nachgezogen, NICHT jetzt.

W-27-5  (P2) Der Anschluss an W-07 wird benannt, ohne ihn zu bauen: W-07 lehnt heute
        nicht-rechteckige Konturen ab (1 % Toleranz), W-27 wuerde die Ecken
        klassifizieren. Was das fuer W-07s V1-Grenze bedeutet, ist eine
        Planner-Entscheidung NACH diesem Auftrag.

W-27-6  (must_preserve) resources/** und app/** byte-identisch — reine Doku-Stufe.
        Der Prototyp wird NICHT verandert, nur gelesen. W-07s Blaetter unberuehrt.

W-27-7  (P1, §3 wird BELEGT) Beide Orte nach ARBEITSREGELN §3, beide Zahlen genannt,
        Messung unmittelbar vor der ersten Aenderung. ACHTUNG REGISTER.md: sie liegt im
        Scope mehrerer W-Blaetter und war am 12.08. bei W-23 im Zugriff.
```

## 6 · Rückweg & Entdeckung

```text
RUECKWEG      reiner Revert. Sieben Doku-Blaetter; kein Code, kein Datenpfad, keine Migration.
KOPIE AUSSERHALB DER MASCHINE  ZUM BAUZEITPUNKT ZU PRUEFEN, hier nicht behauptet.
ENTDECKUNG    das Signal ist die Zahl der Ausgaenge: nennt 2-FUNKTION nur drei joinTypes,
              fehlt 'neutral' — und dann klassifiziert das gebaute Werkzeug spaeter jede
              gewoehnliche Kante als Grat oder Kehle. Das ist der stille Fehler dieses
              Werkzeugs, und er faellt erst am Dach auf.
```

## 7 · Konfliktprüfung §3 — unmittelbar vor dem Schnitt gemessen (H-4)

```text
Tafelzeile   ^\| \*\*[A-Z]+-?[0-9/]+\*\*[^|]*\| *`?IN_ARBEIT   ->  1   (W-23, Generator)
Zustandsfeld ^zustand: *IN_ARBEIT                              ->  1   deckungsgleich
docs/STATUS.md im Arbeitsbaum: 0
SCOPE-UEBERSCHNEIDUNG mit W-23: W-23 haelt die Werkbank-Blaetter und REGISTER.md.
        Dieser SCHNITT fasst NUR das neue Auftragsblatt und STATUS.md an — beides
        ausserhalb von W-23s Scope. Der BAU von W-27 wuerde REGISTER.md beruehren und
        muss §3 dann erneut messen (W-27-7).
W-27 wird auf ENTWURF geschnitten und nimmt keinen §3-Platz.
```

```yaml
zustand: ENTWURF
ballbesitz: "plan-pruefer (DoR)"
warum_ENTWORFEN_und_nicht_BESCHRIEBEN: "die Quelle ist ein Prototyp in docs/, nicht der Bestand in
        resources/. Ein BESCHRIEBEN-Blatt liest Code ab; hier wird VORGEGEBEN, was gebaut werden
        soll. Zweites Werkzeug dieser Stufe nach W-15."
was_es_anschliesst: "F-014s Warnung aus meiner Registermessung 12.08. — W-07 lehnt einspringende
        Ecken ab statt sie zu erkennen, der Prototyp erkennt sie (isInnerReflex, angleDeg > 180).
        Damit ist W-27 der Anschluss an W-07s groesste Grenze, und dieselbe Faehigkeit, deren
        Unkenntnis Yama bei A-01 als Grund fuer ein falsches Nicht-Ziel benannt hat."
nicht_der_skelettweg: "F-020 und F-021 sind nach meiner Messung 12.08. NICHT ZUTREFFEND — 0 Treffer
        auf 'skelett' in allen acht Dachmodulen. W-27 arbeitet auf dem Kanten-/Eckenweg."
```
